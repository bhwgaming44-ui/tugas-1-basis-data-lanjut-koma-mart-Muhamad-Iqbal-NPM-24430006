<?php

namespace App\Http\Controllers;

use App\Models\PromoSlide;
use App\Models\Barang;
use App\Models\PengaturanToko;
use App\Helpers\AkunHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PromoController extends Controller
{
    /** Proteksi: hanya petugas/kasir yang boleh mengelola promo dan tampilan. */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!AkunHelper::isPetugas()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Fitur ini hanya untuk Petugas Toko.'
                ], 403);
            }
            return $next($request);
        });
    }

    /** Daftar semua promo slide (untuk modal kelola promo). */
    public function index()
    {
        $promo  = PromoSlide::with('barang')->orderBy('urutan', 'asc')->get();
        $barangs = Barang::orderBy('nama_barang', 'asc')
            ->get(['id_barang', 'nama_barang', 'harga_jual_umum']);
        return view('petugas.promo.index', compact('promo', 'barangs'));
    }

    /** Simpan promo baru (termasuk upload gambar dari galeri laptop). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'badge'     => 'nullable|string|max:60',
            'judul'     => 'required|string|max:150',
            'sub'       => 'nullable|string',
            'cta'       => 'nullable|string|max:60',
            'url'       => 'nullable|url|max:300',
            'warna'     => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'urutan'    => 'nullable|integer|min:0',
            'aktif'     => 'nullable|boolean',
            'id_barang' => 'nullable|exists:barangs,id_barang',
            'crop_fokus'=> 'nullable|in:center,top,bottom,left,right',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama = 'promo-' . date('YmdHis') . '-' . strtoupper(Str::random(4)) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/promo', $nama);
            $data['gambar'] = 'storage/promo/' . $nama;
        }

        $data['urutan'] = $request->input('urutan', PromoSlide::max('urutan') + 1);
        $data['aktif'] = $request->boolean('aktif', true);
        $data['id_barang'] = $request->input('id_barang') ?: null;
        $data['crop_fokus'] = $request->input('crop_fokus', 'center');

        PromoSlide::create($data);
        return back()->with('success', 'Promo baru berhasil ditambahkan.');
    }

    /** Update promo (termasuk ganti gambar). */
    public function update(Request $request, PromoSlide $promo)
    {
        $data = $request->validate([
            'badge'     => 'nullable|string|max:60',
            'judul'     => 'required|string|max:150',
            'sub'       => 'nullable|string',
            'cta'       => 'nullable|string|max:60',
            'url'       => 'nullable|url|max:300',
            'warna'     => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'urutan'    => 'nullable|integer|min:0',
            'aktif'     => 'nullable|boolean',
            'id_barang' => 'nullable|exists:barangs,id_barang',
            'crop_fokus'=> 'nullable|in:center,top,bottom,left,right',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama = 'promo-' . $promo->id . '-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/promo', $nama);
            $data['gambar'] = 'storage/promo/' . $nama;
        }

        $data['urutan'] = $request->input('urutan', $promo->urutan);
        $data['aktif'] = $request->boolean('aktif', true);
        $data['id_barang'] = $request->input('id_barang') ?: null;
        $data['crop_fokus'] = $request->input('crop_fokus', $promo->crop_fokus ?? 'center');

        $promo->update($data);
        return back()->with('success', 'Promo berhasil diperbarui.');
    }

    /** Hapus promo slide. */
    public function destroy(PromoSlide $promo)
    {
        $promo->delete();
        return back()->with('success', 'Promo berhasil dihapus.');
    }

    /** Render ulang HTML slider promo secara realtime (tanpa reload halaman). */
    public function render()
    {
        $slides = PromoSlide::aktif()->get();
        $warnaDefault = [
            'linear-gradient(135deg, #b91c1c 0%, #dc2626 50%, #f87171 100%)',
            'linear-gradient(135deg, #15803d 0%, #16a34a 50%, #4ade80 100%)',
            'linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #60a5fa 100%)',
            'linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #fbbf24 100%)',
            'linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #c084fc 100%)',
        ];
        return view('petugas.promo._slider', compact('slides', 'warnaDefault'))->render();
    }

    /** Ganti tema warna dashboard (khusus petugas). */
    public function temaWarna(Request $request)
    {
        $request->validate(['warna' => 'required|string|max:20']);
        PengaturanToko::simpan('tema_warna', $request->warna);
        return response()->json(['success' => true, 'warna' => $request->warna]);
    }

    /** Upload & simpan logo minimarket (khusus petugas). */
    public function logoToko(Request $request)
    {
        $request->validate(['logo' => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:4096']);
        $file = $request->file('logo');
        $nama = 'logo-toko-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/toko', $nama);
        PengaturanToko::simpan('logo_toko', 'storage/toko/' . $nama);
        return back()->with('success', 'Logo minimarket berhasil diperbarui.');
    }
}
