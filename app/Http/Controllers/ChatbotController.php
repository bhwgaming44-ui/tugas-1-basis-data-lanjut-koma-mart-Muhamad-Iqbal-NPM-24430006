<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Anggota;
use Illuminate\Http\Request;

/**
 * Chatbot KOPMA MART (mode database)
 * Menjawab dari DATABASE toko + menampilkan LIST BARANG (katalog) di panel chat.
 * Setiap produk punya tombol "Pesan via WA" ke WhatsApp Business (services.wa.number).
 */
class ChatbotController extends Controller
{
    /** POST /chatbot/ask -> { reply, source, products, categories, chips, wa, wa_name } */
    public function ask(Request $request)
    {
        $message = mb_strtolower(trim((string) $request->input('message', '')));

        if ($message === '') {
            return response()->json([
                'reply'  => 'Mohon ketik pesan terlebih dahulu ya.',
                'source' => 'database',
                'chips'  => $this->chipUtama(),
                'wa'     => $this->waNumber(),
            ]);
        }

        $response = [
            'reply'   => '',
            'source'  => 'database',
            'wa'      => $this->waNumber(),
            'wa_name' => config('services.wa.name', 'KOPMA MART'),
            'chips'   => $this->chipUtama(),
        ];

        // ===== KERANJANG BELANJA ONLINE (multi-barang) =====
        // Perintah: "tambah <nama>" / "+<nama>", "hapus <nama>", "keranjang", "kosongkan", "pesan sekarang"
        if ($this->cocok($message, ['tambah ', '+', 'keranjang', 'kosongkan', 'pesan sekarang', 'checkout']) || str_starts_with($message, 'hapus')) {
            $hasil = $this->prosesKeranjang($message, $response);
            if ($hasil !== null) {
                return response()->json($hasil);
            }
        }

        // Kategori / jenis barang -> daftar kategori + contoh produk
        if ($this->cocok($message, ['kategori', 'jenis', 'macam', 'jual apa', 'daftar produk'])) {
            $response['reply']      = "Berikut kategori produk di KOPMA MART. Ketik nama kategori atau produk untuk melihat daftar barangnya.";
            $response['categories'] = $this->daftarKategori();
            $response['products']   = $this->serializeBarang(
                Barang::with('kategori')->orderBy('id_barang')->limit(8)->get()
            );
            return response()->json($response);
        }

        // Barang / produk / stok / belanja / harga -> LIST BARANG (katalog)
        if ($this->cocok($message, ['barang', 'produk', 'stok', 'stock', 'belanja', 'beli', 'pesan', 'harga', 'berapa', 'katalog', 'murah', 'tersedia', 'ada'])) {
            $barangs = $this->cariBarang($message);
            $response['products'] = $this->serializeBarang($barangs);

            if (empty($response['products'])) {
                $response['reply'] = "Maaf, produk yang dicari tidak ditemukan. Coba kata kunci lain (mis. 'beras', 'minyak', 'indomie') atau ketik 'kategori' untuk melihat daftar kategori.";
            } else {
                $response['reply'] = "Ini daftar barang dari database toko. Klik tombol \"Pesan via WA\" pada produk untuk langsung memesan ke WhatsApp Business KOPMA MART.";
            }
            return response()->json($response);
        }

        // Member / poin
        if ($this->cocok($message, ['member', 'anggota', 'poin', 'promo'])) {
            $anggotas = Anggota::where('status_aktif', 'aktif')->limit(3)->get();
            $lines = [];
            foreach ($anggotas as $a) {
                $lines[] = "• {$a->nama} - Kode: {$a->kode_member} - Poin: {$a->poin}";
            }
            $reply = "Kartu member KOPMA MART gratis! Keuntungannya:\n"
                . "- Harga khusus member untuk semua produk\n"
                . "- Poin loyalitas tiap belanja\n"
                . "- Info promo eksklusif\n";
            if (!empty($lines)) {
                $reply .= "\nMember aktif:\n" . implode("\n", $lines);
            }
            $response['reply'] = $reply . "\nDaftar member baru? Silakan ke kasir - gratis, tanpa perlu nomor HP.";
            return response()->json($response);
        }

        // Hubungi / nomor / WhatsApp
        if ($this->cocok($message, ['hubungi', 'nomor', 'kontak', 'whatsapp', 'wa', 'admin'])) {
            $nomor = config('services.wa.number', '087853783946');
            $response['reply'] = "Hubungi WhatsApp Business KOPMA MART di {$nomor}. Klik tombol di bawah untuk chat langsung dengan admin.";
            $response['wa_link'] = $this->waLink("Halo KOPMA MART, saya ingin bertanya mengenai produk.");
            return response()->json($response);
        }

        // Jam buka
        if ($this->cocok($message, ['jam', 'buka', 'tutup', 'operasional'])) {
            $response['reply'] = "KOPMA MART buka setiap hari 07.00 - 22.00 WIB. Selamat berbelanja!";
            return response()->json($response);
        }

        // Sapaan
        if ($this->cocok($message, ['hai', 'halo', 'hello', 'hi', 'pagi', 'siang', 'sore', 'malam', 'assalam'])) {
            $response['reply'] = "Halo! Saya KOPMA MART Bot. Saya bisa menampilkan daftar barang, cek stok & harga, info kategori, member, dan jam buka. Mau lihat produk apa?";
            $response['products'] = $this->serializeBarang(
                Barang::with('kategori')->orderBy('id_barang')->limit(6)->get()
            );
            return response()->json($response);
        }

        // Default: coba cari berdasarkan teks (nama produk); jika tak ada, tampilkan rekomendasi
        $barangs = $this->cariBarang($message);
        if ($barangs->isNotEmpty()) {
            $response['reply'] = "Hasil pencarian barang. Klik \"Pesan via WA\" untuk memesan ke WhatsApp Business KOPMA MART.";
        } else {
            $barangs = Barang::with('kategori')->orderBy('id_barang')->limit(8)->get();
            $response['reply'] = "Berikut beberapa produk KOPMA MART. Ketik nama barang untuk mencari, atau 'kategori' untuk melihat semua kategori.\n"
                . "Ketik 'hubungi' untuk chat admin via WhatsApp Business.";
        }
        $response['products'] = $this->serializeBarang($barangs);
        return response()->json($response);
    }

