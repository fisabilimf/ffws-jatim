# 🚀 FFWS JATIM - Frontend Application

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
│   ├── common/          # Common components
│   ├── layout/          # Layout components
│   ├── sensors/         # Sensor-related components
│   └── MapboxMap.jsx    # Main map component
├── contexts/
│   ├── AppContext.jsx   # App context
│   └── DevicesContext.jsx # Devices context
├── hooks/
│   └── useAutoSwitch.js # Auto switch logic
├── services/
│   ├── api.js          # API service
│   ├── apiClient.js    # API client
│   └── devices.js      # Devices service
└── utils/
    └── statusUtils.js  # Status utilities
```

## 🔧 **CONFIGURATION**

### **API Configuration:**
```javascript
// src/services/apiClient.js
const API_BASE_URL = "https://ffws-backend.rachmanesa.com/api";
const AUTH_TOKEN = "your-token-here";
```

### **Auto Switch Configuration:**
```javascript
// Default settings
interval: 8000ms    // 8 seconds between switches
stopDelay: 5000ms   // 5 seconds delay before stop
zoom: 14            // Map zoom level
```

## 📚 **DOCUMENTATION**

- **[AUTOSWITCH_DOCUMENTATION.md](./AUTOSWITCH_DOCUMENTATION.md)** - Complete auto switch documentation
- **[STATION_DETAIL_TROUBLESHOOTING.md](./STATION_DETAIL_TROUBLESHOOTING.md)** - Troubleshooting guide
- **[API_INTEGRATION_GUIDE.md](./API_INTEGRATION_GUIDE.md)** - API integration guide

## 🎯 **USAGE**

### **Auto Switch:**
1. Open Filter Panel (click filter button)
2. Click Auto Switch toggle
3. Watch automatic station switching

### **Station Detail:**
1. Click marker on map
2. Click "Lihat Detail" in tooltip
3. View station information

## 🔍 **DEBUGGING**

### **Console Logs:**
- Open browser console (F12)
- Check debugging logs for troubleshooting
- Monitor API calls in Network tab

### **Common Issues:**
- **Station Detail not opening** - Check console logs
- **Auto Switch not working** - Verify API connection
- **Map not loading** - Check Mapbox token

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

- ✅ **Code Splitting** - Lazy loading components
- ✅ **Bundle Optimization** - Optimized build
- ✅ **Caching** - API data caching
- ✅ **Error Handling** - Robust error recovery

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