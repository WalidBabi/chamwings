<?php

namespace App\Console\Commands;

use App\Models\Passenger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateFlightRecommendations extends Command
{
    protected $signature = 'recommendations:generate {passengerID} {country}';
    protected $description = 'Generate flight recommendations for a user';

    public function handle()
    {
    
        $passengerID = $this->argument('passengerID');
        $passenger = Passenger::findOrFail($passengerID);
        $usercountry = $this->argument('country');
        // dd($usercountry);
        $pythonScript = 'C:/Users/waled/Desktop/chamwings/app/Python/FlightRecommendation.py';
        $pythonPath = 'C:/Users/waled/AppData/Local/Programs/Python/Python312/python.exe';

        $command = escapeshellcmd("$pythonPath $pythonScript $passengerID $usercountry");
        // dd($command);
        $output = shell_exec($command);

        if ($output === null) {
            $this->error("Python script execution failed.");
            return;
        }

        $cleanOutput = stripslashes(trim($output));
        $json = json_decode($cleanOutput, true);

        if (!is_array($json)) {
            $this->error("Failed to decode JSON or result is not an array.");
            return;
        }

        $airportImages = DB::table('airports')->pluck('image', 'country')->toArray();
        // dd($airportImages);
        foreach ($json as &$flight) {
            $arrivalAirportId = $flight['arrival_country'];
            $flight['image'] = $airportImages[$arrivalAirportId] ?? null;
        }

        $flight_recommendations = [
            'passenger_id' => $passengerID,
            'recommended_flights' => json_encode($json),
            'created_at' => now()
        ];
        DB::table('flight_recommendations')->insert($flight_recommendations);
    }
    
}
