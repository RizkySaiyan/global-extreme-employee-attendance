<?php

namespace App\Console\Commands;

use App\Models\Employee\Employee;
use App\Services\Number\Generator\EmployeeNumber;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'dev-test';
    protected $description = '';

    public function handle()
    {
        $existingEmployee = Employee::whereHas('user', function($query){
            $query->where('email','rizkyrmdhn09@gmail.com');
        })->first();

        $resign = $existingEmployee->resign;
        if(Carbon::parse($resign->date)->diffInYears(now()) < 1){
            $this->updateStatusResign($existingEmployee);
        }
    }

    private function updateStatusResign($employee){
        $employee->resign()->update([
            'isResign' => false
        ]);
    }
}
