<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;

class DiscussionTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_discussion_flow()
    {
        // Buat user
        $user = User::factory()->create();
        
        // Simulasikan login
        $this->actingAs($user);

        // Buat pesan diskusi baru
        $response = $this->post(route('discussions.store'), [
            'message' => 'Integration Test Message',
        ]);

        $response->assertRedirect(route('discussions.index'));

        // Pastikan pesan tersimpan di database
        $this->assertDatabaseHas('discussions', [
            'user_id' => $user->id,
            'message' => 'Integration Test Message',
        ]);

        // Tampilkan halaman diskusi
        $response = $this->get(route('discussions.index'));
        $response->assertStatus(200);
        $response->assertSee('Integration Test Message');

        // Ambil ID dari diskusi yang baru dibuat menggunakan Query Builder
        $discussion = DB::table('discussions')->where('message', 'Integration Test Message')->first();
        
        // Hapus pesan diskusi
        $response = $this->delete(route('discussions.destroy', $discussion->id));
        $response->assertRedirect(route('discussions.index'));

        // Pastikan pesan diskusi ditandai sebagai dihapus
        $this->assertDatabaseHas('discussions', [
            'id' => $discussion->id,
            'is_deleted' => true,
        ]);
    }
}
