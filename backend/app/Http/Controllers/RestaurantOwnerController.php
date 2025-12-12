<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantOwnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only require restaurant_owner middleware for routes that need it
        // Claim routes don't need it (they're for users who don't have a restaurant yet)
        $this->middleware('restaurant_owner')->except(['showClaim', 'claim']);
    }

    /**
     * Show restaurant owner dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $restaurant = $user->ownedRestaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant-owner.claim')
                            ->with('info', 'You need to claim a restaurant first.');
        }

        // Get restaurant statistics
        $stats = [
            'total_reviews' => $restaurant->reviews()->where('status', 'approved')->count(),
            'pending_reviews' => $restaurant->reviews()->where('status', 'pending')->count(),
            'average_rating' => $restaurant->average_rating ?? 0,
            'total_favorites' => $restaurant->favoritedBy()->count(),
        ];

        // Get recent reviews
        $recentReviews = $restaurant->reviews()
            ->with('user')
            ->where('status', 'approved')
            ->latest()
            ->limit(5)
            ->get();

        return view('restaurant-owner.dashboard', compact('restaurant', 'stats', 'recentReviews'));
    }

    /**
     * Show restaurant claim page
     */
    public function showClaim()
    {
        $user = Auth::user();
        
        // If user already has a restaurant, redirect to dashboard
        if ($user->ownedRestaurant) {
            return redirect()->route('restaurant-owner.dashboard');
        }

        // Get restaurants without owners
        $availableRestaurants = Restaurant::whereNull('owner_id')
                                          ->where('is_active', true)
                                          ->orderBy('name')
                                          ->get();

        return view('restaurant-owner.claim', compact('availableRestaurants'));
    }

    /**
     * Handle restaurant claim request
     */
    public function claim(Request $request)
    {
        $user = Auth::user();

        // Check if user already has a restaurant
        if ($user->ownedRestaurant) {
            return back()->with('error', 'You already own a restaurant.');
        }

        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);

        $restaurant = Restaurant::findOrFail($validated['restaurant_id']);

        // Check if restaurant already has an owner
        if ($restaurant->owner_id) {
            return back()->with('error', 'This restaurant is already claimed by another owner.');
        }

        // Link user to restaurant
        $user->update(['restaurant_id' => $restaurant->id]);
        $restaurant->update(['owner_id' => $user->id]);

        return redirect()->route('restaurant-owner.dashboard')
                        ->with('success', 'Restaurant claimed successfully! You can now manage your restaurant.');
    }

    /**
     * Show form to edit restaurant
     */
    public function edit()
    {
        $user = Auth::user();
        $restaurant = $user->ownedRestaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant-owner.claim')
                            ->with('error', 'You need to claim a restaurant first.');
        }

        return view('restaurant-owner.edit', compact('restaurant'));
    }

    /**
     * Update restaurant
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->ownedRestaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant-owner.claim')
                            ->with('error', 'You need to claim a restaurant first.');
        }

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
        ]);

        // Restaurant owners can only update certain fields (not is_active or owner_id)
        $restaurant->update($validated);

        return redirect()->route('restaurant-owner.dashboard')
                        ->with('success', 'Restaurant updated successfully!');
    }

    /**
     * Show restaurant reviews
     */
    public function reviews(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->ownedRestaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant-owner.claim')
                            ->with('error', 'You need to claim a restaurant first.');
        }

        $query = $restaurant->reviews()->with('user');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $reviews = $query->latest()->paginate(15);

        return view('restaurant-owner.reviews', compact('restaurant', 'reviews'));
    }
}
