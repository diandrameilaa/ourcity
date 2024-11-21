<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportsTableSeeder extends Seeder
{
    public function run()
    {
        // Insert sample reports using Query Builder
        DB::table('reports')->insert([
            [
                'user_id' => 3,  // Assuming user with ID 1 exists (admin)
                'description' => 'This is a description for report 1.',
                'photo' => 'photo1.jpg',
                'location' => 'Location 1',
                'status' => 'diproses',
                'longitude' => '106.8272', // Example longitude
                'latitude' => '-6.1751',   // Example latitude
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,  // Assuming user with ID 2 exists (admin)
                'description' => 'This is a description for report 2.',
                'photo' => 'photo2.jpg',
                'location' => 'Location 2',
                'status' => 'selesai',
                'longitude' => '107.6010', // Example longitude
                'latitude' => '-6.2088',   // Example latitude
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,  // Assuming user with ID 3 exists (warga)
                'description' => 'This is a description for report 3.',
                'photo' => 'photo3.jpg',
                'location' => 'Location 3',
                'status' => 'diproses',
                'longitude' => '108.9390', // Example longitude
                'latitude' => '-6.1512',   // Example latitude
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
