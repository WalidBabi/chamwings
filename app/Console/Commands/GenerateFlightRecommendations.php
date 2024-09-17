<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateFlightRecommendations extends Command
{
    protected $signature = 'recommendations:generate {userId} {city}';
    protected $description = 'Generate flight recommendations for a user';

    public function handle()
    {
    
        $userId = $this->argument('userId');
        $user = User::findOrFail($userId);
        $userCity = $this->argument('city');
        // dd($userCity);
        $pythonScript = 'C:/Users/waled/Desktop/chamwings/app/Python/FlightRecommendation.py';
        $pythonPath = 'C:/Users/waled/AppData/Local/Programs/Python/Python312/python.exe';

        $command = escapeshellcmd("$pythonPath $pythonScript $userId $userCity");
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
            'user_id' => $userId,
            'recommended_flights' => json_encode($json),
            'created_at' => now()
        ];
        DB::table('flight_recommendations')->insert($flight_recommendations);
    }
}
