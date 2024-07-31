<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployeesChatController extends Controller
{
    public function chat(Request $request)
    {
        $input = $request->input('input');
        set_time_limit(1800); // Sets the maximum execution time to unlimited
        // Log the received input
        Log::info('Received input for chat processing:', ['input' => $input]);

        // Escape the paths and input text for shell arguments
        $pythonPath = escapeshellarg(env('PYTHON_PATH', 'python'));
        $scriptPath = escapeshellarg(env('CHAT_SCRIPT_PATH', 'Employee_chat_script.py'));

        $command = "$pythonPath $scriptPath " . escapeshellarg($input);

        // Log the command being executed
        Log::info('Executing Python script with command:', ['command' => $command]);

        // Execute the command using shell_exec
        $output = shell_exec($command);

        // Log the output and any potential error
        Log::info('Python script output:', ['output' => $output]);

        // Check if output is valid and return a response
        if ($output === null) {
            Log::error('Failed to execute the script or no output was returned.');
            return response()->json(['error' => 'Chat processing failed'], 500);
        }

        // Decode the JSON response from the script
        $outputData = json_decode($output, true);

        // Check if the output is valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON response from the script.', ['json_error' => json_last_error_msg()]);
            return response()->json(['error' => 'Invalid JSON response from the script.'], 500);
        }

        // Return the JSON response
        return response()->json($outputData);
    }
}


//max_execution_time