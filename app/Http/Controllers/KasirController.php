<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KasirController extends Controller
{
    public function index()
    {
        // Semua produk dikirim — dibedakan di view lewat tab:
        //   ready = stok > 0 (bisa dijual), kosong = stok <= 0 (perlu restock / hapus).
        // Susunan mengikuti kolom urutan yang diatur petugas (sama seperti fitur Promo).
        $semuaBarang = Barang::with('kategori')->orderBy('urutan')->orderBy('nama_barang')->get();
        $barangs = $semuaBarang->filter(fn ($b) => $b->stok > 0)->values();
        $barangsKosong = $semuaBarang->filter(fn ($b) => $b->stok <= 0)->values();
        $anggotas = Anggota::where('status_aktif', 'aktif')->get();

        return view('kasir.index', compact('barangs', 'barangsKosong', 'anggotas'));
    }

    /**
     * Riwayat transaksi terbaru — menu "Cetak Struk" di area petugas.
     */
    public function riwayat()
    {
        $penjualans = Penjualan::with(['anggota', 'kasir'])
            ->orderByDesc('tanggal_transaksi')
            ->paginate(10);

        return view('kasir.riwayat', compact('penjualans'));
    }

    /**
     * Halaman struk: tampilan siap cetak + pengiriman ESC/POS
     * langsung ke printer thermal (WebUSB / Web Bluetooth).
     */
    public function struk($id)
    {
        $penjualan = Penjualan::with(['details.barang', 'kasir', 'anggota'])
            ->findOrFail($id);

        return view('kasir.struk', compact('penjualan'));
    }

    /**
     * Simpan susunan (urutan) katalog produk sekaligus — gaya kelola Promo.
     * Dipanggil dari modal "Atur Urutan" di halaman Kasir (khusus petugas).
     * Input: urutan[id_barang] = angka urut.
     */
    public function aturUrutan(Request $request)
    {
        $data = $request->validate([
            'urutan'   => 'required|array|min:1',
            'urutan.*' => 'required|integer|min:0',
        ]);

        foreach ($data['urutan'] as $idBarang => $posisi) {
            Barang::where('id_barang', (int) $idBarang)
                ->update(['urutan' => (int) $posisi]);
        }

        return back()->with('success', 'Urutan katalog produk berhasil disimpan dan langsung dipakai di halaman toko & kasir.');
    }

    /** Tambah stok produk (restock) — khusus petugas. */
    public function tambahStok(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'jumlah' => 'required|integer|min:1|max:100000',
        ]);

        $barang->increment('stok', (int) $data['jumlah']);

        return back()->with('success', "Stok \"{$barang->nama_barang}\" bertambah {$data['jumlah']} (total kini {$barang->fresh()->stok}).");
    }

    /** Hapus stok produk (stok opname / barang rusak) — khusus petugas. */
    public function hapusStok(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $jumlah = (int) $data['jumlah'];
        if ($jumlah > $barang->stok) {
            return back()->withErrors(['error' => "Jumlah pengurangan melebihi stok tersedia ({$barang->stok})."]);
        }

        $barang->decrement('stok', $jumlah);

        return back()->with('success', "Stok \"{$barang->nama_barang}\" dikurangi {$jumlah} (total kini {$barang->fresh()->stok}).");
    }

    /**
     * Hapus satu produk dari katalog (khusus petugas).
     * Umumnya dipakai untuk produk stok habis yang sudah tidak dijual lagi.
     * Riwayat penjualan_detail ikut terhapus (FK ON DELETE CASCADE).
     */
    public function hapusBarang(Barang $barang)
    {
        $nama = $barang->nama_barang;
        $barang->delete();

        return back()->with('success', "Produk \"{$nama}\" dihapus permanen dari katalog.");
    }

    /**
     * Hapus otomatis SEMUA produk berstok habis sekaligus (khusus petugas).
     * Produk yang masih punya stok tidak tersentuh — aman dipakai
     * berkali-kali untuk bersih-bersih katalog.
     */
    public function hapusBarangKosong()
    {
        $kosong = Barang::where('stok', '<=', 0);
        $jumlah = (clone $kosong)->count();
        $kosong->delete();

        return back()->with('success', $jumlah > 0
            ? "{$jumlah} produk berstok habis berhasil dihapus otomatis dari katalog."
            : 'Tidak ada produk berstok habis untuk dihapus.');
    }

    /** Hapus transaksi/struk beserta detailnya — khusus petugas. */
    public function hapusStruk(Penjualan $penjualan)
    {
        $no = $penjualan->no_struk;
        DB::transaction(function () use ($penjualan) {
            $penjualan->details()->delete();
            $penjualan->delete();
        });

        return back()->with('success', "Struk \"{$no}\" berhasil dihapus.");
    }

    /**
     * Struk versi PDF (publik) — dipakai hasil belanja via chatbot:
     * halaman bersih siap "Cetak / Simpan PDF" + tombol bagikan WhatsApp
     * tanpa nomor tujuan (wa.me/?text=).
     */
    public function strukPdf($id)
    {
        $penjualan = Penjualan::with(['details.barang', 'kasir', 'anggota'])
            ->findOrFail($id);

        return view('kasir.struk-pdf', compact('penjualan'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'id_anggota'    => 'nullable|exists:anggotas,id_anggota',
            'metode_bayar'  => 'required|in:tunai,qris,transfer',
            'nominal_bayar' => 'required|numeric|min:0',
            'items'         => 'required|array|min:1',
            'items.*.id'    => 'required|exists:barangs,id_barang',
            'items.*.qty'   => 'required|integer|min:1',
        ]);

        $isAnggota = !empty($request->id_anggota);

        // Memulai Transaksi ACID Basis Data
        DB::beginTransaction();
        try {
            $totalBruto = 0;
            $totalNetto = 0;
            $detailRecords = [];

            // 1. Validasi stok dan hitung total harga (apakah harga anggota atau harga umum)
            foreach ($request->items as $item) {
                // Lock row barang untuk mencegah race condition (Concurrency Control)
                $barang = Barang::lockForUpdate()->findOrFail($item['id']);

                if ($barang->stok < $item['qty']) {
                    throw new \Exception("Stok barang '{$barang->nama_barang}' tidak mencukupi! Tersisa: {$barang->stok}");
                }

                // Terapkan harga anggota jika terdaftar
                $hargaSatuan = $isAnggota ? $barang->harga_jual_anggota : $barang->harga_jual_umum;
                $subtotal = $hargaSatuan * $item['qty'];

                $totalBruto += ($barang->harga_jual_umum * $item['qty']);
                $totalNetto += $subtotal;

                // Kurangi stok barang
                $barang->decrement('stok', $item['qty']);

                $detailRecords[] = [
                    'id_barang'    => $barang->id_barang,
                    'jumlah'       => $item['qty'],
                    'harga_satuan' => $hargaSatuan,
                    'subtotal'     => $subtotal,
                ];
            }

            $totalDiskon = max(0, $totalBruto - $totalNetto);

            $kembalian = max(0, $request->nominal_bayar - $totalNetto);

            // 3. Simpan Header Penjualan
            $penjualan = Penjualan::create([
                'no_struk'          => 'TRX-' . date('YmdHis') . '-' . strtoupper(Str::random(4)),
                'tanggal_transaksi' => now(),
                'id_anggota'        => $request->id_anggota,
                'id_kasir'          => session('kasir_id') ?? auth()->id() ?? 1,
                'total_bruto'       => $totalBruto,
                'total_diskon'      => $totalDiskon,
                'total_netto'       => $totalNetto,
                'nominal_bayar'     => $request->nominal_bayar,
                'kembalian'         => $kembalian,
                'metode_bayar'      => $request->metode_bayar,
            ]);

            // 4. Simpan Rincian Item (Detail Penjualan)
            foreach ($detailRecords as $detail) {
                $detail['id_penjualan'] = $penjualan->id_penjualan;
                PenjualanDetail::create($detail);
            }

            // 5. Update akumulasi belanja & poin loyalitas member
            if ($isAnggota) {
                Anggota::where('id_anggota', $request->id_anggota)->update([
                    'total_belanja' => DB::raw('total_belanja + ' . (float) $totalNetto),
                    'poin'          => DB::raw('poin + ' . (int) floor($totalNetto / 1000)),
                ]);
            }

            // Selesaikan transaksi jika semua langkah berhasil
            DB::commit();

            // Untuk checkout AJAX (kasir modern) kirim JSON; versi form biasa tetap redirect.
            if ($request->expectsJson()) {
                return response()->json([
                    'success'      => true,
                    'penjualan_id' => $penjualan->id_penjualan,
                    'no_struk'     => $penjualan->no_struk,
                    'kembalian'    => $kembalian,
                    'message'      => "Transaksi berhasil! No Struk: {$penjualan->no_struk}",
                ]);
            }

            // Arahkan petugas ke halaman struk (cetak otomatis ESC/POS ke printer)
            return redirect()
                ->route('kasir.struk', $penjualan->id_penjualan)
                ->with('success', "Transaksi berhasil disimpan! No Struk: {$penjualan->no_struk}. Kembalian: Rp " . number_format($kembalian, 0, ',', '.'));

        } catch (\Exception $e) {
            // Batalkan semua perubahan jika terjadi error / stok habis
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Transaksi Gagal: ' . $e->getMessage()], 422);
            }
            return back()->withErrors(['error' => 'Transaksi Gagal: ' . $e->getMessage()]);
        }
    }
}
