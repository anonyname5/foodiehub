@extends('layouts.app')

@section('title', 'Notifications - FoodieHub')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.read-all') }}" class="inline">
            @csrf
            <button type="submit" class="text-sm text-orange-600 hover:text-orange-800">
                Mark all as read
            </button>
        </form>
        @endif
    </div>

    @if($notifications->count() > 0)
    <div class="bg-white rounded-lg shadow-md">
        <div class="divide-y divide-gray-200">
            @foreach($notifications as $notification)
            <div class="p-4 hover:bg-gray-50 transition {{ $notification->read_at ? '' : 'bg-orange-50' }}">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        @if($notification->data['type'] == 'new_review')
                            <i class="fas fa-comment text-orange-500 text-xl"></i>
                        @elseif($notification->data['type'] == 'review_response')
                            <i class="fas fa-reply text-blue-500 text-xl"></i>
                        @else
                            <i class="fas fa-bell text-gray-400 text-xl"></i>
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $notification->data['message'] ?? 'New notification' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @if(!$notification->read_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                New
                            </span>
                            @endif
                        </div>
                        @if(isset($notification->data['url']))
                        <div class="mt-2">
                            <a href="{{ $notification->data['url'] }}" class="text-sm text-orange-600 hover:text-orange-800">
                                View details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                    @if(!$notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="ml-4">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-gray-600" title="Mark as read">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No notifications</h3>
        <p class="text-gray-500">You're all caught up!</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Update notification count on page load
    fetch('{{ route("notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            const count = document.getElementById('notification-count');
            if (data.count > 0) {
                count.textContent = data.count > 99 ? '99+' : data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });
</script>
@endpush
