<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Akun login pelanggan (daftar / masuk pertama kali ke website)
        Schema::table('anggotas', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
        });

        // Fitur belanja online: checkout website + antar ke rumah + status pesanan
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('alamat_antar', 255)->nullable();
            $table->enum('metode_antar', ['ambil', 'antar'])->default('ambil');
            $table->string('catatan', 255)->nullable();
            $table->enum('status_bayar', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->enum('status_pesanan', ['menunggu', 'diproses', 'diantar', 'selesai'])->default('menunggu');
        });

        // Foto profil pelanggan: path file hasil upload album (uploads/foto/...)
        // atau URL gambar (mis. thumbnail Google Drive).
        if (!Schema::hasColumn('anggotas', 'foto')) {
            Schema::table('anggotas', function (Blueprint $table) {
                $table->string('foto', 500)->nullable()->after('password');
            });
        }
    }

    public function down(): void
    {
        Schema::table('anggotas', function (Blueprint $table) {
            $table->dropColumn('password');
        });
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['alamat_antar', 'metode_antar', 'catatan', 'status_bayar', 'status_pesanan']);
        });
    }
};
