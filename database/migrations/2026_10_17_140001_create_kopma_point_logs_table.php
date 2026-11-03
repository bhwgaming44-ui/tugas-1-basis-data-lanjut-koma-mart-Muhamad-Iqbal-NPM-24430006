<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 1) Tabel riwayat mutasi Kopma Point (top-up & pembayaran member) —
 *    tanpa ini halaman /kopma-point error "Base table not found".
 * 2) Kolom foto profil di tabel users (petugas & kurir mitra).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kopma_point_logs')) {
            Schema::create('kopma_point_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_anggota')->constrained('anggotas', 'id_anggota')->cascadeOnDelete();
                $table->string('jenis', 20);       // 'topup' | 'pembayaran'
                $table->string('metode', 30)->nullable(); // 'tunai','transfer','e-wallet','kopma_point',...
                $table->decimal('nominal', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->index('created_at');
            });
        }

        if (!Schema::hasColumn('users', 'foto')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('foto')->nullable()->after('password'); // path storage/ atau URL gambar/Drive
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kopma_point_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
