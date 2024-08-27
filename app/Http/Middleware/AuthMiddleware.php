<?php

namespace App\Http\Middleware;

use App\Models\LoginLog;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            // log of user authetificaiton
            if (isset($_COOKIE['user_id'])) {
                $userId = $_COOKIE['user_id'];
                $user = User::find($userId);

                if ($user && $user->latestLoginLog && $user->latestLoginLog->isLogged) {
                    // Log the user out
                    LoginLog::create([
                        'userId' => $userId,
                        'isLogged' => false
                    ]);

                    // Clear the cookie
                    setcookie('user_id', '', time() - 3600, "/");
                }
            }
            return redirect()->route('login-form');
        }
        return $next($request);
    }
}
