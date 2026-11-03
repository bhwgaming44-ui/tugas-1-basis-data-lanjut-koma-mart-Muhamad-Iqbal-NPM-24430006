@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Str;

    /**
     * Ambil URL gambar produk dari kolom `gambar`.
     * Mendukung: URL penuh (https://...), path di /public (images/produk/a.jpg),
     * dan path storage (produk/a.jpg -> /storage/produk/a.jpg).
     */
    $gambarUrl = function ($b) {
        $g = trim((string) ($b->gambar ?? ''));
        if ($g === '') return null;
        if (Str::startsWith($g, ['http://', 'https://', '//', 'data:'])) return $g;
        $g = ltrim($g, '/');
        if (file_exists(public_path($g))) return asset($g);
        return asset('storage/' . preg_replace('#^(public/|storage/)#', '', $g));
    };

    // Placeholder bawaan (SVG inline, tidak butuh internet)
    $noImg = 'data:image/svg+xml;utf8,' . rawurlencode(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><rect width="200" height="200" fill="#fff5f5"/>'
        . '<path d="M60 80l40-20 40 20v50l-40 20-40-20z" fill="none" stroke="#e11d2e" stroke-width="6" stroke-linejoin="round"/>'
        . '<path d="M60 80l40 20 40-20M100 100v50" fill="none" stroke="#e11d2e" stroke-width="6" stroke-linejoin="round"/></svg>'
    );

    $persenDiskon = fn ($b) => $b->harga_jual_umum > 0
        ? (int) round(($b->harga_jual_umum - $b->harga_jual_anggota) / $b->harga_jual_umum * 100)
        : 0;

    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

    // Promo: produk dengan diskon member terbesar (hingga 8 produk pilihan)
    $promoBarang = $semuaBarang->sortByDesc(fn ($b) => $persenDiskon($b))->take(8)->values();

    // Kategori: ambil gambar dari produk pertama yang punya foto
    $kategoriList = $semuaBarang
        ->groupBy(fn ($b) => $b->kategori->nama_kategori ?? 'Lainnya')
        ->map(fn ($items, $nama) => [
            'nama'  => $nama,
            'total' => $items->count(),
            'item'  => $items->first(fn ($x) => $gambarUrl($x)),
        ])
        ->take(8);
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --km-red: #d90f23;
        --km-red-dark: #a8071a;
        --km-red-soft: #fff1f2;
        --km-ink: #1c1917;
        --km-muted: #78716c;
        --km-line: #eceae8;
        --km-white: #ffffff;
        --km-green: #15803d;
    }
    .km-page { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: var(--km-ink); }
    .km-page a { text-decoration: none; }
    .km-section { margin-bottom: 36px; }
    .km-section-head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 14px; }
    .km-section-head h2 { font-size: 20px; font-weight: 800; margin: 0; letter-spacing: -0.3px; }
    .km-section-head small { color: var(--km-muted); font-size: 13px; display: block; font-weight: 500; margin-top: 2px; }
    .km-link { color: var(--km-red); font-weight: 700; font-size: 14px; }
    .km-link:hover { color: var(--km-red-dark); }

    /* ===== HERO ===== */
    .km-hero {
        background: var(--km-red);
        border-radius: 24px;
        padding: 32px 36px;
        color: #fff;
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 28px;
        align-items: center;
        margin-bottom: 28px;
    }
    .km-hero h1 { font-size: 36px; font-weight: 800; letter-spacing: -1px; line-height: 1.1; margin: 0 0 10px; }
    .km-hero p { font-size: 15px; opacity: .92; margin: 0 0 18px; max-width: 480px; }
    .km-hero-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .km-chip { background: #fff; color: var(--km-red); font-weight: 700; font-size: 12px; padding: 5px 12px; border-radius: 999px; }
    .km-chip.ghost { background: rgba(255,255,255,.16); color: #fff; }
    .km-search {
        display: flex; background: #fff; border-radius: 14px; padding: 5px; max-width: 520px;
        box-shadow: 0 8px 24px rgba(0,0,0,.14);
    }
    .km-search input { flex: 1; border: 0; outline: 0; padding: 10px 14px; font-size: 14px; background: transparent; color: var(--km-ink); }
    .km-search button { border: 0; background: var(--km-red); color: #fff; font-weight: 700; padding: 0 20px; border-radius: 10px; }
    .km-search button:hover { background: var(--km-red-dark); }

    /* Pencarian realtime: wrap relatif + panel saran produk */
    .km-search-wrap { position: relative; max-width: 520px; }
    .km-search { position: static; }
    .km-search-hasil {
        position: absolute; top: calc(100% + 8px); left: 0; right: 0;
        background: #fff; border-radius: 14px; box-shadow: 0 12px 32px rgba(0,0,0,.18);
        max-height: 340px; overflow-y: auto; z-index: 1050; display: none;
    }
    .km-search-hasil.tampil { display: block; }
    .km-search-info { padding: 8px 14px; font-size: 12.5px; font-weight: 600; color: var(--km-muted); border-bottom: 1px solid var(--km-line); }
    .km-search-info.kosong { color: var(--km-red); }
    .km-search-item {
        display: flex; align-items: center; gap: 10px; width: 100%;
        padding: 9px 14px; border: 0; background: #fff; text-align: left;
        font-size: 13.5px; color: var(--km-ink); cursor: pointer; text-decoration: none;
        border-bottom: 1px solid var(--km-line);
    }
    .km-search-item:last-child { border-bottom: 0; }
    .km-search-item:hover, .km-search-item:focus { background: var(--km-red-soft); outline: none; }
    .km-search-item img { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; background: var(--km-red-soft); flex-shrink: 0; }
    .km-search-item .nama { font-weight: 700; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .km-search-item .harga { color: var(--km-red); font-weight: 700; white-space: nowrap; }
    .km-search-bersih {
        position: absolute; right: 110px; top: 50%; transform: translateY(-50%);
        border: 0; background: transparent; color: var(--km-muted); font-size: 15px;
        cursor: pointer; padding: 4px 6px; display: none; z-index: 1;
    }
    .km-search-bersih.tampil { display: block; }
    .km-search-bersih:hover { color: var(--km-red); }
    .km-hero-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
    .km-btn-ghost { border: 1.5px solid rgba(255,255,255,.7); color: #fff; font-weight: 700; font-size: 13px; padding: 8px 16px; border-radius: 10px; }
    .km-btn-ghost:hover { background: #fff; color: var(--km-red); }
    .km-member-card { background: #fff; color: var(--km-ink); border-radius: 18px; padding: 20px; }
    .km-member-card .t { font-size: 13px; color: var(--km-muted); font-weight: 600; }
    .km-member-card .v { font-size: 22px; font-weight: 800; color: var(--km-red); margin-bottom: 12px; }
    .km-member-row { display: flex; justify-content: space-between; font-size: 13px; padding: 8px 0; border-top: 1px solid var(--km-line); }
    .km-member-row span { color: var(--km-muted); }
    .km-member-row strong { font-weight: 700; }

    /* ===== PROMO BANNER GEDE (GAYA REFERENSI) — MODEL AWAL PENDEK, 2 BANNER PER SLIDE ===== */
    /* Kanvas upload tetap 1200 x 600 px, tapi tampil dipadatkan (height 230px + cover) biar pendek. */
    .promo-slider {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background: transparent;
    }
    .promo-track { display: flex; transition: transform .6s cubic-bezier(.4,0,.2,1); }
    /* 1 halaman = 2 banner pendek berdampingan seperti referensi */
    .promo-slide { min-width: 100%; display: flex; gap: 16px; box-sizing: border-box; }
    .promo-inner {
        flex: 1 1 0; min-width: 0; height: 230px; width: auto;
        padding: 28px 36px; color: #fff; border-radius: 20px;
        display: flex; align-items: center; justify-content: space-between; gap: 24px;
        position: relative; overflow: hidden;
        box-shadow: 0 12px 30px rgba(217, 15, 35, 0.18);
    }
    .promo-inner::before {
        content: ""; position: absolute; right: -40px; top: -40px; width: 340px; height: 340px;
        border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }
    .promo-inner.v0 { background: linear-gradient(135deg, #b91c1c 0%, #dc2626 50%, #f87171 100%); }
    .promo-inner.v1 { background: linear-gradient(135deg, #15803d 0%, #16a34a 50%, #4ade80 100%); }
    .promo-inner.v2 { background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #60a5fa 100%); }
    .promo-inner.v3 { background: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #fbbf24 100%); }
    .promo-inner.v4 { background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 50%, #c084fc 100%); }
    .promo-inner.v5 { background: linear-gradient(135deg, #be185d 0%, #e11d48 50%, #fb7185 100%); }
    .promo-inner.v6 { background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #2dd4bf 100%); }
    .promo-inner.v7 { background: linear-gradient(135deg, #475569 0%, #64748b 50%, #94a3b8 100%); }
    .promo-txt { max-width: 60%; position: relative; z-index: 2; min-width: 0; }
    .promo-tag { display: inline-flex; align-items: center; background: #fff; color: var(--km-red); font-weight: 800; font-size: 12px; padding: 5px 14px; border-radius: 999px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .promo-txt h3 { font-size: 24px; font-weight: 800; letter-spacing: -.4px; line-height: 1.2; margin: 0 0 8px; text-shadow: 0 2px 4px rgba(0,0,0,0.12); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .promo-old { text-decoration: line-through; opacity: .85; font-size: 13px; }
    .promo-new { font-size: 26px; font-weight: 900; margin-left: 8px; letter-spacing: -0.5px; }
    .promo-sub { font-size: 12px; opacity: .92; margin-top: 6px; }
    .promo-cta { display: inline-flex; align-items: center; gap: 8px; margin-top: 14px; background: #fff; color: var(--km-red); font-weight: 800; font-size: 13px; border: 0; padding: 9px 20px; border-radius: 999px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); transition: all 0.2s ease; }
    .promo-cta:hover { background: var(--km-red-soft); transform: translateY(-2px); }
    .promo-photo {
        flex: 0 0 auto; width: 38%; max-width: 210px; aspect-ratio: 1 / 1; border-radius: 16px; background: #fff;
        display: flex; align-items: center; justify-content: center; padding: 8px;
        box-shadow: 0 16px 36px rgba(0,0,0,.26); position: relative; z-index: 2; overflow: hidden;
    }
    .promo-photo img { width: 100%; height: 100%; object-fit: cover; border-radius: 14px; transition: transform 0.35s ease; }
    .promo-photo:hover img { transform: scale(1.04); }

    /* Tombol Navigasi Manual Promo (Next & Prev) */
    .promo-nav-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 46px; height: 46px; border-radius: 50%;
        background: rgba(255, 255, 255, 0.95); color: #1e293b;
        border: none; box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; cursor: pointer; z-index: 10;
        transition: all 0.2s ease;
    }
    .promo-nav-btn:hover { background: #fff; color: var(--km-red); transform: translateY(-50%) scale(1.1); }
    .promo-nav-prev { left: 16px; }
    .promo-nav-next { right: 16px; }

    .promo-dots { display: flex; justify-content: center; gap: 8px; margin-top: 14px; }
    .promo-dot { width: 12px; height: 12px; border-radius: 999px; border: 0; padding: 0; background: #d6d3d1; transition: all .25s ease; cursor: pointer; }
    .promo-dot.on { width: 34px; background: var(--km-red); }
    .promo-dot:focus-visible { outline: 2px solid var(--km-red); outline-offset: 3px; }

    /* ===== KATEGORI ===== */
    .km-cat-row { display: grid; grid-template-columns: repeat(8, 1fr); gap: 12px; }
    .km-cat {
        background: #fff; border: 1px solid var(--km-line); border-radius: 16px; padding: 12px;
        display: flex; flex-direction: column; min-height: 124px; color: var(--km-ink);
        transition: border-color .2s, transform .2s;
    }
    .km-cat:hover { border-color: var(--km-red); transform: translateY(-2px); color: var(--km-ink); }
    .km-cat b { font-size: 13px; font-weight: 700; line-height: 1.3; }
    .km-cat small { color: var(--km-muted); font-size: 11px; }
    .km-cat .ph { margin-top: auto; align-self: flex-end; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 10px; }
    .km-cat .ph img { width: 100%; height: 100%; object-fit: cover; }

    /* ===== PRODUCT CARD (FULL SESUAI KOTAK & JELAS) ===== */
    .km-card {
        background: #fff; border: 1px solid var(--km-line); border-radius: 18px; overflow: hidden;
        display: flex; flex-direction: column; position: relative; transition: border-color .2s, box-shadow .2s;
    }
    .km-card:hover { border-color: var(--km-red); box-shadow: 0 12px 30px rgba(217,15,35,.15); }
    .km-disc {
        position: absolute; top: 10px; left: 10px; z-index: 4;
        background: var(--km-red); color: #fff; font-weight: 800; font-size: 11px; padding: 4px 9px; border-radius: 8px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.18);
    }
    .km-petugas-edit-badge {
        position: absolute; top: 10px; right: 10px; z-index: 5;
    }
    .km-btn-edit-foto {
        background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(4px);
        color: #fff; font-size: 11px; font-weight: 700;
        padding: 5px 10px; border-radius: 20px; cursor: pointer;
        display: inline-flex; align-items: center; gap: 5px;
        transition: all 0.2s ease; border: 1px solid rgba(255,255,255,0.4);
        margin: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    }
    .km-btn-edit-foto:hover {
        background: var(--km-red); color: #fff; transform: scale(1.05);
    }
    .km-img {
        width: 100%;
        aspect-ratio: 1 / 1;
        background: #f8fafc;
        position: relative;
        overflow: hidden;
        display: block;
        border-bottom: 1px solid #f1f5f9;
    }
    .km-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        transition: transform .35s ease;
    }
    .km-card:hover .km-img img { transform: scale(1.06); }
    .km-body { padding: 12px 14px 14px; display: flex; flex-direction: column; flex: 1; }
    .km-cat-name { font-size: 11px; color: var(--km-muted); font-weight: 600; margin-bottom: 3px; }
    .km-name {
        font-size: 13px; font-weight: 700; line-height: 1.4; min-height: 36px; margin-bottom: 8px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        color: #1e293b;
    }
    .km-price { margin-top: auto; }
    .km-price .now { font-size: 17px; font-weight: 800; color: var(--km-red); }
    .km-price .old { font-size: 12px; color: #a8a29e; text-decoration: line-through; margin-left: 6px; }
    .km-member-tag { display: inline-block; font-size: 11px; font-weight: 700; color: var(--km-green); margin-top: 2px; }
    .km-foot { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; gap: 8px; }
    .km-stok { font-size: 11px; color: var(--km-muted); }
    .km-stok.low { color: var(--km-red); font-weight: 700; }
    .km-add {
        border: 0; background: var(--km-red); color: #fff; font-weight: 700; font-size: 13px;
        padding: 8px 16px; border-radius: 999px; display: inline-flex; align-items: center; gap: 6px;
    }
    .km-add:hover { background: var(--km-red-dark); }
    .km-add:disabled { background: #d6d3d1; cursor: not-allowed; }
    .km-add:focus-visible { outline: 2px solid var(--km-ink); outline-offset: 2px; }

    .km-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }

    /* ===== BAND HARGA MEMBER (scroll horizontal) ===== */
    .km-band { background: var(--km-red); border-radius: 22px; padding: 22px 0 22px 22px; position: relative; }
    .km-band-head { display: flex; align-items: baseline; justify-content: space-between; padding-right: 22px; margin-bottom: 14px; color: #fff; }
    .km-band-head h2 { font-size: 20px; font-weight: 800; margin: 0; letter-spacing: -.3px; }
    .km-band-head small { font-size: 13px; opacity: .88; display: block; font-weight: 500; margin-top: 2px; }
    .km-band-head a { color: #fff; font-weight: 700; font-size: 14px; }
    .km-scroll { display: flex; gap: 14px; overflow-x: auto; scroll-snap-type: x mandatory; padding: 2px 22px 6px 0; scrollbar-width: none; }
    .km-scroll::-webkit-scrollbar { display: none; }
    .km-scroll .km-card { flex: 0 0 196px; scroll-snap-align: start; border-color: transparent; }
    .km-arrow {
        position: absolute; right: 14px; top: 50%; width: 42px; height: 42px; border-radius: 50%;
        border: 0; background: #fff; color: var(--km-red); box-shadow: 0 6px 16px rgba(0,0,0,.2); z-index: 3;
    }
    .km-arrow:hover { background: var(--km-red-soft); }

    /* ===== PANEL PETUGAS ===== */
    .km-panel { background: #fff; border: 1px solid var(--km-line); border-radius: 18px; overflow: hidden; height: 100%; }
    .km-panel-head { padding: 16px 20px; border-bottom: 1px solid var(--km-line); display: flex; align-items: center; justify-content: space-between; }
    .km-panel-head h3 { font-size: 16px; font-weight: 800; margin: 0; }
    .km-panel table { margin: 0; font-size: 13px; }
    .km-panel th { background: #fafaf9; font-weight: 700; color: var(--km-muted); font-size: 12px; }
    .km-badge-red { background: var(--km-red-soft); color: var(--km-red); font-weight: 800; padding: 3px 10px; border-radius: 999px; font-size: 12px; }
    .km-staff { padding: 28px 22px; text-align: center; }
    .km-staff .ic { width: 60px; height: 60px; border-radius: 50%; background: var(--km-red-soft); color: var(--km-red); display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1199px) { .km-cat-row { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 991px) {
        .km-hero { grid-template-columns: 1fr; padding: 24px; }
        .km-hero h1 { font-size: 28px; }
        .km-grid { grid-template-columns: repeat(3, 1fr); }
        .promo-photo { width: 160px; height: 160px; }
    }
    @media (max-width: 767px) {
        .km-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .km-cat-row { grid-template-columns: repeat(2, 1fr); }
        .promo-inner { padding: 22px; min-height: 0; }
        .promo-txt { max-width: 100%; }
        .promo-txt h3 { font-size: 22px; }
        .promo-new { font-size: 26px; }
        .promo-photo { display: none; }
        .km-scroll .km-card { flex-basis: 160px; }
        .km-arrow { display: none; }
    }
    @media (prefers-reduced-motion: reduce) {
        .promo-track, .km-card, .km-img img, .km-cat { transition: none; }
    }
</style>

<div class="km-page">

    {{-- ===== HERO ===== --}}
    <section class="km-hero">
        <div>
            <div class="km-hero-meta">
                <span class="km-chip"><i class="fa-solid fa-store me-1"></i> Kopma Mart</span>
                <span class="km-chip ghost"><i class="fa-regular fa-clock me-1"></i> Buka setiap hari 07.00 - 22.00</span>
            </div>
            <h1>Belanja hemat setiap hari, harga member lebih murah</h1>
            <p>Sembako, snack, minuman, ATK, dan kebutuhan rumah tangga lengkap dalam satu toko.</p>

            <div class="km-search-wrap">
                <form class="km-search" action="{{ route('toko.index') }}" method="GET" role="search" id="kmSearchForm">
                    <input type="search" name="q" id="kmCariProduk" placeholder="Cari produk di Kopma Mart" aria-label="Cari produk" autocomplete="off" aria-controls="kmSearchHasil" aria-expanded="false">
                    <button type="button" class="km-search-bersih" id="kmCariBersih" aria-label="Bersihkan pencarian"><i class="fa-solid fa-xmark"></i></button>
                    <button type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Cari</button>
                </form>
                <div class="km-search-hasil" id="kmSearchHasil" role="listbox"></div>
            </div>

            @if(session('kasir_id'))
            <div class="km-hero-actions">
                <a href="{{ route('kasir.index') }}" class="km-chip"><i class="fa-solid fa-cash-register me-1"></i> Buka kasir (POS)</a>
                <a href="{{ route('pegawai.index') }}" class="km-btn-ghost"><i class="fa-solid fa-id-badge me-1"></i> Struktur pegawai</a>
            </div>
            @endif
        </div>

        <div class="km-member-card">
            <div class="t">Kartu member</div>
            <div class="v">Daftar gratis</div>
            <div class="km-member-row"><span>Kode toko</span><strong>KOPMA-MART</strong></div>
            <div class="km-member-row"><span>Produk tersedia</span><strong>{{ $semuaBarang->count() }} item</strong></div>
            <div class="km-member-row"><span>Harga khusus</span><strong class="text-danger">Member</strong></div>
        </div>
    </section>

    {{-- ===== PROMO SLIDER ===== --}}
    @if($promoBarang->count())
    <section class="km-section">
        <div class="km-section-head">
            <div>
                <h2>Promo spesial member</h2>
                <small>Produk dengan potongan harga terbesar</small>
            </div>
            <a href="{{ route('toko.index') }}" class="km-link">Lihat semua</a>
        </div>

        <div class="promo-slider" id="promoSlider">
            {{-- Tombol Next & Prev Manual --}}
            <button type="button" class="promo-nav-btn promo-nav-prev" id="promoPrevBtn" aria-label="Slide Sebelumnya">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="promo-nav-btn promo-nav-next" id="promoNextBtn" aria-label="Slide Selanjutnya">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div class="promo-track" id="promoTrack">
                @php $__promoPages = $promoBarang->chunk(2); @endphp
                @foreach($__promoPages as $pi => $page)
                <div class="promo-slide">
                    @foreach($page as $pb)
                    @php $i = $promoBarang->search(fn($x) => $x->id_barang === $pb->id_barang); $i = $i === false ? 0 : $i; @endphp
                    <div class="promo-inner v{{ $i % 8 }}">
                        <div class="promo-txt">
                            <span class="promo-tag"><i class="fa-solid fa-bolt me-1"></i>Hemat {{ $persenDiskon($pb) }}%</span>
                            <h3>{{ $pb->nama_barang }}</h3>
                            <div>
                                <span class="promo-old">{{ $rp($pb->harga_jual_umum) }}</span>
                                <span class="promo-new">{{ $rp($pb->harga_jual_anggota) }}</span>
                            </div>
                            <div class="promo-sub">{{ $pb->kategori->nama_kategori ?? 'Kopma Mart' }} &middot; stok {{ $pb->stok }}</div>
                            <form action="{{ route('belanja.tambah') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="id_barang" value="{{ $pb->id_barang }}">
                                <button type="submit" class="promo-cta" @disabled($pb->stok < 1)>
                                    <i class="fa-solid fa-cart-shopping"></i> Beli sekarang
                                </button>
                            </form>
                        </div>
                        <div class="promo-photo">
                            <img src="{{ $gambarUrl($pb) ?? $noImg }}" alt="{{ $pb->nama_barang }}"
                                 onerror="this.onerror=null;this.src='{{ $noImg }}';">
                        </div>
                    </div>
                    @endforeach
                    {{-- Jika halaman ganjil (1 banner), isi slot kedua dengan banner duplikat agar layout 2 kolom tetap seimbang seperti referensi --}}
                    @if($page->count() === 1)
                    @php $pb = $page->first(); $i = $promoBarang->search(fn($x) => $x->id_barang === $pb->id_barang); $i = $i === false ? 0 : $i; @endphp
                    <div class="promo-inner v{{ $i % 8 }}" aria-hidden="true">
                        <div class="promo-txt">
                            <span class="promo-tag"><i class="fa-solid fa-bolt me-1"></i>Hemat {{ $persenDiskon($pb) }}%</span>
                            <h3>{{ $pb->nama_barang }}</h3>
                            <div>
                                <span class="promo-old">{{ $rp($pb->harga_jual_umum) }}</span>
                                <span class="promo-new">{{ $rp($pb->harga_jual_anggota) }}</span>
                            </div>
                            <div class="promo-sub">{{ $pb->kategori->nama_kategori ?? 'Kopma Mart' }} &middot; stok {{ $pb->stok }}</div>
                        </div>
                        <div class="promo-photo">
                            <img src="{{ $gambarUrl($pb) ?? $noImg }}" alt="" onerror="this.onerror=null;this.src='{{ $noImg }}';">
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        <div class="promo-dots" id="promoDots">
            @foreach($__promoPages as $pi => $page)
            <button type="button" class="promo-dot {{ $pi === 0 ? 'on' : '' }}" data-idx="{{ $pi }}" aria-label="Promo halaman {{ $pi + 1 }}"></button>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ===== KATEGORI ===== --}}
    @if($kategoriList->count())
    <section class="km-section">
        <div class="km-section-head">
            <h2>Kategori belanja</h2>
            <a href="{{ route('toko.index') }}" class="km-link">Lihat semua</a>
        </div>
        <div class="km-cat-row">
            @foreach($kategoriList as $kat)
            <a href="{{ route('toko.index', ['kategori' => $kat['nama']]) }}" class="km-cat">
                <b>{{ Str::limit($kat['nama'], 22) }}</b>
                <small>{{ $kat['total'] }} produk</small>
                <span class="ph">
                    <img src="{{ $kat['item'] ? ($gambarUrl($kat['item']) ?? $noImg) : $noImg }}" alt=""
                         loading="lazy" onerror="this.onerror=null;this.src='{{ $noImg }}';">
                </span>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ===== BAND HARGA MEMBER (scroll horizontal) ===== --}}
    <section class="km-section">
        <div class="km-band">
            <div class="km-band-head">
                <div>
                    <h2>Super hemat harga member</h2>
                    <small>Geser untuk lihat produk lainnya</small>
                </div>
                <a href="{{ route('toko.index') }}">Lihat semua</a>
            </div>
            <div class="km-scroll" id="kmScroll">
                @foreach($promoBarang->concat($semuaBarang->skip(8)->take(7))->unique('id_barang') as $b)
                    @include('partials._km_card', ['b' => $b])
                @endforeach
            </div>
            <button type="button" class="km-arrow" id="kmNext" aria-label="Geser ke kanan"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </section>

    {{-- ===== PRODUK TERLARIS ===== --}}
    <section class="km-section">
        <div class="km-section-head">
            <div>
                <h2>Produk terlaris</h2>
                <small>Harga member sudah tertera, tinggal tambah ke keranjang</small>
            </div>
            <a href="{{ route('toko.index') }}" class="km-link">Lihat semua</a>
        </div>
        <div class="km-grid">
            @foreach($semuaBarang->take(8) as $b)
                @include('partials._km_card', ['b' => $b])
            @endforeach
        </div>
    </section>

    {{-- ===== PANEL PETUGAS (hanya untuk yang login sebagai kasir) ===== --}}
    @if(session('kasir_id'))
    <section class="km-section">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="km-panel">
                    <div class="km-staff">
                        <div class="ic"><i class="fa-solid fa-user-shield"></i></div>
                        <h5 class="fw-bold">Selamat bertugas, {{ session('kasir_nama') }}</h5>
                        <p class="text-muted small mb-3">Masuk sebagai <strong class="text-uppercase">{{ session('kasir_role') }}</strong>. Kelola kasir dan struktur pegawai dari sini.</p>
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            <a href="{{ route('kasir.index') }}" class="btn btn-sm btn-danger fw-bold"><i class="fa-solid fa-cash-register me-1"></i> Buka kasir</a>
                            <a href="{{ route('pegawai.cetak') }}" target="_blank" class="btn btn-sm btn-outline-dark fw-bold"><i class="fa-solid fa-print me-1"></i> Cetak struktur</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="km-panel">
                    <div class="km-panel-head">
                        <h3><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Stok menipis</h3>
                        <span class="km-badge-red">{{ $barangMenipis->count() }} produk</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead><tr><th>Barang</th><th>Sisa</th><th>Min</th></tr></thead>
                            <tbody>
                                @forelse($barangMenipis->take(5) as $item)
                                <tr>
                                    <td>{{ Str::limit($item->nama_barang, 26) }}<br><code class="small">{{ $item->kode_barcode }}</code></td>
                                    <td><span class="km-badge-red">{{ $item->stok }}</span></td>
                                    <td>{{ $item->stok_minimum }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-3 text-muted">Semua stok aman.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="km-panel">
                    <div class="km-panel-head">
                        <h3><i class="fa-solid fa-receipt text-danger me-2"></i>Transaksi terkini</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead><tr><th>No struk</th><th>Total</th><th>Bayar</th></tr></thead>
                            <tbody>
                                @forelse($transaksiTerbaru as $trx)
                                <tr>
                                    <td><span class="fw-bold">{{ $trx->no_struk }}</span><br><small class="text-muted">{{ $trx->anggota ? $trx->anggota->nama : 'Pelanggan umum' }}</small></td>
                                    <td class="text-danger fw-bold">{{ $rp($trx->total_netto) }}</td>
                                    <td><span class="badge bg-secondary text-uppercase">{{ $trx->metode_bayar }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada transaksi hari ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
</div>

@push('scripts')
<script>
    // ===== Slider promo otomatis (5 detik) =====
    (function () {
        const track = document.getElementById('promoTrack');
        const dotsWrap = document.getElementById('promoDots');
        const slider = document.getElementById('promoSlider');
        if (!track || !dotsWrap || !slider) return;

        const total = track.children.length;
        const dots = dotsWrap.querySelectorAll('.promo-dot');
        let idx = 0, timer = null;

        function go(i) {
            idx = ((i % total) + total) % total;
            track.style.transform = 'translateX(-' + (idx * 100) + '%)';
            dots.forEach((d, k) => d.classList.toggle('on', k === idx));
        }
        function start() { stop(); timer = setInterval(() => go(idx + 1), 5000); }
        function stop() { if (timer) { clearInterval(timer); timer = null; } }

        dots.forEach(d => d.addEventListener('click', () => { go(parseInt(d.dataset.idx, 10)); start(); }));
        
        // Navigasi manual prev & next
        const btnPrev = document.getElementById('promoPrevBtn');
        const btnNext = document.getElementById('promoNextBtn');
        if (btnPrev) btnPrev.addEventListener('click', () => { go(idx - 1); start(); });
        if (btnNext) btnNext.addEventListener('click', () => { go(idx + 1); start(); });

        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);

        go(0);
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) start();
    })();

    // ===== Tombol geser band harga member =====
    (function () {
        const sc = document.getElementById('kmScroll');
        const nx = document.getElementById('kmNext');
        if (!sc || !nx) return;
        nx.addEventListener('click', () => {
            const end = sc.scrollLeft + sc.clientWidth >= sc.scrollWidth - 8;
            sc.scrollTo({ left: end ? 0 : sc.scrollLeft + 400, behavior: 'smooth' });
        });
    })();

    // ===== Realtime Upload Foto Produk dari Galeri Laptop (Khusus Petugas) =====
    (function () {
        document.querySelectorAll('.input-upload-km').forEach(inp => {
            inp.addEventListener('change', async function () {
                const id = this.dataset.id;
                const file = this.files[0];
                if (!file) return;

                const label = document.querySelector(`label[for="${this.id}"]`);
                const oldHtml = label ? label.innerHTML : '';
                if (label) label.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

                const fd = new FormData();
                fd.append('gambar', file);
                fd.append('_token', '{{ csrf_token() }}');

                try {
                    const res = await fetch(`/toko/${id}/gambar`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: fd
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        // Update realtime semua elemen gambar dengan ID ini di dashboard
                        document.querySelectorAll(`.img-produk-target-${id}`).forEach(img => {
                            img.src = data.gambar_url + '?t=' + Date.now();
                        });

                        if (label) {
                            label.classList.add('bg-success');
                            label.innerHTML = '<i class="fa-solid fa-check"></i> Berhasil!';
                            setTimeout(() => {
                                label.classList.remove('bg-success');
                                label.innerHTML = oldHtml;
                            }, 2000);
                        }
                    } else {
                        alert(data.message || 'Gagal mengubah foto produk.');
                        if (label) label.innerHTML = oldHtml;
                    }
                } catch (e) {
                    alert('Terjadi kesalahan jaringan saat mengunggah foto.');
                    if (label) label.innerHTML = oldHtml;
                }
            });
        });
    })();

    // ===== Pencarian Realtime di Dashboard (dropdown saran produk, tanpa Enter) =====
    (function () {
        const inp = document.getElementById('kmCariProduk');
        const hasil = document.getElementById('kmSearchHasil');
        const btnBersih = document.getElementById('kmCariBersih');
        if (!inp || !hasil) return;

        // Data semua produk dikirim dari server (nama, harga member, gambar) —
        // filtering murni di client, sama seperti halaman Kelola Produk/Promo.
        const produk = @json($semuaBarang->map(fn ($b) => [
            'id'     => $b->id_barang,
            'nama'   => $b->nama_barang,
            'harga'  => $rp($b->harga_jual_anggota),
            'gambar' => $gambarUrl($b) ?? $noImg,
        ]));

        const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const norm = s => String(s).toLowerCase().trim();
        const tampilPanel = () => hasil.classList.add('tampil');
        const tutupPanel = () => hasil.classList.remove('tampil');

        function cari() {
            const q = norm(inp.value);
            btnBersih.classList.toggle('tampil', inp.value.trim() !== '');
            if (!q) { hasil.innerHTML = ''; tutupPanel(); return; }

            // Semua kata kunci harus cocok di nama produk (AND, case-insensitive)
            const kws = q.split(/\s+/).filter(Boolean);
            const cocok = produk.filter(p => {
                const teks = norm(p.nama);
                return kws.every(k => teks.includes(k));
            });

            let html = '';
            if (cocok.length) {
                html += '<div class="km-search-info">Menampilkan <b>' + cocok.length + '</b> dari <b>' + produk.length + '</b> produk</div>';
                html += cocok.slice(0, 8).map(p =>
                    '<button type="button" class="km-search-item" data-id="' + p.id + '">' +
                    '<img src="' + esc(p.gambar) + '" alt="">' +
                    '<span class="nama">' + esc(p.nama) + '</span>' +
                    '<span class="harga">' + esc(p.harga) + '</span></button>'
                ).join('');
                if (cocok.length > 8) {
                    html += '<div class="km-search-info">+ <b>' + (cocok.length - 8) + '</b> produk lainnya — tekan Enter untuk melihat semua</div>';
                }
            } else {
                // Pesan konsisten dengan fitur pencarian lain
                html = '<div class="km-search-info kosong">Produk tidak ditemukan</div>';
            }
            hasil.innerHTML = html;
            tampilPanel();
        }

        let jeda;
        inp.addEventListener('input', function () { clearTimeout(jeda); jeda = setTimeout(cari, 120); });
        inp.addEventListener('keydown', function (e) { if (e.key === 'Escape') { inp.value = ''; cari(); } });
        btnBersih.addEventListener('click', function () { inp.value = ''; cari(); inp.focus(); });

        // Klik salah satu saran → buka katalog toko dengan produk yang dicari
        hasil.addEventListener('click', function (e) {
            const it = e.target.closest('.km-search-item');
            if (!it) return;
            const p = produk.find(x => String(x.id) === it.dataset.id);
            if (p) window.location.href = '{{ route('toko.index') }}?q=' + encodeURIComponent(p.nama);
        });

        // Klik di luar wrap → tutup panel saran
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.km-search-wrap')) tutupPanel();
        });
        inp.addEventListener('focus', function () { if (hasil.innerHTML.trim()) tampilPanel(); });
    })();
</script>
@endpush
@endsection