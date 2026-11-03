<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\KopmaPointLog;
use App\Models\TopupRequest;
use App\Services\QrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * TOP UP KOPMA POINT dengan gerbang pembayaran.
 *
 * Dua jalur yang sengaja dibedakan:
 *  - USER (pelanggan): mengajukan top up -> WAJIB bayar dulu (QRIS dinamis /
 *    transfer / tunai di kasir) -> tekan "Saya Sudah Bayar" -> saldo baru masuk
 *    setelah PETUGAS menyetujui (status: disetujui).
 *  - PETUGAS: mencatat top up tunai langsung di toko -> saldo masuk SEKETIKA
 *    (tanpa verifikasi) dan tetap tercatat di kopma_point_logs.
 *
 * QRIS yang ditampilkan adalah payload EMVCo dinamis (nominal sudah melekat di
 * tag 54) sehingga aplikasi bank / e-wallet langsung menampilkan angka tagihan.
 */
class TopupController extends Controller
{
    private QrisService $qris;

    public function __construct(QrisService $qris)
    {
        $this->qris = $qris;
    }

    /** Daftar metode pembayaran top up yang didukung. */
    private const METODE = ['qris', 'transfer', 'tunai'];

    /** Minimal & maksimal nominal top up (rupiah). */
    private const MIN_NOMINAL = 10000;
    private const MAKS_NOMINAL = 1000000;

    /* ============================ SISI USER ============================ */

    /**
     * User mengajukan top up dari halaman Kopma Point.
     * Saldo BELUM bertambah — user diarahkan ke halaman pembayaran.
     */
    public function ajukan(Request $request)
    {
        if (!session('pelanggan_id')) {
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login terlebih dahulu untuk top up Kopma Point.']);
        }

        $request->validate([
            'nominal' => 'required|integer|min:' . self::MIN_NOMINAL . '|max:' . self::MAKS_NOMINAL,
            'metode'  => 'required|in:' . implode(',', self::METODE),
        ], [], ['nominal' => 'nominal top up', 'metode' => 'metode pembayaran']);

        $anggota = Anggota::findOrFail(session('pelanggan_id'));

        $topup = TopupRequest::create([
            'kode_bayar'  => $this->kodeBayarBaru(),
            'id_anggota'  => $anggota->id_anggota,
            'nominal'     => (int) $request->nominal,
            'metode'      => $request->metode,
            'status'      => 'menunggu_pembayaran',
            'catatan'     => 'Pengajuan top up dari akun member ' . $anggota->kode_member,
        ]);

        return redirect()->route('topup.bayar', $topup->kode_bayar)
            ->with('success', 'Pengajuan top up dibuat. Selesaikan pembayaran untuk menambah saldo.');
    }

    /** Halaman pembayaran top up: QRIS dinamis + instruksi + tombol konfirmasi. */
    public function bayar(string $kode)
    {
        if (!session('pelanggan_id')) {
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login terlebih dahulu.']);
        }

        $topup = TopupRequest::where('kode_bayar', $kode)
            ->where('id_anggota', session('pelanggan_id'))
            ->firstOrFail();

        $anggota = Anggota::findOrFail($topup->id_anggota);

        // QR dinamis: nominal mengikuti pengajuan, referensi = kode bayar.
        $payloadQris = $this->qris->payload((float) $topup->nominal, $topup->kode_bayar);

        return view('toko.kopma-point-bayar', [
            'topup'        => $topup,
            'anggota'      => $anggota,
            'payloadQris'  => $payloadQris,
            'qrisCrcValid' => $this->qris->crcValid($payloadQris),
        ]);
    }

    /**
     * User menekan "Saya Sudah Bayar".
     * Status naik ke menunggu_verifikasi — SALDO BELUM MASUK sampai petugas setuju.
     */
    public function konfirmasiBayar(Request $request, string $kode)
    {
        if (!session('pelanggan_id')) {
            return redirect()->route('pelanggan.login');
        }

        $topup = TopupRequest::where('kode_bayar', $kode)
            ->where('id_anggota', session('pelanggan_id'))
            ->firstOrFail();

        if (!$topup->masihMenunggu()) {
            return redirect()->route('topup.bayar', $topup->kode_bayar)
                ->withErrors(['topup' => 'Pengajuan ini sudah diproses: ' . $topup->labelStatus() . '.']);
        }

        $topup->update([
            'status'       => 'menunggu_verifikasi',
            'dibayar_pada' => now(),
            'catatan'      => trim(($topup->catatan ? $topup->catatan . ' | ' : '')
                . 'User menyatakan sudah bayar via ' . strtoupper($topup->metode)
                . ($request->filled('catatan') ? ' — ' . $request->catatan : '')),
        ]);

        return redirect()->route('topup.bayar', $topup->kode_bayar)
            ->with('success', 'Konfirmasi diterima. Saldo akan masuk setelah petugas memverifikasi pembayaran.');
    }

    /** Endpoint polling: status pengajuan top up (dipakai halaman pembayaran). */
    public function status(string $kode)
    {
        if (!session('pelanggan_id')) {
            return response()->json(['ok' => false, 'pesan' => 'Belum login.'], 401);
        }

        $topup = TopupRequest::where('kode_bayar', $kode)
            ->where('id_anggota', session('pelanggan_id'))
            ->first();

        if (!$topup) {
            return response()->json(['ok' => false, 'pesan' => 'Pengajuan tidak ditemukan.'], 404);
        }

        $anggota = Anggota::find($topup->id_anggota);

        return response()->json([
            'ok'         => true,
            'status'     => $topup->status,
            'label'      => $topup->labelStatus(),
            'warna'      => $topup->warnaStatus(),
            'saldo'      => (float) ($anggota->saldo_kopma ?? 0),
            'saldo_teks' => 'Rp ' . number_format((float) ($anggota->saldo_kopma ?? 0), 0, ',', '.'),
            'catatan'    => $topup->catatan,
            'selesai'    => $topup->status === 'disetujui',
            'ditolak'    => $topup->status === 'ditolak',
        ]);
    }

