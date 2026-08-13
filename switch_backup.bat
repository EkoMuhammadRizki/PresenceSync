@echo off
setlocal EnableDelayedExpansion
chcp 65001 >nul

:: ============================================================
::   PresenceSync - SWITCH BACKUP SCRIPT
::   Script untuk beralih ke/dari server backup sekolah
:: ============================================================

title PRESENCESYNC - Switch Backup

if not exist "artisan" (
    echo  [ERROR] Jalankan dari dalam folder PresenceSync!
    pause
    exit /b 1
)

:MENU
cls
echo.
echo  ╔══════════════════════════════════════════════════════════╗
echo  ║        PRESENCESYNC - SWITCH BACKUP SERVER              ║
echo  ╠══════════════════════════════════════════════════════════╣
echo  ║  Sistem Utama   : Shared Hosting (domain online)        ║
echo  ║  Sistem Backup  : Server Sekolah (IP LAN)               ║
echo  ╚══════════════════════════════════════════════════════════╝
echo.
echo  [1] Aktifkan Server Backup (hosting error / tidak bisa akses)
echo  [2] Cek status sistem sekarang
echo  [3] Backup Data dan Kembali ke Hosting Utama
echo  [0] Keluar
echo.
set /p pilihan="  Masukkan nomor: "

if "%pilihan%"=="1" goto AKTIFKAN_BACKUP
if "%pilihan%"=="2" goto CEK_STATUS
if "%pilihan%"=="3" goto KEMBALI_KE_HOSTING
if "%pilihan%"=="0" exit /b 0
echo  [!] Pilihan tidak valid.
timeout /t 2 >nul
goto MENU

:: ============================================================
:AKTIFKAN_BACKUP
:: ============================================================
cls
echo.
echo  ╔══════════════════════════════════════════════╗
echo  ║   MENGAKTIFKAN SERVER BACKUP                 ║
echo  ╚══════════════════════════════════════════════╝
echo.

:: --- Baca konfigurasi .env ---
set "APP_URL_SAAT_INI="
for /f "tokens=1,2 delims==" %%a in (.env) do (
    if "%%a"=="APP_URL" set "APP_URL_SAAT_INI=%%b"
)

echo  Status saat ini:
echo    APP_URL = %APP_URL_SAAT_INI%
echo.

:: --- Cek apakah sudah pakai backup ---
echo %APP_URL_SAAT_INI% | findstr /i "192.168\|localhost\|127.0.0" >nul
if %errorlevel% equ 0 (
    echo  [INFO] Sistem sudah menggunakan server backup!
    echo  Tidak perlu diubah.
    echo.
    pause
    goto MENU
)

:: --- Dapatkan IP server ini ---
echo  [INFO] Mendeteksi IP server sekolah ini...
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4"') do (
    set "IP_RAW=%%a"
    set "IP_SERVER=!IP_RAW: =!"
    goto :IP_FOUND
)
:IP_FOUND

echo  IP Server yang terdeteksi: %IP_SERVER%
echo.
echo  Apakah IP ini sudah benar?
set /p konfirmasi_ip="  (y untuk ya, atau ketik IP manual): "

if /i not "%konfirmasi_ip%"=="y" (
    if not "%konfirmasi_ip%"=="" (
        set "IP_SERVER=%konfirmasi_ip%"
    )
)

echo.
echo  [INFO] Beralih ke server backup...
echo    URL Baru: http://%IP_SERVER%/presencesync/public
echo.

:: --- Simpan URL lama ke file backup ---
echo %APP_URL_SAAT_INI%> ".env_backup_url.txt"
echo  [OK] URL hosting utama disimpan ke .env_backup_url.txt

:: --- Update APP_URL di .env ---
powershell -command "(Get-Content '.env') -replace 'APP_URL=.*', 'APP_URL=http://%IP_SERVER%/presencesync/public' | Set-Content '.env'"
echo  [OK] APP_URL diupdate ke IP lokal.

:: --- Update APP_ENV ke production jika perlu ---
powershell -command "(Get-Content '.env') -replace 'APP_ENV=local', 'APP_ENV=production' | Set-Content '.env'"
powershell -command "(Get-Content '.env') -replace 'APP_DEBUG=true', 'APP_DEBUG=false' | Set-Content '.env'"

