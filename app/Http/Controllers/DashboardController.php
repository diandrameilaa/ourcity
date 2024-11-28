<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Laporan
        $reportStats = [
            'total' => DB::table('reports')->count(),
            'diajukan' => DB::table('reports')->where('status', 'diajukan')->count(),
            'diproses' => DB::table('reports')->where('status', 'diproses')->count(),
            'selesai' => DB::table('reports')->where('status', 'selesai')->count(),
        ];

        // Statistik Proyek
        $projectStats = [
            'planned' => DB::table('projects')->where('status', 'planned')->count(),
            'in_progress' => DB::table('projects')->where('status', 'in_progress')->count(),
            'completed' => DB::table('projects')->where('status', 'completed')->count(),
        ];

        // Analitik Aktivitas Pengguna
        $userActivityStats = [
            'total_reports' => DB::table('reports')->count(),
            'weekly_reports' => DB::table('reports')
                ->where('created_at', '>=', Carbon::now()->subWeek())
                ->count(),
        ];

        // Laporan Aktivitas Mingguan
        $activityReports = [
            'weekly_reports' => DB::table('reports')
                ->where('created_at', '>=', Carbon::now()->subWeek())
                ->count(),
            'weekly_projects' => DB::table('projects')
                ->where('created_at', '>=', Carbon::now()->subWeek())
                ->count(),
        ];

        // Lokasi Laporan (menyesuaikan struktur data dengan lokasi yang ada)
        $reportLocations = DB::table('reports')
            ->select('latitude', 'longitude', 'description') // Pastikan kolom latitude dan longitude ada
            ->get();

        // Lokasi Proyek (menyesuaikan struktur data dengan lokasi yang ada)
        $projectLocations = DB::table('projects')
            ->select('latitude', 'longitude', 'description') // Pastikan kolom latitude dan longitude ada
            ->get();

        return view('dashboard', compact('reportStats', 'projectStats', 'userActivityStats', 'activityReports', 'reportLocations', 'projectLocations'));
    }
}
