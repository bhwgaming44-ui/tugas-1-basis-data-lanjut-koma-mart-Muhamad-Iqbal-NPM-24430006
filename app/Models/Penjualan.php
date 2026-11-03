<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';
    protected $primaryKey = 'id_penjualan';

    protected $fillable = [
        'no_struk',
        'tanggal_transaksi',
        'id_anggota',
        'id_kasir',
        'id_kurir',           // mitra/kurir staf yang MENGAMBIL pesanan (id_user legacy)
        'id_kurir_anggota',   // kurir dari kalangan MEMBER (id_anggota, model Gojek)
        'kurir_tipe',         // 'anggota' | 'staf' | null
        'kurir_diambil_pada', // kapan pesanan diambil mitra
        'total_bruto',
        'total_diskon',
        'total_netto',
        'nominal_bayar',
        'kembalian',
        'metode_bayar', // 'tunai', 'qris', 'transfer'
        'alamat_antar',
        'latitude_antar',
        'longitude_antar',
        'metode_antar', // 'ambil', 'antar'
        'catatan',
        'status_bayar',   // 'belum_bayar', 'lunas'
        'status_pesanan', // 'menunggu', 'diproses', 'diantar', 'selesai'
        // Midtrans Snap
        'midtrans_order_id',
        'midtrans_snap_token',
        'midtrans_payment_type',
        'midtrans_transaction_time',
        'midtrans_gross_amount',
        'midtrans_signature_key',
        'midtrans_status_code',
        'midtrans_raw_response',
    ];

    protected $casts = [
        'kurir_diambil_pada' => 'datetime',
        'midtrans_transaction_time' => 'datetime',
    ];

    /**
     * Titik GPS tujuan pengantaran sudah tersimpan?
     */
    public function sudahPunyaTitikAntar(): bool
    {
        return $this->latitude_antar !== null && $this->longitude_antar !== null;
    }

    /**
     * Sudah ada mitra/kurir yang mengambil pesanan ini?
     */
    public function sudahAdaKurir(): bool
    {
        return $this->id_kurir !== null || $this->id_kurir_anggota !== null;
    }

    /**
     * Nilai siap pakai untuk Google Maps: "lat,lng" bila titik GPS pesanan ada,
     * jika tidak jatuh kembali ke alamat teks.
     */
    public function getTitikAntarAttribute(): ?string
    {
        if ($this->sudahPunyaTitikAntar()) {
            return $this->latitude_antar . ',' . $this->longitude_antar;
        }

        return filled($this->alamat_antar) ? $this->alamat_antar : null;
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'id_kasir', 'id_user');
    }

    /** Mitra/kurir staf yang mengambil & mengantar pesanan ini (legacy users). */
    public function kurir()
    {
        return $this->belongsTo(User::class, 'id_kurir', 'id_user');
    }

    /** Kurir dari kalangan MEMBER (model Gojek: satu akun user+kurir). */
    public function kurirAnggota()
    {
        return $this->belongsTo(Anggota::class, 'id_kurir_anggota', 'id_anggota');
    }

    /** Nama kurir tampil: member dulu, lalu staf/mitra legacy. */
    public function namaKurir(): string
    {
        // Biarkan Eloquent lazy-load relasi (bukan hanya relationLoaded),
        // agar endpoint yang tidak eager-load tetap menampilkan nama kurir asli.
        if ($this->kurirAnggota) {
            return $this->kurirAnggota->nama;
        }
        if ($this->kurir) {
            return $this->kurir->nama_lengkap;
        }
        return 'Kurir KOPMA MART';
    }

    public function details()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }

    /** Seluruh pesan chat yang dirangkai pada pesanan ini. */
    public function chatPesans()
    {
        return $this->hasMany(ChatPesan::class, 'id_penjualan', 'id_penjualan');
    }
}
