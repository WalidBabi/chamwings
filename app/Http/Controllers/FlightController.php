<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFlightRequest;
use App\Models\Flight;
use App\Models\Log;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlightController extends Controller
{
    //Create Flight Function
    public function createFlight(CreateFlightRequest $createFlightRequest)
    {
        $user = Auth::guard('user')->user();
        $flight = Flight::create([
            'airplane_id' => $createFlightRequest->airplane_id,
            'departure_airport' => $createFlightRequest->departure_airport,
            'arrival_airport' => $createFlightRequest->arrival_airport,
            'flight_number' => $createFlightRequest->flight_number,
            'price' => $createFlightRequest->price,
            'departure_terminal' => $createFlightRequest->departure_terminal,
            'arrival_terminal' => $createFlightRequest->arrival_terminal,
            'duration' => $createFlightRequest->duration,
            'miles' => $createFlightRequest->miles,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' create new flight from ' . $flight->departureAirport->airport_name . ' to ' . $flight->arrivalAirport->airport_name,
            'type' => 'insert',
        ]);

        return success(null, 'this flight created successfully', 201);
    }

    //Update Flight Function
    public function updateFlight(Flight $flight, CreateFlightRequest $createFlightRequest)
    {
        $user = Auth::guard('user')->user();
        $flight->update([
            'airplane_id' => $createFlightRequest->airplane_id,
            'departure_airport' => $createFlightRequest->departure_airport,
            'arrival_airport' => $createFlightRequest->arrival_airport,
            'flight_number' => $createFlightRequest->flight_number,
            'price' => $createFlightRequest->price,
            'departure_terminal' => $createFlightRequest->departure_terminal,
            'arrival_terminal' => $createFlightRequest->arrival_terminal,
            'duration' => $createFlightRequest->duration,
            'miles' => $createFlightRequest->miles,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' updated flight witch from ' . $flight->departureAirport->airport_name . ' to ' . $flight->arrivalAirport->airport_name,
            'type' => 'update',
        ]);

        return success(null, 'this flight updated successfully');
    }

    //Delete Flight Function
    public function deleteFlight(Flight $flight)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted flight from ' . $flight->departureAirport->airport_name . ' to ' . $flight->arrivalAirport->airport_name,
            'type' => 'delete',
        ]);
        $flight->delete();
        return success(null, 'this flight deleted successfully');
    }

    //Get Flights Function
    public function getFlights(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $flights = Flight::whereHas('departureAirport', function ($query) use ($search) {
                $query->where('airport_name', 'LIKE', '%' . $search . '%');
            })->orWhereHas('arrivalAirport', function ($query) use ($search) {
                $query->where('airport_name', 'LIKE', '%' . $search . '%');
            })->orWhere('price', $search)->orWhere('departure_terminal', 'LIKE', '%' . $search . '%')
                ->orWhere('arrival_terminal', 'LIKE', '%' . $search . '%')
                ->with(['departureAirport', 'arrivalAirport', 'airplane'])->paginate(15);
        } else {
            $flights = Flight::with(['departureAirport', 'arrivalAirport', 'airplane'])->paginate(15);
        }

        $data = [
            'data' => $flights->items(),
            'total' => $flights->total(),
        ];

        return success($data, null);
    }

    //Get Flight Information Function
    public function getFlightInformation(Flight $flight)
    {
        return success($flight->with(['departureAirport', 'arrivalAirport', 'airplane'])->find($flight->flight_id), null);
    }
}