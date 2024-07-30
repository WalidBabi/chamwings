<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
class EmployeesChatController extends Controller
{
    public function employeeChat(Request $request)
    {
        $request->validate([
            'input' => 'required|string|max:1000',
        ]);

        $input = $request->input('input');

        // Call Python script for chat processing
        $result = Process::run('python C:/Users/waled/Desktop/Cham-Wings/EmployeeChatBot/Employee_chat_script.py "' . escapeshellarg($input) . '"');

        if ($result->successful()) {
            return response()->json(['answer' => trim($result->output())]);
        } else {
            return response()->json(['error' => 'Chat processing failed'], 500);
        }
    }
}

