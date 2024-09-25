<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReservationController;
use Illuminate\Console\Command;

class CheckExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:expiry';

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
        $reservationController = new ReservationController();
        $reservationController->checkExpiry();
    }
}
