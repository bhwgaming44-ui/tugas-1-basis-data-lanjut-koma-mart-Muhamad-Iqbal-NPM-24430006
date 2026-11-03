<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_toko', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Nilai default awal
        \Illuminate\Support\Facades\DB::table('pengaturan_toko')->insert([
            ['key' => 'logo_toko', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'tema_warna', 'value' => '#c8102e', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_toko');
    }
};
