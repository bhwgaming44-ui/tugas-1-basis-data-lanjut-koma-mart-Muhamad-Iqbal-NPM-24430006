<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // chat_pesans: tambah jenis & template_kode
        if (Schema::hasTable('chat_pesans') && !Schema::hasColumn('chat_pesans', 'jenis')) {
            Schema::table('chat_pesans', function (Blueprint $table) {
                $table->enum('jenis', ['teks', 'template', 'lokasi'])->default('teks')->after('pesan');
                $table->string('template_kode', 30)->nullable()->after('jenis');
                $table->decimal('lokasi_lat', 10, 7)->nullable()->after('template_kode');
                $table->decimal('lokasi_lng', 10, 7)->nullable()->after('lokasi_lat');
            });
        }

        // anggotas: tambah kolom lokasi GPS kurir (real-time tracking)
        if (Schema::hasTable('anggotas') && !Schema::hasColumn('anggotas', 'kurir_lat')) {
            Schema::table('anggotas', function (Blueprint $table) {
                $table->decimal('kurir_lat', 10, 7)->nullable();
                $table->decimal('kurir_lng', 10, 7)->nullable()->after('kurir_lat');
                $table->timestamp('kurir_lokasi_diperbarui_pada')->nullable()->after('kurir_lng');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_pesans')) {
            Schema::table('chat_pesans', function (Blueprint $table) {
                if (Schema::hasColumn('chat_pesans', 'jenis')) $table->dropColumn('jenis');
                if (Schema::hasColumn('chat_pesans', 'template_kode')) $table->dropColumn('template_kode');
                if (Schema::hasColumn('chat_pesans', 'lokasi_lat')) $table->dropColumn('lokasi_lat');
                if (Schema::hasColumn('chat_pesans', 'lokasi_lng')) $table->dropColumn('lokasi_lng');
            });
        }
        if (Schema::hasTable('anggotas')) {
            Schema::table('anggotas', function (Blueprint $table) {
                if (Schema::hasColumn('anggotas', 'kurir_lat')) $table->dropColumn('kurir_lat');
                if (Schema::hasColumn('anggotas', 'kurir_lng')) $table->dropColumn('kurir_lng');
                if (Schema::hasColumn('anggotas', 'kurir_lokasi_diperbarui_pada')) $table->dropColumn('kurir_lokasi_diperbarui_pada');
            });
        }
    }
};