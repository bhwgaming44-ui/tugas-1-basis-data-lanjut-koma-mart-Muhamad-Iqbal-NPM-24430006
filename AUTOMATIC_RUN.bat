@echo off
title KOPMA MART - Auto Setup & Run
color 0A

echo ========================================
echo   KOPMA MART - Auto Setup & Run
echo ========================================
echo.

:: Check PHP
echo [1/6] Mengecek PHP...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP tidak ditemukan!
    echo ⚠ Pastikan XAMPP sudah diinstal dan PATH diatur.
    pause
    exit /b
)
echo ✅ PHP terdeteksi

:: Check MySQL
echo.
echo [2/6] Mengecek MySQL...
net start MySQL >nul 2>&1
echo ✅ MySQL dijalankan

:: Create Database
echo.
echo [3/6] Membuat database 'kopma_npm'...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS kopma_npm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1
echo ✅ Database 'kopma_npm' siap

:: Run Migrations
echo.
echo [4/6] Menjalankan migrasi...
php artisan migrate --seed --force
if %errorlevel% neq 0 (
    echo ❌ Migrasi gagal!
    echo 📋 Cek log: storage\logs\laravel.log
    pause
    exit /b
)
echo ✅ Migrasi & Seeder selesai

:: Run Server
echo.
echo [5/6] Menjalankan server...
echo.
php artisan serve --host=127.0.0.1 --port=8000