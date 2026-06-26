<?php

namespace App\Console\Commands;

use App\Models\IntegrantesSocio;
use App\Models\Membresias;
use App\Models\Socio;
use App\Models\SocioMembresia;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
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
        $key = (string) config('portico.api_key');

        if ($url === '' || $key === '') {
            $this->error('Faltan PORTICO_API_URL o PORTICO_API_KEY en el archivo .env');
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

        // 2. Enviar el snapshot completo.
        $this->info(sprintf(
            'Enviando %d socios, %d membresías, %d socios_membresías, %d integrantes...',
            $socios->count(),
            $membresias->count(),
            $sociosMembresias->count(),
            $integrantes->count()
        ));

        try {
            $respuesta = Http::withHeaders(['X-API-Key' => $key])
                ->acceptJson()
                ->timeout(60)
                ->post("{$url}/api/portico/sync", [
                    'socios'            => $socios,
                    'membresias'        => $membresias,
                    'socios_membresias' => $sociosMembresias,
                    'integrantes'       => $integrantes,
                ]);
        } catch (ConnectionException $e) {
            $this->error("No se pudo conectar con la API: {$e->getMessage()}");
            return self::FAILURE;
        }

        if ($respuesta->failed()) {
            $this->error("Error al sincronizar datos: HTTP {$respuesta->status()}");
            $this->line($respuesta->body());
            return self::FAILURE;
        }

        $this->info('Datos sincronizados correctamente.');

        // 3. Subir SOLO las imágenes que la API pide (cambiadas o faltantes).
        if (! $this->option('sin-imagenes')) {
            $requeridas = $respuesta->json('imagenes_requeridas', ['socio' => [], 'integrante' => []]);
            $this->subirImagenes($url, $key, $socios, $integrantes, $requeridas);
        }

        return self::SUCCESS;
    }

    /**
     * Sube en paralelo (por lotes) las fotos que la API pidió, con un reintento
     * por imagen fallida.
     */
    private function subirImagenes(string $url, string $key, $socios, $integrantes, array $requeridas): void
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
            $resultado = $this->subirLote($url, $key, $disk, $grupo);

            $fallaron = [];
            foreach ($grupo as $idx => $item) {
                if ($resultado[$idx] ?? false) {
                    $subidas++;
                } else {
                    $fallaron[] = $item;
                }
            }

            if ($fallaron !== []) {
                $reintento = $this->subirLote($url, $key, $disk, $fallaron);
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
    }

    /**
     * Sube un lote de imágenes en paralelo. Devuelve [índice => bool éxito].
     */
    private function subirLote(string $url, string $key, $disk, array $grupo): array
    {
        try {
            $respuestas = Http::pool(fn (Pool $pool) => collect($grupo)
                ->map(fn ($item) => $pool->withHeaders(['X-API-Key' => $key])
                    ->timeout(60)
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
