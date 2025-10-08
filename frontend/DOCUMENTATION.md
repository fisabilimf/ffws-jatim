# 📚 FFWS JATIM - DOCUMENTATION

## 📋 **OVERVIEW**

Dokumentasi lengkap untuk aplikasi FFWS (Flood Warning System) Jawa Timur. Aplikasi ini adalah sistem monitoring banjir real-time yang menggunakan React, Mapbox, dan API backend untuk menampilkan data stasiun monitoring.

## 🏗️ **ARSITEKTUR APLIKASI**

### **Tech Stack:**
- **Frontend**: React 18 + Vite
- **Maps**: Mapbox GL JS
- **Styling**: Tailwind CSS
- **Charts**: Recharts
- **State Management**: React Context
- **API**: REST API dengan Bearer Token

### **Struktur Komponen:**
```
src/components/
├── common/                    # Reusable UI components
│   ├── AutoSwitchToggle.jsx   # UI controls & toggles
│   ├── FilterPanel.jsx        # UI panels & filters
│   ├── FloodRunningBar.jsx    # UI bars & displays
│   ├── FloatingLegend.jsx     # UI legends & info
│   ├── GoogleMapsSearchbar.jsx # UI search components
│   └── Chart.jsx              # Generic chart components
├── sensors/                   # Sensor-specific components
│   ├── TanggulAktual.jsx      # River level actual data
│   ├── TanggulPrediksi.jsx    # River level prediction
│   └── RechartsDualLineChart.jsx # Dual line monitoring
├── layout/                    # Layout and panel components
│   ├── Layout.jsx             # Main application layout
│   ├── SidebarTemplate.jsx    # Reusable sidebar template
│   ├── DetailPanel.jsx        # Detailed information panel
│   └── StationDetail.jsx      # Station detail sidebar
├── devices/                   # Device-related components
│   └── maptooltip.jsx         # Map tooltip components
└── MapboxMap.jsx             # Main map component
```

## 🔌 **API INTEGRATION**

### **Primary Endpoint:**
```
GET /devices
Base URL: https://ffws-backend.rachmanesa.com/api
Authentication: Bearer Token
```

### **API Client Configuration:**
```javascript
// src/services/apiClient.js
const API_BASE_URL = "https://ffws-backend.rachmanesa.com/api";
const AUTH_TOKEN = "6|aESjE6p715Q32zPDSbAUE0Pa42tEYzYgmQtmh2MA223ef02f";
```

### **Expected API Response:**
```javascript
{
  "data": [
    {
      "id": 1,
      "device_code": "DEV001",
      "name": "Stasiun Bojonegoro",
      "latitude": -7.1502,
      "longitude": 111.8817,
      "status": "active",
      "value": 2.5,
      "unit": "m",
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  ]
}
```

### **Integration Flow:**
1. **Service Layer** (`src/services/devices.js`) - Fetch data dari API
2. **Context Layer** (`src/contexts/AppContext.jsx`) - State management
3. **Component Layer** - Menggunakan data dari context

### **Auto Refresh:**
- **Interval**: 30 detik
- **Retry**: 3x attempts dengan delay 2s, 4s, 6s
- **Error Handling**: Comprehensive error states

## 🚀 **AUTO SWITCH FEATURE**

### **Overview:**
Auto Switch adalah fitur yang memungkinkan aplikasi untuk **otomatis beralih antar stasiun monitoring** dengan interval waktu tertentu, memberikan pengalaman monitoring yang berkelanjutan.

### **Cara Kerja:**
```
0ms     - User clicks toggle button
100ms   - startAutoSwitch() called
200ms   - mapboxAutoSwitch() called
200ms   - Map starts flying to coordinates
800ms   - Map reaches coordinates (zoom: 14)
600ms   - Tooltip appears
1000ms  - Station detail sidebar opens
1500ms  - isAtMarker = true (ready for next switch)
8000ms  - Next autoswitch cycle begins
```

### **Konfigurasi:**
- **Interval**: 8000ms (8 detik)
- **Stop Delay**: 5000ms (5 detik)
- **Zoom Level**: 14
- **Animation Speed**: 1.5
- **Pitch**: 45° untuk 3D effect

### **Visual Indicators:**
- **🟢 Moving...** - Map sedang fly to marker (yellow dot dengan animate-ping)
- **🟢 At Marker** - Sudah sampai dan station detail terbuka (green dot dengan animate-pulse)
- **🟡 Paused** - User berinteraksi (yellow dot dengan animate-ping)
- **⚫ Inactive** - Auto switch dihentikan (gray dot)

### **Cara Menggunakan:**
1. Buka FilterPanel (klik filter button)
2. Klik toggle button di AutoSwitchToggle
3. Autoswitch akan mulai berjalan
4. Klik toggle button lagi untuk menghentikan

## 🏗️ **COMPONENT ARCHITECTURE**

### **SidebarTemplate vs StationDetail:**

#### **SidebarTemplate - Layout Component:**
- **Purpose**: Template/Blueprint untuk membuat sidebar
- **Type**: Reusable Layout Component
- **Role**: Container/Wrapper
- **Props**: `isOpen`, `onClose`, `title`, `children`, dll

#### **StationDetail - Content Component:**
- **Purpose**: Specific Content untuk menampilkan detail stasiun
- **Type**: Business Logic Component
- **Role**: Content Provider
- **Props**: `selectedStation`, `devicesData`, `onClose`, dll

### **Composition Pattern:**
```javascript
<StationDetail>
  <SidebarTemplate>
    {/* SidebarTemplate menyediakan layout */}
    <div className="sidebar-content">
      {/* StationDetail menyediakan konten */}
      <StatusCard />
      <StationInfo />
    </div>
  </SidebarTemplate>
</StationDetail>
```

