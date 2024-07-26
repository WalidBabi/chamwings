<?php

namespace App\Http\Controllers;

use App\Model\Pdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class EmployeesPDFController extends Controller
{
    public function employeeIngestPDF(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
        ]);

        $file = $request->file('pdf');
        $path = Storage::putFile('pdfs', $file);
        $storagePath = storage_path('app/' . $path);

        // Use environment variables for paths
        $pythonPath = env('PYTHON_PATH', 'python');
        $scriptPath = env('PDF_INGEST_SCRIPT_PATH', 'Employee_ingest_pdf_script.py');

        $process = new Process([$pythonPath, $scriptPath, $storagePath]);
        $process->setWorkingDirectory(getcwd());
        $process->setTimeout(300);


        $process->setEnv([
            'PYTHONUNBUFFERED' => '1',
            'PYTHONIOENCODING' => 'UTF-8',
            'PYTHONHASHSEED' => '0',
            'PYTHONPATH' => 'C:\\Users\\waled\\AppData\\Roaming\\Python\\Python312\\site-packages'
        ]);

        \Log::info('Executing command: ' . $process->getCommandLine());
        \Log::info('Current working directory: ' . $process->getWorkingDirectory());

        try {
            $process->mustRun(function ($type, $buffer) {
                $type === Process::ERR ? \Log::error($buffer) : \Log::info($buffer);
            });

            \Log::info('Saving PDF to database');
            $pdf = Pdf::create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
            ]);

            \Log::info('PDF saved to database', ['pdf' => $pdf]);

            return response()->json(['message' => 'PDF ingested successfully']);
        } catch (ProcessFailedException $exception) {
            \Log::error('PDF ingestion failed', [
                'command' => $process->getCommandLine(),
                'output' => $process->getOutput(),
                'errorOutput' => $process->getErrorOutput(),
            ]);

            return response()->json([
                'error' => 'PDF ingestion failed',
                'details' => $process->getErrorOutput(),
                'output' => $process->getOutput(),
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

        // Extract the PDF file name without extension
        $pdfName = pathinfo($pdf->filename, PATHINFO_FILENAME);
        $persistDirectory = 'C:/Users/waled/Desktop/chamwings/EmployeeChatBot/vectorstore/' . $pdfName;

        // Delete the PDF file from storage
        Storage::delete($pdf->path);

        // Delete the associated persist directory
        $this->deleteDirectory($persistDirectory);

        // Delete the PDF record from the database
        $pdf->delete();

        return response()->json(['message' => 'PDF deleted successfully']);
    }
}
// C:/Users/waled/AppData/Local/Programs/Python/Python312/python.exe c:/Users/waled/Desktop/chamwings/EmployeeChatBot/Employee_chat_script.py "what is application development?"
//C:/Users/waled/AppData/Local/Programs/Python/Python312/python.exe C:/Users/waled/Desktop/chamwings/EmployeeChatBot/Employee_ingest_pdf_script.py C:\Users\waled\Desktop\chamwings\application_development.pdf

//upload_max_filesize = 10M
//post_max_size = 16M