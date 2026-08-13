# 📖 Panduan Deploy PresenceSync — Server Sekolah

**Developer:** Eko Muhammad Rizki  
**Repo:** https://github.com/EkoMuhammadRizki/PresenceSync  
**Target:** Windows 10 · Laragon · Jaringan LAN · ADMS Fingerprint

---

## Daftar Isi

1. [Gambaran Arsitektur](#1-gambaran-arsitektur)
2. [Persiapan Server (Sekali Saja)](#2-persiapan-server-sekali-saja)
3. [Deploy Sistem Pertama Kali](#3-deploy-sistem-pertama-kali)
4. [Konfigurasi IP dan ADMS](#4-konfigurasi-ip-dan-adms)
5. [Update Sistem](#5-update-sistem)
6. [Maintenance dan Troubleshooting](#6-maintenance-dan-troubleshooting)
7. [Backup dan Restore Database](#7-backup-dan-restore-database)
8. [FAQ](#8-faq)

---

## 1. Gambaran Arsitektur

```
┌─────────────────────────────────────────────────────┐
│              JARINGAN LAN SEKOLAH                   │
│                                                     │
│  ┌──────────────┐     HTTP/LAN    ┌───────────────┐ │
│  │   Komputer   │ ◄────────────── │  Server       │ │
│  │   Guru/Staff │                 │  Windows 10   │ │
│  └──────────────┘                 │               │ │
│                                   │  Laragon      │ │
│  ┌──────────────┐     HTTP/LAN    │  ├─ Apache    │ │
│  │   Komputer   │ ◄────────────── │  ├─ MySQL     │ │
│  │   Siswa      │                 │  └─ PHP 8.x   │ │
│  └──────────────┘                 │               │ │
│                                   │  PresenceSync │ │
│  ┌──────────────┐   ADMS/TCP      │  (Laravel)    │ │
│  │   Mesin      │ ──────────────► │               │ │
│  │ Fingerprint  │                 └───────────────┘ │
│  └──────────────┘                                   │
└─────────────────────────────────────────────────────┘

URL Akses: http://192.168.x.x/presencesync/public
```

---

## 2. Persiapan Server (Sekali Saja)

### A. Install Git

1. Download: https://git-scm.com/download/win
2. Install → pilih **"Git from the command line and also from 3rd-party software"**
3. Centang **"Add a PATH entry"**
4. Verifikasi: buka Command Prompt → `git --version`

### B. Install Laragon

Laragon sudah bundle **PHP + MySQL + Apache** sekaligus, tidak perlu install terpisah.

1. Download **Laragon Full** (~170MB): https://laragon.org/download/
2. Install → **wajib centang:**
   - ✅ **Run Laragon on Windows startup** ← supaya otomatis start saat server nyala
   - ✅ **Auto virtual host**
3. Setelah install → buka Laragon → klik **"Start All"**
4. Pastikan **Apache** dan **MySQL** berwarna **hijau**
5. Verifikasi: buka browser → `http://localhost` → muncul halaman Laragon ✅

> 💡 **Laragon sudah include MySQL!** Tidak perlu install XAMPP atau MySQL terpisah.

### C. Install Composer

1. Download: https://getcomposer.org/download/ → pilih **Composer-Setup.exe**
2. Install → ikuti wizard
3. Verifikasi: buka Command Prompt baru → `composer --version`

### D. Buat Database di phpMyAdmin

1. Buka browser → http://localhost/phpmyadmin
2. Login: username `root`, password kosong (default Laragon)
3. Klik **"New"** di sidebar kiri
4. Nama database: `presencesync`
5. Collation: `utf8mb4_unicode_ci`
6. Klik **"Create"** ✅

---

## 3. Deploy Sistem Pertama Kali

### Langkah A: Clone dari GitHub

Buka **Command Prompt** → arahkan ke folder Laragon:

```cmd
cd C:\laragon\www
git clone https://github.com/EkoMuhammadRizki/PresenceSync.git presencesync
```

Hasil: folder `C:\laragon\www\presencesync\` berisi semua file sistem.

### Langkah B: Cari IP Server

Buka Command Prompt → ketik:
```cmd
ipconfig
```

Cari **"IPv4 Address"** di bagian **Ethernet adapter** (bukan WiFi):
```
Ethernet adapter Ethernet:
   IPv4 Address. . . . . : 192.168.1.100   ← ini IP-nya
```

Catat IP ini, akan digunakan untuk konfigurasi.

### Langkah C: Edit Konfigurasi

Buka file `C:\laragon\www\presencesync\.env.production` dengan Notepad:

```
APP_URL=http://192.168.1.100/presencesync/public
```

> Ganti `192.168.1.100` dengan IP server yang tadi kamu catat.

### Langkah D: Jalankan Script Deploy

1. Buka folder `C:\laragon\www\presencesync\`
2. Klik kanan pada **`deploy.bat`** → **"Run as administrator"**
3. Jika muncul peringatan Windows → klik **"More info"** → **"Run anyway"**
4. Ikuti instruksi yang muncul di layar

Script akan otomatis:
- Install semua package PHP (Composer)
- Setup file `.env`
- Generate kunci aplikasi
- Buat semua tabel database
- Cache konfigurasi untuk performa optimal

### Langkah E: Verifikasi

Buka browser → akses:
```
http://192.168.1.100/presencesync/public
```

Jika muncul halaman login PresenceSync → ✅ **Berhasil!**

---

## 4. Konfigurasi IP dan ADMS

### IP Server Statis (Sangat Disarankan)

Agar IP server tidak berubah-ubah saat restart:

1. Buka **Control Panel** → **Network and Sharing Center**
2. Klik **"Change adapter settings"**
3. Klik kanan **Ethernet** → **Properties**
4. Pilih **"Internet Protocol Version 4 (TCP/IPv4)"** → **Properties**
5. Pilih **"Use the following IP address"**:
   ```
   IP address:      192.168.1.100   (sesuaikan dengan jaringan sekolah)
   Subnet mask:     255.255.255.0
   Default gateway: 192.168.1.1     (IP router sekolah)
   ```
6. Klik OK → OK

> Tanyakan kepada guru TI/admin jaringan sekolah untuk IP yang tersedia.

### Konfigurasi Mesin Fingerprint (ADMS)

Di menu pengaturan mesin fingerprint, isi:
```
Server Address : 192.168.1.100
Server Port    : 80
URL/Path       : /presencesync/public/api/absensi/sync
```

> ⚠️ Pastikan URL ADMS sudah sesuai dengan endpoint yang ada di sistem.
> Tanyakan ke developer (Eko) untuk endpoint yang benar jika belum yakin.

---

## 5. Update Sistem

Setiap kali developer push update baru ke GitHub, jalankan di server:

1. Klik kanan **`update.bat`** → **"Run as administrator"**
2. Tunggu selesai (biasanya 1–3 menit)

Script update akan otomatis:
- Aktifkan halaman maintenance (pengguna lihat pesan "sedang update")
- `git pull` dari GitHub untuk ambil kode terbaru
- Update package jika ada yang baru
- Jalankan migrasi database baru jika ada
- Rebuild cache
- Matikan halaman maintenance

> ✅ **Server harus terhubung internet** saat menjalankan update.bat

---

## 6. Maintenance dan Troubleshooting

### Gunakan Script Maintenance

Klik kanan **`maintenance.bat`** → **"Run as administrator"**

```
╔══════════════════════════════════════════════════════╗
║     PRESENCESYNC - MAINTENANCE & TROUBLESHOOTING    ║
╚══════════════════════════════════════════════════════╝

[1] Clear semua cache         → solusi pertama untuk error apapun
[2] Lihat log error terbaru   → untuk debug masalah
[3] Cek status database       → pastikan MySQL Laragon jalan
[4] Rebuild semua cache       → setelah ubah konfigurasi
[5] Reset storage link        → jika foto/file tidak muncul
[6] Aktifkan mode maintenance → saat mau perbaikan
[7] Matikan mode maintenance  → setelah perbaikan selesai
[8] Cek info sistem           → versi PHP, Laravel, konfigurasi
[9] Hapus log lama            → bersihkan storage/logs
```

### Tabel Solusi Error Cepat

| Gejala | Penyebab | Solusi |
|---|---|---|
| Halaman putih / Error 500 | Cache rusak | Jalankan maintenance menu **[1]** |
| "Whoops, looks like something went wrong" | Error PHP | Menu **[2]** lihat log, kirim ke developer |
| Tidak bisa konek database | MySQL Laragon mati | Buka Laragon → **Start All** |
| Tampilan CSS/JS rusak | File public rusak | Copy ulang folder `public/` dari developer |
| Foto/upload tidak tampil | Storage link putus | Menu maintenance **[5]** |
| Semua halaman 503 | Mode maintenance aktif | Menu maintenance **[7]** |
| Fingerprint tidak masuk | ADMS URL salah | Cek konfigurasi IP di mesin fingerprint |
| Sistem lambat | Cache tidak aktif | Menu maintenance **[4]** |

### Cek Log Error Manual

File log disimpan harian di:
```
C:\laragon\www\presencesync\storage\logs\laravel-YYYY-MM-DD.log
```

Buka dengan Notepad → `Ctrl+F` → cari kata `ERROR` atau `CRITICAL`

---

## 7. Backup dan Restore Database

### Backup (Lakukan Rutin — Minimal Mingguan)

**Via phpMyAdmin:**
1. Buka http://localhost/phpmyadmin
2. Klik database `presencesync` di sidebar
3. Klik tab **"Export"**
4. Format: **SQL** → klik **"Go"**
5. Simpan file `.sql` di lokasi aman (flashdisk / Google Drive)

**Nama file disarankan:** `presencesync_backup_TANGGAL.sql`

### Restore dari Backup

1. Buka phpMyAdmin → klik database `presencesync`
2. Klik tab **"Import"**
3. Pilih file `.sql` backup → klik **"Go"**

---

## 8. FAQ

**Q: Apakah harus tetap buka Command Prompt agar sistem jalan?**  
A: **Tidak.** Laragon yang menjalankan sistem otomatis di background. Tidak perlu terminal terbuka.

**Q: Apakah server harus selalu terhubung internet?**  
A: **Tidak**, sistem bisa berjalan offline di LAN sekolah. Internet hanya dibutuhkan saat pertama deploy dan saat update.

**Q: Bagaimana jika IP server berubah?**  
A: Edit file `.env` di server (ubah `APP_URL`), lalu jalankan `maintenance.bat` → menu [4] untuk rebuild cache. Update juga konfigurasi ADMS di mesin fingerprint.

**Q: Bagaimana jika Laragon tidak auto-start setelah restart Windows?**  
A: Buka Laragon → klik kanan ikon di system tray → **Preferences** → centang **"Run Laragon on Windows startup"**.

**Q: Bolehkah server digunakan untuk hal lain (browsing, kerja)?**  
A: Boleh, tapi sebaiknya server **dedicated** hanya untuk sistem PresenceSync agar stabil dan tidak kena virus.

**Q: Bagaimana cara tambah user admin baru?**  
A: Login ke sistem → menu Users → tambah user baru dengan role Admin.

---

*Untuk bantuan teknis, hubungi developer: **Eko Muhammad Rizki***
