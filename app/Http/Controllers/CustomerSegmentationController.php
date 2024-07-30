<?php

namespace App\Http\Controllers;

use App\Models\SegmentationResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerSegmentationController extends Controller
{
    public function runSegmentation()
    {
        $pythonScript = base_path('app/Python/customer_segmentation.py');
        
        // Capture both output and error streams
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin
           1 => array("pipe", "w"),  // stdout
           2 => array("pipe", "w")   // stderr
        );
        
        $process = proc_open("python $pythonScript", $descriptorspec, $pipes);
        
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $return_value = proc_close($process);
            
            if ($return_value !== 0) {
                Log::error("Python script execution failed. Error: $error");
                return response()->json(['error' => "Failed to execute segmentation script. Error: $error"], 500);
            }
            
            if (empty($output)) {
                Log::error("Python script produced no output.");
                return response()->json(['error' => 'Segmentation script produced no output'], 500);
            }
            
            $result = json_decode($output, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to parse Python script output: ' . json_last_error_msg());
                Log::error('Raw output: ' . $output);
                
                // Attempt to clean the output
                $cleaned_output = preg_replace('/NaN,/', 'null,', $output);
                $result = json_decode($cleaned_output, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json(['error' => 'Failed to parse segmentation results'], 500);
                }
            }
            
            return response()->json($result);
        } else {
            Log::error("Failed to execute Python script: Unable to create process");
            return response()->json(['error' => 'Failed to execute segmentation script: Unable to create process'], 500);
        }
    }

    public function getLatestResults()
    {
        $latestResult = SegmentationResult::latest()->first();

        if (!$latestResult) {
            return response()->json(['error' => 'No segmentation results found'], 404);
        }

        return response()->json($latestResult->results);
    }
}