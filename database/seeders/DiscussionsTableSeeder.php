<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscussionsTableSeeder extends Seeder
{
    public function run()
    {
        // Insert sample discussions using Query Builder
        DB::table('discussions')->insert([
            [
                'user_id' => 1,  // Assuming user with ID 1 exists
                'message' => 'Hello, I have a question about the new project proposal.',
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,  // Assuming user with ID 2 exists
                'message' => 'I noticed some issues in the recent update, can we discuss it?',
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,  // Assuming user with ID 3 exists
                'message' => 'Is there a meeting scheduled for next week to discuss the status?',
                'is_deleted' => true, // Message deleted by admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,  // Assuming user with ID 1 exists
                'message' => 'Can anyone help me with the new project requirements?',
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
