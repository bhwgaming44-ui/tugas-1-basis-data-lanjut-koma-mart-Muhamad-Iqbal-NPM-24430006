<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Autentikasi sederhana untuk area Kasir/Petugas (tanpa Breeze/Composer).
 * Login memakai username + password dari tabel users (Hash::check).
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        // MULTI-LOGIN 1 DEVICE: petugas, mitra, pelanggan boleh login bersamaan.
        // Jangan redirect otomatis bila salah satu sesi sudah ada — biarkan user
        // menambah akun lain di tab/halaman yang sama.
        return view('auth.login');
    }

    /* ================== LOGIN MITRA / KURIR (TERPISAH) ==================
       Sesi memakai key 'mitra_*' yang BERBEDA dari 'kasir_*' petugas,
       sehingga petugas dan mitra bisa login bersamaan tanpa bentrok. */
    public function showMitraLogin()
    {
        // MULTI-LOGIN 1 DEVICE: sama seperti login petugas — tidak redirect
        // otomatis agar kurir & petugas & pelanggan bisa aktif bersamaan.
        return view('auth.login');
    }

    public function loginMitra(Request $request)
    {
        // SISTEM TERPISAH 1 TAB = 1 PERAN: login kurir menyimpan sesi mitra_*
        // TANPA menghapus sesi user/petugas, lalu memindahkan MODE AKTIF ke
        // 'kurir' dan LANJUT di tab yang sama (tanpa buka tab baru).
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && $user->role === 'mitra' && Hash::check($request->password, $user->password ?? '')) {
            session([
                'mitra_id'       => $user->id_user,
                'mitra_nama'     => $user->nama_lengkap,
                'mitra_username' => $user->username,
                'mitra_foto'     => $user->foto,
            ]);
            session()->save();
            return redirect()->intended(route('kurir.index'))
                ->with('success', "Selamat bertugas, {$user->nama_lengkap}! (Mode: Kurir)");
        }

        // Bedakan pesan: akun ditemukan tetapi bukan mitra, vs password salah.
        if ($user && $user->role !== 'mitra') {
            return back()->withErrors(['username' => 'Akun ini bukan akun mitra. Silakan gunakan halaman Login Petugas (/kasir/login).'])->withInput();
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
    }

    public function logoutMitra()
    {
        session()->forget(['mitra_id', 'mitra_nama', 'mitra_username', 'mitra_foto']);
        // Keluar dari mode kurir: kembalikan ke peran yang masih login.
        if (session('kasir_id')) session(['mode_aktif' => 'petugas']);
        elseif (session('pelanggan_id')) session(['mode_aktif' => 'user']);
        else session()->forget('mode_aktif');
        return redirect('/masuk#kurir')->with('success', 'Anda telah logout mitra.');
    }

    public function login(Request $request)
    {
        // SISTEM TERPISAH 1 TAB = 1 PERAN: login petugas menyimpan sesi kasir_*
        // TANPA menghapus sesi user/kurir, lalu memindahkan MODE AKTIF ke
        // 'petugas' dan LANJUT di tab yang sama (tanpa buka tab baru).
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password ?? '')) {
            // Akun mitra/kurir TIDAK BOLEH login di halaman petugas — sudah dipisah.
            if ($user->role === 'mitra') {
                return back()->withErrors(['username' => 'Akun mitra tidak bisa login di sini. Silakan gunakan halaman Login Mitra (/kurir/login).'])->withInput();
            }
            // Simpan sesi login kasir
            session([
                'kasir_id'       => $user->id_user,
                'kasir_nama'     => $user->nama_lengkap,
                'kasir_username' => $user->username,
                'kasir_role'     => $user->role,
                'kasir_foto'     => $user->foto,
            ]);
            session()->save();
            return redirect()->intended(route('kasir.index'))
                ->with('success', "Selamat bertugas, {$user->nama_lengkap}! (Mode: Petugas)");
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
    }

    public function logout()
    {
        session()->forget(['kasir_id', 'kasir_nama', 'kasir_username', 'kasir_role', 'kasir_foto']);
        // Keluar dari mode petugas: kembalikan ke peran yang masih login.
        if (session('mitra_id')) session(['mode_aktif' => 'kurir']);
        elseif (session('pelanggan_id')) session(['mode_aktif' => 'user']);
        else session()->forget('mode_aktif');
        return redirect('/masuk#petugas')->with('success', 'Anda telah logout.');
    }

    /**
     * PINDAH PERAN dalam 1 tab (tanpa logout, tanpa tab baru).
     * Contoh: dari mode Petugas -> tekan "User" -> tab yang sama langsung
     * jadi tampilan user (menu & nama ikut mode), sesi ketiganya tetap ada.
     */
    public function gantiMode(string $mode)
    {
        $mode = strtolower($mode);
        if (!in_array($mode, ['user', 'petugas', 'kurir'], true)) {
            return back()->withErrors(['mode' => 'Mode tidak dikenal.']);
        }
        if ($mode === 'user' && !session('pelanggan_id')) {
            return redirect()->route('masuk')->withErrors(['login' => 'Belum ada akun User yang login di device ini.']);
        }
        if ($mode === 'petugas' && !session('kasir_id')) {
            return redirect('/masuk#petugas')->withErrors(['username' => 'Belum ada akun Petugas yang login di device ini.']);
        }
        if ($mode === 'kurir' && !session('mitra_id') && !(session('kurir_aktif') && session('pelanggan_id'))) {
            return redirect('/masuk#kurir')->withErrors(['username' => 'Belum ada akun Kurir yang login di device ini.']);
        }
        session(['mode_aktif' => $mode]);
        session()->save();
        return redirect(\App\Helpers\AkunHelper::homePeran($mode))
            ->with('success', 'Mode aktif: ' . ucfirst($mode) . '.');
    }

    /**
     * AKTIFKAN mode kurir untuk member yang sudah login.
     * Mengatur flag sesi kurir_aktif supaya member bisa berperan sebagai kurir
     * (antar pesanan + chat) tanpa harus logout dari mode user.
     */
    public function aktifkanKurir()
    {
        if (!session('pelanggan_id')) {
            return back()->withErrors(['login' => 'Anda harus login sebagai User terlebih dahulu untuk mengaktifkan mode Kurir.']);
        }
        $anggota = \App\Models\Anggota::find(session('pelanggan_id'));
        if (!$anggota || !$anggota->password || $anggota->status_aktif !== 'aktif') {
            return back()->withErrors(['kurir' => 'Akun Anda tidak memenuhi syarat untuk menjadi kurir.']);
        }
        session([
            'kurir_aktif'   => true,
            'mode_aktif'    => 'kurir',
        ]);
        session()->save();
        return redirect()->route('kurir.index')
            ->with('success', 'Mode Kurir diaktifkan. Anda sekarang dapat menerima dan mengantar pesanan.');
    }

    /**
     * NONAKTIFKAN mode kurir untuk member.
     * Menghapus flag kurir_aktif dan kembali ke mode user (jika masih login).
     */
    public function nonaktifkanKurir()
    {
        session()->forget('kurir_aktif');
        if (session('pelanggan_id')) {
            session(['mode_aktif' => 'user']);
            session()->save();
            return redirect()->route('toko.index')->with('success', 'Mode Kurir dinonaktifkan. Kembali ke mode User.');
        }
        session()->forget('mode_aktif');
        session()->save();
        return redirect('/masuk')->with('success', 'Mode Kurir dinonaktifkan.');
    }
}
