<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleRequest;
use App\Models\Flight;
use App\Models\ScheduleDay;
use App\Models\ScheduleTime;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    //Add Schedule Fucntion
    public function addSchedule(Flight $flight, ScheduleRequest $scheduleRequest)
    {
        $scheduleDay = ScheduleDay::create([
            'flight_id' => $flight->flight_id,
            'departure_date' => $scheduleRequest->schedule['departure_date'],
            'arrival_date' => $scheduleRequest->schedule['arrival_date'],
        ]);

        for ($i = 0; $i < count($scheduleRequest->schedule['departure_times']); $i++) {
            ScheduleTime::create([
                'schedule_day_id' => $scheduleDay->schedule_day_id,
                'departure_time' => $scheduleRequest->schedule['departure_times'][$i],
                'arrival_time' => $scheduleRequest->schedule['arrival_times'][$i],
                'duration' => $scheduleRequest->schedule['duration'],
            ]);
        }
        return success(null, 'this schedule added successfully', 201);
    }

    //Edit Day Function
    public function editDay(ScheduleDay $scheduleDay, Request $request)
    {
        $request->validate([
            'departure_date' => 'required|date',
            'arrival_date' => 'required|date',
        ]);
        $scheduleDay->update([
            'departure_date' => $request->departure_date,
            'arrival_date' => $request->arrival_date,
        ]);

        return success($scheduleDay->with('times')->find($scheduleDay->schedule_day_id), 'this day updated successfully');
    }

    //Add Schedule Time To Specific Day Function
    public function addTime(ScheduleDay $scheduleDay, Request $request)
    {
        $request->validate([
            'departure_time' => 'required',
            'arrival_time' => 'required',
            'duration' => 'required',
        ]);
        $time = ScheduleTime::create([
            'schedule_day_id' => $scheduleDay->schedule_day_id,
            'departure_time' => $request->departure_time,
            'arrival_time' => $request->arrival_time,
            'duration' => $request->duration,
        ]);

        return success($time, 'this time updated successfully');
    }

    //Delete Schedule Day Function
    public function deleteScheduleDay(ScheduleDay $scheduleDay)
    {
        // dd($scheduleDay->times());
        $scheduleDay->times()->forceDelete();
        $scheduleDay->forceDelete();

        return success(null, 'this schedule day and its times deleted successfully');
    }

    //Delete Schedule Time Function
    public function deleteScheduleTime(ScheduleTime $scheduleTime)
    {
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
