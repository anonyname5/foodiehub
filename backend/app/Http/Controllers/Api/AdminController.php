<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        try {
            $stats = [
                'restaurants' => Restaurant::count(),
                'users' => User::where('is_admin', false)->count(),
                'reviews' => Review::count(),
                'cities' => Restaurant::distinct('city')->count(),
                'pending_reviews' => Review::where('status', 'pending')->count(),
                'active_users' => User::where('is_active', true)->where('is_admin', false)->count(),
                'this_month' => [
                    'new_users' => User::where('created_at', '>=', now()->startOfMonth())
                        ->where('is_admin', false)
                        ->count(),
                    'new_restaurants' => Restaurant::where('created_at', '>=', now()->startOfMonth())->count(),
                    'new_reviews' => Review::where('created_at', '>=', now()->startOfMonth())->count(),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users with filtering and pagination
     */
    public function getUsers(Request $request)
    {
        try {
            $query = User::where('is_admin', false)
                ->withCount(['reviews', 'favoriteRestaurants'])
                ->with(['reviews' => function ($query) {
                    $query->latest()->limit(3);
                }]);

            // Apply filters
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            }

            if ($request->has('status')) {
                $status = $request->get('status');
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            if ($request->has('location')) {
                $query->where('location', 'like', '%' . $request->get('location') . '%');
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'newest');
            switch ($sortBy) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name':
                    $query->orderBy('name');
                    break;
                case 'most_reviews':
                    $query->orderBy('reviews_count', 'desc');
                    break;
                default:
                    $query->latest();
            }

            // Pagination
            $limit = $request->get('limit', 15);
            $users = $query->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'total_pages' => $users->lastPage(),
                    'total_items' => $users->total(),
                    'per_page' => $users->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single user details
     */
    public function getUser($id)
    {
        try {
            $user = User::with(['reviews.restaurant', 'favoriteRestaurants'])
                ->withCount(['reviews', 'favoriteRestaurants'])
                ->where('is_admin', false)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update user details
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::where('is_admin', false)->findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'bio' => 'sometimes|string|max:1000',
                'location' => 'sometimes|string|max:255',
                'is_active' => 'sometimes|boolean',
                'is_public' => 'sometimes|boolean',
                'email_notifications' => 'sometimes|boolean',
            ]);

            $user->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ban/suspend user
     */
    public function banUser($id)
    {
        try {
            $user = User::where('is_admin', false)->findOrFail($id);
            
            $user->update([
                'is_active' => false,
                'banned_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User has been banned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to ban user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unban user
     */
    public function unbanUser($id)
    {
        try {
            $user = User::where('is_admin', false)->findOrFail($id);
            
            $user->update([
                'is_active' => true,
                'banned_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User has been unbanned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unban user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser($id)
    {
        try {
            $user = User::where('is_admin', false)->findOrFail($id);
            
            // Soft delete the user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve review
     */
    public function approveReview($id)
    {
        try {
            $review = Review::findOrFail($id);
            
            $review->update([
                'status' => 'approved',
                'approved_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject review
     */
    public function rejectReview($id)
    {
        try {
            $review = Review::findOrFail($id);
            
            $review->update([
                'status' => 'rejected',
                'rejected_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system settings
     */
    public function getSettings()
    {
        try {
            // You can implement a settings model or use config files
            // For now, return some basic settings
            $settings = [
                'site_name' => config('app.name', 'FoodieHub'),
                'site_description' => 'Discover and review amazing restaurants in Malaysia',
                'allow_registration' => true,
                'require_email_verification' => false,
                'max_reviews_per_day' => 10,
                'min_review_length' => 10,
                'max_review_length' => 1000,
                'featured_cities' => ['Kuala Lumpur', 'George Town', 'Johor Bahru', 'Ipoh', 'Malacca City'],
            ];

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        try {
            // Validate settings data
            $validatedData = $request->validate([
                'site_name' => 'sometimes|string|max:255',
                'site_description' => 'sometimes|string|max:500',
                'allow_registration' => 'sometimes|boolean',
                'require_email_verification' => 'sometimes|boolean',
                'max_reviews_per_day' => 'sometimes|integer|min:1|max:100',
                'min_review_length' => 'sometimes|integer|min:1|max:500',
                'max_review_length' => 'sometimes|integer|min:10|max:5000',
                'featured_cities' => 'sometimes|array',
                'featured_cities.*' => 'string|max:100',
            ]);

            // In a real application, you would save these to a settings table or config
            // For now, we'll just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'data' => $validatedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activity for dashboard
     */
    public function getRecentActivity()
    {
        try {
            $recentUsers = User::where('is_admin', false)
                ->latest()
                ->limit(5)
                ->get();

            $recentReviews = Review::with(['user', 'restaurant'])
                ->latest()
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'recent_users' => $recentUsers,
                    'recent_reviews' => $recentReviews
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recent activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
