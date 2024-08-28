<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReservationRequest;
use App\Models\Airplane;
use App\Models\ClassM;
use App\Models\Flight;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{

    public function search(Request $request)
    {
        //handle roundtrip
        // if ($request->trip_type == '1' && $request->return_date) {
        //     $query = Flight::query()
        //     ->where('flights.departure_airport', $request->departure_airport)
        //     ->where('flights.arrival_airport', $request->arrival_airport)

        //     ->join('schedule_days', 'flights.flight_id', '=', 'schedule_days.flight_id')
        //     ->where('schedule_days.departure_date', $request->departure_date)
        //     ->join('schedule_times', 'flights.flight_id', '=', 'schedule_times.flight_id')
        //     ->join('airplanes', 'flights.airplane_id', '=', 'airplanes.airplane_id')
        //     ->join('airports as departure_airport', 'flights.departure_airport', '=', 'departure_airport.airport_id')
        //     ->join('airports as arrival_airport', 'flights.arrival_airport', '=', 'arrival_airport.airport_id')
        //     ->join('classes as economy_class', function ($join) {
        //         $join->on('airplanes.airplane_id', '=', 'economy_class.airplane_id')
        //             ->where('economy_class.class_name', 'Economy');
        //     })
        //     ->join('classes as business_class', function ($join) {
        //         $join->on('airplanes.airplane_id', '=', 'business_class.airplane_id')
        //             ->where('business_class.class_name', 'Business');
        //     })
        //     ->select(
        //         'flights.flight_id',
        //         'flights.airplane_id',
        //         'flights.departure_airport',
        //         'flights.arrival_airport',
        //         'flights.flight_number',
        //         'flights.number_of_reserved_seats',
        //         'flights.price',
        //         'flights.departure_terminal',
        //         'flights.arrival_terminal',
        //         'flights.created_at',
        //         'flights.updated_at',
        //         'schedule_days.schedule_day_id',
        //         'schedule_days.departure_date',
        //         'schedule_days.arrival_date',
        //         'schedule_times.schedule_time_id',
        //         'schedule_times.departure_time',
        //         'schedule_times.arrival_time',
        //         'schedule_times.duration',
        //         'airplanes.model',
        //         'airplanes.manufacturer',
        //         'airplanes.range',
        //         'departure_airport.airport_id as departure_airport_id',
        //         'departure_airport.airport_name as departure_airport_name',
        //         'departure_airport.airport_code as departure_airport_code',
        //         'departure_airport.city as departure_city',
        //         'departure_airport.country as departure_country',
        //         'arrival_airport.airport_id as arrival_airport_id',
        //         'arrival_airport.airport_name as arrival_airport_name',
        //         'arrival_airport.airport_code as arrival_airport_code',
        //         'arrival_airport.city as arrival_city',
        //         'arrival_airport.country as arrival_country',
        //         'economy_class.price_rate as economyPrice',
        //         'business_class.price_rate as businessPrice',
        //         'economy_class.weight_allowed as economyWeight',
        //         'economy_class.number_of_meals as economyMeals',
        //         'business_class.weight_allowed as businessWeight',
        //         'business_class.number_of_meals as businessMeals'
        //     );
        //     $flights = $query->get();
        // }
        // else{

        // }

        if ($request->trip_type == '1' && $request->return_date) {
            $departureDate = Carbon::parse($request->departure_date);
            $returnDate = Carbon::parse($request->return_date);

            // Calculate date ranges for departure flights
            $earliestDepartureDate = $departureDate->copy()->subDays(5);
            // dd($earliestDepartureDate);
            $earliestDepartureDate = $earliestDepartureDate->isPast() ? Carbon::now()->startOfDay() : $earliestDepartureDate;
            $latestDepartureDate = $departureDate->copy()->addDays(5);
            // dd($earliestDepartureDate,$latestDepartureDate);
            // Calculate date ranges for return flights
            $earliestReturnDate = $returnDate->copy()->subDays(5);
            $earliestReturnDate = $earliestReturnDate->isPast() ? Carbon::now()->startOfDay() : $earliestReturnDate;
            $latestReturnDate = $returnDate->copy()->addDays(5);

            // Query for Departure Flights within the date range (including the exact date)
            $departureFlightsQuery = Flight::query()
                ->where('flights.departure_airport', $request->departure_airport)
                ->where('flights.arrival_airport', $request->arrival_airport)
                ->join('schedule_days', 'flights.flight_id', '=', 'schedule_days.flight_id')
                ->whereBetween('schedule_days.departure_date', [$earliestDepartureDate, $latestDepartureDate])
                ->join('schedule_times', 'flights.flight_id', '=', 'schedule_times.flight_id')
                ->join('airplanes', 'flights.airplane_id', '=', 'airplanes.airplane_id')
                ->join('airports as departure_airport', 'flights.departure_airport', '=', 'departure_airport.airport_id')
                ->join('airports as arrival_airport', 'flights.arrival_airport', '=', 'arrival_airport.airport_id')
                ->join('classes as economy_class', function ($join) {
                    $join->on('airplanes.airplane_id', '=', 'economy_class.airplane_id')
                        ->where('economy_class.class_name', 'Economy');
                })
                ->join('classes as business_class', function ($join) {
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

            $departureFlights = $departureFlightsQuery->get();

            // Query for Return Flights within the date range (including the exact date)
            $returnFlightsQuery = Flight::query()
                ->where('flights.departure_airport', $request->arrival_airport)
                ->where('flights.arrival_airport', $request->departure_airport)
                ->join('schedule_days', 'flights.flight_id', '=', 'schedule_days.flight_id')
                ->whereBetween('schedule_days.departure_date', [$earliestReturnDate, $latestReturnDate])
                ->join('schedule_times', 'flights.flight_id', '=', 'schedule_times.flight_id')
                ->join('airplanes', 'flights.airplane_id', '=', 'airplanes.airplane_id')
                ->join('airports as departure_airport', 'flights.departure_airport', '=', 'departure_airport.airport_id')
                ->join('airports as arrival_airport', 'flights.arrival_airport', '=', 'arrival_airport.airport_id')
                ->join('classes as economy_class', function ($join) {
                    $join->on('airplanes.airplane_id', '=', 'economy_class.airplane_id')
                        ->where('economy_class.class_name', 'Economy');
                })
                ->join('classes as business_class', function ($join) {
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

            $returnFlights = $returnFlightsQuery->get();


            // Apply booking preference logic
            $adults = $request->adults;
            if ($request->booking_preference == 'a') {
                $adults = $request->adults - 1;
            }

            $triptype = $request->trip_type == 0 ? 'inbound' : 'outbound';

            // Return the JSON response
            return response()->json([
                'trip_type' => $triptype,
                'adults' => $adults,
                'infants' => $request->infants,
                'booking_preference' => $request->booking_preference,
                'departure_flights' => $departureFlights,
                'return_flights' => $returnFlights,
            ]);
        }
        elseif ($request->trip_type == '0') {
            // Handle one-way flights
        
            $departureDate = Carbon::parse($request->departure_date);
        
            // Calculate date ranges for departure flights
            $earliestDepartureDate = $departureDate->copy()->subDays(5);
            $earliestDepartureDate = $earliestDepartureDate->isPast() ? Carbon::now()->startOfDay() : $earliestDepartureDate;
            $latestDepartureDate = $departureDate->copy()->addDays(5);
        
            // Query for Departure Flights within the date range (including the exact date)
            $departureFlightsQuery = Flight::query()
                ->where('flights.departure_airport', $request->departure_airport)
                ->where('flights.arrival_airport', $request->arrival_airport)
                ->join('schedule_days', 'flights.flight_id', '=', 'schedule_days.flight_id')
                ->whereBetween('schedule_days.departure_date', [$earliestDepartureDate, $latestDepartureDate])
                ->join('schedule_times', 'flights.flight_id', '=', 'schedule_times.flight_id')
                ->join('airplanes', 'flights.airplane_id', '=', 'airplanes.airplane_id')
                ->join('airports as departure_airport', 'flights.departure_airport', '=', 'departure_airport.airport_id')
                ->join('airports as arrival_airport', 'flights.arrival_airport', '=', 'arrival_airport.airport_id')
                ->join('classes as economy_class', function ($join) {
                    $join->on('airplanes.airplane_id', '=', 'economy_class.airplane_id')
                        ->where('economy_class.class_name', 'Economy');
                })
                ->join('classes as business_class', function ($join) {
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
        
            $departureFlights = $departureFlightsQuery->get();
        
            // Apply booking preference logic
            $adults = $request->adults;
            if ($request->booking_preference == 'a') {
                $adults = $request->adults - 1;
            }
        
            $triptype = 'inbound'; // For one-way trips, you can categorize as inbound or any appropriate label
        
            // Return the JSON response
            return response()->json([
                'trip_type' => $triptype,
                'adults' => $adults,
                'infants' => $request->infants,
                'booking_preference' => $request->booking_preference,
                'departure_flights' => $departureFlights,
            ]);
        }
    }

    public function getPassengerCompanionsDetails(Request $request)
    {
        $user = Auth::guard('user')->user();

        // Get the passenger along with their travel requirements and passports, if they exist
        $passenger = $user->passenger()->with(['travelRequirement.passports'])->first();

        // Get the companions along with their travel requirements and passports, if they exist
        $companions = $passenger->companions()->with(['travelRequirement.passports'])->get();
        // dd($passenger->companions());
        // Prepare the data
        $data = [
            'passenger' =>  $passenger,
            'companions' => $companions
        ];

        return success($data, 'Passenger details with companions retrieved successfully');
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
