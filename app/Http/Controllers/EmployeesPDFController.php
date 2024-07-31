<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeesPDFController extends Controller
{
    public function employeeIngestPDF(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
        ]);

        set_time_limit(600);  // Set to 10 minutes
        $file = $request->file('pdf');
        $filename = $file->getClientOriginalName();

        // Check if a PDF with the same name already exists
        if (Pdf::where('filename', $filename)->exists()) {
            return response()->json(['error' => 'A PDF with this name already exists. Please rename the file and try again.'], 409);
        }

        // Save the PDF file
        $path = Storage::putFile('pdfs', $file);
        $storagePath = storage_path('app/' . $path);

        // Use environment variables for paths
        $pythonPath = escapeshellarg(env('PYTHON_PATH', 'python'));
        $scriptPath = escapeshellarg(env('PDF_INGEST_SCRIPT_PATH', 'Employee_ingest_pdf_script.py'));

        // Construct the command
        $command = "$pythonPath $scriptPath " . escapeshellarg($storagePath);

        \Log::info('Executing command: ' . $command);

        // Execute the command
        $output = shell_exec($command . ' 2>&1');

        // Check for successful ingestion
        if (strpos($output, 'PDF ingested successfully') !== false) {
            \Log::info('PDF ingested successfully', ['output' => $output]);

            $pdf = Pdf::create([
                'filename' => $filename,
                'path' => $path,
            ]);

            // Retrieve the PDF information from the database
            $pdfRecord = Pdf::find($pdf->id);

            // Return the PDF information
            return response()->json([
                'message' => 'PDF ingested successfully',
                'pdf' => $pdfRecord
            ], 200);
        } else {
            // Handle general ingestion failure
            \Log::error('PDF ingestion failed', ['output' => $output]);
            return response()->json(['error' => 'PDF could not be read provide it in English and not written by hand .', 'details' => $output], 500);
        }
    }


    public function getPDFs()
    {
        $pdfs = Pdf::all();
        return response()->json($pdfs);
    }

    public function deletePDF($id)
    {
        $pdf = Pdf::findOrFail($id);
        $pdfPath = $pdf->path;
        // Extract the PDF file name without extension
        $pdfName = pathinfo($pdfPath, PATHINFO_FILENAME);
        // dd($pdfName);
        $persistDirectory = 'C:/Users/waled/Desktop/chamwings/EmployeeChatBot/vectorstore/' . $pdfName;

        // Delete the PDF file from storage
        Storage::delete($pdf->path);

        // Delete the associated persist directory
        $this->deleteDirectory($persistDirectory);

        // Delete the PDF record from the database
        $pdf->delete();

        return response()->json(['message' => 'PDF deleted successfully'], 200);
    }

    protected function deleteDirectory($directory)
    {
        if (is_dir($directory)) {
            $files = array_diff(scandir($directory), array('.', '..'));

            foreach ($files as $file) {
                (is_dir("$directory/$file")) ? $this->deleteDirectory("$directory/$file") : unlink("$directory/$file");
            }

            rmdir($directory);
        }
    }
}

//C:/Users/waled/AppData/Local/Programs/Python/Python312/python.exe c:/Users/waled/Desktop/chamwings/EmployeeChatBot/Employee_chat_script.py "what is application development?"
//C:/Users/waled/AppData/Local/Programs/Python/Python312/python.exe C:/Users/waled/Desktop/chamwings/EmployeeChatBot/Employee_ingest_pdf_script.py C:\Users\waled\Desktop\chamwings\application_development.pdf

//upload_max_filesize = 10M
//post_max_size = 16M