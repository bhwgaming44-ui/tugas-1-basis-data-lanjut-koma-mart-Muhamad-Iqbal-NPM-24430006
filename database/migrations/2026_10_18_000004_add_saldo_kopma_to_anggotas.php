<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FIX ERROR TOP UP: kolom `anggotas.saldo_kopma` tidak pernah dibuat —
 * migrasi normalize (000016) hanya MODIFY kolom yang SUDAH ADA, sehingga
 * increment('saldo_kopma') di TopupController gagal SQLSTATE[42S22].
 *
 * Kolom lokasi GPS rumah member (latitude/longitude/akurasi/waktu tandai)
 * juga dirujuk model & checkout tapi tidak ada di DB — ditambahkan sekaligus.
 * Aman dijalankan berulang (guard hasColumn).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('anggotas') && !Schema::hasColumn('anggotas', 'saldo_kopma')) {
            Schema::table('anggotas', function (Blueprint $table) {
                // Kopma Point — saldo top-up member untuk belanja online
                $table->decimal('saldo_kopma', 12, 2)->default(0)->after('poin');
            });
        }

        if (Schema::hasTable('anggotas') && !Schema::hasColumn('anggotas', 'latitude')) {
            Schema::table('anggotas', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->after('alamat');
            });
        }
        if (Schema::hasTable('anggotas') && !Schema::hasColumn('anggotas', 'longitude')) {
            Schema::table('anggotas', function (Blueprint $table) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            });
        }
        if (Schema::hasTable('anggotas') && !Schema::hasColumn('anggotas', 'lokasi_akurasi')) {
            Schema::table('anggotas', function (Blueprint $table) {
                $table->decimal('lokasi_akurasi', 10, 2)->nullable()->after('longitude'); // meter
            });
        }
        if (Schema::hasTable('anggotas') && !Schema::hasColumn('anggotas', 'lokasi_ditandai_pada')) {
            Schema::table('anggotas', function (Blueprint $table) {
                $table->timestamp('lokasi_ditandai_pada')->nullable()->after('lokasi_akurasi');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('anggotas')) {
            Schema::table('anggotas', function (Blueprint $table) {
                foreach (['saldo_kopma', 'latitude', 'longitude', 'lokasi_akurasi', 'lokasi_ditandai_pada'] as $c) {
                    if (Schema::hasColumn('anggotas', $c)) {
                        $table->dropColumn($c);
                    }
                }
            });
        }
    }
};
