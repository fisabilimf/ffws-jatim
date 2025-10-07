# 🚀 AUTO SWITCH DOCUMENTATION - FFWS JATIM

## 📋 **OVERVIEW**

Auto Switch adalah fitur yang memungkinkan aplikasi untuk **otomatis beralih antar stasiun monitoring** dengan interval waktu tertentu, memberikan pengalaman monitoring yang berkelanjutan tanpa intervensi manual.

## 🏗️ **ARSITEKTUR**

### **Data Flow:**
```
Dashboard Devices API (/devices)
    ↓
fetchDevices() service (devices.js)
    ↓
DevicesContext (DevicesProvider)
    ↓
useAutoSwitch hook
    ↓
Auto Switch berjalan berdasarkan ID devices dari dashboard
```

### **Komponen Utama:**
- **`DevicesContext.jsx`** - Context untuk mengelola data devices
- **`useAutoSwitch.js`** - Custom hook yang mengatur logika auto switch
- **`AutoSwitchToggle.jsx`** - UI component untuk toggle button
- **`Layout.jsx`** - Integrasi dengan layout utama
- **`FilterPanel.jsx`** - Tempat AutoSwitchToggle ditempatkan

## ⚙️ **CARA KERJA**

### **Alur Autoswitch:**
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

### **Visual Flow:**
1. **🎯 FLY TO MARKER** - Map flies to device coordinates
2. **🔍 ZOOM IN** - Zoom level 14 untuk view optimal
3. **📋 STATION DETAIL** - Sidebar opens dengan informasi station
4. **⏱️ TIMING** - 8 detik cycle time untuk membaca informasi

## 🔧 **KONFIGURASI**

### **Parameter:**
- **`interval`** - Interval antar switch (default: 8000ms = 8 detik)
- **`stopDelay`** - Delay sebelum stop (default: 5000ms = 5 detik)
- **`zoom`** - Zoom level saat fly to marker (default: 14)
- **`speed`** - Kecepatan animasi map (default: 1.5)
- **`pitch`** - Sudut pitch untuk 3D effect (default: 45°)

### **Data Source:**
- **Primary**: Devices dari DevicesContext (API `/devices`)
- **Auto Refresh**: Setiap 30 detik
- **Error Handling**: 3x retry mechanism

## 🎨 **UI/UX**

### **Visual Indicators:**
- **🟢 Moving...** - Ketika map sedang fly to marker (yellow dot dengan animate-ping)
- **🟢 At Marker** - Ketika sudah sampai dan station detail terbuka (green dot dengan animate-pulse)
- **🟡 Paused** - Ketika user berinteraksi (yellow dot dengan animate-ping)
- **⚫ Inactive** - Ketika auto switch dihentikan (gray dot)

### **Toggle Button States:**
- **Gray** - Inactive state
- **Green** - Active/playing state
- **Yellow** - Pending stop state

## 🎯 **CARA MENGGUNAKAN**

### **1. Start Autoswitch:**
1. Buka FilterPanel (klik filter button)
2. Klik toggle button di AutoSwitchToggle
3. Autoswitch akan mulai berjalan

### **2. Autoswitch Flow:**
1. **Map flies** ke marker pertama
2. **Zoom in** ke level 14
3. **Tooltip appears** setelah 600ms
4. **Station detail opens** setelah 500ms
5. **Wait 8 seconds** untuk membaca informasi
6. **Repeat** untuk marker berikutnya

### **3. Stop Autoswitch:**
1. Klik toggle button lagi
2. Autoswitch akan berhenti dengan delay 5 detik
3. Sidebar akan tertutup otomatis

### **4. User Interaction:**
1. Ketika user berinteraksi dengan map
2. Autoswitch akan pause otomatis
3. Auto resume setelah 5 detik

## 🔍 **DEBUGGING**

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

// Event flow
AutoSwitchToggle: Auto switch activated event received
Layout: Auto switch toggle requested
useAutoSwitch: Starting auto switch...
```

### **Event Tracking:**
- `autoSwitchActivated` - Ketika autoswitch dimulai
- `autoSwitchDeactivated` - Ketika autoswitch dihentikan
- `autoSwitchSuccess` - Ketika switch berhasil
- `autoSwitchError` - Ketika terjadi error
- `pauseAutoSwitch` - Ketika di-pause
- `resumeAutoSwitch` - Ketika di-resume

## 🚨 **TROUBLESHOOTING**

### **Station Detail Tidak Bisa Dibuka:**

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

## 🎉 **STATUS IMPLEMENTASI**

### **✅ Completed:**
- ✅ **DevicesContext Integration** - Fully integrated dengan API
- ✅ **Auto Switch Logic** - Complete dengan event system
- ✅ **UI Components** - AutoSwitchToggle dengan visual indicators
- ✅ **Map Integration** - Smooth animation dan zoom
- ✅ **Error Handling** - Comprehensive error handling
- ✅ **Debugging** - Complete debugging system

### **✅ Features:**
- ✅ **Real-time Data** - Auto refresh setiap 30 detik
- ✅ **User Interaction** - Auto pause/resume
- ✅ **Visual Feedback** - Clear status indicators
- ✅ **Smooth Animation** - Map flyTo dengan timing yang optimal
- ✅ **Station Detail** - Sidebar dengan informasi lengkap

## 🚀 **PERFORMANCE**

### **Optimizations:**
- ✅ **Single Source of Truth** - DevicesContext sebagai satu-satunya sumber data
- ✅ **Event-Driven Architecture** - Communication melalui custom events
- ✅ **Lazy Loading** - Komponen di-lazy load untuk performance
- ✅ **Memoization** - Event handlers di-memoize untuk mencegah re-render
- ✅ **Error Recovery** - Retry mechanism untuk API calls

### **Bundle Size:**
- ✅ **Code Splitting** - Komponen di-split untuk optimal loading
- ✅ **Tree Shaking** - Unused code di-remove
- ✅ **Minification** - Code di-minify untuk production

## 📊 **MONITORING**

### **Metrics to Track:**
- **Auto Switch Usage** - Berapa lama user menggunakan fitur
- **Error Rate** - Frequency error dalam auto switch
- **Performance** - Response time dan animation smoothness
- **User Behavior** - Pola penggunaan auto switch

### **Logs to Monitor:**
- API response times
- Auto switch cycle times
- Error frequencies
- User interaction patterns

---

**Auto Switch siap untuk production dan memberikan user experience yang excellent untuk monitoring stasiun! 🚀**
