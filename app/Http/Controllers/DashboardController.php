<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAnggota = Anggota::where('status_aktif', 'aktif')->count();
        $totalBarang = Barang::count();
        $totalPenjualanHariIni = Penjualan::whereDate('tanggal_transaksi', today())->sum('total_netto');
        $totalPenjualanBulanIni = Penjualan::whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->sum('total_netto');

        $barangMenipis = Barang::whereColumn('stok', '<=', 'stok_minimum')->get();
        $transaksiTerbaru = Penjualan::with(['anggota', 'kasir'])->latest()->take(5)->get();
        $totalPegawai = \App\Models\User::count();
        $pegawaiList = \App\Models\User::orderBy('id_user', 'asc')->get();
        $semuaBarang = Barang::with('kategori')->orderBy('id_barang', 'asc')->get();
        $kategoriList = \App\Models\KategoriBarang::withCount('barangs')->get();

        return view('dashboard', compact(
            'totalAnggota',
            'totalBarang',
            'totalPenjualanHariIni',
            'totalPenjualanBulanIni',
            'barangMenipis',
            'transaksiTerbaru',
            'totalPegawai',
            'pegawaiList',
            'semuaBarang',
            'kategoriList'
        ));
    }

    /**
     * Dashboard ringkas khusus PETUGAS (dilindungi login).
     * Menampilkan statistik yang sebelumnya ada di dashboard publik:
     * Member Aktif, Penjualan Bulan Ini, Penjualan Hari Ini, Jumlah Barang, Jumlah Pegawai.
     */
    public function petugas()
    {
        $totalAnggota = Anggota::where('status_aktif', 'aktif')->count();
        $totalBarang = Barang::count();
        $totalPenjualanHariIni = Penjualan::whereDate('tanggal_transaksi', today())->sum('total_netto');
        $totalPenjualanBulanIni = Penjualan::whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->sum('total_netto');
        $totalPegawai = \App\Models\User::count();
        $stokMenipis = Barang::whereColumn('stok', '<=', 'stok_minimum')->count();
        $stokHabis = Barang::where('stok', '<=', 0)->count();
        // Daftar produk berstok habis untuk widget "Barang Kosong" petugas
        $barangKosong = Barang::with('kategori')
            ->where('stok', '<=', 0)
            ->orderBy('nama_barang')
            ->get();

        return view('petugas.dashboard', compact(
            'totalAnggota',
            'totalBarang',
            'totalPenjualanHariIni',
            'totalPenjualanBulanIni',
            'totalPegawai',
            'stokMenipis',
            'stokHabis',
            'barangKosong'
        ));
    }
}
