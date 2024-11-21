<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsTableSeeder extends Seeder
{
    public function run()
    {
        // Insert sample notifications using Query Builder
        DB::table('notifications')->insert([
            [
                'user_id' => 1,  // Assuming user with ID 1 exists
                'title' => 'New Project Announcement',
                'message' => 'There is a new project available for you to join. Please check the details.',
                'is_sent' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,  // Assuming user with ID 2 exists
                'title' => 'Project Update',
                'message' => 'The project you are working on has been updated. Please review the new requirements.',
                'is_sent' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,  // Notification for all users (user_id is NULL)
                'title' => 'System Maintenance',
                'message' => 'The system will be undergoing maintenance from 2 AM to 4 AM tomorrow. Please plan accordingly.',
                'is_sent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,  // Assuming user with ID 3 exists
                'title' => 'Reminder: Project Deadline Approaching',
                'message' => 'The deadline for the project you are working on is approaching. Please ensure all tasks are completed.',
                'is_sent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
