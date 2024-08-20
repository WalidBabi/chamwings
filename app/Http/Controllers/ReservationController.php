<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReservationRequest;
use App\Models\Airplane;
use App\Models\ClassM;
use App\Models\Flight;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{

    public function search(Request $request)
    {

        $request->validate([
            'trip_type' => 'required|in:0,1',
            'booking_preference' => 'required|in:a,b,c',
            'class' => 'required|in:economy,business',
            'adults' => 'required|integer|min:0|max:9',
            'infants' => 'required|integer|min:0|max:8',
            'departure_airport' => 'required|exists:airports,airport_id',
            'arrival_airport' => 'required|exists:airports,airport_id|different:departure_airport',
            'departure_date' => 'required|date',
            'arrival_date' => 'nullable|date|after_or_equal:departure_date',
        ]);
        // dd($request);
        $query = Flight::query()
        ->where('flights.departure_airport', $request->departure_airport)
        ->where('flights.arrival_airport', $request->arrival_airport)
        ->join('schedule_days', 'flights.flight_id', '=', 'schedule_days.flight_id')
        ->where('schedule_days.departure_date', $request->departure_date)
        ->where('schedule_days.arrival_date', $request->arrival_date)
        ->join('schedule_times', 'flights.flight_id', '=', 'schedule_times.flight_id')
        ->join('airplanes', 'flights.airplane_id', '=', 'airplanes.airplane_id')
        ->join('airports as departure_airport', 'flights.departure_airport', '=', 'departure_airport.airport_id')
        ->join('airports as arrival_airport', 'flights.arrival_airport', '=', 'arrival_airport.airport_id')
        ->join('classes as economy_class', function($join) {
            $join->on('airplanes.airplane_id', '=', 'economy_class.airplane_id')
                 ->where('economy_class.class_name', 'Economy');
        })
        ->join('classes as business_class', function($join) {
            $join->on('airplanes.airplane_id', '=', 'business_class.airplane_id')
                 ->where('business_class.class_name', 'Business');
        })
        ->select(
            'flights.flight_id',
            'flights.airplane_id',
            'flights.departure_airport',
            'flights.arrival_airport',
            'flights.flight_number',
            'flights.number_of_reserved_seats',
            'flights.price',
            'flights.departure_terminal',
            'flights.arrival_terminal',
            'flights.created_at',
            'flights.updated_at',
            'schedule_days.schedule_day_id',
            'schedule_days.departure_date',
            'schedule_days.arrival_date',
            'schedule_times.schedule_time_id',
            'schedule_times.departure_time',
            'schedule_times.arrival_time',
            'schedule_times.duration',
            'airplanes.model',
            'airplanes.manufacturer',
            'airplanes.range',
            'departure_airport.airport_id as departure_airport_id',
            'departure_airport.airport_name as departure_airport_name',
            'departure_airport.airport_code as departure_airport_code',
            'departure_airport.city as departure_city',
            'departure_airport.country as departure_country',
            'arrival_airport.airport_id as arrival_airport_id',
            'arrival_airport.airport_name as arrival_airport_name',
            'arrival_airport.airport_code as arrival_airport_code',
            'arrival_airport.city as arrival_city',
            'arrival_airport.country as arrival_country',
            'economy_class.price_rate as economyPrice',
            'business_class.price_rate as businessPrice',
            'economy_class.weight_allowed as economyWeight',
            'economy_class.number_of_meals as economyMeals',
            'business_class.weight_allowed as businessWeight',
            'business_class.number_of_meals as businessMeals'
        );
        // $classquery = ClassM::query()->select('class_name','price_rate','weight_allowed','number_of_meals','cabin_weight');
        // $class =$classquery->get();
        $flights = $query->get();
        // dd($aircraft);
        // Handle round trip
        // if ($request->trip_type == '1' && $request->arrival_date) {
        //     $returnQuery = Flight::query()
        //         ->where('departure_airport', $request->arrival_airport)
        //         ->where('arrival_airport', $request->departure_airport)
        //         ->whereDate('departure_date', $request->arrival_date)
        //         ->where('class', $request->class);

        //     $query = $query->union($returnQuery);
        // }

        // Apply booking preference logic
        // if ($request->booking_preference == 'b') {
        //     $seatsQuery = Seat::query()
        //     ->where('flights.departure_airport',$request->departure_airport)
        //     // For me: Limit to 1 adult, no infants
        //     $flights = $flights->where('available_seats', '>=', 1);
        // } else {
        //     // For me and companions or For others only
        //     $totalPassengers = $request->adults + $request->infants;
        //     $flights = $flights->where('available_seats', '>=', $totalPassengers);
        // }

        if ($request->trip_type == 0) {
            $triptype = 'inbound';
        } else {
            $triptype = 'outbound';
        }

        return response()->json([
            'trip_type' => $triptype,
            'adults' => $request->adults,
            'infants' => $request->infants,
            'booking_preference' => $request->booking_preference,
            // 'class'=>$class,
            'flights' => $flights

        ]);
    }

    /*********************************** Need Editing ***********************************/

    // //Create Reservation Function
    // public function createReservation(CreateReservationRequest $createReservationRequest)
    // {
    //     Reservation::create([
    //         'user_id' => Auth::guard('user')->user()->user_id,
    //         'number_of_passengers' => $createReservationRequest->number_of_passengers,
    //         'reservation_date' => $createReservationRequest->reservation_date,
    //         'round_trip' => $createReservationRequest->round_trip,
    //         'status' => $createReservationRequest->status,
    //     ]);

    //     return success(null, 'this reservation created successfully', 201);
    // }

    // //Update Reservation Function
    // public function updateReservation(Reservation $reservation, CreateReservationRequest $createReservationRequest)
    // {
    //     $reservation->update([
    //         'number_of_passengers' => $createReservationRequest->number_of_passengers,
    //         'reservation_date' => $createReservationRequest->reservation_date,
    //         'round_trip' => $createReservationRequest->round_trip,
    //         'status' => $createReservationRequest->status,
    //     ]);

    //     return success(null, 'this reservation updated successfully');
    // }

    // //Get Reservations Function
    // public function getReservations()
    // {
    //     $reservations = Reservation::with('user')->get();

    //     return success($reservations, null);
    // }

    // //Get Reservation Information Function
    // public function getReservationInformation(Reservation $reservation)
    // {
    //     return success($reservation->with(['user.employee', 'flights.departureAirport', 'flights.arrivalAirport', 'flights.airplane'])->find($reservation->reservation_id), null);
    // }
}
