<?php

namespace App\Http\Controllers;

use App\Http\Requests\PointRequest;
use App\Models\Point;
use App\Models\User;
use Illuminate\Http\Request;

class PointController extends Controller
{
    //Add Points Function
    public function addPoint(User $user, PointRequest $pointRequest)
    {
        $point = Point::create([
            'user_id' => $user->user_id,
            'points' => $pointRequest->points,
        ]);

        return success($point, 'points added successfully', 201);
    }

    //Edit Point Function
    public function editPoint(Point $point, PointRequest $pointRequest)
    {
        $point->update([
            'points' => $pointRequest->points,
        ]);

        return success($point, 'this points updated successfully');
    }

    //Delete Point Function
    public function deletePoint(Point $point)
    {
        $point->delete();

        return  success(null, 'this points deleted successfully');
    }

    //Get Points Function
    public function getPoints()
    {
        $points = Point::with('user')->get();

        return success($points, null);
    }

    //Get Point Information Function
    public function getPointInformation(Point $point)
    {
        return success($point->with('user')->find($point->point_id), null);
    }
}
