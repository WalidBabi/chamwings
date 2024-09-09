<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    //Get Logs Function
    public function getLogs(Request $request)
    {
        if ($request->search) {
            $logs = Log::where('message','LIKE','%'.$request->search.'%')->paginate(15);
        } else {
            $logs = Log::paginate(15);
        }
        $data = [
            'data' => $logs->items(),
            'total' => $logs->total(),
        ];

        return success($data, null);
    }

    //Get Log Information Function
    public function getLogInformation(Log $log)
    {
        return success($log, null);
    }
}