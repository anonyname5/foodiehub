<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'restaurants' => Restaurant::count(),
            'users' => User::where('is_admin', false)->count(),
            'reviews' => Review::count(),
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

        $recentUsers = User::where('is_admin', false)
            ->latest()
            ->limit(5)
            ->get();

        $recentReviews = Review::with(['user', 'restaurant'])
            ->latest()
            ->limit(5)
            ->get();

        $pendingReviews = Review::where('status', 'pending')
            ->with(['user', 'restaurant'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentReviews', 'pendingReviews'));
    }

    /**
     * Show users management page
     */
    public function users(Request $request)
    {
        $query = User::where('is_admin', false)
            ->withCount(['reviews', 'favoriteRestaurants']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
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

        $users = $query->paginate(15);

        return view('admin.users', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser($id)
    {
        $user = User::with(['reviews.restaurant', 'favoriteRestaurants'])
            ->withCount(['reviews', 'favoriteRestaurants'])
            ->where('is_admin', false)
            ->findOrFail($id);

        return view('admin.user-show', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::where('is_admin', false)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'bio' => 'sometimes|string|max:1000',
            'location' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
            'is_public' => 'sometimes|boolean',
            'email_notifications' => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $id)
                        ->with('success', 'User updated successfully');
    }

    /**
     * Ban user
     */
    public function banUser($id)
    {
        $user = User::where('is_admin', false)->findOrFail($id);
        $user->update([
            'is_active' => false,
            'banned_at' => now()
        ]);

        return back()->with('success', 'User has been banned successfully');
    }

    /**
     * Unban user
     */
    public function unbanUser($id)
    {
        $user = User::where('is_admin', false)->findOrFail($id);
        $user->update([
            'is_active' => true,
            'banned_at' => null
        ]);

        return back()->with('success', 'User has been unbanned successfully');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::where('is_admin', false)->findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')
                        ->with('success', 'User deleted successfully');
    }

    /**
     * Show restaurants management page
     */
    public function restaurants(Request $request)
    {
        $query = Restaurant::with(['images', 'primaryImage'])
            ->withCount('reviews');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('cuisine', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $restaurants = $query->latest()->paginate(15);

        return view('admin.restaurants', compact('restaurants'));
    }

    /**
     * Show reviews management page
     */
    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'restaurant']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        } else {
            // Default to pending reviews
            $query->where('status', 'pending');
        }

        $reviews = $query->latest()->paginate(15);

        return view('admin.reviews', compact('reviews'));
    }

    /**
     * Approve review
     */
    public function approveReview($id)
    {
        $review = Review::findOrFail($id);
        $review->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        return back()->with('success', 'Review approved successfully');
    }

    /**
     * Reject review
     */
    public function rejectReview($id)
    {
        $review = Review::findOrFail($id);
        $review->update([
            'status' => 'rejected',
            'rejected_at' => now()
        ]);

        return back()->with('success', 'Review rejected successfully');
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $settings = [
            'site_name' => config('app.name', 'FoodieHub'),
            'site_description' => 'Discover and review amazing restaurants',
            'allow_registration' => true,
            'require_email_verification' => false,
            'max_reviews_per_day' => 10,
            'min_review_length' => 10,
            'max_review_length' => 1000,
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'sometimes|string|max:255',
            'site_description' => 'sometimes|string|max:500',
            'allow_registration' => 'sometimes|boolean',
            'require_email_verification' => 'sometimes|boolean',
            'max_reviews_per_day' => 'sometimes|integer|min:1|max:100',
            'min_review_length' => 'sometimes|integer|min:1|max:500',
            'max_review_length' => 'sometimes|integer|min:10|max:5000',
        ]);

        // In a real app, save to database or config file
        // For now, just return success

        return back()->with('success', 'Settings updated successfully');
    }
}

