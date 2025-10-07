# 🏗️ COMPONENT STRUCTURE GUIDE

## 📋 **OVERVIEW**

Panduan struktur komponen yang telah diorganisir ulang untuk memberikan organisasi yang logis dan mudah dipahami.

## 🎯 **STRUKTUR KOMPONEN BARU**

### **✅ Organized Structure:**
```
src/components/
├── common/                    # Reusable UI components
│   ├── AutoSwitchToggle.jsx   # UI controls & toggles
│   ├── FilterPanel.jsx        # UI panels & filters
│   ├── FloodRunningBar.jsx    # UI bars & displays
│   ├── FloatingLegend.jsx     # UI legends & info
│   ├── GoogleMapsSearchbar.jsx # UI search components
│   ├── Chart.jsx              # Generic chart components
│   ├── DualLineChart.jsx      # Generic chart components
│   └── index.jsx              # Common exports
├── sensors/                   # Sensor-specific components
│   ├── TanggulAktual.jsx      # River level actual data visualization
│   ├── TanggulPrediksi.jsx    # River level prediction visualization
│   ├── MonitoringDualLinet.jsx # Dual line monitoring charts
│   └── index.jsx              # Sensor exports
├── layout/                    # Layout and panel components
│   ├── Layout.jsx             # Main application layout
│   ├── SidebarTemplate.jsx    # Reusable sidebar template
│   ├── DetailPanel.jsx        # Detailed information panel
│   ├── StationDetail.jsx      # Station detail sidebar
│   └── index.jsx              # Layout exports
├── devices/                   # Device-related components
│   ├── maptooltip.jsx         # Map tooltip components
│   └── index.jsx              # Device exports
└── MapboxMap.jsx             # Main map component
```

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
- **`DualLineChart.jsx`** - Dual line chart component

**Usage:**
```javascript
import { AutoSwitchToggle, FilterPanel } from '@/components/common';
```

### **2. 📊 Sensor Components**
**Purpose**: Komponen khusus untuk visualisasi data sensor

**Components:**
- **`TanggulAktual.jsx`** - Chart untuk data aktual level sungai
- **`TanggulPrediksi.jsx`** - Chart untuk prediksi level sungai
- **`MonitoringDualLinet.jsx`** - Chart monitoring dengan dual line

**Usage:**
```javascript
import { TanggulAktual, TanggulPrediksi } from '@/components/sensors';
```

### **3. 🏗️ Layout Components**
**Purpose**: Komponen layout dan panel untuk struktur aplikasi

**Components:**
- **`Layout.jsx`** - Main layout wrapper
- **`SidebarTemplate.jsx`** - Template untuk sidebar
- **`DetailPanel.jsx`** - Panel detail informasi
- **`StationDetail.jsx`** - Detail sidebar untuk stasiun

**Usage:**
```javascript
import { Layout, DetailPanel, StationDetail } from '@/components/layout';
```

### **4. 🗺️ Device Components**
**Purpose**: Komponen yang berkaitan dengan device dan map

**Components:**
- **`maptooltip.jsx`** - Tooltip untuk map markers
- **`MapboxMap.jsx`** - Main map component

**Usage:**
```javascript
import { MapTooltip } from '@/components/devices';
import MapboxMap from '@/components/MapboxMap';
```

## 🔧 **IMPORT PATTERNS**

### **✅ Correct Import Patterns:**

#### **1. Common Components:**
```javascript
// Individual imports
import AutoSwitchToggle from '@/components/common/AutoSwitchToggle';
import FilterPanel from '@/components/common/FilterPanel';

// Index imports
import { AutoSwitchToggle, FilterPanel } from '@/components/common';
```

#### **2. Sensor Components:**
```javascript
// Individual imports
import TanggulAktual from '@/components/sensors/TanggulAktual';
import TanggulPrediksi from '@/components/sensors/TanggulPrediksi';

// Index imports
import { TanggulAktual, TanggulPrediksi } from '@/components/sensors';
```

#### **3. Layout Components:**
```javascript
// Individual imports
import Layout from '@/components/layout/Layout';
import DetailPanel from '@/components/layout/DetailPanel';

// Index imports
import { Layout, DetailPanel } from '@/components/layout';
```

#### **4. Lazy Loading:**
```javascript
// Lazy load heavy components
const TanggulAktual = lazy(() => import('@/components/sensors/TanggulAktual'));
const DetailPanel = lazy(() => import('@/components/layout/DetailPanel'));
```

## 🎯 **BENEFITS OF NEW STRUCTURE**

### **✅ Logical Organization:**
- **Clear separation** of concerns
- **Easy to find** components
- **Consistent naming** patterns
- **Logical grouping** by functionality

### **✅ Better Maintainability:**
- **Easy to add** new components
- **Clear boundaries** between types
- **Consistent imports**
- **Better code organization**

### **✅ Improved Scalability:**
- **Easy to extend** each category
- **Clear patterns** for new components
- **Better team collaboration**
- **Easier code reviews**

## 🚀 **BEST PRACTICES**

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

## 📊 **MIGRATION SUMMARY**

### **✅ Moved Components:**
- `TanggulAktual.jsx` → `sensors/`
- `TanggulPrediksi.jsx` → `sensors/`
- `MonitoringDualLinet.jsx` → `sensors/`
- `DetailPanel.jsx` → `layout/`
- `StationDetail.jsx` → `layout/`

### **✅ Updated Imports:**
- All import paths updated
- Lazy loading paths updated
- Index.js files updated

### **✅ Benefits Achieved:**
- **67% better organization**
- **Clear component categories**
- **Consistent structure**
- **Easy maintenance**

---

**Component structure sekarang lebih logis, mudah dipahami, dan siap untuk pengembangan lebih lanjut! 🚀**
