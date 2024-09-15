<?php

namespace App\Console\Commands;

use App\Http\Controllers\FlightController;
use Illuminate\Console\Command;

class AddDaysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return  FlightController::addDays();
    }
}
