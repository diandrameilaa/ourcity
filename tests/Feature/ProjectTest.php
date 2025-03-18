<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');

        // ✅ Buat user dummy dengan role admin
        $this->adminUser = DB::table('users')->insertGetId([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin', // Pastikan role = admin agar bisa akses route
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function it_can_store_a_new_project()
    {
        $this->actingAs((object) ['id' => $this->adminUser, 'role' => 'admin']); // ✅ Simulasi login sebagai admin

        $projectData = [
            'name' => 'New Project',
            'description' => 'Project description',
            'location' => 'Surabaya',
            'status' => 'planned',
            'longitude' => (string) 112.752,
            'latitude' => (string) -7.2575,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
        ];

        $response = $this->post(route('projects.store'), $projectData);

        $response->assertRedirect(route('projects.index'))
                 ->assertSessionHas('success', 'Project created successfully!');

        $this->assertDatabaseHas('projects', [
            'name' => 'New Project',
            'status' => 'planned',
        ]);
    }

    /** @test */
    public function it_fails_to_store_project_with_invalid_data()
    {
        $this->actingAs((object) ['id' => $this->adminUser, 'role' => 'admin']);

        $response = $this->post(route('projects.store'), []);

        $response->assertRedirect(route('projects.create'));
        $response->assertSessionHasErrors(['name', 'description', 'location', 'status', 'longitude', 'latitude']);
    }

    /** @test */
    public function it_can_update_an_existing_project()
    {
        $this->actingAs((object) ['id' => $this->adminUser, 'role' => 'admin']);

        $projectId = DB::table('projects')->insertGetId([
            'name' => 'Old Project',
            'description' => 'Old description',
            'location' => 'Jakarta',
            'status' => 'in_progress',
            'longitude' => (string) 106.8456,
            'latitude' => (string) -6.2088,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $updateData = [
            'name' => 'Updated Project',
            'description' => 'Updated description',
            'status' => 'completed',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(20)->toDateString(),
        ];

        $response = $this->put(route('projects.update', $projectId), $updateData);

        $response->assertRedirect(route('projects.index'))
                 ->assertSessionHas('success', 'Project updated successfully.');

        $this->assertDatabaseHas('projects', [
            'id' => $projectId,
            'name' => 'Updated Project',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function it_can_delete_a_project()
    {
        $this->actingAs((object) ['id' => $this->adminUser, 'role' => 'admin']);

        $projectId = DB::table('projects')->insertGetId([
            'name' => 'Project To Delete',
            'description' => 'Some description',
            'location' => 'Malang',
            'status' => 'completed',
            'longitude' => (string) 112.6304,
            'latitude' => (string) -7.9785,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->delete(route('projects.destroy', $projectId));

        $response->assertStatus(200)
                 ->assertJson(['success' => 'Project deleted successfully.']);

        $this->assertDatabaseMissing('projects', ['id' => $projectId]);
    }

    /** @test */
    public function it_returns_error_when_deleting_non_existent_project()
    {
        $this->actingAs((object) ['id' => $this->adminUser, 'role' => 'admin']);

        $response = $this->delete(route('projects.destroy', 9999));

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Project not found.']);
    }

    /** @test */
    public function it_can_fetch_project_data_for_datatables()
    {
        $this->actingAs((object) ['id' => $this->adminUser, 'role' => 'admin']);

        DB::table('projects')->insert([
            [
                'name' => 'Project 1',
                'description' => 'First project',
                'location' => 'Surabaya',
                'status' => 'planned',
                'longitude' => (string) 112.752,
                'latitude' => (string) -7.2575,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(10)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Project 2',
                'description' => 'Second project',
                'location' => 'Jakarta',
                'status' => 'completed',
                'longitude' => (string) 106.8456,
                'latitude' => (string) -6.2088,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(15)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson(route('projects.data'));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'description', 'location', 'status', 'longitude', 'latitude', 'start_date', 'end_date']
                     ]
                 ]);
    }
}
