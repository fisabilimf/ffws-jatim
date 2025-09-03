<!-- Sidebar -->
<aside class="bg-white min-h-screen flex-shrink-0 transition-all duration-300 ease-in-out fixed lg:relative z-30 sidebar-mobile lg:sidebar-desktop" 
       :class="{ 
           '-translate-x-full': !$store.sidebar.open && window.innerWidth < 1024,
           'translate-x-0': $store.sidebar.open || window.innerWidth >= 1024,
           'w-16': !$store.sidebar.open && window.innerWidth >= 1024,
           'w-64': $store.sidebar.open || window.innerWidth < 1024
       }">
    
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center group sidebar-logo">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden bg-white">
                <img src="{{ asset('assets/images/PUSDAJATIM.png') }}" alt="Logo PUSDAJATIM" class="object-contain w-full h-full" />
            </div>
            <!-- Full logo text (hidden when sidebar is collapsed) -->
            <span class="ml-3 text-xl font-semibold text-gray-900 transition-opacity duration-300"
                  :class="{ 'opacity-0 lg:opacity-100': $store.sidebar.open, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">FFWS</span>
        </a>
        
        <!-- Toggle button (hidden on desktop) -->
        <button @click="$store.sidebar.toggle()" 
                class="lg:hidden sidebar-toggle-btn text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 rounded-md p-1">
            <!-- Burger Icon -->
            <i x-show="!$store.sidebar.open" class="fas fa-bars w-6 h-6 transition-all duration-300"></i>
            <!-- Close Icon -->
            <i x-show="$store.sidebar.open" class="fas fa-times w-6 h-6 transition-all duration-300"></i>
        </button>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="mt-6 px-3">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="sidebar-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md relative
                      {{ request()->routeIs('admin.dashboard') 
                         ? 'active' 
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :title="$store.sidebar.open ? '' : 'Dashboard'">
                <i class="fas fa-tachometer-alt transition-all duration-300"
                   :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
                <span class="transition-opacity duration-300"
                      :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Dashboard</span>
            </a>
            
            <!-- Users Management -->
            <a href="{{ route('admin.users.index') }}" 
               class="sidebar-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md relative
                      {{ request()->routeIs('admin.users.*') 
                         ? 'active' 
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :title="$store.sidebar.open ? '' : 'Manajemen User'">
                <i class="fas fa-users transition-all duration-300"
                   :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
                <span class="transition-opacity duration-300"
                      :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Manajemen User</span>
            </a>
            
            <!-- Settings -->
            <a href="{{ route('admin.settings.index') }}" 
               class="sidebar-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md relative
                      {{ request()->routeIs('admin.settings.*') 
                         ? 'active' 
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :title="$store.sidebar.open ? '' : 'Pengaturan'">
                <i class="fas fa-cog transition-all duration-300"
                   :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
                <span class="transition-opacity duration-300"
                      :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Pengaturan</span>
            </a>
        </div>
        
        <!-- Divider -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="space-y-1">
                <!-- Master Heading -->
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider transition-opacity duration-300"
                     :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Master</div>

                <!-- Master: Kabupaten -->
                <a href="{{ route('admin.master.kabupaten') }}" 
                   class="sidebar-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md relative
                          {{ request()->routeIs('admin.master.kabupaten') 
                             ? 'active' 
                             : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                   :title="$store.sidebar.open ? '' : 'Kabupaten'">
                    <i class="fas fa-city transition-all duration-300"
                       :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
                    <span class="transition-opacity duration-300"
                          :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Kabupaten</span>
                </a>

                <!-- Master: Kecamatan -->
                <a href="{{ route('admin.master.kecamatan') }}" 
                   class="sidebar-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md relative
                          {{ request()->routeIs('admin.master.kecamatan') 
                             ? 'active' 
                             : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                   :title="$store.sidebar.open ? '' : 'Kecamatan'">
                    <i class="fas fa-layer-group transition-all duration-300"
                       :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
                    <span class="transition-opacity duration-300"
                          :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Kecamatan</span>
                </a>

                <!-- Master: Desa -->
                <a href="{{ route('admin.master.desa') }}" 
                   class="sidebar-nav-item group flex items-center px-3 py-2 text-sm font-medium rounded-md relative
                          {{ request()->routeIs('admin.master.desa') 
                             ? 'active' 
                             : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                   :title="$store.sidebar.open ? '' : 'Desa'">
                    <i class="fas fa-home transition-all duration-300"
                       :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
                    <span class="transition-opacity duration-300"
                          :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Desa</span>
                </a>

                
            </div>
        </div>
    </nav>
</aside>
