# FFWS JATIM - Flood Forecasting & Weather System

Sistem monitoring dan prediksi banjir cerdas untuk wilayah Jawa Timur.

## 🚀 Fitur Utama

- **Monitoring Real-time**: Pemantauan kondisi air sungai dengan update setiap jam
- **Prediksi Cerdas**: Sistem prediksi banjir menggunakan AI dan machine learning
- **Visualisasi Data**: Grafik dan chart untuk monitoring level air
- **Status Monitoring**: Indikator status (Aman, Waspada, Bahaya)
- **Peta Interaktif**: Mapbox integration dengan marker status real-time

## 🛠️ Teknologi

- **Frontend**: React.js + Vite
- **Styling**: Tailwind CSS
- **Charts**: Recharts + Custom SVG charts
- **Maps**: Mapbox GL JS
- **Spatial Data**: shpjs v6.1.0
- **Build**: Terser v5.44.0

## 📱 Halaman

- **Dashboard** (`/dashboard`): Monitoring real-time dengan grafik prediksi
- **History** (`/history`): Riwayat data monitoring dengan filter

## 🚀 Instalasi & Menjalankan

```bash
# Install dependencies
npm install

# Development server
npm run dev

# Build production
npm run build

# Preview build
npm run preview
```

## 📁 Struktur Project

```
src/
├── public/
│   ├── components/          # Komponen UI
│   │   ├── ui/             # Komponen chart, toggle, legend
│   │   ├── sensors/        # Detail panel, station detail
│   │   └── devices/        # Mapbox map, tooltip
│   ├── config/             # Konfigurasi stasiun
│   ├── layout/             # Layout utama
│   └── pages/              # Halaman aplikasi
├── utils/                  # Utility functions
│   ├── statusUtils.js      # Status color & text helpers
│   ├── optimizationConfig.js
│   └── performanceMonitor.js
└── main.jsx               # Entry point
```

## 🎨 Design System

### Status Colors
- **Aman**: Green (#10B981)
- **Waspada**: Yellow (#F59E0B) 
- **Bahaya**: Red (#EF4444)

### Chart Features
- **Real-time updates**: Data terupdate setiap jam
- **Status-based coloring**: Warna otomatis berdasarkan status
- **Responsive design**: Mobile dan desktop friendly
- **Mini charts**: Untuk ticker dan running bar

## 🔧 Konfigurasi

### Mapbox Setup
1. Daftar di [Mapbox](https://account.mapbox.com/)
2. Dapatkan access token
3. Update token di `src/public/components/devices/MapboxMap.jsx`

### Environment Variables
```bash
VITE_API_URL=http://localhost:8000/api
VITE_APP_NAME=FFWS JATIM
```

## 📊 Performance

- **Optimized charts**: 10 FPS animation, memoization
- **Lazy loading**: Komponen non-critical di-load on-demand
- **Bundle splitting**: Vendor chunks terpisah
- **Terser minification**: Production build dioptimasi

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch
3. Commit changes
4. Push ke branch
5. Buat Pull Request

## 📄 Lisensi

ISC License

---

**FFWS JATIM** - Melindungi Jawa Timur dari ancaman banjir dengan teknologi cerdas 🚀