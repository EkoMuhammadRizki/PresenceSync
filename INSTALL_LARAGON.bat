@echo off
setlocal EnableDelayedExpansion
chcp 65001 >nul

:: ============================================================
::   PresenceSync - INSTALL KE LARAGON (Server Sekolah)
::   Tidak perlu Node.js, tidak perlu install Composer terpisah
::   Cukup jalankan script ini sebagai Administrator
:: ============================================================

title PRESENCESYNC - Install ke Laragon

echo.
echo  ============================================================
echo   PresenceSync - Install ke Server Sekolah (Laragon)
echo  ============================================================
echo.

:: --- Cek harus dijalankan sebagai Administrator ---
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo  [ERROR] Script ini harus dijalankan sebagai Administrator!
    echo.
    echo  Cara: Klik kanan pada file ini -^> "Run as administrator"
    pause
    exit /b 1
)

:: --- Deteksi path Laragon ---
set "LARAGON_PATH=C:\laragon"
set "WWW_PATH=C:\laragon\www"
set "TARGET_FOLDER=presencesync"
set "TARGET_PATH=%WWW_PATH%\%TARGET_FOLDER%"

:: Deteksi otomatis versi PHP di Laragon
for /d %%d in ("C:\laragon\bin\php\php-8*") do set "LARAGON_PHP=%%d\php.exe"
set "LARAGON_COMPOSER=C:\laragon\bin\composer\composer.phar"
set "LARAGON_MYSQL=C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe"

:: Cek Laragon terinstall
if not exist "%LARAGON_PATH%" (
    echo  [ERROR] Laragon tidak ditemukan di %LARAGON_PATH%
    echo.
    echo  Download Laragon di: https://laragon.org/download/
    echo  Install Laragon Full, lalu jalankan script ini lagi.
    pause
    exit /b 1
)

if not exist "%LARAGON_PHP%" (
    echo  [ERROR] PHP Laragon tidak ditemukan!
    echo  Cek folder: C:\laragon\bin\php\
    pause
    exit /b 1
)

echo  [OK] Laragon ditemukan di: %LARAGON_PATH%
echo  [OK] PHP Laragon: %LARAGON_PHP%
echo.

:: --- Dapatkan IP server ---
echo  ============================================================
echo   Mendeteksi IP Server...
echo  ============================================================
echo.

for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr /v "127.0.0.1"') do (
    set "SERVER_IP=%%a"
    goto :ip_found
)
:ip_found
set "SERVER_IP=%SERVER_IP: =%"

echo  IP Server yang terdeteksi: %SERVER_IP%
echo.
set /p "INPUT_IP=  Masukkan IP server (Enter jika sudah benar) [%SERVER_IP%]: "
if not "!INPUT_IP!"=="" set "SERVER_IP=!INPUT_IP!"
echo  [OK] IP Server: %SERVER_IP%
echo.

:: --- Pastikan Laragon jalan ---
echo  ============================================================
echo   Pastikan Laragon Sudah Berjalan
echo  ============================================================
echo.
echo  [!] PENTING: Buka Laragon dan klik "Start All"
echo      Apache dan MySQL harus berwarna HIJAU sebelum lanjut.
echo.
echo  Tekan Enter jika Laragon sudah jalan (Apache dan MySQL hijau)...
pause

:: --- Cek koneksi MySQL ---
echo.
echo  Mengecek koneksi MySQL...
"%LARAGON_MYSQL%" -u root --connect-timeout=5 -e "SELECT 1;" >nul 2>&1
if %errorlevel% neq 0 (
    echo  [ERROR] Tidak bisa konek ke MySQL!
    echo  Pastikan MySQL di Laragon sudah jalan (hijau) dan coba lagi.
    pause
    exit /b 1
)
echo  [OK] MySQL terhubung!
echo.

