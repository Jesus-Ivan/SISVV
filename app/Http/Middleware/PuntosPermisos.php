<?php

namespace App\Http\Middleware;

use App\Models\UserPermisos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PuntosPermisos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $result = UserPermisos::where('id_user', auth()->user()->id)
            ->where('clave_departamento', 'PV')
            ->where('clave_punto_venta', $request->route('codigopv'))
            ->first();
        if (! $result) {
            return redirect()->route('home');
        }
        $request->merge(['permisos_pv' => $result]);
        return $next($request);
    }
}
