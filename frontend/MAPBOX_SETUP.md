# Setup Mapbox untuk FFWS Jatim

## Langkah-langkah Setup

### 1. Daftar Akun Mapbox
1. Kunjungi [https://account.mapbox.com/](https://account.mapbox.com/)
2. Buat akun gratis atau login jika sudah memiliki akun
3. Setelah login, Anda akan mendapatkan access token

### 2. Dapatkan Access Token
1. Di dashboard Mapbox, klik pada "Access tokens"
2. Copy token yang dimulai dengan `pk.`
3. Token default adalah `pk.eyJ1...` (token publik)

### 3. Update Access Token
Buka file `src/public/components/MapboxMap.jsx` dan ganti baris berikut:

```javascript
mapboxgl.accessToken = 'pk.eyJ1IjoiZXhhbXBsZSIsImEiOiJjbGV4YW1wbGUifQ.example';
```

Dengan token Mapbox Anda yang sebenarnya:

```javascript
mapboxgl.accessToken = 'pk.eyJ1IjoiYWFhYSIsImEiOiJjY2JiYmJiYiJ9.xxxxx';
```

### 4. Fitur yang Tersedia

#### Peta Interaktif
- **Zoom & Pan**: Navigasi peta dengan mouse atau touch
- **Navigation Controls**: Tombol zoom in/out dan kompas
- **Fullscreen**: Mode layar penuh
- **Scale**: Indikator skala peta

#### Marker Stasiun
- **Status Color**: 
  - 🟢 Hijau: Status Aman (safe)
  - 🟡 Kuning: Status Waspada (warning)  
  - 🔴 Merah: Status Bahaya (alert)
- **Popup Info**: Klik marker untuk melihat detail stasiun
- **Real-time Update**: Marker berubah warna sesuai status terbaru

#### Data yang Ditampilkan
- Nama stasiun
- Status terkini (Aman/Waspada/Bahaya)
- Level air (meter)
- Lokasi stasiun

### 5. Koordinat Stasiun

Koordinat stasiun saat ini menggunakan koordinat umum untuk setiap kota di Jawa Timur. Untuk akurasi yang lebih baik, Anda dapat:

1. Menggunakan GPS untuk mendapatkan koordinat tepat
2. Menggunakan Google Maps untuk mendapatkan koordinat
3. Mengupdate koordinat di fungsi `getStationCoordinates()` dalam file `MapboxMap.jsx`

### 6. Customization

#### Mengubah Style Peta
Ganti `style` dalam konfigurasi map:

```javascript
style: 'mapbox://styles/mapbox/streets-v12'  // Street map
style: 'mapbox://styles/mapbox/satellite-v9' // Satellite
style: 'mapbox://styles/mapbox/outdoors-v11' // Outdoor
```

#### Mengubah Zoom Level
```javascript
zoom: 8  // 1-20, semakin besar semakin detail
```

#### Mengubah Center Map
```javascript
center: [112.5, -7.5]  // [longitude, latitude]
```

### 7. Troubleshooting

#### Peta Tidak Muncul
- Pastikan access token sudah benar
- Periksa console browser untuk error
- Pastikan internet connection aktif

#### Marker Tidak Muncul
- Periksa data `tickerData` dari FloodTicker
- Pastikan koordinat stasiun sudah benar
- Periksa console untuk error JavaScript

#### Performance Issues
- Kurangi jumlah marker jika terlalu banyak
- Gunakan clustering untuk marker yang berdekatan
- Optimasi update interval data

### 8. Dependencies

Aplikasi menggunakan:
- `mapbox-gl`: Library peta interaktif
- `react`: Framework UI
- `tailwindcss`: Styling

### 9. File yang Terlibat

- `src/public/components/MapboxMap.jsx`: Komponen peta utama
- `src/public/layout/Layout.jsx`: Layout yang mengintegrasikan peta
- `src/public/components/FloodTicker.jsx`: Komponen ticker yang menyediakan data
- `src/main.jsx`: Import CSS Mapbox

### 10. Catatan Penting

- Mapbox memiliki limit penggunaan untuk akun gratis
- Token publik dapat dilihat di source code, gunakan token terbatas untuk production
- Peta akan otomatis update setiap 3 detik sesuai dengan data FloodTicker
- Marker akan berubah warna sesuai status stasiun secara real-time
