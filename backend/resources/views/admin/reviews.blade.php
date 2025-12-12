@extends('layouts.admin')

@section('title', 'Reviews Management - FoodieHub')
@section('page-title', 'Reviews')
@section('page-description', 'Moderate and manage reviews')

@section('content')
    <section class="panel mb-4">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-filter text-orange-500"></i> Filters</h2>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.reviews') }}" method="GET" class="flex gap-3">
                <select name="status" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="" {{ !request('status') ? 'selected' : '' }}>All</option>
                </select>
                <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </form>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-star-half-alt text-orange-500"></i> Reviews ({{ $reviews->total() }})</h2>
        </div>
        <div class="panel-body divide-y divide-gray-200">
            @forelse($reviews as $review)
                <div class="py-4">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <img src="{{ image_url($review->user->avatar) }}" 
                                     alt="{{ $review->user->name }}" 
                                     class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $review->user->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        <a href="{{ route('restaurants.show', $review->restaurant_id) }}" class="hover:text-orange-500">
                                            {{ $review->restaurant->name }}
                                        </a>
                                        • {{ $review->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="font-medium text-gray-800 mb-1">{{ $review->title }}</div>
                                <div class="text-sm text-gray-600">{{ $review->content }}</div>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span>Food: {{ $review->food_rating }}/5</span>
                                <span>Service: {{ $review->service_rating }}/5</span>
                                <span>Ambiance: {{ $review->ambiance_rating }}/5</span>
                                <span>Value: {{ $review->value_rating }}/5</span>
                                @if($review->recommend)
                                    <span class="text-green-600"><i class="fas fa-thumbs-up"></i> Recommended</span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <span class="badge {{ $review->status == 'approved' ? 'badge-success' : ($review->status == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                {{ ucfirst($review->status) }}
                            </span>
                        </div>
                    </div>
                    @if($review->status == 'pending')
                        <div class="flex gap-2 mt-3">
                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600"
                                        onclick="return confirm('Are you sure you want to reject this review?')">
                                    <i class="fas fa-times mr-1"></i>Reject
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 py-8 text-center">No reviews found</p>
            @endforelse
        </div>
        <div class="panel-footer">
            {{ $reviews->links() }}
        </div>
    </section>
@endsection

