<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Caja;
use App\Models\Socio;
use App\Models\SocioMembresia;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiMobileTest extends TestCase
{
    // Usamos RefreshDatabase si queremos limpiar la DB de pruebas, pero como el proyecto corre en Sail con base de datos configurada,
    // podemos crear registros en transacciones que se limpian o usar factories.
    // Para no perturbar datos de desarrollo, crearemos usuarios temporales y los limpiaremos.

    public function test_login_validation()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_incorrect_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@vistaverde.com',
            'password' => 'secret123'
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_authenticated_endpoints_require_token()
    {
        $this->getJson('/api/sync/productos')->assertStatus(401);
        $this->getJson('/api/sync/socios')->assertStatus(401);
        $this->getJson('/api/ventas')->assertStatus(401);
    }

    public function test_sync_endpoints_work_for_authenticated_users()
    {
        // Crear un usuario de prueba
        $user = User::factory()->create([
            'password' => Hash::make('password123')
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/sync/productos');
        $response->assertStatus(200);

        $responseSocios = $this->actingAs($user, 'sanctum')->getJson('/api/sync/socios');
        $responseSocios->assertStatus(200);

        // Limpiar el usuario de prueba
        $user->delete();
    }
}
