<?php

namespace App\Http\Controllers;

use App\Models\FileFolder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    //
    public function index(){
        $data = [
            'title' => "Fndaaa",
            'date' => date('m/d/Y'),
        ];

        $pdf = Pdf::loadView('pdf', $data);

        $uniqueFileName = uniqid() . '_' . time() . '.pdf';  // Unique name for the PDF

        // Define the path where you want to store the PDF (in the 'pdfs' directory)
        $filePath = $uniqueFileName;

        // Save file information to the database
        FileFolder::create([
            'userId' => auth()->user()->id,  // Assuming authenticated user, replace if needed
            'name' => $uniqueFileName,  // Original file name
            'isFile' => true,
            'path' => $filePath,  // Path where the file is stored
            'extension' => 'pdf',
            'parentId' => 1,  // Set parent ID if applicable
        ]);

        // Store the PDF in the storage/app/public directory
        Storage::disk('public')->put($filePath, $pdf->output());

        return $pdf->download('invoice.pdf');
    }
}
