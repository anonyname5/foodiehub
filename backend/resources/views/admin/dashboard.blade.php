@extends('layouts.admin')

@section('title', 'Admin Dashboard - FoodieHub')
@section('page-title', 'Dashboard')
@section('page-description', 'Platform overview and recent activity')

@section('content')
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-label">Restaurants</div>
            <div class="stat-value">{{ number_format($stats['restaurants']) }}</div>
            <div class="stat-subtext">Total restaurants</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Users</div>
            <div class="stat-value">{{ number_format($stats['users']) }}</div>
            <div class="stat-subtext">Active users</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Reviews</div>
            <div class="stat-value">{{ number_format($stats['reviews']) }}</div>
            <div class="stat-subtext">Total reviews</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ number_format($stats['pending_reviews']) }}</div>
            <div class="stat-subtext">Pending reviews</div>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-label">New This Month</div>
            <div class="grid grid-cols-3 gap-4 mt-4">
                <div>
                    <div class="text-2xl font-bold text-orange-500">{{ $stats['this_month']['new_users'] }}</div>
                    <div class="text-xs text-gray-600">Users</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-orange-500">{{ $stats['this_month']['new_restaurants'] }}</div>
                    <div class="text-xs text-gray-600">Restaurants</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-orange-500">{{ $stats['this_month']['new_reviews'] }}</div>
                    <div class="text-xs text-gray-600">Reviews</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active Users</div>
            <div class="stat-value">{{ number_format($stats['active_users']) }}</div>
            <div class="stat-subtext">Currently active</div>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-bolt text-orange-500"></i> Recent Activity</h2>
            </div>
            <div class="panel-body space-y-3 text-sm text-gray-700">
                @forelse($recentUsers->take(3) as $user)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <div class="flex items-center">
                            @php
                                $avatarPath = $user->avatar;
                                $avatarUrl = \Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://'])
                                    ? $avatarPath
                                    : image_url($avatarPath);
                            @endphp
                            <img src="{{ $avatarUrl }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-8 h-8 rounded-full mr-3">
                            <div>
                                <div class="font-medium">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">Joined {{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No recent users</p>
                @endforelse

                @forelse($recentReviews->take(2) as $review)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <div>
                            <div class="font-medium">{{ $review->user->name }} reviewed {{ $review->restaurant->name }}</div>
                            <div class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No recent reviews</p>
                @endforelse
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-clipboard-check text-orange-500"></i> Pending Reviews</h2>
                <a href="{{ route('admin.reviews') }}" class="text-sm text-orange-500 hover:text-orange-600">View All</a>
            </div>
            <div class="panel-body space-y-3 text-sm text-gray-700">
                @forelse($pendingReviews as $review)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <div class="flex-1">
                            <div class="font-medium">{{ $review->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $review->restaurant->name }} - {{ Str::limit($review->title, 40) }}</div>
                            <div class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-700 text-xs">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST" class="inline"
                                  data-confirm-title="Reject Review" 
                                  data-confirm-message="Are you sure you want to reject this review? The review will be hidden from the restaurant page.">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No pending reviews</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection

