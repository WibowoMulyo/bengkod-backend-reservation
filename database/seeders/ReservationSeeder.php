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
            ],
            [
                'code' => 'RSV002',
                'status' => 'Terverifikasi',
                'type' => 'Kelompok',
                'purpose' => 'Diskusi',
                'time_slot' => '10:00-12:00',
                'date' => Carbon::create(2024, 10, 28)->toDateString(),
                'table_id' => 1,
            ],
            [
                'code' => 'RSV003',
                'status' => 'Terverifikasi',
                'type' => 'Kelompok',
                'purpose' => 'Diskusi',
                'time_slot' => '08:00-10:00',
                'date' => Carbon::create(2024, 10, 29)->toDateString(),
                'table_id' => 1,
            ],
            [
                'code' => 'RSV004',
                'status' => 'Terverifikasi',
                'type' => 'Kelompok',
                'purpose' => 'Diskusi',
                'time_slot' => '10:00-12:00',
                'date' => Carbon::create(2024, 10, 30)->toDateString(),
                'table_id' => 2,
            ],
            [
                'code' => 'RSV005',
                'status' => 'Selesai',
                'type' => 'Individu',
                'purpose' => 'Penelitian',
                'time_slot' => '13:00-15:00',
                'date' => Carbon::now()->startOfWeek()->addDay(1)->toDateString(),
                'table_id' => 2,
            ],
            [
                'code' => 'RSV006',
                'status' => 'Selesai',
                'type' => 'Individu',
                'purpose' => 'Penelitian',
                'time_slot' => '08:00-10:00',
                'date' => Carbon::now()->startOfWeek()->addDay(2)->toDateString(),
                'table_id' => 1,
            ],
            [
                'code' => 'RSV007',
                'status' => 'Selesai',
                'type' => 'Individu',
                'purpose' => 'Penelitian',
                'time_slot' => '13:00-15:00',
                'date' => Carbon::now()->startOfWeek()->addDay(3)->toDateString(),
                'table_id' => 2,
            ],
            [
                'code' => 'RSV008',
                'status' => 'Selesai',
                'type' => 'Individu',
                'purpose' => 'Penelitian',
                'time_slot' => '13:00-15:00',
                'date' => Carbon::now()->startOfWeek()->addDay(3)->toDateString(),
                'table_id' => 1,
            ],
            [
                'code' => 'RSV009',
                'status' => 'Selesai',
                'type' => 'Individu',
                'purpose' => 'Penelitian',
                'time_slot' => '10:00-12:00',
                'date' => Carbon::now()->startOfWeek()->addDay(2)->toDateString(),
                'table_id' => 1,
            ],
            [
                'code' => 'RSV010',
                'status' => 'Selesai',
                'type' => 'Individu',
                'purpose' => 'Penelitian',
                'time_slot' => '15:00-17:00',
                'date' => Carbon::now()->startOfWeek()->addDay(3)->toDateString(),
                'table_id' => 2,
            ],
            [
                'code' => 'RSV011',
                'status' => 'Selesai',
                'type' => 'Individu',
                'purpose' => 'Penelitian',
                'time_slot' => '08:00-10:00',
                'date' => Carbon::now()->startOfWeek()->addDay(3)->toDateString(),
                'table_id' => 1,
            ],
            [
                'code' => 'RSV012',
                'status' => 'Selesai',
                'type' => 'Individu',
                'purpose' => 'Penelitian',
                'time_slot' => '08:00-10:00',
                'date' => Carbon::now()->startOfWeek()->addDay(4)->toDateString(),
                'table_id' => 1,
            ],
        ]);
        
    }
}
