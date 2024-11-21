<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Insert 2 admin users using Query Builder
        DB::table('users')->insert([
            [
                'name' => 'Diandra',
                'email' => 'dianndraaa00@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert 3 warga (citizen) users using Query Builder
        DB::table('users')->insert([
            [
                'name' => 'faiza',
                'email' => 'faizatun.nimah.410724-2023@vokasi.unair.ac.id',
                'password' => Hash::make('password'),
                'role' => 'warga',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'festi',
                'email' => 'festianaramaya@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'warga',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bella',
                'email' => 'bellarahmaanrs@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'warga',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
