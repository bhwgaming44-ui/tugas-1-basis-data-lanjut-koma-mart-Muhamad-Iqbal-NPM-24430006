<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Barang;
use App\Models\KopmaPointLog;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Belanja online langsung dari website (TANPA WhatsApp):
 * keranjang (session) -> checkout -> halaman pembayaran website
 * -> status pesanan + pengantaran ke rumah via aplikasi kurir.
 */
class BelanjaController extends Controller
{
    /** Tambah produk ke keranjang (session: id_barang => qty). */
    public function tambah(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|integer|exists:barangs,id_barang',
            'qty'       => 'nullable|integer|min:1|max:99',
        ]);

        $cart = session('keranjang', []);
        $id   = (int) $request->id_barang;
        $qty  = (int) ($request->qty ?? 1);

        $barang = Barang::find($id);
        if (!$barang || $barang->stok < 1) {
            return back()->with('success', 'Maaf, produk tersebut sedang habis.');
        }

        $cart[$id] = min(99, ($cart[$id] ?? 0) + $qty);
        session(['keranjang' => $cart]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => "\"{$barang->nama_barang}\" berhasil ditambahkan ke keranjang.",
                'total_cart' => array_sum($cart),
                'id_barang'  => $id,
                'qty'        => $cart[$id],
            ]);
        }

        if ($request->input('stay') === '1') {
            return back()->with('success', "\"{$barang->nama_barang}\" berhasil dimasukkan ke keranjang.");
        }

        return redirect()->route('keranjang.index')
            ->with('success', "\"{$barang->nama_barang}\" masuk keranjang.");
    }

    /** Halaman keranjang belanja. */
    public function keranjang()
    {
        $cart    = session('keranjang', []);
        $barangs = Barang::with('kategori')->whereIn('id_barang', array_keys($cart))->get();
        $total   = $barangs->sum(fn ($b) => $b->harga_jual_anggota * ($cart[$b->id_barang] ?? 0));
        $anggota = session('pelanggan_id') ? Anggota::find(session('pelanggan_id')) : null;

        return view('toko.keranjang', compact('cart', 'barangs', 'total', 'anggota'));
    }

    /** Update jumlah item di keranjang (qty=0 -> hapus). */
    public function update(Request $request)
    {
        $request->validate(['qty' => 'required|array', 'qty.*' => 'integer|min:0|max:99']);
        $cart = session('keranjang', []);
        foreach ($request->qty as $id => $qty) {
            $id = (int) $id;
            if (!isset($cart[$id])) continue;
            if ((int) $qty <= 0) unset($cart[$id]);
            else $cart[$id] = (int) $qty;
        }
        session(['keranjang' => $cart]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    /** Hapus satu item dari keranjang. */
    public function hapus(Request $request)
    {
        $request->validate(['id_barang' => 'required|integer']);
        $cart = session('keranjang', []);
        unset($cart[(int) $request->id_barang]);
        session(['keranjang' => $cart]);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }


    /** Checkout: simpan pesanan + kurangi stok (transaksi ACID). */
    public function checkout(Request $request)
    {
        if (!session('pelanggan_id')) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Silakan login / daftar dulu sebelum checkout.'], 403);
            }
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login / daftar dulu sebelum checkout.']);
        }

        $request->validate([
            'metode_bayar' => 'required|in:tunai,qris,transfer,kopma_point,midtrans',
            'metode_antar' => 'required|in:ambil,antar',
            'alamat'       => 'nullable|string|max:255',
            // Titik GPS tidak lagi wajib (UI tandai-lokasi dihapus dari keranjang);
            // koordinat dikirim hanya bila tersedia, opsional untuk "antar ke rumah".
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'akurasi'      => 'nullable|numeric|min:0|max:100000',
            'catatan'      => 'nullable|string|max:255',
        ], [
            'latitude.required_if'  => 'Titik lokasi rumah belum ditandai. Buka Keranjang -> "Tandai Lokasi Saya (GPS)" dengan GPS/Lokasi perangkat AKTIF, lalu checkout lagi.',
            'longitude.required_if' => 'Titik lokasi rumah belum ditandai. Buka Keranjang -> "Tandai Lokasi Saya (GPS)" dengan GPS/Lokasi perangkat AKTIF, lalu checkout lagi.',
            'latitude.between'      => 'Koordinat lokasi tidak valid.',
            'longitude.between'     => 'Koordinat lokasi tidak valid.',
        ]);

        $cart = session('keranjang', []);
        if (empty($cart)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Keranjang masih kosong.'], 422);
            }
            return back()->withErrors(['error' => 'Keranjang masih kosong.']);
        }

        $anggota    = Anggota::find(session('pelanggan_id'));
        $totalNetto = 0;
        $totalBruto = 0;
        $detailRecords = [];

        DB::beginTransaction();
        try {
            foreach ($cart as $id => $qty) {
                $barang = Barang::lockForUpdate()->findOrFail($id);
                if ($barang->stok < $qty) {
                    throw new \Exception("Stok '{$barang->nama_barang}' tidak mencukupi! Sisa: {$barang->stok}");
                }
                $subtotal = $barang->harga_jual_anggota * $qty;
                $totalNetto += $subtotal;
                $totalBruto += $barang->harga_jual_umum * $qty;
                $barang->decrement('stok', $qty);
                $detailRecords[] = [
                    'id_barang'    => $barang->id_barang,
                    'jumlah'       => $qty,
                    'harga_satuan' => $barang->harga_jual_anggota,
                    'subtotal'     => $subtotal,
                ];
            }

            // Midtrans: status_bayar sengaja 'belum_bayar' — baru menjadi 'lunas' setelah
            // settlement Snap terkonfirmasi lewat cek-status (polling) atau webhook.
            $lunas = ($request->metode_bayar === 'tunai' && $request->metode_antar === 'ambil');

            // Bayar pakai saldo Kopma Point: saldo harus cukup untuk total belanja,
            // dipotong dalam transaksi yang sama + dicatat ke riwayat mutasi, langsung lunas.
            // Catatan: nilai yang disimpan di kolom enum DB adalah 'koppay' (sudah ada di enum),
            // 'kopma_point' hanya nama tampilan di form checkout.
            if ($request->metode_bayar === 'kopma_point') {
                $anggotaRow  = Anggota::lockForUpdate()->find($anggota->id_anggota);
                $saldoKopma  = (float) $anggotaRow->saldo_kopma;
                if ($saldoKopma < $totalNetto) {
                    throw new \Exception('Saldo Kopma Point tidak mencukupi. Saldo Rp '
                        . number_format($saldoKopma, 0, ',', '.') . ', total belanja Rp '
                        . number_format($totalNetto, 0, ',', '.') . '. Top up dulu di menu Kopma Point.');
                }
                Anggota::where('id_anggota', $anggota->id_anggota)
                    ->decrement('saldo_kopma', $totalNetto);
                $lunas = true;
            }

            $penjualan = Penjualan::create([
                'no_struk'          => 'WEB-' . date('YmdHis') . '-' . strtoupper(Str::random(4)),
                'tanggal_transaksi' => now(),
                'id_anggota'        => $anggota->id_anggota,
                'id_kasir'          => 1, // sistem / admin
                'total_bruto'       => $totalBruto,
                'total_diskon'      => max(0, $totalBruto - $totalNetto),
                'total_netto'       => $totalNetto,
                'nominal_bayar'     => $totalNetto,
                'kembalian'         => 0,
                'metode_bayar'      => $request->metode_bayar === 'kopma_point' ? 'koppay' : $request->metode_bayar,
                'alamat_antar'      => $request->alamat,
                'latitude_antar'    => filled($request->latitude) ? round((float) $request->latitude, 7) : null,
                'longitude_antar'   => filled($request->longitude) ? round((float) $request->longitude, 7) : null,
                'metode_antar'      => $request->metode_antar,
                'catatan'           => $request->catatan,
                'status_bayar'      => $lunas ? 'lunas' : 'belum_bayar',
                'status_pesanan'    => $lunas ? 'diproses' : 'menunggu',
            ]);

            foreach ($detailRecords as $detail) {
                $detail['id_penjualan'] = $penjualan->id_penjualan;
                PenjualanDetail::create($detail);
            }

            Anggota::where('id_anggota', $anggota->id_anggota)->update([
                'total_belanja' => DB::raw('total_belanja + ' . (float) $totalNetto),
                'poin'          => DB::raw('poin + ' . (int) floor($totalNetto / 1000)),
            ]);

            // Catat mutasi saldo Kopma Point untuk pesanan ini
            if ($request->metode_bayar === 'kopma_point') {
                KopmaPointLog::create([
                    'id_anggota' => $anggota->id_anggota,
                    'jenis'      => 'pembayaran',
                    'metode'     => 'kopma_point',
                    'nominal'    => $totalNetto,
                    'keterangan' => 'Pembayaran pesanan ' . $penjualan->no_struk,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Checkout gagal: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Checkout gagal: ' . $e->getMessage()]);
        }

        session()->forget('keranjang');

        // Untuk Midtrans: frontend meminta snap_token terpisah lewat AJAX.
        if ($request->expectsJson()) {
            return response()->json([
                'success'      => true,
                'penjualan_id' => $penjualan->id_penjualan,
                'no_struk'     => $penjualan->no_struk,
                'metode_bayar' => $penjualan->metode_bayar,
                'total'        => (float) $penjualan->total_netto,
                'message'      => 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.',
            ]);
        }

        // Midtrans: bawa auto_bayar=1 supaya popup pembayaran langsung terbuka
        // otomatis begitu halaman bayar dimuat (user baru klik "Bayar Sekarang").
        $param = $penjualan->metode_bayar === 'midtrans' ? ['auto_bayar' => 1] : [];

        return redirect()->route('belanja.bayar', array_merge(['id' => $penjualan->id_penjualan], $param));
    }

    /** Halaman pembayaran di website (transfer / QRIS / tunai). */
    public function bayar($id)
    {
        $penjualan = Penjualan::with('details.barang')
            ->where('id_anggota', session('pelanggan_id'))
            ->findOrFail($id);

        return view('toko.bayar', compact('penjualan'));
    }

    /** Konfirmasi "Saya sudah bayar" -> status lunas, pesanan diproses. */
    public function konfirmasiBayar(Request $request, $id)
    {
        $penjualan = Penjualan::where('id_anggota', session('pelanggan_id'))->findOrFail($id);
        $request->validate(['bukti' => 'nullable|string|max:255']);

        $penjualan->update([
            'status_bayar'   => 'lunas',
            'status_pesanan' => 'diproses',
        ]);

        return redirect()->route('pesanan.index')
            ->with('success', "Pembayaran pesanan {$penjualan->no_struk} terkonfirmasi. Pesanan segera diproses & diantar.");
    }

    /**
     * Ubah metode pembayaran langsung di halaman bayar (tanpa input ulang checkout).
     * Hanya pesanan milik sendiri & BELUM lunas. Kopma Point: saldo dipotong dalam
     * transaksi & pesanan langsung lunas (sama seperti alur checkout).
     */
    public function ubahMetodeBayar(Request $request, $id)
    {
        $penjualan = Penjualan::where('id_anggota', session('pelanggan_id'))->findOrFail($id);

        $request->validate([
            'metode_bayar' => 'required|in:tunai,transfer,qris,kopma_point,midtrans',
        ]);

        if ($penjualan->status_bayar === 'lunas') {
            return back()->withErrors(['error' => 'Pesanan sudah lunas — metode pembayaran tidak bisa diubah lagi.']);
        }

        $metode = $request->metode_bayar;
        $label  = [
            'tunai'       => 'Tunai (bayar di tempat)',
            'transfer'    => 'Transfer Bank',
            'qris'        => 'QRIS',
            'kopma_point' => 'Kopma Point',
            'midtrans'    => 'Pembayaran Online (Midtrans)',
        ][$metode];

        // --- Kopma Point: potong saldo dulu (transaksi aman), pesanan langsung lunas ---
        if ($metode === 'kopma_point') {
            try {
                DB::beginTransaction();

                $anggota = Anggota::lockForUpdate()->find(session('pelanggan_id'));
                $saldo   = $anggota ? (float) $anggota->saldo_kopma : 0;

                if (!$anggota || $saldo < (float) $penjualan->total_netto) {
                    DB::rollBack();
                    return back()->withErrors(['error' => 'Saldo Kopma Point tidak cukup (saldo Rp '
                        . number_format($saldo, 0, ',', '.') . ', total belanja Rp '
                        . number_format($penjualan->total_netto, 0, ',', '.') . '). Top up dulu di menu Kopma Point.']);
                }

                Anggota::where('id_anggota', $anggota->id_anggota)
                    ->decrement('saldo_kopma', $penjualan->total_netto);

                $penjualan->update([
                    'metode_bayar'   => 'koppay',
                    'status_bayar'   => 'lunas',
                    'status_pesanan' => 'diproses',
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Gagal memotong saldo Kopma Point: ' . $e->getMessage()]);
            }

            return redirect()->route('belanja.bayar', $penjualan->id_penjualan)
                ->with('success', 'Metode pembayaran diubah ke Kopma Point — saldo Rp '
                    . number_format($penjualan->total_netto, 0, ',', '.') . ' dipotong, pesanan langsung LUNAS.');
        }

        // --- Metode lain: cukup ganti kolom, data pesanan (item, alamat, kurir) tetap ---
        $penjualan->update(['metode_bayar' => $metode]);

        // Pindah ke Midtrans? Bawa auto_bayar=1 supaya popup Snap langsung terbuka.
        $param = $metode === 'midtrans' ? ['auto_bayar' => 1] : [];

        return redirect()->route('belanja.bayar', array_merge(['id' => $penjualan->id_penjualan], $param))
            ->with('success', "Metode pembayaran diubah ke {$label}. Data pesanan tetap tersimpan — tidak perlu input ulang.");
    }

    /** Riwayat + tracking status pesanan pelanggan. */
    public function pesanan()
    {
        $pesanan = Penjualan::with('details.barang')
            ->where('id_anggota', session('pelanggan_id'))
            ->orderByDesc('tanggal_transaksi')
            ->get();

        return view('toko.pesanan', compact('pesanan'));
    }

    /**
     * Batalkan / hapus pesanan (salah pesan) -> detail dihapus,
     * stok barang DIKEMBALIKAN (transaksi ACID).
     */
    public function batalkan($id)
    {
        if (!session('pelanggan_id')) {
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login dulu untuk membatalkan pesanan.']);
        }

        $penjualan = Penjualan::with('details')
            ->where('id_anggota', session('pelanggan_id'))
            ->findOrFail($id);

        if (in_array($penjualan->status_pesanan, ['diantar', 'selesai'])) {
            return back()->withErrors(['error' => 'Pesanan sudah dalam pengantaran / selesai dan tidak bisa dibatalkan. Hubungi kurir atau toko.']);
        }

        try {
            DB::beginTransaction();

            foreach ($penjualan->details as $detail) {
                Barang::where('id_barang', $detail->id_barang)
                    ->increment('stok', (int) $detail->jumlah);
            }

            $penjualan->details()->delete();
            $penjualan->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membatalkan pesanan: ' . $e->getMessage()]);
        }

        // Redirect ke daftar pesanan (bukan back()) agar aman juga saat dibatalkan
        // dari halaman bayar — pesanan sudah terhapus, halaman bayarnya jadi 404.
        return redirect()->route('pesanan.index')
            ->with('success', "Pesanan {$penjualan->no_struk} dibatalkan & stok produk telah dikembalikan.");
    }

    /* ================== APLIKASI KURIR (ANTAR KE RUMAH) ================== */

    /**
     * App kurir TERPADU ala Gojek Ã¢â‚¬â€ SATU AKUN member = user + kurir.
     *  - "Pesanan Masuk" : pesanan antar lunas yang BELUM diambil siapa pun (pool:
     *    id_kurir DAN id_kurir_anggota sama-sama NULL).
     *  - "Pesanan Saya"  : pesanan yang SUDAH diambil identitas kurir efektif
     *    (member via id_kurir_anggota, atau staf/mitra legacy via id_kurir) dan
     *    statusnya belum selesai.
     */
    public function kurir()
    {
        $efektif  = \App\Helpers\AkunHelper::kurirEfektif();
        $namaTampil = \App\Helpers\AkunHelper::namaKurirEfektif();

        // Pool: belum ada kurir mana pun yang mengambil (legacy + member).
        $pesananMasuk = Penjualan::with(['details.barang', 'anggota'])
            ->where('metode_antar', 'antar')
            ->where('status_bayar', 'lunas')
            ->whereNull('id_kurir')
            ->whereNull('id_kurir_anggota')
            ->where('status_pesanan', '!=', 'selesai')
            ->orderBy('tanggal_transaksi')
            ->get();

        // Pesanan yang sedang dipegang identitas kurir ini & belum selesai.
        $qSaya = Penjualan::with(['details.barang', 'anggota'])
            ->where('status_pesanan', '!=', 'selesai');
        if ($efektif && $efektif['tipe'] === 'anggota') {
            $qSaya->where('id_kurir_anggota', $efektif['id']);
        } elseif ($efektif) {
            $qSaya->where('id_kurir', $efektif['id']);
        } else {
            $qSaya->whereRaw('1 = 0');
        }
        $pesananSaya = $qSaya->orderByDesc('tanggal_transaksi')->get();

        $modeKurirMember = (bool) (session('kurir_aktif') && session('pelanggan_id'));

        return view('kurir.index', compact('pesananMasuk', 'pesananSaya', 'namaTampil', 'modeKurirMember'));
    }

    /**
     * Kurir MENGAMBIL satu pesanan dari pool (model Gojek).
     * Identitas yang dicatat menyesuaikan akun: member -> id_kurir_anggota,
     * staf/mitra/petugas legacy -> id_kurir. Pesanan jadi miliknya -> user
     * bisa chat dengan kurir ini.
     */
    public function kurirAmbil($id)
    {
        $efektif = \App\Helpers\AkunHelper::kurirEfektif();
        if (!$efektif) {
            return redirect()->route('mitra.login')
                ->withErrors(['username' => 'Aktifkan mode kurir / login mitra / login petugas dulu untuk mengambil pesanan.']);
        }

        // Hanya bisa mengambil pesanan antar, lunas, yang belum diambil & belum selesai.
        $penjualan = Penjualan::where('id_penjualan', $id)
            ->where('metode_antar', 'antar')
            ->where('status_bayar', 'lunas')
            ->whereNull('id_kurir')
            ->whereNull('id_kurir_anggota')
            ->where('status_pesanan', '!=', 'selesai')
            ->first();

        if (!$penjualan) {
            return back()->withErrors(['error' => 'Pesanan tidak tersedia / sudah diambil kurir lain.']);
        }

        $dataAmbil = [
            'kurir_diambil_pada'=> now(),
            'status_pesanan'    => 'diproses',
        ];
        if ($efektif['tipe'] === 'anggota') {
            $dataAmbil['id_kurir_anggota'] = $efektif['id'];
            $dataAmbil['kurir_tipe']       = 'anggota';
        } else {
            $dataAmbil['id_kurir']    = $efektif['id'];
            $dataAmbil['kurir_tipe']  = 'staf';
        }
        $penjualan->update($dataAmbil);

        return back()->with('success', "Pesanan {$penjualan->no_struk} kini tugas Anda. Pelanggan bisa mengobrol dengan Anda sekarang.");
    }

    /**
     * Kurir update status pesanan yang dipegangnya: diproses -> diantar -> selesai.
     * Hanya kurir yang mengangkat pesanan ini yang boleh mengubah statusnya.
     */
    public function kurirUpdate(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:diproses,diantar,selesai']);

        $efektif = \App\Helpers\AkunHelper::kurirEfektif();
        if (!$efektif) {
            return back()->withErrors(['error' => 'Sesi kurir tidak ditemukan.']);
        }

        $q = Penjualan::where('id_penjualan', $id);
        if ($efektif['tipe'] === 'anggota') {
            $q->where('id_kurir_anggota', $efektif['id']);
        } else {
            $q->where('id_kurir', $efektif['id']);
        }
        $penjualan = $q->firstOrFail();

        $penjualan->update(['status_pesanan' => $request->status]);

        $label = ['diproses' => 'siap diproses', 'diantar' => 'sedang diantar kurir', 'selesai' => 'selesai (sampai di rumah)'];
        return back()->with('success', "Pesanan {$penjualan->no_struk} {$label[$request->status]}.");
    }
}

