@extends('layouts.app')

@section('content')
<style>
    .ord-hero{background:linear-gradient(135deg,#c8102e,#ef4444);border-radius:18px;padding:22px 26px;color:#fff}
    .ord-card{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:18px;margin-bottom:14px}
    .pill{display:inline-block;border-radius:20px;padding:4px 12px;font-size:11px;font-weight:800}
    .track{display:flex;align-items:center;margin-top:14px}
    .track .st{flex:1;text-align:center;font-size:10px;color:#9ca3af;position:relative}
    .track .st::before{content:"";display:block;width:16px;height:16px;border-radius:50%;background:#e5e7eb;margin:0 auto 5px;position:relative;z-index:2}
    .track .st::after{content:"";position:absolute;top:7px;left:-50%;width:100%;height:3px;background:#e5e7eb;z-index:1}
    .track .st:first-child::after{display:none}
    .track .st.done{color:#c8102e;font-weight:700}
    .track .st.done::before{background:#c8102e}
    .track .st.done::after{background:#c8102e}
    /* ===== SIDEBAR GOOGLE MAPS (pengantaran ke rumah) ===== */
    .map-side{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:18px;position:sticky;top:90px}
    .map-frame{width:100%;height:280px;border:0;border-radius:12px;display:block;background:#e5e7eb}
    .map-pick{width:100%;border:1px solid #d1d5db;border-radius:10px;padding:8px 10px;font-size:13px;font-weight:600;color:#374151;background:#fff}
    .map-pick:focus{outline:2px solid rgba(200,16,46,.3);border-color:#c8102e}
</style>

<div class="ord-hero mb-4">
    <h4 class="fw-bold mb-1"><i class="fa-solid fa-box me-2"></i>Pesanan Saya</h4>
    <small style="opacity:.9">Lacak pesanan & status pengantaran ke rumah secara realtime</small>
</div>

@if($pesanan->isEmpty())
    <div class="text-center py-5 bg-white rounded-4 border">
        <div style="font-size:54px">📦</div>
        <h5 class="fw-bold mt-2">Belum ada pesanan</h5>
        <p class="text-muted small">Belanja dulu di toko, deh!</p>
        <a href="{{ route('toko.index') }}" class="btn btn-danger fw-bold px-4">Mulai Belanja</a>
    </div>
@else
    @php
        // Pesanan "antar ke rumah" yang punya titik tujuan -> sumber marker peta sidebar.
        // `titik_antar` berisi "lat,lng" hasil GPS bila ada; jika belum, jatuh ke alamat teks.
        $antarList = $pesanan->filter(fn ($p) => $p->metode_antar === 'antar' && filled($p->titik_antar));
        $mapAktif  = $antarList->first();
    @endphp
    <div class="row g-4">
    <div class="col-lg-7">
    @foreach($pesanan as $p)
        @php
            $urut = ['menunggu', 'diproses', 'diantar', 'selesai'];
            if ($p->metode_antar === 'ambil') $urut = ['menunggu', 'diproses', 'selesai'];
            $idx = array_search($p->status_pesanan, $urut);
            $label = ['menunggu' => 'Menunggu Pembayaran', 'diproses' => 'Diproses', 'diantar' => 'Diantar Kurir', 'selesai' => 'Sampai di Rumah'];
            $warnaP = ['menunggu' => '#fef3c7;color:#b45309', 'diproses' => '#dbeafe;color:#1d4ed8', 'diantar' => '#e0e7ff;color:#4338ca', 'selesai' => '#fee2e2;color:#c8102e'];
        @endphp
        <div class="ord-card">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <strong class="small">{{ $p->no_struk }}</strong>
                    <div class="text-muted" style="font-size:12px">{{ $p->tanggal_transaksi }} &bull; {{ $p->metode_antar === 'antar' ? '🚚 Antar ke Rumah' : '🏪 Ambil di Toko' }} &bull; <span class="text-uppercase">{{ $p->metode_bayar }}</span></div>
                </div>
                <div class="text-end">
                    <span class="pill" style="background:{{ $warnaP[$p->status_pesanan] }}">{{ strtoupper($label[$p->status_pesanan]) }}</span>
                    <span class="pill" style="background:{{ $p->status_bayar === 'lunas' ? '#fee2e2;color:#c8102e' : '#fef3c7;color:#b45309' }}">{{ $p->status_bayar === 'lunas' ? 'LUNAS' : 'BELUM BAYAR' }}</span>
                </div>
            </div>

            <div class="small text-muted mt-2">
                @foreach($p->details as $d)
                    {{ $d->barang->nama_barang ?? '-' }} x{{ $d->jumlah }}{{ !$loop->last ? ',' : '' }}
                @endforeach
            </div>
            @if($p->alamat_antar)
                <div class="small mt-1"><i class="fa-solid fa-location-dot text-danger"></i> {{ $p->alamat_antar }}</div>
            @endif
            @if($p->catatan)
                <div class="small mt-1 fst-italic">"{{ $p->catatan }}"</div>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-2">
                <strong class="text-danger">Rp {{ number_format($p->total_netto, 0, ',', '.') }}</strong>
                <div class="d-flex gap-2">
                    @if($p->status_bayar === 'belum_bayar')
                        <a href="{{ route('belanja.bayar', $p->id_penjualan) }}" class="btn btn-warning btn-sm fw-bold">
                            <i class="fa-solid fa-credit-card me-1"></i> Bayar Sekarang
                        </a>
                    @endif
                    <a href="{{ route('belanja.bayar', $p->id_penjualan) }}" class="btn btn-outline-danger btn-sm fw-bold">Detail</a>
                    @if($p->metode_antar === 'antar' && $p->sudahAdaKurir() && $p->status_pesanan !== 'selesai')
                        <a href="{{ route('chat.index') }}" class="btn btn-danger btn-sm fw-bold" title="Chat mitra kurir pesanan ini">
                            <i class="fa-solid fa-comments me-1"></i> Chat Kurir
                        </a>
                    @endif
                    @if(!in_array($p->status_pesanan, ['diantar', 'selesai']))
                        <form action="{{ route('pesanan.batalkan', $p->id_penjualan) }}" method="POST" class="m-0"
                              onsubmit="return confirm('Batalkan pesanan {{ $p->no_struk }}? Stok produk akan dikembalikan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold">
                                <i class="fa-solid fa-trash me-1"></i> Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="track">
                @foreach($urut as $u)
                    <div class="st {{ array_search($u, $urut) <= $idx ? 'done' : '' }}">{{ $label[$u] }}</div>
                @endforeach
            </div>
        </div>
    @endforeach
    </div>{{-- /col-lg-7 daftar pesanan --}}

    {{-- ===== SIDEBAR GOOGLE MAPS: lacak pengantaran ke rumah ===== --}}
    <div class="col-lg-5">
        <div class="map-side">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-map-location-dot me-1 text-danger"></i> Lacak Pengantaran</h6>
            <small class="text-muted d-block mb-3">Peta alamat tujuan kurir KOPMA untuk pesanan antar ke rumah.</small>

            @if($antarList->isEmpty())
                <div class="text-center py-4 text-muted small rounded-3" style="background:#f8fafc">
                    <div style="font-size:38px">🗺️</div>
                    Belum ada pesanan <strong>antar ke rumah</strong>.<br>
                    Checkout dengan metode "Antar ke Rumah" agar alammu muncul di peta.
                    <div class="mt-2">
                        <a href="{{ route('keranjang.index') }}" class="btn btn-danger btn-sm fw-bold"><i class="fa-solid fa-cart-shopping me-1"></i> Belanja Sekarang</a>
                    </div>
                </div>
            @else
                @if($antarList->count() > 1)
                    <select class="map-pick mb-3" id="mapPick" onchange="mapPilih(this.value)">
                        @foreach($antarList as $p)
                            <option value="{{ $p->id_penjualan }}"
                                    data-titik="{{ $p->titik_antar }}"
                                    data-struk="{{ $p->no_struk }}"
                                    data-status="{{ $p->status_pesanan }}"
                                    {{ $p->id_penjualan === $mapAktif->id_penjualan ? 'selected' : '' }}>
                                {{ $p->no_struk }} — {{ \Illuminate\Support\Str::limit($p->alamat_antar ?: $p->titik_antar, 34) }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <iframe id="mapFrame" class="map-frame" loading="lazy" allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps?q={{ rawurlencode($mapAktif->titik_antar) }}&z=17&output=embed"></iframe>

                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <small class="text-muted d-block" style="font-size:12px">Tujuan</small>
                            <strong class="small" id="mapAlamat">{{ $mapAktif->alamat_antar ?: 'Titik GPS: ' . $mapAktif->titik_antar }}</strong>
                            @if($mapAktif->sudahPunyaTitikAntar())
                                <div class="text-danger" style="font-size:11px">
                                    <i class="fa-solid fa-location-dot me-1"></i> Titik GPS: {{ $mapAktif->titik_antar }}
                                </div>
                            @endif
                        </div>
                        <span class="pill" id="mapStatus" style="background:{{ ['menunggu' => '#fef3c7;color:#b45309', 'diproses' => '#dbeafe;color:#1d4ed8', 'diantar' => '#e0e7ff;color:#4338ca', 'selesai' => '#fee2e2;color:#c8102e'][$mapAktif->status_pesanan] ?? '#f3f4f6;color:#374151' }}">
                            {{ strtoupper(['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'diantar' => 'Diantar', 'selesai' => 'Selesai'][$mapAktif->status_pesanan] ?? $mapAktif->status_pesanan) }}
                        </span>
                    </div>
                    <a id="mapLink" class="btn btn-outline-danger btn-sm fw-bold w-100 mt-3"
                       href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($mapAktif->titik_antar) }}"
                       target="_blank" rel="noopener">
                        <i class="fa-solid fa-diamond-turn-right me-1"></i> Buka Rute di Google Maps
                    </a>
                </div>
            @endif
        </div>
    </div>
    </div>{{-- /row --}}
@endif
@endsection

@push('scripts')
<script>
    // Ganti peta sidebar sesuai pesanan antar yang dipilih (jika ada >1)
    function mapPilih(idPenjualan) {
        const opt = document.querySelector('#mapPick option[value="' + idPenjualan + '"]');
        if (!opt) return;
        const alamat = opt.dataset.alamat;
        document.getElementById('mapFrame').src =
            'https://www.google.com/maps?q=' + encodeURIComponent(alamat) + '&output=embed';
        document.getElementById('mapAlamat').textContent = alamat;
        document.getElementById('mapLink').href =
            'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(alamat);
    }
</script>
@endpush
