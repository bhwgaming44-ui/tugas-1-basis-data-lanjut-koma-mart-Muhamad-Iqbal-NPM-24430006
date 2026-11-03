<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware proteksi area MITRA / KURIR.
 * Terpisah dari akun Petugas (kasir.auth). Mitra adalah akun BIASA dengan
 * role 'mitra' — hanya boleh membuka aplikasi kurir (Antar Pesanan + Chat).
 */
class MitraAuth
{
    public function handle(Request $request, Closure $next)
    {
        // MODEL TERPADU ala Gojek: mode kurir member, mitra legacy, ATAU
        // petugas sistem utama (otomatis punya akses menu Kurir).
        $modeKurirMember = session('kurir_aktif') && session('pelanggan_id');
        if ($modeKurirMember || session('mitra_id') || session('kasir_id')) {
            return $next($request);
        }
        return redirect()->route('mitra.login')
            ->withErrors(['username' => 'Aktifkan mode kurir dulu (masuk sebagai member lalu tekan "Jadi Kurir"), atau login sebagai Mitra/Petugas.']);
    }
}