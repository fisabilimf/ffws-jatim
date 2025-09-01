<!-- Sidebar -->
<aside class="bg-white shadow-lg w-64 min-h-screen flex-shrink-0 transition-all duration-300 ease-in-out" 
       x-data="{ open: true }" 
       :class="{ '-translate-x-full': !open, 'translate-x-0': open }">
    
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <span class="ml-3 text-xl font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</span>
        </div>
        
        <!-- Mobile close button -->
        <button @click="open = false" class="lg:hidden text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="mt-6 px-3">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200
                      {{ request()->routeIs('admin.dashboard') 
                         ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' 
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                Dashboard
            </a>
            
            <!-- Users Management -->
            <a href="{{ route('admin.users.index') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200
                      {{ request()->routeIs('admin.users.*') 
                         ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' 
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                Manajemen User
            </a>
            
            <!-- Settings -->
            <a href="{{ route('admin.settings.index') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200
                      {{ request()->routeIs('admin.settings.*') 
                         ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' 
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Pengaturan
            </a>
        </div>
        
        <!-- Divider -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="space-y-1">
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="group w-full flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                        <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>
</aside>

<!-- Mobile overlay -->
<div x-show="!open" 
     @click="open = true"
     class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>


