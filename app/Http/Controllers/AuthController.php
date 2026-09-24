<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $u = Auth::user();
            if ($u->isAdmin()) return redirect()->route('admin.dashboard');
            if ($u->hasRole('creator')) return redirect()->route('creator.dashboard');
            return redirect()->route('business.home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email','password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email'=>'Your account is disabled. Contact support.']);
            }
            // Redirect by role
            if ($user->isAdmin()) return redirect()->intended(route('admin.dashboard'));
            if ($user->hasRole('creator')) return redirect()->intended(route('creator.dashboard'));
            return redirect()->intended(route('business.home'));
        }

        return back()->withErrors(['email'=>'Invalid email or password.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing')->with('toast','Logged out');
    }
}
