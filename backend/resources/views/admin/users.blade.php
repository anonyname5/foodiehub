@extends('layouts.admin')

@section('title', 'Users Management - FoodieHub')
@section('page-title', 'Users')
@section('page-description', 'Manage platform users')

@section('content')
    <section class="panel mb-4">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-filter text-orange-500"></i> Filters</h2>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.users') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name/email/location" 
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                <select name="status" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">All statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <select name="sort_by" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="most_reviews" {{ request('sort_by') == 'most_reviews' ? 'selected' : '' }}>Most reviews</option>
                </select>
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.users') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-users text-orange-500"></i> Users ({{ $users->total() }})</h2>
        </div>
        <div class="panel-body divide-y divide-gray-200">
            @forelse($users as $user)
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center flex-1">
                        <img src="{{ image_url($user->avatar) }}" 
                             alt="{{ $user->name }}" 
                             class="w-12 h-12 rounded-full mr-4">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">{{ $user->name }}</div>
                            <div class="text-sm text-gray-600">{{ $user->email }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $user->reviews_count }} reviews • 
                                {{ $user->favorite_restaurants_count }} favorites •
                                Joined {{ $user->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($user->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-orange-500 hover:text-orange-600">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 py-8 text-center">No users found</p>
            @endforelse
        </div>
        <div class="panel-footer">
            {{ $users->links() }}
        </div>
    </section>
@endsection

