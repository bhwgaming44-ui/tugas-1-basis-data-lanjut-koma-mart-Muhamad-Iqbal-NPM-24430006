<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            if (!Schema::hasColumn('penjualans', 'latitude_antar')) {
                $table->decimal('latitude_antar', 10, 7)->nullable()->after('alamat_antar');
            }
            if (!Schema::hasColumn('penjualans', 'longitude_antar')) {
                $table->decimal('longitude_antar', 10, 7)->nullable()->after('latitude_antar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['latitude_antar', 'longitude_antar']);
        });
    }
};