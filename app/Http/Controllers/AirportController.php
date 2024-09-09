<?php

namespace App\Http\Controllers;

use App\Http\Requests\AirportRequest;
use App\Models\Airport;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AirportController extends Controller
{
    //Add Airport Function
    public function addAirport(AirportRequest $airportRequest)
    {
        $user = Auth::guard('user')->user();
        Airport::create([
            'airport_name' => $airportRequest->airport_name,
            'city' => $airportRequest->city,
            'country' => $airportRequest->country,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added new airport to system its name ' . $airportRequest->airport_name,
            'type' => 'insert',
        ]);

        return success(null, 'this airport added successfully', 201);
    }

    //Edit Airport Function
    public function editAirport(Airport $airport, AirportRequest $airportRequest)
    {
        $user = Auth::guard('user')->user();

        $airport->update([
            'airport_name' => $airportRequest->airport_name,
            'city' => $airportRequest->city,
            'country' => $airportRequest->country,
        ]);
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' updated airport its name ' . $airportRequest->airport_name,
            'type' => 'update',
        ]);
        return success(null, 'this airport updated successfully');
    }

    //Delete Airport Function
    public function deleteAirport(Airport $airport)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted airport from system its name ' . $airport->airport_name,
            'type' => 'delete',
        ]);
        $airport->delete();

        return success(null, 'this airport deleted successfully');
    }

    //Get Airports Function
    public function getAirports()
    {
        $airports = Airport::paginate(15);

        $data = [
            'data' => $airports->items(),
            'total' => $airports->total(),
        ];

        return success($data, null);
    }

    //Get Airport Information Function
    public function getAirportInformation(Airport $airport)
    {
        return success($airport, null);
    }
}