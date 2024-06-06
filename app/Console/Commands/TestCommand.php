<?php

namespace App\Console\Commands;

use App\Services\Number\Generator\EmployeeNumber;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'dev-test';
    protected $description = '';

    public function handle()
    {
        //
        EmployeeNumber::generate();
    }
}
