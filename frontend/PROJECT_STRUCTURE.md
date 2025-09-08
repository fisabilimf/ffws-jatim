# Struktur File Sistem FFWS Jatim

## 📁 Struktur Direktori Utama

```
src/
├── components/                    # Komponen yang dapat digunakan ulang (shared)
│   ├── Button.jsx                # ✅ Sudah ada - Komponen button
│   ├── Card.jsx                  # ✅ Sudah ada - Komponen card
│   ├── StockTicker.jsx           # ✅ Sudah ada - Komponen ticker
│   ├── Modal.jsx                 # Komponen modal untuk popup
│   ├── Loading.jsx               # Komponen loading spinner
│   ├── Alert.jsx                 # Komponen alert/notification
│   ├── Table.jsx                 # Komponen tabel yang dapat digunakan ulang
│   ├── Input.jsx                 # Input field dengan validasi
│   ├── Select.jsx                # Dropdown select
│   ├── TextArea.jsx              # Text area input
│   ├── Checkbox.jsx              # Checkbox input
│   ├── Radio.jsx                 # Radio button input
│   ├── FormGroup.jsx             # Wrapper form dengan label dan error
│   ├── Badge.jsx                 # Badge/tag component
│   ├── Tooltip.jsx               # Tooltip component
│   ├── Pagination.jsx            # Pagination component
│   └── Breadcrumb.jsx            # Breadcrumb navigation
├── pages/                        # Halaman-halaman aplikasi
│   ├── public/                   # Halaman yang dapat diakses publik
│   │   ├── components/           # Komponen khusus halaman public
│   │   │   ├── HeroSection.jsx   # Hero section untuk homepage
│   │   │   ├── FeatureCard.jsx   # Card fitur untuk homepage
│   │   │   ├── NewsCard.jsx      # Card berita
│   │   │   └── ContactForm.jsx   # Form kontak
│   │   ├── layouts/              # Layout khusus halaman public
│   │   │   ├── PublicLayout.jsx  # Layout utama halaman public
│   │   │   ├── PublicHeader.jsx  # Header khusus public
│   │   │   └── PublicFooter.jsx  # Footer khusus public
│   │   ├── Home.jsx              # Halaman beranda
│   │   ├── About.jsx             # Halaman tentang kami
│   │   ├── News.jsx              # Halaman berita/informasi
│   │   ├── Contact.jsx           # Halaman kontak
│   │   └── index.js              # Export semua halaman public
│   └── admin/                    # Halaman khusus admin
│       ├── components/            # Komponen khusus halaman admin
│       │   ├── DashboardCard.jsx # Card untuk dashboard
│       │   ├── UserTable.jsx     # Tabel user management
│       │   ├── ContentEditor.jsx # Editor konten
│       │   ├── DataTable.jsx     # Tabel data dengan fitur CRUD
│       │   ├── SearchFilter.jsx  # Komponen pencarian dan filter
│       │   └── ConfirmationModal.jsx # Modal konfirmasi
│       ├── layouts/               # Layout khusus halaman admin
│       │   ├── AdminLayout.jsx    # Layout utama admin
│       │   ├── AdminHeader.jsx    # Header khusus admin
│       │   ├── AdminSidebar.jsx   # Sidebar admin (✅ Sudah ada Sidebar.jsx)
│       │   └── AdminFooter.jsx    # Footer khusus admin
│       ├── Dashboard.jsx          # Dashboard utama admin
│       ├── Users.jsx              # Manajemen user
│       ├── Content.jsx            # Manajemen konten
│       ├── Settings.jsx           # Pengaturan sistem
│       └── index.js               # Export semua halaman admin
├── hooks/                         # Custom React hooks (shared)
│   ├── useAuth.js                # Hook untuk autentikasi
│   ├── useApi.js                 # Hook untuk API calls
│   ├── useLocalStorage.js        # Hook untuk localStorage
│   ├── useDebounce.js            # Hook untuk debounce input
│   ├── usePagination.js          # Hook untuk pagination
│   └── useForm.js                # Hook untuk form handling
├── services/                      # Layanan API dan utilitas (shared)
│   ├── api/                      # Fungsi-fungsi untuk komunikasi dengan backend
│   │   ├── auth.js               # API autentikasi
│   │   ├── users.js              # API user management
│   │   ├── content.js            # API content management
│   │   ├── news.js               # API berita
│   │   └── index.js              # Export semua API services
│   ├── utils/                     # Fungsi helper dan konstanta
│   │   ├── constants.js           # Konstanta aplikasi
│   │   ├── helpers.js             # Fungsi helper umum
│   │   ├── validation.js          # Validasi form
│   │   ├── dateUtils.js           # Utility untuk tanggal
│   │   └── index.js               # Export semua utils
│   ├── config/                    # Konfigurasi aplikasi
│   │   ├── apiConfig.js           # Konfigurasi API
│   │   ├── appConfig.js           # Konfigurasi aplikasi
│   │   └── index.js               # Export semua config
│   └── index.js                   # Export semua services
├── context/                       # React Context untuk state management (shared)
│   ├── AuthContext.js             # Context untuk autentikasi
│   ├── AppContext.js              # Context untuk data aplikasi
│   ├── ThemeContext.js            # Context untuk tema aplikasi
│   └── index.js                   # Export semua context
├── assets/                        # File statis (shared)
│   ├── images/                    # Gambar dan foto
│   │   ├── logo/                  # Logo aplikasi
│   │   ├── icons/                 # Icon aplikasi
│   │   └── backgrounds/           # Background images
│   ├── icons/                     # Icon SVG/PNG
│   └── styles/                    # File styling tambahan
├── styles/                        # File CSS/SCSS global (shared)
│   ├── globals.css                # CSS global
│   ├── variables.css              # CSS variables
│   ├── components.css             # Styling komponen
│   └── utilities.css              # Utility classes
├── routes/                        # Konfigurasi routing (shared)
│   ├── PublicRoutes.js            # Route untuk halaman public
│   ├── AdminRoutes.js             # Route untuk halaman admin
│   ├── ProtectedRoute.js          # Komponen proteksi route admin
│   ├── AppRoutes.js               # Route utama aplikasi
│   └── index.js                   # Export semua routes
├── types/                         # Type definitions (jika menggunakan TypeScript)
│   ├── api.types.js               # Type untuk API responses
│   ├── components.types.js        # Type untuk komponen
│   └── index.js                   # Export semua types
├── constants/                      # Konstanta aplikasi (shared)
│   ├── routes.js                  # Konstanta route
│   ├── api.js                     # Konstanta API
│   ├── messages.js                # Pesan aplikasi
│   └── index.js                   # Export semua constants
├── utils/                         # Utility functions (shared)
│   ├── storage.js                 # Local storage utilities
│   ├── format.js                  # Formatting utilities
│   ├── validation.js              # Validation utilities
│   └── index.js                   # Export semua utils
├── App.jsx                        # ✅ Sudah ada - Komponen utama aplikasi
├── App.css                        # ✅ Sudah ada - Styling utama aplikasi
├── main.jsx                       # ✅ Sudah ada - Entry point aplikasi
└── index.css                      # ✅ Sudah ada - CSS global
```

