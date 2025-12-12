@extends('layouts.app')

@section('title', 'Claim Restaurant - FoodieHub')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Claim Your Restaurant</h1>
        <p class="text-gray-600 mt-2">Select a restaurant to claim and start managing it</p>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif

    @if($availableRestaurants->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Available Restaurants</h2>
            <p class="text-sm text-gray-600 mt-1">Select a restaurant to claim as yours</p>
        </div>
        <div class="p-6">
            <form action="{{ route('restaurant-owner.claim.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    @foreach($availableRestaurants as $restaurant)
                    <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 cursor-pointer transition">
                        <input type="radio" name="restaurant_id" value="{{ $restaurant->id }}" class="mt-1 mr-4" required>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">{{ $restaurant->name }}</h3>
                                <span class="text-sm text-gray-600">{{ $restaurant->price_range }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $restaurant->cuisine }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $restaurant->location }}
                            </p>
                            @if($restaurant->description)
                            <p class="text-sm text-gray-700 mt-2">{{ \Illuminate\Support\Str::limit($restaurant->description, 100) }}</p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">
                        <i class="fas fa-check mr-2"></i>Claim Selected Restaurant
                    </button>
                    <a href="{{ route('home') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-store text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Available Restaurants</h3>
        <p class="text-gray-600 mb-4">All restaurants have been claimed or there are no restaurants in the system yet.</p>
        <p class="text-sm text-gray-500">Please contact an administrator to add a new restaurant or assign one to you.</p>
    </div>
    @endif
</div>
@endsection

