<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    //
    public function index()
    {
        return view('profile');
    }

    public function change(Request $req)
    {
        if ($req->type == 'pass') {
            $validation = $req->validate([
                'new_password' => 'required|confirmed|min:8',
                'new_password_confirmation' => 'required'
            ]);
            $User = User::find(auth()->user()->id);
            $User->update([
                'password' => Hash::make($req->new_password)
            ]);
            return redirect()->route('personal-profile');
        } else if ($req->type == 'info') {
            $User = User::find(auth()->user()->id);
            $User->update([
                'Fname' => $req->Fname,
                'Lname' => $req->Lname,
                'tell' => $req->tell,
            ]);
            return response()->json(['success' => true]);
        }
        //  else if ($req->type == 'pic') {
        //     $req->validate([
        //         'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        //     ]);

        //     // Check if the user has a custom profile image
        //     if (auth()->user()->imgUrl != 'img/users/user.png') {
        //         // Delete the current profile image
        //         $imagePath = public_path(auth()->user()->imgUrl);
        //         if (File::exists($imagePath)) {
        //             File::delete($imagePath);
        //         }
        //     }
        //     // Save the profile image
        //     if ($req->hasFile('profile_image')) {
        //         $image = $req->file('profile_image');
        //         $imageName = time() . '_' . uniqid() . '_' . auth()->user()->id . '.' . $image->getClientOriginalExtension();
        //         $imagePath = 'img/users/avatar';
        //         $image->move(public_path($imagePath), $imageName);

        //         $User = User::find(auth()->user()->id);
        //         $User->update([
        //             'imgUrl' => $imagePath . '/' . $imageName,
        //         ]);

        //         // Return the URL or other information about the uploaded image
        //         return response()->json(['success' => 'Profile image uploaded successfully.']);
        //     }
        //     return response()->json(['error' => 'Error uploading profile image.']);
        // }
        return response()->json(['success' => false]);
    }
}
