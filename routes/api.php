<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Barang;
use App\Models\Anggota;

/*
|--------------------------------------------------------------------------
| API Routes untuk Keperluan Scan Barcode Kasir & Mobile Anggota
|--------------------------------------------------------------------------
*/

// Cari data barang berdasarkan barcode
Route::get('/barang/{barcode}', function ($barcode) {
    $barang = Barang::where('kode_barcode', $barcode)->first();
    if (!$barang) {
        return response()->json(['message' => 'Barang tidak ditemukan'], 404);
    }
    return response()->json($barang);
});

// Cari data member berdasarkan Kode Member
Route::get('/anggota/{kode}', function ($kode) {
    $anggota = Anggota::where('kode_member', $kode)->first();
    if (!$anggota) {
        return response()->json(['message' => 'Member tidak ditemukan'], 404);
    }
    return response()->json([
        'id_anggota'   => $anggota->id_anggota,
        'kode_member'  => $anggota->kode_member,
        'nama'         => $anggota->nama,
        'status_aktif' => $anggota->status_aktif,
        'poin'         => $anggota->poin,
    ]);
});
