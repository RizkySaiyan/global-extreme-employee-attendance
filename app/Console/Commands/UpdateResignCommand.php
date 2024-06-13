<?php

namespace App\Console\Commands;

use App\Models\Employee\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateResignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-resign-command';

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
        //
        $today = Carbon::today()->format('Y-m-d');

        Employee::whereHas('resign', function ($query) use ($today) {
            $query->where('date', '<=', $today);
        })->update(['isResign' => true]);

        $this->info('Employee resign status updated successfully.');
    }
}
