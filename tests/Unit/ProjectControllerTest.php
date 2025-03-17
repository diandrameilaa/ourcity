<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_store_a_new_project()
    {
        $projectData = [
            'name' => 'New Project',
            'description' => 'Project description',
            'location' => 'Surabaya',
            'status' => 'planned',
            'longitude' => 112.752,
            'latitude' => -7.2575,
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
        $response = $this->post(route('projects.store'), []);

        $response->assertRedirect(route('projects.create'));
        $response->assertSessionHasErrors(['name', 'description', 'location', 'status', 'longitude', 'latitude']);
    }

    /** @test */
    public function it_can_update_an_existing_project()
    {
        $projectId = DB::table('projects')->insertGetId([
            'name' => 'Old Project',
            'description' => 'Old description',
            'location' => 'Jakarta',
            'status' => 'in_progress',
            'longitude' => 106.8456,
            'latitude' => -6.2088,
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
    public function it_fails_to_update_project_with_invalid_data()
    {
        $projectId = DB::table('projects')->insertGetId([
            'name' => 'Test Project',
            'description' => 'Some description',
            'location' => 'Bandung',
            'status' => 'planned',
            'longitude' => 107.6098,
            'latitude' => -6.9175,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->put(route('projects.update', $projectId), [
            'name' => '',
            'description' => '',
            'status' => 'invalid_status',
        ]);

        $response->assertRedirect(route('projects.edit', $projectId));
        $response->assertSessionHasErrors(['name', 'description', 'status', 'start_date', 'end_date']);
    }

    /** @test */
    public function it_can_delete_a_project()
    {
        $projectId = DB::table('projects')->insertGetId([
            'name' => 'Project To Delete',
            'description' => 'Some description',
            'location' => 'Malang',
            'status' => 'completed',
            'longitude' => 112.6304,
            'latitude' => -7.9785,
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
        $response = $this->delete(route('projects.destroy', 9999));

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Project not found.']);
    }

    /** @test */
    public function it_can_fetch_project_data_for_datatables()
    {
        DB::table('projects')->insert([
            [
                'name' => 'Project 1',
                'description' => 'First project',
                'location' => 'Surabaya',
                'status' => 'planned',
                'longitude' => 112.752,
                'latitude' => -7.2575,
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
                'longitude' => 106.8456,
                'latitude' => -6.2088,
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
