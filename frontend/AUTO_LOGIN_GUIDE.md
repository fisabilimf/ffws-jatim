# 🔐 Auto-Login Implementation Guide

Sistem auto-login telah berhasil diimplementasikan untuk menghindari login manual. Aplikasi akan login otomatis menggunakan credentials yang disimpan di environment variables.

## ✨ Fitur Utama

- ✅ **Auto-Login** - Login otomatis menggunakan environment variables
- ✅ **Automatic Token Refresh** - Token di-refresh otomatis sebelum expired
- ✅ **Token Persistence** - Token tersimpan di localStorage
- ✅ **Error Handling** - Handle login failure dengan retry mechanism
- ✅ **UI Management** - Login manual dan set token melalui interface
- ✅ **Fallback Token** - Menggunakan token default jika auto-login gagal
- ✅ **Auto Retry** - Retry request otomatis saat 401

## 🚀 Setup Auto-Login

### 1. Buat File Environment Variables

Buat file `.env.local` di root project:

```env
# API Configuration
VITE_API_BASE_URL=https://ffws-backend.rachmanesa.com/api

# Auto Login Configuration
VITE_AUTO_LOGIN_ENABLED=true
VITE_AUTO_LOGIN_EMAIL=your_email@example.com
VITE_AUTO_LOGIN_PASSWORD=your_password_here

# Auto Login Settings
VITE_AUTO_LOGIN_RETRY_ATTEMPTS=3
VITE_AUTO_LOGIN_RETRY_DELAY=5000

# Token Configuration (Optional - untuk set token manual)
VITE_DEFAULT_TOKEN=your_access_token_here
VITE_DEFAULT_TOKEN_EXPIRES_AT=2024-01-01T01:00:00+00:00
```

### 2. Restart Development Server

```bash
npm run dev
```

### 3. Verifikasi Auto-Login

1. Buka aplikasi di browser
2. Cek komponen TokenManager
3. Pastikan "Auto-Login: Enabled" dan "Credentials: Available"
4. Token akan di-set otomatis

## 🔧 Cara Kerja Sistem

### 1. Initial Setup
```
Aplikasi Start
  ↓
Load token dari localStorage
  ↓
Jika tidak ada token atau expired:
  - Cek VITE_AUTO_LOGIN_ENABLED
  - Jika enabled, cek credentials
  - Login otomatis
  - Simpan token
  ↓
Setup auto refresh
```

### 2. Auto-Login Process
```
Auto-Login Trigger
  ↓
Cek credentials dari environment
  ↓
POST /api/auth/login
  ↓
Jika berhasil:
  - Simpan token
  - Setup auto refresh
  ↓
Jika gagal:
  - Retry dengan delay
  - Fallback ke token default
```

### 3. Token Refresh Process
```
Token Refresh Trigger
  ↓
POST /api/auth/refresh
  ↓
Jika berhasil:
  - Update token
  - Continue request
  ↓
Jika gagal:
  - Coba auto-login
  - Retry request
```

## 🧪 Testing

### 1. Test Auto-Login
1. Set environment variables
2. Restart aplikasi
3. Cek console logs untuk auto-login
4. Verify token tersimpan

### 2. Test Token Refresh
1. Set token dengan waktu expired pendek
2. Lakukan API call
3. Verify token di-refresh otomatis

### 3. Test Error Handling
1. Set credentials yang salah
2. Restart aplikasi
3. Verify retry mechanism
4. Verify fallback ke token default

## 🔍 Debugging

### Console Logs
Buka browser DevTools → Console untuk melihat logs:

```
🔐 Auto-login setup started
🔐 No valid token found, attempting auto-login...
🔐 Attempting auto-login...
✅ Auto-login successful
🔄 Auto-refresh token setup started
=== API CALL ===
URL: https://ffws-backend.rachmanesa.com/api/endpoint
Headers: { Authorization: 'Bearer [TOKEN]' }
Response status: 200
=== END API CALL ===
```

### Environment Variables
Buka DevTools → Console dan jalankan:

```javascript
// Check environment variables
console.log('Auto-login enabled:', import.meta.env.VITE_AUTO_LOGIN_ENABLED);
console.log('Email:', import.meta.env.VITE_AUTO_LOGIN_EMAIL);
console.log('Password:', import.meta.env.VITE_AUTO_LOGIN_PASSWORD ? '***' : 'Not set');
```

### LocalStorage
Buka DevTools → Application → Local Storage:

- `ffws_token`: Access token
- `ffws_token_expires_at`: Token expiry time

## 🚨 Troubleshooting

### Problem: Auto-login tidak berjalan
**Solution:**
1. Check environment variables
2. Verify VITE_AUTO_LOGIN_ENABLED=true
3. Check email dan password
4. Restart development server

### Problem: Auto-login gagal
**Solution:**
1. Check credentials benar
2. Check API endpoint tersedia
3. Check console untuk error messages
4. Verify network connectivity

### Problem: Token tidak persist
**Solution:**
1. Check localStorage availability
2. Verify token format
3. Check browser security settings

## 🔒 Security Considerations

### Environment Variables
- **Development**: Simpan di `.env.local` (tidak di-commit)
- **Production**: Set di server environment variables
- **Credentials**: Gunakan akun khusus untuk auto-login

### Token Storage
- Token disimpan di localStorage
- Auto-refresh sebelum expired
- Fallback ke token default jika gagal

### Network Security
- Gunakan HTTPS di production
- Verify API endpoints
- Monitor failed login attempts

## 📊 Monitoring

### Browser DevTools

1. **Network Tab**: Monitor login requests
2. **Console**: Check logs untuk debugging
3. **Application Tab**: Monitor localStorage contents

### Log Messages

```
🔐 Auto-login setup started
🔐 No valid token found, attempting auto-login...
🔐 Attempting auto-login...
✅ Auto-login successful
🔄 Auto-refresh token setup started
⏰ Token akan expired dalam 4 menit, refreshing...
✅ Token refreshed successfully
```

## 🎯 Configuration Options

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `VITE_AUTO_LOGIN_ENABLED` | Enable/disable auto-login | `false` |
| `VITE_AUTO_LOGIN_EMAIL` | Email untuk auto-login | Required |
| `VITE_AUTO_LOGIN_PASSWORD` | Password untuk auto-login | Required |
| `VITE_AUTO_LOGIN_RETRY_ATTEMPTS` | Jumlah retry attempts | `3` |
| `VITE_AUTO_LOGIN_RETRY_DELAY` | Delay antar retry (ms) | `5000` |

### Token Configuration

| Variable | Description | Default |
|----------|-------------|---------|
| `VITE_DEFAULT_TOKEN` | Token default fallback | Hardcoded |
| `VITE_DEFAULT_TOKEN_EXPIRES_AT` | Token expiry time | 1 jam |

## 🔄 Migration dari Sistem Lama

Sistem lama menggunakan hardcoded token. Sistem baru:

1. ✅ Auto-login menggunakan environment variables
2. ✅ Automatic token refresh
3. ✅ Better error handling
4. ✅ UI untuk manual login
5. ✅ Fallback ke token default

**Tidak ada breaking changes** - semua API calls existing akan tetap berfungsi.

## 📞 Support

Jika ada masalah atau pertanyaan:

1. Check console logs untuk error messages
2. Verify environment variables
3. Check localStorage contents
4. Verify API endpoints tersedia
5. Test dengan credentials manual

---

**Sistem auto-login siap digunakan! 🎉**

Aplikasi akan login otomatis, token akan di-refresh otomatis, dan semua API calls akan berfungsi tanpa interaksi manual.
