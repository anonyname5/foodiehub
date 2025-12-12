<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return redirect()->route('home');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if admin login checkbox is checked
        // Checkbox with value="1" sends '1' when checked, nothing when unchecked
        $isAdminLogin = $request->filled('admin_login') && $request->input('admin_login') == '1';

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $user = Auth::user();
            
            // Check if this is an admin login request
            if ($isAdminLogin && !$user->isAdmin()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Access denied. Admin privileges required.'])->withInput();
            }

            // Update last login timestamp (if method exists)
            try {
                if (method_exists($user, 'updateLastLogin')) {
                    $user->updateLastLogin();
                } elseif (isset($user->last_login_at)) {
                    $user->update(['last_login_at' => now()]);
                }
            } catch (\Exception $e) {
                // Ignore if update fails - not critical for login
            }

            $request->session()->regenerate();

            // Redirect based on admin login checkbox
            // If checkbox was checked AND user is admin, go to admin panel
            if ($isAdminLogin && $user->isAdmin()) {
                // Clear any intended URL to force admin redirect
                $request->session()->forget('url.intended');
                return redirect()->route('admin.dashboard');
            }

            // Otherwise, redirect to intended page or home
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return redirect()->route('home');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Registration successful!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

