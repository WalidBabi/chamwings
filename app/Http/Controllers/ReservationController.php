<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Airplane;
use App\Models\ClassM;
use App\Models\Companion;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\Log;
use App\Models\Reservation;
use App\Models\ScheduleTime;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{

    public function search(Request $request)
    {
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
                ->join('schedule_times', 'schedule_days.schedule_day_id', '=', 'schedule_times.schedule_day_id')
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
                ->join('schedule_times', 'schedule_days.schedule_day_id', '=', 'schedule_times.schedule_day_id')
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
        } elseif ($request->trip_type == '0') {
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
                ->join('schedule_times', 'schedule_days.schedule_day_id', '=', 'schedule_times.schedule_day_id')
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

    public function searchWithDates(Request $request)
    {


        $departureFlightsQuery = Flight::join('schedule_days', 'flights.flight_id', '=', 'schedule_days.flight_id')
            ->join('schedule_times', 'schedule_days.schedule_day_id', '=', 'schedule_times.schedule_day_id')
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
            ->where('flights.departure_airport', $request->departure_airport)
            ->where('flights.arrival_airport', $request->arrival_airport)
            ->whereBetween('schedule_days.departure_date', [$request->start_date, $request->end_date])
            ->select(
                'flights.flight_id',
                'flights.airplane_id',
                'flights.departure_airport',
                'flights.arrival_airport',
                'flights.flight_number',
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
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
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

    //Create Reservation Function
    public function createReservation(CreateReservationRequest $createReservationRequest)
    {
        $user = Auth::guard('user')->user();
        $time = ScheduleTime::find($createReservationRequest->schedule_time_id);

        if ($time->day->departure_date <= Carbon::now()) {
            return error('some thing went wrong', 'you cannot reserve in this day', 422);
        }
        if ($createReservationRequest->round_trip) {
            $createReservationRequest->validated([
                'round_flight_id' => 'required|exists:flights,flight_id|integer',
                'round_schedule_time_id' => 'required|exists:schedule_times,schedule_time_id|integer',
            ]);
            $round_time = ScheduleTime::find($createReservationRequest->round_schedule_time_id);
            // dd($round_time);
            if ($round_time->day->departure_date <= Carbon::now()) {
                return error('some thing went wrong', 'you cannot reserve in this day', 422);
            }
        }

        $reservation = Reservation::create([
            'passenger_id' => Auth::guard('user')->user()->passenger->passenger_id,
            'flight_id' => $createReservationRequest->flight_id,
            'round_flight_id' => $createReservationRequest->round_flight_id,
            'schedule_time_id' => $createReservationRequest->schedule_time_id,
            'round_schedule_time_id' => $createReservationRequest->round_schedule_time_id,
            'round_trip' => $createReservationRequest->round_trip,
            'status' => 'Pending',
            'is_traveling' => $createReservationRequest->is_traveling,
            'have_companions' => $createReservationRequest->have_companions,
            'infants' => $createReservationRequest->infants,
        ]);

        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' reserved in a flight',
            'type' => 'insert',
        ]);
        return success($reservation->with('time')->find($reservation->reservation_id), 'this reservation created successfully', 201);
    }
    //Add Seats To Reservation Function
    public function addSeats(Reservation $reservation, Request $request)
    {
        $user = Auth::guard('user')->user();
        // dd($user);
        if ($reservation->status === 'Confirmed') {
            return error('some thing went wrong', 'you cannot add seats to this reservation now', 422);
        }

        $seats = explode(',', $request->seats);
        $request->validate([
            'seats' => 'required',
        ]);
        // dd($seats);
        DB::beginTransaction();
        try {
            $unavailableSeats = [];
            $isRoundTrip = $request->round_trip == 1;
            foreach ($seats as $seatId) {
                $seat = Seat::lockForUpdate()->find($seatId);
                $time_id = $isRoundTrip ? $reservation->round_schedule_time_id : $reservation->schedule_time_id;

                $isOccupied = $seat->flightSeat()
                    ->whereHas('reservation', function ($query) use ($time_id, $isRoundTrip) {
                        $query->where($isRoundTrip ? 'round_schedule_time_id' : 'schedule_time_id', $time_id);
                    })
                    ->where('is_round_flight', $isRoundTrip)
                    ->exists();

                if ($isOccupied || $seat->checked) {
                    $unavailableSeats[] = '(' . $seat->seat_number . $seat->row_number . ')';
                }
            }

            if (!empty($unavailableSeats)) {
                DB::rollBack();
                return error('some thing went wrong', 'Seats ' . implode(', ', $unavailableSeats) . ' not available right now', 422);
            }

            foreach ($seats as $seatId) {
                $seat = Seat::lockForUpdate()->find($seatId);
                if (!$seat) {
                    throw new \Exception("Seat not found: " . $seatId);
                }

                // Double-check if the seat is still available
                $isStillAvailable = !$seat->flightSeat()
                    ->whereHas('reservation', function ($query) use ($time_id, $isRoundTrip) {
                        $query->where($isRoundTrip ? 'round_schedule_time_id' : 'schedule_time_id', $time_id);
                    })
                    ->where('is_round_flight', $isRoundTrip)
                    ->exists() && !$seat->checked;

                if (!$isStillAvailable) {
                    DB::rollBack();
                    return error('some thing went wrong', 'Seat ' . $seat->seat_number . $seat->row_number . ' was just taken', 422);
                }

                FlightSeat::create([
                    'seat_id' => $seatId,
                    'reservation_id' => $reservation->reservation_id,
                    'is_round_flight' => $isRoundTrip,
                ]);

                $seat->update(['checked' => 0]);
            }

            Log::create([
                'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' reserved seats for ' . ($isRoundTrip ? 'return' : 'outbound') . ' flight',
                'type' => 'insert',
            ]);

            DB::commit();
            return success($reservation->seats()->where('is_round_flight', $isRoundTrip)->get(), 'these seats added to this reservation successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return error('some thing went wrong', $e->getMessage(), 500);
        }
    }
    //Update Reservation Function
    public function updateReservation(Reservation $reservation, UpdateReservationRequest $updateReservationRequest)
    {
        $user = Auth::guard('user')->user();
        if ($reservation->status === 'Confirmed') {
            return error('some thing went wrong', 'you cannot update this reservation now', 422);
        }
        $reservation->update([
            'round_trip' => $updateReservationRequest->round_trip,
            'is_traveling' => $updateReservationRequest->is_traveling,
        ]);

        return success(null, 'this reservation updated successfully');
    }

    //Update Reservation From Employee Function
    public function employeeUpdateReservation(Reservation $reservation, UpdateReservationRequest $updateReservationRequest)
    {
        $user = Auth::guard('user')->user();
        $reservation->update([
            'round_trip' => $updateReservationRequest->round_trip,
            'status' => $updateReservationRequest->status,
            'is_traveling' => $updateReservationRequest->is_traveling,
        ]);
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' updated a reservation',
            'type' => 'update',
        ]);
        return success(null, 'this reservation updated successfully');
    }

    //Update Seat Reservation Function
    public function updateSeats(Reservation $reservation, Request $request)
    {
        $user = Auth::guard('user')->user();
        if ($reservation->status === 'Confirmed') {
            return error('some thing went wrong', 'you cannot update seats of this reservation now', 422);
        }
        $seats = explode(',', $request->seats);
        $request->validate([
            'seats' => 'required',
        ]);


        $checked = '';
        foreach ($seats as $seat) {
            $seat = Seat::find($seat);
            $reserved_seats = $reservation->flightSeats->where('seat_id', $seats)->first();
            $time_id = $request->schedule_time_id;
            if ($request->round_trip) {
                $check_seat = $seat->flightSeat()->whereHas('reservation', function ($query) use ($time_id) {
                    $query->where('round_schedule_time_id', $time_id);
                })->get();
            } else {
                $check_seat = $seat->flightSeat()->whereHas('reservation', function ($query) use ($time_id) {
                    $query->where('schedule_time_id', $time_id);
                })->get();
            }
            if ($check_seat != '[]' && !$reserved_seats) {
                $checked .= '(' . $seat->seat_number . $seat->row_number . ') ';
            }
        }

        if ($checked) {
            return error('some thing went wrong', 'Seats ' . $checked . 'not available right now', 422);
        }
        foreach ($seats as $seat) {
            if ($reservation->flightSeats->where('seat_id', $seat)->first()) {
                continue;
            }
            FlightSeat::create([
                'seat_id' => $seat,
                'reservation_id' => $reservation->reservation_id,
                'is_round_flight' => $request->round_trip,
            ]);
            return 1;
            $seat = Seat::find($seat);
            $seat->update([
                'checked' => 1,
            ]);
            $reservation->flightSeats->where('seat_id', $seat)->first()->delete();
        }
        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' updated its reserved seats',
            'type' => 'update',
        ]);
        return success(null, 'this reservation seats updated successfully');
    }

    //Update Companions Reservation Function
    public function updateCompanions(Reservation $reservation, Request $request)
    {
        $user = Auth::guard('user')->user();
        if ($reservation->status === 'Confirmed') {
            return error('some thing went wrong', 'you cannot update companions of this reservation now', 422);
        }
        $request->validate([
            'have_companions' => 'required',
            'infants' => 'required',
        ]);

        $reservation->update([
            'have_companions' => $request->have_companions,
            'infants' => $request->infants,
        ]);

        $adults = explode(',', $reservation->have_companions);
        $infants = explode(',', $reservation->infants);
        $adults = Companion::whereIn('companion_id', $adults)->with('travelRequirement')->get();
        $infants = Companion::whereIn('companion_id', $infants)->with('travelRequirement')->get();
        $data = [
            'adults' => $adults,
            'infants' => $infants,
        ];
        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' updated his companions reservation',
            'type' => 'update',
        ]);
        return success($data, 'those companions updated successfully');
    }

    //Get User Reservations Function
    public function getUserReservations()
    {
        $reservations = Auth::guard('user')->user()->passenger->reservations()->with('flight', 'seats')->paginate(15);
        $data = [
            'data' => $reservations->items(),
            'total' => $reservations->total(),
        ];
        return success($data, null);
    }

    //Get Reservations Function
    public function getReservations(Request $request)
    {
        $query = Reservation::with(['flight', 'seats', 'passenger.travelRequirement'])
            ->withTrashed()  // Include soft deleted records
            ->orderBy('reservation_id', 'desc');

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('passenger.travelRequirement', function ($subQuery) use ($searchTerm) {
                    $subQuery->where('first_name', 'LIKE', "%{$searchTerm}%");
                });
            });
        }

        $perPage = $request->input('per_page', 15); // Default to 15 items per page
        $reservations = $query->paginate($perPage);

        $reservationsWithFirstName = $reservations->map(function ($reservation) {
            $firstName = $reservation->passenger->travelRequirement->first_name ?? null;
            return array_merge($reservation->toArray(), ['passenger_first_name' => $firstName]);
        });

        return success([
            'data' => $reservationsWithFirstName,
            'pagination' => [
                'total' => $reservations->total(),
                'per_page' => $reservations->perPage(),
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'from' => $reservations->firstItem(),
                'to' => $reservations->lastItem(),
            ]
        ], null);
    }

    //Get Reservation Information Function
    public function getReservationInformation(Reservation $reservation)
    {
        return success($reservation->with(['flight', 'seats', 'roundFlight', 'time.day', 'roundTime.day'])->find($reservation->reservation_id), null);
    }

    //Get Passengers Function
    public function getUserPassengers(Reservation $reservation)
    {
        $adults = explode(',', $reservation->have_companions);
        $infants = explode(',', $reservation->infants);
        $adults = Companion::whereIn('companion_id', $adults)->with('travelRequirement')->get();
        $infants = Companion::whereIn('companion_id', $infants)->with('travelRequirement')->get();
        $data = [
            'adults' => $adults,
            'infants' => $infants,
        ];

        return success($data, null);
    }

    //Check Reservation Expiry Date Function
    public function checkExpiry()
    {
        $reservations = Reservation::all();

        foreach ($reservations as $reservation) {
            $date = $reservation->created_at;
            $expiry_date = $date->addDays(3);

            if (Carbon::now() > $expiry_date && $reservation->status != 'Confirmed' && Carbon::now() < $reservation->time->day->departure_date) {
                foreach ($reservation->flightSeats as $seat) {
                    $seat->delete();
                }
                $reservation->delete();
            }
        }
    }

    //Get Seats Status In Specific Time
    public function GoingSeatsStatus(ScheduleTime $scheduleTime)
    {
        // dd($scheduleTime);

        $reserved_seats = [];
        $reservations = $scheduleTime->reservations()->get();
        foreach ($reservations as $reservation) {
            foreach ($reservation->flightSeats()->where('is_round_flight', 0)->get() as $flightSeat) {
                array_push($reserved_seats, $flightSeat->seat);
            }
        }
        return success([
            'one_way_reserved_seats' => $reserved_seats
        ], null);
    }

    public function ReturningSeatsStatus(ScheduleTime $scheduleTime)
    {
        // dd($scheduleTime);
        // $scheduleTime = ScheduleTime::findOrFail($scheduleTime);

        $round_reserved_seats = [];
        $round_reservations = Reservation::where('round_schedule_time_id', $scheduleTime->schedule_time_id)->get();
        // dd($round_reservations);
        foreach ($round_reservations as $round_reservation) {
            foreach ($round_reservation->flightSeats()->where('is_round_flight', 1)->get() as $flightSeat) {
                array_push($round_reserved_seats, $flightSeat->seat);
            }
        }

        return success([
            'round_trip_reserved_seats' => $round_reserved_seats
        ], null);
    }

    // Cancel Reservation by Employee Function
    public function cancelReservationByEmployee(Reservation $reservation)
    {
        // Check if the reservation exists
        if (!$reservation) {
            return error('Reservation not found', 404);
        }

        // Check if the reservation is already cancelled
        if ($reservation->status === 'Cancelled') {
            return error('Reservation is already cancelled', 400);
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Update reservation status and soft delete
            $reservation->status = 'Cancelled';
            $reservation->deleted_at = now();
            $reservation->save();

            // Soft delete associated flight seats and update seat checked status
            foreach ($reservation->flightSeats as $flightSeat) {
                $flightSeat->deleted_at = now();
                $flightSeat->save();

                // Update the checked attribute of the associated seat
                $seat = $flightSeat->seat;
                $seat->checked = 0;
                $seat->save();
            }

            // Commit the transaction
            DB::commit();

            return success(['success' => 'Reservation cancelled and seats updated successfully'], 200);
        } catch (\Exception $e) {
            // If an error occurs, rollback the transaction
            DB::rollBack();
            return error('An error occurred while cancelling the reservation: ' . $e->getMessage(), 500);
        }
    }
    // Reactivate Reservation by Employee Function
    public function reactivateReservationByEmployee($reservationId)
    {
        $reservation = Reservation::withTrashed()->find($reservationId);

        if (!$reservation) {
            return error('Reservation not found', 404);
        }

        if ($reservation->status !== 'Cancelled') {
            return error('Reservation is already active', 400);
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {
            $reservation->status = 'Confirmed';
            $reservation->deleted_at = null;
            $reservation->save();

            // Reactivate associated flight seats and update seat status
            foreach ($reservation->flightSeats()->withTrashed()->get() as $flightSeat) {
                $flightSeat->restore();

                // Update the associated seat's checked status
                $seat = $flightSeat->seat;
                $seat->checked = 1;
                $seat->save();
            }

            // Commit the transaction
            DB::commit();

            return success(['message' => 'Reservation reactivated successfully', 'reservation' => $reservation], 200);
        } catch (\Exception $e) {
            // If an error occurs, rollback the transaction
            DB::rollBack();
            return error('An error occurred while reactivating the reservation: ' . $e->getMessage(), 500);
        }
    }

    public function getAllSeats()
    {
        try {
            $seats = Seat::all()->map(function ($seat) {
                $seat->checked = 0;
                return $seat;
            });
            return success(['seats' => $seats], 200);
        } catch (\Exception $e) {
            return error('An error occurred while fetching seats: ' . $e->getMessage(), 500);
        }
    }

    public function getCheckedSeats($reservation_id)
    {
        try {
            $reservation = Reservation::findOrFail($reservation_id);

            $goingFlightSeats = $reservation->flightSeats()
                ->with('seat')
                ->whereHas('seat', function ($query) {
                    $query->where('checked', 1)->where('is_round_flight', 0);
                })
                ->get()
                ->pluck('seat')
                ->map(function ($seat) {
                    $seat->flight_type = 'outbound';
                    return $seat;
                });

            $returnFlightSeats = $reservation->flightSeats()
                ->with('seat')
                ->whereHas('seat', function ($query) {
                    $query->where('checked', 1)->where('is_round_flight', 1);
                })
                ->get()
                ->pluck('seat')
                ->map(function ($seat) {
                    $seat->flight_type = 'inbound';
                    return $seat;
                });

            return success([
                'going_flight_seats' => $goingFlightSeats,
                'return_flight_seats' => $returnFlightSeats
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return error('Reservation not found', 404);
        } catch (\Exception $e) {
            return error('An error occurred while fetching checked seats: ' . $e->getMessage(), 500);
        }
    }
}
