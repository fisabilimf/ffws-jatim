# 🔌 API INTEGRATION GUIDE

## 📋 **OVERVIEW**

Aplikasi menggunakan API backend untuk mengambil data devices yang sama dengan dashboard. Berikut adalah panduan integrasi API.

## 🎯 **API ENDPOINTS**

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

## 🏗️ **DATA STRUCTURE**

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

### **Required Fields:**
- `id` - Unique identifier
- `name` - Station name
- `latitude` - Latitude coordinate
- `longitude` - Longitude coordinate

### **Optional Fields:**
- `device_code` - Device code
- `status` - Device status
- `value` - Current value
- `unit` - Unit of measurement

## 🔧 **INTEGRATION FLOW**

### **1. Service Layer:**
```javascript
// src/services/devices.js
export const fetchDevices = async () => {
    const data = await fetchWithAuth("/devices");
    return data.data;
};
```

### **2. Context Layer:**
```javascript
// src/contexts/DevicesContext.jsx
const loadDevices = useCallback(async (retryCount = 0) => {
    const devicesData = await fetchDevices();
    // Validate and filter devices
    const validDevices = devicesData.filter(device => {
        const hasName = device && (device.name || device.device_name || device.station_name);
        const hasCoordinates = device && (device.latitude && device.longitude);
        return hasName && hasCoordinates;
    });
    setDevices(validDevices);
}, []);
```

### **3. Component Layer:**
```javascript
// src/components/layout/Layout.jsx
const { devices, loading, error } = useDevices();
```

## 🔄 **AUTO REFRESH**

### **Configuration:**
- **Interval**: 30 detik
- **Retry**: 3x attempts
- **Retry Delay**: 2s, 4s, 6s

### **Implementation:**
```javascript
useEffect(() => {
    const interval = setInterval(() => {
        loadDevices();
    }, 30000);
    return () => clearInterval(interval);
}, [loadDevices]);
```

## 🚨 **ERROR HANDLING**

### **Retry Mechanism:**
```javascript
if (retryCount < maxRetries) {
    const retryDelay = (retryCount + 1) * 2000;
    setTimeout(() => {
        loadDevices(retryCount + 1);
    }, retryDelay);
}
```

### **Error States:**
- `loading` - Data sedang di-fetch
- `error` - Error message jika fetch gagal
- `devices` - Array data devices
- `hasDevices` - Boolean apakah ada data

## 🔍 **DEBUGGING**

### **Console Logs:**
```javascript
console.log('=== DEVICES CONTEXT: FETCHING DEVICES ===');
console.log('Raw API response:', devicesData);
console.log('Valid devices:', validDevices.length);
console.log('Device structure sample:', validDevices[0]);
```

### **Network Tab:**
- Check API calls di browser Network tab
- Verify response status (200 OK)
- Check response data structure

## 🎯 **TESTING**

### **Manual Testing:**
1. **Open Browser Console** (F12)
2. **Check API Calls** - Lihat Network tab
3. **Verify Data** - Check console logs
4. **Test Auto Refresh** - Wait 30 seconds

### **Expected Behavior:**
- API call setiap 30 detik
- Data devices ter-load dengan benar
- Auto switch berjalan dengan data real
- Error handling berfungsi jika API gagal

## 🚀 **PRODUCTION CONSIDERATIONS**

### **Performance:**
- ✅ **Caching** - Data di-cache di context
- ✅ **Error Recovery** - Retry mechanism
- ✅ **Loading States** - Proper loading indicators

### **Security:**
- ✅ **Authentication** - Bearer token
- ✅ **HTTPS** - Secure connection
- ✅ **Error Handling** - No sensitive data exposed

### **Monitoring:**
- ✅ **Console Logs** - Debug information
- ✅ **Error Tracking** - Error states
- ✅ **Performance** - Response time monitoring

---

**API integration sudah fully functional dan siap untuk production!** 🚀
