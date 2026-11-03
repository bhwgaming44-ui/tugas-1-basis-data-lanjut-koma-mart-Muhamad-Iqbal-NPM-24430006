<?php
namespace App\Http\Controllers;
use App\Models\Anggota;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\PromoSlide;
use App\Models\PengaturanToko;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
/**
 * Katalog Toko untuk Area Pelanggan (publik) + kelola stok untuk petugas.
 * - index : katalog publik dengan pencarian (?q=) + pagination ringkas (12/halaman).
 * - store : tambah produk baru (nama, harga, stok, kategori, barcode).
 * - destroy : hapus produk (stok ikut terhapus bersama baris barang).
 */
class TokoController extends Controller
{
    public function index(Request $request)
    {
        $q   = trim((string) $request->query('q', ''));
        $kat = trim((string) $request->query('kategori', ''));
        // Katalog tampil sesuai susunan yang diatur petugas (kolom urutan) —
        // sama seperti fitur Promo. Nomor kecil tampil lebih dulu, lalu nama.
        $barangs = Barang::with('kategori')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama_barang', 'like', '%' . $q . '%')
                      ->orWhere('kode_barcode', 'like', '%' . $q . '%');
                });
            })
            ->when($kat !== '', function ($query) use ($kat) {
                $query->whereHas('kategori', function ($w) use ($kat) {
                    $w->where('nama_kategori', $kat);
                });
            })
            ->orderBy('urutan', 'asc')
            ->orderBy('id_barang', 'asc')
            ->paginate(20)
            ->withQueryString();
        $kategoris = KategoriBarang::withCount('barangs')->get();
        $katAktif  = $kat;
        // Data ringkas SEMUA produk untuk pencarian realtime di dasbor (dropdown saran) —
        // filtering client-side ala halaman Kelola Produk, tanpa request server saat mengetik.
        $semuaUntukCari = Barang::with('kategori')
            ->orderBy('urutan', 'asc')
            ->orderBy('id_barang', 'asc')
            ->get()
            ->map(fn ($b) => [
                'id'     => $b->id_barang,
                'nama'   => $b->nama_barang,
                'harga'  => 'Rp ' . number_format($b->harga_jual_anggota, 0, ',', '.'),
                'kat'    => $b->kategori->nama_kategori ?? '',
                'gambar' => $this->urlGambarProduk($b->gambar),
            ]);
        // ===== SLIDE PROMO (halaman utama) =====
        // Prioritas: promo yang diatur petugas di database (bisa edit/hapus/tambah realtime tanpa coding).
        // Fallback: slide otomatis dari diskon produk jika belum ada promo di database.
        $warnaSlide = [
            'linear-gradient(120deg,#122314 0%,#15803d 55%,#22c55e 120%)',
            'linear-gradient(120deg,#0d233a 0%,#1d4a75 70%,#31547e 130%)',
            'linear-gradient(120deg,#7c2d12 0%,#c2410c 60%,#f59e0b 130%)',
            'linear-gradient(120deg,#4c1d95 0%,#7c3aed 60%,#a78bfa 130%)',
            'linear-gradient(120deg,#7f1d1d 0%,#c8102e 60%,#ef4444 130%)',
        ];

        $slides = [];
        $promoDb = PromoSlide::with('barang')->aktif()->take(5)->get();
        if ($promoDb->count() > 0) {
            foreach ($promoDb as $i => $p) {
                $slides[] = [
                    'badge' => $p->badge,
                    'judul' => $p->judul,
                    'sub'   => $p->sub,
                    'cta'   => $p->cta ?: 'Lihat Promo',
                    'url'   => $p->url ?: ($p->barang ? route('toko.index', ['q' => $p->barang->nama_barang]) : route('toko.index')),
                    'warna' => $p->warna ?: $warnaSlide[$i % count($warnaSlide)],
                    'gambar'=> $p->gambar,
                    'fokus' => $p->posisiCrop(),  // posisi crop background-image
                    'ikon'  => 'fa-tags',
                ];
            }
        } else {
            // Fallback otomatis lama
            $produkPromo = Barang::with('kategori')
                ->where('stok', '>', 0)
                ->whereColumn('harga_jual_umum', '>', 'harga_jual_anggota')
                ->get()
                ->map(function ($b) {
                    $hemat = $b->harga_jual_umum - $b->harga_jual_anggota;
                    $b->persen_hemat = $b->harga_jual_umum > 0 ? (int) round(($hemat / $b->harga_jual_umum) * 100) : 0;
                    return $b;
                })
                ->sortByDesc('persen_hemat')
                ->take(4)
                ->values();

            $slides[] = [
                'badge' => 'PROMO RUTIN',
                'judul' => 'GRATIS ONGKIR Area Kampus',
                'sub'   => 'Minimum belanja Rp25.000 — diantar kurir KOPMA ke rumah Anda.',
                'cta'   => 'Belanja Sekarang',
                'url'   => route('toko.index'),
                'warna' => $warnaSlide[0],
                'gambar'=> null,
                'ikon'  => 'fa-motorcycle',
            ];
            foreach ($produkPromo as $b) {
                $hemat = $b->harga_jual_umum - $b->harga_jual_anggota;
                $slides[] = [
                    'badge' => 'DISKON ' . $b->persen_hemat . '%',
                    'judul' => $b->nama_barang,
                    'sub'   => 'Hemat Rp ' . number_format($hemat, 0, ',', '.') . ' — dari Rp '
                             . number_format($b->harga_jual_umum, 0, ',', '.') . ' jadi Rp '
                             . number_format($b->harga_jual_anggota, 0, ',', '.') . '. Stok ' . $b->stok . '.',
                    'cta'   => 'Ambil Promo',
                    'url'   => route('toko.index', ['q' => $b->nama_barang]),
                    'warna' => null,
                    'gambar'=> null,
                    'ikon'  => 'fa-tags',
                ];
            }
            $cadangan = [
                ['badge' => 'MEMBER', 'judul' => 'Harga Khusus Member', 'sub' => 'Daftar gratis — langsung dapat harga member di semua produk.', 'cta' => 'Daftar Member', 'url' => route('pelanggan.daftar'), 'warna' => null, 'gambar' => null, 'ikon' => 'fa-id-card'],
                ['badge' => 'POIN', 'judul' => 'Poin Loyalitas Tiap Belanja', 'sub' => '1 poin per Rp1.000 belanja — kumpulkan, tuker jadi potongan harga.', 'cta' => 'Lihat Produk', 'url' => route('toko.index'), 'warna' => null, 'gambar' => null, 'ikon' => 'fa-star'],
                ['badge' => 'STOK BARU', 'judul' => '100 Produk Selalu Ready', 'sub' => 'Sembako, snack, minuman, ATK — belanja hemat setiap hari.', 'cta' => 'Kunjungi Toko', 'url' => route('toko.index'), 'warna' => null, 'gambar' => null, 'ikon' => 'fa-boxes-stacked'],
            ];
            foreach ($cadangan as $c) {
                if (count($slides) >= 5) break;
                $slides[] = $c;
            }
            $slides = array_slice($slides, 0, 5);
            foreach ($slides as $i => &$s) {
                if (empty($s['warna'])) {
                    $s['warna'] = $warnaSlide[$i % count($warnaSlide)];
                }
            }
            unset($s);
        }
        // Saldo Kopma Point member yang sedang login (untuk kartu dompet)
        $saldoKopma = 0;
        if (session('pelanggan_id')) {
            $saldoKopma = (float) (Anggota::find(session('pelanggan_id'))->saldo_kopma ?? 0);
        }
        // Susunan global id produk (khusus petugas) — dipakai mode "Atur Urutan Produk"
        // di dasbor: menyimpan urutan global 0..n-1 agar tidak tabrakan antar halaman.
        $urutanSemua = \App\Helpers\AkunHelper::isPetugas()
            ? Barang::orderBy('urutan')->orderBy('id_barang')->pluck('id_barang')->all()
            : [];
        return view('toko.index', [
            'barangs'     => $barangs,
            'kategoris'   => $kategoris,
            'q'           => $q,
            'katAktif'    => $katAktif,
            'promoSlides' => $slides,   // slide promo (DB petugas / fallback diskon)
            'saldoKopma'  => $saldoKopma,
            'semuaUntukCari' => $semuaUntukCari, // dropdown saran pencarian realtime dasbor
            'urutanSemua' => $urutanSemua,       // mode atur urutan (hanya petugas)
        ]);
    }

    /**
     * URL gambar produk dari kolom `gambar` — mendukung URL penuh (https://...),
     * path di /public (images/produk/a.jpg), dan path storage (produk/a.jpg).
     */
    private function urlGambarProduk(?string $g): ?string
    {
        $g = trim((string) $g);
        if ($g === '') return null;
        if (Str::startsWith($g, ['http://', 'https://', '//', 'data:'])) return $g;
        $g = ltrim($g, '/');
        if (file_exists(public_path($g))) return asset($g);
        return asset('storage/' . preg_replace('#^(public/|storage/)#', '', $g));
    }
    /** Tambah produk baru ke stok toko. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_barang'        => 'required|string|max:150',
            'kode_barcode'       => 'nullable|string|max:50|unique:barangs,kode_barcode',
            'id_kategori'        => 'nullable|exists:kategori_barangs,id_kategori',
            'harga_beli'         => 'nullable|numeric|min:0',
            'harga_jual_umum'    => 'required|numeric|min:0',
            'harga_jual_anggota' => 'required|numeric|min:0',
            'stok'               => 'required|integer|min:0',
            'stok_minimum'       => 'nullable|integer|min:0',
            'urutan'             => 'nullable|integer|min:0',
            'gambar'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        // ===== Upload gambar produk asli (opsional) =====
        // Disimpan ke storage/app/public/produk lalu diakses lewat URL publik storage/produk/...
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama = 'produk-' . date('YmdHis') . '-' . strtoupper(Str::random(4)) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/produk', $nama);   // storage/app/public/produk/<nama>
            $data['gambar'] = 'storage/produk/' . $nama; // path URL publik
        }
        if (empty($data['kode_barcode'])) {
            $data['kode_barcode'] = 'BRG-' . date('YmdHis') . '-' . strtoupper(Str::random(3));
        }
        if (empty($data['harga_beli'])) {
            $data['harga_beli'] = round($data['harga_jual_anggota'] * 0.85);
        }
        $data['stok_minimum'] = $data['stok_minimum'] ?? 5;
        // Urutan tampil katalog: kosong = otomatis paling bawah (nilai terbesar + 1)
        $data['urutan'] = $data['urutan'] ?? (Barang::max('urutan') + 1);
        Barang::create($data);
        return back()->with('success', 'Produk "' . $data['nama_barang'] . '" berhasil ditambahkan ke katalog toko.');
    }

    /**
     * Halaman "Kelola Produk" (gaya Kelola Promo — khusus petugas).
     * Menampilkan SEMUA produk (siap jual & stok habis) dengan urutan katalog,
     * plus data kategori untuk dropdown modal Tambah/Ubah.
     */
    public function kelola()
    {
        if (!\App\Helpers\AkunHelper::isPetugas()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Petugas Toko.');
        }

        $barangs  = Barang::with('kategori')->orderBy('urutan')->orderBy('nama_barang')->get();
        $kategoris = KategoriBarang::orderBy('nama_kategori')->get();

        return view('petugas.produk.index', compact('barangs', 'kategoris'));
    }

    /**
     * Update produk lengkap dari halaman Kelola Produk (khusus petugas).
     * Bisa ubah nama, barcode, kategori, semua harga, stok, urutan & ganti foto.
     */
    public function update(Request $request, Barang $barang)
    {
        if (!\App\Helpers\AkunHelper::isPetugas()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Petugas Toko.');
        }

        $data = $request->validate([
            'nama_barang'        => 'required|string|max:150',
            'kode_barcode'       => 'nullable|string|max:50|unique:barangs,kode_barcode,' . $barang->id_barang . ',id_barang',
            'id_kategori'        => 'nullable|exists:kategori_barangs,id_kategori',
            'harga_beli'         => 'nullable|numeric|min:0',
            'harga_jual_umum'    => 'required|numeric|min:0',
            'harga_jual_anggota' => 'required|numeric|min:0',
            'stok'               => 'required|integer|min:0',
            'stok_minimum'       => 'nullable|integer|min:0',
            'urutan'             => 'nullable|integer|min:0',
            'gambar'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Ganti foto produk asli (opsional) — sama seperti upload saat tambah
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama = 'produk-' . $barang->id_barang . '-' . date('YmdHis') . '-' . strtoupper(Str::random(3)) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/produk', $nama);
            $data['gambar'] = 'storage/produk/' . $nama;
        }

        if (empty($data['harga_beli'])) {
            $data['harga_beli'] = round($data['harga_jual_anggota'] * 0.85);
        }
        $data['stok_minimum'] = $data['stok_minimum'] ?? 5;
        // Urutan tampil katalog: kosong = pertahankan urutan lama
        $data['urutan'] = $data['urutan'] ?? $barang->urutan;

        $barang->update($data);

        return back()->with('success', 'Produk "' . $barang->nama_barang . '" berhasil diperbarui.');
    }

    /** Update foto produk realtime dari galeri laptop (khusus petugas). */
    public function updateGambar(Request $request, Barang $barang)
    {
        // Proteksi: hanya sesi petugas/kasir yang boleh mengubah
        if (!\App\Helpers\AkunHelper::isPetugas()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Fitur ganti foto produk hanya untuk Petugas Toko.'
            ], 403);
        }
        $request->validate([
            'gambar' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);
        $file = $request->file('gambar');
        $nama = 'produk-' . $barang->id_barang . '-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
        
        // Simpan ke storage publik
        $file->storeAs('public/produk', $nama);
        $publicPath = 'storage/produk/' . $nama;
        // Copy langsung juga ke public/storage/produk jika symbolic link di Windows berbeda
        try {
            $destDir = public_path('storage/produk');
            if (!file_exists($destDir)) {
                mkdir($destDir, 0777, true);
            }
            copy($file->getRealPath(), $destDir . DIRECTORY_SEPARATOR . $nama);
        } catch (\Throwable $e) {}
        $barang->update(['gambar' => $publicPath]);
        return response()->json([
            'success'   => true,
            'message'   => 'Foto produk "' . $barang->nama_barang . '" berhasil diperbarui secara realtime.',
            'gambar_url'=> asset($publicPath),
            'id_barang' => $barang->id_barang
        ]);
    }
    /** Hapus produk dari stok toko. */
    public function destroy(Barang $barang)
    {
        $nama = $barang->nama_barang;
        $barang->delete();
        return back()->with('success', 'Produk "' . $nama . '" berhasil dihapus dari stok.');
    }
}
