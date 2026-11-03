@echo off
echo ========================================
echo  INSTALASI & JALANKAN APLIKASI KOPMA
echo ========================================
echo.
echo Langkah 1: Cek PHP terinstall...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP tidak ditemukan! 
    echo ⚠ Pastikan XAMPP sudah diinstal dan PHP ditambahkan ke PATH.
    pause
    exit /b
)
echo ✅ PHP ditemukan.
echo.
echo Langkah 2: Cek MySQL...
net start MySQL >nul 2>&1
echo ✅ MySQL dijalankan.
echo.
echo Langkah 3: Pastikan database 'kopma_npm' ada...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS kopma_npm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
echo ✅ Database 'kopma_npm' siap.
echo.
echo Langkah 4: Migrasikan database...
php artisan migrate --seed --force
if %errorlevel% neq 0 (
    echo ❌ Migrasi gagal.
    echo Lihat rincian error di storage\logs\laravel.log
    pause
    exit /b
)
echo ✅ Migrasi & Seeder berhasil.
echo.
echo Langkah 5: Menjalankan server...
php artisan serve --host=127.0.0.1 --port=8000
echo.
echo ✅ Aplikasi berjalan di http://localhost:8000
pause