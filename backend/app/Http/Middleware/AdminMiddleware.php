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
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive'
            ], 403);
        }

        // Check admin privileges based on role requirement
        switch ($role) {
            case 'super_admin':
                if (!$user->isSuperAdmin()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Super admin access required'
                    ], 403);
                }
                break;
            
            case 'admin':
            default:
                if (!$user->isAdmin()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin access required'
                    ], 403);
                }
                break;
        }

        return $next($request);
    }
}
