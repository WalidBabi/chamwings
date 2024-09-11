<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisaRequest;
use App\Models\Airport;
use App\Models\Log;
use App\Models\Visa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisaController extends Controller
{
    //Add Visa Function
    public function addVisa(Airport $airport, VisaRequest $visaRequest)
    {
        $user = Auth::guard('user')->user();
        $visa = Visa::create([
            'airport_id' => $airport->airport_id,
            'visa_and_residence' => $visaRequest->visa_and_residence,
            'origin' => $visaRequest->origin,
            'destination' => $visaRequest->destination,
        ]);
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added  visa to airport ' . $airport->airport_name,
            'type' => 'insert',
        ]);
        return success($visa, 'this visa added successfully', 201);
    }

    //Update Visa Function
    public function updateVisa(Visa $visa, VisaRequest $visaRequest)
    {
        $user = Auth::guard('user')->user();
        $visa->update([
            'visa_and_residence' => $visaRequest->visa_and_residence,
            'origin' => $visaRequest->origin,
            'destination' => $visaRequest->destination,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added  a visa of airport ' . $visa->airport->airport_name,
            'type' => 'update',
        ]);
        return success($visa, 'this visa updated successfully');
    }

    //Delete Visa Function
    public function deleteVisa(Visa $visa)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted a visa of airport ' . $visa->airport->airport_name,
            'type' => 'delete',
        ]);
        $visa->delete();
        return success(null, 'this visa deleted successfully');
    }

    //Get Airport Visa Function
    public function getVisa(Airport $airport)
    {
        $visa = $airport->visa()->paginate(15);

        $data = [
            'data' => $visa->items(),
            'total' => $visa->total(),
        ];

        return success($data, null);
    }

    //Get Visa Information Function
    public function getVisaInformation(Visa $visa)
    {
        return success($visa->with('airport')->find($visa->visainfo_id), null);
    }
}