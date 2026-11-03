<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->string('no_struk', 30)->unique();
            $table->timestamp('tanggal_transaksi')->useCurrent();
            $table->unsignedBigInteger('id_anggota')->nullable(); // NULL jika pembeli umum/non-member
            $table->unsignedBigInteger('id_kasir');
            $table->decimal('total_bruto', 12, 2);
            $table->decimal('total_diskon', 12, 2)->default(0);
            $table->decimal('total_netto', 12, 2);
            $table->decimal('nominal_bayar', 12, 2);
            $table->decimal('kembalian', 12, 2)->default(0);
            $table->enum('metode_bayar', ['tunai', 'qris', 'transfer'])->default('tunai');
            $table->timestamps();

            $table->foreign('id_anggota')->references('id_anggota')->on('anggotas')->onDelete('set null');
            $table->foreign('id_kasir')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
