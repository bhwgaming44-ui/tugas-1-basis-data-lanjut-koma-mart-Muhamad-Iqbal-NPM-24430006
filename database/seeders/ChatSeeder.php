<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ChatSeeder
 * -----------------------------------------------------------------------------
 * Mengisi CONTOH PERCAKAPAN antara member (user) dan kurir, supaya fitur chat
 * langsung terlihat saat aplikasi dipresentasikan / dijalankan setelah
 * `php artisan migrate:fresh --seed`.
 *
 * Relasi: chat_pesans.id_anggota -> anggotas.id_anggota
 * Data member (anggotas) diasumsikan SUDAH dibuat oleh KopmaSeeder.
 *
 * Cara pakai:
 *   php artisan db:seed --class=ChatSeeder
 * -----------------------------------------------------------------------------
 */
class ChatSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('chat_pesans')) {
            return;
        }

        // Cegah data dobel kalau seeder dijalankan berulang.
        DB::table('chat_pesans')->delete();

        // Member 1 : Muhamad Iqbal  (MBR-001) — sudah dijawab kurir, ada balasan belum dibaca user
        // Member 2 : Nabila Putri   (MBR-002) — pesan user belum dibaca kurir (badge di sisi kurir)
        // Member 3 : Reza Pratama   (MBR-003) — percakapan selesai / sudah dibaca dua arah
        DB::table('chat_pesans')->insert([
            // ===== Percakapan 1: Muhamad Iqbal =====
            [
                'id_anggota'    => 1,
                'pengirim'      => 'user',
                'nama_pengirim' => 'Muhamad Iqbal',
                'pesan'         => 'Selamat siang, pesanan saya STR-20261010-001 sudah sampai mana ya?',
                'dibaca'        => true, // sudah dibaca kurir
                'created_at'    => '2026-10-10 12:40:00',
                'updated_at'    => '2026-10-10 12:40:00',
            ],
            [
                'id_anggota'    => 1,
                'pengirim'      => 'kurir',
                'nama_pengirim' => 'Rahmat Hidayat',
                'pesan'         => 'Siang Pak Iqbal, pesanan sedang saya bawa. Estimasi 15 menit lagi sampai.',
                'dibaca'        => true, // sudah dibaca user
                'created_at'    => '2026-10-10 12:43:00',
                'updated_at'    => '2026-10-10 12:43:00',
            ],
            [
                'id_anggota'    => 1,
                'pengirim'      => 'kurir',
                'nama_pengirim' => 'Rahmat Hidayat',
                'pesan'         => 'Saya sudah di depan gerbang. Mohon dibukakan ya, Pak.',
                'dibaca'        => false, // BELUM dibaca user -> badge di sisi user
                'created_at'    => '2026-10-10 12:58:00',
                'updated_at'    => '2026-10-10 12:58:00',
            ],

            // ===== Percakapan 2: Nabila Putri =====
            [
                'id_anggota'    => 2,
                'pengirim'      => 'user',
                'nama_pengirim' => 'Nabila Putri',
                'pesan'         => 'Kak, alamat antar saya tolong diarahkan ke kosan sebelah ya, nomor 9.',
                'dibaca'        => false, // BELUM dibaca kurir -> badge di sisi kurir
                'created_at'    => '2026-10-11 09:15:00',
                'updated_at'    => '2026-10-11 09:15:00',
            ],
            [
                'id_anggota'    => 2,
                'pengirim'      => 'user',
                'nama_pengirim' => 'Nabila Putri',
                'pesan'         => 'Barangnya tolong ditaruh di pos depan saja kalau saya belum pulang.',
                'dibaca'        => false,
                'created_at'    => '2026-10-11 09:17:00',
                'updated_at'    => '2026-10-11 09:17:00',
            ],

            // ===== Percakapan 3: Reza Pratama (sudah selesai, dua arah terbaca) =====
            [
                'id_anggota'    => 3,
                'pengirim'      => 'user',
                'nama_pengirim' => 'Reza Pratama',
                'pesan'         => 'Terima kasih, pesanannya sudah diterima dengan lengkap.',
                'dibaca'        => true,
                'created_at'    => '2026-10-09 16:20:00',
                'updated_at'    => '2026-10-09 16:20:00',
            ],
            [
                'id_anggota'    => 3,
                'pengirim'      => 'kurir',
                'nama_pengirim' => 'Dimas Prayogo',
                'pesan'         => 'Terima kasih sudah berbelanja di KOPMA MART, Mas Reza. Ditunggu pesanan berikutnya!',
                'dibaca'        => true,
                'created_at'    => '2026-10-09 16:25:00',
                'updated_at'    => '2026-10-09 16:25:00',
            ],
        ]);
    }
}
