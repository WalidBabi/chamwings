<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddPassportRequest;
use App\Http\Requests\UpdatePassportRequest;
use App\Models\Log;
use App\Models\Passport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PassportController extends Controller
{
    //Add Passport Function
    public function addPassport(AddPassportRequest $addPassportRequest)
    {
        $user = Auth::guard('user')->user();
        if ($addPassportRequest->file('passport_image')) {
            $path = $addPassportRequest->file('passport_image')->storePublicly('PassportImage', 'public');
        }
        Passport::create([
            'travel_requirement_id' => $user->passenger->travelRequirement->travel_requirement_id,
            'number' => encrypt($addPassportRequest->number),
            'status' => $addPassportRequest->status,
            'passport_expiry_date' => $addPassportRequest->passport_expiry_date,
            'passport_issued_date' => $addPassportRequest->passport_issued_date,
            'passport_issued_country' => $addPassportRequest->passport_issued_country,
            'passport_image' => 'storage/' . $path,
        ]);

        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' added a passport',
            'type' => 'insert',
        ]);

        return success(null, 'this passport added successfully', 201);
    }

    //Update Passport Function
    public function updatePassport(Passport $passport, UpdatePassportRequest $updatePassportRequest)
    {
        $user = Auth::guard('user')->user();
        $passport->update([
            'number' => $updatePassportRequest->number,
            'status' => $updatePassportRequest->status,
            'passport_expiry_date' => $updatePassportRequest->passport_expiry_date,
            'passport_issued_date' => $updatePassportRequest->passport_issued_date,
            'passport_issued_country' => $updatePassportRequest->passport_issued_country,
        ]);
        if ($updatePassportRequest->file('passport_image')) {
            if (File::exists($passport->passport_image)) {
                File::delete($passport->passport_image);
            }
            $path = $updatePassportRequest->file('passport_image')->storePublicly('PassportImage', 'public');
            $passport->update([
                'passport_image' => 'storage/' . $path,
            ]);
        }

        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' updated his passenger',
            'type' => 'update',
        ]);

        return success(null, 'this passport updated successfully');
    }

    //Delete Passport Function
    public function deletePassport(Passport $passport)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Passenger ' . $user->passenger->travelRequirement->first_name . ' ' . $user->passenger->travelRequirement->last_name . ' deleted a passport',
            'type' => 'delete',
        ]);
        $passport->delete();

        return success(null, 'this passport deleted successfully');
    }

    //Get Passports Function
    public function getPassports()
    {
        $user = Auth::guard('user')->user();
        $passports = $user->passenger->travelRequirement->passports;
        // dd($passports);
        $result = [];
        foreach ($passports as $passport) {
            $merge = [];
            $data = [
                'number' => $passport->number,
            ];
            $merge = array_merge($passport->toArray(), $data);
            $result = $merge;
        }

        return success($result, null);
    }

    //Get All Passengers Passports Function
    public function getAllPassports()
    {
        $passports = Passport::with(['travelRequirement.passenger.user'])->paginate(15);
        $result = [];
        foreach ($passports as $passport) {
            $merge = [];
            $data = [
                'number' => decrypt($passport->number),
            ];
            $merge = array_merge($passport->toArray(), $data);
            $result = $merge;
        }

        $data = [
            'data' => $result,
            'total' => $passports->total(),
        ];

        return success($data, null);
    }

    //Get Passport Information Function
    public function getPassportInformation(Passport $passport)
    {
        $passport = $passport->with('travelRequirement.passenger.user')->find($passport->passport_id);
        $result = [];
        $merge = [];
        $data = [
            'number' => decrypt($passport->number),
        ];
        $merge = array_merge($passport->toArray(), $data);
        $result = $merge;
        return success($result, null);
    }
}