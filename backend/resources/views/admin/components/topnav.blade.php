<!-- Top Navigation -->
<header class="topnav-custom shadow-sm transition-colors duration-300">
    <div class="flex items-center justify-between h-16 px-6">
        <!-- Left side -->
        <div class="flex items-center">
            <!-- Mobile toggle button -->
            <button @click="$store.sidebar.toggle()" class="lg:hidden sidebar-toggle-btn p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <i x-show="!$store.sidebar.open" class="fas fa-bars w-6 h-6 transition-all duration-300"></i>
                <i x-show="$store.sidebar.open" class="fas fa-times w-6 h-6 transition-all duration-300"></i>
            </button>
            
            <!-- Desktop toggle button -->
            <button @click="$store.sidebar.toggle()" class="hidden lg:block sidebar-toggle-btn p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <i class="fas fa-bars transition-all duration-300"></i>
            </button>
        </div>
        
        <!-- Right side -->
        <div class="flex items-center space-x-4">
            <!-- Theme Toggle -->
            <div class="relative" x-data="themeDropdown">
                <button @click="toggle()" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <i :class="getThemeIcon($store.theme.current)" class="w-5 h-5"></i>
                </button>
                
                <!-- Theme Dropdown -->
                <div x-show="isOpen" x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="dropdown-dark absolute right-0 mt-2 w-36 rounded-md shadow-lg py-1 z-50">
                    
                    <button @click="selectTheme('light')" 
                            :class="$store.theme.current === 'light' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="flex items-center w-full px-4 py-2 text-sm transition-colors duration-200">
                        <i class="fas fa-sun mr-3 w-4 h-4 text-yellow-500"></i>
                        Terang
                    </button>
                    
                    <button @click="selectTheme('dark')" 
                            :class="$store.theme.current === 'dark' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="flex items-center w-full px-4 py-2 text-sm transition-colors duration-200">
                        <i class="fas fa-moon mr-3 w-4 h-4 text-indigo-500"></i>
                        Gelap
                    </button>
                    
                    <button @click="selectTheme('system')" 
                            :class="$store.theme.current === 'system' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                            class="flex items-center w-full px-4 py-2 text-sm transition-colors duration-200">
                        <i class="fas fa-desktop mr-3 w-4 h-4 text-gray-500"></i>
                        Sistem
                    </button>
                </div>
            </div>
            
            <!-- Notifications -->
            <button class="p-2 text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md relative transition-colors duration-200">
                <i class="fas fa-bell"></i>
                <!-- Notification badge -->
                <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-400"></span>
            </button>
            
            <!-- User dropdown -->
            <div class="relative" x-data="adminDropdown">
                <button @click="toggle()" class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="w-8 h-8 bg-blue-600 dark:bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-white">
                            {{ auth()->user()->name ? substr(auth()->user()->name, 0, 1) : 'U' }}
                        </span>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                    </div>
                    <i class="fas fa-chevron-down w-4 h-4 text-gray-400 dark:text-gray-300"></i>
                </button>
                
                <!-- Dropdown menu -->
                <div x-show="isOpen" x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="dropdown-dark absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 z-50">
                    
                    <a href="{{ route('admin.profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-user mr-3 h-4 w-4"></i>
                            Profil Saya
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-cog mr-3 h-4 w-4"></i>
                            Pengaturan
                        </div>
                    </a>
                    
                    <div class="border-t border-gray-100 dark:border-gray-600"></div>
                    
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
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


