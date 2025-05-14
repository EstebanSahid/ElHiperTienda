<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $rol = Rol::factory()->create();

        $user = User::factory()->create([
            'id_rol' => $rol->id_rol,
        ]);
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $rol = Rol::factory()->create();

        $user = User::factory()->create([
            'id_rol' => $rol->id_rol,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $rol = Rol::factory()->create();
        
        $user = User::factory()->create([
            'id_rol' => $rol->id_rol,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
        
    }
}
