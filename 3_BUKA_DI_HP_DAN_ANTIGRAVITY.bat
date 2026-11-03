@echo off
chcp 65001 >nul
title KOPMA - Akses dari HP & Preview Antigravity
color 0B
echo ============================================================
echo    SI-KOPMA  ^|  Server untuk HP (Android/iOS) + Auto-Reload
echo ============================================================
echo.

REM --- 1. Pastikan MySQL (Laragon) jalan ---
echo [1/3] Memeriksa MySQL...
netstat -an | findstr ":3306" | findstr "LISTENING" >nul
if %errorlevel%==0 (
    echo       MySQL sudah jalan. OK
) else (
    echo       MySQL belum jalan! Nyalakan dulu lewat XAMPP Control Panel ^(Start MySQL^).
    echo       Tekan tombol apa saja untuk lanjut tetap mencoba...
    pause >nul
)

REM --- 2. Tampilkan alamat IP untuk dibuka di HP ---
echo.
echo [2/3] Alamat untuk dibuka di HP (hubungkan HP ke WiFi yang sama):
echo ------------------------------------------------------------
for /f "tokens=2 delims=:" %%i in ('ipconfig ^| findstr /c:"IPv4 Address"') do (
    set ip=%%i
    setlocal enabledelayedexpansion
    echo       http://!ip: =!:8000
    endlocal
)
echo ------------------------------------------------------------
echo   ^> Di HP: buka alamat di atas, lalu pilih menu browser
echo     "Add to Home Screen" untuk menginstall jadi aplikasi.
echo.

REM --- 3. Jalankan server Laravel bind ke semua interface (0.0.0.0) ---
echo [3/3] Menjalankan server Laravel (Ctrl+C untuk berhenti)...
echo.
cd /d "%~dp0laravel_kopma"
"C:\xampp\php\php.exe" artisan serve --host=0.0.0.0 --port=8000

pause
