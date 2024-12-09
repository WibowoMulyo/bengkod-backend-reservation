<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailReservationSeeder extends Seeder
{
    public function run()
    {
        DB::table('detail_reservations')->insert([
            [
                'user_id' => 1,
                'reservation_id' => 1,
                'status' => 'Terkonfirmasi'
            ],
        ]);
    }
}
