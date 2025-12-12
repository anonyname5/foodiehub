<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role = 'admin'): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // For web routes, redirect to login
            if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
            }
            return redirect()->route('home')->with('error', 'Please log in to access this page.');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive'
            ], 403);
            }
            Auth::logout();
            return redirect()->route('home')->with('error', 'Your account is inactive.');
        }

        // Check admin privileges based on role requirement
        switch ($role) {
            case 'super_admin':
                if (!$user->isSuperAdmin()) {
                    if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Super admin access required'
                    ], 403);
                    }
                    return redirect()->route('home')->with('error', 'Super admin access required.');
                }
                break;
            
            case 'admin':
            default:
                if (!$user->isAdmin()) {
                    if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin access required'
                    ], 403);
                    }
                    return redirect()->route('home')->with('error', 'Admin access required.');
                }
                break;
        }

        return $next($request);
    }
}
