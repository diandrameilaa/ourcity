<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Jalankan migrasi
        $this->artisan('migrate');

        // Buat user dummy
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
    public function dashboard_displays_correct_statistics_and_data()
    {
        // Simulasi login sebagai user
        Auth::loginUsingId(1);

        // Masukkan data laporan (reports)
        DB::table('reports')->insert([
            [
                'user_id' => 1,
                'status' => 'diajukan',
                'created_at' => now(),
                'latitude' => -7.2575,
                'longitude' => 112.752,
                'description' => 'Laporan 1',
                'location' => 'Surabaya',
            ],
            [
                'user_id' => 1,
                'status' => 'diproses',
                'created_at' => now()->subDays(3),
                'latitude' => -7.2585,
                'longitude' => 112.753,
                'description' => 'Laporan 2',
                'location' => 'Jakarta',
            ],
            [
                'user_id' => 1,
                'status' => 'selesai',
                'created_at' => now()->subDays(6), // Perbaikan agar masuk hitungan weekly_reports
                'latitude' => -7.2595,
                'longitude' => 112.754,
                'description' => 'Laporan 3',
                'location' => 'Bandung',
            ],
        ]);

        // Masukkan data proyek (projects)
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
                'created_at' => now()->subDays(6),
                'latitude' => -7.2595,
                'longitude' => 112.754,
                'description' => 'Proyek 3',
                'location' => 'Bandung',
            ],
        ]);

        // Akses endpoint dashboard
        $response = $this->get(route('dashboard'));

        // Debug: Cek data laporan mingguan
        $weeklyReports = DB::table('reports')
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->count();
        
        $weeklyProjects = DB::table('projects')
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->count();
        
        dump("Weekly Reports: $weeklyReports, Weekly Projects: $weeklyProjects");

        // Pastikan response OK
        $response->assertStatus(200);
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

        // Cek aktivitas pengguna
        $response->assertViewHas('userActivityStats', [
            'total_reports' => 3,
            'weekly_reports' => 3,
        ]);

        // Cek aktivitas mingguan
        $response->assertViewHas('activityReports', [
            'weekly_reports' => 3,
            'weekly_projects' => 3,
        ]);
    }
}
