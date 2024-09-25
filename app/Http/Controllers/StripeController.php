<?php

namespace App\Http\Controllers;

use App\Helpers\PolicyHelper;
use App\Models\Log;
use App\Models\Policy;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Charge;
use Stripe\Refund;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function index()
    {
        return success(null, 'Payment Cancelled');
    }

    public function checkout(Reservation $reservation)
    {
        if ($reservation->status === 'Cancelled') {
            return error('some thing went wrong', 'this reservation cancelled before', 422);
        } else if ($reservation->status === 'Ended') {
            return error('some thing went wrong', 'this reservation ended', 422);
        }
        $count = 0;
        $price = 0;
        $discount = 0;

        $count = count($reservation->flightSeats);

        $price += $reservation->flight->price * $count;

        if ($reservation->round_trip) {
            $price += $reservation->roundFlight->price * $count;
        }

        foreach ($reservation->flight->offers as $offer) {
            if ($offer->start_date <= $reservation->time->day->departure_date && $offer->end_date >= $reservation->time->day->departure_date) {
                $price = $price - $price * $offer->discount / 100;
                break;
            }
        }
        $reservation_date = $reservation->created_at;
        $departure_date = $reservation->time->day->departure_date;

        $days = $reservation_date->diffInDays($departure_date);
        // if ($days >= 14 && $days < 30)
        //     while ($days >= 14) {
        //         $discount = Policy::where('after two weeks')->first()->value;
        //         $days -= 14;
        //     }
        // if ($days >= 30) {
        //     while ($days >= 30) {
        //         $discount += Policy::where('after month')->first()->value;
        //         $days -= 30;
        //     }
        // }
        $price = $price - $price * $discount / 100;

        // Apply seat scarcity surcharge
        // $price = PolicyHelper::applySeatScarcitySurcharge($reservation, $price);

        $user = Auth::guard('user')->user();
        $token = $user->createToken('auth-token')->plainTextToken;
    
        // dd($token);
        // dd($token);
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Send me money!!!'
                        ],
                        'unit_amount' => $price * 100, //5.00
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('success', [
                'reservation' => $reservation->reservation_id,
                'token' => $token
            ]),

            'cancel_url' => route('index'),
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function success(Request $request, Reservation $reservation)
    {
        $token = $request->query('token');
        if (!$token) {
            return error('Authentication failed', 'Token not provided', 401);
        }
    
        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        if (!$tokenModel) {
            return error('Authentication failed', 'Invalid token', 401);
        }
    
        $user = $tokenModel->tokenable;
        Auth::login($user);
    
        $reservation->update([
            'status' => 'Confirmed'
        ]);
    
        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' confirmed his reservation',
            'type' => 'insert',
        ]);

            return redirect('http://localhost:3000/my_reservations')->with([
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
    }
    

    //Cancel Reservation Function
    public function cancelReservation(Reservation $reservation)
    {
        $discount = 0;
        $user = Auth::guard('user')->user();
        if ($reservation->status === 'Cancelled') {
            return error('some thing went wrong', 'this reservation already cancelled', 422);
        } else if ($reservation->status === 'Pending') {
            foreach ($reservation->flightSeats as $flightSeat) {
                $flightSeat->delete();
            }
            $reservation->update([
                'status' => 'Cancelled'
            ]);
            Log::create([
                'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' cancelled his reservation',
                'type' => 'insert',
            ]);
            return success(null, 'this reservation cancelled successfully');
        }
        if (Carbon::now() > $reservation->time->day->departure_date) {
            return error('some thing went wrong', 'you cannot cancel this reservation now');
        }
        $cost = 0;
        $companions_count = count(explode(',', $reservation->have_companions));
        if ($reservation->is_traveling) {
            $companions_count++;
        }
        $cost += $reservation->flight->price * $companions_count;
        if ($reservation->round_trip) {
            $cost += $reservation->roundFlight->price * $companions_count;
        }
        foreach ($reservation->flight->offers as $offer) {
            if ($offer->start_date <= $reservation->time->day->departure_date && $offer->end_date >= $reservation->time->day->departure_date) {
                $cost = $cost - $cost * $offer->discount / 100;
                break;
            }
        }

        $cost = PolicyHelper::applyPolicies($reservation, $cost);
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $charge = Charge::create([
            'amount' => $cost,
            'currency' => 'usd',
            'source' => 'tok_visa',
            'description' => 'Test Charge',
        ]);
        $chargeId = $charge->id;
        $refund = Refund::create([
            'charge' => $chargeId,
            'amount' => $cost,
        ]);

        if ($refund->status == 'succeeded') {
            $reservation->update([
                'status' => 'Cancelled'
            ]);
            foreach ($reservation->flightSeats as $flightSeat) {
                $flightSeat->delete();
            }
            Log::create([
                'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' cancelled his reservation and return to him ' . $cost . '$',
                'type' => 'insert',
            ]);
            return success(null, 'this reservation cancelled successfully and return to you ' . $cost . '$');
        } else {
            return error('some thing went wrong', 'cancel faild', 422);
        }
    }
}
