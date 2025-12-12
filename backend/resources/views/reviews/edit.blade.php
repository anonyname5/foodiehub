@extends('layouts.app')

@section('title', 'Edit Review - FoodieHub')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Review</h1>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">
                <a href="{{ route('restaurants.show', $review->restaurant_id) }}" class="hover:text-orange-500">
                    {{ $review->restaurant->name }}
                </a>
            </h2>
            <p class="text-gray-600">{{ $review->restaurant->cuisine }} • {{ $review->restaurant->location }}</p>
        </div>

        <form action="{{ route('reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-6">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ratings</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Food</label>
                        <select name="food_rating" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('food_rating', $review->food_rating) == $i ? 'selected' : '' }}>
                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Service</label>
                        <select name="service_rating" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('service_rating', $review->service_rating) == $i ? 'selected' : '' }}>
                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Ambiance</label>
                        <select name="ambiance_rating" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('ambiance_rating', $review->ambiance_rating) == $i ? 'selected' : '' }}>
                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Value</label>
                        <select name="value_rating" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('value_rating', $review->value_rating) == $i ? 'selected' : '' }}>
                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Review Title</label>
                <input type="text" name="title" id="title" required maxlength="255" 
                       value="{{ old('title', $review->title) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                       placeholder="Summarize your experience">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Review Content</label>
                <textarea name="content" id="content" required rows="6" maxlength="5000"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                          placeholder="Share your detailed experience...">{{ old('content', $review->content) }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="recommend" value="1" 
                           {{ old('recommend', $review->recommend) ? 'checked' : '' }}
                           class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">I recommend this restaurant</span>
                </label>
            </div>

            @if($review->images && $review->images->count() > 0)
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Photos</label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach($review->images as $image)
                        <img src="{{ image_url($image->path) }}" alt="Review image" class="w-full h-24 object-cover rounded">
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mb-6">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Add More Photos (Optional)</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                <p class="text-xs text-gray-500 mt-1">You can upload additional images</p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    Update Review
                </button>
                <a href="{{ route('restaurants.show', $review->restaurant_id) }}" 
                   class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