:: --- Buat database ---
echo  ============================================================
echo   Membuat Database presencesync
echo  ============================================================
echo.
"%LARAGON_MYSQL%" -u root -e "CREATE DATABASE IF NOT EXISTS presencesync CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1
echo  [OK] Database presencesync siap.
echo.

:: --- Copy file ke Laragon www ---
echo  ============================================================
echo   Menyalin PresenceSync ke Laragon www
echo  ============================================================
echo.

if exist "%TARGET_PATH%" (
    echo  [INFO] Folder sudah ada: %TARGET_PATH%
    echo.
    echo    [1] Hapus dan install ulang (data DB akan hilang!)
    echo    [2] Update file saja (data aman)
    echo    [3] Batalkan
    echo.
    set /p "PILIHAN=  Pilihan [2]: "
    if "!PILIHAN!"=="1" (
        rmdir /s /q "%TARGET_PATH%"
        echo  [OK] Folder lama dihapus.
    ) else if "!PILIHAN!"=="3" (
        echo  Dibatalkan.
        pause
        exit /b 0
    ) else (
        echo  [INFO] Mode update - tetap lanjut update file...
    )
)

echo  [INFO] Menyalin file... (mohon tunggu 1-3 menit)
echo.

if not exist "%TARGET_PATH%" mkdir "%TARGET_PATH%"

xcopy "%~dp0app"         "%TARGET_PATH%\app\"         /E /I /Y /Q 2>nul
xcopy "%~dp0bootstrap"   "%TARGET_PATH%\bootstrap\"   /E /I /Y /Q 2>nul
xcopy "%~dp0config"      "%TARGET_PATH%\config\"      /E /I /Y /Q 2>nul
xcopy "%~dp0database"    "%TARGET_PATH%\database\"    /E /I /Y /Q 2>nul
xcopy "%~dp0public"      "%TARGET_PATH%\public\"      /E /I /Y /Q 2>nul
xcopy "%~dp0resources"   "%TARGET_PATH%\resources\"   /E /I /Y /Q 2>nul
xcopy "%~dp0routes"      "%TARGET_PATH%\routes\"      /E /I /Y /Q 2>nul
xcopy "%~dp0storage"     "%TARGET_PATH%\storage\"     /E /I /Y /Q 2>nul
xcopy "%~dp0vendor"      "%TARGET_PATH%\vendor\"      /E /I /Y /Q 2>nul

copy /Y "%~dp0artisan"       "%TARGET_PATH%\artisan"       >nul 2>&1
copy /Y "%~dp0composer.json" "%TARGET_PATH%\composer.json" >nul 2>&1
copy /Y "%~dp0composer.lock" "%TARGET_PATH%\composer.lock" >nul 2>&1
copy /Y "%~dp0server.php"    "%TARGET_PATH%\server.php"    >nul 2>&1
copy /Y "%~dp0phpunit.xml"   "%TARGET_PATH%\phpunit.xml"   >nul 2>&1

echo  [OK] File berhasil disalin.
echo.

:: --- Setup .env ---
echo  ============================================================
echo   Konfigurasi .env Production
echo  ============================================================
echo.

if exist "%~dp0.env.production" (
    copy /Y "%~dp0.env.production" "%TARGET_PATH%\.env" >nul
) else (
    copy /Y "%~dp0.env.example" "%TARGET_PATH%\.env" >nul
)

powershell -Command "(Get-Content '%TARGET_PATH%\.env') -replace '\[IP-SERVER\]', '%SERVER_IP%' | Set-Content '%TARGET_PATH%\.env'"
echo  [OK] .env dikonfigurasi dengan IP: %SERVER_IP%
echo.

:: --- PHP Artisan Setup ---
echo  ============================================================
echo   Setup Aplikasi (tanpa Node.js / npm)
echo  ============================================================
echo.

cd /d "%TARGET_PATH%"

echo  [INFO] Generate application key...
"%LARAGON_PHP%" artisan key:generate --force
if %errorlevel% neq 0 (
    echo  [ERROR] key:generate gagal! Cek file .env
    pause
    exit /b 1
)
echo  [OK] Key berhasil dibuat.
echo.