    /** GET /chatbot/katalog -> dipanggil saat panel chat dibuka. */
    public function katalog()
    {
        return response()->json([
            'reply'      => "Daftar barang KOPMA MART. Klik \"Pesan via WA\" untuk memesan ke WhatsApp Business.",
            'source'     => 'database',
            'categories' => $this->daftarKategori(),
            'products'   => $this->serializeBarang(
                Barang::with('kategori')->orderBy('id_barang')->limit(20)->get()
            ),
            'chips'      => $this->chipUtama(),
            'wa'         => $this->waNumber(),
            'wa_name'    => config('services.wa.name', 'KOPMA MART'),
        ]);
    }

    // =========================================================================
    // KERANJANG BELANJA ONLINE (multi-barang, tersimpan di session)
    // =========================================================================

    /**
     * Proses perintah keranjang. Mengembalikan array response, atau null bila
     * pesan bukan perintah keranjang (agar lanjut ke alur lain).
     */
    private function prosesKeranjang(string $message, array $response): ?array
    {
        $cart = session('bot_cart', []); // [id_barang => ['nama','qty','harga','stok']]

        // Kosongkan keranjang
        if (str_contains($message, 'kosongkan')) {
            session()->forget('bot_cart');
            $response['reply']   = "Keranjang dikosongkan. Ketik nama barang untuk menambah lagi.";
            $response['cart']    = 0;
            $response['cart_items'] = [];
            return $response;
        }

        // Lihat isi keranjang
        if (trim($message) === 'keranjang' || str_contains($message, 'lihat keranjang')) {
            return $this->tampilkanKeranjang($response);
        }

        // Checkout / pesan sekarang -> buat 1 link WA berisi SEMUA barang
        if (str_contains($message, 'pesan sekarang') || str_contains($message, 'checkout')) {
            if (empty($cart)) {
                $response['reply'] = "Keranjang masih kosong. Tambah barang dulu (klik \"Tambah\" pada produk, atau ketik 'tambah <nama barang>').";
            } else {
                $response['reply']    = "Pesanan Anda siap! Klik tombol di bawah untuk mengirim SEMUA barang ke WhatsApp Business KOPMA MART.";
                $response['wa_link']  = $this->waLinkKeranjang($cart);
            }
            $response['cart'] = count($cart);
            $response['cart_items'] = $this->cartItems($cart);
            return $response;
        }

        // Hapus satu barang dari keranjang: "hapus <nama>"
        if (str_starts_with($message, 'hapus')) {
            $nama = trim(preg_replace('/^hapus(dari keranjang)?/', '', $message));
            $cart = $this->hapusDariKeranjang($cart, $nama);
            $response['cart'] = count($cart);
            $response['cart_items'] = $this->cartItems($cart);
            $response['reply'] = empty($cart)
                ? "Barang dihapus. Keranjang sekarang kosong."
                : "Barang dihapus dari keranjang. Ketik 'keranjang' untuk melihat isi, atau 'pesan sekarang' untuk memesan.";
            return $response;
        }

        // Tambah barang: "tambah <nama>" atau "+<nama>"
        $nama = trim(preg_replace('/^(tambah|\+)\s*/', '', $message));
        if ($nama === '') {
            return null;
        }

        $barang = Barang::where('nama_barang', 'like', '%' . $nama . '%')->orderBy('id_barang')->first();
        if (!$barang) {
            return null; // biarkan alur pencarian normal
        }
        if ($barang->stok <= 0) {
            $response['reply'] = "Maaf, \"{$barang->nama_barang}\" sedang habis. Silakan pilih produk lain.";
            return $response;
        }

        $id = (string) $barang->id_barang;
        if (isset($cart[$id])) {
            if ($cart[$id]['qty'] < $barang->stok) {
                $cart[$id]['qty'] += 1;
            } else {
                $response['reply'] = "Jumlah \"{$barang->nama_barang}\" sudah mencapai batas stok ({$barang->stok}).";
                $response['cart'] = count($cart);
                $response['cart_items'] = $this->cartItems($cart);
                return $response;
            }
        } else {
            $cart[$id] = [
                'nama'  => $barang->nama_barang,
                'qty'   => 1,
                'harga' => (float) $barang->harga_jual_umum,
                'stok'  => (int) $barang->stok,
            ];
        }

        session(['bot_cart' => $cart]);
        $response['reply'] = "✓ \"{$barang->nama_barang}\" ditambahkan ke keranjang (x{$cart[$id]['qty']}). Tambah barang lain, atau ketik 'keranjang' / 'pesan sekarang'.";
        $response['cart'] = count($cart);
        $response['cart_items'] = $this->cartItems($cart);
        return $response;
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /** Cari barang berdasarkan kata kunci pada pesan (nama / barcode). */
    private function cariBarang(string $message)
    {
        // Kata umum yang bukan nama produk -> diabaikan saat pencarian
        $stop = ['barang', 'produk', 'stok', 'stock', 'belanja', 'beli', 'pesan', 'harga',
                 'berapa', 'katalog', 'murah', 'tersedia', 'ada', 'yang', 'untuk', 'cari',
                 'lihat', 'tampilkan', 'semua', 'daftar', 'saya', 'mau', 'ingin'];

        $kata = preg_split('/\s+/', $message, -1, PREG_SPLIT_NO_EMPTY);
        $kunci = array_values(array_filter($kata, fn ($k) => mb_strlen($k) >= 3 && !in_array($k, $stop, true)));

        $query = Barang::with('kategori');

        if (!empty($kunci)) {
            $query->where(function ($w) use ($kunci) {
                foreach ($kunci as $k) {
                    $w->orWhere('nama_barang', 'like', '%' . $k . '%')
                      ->orWhere('kode_barcode', 'like', '%' . $k . '%');
                }
            });
        }

        return $query->orderBy('id_barang')->limit(20)->get();
    }

    /** Ubah koleksi Barang menjadi array rapi untuk JSON. */
    private function serializeBarang($barangs): array
    {
        return $barangs->map(function ($b) {
            $stok = (int) $b->stok;
            return [
                'id'            => $b->id_barang,
                'nama'          => $b->nama_barang,
                'kategori'      => optional($b->kategori)->nama_kategori ?? 'Umum',
                'harga_umum'    => (float) $b->harga_jual_umum,
                'harga_anggota' => (float) $b->harga_jual_anggota,
                'stok'          => $stok,
                'habis'         => $stok <= 0,
                'wa'            => $this->waLink(
                    "Halo KOPMA MART, saya ingin memesan:\n"
                    . "• {$b->nama_barang} - Rp " . number_format($b->harga_jual_umum, 0, ',', '.')
                ),
            ];
        })->values()->all();
    }

    /** Daftar kategori beserta jumlah produk. */
    private function daftarKategori(): array
    {
        return KategoriBarang::withCount('barangs')
            ->orderBy('id_kategori')
            ->get()
            ->map(fn ($k) => [
                'nama'   => $k->nama_kategori,
                'jumlah' => (int) $k->barangs_count,
            ])
            ->values()
            ->all();
    }

    /** Chip saran cepat di bawah panel chat. */
    private function chipUtama(): array
    {
        return ['Lihat barang', 'Lihat kategori', 'Cari produk', 'Hubungi admin'];
    }

    /** Apakah pesan mengandung salah satu kata kunci. */
    private function cocok(string $message, array $kata): bool
    {
        foreach ($kata as $k) {
            if (str_contains($message, $k)) {
                return true;
            }
        }
        return false;
    }

    /** Nomor WA Business dalam format internasional (tanpa + / spasi). */
    private function waNumber(): string
    {
        $raw = preg_replace('/[^0-9]/', '', (string) config('services.wa.number', '087853783946'));
        if ($raw === '') {
            $raw = '087853783946';
        }
        if (str_starts_with($raw, '62')) {
            return $raw;
        }
        if (str_starts_with($raw, '0')) {
            return '62' . substr($raw, 1);
        }
        if (str_starts_with($raw, '8')) {
            return '62' . $raw;
        }
        return '62' . $raw;
    }

    /** Buat link wa.me ke nomor WA Business dengan pesan ter-isi. */
    private function waLink(string $text): string
    {
        return 'https://wa.me/' . $this->waNumber() . '?text=' . rawurlencode($text);
    }

    /** Tampilkan isi keranjang. */
    private function tampilkanKeranjang(array $response): array
    {
        $cart = session('bot_cart', []);
        if (empty($cart)) {
            $response['reply'] = "Keranjang masih kosong. Ketik nama barang untuk menambah, atau ketik 'tambah <nama barang>'.";
        } else {
            $baris = [];
            $total = 0;
            foreach ($cart as $it) {
                $sub = $it['harga'] * $it['qty'];
                $total += $sub;
                $baris[] = "• {$it['nama']} x{$it['qty']} = Rp " . number_format($sub, 0, ',', '.');
            }
            $response['reply'] = "Isi keranjang Anda:\n" . implode("\n", $baris)
                . "\n\nTotal: Rp " . number_format($total, 0, ',', '.')
                . "\nKetik 'pesan sekarang' untuk kirim ke WhatsApp, atau 'kosongkan' untuk mengosongkan.";
        }
        $response['cart'] = count($cart);
        $response['cart_items'] = $this->cartItems($cart);
        return $response;
    }

    /** Hapus barang dari keranjang berdasarkan nama. */
    private function hapusDariKeranjang(array $cart, string $nama): array
    {
        foreach ($cart as $id => $it) {
            if ($nama !== '' && stripos($it['nama'], $nama) !== false) {
                unset($cart[$id]);
            }
        }
        session(['bot_cart' => $cart]);
        return $cart;
    }

    /** Ubah cart (session) menjadi array ringkas untuk frontend. */
    private function cartItems(array $cart): array
    {
        $items = [];
        foreach ($cart as $id => $it) {
            $items[] = [
                'id'       => (int) $id,
                'nama'     => $it['nama'],
                'qty'      => (int) $it['qty'],
                'harga'    => (float) $it['harga'],
                'subtotal' => (float) $it['harga'] * (int) $it['qty'],
            ];
        }
        return $items;
    }

    /** Buat satu link WA berisi SEMUA barang di keranjang. */
    private function waLinkKeranjang(array $cart): string
    {
        $baris = [];
        $total = 0;
        foreach ($cart as $it) {
            $sub = $it['harga'] * $it['qty'];
            $total += $sub;
            $baris[] = "• {$it['nama']} x{$it['qty']} = Rp " . number_format($sub, 0, ',', '.');
        }
        $teks = "Halo KOPMA MART, saya ingin memesan:\n" . implode("\n", $baris)
            . "\n\nTotal: Rp " . number_format($total, 0, ',', '.') . "\nTerima kasih.";
        return $this->waLink($teks);
    }
}