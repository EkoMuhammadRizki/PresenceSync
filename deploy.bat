@echo off
setlocal EnableDelayedExpansion
chcp 65001 >nul

:: ============================================================
::   PresenceSync - DEPLOY SCRIPT (Install Pertama Kali)
::   Repo: https://github.com/EkoMuhammadRizki/PresenceSync.git
::   OS Target: Windows 10
::   Web Server: Laragon (Apache + MySQL)
:: ============================================================

title PRESENCESYNC - Deploy Installer

echo.
echo  ██████╗ ██████╗ ███████╗███████╗███████╗███╗   ██╗ ██████╗███████╗
echo  ██╔══██╗██╔══██╗██╔════╝██╔════╝██╔════╝████╗  ██║██╔════╝██╔════╝
echo  ██████╔╝██████╔╝█████╗  ███████╗█████╗  ██╔██╗ ██║██║     █████╗
echo  ██╔═══╝ ██╔══██╗██╔══╝  ╚════██║██╔══╝  ██║╚██╗██║██║     ██╔══╝
echo  ██║     ██║  ██║███████╗███████║███████╗██║ ╚████║╚██████╗███████╗
echo  ╚═╝     ╚═╝  ╚═╝╚══════╝╚══════╝╚══════╝╚═╝  ╚═══╝ ╚═════╝╚══════╝
echo.
echo  [DEPLOY] Instalasi Pertama Kali - Server Sekolah (Windows 10 + Laragon)
echo  ================================================================
echo.

:: --- Cek apakah dijalankan dari folder yang benar ---
if not exist "artisan" (
    echo  [ERROR] Script ini harus dijalankan dari dalam folder PresenceSync!
    echo  Contoh: C:\laragon\www\presencesync\deploy.bat
    pause
    exit /b 1
)

:: --- Cek PHP (dari Laragon) ---
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo  [ERROR] PHP tidak ditemukan di PATH!
    echo.
    echo  Solusi:
    echo    1. Pastikan Laragon sudah terinstall
    echo    2. Buka Laragon ^> klik "Start All"
    echo    3. Buka Laragon ^> Menu ^> PHP ^> Add PHP to PATH
    echo    4. Tutup jendela ini dan buka Command Prompt baru, lalu jalankan ulang
    pause
    exit /b 1
)

:: --- Cek Composer ---
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo  [ERROR] Composer tidak ditemukan!
    echo  Download di: https://getcomposer.org/download/
    echo  Install Composer-Setup.exe, lalu jalankan script ini lagi.
    pause
    exit /b 1
)

:: --- Cek Git ---
where git >nul 2>&1
if %errorlevel% neq 0 (
    echo  [ERROR] Git tidak ditemukan!
    echo  Download di: https://git-scm.com/download/win
    echo  Install Git, lalu jalankan script ini lagi.
    pause
    exit /b 1
)

echo  [OK] PHP    : 
php -v | findstr "PHP 8"
echo  [OK] Composer: 
composer --version 2>nul | findstr "Composer"
echo  [OK] Git     : 
git --version

echo.
echo  ================================================================
echo  LANGKAH 1: Pull/Update kode dari GitHub
echo  ================================================================
echo.

:: Cek apakah sudah ada repo git atau baru pertama kali
if exist ".git" (
    echo  [INFO] Repo sudah ada. Mengambil update terbaru dari GitHub...
    git fetch origin
    git reset --hard origin/main
    if %errorlevel% neq 0 (
        git reset --hard origin/master
    )
    echo  [OK] Kode berhasil diupdate dari GitHub.
) else (
    echo  [INFO] Ini instalasi pertama. Menginisialisasi git...
    git init
    git remote add origin https://github.com/EkoMuhammadRizki/PresenceSync.git
    git fetch origin
    git checkout -b main origin/main 2>nul || git checkout -b master origin/master
    echo  [OK] Kode berhasil diclone dari GitHub.
)

echo.
echo  ================================================================
echo  LANGKAH 2: Install Dependensi PHP (Composer)
echo  ================================================================
echo.

