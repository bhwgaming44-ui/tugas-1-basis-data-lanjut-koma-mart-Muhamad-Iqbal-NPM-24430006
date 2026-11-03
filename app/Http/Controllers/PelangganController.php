<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Autentikasi PELANGGAN / KASTEMER (bukan petugas).
 * Daftar pertama kali -> akun member dibuat + sesi pelanggan_id.
 * Login: no_hp atau email + password.
 */
class PelangganController extends Controller
{
    public function showDaftar()
    {
        // MULTI-LOGIN 1 DEVICE: pelanggan, petugas (kasir_*), dan mitra (mitra_*)
        // memakai key sesi berbeda sehingga boleh aktif bersamaan — tidak saling blokir.
        if (session('pelanggan_id')) {
            return redirect()->route('toko.index');
        }
        return view('auth.pelanggan', ['mode' => 'daftar']);
    }

    public function daftar(Request $request)
    {
        // MULTI-LOGIN: tidak menolak bila petugas sedang login di device yang sama.

        $data = $request->validate([
            'nama'    => 'required|string|max:100',
            'no_hp'   => 'required|string|max:20|unique:anggotas,no_hp',
            'email'   => 'nullable|email|max:100|unique:anggotas,email',
            'alamat'  => 'nullable|string|max:150',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data['password']       = Hash::make($data['password']);
        $data['kode_member']    = 'KPM-' . strtoupper(Str::random(6));
        $data['tanggal_daftar'] = now()->toDateString();
        $data['status_aktif']   = 'aktif';
        $data['poin']           = 0;
        $data['total_belanja']  = 0;

        // Titik GPS opsional saat daftar: hanya disimpan bila koordinatnya lengkap.
        if (filled($data['latitude'] ?? null) && filled($data['longitude'] ?? null)) {
            $data['lokasi_ditandai_pada'] = now();
        } else {
            $data['latitude']  = null;
            $data['longitude'] = null;
        }

        $anggota = Anggota::create($data);

        session([
            'pelanggan_id'   => $anggota->id_anggota,
            'pelanggan_nama' => $anggota->nama,
            'pelanggan_kode' => $anggota->kode_member,
            'pelanggan_foto' => $anggota->foto,
        ]);

        return redirect()->route('toko.index')
            ->with('success', "Selamat datang, {$anggota->nama}! Akun member {$anggota->kode_member} berhasil dibuat.");
    }

    public function showLogin()
    {
        // MULTI-LOGIN 1 DEVICE: boleh buka halaman login pelanggan walau
        // petugas/mitra sedang login — sesi ketiganya terpisah.
        if (session('pelanggan_id')) {
            return redirect()->route('toko.index');
        }
        return view('auth.pelanggan', ['mode' => 'login']);
    }

    public function login(Request $request)
    {
        // MULTI-LOGIN: tidak menolak bila akun petugas masih login.

        $request->validate([
            'nama'    => 'required|string',
            'password' => 'required|string',
        ]);

        $anggota = Anggota::where('nama', $request->nama)
            ->first();

        if ($anggota && $anggota->password && Hash::check($request->password, $anggota->password)) {
            session([
                'pelanggan_id'   => $anggota->id_anggota,
                'pelanggan_nama' => $anggota->nama,
                'pelanggan_kode' => $anggota->kode_member,
                'pelanggan_foto' => $anggota->foto,
            ]);
            session()->save();
            return redirect()->intended(route('toko.index'))
                ->with('success', "Halo kembali, {$anggota->nama}! (Mode: User)");
        }

        return back()->withErrors(['login' => 'Nama atau password salah.'])->withInput();
    }

    public function logout()
    {
        session()->forget(['pelanggan_id', 'pelanggan_nama', 'pelanggan_kode', 'pelanggan_foto']);
        if (session('kasir_id')) session(['mode_aktif' => 'petugas']);
        elseif (session('mitra_id')) session(['mode_aktif' => 'kurir']);
        else session()->forget('mode_aktif');
        return redirect()->route('toko.index')->with('success', 'Anda telah logout.');
    }

    /**
     * Halaman profil pelanggan (hanya untuk yang sudah login).
     */
    public function profil()
    {
        if (!session('pelanggan_id')) {
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login terlebih dahulu untuk membuka profil.']);
        }

        $anggota = Anggota::findOrFail(session('pelanggan_id'));

        return view('auth.profil', compact('anggota'));
    }

    /**
     * Ganti foto profil — dua pilihan:
     *  1. Upload dari album/perangkat (input file "foto_album")
     *  2. Tempel link Google Drive / URL gambar (input "foto_url")
     * Disimpan ke kolom anggotas.foto (path relatif atau URL eksternal).
     */
    public function ubahFoto(Request $request)
    {
        if (!session('pelanggan_id')) {
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login terlebih dahulu.']);
        }

        $request->validate([
            'foto_album' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'foto_url'   => 'nullable|string|max:500',
        ]);

        $anggota = Anggota::findOrFail(session('pelanggan_id'));
        $foto = null;

        // Opsi 1: file dari album perangkat
        if ($request->hasFile('foto_album')) {
            $file = $request->file('foto_album');
            $nama = 'profil-' . $anggota->id_anggota . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/foto', $nama); // storage/app/public/foto/
            $foto = 'storage/foto/' . $nama;              // path URL publik
        }
        // Opsi 2: link Google Drive / URL gambar lain
        elseif (filled($request->foto_url)) {
            $foto = $this->normalisasiUrlGambar(trim($request->foto_url));
            if ($foto === null) {
                return back()->withErrors(['foto_url' => 'Link tidak valid. Tempel link Google Drive (share) atau URL gambar (diakhiri .jpg/.png).']);
            }
        } else {
            return back()->withErrors(['foto_album' => 'Pilih foto dari album atau tempel link Google Drive dulu.']);
        }

        $anggota->update(['foto' => $foto]);
        session(['pelanggan_foto' => $foto]);

        return redirect()->route('pelanggan.profil')->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * SIMPAN TITIK LOKASI RUMAH (GPS).
     * -------------------------------------------------------------------------
     * Titik diambil dari perangkat user (HP/laptop) memakai Geolocation API
     * browser, jadi yang tersimpan adalah koordinat NYATA dari GPS — bukan
     * alamat teks yang bisa salah/ngawur.
     *
     * Syaratnya lokasi di perangkat memang AKTIF dan user memberi izin:
     * bila GPS mati atau izin ditolak, request tidak akan sampai ke sini
     * (dicegah di sisi browser) — dan bila tetap dikirim tanpa koordinat,
     * server menolak dengan pesan yang jelas.
     */
    public function simpanLokasi(Request $request)
    {
        if (!session('pelanggan_id')) {
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login terlebih dahulu untuk menandai lokasi rumah.']);
        }

        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'akurasi'   => 'nullable|numeric|min:0|max:100000',
            'alamat'    => 'nullable|string|max:150',
        ], [
            'latitude.required'  => 'Titik lokasi belum terdeteksi. Aktifkan GPS/Lokasi di perangkat lalu tekan "Tandai Lokasi Saya" lagi.',
            'longitude.required' => 'Titik lokasi belum terdeteksi. Aktifkan GPS/Lokasi di perangkat lalu tekan "Tandai Lokasi Saya" lagi.',
            'latitude.between'   => 'Koordinat lokasi tidak valid.',
            'longitude.between'  => 'Koordinat lokasi tidak valid.',
        ]);

        $anggota = Anggota::findOrFail(session('pelanggan_id'));

        $perubahan = [
            'latitude'            => round((float) $request->latitude, 7),
            'longitude'           => round((float) $request->longitude, 7),
            'lokasi_akurasi'      => filled($request->akurasi) ? round((float) $request->akurasi, 2) : null,
            'lokasi_ditandai_pada' => now(),
        ];

        // Alamat teks boleh diperbarui sekalian (opsional, tidak wajib).
        if (filled($request->alamat)) {
            $perubahan['alamat'] = $request->alamat;
        }

        $anggota->update($perubahan);

        return redirect()->route('pelanggan.profil')
            ->with('success', 'Lokasi rumah berhasil ditandai: '
                . $anggota->latitude . ', ' . $anggota->longitude
                . '. Kurir akan diarahkan ke titik ini.');
    }

    /**
     * Ubah link Google Drive menjadi URL gambar langsung yang bisa ditampilkan.
     * Contoh: https://drive.google.com/file/d/ID/view?sharing -> https://drive.google.com/thumbnail?id=ID&sz=w400
     * URL gambar biasa (https://.../.jpg) diterima apa adanya.
     */
    private function normalisasiUrlGambar(string $url): ?string
    {
        // Logika dipindah ke App\Support\FotoProfil agar dipakai bersama
        // oleh ProfilController (foto profil terpadu semua peran).
        return \App\Support\FotoProfil::dariTautan($url);
    }
}
