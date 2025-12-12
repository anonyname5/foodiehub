@extends('layouts.app')

@section('title', 'Restaurant Reviews - FoodieHub')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Restaurant Reviews</h1>
        <p class="text-gray-600 mt-2">View all reviews for {{ $restaurant->name }}</p>
    </div>

    <div class="bg-white rounded-lg shadow mb-4">
        <div class="p-4 border-b border-gray-200">
            <form action="{{ route('restaurant-owner.reviews') }}" method="GET" class="flex gap-3">
                <select name="status" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">All Reviews</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('restaurant-owner.reviews') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Clear
                </a>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            @forelse($reviews as $review)
            <div class="border-b border-gray-200 pb-6 mb-6 last:border-0 last:pb-0 last:mb-0">
                <div class="flex items-start">
                    <img src="{{ image_url($review->user->avatar) }}" alt="{{ $review->user->name }}" class="w-12 h-12 rounded-full mr-4">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $review->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($review->status == 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">Pending</span>
                                @elseif($review->status == 'approved')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Approved</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">Rejected</span>
                                @endif
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600">{{ number_format($review->overall_rating, 1) }}</span>
                                </div>
                            </div>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h4>
                        <p class="text-gray-700 mb-3">{{ $review->content }}</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-600">
                            <div><span class="font-semibold">Food:</span> {{ $review->food_rating }}/5</div>
                            <div><span class="font-semibold">Service:</span> {{ $review->service_rating }}/5</div>
                            <div><span class="font-semibold">Ambiance:</span> {{ $review->ambiance_rating }}/5</div>
                            <div><span class="font-semibold">Value:</span> {{ $review->value_rating }}/5</div>
                        </div>
                        @if($review->recommend)
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                <i class="fas fa-thumbs-up mr-1"></i>Recommended
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <i class="fas fa-comments text-gray-400 text-6xl mb-4"></i>
                <p class="text-gray-600">No reviews found</p>
            </div>
            @endforelse
        </div>
        <div class="p-6 border-t border-gray-200">
            {{ $reviews->links() }}
        </div>
    </div>
</div>
@endsection

