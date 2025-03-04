<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Jalankan migrasi
        $this->artisan('migrate');

        // Buat user dummy menggunakan Query Builder (tanpa model)
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /** @test */
    public function it_can_display_dashboard_with_correct_statistics()
    {
        // Simulasi login sebagai user ID 1
        Auth::loginUsingId(1);

        // Seed data laporan (reports) dengan kolom user_id dan location
        DB::table('reports')->insert([
            [
                'user_id' => 1,
                'status' => 'diajukan',
                'created_at' => now(), // Hari ini
                'latitude' => -7.2575,
                'longitude' => 112.752,
                'description' => 'Laporan 1',
                'location' => 'Surabaya',
            ],
            [
                'user_id' => 1,
                'status' => 'diproses',
                'created_at' => now()->subDays(3), // 3 hari lalu
                'latitude' => -7.2585,
                'longitude' => 112.753,
                'description' => 'Laporan 2',
                'location' => 'Jakarta',
            ],
            [
                'user_id' => 1,
                'status' => 'selesai',
                'created_at' => now()->subDays(7), // Tepat 7 hari lalu
                'latitude' => -7.2595,
                'longitude' => 112.754,
                'description' => 'Laporan 3',
                'location' => 'Bandung',
            ],
        ]);

        // Seed data proyek (projects) dengan kolom name dan location
        DB::table('projects')->insert([
            [
                'name' => 'Proyek 1',
                'status' => 'planned',
                'created_at' => now(),
                'latitude' => -7.2575,
                'longitude' => 112.752,
                'description' => 'Proyek 1',
                'location' => 'Surabaya',
            ],
            [
                'name' => 'Proyek 2',
                'status' => 'in_progress',
                'created_at' => now()->subDays(3),
                'latitude' => -7.2585,
                'longitude' => 112.753,
                'description' => 'Proyek 2',
                'location' => 'Jakarta',
            ],
            [
                'name' => 'Proyek 3',
                'status' => 'completed',
                'created_at' => now()->subDays(7),
                'latitude' => -7.2595,
                'longitude' => 112.754,
                'description' => 'Proyek 3',
                'location' => 'Bandung',
            ],
        ]);

        // Akses endpoint dashboard
        $response = $this->get(route('dashboard'));

        // Pastikan response OK
        $response->assertStatus(200);

        // Pastikan view dashboard ditampilkan
        $response->assertViewIs('dashboard');

        // Cek statistik laporan
        $response->assertViewHas('reportStats', [
            'total' => 3,
            'diajukan' => 1,
            'diproses' => 1,
            'selesai' => 1,
        ]);

        // Cek statistik proyek
        $response->assertViewHas('projectStats', [
            'planned' => 1,
            'in_progress' => 1,
            'completed' => 1,
        ]);

        // Cek aktivitas pengguna (weekly_reports = 3 karena semua masih di range subWeek)
        $response->assertViewHas('userActivityStats', [
            'total_reports' => 3,
            'weekly_reports' => 3, // Semua laporan dihitung (7 hari penuh)
        ]);

        // Cek aktivitas mingguan (weekly_projects = 3 karena semua masih di range subWeek)
        $response->assertViewHas('activityReports', [
            'weekly_reports' => 3, // Semua laporan dihitung
            'weekly_projects' => 3, // Semua proyek dihitung
        ]);

        // Cek lokasi laporan
        $response->assertViewHas('reportLocations', function ($locations) {
            return count($locations) === 3 &&
                $locations->contains('description', 'Laporan 1') &&
                $locations->contains('description', 'Laporan 2') &&
                $locations->contains('description', 'Laporan 3');
        });

        // Cek lokasi proyek
        $response->assertViewHas('projectLocations', function ($locations) {
            return count($locations) === 3 &&
                $locations->contains('description', 'Proyek 1') &&
                $locations->contains('description', 'Proyek 2') &&
                $locations->contains('description', 'Proyek 3');
        });
    }
}
