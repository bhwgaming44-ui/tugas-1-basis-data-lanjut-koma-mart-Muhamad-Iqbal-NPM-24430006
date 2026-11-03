<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** Tabel users memakai PK custom `id_user` (bukan `id`). Tanpa ini,
     *  route binding /pegawai/{user} (ubah & hapus) error 500. */
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'nama_lengkap',
        'jabatan',
        'password',
        'role', // 'admin', 'kasir', 'manajer'
        'foto', // foto profil petugas/kurir mitra (path storage atau URL)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** GPS kurir (tracking chat) — kolom waktu harus datetime, bukan string. */
    protected $casts = [
        'kurir_lokasi_diperbarui_pada' => 'datetime',
    ];

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'id_kasir');
    }
}
