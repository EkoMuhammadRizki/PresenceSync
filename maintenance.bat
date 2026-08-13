@echo off
setlocal EnableDelayedExpansion
chcp 65001 >nul

:: ============================================================
::   PresenceSync - MAINTENANCE & TROUBLESHOOTING SCRIPT
::   Jalankan ini ketika ada error atau masalah di sistem
:: ============================================================

title PRESENCESYNC - Maintenance & Troubleshooting

:MENU
cls
echo.
echo  ╔══════════════════════════════════════════════════════╗
echo  ║     PRESENCESYNC - MAINTENANCE ^& TROUBLESHOOTING   ║
echo  ╚══════════════════════════════════════════════════════╝
echo.
echo  Pilih aksi yang ingin dilakukan:
echo.
echo  [1] Clear semua cache (solusi paling umum untuk error)
echo  [2] Lihat log error terbaru
echo  [3] Cek status database
echo  [4] Rebuild semua cache (config + route + view)
echo  [5] Reset storage link
echo  [6] Aktifkan mode maintenance (DOWN)
echo  [7] Matikan mode maintenance (UP)
echo  [8] Cek info PHP ^& Laravel
echo  [9] Hapus log lama (bersihkan storage/logs)
echo  [0] Keluar
echo.
set /p pilihan="  Masukkan nomor pilihan: "

if "%pilihan%"=="1" goto CLEAR_CACHE
if "%pilihan%"=="2" goto LIHAT_LOG
if "%pilihan%"=="3" goto CEK_DB
if "%pilihan%"=="4" goto REBUILD_CACHE
if "%pilihan%"=="5" goto RESET_STORAGE
if "%pilihan%"=="6" goto MAINTENANCE_ON
if "%pilihan%"=="7" goto MAINTENANCE_OFF
if "%pilihan%"=="8" goto INFO_SISTEM
if "%pilihan%"=="9" goto HAPUS_LOG
if "%pilihan%"=="0" exit /b 0
echo  [!] Pilihan tidak valid.
timeout /t 2 >nul
goto MENU

:: ============================================================
:CLEAR_CACHE
:: ============================================================
cls
echo.
echo  [AKSI] Menghapus semua cache...
echo  -------------------------------------------
php artisan cache:clear
echo  [OK] Cache aplikasi dihapus.
php artisan config:clear
echo  [OK] Cache config dihapus.
php artisan route:clear
echo  [OK] Cache route dihapus.
php artisan view:clear
echo  [OK] Cache view dihapus.
echo.
echo  [SELESAI] Semua cache berhasil dihapus!
echo  Coba akses sistem lagi di browser.
echo.
pause
goto MENU

:: ============================================================
:LIHAT_LOG
:: ============================================================
cls
echo.
echo  [AKSI] Menampilkan 50 baris log error terbaru...
echo  -------------------------------------------
echo.
if exist "storage\logs\laravel.log" (
    echo  === ISI LOG (50 baris terakhir) ===
    echo.
    powershell -command "Get-Content 'storage\logs\laravel.log' -Tail 50"
    echo.
    echo  === LOKASI FILE LOG ===
    echo  %cd%\storage\logs\laravel.log
    echo.
    echo  Tips: Buka file log tersebut dengan Notepad untuk melihat lengkap.
) else (
    echo  [INFO] File log belum ada (storage\logs\laravel.log tidak ditemukan).
    echo  Ini normal jika sistem baru diinstall dan belum ada error.
)
echo.
pause
goto MENU

