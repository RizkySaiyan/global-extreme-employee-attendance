<?php

namespace App\Console\Commands;

use App\Jobs\Employee\TestCronUpdateEmployeeJobs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCronServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-cron-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        TestCronUpdateEmployeeJobs::dispatch();
    }
}
