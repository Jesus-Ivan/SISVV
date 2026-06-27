<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ApiAuthController extends Controller
{
    /**
     * Inicia sesión de mesero y retorna token Sanctum con sus permisos asociados.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.'
            ], 401);
        }

        // Obtener permisos y roles en puntos de venta para el usuario
        $permisos = DB::table('users_permisos')
            ->join('puntos_venta', 'users_permisos.clave_punto_venta', '=', 'puntos_venta.clave')
            ->select('users_permisos.clave_punto_venta', 'puntos_venta.nombre as punto_venta_nombre', 'users_permisos.clave_rol')
            ->where('users_permisos.id_user', $user->id)
            ->get();

        // Crear token de Sanctum
        $token = $user->createToken($request->device_name ?? 'mobile_app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'permisos' => $permisos
            ]
        ]);
    }

    /**
     * Cierra la sesión activa revocando el token actual.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.'
        ]);
    }
}