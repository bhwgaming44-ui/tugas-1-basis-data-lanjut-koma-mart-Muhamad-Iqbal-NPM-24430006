<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('kode_barcode', 50)->unique();
            $table->string('nama_barang', 150);
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->decimal('harga_beli', 12, 2);
            $table->decimal('harga_jual_umum', 12, 2);
            $table->decimal('harga_jual_anggota', 12, 2);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->timestamps();

            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_barangs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
