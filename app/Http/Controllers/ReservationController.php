<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Airplane;
use App\Models\ClassM;
use App\Models\Companion;
use App\Models\Flight;
use App\Models\FlightSeat;
use App\Models\Reservation;
use App\Models\ScheduleTime;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

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
        $adults = $request->adults;
        // Apply booking preference logic
        if ($request->booking_preference == 'a') {
            $adults = $request->adults - 1;
        }
        if ($request->trip_type == 0) {
            $triptype = 'inbound';
        } else {
            $triptype = 'outbound';
        }

        return response()->json([
            'trip_type' => $triptype,
            'adults' => $adults,
            'infants' => $request->infants,
            'booking_preference' => $request->booking_preference,
            // 'class'=>$class,
            'flights' => $flights

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
        $time = ScheduleTime::find($createReservationRequest->schedule_time_id);

        if ($time->day->departure_date <= Carbon::now()) {
            return error('some thing went wrong', 'you cannot reserve in this day', 422);
        }

        $reservation = Reservation::create([
            'passenger_id' => Auth::guard('user')->user()->passenger->passenger_id,
            'flight_id' => $createReservationRequest->flight_id,
            'schedule_time_id' => $createReservationRequest->schedule_time_id,
            'round_trip' => $createReservationRequest->round_trip,
            'status' => 'Pending',
            'is_traveling' => $createReservationRequest->is_traveling,
            'have_companions' => $createReservationRequest->have_companions,
            'infants' => $createReservationRequest->infants,
            'reservation_date' => Date::now(),
        ]);
        return success($reservation->with('time')->find($reservation->reservation_id), 'this reservation created successfully', 201);
    }

    //Add Seats To Reservation Function
    public function addSeats(Reservation $reservation, Request $request)
    {
        $seats = explode(',', $request->seats);
        $request->validate([
            'seats' => 'required',
        ]);
        $checked = '';

        foreach ($seats as $seat) {
            $seat = Seat::find($seat);
            $time_id = $request->schedule_time_id;
            $check_seat = $seat->flightSeat()->whereHas('reservation', function ($query) use ($time_id) {
                $query->where('schedule_time_id', $time_id);
            })->get();
            if ($check_seat != '[]') {
                $checked .= '(' . $seat->seat_number . $seat->row_number . ') ';
            }
        }

        if ($checked) {
            return error('some thing went wrong', 'Seats ' . $checked . 'not available right now', 422);
        }

        foreach ($seats as $seat) {
            FlightSeat::create([
                'seat_id' => $seat,
                'reservation_id' => $reservation->reservation_id
            ]);
            $seat = Seat::find($seat);
            $seat->update([
                'checked' => 1,
            ]);
        }
        return success($reservation->seats, 'this seats added to this reservation successfully', 201);
    }

    //Update Reservation Function
    public function updateReservation(Reservation $reservation, UpdateReservationRequest $updateReservationRequest)
    {
        $reservation->update([
            'round_trip' => $updateReservationRequest->round_trip,
            'is_traveling' => $updateReservationRequest->is_traveling,
        ]);

        return success(null, 'this reservation updated successfully');
    }

    //Update Reservation From Employee Function
    public function employeeUpdateReservation(Reservation $reservation, UpdateReservationRequest $updateReservationRequest)
    {
        $reservation->update([
            'round_trip' => $updateReservationRequest->round_trip,
            'status' => $updateReservationRequest->status,
            'is_traveling' => $updateReservationRequest->is_traveling,
        ]);

        return success(null, 'this reservation updated successfully');
    }

    //Update Seat Reservation Function
    public function updateSeats(Reservation $reservation, Request $request)
    {
        $seats = explode(',', $request->seats);
        $request->validate([
            'seats' => 'required',
        ]);


        $checked = '';
        foreach ($seats as $seat) {
            $seat = Seat::find($seat);
            $reserved_seats = $reservation->flightSeats->where('seat_id', $seats)->first();
            $time_id = $request->schedule_time_id;
            $check_seat = $seat->flightSeat()->whereHas('reservation', function ($query) use ($time_id) {
                $query->where('schedule_time_id', $time_id);
            })->get();
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
                'reservation_id' => $reservation->reservation_id
            ]);
            return 1;
            $seat = Seat::find($seat);
            $seat->update([
                'checked' => 1,
            ]);
            $reservation->flightSeats->where('seat_id', $seat)->first()->delete();
        }

        return success(null, 'this reservation seats updated successfully');
    }

    //Update Companions Reservation Function
    public function updateCompanions(Reservation $reservation, Request $request)
    {
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

        return success($data, 'those companions updated successfully');
    }

    //Get User Reservations Function
    public function getUserReservations()
    {
        $reservations = Auth::guard('user')->user()->passenger->reservations()->with('flight', 'seats')->get();
        return success($reservations, null);
    }

    //Get Reservations Function
    public function getReservations()
    {
        $reservations = Reservation::with('flight', 'seats')->get();

        return success($reservations, null);
    }

    //Get Reservation Information Function
    public function getReservationInformation(Reservation $reservation)
    {
        return success($reservation->with(['flight', 'seats'])->find($reservation->reservation_id), null);
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

            if (Carbon::now() > $expiry_date) {
                foreach ($reservation->flightSeats as $seat) {
                    $seat->delete();
                }
                $reservation->delete();
            }
        }
    }

    //Get Seats Status In Specific Time
    public function SeatsStatus(ScheduleTime $scheduleTime)
    {
        $reserved_seats = [];
        $reservations = $scheduleTime->reservations;

        foreach ($reservations as $reservation) {
            foreach ($reservation->flightSeats as $flightSeat) {
                array_push($reserved_seats, $flightSeat->seat);
            }
        }

        return success($reserved_seats, null);
    }
}