## 🔧 **TROUBLESHOOTING**

### **Station Detail Tidak Bisa Dibuka:**

#### **Kemungkinan Penyebab:**
1. **Data Structure Mismatch** - Data dari API tidak memiliki field yang diharapkan
2. **ID Mismatch** - ID dari selectedStation tidak match dengan devicesData
3. **Missing Data Fields** - StationDetail mencoba mengakses field yang tidak ada
4. **Event Chain Broken** - Marker click → tooltip → "Lihat Detail" button

#### **Debugging Steps:**
1. **Buka Browser Console** (F12)
2. **Test Flow:**
   - Klik marker di map → Lihat log "MARKER CLICK DEBUG"
   - Klik "Lihat Detail" di tooltip → Lihat log "HANDLE SHOW DETAIL DEBUG"
   - Lihat station detail → Lihat log "HANDLE STATION SELECT DEBUG"
   - Lihat data loading → Lihat log "STATION DETAIL DEBUG"

#### **Expected Logs:**
```javascript
// 1. Marker Click
=== MARKER CLICK DEBUG ===
Clicked station: {id: 1, name: "Station A", ...}

// 2. Handle Show Detail
=== HANDLE SHOW DETAIL DEBUG ===
Station to show detail: {id: 1, name: "Station A", ...}

// 3. Handle Station Select
=== HANDLE STATION SELECT DEBUG ===
Selected station: {id: 1, name: "Station A", ...}

// 4. Station Detail
=== STATION DETAIL DEBUG ===
selectedStation: {id: 1, name: "Station A", ...}
devicesData length: 5
Found station: {id: 1, name: "Station A", ...}
```

### **Common Errors:**
- **"Station not found in devicesData"** - ID tidak match, check data structure
- **"onStationSelect function not available"** - Check props passing di MapboxMap
- **"No devices available from context"** - Check DevicesContext dan API connection

## 🚀 **PERFORMANCE OPTIMIZATIONS**

### **Code Splitting:**
- **Lazy Loading** - Komponen di-lazy load untuk performance
- **Bundle Optimization** - Proper chunking berdasarkan kategori komponen
- **Tree Shaking** - Unused code di-remove

### **State Management:**
- **Single Source of Truth** - DevicesContext sebagai satu-satunya sumber data
- **Event-Driven Architecture** - Communication melalui custom events
- **Memoization** - Event handlers di-memoize untuk mencegah re-render

### **Bundle Analysis:**
```
dist/js/sensor-components-C9KTntsr.js     11.40 kB │ gzip:   2.53 kB
dist/js/common-components-BOYOfueT.js     21.86 kB │ gzip:  6.69 kB
dist/js/layout-components-B7VLFkq4.js     25.03 kB │ gzip:  6.50 kB
```

## 📊 **MONITORING & DEBUGGING**

### **Console Logging:**
```javascript
// Data source selection
=== GET CURRENT DATA SOURCE ===
Devices from context: 5
Using devices from context

// Autoswitch tick
=== AUTO SWITCH TICK ===
Current index: 0
Next index: 1
Data source length: 5
Next device: Station A
```

### **Event Tracking:**
- `autoSwitchActivated` - Ketika autoswitch dimulai
- `autoSwitchDeactivated` - Ketika autoswitch dihentikan
- `autoSwitchSuccess` - Ketika switch berhasil
- `autoSwitchError` - Ketika terjadi error

### **Metrics to Track:**
- **Auto Switch Usage** - Berapa lama user menggunakan fitur
- **Error Rate** - Frequency error dalam auto switch
- **Performance** - Response time dan animation smoothness
- **User Behavior** - Pola penggunaan auto switch

## 🎯 **DEVELOPMENT GUIDELINES**

### **Component Placement:**
- **Common**: Reusable UI components
- **Sensors**: Sensor-specific visualizations
- **Layout**: Layout and panel components
- **Devices**: Device and map components

### **Import Patterns:**
```javascript
// Individual imports
import AutoSwitchToggle from '@/components/common/AutoSwitchToggle';
import TanggulAktual from '@/components/sensors/TanggulAktual';

// Index imports
import { AutoSwitchToggle, FilterPanel } from '@/components/common';
import { TanggulAktual, TanggulPrediksi } from '@/components/sensors';

// Lazy loading
const TanggulAktual = lazy(() => import('@/components/sensors/TanggulAktual'));
```

### **Naming Conventions:**
- **PascalCase** untuk component files
- **Descriptive names** yang menunjukkan purpose
- **Consistent suffixes** (Panel, Chart, Toggle)

## 🚀 **PRODUCTION CONSIDERATIONS**

### **Security:**
- ✅ **Authentication** - Bearer token
- ✅ **HTTPS** - Secure connection
- ✅ **Error Handling** - No sensitive data exposed

### **Performance:**
- ✅ **Caching** - Data di-cache di context
- ✅ **Error Recovery** - Retry mechanism
- ✅ **Loading States** - Proper loading indicators

### **Monitoring:**
- ✅ **Console Logs** - Debug information
- ✅ **Error Tracking** - Error states
- ✅ **Performance** - Response time monitoring

## 📚 **QUICK REFERENCE**

### **For New Developers:**
1. Start dengan `README.md`
2. Baca bagian API Integration
3. Pahami Auto Switch feature
4. Check Component Architecture

### **For Troubleshooting:**
1. Check Console Logs
2. Verify API connection
3. Test Event Flow
4. Check Data Structure

### **For Auto Switch Development:**
1. Pahami useAutoSwitch hook
2. Check debugging section
3. Test dengan console logs
4. Verify event system

---

**Aplikasi FFWS Jatim siap untuk production dengan fitur monitoring real-time yang comprehensive! 🚀**

