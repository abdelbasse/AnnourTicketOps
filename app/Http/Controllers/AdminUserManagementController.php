<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class AdminUserManagementController extends Controller
{
    private function deleteUserById($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return false; // User not found
        }

        try {
            $user->delete();
            return true; // User deleted successfully
        } catch (\Exception $e) {
            // Log or handle exception as needed
            return false; // Failed to delete user
        }
    }

    public function getUsersJson()
    {
        $ListOfUsersNoAdmin = User::whereIn('role', [3, 4])
            ->with('latestLoginLog')
            ->get();

        $ListOfAdmins = User::whereIn('role', [1, 2])
            ->where('id', '!=', auth()->user()->id)->where('id', '!=', '0')
            ->with('latestLoginLog')
            ->get();

        return response()->json(['users' => $ListOfAdmins, 'admins' => $ListOfUsersNoAdmin]);
    }

    //
    public function index()
    {
        $ListOfUsersNoAdmin = User::whereIn('role', [3, 4])
            ->with('latestLoginLog')
            ->get();

        $ListOfAdmins = User::whereIn('role', [1, 2])
            ->where('id', '!=', auth()->user()->id)->where('id', '!=', '0')
            ->with('latestLoginLog')
            ->get();

        return view('Admin.listUser')->with(['users' => $ListOfUsersNoAdmin, 'admins' => $ListOfAdmins]);
    }

    public function submit(Request $req)
    {
        if ($req->type == 'Cuser') {
            $req->validate([
                'email' => 'required',
                'role' => 'required|integer|in:1,2,3,4',
            ]);

            $nbrofEmail = User::where(['email' => $req->email])->count();

            if ($nbrofEmail == 0) {
                User::create([
                    'Fname' => $req->Fname,
                    'Lname' => $req->Lname,
                    'email' => $req->email,
                    'tell' => $req->tell,
                    'role' => $req->role,
                    'imgUrl' => 'img/users/user.png',
                    'password' => Hash::make('secret'),
                    'password_Org' => Hash::make('secret'),
                ]);

                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Email Already Exists!']);
            }
            return response()->json(['success' => false, 'message' => 'Something went Wrong!']);
        } else if ($req->type == 'Duser') {
            if ($this->deleteUserById($req->userId)) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Something went Wrong!']);
            }
        } else if ($req->type == 'Dusers') {
            try {
                // Delete users based on IDs
                User::whereIn('id', $req->usersIds)->delete();
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                // Log or handle exception as needed
                return response()->json(['success' => false, 'message' => 'Failed to delete users.']);
            }
        } else if ($req->type == 'ImportExcel') {
            $req->validate([
                'excelFile' => 'required|mimes:xls,xlsx|max:2048', // Adjust max file size as needed
            ]);

            if ($req->hasFile('excelFile')) {
                $file = $req->file('excelFile');

                // try {
                //     // Process the Excel file using Laravel-Excel
                //     $import = new UsersImport();
                //     Excel::import($import, $file);

                //     // Retrieve imported users from the import class
                //     $users = $import->getUsers();

                //     return response()->json(['success' => true, 'users' => $users]);
                // } catch (\Exception $e) {
                //     return response()->json(['success' => false, 'error' => $e->getMessage()]);
                // }
            } else {
                return response()->json(['success' => false, 'error' => 'No file uploaded']);
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Error.']);
    }
}
