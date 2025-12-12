@extends('layouts.admin')

@section('title', 'Edit Restaurant - FoodieHub')
@section('page-title', 'Edit Restaurant')
@section('page-description', 'Update restaurant information')

@section('content')
    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-edit text-orange-500"></i> Edit Restaurant</h2>
            <div class="flex gap-2">
                <a href="{{ route('restaurants.show', $restaurant->id) }}" target="_blank" class="btn btn-secondary">
                    <i class="fas fa-external-link-alt mr-2"></i>View on Site
                </a>
                <a href="{{ route('admin.restaurants') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.restaurants.update', $restaurant->id) }}" method="POST">
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
                            <option value="$" {{ old('price_range', $restaurant->price_range) == '$' ? 'selected' : '' }}>$ - Budget Friendly</option>
                            <option value="$$" {{ old('price_range', $restaurant->price_range) == '$$' ? 'selected' : '' }}>$$ - Moderate</option>
                            <option value="$$$" {{ old('price_range', $restaurant->price_range) == '$$$' ? 'selected' : '' }}>$$$ - Expensive</option>
                            <option value="$$$$" {{ old('price_range', $restaurant->price_range) == '$$$$' ? 'selected' : '' }}>$$$$ - Very Expensive</option>
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

                    <div>
                        <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Owner</label>
                        <select id="owner_id" name="owner_id"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">No owner assigned</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" {{ old('owner_id', $restaurant->owner_id) == $owner->id ? 'selected' : '' }}>
                                    {{ $owner->name }} ({{ $owner->email }})
                                    @if($owner->restaurant_id == $restaurant->id) - Current Owner @endif
                                </option>
                            @endforeach
                        </select>
                        @error('owner_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Assign or change the restaurant owner. They will be able to manage this restaurant.</p>
                    </div>

                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $restaurant->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Active (visible to users)</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">
                        <i class="fas fa-save mr-2"></i>Update Restaurant
                    </button>
                    <a href="{{ route('admin.restaurants') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection

