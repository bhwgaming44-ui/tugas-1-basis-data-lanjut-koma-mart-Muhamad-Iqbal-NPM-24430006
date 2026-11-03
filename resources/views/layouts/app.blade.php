<!DOCTYPE html>
<html lang="id">
@php
    /* Tema warna & logo toko bisa diubah petugas via menu Promo & Tampilan */
    $temaWarna = \App\Models\PengaturanToko::ambil('tema_warna', '#c8102e');
    $logoToko  = \App\Models\PengaturanToko::ambil('logo_toko');
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KOPMA MART | Sistem Informasi Minimarket</title>
    <!-- PWA: agar bisa di-install di HP (Add to Home Screen) -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="{{ $temaWarna }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-kopma.svg') }}">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="KOPMA">
    <meta name="mobile-web-app-capable" content="yes">
    <script>
        if ("serviceWorker" in navigator) {
            window.addEventListener("load", function() {
                navigator.serviceWorker.register("/sw.js").catch(function(err) {
                    console.log("[PWA] Service Worker gagal register:", err);
                });
            });
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cabinet+Grotesk:wght@800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS (untuk peta pelacakan kurir) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />

    <style>
        /* ============================================================
           DESIGN TOKENS (dari blok @theme) â€” Kopma Mart UI Theme
           Proyek memakai Bootstrap (bukan build Tailwind), jadi @theme
           dikonversi ke :root; nama variabel dipertahankan 1:1.
           ============================================================ */
        :root {
            /* Colors */
            --color-merah-gelap: #450a0a;
            --color-merah-maroon: #7f1d1d;
            --color-abu-sage: #9ca3af;
            --color-pink-muda: #e8c4c4;
            --color-merah-terang: #ff3b30;
            --color-merah: {{ $temaWarna }};
            --color-merah-wash: #fee2e2;
            --color-merah-mist: #f5d0d0;
            --color-merah-hitam: #2b1215;
            --color-pure-white: #ffffff;
            --color-putih-tulang: #fef2f2;
            --color-soft-mist: #dcdfe3;
            --color-cool-stone: #d6d6d6;
            --color-carbon: #222222;

            /* Typography */
            --font-ozik: 'Ozik', 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-aeonik: 'Aeonik', 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-instrument-serif: 'Instrument Serif', ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;

            /* Typography â€” Scale */
            --text-caption: 12px;
            --leading-caption: 1.5;
            --tracking-caption: -0.036px;
            --text-body: 14px;
            --leading-body: 1.5;
            --tracking-body: -0.042px;
            --text-body-md: 16px;
            --leading-body-md: 1.5;
            --tracking-body-md: -0.496px;
            --text-subheading: 18px;
            --leading-subheading: 1.5;
            --tracking-subheading: -0.378px;
            --text-heading-sm: 20px;
            --leading-heading-sm: 1.43;
            --tracking-heading-sm: -0.3px;
            --text-heading: 24px;
            --leading-heading: 1.33;
            --tracking-heading: -0.36px;
            --text-heading-lg: 32px;
            --leading-heading-lg: 1.2;
            --tracking-heading-lg: -0.672px;
            --text-display: 40px;
            --leading-display: 1.2;
            --tracking-display: -1.24px;
            --text-display-xl: 56px;
            --leading-display-xl: 0.86;
            --tracking-display-xl: -0.784px;
            --text-display-2xl: 80px;
            --leading-display-2xl: 0.9;
            --tracking-display-2xl: -0.8px;

            /* Spacing */
            --spacing-8: 8px;
            --spacing-16: 16px;
            --spacing-24: 24px;
            --spacing-32: 32px;
            --spacing-40: 40px;
            --spacing-48: 48px;
            --spacing-56: 56px;
            --spacing-64: 64px;
            --spacing-80: 80px;
            --spacing-128: 128px;
            --spacing-168: 168px;
            --spacing-240: 240px;

            /* Border Radius */
            --radius-lg: 8px;
            --radius-xl: 12px;
            --radius-2xl: 16px;
            --radius-2xl-2: 20px;
            --radius-3xl: 24px;
            --radius-3xl-2: 28px;
            --radius-3xl-3: 32px;
            --radius-3xl-4: 40px;
            --radius-full: 48px;
            --radius-full-2: 64px;
            --radius-full-3: 80px;
            --radius-full-4: 100px;

            /* Brand legacy (kompatibel view lama) */
            --hijau-utama: {{ $temaWarna }};
            --hijau-terang: #ff3b30;
            --hijau-gelap: #450a0a;
            --putih-bersih: #ffffff;
            --navy-koperasi: #450a0a;
            --emas-aksen: #dc2626;
            --bg-pattern: #fef2f2;

            /* Bootstrap 5 override -> ikut tema baru */
            --bs-body-bg: #fef2f2;
            --bs-body-color: #222222;
            --bs-body-font-family: var(--font-aeonik);
            --bs-border-radius: 8px;
            --bs-border-radius-lg: 12px;
            --bs-border-radius-xl: 16px;
            --bs-border-radius-2xl: 24px;
            --bs-primary: {{ $temaWarna }};
            --bs-primary-rgb: 38, 162, 0;
            --bs-success: {{ $temaWarna }};
            --bs-success-rgb: 38, 162, 0;
            --bs-success-bg-subtle: #fee2e2;
            --bs-success-border-subtle: #b7e8a4;
        }

        body {
            font-family: var(--font-aeonik);
            font-size: var(--text-body);
            line-height: var(--leading-body);
            letter-spacing: var(--tracking-body);
            background-color: var(--color-putih-tulang);
            background-image:
                radial-gradient(circle at 10% 20%, rgba(104, 239, 63, 0.10) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(38, 162, 0, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.85) 0%, transparent 100%),
                repeating-linear-gradient(45deg, rgba(38, 162, 0, 0.02) 0px, rgba(38, 162, 0, 0.02) 2px, transparent 2px, transparent 16px);
            background-attachment: fixed;
            color: var(--color-carbon);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6 { font-family: var(--font-ozik); }

        /* Top Bar hijau Putih & Teks Bergerak HUT RI 81 */
/* Topbar putih minimalis */
        .topbar-putih {
            background: #ffffff;
            color: #6b7280;
            font-size: 11.5px;
            border-bottom: 1px solid #eef1f4;
        }
        .topbar-putih a { color: #6b7280; text-decoration: none; }
        .topbar-putih a:hover { color: #c8102e; }

        /* Navbar generik (header utama kini memakai .km-header full-width) */
        .navbar-kopma {
            background: #ffffff;
            border-bottom: 1px solid #eef1f4;
            box-shadow: none;
        }

        .navbar-brand-kopma {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-badge {
            background: linear-gradient(135deg, #c8102e, #ef4444);
            color: #fff;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 4px 12px rgba(200, 16, 46, 0.35);
            border: 2px solid #fff;
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--hijau-utama);
            line-height: 1.1;
            letter-spacing: -0.5px;
        }

        .brand-sub {
            font-size: 0.72rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-link-kopma {
            color: #334155 !important;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 8px 14px !important;
            border-radius: 8px;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-kopma:hover {
            color: var(--color-merah) !important;
            background: var(--color-merah-wash);
            transform: translateY(-1px);
        }

        .nav-link-kopma.active {
            color: var(--color-merah-gelap) !important;
            background: linear-gradient(135deg, var(--color-merah-terang), var(--color-merah)) !important;
            box-shadow: 0 3px 10px rgba(38, 162, 0, 0.3);
        }

        /* Banner Plang hijau Putih (Inspired by Koperasi Desa hijau Putih) */
        .kopma-hero-plang {
            background: linear-gradient(135deg, #c8102e 0%, #dc2626 60%, #c8102e 100%);
            border-radius: 16px;
            border: 4px solid #ffffff;
            box-shadow: 0 10px 30px rgba(200, 16, 46, 0.25);
            padding: 24px 30px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .kopma-hero-plang::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 320px;
            height: 100%;
            background: repeating-linear-gradient(
                -45deg,
                rgba(255, 255, 255, 0.08) 0px,
                rgba(255, 255, 255, 0.08) 25px,
                transparent 25px,
                transparent 50px
            );
            pointer-events: none;
        }

        /* Card hijau Putih Premium */
        .card-kopma {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 14px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
        }

        .card-kopma:hover {
            box-shadow: 0 10px 25px rgba(200, 16, 46, 0.1);
            transform: translateY(-2px);
        }

        .card-kopma-header {
            background: #ffffff;
            border-bottom: 2px solid #fee2e2;
            padding: 16px 20px;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }

        /* Footer ala Alfagift: pita manfaat merah + artikel SEO + link kolom */
        footer.kopma-footer { background: #ffffff; margin-top: auto; }
        .km-foot-benefit { background: linear-gradient(180deg, #e23b3b 0%, #ee5a5a 100%); color: #ffffff; text-align: center; padding: 26px 16px 30px; }
        .km-foot-benefit h5 { font-weight: 700; font-size: 15px; margin-bottom: 22px; }
        .km-foot-benefit-grid { display: flex; justify-content: center; gap: 36px; flex-wrap: wrap; max-width: 900px; margin: 0 auto; }
        .km-foot-benefit-item { width: 130px; }
        .km-foot-benefit-item .km-bf-ico { width: 58px; height: 58px; margin: 0 auto 10px; border-radius: 50%; border: 2px solid rgba(255,255,255,.9); display: flex; align-items: center; justify-content: center; font-size: 22px; background: rgba(0,0,0,.18); }
        .km-foot-benefit-item b { display: block; font-size: 11.5px; font-weight: 700; margin-bottom: 6px; }
        .km-foot-benefit-item small { display: block; font-size: 10px; line-height: 1.5; opacity: .92; }
        .km-foot-seo { background: #f4f6f8; padding: 34px 16px; }
        .km-foot-seo-inner { max-width: 820px; margin: 0 auto; color: #374151; font-size: 11.5px; line-height: 1.8; }
        .km-foot-seo-inner h6 { font-weight: 800; font-size: 12.5px; color: #111827; margin: 18px 0 6px; }
        .km-foot-seo-inner h6:first-child { margin-top: 0; }
        .km-foot-seo-inner p { margin-bottom: 8px; text-align: justify; }
        .km-foot-links { background: #ffffff; border-top: 1px solid #eef1f4; padding: 30px 16px 18px; }
        .km-foot-links-grid { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(5, 1fr); gap: 24px; }
        .km-foot-links h6 { font-weight: 800; font-size: 12.5px; color: #111827; margin-bottom: 12px; }
        .km-foot-links a { display: block; font-size: 12px; color: #4b5563; text-decoration: none; margin-bottom: 8px; }
        .km-foot-links a:hover { color: #c8102e; }
        .km-foot-soc { display: flex; gap: 8px; margin-bottom: 6px; }
        .km-foot-soc a { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 13px; margin-bottom: 0; }
        .km-foot-copy { background: #ffffff; border-top: 1px solid #eef1f4; text-align: center; padding: 14px 16px 18px; font-size: 11.5px; color: #6b7280; }
        .km-foot-badges { display: flex; justify-content: center; flex-wrap: wrap; gap: 8px; margin-bottom: 10px; }
        .km-foot-badges span { font-size: 11.5px; font-weight: 800; padding: 6px 14px; border-radius: 8px; }
        @media (max-width: 767.98px) { .km-foot-links-grid { grid-template-columns: repeat(2, 1fr); } }

        /* ===== BOXED LAYOUT â€” max-width 1280px, centered, ruang kosong kiri-kanan ===== */
        .boxed-layout {
            max-width: none;
            margin: 0;
            background: #f3f4f6;
            border-radius: 0;
            box-shadow: none;
            overflow: visible;
            border: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        body.bg-gray-100 { background: #f3f4f6; }
        @media (max-width: 767.98px) {
            .boxed-layout { margin: 0; border-radius: 0; }
        }

        /* ===== HEADER KOMPAK â€” Satu baris horizontal, clean white ===== */
/* ===== FULL-WIDTH HEADER BAR (pengganti header kompak lama) ===== */
.km-header {
    background: #ffffff;
    border-bottom: 1px solid #eef1f4;
    box-shadow: 0 2px 10px rgba(15, 23, 42, .05);
    position: sticky;
    top: 0;
    z-index: 1030;
}
.km-header-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.km-head-left { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.km-brand { display: flex; align-items: center; gap: 9px; text-decoration: none; }
.km-brand-badge { width: 40px; height: 40px; border-radius: 9px; overflow: hidden; background: #f8fafc; border: 1px solid #eef2f6; flex-shrink: 0; }
.km-brand-badge img { width: 100%; height: 100%; object-fit: cover; }
.km-brand-text { display: flex; flex-direction: column; line-height: 1.15; }
.km-brand-title { font-weight: 800; font-size: 15px; color: #111827; letter-spacing: -.2px; white-space: nowrap; }
.km-brand-sub { font-size: 10.5px; color: #6b7280; white-space: nowrap; }
.km-btn-kategori { display: inline-flex; align-items: center; gap: 8px; height: 40px; padding: 0 16px; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 10px; color: #c8102e; font-size: 13.5px; font-weight: 700; white-space: nowrap; transition: background .18s ease, color .18s ease, box-shadow .18s ease; }
.km-btn-kategori:hover { background: #fee2e2; box-shadow: 0 2px 10px rgba(200,16,46,.15); }
.km-btn-kategori.show { background: #c8102e; border-color: #c8102e; color: #ffffff; box-shadow: 0 4px 14px rgba(200,16,46,.35); }
.km-btn-kategori i { font-size: 16px; }
.km-kategori-panel { background: #ffffff; border-radius: 10px; padding: 4px 0 10px; min-width: 300px; max-height: 400px; overflow-y: auto; border: 1px solid #f1f5f9; box-shadow: 0 16px 40px rgba(15,23,42,.14), 0 2px 8px rgba(15,23,42,.06); animation: kmKatFade .18s ease; }
@keyframes kmKatFade { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
.km-kategori-panel .km-kat-head { font-weight: 800; font-size: 16px; color: #111827; padding: 12px 18px 8px; }
.km-kat-item { display: flex; align-items: center; gap: 13px; padding: 11px 18px; font-size: 13.5px; font-weight: 500; color: #4b5563; text-decoration: none; transition: background .15s ease, color .15s ease; }
.km-kat-item:hover { background: #fef2f2; color: #c8102e; }
.km-kat-item i.kat-ico { color: #c8102e; width: 22px; text-align: center; font-size: 16px; flex-shrink: 0; }
.km-kat-item .fa-chevron-right { margin-left: auto; font-size: 11px; color: #d1d5db; }
.km-kat-item:hover .fa-chevron-right { color: #c8102e; }
.km-search { flex: 1 1 auto; display: flex; min-width: 180px; }
.km-search-group { display: flex; width: 100%; height: 42px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden; transition: border-color .18s ease, box-shadow .18s ease; }
.km-search-group:focus-within { border-color: #c8102e; box-shadow: 0 0 0 3px rgba(200,16,46,.08); }
.km-search-group > i { align-self: center; margin-left: 14px; color: #9ca3af; font-size: 14px; }
.km-search-input { flex: 1; border: 0; outline: 0; padding: 0 12px; font-size: 13.5px; color: #111827; background: transparent; min-width: 0; }
.km-search-input::placeholder { color: #9ca3af; }
.km-search-btn { width: 52px; border: 0; background: #c8102e; color: #ffffff; font-size: 16px; transition: background .18s ease; }
.km-search-btn:hover { background: #9f141e; }
.km-head-right { display: flex; align-items: center; gap: 4px; flex-shrink: 0; margin-left: auto; }
.km-nav { display: flex; align-items: center; gap: 2px; list-style: none; margin: 0; padding: 0; }
.km-head-right .navbar-nav { flex-direction: row !important; align-items: center; gap: 4px; margin: 0 !important; }
        /* Menu petugas di header utama disembunyikan (sudah pindah ke bar kecil atas) */
        .km-nav .km-hide-petugas { display: none !important; }
@media (max-width: 991.98px) {
    .km-header-inner { flex-wrap: wrap; gap: 10px; }
    .km-search { order: 3; flex: 1 1 100%; }
    .km-head-right { max-width: 100%; overflow-x: auto; scrollbar-width: thin; }
    .km-nav { flex-wrap: nowrap; }
    .km-brand-sub { display: none; }
}
@media (max-width: 575.98px) {
    .km-btn-kategori span { display: none; }
    .km-btn-kategori { padding: 0 11px; }
    .km-brand-title { font-size: 13.5px; }
}
        /* Nav link standar â€” teks abu-abu gelap, ukuran kecil */
        .nav-link-kopma {
            color: #374151 !important; font-weight: 700; font-size: 12.5px;
            padding: 6px 12px !important; border-radius: 8px;
            transition: all 0.18s ease; display: inline-flex; align-items: center;
            gap: 5px; text-decoration: none; height: 34px;
        }
        .nav-link-kopma:hover, .nav-link-kopma.active {
            color: #c8102e !important; background: #fef2f2;
        }
        .nav-link-kopma i { color: #9ca3af; font-size: 14px; }
        .nav-link-kopma:hover i, .nav-link-kopma.active i { color: #c8102e; }
        /* Badge mode / nama petugas â€” merah solid elegan, ukuran kecil */
        .badge-mode-petugas {
            background: #c8102e !important; color: #ffffff !important; font-weight: 800;
            font-size: 11.5px; padding: 5px 11px; border-radius: 8px;
            display: inline-flex; align-items: center; gap: 5px; height: 30px;
        }
        /* Tombol logout kecil */
        .btn-logout-sm {
            background: #fef2f2; border: 1px solid #fca5a5; color: #c8102e;
            font-weight: 800; font-size: 11px; padding: 5px 11px; border-radius: 8px;
            transition: all 0.18s ease; height: 30px;
        }
        .btn-logout-sm:hover { background: #c8102e; border-color: #c8102e; color: #ffffff; }
        @stack('styles')
    </style>
</head>
<body class="bg-gray-100 min-h-dvh">

    <div class="boxed-layout">

    <!-- TOPBAR PUTIH MINIMALIS -->
    <div class="topbar-putih">
        <div class="container-fluid px-3 px-lg-4">
            <div class="d-flex align-items-center justify-content-between py-1">
                <small><i class="fa-solid fa-location-dot text-danger me-1"></i> KOPMA MART &bull; Minimarket Modern, Belanja Hemat Setiap Hari</small>
                <div class="d-none d-md-flex align-items-center gap-3">
                    {{-- Menu lengkap KOPMA MART sesuai screenshot UI --}}
                    <a href="{{ route('kasir.index') }}">Kasir</a>
                    <a href="{{ route('kasir.riwayat') }}">Cetak Struk</a>
                    <a href="{{ route('petugas.produk.index') }}">Produk</a>
                    <a href="{{ route('petugas.promo.index') }}">Promo</a>
                    <a href="{{ route('petugas.topup') }}">Top Up</a>
                    <a href="{{ route('laporan.index') }}">Laporan</a>
                    <a href="{{ route('laporan.analisis') }}">Analisis</a>
                    <a href="{{ route('admin.payment-setting') }}">Bayar</a>
                    <a href="{{ route('keranjang.index') }}">Keranjang</a>
                    <a href="{{ route('pegawai.index') }}">Pegawai</a>
                    <a href="{{ route('anggota.index') }}">Member</a>
                </div>
            </div>
        </div>
    </div>

    @php
        // Jumlah pesan chat yang belum dibaca â€” untuk badge di navbar.
        // Dibungkus try/catch agar layout tetap tampil walau tabel chat belum ada.
        $chatBelumDibaca = 0;  // sisi USER : pesan dari kurir
        $chatKurirBaru   = 0;  // sisi KURIR: pesan dari user
        try {
            if (session('pelanggan_id')) {
                $chatBelumDibaca = \App\Models\ChatPesan::where('id_anggota', session('pelanggan_id'))
                    ->where('pengirim', 'kurir')->where('dibaca', false)->count();
            } elseif (session('kasir_id')) {
                // Mitra hanya melihat pesan dari user pada pesanan yang dipegangnya.
                $chatKurirBaru = \App\Models\ChatPesan::where('id_kurir', session('kasir_id'))
                    ->where('pengirim', 'user')->where('dibaca', false)->count();
            }
        } catch (\Throwable $e) {
            $chatBelumDibaca = 0;
            $chatKurirBaru   = 0;
        }
    @endphp

    <!-- HEADER FULL-WIDTH : bilah putih selebar layar (logo+kategori kiri, search tengah, nav kanan) -->
    <header class="km-header">
        <div class="km-header-inner">
            <!-- Kiri: logo berdampingan tombol kategori grid -->
            <div class="km-head-left">
                <a class="km-brand" href="{{ route('dashboard') }}">
                    <span class="km-brand-badge">
                        <img src="{{ asset($logoToko ?: 'images/logo-kopma.svg') }}" alt="Logo KOPMA MART"
                             onerror="this.onerror=null; this.src='/icon-192.png';">
                    </span>
                    <span class="km-brand-text">
                        <span class="km-brand-title">KOPMA MART</span>
                        <span class="km-brand-sub">Minimarket &bull; Belanja Hemat</span>
                    </span>
                </a>
                <div class="dropdown km-kategori">
                    <button type="button" class="km-btn-kategori" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-border-all"></i><span>Kategori</span>
                    </button>
                    <div class="dropdown-menu km-kategori-panel mt-2">
                        <div class="km-kat-head">Kategori</div>
                        @php
                            $kats = \App\Models\KategoriBarang::withCount('barangs')->orderBy('nama_kategori')->get();
                            $ikonKat = ['sembako' => 'fa-basket-shopping', 'pokok' => 'fa-basket-shopping', 'dapur' => 'fa-kitchen-set', 'makan' => 'fa-bowl-food', 'minum' => 'fa-mug-saucer', 'ringan' => 'fa-cookie-bite', 'snack' => 'fa-cookie-bite', 'camilan' => 'fa-cookie-bite', 'tulis' => 'fa-pen-ruler', 'kantor' => 'fa-briefcase', 'kuliah' => 'fa-graduation-cap', 'kampus' => 'fa-graduation-cap', 'bersih' => 'fa-soap', 'perawatan' => 'fa-pump-soap', 'diri' => 'fa-pump-soap', 'personal' => 'fa-pump-soap', 'obat' => 'fa-pills', 'farmasi' => 'fa-briefcase-medical', 'apotek' => 'fa-briefcase-medical', 'kesehatan' => 'fa-heart-pulse', 'merchandise' => 'fa-shirt', 'ibu' => 'fa-baby', 'anak' => 'fa-baby', 'bayi' => 'fa-baby', 'rumah' => 'fa-house-chimney', 'susu' => 'fa-glass-water', 'segar' => 'fa-snowflake', 'beku' => 'fa-snowflake', 'lifestyle' => 'fa-shirt', 'pet' => 'fa-paw', 'hewan' => 'fa-paw'];
                        @endphp
                        @foreach($kats as $kat)
                        @php
                            $nkKat = strtolower($kat->nama_kategori);
                            $icoKat = 'fa-tag';
                            foreach ($ikonKat as $kunciKat => $vKat) { if (str_contains($nkKat, $kunciKat)) { $icoKat = $vKat; break; } }
                        @endphp
                        <a class="km-kat-item" href="{{ route('toko.index', ['kategori' => $kat->nama_kategori]) }}">
                            <i class="fa-solid {{ $icoKat }} kat-ico"></i>
                            <span>{{ $kat->nama_kategori }} <small class="text-muted">({{ $kat->barangs_count }})</small></span>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tengah: search bar panjang + tombol merah ikon di ujung -->
            <form class="km-search" action="{{ route('toko.index') }}" method="GET" role="search">
                <div class="km-search-group">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" name="q" class="km-search-input" placeholder="Cari produk di KOPMA MART..." value="{{ request('q') ?? '' }}" aria-label="Cari produk">
                    <button type="submit" class="km-search-btn" aria-label="Cari"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>

            <!-- Kanan: nav fungsional horizontal rapih -->
            <div class="km-head-right">
                @include('partials.pindah-peran')
                <ul class="km-nav">
                    @include('partials.nav-mode')
                    @include('partials.foto-profil-menu', ['as_li' => true])
                </ul>
            </div>
        </div>
    </header>

    <!-- CONTENT WRAPPER -->
    <main class="container-fluid px-3 px-lg-4 py-4 flex-grow-1">
        @if(session('success'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-4 border-danger" role="alert">
                <i class="fa-solid fa-circle-check text-danger me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-4 border-danger" role="alert">
                <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- BOTTOM NAVIGATION (mobile) â€” berisi pintu masuk CHAT KURIR <-> USER + badge belum dibaca -->
    @include('partials.bottom-nav')

    <!-- FOOTER ALA ALFAGIFT -->
    <footer class="kopma-footer">
        <div class="km-foot-benefit">
            <h5>Yuk, belanja di KOPMA MART dan Dapatkan Keuntungannya!</h5>
            <div class="km-foot-benefit-grid">
                <div class="km-foot-benefit-item">
                    <div class="km-bf-ico"><i class="fa-solid fa-truck-fast"></i></div>
                    <b>Gratis Ongkir Tanpa Syarat</b>
                    <small>Belanja berapa pun, gratis biaya pengiriman area kampus</small>
                </div>
                <div class="km-foot-benefit-item">
                    <div class="km-bf-ico"><i class="fa-solid fa-bolt"></i></div>
                    <b>Pengiriman Cepat</b>
                    <small>Pesanan diantar lebih cepat karena dikirim dari toko terdekat</small>
                </div>
                <div class="km-foot-benefit-item">
                    <div class="km-bf-ico"><i class="fa-solid fa-coins"></i></div>
                    <b>Poin Terintegrasi</b>
                    <small>Dapatkan poin untuk setiap pembelanjaan di KOPMA MART</small>
                </div>
                <div class="km-foot-benefit-item">
                    <div class="km-bf-ico"><i class="fa-solid fa-basket-shopping"></i></div>
                    <b>Produk Lebih Lengkap</b>
                    <small>Stok lengkap: sembako, ATK, apotek, dan merchandise kampus</small>
                </div>
            </div>
        </div>
        <div class="km-foot-seo">
            <div class="km-foot-seo-inner">
                <h6>KOPMA MART, Toko Belanja Online Terlengkap dari Koperasi Mahasiswa</h6>
                <p>KOPMA MART adalah minimarket modern milik koperasi mahasiswa yang hadir memberikan kemudahan dan kenyamanan berbelanja bagi seluruh warga kampus. KOPMA MART menjual puluhan pilihan produk kebutuhan sehari-hari: sembako, makanan dan minuman, alat tulis dan perkuliahan, kebersihan dan perawatan diri, obat ringan, hingga merchandise kampus.</p>
                <h6>Cara Belanja di KOPMA MART</h6>
                <p>Pilih produk di katalog, masukkan ke keranjang, lalu checkout dan pilih pembayaran tunai, transfer, atau Kopma Point. Pesanan diantar cepat ke area kampus dan riwayat pesanan tersimpan rapi.</p>
                <h6>Beragam Promo KOPMA MART Super Hemat</h6>
                <p>Pantau banner promo di halaman utama untuk penawaran terbaru. Harga anggota (member) selalu lebih murah dibanding harga umum di setiap kategori.</p>
            </div>
        </div>

        <div class="km-foot-links">
            <div class="km-foot-links-grid">
                <div>
                    <h6>Layanan Pelanggan</h6>
                    <a href="{{ route('toko.index') }}">Pertanyaan Umum</a>
                    <a href="{{ route('pesanan.index') }}">Cara Belanja</a>
                    <a href="{{ route('kopma.point.index') }}">Kopma Point</a>
                    <a href="{{ route('keranjang.index') }}">Keranjang</a>
                    <a href="{{ route('chat.index') }}">Chat Kurir</a>
                </div>
                <div>
                    <h6>Jelajahi KOPMA MART</h6>
                    <a href="{{ route('dashboard') }}">Beranda</a>
                    <a href="{{ route('toko.index') }}">Semua Produk</a>
                    <a href="{{ route('pesanan.index') }}">Pesanan Saya</a>
                    <a href="{{ route('pembayaran') }}">Metode Pembayaran</a>
                </div>
                <div>
                    <h6>Metode Pembayaran</h6>
                    <a href="{{ route('pembayaran') }}">Tunai (COD)</a>
                    <a href="{{ route('pembayaran') }}">Transfer Bank</a>
                    <a href="{{ route('pembayaran') }}">QRIS</a>
                    <a href="{{ route('kopma.point.index') }}">Kopma Point</a>
                </div>
                <div>
                    <h6>Ikuti Kami</h6>
                    <div class="km-foot-soc">
                        <a href="#" style="background:#1877f2;" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" style="background:#1da1f2;" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" style="background:#e1306c;" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                    <h6 class="mt-3">Hubungi Kami</h6>
                    <a href="{{ route('chat.index') }}">Chat KOPMA MART</a>
                    <a href="{{ route('toko.index') }}">Buka 07.00 - 22.00</a>
                </div>
                <div>
                    <h6>Akun Saya</h6>
                    <a href="{{ route('pelanggan.daftar') }}">Daftar Member</a>
                    <a href="{{ route('pelanggan.login') }}">Masuk Akun</a>
                    <a href="{{ route('pelanggan.profil') }}">Profil Saya</a>
                    <a href="{{ route('kasir.login') }}">Login Petugas</a>
                </div>
            </div>
        </div>

        <div class="km-foot-copy">
            <div class="km-foot-badges">
                <span style="background:#c8102e;color:#fff;">KOPMA MART</span>
                <span style="background:#4b5563;color:#fff;">Minimarket &bull; Belanja Hemat</span>
                <span style="background:#facc15;color:#111827;">Buka 07.00 - 22.00</span>
            </div>
            &copy; {{ date('Y') }} <strong>KOPMA MART - Sistem Informasi Minimarket</strong> &bull; Praktikum Basis Data Lanjut
        </div>
    </footer>

    <!-- MODAL WHATSAPP POPUP INTERAKTIF (BISA BUKA WA WEB MAUPUN APLIKASI) -->
    <div class="modal fade" id="modalWhatsapp" tabindex="-1" aria-labelledby="modalWhatsappLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #128C7E, #25D366);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 24px;">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="modalWhatsappLabel">Pesan Toko via WhatsApp</h5>
                            <small class="text-white-50"><i class="fa-solid fa-circle text-light me-1" style="font-size: 8px;"></i> Admin KOPMA MART Online</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <p class="text-secondary small mb-3">
                        Pilih barang yang ingin Anda pesan langsung ke kasir atau kirim pertanyaan mengenai ketersediaan stok 100 produk KOPMA:
                    </p>

                    <form id="formPesanWa">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Nama Pemesan:</label>
                            <input type="text" id="waNama" class="form-control" placeholder="Contoh: Budi Santoso (Member: MBR-001)" value="Budi Santoso">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Pesan / Barang yang Dipesan:</label>
                            <textarea id="waPesan" class="form-control" rows="3" placeholder="Contoh: Halo admin, saya mau pesan Beras Premium 5kg (1 sak) dan Indomie Goreng (5 pcs)...">Halo Admin KOPMA MART, saya ingin menanyakan stok dan memesan barang toko...</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <!-- Buka WhatsApp Universal (Desktop / Mobile App) -->
                            <button type="button" class="btn text-white fw-bold py-2 shadow-sm" style="background-color: #25D366;" onclick="bukaWhatsApp('app')">
                                <i class="fa-brands fa-whatsapp me-2 fa-lg"></i> Buka di Aplikasi WhatsApp
                            </button>
                            <!-- Buka WhatsApp Web di Browser PC -->
                            <button type="button" class="btn btn-outline-dark fw-bold py-2" onclick="bukaWhatsApp('web')">
                                <i class="fa-solid fa-laptop me-2"></i> Buka di WhatsApp Web (Browser)
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-white border-0 py-2 justify-content-center">
                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i> No. Layanan Toko: +62 812-3456-7890</small>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL GANTI FOTO PROFIL (file laptop / album HP / link Google Drive) -->
    @include('partials.foto-profil-modal')

    <!-- Live Reload Realtime (1 Detik) untuk Antigravity (Pojok Kanan Bawah) â€” DISEMBUNYIKAN -->
    <div id="live-reload-badge" style="display: none !important;">

    <style>
        @keyframes pulseLive {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
    </style>

    <!-- Chatbot KOPMA MART (AI + Database) -->
    @include('partials.chatbot')

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS (untuk peta pelacakan kurir) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <!-- qrcodejs: dipakai partials/qris.blade.php untuk menggambar QRIS di browser
         (server ini tidak punya ekstensi GD, jadi QR dibuat di sisi klien). -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    @stack('scripts')

    <!-- SCRIPT WHATSAPP & REALTIME AUTO-SYNC -->
    <script>
        // Nomor WhatsApp Business resmi KOPMA MART (dari config services.wa.number).
        // Semua pesanan/pembayaran dikirim LANGSUNG ke nomor ini.
        const WA_BUSINESS = "{{ $waBusinessNumber ?? '' }}";

        function bukaWhatsApp(mode) {
            const nama = document.getElementById('waNama').value || 'Pelanggan';
            const isiPesan = document.getElementById('waPesan').value || 'Halo Admin KOPMA MART';
            const teksLengkap = encodeURIComponent(`*PESANAN KOPMA MART*\nNama: ${nama}\nPesan: ${isiPesan}\n\n_Dikirim melalui Portal KOPMA MART_`);

            // Kirim langsung ke nomor WhatsApp Business (fallback: pilih kontak bila nomor kosong)
            let url;
            if (WA_BUSINESS) {
                url = mode === 'app'
                    ? `https://api.whatsapp.com/send?phone=${WA_BUSINESS}&text=${teksLengkap}`
                    : `https://wa.me/${WA_BUSINESS}?text=${teksLengkap}`;
            } else {
                url = mode === 'web'
                    ? `https://wa.me/?text=${teksLengkap}`
                    : `https://api.whatsapp.com/send?text=${teksLengkap}`;
            }
            window.open(url, '_blank');
        }

        // Live Reload Realtime (1 Detik)
        (function() {
            let lastModifiedTime = null;
            let isChecking = false;
            const badge = document.getElementById('live-reload-badge');

            setInterval(() => {
                if (isChecking) return;
                isChecking = true;

                fetch('/live-reload-hash')
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(data => {
                        isChecking = false;
                        if (lastModifiedTime === null) {
                            lastModifiedTime = data.timestamp;
                        } else if (data.timestamp > lastModifiedTime) {
                            if (badge) {
                                badge.style.background = 'rgba(243, 156, 18, 0.95)';
                                badge.innerHTML = '<span style="display:inline-block;width:8px;height:8px;background:#fff;border-radius:50%;"></span><span>Sinkronisasi Otomatis...</span>';
                            }
                            console.log('[LiveSync] Perubahan terdeteksi di Antigravity! Memperbarui tampilan & data...');
                            setTimeout(() => {
                                window.location.reload();
                            }, 300);
                        }
                    })
                    .catch(err => {
                        isChecking = false;
                    });
            }, 1000);
        })();
    </script>
    </div>
    <!-- /boxed-layout -->

</body>
</html>

