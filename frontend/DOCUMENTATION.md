# 📚 FFWS JATIM - DOCUMENTATION

## 📋 **OVERVIEW**

Aplikasi frontend untuk sistem monitoring banjir Jawa Timur dengan fitur auto switch untuk monitoring stasiun secara otomatis.

## 🎯 **FEATURES**

- ✅ **Real-time Monitoring** - Data devices real-time dari API
- ✅ **Auto Switch** - Otomatis beralih antar stasiun monitoring
- ✅ **Interactive Map** - Mapbox integration dengan markers
- ✅ **Station Detail** - Detail informasi stasiun monitoring
- ✅ **Responsive Design** - Mobile-friendly interface

## 🏗️ **TECHNOLOGY STACK**

- **React 18** - Frontend framework
- **Vite** - Build tool
- **Tailwind CSS** - Styling
- **Mapbox GL JS** - Map integration
- **Context API** - State management

## 🚀 **QUICK START**

### **Installation:**
```bash
npm install
```

### **Development:**
```bash
npm run dev
```

### **Build:**
```bash
npm run build
```

## 📁 **PROJECT STRUCTURE**

```
src/
├── components/
│   ├── common/          # Reusable UI components
│   │   ├── AutoSwitchToggle.jsx   # UI controls & toggles
│   │   ├── FilterPanel.jsx        # UI panels & filters
│   │   ├── FloodRunningBar.jsx    # UI bars & displays
│   │   ├── FloatingLegend.jsx     # UI legends & info
│   │   ├── GoogleMapsSearchbar.jsx # UI search components
│   │   ├── Chart.jsx              # Generic chart components
│   │   └── index.jsx              # Common exports
│   ├── sensors/         # Sensor-specific components
│   │   ├── TanggulAktual.jsx      # River level actual data
│   │   ├── TanggulPrediksi.jsx    # River level prediction
│   │   ├── RechartsDualLineChart.jsx # Dual line monitoring
│   │   └── index.jsx              # Sensor exports
│   ├── layout/          # Layout and panel components
│   │   ├── Layout.jsx             # Main application layout
│   │   ├── SidebarTemplate.jsx    # Reusable sidebar template
│   │   ├── DetailPanel.jsx        # Detailed information panel
│   │   ├── StationDetail.jsx      # Station detail sidebar
│   │   └── index.jsx              # Layout exports
│   ├── devices/         # Device-related components
│   │   ├── maptooltip.jsx         # Map tooltip components
│   │   └── index.jsx              # Device exports
│   └── MapboxMap.jsx    # Main map component
├── contexts/
│   └── AppContext.jsx   # App context
├── hooks/
│   ├── useAppContext.js # App context hook
│   └── useAutoSwitch.js # Auto switch logic
├── services/
│   ├── api.js          # API service
│   ├── apiClient.js    # API client
│   └── devices.js      # Devices service
├── utils/
│   └── statusUtils.js  # Status utilities
└── pages/
    └── Dashboard.jsx   # Main dashboard page
```

## 🔧 **CONFIGURATION**

### **API Configuration:**
```javascript
// src/services/apiClient.js
const API_BASE_URL = "https://ffws-backend.rachmanesa.com/api";
const AUTH_TOKEN = "6|aESjE6p715Q32zPDSbAUE0Pa42tEYzYgmQtmh2MA223ef02f";
```

### **Auto Switch Configuration:**
```javascript
// Default settings
interval: 8000ms    // 8 seconds between switches
stopDelay: 5000ms   // 5 seconds delay before stop
zoom: 14            // Map zoom level
speed: 1.5          // Animation speed
pitch: 45           // 3D effect angle
```

## 🚀 **AUTO SWITCH FEATURE**

### **Overview:**
Auto Switch adalah fitur yang memungkinkan aplikasi untuk **otomatis beralih antar stasiun monitoring** dengan interval waktu tertentu.

### **Data Flow:**
```
Dashboard Devices API (/devices)
    ↓
fetchDevices() service (devices.js)
    ↓
AppContext (DevicesProvider)
    ↓
useAutoSwitch hook
    ↓
Auto Switch berjalan berdasarkan ID devices dari dashboard
```

### **Cara Menggunakan:**
1. **Start Autoswitch:**
   - Buka FilterPanel (klik filter button)
   - Klik toggle button di AutoSwitchToggle
   - Autoswitch akan mulai berjalan

2. **Autoswitch Flow:**
   - Map flies ke marker pertama
   - Zoom in ke level 14
   - Tooltip appears setelah 600ms
   - Station detail opens setelah 500ms
   - Wait 8 seconds untuk membaca informasi
   - Repeat untuk marker berikutnya

3. **Stop Autoswitch:**
   - Klik toggle button lagi
   - Autoswitch akan berhenti dengan delay 5 detik
   - Sidebar akan tertutup otomatis

### **Visual Indicators:**
- **🟢 Moving...** - Ketika map sedang fly to marker (yellow dot dengan animate-ping)
- **🟢 At Marker** - Ketika sudah sampai dan station detail terbuka (green dot dengan animate-pulse)
- **🟡 Paused** - Ketika user berinteraksi (yellow dot dengan animate-ping)
- **⚫ Inactive** - Ketika auto switch dihentikan (gray dot)

## 🔌 **API INTEGRATION**

