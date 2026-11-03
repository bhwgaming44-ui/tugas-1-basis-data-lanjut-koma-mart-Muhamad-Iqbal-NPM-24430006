<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Susunan tampil produk di katalog toko & POS kasir —
        // diatur petugas lewat modal "Atur Urutan" (mirip fitur urutan pada Promo).
        // Nomor kecil = tampil lebih dulu. Default 0 untuk data lama.
        Schema::table('barangs', function (Blueprint $table) {
            $table->integer('urutan')->default(0)->after('stok_minimum');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};
