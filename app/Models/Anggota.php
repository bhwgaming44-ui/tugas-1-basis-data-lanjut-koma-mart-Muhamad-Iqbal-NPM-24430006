<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggotas';
    protected $primaryKey = 'id_anggota';

    protected $fillable = [
        'kode_member',
        'nama',
        'alamat',
        'latitude',
        'longitude',
        'lokasi_akurasi',
        'lokasi_ditandai_pada',
        'no_hp',
        'email',
        'password',
        'foto',
        'is_kurir', // flag member terdaftar mode kurir (satu akun: user + kurir)
        'poin',
        'saldo_kopma', // Kopma Point — saldo top-up member untuk belanja online
        'total_belanja',
        'status_aktif',
        'tanggal_daftar',
        'kurir_lat',          // GPS lokasi kurir saat ini (real-time tracking)
        'kurir_lng',
        'kurir_lokasi_diperbarui_pada', // kapan lokasi kurir terakhir diperbarui
    ];

    /** GPS kurir (tracking chat) — kolom waktu harus datetime, bukan string. */
    protected $casts = [
        'kurir_lokasi_diperbarui_pada' => 'datetime',
    ];

    /**
     * Titik rumah sudah ditandai lewat GPS?
     * Hanya dianggap sah bila koordinatnya lengkap dan berada di rentang bumi.
     */
    public function sudahTandaiLokasi(): bool
    {
        return $this->latitude !== null && $this->longitude !== null
            && abs((float) $this->latitude) <= 90
            && abs((float) $this->longitude) <= 180;
    }

    /**
     * Nilai siap pakai untuk Google Maps: "lat,lng" bila titik GPS ada,
     * jika belum ada jatuh kembali ke alamat teks.
     */
    public function getTitikMapsAttribute(): ?string
    {
        if ($this->sudahTandaiLokasi()) {
            return $this->latitude . ',' . $this->longitude;
        }

        return filled($this->alamat) ? $this->alamat : null;
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'id_anggota', 'id_anggota');
    }

    public function kopmaLogs()
    {
        return $this->hasMany(KopmaPointLog::class, 'id_anggota', 'id_anggota');
    }
}
