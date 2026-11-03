@extends('layouts.app')

@section('content')
<style>
    .kur-hero{background:linear-gradient(135deg,#7f1d1d,#c8102e 60%,#ef4444);border-radius:18px;padding:24px 26px;color:#fff}
    .kur-sec-title{font-weight:800;font-size:15px;margin:22px 0 12px;display:flex;align-items:center;gap:8px}
    .kur-card{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:18px;margin-bottom:14px;box-shadow:0 6px 18px rgba(200,16,46,.06)}
    .kur-card.saya{border-left:4px solid #dc2626}
    .kur-map{background:#fef2f2;border-radius:12px;padding:12px 14px;font-size:13px}
    .map-frame{width:100%;height:160px;border:0;border-radius:10px;display:block;background:#fee2e2;margin-top:8px}
    .pill{display:inline-block;border-radius:20px;padding:4px 12px;font-size:11px;font-weight:800}
    .btn-aksi{border-radius:10px;font-weight:700;font-size:13px}
</style>

<div class="kur-hero mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-motorcycle me-2"></i>Aplikasi Mitra KOPMA MART</h4>
        <small style="opacity:.9">Ambil pesanan &bull; antar ke rumah user &bull; chat 1-on-1 &bull; update status</small>
    </div>
    <span class="pill" style="background:rgba(255,255,255,.2);color:#fff">
        <i class="fa-solid fa-user-shield me-1"></i> {{ session('mitra_nama') ?? 'Mitra' }}
    </span>
    <form action="{{ route('mitra.logout') }}" method="POST" class="m-0">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-light" onclick="return confirm('Logout mitra?')">
            <i class="fa-solid fa-right-to-bracket me-1"></i> Keluar
        </button>
    </form>
</div>

{{-- =================== PESANAN SAYA (sudah diambil) =================== --}}
<div class="kur-sec-title text-danger"><i class="fa-solid fa-route"></i> Pesanan Saya
    <span class="badge bg-danger ms-1">{{ $pesananSaya->count() }}</span>
</div>

@if($pesananSaya->isEmpty())
    <div class="text-center py-4 bg-white rounded-4 border mb-2">
        <div style="font-size:34px">🧑‍🔧</div>
        <p class="text-muted small mb-0 mt-1">Belum ada pesanan yang Anda ambil. Silakan ambil dari “Pesanan Masuk” di bawah.</p>
    </div>
@else
    <div class="row g-3">
        @foreach($pesananSaya as $p)
            @php
                $warna = ['menunggu' => '#fef3c7;color:#b45309', 'diproses' => '#dbeafe;color:#1d4ed8', 'diantar' => '#e0e7ff;color:#4338ca'];
                $label = ['menunggu' => 'MENUNGGU', 'diproses' => 'DIPROSES', 'diantar' => 'DIANTAR'];
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="kur-card saya h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong class="small">{{ $p->no_struk }}</strong>
                            <div class="text-muted" style="font-size:12px">{{ $p->tanggal_transaksi }}</div>
                        </div>
                        <span class="pill" style="background:{{ $warna[$p->status_pesanan] ?? '#f3f4f6' }}">{{ $label[$p->status_pesanan] ?? strtoupper($p->status_pesanan) }}</span>
                    </div>
                    <div class="mt-2 small">
                        <div><i class="fa-solid fa-user text-danger me-1"></i> {{ $p->anggota->nama ?? 'Pelanggan' }} ({{ $p->anggota->no_hp ?? '-' }})</div>
                        <div class="kur-map mt-2">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                            <strong>{{ $p->alamat_antar ?? 'Alamat belum diisi' }}</strong>
                            @if($p->sudahPunyaTitikAntar())
                                <div class="text-danger" style="font-size:11px">Titik GPS: {{ $p->titik_antar }}</div>
                            @endif
                            @if($p->catatan)<div class="text-muted fst-italic mt-1">Catatan: "{{ $p->catatan }}"</div>@endif
                        </div>
<div class="kur-map mt-2">
    <i class="fa-solid fa-location-dot text-danger me-1"></i>
    <iframe class="map-frame" title="Peta tujuan pesanan {{ $p->no_struk }}"
            src="https://www.google.com/maps?q={{ rawurlencode($p->titik_antar) }}&amp;z=16&amp;output=embed"
            loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
    <a href="https://www.google.com/maps/search/?api=1&amp;query={{ rawurlencode($p->titik_antar) }}"
       target="_blank" rel="noopener" class="btn btn-sm btn-outline-danger mt-1">
        <i class="fa-solid fa-route me-1"></i> Buka Rute
    </a>
</div>
                        <div class="mt-2">
                            @foreach($p->details as $d)
                                <span class="badge bg-light text-dark border me-1 mb-1">{{ $d->barang->nama_barang ?? '-' }} x{{ $d->jumlah }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                        <strong class="text-danger">Rp {{ number_format($p->total_netto, 0, ',', '.') }}</strong>
                        <div class="d-flex gap-2">
                            <a href="{{ route('kurir.chat', ['pesanan' => $p->id_penjualan]) }}"
                               class="btn btn-outline-danger btn-aksi" title="Chat pelanggan pesanan ini">
                                <i class="fa-solid fa-comments"></i> Chat
                            </a>
                            @if($p->status_pesanan === 'diproses')
                                <form action="{{ route('kurir.update', $p->id_penjualan) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="diantar">
                                    <button type="submit" class="btn btn-primary btn-aksi"><i class="fa-solid fa-motorcycle me-1"></i> Mulai Antar</button>
                                </form>
                            @elseif($p->status_pesanan === 'diantar')
                                <form action="{{ route('kurir.update', $p->id_penjualan) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="selesai">
                                    <button type="submit" class="btn btn-danger btn-aksi"><i class="fa-solid fa-circle-check me-1"></i> Sampai</button>
                                </form>
                            @else
                                <form action="{{ route('kurir.update', $p->id_penjualan) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="diproses">
                                    <button type="submit" class="btn btn-outline-primary btn-aksi"><i class="fa-solid fa-box me-1"></i> Proses</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif


{{-- =================== PESANAN MASUK (pool, belum diambil) =================== --}}
<div class="kur-sec-title text-primary"><i class="fa-solid fa-inbox"></i> Pesanan Masuk
    <span class="badge bg-primary ms-1">{{ $pesananMasuk->count() }}</span>
    <small class="text-muted fw-normal ms-1" style="font-size:12px">— ambil untuk jadi tugas Anda</small>
</div>

@if($pesananMasuk->isEmpty())
    <div class="text-center py-5 bg-white rounded-4 border">
        <div style="font-size:54px">🛵</div>
        <h5 class="fw-bold mt-2">Tidak ada pesanan masuk</h5>
        <p class="text-muted small">Pesanan berbayar dengan metode "Antar ke Rumah" akan muncul di sini.</p>
    </div>
@else
    <div class="row g-3">
        @foreach($pesananMasuk as $p)
            <div class="col-md-6 col-xl-4">
                <div class="kur-card h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong class="small">{{ $p->no_struk }}</strong>
                            <div class="text-muted" style="font-size:12px">{{ $p->tanggal_transaksi }}</div>
                        </div>
                        <span class="pill" style="background:#fef3c7;color:#b45309">MENUNGGU MITRA</span>
                    </div>
<div class="kur-map mt-2">
    <i class="fa-solid fa-location-dot text-danger me-1"></i>
    <iframe class="map-frame" title="Peta tujuan pesanan {{ $p->no_struk }}"
            src="https://www.google.com/maps?q={{ rawurlencode($p->titik_antar) }}&amp;z=16&amp;output=embed"
            loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
    <a href="https://www.google.com/maps/search/?api=1&amp;query={{ rawurlencode($p->titik_antar) }}"
       target="_blank" rel="noopener" class="btn btn-sm btn-outline-danger mt-1">
        <i class="fa-solid fa-route me-1"></i> Buka Rute
    </a>
</div>
                    <div class="mt-2 small">
                        <div><i class="fa-solid fa-user text-danger me-1"></i> {{ $p->anggota->nama ?? 'Pelanggan' }} ({{ $p->anggota->no_hp ?? '-' }})</div>
                        <div class="kur-map mt-2">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                            <strong>{{ $p->alamat_antar ?? 'Alamat belum diisi' }}</strong>
                            @if($p->sudahPunyaTitikAntar())
                                <div class="text-danger" style="font-size:11px">Titik GPS: {{ $p->titik_antar }}</div>
                            @endif
                            @if($p->catatan)<div class="text-muted fst-italic mt-1">Catatan: "{{ $p->catatan }}"</div>@endif
                        </div>
                        <div class="mt-2">
                            @foreach($p->details as $d)
                                <span class="badge bg-light text-dark border me-1 mb-1">{{ $d->barang->nama_barang ?? '-' }} x{{ $d->jumlah }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                        <strong class="text-danger">Rp {{ number_format($p->total_netto, 0, ',', '.') }}</strong>
                        <form action="{{ route('kurir.ambil', $p->id_penjualan) }}" method="POST"
                              onsubmit="return confirm('Ambil pesanan ini? Pesanan akan jadi tugas Anda.')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-aksi"><i class="fa-solid fa-hand-pointer me-1"></i> Ambil Pesanan</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

