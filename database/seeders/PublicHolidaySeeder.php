<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            [
                'name' => 'Tahun Baru 2024 Masehi',
                'date' => '2024-01-01',

            ],
            [
                'name' => 'Isra Miraj Nabi Muhammad',
                'date' => '2024-02-08'
            ],
            [
                'name' => 'Tahun Baru Imlek 2575',
                'date' => '2024-02-10'
            ],
            [
                'name' => 'Hari Raya Nyepi Tahun Baru Saka 1946',
                'date' => '2024-03-11'
            ],
            [
                'name' => 'Wafat Isa Almasih',
                'date' => '2024-03-29'
            ],
            [
                'name' => 'Hari Paskah',
                'date' => '2024-03-31'
            ],
            [
                'name' => 'Hari Raya Idul Fitri 1445H',
                'date' => '2024-04-10'
            ],
            [
                'name' => 'Hari Raya Idul Fitri 1445H',
                'date' => '2024-04-11'
            ],
            [
                'name' => 'Hari Buruh Internasional',
                'date' => '2024-05-01'
            ],
            [
                'name' => 'Kenaikan Isa Almasih',
                'date' => '2024-05-09'
            ],
            [
                'name' => 'Hari Raya Waisak 2568',
                'date' => '2024-05-23'
            ],
            [
                'name' => 'Hari Lahir Pancasila',
                'date' => '2024-06-01'
            ],
            [
                'name' => 'Hari Raya Idul Adha 1445H',
                'date' => '2024-06-17'
            ],
            [
                'name' => 'Tahun Baru Islam 1446H',
                'date' => '2024-07-07'
            ],
            [
                'name' => 'Hari Kemerdekaan RI',
                'date' => '2024-08-17'
            ],
            [
                'name' => 'Maulid Nabi Muhammad',
                'date' => '2024-08-10'
            ],
            [
                'name' => 'Hari Raya Natal',
                'date' => '2024-12-25'
            ],
        ];

        foreach ($holidays as $holiday) {
            DB::table('attendance_public_holidays')->insert([
                'name' => $holiday['name'],
                'date' => $holiday['date'],
                'createdBy' => 'System',
                'createdByName' => 'System',
                'createdAt' => now(),
                'updatedAt' => now()
            ]);
        }
    }


    /** --- FUNCTIONS --- */

    private function getData()
    {
        return [];
    }
}
