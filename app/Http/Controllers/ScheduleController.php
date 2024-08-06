<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleDayRequest;
use App\Models\Flight;
use App\Models\ScheduleDay;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    //Add Schedule Days Fucntion
    public function addScheduleDays(Flight $flight, ScheduleDayRequest $scheduleDayRequest)
    {
        
        ScheduleDay::create([
            
        ]);
    }
}