composer install --no-dev --optimize-autoloader --no-interaction
if %errorlevel% neq 0 (
    echo.
    echo  [ERROR] Composer install gagal!
    echo  Kemungkinan: tidak ada koneksi internet atau composer.json rusak.
    pause
    exit /b 1
)
echo  [OK] Composer install selesai.

echo.
echo  ================================================================
echo  LANGKAH 3: Setup file konfigurasi (.env)
echo  ================================================================
echo.

if exist ".env" (
    echo  [INFO] File .env sudah ada, tidak akan ditimpa.
) else (
    if exist ".env.production" (
        copy ".env.production" ".env" >nul
        echo  [OK] File .env dibuat dari .env.production
    ) else (
        copy ".env.example" ".env" >nul
        echo  [OK] File .env dibuat dari .env.example
    )
    echo.
    echo  [!] WAJIB: Edit file .env sebelum lanjut!
    echo      Buka file .env dengan Notepad dan isi:
    echo.
    echo      APP_URL=http://[IP-SERVER]/presencesync/public
    echo      DB_DATABASE=presencesync
    echo      DB_USERNAME=root
    echo      DB_PASSWORD=(kosong jika belum diset)
    echo.
    echo  Tekan Enter setelah selesai mengedit .env...
    pause
)

echo.
echo  ================================================================
echo  LANGKAH 4: Generate Application Key
echo  ================================================================
echo.

php artisan key:generate --force
echo  [OK] Application key berhasil dibuat.

echo.
echo  ================================================================
echo  LANGKAH 5: Setup Database
echo  ================================================================
echo.

echo  [INFO] Pastikan Laragon MySQL sudah jalan (hijau) sebelum lanjut!
echo  Tekan Enter untuk melanjutkan migrasi...
pause

echo  [INFO] Membuat tabel database...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo.
    echo  [ERROR] Migrasi database gagal! Kemungkinan penyebab:
    echo    1. MySQL Laragon belum jalan ^> buka Laragon ^> klik "Start All"
    echo    2. Database belum dibuat ^> buka http://localhost/phpmyadmin
    echo       ^> klik "New" ^> nama: presencesync ^> Create
    echo    3. Konfigurasi .env salah ^> cek DB_HOST, DB_DATABASE, DB_PASSWORD
    pause
    exit /b 1
)
echo  [OK] Migrasi database selesai.

echo  [INFO] Menjalankan seeder (data awal)...
php artisan db:seed --force
if %errorlevel% neq 0 (
    echo  [WARN] Seeder tidak ada atau dilewati. Lanjut...
) else (
    echo  [OK] Seeder selesai.
)

echo.
echo  ================================================================
echo  LANGKAH 6: Setup Storage Link
echo  ================================================================
echo.

php artisan storage:link
echo  [OK] Storage link berhasil dibuat.

echo.
echo  ================================================================
echo  LANGKAH 7: Optimasi untuk Production
echo  ================================================================
echo.

php artisan config:cache
echo  [OK] Config di-cache.

php artisan route:cache
echo  [OK] Route di-cache.

php artisan view:cache
echo  [OK] View di-cache.

echo.
echo  ================================================================
echo  LANGKAH 8: Set Permission Folder
echo  ================================================================
echo.

icacls "storage" /grant Everyone:F /T >nul 2>&1
icacls "bootstrap\cache" /grant Everyone:F /T >nul 2>&1
echo  [OK] Permission storage dan cache berhasil diset.

echo.
echo  ================================================================
echo  [SUKSES] PresenceSync berhasil diinstall!
echo  ================================================================
echo.
echo  Cara mengetahui IP server ini:
echo    Buka Command Prompt -^> ketik: ipconfig
echo    Lihat "IPv4 Address" (contoh: 192.168.1.100)
echo.
echo  Akses sistem dari browser:
echo    http://[IP-SERVER]/presencesync/public
echo.
echo  Untuk konfigurasi ADMS fingerprint, set URL ke:
echo    http://[IP-SERVER]/presencesync/public/[endpoint-fingerprint]
echo.
echo  Jika ada masalah: jalankan maintenance.bat
echo.
pause
