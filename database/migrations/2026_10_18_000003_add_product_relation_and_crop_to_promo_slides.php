<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_slides', function (Blueprint $table) {
            // Relasi: promo bisa dikaitkan ke produk tertentu (FK ke barangs).
            // Jika produk dihapus, promo tetap ada (id_barang jadi NULL).
            $table->unsignedBigInteger('id_barang')->nullable()->after('gambar');
            // Posisi tampilan gambar saat dipotong oleh container banner
            // (center | top | bottom | left | right) -> background-position CSS.
            $table->string('crop_fokus', 20)->default('center')->after('aktif');
            // Index agar query slider (aktif + urutan) cepat.
            $table->index(['aktif', 'urutan']);
        });

        // Tambahkan foreign key terpisah agar nama constraint rapi & eksplisit.
        Schema::table('promo_slides', function (Blueprint $table) {
            $table->foreign('id_barang')
                ->references('id_barang')->on('barangs')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('promo_slides', function (Blueprint $table) {
            $table->dropForeign(['id_barang']);
            $table->dropIndex(['aktif', 'urutan']);
            $table->dropColumn(['id_barang', 'crop_fokus']);
        });
    }
};
