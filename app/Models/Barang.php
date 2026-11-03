<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'kode_barcode',
        'nama_barang',
        'gambar',
        'id_kategori',
        'harga_beli',
        'harga_jual_umum',
        'harga_jual_anggota',
        'stok',
        'stok_minimum',
        'urutan',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'id_kategori', 'id_kategori');
    }

    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_barang', 'id_barang');
    }

    /** Relasi: promo slide yang dikaitkan ke produk ini. */
    public function promoSlides()
    {
        return $this->hasMany(PromoSlide::class, 'id_barang', 'id_barang');
    }

    // Helper untuk cek ketersediaan stok
    public function isStokMenipis()
    {
        return $this->stok <= $this->stok_minimum;
    }
}
