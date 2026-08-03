<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ApiManualController extends Controller
{
    private const DISK = 'public';
    private const MANUAL_PATH = 'manual/manual_usuario.pdf';

    /**
     * Devuelve el PDF del manual de usuario que se sube desde la web.
     */
    public function show()
    {
        $disk = Storage::disk(self::DISK);
        if (! $disk->exists(self::MANUAL_PATH)) {
            return response()->json([
                'message' => 'El manual de usuario no está disponible.'
            ], 404);
        }

        return response()->file($disk->path(self::MANUAL_PATH), [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}