### **Primary Endpoint:**
```
GET /devices
Base URL: https://ffws-backend.rachmanesa.com/api
Authentication: Bearer Token
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

### **Auto Refresh:**
- **Interval**: 30 detik
- **Retry**: 3x attempts
- **Retry Delay**: 2s, 4s, 6s

## 🎯 **COMPONENT CATEGORIES**

### **1. 📱 Common Components**
**Purpose**: Reusable UI components yang bisa digunakan di mana saja

**Components:**
- **`AutoSwitchToggle.jsx`** - Toggle button untuk auto switch
- **`FilterPanel.jsx`** - Panel filter dan controls
- **`FloodRunningBar.jsx`** - Running bar untuk data real-time
- **`FloatingLegend.jsx`** - Legend yang floating di map
- **`GoogleMapsSearchbar.jsx`** - Search bar untuk pencarian
- **`Chart.jsx`** - Generic chart component

### **2. 📊 Sensor Components**
**Purpose**: Komponen khusus untuk visualisasi data sensor

**Components:**
- **`TanggulAktual.jsx`** - Chart untuk data aktual level sungai
- **`TanggulPrediksi.jsx`** - Chart untuk prediksi level sungai
- **`RechartsDualLineChart.jsx`** - Chart monitoring dengan dual line

### **3. 🏗️ Layout Components**
**Purpose**: Komponen layout dan panel untuk struktur aplikasi

**Components:**
- **`Layout.jsx`** - Main layout wrapper
- **`SidebarTemplate.jsx`** - Template untuk sidebar
- **`DetailPanel.jsx`** - Panel detail informasi
- **`StationDetail.jsx`** - Detail sidebar untuk stasiun

### **4. 🗺️ Device Components**
**Purpose**: Komponen yang berkaitan dengan device dan map

**Components:**
- **`maptooltip.jsx`** - Tooltip untuk map markers
- **`MapboxMap.jsx`** - Main map component

## 🔍 **DEBUGGING**

### **Console Logs:**
```javascript
// Auto Switch Debug
=== AUTO SWITCH TICK ===
Current index: 0
Next index: 1
Data source length: 5
Next device: Station A

// Station Detail Debug
=== STATION DETAIL DEBUG ===
selectedStation: {id: 1, name: "Station A", ...}
devicesData length: 5
Found station: {id: 1, name: "Station A", ...}
```

### **Common Issues:**

#### **Station Detail Tidak Bisa Dibuka:**
**Kemungkinan Penyebab:**
1. **Data Mismatch** - Data dari API tidak memiliki field yang diharapkan
2. **ID Mismatch** - ID dari selectedStation tidak match dengan devicesData
3. **Missing Data Fields** - StationDetail mencoba mengakses field yang tidak ada
4. **Event Chain Broken** - Marker click → tooltip → "Lihat Detail" button

**Solusi:**
1. **Check Console Logs** - Lihat debugging logs untuk identifikasi masalah
2. **Verify Data Structure** - Pastikan API response memiliki field yang diperlukan
3. **Check Event Flow** - Pastikan event chain berjalan dengan benar
4. **Validate IDs** - Pastikan ID match antara selectedStation dan devicesData

### **Debug Commands:**
```javascript
// Check devices data
console.log('Devices:', devices);
console.log('Selected station:', selectedStation);

// Check event flow
// 1. Click marker → Check "MARKER CLICK DEBUG"
// 2. Click "Lihat Detail" → Check "HANDLE SHOW DETAIL DEBUG"
// 3. Check station detail → Check "STATION DETAIL DEBUG"
```

## 🚀 **DEPLOYMENT**

### **Build for Production:**
```bash
npm run build
```

### **Deploy:**
- Copy `dist/` folder to web server
- Configure web server for SPA routing
- Set up environment variables

## 📊 **PERFORMANCE**

### **Optimizations:**
- ✅ **Code Splitting** - Lazy loading components
- ✅ **Bundle Optimization** - Optimized build
- ✅ **Caching** - API data caching
- ✅ **Error Handling** - Robust error recovery
- ✅ **Single Source of Truth** - AppContext sebagai satu-satunya sumber data
- ✅ **Event-Driven Architecture** - Communication melalui custom events
- ✅ **Memoization** - Event handlers di-memoize untuk mencegah re-render

### **Bundle Analysis:**
```
dist/js/sensor-components-C9KTntsr.js     11.40 kB │ gzip:   2.53 kB
dist/js/common-components-BOYOfueT.js     21.86 kB │ gzip:  6.69 kB
dist/js/layout-components-B7VLFkq4.js     25.03 kB │ gzip:  6.50 kB
```

## 🎯 **BEST PRACTICES**

### **1. Component Placement:**
- **Common**: Reusable UI components
- **Sensors**: Sensor-specific visualizations
- **Layout**: Layout and panel components
- **Devices**: Device and map components

### **2. Naming Conventions:**
- **PascalCase** for component files
- **Descriptive names** that indicate purpose
- **Consistent suffixes** (Panel, Chart, Toggle)

### **3. Import Organization:**
- **Use index.js** for clean imports
- **Lazy load** heavy components
- **Group imports** by category

### **4. File Structure:**
- **One component** per file
- **Export default** for main component
- **Named exports** for utilities

## 🤝 **CONTRIBUTING**

1. Fork the repository
2. Create feature branch
3. Make changes
4. Test thoroughly
5. Submit pull request

## 📄 **LICENSE**

This project is licensed under the MIT License.

---

**FFWS JATIM Frontend - Ready for Production! 🚀**
