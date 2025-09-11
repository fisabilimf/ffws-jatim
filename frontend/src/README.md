# Struktur Folder FFWS JATIM Frontend

## 📁 **Struktur Folder yang Diorganisir**

```
src/
├── components/           # Komponen UI yang dapat digunakan kembali
│   ├── common/          # Komponen umum (Card, Button, dll)
│   ├── layout/          # Komponen layout (Header, Sidebar, Footer)
│   ├── ui/              # Komponen UI khusus (StatusBadge, dll)
│   ├── charts/          # Komponen chart dan visualisasi
│   └── index.js         # Export semua komponen
├── features/             # Fitur-fitur utama aplikasi
│   ├── dashboard/        # Fitur dashboard monitoring
│   ├── history/          # Fitur riwayat data
│   └── index.js          # Export semua features
├── pages/                # Halaman utama aplikasi
│   ├── Home.jsx          # Halaman landing/home
│   └── index.js          # Export semua pages
├── hooks/                # Custom React hooks
├── utils/                # Utility functions
├── constants/            # Konstanta dan data statis
├── types/                # Type definitions (untuk TypeScript)
├── assets/               # Gambar, icon, dan asset lainnya
├── App.jsx               # Komponen utama aplikasi
├── main.jsx              # Entry point React
└── index.css             # Styling global
```

## 🎯 **Prinsip Organisasi**

### **1. Feature-Based Structure**
- Setiap fitur utama memiliki folder sendiri
- Komponen yang berkaitan dikelompokkan bersama
- Mudah untuk scaling dan maintenance

### **2. Component Reusability**
- Komponen umum di folder `components/common`
- Komponen khusus di folder `components/ui`
- Export terpusat melalui `index.js`

### **3. Clear Separation of Concerns**
- **Pages**: Halaman utama aplikasi
- **Features**: Logika bisnis dan fitur
- **Components**: UI components yang reusable
- **Utils**: Helper functions dan utilities

## 📋 **Cara Penggunaan**

### **Import Components**
```javascript
// Import dari index.js
import { Card, StatusBadge } from '@/components/ui'
import { Dashboard, History } from '@/features'
import { Home } from '@/pages'

// Atau import langsung
import Card from '@/components/common/Card'
import Dashboard from '@/features/dashboard/Dashboard'
```

### **Menambah Fitur Baru**
1. Buat folder baru di `src/features/[nama-fitur]`
2. Buat komponen utama di folder tersebut
3. Export melalui `src/features/index.js`
4. Import di `App.jsx` atau komponen lain

### **Menambah Komponen UI**
1. Buat file komponen di `src/components/ui/`
2. Export melalui `src/components/ui/index.js`
3. Gunakan di fitur atau halaman yang membutuhkan

## 🔧 **Best Practices**

1. **Naming Convention**: Gunakan PascalCase untuk komponen, camelCase untuk file
2. **File Organization**: Satu komponen per file
3. **Import/Export**: Gunakan named exports untuk komponen, default exports untuk halaman
4. **Folder Structure**: Jaga konsistensi struktur folder
5. **Index Files**: Gunakan index.js untuk export yang terpusat

## 📱 **Komponen yang Tersedia**

### **Common Components**
- `Card`: Komponen card dengan title dan content
- `Button`: Komponen button dengan berbagai variant
- `Layout`: Layout utama aplikasi dengan sidebar
- `Header`: Header dengan navigation dan user menu
- `Footer`: Footer dengan informasi dan links

### **UI Components**
- `StatusBadge`: Badge untuk status dengan color coding
- `WaterLevelChart`: Chart untuk visualisasi level air
- `PredictionChart`: Chart untuk prediksi data

### **Features**
- `Dashboard`: Dashboard monitoring air sungai
- `History`: Halaman riwayat data monitoring
- `Home`: Landing page dengan informasi FFWS JATIM

## 🚀 **Keuntungan Struktur Ini**

1. **Scalability**: Mudah menambah fitur baru
2. **Maintainability**: Kode terorganisir dengan baik
3. **Reusability**: Komponen dapat digunakan kembali
4. **Team Collaboration**: Struktur yang jelas untuk tim
5. **Performance**: Lazy loading dan code splitting yang mudah
6. **Testing**: Unit testing yang lebih mudah
7. **Documentation**: Dokumentasi yang terstruktur
