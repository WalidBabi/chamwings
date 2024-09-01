<?php

namespace App\Http\Controllers;

use App\Http\Requests\AirportRequest;
use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    //Add Airport Function
    public function addAirport(AirportRequest $airportRequest)
    {
        Airport::create([
            'airport_name' => $airportRequest->airport_name,
            'city' => $airportRequest->city,
            'country' => $airportRequest->country,
        ]);

        return success(null, 'this airport added successfully', 201);
    }

    //Edit Airport Function
    public function editAirport(Airport $airport, AirportRequest $airportRequest)
    {
        $airport->update([
            'airport_name' => $airportRequest->airport_name,
            'city' => $airportRequest->city,
            'country' => $airportRequest->country,
        ]);

        return success(null, 'this airport updated successfully');
    }

    //Delete Airport Function
    public function deleteAirport(Airport $airport)
    {
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

    public function getAirportsForReservation()
    {
        $airports = Airport::all();
        // dd($airports);
        return success($airports, null);
        
    }

    //Get Airport Information Function
    public function getAirportInformation(Airport $airport)
    {
        return success($airport, null);
    }
    public function activateAirport($airport)
    {
        $airport = Airport::withTrashed()->find($airport);
        if (!$airport) {
            return error(null, null, 404);
        }
        $airport->deleted_at = null;
        $airport->update();

        return success(null, 'this airport activated successfully');
    }
}