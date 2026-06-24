<?php

namespace App\Console\Commands;

use App\Models\IntegrantesSocio;
use App\Models\Membresias;
use App\Models\Socio;
use App\Models\SocioMembresia;
use Illuminate\Console\Command;
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

        $idsSocios = $socios->pluck('id');

        $membresias = Membresias::query()
            ->select('clave', 'descripcion')
            ->get();

        $sociosMembresias = SocioMembresia::query()
            ->select('id', 'id_socio', 'clave_membresia', 'estado')
            ->whereIn('id_socio', $idsSocios)
            ->get();

        // IntegrantesSocio no usa SoftDeletes: filtrar borrados manualmente
        // y solo los que pertenecen a un socio vigente.
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
            ->whereIn('id_socio', $idsSocios)
            ->get();

        // 2. Enviar el snapshot completo.
        $this->info(sprintf(
            'Enviando %d socios, %d membresías, %d socios_membresías, %d integrantes...',
            $socios->count(),
            $membresias->count(),
            $sociosMembresias->count(),
            $integrantes->count()
        ));

        $respuesta = Http::withHeaders(['X-API-Key' => $key])
            ->acceptJson()
            ->timeout(60)
            ->post("{$url}/api/portico/sync", [
                'socios'            => $socios,
                'membresias'        => $membresias,
                'socios_membresias' => $sociosMembresias,
                'integrantes'       => $integrantes,
            ]);

        if ($respuesta->failed()) {
            $this->error("Error al sincronizar datos: HTTP {$respuesta->status()}");
            $this->line($respuesta->body());
            return self::FAILURE;
        }

        $this->info('Datos sincronizados correctamente.');

        // 3. Subir imágenes (salvo que se pida omitirlas).
        if (! $this->option('sin-imagenes')) {
            $this->subirImagenes($url, $key, $socios, $integrantes);
        }

        return self::SUCCESS;
    }

    /**
     * Sube las fotos de socios e integrantes que tengan imagen.
     */
    private function subirImagenes(string $url, string $key, $socios, $integrantes): void
    {
        $disk = Storage::disk('public');

        $items = [];
        foreach ($socios as $s) {
            if (! empty($s->img_path)) {
                $items[] = ['socio', $s->id, $s->img_path];
            }
        }
        foreach ($integrantes as $i) {
            if (! empty($i->img_path_integrante)) {
                $items[] = ['integrante', $i->id, $i->img_path_integrante];
            }
        }

        if ($items === []) {
            $this->info('No hay imágenes que subir.');
            return;
        }

        $this->info('Subiendo ' . count($items) . ' imágenes...');
        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        $subidas = 0;
        $omitidas = 0;
        $fallidas = 0;

        foreach ($items as [$tipo, $id, $path]) {
            if (! $disk->exists($path)) {
                $omitidas++;
                $bar->advance();
                continue;
            }

            try {
                $resp = Http::withHeaders(['X-API-Key' => $key])
                    ->acceptJson()
                    ->timeout(60)
                    ->attach('image', $disk->get($path), basename($path))
                    ->post("{$url}/api/portico/images/{$tipo}/{$id}");

                $resp->successful() ? $subidas++ : $fallidas++;
            } catch (\Throwable $e) {
                $fallidas++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Imágenes: {$subidas} subidas, {$omitidas} sin archivo, {$fallidas} con error.");
    }
}
