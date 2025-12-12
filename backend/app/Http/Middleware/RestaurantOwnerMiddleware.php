<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestaurantOwnerMiddleware
{
    /**
     * Handle an incoming request.
     * Ensures the user is a restaurant owner
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
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

        // Check if user is a restaurant owner
        if (!$user->isRestaurantOwner()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant owner access required'
                ], 403);
            }
            return redirect()->route('home')->with('error', 'You must be a restaurant owner to access this page.');
        }

        return $next($request);
    }
}
