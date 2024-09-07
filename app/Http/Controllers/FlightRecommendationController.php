<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\FlightRecommendation;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FlightRecommendationController extends Controller
{
    public function getRecommendations($userId)
    {
        // Verify the userId
        // dd((int)$userId);
        $userId = (int)$userId;
        // Check if the user exists
        $user = User::where('user_id', (int)$userId)->first();
        // dd($user);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Generate new recommendations
        Artisan::call('recommendations:generate', ['userId' => $userId]);

        // Retrieve the latest flight recommendations for the user
        $latestRecommendations = $user->flightRecommendations()
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
    }
}
