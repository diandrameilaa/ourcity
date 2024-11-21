<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('reports')->insert([
            [
                'user_id' => 2, // ID pengguna admin pertama
                'description' => 'Laporan mengenai kerusakan jalan di daerah pusat kota.',
                'photo' => 'kerusakan_jalan.jpg',
                'location' => 'Jalan Sudirman, Jakarta',
                'status' => 'diproses',
                'longitude' => '106.8272',
                'latitude' => '-6.1751',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3, // ID pengguna warga pertama
                'description' => 'Lampu jalan mati di sekitar taman kota.',
                'photo' => 'lampu_jalan_mati.jpg',
                'location' => 'Taman Bungkul, Surabaya',
                'status' => 'selesai',
                'longitude' => '107.6010',
                'latitude' => '-7.2575',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4, // ID pengguna warga kedua
                'description' => 'Pohon tumbang akibat angin kencang.',
                'photo' => 'pohon_tumbang.jpg',
                'location' => 'Jalan Diponegoro, Bandung',
                'status' => 'diajukan',
                'longitude' => '107.6191',
                'latitude' => '-6.9175',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
