# PresenceSync System Requirements & Tech Stack

Sistem Manajemen Absensi & Real-time Hardware Sync Solution X100-C

---

## 🛠️ 1. Core Backend & Engine
- **Programming Language**: PHP (^8.1 / ^8.2)
- **Web Framework**: Laravel Framework (^13.0)
- **Database Management**: MySQL / MariaDB (InnoDB engine, Support Foreign Keys, Indexing)
- **Web Server**: Apache / Nginx / IIS / PHP Built-in Server (`php artisan serve`)

---

## 📟 2. Physical Hardware & Network Protocol
- **Supported Hardware**: Mesin Fingerprint Solution X100-C (Multi-device: Gerbang 1, Gerbang 2, Kampus)
- **Communication Protocol**: Native SOAP / HTTP XML Web Service Protocol (`/iWsService` endpoint over TCP Port 80)
- **Socket Driver**: Native PHP `fsockopen` socket driver
- **Hardware SOAP Commands**:
  - `GetAttLog` - Tarik data log absensi realtime / batch dari mesin
  - `SetUserInfo` - Push / sync nama & PIN pengguna ke mesin
  - `DeleteUser` - Hapus PIN pengguna dari mesin saat data diedit/dihapus
  - `RefreshDB` - Reload memori display mesin secara instan
  - `ClearData` - Bersihkan memori log fisik mesin
  - `SetDeviceTime` - Sinkronisasi jam mesin dengan jam server

---

## 📦 3. Backend Packages & Dependencies (Composer)
- **`phpoffice/phpspreadsheet`** (^5.7): Import & Export data Excel (Siswa, Guru, Rekap Absensi)
- **`dompdf/dompdf`** (^3.1): Generating laporan absensi format PDF
- **`spatie/laravel-activitylog`** (^4.8): Logging aktivitas realtime admin & user (Audit Trail)
- **`spatie/laravel-permission`** (^6.4): Manajemen Role & Akses Pengguna (Admin, Guru, Siswa)
- **`yajra/laravel-datatables-oracle`** (^13.0): DataTables Server-side processing
- **`laravel/socialite`** (^5.12): Autentikasi OAuth (opsional)

---

## 🎨 4. Frontend & User Interface System
- **Admin Theme**: Metronic 8 HTML/Laravel Admin Template
- **CSS Framework**: Bootstrap 5 (v5.1.3)
- **Icon Libraries**:
  - Bootstrap Icons (^1.5.0)
  - FontAwesome Free (^5.15.3)
  - Metronic Duotune SVG Icons
- **JavaScript Stack**:
  - **jQuery**: Client-side DOM & AJAX Requests
  - **SweetAlert2**: Modal konfirmasi, alert flash, dan dialog interaktif
  - **Flatpickr / Daterangepicker**: Filter rentang tanggal laporan & log absensi
  - **DataTables (BS5)**: Tabel interaktif (Search, Sort, Pagination)
  - **Select2**: Dropdown select dinamis dengan fitur pencarian

---

## 📋 5. Minimum Server Specifications
- **Operating System**: Windows Server / Windows 10/11 / Linux (Ubuntu 20.04/22.04 LTS)
- **PHP Extensions Required**:
  - `ext-pdo` & `ext-pdo_mysql`
  - `ext-sockets`
  - `ext-mbstring`
  - `ext-xml` & `ext-dom`
  - `ext-gd` / `ext-imagick` (untuk cetak dokumen/PDF)
  - `ext-zip` (untuk modul Excel PhpSpreadsheet)
  - `ext-curl`
- **Network Requirement**: Terhubung dalam satu segmen jaringan LAN / VPN yang sama dengan IP mesin fingerprint (Contoh IP: `10.10.6.4`, `10.10.6.5`).
