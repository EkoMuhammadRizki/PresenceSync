# 🚀 Panduan Setup Local Server PresenceSync (SIAP)
**Domain VHost:** `https://siapsman1ciparay.test`  
**Launcher:** `SIAP-PresenceSync.exe`  
**Lokasi Standar:** `C:\laragon\www\siapsman1ciparay`

Dokumen ini adalah panduan lengkap untuk melakukan konfigurasi instalasi **PresenceSync (SIAP)** di Komputer Server Sekolah dari awal hingga siap digunakan oleh Guru, Siswa, Kesiswaan, dan Admin.

---

## 📑 Daftar Isi
1. [Prasyarat Sistem](#1-prasyarat-sistem)
2. [Langkah 1: Setup Laragon Web Server](#2-langkah-1-setup-laragon-web-server)
3. [Langkah 2: Menempatkan Source Code Project](#3-langkah-2-menempatkan-source-code-project)
4. [Langkah 3: Konfigurasi PHP Version & Ekstensi](#4-langkah-3-konfigurasi-php-version--ekstensi)
5. [Langkah 4: Konfigurasi Virtual Host & Local SSL (HTTPS)](#5-langkah-4-konfigurasi-virtual-host--local-ssl-https)
6. [Langkah 5: Konfigurasi File Environment (.env)](#6-langkah-5-konfigurasi-file-environment-env)
7. [Langkah 6: Eksekusi Database & Storage Link](#7-langkah-6-eksekusi-database--storage-link)
8. [Langkah 7: Konfigurasi Auto-Start Windows](#8-langkah-7-konfigurasi-auto-start-windows)
9. [Langkah 8: Shortcut Desktop & Launcher SIAP-PresenceSync.exe](#9-langkah-8-shortcut-desktop--launcher-siap-presencesyncexe)
10. [Langkah 9: Akses dari Komputer Lain di Jaringan LAN / WiFi Sekolah](#10-langkah-9-akses-dari-komputer-lain-di-jaringan-lan--wifi-sekolah)
11. [Daftar Kredensial Akun Standar](#11-daftar-kredensial-akun-standar)
12. [Script Cepat Setup Otomatis (All-in-One)](#12-script-cepat-setup-otomatis-all-in-one)
13. [Troubleshooting & Solusi](#13-troubleshooting--solusi)

---

## 1. Prasyarat Sistem
* **Sistem Operasi**: Windows 10 / Windows 11 / Windows Server (64-bit).
* **Hak Akses**: Administrator Windows.
* **Software Wajib**:
  * [Laragon Full](https://laragon.org/download/) (PHP 8.4/8.5+, Apache 2.4, MySQL 8.0).
  * Web Browser modern (Google Chrome, Microsoft Edge, atau Mozilla Firefox).
  * Git for Windows (opsional tapi disarankan).

---

## 2. Langkah 1: Setup Laragon Web Server
1. Download dan instal **Laragon Full** di lokasi default:
   ```
   C:\laragon
   ```
2. Buka aplikasi **Laragon**, klik tombol **Start All** untuk memastikan service Apache dan MySQL dapat berjalan dengan normal.

---

## 3. Langkah 2: Menempatkan Source Code Project
Pastikan seluruh folder project PresenceSync ditaruh di dalam direktori `www` Laragon dengan nama folder **`siapsman1ciparay`** (atau nama folder project Anda):

```
C:\laragon\www\siapsman1ciparay
```

Struktur folder yang benar di dalam server:
```
C:\laragon\www\siapsman1ciparay\
  ├── app/
  ├── bootstrap/
  ├── config/
  ├── database/
  ├── logo/
  │    └── Siap_Logo.ico
  ├── public/                  <-- Document Root
  │    ├── index.php
  │    ├── demo1/
  │    └── ...
  ├── resources/
  ├── routes/
  ├── storage/
  ├── .env
  ├── SIAP-PresenceSync.exe     <-- Launcher Langsung
  ├── create_shortcut.ps1
  └── composer.json
```

---

## 4. Langkah 3: Konfigurasi PHP Version & Ekstensi
Sistem SIAP membutuhkan **PHP 8.4 atau lebih baru** (disarankan PHP 8.4 atau 8.5).

1. Di jendela utama **Laragon**, klik **Menu** (atau klik kanan di sembarang tempat).
2. Arahkan ke **PHP** ➡️ **Version** ➡️ Pilih **`php-8.4.x`** atau **`php-8.5.x`**.
3. Pastikan ekstensi berikut telah aktif di **Menu** ➡️ **PHP** ➡️ **Extensions**:
   - [x] `pdo_mysql`
   - [x] `mbstring`
   - [x] `openssl`
   - [x] `curl`
   - [x] `gd`
   - [x] `fileinfo`
   - [x] `zip`
   - [x] `exif`

---

## 5. Langkah 4: Konfigurasi Virtual Host & Local SSL (HTTPS)

### A. Mengaktifkan Fitur SSL di Laragon
1. Klik **Menu** ➡️ **Apache** ➡️ **SSL** ➡️ Centang **`Enabled`** (akan muncul centang ✔️).
2. Klik **Menu** ➡️ **Apache** ➡️ **SSL** ➡️ Klik **`Add laragon.crt to Trust Store`** (Pilih **Yes** saat popup UAC Windows muncul).
3. Klik tombol **Reload** pada Laragon.
4. Perhatikan pada status Apache di Laragon, port akan berubah menjadi **`80/443`** dengan ikon gembok hijau 🔒.

### B. Memastikan Hostname Aktif
Laragon Magic DNS secara otomatis mendaftarkan nama domain ke file `C:\Windows\System32\drivers\etc\hosts`:
```hosts
127.0.0.1      siapsman1ciparay.test    #laragon magic!
```
*Jika nama folder project adalah `siapsman1ciparay`, maka domainnya otomatis menjadi `https://siapsman1ciparay.test`*.

---

## 6. Langkah 5: Konfigurasi File Environment (.env)

Buat / edit file `.env` di folder project (`C:\laragon\www\siapsman1ciparay\.env`):

```env
APP_NAME="SIAP - PresenceSync"
APP_ENV=production
APP_KEY=base64:GENERATE_NANTI_DI_LANGKAH_6
APP_DEBUG=false
APP_URL=https://siapsman1ciparay.test

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siapsman1ciparay
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

---

## 7. Langkah 6: Eksekusi Database & Storage Link

Buka **Terminal Laragon** (klik tombol **Terminal** di jendela Laragon), lalu jalankan serangkaian perintah berikut:

```bash
# 1. Masuk ke direktori project jika belum
cd C:\laragon\www\siapsman1ciparay

# 2. Buat database MySQL di server
mysql -u root -e "CREATE DATABASE IF NOT EXISTS siapsman1ciparay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Generate App Key (jika belum ada di .env)
php artisan key:generate --force

# 4. Hubungkan Storage Link (untuk foto profil, bukti pengaduan, dsb)
php artisan storage:link

# 5. Jalankan Migrasi Database
php artisan migrate --force

# 6. Opsi Seeder Data:
# Opsi A: Jika ingin Database Bersih Standar untuk Sekolah Asli:
php artisan db:seed --force

# Opsi B: Jika ingin Database berisi Akun Dummy Lengkap (Guru Wali, Siswa, Kesiswaan, Presensi Juni-Juli & Hari Ini):
php artisan db:seed-dummy --force

# 7. Bersihkan & Optimasi Cache Laravel
php artisan optimize:clear
php artisan optimize
```

---

## 8. Langkah 7: Konfigurasi Auto-Start Windows
Agar admin sekolah **tidak perlu menyalakan terminal atau menekan tombol apapun** setiap kali komputer server dihidupkan:

1. Di jendela utama **Laragon**, klik tombol **Gerigi (Preferences)** di kanan atas (atau **Menu ➡️ Preferences**).
2. Pada tab **General**, centang 2 opsi:
   - [x] **`Run Laragon when Windows starts`**
   - [x] **`Start All automatically`**
3. Tutup jendela Preferences.

> 💡 **Hasilnya**: Setiap komputer server dinyalakan atau di-restart, Apache, MySQL, dan SSL langsung aktif di background.

---

## 9. Langkah 8: Shortcut Desktop & Launcher SIAP-PresenceSync.exe

### A. Melalui File Launcher SIAP-PresenceSync.exe
Di dalam folder project terdapat file eksekusi:
```
C:\laragon\www\siapsman1ciparay\SIAP-PresenceSync.exe
```
Ketika di-double-klik, file ini akan langsung meluncurkan browser default Windows dan membuka alamat `https://siapsman1ciparay.test/`.

### B. Membuat Shortcut di Desktop & Taskbar
Jalankan script PowerShell di terminal server untuk memasang icon shortcut SIAP di Desktop:
```powershell
powershell -ExecutionPolicy Bypass -File "C:\laragon\www\siapsman1ciparay\create_shortcut.ps1"
```
Icon shortcut **"SIAP PresenceSync"** dengan logo sekolah resmi akan langsung muncul di Desktop komputer server.

---

## 10. Langkah 9: Akses dari Komputer Lain di Jaringan LAN / WiFi Sekolah

Agar laptop guru, TU, atau admin lain di sekolah bisa membuka SIAP melalui jaringan lokal:

### 1. Cek IP Address Komputer Server:
Buka Command Prompt (CMD) di server, ketik:
```cmd
ipconfig
```
*Catat IPv4 Address server, contoh:* `192.168.1.100`.

### 2. Cara Akses dari Laptop/Komputer Klien:
* **Metode A (Rekomendasi Tanpa Edit Hosts - via IP)**:
  Buka browser di laptop klien dan akses:
  ```
  http://192.168.1.100/siapsman1ciparay/public
  ```
* **Metode B (Menggunakan Domain yang Sama di Klien)**:
  Tambahkan baris berikut pada file `C:\Windows\System32\drivers\etc\hosts` di laptop klien:
  ```hosts
  192.168.1.100      siapsman1ciparay.test
  ```
  Lalu laptop klien bisa membuka langsung `https://siapsman1ciparay.test`.

### 3. Buka Port Firewall Windows di Komputer Server:
Jalankan perintah ini di PowerShell (Run as Administrator) di Komputer Server:
```powershell
New-NetFirewallRule -DisplayName "Laragon HTTP & HTTPS" -Direction Inbound -LocalPort 80,443 -Protocol TCP -Action Allow
```

---

## 11. Daftar Kredensial Akun Standar

| Role | Identifier / Email / NIP / NIS | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@demo.com` | `demo` | Hak akses penuh admin sistem |
| **Kesiswaan** | `kesiswaan@sman1ciparay.com` | `kesiswaan123` | Dashboard tren kehadiran & rekap |
| **Guru (Wali)** | `198501012010011001` | `guru123` | Drs. Budi Santoso (Wali X IPA 1) |
| **Siswa (Sekretaris)** | `2024100102` | `siswa123` | Ahmad Fauzi (Sekretaris Kelas) |
| **Siswa** | `2024100101` | `siswa123` | Rina Wulandari |
| **Siswa** | `2024100103` s/d `2024100105` | `siswa123` | Siswa kelas X IPA 1 lainnya |

---

## 12. Script Cepat Setup Otomatis (All-in-One)

Untuk menjalankan seluruh setup di atas dalam 1 kali klik, buka **Terminal Laragon** (atau PowerShell Administrator) lalu copy-paste perintah berikut:

```powershell
cd C:\laragon\www\siapsman1ciparay

# Setup Database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS siapsman1ciparay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Artisan setup
php artisan key:generate --force
php artisan storage:link
php artisan migrate --force
php artisan db:seed-dummy --force
php artisan optimize:clear
php artisan optimize

# Pasang Shortcut Desktop
powershell -ExecutionPolicy Bypass -File "create_shortcut.ps1"

Write-Host "`n=== SETUP SELESAI! APLIKASI SIAP DIGUNAKAN DI https://siapsman1ciparay.test ===" -ForegroundColor Green
```

---

## 13. Troubleshooting & Solusi

### 1. Browser Menampilkan Peringatan SSL ("Your connection is not private")
* **Solusi**: Di Laragon, klik **Menu ➡️ Apache ➡️ SSL ➡️ Add laragon.crt to Trust Store**. Tutup semua jendela browser dan buka kembali.

### 2. Website Tidak Bisa Dibuka (`404 Not Found` atau `Connection Refused`)
* **Solusi**:
  1. Pastikan tombol **Start All** di Laragon sudah menyala.
  2. Periksa apakah nama folder di `C:\laragon\www\` adalah `siapsman1ciparay`.
  3. Cek file `C:\Windows\System32\drivers\etc\hosts`, pastikan baris `127.0.0.1 siapsman1ciparay.test` sudah ada.

### 3. Foto Profil / Bukti Pengaduan Tidak Muncul
* **Solusi**: Jalankan perintah `php artisan storage:link` di terminal project.

### 4. Upload Foto Gagal Melebihi Ukuran
* **Solusi**: Batas maksimal foto avatar profil adalah **2MB**, sedangkan foto bukti pengaduan maksimal **10MB**. Pastikan file berformat `.jpg`, `.jpeg`, `.png`, atau `.webp`.

---
*Dokumen ini dibuat otomatis untuk mempermudah konfigurasi dan instalasi server lokal PresenceSync.*
