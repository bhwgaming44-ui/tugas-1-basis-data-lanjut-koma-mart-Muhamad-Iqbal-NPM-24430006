<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GPS kurir berasal dari tabel `users` (akun mitra/petugas pengantar).
 * Diperlukan agar sisi USER dapat membaca lokasi kurir lintas-sesi —
 * sebelumnya lokasi disimpan di session kurir sehingga tidak pernah
 * terlihat oleh session pelanggan (peta selalu "belum tersedia").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('kurir_lat', 10, 7)->nullable()->after('remember_token');
            $table->decimal('kurir_lng', 10, 7)->nullable()->after('kurir_lat');
            $table->timestamp('kurir_lokasi_diperbarui_pada')->nullable()->after('kurir_lng');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kurir_lat', 'kurir_lng', 'kurir_lokasi_diperbarui_pada']);
        });
    }
};
