<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    //Get Cities Function
    public function getCities()
    {
        $cities = City::all();
        return success($cities, null);
    }

    //Get City Information Function
    public function getCityInformation(City $city)
    {
        return success($city, null);
    }
}