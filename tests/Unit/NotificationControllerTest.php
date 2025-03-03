<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Mail\NotificationMail;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_returns_data_table_json()
    {
        // Seed database dengan dummy notification
        DB::table('notifications')->insert([
            'title' => 'Test Notification',
            'message' => 'This is a test notification.',
            'is_sent' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Panggil endpoint index
        $response = $this->get(route('notifications.index'), ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        // Pastikan response status 200 dan memiliki struktur data yang benar
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'message', 'is_sent', 'created_at', 'updated_at']
            ]
        ]);
    }

    /** @test */
    public function store_sends_emails_and_updates_database()
    {
        Mail::fake(); // Hindari pengiriman email nyata

        // Buat user dummy dengan role "warga"
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test User', // Perbaikan karena field "name" diperlukan
            'email' => 'test@example.com',
            'role' => 'warga',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Data notifikasi untuk dikirim
        $data = [
            'title' => 'New Notification',
            'message' => 'This is a test message.',
        ];

        // Panggil endpoint store
        $response = $this->post(route('notifications.store'), $data);

        // Pastikan redirect sukses
        $response->assertRedirect(route('notifications.index'));

        // Pastikan notifikasi tersimpan di database
        $this->assertDatabaseHas('notifications', [
            'title' => 'New Notification',
            'message' => 'This is a test message.',
            'is_sent' => true,
        ]);

        // Pastikan email dikirim ke user
        Mail::assertSent(NotificationMail::class, function ($mail) use ($userId) {
            return $mail->hasTo('test@example.com');
        });
    }
}
