<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
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
        $count = 0;
        $price = 0;
        if ($reservation->is_traveling) {
            $count++;
        }
        if ($reservation->have_companions) {
            $comanions = explode(',', $reservation->have_companions);
            $count += count($comanions);
        }

        $price += $reservation->flight->price * $count;

        if ($reservation->round_trip) {
            $price += $reservation->roundFlight->price * $count;
        }
        \Stripe\Stripe::setApiKey(config('stripe.sk'));
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
            'success_url' => route('success', $reservation->reservation_id),
            'cancel_url' => route('index'),
        ]);

        return redirect()->away($session->url);
    }

    public function success(Reservation $reservation)
    {
        $reservation->update([
            'status' => 'Confirmed'
        ]);
        return success($reservation, 'Payment Completed Successfully');
    }

    //Cancel Reservation Function
    public function cancelReservation(Reservation $reservation)
    {
        if ($reservation->status === 'Cancelled') {
            return error('some thing went wrong', 'this reservation already cancelled', 422);
        } else if ($reservation->status === 'Pending') {
            $reservation->update([
                'status' => 'Cancelled'
            ]);
            return success(null, 'this reservation cancelled successfully');
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
        $cost = $cost - $cost * 5 / 100;
        Stripe::setApiKey(config('stripe.sk'));
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
            return success(null, 'this reservation cancelled successfully and return to you ' . $cost . '$');
        } else {
            return error('some thing went wrong', 'cancel faild', 422);
        }
    }
}