:: --- Rebuild cache ---
echo.
echo  [INFO] Rebuild cache konfigurasi...
php artisan config:clear >nul
php artisan config:cache >nul
php artisan route:clear >nul
php artisan route:cache >nul
echo  [OK] Cache berhasil direbuild.

:: --- Catat waktu mulai backup ---
for /f "tokens=1-3 delims=/ " %%a in ("%date%") do set TGL=%%a-%%b-%%c
echo [BACKUP AKTIF] %TGL% %time% - Beralih dari: %APP_URL_SAAT_INI%>> ".backup_log.txt"

echo.
echo  ╔══════════════════════════════════════════════════════╗
echo  ║   [SUKSES] Server Backup AKTIF!                     ║
echo  ╠══════════════════════════════════════════════════════╣
echo  ║                                                      ║
echo  ║   Akses sistem backup:                               ║
echo  ║   http://%IP_SERVER%/presencesync/public
echo  ║                                                      ║
echo  ║   LANGKAH SELANJUTNYA:                               ║
echo  ║   Ubah URL ADMS di mesin fingerprint ke:             ║
echo  ║   Server: %IP_SERVER%
echo  ║   Port  : 80                                         ║
echo  ║                                                      ║
echo  ╚══════════════════════════════════════════════════════╝
echo.
echo  Catat tanggal mulai backup: %TGL%
echo  (Informasikan ke developer untuk sinkronisasi data nanti)
echo.
pause
goto MENU

:: ============================================================
:CEK_STATUS
:: ============================================================
cls
echo.
echo  ╔══════════════════════════════════════════════╗
echo  ║   STATUS SISTEM SAAT INI                    ║
echo  ╚══════════════════════════════════════════════╝
echo.

for /f "tokens=1,2 delims==" %%a in (.env) do (
    if "%%a"=="APP_URL" (
        echo  APP_URL = %%b
        echo %%b | findstr /i "192.168\|localhost\|127.0.0" >nul
        if !errorlevel! equ 0 (
            echo.
            echo  Status: 🔴 MENGGUNAKAN SERVER BACKUP (LAN Lokal)
        ) else (
            echo.
            echo  Status: 🟢 MENGGUNAKAN HOSTING UTAMA (Online)
        )
    )
    if "%%a"=="APP_ENV" echo  APP_ENV = %%b
    if "%%a"=="APP_DEBUG" echo  APP_DEBUG = %%b
    if "%%a"=="DB_DATABASE" echo  DB_DATABASE = %%b
)

echo.
echo  --- Cek koneksi database ---
php artisan db:show >nul 2>&1
if %errorlevel% equ 0 (
    echo  Database: TERHUBUNG ✅
) else (
    echo  Database: TIDAK TERHUBUNG ❌ (pastikan Laragon jalan!)
)

echo.
echo  --- Log penggunaan backup ---
if exist ".backup_log.txt" (
    echo  Riwayat switch backup:
    type ".backup_log.txt"
) else (
    echo  (Belum pernah beralih ke backup)
)

echo.
pause
goto MENU

:: ============================================================
:KEMBALI_KE_HOSTING
:: ============================================================
cls
echo.
echo  ╔══════════════════════════════════════════════════════╗
echo  ║   BACKUP DATA DAN KEMBALI KE HOSTING UTAMA          ║
echo  ╚══════════════════════════════════════════════════════╝
echo.

:: --- Cek apakah sedang pakai backup ---
set "APP_URL_SAAT_INI="
for /f "tokens=1,2 delims==" %%a in (.env) do (
    if "%%a"=="APP_URL" set "APP_URL_SAAT_INI=%%b"
)

echo %APP_URL_SAAT_INI% | findstr /i "192.168\|localhost\|127.0.0" >nul
if %errorlevel% neq 0 (
    echo  [INFO] Sistem saat ini SUDAH menggunakan hosting utama.
    echo  Tidak perlu diubah.
    echo.
    pause
    goto MENU
)

echo  [INFO] Sistem saat ini menggunakan server backup.
echo  Proses ini akan:
echo    1. Export database lokal ke file SQL
echo    2. Kembalikan APP_URL ke hosting utama
echo    3. Rebuild cache
echo.
echo  File export akan dikirim ke developer untuk
echo  dimasukkan ke database hosting utama.
echo.
set /p lanjut="  Lanjutkan? (y/n): "
if /i not "%lanjut%"=="y" goto MENU

