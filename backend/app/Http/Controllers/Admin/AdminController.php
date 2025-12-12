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
        $query = Restaurant::with(['images', 'primaryImage', 'owner'])
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
     * Show form to create a new restaurant
     */
    public function createRestaurant()
    {
        $owners = User::where('is_admin', false)->whereNull('restaurant_id')->get();
        return view('admin.restaurant-create', compact('owners'));
    }

    /**
     * Store a newly created restaurant
     */
    public function storeRestaurant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cuisine' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'price_range' => 'required|in:$,$$,$$$,$$$$',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'hours' => 'nullable|array',
            'features' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $restaurant = Restaurant::create($validated);

        // If owner_id is provided, link the user to the restaurant
        if ($request->has('owner_id') && $request->owner_id) {
            $owner = User::findOrFail($request->owner_id);
            $owner->update(['restaurant_id' => $restaurant->id]);
            $restaurant->update(['owner_id' => $owner->id]);
        }

        return redirect()->route('admin.restaurants')
                        ->with('success', 'Restaurant created successfully!');
    }

    /**
     * Show form to edit a restaurant
     */
    public function editRestaurant($id)
    {
        $restaurant = Restaurant::with('owner')->findOrFail($id);
        $owners = User::where('is_admin', false)
                     ->where(function($q) use ($id) {
                         $q->whereNull('restaurant_id')->orWhere('restaurant_id', $id);
                     })
                     ->get();
        
        return view('admin.restaurant-edit', compact('restaurant', 'owners'));
    }

    /**
     * Update a restaurant
     */
    public function updateRestaurant(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cuisine' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'price_range' => 'required|in:$,$$,$$$,$$$$',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'hours' => 'nullable|array',
            'features' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        // Handle owner change
        if ($request->has('owner_id')) {
            $oldOwner = $restaurant->owner;
            
            // Remove old owner link
            if ($oldOwner) {
                $oldOwner->update(['restaurant_id' => null]);
            }

            // Set new owner
            if ($request->owner_id) {
                $newOwner = User::findOrFail($request->owner_id);
                $newOwner->update(['restaurant_id' => $restaurant->id]);
                $validated['owner_id'] = $newOwner->id;
            } else {
                $validated['owner_id'] = null;
            }
        }

        $restaurant->update($validated);

        return redirect()->route('admin.restaurants')
                        ->with('success', 'Restaurant updated successfully!');
    }

    /**
     * Delete a restaurant
     */
    public function deleteRestaurant($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        
        // Remove owner link
        if ($restaurant->owner) {
            $restaurant->owner->update(['restaurant_id' => null]);
        }

        $restaurant->delete();

        return redirect()->route('admin.restaurants')
                        ->with('success', 'Restaurant deleted successfully!');
    }

    /**
     * Toggle restaurant active status
     */
    public function toggleRestaurantStatus($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->update(['is_active' => !$restaurant->is_active]);

        $status = $restaurant->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Restaurant {$status} successfully!");
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

