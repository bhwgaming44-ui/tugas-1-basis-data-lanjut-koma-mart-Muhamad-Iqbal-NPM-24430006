<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Pengajuan top up Kopma Point dengan gerbang pembayaran.
 *
 * Alur status:
 *   menunggu_pembayaran -> pengajuan dibuat, user diarahkan ke menu pembayaran
 *                          (QRIS dinamis / transfer / tunai). SALDO BELUM MASUK.
 *   menunggu_verifikasi -> user menekan "Saya Sudah Bayar", menunggu dicek petugas.
 *   disetujui           -> petugas menyetujui, SALDO BERTAMBAH + tercatat di mutasi.
 *   ditolak             -> petugas menolak, saldo tidak berubah.
 *
 * Top up yang dilakukan LANGSUNG oleh petugas (bayar tunai di toko) tidak lewat
 * alur ini — saldo masuk seketika, dan tetap tercatat di kopma_point_logs.
 */
class TopupRequest extends Model
{
    use HasFactory;

    protected $table = 'topup_requests';

    protected $fillable = [
        'kode_bayar',
        'id_anggota',
        'nominal',
        'metode',        // 'qris' | 'transfer' | 'tunai'
        'status',        // lihat daftar status di atas
        'id_petugas',    // petugas yang memproses (nullable)
        'catatan',
        'dibayar_pada',
    ];

    protected $casts = [
        'nominal'      => 'decimal:2',
        'dibayar_pada' => 'datetime',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas', 'id_user');
    }

    /** Label status yang ramah dibaca untuk ditampilkan di UI. */
    public function labelStatus(): string
    {
        return [
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi Petugas',
            'disetujui'           => 'Disetujui — Saldo Masuk',
            'ditolak'             => 'Ditolak',
        ][$this->status] ?? strtoupper((string) $this->status);
    }

    /** Warna pill Bootstrap sesuai status (bg-*). */
    public function warnaStatus(): string
    {
        return [
            'menunggu_pembayaran' => 'warning',
            'menunggu_verifikasi' => 'info',
            'disetujui'           => 'success',
            'ditolak'             => 'danger',
        ][$this->status] ?? 'secondary';
    }

    /** Masih bisa dibayar / dibatalkan user? */
    public function masihMenunggu(): bool
    {
        return in_array($this->status, ['menunggu_pembayaran', 'menunggu_verifikasi'], true);
    }
}
