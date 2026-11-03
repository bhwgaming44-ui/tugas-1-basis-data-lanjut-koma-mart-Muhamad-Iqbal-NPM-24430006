<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel CHAT PESAN: percakapan antara PELANGGAN (user) dan KURIR.
 * -----------------------------------------------------------------------------
 * Satu baris = satu pesan. Kolom `pengirim` menandai penulisnya:
 *   'user'  = pelanggan/member yang login (session pelanggan_id)
 *   'kurir' = kurir/petugas yang membalas dari aplikasi kurir
 *
 * Satu percakapan diidentifikasi oleh `id_anggota` (satu member = satu ruang
 * chat), sehingga kurir cukup memilih member lalu membalas.
 *
 * Transport pengiriman pesan memakai POLLING (tanpa WebSocket / tanpa server
 * tambahan), jadi tabel ini adalah satu-satunya sumber data.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Tabel mungkin sudah dibuat manual di database lama -> jangan diulang.
        if (Schema::hasTable('chat_pesans')) {
            return;
        }

        Schema::create('chat_pesans', function (Blueprint $table) {
            $table->id();

            // Rangkaian chat dirangkai per-PESANAN ala Gojek (id_penjualan):
            // user mengobrol dengan kurir ASLI yang mengambil pesanannya.
            $table->unsignedBigInteger('id_penjualan')->nullable();
            $table->unsignedBigInteger('id_kurir')->nullable(); // mitra pengantar (id_user)

            // Pemilik percakapan (member/pelanggan). Ikut terhapus bila member dihapus.
            $table->unsignedBigInteger('id_anggota');
            $table->foreign('id_anggota')
                ->references('id_anggota')->on('anggotas')
                ->onDelete('cascade');

            $table->enum('pengirim', ['user', 'kurir']); // penulis pesan
            $table->string('nama_pengirim', 100)->nullable(); // nama tampil di gelembung chat
            $table->text('pesan');
            $table->boolean('dibaca')->default(false); // true = sudah dibaca lawan bicara

            $table->timestamps();

            // Mempercepat polling: ambil pesan terakhir satu percakapan + hitung yang belum dibaca.
            $table->index(['id_anggota', 'id']);
            $table->index(['id_penjualan', 'id']);
            $table->index(['id_kurir', 'dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_pesans');
    }
};
