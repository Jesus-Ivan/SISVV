<?php

namespace App\Http\Middleware;

use App\Models\UserPermisos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PorticoPermisos
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $result = UserPermisos::where('id_user', auth()->user()->id)
            ->where('clave_departamento', 'PORT')
            ->take(1)->get();
        if (!count($result) > 0) {
            return redirect()->route('home')->with('error_permisos', 'No tienes permisos para portico');
        }
        return $next($request);
    }
}
