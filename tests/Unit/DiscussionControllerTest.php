<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class DiscussionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_discussions()
    {
        // Insert data dummy langsung ke database menggunakan Query Builder
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        DB::table('discussions')->insert([
            'id' => 1,
            'user_id' => 1,
            'message' => 'Test Message',
            'is_deleted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simulasikan user login
        Auth::loginUsingId(1);

        // Akses halaman diskusi
        $response = $this->get(route('discussions.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Message');
    }

    public function test_user_can_store_discussion_message()
    {
        // Insert user dummy
        DB::table('users')->insert([
            'id' => 2,
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
        ]);

        // Simulasikan user login
        Auth::loginUsingId(2);

        // Kirim POST request untuk menyimpan pesan diskusi
        $response = $this->post(route('discussions.store'), [
            'message' => 'This is a test message',
        ]);

        $response->assertRedirect(route('discussions.index'));

        // Pastikan data tersimpan di database
        $this->assertDatabaseHas('discussions', [
            'user_id' => 2,
            'message' => 'This is a test message',
        ]);
    }

    public function test_user_can_delete_discussion_message()
    {
        // Insert user dummy
        DB::table('users')->insert([
            'id' => 3,
            'name' => 'Test User 3',
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
        ]);

        // Insert diskusi dummy
        DB::table('discussions')->insert([
            'id' => 3,
            'user_id' => 3,
            'message' => 'Message to be deleted',
            'is_deleted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simulasikan user login
        Auth::loginUsingId(1);

        // Kirim DELETE request untuk menghapus pesan
        $response = $this->delete(route('discussions.destroy', 3));

        $response->assertRedirect(route('discussions.index'));

        // Pastikan status is_deleted berubah menjadi true
        $this->assertDatabaseHas('discussions', [
            'id' => 4,
            'is_deleted' => true,
        ]);
    }
}
