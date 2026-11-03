<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan role 'mitra' (kurir) ke enum users.role agar akun mitra
        // terpisah dari petugas/kasir. Data existing (admin/manajer/kasir) aman.
        DB::statement("ALTER TABLE users
            MODIFY role ENUM('admin','manajer','kasir','mitra') NOT NULL DEFAULT 'kasir'");
    }

    public function down(): void
    {
        // Kembalikan enum ke 3 role (data 'mitra' perlu diganti dulu agar tidak error).
        DB::statement("ALTER TABLE users
            MODIFY role ENUM('admin','manajer','kasir') NOT NULL DEFAULT 'kasir'");
    }
};