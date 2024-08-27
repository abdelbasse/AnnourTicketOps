<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Check if the request has a file
        if ($request->hasFile('upload')) {
            // Get the uploaded file
            $file = $request->file('upload');
            // Generate a unique filename
            $filename = time() . '.' . $file->getClientOriginalExtension();
            // Define the file path
            $filePath = 'img/Rapports/' . $filename;
            // Move the file to the correct directory
            $file->move(public_path('img/Rapports'), $filename);
            // Create the URL for the uploaded image
            $url = asset($filePath);

            // Return a success JSON response
            return response()->json([
                'uploaded' => true,
                'url' => $url
            ]);
        }

        // Return a JSON response if no file is found
        return response()->json([
            'uploaded' => false,
            'error' => [
                'message' => 'No file uploaded . no file found at all first'
            ]
        ], 400);
    }
}
