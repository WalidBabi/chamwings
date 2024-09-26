<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\FlightRecommendation;
use App\Models\Passenger;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FlightRecommendationController extends Controller
{
    public function getRecommendations($passengerID, $country)
    {
        // Verify the passengerID
        // dd((int)$passengerID);
        // dd($country);
        
        $passengerID = (int)$passengerID;
        // Check if the passenger exists
        $passenger = Passenger::where('passenger_id', (int)$passengerID)->first();
        // dd($passenger);
        if (!$passenger) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Generate new recommendations
        Artisan::call('recommendations:generate', ['passengerID' => $passengerID, 'country' => $country]);

        // Retrieve the latest flight recommendations for the passenger
        $latestRecommendations = $passenger->flightRecommendations()
            ->latest('created_at')
            ->first();

        // Check if recommendations were generated
        if (!$latestRecommendations) {
            return response()->json(['error' => 'No recommendations found'], 404);
        }

        // Clean recommendations from backslashes
            $cleanedRecommendations = json_decode(str_replace('\\', '', $latestRecommendations->recommended_flights), true);
        
        return response()->json([
            'message' => 'Recommendations retrieved',
            'recommendations' => $cleanedRecommendations
        ]);
    }}
