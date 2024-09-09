<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferRequest;
use App\Models\Log;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class OfferController extends Controller
{
    //Create Offer Function
    public function createOffer(OfferRequest $offerRequest)
    {
        $user = Auth::guard('user')->user();
        if ($offerRequest->file('image')) {
            $path = $offerRequest->file('image')->storePublicly('OfferImage', 'public');
        }

        $offer = Offer::create([
            'employee_id' => Auth::guard('user')->user()->employee->employee_id,
            'flight_id' => $offerRequest->flight_id,
            'description' => $offerRequest->description,
            'start_date' => $offerRequest->start_date,
            'end_date' => $offerRequest->end_date,
            'image' => 'storage/' . $path,
            'title' => $offerRequest->title,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added offer its description ' . $offer->description,
            'type' => 'insert',
        ]);

        return success(null, 'this offer created successfully', 201);
    }

    //Update Offer Function
    public function updateOffer(Offer $offer, OfferRequest $offerRequest)
    {
        $user = Auth::guard('user')->user();
        if ($offerRequest->file('image')) {
            if (File::exists($offer->image)) {
                File::delete($offer->image);
            }
            $path = $offerRequest->file('image')->storePublicly('OfferImage', 'public');

            $offer->update([
                'image' => 'storage/' . $path,
            ]);
        }

        $offer->update([
            'flight_id' => $offerRequest->flight_id,
            'description' => $offerRequest->description,
            'start_date' => $offerRequest->start_date,
            'end_date' => $offerRequest->end_date,
            'title' => $offerRequest->title,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' update offer its description ' . $offer->description,
            'type' => 'update',
        ]);


        return success(null, 'thid offer updated successfully');
    }

    //Delete Offer Function
    public function deleteOffer(Offer $offer)
    {
        $user = Auth::guard('user')->user();
        if (File::exists($offer->image)) {
            File::delete($offer->image);
        }
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted offer its description ' . $offer->description,
            'type' => 'delete',
        ]);

        $offer->delete();

        return success(null, 'this offer deleted successfully');
    }

    //Get Offers Function
    public function getOffers()
    {
        $offers = Offer::with('flight')->pagiante(15);

        return success($offers, null);
    }

    //Get Offer Information Fucntion
    public function getOfferInformation(Offer $offer)
    {
        return success($offer->with('flight')->find($offer->offer_id), null);
    }
}