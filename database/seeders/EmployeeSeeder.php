<?php

namespace Database\Seeders;

use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeSibling;
use App\Models\Employee\EmployeeUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::factory()
            ->count(12)
            ->has(EmployeeUser::factory()->count(1), 'user')
            ->has(EmployeeSibling::factory()->count(2), 'siblings')
            ->create();
    }


    /** --- FUNCTIONS --- */

    private function getData()
    {
        return array(
            []
        );
    }
}
