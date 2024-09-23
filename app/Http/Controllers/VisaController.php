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
            'departure_airport' => $visaRequest->departure_airport,
            'arrival_airport' => $visaRequest->arrival_airport,
            'visa_and_residence' => $visaRequest->visa_and_residence,
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
            'departure_airport' => $visaRequest->departure_airport,
            'arrival_airport' => $visaRequest->arrival_airport,
            'visa_and_residence' => $visaRequest->visa_and_residence,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added  a visa of airport ' . $visa->departureVisa->airport_name,
            'type' => 'update',
        ]);
        return success($visa, 'this visa updated successfully');
    }

    //Delete Visa Function
    public function deleteVisa(Visa $visa)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted a visa of airport ' . $visa->departureVisa->airport_name,
            'type' => 'delete',
        ]);
        $visa->delete();
        return success(null, 'this visa deleted successfully');
    }

    //Get Airport Visa Function
    public function getVisa(Airport $airport, Request $request)
    {
        $query = $airport->visa()->orderByDesc('visainfo_id');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('departure_airport', $search)
                    ->orWhere('arrival_airport', $search);
            });
        }

        $visa = $query->paginate(15);

        $data = [
            'data' => $visa->items(),
            'total' => $visa->total(),
        ];

        return success($data, null);
    }

    //Get Visa Information Function
    public function getVisaInformation(Visa $visa)
    {
        return success($visa->with('departureVisa')->find($visa->visainfo_id), null);
    }



    //Get All Visas Function
    public function getAllVisas(Request $request)
    {
        $query = Visa::with(['departureVisa', 'arrivalVisa'])
            ->orderByDesc('visainfo_id');

        $visas = $query->paginate(15);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('departureVisa', function ($query) use ($search) {
                    $query->where('airport_name', 'like', '%' . $search . '%')
                        ->orWhere('airport_code', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('arrivalVisa', function ($query) use ($search) {
                        $query->where('airport_name', 'like', '%' . $search . '%')
                            ->orWhere('airport_code', 'like', '%' . $search . '%');
                    });
            });
        }

        $visas = $query->paginate(15);

        $data = [
            'data' => collect($visas->items())->map(function ($visa) {
                return [
                    'visainfo_id' => $visa->visainfo_id,
                    'departure_airport' => [
                        'id' => $visa->departureVisa->id,
                        'airport_name' => $visa->departureVisa->airport_name,
                        'airport_code' => $visa->departureVisa->airport_code,
                        'city' => $visa->departureVisa->city,
                        'country' => $visa->departureVisa->country,
                        'image' => $visa->departureVisa->image,

                    ],
                    'arrival_airport' => [
                        'id' => $visa->arrivalVisa->id,
                        'airport_name' => $visa->arrivalVisa->airport_name,
                        'airport_code' => $visa->arrivalVisa->airport_code,
                        'city' => $visa->arrivalVisa->city,
                        'country' => $visa->arrivalVisa->country,
                        'image' => $visa->arrivalVisa->image,
                    ],
                    'created_at' => $visa->created_at,
                    'updated_at' => $visa->updated_at,
                ];
            }),
            'total' => $visas->total(),
        ];

        return success($data, null);
    }
}