echo  [INFO] Migrasi database...
"%LARAGON_PHP%" artisan migrate --force
if %errorlevel% neq 0 (
    echo  [ERROR] Migrasi gagal! Cek MySQL dan konfigurasi .env
    pause
    exit /b 1
)
echo  [OK] Migrasi selesai.
echo.

echo  [INFO] Seeder data awal...
"%LARAGON_PHP%" artisan db:seed --force 2>nul
echo  [OK] Seeder selesai (atau tidak ada seeder, lanjut).
echo.

echo  [INFO] Buat storage link...
"%LARAGON_PHP%" artisan storage:link --force 2>nul
echo  [OK] Storage link selesai.
echo.

echo  [INFO] Cache production...
"%LARAGON_PHP%" artisan config:cache
"%LARAGON_PHP%" artisan route:cache
"%LARAGON_PHP%" artisan view:cache
echo  [OK] Cache selesai.
echo.

:: --- Permission ---
icacls "%TARGET_PATH%\storage" /grant Everyone:F /T >nul 2>&1
icacls "%TARGET_PATH%\bootstrap\cache" /grant Everyone:F /T >nul 2>&1
echo  [OK] Permission storage dan cache diset.
echo.

:: --- Buka port 80 di Firewall ---
echo  ============================================================
echo   Konfigurasi Windows Firewall
echo  ============================================================
echo.
netsh advfirewall firewall delete rule name="Laragon Apache HTTP" >nul 2>&1
netsh advfirewall firewall add rule name="Laragon Apache HTTP" dir=in action=allow protocol=TCP localport=80 >nul 2>&1
echo  [OK] Port 80 dibuka di Windows Firewall untuk akses LAN.
echo.

:: --- Fix Apache agar listen ke semua IP (bukan hanya localhost) ---
echo  ============================================================
echo   Konfigurasi Apache (Akses LAN)
echo  ============================================================
echo.

for /d %%d in ("C:\laragon\bin\apache\httpd-*") do set "APACHE_DIR=%%d"
set "APACHE_CONF=%APACHE_DIR%\conf\httpd.conf"

if exist "%APACHE_CONF%" (
    powershell -Command "(Get-Content '%APACHE_CONF%') -replace 'Listen 127\.0\.0\.1:80', 'Listen 0.0.0.0:80' | Set-Content '%APACHE_CONF%'"
    echo  [OK] Apache dikonfigurasi untuk akses LAN.
    echo.
    echo  [INFO] Restart Apache...
    net stop apache2.4 >nul 2>&1
    net stop "Apache2.4" >nul 2>&1
    timeout /t 2 /nobreak >nul
    net start apache2.4 >nul 2>&1
    net start "Apache2.4" >nul 2>&1
    echo  [OK] Apache direstart.
) else (
    echo  [WARN] httpd.conf tidak ditemukan. Restart Apache manual via Laragon.
)
echo.

:: --- Selesai ---
echo.
echo  ============================================================
echo   [SUKSES] PresenceSync Berhasil Diinstall!
echo  ============================================================
echo.
echo  Akses dari browser:
echo.
echo    Komputer ini sendiri:
echo      http://localhost/presencesync/public
echo.
echo    Komputer lain di jaringan sekolah:
echo      http://%SERVER_IP%/presencesync/public
echo.
echo  Login default:
echo    Username : admin
echo    Password : password
echo.
echo  Fingerprint ADMS:
echo    Server  : %SERVER_IP%
echo    Port    : 80
echo    URL     : /presencesync/public/api/absensi/sync
echo.
echo  ============================================================
echo   Sistem berjalan otomatis lewat Laragon.
echo   Tidak perlu terminal / Command Prompt lagi!
echo  ============================================================
echo.
echo  Tekan Enter untuk membuka browser...
pause

start http://localhost/presencesync/public
endlocal
