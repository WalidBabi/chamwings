<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReservationRequest;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
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