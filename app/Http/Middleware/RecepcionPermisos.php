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
        $result = UserPermisos::where('id_user', auth()->user()->id)
            ->where('clave_departamento', 'RECEP')
            ->take(1)->get();
        if (!count($result) > 0) {
            return redirect()->route('home');
        }
        return $next($request);
    }
}
