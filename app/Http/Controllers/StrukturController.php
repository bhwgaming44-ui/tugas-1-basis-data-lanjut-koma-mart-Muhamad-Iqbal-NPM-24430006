<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Manajemen Petugas / Pegawai (area Kasir/Petugas).
 * Halaman ini TIDAK ditampilkan ke pelanggan.
 * CRUD: lihat, tambah, ubah, hapus â€” username & password tersimpan (password di-hash).
 */
class StrukturController extends Controller
{
    public function index()
    {
        $pegawais = User::orderBy('id_user', 'asc')->get();
        return view('pegawai.index', compact('pegawais'));
    }

    /** Halaman cetak rapi untuk di-Save-as-PDF dari browser. */
    public function cetak()
    {
        $pegawais = User::orderBy('id_user', 'asc')->get();
        return view('struktur.cetak', compact('pegawais'));
    }

    /** Tambah petugas baru (username & password tersimpan di tabel users). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username'     => 'required|string|max:50|unique:users,username',
            'nama_lengkap' => 'required|string|max:100',
            'jabatan'      => 'nullable|string|max:100',
            'role'         => 'required|in:admin,manajer,kasir,mitra',
            'password'     => 'required|string|min:4|max:100',
        ], [
            'username.unique'      => 'Username sudah dipakai petugas lain.',
            'password.min'         => 'Password minimal 4 karakter.',
        ]);

        User::create([
            'username'     => $data['username'],
            'nama_lengkap' => $data['nama_lengkap'],
            'jabatan'      => $data['jabatan'] ?? 'Staf Operasional',
            'role'         => $data['role'],
            'password'     => Hash::make($data['password']),
        ]);

        return back()->with('success', "Petugas \"{$data['nama_lengkap']}\" berhasil ditambahkan. Username: {$data['username']}.");
    }

    /** Ubah data petugas. Password opsional (kosongkan jika tidak diganti). */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'username'     => ['required','string','max:50', Rule::unique('users','username')->ignore($user->id_user, 'id_user')],
            'nama_lengkap' => 'required|string|max:100',
            'jabatan'      => 'nullable|string|max:100',
            'role'         => 'required|in:admin,manajer,kasir,mitra',
            'password'     => 'nullable|string|min:4|max:100',
        ], [
            'username.unique' => 'Username sudah dipakai petugas lain.',
            'password.min'    => 'Password minimal 4 karakter.',
        ]);

        $user->username     = $data['username'];
        $user->nama_lengkap = $data['nama_lengkap'];
        $user->jabatan      = $data['jabatan'] ?? 'Staf Operasional';
        $user->role         = $data['role'];

        // Ganti password hanya jika diisi
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', "Data petugas \"{$user->nama_lengkap}\" berhasil diperbarui.");
    }

    /** Hapus petugas. */
    public function destroy(User $user)
    {
        // Cegah menghapus diri sendiri saat sedang login
        if (session('kasir_id') == $user->id_user) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun yang sedang Anda pakai login.']);
        }

        $nama = $user->nama_lengkap;
        $user->delete();

        return back()->with('success', "Petugas \"{$nama}\" berhasil dihapus.");
    }
}

