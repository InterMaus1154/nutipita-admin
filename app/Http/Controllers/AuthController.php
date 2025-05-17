<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * Show login page
     * @return mixed
     */
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Authenticate user
     * @return mixed
     */
    public function login(LoginRequest $request)
    {
        $user = User::whereUsername($request->validated('username'))->first();
        // check if user exists
        // and password matches
        if (!$user || !Hash::check($request->validated('password'), $user->password)) {
            return redirect(status: 401)
                ->route('auth.view.login')
                ->withErrors(
                    ['auth_error' => 'Invalid credentials!']
                )->withInput(['username']);
        }

        auth()->login($user);
        return redirect()->intended(route('admin.view.dashboard'));
    }

    /**
     * logout user
     */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.view.login');
    }
}
