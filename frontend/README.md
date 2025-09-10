# FFWS JATIM - Flood Forecasting & Weather System

Sistem monitoring dan prediksi banjir cerdas untuk wilayah Jawa Timur yang dikembangkan dengan teknologi modern dan user-friendly.

## 🚀 Fitur Utama

### 📊 Dashboard Monitoring
- **Monitoring Real-time**: Pemantauan kondisi air sungai secara real-time dengan update setiap jam
- **Prediksi Cerdas**: Sistem prediksi banjir menggunakan teknologi AI dan machine learning
- **Visualisasi Data**: Grafik dan chart yang mudah dipahami untuk monitoring level air
- **Status Monitoring**: Indikator status kondisi air (Aman, Siaga, Waspada, Bahaya)

### 📈 Riwayat Data
- **Data Historis**: Akses ke data historis monitoring air sungai
- **Filter & Pencarian**: Filter berdasarkan periode waktu dan lokasi
- **Export Data**: Kemampuan untuk export data dalam berbagai format
- **Statistik**: Analisis statistik data monitoring

### 🎨 UI/UX Modern
- **Responsive Design**: Tampilan yang responsif untuk desktop dan mobile
- **Modern Interface**: Desain yang clean, modern dan mudah dipahami
- **User-Friendly**: Interface yang intuitif untuk pengguna umum
- **Color Coding**: Sistem warna yang konsisten untuk status dan informasi

## 🛠️ Teknologi yang Digunakan

- **Frontend**: React.js dengan Vite
- **Styling**: Tailwind CSS
- **Routing**: React Router DOM
- **Charts**: SVG-based charts untuk visualisasi data
- **Responsive**: Mobile-first design approach

## 📱 Halaman yang Tersedia

### 1. Home (/)
- Landing page dengan informasi lengkap tentang FFWS JATIM
- Fitur unggulan dan statistik sistem
- Peringatan terbaru dan status sungai
- Informasi tentang pemerintah provinsi

### 2. Dashboard (/dashboard)
- Monitoring real-time kondisi air sungai Dhompo
- Grafik perkembangan air aktual dan prediksi
- Konfigurasi model prediksi (LSTM)
- Tabel data prediksi dengan update per jam

### 3. History (/history)
- Riwayat lengkap data monitoring
- Filter berdasarkan periode dan lokasi
- Statistik data historis
- Export dan generate report

## 🎯 Fitur Khusus

### Monitoring Air Sungai
- **Tinggi Muka Air**: Monitoring level air sungai dalam meter
- **Status Kondisi**: Indikator status dengan color coding
- **Prediksi**: Sistem prediksi menggunakan model LSTM
- **Update Real-time**: Data terupdate setiap jam

### Konfigurasi Prediksi
- **Model AI**: LSTM (Long Short-Term Memory)
- **Periode Prediksi**: 1 jam ke depan
- **Parameter**: Dapat dikonfigurasi sesuai kebutuhan

### Notifikasi & Alert
- **Sistem Peringatan**: Alert otomatis untuk kondisi berbahaya
- **Request Notifikasi**: Form untuk meminta notifikasi
- **Status Monitoring**: Real-time status update

## 🚀 Cara Menjalankan

### Prerequisites
- Node.js 20.19+ atau 22.12+
- npm atau yarn

### Installation
```bash
# Clone repository
git clone https://github.com/fisabilimf/ffws-jatim.git

# Masuk ke direktori frontend
cd ffws-jatim/frontend

# Install dependencies
npm install

# Jalankan development server
npm run dev
```

### Build Production
```bash
# Build untuk production
npm run build

# Preview build
npm run preview
```

## 📁 Struktur Project

```
frontend/
├── src/
│   ├── components/
│   │   ├── common/
│   │   │   ├── Button.jsx
│   │   │   ├── Card.jsx
│   │   │   └── StockTicker.jsx
│   │   ├── dashboard/
│   │   │   ├── Dashboard.jsx
│   │   │   └── History.jsx
│   │   ├── layout/
│   │   │   ├── Footer.jsx
│   │   │   ├── Header.jsx
│   │   │   └── Layout.jsx
│   │   └── Home.jsx
│   ├── App.jsx
│   ├── App.css
│   ├── index.css
│   └── main.jsx
├── package.json
├── tailwind.config.js
├── vite.config.js
└── README.md
```

## 🎨 Design System

### Color Palette
- **Primary**: Blue (#3B82F6, #1D4ED8)
- **Success**: Green (#10B981, #059669)
- **Warning**: Yellow (#F59E0B, #D97706)
- **Danger**: Red (#EF4444, #DC2626)
- **Neutral**: Gray (#6B7280, #374151)

### Typography
- **Headings**: Font-bold dengan ukuran yang bervariasi
- **Body**: Font-medium untuk teks utama
- **Captions**: Font-normal untuk informasi tambahan

### Components
- **Cards**: Shadow dan border radius yang konsisten
- **Buttons**: Hover effects dan transitions
- **Tables**: Responsive design dengan hover effects
- **Charts**: SVG-based dengan color coding yang jelas

## 🔧 Konfigurasi

### Environment Variables
```bash
# .env
VITE_API_URL=http://localhost:8000/api
VITE_APP_NAME=FFWS JATIM
```

### Tailwind Configuration
- Custom color palette
- Responsive breakpoints
- Component variants

## 📊 Data Sources

- **Sensor IoT**: Data real-time dari sensor air sungai
- **Weather API**: Data cuaca dan curah hujan
- **Historical Data**: Database historis monitoring
- **AI Model**: Prediksi menggunakan machine learning

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Distributed under the ISC License. See `LICENSE` for more information.

## 📞 Kontak

- **Tim FFWS JATIM** - [@ffws_jatim](https://twitter.com/ffws_jatim)
- **Email** - info@ffws-jatim.go.id
- **Website** - [https://ffws-jatim.go.id](https://ffws-jatim.go.id)

## 🙏 Ucapan Terima Kasih

- Pemerintah Provinsi Jawa Timur
- Dinas PUPR Jawa Timur
- Tim Pengembang FFWS JATIM
- Masyarakat Jawa Timur

---

**FFWS JATIM** - Melindungi Jawa Timur dari ancaman banjir dengan teknologi cerdas 🚀



