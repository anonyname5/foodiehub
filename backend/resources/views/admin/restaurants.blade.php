@extends('layouts.admin')

@section('title', 'Restaurants Management - FoodieHub')
@section('page-title', 'Restaurants')
@section('page-description', 'Manage restaurant listings')

@section('content')
    <section class="panel mb-4">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-filter text-orange-500"></i> Filters</h2>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.restaurants') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search restaurants..." 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                <select name="status" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">All statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.restaurants') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-store text-orange-500"></i> Restaurants ({{ $restaurants->total() }})</h2>
            <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Add New Restaurant
            </a>
        </div>
        <div class="panel-body divide-y divide-gray-200">
            @forelse($restaurants as $restaurant)
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center flex-1">
                        @php
                            $displayImage = null;
                            if ($restaurant->images && $restaurant->images->isNotEmpty()) {
                                $primaryImage = $restaurant->images->where('is_primary', true)->first();
                                $displayImage = $primaryImage ?? $restaurant->images->first();
                            }
                        @endphp
                        @if($displayImage)
                            <img src="{{ image_url($displayImage->path) }}" alt="{{ $restaurant->name }}" 
                                 class="w-16 h-16 rounded-lg object-cover mr-4">
                        @else
                            <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                <i class="fas fa-utensils text-gray-400"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">{{ $restaurant->name }}</div>
                            <div class="text-sm text-gray-600">{{ $restaurant->cuisine }} • {{ $restaurant->location }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $restaurant->reviews_count }} reviews • 
                                Rating: {{ number_format($restaurant->average_rating, 1) }} ⭐
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($restaurant->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                        @if($restaurant->owner)
                            <span class="badge badge-info">Owned by {{ $restaurant->owner->name }}</span>
                        @endif
                        <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-orange-500 hover:text-orange-600" title="View on site">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}" class="text-blue-500 hover:text-blue-600" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.restaurants.toggle-status', $restaurant->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-500 hover:text-yellow-600" title="{{ $restaurant->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas fa-{{ $restaurant->is_active ? 'pause' : 'play' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.restaurants.delete', $restaurant->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this restaurant? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-600" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 py-8 text-center">No restaurants found</p>
            @endforelse
        </div>
        <div class="panel-footer">
            {{ $restaurants->links() }}
        </div>
    </section>
@endsection

