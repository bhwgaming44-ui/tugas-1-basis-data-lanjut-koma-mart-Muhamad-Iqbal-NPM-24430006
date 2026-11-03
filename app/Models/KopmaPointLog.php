<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Riwayat mutasi Kopma Point (saldo top-up member):
 * jenis = 'topup' (saldo masuk) / 'pembayaran' (saldo keluar belanja).
 */
class KopmaPointLog extends Model
{
    use HasFactory;

    protected $table = 'kopma_point_logs';

    protected $fillable = [
        'id_anggota',
        'jenis',
        'metode',
        'nominal',
        'keterangan',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }
}