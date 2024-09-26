<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Flight;
use App\Models\Log;
use Carbon\Carbon;

class CheckOverdueFlightsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $now = Carbon::now();

        $overdueFlights = Flight::whereHas('days.times', function ($query) use ($now) {
            $query->where('arrival_time', '<', $now);
        })->whereNull('deleted_at')->get();

        foreach ($overdueFlights as $flight) {
            $flight->delete();
            Log::create([
                'message' => 'Flight ' . $flight->flight_id . ' ended due to overdue arrival time',
                'type' => 'delete',
            ]);
        }
    }
}
