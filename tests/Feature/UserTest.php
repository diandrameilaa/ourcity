<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_a_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->postJson(route('user.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'warga',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    /** @test */
    public function admin_can_update_a_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $this->actingAs($admin);

        $response = $this->putJson(route('user.update', $user->id), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'admin',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'updated@example.com']);
    }

    /** @test */
    public function admin_can_delete_a_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $this->actingAs($admin);

        $response = $this->deleteJson(route('user.destroy', $user->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
