<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware proteksi area Kasir/Petugas.
 * Redirect ke halaman login bila belum ada sesi kasir_id.
 * Akun mitra (role 'mitra') DIBLOKIR dari area petugas — sudah dipisah.
 */
class KasirAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('kasir_id')) {
            return redirect()->route('kasir.login')
                ->withErrors(['username' => 'Silakan login terlebih dahulu untuk mengakses area kasir.']);
        }
        return $next($request);
    }
}
