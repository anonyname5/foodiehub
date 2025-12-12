<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - FoodieHub')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin-assets/assets/css/admin.css') }}">
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <i class="fas fa-shield-alt text-orange-500 text-2xl"></i>
                <div>
                    <div class="text-lg font-bold text-gray-800">FoodieHub</div>
                    <div class="text-xs text-gray-500">Admin Panel</div>
                </div>
            </div>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="{{ route('admin.restaurants') }}" class="nav-link {{ request()->routeIs('admin.restaurants*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i> Restaurants
                </a>
                <a href="{{ route('admin.reviews') }}" class="nav-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                    <i class="fas fa-star-half-alt"></i> Reviews
                </a>
                <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </nav>
            <div class="admin-sidebar-footer">
                <div class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-sm text-gray-500">@yield('page-description', 'Platform overview')</p>
                </div>
                <div class="admin-header-actions">
                    <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-orange-500">
                        <i class="fas fa-home mr-1"></i> View Site
                    </a>
                </div>
            </header>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- Confirmation Modal --}}
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center" id="confirm-title">Confirm Delete</h3>
                <p class="text-sm text-gray-600 mb-6 text-center" id="confirm-message">Are you sure you want to delete this item? This action cannot be undone.</p>
                <div class="flex gap-3">
                    <button type="button" id="confirm-cancel" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition font-medium">
                        Cancel
                    </button>
                    <button type="button" id="confirm-ok" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition font-medium">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Confirmation Modal Handler
        let confirmCallback = null;
        const confirmModal = document.getElementById('confirm-modal');
        const confirmTitle = document.getElementById('confirm-title');
        const confirmMessage = document.getElementById('confirm-message');
        const confirmOk = document.getElementById('confirm-ok');
        const confirmCancel = document.getElementById('confirm-cancel');

        function showConfirmModal(title, message, callback) {
            confirmTitle.textContent = title || 'Confirm Delete';
            confirmMessage.textContent = message || 'Are you sure you want to delete this item? This action cannot be undone.';
            confirmCallback = callback;
            confirmModal.classList.remove('hidden');
            confirmModal.style.display = 'flex';
        }

        function hideConfirmModal() {
            confirmModal.classList.add('hidden');
            confirmModal.style.display = 'none';
            confirmCallback = null;
        }

        confirmOk.addEventListener('click', function() {
            if (confirmCallback) {
                confirmCallback();
            }
            hideConfirmModal();
        });

        confirmCancel.addEventListener('click', hideConfirmModal);
        
        // Close modal when clicking outside
        confirmModal.addEventListener('click', function(e) {
            if (e.target === confirmModal) {
                hideConfirmModal();
            }
        });

        // Handle delete forms
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.method === 'POST' && form.querySelector('input[name="_method"][value="DELETE"]')) {
                e.preventDefault();
                const title = form.dataset.confirmTitle || 'Confirm Delete';
                const message = form.dataset.confirmMessage || 'Are you sure you want to delete this item? This action cannot be undone.';
                
                showConfirmModal(title, message, function() {
                    form.submit();
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

