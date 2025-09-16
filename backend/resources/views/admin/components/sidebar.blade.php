<!-- Sidebar -->
<aside
   class="bg-white min-h-screen flex-shrink-0 transition-transform duration-200 ease-out fixed top-0 left-0 lg:sticky lg:top-0 z-30 border-r border-gray-200 overflow-y-auto"
   x-cloak :class="{ 
           '-translate-x-full': !$store.sidebar.open && window.innerWidth < 1024,
           'translate-x-0': $store.sidebar.open || window.innerWidth >= 1024,
           'w-16': !$store.sidebar.open && window.innerWidth >= 1024,
           'w-64': $store.sidebar.open && window.innerWidth >= 1024
       }">

   <!-- Logo -->
   <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center group sidebar-logo">
         <div class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden bg-white">
            <img src="{{ asset('assets/images/PUSDAJATIM.png') }}" alt="Logo PUSDAJATIM"
               class="object-contain w-full h-full" />
         </div>
         <!-- Full logo text (hidden when sidebar is collapsed) -->
         <span class="ml-3 text-xl font-semibold text-gray-900"
            :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 w-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">FFWS</span>
      </a>


   </div>

   <!-- Navigation Menu -->
   <nav class="mt-6"
      :class="{ 'px-3': $store.sidebar.open || window.innerWidth < 1024, 'px-2': !$store.sidebar.open && window.innerWidth >= 1024 }">
      <div class="space-y-1">
         <!-- Dashboard -->
         <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                      {{ request()->routeIs('admin.dashboard')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
            :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
            <i class="fas fa-tachometer-alt text-base"
               :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
            <span
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Dashboard</span>
         </a>

         <!-- Users Management -->
         <a href="{{ route('admin.users.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                      {{ request()->routeIs('admin.users.*')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
            :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
            <i class="fas fa-users text-base"
               :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
            <span
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Manajemen
               User</span>
         </a>

         <!-- Settings -->
         <a href="{{ route('admin.settings.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                      {{ request()->routeIs('admin.settings.*')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
            :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
            <i class="fas fa-cog text-base"
               :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
            <span
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Pengaturan</span>
         </a>
      </div>

      <!-- Divider -->
      <div class="mt-6 pt-6 border-t border-gray-200">
         <div class="space-y-1">
            <!-- Device Heading -->
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider"
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 h-0 py-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">
               Master</div>

            <!-- Devices -->
            <a href="{{ route('admin.devices.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                                           {{ request()->routeIs('admin.devices.*')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-screwdriver-wrench text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Devices</span>
            </a>

            <!-- Sensors -->
            <a href="{{ route('admin.sensors.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                                           {{ request()->routeIs('admin.sensors.*')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fa-solid fa-microchip text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Sensors</span>
            </a>

            <!-- Data Heading -->
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider"
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 h-0 py-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">
               Data</div>

            <!-- Data: Data Actuals -->
            <a href="{{ route('admin.data-actuals.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                                           {{ request()->routeIs('admin.data-actuals.*')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-chart-line text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Data
                  Actuals</span>
            </a>

            <!-- Forecasting Heading -->
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider"
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 h-0 py-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">
               Forecasting</div>

            <!-- Models -->
            <a href="{{ route('admin.mas-models.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                                                            {{ request()->routeIs('admin.mas-models.*')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-brain text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Models</span>
            </a>

            <!-- Region Heading -->
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider"
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 h-0 py-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">
               Region</div>

            <!-- Region: Data Aliran Sungai -->
            <a href="{{ route('admin.region.river-basins.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                            {{ request()->routeIs('admin.region.river-basins.*')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-water text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Daerah
                  Aliran Sungai</span>
            </a>


            <!-- Region: Kabupaten -->
            <a href="{{ route('admin.region.kabupaten') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                          {{ request()->routeIs('admin.region.kabupaten')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-city text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Kabupaten</span>
            </a>

            <!-- Region: Kecamatan -->
            <a href="{{ route('admin.region.kecamatan') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                          {{ request()->routeIs('admin.region.kecamatan')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-layer-group text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Kecamatan</span>
            </a>

            <!-- Region: Desa -->
            <a href="{{ route('admin.region.desa') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative
                          {{ request()->routeIs('admin.region.desa')
   ? 'active'
   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-home text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Desa</span>
            </a>


         </div>
      </div>
   </nav>
</aside>