    /* =========================== SISI PETUGAS =========================== */

    /** Halaman petugas: daftar pengajuan top up + form top up tunai langsung. */
    public function index()
    {
        $menunggu = TopupRequest::with('anggota')
            ->whereIn('status', ['menunggu_pembayaran', 'menunggu_verifikasi'])
            ->orderByRaw("CASE status WHEN 'menunggu_verifikasi' THEN 0 ELSE 1 END")
            ->orderBy('created_at')
            ->get();

        $riwayat = TopupRequest::with(['anggota', 'petugas'])
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get();

        $anggota = Anggota::orderBy('nama')->get();

        return view('petugas.topup', compact('menunggu', 'riwayat', 'anggota'));
    }

    /** Petugas menyetujui: SALDO MASUK + mutasi tercatat (satu transaksi). */
    public function setujui(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:200']);

        $topup = TopupRequest::findOrFail($id);

        if ($topup->status === 'disetujui') {
            return back()->withErrors(['topup' => 'Pengajuan ini sudah disetujui sebelumnya.']);
        }

        DB::transaction(function () use ($topup, $request) {
            Anggota::where('id_anggota', $topup->id_anggota)
                ->increment('saldo_kopma', (float) $topup->nominal);

            KopmaPointLog::create([
                'id_anggota' => $topup->id_anggota,
                'jenis'      => 'topup',
                'metode'     => $topup->metode,
                'nominal'    => (float) $topup->nominal,
                'keterangan' => 'Top up ' . strtoupper($topup->metode) . ' — ' . $topup->kode_bayar
                    . ' (disetujui ' . (session('kasir_nama') ?? 'petugas') . ')'
                    . ($request->filled('catatan') ? ': ' . $request->catatan : ''),
            ]);

            $topup->update([
                'status'     => 'disetujui',
                'id_petugas' => session('kasir_id'),
                'catatan'    => trim(($topup->catatan ? $topup->catatan . ' | ' : '')
                    . 'Disetujui petugas'
                    . ($request->filled('catatan') ? ': ' . $request->catatan : '')),
            ]);
        });

        return back()->with('success', 'Top up ' . $topup->kode_bayar . ' disetujui — saldo Rp '
            . number_format((float) $topup->nominal, 0, ',', '.') . ' sudah masuk.');
    }

    /** Petugas menolak: saldo tidak berubah. */
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:200']);

        $topup = TopupRequest::findOrFail($id);

        if ($topup->status === 'disetujui') {
            return back()->withErrors(['topup' => 'Pengajuan yang sudah disetujui tidak bisa ditolak.']);
        }

        $topup->update([
            'status'     => 'ditolak',
            'id_petugas' => session('kasir_id'),
            'catatan'    => trim(($topup->catatan ? $topup->catatan . ' | ' : '')
                . 'Ditolak petugas'
                . ($request->filled('catatan') ? ': ' . $request->catatan : '')),
        ]);

        return back()->with('success', 'Pengajuan ' . $topup->kode_bayar . ' ditolak.');
    }

    /**
     * PETUGAS mencatat top up TUNAI langsung di toko.
     * Saldo masuk SEKETIKA (tanpa gerbang pembayaran / verifikasi), sesuai aturan
     * "top up oleh petugas = instan". Tetap tercatat di kopma_point_logs.
     */
    public function topupTunai(Request $request)
    {
        $request->validate([
            'id_anggota' => 'required|exists:anggotas,id_anggota',
            'nominal'    => 'required|integer|min:' . self::MIN_NOMINAL . '|max:' . self::MAKS_NOMINAL,
            'catatan'    => 'nullable|string|max:200',
        ], [], ['id_anggota' => 'member', 'nominal' => 'nominal top up']);

        $anggota = Anggota::findOrFail($request->id_anggota);
        $nominal = (int) $request->nominal;

        DB::transaction(function () use ($anggota, $nominal, $request) {
            Anggota::where('id_anggota', $anggota->id_anggota)
                ->increment('saldo_kopma', $nominal);

            KopmaPointLog::create([
                'id_anggota' => $anggota->id_anggota,
                'jenis'      => 'topup',
                'metode'     => 'tunai',
                'nominal'    => $nominal,
                'keterangan' => 'Top up TUNAI di kasir oleh ' . (session('kasir_nama') ?? 'petugas')
                    . ($request->filled('catatan') ? ' — ' . $request->catatan : ''),
            ]);
        });

        return back()->with('success', 'Top up tunai instan Rp ' . number_format($nominal, 0, ',', '.')
            . ' untuk ' . $anggota->nama . ' — saldo langsung masuk.');
    }

    /* ============================== UTILITAS ============================== */

    /** Kode bayar unik: KPM-<tanggal>-<acak> (dipakai juga sebagai referensi QRIS). */
    private function kodeBayarBaru(): string
    {
        do {
            $kode = 'KPM-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        } while (TopupRequest::where('kode_bayar', $kode)->exists());

        return $kode;
    }
}


