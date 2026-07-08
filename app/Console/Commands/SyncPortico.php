<?php

namespace App\Console\Commands;

use App\Models\IntegrantesSocio;
use App\Models\Membresias;
use App\Models\Socio;
use App\Models\SocioMembresia;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Envía un snapshot de socios, membresías e integrantes (con sus fotos) a la
 * API PORTICO, desde donde la app de escritorio PorticoVV los consume.
 *
 * Solo se envían los campos necesarios para el control de acceso; no se
 * exponen datos personales sensibles (dirección, teléfonos, CURP, RFC, correos).
 */
class SyncPortico extends Command
{
    protected $signature = 'sync:portico {--sin-imagenes : Enviar solo los datos, omitir la subida de fotos}';

    protected $description = 'Sincroniza socios, membresías e integrantes hacia la API PORTICO';

    public function handle(): int
    {
        $url = rtrim((string) config('portico.api_url'), '/');

        // La API es de acceso público (sin API key). Solo se requiere la URL.
        // Los errores se escriben también en el log: cuando el comando corre
        // vía Artisan::call() (botón de Sistemas) o por el scheduler, la
        // salida de consola ($this->error) no la ve nadie.
        if ($url === '') {
            $this->error('Falta PORTICO_API_URL en el archivo .env');
            Log::error('sync:portico: falta PORTICO_API_URL en el archivo .env');
            return self::FAILURE;
        }

        // 1. Recolectar datos (Socio excluye borrados por SoftDeletes).
        $socios = Socio::query()
            ->select('id', 'nombre', 'apellido_p', 'apellido_m', 'img_path')
            ->get();

        $membresias = Membresias::query()
            ->select('clave', 'descripcion')
            ->get();

        // Solo membresías de socios vigentes. Se usa whereExists (en vez de
        // whereIn con todos los ids) para que escale con miles de socios.
        $sociosMembresias = SocioMembresia::query()
            ->select('id', 'id_socio', 'clave_membresia', 'estado')
            ->whereExists(fn ($q) => $q->from('socios')
                ->whereColumn('socios.id', 'socios_membresias.id_socio')
                ->whereNull('socios.deleted_at'))
            ->get();

        // IntegrantesSocio no usa SoftDeletes: filtrar borrados manualmente y
        // solo los que pertenecen a un socio vigente (whereExists, escalable).
        $integrantes = IntegrantesSocio::query()
            ->select(
                'id',
                'id_socio',
                'nombre_integrante',
                'apellido_p_integrante',
                'apellido_m_integrante',
                'img_path_integrante',
                'parentesco'
            )
            ->whereNull('deleted_at')
            ->whereExists(fn ($q) => $q->from('socios')
                ->whereColumn('socios.id', 'integrantes_socios.id_socio')
                ->whereNull('socios.deleted_at'))
            ->get();

        // Usuarios del sistema: la API los usa para validar el inicio de
        // sesión de PorticoVV (y otras funciones futuras). Se envían todos.
        // Se consulta con DB::table porque el modelo User oculta "password"
        // al serializar y el hash es justo lo que la API necesita para validar.
        $users = DB::table('users')
            ->select('id', 'name', 'email', 'password')
            ->get();

        // 2. Enviar el snapshot completo.
        $this->info(sprintf(
            'Enviando %d socios, %d membresías, %d socios_membresías, %d integrantes, %d usuarios...',
            $socios->count(),
            $membresias->count(),
            $sociosMembresias->count(),
            $integrantes->count(),
            $users->count()
        ));

        try {
            $respuesta = Http::acceptJson()
                ->timeout(60)
                ->post("{$url}/api/portico/sync", [
                    'socios'            => $socios,
                    'membresias'        => $membresias,
                    'socios_membresias' => $sociosMembresias,
                    'integrantes'       => $integrantes,
                    'users'             => $users,
                ]);
        } catch (ConnectionException $e) {
            $this->error("No se pudo conectar con la API: {$e->getMessage()}");
            Log::error("sync:portico: no se pudo conectar con la API: {$e->getMessage()}");
            return self::FAILURE;
        }

        if ($respuesta->failed()) {
            $this->error("Error al sincronizar datos: HTTP {$respuesta->status()}");
            $this->line($respuesta->body());
            Log::error("sync:portico: error HTTP {$respuesta->status()} al sincronizar datos", [
                'respuesta' => mb_substr($respuesta->body(), 0, 1000),
            ]);
            return self::FAILURE;
        }

        $this->info('Datos sincronizados correctamente.');

        // 3. Subir SOLO las imágenes que la API pide (cambiadas o faltantes).
        if (! $this->option('sin-imagenes')) {
            $requeridas = $respuesta->json('imagenes_requeridas', ['socio' => [], 'integrante' => []]);
            $this->subirImagenes($url, $socios, $integrantes, $requeridas);
        }

        return self::SUCCESS;
    }

