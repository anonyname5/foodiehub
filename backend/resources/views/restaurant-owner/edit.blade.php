@extends('layouts.app')

@section('title', 'Edit Restaurant - FoodieHub')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Restaurant</h1>
        <p class="text-gray-600 mt-2">Update your restaurant information</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Restaurant Details</h2>
                <a href="{{ route('restaurant-owner.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('restaurant-owner.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $restaurant->name) }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="cuisine" class="block text-sm font-medium text-gray-700 mb-2">Cuisine *</label>
                        <input type="text" id="cuisine" name="cuisine" value="{{ old('cuisine', $restaurant->cuisine) }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="e.g., Italian, Chinese, Mexican">
                        @error('cuisine')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="price_range" class="block text-sm font-medium text-gray-700 mb-2">Price Range *</label>
                        <select id="price_range" name="price_range" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Select price range</option>
                            <option value="Budget" {{ old('price_range', $restaurant->price_range) == 'Budget' ? 'selected' : '' }}>Budget</option>
                            <option value="Standard" {{ old('price_range', $restaurant->price_range) == 'Standard' ? 'selected' : '' }}>Standard</option>
                            <option value="Exclusive" {{ old('price_range', $restaurant->price_range) == 'Exclusive' ? 'selected' : '' }}>Exclusive</option>
                            <option value="Premium" {{ old('price_range', $restaurant->price_range) == 'Premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                        @error('price_range')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea id="description" name="description" rows="4" required
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('description', $restaurant->description) }}</textarea>
                        @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                        <input type="text" id="address" name="address" value="{{ old('address', $restaurant->address) }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @error('address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location/City *</label>
                        <input type="text" id="location" name="location" value="{{ old('location', $restaurant->location) }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="e.g., New York, Los Angeles">
                        @error('location')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $restaurant->phone) }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                        <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude', $restaurant->latitude) }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @error('latitude')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                        <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude', $restaurant->longitude) }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @error('longitude')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">
                        <i class="fas fa-save mr-2"></i>Update Restaurant
                    </button>
                    <a href="{{ route('restaurant-owner.dashboard') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

