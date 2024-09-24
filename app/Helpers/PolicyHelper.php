<?php

namespace App\Helpers;

use App\Models\Policy;
use Carbon\Carbon;

class PolicyHelper
{
    public static function applyPolicies($reservation, $cost)
    {
        $departure_date = $reservation->time->day->departure_date;
        $days_before_cancel = Carbon::parse($departure_date)->diffInDays(Carbon::now());

        if ($days_before_cancel == 1) {
            $policy = Policy::where('policy_name', 'cancelation before a day')->first();
        } elseif ($days_before_cancel > 1 && $days_before_cancel <= 7) {
            $policy = Policy::where('policy_name', 'cancelation before a week')->first();
        } elseif ($days_before_cancel > 7) {
            $policy = Policy::where('policy_name', 'cancelation before more than a week')->first();
        }

        if ($policy) {
            $cost = $cost - $cost * $policy->value / 100;
        }

        return $cost;
    }


    public static function applySeatScarcitySurcharge($reservation, $price)
    {
        $policy = Policy::where('policy_name', 'seat_scarcity_surcharge')->first();
        if ($policy) {
            $availableSeats = $reservation->flight->seats()->where('status', 'available')->count();
            $totalSeats = $reservation->flight->seats()->count();
            $occupancyRate = 1 - ($availableSeats / $totalSeats);
            $surchargePercentage = $policy->value * $occupancyRate;
            $price += $price * $surchargePercentage;
        }
        return $price;
    }
}
