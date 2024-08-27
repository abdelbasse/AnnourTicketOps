<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index(){
        return view('Loging');
    }

    public function login(Request $req){
        $validatedData = $req->validate([
            'mail' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt(['email' => $req->mail, 'password' => $req->password])) {
            // log of user authetificaiton
            LoginLog::create([
                'userId'=> auth()->user()->id,
                'isLogged'=>true
            ]);
            // Set or update the cookie with user ID
            $userId = auth()->user()->id;
            $cookieName = 'user_id';
            $cookieValue = $userId;
            $cookieExpiration = time() + (86400 * 30 * 3); // 30 days

            if (isset($_COOKIE[$cookieName])) {
                setcookie($cookieName, $cookieValue, $cookieExpiration, "/");
            } else {
                setcookie($cookieName, $cookieValue, $cookieExpiration, "/");
            }
            return redirect()->route('home');
        }
        return redirect()->back()->withErrors([
            'login_error' => 'Invalid credentials. Please try again.',
        ]);
    }

    public function logout(){
        if(Auth()->check()){

            LoginLog::create([
                'userId'=> auth()->user()->id,
                'isLogged'=>false
            ]);
            // save the log that he logout at time
            Auth::logout();
        }
        return redirect()->route('home');
    }
}
