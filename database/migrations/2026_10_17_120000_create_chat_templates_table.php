<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel TEMPLATE TEKS CHAT: templat pesan siap pakai untuk user & kurir.
     * --------------------------------------------------------------------------
     * `jenis`     = 'user' | 'kurir' | 'both'  (siapa yang boleh pakai template)
     * `otomatis`  = true  -> template ini dikirim OTOMATIS saat lawan bicara
     *               mengirim pesan yang mengandung kata kunci `trigger_kata_kunci`
     * `trigger`   = kata kunci yang memicu auto-balas (contoh: 'status','alamat')
     */
    public function up(): void
    {
        Schema::create('chat_templates', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique(); // contoh: 'STATUS', 'ALAMAT', 'TERIMA_KASIH'
            $table->string('nama', 100); // nama tampil di picker
            $table->text('isi'); // isi template teks
            $table->enum('jenis', ['user', 'kurir', 'both'])->default('both');
            $table->boolean('otomatis')->default(false); // auto-balas?
            $table->string('trigger_kata_kunci', 100)->nullable(); // kata kunci auto-balas
            $table->integer('urutan')->default(0); // urutan tampil di picker
            $table->timestamps();

            $table->index(['jenis', 'otomatis']);
            $table->index('kode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_templates');
    }
};