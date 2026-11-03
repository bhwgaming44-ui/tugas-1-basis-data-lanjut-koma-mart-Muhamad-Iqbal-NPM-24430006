<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Pesan chat antara PELANGGAN (user) dan KURIR.
 * Satu baris = satu pesan. Kolom `pengirim` menandai penulisnya:
 * 'user'  = pelanggan/member yang login (session pelanggan_id)
 * 'kurir' = kurir yang membalas dari aplikasi kurir
 */
class ChatPesan extends Model
{
    use HasFactory;

    protected $table = 'chat_pesans';

    protected $fillable = [
        'id_penjualan',
        'id_anggota',
        'id_kurir',
        'id_kurir_anggota', // kurir dari kalangan member (id_anggota)
        'pengirim',       // 'user' | 'kurir'
        'nama_pengirim',
        'pesan',
        'jenis',          // 'teks' | 'template' | 'lokasi'
        'template_kode',  // kode template jika jenis = template
        'lokasi_lat',     // lat lokasi jika jenis = lokasi
        'lokasi_lng',     // lng lokasi jika jenis = lokasi
        'dibaca',         // true = sudah dibaca oleh lawan bicara
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }
}
