# FilterControl - All-in-One Component

## Deskripsi
FilterControl adalah komponen tunggal yang menggabungkan FilterButton, FilterPanel, dan logic control menjadi satu komponen yang kompak dan mudah digunakan. Komponen ini independen dan tidak bergantung pada komponen lain.

## Komponen Utama

### FilterControl (`src/components/common/FilterControl.jsx`) - **SINGLE UNIFIED COMPONENT**
- **All-in-one solution** yang menggabungkan button, panel, dan state management
- Fully self-contained dengan internal state management
- Fixed positioning di pojok kanan atas (top-4 right-4)
- Mengelola semua aspek filter dalam satu komponen

**Features yang Disertakan:**
- ✅ **Filter Button**: Tombol floating dengan visual feedback
- ✅ **Filter Panel**: Side panel dengan 2 tab (Auto Switch & Layer Map)
- ✅ **State Management**: Internal state untuk panel visibility dan layer toggles
- ✅ **Keyboard Shortcuts**: ESC dan Ctrl+Tab support
- ✅ **Layer Management**: Built-in layer toggle functionality
- ✅ **Responsive Design**: Adaptive button size dan panel layout

**Props:**
- `tickerData`: Data stasiun monitoring
- `onStationChange`: Function callback ketika stasiun berubah
- `currentStationIndex`: Index stasiun yang sedang aktif
- `onAutoSwitchToggle`: Function callback untuk toggle auto switch
- `onLayerToggle`: Function callback untuk toggle layer peta

### 2. FilterPanel (`src/components/common/FilterPanel.jsx`)
- Side panel yang slide dari kanan dengan dua tab utama:
  - **Auto Switch**: Berisi kontrol AutoSwitchToggle dan informasi status
  - **Layer Map**: Berisi daftar layer peta yang bisa di-toggle

**Fitur:**
- **Keyboard Shortcuts:**
  - `ESC`: Menutup panel
  - `Ctrl + Tab`: Beralih antar tab
- **Layer Management:** Toggle on/off untuk berbagai lapisan peta
- **Status Information:** Menampilkan informasi real-time tentang stasiun monitoring
- **Responsive Design:** Menyesuaikan dengan ukuran layar yang berbeda

**Props:**
- `isOpen`: Boolean untuk kontrol visibility panel
- `onClose`: Function untuk menutup panel
- `tickerData`: Data stasiun monitoring
- `onStationChange`: Function callback ketika stasiun berubah
- `currentStationIndex`: Index stasiun yang sedang aktif
- `onAutoSwitchToggle`: Function callback untuk toggle auto switch
- `onLayerToggle`: Function callback untuk toggle layer peta

## Layer Peta yang Tersedia

1. **Stasiun Monitoring** (Default: Aktif)
2. **Sungai** (Default: Aktif)
3. **Area Risiko Banjir** (Default: Non-aktif)
4. **Data Curah Hujan** (Default: Non-aktif)
5. **Elevasi Terrain** (Default: Non-aktif)
6. **Batas Administrasi** (Default: Aktif)

## Arsitektur All-in-One

### Keuntungan Unified Component:
- **✅ Single File**: Semua filter logic hanya dalam 1 file
- **✅ Simplified Import**: Cukup import 1 komponen saja
- **✅ Self-Contained**: Tidak ada dependency antar file filter
- **✅ Easy Maintenance**: Logic tersentralisasi dan mudah di-debug
- **✅ Better Performance**: Mengurangi bundle splitting dan lazy loading overhead
- **✅ Consistent State**: Internal state management yang terpusat
- **✅ Clean Architecture**: Eliminasi prop drilling antar komponen

### Integrasi dengan Layout.jsx:
- Menambahkan FilterControl sebagai komponen independen
- FloodRunningBar dikembalikan ke kondisi semula
- AutoSwitchToggle di bottom-right tetap berfungsi normal
- Tidak ada konflik positioning atau z-index

