<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferRequest;
use App\Models\Flight;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class OfferController extends Controller
{
    //Create Offer Function
    public function createOffer(OfferRequest $offerRequest)
    {
        if ($offerRequest->file('image')) {
            $path = $offerRequest->file('image')->storePublicly('OfferImage', 'public');
        }

        Offer::create([
            'employee_id' => Auth::guard('user')->user()->employee->employee_id,
            'flight_id' => $offerRequest->flight_id,
            'description' => $offerRequest->description,
            'start_date' => $offerRequest->start_date,
            'end_date' => $offerRequest->end_date,
            'image' => 'storage/' . $path,
            'title' => $offerRequest->title,
        ]);

        return success(null, 'this offer created successfully', 201);
    }

    //Update Offer Function
    public function updateOffer(Offer $offer, OfferRequest $offerRequest)
    {
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

        return success(null, 'thid offer updated successfully');
    }

    //Delete Offer Function
    public function deleteOffer(Offer $offer)
    {
        if (File::exists($offer->image)) {
            File::delete($offer->image);
        }
        $offer->forceDelete();

        return success(null, 'this offer has been permanently deleted');
    }

    //Get Offers Function
    public function getOffers(Request $request)
    {
        $query = Offer::with('flight')->orderBy('offer_id', 'desc');

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        $offers = $query->paginate(15);

        return success($offers, null);
    }

    public function getFlightsForOffers(){
        $flights = Flight::all();
        return success($flights, null);
    }

    //Get Offer Information Fucntion
    public function getOfferInformation(Offer $offer)
    {
        return success($offer->with('details', 'flight')->find($offer->offer_id), null);
    }
}
