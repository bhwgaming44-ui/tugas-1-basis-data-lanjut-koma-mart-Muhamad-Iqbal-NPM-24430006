<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\KopmaPointLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * KOPMA POINT — saldo top-up member untuk belanja online.
 * - halaman  : cek saldo + riwayat mutasi + form top-up
 * - topup    : tambah saldo (tunai / transfer / QRIS simulasi) + catat mutasi
 */
class KopmaPointController extends Controller
{
    /** Halaman Kopma Point: saldo saat ini + riwayat mutasi. */
    public function index()
    {
        if (!session('pelanggan_id')) {
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login terlebih dahulu untuk membuka Kopma Point.']);
        }

        $anggota = Anggota::findOrFail(session('pelanggan_id'));
        $mutasi  = KopmaPointLog::where('id_anggota', $anggota->id_anggota)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('toko.kopma-point', compact('anggota', 'mutasi'));
    }

    /** Proses top up saldo Kopma Point. */
    public function topup(Request $request)
    {
        if (!session('pelanggan_id')) {
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login terlebih dahulu.']);
        }

        $request->validate([
            'nominal' => 'required|integer|min:10000|max:1000000',
            'metode'  => 'required|in:tunai,transfer,qris',
        ]);

        $anggota = Anggota::findOrFail(session('pelanggan_id'));
        $nominal = (int) $request->nominal;

        DB::transaction(function () use ($anggota, $nominal, $request) {
            Anggota::where('id_anggota', $anggota->id_anggota)
                ->increment('saldo_kopma', $nominal);

            KopmaPointLog::create([
                'id_anggota' => $anggota->id_anggota,
                'jenis'      => 'topup',
                'metode'     => $request->metode,
                'nominal'    => $nominal,
                'keterangan' => 'Top up via ' . strtoupper($request->metode),
            ]);
        });

        return redirect()->route('kopma.point.index')
            ->with('success', 'Top up berhasil! Saldo Kopma Point kamu bertambah Rp ' . number_format($nominal, 0, ',', '.') . '.');
    }
}