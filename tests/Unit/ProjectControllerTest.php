<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User; // Pastikan User dimuat dengan benar

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling(); // Untuk debugging error lebih mudah
    }

    /** @test */
    public function it_can_access_index_page()
    {
        $user = User::factory()->create(); // Pastikan ada user
        $response = $this->actingAs($user)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertViewIs('projects.index');
    }

    /** @test */
    public function it_can_store_a_project()
    {
        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'description' => 'This is a test project',
            'location' => 'Test Location',
            'status' => 'planned',
            'longitude' => 112.75,
            'latitude' => -7.25,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
        ]);

        $response->assertRedirect(route('projects.index'));

        // Periksa apakah data benar-benar masuk ke database
        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
    }

    /** @test */
    public function it_can_update_a_project()
    {
        // Simpan project langsung ke database tanpa model
        $projectId = DB::table('projects')->insertGetId([
            'name' => 'Old Project',
            'description' => 'Old description',
            'location' => 'Old Location',
            'status' => 'planned',
            'longitude' => 112.75,
            'latitude' => -7.25,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->put(route('projects.update', $projectId), [
            'name' => 'Updated Project',
            'description' => 'Updated description',
            'location' => 'Updated Location',
            'status' => 'in_progress',
            'longitude' => 112.80,
            'latitude' => -7.20,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
        ]);

        $response->assertRedirect(route('projects.index'));

        // Periksa perubahan di database
        $this->assertDatabaseHas('projects', ['name' => 'Updated Project']);
    }

    /** @test */
    public function it_can_delete_a_project()
    {
        $projectId = DB::table('projects')->insertGetId([
            'name' => 'Project to be deleted',
            'description' => 'Some description',
            'location' => 'Delete Location',
            'status' => 'completed',
            'longitude' => 112.75,
            'latitude' => -7.25,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->delete(route('projects.destroy', $projectId));

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Project deleted successfully.']);
        $this->assertDatabaseMissing('projects', ['id' => $projectId]);        
    }

    /** @test */
    public function it_can_fetch_data_for_datatables()
    {
        DB::table('projects')->insert([
            'name' => 'Test Project',
            'description' => 'Testing datatables',
            'location' => 'Datatables Location',
            'status' => 'planned',
            'longitude' => 112.75,
            'latitude' => -7.25,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson(route('projects.data'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        $response->assertJsonFragment(['name' => 'Test Project']);
    }
}
