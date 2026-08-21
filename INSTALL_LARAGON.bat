@echo off
setlocal EnableDelayedExpansion
chcp 65001 >nul

:: ============================================================
::   PresenceSync - INSTALL KE LARAGON (Server Sekolah)
::   Admin hanya perlu:
::     Buka browser -> https://presencesync.lokal
::   Tidak perlu terminal, npm run dev, atau php artisan serve!
:: ============================================================

title PRESENCESYNC - Install ke Laragon

echo.
echo  ============================================================
echo   PresenceSync - Install ke Server Sekolah (Laragon)
echo   Setelah install, akses: https://presencesync.lokal
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

echo  [OK] .env dikonfigurasi (APP_URL=https://presencesync.lokal).
echo.

:: --- PHP Artisan Setup ---
echo  ============================================================
echo   Setup Aplikasi Laravel
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

:: ============================================================
:: BAGIAN BARU: Setup Virtual Host + HTTPS presencesync.lokal
:: ============================================================
echo  ============================================================
echo   Setup Domain presencesync.lokal + HTTPS (SSL)
echo  ============================================================
echo.

:: --- Deteksi Apache dir ---
for /d %%d in ("C:\laragon\bin\apache\httpd-*") do set "APACHE_DIR=%%d"
set "APACHE_CONF=%APACHE_DIR%\conf\httpd.conf"
set "VHOST_CONF=%APACHE_DIR%\conf\extra\httpd-vhosts.conf"
set "SSL_DIR=%LARAGON_PATH%\etc\ssl"

:: --- Aktifkan mod_ssl di Apache ---
if exist "%APACHE_CONF%" (
    powershell -Command "(Get-Content '%APACHE_CONF%') -replace '#LoadModule ssl_module', 'LoadModule ssl_module' | Set-Content '%APACHE_CONF%'"
    powershell -Command "(Get-Content '%APACHE_CONF%') -replace '#LoadModule socache_shmcb_module', 'LoadModule socache_shmcb_module' | Set-Content '%APACHE_CONF%'"
    powershell -Command "(Get-Content '%APACHE_CONF%') -replace '#Include conf/extra/httpd-ssl.conf', 'Include conf/extra/httpd-ssl.conf' | Set-Content '%APACHE_CONF%'"
    powershell -Command "(Get-Content '%APACHE_CONF%') -replace '#Include conf/extra/httpd-vhosts.conf', 'Include conf/extra/httpd-vhosts.conf' | Set-Content '%APACHE_CONF%'"
    powershell -Command "(Get-Content '%APACHE_CONF%') -replace 'Listen 127\.0\.0\.1:80', 'Listen 0.0.0.0:80' | Set-Content '%APACHE_CONF%'"
    echo  [OK] mod_ssl diaktifkan di Apache.
) else (
    echo  [WARN] httpd.conf tidak ditemukan. Setup manual mungkin diperlukan.
)

:: --- Buat SSL certificate self-signed untuk presencesync.lokal ---
echo.
echo  [INFO] Membuat SSL certificate untuk presencesync.lokal...

:: Cek apakah Laragon sudah punya openssl
set "LARAGON_OPENSSL="
for /d %%d in ("C:\laragon\bin\openssl\*") do set "LARAGON_OPENSSL=%%d\openssl.exe"
if not defined LARAGON_OPENSSL (
    :: Coba path default Laragon
    if exist "C:\laragon\bin\apache\httpd-2.4.54-win64-VS16\bin\openssl.exe" (
        set "LARAGON_OPENSSL=C:\laragon\bin\apache\httpd-2.4.54-win64-VS16\bin\openssl.exe"
    )
    for %%d in ("C:\laragon\bin\apache\httpd-*\bin\openssl.exe") do set "LARAGON_OPENSSL=%%d"
)

if not exist "%SSL_DIR%" mkdir "%SSL_DIR%"

if defined LARAGON_OPENSSL (
    if exist "!LARAGON_OPENSSL!" (
        "!LARAGON_OPENSSL!" req -x509 -nodes -days 3650 -newkey rsa:2048 ^
            -keyout "%SSL_DIR%\presencesync.key" ^
            -out "%SSL_DIR%\presencesync.crt" ^
            -subj "/C=ID/ST=Jawa Barat/L=Sekolah/O=PresenceSync/CN=presencesync.lokal" ^
            -addext "subjectAltName=DNS:presencesync.lokal" 2>nul
        echo  [OK] SSL certificate dibuat.
    ) else (
        echo  [WARN] OpenSSL tidak ditemukan. Menggunakan SSL bawaan Laragon jika ada.
    )
) else (
    echo  [WARN] OpenSSL tidak ditemukan. Menggunakan SSL bawaan Laragon jika ada.
)

:: Fallback: gunakan SSL default Laragon jika ada
if not exist "%SSL_DIR%\presencesync.crt" (
    if exist "%LARAGON_PATH%\etc\ssl\laragon.crt" (
        copy /Y "%LARAGON_PATH%\etc\ssl\laragon.crt" "%SSL_DIR%\presencesync.crt" >nul 2>&1
        copy /Y "%LARAGON_PATH%\etc\ssl\laragon.key" "%SSL_DIR%\presencesync.key" >nul 2>&1
        echo  [OK] Menggunakan SSL certificate Laragon bawaan.
    )
)

:: --- Buat Virtual Host config ---
echo.
echo  [INFO] Membuat Virtual Host presencesync.lokal...

:: Hapus entry lama kalau ada
if exist "%VHOST_CONF%" (
    powershell -Command ^
        "$content = Get-Content '%VHOST_CONF%' -Raw; " ^
        "$start = $content.IndexOf('# BEGIN PresenceSync'); " ^
        "$end = $content.IndexOf('# END PresenceSync'); " ^
        "if ($start -ge 0 -and $end -ge 0) { " ^
        "    $content = $content.Substring(0, $start) + $content.Substring($end + '# END PresenceSync'.Length); " ^
        "    $content | Set-Content '%VHOST_CONF%' -NoNewline; " ^
        "}"
)

:: Tambahkan Virtual Host baru
(
echo.
echo # BEGIN PresenceSync
echo ^<VirtualHost *:80^>
echo     ServerName presencesync.lokal
echo     DocumentRoot "%TARGET_PATH%\public"
echo     ^<Directory "%TARGET_PATH%\public"^>
echo         Options Indexes FollowSymLinks
echo         AllowOverride All
echo         Require all granted
echo     ^</Directory^>
echo     # Redirect HTTP ke HTTPS
echo     RewriteEngine On
echo     RewriteCond %%{HTTPS} off
echo     RewriteRule ^(.*)$ https://%%{HTTP_HOST}%%{REQUEST_URI} [L,R=301]
echo ^</VirtualHost^>
echo.
echo ^<VirtualHost *:443^>
echo     ServerName presencesync.lokal
echo     DocumentRoot "%TARGET_PATH%\public"
echo     SSLEngine on
) >> "%VHOST_CONF%"

:: Cek apakah ada SSL cert
if exist "%SSL_DIR%\presencesync.crt" (
    (
    echo     SSLCertificateFile "%SSL_DIR%\presencesync.crt"
    echo     SSLCertificateKeyFile "%SSL_DIR%\presencesync.key"
    ) >> "%VHOST_CONF%"
) else (
    :: Gunakan Laragon default SSL
    (
    echo     SSLCertificateFile "C:/laragon/etc/ssl/laragon.crt"
    echo     SSLCertificateKeyFile "C:/laragon/etc/ssl/laragon.key"
    ) >> "%VHOST_CONF%"
)

(
echo     ^<Directory "%TARGET_PATH%\public"^>
echo         Options Indexes FollowSymLinks
echo         AllowOverride All
echo         Require all granted
echo     ^</Directory^>
echo ^</VirtualHost^>
echo # END PresenceSync
) >> "%VHOST_CONF%"

echo  [OK] Virtual Host presencesync.lokal dibuat.

:: --- Daftarkan ke Windows hosts file ---
echo.
echo  [INFO] Mendaftarkan presencesync.lokal ke hosts file...

:: Hapus entry lama
powershell -Command "(Get-Content 'C:\Windows\System32\drivers\etc\hosts') | Where-Object { $_ -notmatch 'presencesync\.test' } | Set-Content 'C:\Windows\System32\drivers\etc\hosts'"

:: Tambah entry baru
echo 127.0.0.1 presencesync.lokal >> "C:\Windows\System32\drivers\etc\hosts"
echo  [OK] presencesync.lokal didaftarkan ke hosts file.

:: --- Buka port 80 dan 443 di Firewall ---
echo.
echo  [INFO] Buka port 80 dan 443 di Windows Firewall...
netsh advfirewall firewall delete rule name="Laragon Apache HTTP" >nul 2>&1
netsh advfirewall firewall delete rule name="Laragon Apache HTTPS" >nul 2>&1
netsh advfirewall firewall add rule name="Laragon Apache HTTP" dir=in action=allow protocol=TCP localport=80 >nul 2>&1
netsh advfirewall firewall add rule name="Laragon Apache HTTPS" dir=in action=allow protocol=TCP localport=443 >nul 2>&1
echo  [OK] Port 80 dan 443 dibuka.

:: --- Set Laragon auto-start saat Windows Boot ---
echo.
echo  ============================================================
echo   Set Laragon Auto-Start saat Windows Boot
echo  ============================================================
echo.

:: Cek lokasi Laragon.exe
set "LARAGON_EXE=C:\laragon\laragon.exe"
if exist "%LARAGON_EXE%" (
    reg add "HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\Run" /v "Laragon" /t REG_SZ /d "\"%LARAGON_EXE%\"" /f >nul 2>&1
    echo  [OK] Laragon akan auto-start saat Windows Boot.
    echo  [INFO] Pastikan Laragon di-set "Auto Run on Startup" di menu Laragon:
    echo         Laragon -^> Preferences -^> General -^> Auto Run on Startup: ON
) else (
    echo  [WARN] laragon.exe tidak ditemukan, lewati auto-start setup.
)

:: --- Restart Apache ---
echo.
echo  [INFO] Restart Apache untuk menerapkan konfigurasi...
net stop apache2.4 >nul 2>&1
net stop "Apache2.4" >nul 2>&1
timeout /t 2 /nobreak >nul
net start apache2.4 >nul 2>&1
net start "Apache2.4" >nul 2>&1
echo  [OK] Apache direstart.

:: --- Buat shortcut di Desktop ---
echo.
echo  ============================================================
echo   Membuat Shortcut di Desktop
echo  ============================================================
echo.

set "DESKTOP=%USERPROFILE%\Desktop"
set "SHORTCUT_URL=%DESKTOP%\PresenceSync.url"

(
echo [InternetShortcut]
echo URL=https://presencesync.lokal
echo IconFile=C:\laragon\laragon.exe
echo IconIndex=0
) > "%SHORTCUT_URL%"

echo  [OK] Shortcut "PresenceSync" dibuat di Desktop.
echo  [INFO] Admin cukup double-klik shortcut tersebut untuk buka sistem.
echo.

:: ============================================================
echo.
echo  ============================================================
echo   [SUKSES] PresenceSync Berhasil Diinstall!
echo  ============================================================
echo.
echo  Admin cukup buka browser dan ketik:
echo.
echo    https://presencesync.lokal
echo.
echo  ATAU double-klik shortcut "PresenceSync" di Desktop.
echo.
echo  Tidak perlu terminal, npm run dev, atau php artisan serve!
echo  Sistem berjalan otomatis saat komputer dinyalakan.
echo.
echo  Login default:
echo    Username : admin
echo    Password : password
echo.
echo  Fingerprint ADMS:
echo    Server  : presencesync.lokal
echo    Port    : 443 (HTTPS)
echo    URL     : /api/absensi/sync
echo.
echo  CATATAN: Jika browser menampilkan peringatan "Not Secure",
echo  klik "Advanced" -> "Proceed to presencesync.lokal (unsafe)"
echo  Ini normal untuk sertifikat SSL lokal.
echo.
echo  ============================================================
echo   Sistem berjalan otomatis lewat Laragon.
echo   Tidak perlu terminal / Command Prompt lagi!
echo  ============================================================
echo.
echo  Tekan Enter untuk membuka browser...
pause

start https://presencesync.lokal
endlocal
