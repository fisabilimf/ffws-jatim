<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js', 'resources/js/admin.js'])
    
    <!-- Additional CSS -->
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @include('admin.components.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            @include('admin.components.topnav')
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <!-- Page Header -->
                @hasSection('page-header')
                    @yield('page-header')
                @else
                    <div class="mb-6">
                        <h1 class="text-2xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                        @hasSection('page-description')
                            <p class="mt-2 text-sm text-gray-600">@yield('page-description')</p>
                        @endif
                    </div>
                @endif
                
                <!-- Flash Messages -->
                @include('admin.components.flash-messages')
                
                <!-- Main Content -->
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    @stack('scripts')
</body>
</html>


