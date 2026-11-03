<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\ChatPesan;
use App\Models\ChatTemplate;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private const MAKS_PESAN = 200;
    private const A = '\App\Helpers\AkunHelper';

    /** Chat sisi USER: hanya tampil bila mode aktif = user (disembunyikan dari akun kurir). */
    public function index()
    {
        // CHAT KURIR sisi USER hanya untuk akun member (pelanggan_id).
        if (!session('pelanggan_id')) {
            // Akun ini sudah login sebagai Petugas / Mitra — JANGAN suruh login
            // member lagi (keluhan: "akun user kok kalo mau chat kurir jangan
            // kasih halaman login lagi"). Alihkan ke chat kurir miliknya sendiri.
            if (session('kasir_id') || session('mitra_id')) {
                return redirect()->route('kurir.chat');
            }
            // Benar-benar tamu (belum login apa pun) -> halaman login member.
            return redirect()->route('pelanggan.login')
                ->withErrors(['login' => 'Silakan login sebagai member untuk chat dengan kurir.']);
        }
        // Anti-campur: akun kurir (mode kurir) tidak boleh membuka halaman chat member.
        if (self::A::modeAktif() === 'kurir') {
            return redirect()->route('kurir.chat')
                ->withErrors(['chat' => 'Anda sedang dalam Mode Kurir. Chat member hanya tampil di halaman Chat Kurir.']);
        }
        $anggota = Anggota::findOrFail(session('pelanggan_id'));
        $pesananChat = Penjualan::with(['kurir', 'kurirAnggota'])
            ->where('id_anggota', $anggota->id_anggota)
            ->where('metode_antar', 'antar')
            ->where(function ($w) {
                $w->whereNotNull('id_kurir')->orWhereNotNull('id_kurir_anggota');
            })
            ->where('status_pesanan', '!=', 'selesai')
            ->orderByDesc('tanggal_transaksi')->get();
        $menungguKurir = Penjualan::where('id_anggota', $anggota->id_anggota)
            ->where('metode_antar', 'antar')
            ->whereNull('id_kurir')->whereNull('id_kurir_anggota')
            ->where('status_pesanan', '!=', 'selesai')
            ->orderByDesc('tanggal_transaksi')->get();
        $templatesUser = ChatTemplate::jenis('user')->urutan()->get();
        return view('chat.index', compact('anggota', 'pesananChat', 'menungguKurir', 'templatesUser'));
    }

    public function kirimUser(Request $request)
    {
        if (!session('pelanggan_id')) {
            return response()->json(['ok' => false], 401);
        }
        $request->validate([
            'id_penjualan' => 'required|integer',
            'pesan'        => 'required|string|max:500',
            'jenis'        => 'sometimes|in:teks,template,lokasi',
            'template_kode'=> 'sometimes|string|max:30',
            'lokasi_lat'   => 'sometimes|numeric|between:-90,90',
            'lokasi_lng'   => 'sometimes|numeric|between:-180,180',
        ]);
        $anggota = Anggota::findOrFail(session('pelanggan_id'));
        $penjualan = Penjualan::where('id_penjualan', $request->id_penjualan)
            ->where('id_anggota', $anggota->id_anggota)->first();
        if (!$penjualan) return response()->json(['ok' => false], 404);
        if (!$penjualan->sudahAdaKurir()) return response()->json(['ok' => false], 422);

        $jenis   = $request->jenis ?? 'teks';
        $kodeTpl = $request->template_kode;

        $isiPesan = trim($request->pesan);
        if ($jenis === 'template' && $kodeTpl) {
            $tpl = ChatTemplate::where('kode', $kodeTpl)->jenis('user')->first();
            if ($tpl) {
                $isiPesan = $tpl->isi;
            }
        }

        $pesan = ChatPesan::create([
            'id_penjualan'   => $penjualan->id_penjualan,
            'id_anggota'     => $anggota->id_anggota,
            'id_kurir'       => $penjualan->id_kurir,
            'id_kurir_anggota'=> $penjualan->id_kurir_anggota,
            'pengirim'       => 'user',
            'nama_pengirim'  => $anggota->nama,
            'pesan'          => $isiPesan,
            'jenis'          => $jenis,
            'template_kode'  => $kodeTpl,
            'lokasi_lat'     => $request->lokasi_lat,
            'lokasi_lng'     => $request->lokasi_lng,
            'dibaca'         => false,
        ]);

        // Auto-balas kurir jika ada template otomatis yang cocok
        $this->cobaAutoBalasKurir($penjualan, $isiPesan, $anggota->nama);

        return response()->json(['ok' => true, 'id' => $pesan->id]);
    }

    private function cobaAutoBalasKurir(Penjualan $penjualan, string $pesanMasuk, string $namaUser)
    {
        $pesanLower = mb_strtolower($pesanMasuk);
        $template = ChatTemplate::query()
            ->jenis('kurir')
            ->otomatis()
            ->whereNotNull('trigger_kata_kunci')
            ->urutan()
            ->get()
            ->first(function ($t) use ($pesanLower) {
                $kunci = mb_strtolower($t->trigger_kata_kunci);
                return str_contains($pesanLower, $kunci);
            });

        if (!$template) {
            return;
        }

        ChatPesan::create([
            'id_penjualan'   => $penjualan->id_penjualan,
            'id_anggota'     => $penjualan->id_anggota,
            'id_kurir'       => $penjualan->id_kurir,
            'id_kurir_anggota'=> $penjualan->id_kurir_anggota,
            'pengirim'       => 'kurir',
            'nama_pengirim'  => self::A::namaKurirEfektif(),
            'pesan'          => $template->isi,
            'jenis'          => 'template',
            'template_kode'  => $template->kode,
            'lokasi_lat'     => null,
            'lokasi_lng'     => null,
            'dibaca'         => false,
        ]);
    }

    public function kurir()
    {
        $efektif = self::A::kurirEfektif();
        if (!$efektif) {
            return redirect()->route('mitra.login');
        }
        // Anti-campur: halaman chat kurir hanya untuk mode kurir.
        // Kalau masih mode user/petugas, arahkan pindah mode dulu.
        if (self::A::modeAktif() !== 'kurir') {
            session(['mode_aktif' => 'kurir']);
        }
        $namaKurir = self::A::namaKurirEfektif();
        $templatesKurir = ChatTemplate::jenis('kurir')->urutan()->get();
        return view('kurir.chat', ['percakapan' => $this->daftarPercakapan(), 'namaKurir' => $namaKurir, 'templatesKurir' => $templatesKurir]);
    }
    public function kirimKurir(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'required|integer',
            'pesan'        => 'required|string|max:500',
            'jenis'        => 'sometimes|in:teks,template,lokasi',
            'template_kode'=> 'sometimes|string|max:30',
            'lokasi_lat'   => 'sometimes|numeric|between:-90,90',
            'lokasi_lng'   => 'sometimes|numeric|between:-180,180',
        ]);
        $efektif = self::A::kurirEfektif();
        if (!$efektif) return response()->json(['ok' => false], 403);
        $penjualan = Penjualan::find($request->id_penjualan);
        if (!$penjualan || !$this->milikKurir($penjualan, $efektif)) {
            return response()->json(['ok' => false], 403);
        }
        $jenis   = $request->jenis ?? 'teks';
        $kodeTpl = $request->template_kode;
        $isiPesan = trim($request->pesan);
        if ($jenis === 'template' && $kodeTpl) {
            $tpl = ChatTemplate::where('kode', $kodeTpl)->jenis('kurir')->first();
            if ($tpl) {
                $isiPesan = $tpl->isi;
            }
        }
        $data = [
            'id_penjualan'   => $penjualan->id_penjualan,
            'id_anggota'     => $penjualan->id_anggota,
            'id_kurir'       => $penjualan->id_kurir,
            'id_kurir_anggota'=> $penjualan->id_kurir_anggota,
            'pengirim'       => 'kurir',
            'nama_pengirim'  => self::A::namaKurirEfektif(),
            'pesan'          => $isiPesan,
            'jenis'          => $jenis,
            'template_kode'  => $kodeTpl,
            'lokasi_lat'     => $request->lokasi_lat,
            'lokasi_lng'     => $request->lokasi_lng,
            'dibaca'         => false,
        ];
        if ($efektif['tipe'] === 'anggota') {
            $data['id_kurir_anggota'] = $efektif['id'];
        } else {
            $data['id_kurir'] = $efektif['id'];
        }
        ChatPesan::create($data);
        return response()->json(['ok' => true]);
    }

    public function pesan(Request $request)
    {
        $idPenjualan = (int) $request->query('id_penjualan', 0);
        $penjualan = Penjualan::find($idPenjualan);
        if (!$penjualan) return response()->json(['ok' => false], 404);
        // Tentukan sisi pengakses TANPA tercampur:
        // - user hanya boleh membaca pesanan miliknya sendiri,
        // - kurir hanya boleh membaca pesanan yang ia antar.
        if (self::A::modeAktif() === 'kurir') {
            $ef = self::A::kurirEfektif();
            if (!$ef || !$this->milikKurir($penjualan, $ef)) {
                return response()->json(['ok' => false], 403);
            }
            $sebagaiKurir = true;
        } else {
            if (!session('pelanggan_id') || (int) $penjualan->id_anggota !== (int) session('pelanggan_id')) {
                return response()->json(['ok' => false], 403);
            }
            $sebagaiKurir = false;
        }
        ChatPesan::where('id_penjualan', $idPenjualan)
            ->where('pengirim', $sebagaiKurir ? 'user' : 'kurir')
            ->where('dibaca', false)->update(['dibaca' => true]);
        $pesan = ChatPesan::where('id_penjualan', $idPenjualan)
            ->orderByDesc('id')->limit(self::MAKS_PESAN)->get()->reverse()->values()
            ->map(fn (ChatPesan $p) => [
                'id' => $p->id, 'pengirim' => $p->pengirim, 'pesan' => $p->pesan,
                'waktu' => optional($p->created_at)->format('H:i'),
            ]);
        return response()->json(['ok' => true, 'pesan' => $pesan]);
    }
    public function percakapan()
    {
        if (!self::A::kurirEfektif()) return response()->json(['ok' => false], 403);
        return response()->json(['ok' => true, 'percakapan' => $this->daftarPercakapan()]);
    }

    private function milikKurir(Penjualan $penjualan, ?array $efektif): bool
    {
        if (!$efektif) return false;
        if ($efektif['tipe'] === 'anggota') {
            return $penjualan->id_kurir_anggota !== null && (int) $penjualan->id_kurir_anggota === (int) $efektif['id'];
        }
        return $penjualan->id_kurir !== null && (int) $penjualan->id_kurir === (int) $efektif['id'];
    }

    private function daftarPercakapan(): array
    {
        $efektif = self::A::kurirEfektif();
        $q = Penjualan::with('anggota')->where('status_pesanan', '!=', 'selesai');
        if ($efektif && $efektif['tipe'] === 'anggota') {
            $q->where('id_kurir_anggota', $efektif['id']);
        } elseif ($efektif) {
            $q->where('id_kurir', $efektif['id']);
        } else {
            $q->whereRaw('1 = 0');
        }
        $pesanan = $q->orderByDesc('tanggal_transaksi')->get();
        $ids = $pesanan->pluck('id_penjualan');
        $terakhir = ChatPesan::whereIn('id_penjualan', $ids)->orderByDesc('id')->get()->unique('id_penjualan');
        $belum = ChatPesan::selectRaw('id_penjualan, COUNT(*) as jumlah')
            ->whereIn('id_penjualan', $ids)->where('pengirim', 'user')->where('dibaca', false)
            ->groupBy('id_penjualan')->pluck('jumlah', 'id_penjualan');
        return $pesanan->map(function (Penjualan $p) use ($terakhir, $belum) {
            $m = $terakhir->firstWhere('id_penjualan', $p->id_penjualan);
            return [
                'id_penjualan' => $p->id_penjualan, 'nama' => $p->anggota->nama ?? 'Pelanggan',
                'no_struk' => $p->no_struk, 'status_pesanan' => $p->status_pesanan,
                'pesan_terakhir' => $m->pesan ?? 'Belum ada pesan — sapa pelanggan dulu.',
                'waktu'          => $m ? optional($m->created_at)->format('H:i') : '',
                'pengirim'       => $m->pengirim ?? 'kosong',
                'belum_dibaca'   => (int) ($belum[$p->id_penjualan] ?? 0),
            ];
        })->values()->all();
    }
    /** Kurir mengirim lokasi GPS terkini untuk tracking real-time. */
    public function kurirLokasi(Request $request)
    {
        $efektif = self::A::kurirEfektif();

        if (!$efektif) return response()->json(['ok' => false], 403);

        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        // GPS disimpan ke DB (bukan session) agar sisi USER dapat membaca
        // lokasi kurir lintas-sesi saat tracking peta.
        $dataLokasi = [
            'kurir_lat'                   => round((float) $request->latitude, 7),
            'kurir_lng'                   => round((float) $request->longitude, 7),
            'kurir_lokasi_diperbarui_pada'=> now(),
        ];
        if ($efektif['tipe'] === 'anggota') {
            Anggota::findOrFail($efektif['id'])->update($dataLokasi);
        } else {
            // tipe 'user' (akun mitra) & 'petugas' (kasir) sama-sama dari tabel users
            User::where('id_user', $efektif['id'])->update($dataLokasi);
        }

        return response()->json(['ok' => true]);
    }

    /** User mengambil lokasi terkini kurir yang mengantarkan pesanannya. */
    public function kurirLokasiTerbaru(Request $request)
    {
        if (!session('pelanggan_id')) {
            return response()->json(['ok' => false], 401);
        }
        $idPenjualan = (int) $request->query('id_penjualan', 0);
        $penjualan = Penjualan::where('id_penjualan', $idPenjualan)
            ->where('id_anggota', session('pelanggan_id'))->first();
        if (!$penjualan) return response()->json(['ok' => false], 404);

        $lat = $lng = null;
        $waktu = null;

        if ($penjualan->id_kurir_anggota) {
            $kurir = Anggota::find($penjualan->id_kurir_anggota);
            if ($kurir && $kurir->kurir_lat && $kurir->kurir_lng) {
                $lat = $kurir->kurir_lat;
                $lng = $kurir->kurir_lng;
                $waktu = $kurir->kurir_lokasi_diperbarui_pada;
            }
        } elseif ($penjualan->id_kurir) {
            $kurir = User::find($penjualan->id_kurir);
            if ($kurir && $kurir->kurir_lat && $kurir->kurir_lng) {
                $lat = $kurir->kurir_lat;
                $lng = $kurir->kurir_lng;
                $waktu = $kurir->kurir_lokasi_diperbarui_pada;
            }
        }

        if ($lat === null || $lng === null) {
            return response()->json(['ok' => false, 'message' => 'Lokasi kurir belum tersedia.']);
        }

        return response()->json([
            'ok'       => true,
            'latitude' => (float) $lat,
            'longitude'=> (float) $lng,
            'waktu'    => $waktu ? \Illuminate\Support\Carbon::parse($waktu)->format('Y-m-d H:i:s') : null,
            'nama_kurir'=> $penjualan->namaKurir(),
        ]);
    }
}


