<!-- Top Navigation -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-6">
        <!-- Left side -->
        <div class="flex items-center">
            <!-- Mobile toggle button -->
            <button @click="$store.sidebar.toggle()" class="lg:hidden sidebar-toggle-btn p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                <i x-show="!$store.sidebar.open" class="fas fa-bars w-6 h-6 transition-all duration-300"></i>
                <i x-show="$store.sidebar.open" class="fas fa-times w-6 h-6 transition-all duration-300"></i>
            </button>
            
            <!-- Desktop toggle button -->
            <button @click="$store.sidebar.toggle()" class="hidden lg:block sidebar-toggle-btn p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                <i class="fas fa-bars transition-all duration-300"></i>
            </button>
        </div>
        
        <!-- Right side -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <button class="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-md relative">
                <i class="fas fa-bell"></i>
                <!-- Notification badge -->
                <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-400"></span>
            </button>
            
            <!-- User dropdown -->
            <div class="relative" x-data="adminDropdown">
                <button @click="toggle()" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-100">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-white">
                            {{ auth()->user()->name ? substr(auth()->user()->name, 0, 1) : 'U' }}
                        </span>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                    </div>
                    <i class="fas fa-chevron-down w-4 h-4 text-gray-400"></i>
                </button>
                
                <!-- Dropdown menu -->
                <div x-show="isOpen" x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                    
                    <a href="{{ route('admin.profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center">
                            <i class="fas fa-user mr-3 h-4 w-4"></i>
                            Profil Saya
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center">
                            <i class="fas fa-cog mr-3 h-4 w-4"></i>
                            Pengaturan
                        </div>
                    </a>
                    
                    <div class="border-t border-gray-100"></div>
                    
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <div class="flex items-center">
                                <i class="fas fa-sign-out-alt mr-3 h-4 w-4"></i>
                                Keluar
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>


