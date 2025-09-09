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
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js', 'resources/js/admin.js'])
    
    <!-- Additional CSS -->
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 no-transitions" 
      x-data="{}" 
      x-init="
          // Initialize Alpine store for sidebar state
          Alpine.store('sidebar', {
              open: window.innerWidth >= 1024,
              toggle() {
                  this.open = !this.open;
              },
              close() {
                  this.open = false;
              }
          });
          
          // Handle window resize
          window.addEventListener('resize', () => {
              if (window.innerWidth >= 1024) {
                  Alpine.store('sidebar').open = true;
              } else {
                  Alpine.store('sidebar').open = false;
              }
          });
      ">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @include('admin.components.sidebar')
        
        <!-- Mobile Overlay -->
        <div x-show="$store.sidebar.open && window.innerWidth < 1024" x-cloak
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$store.sidebar.close()"
             class="sidebar-overlay fixed inset-0 z-20 bg-gray-600 bg-opacity-75 lg:hidden"
             style="display: none;">
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col transition-all duration-300 ease-in-out" x-cloak
             :class="{ 
                 'ml-0': !$store.sidebar.open && window.innerWidth < 1024,
                 'ml-64': $store.sidebar.open && window.innerWidth < 1024,
                 'lg:ml-16': !$store.sidebar.open && window.innerWidth >= 1024,
                 'lg:ml-64': $store.sidebar.open && window.innerWidth >= 1024
             }"
             style="min-height: 100vh;">
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
    
    <!-- SweetAlert Component -->
    <x-admin.sweetalert />

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>