    /**
     * Sube en paralelo (por lotes) las fotos que la API pidió, con un reintento
     * por imagen fallida.
     */
    private function subirImagenes(string $url, $socios, $integrantes, array $requeridas): void
    {
        $disk = Storage::disk('public');

        // IDs que la API pidió (cambiados o faltantes); el resto no se re-sube.
        $idsSocios      = array_flip($requeridas['socio'] ?? []);
        $idsIntegrantes = array_flip($requeridas['integrante'] ?? []);

        // Solo los que la API pide y cuyo archivo existe en disco.
        $items = [];
        $omitidas = 0;
        foreach ($socios as $s) {
            if (! empty($s->img_path) && isset($idsSocios[$s->id])) {
                if ($disk->exists($s->img_path)) {
                    $items[] = ['socio', $s->id, $s->img_path];
                } else {
                    $omitidas++;
                }
            }
        }
        foreach ($integrantes as $i) {
            if (! empty($i->img_path_integrante) && isset($idsIntegrantes[$i->id])) {
                if ($disk->exists($i->img_path_integrante)) {
                    $items[] = ['integrante', $i->id, $i->img_path_integrante];
                } else {
                    $omitidas++;
                }
            }
        }

        if ($items === []) {
            $this->info($omitidas > 0
                ? "No hay imágenes que subir ({$omitidas} sin archivo en disco)."
                : 'No hay imágenes nuevas o modificadas que subir.');
            return;
        }

        $this->info('Subiendo ' . count($items) . ' imágenes...');
        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        $subidas = 0;
        $fallidas = 0;

        // Subir de a 5 en paralelo; reintentar una vez las que fallen.
        foreach (array_chunk($items, 5) as $grupo) {
            $resultado = $this->subirLote($url, $disk, $grupo);

            $fallaron = [];
            foreach ($grupo as $idx => $item) {
                if ($resultado[$idx] ?? false) {
                    $subidas++;
                } else {
                    $fallaron[] = $item;
                }
            }

            if ($fallaron !== []) {
                $reintento = $this->subirLote($url, $disk, $fallaron);
                foreach ($fallaron as $idx => $item) {
                    if ($reintento[$idx] ?? false) {
                        $subidas++;
                    } else {
                        $fallidas++;
                    }
                }
            }

            $bar->advance(count($grupo));
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imágenes: {$subidas} subidas, {$omitidas} sin archivo, {$fallidas} con error.");

        if ($fallidas > 0) {
            Log::warning("sync:portico: {$fallidas} imágenes no se pudieron subir a la API (se reintentarán en la siguiente sincronización).");
        }
    }

    /**
     * Sube un lote de imágenes en paralelo. Devuelve [índice => bool éxito].
     */
    private function subirLote(string $url, $disk, array $grupo): array
    {
        try {
            $respuestas = Http::pool(fn (Pool $pool) => collect($grupo)
                ->map(fn ($item) => $pool->timeout(60)
                    ->attach('image', $disk->get($item[2]), basename($item[2]))
                    ->post("{$url}/api/portico/images/{$item[0]}/{$item[1]}"))
                ->all());
        } catch (\Throwable $e) {
            // Si el lote entero falla (ej. error de conexión), marcar todo fallido.
            return array_fill(0, count($grupo), false);
        }

        $exitos = [];
        foreach ($respuestas as $idx => $resp) {
            // En un pool, un request con error de conexión devuelve un Throwable
            // en lugar de una Response.
            $exitos[$idx] = $resp instanceof \Illuminate\Http\Client\Response && $resp->successful();
        }

        return $exitos;
    }
}
