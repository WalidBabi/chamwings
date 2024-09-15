<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFlightRequest;
use App\Models\Flight;
use App\Models\Log;
use App\Models\Reservation;
use App\Models\ScheduleDay;
use App\Models\ScheduleTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlightController extends Controller
{
    //Create Flight Function
    public function createFlight(CreateFlightRequest $createFlightRequest)
    {
        $user = Auth::guard('user')->user();
        Flight::create([
            'airplane_id' => $createFlightRequest->airplane_id,
            'departure_airport' => $createFlightRequest->departure_airport,
            'arrival_airport' => $createFlightRequest->arrival_airport,
            'flight_number' => $createFlightRequest->flight_number,
            'price' => $createFlightRequest->price,
            'departure_terminal' => $createFlightRequest->departure_terminal,
            'arrival_terminal' => $createFlightRequest->arrival_terminal,
            // 'duration' => $createFlightRequest->duration,
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
            // 'duration' => $createFlightRequest->duration,
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
        $query = Flight::withTrashed()->with(['departureAirport', 'arrivalAirport', 'airplane']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('departureAirport', function ($query) use ($search) {
                    $query->where('airport_name', 'LIKE', '%' . $search . '%');
                })->orWhereHas('arrivalAirport', function ($query) use ($search) {
                    $query->where('airport_name', 'LIKE', '%' . $search . '%');
                })->orWhere('price', $search)
                  ->orWhere('departure_terminal', 'LIKE', '%' . $search . '%')
                  ->orWhere('arrival_terminal', 'LIKE', '%' . $search . '%');
            });
        } else {
            $query->addSelect(['duration' => function($subquery) {
                $subquery->select('duration')
                    ->from('schedule_times')
                    ->whereColumn('flight_id', 'flights.flight_id')
                    ->limit(1);
            }]);
        }

        $flights = $query->orderBy('flight_id', 'desc')->paginate(15);
        
        $data = [
            'data' => $flights->items(),
            'total' => $flights->total(),
        ];

        return success($data, null);
    }

    //Get All Flights Function
    public function getAllFlights()
    {
        $flights = Flight::with(['departureAirport', 'arrivalAirport', 'airplane'])->get();
        return success($flights, null);
    }

    //Get Flight Information Function
    public function getFlightInformation(Flight $flight)
    {
        return success($flight->with(['departureAirport', 'arrivalAirport', 'airplane'])->find($flight->flight_id), null);
    }

    public function activateFlight($flight)
    {
        $flight = Flight::withTrashed()->find($flight);
        if (!$flight) {
            return error(null, null, 404);
        }
        $flight->deleted_at = null;
        $flight->update();

        return success(null, 'this flight activated successfully');
    }


    //Add 7 Days To Departure And Arrival Date Function
    public static function addDays()
    {
        $flights = Flight::whereBetween('flight_id', [1, 10])->get();

        foreach ($flights as $flight) {
            foreach ($flight->days as $day) {
                $departure_date = Carbon::parse($day->departure_date)->addDays(7)->format('Y-m-d');
                $arrival_date = Carbon::parse($day->arrival_date)->addDays(7)->format('Y-m-d');
                $check = $flight->days()->where('departure_date', $departure_date)->first();
                if (!$check) {
                    $schedule_day = ScheduleDay::create([
                        'flight_id' => $flight->flight_id,
                        'departure_date' => $departure_date,
                        'arrival_date' => $arrival_date,
                    ]);
                    foreach ($day->times as $time) {
                        $duration = Carbon::parse($time->duration)->addDays(7)->format('Y-m-d');
                        ScheduleTime::create([
                            'schedule_day_id' => $schedule_day->schedule_day_id,
                            'departure_time' => $time->departure_time,
                            'arrival_time' => $time->arrival_time,
                            'duration' => $duration,
                        ]);
                    }
                }
            }
        }
    }
}