:: ============================================================
:CEK_DB
:: ============================================================
cls
echo.
echo  [AKSI] Mengecek koneksi database...
echo  -------------------------------------------
php artisan db:show 2>nul
if %errorlevel% neq 0 (
    echo.
    echo  [ERROR] Tidak bisa konek ke database!
    echo.
    echo  Kemungkinan penyebab:
    echo    1. MySQL/Laragon belum jalan - Buka Laragon, klik Start All
    echo    2. Database belum dibuat - Buka phpMyAdmin, buat database 'presencesync'
    echo    3. Password MySQL salah - Cek file .env (DB_PASSWORD)
    echo.
    echo  Konfigurasi database saat ini (dari .env):
    for /f "tokens=1,2 delims==" %%a in (.env) do (
        if "%%a"=="DB_HOST" echo    DB_HOST=%%b
        if "%%a"=="DB_DATABASE" echo    DB_DATABASE=%%b
        if "%%a"=="DB_USERNAME" echo    DB_USERNAME=%%b
    )
) else (
    echo.
    echo  [OK] Database berhasil terkoneksi!
    echo.
    echo  Status migrasi:
    php artisan migrate:status
)
echo.
pause
goto MENU

:: ============================================================
:REBUILD_CACHE
:: ============================================================
cls
echo.
echo  [AKSI] Rebuild semua cache...
echo  -------------------------------------------
php artisan config:cache
echo  [OK] Cache config dibuat.
php artisan route:cache
echo  [OK] Cache route dibuat.
php artisan view:cache
echo  [OK] Cache view dibuat.
echo.
echo  [SELESAI] Semua cache berhasil direbuild!
echo.
pause
goto MENU

:: ============================================================
:RESET_STORAGE
:: ============================================================
cls
echo.
echo  [AKSI] Reset storage link...
echo  -------------------------------------------
if exist "public\storage" (
    rmdir "public\storage" /s /q >nul 2>&1
    echo  [OK] Link lama dihapus.
)
php artisan storage:link
echo  [OK] Storage link berhasil dibuat ulang.
echo.
pause
goto MENU

:: ============================================================
:MAINTENANCE_ON
:: ============================================================
cls
echo.
echo  [AKSI] Mengaktifkan mode maintenance...
echo  -------------------------------------------
set /p pesan="  Masukkan pesan untuk pengguna (Enter untuk default): "
if "%pesan%"=="" set pesan=Sistem sedang dalam perbaikan. Mohon tunggu sebentar.
php artisan down --message="%pesan%" --retry=60
echo.
echo  [OK] Mode maintenance AKTIF.
echo  Pengguna akan melihat halaman 503 sementara.
echo.
pause
goto MENU

:: ============================================================
:MAINTENANCE_OFF
:: ============================================================
cls
echo.
echo  [AKSI] Mematikan mode maintenance...
echo  -------------------------------------------
php artisan up
echo.
echo  [OK] Sistem kembali ONLINE!
echo.
pause
goto MENU

:: ============================================================
:INFO_SISTEM
:: ============================================================
cls
echo.
echo  [INFO] Informasi Sistem
echo  -------------------------------------------
echo.
echo  === PHP ===
php -v
echo.
echo  === Laravel ===
php artisan --version
echo.
echo  === Composer ===
composer --version 2>nul
echo.
echo  === Konfigurasi .env (tidak termasuk data sensitif) ===
for /f "tokens=1,2 delims==" %%a in (.env) do (
    if "%%a"=="APP_NAME" echo    APP_NAME=%%b
    if "%%a"=="APP_ENV" echo    APP_ENV=%%b
    if "%%a"=="APP_URL" echo    APP_URL=%%b
    if "%%a"=="DB_CONNECTION" echo    DB_CONNECTION=%%b
    if "%%a"=="DB_HOST" echo    DB_HOST=%%b
    if "%%a"=="DB_DATABASE" echo    DB_DATABASE=%%b
)
echo.
pause
goto MENU

:: ============================================================
:HAPUS_LOG
:: ============================================================
cls
echo.
echo  [AKSI] Menghapus file log lama...
echo  -------------------------------------------
echo.
echo  [!] PERHATIAN: File log akan dihapus permanen!
set /p konfirmasi="  Yakin ingin hapus log? (y/n): "
if /i "%konfirmasi%"=="y" (
    if exist "storage\logs\laravel.log" (
        del "storage\logs\laravel.log" >nul 2>&1
        echo  [OK] File log berhasil dihapus.
    ) else (
        echo  [INFO] File log tidak ada.
    )
) else (
    echo  [INFO] Dibatalkan.
)
echo.
pause
goto MENU
