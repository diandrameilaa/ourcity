<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectsTableSeeder extends Seeder
{
    public function run()
    {
        // Insert sample projects using Query Builder
        DB::table('projects')->insert([
            [
                'name' => 'Project A',
                'description' => 'This is a description for Project A.',
                'location' => 'Location A',
                'status' => 'planned',
                'longitude' => '105,9729255', // Add longitude
                'latitude' => '-6,0317827',   // Add latitude
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Project B',
                'description' => 'This is a description for Project B.',
                'location' => 'Location B',
                'status' => 'in_progress',
                'longitude' => '111,8990977', // Add longitude
                'latitude' => '-7,612989139',   // Add latitude
                'start_date' => '2024-03-01',
                'end_date' => '2024-09-30',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Project C',
                'description' => 'This is a description for Project C.',
                'location' => 'Location C',
                'status' => 'completed',
                'longitude' => '112,6541348', // Add longitude
                'latitude' => '-7,3056512',   // Add latitude
                'start_date' => '2023-06-15',
                'end_date' => '2024-06-15',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
