<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_slides', function (Blueprint $table) {
            $table->id();
            $table->string('badge', 60)->nullable();
            $table->string('judul', 150);
            $table->text('sub')->nullable();
            $table->string('cta', 60)->nullable();
            $table->string('url', 300)->nullable();
            $table->text('warna')->nullable(); // gradient CSS
            $table->string('gambar', 300)->nullable(); // upload galeri
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_slides');
    }
};
