<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFlightRequest;
use App\Models\Flight;
use App\Models\Reservation;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    //Create Flight Function
    public function createFlight(CreateFlightRequest $createFlightRequest)
    {
        Flight::create([
            'airplane_id' => $createFlightRequest->airplane_id,
            'departure_airport' => $createFlightRequest->departure_airport,
            'arrival_airport' => $createFlightRequest->arrival_airport,
            'flight_number' => $createFlightRequest->flight_number,
            'price' => $createFlightRequest->price,
            'departure_terminal' => $createFlightRequest->departure_terminal,
            'arrival_terminal' => $createFlightRequest->arrival_terminal,
        ]);

        return success(null, 'this flight created successfully', 201);
    }

    //Update Flight Function
    public function updateFlight(Flight $flight, CreateFlightRequest $createFlightRequest)
    {
        $flight->update([
            'airplane_id' => $createFlightRequest->airplane_id,
            'departure_airport' => $createFlightRequest->departure_airport,
            'arrival_airport' => $createFlightRequest->arrival_airport,
            'flight_number' => $createFlightRequest->flight_number,
            'price' => $createFlightRequest->price,
            'departure_terminal' => $createFlightRequest->departure_terminal,
            'arrival_terminal' => $createFlightRequest->arrival_terminal,
        ]);

        return success(null, 'this flight updated successfully');
    }

    //Delete Flight Function
    public function deleteFlight(Flight $flight)
    {
        $flight->delete();

        return success(null, 'this flight deleted successfully');
    }

    //Get Flights Function
    public function getFlights()
    {
        $flights = Flight::with(['departureAirport', 'arrivalAirport', 'airplane'])->paginate(15);

        return success($flights, null);
    }

    //Get Flight Information Function
    public function getFlightInformation(Flight $flight)
    {
        return success($flight->with(['departureAirport', 'arrivalAirport', 'airplane'])->find($flight->flight_id), null);
    }
}