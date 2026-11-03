<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_penjualan');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualans')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barangs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};
