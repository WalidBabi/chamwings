<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleRequest;
use App\Models\Flight;
use App\Models\Log;
use App\Models\ScheduleDay;
use App\Models\ScheduleTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    //Add Schedule Fucntion
    public function addSchedule(Flight $flight, ScheduleRequest $scheduleRequest)
    {
        $user = Auth::guard('user')->user();
        $scheduleDay = ScheduleDay::create([
            'flight_id' => $flight->flight_id,
            'departure_date' => $scheduleRequest->schedule['departure_date'],
            'arrival_date' => $scheduleRequest->schedule['arrival_date'],
        ]);

        for ($i = 0; $i < count($scheduleRequest->schedule['departure_times']); $i++) {
            $departureTime = Carbon::parse($scheduleRequest->schedule['departure_times'][$i]);
            $arrivalTime = Carbon::parse($scheduleRequest->schedule['arrival_times'][$i]);
            $duration = $departureTime->diffInHours($arrivalTime);

            ScheduleTime::create([
                'schedule_day_id' => $scheduleDay->schedule_day_id,
                'departure_time' => $scheduleRequest->schedule['departure_times'][$i],
                'arrival_time' => $scheduleRequest->schedule['arrival_times'][$i],
                'duration' => $duration,
            ]);
        }
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added  schedule to a flight',
            'type' => 'insert',
        ]);
        return success(null, 'this schedule added successfully', 201);
    }

    //Edit Day Function
    public function editDay(ScheduleDay $scheduleDay, Request $request)
    {
        $user = Auth::guard('user')->user();
        $request->validate([
            'departure_date' => 'required|date',
            'arrival_date' => 'required|date',
        ]);
        $scheduleDay->update([
            'departure_date' => $request->departure_date,
            'arrival_date' => $request->arrival_date,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' updated  schedule day of a flight',
            'type' => 'update',
        ]);

        return success($scheduleDay->with('times')->find($scheduleDay->schedule_day_id), 'this day updated successfully');
    }

    //Add Schedule Time To Specific Day Function
    public function addTime(ScheduleDay $scheduleDay, Request $request)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added  schedule time to a flight',
            'type' => 'insert',
        ]);
        $request->validate([
            'departure_time' => 'required',
            'arrival_time' => 'required',
        ]);

        $departureTime = Carbon::parse($request->departure_time);
        $arrivalTime = Carbon::parse($request->arrival_time);
        $duration = $departureTime->diffInMinutes($arrivalTime);

        $time = ScheduleTime::create([
            'schedule_day_id' => $scheduleDay->schedule_day_id,
            'departure_time' => $request->departure_time,
            'arrival_time' => $request->arrival_time,
            'duration' => $duration,
        ]);

        return success($time, 'this time updated successfully');
    }

    //Delete Schedule Day Function
    public function deleteScheduleDay(ScheduleDay $scheduleDay)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted  schedule day of a flight',
            'type' => 'delete',
        ]);
        // dd($scheduleDay->times());
        $scheduleDay->times()->forceDelete();
        $scheduleDay->forceDelete();

        return success(null, 'this schedule day and its times deleted successfully');
    }

    //Delete Schedule Time Function
    public function deleteScheduleTime(ScheduleTime $scheduleTime)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted  schedule time of a flight',
            'type' => 'delete',
        ]);
        $scheduleTime->forceDelete();

        return success(null, 'this schedule time deleted successfully');
    }

    //Get Flight Schedules Function
    public function getFlightSchedules(Flight $flight)
    {
        $schedules = $flight->days()->with('times')->orderBy('schedule_day_id', 'desc')->get();

        return success($schedules, null);
    }

    //Get Schedule Day Information
    public function getScheduleDayInformation(ScheduleDay $scheduleDay)
    {
        return success($scheduleDay->times, null);
    }
}