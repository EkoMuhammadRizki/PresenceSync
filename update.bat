@echo off
setlocal EnableDelayedExpansion
chcp 65001 >nul

:: ============================================================
::   PresenceSync - UPDATE SISTEM
::   Mengupdate sistem dari GitHub
::   Cukup double-klik, tidak perlu terminal!
:: ============================================================

title PRESENCESYNC - Update Sistem

echo.
echo  ╔═══════════════════════════════════════════════════════╗
echo  ║   PRESENCESYNC - UPDATE SISTEM                       ║
echo  ║   Sistem akan diupdate otomatis                      ║
echo  ╚═══════════════════════════════════════════════════════╝
echo.

:: --- Deteksi path PHP Laragon ---
set "LARAGON_PHP="
for /d %%d in ("C:\laragon\bin\php\php-8*") do set "LARAGON_PHP=%%d\php.exe"

:: Fallback: coba php dari PATH global
if not defined LARAGON_PHP (
    where php >nul 2>&1
    if %errorlevel% equ 0 set "LARAGON_PHP=php"
)

if not defined LARAGON_PHP (
    echo  [ERROR] PHP tidak ditemukan!
    echo  Pastikan Laragon terinstall dan PHP-nya ada di C:\laragon\bin\php\
    pause
    exit /b 1
)

:: --- Cek ada di folder yang benar ---
if not exist "artisan" (
    echo  [ERROR] Jalankan dari dalam folder PresenceSync!
    echo  Folder ini: %~dp0
    pause
    exit /b 1
)

if not exist ".git" (
    echo  [ERROR] Folder ini belum terhubung ke Git!
    echo  Hubungi developer untuk setup awal.
    pause
    exit /b 1
)

:: --- Tampilkan versi saat ini ---
echo  Versi sekarang:
git log --oneline -1
echo.
echo  Tanggal update: %date% %time%
echo.

echo  ============================================================
echo  Proses update akan dimulai. Sistem akan offline sebentar.
echo  ============================================================
echo.
set /p "LANJUT=  Lanjutkan update? (Y/N) [Y]: "
if /i "!LANJUT!"=="N" (
    echo  Update dibatalkan.
    pause
    exit /b 0
)

:: ================================================================
echo.
echo  [1/6] Aktifkan Mode Maintenance...
:: ================================================================
"%LARAGON_PHP%" artisan down --message="Sistem sedang diupdate, mohon tunggu sebentar..." --retry=60
echo  [OK] Mode maintenance AKTIF. Sistem sementara offline.

:: ================================================================
echo.
echo  [2/6] Mengambil update terbaru dari GitHub...
:: ================================================================

git stash >nul 2>&1
git fetch origin
git reset --hard origin/main 2>nul || git reset --hard origin/master
if %errorlevel% neq 0 (
    echo  [ERROR] Gagal mengambil update. Kemungkinan tidak ada koneksi internet.
    echo  [INFO] Mengembalikan sistem online...
    git stash pop >nul 2>&1
    "%LARAGON_PHP%" artisan up
    pause
    exit /b 1
)
git stash pop >nul 2>&1
echo  [OK] Kode berhasil diupdate.
git log --oneline -1

:: ================================================================
echo.
echo  [3/6] Update Dependensi PHP (Composer)...
:: ================================================================

:: Cek composer tersedia
set "LARAGON_COMPOSER=C:\laragon\bin\composer\composer.phar"
set "PHP_CMD=%LARAGON_PHP%"

if exist "%LARAGON_COMPOSER%" (
    "%LARAGON_PHP%" "%LARAGON_COMPOSER%" install --no-dev --optimize-autoloader --no-interaction 2>&1
) else (
    where composer >nul 2>&1
    if %errorlevel% equ 0 (
        composer install --no-dev --optimize-autoloader --no-interaction 2>&1
    ) else (
        echo  [WARN] Composer tidak ditemukan, lewati. (Biasanya aman jika tidak ada dependensi baru)
    )
)
echo  [OK] Dependensi PHP selesai.

:: ================================================================
echo.
echo  [4/6] Jalankan Migrasi Database...
:: ================================================================

"%LARAGON_PHP%" artisan migrate --force
if %errorlevel% neq 0 (
    echo  [ERROR] Migrasi gagal! Pastikan Laragon (MySQL) sedang berjalan.
    "%LARAGON_PHP%" artisan up
    pause
    exit /b 1
)
echo  [OK] Migrasi database selesai.

:: ================================================================
echo.
echo  [5/6] Clear dan Rebuild Cache...
:: ================================================================

"%LARAGON_PHP%" artisan cache:clear >nul 2>&1
"%LARAGON_PHP%" artisan config:clear >nul 2>&1
"%LARAGON_PHP%" artisan route:clear >nul 2>&1
"%LARAGON_PHP%" artisan view:clear >nul 2>&1
echo  [OK] Cache lama dihapus.

"%LARAGON_PHP%" artisan config:cache >nul 2>&1
"%LARAGON_PHP%" artisan route:cache >nul 2>&1
"%LARAGON_PHP%" artisan view:cache >nul 2>&1
echo  [OK] Cache baru dibuat.

:: ================================================================
echo.
echo  [6/6] Sistem Kembali Online...
:: ================================================================

"%LARAGON_PHP%" artisan up
echo  [OK] Mode maintenance DIMATIKAN. Sistem kembali online!

:: --- Selesai ---
echo.
echo  ╔═══════════════════════════════════════════════════════╗
echo  ║   [SUKSES] Update selesai!                           ║
echo  ╚═══════════════════════════════════════════════════════╝
echo.
echo  Versi terbaru:
git log --oneline -1
echo.
echo  Akses sistem: https://presencesync.lokal
echo.
echo  Tekan Enter untuk membuka browser...
pause

start https://presencesync.lokal
endlocal
