<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $storedPath = $file->storeAs('public/users', $filename);
            // Save path relative to storage (image_url helper will handle)
            $validated['avatar'] = 'users/' . $filename;
        }

        $user->update($validated);

        return redirect()->route('profile.show')
                        ->with('success', 'Profile updated successfully!');
    }
}

