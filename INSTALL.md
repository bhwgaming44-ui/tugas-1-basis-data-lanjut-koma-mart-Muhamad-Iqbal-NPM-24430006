# 🚀 Panduan Instalasi KOPMA MART - Laravel

## 📋 Prasyarat
1. XAMPP sudah diinstal
2. **Apache** & **MySQL** dijalankan di XAMPP Control Panel

---

## ⚙️ Langkah 1: Siapkan Database

1. Buka browser → `http://localhost/phpmyadmin`
2. Klik **New** (Buat Database Baru)
3. Nama database: **`kopma_npm`**
4. Kolasi: `utf8mb4_unicode_ci`
5. Klik **Create**

---

## ⚙️ Langkah 2: Pastikan File .env

Buka folder `laravel_kopma` → cek file `.env`:

```
DB_DATABASE=kopma_npm
DB_USERNAME=root
DB_PASSWORD=
```

> ✅ Pastikan tidak ada tambahan karakter di belakang nilai

---

## 🚀 Langkah 3: Jalankan Migrasi

Buka **CMD** dari folder project:

```cmd
cd C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma
```

Lalu jalankan:
```cmd
php artisan migrate --seed
```

---

## 🌐 Langkah 4: Jalankan Web Server

```cmd
php artisan serve
```

Aplikasi akan berjalan di: **`http://localhost:8000`**

---

## ✅ Cek Status

| Hal | Cara Cek |
|-----|----------|
| Apache | XAMPP Control Panel → Apache = Running |
| MySQL | XAMPP Control Panel → MySQL = Running |
| Database | phpMyAdmin → ada database `kopma_npm` |
| Migrasi | `storage\logs\laravel.log` |
| Server | `http://localhost:8000` |

---

## 🐛 Troubleshooting

### ❌ "Connection refused" / PHP tidak ditemukan
→ Pastikan XAMPP sudah diinstal, dan PHP ditambahkan ke Environment Variables

### ❌ SQLSTATE[42S02]: Table doesn't exist
→ Jalankan ulang: `php artisan migrate --seed`

### ❌ HTTP ERROR 500
→ Cek `storage\logs\laravel.log` untuk detail error

### ❌ Chatbot masih "koneksi bermasalah"
→ Pastikan route `chatbot/ask` sudah ada di `routes/web.php`
→ Pastikan `app/Http/Controllers/ChatbotController.php` sudah ada

---

## 📁 File Yang Telah Diperbaiki

| File | Aksi |
|------|------|
| `app/Http/Controllers/ChatbotController.php` | ✅ Sudah dibuat |
| `database/migrations/2024_01_01_000009_create_transaksi_simpanan_table.php` | ✅ Sudah dibuat |
| `routes/web.php` | ✅ Sudah mengarah ke ChatbotController |

---

## 🎯 Akses Aplikasi

```
http://localhost:8000
```

Chatbot ada di **kanan bawah** → klik icon WhatsApp ✅

---
