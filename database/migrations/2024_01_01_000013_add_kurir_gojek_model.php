<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PENYATUAN AKUN ala Gojek: satu akun member bisa jadi USER + KURIR.
 * - anggotas.is_kurir : flag member terdaftar sebagai kurir (mode antar aktif)
 * - penjualans.id_kurir_anggota : kurir dari kalangan member (id_anggota)
 * - penjualans.kurir_tipe : 'anggota' (member) vs 'staf' (users/mitra/petugas legacy)
 * - chat_pesans.id_kurir_anggota : id_anggota kurir bila pengirim kurir-member
 *
 * Kolom id_kurir LAMA (id_user staf/mitra) tetap dipertahankan agar data
 * live (pesanan 11-12) tidak rusak. Query harus cek KEDUA kolom.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('anggotas') && !Schema::hasColumn('anggotas', 'is_kurir')) {
            Schema::table('anggotas', function (Blueprint $table) {
                $table->boolean('is_kurir')->default(false)->after('foto');
            });
        }

        if (Schema::hasTable('penjualans') && !Schema::hasColumn('penjualans', 'id_kurir_anggota')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->unsignedBigInteger('id_kurir')->nullable();
                $table->unsignedBigInteger('id_kurir_anggota')->nullable();
                $table->string('kurir_tipe', 20)->nullable();
                $table->index(['id_kurir_anggota', 'status_pesanan'], 'penjualans_kurir_anggota_idx');
            });
        }

        if (Schema::hasTable('chat_pesans') && !Schema::hasColumn('chat_pesans', 'id_kurir_anggota')) {
            Schema::table('chat_pesans', function (Blueprint $table) {
                if (!Schema::hasColumn('chat_pesans', 'id_kurir')) {
                    $table->unsignedBigInteger('id_kurir')->nullable();
                }
                $table->unsignedBigInteger('id_kurir_anggota')->nullable();
                $table->index(['id_kurir_anggota', 'dibaca'], 'chat_kurir_anggota_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_pesans') && Schema::hasColumn('chat_pesans', 'id_kurir_anggota')) {
            Schema::table('chat_pesans', function (Blueprint $table) {
                $table->dropIndex('chat_kurir_anggota_idx');
                $table->dropColumn('id_kurir_anggota');
            });
        }
        if (Schema::hasTable('penjualans') && Schema::hasColumn('penjualans', 'id_kurir_anggota')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->dropIndex('penjualans_kurir_anggota_idx');
                $table->dropColumn(['id_kurir_anggota', 'kurir_tipe']);
            });
        }
        if (Schema::hasTable('anggotas') && Schema::hasColumn('anggotas', 'is_kurir')) {
            Schema::table('anggotas', function (Blueprint $table) {
                $table->dropColumn('is_kurir');
            });
        }
    }
};