## 🎯 Penjelasan Struktur

### 1. **Components/** (Shared)
- Semua komponen yang dapat digunakan di kedua halaman (public & admin)
- Komponen form, UI, dan utility yang dapat digunakan ulang
- Struktur flat seperti Laravel untuk kemudahan maintenance

### 2. **Pages/**
- **public/**: Halaman yang dapat diakses semua pengunjung
  - **components/**: Komponen khusus halaman public
  - **layouts/**: Layout khusus halaman public
- **admin/**: Halaman khusus admin dengan fitur CRUD
  - **components/**: Komponen khusus halaman admin
  - **layouts/**: Layout khusus halaman admin

### 3. **Hooks/** (Shared)
- Custom hooks untuk logic yang dapat digunakan ulang
- Hook untuk autentikasi, API calls, dan state management

### 4. **Services/** (Shared)
- **api/**: Fungsi-fungsi untuk komunikasi dengan backend
- **utils/**: Fungsi helper dan konstanta
- **config/**: Konfigurasi aplikasi

### 5. **Context/** (Shared)
- State management global menggunakan React Context
- Context untuk autentikasi, data aplikasi, dan tema

### 6. **Assets & Styles/** (Shared)
- File statis dan styling global
- Variabel CSS dan komponen styling

### 7. **Routes/** (Shared)
- Konfigurasi routing dengan proteksi halaman admin
- Pemisahan route public dan admin

## 🔐 Sistem Autentikasi

- **Public Routes**: Dapat diakses tanpa login
- **Protected Routes**: Hanya dapat diakses admin yang sudah login
- **Role-based Access**: Kontrol akses berdasarkan role user

## 📱 Responsive Design

- Menggunakan Tailwind CSS untuk styling
- Komponen yang responsive untuk mobile dan desktop
- Layout yang adaptif untuk berbagai ukuran layar

## 🚀 Fitur Utama

### Public Pages:
- Home (Beranda) - Informasi umum FFWS Jatim
- About (Tentang Kami) - Profil dan visi misi
- News (Berita/Informasi) - Informasi terkini dan peringatan
- Contact (Kontak) - Informasi kontak dan form

### Admin Pages:
- Dashboard (Overview sistem) - Statistik dan monitoring
- Users (Manajemen user) - CRUD user dan role management
- Content (Manajemen konten) - CRUD berita dan informasi
- Settings (Pengaturan sistem) - Konfigurasi aplikasi

## 🔧 Teknologi yang Digunakan

- **Frontend**: React + Vite
- **Styling**: Tailwind CSS
- **State Management**: React Context
- **Routing**: React Router
- **HTTP Client**: Fetch API (native) / Axios
- **Build Tool**: Vite
- **Package Manager**: npm

## 📋 File yang Sudah Ada (✅)

- `src/App.jsx` - Komponen utama aplikasi
- `src/App.css` - Styling utama aplikasi  
- `src/main.jsx` - Entry point aplikasi
- `src/index.css` - CSS global
- `src/components/common/Button.jsx` - Komponen button
- `src/components/common/Card.jsx` - Komponen card
- `src/components/common/StockTicker.jsx` - Komponen ticker
- `src/components/layout/Layout.jsx` - Layout utama
- `src/components/layout/Header.jsx` - Header komponen
- `src/components/layout/Footer.jsx` - Footer komponen
- `src/components/layout/Sidebar.jsx` - Sidebar komponen

## 🎯 Keuntungan Struktur Ini

1. **Separation of Concerns**: Pemisahan yang jelas antara public dan admin
2. **Reusability**: Komponen shared dapat digunakan ulang
3. **Maintainability**: Struktur yang mudah dipahami dan di-maintain
4. **Scalability**: Mudah untuk menambah fitur baru
5. **Professional**: Mengikuti best practices seperti Laravel
6. **Consistent**: Konsistensi dalam penamaan dan struktur
