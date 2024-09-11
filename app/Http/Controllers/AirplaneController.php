<?php

namespace App\Http\Controllers;

use App\Http\Requests\AirplaneRequest;
use App\Models\Airplane;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AirplaneController extends Controller
{
    //Add Airplane Function
    public function addAirplane(AirplaneRequest $airplaneRequest)
    {
        $user = Auth::guard('user')->user();
        Airplane::create([
            'model' => $airplaneRequest->model,
            'manufacturer' => $airplaneRequest->manufacturer,
            'range' => $airplaneRequest->range,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added a new airplane to system its model ' . $airplaneRequest->model,
            'type' => 'insert',
        ]);

        return success(null, 'this airplane added successfully', 201);
    }

    //Edit Airplane Function
    public function editAirplane(Airplane $airplane, AirplaneRequest $airplaneRequest)
    {
        $user = Auth::guard('user')->user();
        $airplane->update([
            'model' => $airplaneRequest->model,
            'manufacturer' => $airplaneRequest->manufacturer,
            'range' => $airplaneRequest->range,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' edited airplane its model ' . $airplaneRequest->model,
            'type' => 'update',
        ]);

        return success(null, 'this airplane updated successfully');
    }

    //Delete Airplane Function
    public function deleteAirplane(Airplane $airplane)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted airplane its model ' . $airplane->model,
            'type' => 'delete',
        ]);
        $airplane->delete();

        return success(null, 'this airplane deleted successfully');
    }

    //Get Airplanes Function
    public function getAirplanes()
    {
        $airplanes = Airplane::withTrashed()->orderBy('airplane_id', 'desc')->paginate(15);

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