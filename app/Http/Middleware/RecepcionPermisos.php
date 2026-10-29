<?php

namespace App\Http\Middleware;

use App\Models\UserPermisos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecepcionPermisos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //Permisos para ingresar al departamento (obligatorios)
        $result = UserPermisos::where('id_user', auth()->user()->id)
            ->where('clave_departamento', 'RECEP')
            ->take(1)->get();

        //Obtenemos permisos de sistemas, para acciones especificas dentro del departamento (opcionales)
        $permisos_sistemas = UserPermisos::where('id_user', auth()->user()->id)
            ->where('clave_departamento', 'SIS')
            ->first();

        if (!count($result) > 0) {
            return redirect()->route('home')->with('error_permisos', 'No tienes permisos para recepcion');
        }
        $request->merge(['permisos_sistemas' => $permisos_sistemas]);
        return $next($request);
    }
}
