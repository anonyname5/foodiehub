<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user->only(['id', 'name', 'email', 'bio', 'avatar', 'is_public', 'email_notifications'])
        ], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Check if this is an admin login request
            $isAdminLogin = $request->boolean('admin', false);
            
            if ($isAdminLogin && !$user->isAdmin()) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Admin privileges required.'
                ], 403);
            }

            // Update last login timestamp
            $user->updateLastLogin();
            
            // Determine what user data to return
            $userData = $user->only(['id', 'name', 'email', 'bio', 'avatar', 'is_public', 'email_notifications']);
            
            if ($isAdminLogin || $user->isAdmin()) {
                $userData = array_merge($userData, [
                    'is_admin' => $user->is_admin,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                    'last_login_at' => $user->last_login_at,
                    'location' => $user->location
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $userData,
                'is_admin' => $user->isAdmin()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => $user->only(['id', 'name', 'email', 'bio', 'avatar', 'is_public', 'email_notifications'])
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|string|max:255',
            'is_public' => 'sometimes|boolean',
            'email_notifications' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'name', 'email', 'bio', 'avatar', 'is_public', 'email_notifications'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user->only(['id', 'name', 'email', 'bio', 'avatar', 'is_public', 'email_notifications'])
        ]);
    }

    /**
     * Check if user is authenticated.
     */
    public function checkAuth(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'authenticated' => $user ? true : false,
            'user' => $user ? $user->only(['id', 'name', 'email', 'bio', 'avatar', 'is_public', 'email_notifications']) : null
        ]);
    }
}