### FloodRunningBar.jsx:
- **DIKEMBALIKAN KE KONDISI SEMULA dengan PENYESUAIAN RESPONSIVE**
- ✅ Positioning responsive dengan margin yang seragam
- ✅ Tidak ada dependency pada filter components
- ✅ Layout dan styling kembali ke original
- 📏 **Margin Enhancement**: Disesuaikan untuk margin yang sama dengan GoogleSearchbar dan FilterButton
  - Mobile: `right: calc(1rem + 48px + 1rem)` (total 80px dari kanan)
  - Desktop: `right: calc(1rem + 56px + 1rem)` (total 88px dari kanan)
  - Menggunakan CSS media queries untuk responsive behavior

## Penggunaan

### Implementasi Simple di Layout.jsx:
```jsx
// Di Layout.jsx - Sangat simple!
<FilterControl
    tickerData={tickerData}
    onStationChange={handleStationChange}
    currentStationIndex={currentStationIndex}
    onAutoSwitchToggle={handleAutoSwitchToggle}
    onLayerToggle={handleLayerToggle}
/>

// FloodRunningBar kembali ke bentuk original
<FloodRunningBar
    onDataUpdate={setTickerData}
    onStationSelect={handleStationSelect}
    isSidebarOpen={isSidebarOpen}
/>
```

### Struktur Komponen Tunggal:
```
FilterControl (All-in-one unified component)
├── Filter Button (Built-in, fixed top-right positioning)
│   ├── Dynamic Icon (Filter ↔ Sliders)
│   ├── Hover Effects & Animations  
│   └── Active State Indicator
└── Filter Panel (Built-in slide-in panel)
    ├── Auto Switch Tab
    │   ├── AutoSwitchToggle Integration
    │   └── Real-time Station Info
    └── Layer Map Tab
        ├── Interactive Layer Toggle List
        ├── Color-coded Layer Indicators
        └── Layer Statistics Dashboard
```

### Internal State Management:
- `isFilterPanelOpen`: Panel visibility state
- `activeTab`: Current active tab ("controls" | "layers")  
- `layers`: Built-in layer configuration with toggle states

## Dependencies
- `lucide-react`: Icons untuk UI (Filter, Sliders, Layers, ToggleLeft, ToggleRight, X, Keyboard)
- `AutoSwitchToggle`: Import dari common components untuk auto switch functionality

## File Structure (After Merge)
```
src/components/common/
├── FilterControl.jsx      ← All-in-one component (NEW)
├── AutoSwitchToggle.jsx   ← Used by FilterControl  
├── index.jsx              ← Updated exports
└── ... (other components)

❌ FilterButton.jsx        ← REMOVED (merged into FilterControl)
❌ FilterPanel.jsx         ← REMOVED (merged into FilterControl)
```

## Styling
- Menggunakan Tailwind CSS untuk semua styling
- Consistent dengan desain sistem yang sudah ada
- Menggunakan backdrop-blur dan gradient untuk modern glass effect
- Animasi smooth untuk transisi dan hover states

## Spacing & Layout Consistency

### Margin Seragam di Top Bar:
```
┌─────────────────────────────────────────────────────────────────┐
│ [1rem] GoogleSearchbar [1rem] | [1rem] FloodRunningBar [1rem] FilterButton [1rem] │
└─────────────────────────────────────────────────────────────────┘
```

**Komponen Positioning:**
- **GoogleSearchbar**: `left-4 right-4` (margin 1rem kiri-kanan)
- **FloodRunningBar**: `left-[calc(368px+2rem)]` dengan `right` responsive:
  - Mobile: `calc(1rem + 48px + 1rem)` = 80px dari kanan
  - Desktop: `calc(1rem + 56px + 1rem)` = 88px dari kanan
- **FilterButton**: `top-4 right-4` (margin 1rem dari kanan)

### Responsive Behavior:
- FilterButton: `w-12 h-12` (48px) pada mobile, `w-14 h-14` (56px) pada desktop
- FloodRunningBar otomatis menyesuaikan right spacing berdasarkan ukuran FilterButton
- CSS media queries memastikan spacing yang konsisten di semua breakpoint

## Future Enhancements
- Integrasi dengan backend untuk persistence layer settings
- Menambahkan preset filter configurations
- Export/import filter settings
- Integration dengan MapboxGL untuk kontrol layer yang lebih advanced