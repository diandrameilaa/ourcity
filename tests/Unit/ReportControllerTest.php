<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test 1*/
    public function user_can_access_reports_index_page()
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    /** @test 2*/
    public function user_can_get_reports_data()
    {
        DB::table('reports')->insert([
            'user_id' => $this->user->id,
            'description' => 'Test Report',
            'status' => 'diajukan',
            'photo' => null,
            'location' => 'Jakarta',
            'longitude' => '106.816666',
            'latitude' => '-6.200000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                '*' => [
                    'id',
                    'user_name',
                    'description',
                    'photo',
                    'location',
                    'status',
                    'longitude',
                    'latitude',
                    'created_at',
                ]
            ]
        ]);
    }

    /** @test 3*/
    public function user_can_access_create_report_page()
    {
        $response = $this->actingAs($this->user)->get(route('reports.create'));
        $response->assertStatus(200);
    }

    /** @test 4*/
    public function user_can_access_edit_report_page()
    {
        $reportId = DB::table('reports')->insertGetId([
            'user_id' => $this->user->id,
            'description' => 'Test Report',
            'status' => 'diajukan',
            'photo' => null,
            'location' => 'Jakarta',
            'longitude' => '106.816666',
            'latitude' => '-6.200000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.edit', $reportId));
        $response->assertStatus(200);
    }

    /** @test 5*/
    public function user_can_update_a_report()
    {
        $reportId = DB::table('reports')->insertGetId([
            'user_id' => $this->user->id,
            'description' => 'Old Report',
            'status' => 'diajukan',
            'photo' => null,
            'location' => 'Jakarta',
            'longitude' => '106.816666',
            'latitude' => '-6.200000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->put(route('reports.update', $reportId), [
            'description' => 'Laporan diperbarui',
            'status' => 'diproses',
        ]);

        $response->assertRedirect(route('reports.index'));
        $response->assertSessionHas('success', 'Report updated successfully!');
        $this->assertDatabaseHas('reports', [
            'id' => $reportId,
            'description' => 'Laporan diperbarui',
            'status' => 'diproses',
        ]);
    }

    /** @test 6*/
    public function user_can_delete_a_report()
    {
        $reportId = DB::table('reports')->insertGetId([
            'user_id' => $this->user->id,
            'description' => 'Report to be deleted',
            'status' => 'diajukan',
            'photo' => null,
            'location' => 'Jakarta',
            'longitude' => '106.816666',
            'latitude' => '-6.200000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->delete(route('reports.destroy', $reportId));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('reports', ['id' => $reportId]);
    }
    
       /** @test */
    public function test_report_store_functionality()
    {
        // Simulasi file gambar
        $file = UploadedFile::fake()->image('report.jpg');

        // Data valid
        $validData = [
            'description' => 'Test report description',
            'photo' => $file,
            'location' => 'Test Location',
            'longitude' => '123.456',
            'latitude' => '-123.456',
        ];

        // Simulasi permintaan POST dengan data valid
        $response = $this->post(route('reports.store'), $validData);

        // Pastikan data tersimpan di database
        $this->assertDatabaseHas('reports', [
            'description' => 'Test report description',
            'location' => 'Test Location',
            'status' => 'diajukan',
            'longitude' => '123.456',
            'latitude' => '-123.456',
        ]);

        // Pastikan redirect ke halaman index dengan pesan sukses
        $response->assertRedirect(route('reports.index'));
        $response->assertSessionHas('success', 'Report created successfully!');

        // Data tidak valid
        $invalidData = [
            'description' => '', // Kosong, harusnya required
            'location' => '', // Kosong, harusnya required
            'longitude' => '', // Kosong, harusnya required
            'latitude' => '', // Kosong, harusnya required
        ];

        // Simulasi permintaan POST dengan data tidak valid
        $responseInvalid = $this->post(route('reports.store'), $invalidData);

        // Pastikan ada error validasi
        $responseInvalid->assertSessionHasErrors(['description', 'location', 'longitude', 'latitude']);
    }
}