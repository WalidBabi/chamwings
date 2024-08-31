<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPassportRequest;
use App\Http\Requests\PassengerRequest;
use App\Models\Companion;
use App\Models\Passport;
use App\Models\TravelRequirement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassengerController extends Controller
{
    //Add Passenger Function
    public function addPassenger(PassengerRequest $passengerRequest, AddPassportRequest $addPassportRequest)
    {
        $year = explode('-', $passengerRequest->date_of_birth);
        $travel_requirement = TravelRequirement::create([
            'first_name' => $passengerRequest->first_name,
            'last_name' => $passengerRequest->last_name,
            'date_of_birth' => $passengerRequest->date_of_birth,
            'gender' => $passengerRequest->gender,
            'nationality' => $passengerRequest->nationality,
            'address' => $passengerRequest->address,
            'title' => $passengerRequest->title,
            'city' => $passengerRequest->city,
            'id_number' => $passengerRequest->id_number,
            'mobile_during_travel' => $passengerRequest->mobile_during_travel,
            'age' => Carbon::now()->year - $year[0],
            'country_of_residence' => $passengerRequest->country_of_residence,
        ]);

        Companion::create([
            'passenger_id' => Auth::guard('user')->user()->passenger->passenger_id,
            'travel_requirement_id' => $travel_requirement->travel_requirement_id,
            'infant' => $passengerRequest->infant,
        ]);
        if ($addPassportRequest->file('passport_image')) {
            $path = $addPassportRequest->file('passport_image')->storePublicly('PassportImage', 'public');
        }

        Passport::create([
            'travel_requirement_id' => $travel_requirement->travel_requirement_id,
            'number' => encrypt($addPassportRequest->number),
            'status' => $addPassportRequest->status,
            'passport_expiry_date' => $addPassportRequest->passport_expiry_date,
            'passport_issued_date' => $addPassportRequest->passport_issued_date,
            'passport_issued_country' => $addPassportRequest->passport_issued_country,
            'passport_image' => 'storage/' . $path,
        ]);

        return success($travel_requirement->with('companion', 'passports')->find($travel_requirement->travel_requirement_id), 'this passenger added successfully', 201);
    }

    //Update Passenger Function
    public function updatePassenger(TravelRequirement $travelRequirement, PassengerRequest $passengerRequest)
    {
        $year = explode('-', $passengerRequest->date_of_birth);

        $travelRequirement->update([
            'first_name' => $passengerRequest->first_name,
            'last_name' => $passengerRequest->last_name,
            'date_of_birth' => $passengerRequest->date_of_birth,
            'gender' => $passengerRequest->gender,
            'nationality' => $passengerRequest->nationality,
            'address' => $passengerRequest->address,
            'title' => $passengerRequest->title,
            'city' => $passengerRequest->city,
            'id_number' => $passengerRequest->id_number,
            'mobile_during_travel' => $passengerRequest->mobile_during_travel,
            'age' => Carbon::now()->year - $year[0],
            'country_of_residence' => $passengerRequest->country_of_residence,
        ]);

        $travelRequirement->companion->update([
            'infant' => $passengerRequest->infant,
        ]);

        return success(null, 'this passenger updated successfully');
    }

    //Delete Passenger Function
    public function deletePassenger(TravelRequirement $travelRequirement)
    {
        $travelRequirement->delete();

        return success(null, 'this passenger deleted successfully');
    }

    //Get User Passengers Function
    public function getUserPassengers()
    {
        $user = Auth::guard('user')->user();

        return success($user->passenger->companions()->with('travelRequirement')->get(), null);
    }

    //Get Passenger Information Function
    public function getPassengerInformation(TravelRequirement $travelRequirement)
    {
        return success($travelRequirement->with('companion', 'passports')->find($travelRequirement->travel_requirement_id), null);
    }
}