# FFWS Jatim - Frontend

Flood Forecasting and Weather System Jawa Timur - Frontend React Application

## 🚀 Fitur

- Dashboard monitoring cuaca dan banjir
- Prakiraan cuaca real-time
- Sistem peringatan dini banjir
- Analisis data meteorologi
- Laporan dan visualisasi data
- Responsive design dengan Tailwind CSS

## 🛠️ Teknologi

- **React 19** - Library JavaScript untuk UI
- **Vite** - Build tool yang cepat
- **Tailwind CSS** - Framework CSS utility-first
- **PostCSS** - Tool untuk memproses CSS

## 📦 Instalasi

1. Clone repository:
```bash
git clone https://github.com/fisabilimf/ffws-jatim.git
cd ffws-jatim/frontend
```

2. Install dependencies:
```bash
npm install
```

3. Jalankan development server:
```bash
npm run dev
```

4. Build untuk production:
```bash
npm run build
```

## 🏗️ Struktur Proyek

```
src/
├── components/
│   ├── common/          # Komponen yang dapat digunakan kembali
│   │   ├── Button.jsx
│   │   └── Card.jsx
│   └── layout/          # Komponen layout utama
│       ├── Layout.jsx
│       ├── Header.jsx
│       ├── Sidebar.jsx
│       └── Footer.jsx
├── App.jsx              # Komponen utama aplikasi
├── main.jsx             # Entry point React
├── App.css              # Styling komponen utama
└── index.css            # Styling global dengan Tailwind
```

## 🎨 Komponen

### Layout Components
- **Layout**: Struktur utama aplikasi
- **Header**: Navigasi atas dengan menu utama
- **Sidebar**: Menu samping dengan navigasi
- **Footer**: Informasi copyright dan link

### Common Components
- **Button**: Komponen tombol dengan berbagai variant
- **Card**: Komponen card untuk konten

## 🔧 Scripts

- `npm run dev` - Development server dengan hot reload
- `npm run build` - Build production
- `npm run test` - Jalankan test (belum dikonfigurasi)

## 🌐 Development Server

Aplikasi akan berjalan di `http://localhost:3000` dengan fitur:
- Hot reload
- CORS enabled
- Source maps untuk debugging

## 📱 Responsive Design

Aplikasi sudah dioptimalkan untuk berbagai ukuran layar menggunakan Tailwind CSS responsive utilities.

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Distributed under the ISC License. See `LICENSE` for more information.

## 📞 Kontak

- Repository: [https://github.com/fisabilimf/ffws-jatim](https://github.com/fisabilimf/ffws-jatim)
- Issues: [https://github.com/fisabilimf/ffws-jatim/issues](https://github.com/fisabilimf/ffws-jatim/issues)