:: --- Export database ---
echo.
echo  [LANGKAH 1] Export database...
for /f "tokens=1-3 delims=/ " %%a in ("%date%") do set TGL_EXPORT=%%a%%b%%c

set "NAMA_EXPORT=presencesync_backup_%TGL_EXPORT%.sql"

:: Baca konfigurasi DB dari .env
set "DB_HOST=127.0.0.1"
set "DB_NAME=presencesync"
set "DB_USER=root"
set "DB_PASS="
for /f "tokens=1,2 delims==" %%a in (.env) do (
    if "%%a"=="DB_HOST" set "DB_HOST=%%b"
    if "%%a"=="DB_DATABASE" set "DB_NAME=%%b"
    if "%%a"=="DB_USERNAME" set "DB_USER=%%b"
    if "%%a"=="DB_PASSWORD" set "DB_PASS=%%b"
)

:: Coba mysqldump dari Laragon
set "MYSQLDUMP=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe"
if not exist "%MYSQLDUMP%" (
    set "MYSQLDUMP=C:\laragon\bin\mysql\mysql-8.0.18-winx64\bin\mysqldump.exe"
)
if not exist "%MYSQLDUMP%" (
    :: Cari versi lain
    for /d %%d in (C:\laragon\bin\mysql\*) do (
        if exist "%%d\bin\mysqldump.exe" set "MYSQLDUMP=%%d\bin\mysqldump.exe"
    )
)

if exist "%MYSQLDUMP%" (
    if "%DB_PASS%"=="" (
        "%MYSQLDUMP%" -h %DB_HOST% -u %DB_USER% %DB_NAME% > "%NAMA_EXPORT%"
    ) else (
        "%MYSQLDUMP%" -h %DB_HOST% -u %DB_USER% -p%DB_PASS% %DB_NAME% > "%NAMA_EXPORT%"
    )
    echo  [OK] Database berhasil di-export ke: %NAMA_EXPORT%
) else (
    echo  [WARN] mysqldump tidak ditemukan otomatis.
    echo  Export manual via phpMyAdmin:
    echo    1. Buka http://localhost/phpmyadmin
    echo    2. Klik database 'presencesync'
    echo    3. Tab Export ^> Format SQL ^> Go
    echo    4. Simpan file dengan nama: %NAMA_EXPORT%
    echo.
    pause
)

:: --- Kembalikan URL hosting ---
echo.
echo  [LANGKAH 2] Kembalikan ke hosting utama...

:: Baca URL backup dari file simpanan
set "URL_HOSTING="
if exist ".env_backup_url.txt" (
    set /p URL_HOSTING=<".env_backup_url.txt"
    echo  URL Hosting yang tersimpan: %URL_HOSTING%
) else (
    echo  [!] File .env_backup_url.txt tidak ditemukan!
    set /p URL_HOSTING="  Ketik URL hosting utama (contoh: https://presencesync.domain.com): "
)

powershell -command "(Get-Content '.env') -replace 'APP_URL=.*', 'APP_URL=%URL_HOSTING%' | Set-Content '.env'"
echo  [OK] APP_URL dikembalikan ke: %URL_HOSTING%

:: --- Rebuild cache ---
php artisan config:clear >nul
php artisan config:cache >nul
php artisan route:clear >nul
php artisan route:cache >nul
echo  [OK] Cache direbuild.

:: --- Catat log ---
for /f "tokens=1-3 delims=/ " %%a in ("%date%") do set TGL=%%a-%%b-%%c
echo [KEMBALI KE HOSTING] %TGL% %time% - Kembali ke: %URL_HOSTING%>> ".backup_log.txt"

echo.
echo  ╔══════════════════════════════════════════════════════════╗
echo  ║   [SELESAI] Berhasil kembali ke Hosting Utama!          ║
echo  ╠══════════════════════════════════════════════════════════╣
echo  ║                                                          ║
echo  ║   LANGKAH SELANJUTNYA:                                   ║
echo  ║                                                          ║
echo  ║   1. Kirim file berikut ke developer:                    ║
echo  ║      %NAMA_EXPORT%
echo  ║                                                          ║
echo  ║   2. Ubah URL ADMS mesin fingerprint kembali ke:         ║
echo  ║      %URL_HOSTING%
echo  ║                                                          ║
echo  ║   3. Restart mesin fingerprint                           ║
echo  ║                                                          ║
echo  ╚══════════════════════════════════════════════════════════╝
echo.
pause
goto MENU
