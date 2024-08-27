<?php

namespace App\Http\Middleware;

use App\Models\UserPermisos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AlmacenPermisos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $result = UserPermisos::where('id_user', auth()->user()->id)
            ->where('clave_departamento', 'ALM')
            ->first();
        if (! $result) {
            return redirect()->route('home')->with('error_permisos', 'No tienes permisos para almacen');
        }
        $request->merge(['permisos_pv' => $result]);
        return $next($request);
    }
}
