<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PaymentSetting;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Laporan Keuangan Kalender (Khusus Petugas)
 * - PEMASUKAN   : total penjualan (total_netto) per hari
 * - PENGELUARAN : total modal pembelian (harga_beli x jumlah) barang terjual per hari
 * - PENGHASILAN : pemasukan - pengeluaran (laba kotor)
 */
class LaporanController extends Controller
{
    /**
     * Halaman laporan keuangan + kalender + BANDING 2 PERIODE MANUAL.
     * - ?bulan=YYYY-MM        : periode utama (periode A)
     * - ?banding=YYYY-MM      : periode pembanding (periode B, opsional, boleh beda bulan/tahun)
     * Hasil sudah disesuaikan: selisih Rp + persen otomatis dihitung di view.
     */
    public function index(Request $request)
    {
        [$tahun, $bulan] = $this->resolvePeriode($request);

        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $akhirBulan = (clone $awalBulan)->endOfMonth();

        $harian = $this->ringkasanHarian($awalBulan, $akhirBulan);

        $totalPemasukan   = array_sum(array_column($harian, 'pemasukan'));
        $totalPengeluaran = array_sum(array_column($harian, 'pengeluaran'));
        $totalPenghasilan = $totalPemasukan - $totalPengeluaran;
        $jumlahTransaksi  = Penjualan::whereBetween('tanggal_transaksi', [$awalBulan, $akhirBulan])->count();

        $kalender = $this->bangunKalender($tahun, $bulan, $harian);

        $prev = (clone $awalBulan)->subMonth();
        $next = (clone $awalBulan)->addMonth();

        // ===== PERIODE PEMBANDING MANUAL (boleh beda bulan ATAU beda tahun) =====
        $bandingInput = trim((string) $request->query('banding', ''));
        $banding = null;
        if (preg_match('/^(\d{4})-(\d{2})$/', $bandingInput, $m)) {
            $bTahun = (int) $m[1];
            $bBulan = max(1, min(12, (int) $m[2]));
            $bAwal  = Carbon::create($bTahun, $bBulan, 1)->startOfDay();
            $bAkhir = (clone $bAwal)->endOfMonth();
            $bHarian = $this->ringkasanHarian($bAwal, $bAkhir);
            $bMasuk  = array_sum(array_column($bHarian, 'pemasukan'));
            $bKeluar = array_sum(array_column($bHarian, 'pengeluaran'));
            $banding = [
                'input'      => sprintf('%04d-%02d', $bTahun, $bBulan),
                'tahun'      => $bTahun,
                'bulan'      => $bBulan,
                'awal'       => $bAwal,
                'pemasukan'  => $bMasuk,
                'pengeluaran' => $bKeluar,
                'penghasilan' => $bMasuk - $bKeluar,
                'transaksi'  => Penjualan::whereBetween('tanggal_transaksi', [$bAwal, $bAkhir])->count(),
            ];
        }

        return view('laporan.keuangan', compact(
            'tahun', 'bulan', 'awalBulan', 'kalender', 'harian',
            'totalPemasukan', 'totalPengeluaran', 'totalPenghasilan', 'jumlahTransaksi', 'prev', 'next',
            'banding'
        ));
    }

    /** API ringkasan keuangan bulan berjalan (opsional, untuk grafik). */
    public function data(Request $request)
    {
        [$tahun, $bulan] = $this->resolvePeriode($request);
        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $akhirBulan = (clone $awalBulan)->endOfMonth();

        $harian = $this->ringkasanHarian($awalBulan, $akhirBulan);

        return response()->json([
            'periode'    => $awalBulan->translatedFormat('F Y'),
            'harian'     => $harian,
            'pemasukan'  => array_sum(array_column($harian, 'pemasukan')),
            'pengeluaran' => array_sum(array_column($harian, 'pengeluaran')),
        ]);
    }

    /** Tentukan tahun & bulan dari query ?bulan=YYYY-MM. */
    private function resolvePeriode(Request $request): array
    {
        $bulanInput = (string) $request->query('bulan', '');
        if (preg_match('/^(\d{4})-(\d{2})$/', $bulanInput, $m)) {
            $tahun = (int) $m[1];
            $bulan = max(1, min(12, (int) $m[2]));
        } else {
            $tahun = (int) today()->year;
            $bulan = (int) today()->month;
        }
        return [$tahun, $bulan];
    }

