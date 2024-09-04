<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

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

    
}