<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanToko extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_toko';
    protected $guarded = ['key'];

    public static function ambil($key, $default = null)
    {
        $row = self::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function simpan($key, $value)
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
