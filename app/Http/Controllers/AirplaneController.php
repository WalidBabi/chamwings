<?php

namespace App\Http\Controllers;

use App\Http\Requests\AirplaneRequest;
use App\Models\Airplane;
use Illuminate\Http\Request;

class AirplaneController extends Controller
{
    //Add Airplane Function
    public function addAirplane(AirplaneRequest $airplaneRequest)
    {
        Airplane::create([
            'model' => $airplaneRequest->model,
            'manufacturer' => $airplaneRequest->manufacturer,
            'range' => $airplaneRequest->range,
        ]);

        return success(null, 'this airplane added successfully', 201);
    }

    //Edit Airplane Function
    public function editAirplane(Airplane $airplane, AirplaneRequest $airplaneRequest)
    {
        $airplane->update([
            'model' => $airplaneRequest->model,
            'manufacturer' => $airplaneRequest->manufacturer,
            'range' => $airplaneRequest->range,
        ]);

        return success(null, 'this airplane updated successfully');
    }

    //Delete Airplane Function
    public function deleteAirplane(Airplane $airplane)
    {
        $airplane->delete();

        return success(null, 'this airplane deleted successfully');
    }

    //Get Airplanes Function
    public function getAirplanes()
    {
        $airplanes = Airplane::paginate(15);

        $data = [
            'data' => $airplanes->items(),
            'total' => $airplanes->total(),
        ];

        return success($data, null);
    }

    //Get Airplane Information Function
    public function getAirplaneInformation(Airplane $airplane)
    {
        return success($airplane->with(['classes.seats'])->find($airplane->airplane_id), null);
    }

    public function activateAirplane($airplane)
    {
        $airplane = Airplane::withTrashed()->find($airplane);
        if (!$airplane) {
            return error(null, null, 404);
        }
        $airplane->deleted_at = null;
        $airplane->update();

        return success(null, 'this airplane activated successfully');
    }
}