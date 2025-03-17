<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_store_creates_a_new_user()
    {
        $response = $this->postJson(route('user.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'role', 'status', 'created_at', 'updated_at'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com', 
        ]);
    }

    public function test_store_fails_with_invalid_data()
    {
        $response = $this->postJson(route('user.store'), [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
            'role' => 'invalid-role',
        ]);

        $response->assertStatus(422);
    }

    public function test_update_modifies_existing_user()
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->putJson(route('user.update', $userId), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'role' => 'admin',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_update_fails_with_invalid_data()
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->putJson(route('user.update', $userId), [
            'name' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
    }

    public function test_destroy_deletes_user()
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Delete Me',
            'email' => 'delete@example.com',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->deleteJson(route('user.destroy', $userId));

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_destroy_fails_for_non_existent_user()
    {
        $response = $this->deleteJson(route('user.destroy', 9999));

        $response->assertStatus(404);
    }

    public function test_toggle_status_flips_user_status()
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Status Test',
            'email' => 'status@example.com',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson(route('user.toggle_status'), ['id' => $userId]);

        $response->assertStatus(200)->assertJsonStructure(['status']);

        $this->assertDatabaseHas('users', ['id' => $userId, 'status' => 0]);
    }
}
