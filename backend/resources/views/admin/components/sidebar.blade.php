<!-- Sidebar -->
<aside
   class="sidebar-custom min-h-screen flex-shrink-0 transition-all duration-200 ease-out fixed top-0 left-0 lg:sticky lg:top-0 z-30 overflow-y-auto"
   x-cloak
   :class="{
       '-translate-x-full w-0': !$store.sidebar.open && window.innerWidth < 1024,
       'translate-x-0 w-full max-w-xs sidebar-mobile': $store.sidebar.open && window.innerWidth < 1024,
       'w-16': !$store.sidebar.open && window.innerWidth >= 1024,
       'w-64': $store.sidebar.open && window.innerWidth >= 1024
   }">

   <!-- Logo -->
   <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center group sidebar-logo">
         <div class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden bg-white dark:bg-gray-700">
            <img src="{{ asset('assets/images/PUSDAJATIM.png') }}" alt="Logo PUSDAJATIM"
               class="object-contain w-full h-full" />
         </div>
         <!-- Full logo text (hidden when sidebar is collapsed) -->
         <span class="ml-3 text-xl font-semibold text-gray-900 dark:text-white transition-colors duration-200"
            :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 w-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">FFWS</span>
      </a>


   </div>

   <!-- Navigation Menu -->
   <nav class="mt-6"
      :class="{ 'px-3': $store.sidebar.open || window.innerWidth < 1024, 'px-2': !$store.sidebar.open && window.innerWidth >= 1024 }">
      <div class="space-y-1">
         <!-- Dashboard -->
         <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                      {{ request()->routeIs('admin.dashboard')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
            :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
            <i class="fas fa-tachometer-alt text-base"
               :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
            <span
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Dashboard</span>
         </a>

         <!-- Users Management -->
         <a href="{{ route('admin.users.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                      {{ request()->routeIs('admin.users.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
            :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
            <i class="fas fa-users text-base"
               :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
            <span
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Manajemen
               User</span>
         </a>

         <!-- Settings -->
         <a href="{{ route('admin.settings.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                      {{ request()->routeIs('admin.settings.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
            :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
            <i class="fas fa-cog text-base"
               :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
            <span
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Pengaturan</span>
         </a>
      </div>

      <!-- Divider -->
      <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
         <div class="space-y-1">
            <!-- Device Heading -->
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider transition-colors duration-200"
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 h-0 py-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">
               Master</div>

            <!-- Provinces -->
            <a href="{{ route('admin.mas-provinces.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-provinces.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-map text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Provinces</span>
            </a>

            <!-- Cities -->
            <a href="{{ route('admin.mas-cities.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-cities.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-city text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Cities</span>
            </a>

            <!-- Regencies -->
            <a href="{{ route('admin.mas-regencies.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-regencies.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-building text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Regencies</span>
            </a>

            <!-- Villages -->
            <a href="{{ route('admin.mas-villages.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-villages.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-home text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Villages</span>
            </a>

            <!-- Watersheds -->
            <a href="{{ route('admin.mas-watersheds.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-watersheds.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-tint text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Watersheds</span>
            </a>

            <!-- UPTs -->
            <a href="{{ route('admin.mas-upts.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-upts.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-building text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">UPTs</span>
            </a>

            <!-- UPTDs -->
            <a href="{{ route('admin.mas-uptds.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-uptds.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-sitemap text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">UPTDs</span>
            </a>

            <!-- Device Parameters -->
            <a href="{{ route('admin.mas-device-parameters.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-device-parameters.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-cogs text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Device Parameters</span>
            </a>

            <!-- Sensor Parameters -->
            <a href="{{ route('admin.mas-sensor-parameters.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-sensor-parameters.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-sliders-h text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Sensor Parameters</span>
            </a>

            <!-- Sensor Thresholds -->
            <a href="{{ route('admin.mas-sensor-thresholds.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-sensor-thresholds.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-exclamation-triangle text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Sensor Thresholds</span>
            </a>

            <!-- Scalers -->
            <a href="{{ route('admin.mas-scalers.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-scalers.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-balance-scale text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Scalers</span>
            </a>

            <!-- WhatsApp Numbers -->
            <a href="{{ route('admin.mas-whatsapp-numbers.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.mas-whatsapp-numbers.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fab fa-whatsapp text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">WhatsApp Numbers</span>
            </a>

            <!-- Rating Curves -->
            <a href="{{ route('admin.rating-curves.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.rating-curves.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-chart-line text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Rating Curves</span>
            </a>

            <!-- Calculated Discharges -->
            <a href="{{ route('admin.calculated-discharges.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.calculated-discharges.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-tint text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Calculated Discharges</span>
            </a>

            <!-- Predicted Calculated Discharges -->
            <a href="{{ route('admin.predicted-calculated-discharges.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.predicted-calculated-discharges.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-wave-square text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Predicted Discharges</span>
            </a>

            <!-- Devices -->
            <a href="{{ route('admin.devices.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.devices.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-screwdriver-wrench text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Devices</span>
            </a>

            <!-- Sensors -->
            <a href="{{ route('admin.sensors.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.sensors.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fa-solid fa-microchip text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Sensors</span>
            </a>

            <!-- Data Heading -->
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider transition-colors duration-200"
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 h-0 py-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">
               Data</div>

            <!-- Data: Data Actuals -->
            <a href="{{ route('admin.data-actuals.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.data-actuals.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-chart-line text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Data
                  Actuals</span>
            </a>

            <!-- Forecasting Heading -->
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider transition-colors duration-200"
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 h-0 py-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">
               Forecasting</div>

            <!-- Models -->
            <a href="{{ route('admin.mas-models.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                                            {{ request()->routeIs('admin.mas-models.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-brain text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Models</span>
            </a>

            <!-- Data Predictions -->
            <a href="{{ route('admin.data_predictions.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                                                        {{ request()->routeIs('admin.data_predictions.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-chart-simple text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Data Predictions</span>
            </a>

            <!-- Region Heading -->
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider transition-colors duration-200"
               :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0 h-0 py-0 overflow-hidden': !$store.sidebar.open && window.innerWidth >= 1024 }">
               Region</div>

            <!-- Json Files -->
            <a href="{{ route('admin.geojson-files.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                                           {{ request()->routeIs('admin.geojson-files.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-file text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">GeoJSON Files</span>
            </a>

            <!-- Region: Data Aliran Sungai -->
            <a href="{{ route('admin.region.river-basins.index') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                            {{ request()->routeIs('admin.region.river-basins.*')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-water text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Daerah
                  Aliran Sungai</span>
            </a>


            <!-- Region: Kabupaten -->
            <a href="{{ route('admin.region.kabupaten') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                          {{ request()->routeIs('admin.region.kabupaten')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-city text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Kabupaten</span>
            </a>

            <!-- Region: Kecamatan -->
            <a href="{{ route('admin.region.kecamatan') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                          {{ request()->routeIs('admin.region.kecamatan')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
               :class="{ 'px-3 py-2': $store.sidebar.open || window.innerWidth < 1024, 'px-2 py-2 justify-center': !$store.sidebar.open && window.innerWidth >= 1024 }">
               <i class="fas fa-layer-group text-base"
                  :class="{ 'mr-3': $store.sidebar.open || window.innerWidth < 1024, 'mr-0': !$store.sidebar.open && window.innerWidth >= 1024 }"></i>
               <span
                  :class="{ 'opacity-100': $store.sidebar.open || window.innerWidth < 1024, 'opacity-0': !$store.sidebar.open && window.innerWidth >= 1024 }">Kecamatan</span>
            </a>

            <!-- Region: Desa -->
            <a href="{{ route('admin.region.desa') }}" class="sidebar-nav-item group flex items-center text-sm font-medium rounded-md relative transition-colors duration-200
                          {{ request()->routeIs('admin.region.desa')
   ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
   : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
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