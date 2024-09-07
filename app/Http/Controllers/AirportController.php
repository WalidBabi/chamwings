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
        // dd($airportRequest);
        // Handle image upload
        if ($airportRequest->hasFile('image')) {
            $image = $airportRequest->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('CountriesImages'), $imageName);
        }

        Airport::create([
            'airport_name' => $airportRequest->airport_name,
            'airport_code' => strtoupper($airportRequest->airport_code),
            'city' => $airportRequest->city,
            'country' => $airportRequest->country,
            'image' => $imageName ?? null,
        ]);

        return success(null, 'This airport added successfully', 201);
    }

    //Edit Airport Function
    public function editAirport(Airport $airport, AirportRequest $airportRequest)
    {
        $oldImage = $airport->image;

        if ($airportRequest->hasFile('image')) {
            $image = $airportRequest->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('CountriesImages'), $imageName);

            // Delete old image if it exists
            if ($oldImage && file_exists(public_path('CountriesImages/' . $oldImage))) {
                unlink(public_path('CountriesImages/' . $oldImage));
            }
        }

        $airport->update([
            'airport_name' => $airportRequest->airport_name,
            'city' => $airportRequest->city,
            'country' => $airportRequest->country,
            'airport_code' => $airportRequest->airport_code,
            'image' => $imageName ?? $airport->image,
        ]);

        return success(null, 'this airport updated successfully');
    }

    //Delete Airport Function
    public function deleteAirport(Airport $airport)
    {
        if ($airport->image) {
            $imagePath = public_path('CountriesImages/' . $airport->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $airport->delete();

        return success(null, 'this airport deleted successfully');
    }

    //Get Airports Function
    public function getAirports()
    {
        $airports = Airport::withTrashed()->orderBy('airport_id', 'desc')->paginate(15);

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