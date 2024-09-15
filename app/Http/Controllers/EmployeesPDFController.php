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

        // Save the PDF file temporarily
        $path = Storage::putFile('pdfs', $file);
        $storagePath = storage_path('app/' . $path);

        // Use environment variables for paths
        $pythonPath = escapeshellarg(env('PYTHON_PATH', 'python'));
        $scriptPath = escapeshellarg(env('PDF_INGEST_SCRIPT_PATH', 'C:/Users/waled/Desktop/chamwings/EmployeeChatBot/Employee_ingest_pdf_script.py'));
        // dd($scriptPath);
        // Construct the command
        $command = "$pythonPath $scriptPath " . escapeshellarg($storagePath);

        \Log::info('Executing command: ' . $command);

        // Execute the command
        $output = shell_exec($command . ' 2>&1');

        // Check for successful ingestion
        if (strpos($output, 'PDF ingested successfully') !== false) {
            \Log::info('PDF ingested successfully', ['output' => $output]);

            // Create the PDF record in the database
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

            // Extract the PDF file name without extension
            $pdfName = pathinfo($path, PATHINFO_FILENAME);

            // Get the persist directory base path from the .env file
            $persistDirectoryBase = env('PERSIST_DIRECTORY');

            // Construct the full path to the persist directory
            $persistDirectory = rtrim($persistDirectoryBase, '/') . '/' . $pdfName;
            // dd($persistDirectory);
            // Delete the temporarily saved PDF since it's not valid
            Storage::delete($path);

            // Delete the associated persist directory
            $this->deleteDirectory($persistDirectory);

            // Return an error response
            return response()->json([
                'error' => 'PDF could not be read. Provide it in English and ensure it is not handwritten.',
                'details' => $output
            ], 500);
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

        // Get the persist directory base path from the .env file
        $persistDirectoryBase = "C:/Users/waled/Desktop/chamwings/EmployeeChatBot/vectorstore/";

        // Construct the full path to the persist directory
        $persistDirectory = rtrim($persistDirectoryBase, '/') . '/' . $pdfName;

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

    public function downloadPDF($id)
    {
        $pdf = Pdf::find($id);
        $pdfPath = $pdf->path;
        // dd($pdf,$pdfPath);
        if (Storage::exists($pdfPath)) {
            return Storage::download($pdfPath, $pdf->name);
        } else {
            return response()->json(['message' => 'PDF file not found'], 404);
        }
    }
}

//C:/Users/waled/AppData/Local/Programs/Python/Python312/python.exe c:/Users/waled/Desktop/chamwings/EmployeeChatBot/Employee_chat_script.py "what is application development?"
//C:/Users/waled/AppData/Local/Programs/Python/Python312/python.exe C:/Users/waled/Desktop/chamwings/EmployeeChatBot/Employee_ingest_pdf_script.py C:\Users\waled\Desktop\chamwings\application_development.pdf

//upload_max_filesize = 10M
//post_max_size = 16M