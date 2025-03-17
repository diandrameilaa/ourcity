<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['loginError']);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
