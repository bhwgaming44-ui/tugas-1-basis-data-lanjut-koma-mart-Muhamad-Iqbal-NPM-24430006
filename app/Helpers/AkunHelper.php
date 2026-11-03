<?php

namespace App\Helpers;

use App\Models\Anggota;
use App\Models\User;

/**
 * Helper AKUN TERPADU ala Gojek.
 * ------------------------------------------------------------------
 * Satu ORANG = satu AKUN UTAMA (tabel anggotas) dengan STATUS GANDA:
 *   - mode_user  : belanja, Kopma Point, chat sebagai pembeli
 *   - mode_kurir : ambil & antar pesanan + chat sebagai kurir
 *
 * Dua mode ini BOLEH AKTIF BERSAMAAN (tidak saling logout), cukup lewat
 * flag sesi: pelanggan_id (mode_user) + kurir_aktif (mode_kurir).
 *
 * SISTEM UTAMA = PETUGAS (tabel users: admin/manajer/kasir):
 *   - login via /kasir/login (sesi kasir_*)
 *   - boleh login BERSAMAAN dengan sesi user/kurir (tidak saling blokir)
 *   - otomatis punya akses ke menu User (Member) & Kurir (Antar+Chat)
 */
class AkunHelper
{
    /** Sesi user/member sedang login? */
    public static function isUser(): bool
    {
        return (bool) session('pelanggan_id');
    }

    /** Sesi kurir (mode antar) sedang aktif? */
    public static function isKurir(): bool
    {
        return (bool) session('kurir_aktif');
    }

    /** Sesi petugas (sistem utama) sedang login? */
    public static function isPetugas(): bool
    {
        return (bool) session('kasir_id');
    }

    /**
     * ID kurir efektif untuk query pesanan/chat:
     * prioritas = akun member sendiri (id_anggota) bila mode kurir aktif,
     * lalu akun mitra lama (mitra_id), lalu petugas (kasir_id).
     * Mengembalikan ['tipe' => 'anggota'|'user'|'petugas', 'id' => int|string]|null.
     */
    public static function kurirEfektif(): ?array
    {
        if (session('kurir_aktif') && session('pelanggan_id')) {
            return ['tipe' => 'anggota', 'id' => (int) session('pelanggan_id')];
        }
        if (session('mitra_id')) {
            return ['tipe' => 'user', 'id' => (int) session('mitra_id')];
        }
        // Petugas (sistem utama) otomatis boleh membuka menu Kurir.
        if (session('kasir_id')) {
            return ['tipe' => 'petugas', 'id' => (int) session('kasir_id')];
        }
        return null;
    }

    /** Nama tampil kurir efektif. */
    public static function namaKurirEfektif(): string
    {
        if (session('kurir_aktif') && session('pelanggan_nama')) {
            return (string) session('pelanggan_nama');
        }
        if (session('mitra_nama')) {
            return (string) session('mitra_nama');
        }
        if (session('kasir_nama')) {
            return (string) session('kasir_nama');
        }
        return 'Kurir KOPMA MART';
    }

    /**
     * Cek apakah akun member boleh mengaktifkan mode kurir.
     * Syarat: akun aktif + sudah punya password (akun login penuh).
     */
    public static function bolehJadiKurir(?Anggota $anggota): bool
    {
        if (!$anggota) return false;
        return $anggota->status_aktif === 'aktif' && filled($anggota->password);
    }

    /** Petugas sistem utama (untuk guard menu User & Kurir). */
    public static function petugas(): ?User
    {
        if (!session('kasir_id')) return null;
        return User::find(session('kasir_id'));
    }

    /** Member yang sedang login (mode user). */
    public static function member(): ?Anggota
    {
        if (!session('pelanggan_id')) return null;
        return Anggota::find(session('pelanggan_id'));
    }

    /**
     * MODE AKTIF 1 TAB = 1 PERAN (anti-campur).
     * Walau 3 akun tersimpan di sesi (user+petugas+kurir), yang DITAMPILKAN
     * dan DIANGGAP AKTIF hanya satu: 'user' | 'petugas' | 'kurir'.
     * Login baru otomatis memindahkan mode ke peran itu (lanjut di tab yang
     * sama, tanpa buka tab baru). Pindah peran via tombol tanpa logout.
     */
    public static function modeAktif(): ?string
    {
        $mode = session('mode_aktif');
        if (in_array($mode, ['user', 'petugas', 'kurir'], true)) {
            // Pastikan akun untuk mode itu masih login; kalau sudah logout, fallback.
            if ($mode === 'user' && session('pelanggan_id')) return 'user';
            if ($mode === 'petugas' && session('kasir_id')) return 'petugas';
            if ($mode === 'kurir' && (session('mitra_id') || (session('kurir_aktif') && session('pelanggan_id')))) return 'kurir';
        }
        // Fallback: pilih yang masih login (prioritas terakhir login tersimpan di mode_aktif sudah dicek).
        if (session('kasir_id')) return 'petugas';
        if (session('mitra_id')) return 'kurir';
        if (session('pelanggan_id')) return 'user';
        return null;
    }

    /** Daftar peran yang sedang login (untuk tombol pindah). */
    public static function peranLogin(): array
    {
        $out = [];
        if (session('pelanggan_id')) $out['user'] = (string) (session('pelanggan_nama') ?? 'User');
        if (session('kasir_id')) $out['petugas'] = (string) (session('kasir_nama') ?? 'Petugas');
        if (session('mitra_id')) $out['kurir'] = (string) (session('mitra_nama') ?? 'Kurir');
        elseif (session('kurir_aktif') && session('pelanggan_id')) $out['kurir'] = (string) (session('pelanggan_nama') ?? 'Kurir') . ' (mode kurir)';
        return $out;
    }

    /** URL tujuan tiap peran (dipakai setelah login & pindah peran). */
    public static function homePeran(string $mode): string
    {
        return match ($mode) {
            'petugas' => route('kasir.index'),
            'kurir'   => route('kurir.index'),
            default   => route('toko.index'),
        };
    }
}
