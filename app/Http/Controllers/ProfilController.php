<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User;
use App\Support\FotoProfil;
use Illuminate\Http\Request;

/**
 * Foto profil TERPADU untuk semua peran:
 *  - mode 'user'    -> member (tabel anggotas, kolom foto)
 *  - mode 'petugas' -> admin/kasir/manajer (tabel users, kolom foto)
 *  - mode 'kurir'   -> kurir member (anggotas) ATAU kurir mitra (users)
 *
 * Sumber foto: upload file (laptop/album HP) atau link Google Drive/URL.
 */
class ProfilController extends Controller
{
    /** POST /profil/foto/ubah — simpan foto profil peran yang sedang aktif. */
    public function simpanFoto(Request $request)
    {
        $akun = $this->akunAktif();
        if ($akun === null) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
            }
            return redirect()->route('masuk')->withErrors(['login' => 'Silakan login terlebih dahulu.']);
        }

        $request->validate([
            'foto_album' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'foto_url'   => 'nullable|string|max:500',
        ], [
            'foto_album.image' => 'File harus berupa gambar.',
            'foto_album.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'foto_album.max'   => 'Ukuran foto maksimal 2 MB.',
        ]);

        $foto = null;

        // Sumber 1: file dari laptop / album HP
        if ($request->hasFile('foto_album')) {
            $foto = FotoProfil::simpanUpload($request->file('foto_album'), $akun['tipe'] . '-' . $akun['id']);
        }
        // Sumber 2: link Google Drive (share) atau URL gambar
        elseif (filled($request->foto_url)) {
            $foto = FotoProfil::dariTautan(trim($request->foto_url));
            if ($foto === null) {
                $msg = 'Link tidak valid. Tempel link Google Drive (bagikan "Siapa saja yang memiliki link") atau URL gambar (.jpg/.png).';
                if ($request->expectsJson()) {
                    return response()->json(['ok' => false, 'message' => $msg], 422);
                }
                return back()->withErrors(['foto_url' => $msg]);
            }
        } else {
            $msg = 'Pilih foto dari file/album atau tempel link Google Drive dulu.';
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['foto_album' => $msg]);
        }

        $akun['model']->update(['foto' => $foto]);
        session([$akun['sesi_foto'] => $foto]); // pelanggan_foto | kasir_foto | mitra_foto

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'foto' => $foto]);
        }
        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    /** POST /profil/nama/ubah — ganti nama tampilan peran yang sedang aktif (user/petugas/kurir). */
    public function ubahNama(Request $request)
    {
        $akun = $this->akunAktif();
        if ($akun === null) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
            }
            return redirect()->route('masuk')->withErrors(['login' => 'Silakan login terlebih dahulu.']);
        }

        $request->validate([
            'nama' => 'required|string|min:2|max:100',
        ], [
            'nama.min' => 'Nama minimal 2 karakter.',
            'nama.max' => 'Nama maksimal 100 karakter.',
        ]);

        $namaBaru = trim(preg_replace('/\s+/', ' ', $request->nama));
        $kolom = $akun['tipe'] === 'anggota' ? 'nama' : 'nama_lengkap';
        $akun['model']->update([$kolom => $namaBaru]);

        // Kunci sesi nama mengikuti kunci sesi foto (kasir_* | mitra_* | pelanggan_*).
        $sesiNama = [
            'kasir_foto'    => 'kasir_nama',
            'mitra_foto'    => 'mitra_nama',
            'pelanggan_foto'=> 'pelanggan_nama',
        ][$akun['sesi_foto']] ?? 'pelanggan_nama';
        session([$sesiNama => $namaBaru]);
        session()->save();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'nama' => $namaBaru]);
        }
        return back()->with('success', 'Nama profil berhasil diubah menjadi "' . $namaBaru . '".');
    }

    /**
     * Akun mana yang difoto? Mengikuti MODE AKTIF tab (anti-campur peran).
     * @return array{model: Anggota|User, tipe: string, id: int, sesi_foto: string}|null
     */
    private function akunAktif(): ?array
    {
        $mode = session('mode_aktif') ?? 'user';

        if ($mode === 'petugas' && session('kasir_id')) {
            $u = User::find(session('kasir_id'));
            if ($u) {
                return ['model' => $u, 'tipe' => 'user', 'id' => (int) $u->id_user, 'sesi_foto' => 'kasir_foto'];
            }
        }

        if ($mode === 'kurir') {
            if (session('kurir_aktif') && session('pelanggan_id')) {
                $a = Anggota::find(session('pelanggan_id'));
                if ($a) {
                    return ['model' => $a, 'tipe' => 'anggota', 'id' => (int) $a->id_anggota, 'sesi_foto' => 'pelanggan_foto'];
                }
            }
            if (session('mitra_id')) {
                $u = User::find(session('mitra_id'));
                if ($u) {
                    return ['model' => $u, 'tipe' => 'user', 'id' => (int) $u->id_user, 'sesi_foto' => 'mitra_foto'];
                }
            }
        }

        if (session('pelanggan_id')) {
            $a = Anggota::find(session('pelanggan_id'));
            if ($a) {
                return ['model' => $a, 'tipe' => 'anggota', 'id' => (int) $a->id_anggota, 'sesi_foto' => 'pelanggan_foto'];
            }
        }

        return null;
    }
}
