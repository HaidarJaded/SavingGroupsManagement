<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use function auth;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

/**
 * @group Sessions management
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     * @throws ValidationException
     */

    #function to restore login page
    public function showLoginPage()
    {
        return view('login_page');
    }
    #function to login
    public function login(LoginRequest $loginRequest)
    {
        $credentials = $loginRequest->only('email', 'password');

        if (auth()->attempt($credentials)) {
            return redirect()->intended('saving_groups');
        }

        // Authentication failed: redirect back with an error message
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($loginRequest->only('email'));
    }


    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login')->with('message', 'You have been logged out.');
    }
}
