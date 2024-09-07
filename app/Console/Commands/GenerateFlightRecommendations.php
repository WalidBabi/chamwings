<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateFlightRecommendations extends Command
{
    protected $signature = 'recommendations:generate {userId}';
    protected $description = 'Generate flight recommendations for a user';

    public function handle()
    {
        $userId = $this->argument('userId');
        // dd($userId);
        $pythonScript = 'C:/Users/waled/Desktop/chamwings/app/Python/FlightRecommendation.py';
        $pythonPath = 'C:/Users/waled/AppData/Local/Programs/Python/Python312/python.exe';

        $command = escapeshellcmd("$pythonPath $pythonScript $userId");
        // dd($command);
        $output = shell_exec($command);
        // dd($output);
        if ($output === null) {
            $this->error("Python script execution failed.");
            return;
        }

        // Remove escape sequences and extra whitespace
        $cleanOutput = stripslashes(trim($output));
        // dd($cleanOutput);
        // Decode the JSON
        $json = json_decode($cleanOutput, true);

        if (!is_array($json)) {
            $this->error("Failed to decode JSON or result is not an array.");
            return;
        }

        // Fetch airport images
        $airportImages = \DB::table('airports')->pluck('image', 'airport_id')->toArray();

        // Add image attribute to each flight
        foreach ($json as &$flight) {
            $arrivalAirportId = $flight['arrival_airport'];
            $flight['image'] = $airportImages[$arrivalAirportId] ?? null;
        }

        $flight_recommendations = [
            'user_id' => $userId,
            'recommended_flights' => json_encode($json),
            'created_at' => now()
        ];
        \DB::table('flight_recommendations')->insert($flight_recommendations);

    }
}
