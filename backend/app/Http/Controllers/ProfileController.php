<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile
     */
    public function show()
    {
        $user = Auth::user();
        $reviews = $user->reviews()->with('restaurant')->latest()->paginate(10);
        $favorites = $user->favoriteRestaurants()->with('images')->paginate(10);
        
        return view('profile.show', compact('user', 'reviews', 'favorites'));
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'is_public' => 'sometimes|boolean',
            'email_notifications' => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
                        ->with('success', 'Profile updated successfully!');
    }
}

