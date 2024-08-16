<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferDetailRequest;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferDetailController extends Controller
{
    //Add Detail To Offer Function
    public function addDetail(Offer $offer, OfferDetailRequest $offerDetailRequest)
    {
        if ($offerDetailRequest->file('image')) {
            $path = $offerDetailRequest->file('image')->storePublicly('OfferImage', 'public');
        }

        $data = [
            'days'=>$offerDetailRequest->day,
            'description'=>
        ];
    }
}