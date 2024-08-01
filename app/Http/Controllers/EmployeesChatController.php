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

        // Open a process
        $descriptorspec = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"]   // stderr
        ];

        $process = proc_open($command, $descriptorspec, $pipes, null, null);

        if (is_resource($process)) {
            // Close stdin since we don't need to send any input
            fclose($pipes[0]);

            // Set headers for streaming response
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');

            // Continuously read the output from the process
            while ($output = fgets($pipes[1])) {
                echo "data: " . json_encode(['answer' => $output]) . "\n\n";
                ob_flush();
                flush();
            }

            // Close stdout and stderr pipes
            fclose($pipes[1]);
            fclose($pipes[2]);

            // Close the process
            $return_value = proc_close($process);

            // Check if the process ended with an error
            if ($return_value !== 0) {
                Log::error('Python script returned an error.', ['return_value' => $return_value]);
                echo "data: " . json_encode(['error' => 'Chat processing failed']) . "\n\n";
                ob_flush();
                flush();
            }
        } else {
            Log::error('Failed to open process for Python script.');
            return response()->json(['error' => 'Failed to open process for Python script.'], 500);
        }
    }
}

//max_execution_time