    /**
     * Pemasukan & pengeluaran per tanggal (key: Y-m-d).
     * - Pemasukan   = SUM(total_netto) dari penjualans
     * - Pengeluaran = SUM(harga_beli * jumlah) dari detail transaksi (modal barang terjual)
     */
    private function ringkasanHarian(Carbon $awal, Carbon $akhir): array
    {
        $pemasukan = Penjualan::selectRaw('DATE(tanggal_transaksi) as tgl, SUM(total_netto) as total')
            ->whereBetween('tanggal_transaksi', [$awal->copy()->startOfDay(), $akhir->copy()->endOfDay()])
            ->groupBy('tgl')
            ->pluck('total', 'tgl')
            ->toArray();

        $pengeluaran = PenjualanDetail::query()
            ->join('barangs', 'barangs.id_barang', '=', 'penjualan_details.id_barang')
            ->join('penjualans', 'penjualans.id_penjualan', '=', 'penjualan_details.id_penjualan')
            ->whereBetween('penjualans.tanggal_transaksi', [$awal->copy()->startOfDay(), $akhir->copy()->endOfDay()])
            ->groupBy(DB::raw('DATE(penjualans.tanggal_transaksi)'))
            ->selectRaw('DATE(penjualans.tanggal_transaksi) as tgl, SUM(barangs.harga_beli * penjualan_details.jumlah) as total')
            ->pluck('total', 'tgl')
            ->toArray();

        $hasil = [];
        for ($d = $awal->copy()->startOfMonth(); $d->lte($akhir); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $masuk  = (float) ($pemasukan[$key] ?? 0);
            $keluar = (float) ($pengeluaran[$key] ?? 0);
            $hasil[$key] = [
                'pemasukan'   => $masuk,
                'pengeluaran' => $keluar,
                'penghasilan' => $masuk - $keluar,
            ];
        }
        return $hasil;
    }

    /**
     * Bangun grid kalender: array minggu, tiap minggu 7 hari (Senin..Minggu).
     * Setiap sel berisi info tanggal + ringkasan keuangan hari itu.
     */
    private function bangunKalender(int $tahun, int $bulan, array $harian): array
    {
        $awal = Carbon::create($tahun, $bulan, 1);
        $jumlahHari = $awal->daysInMonth;
        $offsetAwal = ($awal->dayOfWeekIso - 1); // 0 untuk Senin

        $sel = [];
        for ($i = 0; $i < $offsetAwal; $i++) {
            $sel[] = null;
        }
        for ($h = 1; $h <= $jumlahHari; $h++) {
            $tgl = Carbon::create($tahun, $bulan, $h);
            $key = $tgl->format('Y-m-d');
            $data = $harian[$key] ?? ['pemasukan' => 0, 'pengeluaran' => 0, 'penghasilan' => 0];
            $sel[] = [
                'hari'        => $h,
                'tanggal'     => $key,
                'hari_ini'    => $tgl->isToday(),
                'pemasukan'   => $data['pemasukan'],
                'pengeluaran' => $data['pengeluaran'],
                'penghasilan' => $data['penghasilan'],
            ];
        }
        while (count($sel) % 7 !== 0) {
            $sel[] = null;
        }

        return array_chunk($sel, 7);
    }

