@extends('layouts.app')

@section('title', 'My Profile - FoodieHub')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">My Profile</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <div class="text-center mb-6">
                        <img src="{{ image_url($user->avatar) }}" 
                             alt="{{ $user->name }}" 
                             class="w-20 h-20 rounded-full object-cover mx-auto mb-4 border-4 border-orange-500 shadow-md">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-orange-500 mb-2">{{ $reviews->total() }}</div>
                        <div class="text-gray-600">Reviews Written</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-orange-500 mb-2">{{ $favorites->total() }}</div>
                        <div class="text-gray-600">Favorites</div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 text-center">
                        <div class="text-3xl font-bold text-orange-500 mb-2">
                            @if($reviews->count() > 0)
                                {{ number_format($reviews->avg(function($review) { return ($review->food_rating + $review->service_rating + $review->ambiance_rating + $review->value_rating) / 4; }), 1) }}
                            @else
                                0.0
                            @endif
                        </div>
                        <div class="text-gray-600">Average Rating</div>
                    </div>
                </div>

                <!-- Profile Settings -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Profile Settings</h2>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                            <textarea name="bio" id="bio" rows="4" maxlength="1000"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                            Update Profile
                        </button>
                    </form>
                </div>

                <!-- My Reviews -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">My Reviews</h2>
                    
                    @forelse($reviews as $review)
                        <div class="border-b border-gray-200 pb-4 mb-4 last:border-0 last:mb-0">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-800">
                                        <a href="{{ route('restaurants.show', $review->restaurant_id) }}" class="hover:text-orange-500">
                                            {{ $review->restaurant->name }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-orange-500 font-bold mr-1">
                                        {{ number_format(($review->food_rating + $review->service_rating + $review->ambiance_rating + $review->value_rating) / 4, 1) }}
                                    </span>
                                    <i class="fas fa-star text-orange-500 text-xs"></i>
                                </div>
                            </div>
                            <h4 class="font-medium text-gray-700 mb-1">{{ $review->title }}</h4>
                            <p class="text-gray-600 text-sm">{{ Str::limit($review->content, 150) }}</p>
                            <div class="mt-2 flex gap-2">
                                <a href="{{ route('reviews.edit', $review->id) }}" class="text-orange-500 hover:text-orange-600 text-sm">Edit</a>
                                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline"
                                      data-confirm-title="Delete Review" 
                                      data-confirm-message="Are you sure you want to delete this review? This action cannot be undone.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600 text-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">You haven't written any reviews yet.</p>
                    @endforelse

                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

