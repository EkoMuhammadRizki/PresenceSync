@echo off
setlocal EnableDelayedExpansion
chcp 65001 >nul

:: ============================================================
::   PresenceSync - UPDATE SCRIPT
::   Mengupdate sistem via git pull dari GitHub
::   Jalankan setiap kali ada update dari developer
:: ============================================================

title PRESENCESYNC - Update Sistem

echo.
echo  ╔═══════════════════════════════════════════════════════╗
echo  ║   PRESENCESYNC - UPDATE SISTEM VIA GIT               ║
echo  ║   Repo: github.com/EkoMuhammadRizki/PresenceSync     ║
echo  ╚═══════════════════════════════════════════════════════╝
echo.

if not exist "artisan" (
    echo  [ERROR] Jalankan dari dalam folder PresenceSync!
    pause
    exit /b 1
)

if not exist ".git" (
    echo  [ERROR] Folder ini belum diinisialisasi sebagai git repo!
    echo  Jalankan deploy.bat terlebih dahulu.
    pause
    exit /b 1
)

:: --- Tampilkan versi saat ini ---
echo  Versi sekarang (commit terakhir):
git log --oneline -1
echo.

for /f "tokens=1-3 delims=/ " %%a in ("%date%") do set tanggal=%%a-%%b-%%c
for /f "tokens=1-2 delims=: " %%a in ("%time%") do set waktu=%%a.%%b
echo  Update dijalankan: %tanggal% %waktu%
echo.

echo  ================================================================
echo  LANGKAH 1: Aktifkan Mode Maintenance
echo  ================================================================
php artisan down --message="Sistem sedang diupdate, mohon tunggu 5 menit..." --retry=300
echo  [OK] Mode maintenance AKTIF.

echo.
echo  ================================================================
echo  LANGKAH 2: Ambil Update dari GitHub (git pull)
echo  ================================================================
echo.

:: Simpan perubahan lokal jika ada (file .env tidak akan tersentuh)
git stash

:: Pull update terbaru
git fetch origin
git reset --hard origin/main 2>nul || git reset --hard origin/master
if %errorlevel% neq 0 (
    echo  [ERROR] Git pull gagal! Kemungkinan tidak ada koneksi internet.
    echo  [INFO] Mengembalikan sistem online...
    git stash pop >nul 2>&1
    php artisan up
    pause
    exit /b 1
)

:: Kembalikan stash (termasuk .env yang mungkin dimodifikasi lokal)
git stash pop >nul 2>&1

echo.
git log --oneline -1
echo  [OK] Kode berhasil diupdate dari GitHub.

echo.
echo  ================================================================
echo  LANGKAH 3: Update Dependensi PHP
echo  ================================================================
echo.

composer install --no-dev --optimize-autoloader --no-interaction
if %errorlevel% neq 0 (
    echo  [ERROR] Composer install gagal!
    php artisan up
    pause
    exit /b 1
)
echo  [OK] Composer selesai.

echo.
echo  ================================================================
echo  LANGKAH 4: Jalankan Migrasi Database
echo  ================================================================
echo.

php artisan migrate --force
if %errorlevel% neq 0 (
    echo  [ERROR] Migrasi gagal! Cek koneksi database (Laragon harus jalan).
    php artisan up
    pause
    exit /b 1
)
echo  [OK] Migrasi selesai.

echo.
echo  ================================================================
echo  LANGKAH 5: Clear dan Rebuild Cache
echo  ================================================================
echo.

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo  [OK] Cache lama dihapus.

php artisan config:cache
php artisan route:cache
php artisan view:cache
echo  [OK] Cache baru dibuat.

echo.
echo  ================================================================
echo  LANGKAH 6: Sistem Kembali Online
echo  ================================================================
echo.

php artisan up
echo  [OK] Mode maintenance DIMATIKAN. Sistem kembali online!

echo.
echo  ╔═══════════════════════════════════════════╗
echo  ║   [SUKSES] Update selesai!                ║
echo  ╚═══════════════════════════════════════════╝
echo.
echo  Versi terbaru:
git log --oneline -1
echo.
pause