    /**
     * ANALISIS CERDAS LAPORAN — data rinci + rencana ke depan untuk admin/petugas.
     * Berisi: perbandingan tren bulan ini vs bulan lalu, produk terlaris & kategori,
     * laju jual 30 hari → proyeksi kapan stok habis + saran restock,
     * pola hari ramai/sepi, proyeksi pemasukan bulan depan, dan daftar rekomendasi berprioritas.
     */
    public function analisis(Request $request)
    {
        [$tahun, $bulan] = $this->resolvePeriode($request);
        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $akhirBulan = (clone $awalBulan)->endOfMonth();

        $harian           = $this->ringkasanHarian($awalBulan, $akhirBulan);
        $totalPemasukan   = array_sum(array_column($harian, 'pemasukan'));
        $totalPengeluaran = array_sum(array_column($harian, 'pengeluaran'));
        $totalPenghasilan = $totalPemasukan - $totalPengeluaran;
        $jumlahTransaksi  = Penjualan::whereBetween('tanggal_transaksi', [$awalBulan, $akhirBulan])->count();

        // ===== Perbandingan dengan bulan sebelumnya =====
        $prevAwal        = (clone $awalBulan)->subMonthNoOverflow();
        $prevAkhir       = (clone $prevAwal)->endOfMonth();
        $prevHarian      = $this->ringkasanHarian($prevAwal, $prevAkhir);
        $prevPemasukan   = array_sum(array_column($prevHarian, 'pemasukan'));
        $prevPengeluaran = array_sum(array_column($prevHarian, 'pengeluaran'));
        $prevPenghasilan = $prevPemasukan - $prevPengeluaran;
        $prevTransaksi   = Penjualan::whereBetween('tanggal_transaksi', [$prevAwal, $prevAkhir])->count();

        $persen = function ($baru, $lama) {
            $lama = (float) $lama;
            $baru = (float) $baru;
            if ($lama <= 0) {
                return $baru > 0 ? 100.0 : 0.0;
            }
            return round(($baru - $lama) / $lama * 100, 1);
        };
        $deltaPemasukan   = $persen($totalPemasukan, $prevPemasukan);
        $deltaPenghasilan = $persen($totalPenghasilan, $prevPenghasilan);
        $deltaTransaksi   = $persen($jumlahTransaksi, $prevTransaksi);

        // ===== Hari efektif, rata-rata harian & proyeksi bulan depan =====
        $hariIni      = Carbon::today();
        $hariTerpakai = $akhirBulan->lt($hariIni)
            ? (int) $awalBulan->diffInDays($akhirBulan) + 1
            : ($awalBulan->gt($hariIni) ? 0 : (int) $awalBulan->diffInDays($hariIni) + 1);
        $rataHarian      = $hariTerpakai > 0 ? $totalPemasukan / $hariTerpakai : 0;
        $bulanDepan      = (clone $awalBulan)->addMonthNoOverflow();
        $proyeksiPemasukan = $rataHarian * $bulanDepan->daysInMonth;

        // ===== Hari kosong (tanpa pemasukan) =====
        $hariKosong = 0;
        foreach ($harian as $data) {
            if ($data['pemasukan'] <= 0) {
                $hariKosong++;
            }
        }
        // ===== Produk terlaris periode ini =====
        $topProduk = PenjualanDetail::query()
            ->join('penjualans', 'penjualans.id_penjualan', '=', 'penjualan_details.id_penjualan')
            ->join('barangs', 'barangs.id_barang', '=', 'penjualan_details.id_barang')
            ->leftJoin('kategori_barangs', 'kategori_barangs.id_kategori', '=', 'barangs.id_kategori')
            ->whereBetween('penjualans.tanggal_transaksi', [$awalBulan, $akhirBulan])
            ->groupBy('barangs.nama_barang', 'kategori_barangs.nama_kategori', 'barangs.stok', 'barangs.stok_minimum', 'barangs.harga_beli')
            ->selectRaw('barangs.nama_barang, kategori_barangs.nama_kategori, barangs.stok, barangs.stok_minimum, barangs.harga_beli,
                SUM(penjualan_details.jumlah) as terjual, SUM(penjualan_details.subtotal) as omzet')
            ->orderByDesc('terjual')
            ->limit(10)
            ->get();
        $omzetTeratas = max(1, (float) $topProduk->sum('omzet'));

        // ===== Kontribusi per kategori =====
        $perKategori = PenjualanDetail::query()
            ->join('penjualans', 'penjualans.id_penjualan', '=', 'penjualan_details.id_penjualan')
            ->join('barangs', 'barangs.id_barang', '=', 'penjualan_details.id_barang')
            ->leftJoin('kategori_barangs', 'kategori_barangs.id_kategori', '=', 'barangs.id_kategori')
            ->whereBetween('penjualans.tanggal_transaksi', [$awalBulan, $akhirBulan])
            ->groupBy(DB::raw("COALESCE(kategori_barangs.nama_kategori, 'Lainnya')"))
            ->selectRaw("COALESCE(kategori_barangs.nama_kategori, 'Lainnya') as nama_kategori,
                SUM(penjualan_details.jumlah) as terjual, SUM(penjualan_details.subtotal) as omzet")
            ->orderByDesc('omzet')
            ->get();

        // ===== Metode bayar =====
        $perMetode = Penjualan::whereBetween('tanggal_transaksi', [$awalBulan, $akhirBulan])
            ->groupBy('metode_bayar')
            ->selectRaw('metode_bayar, COUNT(*) as jml, SUM(total_netto) as omzet')
            ->orderByDesc('omzet')
            ->get();

        // ===== Laju jual 30 hari → proyeksi stok habis & saran restock =====
        $winAkhir = (clone $akhirBulan)->endOfDay();
        $winAwal  = (clone $winAkhir)->subDays(29)->startOfDay();
        $lajuRows = PenjualanDetail::query()
            ->join('penjualans', 'penjualans.id_penjualan', '=', 'penjualan_details.id_penjualan')
            ->join('barangs', 'barangs.id_barang', '=', 'penjualan_details.id_barang')
            ->whereBetween('penjualans.tanggal_transaksi', [$winAwal, $winAkhir])
            ->groupBy('barangs.nama_barang', 'barangs.stok', 'barangs.stok_minimum', 'barangs.harga_beli')
            ->selectRaw('barangs.nama_barang, barangs.stok, barangs.stok_minimum, barangs.harga_beli,
                SUM(penjualan_details.jumlah) as terjual30')
            ->get();

        $rencanaRestock = $lajuRows->map(function ($r) {
            $lajuHari = $r->terjual30 / 30;
            $target   = max((int) $r->stok_minimum * 2, (int) ceil($lajuHari * 30));
            $saranQty = max(0, $target - (int) $r->stok);
            return [
                'nama'      => $r->nama_barang,
                'stok'      => (int) $r->stok,
                'minimum'   => (int) $r->stok_minimum,
                'laju'      => round($lajuHari, 2),
                'sisa_hari' => $lajuHari > 0 ? (int) floor($r->stok / $lajuHari) : 999,
                'saran_qty' => $saranQty,
                'est_modal' => $saranQty * (float) $r->harga_beli,
            ];
        })
            ->filter(fn ($r) => $r['sisa_hari'] <= 14 || $r['stok'] <= $r['minimum'])
            ->sortBy('sisa_hari')
            ->take(12)
            ->values();
        // ===== Pola hari dalam seminggu (rata-rata pemasukan & transaksi) =====
        $namaHari  = [1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $trxPerTgl = Penjualan::selectRaw('DATE(tanggal_transaksi) as tgl, COUNT(*) as jml')
            ->whereBetween('tanggal_transaksi', [$awalBulan, $akhirBulan])
            ->groupBy('tgl')->pluck('jml', 'tgl')->toArray();
        $dow = [];
        foreach ($namaHari as $i => $n) {
            $dow[$i] = ['total' => 0.0, 'trx' => 0, 'jumlah_hari' => 0];
        }
        foreach ($harian as $key => $data) {
            $idx = (int) Carbon::parse($key)->dayOfWeekIso;
            $dow[$idx]['total']      += $data['pemasukan'];
            $dow[$idx]['trx']        += (int) ($trxPerTgl[$key] ?? 0);
            $dow[$idx]['jumlah_hari']++;
        }
        $polaHari = [];
        foreach ($dow as $idx => $d) {
            $polaHari[] = [
                'nama'     => $namaHari[$idx],
                'rata'     => $d['jumlah_hari'] > 0 ? $d['total'] / $d['jumlah_hari'] : 0,
                'rata_trx' => $d['jumlah_hari'] > 0 ? $d['trx'] / $d['jumlah_hari'] : 0,
            ];
        }
        $hariRamai = collect($polaHari)->sortByDesc('rata')->first();
        $hariSepi  = collect($polaHari)->filter(fn ($p) => $p['rata'] > 0)->sortBy('rata')->first();

        // ===== Daftar rekomendasi berprioritas (rencana ke depan) =====
        $bobotPrioritas = ['tinggi' => 0, 'sedang' => 1, 'rendah' => 2];
        $rekomendasi = [];
        foreach ($rencanaRestock->take(6) as $r) {
            if ($r['sisa_hari'] <= 7 && $r['saran_qty'] > 0) {
                $batas = Carbon::today()->addDays(max(1, $r['sisa_hari']))->translatedFormat('d M Y');
                $rekomendasi[] = [
                    'prioritas' => 'tinggi', 'ikon' => 'fa-box-open',
                    'judul'     => "Restock darurat: {$r['nama']}",
                    'pesan'     => "Laju jual ±{$r['laju']}/hari dengan stok {$r['stok']} — diperkirakan HABIS sekitar {$batas}."
                        . " Siapkan ±{$r['saran_qty']} unit (estimasi modal Rp " . number_format($r['est_modal'], 0, ',', '.') . ').',
                ];
            } elseif ($r['stok'] <= $r['minimum']) {
                $rekomendasi[] = [
                    'prioritas' => 'sedang', 'ikon' => 'fa-cart-flatbed',
                    'judul'     => "Stok di bawah minimum: {$r['nama']}",
                    'pesan'     => "Sisa {$r['stok']} unit (minimum {$r['minimum']}). Segera tambah persediaan sebelum kehabisan.",
                ];
            }
        }
        if ($deltaPemasukan <= -10) {
            $rekomendasi[] = [
                'prioritas' => 'sedang', 'ikon' => 'fa-arrow-trend-down',
                'judul'     => 'Omzet menurun',
                'pesan'     => "Pemasukan turun {$deltaPemasukan}% dibanding {$prevAwal->translatedFormat('F Y')}."
                    . ' Pertimbangkan promo di hari sepi (' . ($hariSepi['nama'] ?? '-') . ') untuk mendongkrak kembali.',
            ];
        } elseif ($deltaPemasukan >= 10) {
            $rekomendasi[] = [
                'prioritas' => 'rendah', 'ikon' => 'fa-arrow-trend-up',
                'judul'     => 'Omzet tumbuh',
                'pesan'     => "Pemasukan naik {$deltaPemasukan}% vs bulan lalu. Pastikan stok produk terlaris ("
                    . ($topProduk->first()->nama_barang ?? '-') . ') mencukupi untuk bulan depan.',
            ];
        }
        if ($hariKosong >= (int) ceil($akhirBulan->daysInMonth * 0.3)) {
            $rekomendasi[] = [
                'prioritas' => 'sedang', 'ikon' => 'fa-calendar-xmark',
                'judul'     => 'Banyak hari tanpa transaksi',
                'pesan'     => "Ada {$hariKosong} hari kosong bulan ini. Jadwalkan promo atau kegiatan penggalangan member di hari-hari tersebut.",
            ];
        }
        if ($rataHarian > 0) {
            $rekomendasi[] = [
                'prioritas' => 'rendah', 'ikon' => 'fa-calculator',
                'judul'     => "Proyeksi {$bulanDepan->translatedFormat('F Y')}",
                'pesan'     => 'Rata-rata Rp ' . number_format($rataHarian, 0, ',', '.') . '/hari → pemasukan bulan depan diperkirakan ±Rp '
                    . number_format($proyeksiPemasukan, 0, ',', '.') . '. Siapkan modal & stok pendukung sejak awal bulan.',
            ];
        }
        $omzetDigital = (float) $perMetode->whereIn('metode_bayar', ['qris', 'transfer'])->sum('omzet');
        $cfgGateway   = PaymentSetting::getMidtransConfig();
        if ($omzetDigital > 0 && ! $cfgGateway['is_enabled']) {
            $rekomendasi[] = [
                'prioritas' => 'tinggi', 'ikon' => 'fa-credit-card',
                'judul'     => 'Aktifkan pembayaran digital (Midtrans)',
                'pesan'     => 'Transaksi QRIS/transfer bulan ini Rp ' . number_format($omzetDigital, 0, ',', '.')
                    . ' belum terverifikasi otomatis karena gateway belum aktif. Aktifkan di menu Pengaturan Bayar.',
            ];
        }
        usort($rekomendasi, fn ($a, $b) => $bobotPrioritas[$a['prioritas']] <=> $bobotPrioritas[$b['prioritas']]);
        if (! $rekomendasi) {
            $rekomendasi[] = [
                'prioritas' => 'rendah', 'ikon' => 'fa-circle-check',
                'judul'     => 'Semua indikator sehat',
                'pesan'     => 'Tidak ada peringatan stok, tren, maupun pembayaran untuk periode ini. Pertahankan ritme operasional.',
            ];
        }

        $prev = (clone $awalBulan)->subMonth();
        $next = (clone $awalBulan)->addMonth();

        return view('laporan.analisis', compact(
            'tahun', 'bulan', 'prev', 'next', 'awalBulan', 'akhirBulan',
            'totalPemasukan', 'totalPengeluaran', 'totalPenghasilan', 'jumlahTransaksi',
            'prevPemasukan', 'prevPenghasilan', 'prevTransaksi', 'deltaPemasukan', 'deltaPenghasilan', 'deltaTransaksi',
            'harian', 'hariTerpakai', 'rataHarian', 'proyeksiPemasukan', 'bulanDepan', 'hariKosong',
            'topProduk', 'omzetTeratas', 'perKategori', 'perMetode',
            'rencanaRestock', 'polaHari', 'hariRamai', 'hariSepi', 'rekomendasi'
        ));
    }
}
