<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Samakan enum metode_bayar dengan data live:
        // tunai, qris, transfer (kode lama) + koppay, transfer_bank, virtual_account (data existing)
        DB::statement("ALTER TABLE penjualans
            MODIFY metode_bayar ENUM('tunai','qris','transfer','koppay','transfer_bank','virtual_account')
            NOT NULL DEFAULT 'tunai'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE penjualans
            MODIFY metode_bayar ENUM('tunai','qris','transfer') NOT NULL DEFAULT 'tunai'");
    }
};
