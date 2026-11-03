<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoSlide extends Model
{
    use HasFactory;

    protected $table = 'promo_slides';
    protected $guarded = [];

    /** Relasi: promo dikaitkan ke produk (opsional). */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urutan', 'asc');
    }

    /** Nilai background-position CSS dari crop_fokus. */
    public function posisiCrop(): string
    {
        $peta = [
            'center' => 'center',
            'top'    => 'center top',
            'bottom' => 'center bottom',
            'left'   => 'left center',
            'right'  => 'right center',
        ];
        return $peta[$this->crop_fokus ?? 'center'] ?? 'center';
    }
}
