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

    @stack('scripts')
</body>
</html>

