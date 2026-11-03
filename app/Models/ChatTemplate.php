<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Template teks chat siap pakai untuk user & kurir.
 */
class ChatTemplate extends Model
{
    use HasFactory;

    protected $table = 'chat_templates';

    protected $fillable = [
        'kode',
        'nama',
        'isi',
        'jenis',
        'otomatis',
        'trigger_kata_kunci',
        'urutan',
    ];

    protected $casts = [
        'otomatis' => 'boolean',
    ];

    public function scopeJenis($query, string $jenis)
    {
        return $query->where('jenis', $jenis)->orWhere('jenis', 'both');
    }

    public function scopeOtomatis($query)
    {
        return $query->where('otomatis', true);
    }

    public function scopeUrutan($query)
    {
        return $query->orderBy('urutan')->orderBy('nama');
    }
}