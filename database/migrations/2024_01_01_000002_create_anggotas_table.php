<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggotas', function (Blueprint $table) {
            $table->id('id_anggota');
            $table->string('kode_member', 20)->unique(); // Kode kartu member pelanggan
            $table->string('nama', 100);
            $table->string('alamat', 150)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->integer('poin')->default(0); // Poin loyalitas pelanggan
            $table->decimal('total_belanja', 12, 2)->default(0); // Akumulasi belanja member
            $table->enum('status_aktif', ['aktif', 'non_aktif'])->default('aktif');
            $table->date('tanggal_daftar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggotas');
    }
};
