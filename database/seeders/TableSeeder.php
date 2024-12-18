<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    public function run()
    {
        DB::table('tables')->insert([
            [
                'table_number' => 'Table-01',
                'total_seats' => 4,
                'available_seats' => 4,
                'thumbnail' => 'table_01_thumbnail.jpg',
                'type' => 'Individu',
                'is_available' => true,
                'type' => 'Individu',
            ],
            [
                'table_number' => 'Table-02',
                'total_seats' => 6,
                'available_seats' => 6,
                'thumbnail' => 'table_02_thumbnail.jpg',
                'type' => 'Individu',
                'is_available' => true,
                'type' => 'Individu',
            ],
            [
                'table_number' => 'Table-03',
                'total_seats' => 2,
                'available_seats' => 2,
                'thumbnail' => 'table_03_thumbnail.jpg',
                'type' => 'Kelompok',
                'is_available' => false,
                'type' => 'Bersama',
            ],
            [
                'table_number' => 'Table-04',
                'total_seats' => 4,
                'available_seats' => 4,
                'thumbnail' => 'table_04_thumbnail.jpg',
                'type' => 'Kelompok',
                'is_available' => true,
                'type' => 'Kelompok',
            ],
        ]);
    }
}
