<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('topup_requests')) return;
        Schema::create('topup_requests', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bayar', 30)->unique()->index();
            $table->unsignedBigInteger('id_anggota');
            $table->foreign('id_anggota')->references('id_anggota')->on('anggotas')->onDelete('cascade');
            $table->decimal('nominal', 12, 2);
            $table->string('metode', 20)->default('qris');
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->unsignedBigInteger('id_petugas')->nullable();
            $table->foreign('id_petugas')->references('id_user')->on('users')->onDelete('set null');
            $table->string('catatan', 255)->nullable();
            $table->timestamp('dibayar_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topup_requests');
    }
};