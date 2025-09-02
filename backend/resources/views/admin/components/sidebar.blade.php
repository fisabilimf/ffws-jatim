<!-- Sidebar -->
<aside class="bg-white shadow-lg w-64 min-h-screen flex-shrink-0 transition-all duration-300 ease-in-out fixed lg:relative z-30" 
       x-data="{ open: false }" 
       :class="{ '-translate-x-full lg:translate-x-0': !open, 'translate-x-0': open }"
       x-init="open = window.innerWidth >= 1024">
    
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center group hover:opacity-80 transition">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden bg-white">
                <img src="{{ asset('assets/images/PUSDAJATIM.png') }}" alt="Logo PUSDAJATIM" class="object-contain w-full h-full" />
            </div>
            <span class="ml-3 text-xl font-semibold text-gray-900">FFWS</span>
        </a>
        
        <!-- Toggle button -->
        <button @click="open = !open" 
                class="text-gray-500 hover:text-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 rounded-md p-1">
            <!-- Burger Icon -->
            <i x-show="!open" class="fas fa-bars w-6 h-6 transition-all duration-300"></i>
            <!-- Close Icon -->
            <i x-show="open" class="fas fa-times w-6 h-6 transition-all duration-300"></i>
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
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>
            
            <!-- Users Management -->
            <a href="{{ route('admin.users.index') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200
                      {{ request()->routeIs('admin.users.*') 
                         ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' 
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-users mr-3"></i>
                Manajemen User
            </a>
            
            <!-- Settings -->
            <a href="{{ route('admin.settings.index') }}" 
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200
                      {{ request()->routeIs('admin.settings.*') 
                         ? 'bg-blue-100 text-blue-700 border-r-2 border-blue-700' 
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-cog mr-3"></i>
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
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>
</aside>
