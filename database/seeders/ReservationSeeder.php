<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        DB::table('reservations')->insert([
            [
                'code' => 'RSV001',
                'status' => 'Menunggu',
                'type' => 'Individu',
                'purpose' => 'Belajar Mandiri',
                'time_slot' => '08:00-10:00',
                'date' => Carbon::create(2024, 10, 28)->toDateString(),
                'table_id' => 1,
                'expires_at' => Carbon::create(2024, 10, 28, 10, 0, 0), // Contoh: setelah slot selesai
            ],
            [
                'code' => 'RSV002',
                'status' => 'Terverifikasi',
                'type' => 'Kelompok',
                'purpose' => 'Diskusi',
                'time_slot' => '10:00-12:00',
                'date' => Carbon::create(2024, 10, 28)->toDateString(),
                'table_id' => 1,
                'expires_at' => Carbon::create(2024, 10, 28, 12, 0, 0),
            ],
        ]);
    }
}
