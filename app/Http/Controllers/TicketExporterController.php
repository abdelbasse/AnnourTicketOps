<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TableExport;

class TicketExporterController extends Controller
{
    public function exportTable(Request $request)
    {
        // Validate and retrieve the selected IDs
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:tickets,id',
        ]);

        $ids = $request->ids;

        // Generate the filename with the current date and time
        $fileName = 'tableExcel_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = 'public/' . $fileName;

        // Store the Excel file in the public storage
        Excel::store(new TableExport($ids), $filePath);

        // Return the file download URL
        return response()->json([
            'message' => 'File generated successfully.',
            'fileUrl' => route('download.file', ['filename' => $fileName])
        ]);
    }

}
