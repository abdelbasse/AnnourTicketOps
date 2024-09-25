<?php

namespace App\Http\Controllers;

use App\Models\FileFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileManagementController extends Controller
{
    //
    public function fetch(){
        $filesAndFolder = FileFolder::all();
        return response()->json([
            'filesAndFolders' => $filesAndFolder,
        ]);
    }

    //
    public function index(){
        $filesAndFolder = FileFolder::all();
        return view('FileManagement')->with([
            'filesAndFolders' => $filesAndFolder,
        ]);
    }

    public function submit(Request $req){
        if($req->type === "NFile"){
            $req->validate([
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf,txt,docx,xls,xlsx,xlsm,xlsb,xlt,xltx,xltm,csv',
                'folder_id' => 'required|integer',
            ]);

            $file = $req->file('file');
            $originalFileName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $uniqueFileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('/', $uniqueFileName, 'public');

            FileFolder::create([
                'userId' => auth()->user()->id,
                'name' => $originalFileName,
                'isFile' => true,
                'path' => $path,
                'parentId' => $req->folder_id,
                'extension' => $extension,
            ]);

            return response()->json(['success' => true,'message' => 'File uploaded successfully !!!',201]);
        }else if($req->type === "NFolder"){
            // Validate folder name
            $req->validate([
                'name' => 'required|string|max:255',
                'folderId' => 'nullable|integer' // Assuming folderId can be nullable
            ]);
            // Construct the new folder path
            $folderName = $req->name;

            // Store folder information in the database
            FileFolder::create([
                'userId' => auth()->user()->id, // Assuming you want to associate the folder with the authenticated user
                'name' => $folderName,
                'isFile' => false, // Since it's a folder
                'path' => null,
                'parentId' => $req->folderId, // Set the parent ID if available
            ]);

            // Return success response
            return response()->json(['message' => 'Folder created successfully'], 201);
        }else if($req->type === "update"){
            // Validate the input fields
            $req->validate([
                'id' => 'required|integer',
                'name' => 'required|string',
            ]);

            // Find the file/folder by ID
            $item = FileFolder::find($req->id);

            // Check if the item exists
            if (!$item) {
                return response()->json(['error' => 'Item not found'], 404);
            }

            // Update the 'name' column with the new name
            $item->name = $req->name;
            $item->save();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Name updated successfully!']);
        }else if($req->type === "delete"){
            $item = FileFolder::find($req->itemId);
            // If the item does not exist, return an error
            if (!$item) {
                return response()->json(['error' => 'Item not found'], 404);
            }

            // If the item is a file, delete the file from storage
            if ($item->isFile == 1) {
                // Construct the full path (prepend 'public/' to the file name)
                $filePath = 'public/' . $item->path;

                // Check if the file exists in the storage, and delete it
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                } else {
                    return response()->json(['error' => 'File not found in storage'], 404);
                }

                // Delete the file entry from the database
                $item->delete();

            } else {
                // If it's a folder (isFile = false), recursively delete its children first
                $this->deleteFolderContents($item);

                // Finally, delete the folder itself from the database
                $item->delete();
            }

            return response()->json(['success' => 'Item deleted successfully']);
        }else{
            return response()->json(['message' => 'Somthing went wrong'], 401);
        }
        return response()->json(['message' => 'Somthing went wrong'], 401);
    }

    public function OrderSubmit(Request $req){
        $data = json_decode($req->data, true);

        foreach ($data as $item) {
            $record = FileFolder::find($item['id']);

            if ($record) {
                if ($item['parentOrg'] !== null) {
                    $record->parentId = $item['parent'];
                }
                $record->save();
            }
        }
        return response()->json(['success' => true,'message' => 'File uploaded successfully !!!','data' => $req->data,201]);
    }

    // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // Function to delete a file from the storage and database
    private function deleteFolderContents(FileFolder $folder)
    {
        // Get all children of the folder
        $children = $folder->children;

        // Loop through each child and delete them
        foreach ($children as $child) {
            if ($child->isFile) {
                // Construct the full path (prepend 'public/' to the file name)
                $filePath = 'public/' . $child->path;

                // Check if the file exists in the storage, and delete it
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                } else {
                    return response()->json(['error' => 'File not found in storage'], 404);
                }
            } else {
                // If it's a folder, recursively delete its contents
                $this->deleteFolderContents($child);
            }

            // Delete the child (whether file or folder) from the database
            $child->delete();
        }
    }
}
