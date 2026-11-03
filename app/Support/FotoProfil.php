<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Utilitas bersama foto profil (member, petugas, kurir mitra).
 * Dipakai PelangganController (lama) & ProfilController (terpadu).
 */
class FotoProfil
{
    /**
     * Simpan hasil upload ke storage/app/public/foto lalu kembalikan
     * path URL publik siap pakai: "storage/foto/xxx.jpg".
     */
    public static function simpanUpload(UploadedFile $file, string $prefixId): string
    {
        $nama = 'profil-' . $prefixId . '-' . time() . '-' . bin2hex(random_bytes(3))
            . '.' . strtolower($file->getClientOriginalExtension());
        $file->storeAs('public/foto', $nama); // storage/app/public/foto/ (via symlink public/storage)

        return 'storage/foto/' . $nama;
    }

    /**
     * Ubah link Google Drive / URL gambar menjadi URL tampilan langsung.
     * Contoh Drive: https://drive.google.com/file/d/ID/view
     *            -> https://drive.google.com/thumbnail?id=ID&sz=w400
     * (Catatan: file Drive harus dibagikan "Siapa saja yang memiliki link".)
     * URL gambar biasa (https://.../.jpg) diterima apa adanya.
     */
    public static function dariTautan(string $url): ?string
    {
        // Link berbagi Google Drive (file/d/ID, open?id=ID, uc?export=view&id=ID)
        if (preg_match('#drive\.google\.com/.*/d/([\w-]{10,})#', $url, $m)
            || preg_match('#drive\.google\.com/open\?id=([\w-]{10,})#', $url, $m)
            || preg_match('#[?&]id=([\w-]{10,})#', $url, $m)) {
            return 'https://drive.google.com/thumbnail?id=' . $m[1] . '&sz=w400';
        }
        // URL gambar langsung
        if (preg_match('#^https?://.+\.(jpg|jpeg|png|webp)(\?.*)?$#i', $url)) {
            return $url;
        }
        return null;
    }
}
