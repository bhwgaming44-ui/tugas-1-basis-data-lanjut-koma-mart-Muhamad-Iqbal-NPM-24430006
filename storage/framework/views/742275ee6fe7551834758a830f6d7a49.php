
<?php $__env->startSection('content'); ?>
<style>
    /* ============================================================
       KATALOG PRODUK KOPMA MART - MODERN RED & WHITE THEME
       ============================================================ */
    /* Style slider promo lengkap ada di blok <style> bagian 1b (di bawah). */
    .hero-search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 50px;
        padding: 6px 14px;
        max-width: 560px;
        flex-grow: 1;
    }
    .hero-search-box input {
        border: none;
        outline: none;
        flex: 1;
        font-size: 14px;
        color: #111827;
        background: transparent;
    }
    /* Pencarian realtime dasbor: wrap relatif + panel saran produk + tombol bersih */
    .hero-search-wrap { position: relative; flex-grow: 1; max-width: 560px; }
    .hero-search-wrap .hero-search-box { max-width: none; }
    .hero-search-bersih {
        border: 0; background: transparent; color: #94a3b8; font-size: 15px;
        cursor: pointer; padding: 2px 4px; display: none; flex-shrink: 0;
    }
    .hero-search-bersih.tampil { display: inline-flex; }
    .hero-search-bersih:hover { color: #dc2626; }
    .hero-saran-panel {
        position: absolute; top: calc(100% + 8px); left: 0; right: 0;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        box-shadow: 0 12px 32px rgba(0,0,0,.15); max-height: 340px; overflow-y: auto;
        z-index: 1050; display: none;
    }
    .hero-saran-panel.tampil { display: block; }
    .hero-saran-info { padding: 8px 14px; font-size: 12.5px; font-weight: 600; color: #64748b; border-bottom: 1px solid #f1f5f9; }
    .hero-saran-info.kosong { color: #dc2626; }
    .hero-saran-item {
        display: flex; align-items: center; gap: 10px; width: 100%;
        padding: 9px 14px; border: 0; background: #fff; text-align: left;
        font-size: 13.5px; color: #111827; cursor: pointer; text-decoration: none;
        border-bottom: 1px solid #f1f5f9;
    }
    .hero-saran-item:last-child { border-bottom: 0; }
    .hero-saran-item:hover, .hero-saran-item:focus { background: #fef2f2; outline: none; }
    .hero-saran-item img { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; background: #fef2f2; flex-shrink: 0; }
    .hero-saran-item .nama { font-weight: 700; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .hero-saran-item .harga { color: #c8102e; font-weight: 700; white-space: nowrap; }
    /* Toolbar katalog kompak - pencarian instan + aksi singkat */
    .catalog-toolbar {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    /* Quick Info / Wallet Bar */
    .quick-info-bar {
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        border: 1px solid rgba(226, 232, 240, 0.9);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: -24px;
        position: relative;
        z-index: 3;
    }
    .quick-stat-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stat-circle-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #c8102e, #ef4444);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 10px rgba(200, 16, 46, 0.25);
    }
    .quick-act-link {
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        text-decoration: none;
        padding: 4px 10px;
        border-radius: 10px;
        transition: all 0.15s ease;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
    }
    .quick-act-link i {
        display: flex;
        width: 38px;
        height: 38px;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #fef2f2;
        color: #c8102e;
        font-size: 15px;
        margin-bottom: 4px;
        transition: all 0.2s ease;
    }
    .quick-act-link:hover i {
        background: #c8102e;
        color: #ffffff;
        transform: scale(1.08);
    }
    /* Menu Kategori */
    .menu-grid-clean {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 12px;
        background: #ffffff;
        border-radius: 18px;
        padding: 18px 16px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.04);
        border: 1px solid #eef2f6;
    }
    .menu-cat-item {
        text-align: center;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        padding: 6px;
        border-radius: 14px;
        transition: all 0.2s ease;
    }
    .menu-cat-item .cat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        margin: 0 auto 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
        transition: all 0.2s ease;
    }
    .menu-cat-item:hover .cat-icon {
        transform: translateY(-3px) scale(1.06);
        box-shadow: 0 8px 18px rgba(200,16,46,0.3);
    }
    /* Category Chips Filter */
    .kat-chips-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .kat-chip-btn {
        border: 1px solid #e2e8f0;
        border-radius: 50px;
        background: #ffffff;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .kat-chip-btn:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #c8102e;
    }
    .kat-chip-btn.active {
        background: linear-gradient(135deg, #c8102e, #ef4444);
        border-color: #c8102e;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(200, 16, 46, 0.35);
    }
    .kat-chip-btn.active small {
        color: #fee2e2;
    }
    /* 5 Kolom Produk di Desktop (5 x 4 = 20 Produk Per Halaman Minimalis & Rapi) */
    @media (min-width: 1200px) {
        .col-xl-custom-5 {
            flex: 0 0 auto;
            width: 20%;
        }
    }
    /* Product Card Minimalis & Kompak â€” estetika Alfagift style */
    .product-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(200, 16, 46, 0.10);
        border-color: #fca5a5;
    }
    .product-image-box {
        width: 100%;
        aspect-ratio: 1 / 1;
        background: #f8fafc;
        position: relative;
        overflow: hidden;
        margin: 0;
        border-bottom: 1px solid #f1f5f9;
        display: block;
    }
    .product-image-full {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        transition: transform 0.35s ease;
    }
    .product-card:hover .product-image-full {
        transform: scale(1.05);
    }
    .product-badge-float {
        position: absolute;
        top: 6px;
        left: 6px;
        background: #c8102e;
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        padding: 3px 7px;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.18);
        z-index: 3;
    }
    .toko-petugas-edit {
        position: absolute;
        top: 6px;
        right: 6px;
        z-index: 4;
    }
    .toko-btn-edit {
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(4px);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 16px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin: 0;
        border: 1px solid rgba(255,255,255,0.4);
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        transition: all 0.2s ease;
    }
    .toko-btn-edit:hover {
        background: #c8102e;
        transform: scale(1.05);
    }
    .product-body {
        padding: 10px 12px 12px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .product-title {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.3;
        min-height: 30px;
        margin-bottom: 3px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-price-member {
        font-size: 13.5px;
        font-weight: 800;
        color: #c8102e;
        line-height: 1.2;
    }
    .product-price-strike {
        font-size: 11px;
        color: #94a3b8;
        text-decoration: line-through;
    }
    /* Cart Button Kompak */
    .btn-add-cart {
        background: #c8102e; color: #ffffff; border: none; border-radius: 7px;
        padding: 6px 9px; font-size: 11.5px; font-weight: 800; width: 100%;
        transition: all 0.2s ease; display: inline-flex; align-items: center;
        justify-content: center; gap: 4px; cursor: pointer;
    }
    .btn-add-cart:hover {
        background: #9f141e; color: #ffffff;
        box-shadow: 0 3px 10px rgba(200, 16, 46, 0.30); transform: translateY(-1px);
    }
    .btn-add-cart:active { transform: scale(0.98); }
    /* Floating Toast */
    .toast-container-custom {
        position: fixed;
        bottom: 85px;
        right: 24px;
        z-index: 1060;
    }
    /* Pagination Styling */
    .kopma-pagination .pagination {
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .kopma-pagination .page-link {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        background: #fff;
        color: #374151;
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .kopma-pagination .page-link:hover {
        background: #fef2f2;
        border-color: #c8102e;
        color: #c8102e;
    }
    .kopma-pagination .page-item.active .page-link {
        background: #c8102e;
        border-color: #c8102e;
        color: #fff;
        box-shadow: 0 4px 10px rgba(200,16,46,0.35);
    }
</style>

<div class="catalog-toolbar mb-3">
    <form action="<?php echo e(route('toko.index')); ?>" method="GET" id="formCariHero" class="d-flex align-items-center gap-2 flex-grow-1">
        <?php if($katAktif): ?><input type="hidden" name="kategori" value="<?php echo e($katAktif); ?>"><?php endif; ?>
        <div class="hero-search-wrap">
            <div class="hero-search-box">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                <input type="text" name="q" id="inputSearchClient" value="<?php echo e($q); ?>" autocomplete="off"
                       placeholder="Ketik langsung - hasil muncul otomatis (contoh: Indomie, Beras, Kopi)..."
                       aria-controls="heroSaranPanel" aria-expanded="false">
                <button type="button" class="hero-search-bersih" id="heroBersihCari" aria-label="Bersihkan pencarian">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">Cari</button>
            </div>
            <div class="hero-saran-panel" id="heroSaranPanel" role="listbox"></div>
        </div>
    </form>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <?php if(\App\Helpers\AkunHelper::isPetugas()): ?>
        <!-- Tambah Produk Cepat (Hanya Petugas) -->
        <button type="button" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
            <i class="fa-solid fa-plus me-1"></i> Tambah Produk
        </button>
        <?php endif; ?>
        <!-- Buka Keranjang -->
        <a href="<?php echo e(route('keranjang.index')); ?>" class="btn btn-sm btn-dark fw-bold rounded-pill px-3">
            <i class="fa-solid fa-cart-shopping"></i> Keranjang
            <span class="badge bg-danger rounded-pill cart-count-badge" id="badgeKeranjangHero">
                <?php echo e(array_sum(session('keranjang', []))); ?>

            </span>
        </a>
        <?php if(!session('pelanggan_id')): ?>
            <a href="<?php echo e(route('pelanggan.daftar')); ?>" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3">
                <i class="fa-solid fa-id-card"></i> Daftar Member
            </a>
        <?php endif; ?>
        
        <?php echo $__env->make('partials.foto-profil-menu', ['as_li' => false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <div id="infoCariHero" class="small text-muted w-100"></div>
</div>

<style>
    .promo-slider-wrap { position: relative; overflow: hidden; border-radius: 20px; box-shadow: 0 10px 26px rgba(0,0,0,0.14); border: 2px solid rgba(255,255,255,0.4); }
    .promo-track { display: flex; transition: transform .6s cubic-bezier(.4,0,.2,1); }
    /* Satu "halaman" slide = 2 promo berdampingan (geser per 2 promo).
       Kanvas promo 1200 x 600 px = rasio 2:1 standar slider website.
       Banner gambar tampil full-bleed (tanpa overlay teks) agar desain tidak tertutup. */
    .promo-slider-wrap { position: relative; }
    .promo-slide { min-width: 100%; height: auto; display: flex; gap: 16px; padding: 6px 44px; box-sizing: border-box; }
    .promo-slide-inner { flex: 1 1 0; min-width: 0; height: 230px; display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 22px 30px; color: #fff; position: relative; overflow: hidden; border-radius: 16px; box-sizing: border-box; }
    .promo-slide-inner.has-img { padding: 0; display: block; background: #f1f5f9; }
    .promo-slide-inner.has-img .promo-slide-txt,
    .promo-slide-inner.has-img .promo-ikon { display: none; }
    .promo-banner-img { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center; }
    .promo-banner-img img { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center; }
    /* Panah prev/next ala referensi (lingkaran putih di tepi slider) */
    .promo-nav-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%; border: none; background: #fff; color: #64748b; box-shadow: 0 6px 18px rgba(0,0,0,0.18); display: inline-flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; z-index: 5; transition: transform .15s ease, color .15s ease; }
    .promo-nav-btn:hover { transform: translateY(-50%) scale(1.06); color: #c8102e; }
    .promo-nav-prev { left: 2px; }
    .promo-nav-next { right: 2px; }
    @media (max-width: 767.98px) {
        .promo-slide { height: auto; flex-direction: column; }
        .promo-slide-inner { height: 170px; padding: 16px 20px; }
        .promo-slide-inner h4 { font-size: 17px; }
        .promo-slide-txt { max-width: 78%; }
    }
    .promo-slide-inner.has-img { background-size: cover; background-position: center; background-repeat: no-repeat; }
    .promo-slide-inner.has-img::before { display: none; }
    .promo-slide-inner::after { content: ""; position: absolute; right: -50px; top: -50px; width: 220px; height: 220px; border-radius: 50%; background: rgba(255,255,255,0.12); }
    .promo-slide-txt { position: relative; z-index: 2; max-width: 62%; }
    .promo-badge { display: inline-flex; align-items: center; background: #fff; color: #c8102e; font-weight: 800; font-size: 11px; letter-spacing: 1px; padding: 5px 14px; border-radius: 50px; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .promo-slide-inner h4 { font-weight: 900; margin: 0 0 6px; font-size: 22px; line-height: 1.2; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    .promo-slide-inner p { margin: 0; font-size: 13px; opacity: 0.92; line-height: 1.4; }
    .promo-cta { display: inline-flex; align-items: center; gap: 6px; margin-top: 14px; background: #fff; color: #b91c1c; font-weight: 800; font-size: 13px; padding: 8px 18px; border-radius: 50px; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.18); transition: transform .15s ease; border: none; cursor: pointer; }
    .promo-cta:hover { transform: translateY(-2px); color: #7f1d1d; }
    .promo-ikon { position: relative; z-index: 2; font-size: 56px; opacity: 1; }
    .promo-ikon i { opacity: 0.45; }
    .promo-ikon img { opacity: 1; }
    .promo-dots { display: flex; justify-content: center; gap: 8px; margin-top: 12px; }
    .promo-dot { width: 34px; height: 34px; border-radius: 10px; border: 1.5px solid #e5e7eb; background: #fff; color: #374151; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; transition: all .2s ease; padding: 0; }
    .promo-dot:hover { border-color: #c8102e; color: #c8102e; }
    .promo-dot.on { background: linear-gradient(135deg,#c8102e,#ef4444); border-color: transparent; color: #fff; box-shadow: 0 4px 10px rgba(200,16,46,0.35); }
</style>
<div class="promo-slider-wrap mb-4" id="promoSlider">
    <div class="promo-track" id="promoTrack">
        <?php
            $__warnaDefault = [
                'linear-gradient(135deg,#b91c1c 0%,#dc2626 50%,#f87171 100%)',
                'linear-gradient(135deg,#15803d 0%,#16a34a 50%,#4ade80 100%)',
                'linear-gradient(135deg,#1d4ed8 0%,#2563eb 50%,#60a5fa 100%)',
                'linear-gradient(135deg,#d97706 0%,#f59e0b 50%,#fbbf24 100%)',
                'linear-gradient(135deg,#7c3aed 0%,#8b5cf6 50%,#c084fc 100%)',
            ];
            // Dikelompokkan 2 promo per halaman slide (geser 2 gambar tiap swipe)
            $__promoPages = collect($promoSlides)->chunk(2);
        ?>
        <?php $__currentLoopData = $__promoPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $__pi => $__page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="promo-slide">
            <?php $__currentLoopData = $__page; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j => $sl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $i = $__pi * 2 + $j; // indeks global slide untuk pilihan warna default
                $__bg = $sl['gambar']
                    ? "background-image:url('".asset($sl['gambar'])."');background-size:cover;background-position:".($sl['fokus'] ?? 'center').";"
                    : ($sl['warna'] ? "background:".$sl['warna'].";" : ($__warnaDefault[$i % count($__warnaDefault)].";"));
            ?>
            <div class="promo-slide-inner <?php echo e($sl['gambar'] ? 'has-img' : ''); ?>" style="<?php echo e($__bg); ?>">
                <div class="promo-slide-txt">
                    <?php if($sl['badge']): ?> <span class="promo-badge"><i class="fa-solid fa-bolt me-1"></i><?php echo e($sl['badge']); ?></span> <?php endif; ?>
                    <h4><?php echo e($sl['judul']); ?></h4>
                    <p><?php echo e($sl['sub'] ?? ''); ?></p>
                    <a href="<?php echo e($sl['url'] ?? route('toko.index')); ?>" class="promo-cta"><?php echo e($sl['cta'] ?? 'Lihat Promo'); ?> <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <?php if(!$sl['gambar']): ?>
                <div class="promo-ikon d-none d-md-block"><i class="fa-solid <?php echo e($sl['ikon'] ?? 'fa-tags'); ?>"></i></div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<div class="promo-dots mb-4" id="promoDots">
    <?php $__currentLoopData = $__promoPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $__pi => $__page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <button type="button" class="promo-dot <?php echo e($__pi === 0 ? 'on' : ''); ?>" data-idx="<?php echo e($__pi); ?>" aria-label="Slide promo halaman <?php echo e($__pi + 1); ?>"><?php echo e($__pi + 1); ?></button>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<script>
(function() {
    var track = document.getElementById('promoTrack');
    var dotsWrap = document.getElementById('promoDots');
    var slider = document.getElementById('promoSlider');
    if (!track || !dotsWrap || !slider) return;
    var idx = 0, timer = null;
    function goSlide(i) {
        var total = track.children.length;
        if (total === 0) return;
        idx = ((i % total) + total) % total;
        track.style.transform = 'translateX(-' + (idx * 100) + '%)';
        dotsWrap.querySelectorAll('.promo-dot').forEach(function(d, k) { d.classList.toggle('on', k === idx); });
    }
    function startAuto() { stopAuto(); timer = setInterval(function() { goSlide(idx + 1); }, 5000); }
    function stopAuto() { if (timer) { clearInterval(timer); timer = null; } }
    // Delegasi klik dot â€” tetap hidup walau isi track/dots diganti realtime
    dotsWrap.addEventListener('click', function(e) {
        var d = e.target.closest('.promo-dot');
        if (d) { goSlide(parseInt(d.dataset.idx, 10)); startAuto(); }
    });
    slider.addEventListener('mouseenter', stopAuto);
    slider.addEventListener('mouseleave', startAuto);
    window.addEventListener('promoUpdated', function() { goSlide(0); startAuto(); });
    goSlide(0);
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) startAuto();
})();
</script>
<?php $promoSyncAktif = \App\Helpers\AkunHelper::isPetugas(); ?>
<?php if($promoSyncAktif): ?>

<script>
(async function petugasSyncSlider() {
    try {
        var res = await fetch('<?php echo e(route('petugas.promo.render')); ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (res.ok) {
            var html = await res.text();
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var track = document.getElementById('promoTrack');
            var dotsWrap = document.getElementById('promoDots');
            var newTrack = doc.getElementById('promoTrack');
            var newDots = doc.getElementById('promoDots');
            if (track && newTrack && newTrack.innerHTML !== track.innerHTML) {
                track.innerHTML = newTrack.innerHTML;
                if (newDots && dotsWrap) dotsWrap.innerHTML = newDots.innerHTML;
                window.dispatchEvent(new CustomEvent('promoUpdated'));
            }
        }
    } catch (e) { /* abaikan gangguan jaringan */ }
    setTimeout(petugasSyncSlider, 10000);
})();
</script>
<?php endif; ?>

<div class="quick-info-bar mb-4">
    <div class="quick-stat-item">
        <div class="stat-circle-icon">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div>
            <div class="fw-bold fs-6 text-dark">Rp <?php echo e(number_format($saldoKopma ?? 0, 0, ',', '.')); ?></div>
            <small class="text-muted" style="font-size: 11px;">
                Kopma Point &bull; <strong id="teksJmlKeranjang"><?php echo e(array_sum(session('keranjang', []))); ?></strong> item di keranjang
            </small>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <?php if(\App\Helpers\AkunHelper::isPetugas()): ?>
        <!-- Tambah Produk Cepat (Hanya Petugas) -->
        <button type="button" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
            <i class="fa-solid fa-plus me-1"></i> Tambah Produk
        </button>
        <?php endif; ?>
        <a class="quick-act-link" href="<?php echo e(route('kopma.point.index')); ?>">
            <i class="fa-solid fa-coins"></i>Top Up
        </a>
        <a class="quick-act-link" href="<?php echo e(route('keranjang.index')); ?>">
            <i class="fa-solid fa-cart-shopping"></i>Keranjang
        </a>
        <a class="quick-act-link" href="<?php echo e(route('pesanan.index')); ?>">
            <i class="fa-solid fa-receipt"></i>Pesanan
        </a>
    </div>
</div>

<div class="card card-kopma p-3 mb-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2 mb-2 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                <i class="fa-solid fa-boxes-stacked text-white" style="font-size:12px;"></i>
            </span>
            <h6 class="fw-bold mb-0 text-dark">Katalog & Pilihan Kategori</h6>
            <?php if($katAktif): ?>
                <span class="badge bg-danger rounded-pill px-2 py-1 small"><?php echo e($katAktif); ?></span>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if(\App\Helpers\AkunHelper::isPetugas()): ?>
            <!-- Tombol Utama Mode Petugas: Atur Urutan Produk (drag & panah di kartu) -->
            <button type="button" class="btn btn-sm btn-outline-dark fw-bold rounded-pill px-3"
                    id="btnModeUrutan" aria-pressed="false"
                    <?php if($q !== '' || $katAktif !== ''): ?> disabled title="Nonaktifkan pencarian/kategori dulu agar urutan global tidak salah." <?php endif; ?>>
                <i class="fa-solid fa-arrow-down-wide-short me-1"></i> <span id="teksBtnModeUrutan">Atur Urutan Produk</span>
            </button>
            <!-- Tombol Tambah Produk Cepat (Hanya Petugas) -->
            <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                <i class="fa-solid fa-circle-plus me-1"></i> + Tambah Produk
            </button>
            <?php endif; ?>
            <span class="badge bg-light text-muted border px-2 py-1 fw-semibold" id="countProdukLabel"><?php echo e($barangs->total()); ?> produk (20/hlm)</span>
        </div>
    </div>
    <!-- Pilihan Kategori Rapi & Kompak (Dari Kiri ke Kanan dan Ke Bawah) -->
    <div class="kat-chips-wrap">
        <a class="kat-chip-btn <?php echo e($katAktif === '' ? 'active' : ''); ?>" href="<?php echo e(route('toko.index')); ?>">
            <i class="fa-solid fa-border-all"></i> Semua Produk <small>(<?php echo e($barangs->total()); ?>)</small>
        </a>
        <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $icon = $catIcons[$kat->nama_kategori] ?? 'fa-tag';
            ?>
            <a class="kat-chip-btn <?php echo e($katAktif === $kat->nama_kategori ? 'active' : ''); ?>"
               href="<?php echo e(route('toko.index', ['kategori' => $kat->nama_kategori])); ?>">
                <i class="fa-solid <?php echo e($icon); ?>"></i> <?php echo e($kat->nama_kategori); ?> <small>(<?php echo e($kat->barangs_count); ?>)</small>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<style>
    /* Kartu produk minimalis ala referensi */
    @media (min-width: 1200px) { #productGridContainer .col-xl-2 { flex: 0 0 auto; width: 16.666%; } }
    @media (min-width: 992px) { #productGridContainer .col-lg-2 { flex: 0 0 auto; width: 16.666%; } }
    @media (min-width: 768px) { #productGridContainer .col-md-4 { flex: 0 0 auto; width: 33.333%; } }
    #productGridContainer .product-card { height: 100%; display: flex; flex-direction: column; background: #fff; border: 1px solid #eef1f4; border-radius: 10px; overflow: hidden; }
    #productGridContainer .product-card:hover { box-shadow: 0 10px 24px rgba(15,23,42,.10); transform: translateY(-2px); }
    #productGridContainer .product-image-box { aspect-ratio: 1 / 1; height: 150px; background: #fff; overflow: hidden; position: relative; }
    #productGridContainer .product-image-full { width: 100%; height: 100%; object-fit: contain; object-position: center; background: #fff; padding: 10px; display: block; }
    #productGridContainer .product-cat-ribbon { position: absolute; left: 0; right: 0; bottom: 0; background: #c8102e; color: #fff; font-size: 10px; font-weight: 700; text-align: center; padding: 3px 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; z-index: 2; }
    #productGridContainer .product-body { padding: 10px 12px 12px; display: flex; flex-direction: column; flex: 1; }
    #productGridContainer .product-title { font-size: 12.5px; font-weight: 500; color: #374151; line-height: 1.4; min-height: 35px; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    #productGridContainer .product-price-member { font-size: 15px; font-weight: 800; color: #c8102e; margin-bottom: 6px; line-height: 1.2; }
    #productGridContainer .product-instant { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; color: #c8102e; margin-bottom: 10px; }
    #productGridContainer .product-instant i { font-size: 12px; }
    #productGridContainer .btn-add-cart { background: #c8102e; color: #fff; border: none; border-radius: 8px; padding: 9px 10px; font-size: 13.5px; font-weight: 700; width: 100%; display: inline-flex; align-items: center; justify-content: center; transition: background .18s ease; cursor: pointer; }
    #productGridContainer .btn-add-cart:hover { background: #9f141e; }
    #productGridContainer .btn-add-cart:disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; }
    #productGridContainer .product-badge-float { position: absolute; top: 6px; left: 6px; background: #c8102e; color: #fff; font-size: 9.5px; font-weight: 800; padding: 2px 6px; border-radius: 6px; z-index: 3; box-shadow: 0 2px 6px rgba(0,0,0,0.18); }
    #productGridContainer .toko-hapus-produk { position: absolute; top: 6px; right: 6px; z-index: 5; margin: 0; }
    #productGridContainer .toko-btn-hapus { width: 28px; height: 28px; border: 0; border-radius: 8px; background: rgba(255,255,255,.95); color: #dc2626; font-size: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,.18); cursor: pointer; transition: all .18s ease; }
    #productGridContainer .toko-btn-hapus:hover { background: #dc2626; color: #fff; }
    <?php if(\App\Helpers\AkunHelper::isPetugas()): ?>
    /* ===== Mode Atur Urutan (Mode Petugas) ===== */
    .urutan-kartu-ctrl {
        position: absolute; top: 6px; left: 6px; z-index: 6;
        display: none; align-items: center; gap: 3px;
        background: rgba(15, 23, 42, .88); padding: 3px 5px; border-radius: 16px;
        box-shadow: 0 3px 8px rgba(0,0,0,.25);
    }
    body.mode-urutan .urutan-kartu-ctrl { display: inline-flex; }
    body.mode-urutan .product-badge-float { display: none; }
    body.mode-urutan .toko-petugas-edit,
    body.mode-urutan .toko-hapus-produk { display: none; }
    .urutan-kartu-ctrl .urutan-handle { color: #cbd5e1; cursor: grab; font-size: 11px; line-height: 1; }
    .urutan-kartu-ctrl button {
        width: 20px; height: 20px; border: 0; border-radius: 6px;
        background: #334155; color: #fff; font-size: 9px; padding: 0;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .urutan-kartu-ctrl button:hover { background: #c8102e; }
    .urutan-nomor-kecil {
        display: none; position: absolute; top: 34px; left: 6px; z-index: 6;
        background: #0f172a; color: #fff; font-size: 10px; font-weight: 800;
        min-width: 20px; height: 20px; padding: 0 5px; border-radius: 10px;
        align-items: center; justify-content: center;
    }
    body.mode-urutan .urutan-nomor-kecil { display: inline-flex; }
    body.mode-urutan #productGridContainer .product-card { outline: 2px dashed #c8102e; outline-offset: -2px; }
    body.mode-urutan .form-tambah-keranjang { pointer-events: none; opacity: .55; }
    body.mode-urutan .product-item-col.dragging { opacity: .4; }
    .bar-mode-urutan {
        display: none; position: fixed; left: 50%; bottom: 18px; transform: translateX(-50%);
        z-index: 1040; align-items: center; gap: 10px;
        background: #0f172a; color: #fff; padding: 8px 14px; border-radius: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,.35); max-width: 94vw;
    }
    body.mode-urutan .bar-mode-urutan { display: flex; }
    .bar-mode-urutan .petunjuk-urutan { font-size: 12px; }
    <?php endif; ?>
</style>
<div class="row g-3" id="productGridContainer" data-offset="<?php echo e(($barangs->firstItem() ?? 1) - 1); ?>" data-url-urutan="<?php echo e(route('petugas.produk.atur-urutan')); ?>">
    <?php $__empty_1 = true; $__currentLoopData = $barangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $hemat = $b->harga_jual_umum - $b->harga_jual_anggota;
            $persen = $b->harga_jual_umum > 0 ? round(($hemat / $b->harga_jual_umum) * 100) : 0;
            $habis = $b->stok < 1;
            $gToko = trim((string) ($b->gambar ?? ''));
            if ($gToko === '') { $srcToko = asset('images/logo-kopma.svg'); }
            elseif (Str::startsWith($gToko, ['http://','https://','//','data:'])) { $srcToko = $gToko; }
            else { $gToko = ltrim($gToko,'/'); $srcToko = file_exists(public_path($gToko)) ? asset($gToko) : asset('storage/'.preg_replace('#^(public/|storage/)#','',$gToko)); }
        ?>
        <div class="col-6 col-sm-6 col-md-4 col-lg-2 col-xl-2 product-item-col"
             data-id="<?php echo e($b->id_barang); ?>"
             data-urutan="<?php echo e((int) $b->urutan); ?>"
             data-nama="<?php echo e(strtolower($b->nama_barang)); ?>"
             data-barcode="<?php echo e(strtolower($b->kode_barcode)); ?>"
             data-kategori="<?php echo e(strtolower($b->kategori->nama_kategori ?? '')); ?>">
            <div class="product-card">
                <div class="product-image-box position-relative">
                    <?php if(\App\Helpers\AkunHelper::isPetugas()): ?>
                    
                    <div class="urutan-kartu-ctrl" title="Geser untuk mengubah urutan, atau tekan panah naik/turun">
                        <span class="urutan-handle" aria-label="Geser kartu"><i class="fa-solid fa-grip-vertical"></i></span>
                        <button type="button" class="urutan-naik" title="Naikkan" aria-label="Naikkan"><i class="fa-solid fa-arrow-up"></i></button>
                        <button type="button" class="urutan-turun" title="Turunkan" aria-label="Turunkan"><i class="fa-solid fa-arrow-down"></i></button>
                    </div>
                    <span class="urutan-nomor-kecil" data-slot-label><?php echo e((int) $b->urutan); ?></span>
                    <div class="toko-petugas-edit" title="Ganti foto produk dari galeri laptop">
                        <label for="input-foto-toko-<?php echo e($b->id_barang); ?>" class="toko-btn-edit">
                            <i class="fa-solid fa-camera"></i> <span>Ubah</span>
                        </label>
                        <input type="file" id="input-foto-toko-<?php echo e($b->id_barang); ?>" class="d-none input-upload-toko" data-id="<?php echo e($b->id_barang); ?>" accept="image/*">
                    </div>
                    
                    <form action="<?php echo e(route('toko.destroy', $b->id_barang)); ?>" method="POST" class="toko-hapus-produk"
                          onsubmit="return confirm('Hapus produk &quot;<?php echo e($b->nama_barang); ?>&quot; dari stok toko?\nTindakan ini tidak bisa dibatalkan.');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="toko-btn-hapus" title="Hapus produk">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <img src="<?php echo e($srcToko); ?>" alt="<?php echo e($b->nama_barang); ?>" loading="lazy" class="product-image-full img-produk-target-<?php echo e($b->id_barang); ?>" onerror="this.onerror=null;this.src='<?php echo e(asset('images/logo-kopma.svg')); ?>';">
                    <?php if($persen >= 5): ?> <span class="product-badge-float">-<?php echo e($persen); ?>%</span> <?php endif; ?>
                    <div class="product-cat-ribbon"><?php echo e($b->kategori ? Str::limit($b->kategori->nama_kategori, 18) : 'Umum'); ?></div>
                </div>
                <div class="product-body">
                    <h6 class="product-title" title="<?php echo e($b->nama_barang); ?>"><?php echo e($b->nama_barang); ?></h6>
                    <div class="mt-auto">
                        <div class="product-price-member">Rp <?php echo e(number_format($b->harga_jual_anggota, 0, ',', '.')); ?></div>
                        <div class="product-instant"><i class="fa-solid fa-truck-fast"></i> Pengiriman Instan</div>
                        <form action="<?php echo e(route('belanja.tambah')); ?>" method="POST" class="form-tambah-keranjang d-inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id_barang" value="<?php echo e($b->id_barang); ?>">
                            <button type="submit" class="btn-add-cart w-100 <?php echo e($habis ? 'disabled' : ''); ?>" <?php echo e($habis ? 'disabled' : ''); ?>>
                                <?php echo e($habis ? 'Habis' : 'Beli'); ?>

                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12"><div class="text-center text-muted py-4">Produk tidak ditemukan.</div></div>
    <?php endif; ?>
</div>

<?php if($barangs->hasPages()): ?>
    <div class="mt-4 d-flex justify-content-center kopma-pagination">
        <?php echo e($barangs->links('pagination.kopma')); ?>

    </div>
<?php endif; ?>

<?php if(\App\Helpers\AkunHelper::isPetugas()): ?>
<div class="bar-mode-urutan" id="barModeUrutan" role="status" data-semua="<?php echo e(json_encode($urutanSemua ?? [])); ?>">
    <span class="petunjuk-urutan"><i class="fa-solid fa-grip-vertical me-1"></i><b>Mode Atur Urutan</b> — geser kartu atau tekan ▲ / ▼, lalu <b>Simpan Urutan</b>.</span>
    <button type="button" class="btn btn-sm btn-outline-light fw-bold rounded-pill px-3" id="btnBatalUrutan">Batal</button>
    <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" id="btnSimpanUrutan">
        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Urutan
    </button>
</div>
<?php endif; ?>
 
<?php if(\App\Helpers\AkunHelper::isPetugas()): ?>
<section class="km-section mb-4">
    <div class="card card-kopma shadow-sm">
        <div class="card-kopma-header">
            <h5 class="fw-bold text-danger mb-0"><i class="fa-solid fa-sliders me-2"></i> Kelola Promo &amp; Tampilan Toko</h5>
            <span class="badge bg-danger">Petugas Only</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-grid">
                        <a href="<?php echo e(route('petugas.produk.index')); ?>" class="btn btn-danger w-100 py-3 fw-bold">
                            <i class="fa-solid fa-boxes-stacked fa-lg mb-1 d-block"></i> Kelola Produk
                        </a>
                        <p class="text-muted small mt-2">Lihat, tambah, ubah, hapus &amp; atur urutan semua produk katalog.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-grid">
                        <a href="<?php echo e(route('petugas.promo.index')); ?>" class="btn btn-danger w-100 py-3 fw-bold">
                            <i class="fa-solid fa-bolt fa-lg mb-1 d-block"></i> Kelola Promo
                        </a>
                        <p class="text-muted small mt-2">Tambah, ubah, hapus slide promo dan gambar dari galeri laptop.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-grid">
                        <button type="button" class="btn btn-warning w-100 py-3 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalLogoToko">
                            <i class="fa-solid fa-store fa-lg mb-1 d-block"></i> Ganti Logo Minimarket
                        </button>
                        <p class="text-muted small mt-2">Upload foto logo toko yang akan tampil di navbar &amp; hero.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-grid">
                        <button type="button" class="btn btn-info w-100 py-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalTemaWarna">
                            <i class="fa-solid fa-palette fa-lg mb-1 d-block"></i> Ganti Tema Warna
                        </button>
                        <p class="text-muted small mt-2">Ubah warna utama dashboard (hanya petugas).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Modal Ganti Logo Toko -->
<div class="modal fade" id="modalLogoToko" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white" style="background: linear-gradient(135deg,#c8102e,#ef4444);">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-store me-2"></i> Ganti Logo Minimarket</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('petugas.logo-toko')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="logoPreview" src="<?php echo e(asset(\App\Models\PengaturanToko::ambil('logo_toko') ?: 'images/logo-kopma.svg')); ?>" alt="logo" style="max-height:90px;border-radius:10px;border:1px solid #e2e8f0;background:#fff;">
                        <div class="text-muted small mt-2">Logo saat ini</div>
                    </div>
                    <label class="form-label fw-bold small text-dark">Pilih file logo dari laptop</label>
                    <input type="file" name="logo" class="form-control" accept="image/*" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-save me-1"></i> Simpan Logo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ganti Tema Warna -->
<div class="modal fade" id="modalTemaWarna" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white" style="background: linear-gradient(135deg,#c8102e,#ef4444);">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-palette me-2"></i> Ganti Tema Warna Dashboard</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTemaWarna">
                <div class="modal-body">
                    <label class="form-label fw-bold small text-dark">Pilih warna utama</label>
                    <div class="warna-grid" style="margin-top:8px;">
                        <button type="button" class="warna-opt" style="background:#c8102e;" data-w="#c8102e" onclick="setWarna(this)"></button>
                        <button type="button" class="warna-opt" style="background:#15803d;" data-w="#15803d" onclick="setWarna(this)"></button>
                        <button type="button" class="warna-opt" style="background:#1d4ed8;" data-w="#1d4ed8" onclick="setWarna(this)"></button>
                        <button type="button" class="warna-opt" style="background:#d97706;" data-w="#d97706" onclick="setWarna(this)"></button>
                        <button type="button" class="warna-opt custom" id="wCustom" onclick="setWarna(this)">#</button>
                    </div>
                    <input type="color" id="wColor" value="#c8102e" style="display:none;">
                    <input type="hidden" name="warna" id="wHidden">
                    <div class="preview-wrap mt-3" style="height:70px;">
                        <div style="width:100%;height:100%;border-radius:8px;border:2px solid #c8102e;" id="wPreview"></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="simpanWarna()"><i class="fa-solid fa-save me-1"></i> Simpan Tema</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    let currentWarna = '<?php echo e(\App\Models\PengaturanToko::ambil('tema_warna') ?: '#c8102e'); ?>';
    document.getElementById('wHidden').value = currentWarna;
    document.getElementById('wColor').value = currentWarna;
    document.getElementById('wPreview').style.background = currentWarna;
    function setWarna(el){
        document.querySelectorAll('.warna-opt').forEach(o=>o.classList.remove('active'));
        el.classList.add('active');
        const w = el.dataset.w || el.value;
        document.getElementById('wHidden').value = w;
        document.getElementById('wColor').value = w.includes('#')?w:'#c8102e';
        document.getElementById('wPreview').style.background = w;
    }
    document.getElementById('wColor').addEventListener('input', function(){
        document.getElementById('wHidden').value = this.value;
        document.getElementById('wPreview').style.background = this.value;
    });
    function simpanWarna(){
        const w = document.getElementById('wHidden').value;
        fetch('<?php echo e(route('petugas.tema-warna')); ?>', {
            method:'POST',
            headers: {'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>','Content-Type':'application/json','Accept':'application/json'},
            body: JSON.stringify({warna: w})
        }).then(r=>r.json()).then(d=>{
            if(d.success){
                document.documentElement.style.setProperty('--color-merah', w);
                document.documentElement.style.setProperty('--bs-primary', w);
                document.querySelector('meta[name="theme-color"]').setAttribute('content', w);
                new bootstrap.Modal(document.getElementById('modalTemaWarna')).hide();
                location.reload();
            } else alert(d.message||'Gagal');
        });
    }
</script>
<?php $__env->stopPush(); ?>

<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #7f1d1d, #c8102e);">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked fa-lg"></i>
                    <h5 class="modal-title fw-bold mb-0" id="modalTambahProdukLabel">Tambah Produk Baru ke Toko</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('toko.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4 bg-light">
                    <p class="text-secondary small mb-3">
                        Lengkapi detail produk di bawah ini untuk menambahkan barang ke dalam katalog KOPMA MART.
                    </p>
                    <div class="row g-3">
                        <!-- Nama Barang -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-dark">Nama Barang / Produk <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" required
                                   placeholder="Contoh: Indomie Goreng Spesial 85g">
                        </div>
                        <!-- Kategori Barang -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                <?php $__currentLoopData = $kategoris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($k->id_kategori); ?>"><?php echo e($k->nama_kategori); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <!-- Kode Barcode -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">Kode Barcode</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-barcode text-muted"></i></span>
                                <input type="text" name="kode_barcode" id="inputKodeBarcode" class="form-control"
                                       placeholder="Kosongkan untuk auto-generate">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="generateBarcode()">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto
                                </button>
                            </div>
                            <small class="text-muted" style="font-size: 11px;">Bisa scan barcode fisik atau generate otomatis.</small>
                        </div>
                        <!-- Foto Produk Asli (Upload) -->
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Foto Produk</label>
                            <input type="file" name="gambar" id="inputGambarProduk" class="form-control" accept="image/*">
                            <small class="text-muted" style="font-size: 11px;">Opsional â€” upload foto asli produk (JPG/PNG/WEBP, maks 2MB). Kalau kosong, dipakai logo KOPMA.</small>
                        </div>
                        <!-- Stok Awal & Stok Minimum -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-dark">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" min="0" value="20" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-dark">Stok Minimum</label>
                            <input type="number" name="stok_minimum" class="form-control" min="0" value="5">
                        </div>
                        <!-- Harga Jual Anggota (Member) -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Harga Anggota (Member) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-danger fw-bold">Rp</span>
                                <input type="number" name="harga_jual_anggota" id="inpHargaAnggota" class="form-control" min="0" required
                                       placeholder="3000">
                            </div>
                        </div>
                        <!-- Harga Jual Umum -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Harga Umum (Non-Member) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted">Rp</span>
                                <input type="number" name="harga_jual_umum" id="inpHargaUmum" class="form-control" min="0" required
                                       placeholder="3500">
                            </div>
                        </div>
                        <!-- Harga Beli / Kulakan -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Harga Beli / Kulakan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted">Rp</span>
                                <input type="number" name="harga_beli" id="inpHargaBeli" class="form-control" min="0"
                                       placeholder="Otomatis 85%">
                            </div>
                            <small class="text-muted" style="font-size: 11px;">Opsional (bisa diisi untuk hitung laba bersih).</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container-custom">
    <div id="cartToast" class="toast align-items-center text-white bg-dark border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex align-items-center p-3">
            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; flex-shrink: 0;">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold small" id="cartToastTitle">Produk Masuk Keranjang!</div>
                <div class="text-white-50" style="font-size: 11px;" id="cartToastMsg">Item berhasil ditambahkan.</div>
            </div>
            <a href="<?php echo e(route('keranjang.index')); ?>" class="btn btn-sm btn-danger fw-bold rounded-pill ms-2 text-nowrap">
                Lihat
            </a>
            <button type="button" class="btn-close btn-close-white me-1 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
    // Live Search INSTAN ala halaman Kelola Produk: ketik -> langsung filter kartu di
    // halaman ini + tampilkan dropdown saran dari SELURUH katalog (client-side),
    // tanpa perlu tekan Enter / tombol Cari dan tanpa reload halaman.
    const clientInput = document.getElementById('inputSearchClient');
    const formCariHero = document.getElementById('formCariHero');
    const infoCariHero = document.getElementById('infoCariHero');
    const panelSaran = document.getElementById('heroSaranPanel');
    const btnBersihHero = document.getElementById('heroBersihCari');
    // Data semua produk dari server (untuk saran realtime - sama seperti Kelola Produk/Promo)
    const produkCari = <?php echo json_encode($semuaUntukCari ?? [], 15, 512) ?>;
    const escTeks = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const normTeks = s => String(s || '').toLowerCase().trim();
    const tutupSaran = () => { if (panelSaran) { panelSaran.classList.remove('tampil'); clientInput.setAttribute('aria-expanded', 'false'); } };
    if (clientInput) {
        clientInput.addEventListener('input', function(e) {
            const term = normTeks(e.target.value);
            // 1) Filter instan kartu produk yang sedang tampil di halaman ini
            let countVisible = 0;
            document.querySelectorAll('.product-item-col').forEach(col => {
                const nama = col.getAttribute('data-nama') || '';
                const barcode = col.getAttribute('data-barcode') || '';
                const kat = col.getAttribute('data-kategori') || '';
                const match = !term || nama.includes(term) || barcode.includes(term) || kat.includes(term);
                col.style.display = match ? '' : 'none';
                if (match) countVisible++;
            });
            const lbl = document.getElementById('countProdukLabel');
            if (lbl) lbl.textContent = countVisible + ' produk ditemukan';
            // Tombol bersih ✕ hanya tampil saat ada teks
            if (btnBersihHero) btnBersihHero.classList.toggle('tampil', term !== '');
            // Input kosong -> tutup panel saran (kartu sudah ditampilkan semua kembali)
            if (!term) {
                if (infoCariHero) infoCariHero.textContent = '';
                if (panelSaran) panelSaran.innerHTML = '';
                tutupSaran();
                return;
            }
            // 2) Dropdown saran dari SELURUH katalog (semua kata kunci harus cocok)
            const kws = term.split(/\s+/).filter(Boolean);
            const cocok = produkCari.filter(p => {
                const teks = normTeks(p.nama) + ' ' + normTeks(p.kat);
                return kws.every(k => teks.includes(k));
            });
            let htmlSaran = '';
            if (cocok.length) {
                htmlSaran += '<div class="hero-saran-info">Menampilkan <b>' + cocok.length + '</b> dari <b>' + produkCari.length + '</b> produk — klik salah satu untuk melihat detailnya</div>';
                htmlSaran += cocok.slice(0, 8).map(p =>
                    '<button type="button" class="hero-saran-item" data-nama="' + escTeks(p.nama) + '">' +
                    (p.gambar
                        ? '<img src="' + escTeks(p.gambar) + '" alt="">'
                        : '<span style="width:36px;height:36px;border-radius:8px;background:#fef2f2;display:inline-flex;align-items:center;justify-content:center;color:#fca5a5;"><i class="fa-solid fa-box-open"></i></span>') +
                    '<span class="nama">' + escTeks(p.nama) + '</span>' +
                    '<span class="harga">' + escTeks(p.harga) + '</span></button>'
                ).join('');
                if (cocok.length > 8) {
                    htmlSaran += '<div class="hero-saran-info">+ <b>' + (cocok.length - 8) + '</b> produk lainnya — tekan Enter untuk melihat semua</div>';
                }
            } else {
                // Pesan konsisten dengan fitur pencarian lain
                htmlSaran = '<div class="hero-saran-info kosong">Produk tidak ditemukan</div>';
            }
            if (panelSaran) {
                panelSaran.innerHTML = htmlSaran;
                panelSaran.classList.add('tampil');
                clientInput.setAttribute('aria-expanded', 'true');
            }
            // Info kecil di toolbar: jumlah cocok halaman ini vs seluruh katalog
            if (infoCariHero) {
                infoCariHero.textContent = countVisible + ' cocok di halaman ini · ' + cocok.length + ' dari ' + produkCari.length + ' di seluruh katalog';
            }
        });
        // Klik salah satu saran -> buka katalog toko dengan produk yang dicari
        if (panelSaran) {
            panelSaran.addEventListener('click', function(e) {
                const it = e.target.closest('.hero-saran-item');
                if (!it) return;
                const q = it.getAttribute('data-nama') || '';
                if (formCariHero) {
                    const inp = formCariHero.querySelector('input[name="q"]');
                    if (inp) inp.value = q;
                    formCariHero.submit();
                }
            });
        }
        // Tombol bersih ✕ -> kosongkan input + tampilkan lagi semua kartu
        if (btnBersihHero) {
            btnBersihHero.addEventListener('click', function() {
                clientInput.value = '';
                clientInput.dispatchEvent(new Event('input'));
                clientInput.focus();
            });
        }
        // Escape -> bersihkan pencarian; klik di luar panel -> tutup saran
        clientInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { clientInput.value = ''; clientInput.dispatchEvent(new Event('input')); }
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.hero-search-wrap')) tutupSaran();
        });
    }
    // Auto Generate Barcode Generator Helper
    function generateBarcode() {
        const t = new Date();
        const code = 'BRG-' + t.getFullYear() +
            String(t.getMonth() + 1).padStart(2, '0') +
            String(t.getDate()).padStart(2, '0') +
            String(t.getHours()).padStart(2, '0') +
            String(t.getMinutes()).padStart(2, '0') +
            String(t.getSeconds()).padStart(2, '0');
        document.getElementById('inputKodeBarcode').value = code;
    }
    // Auto-calculate Harga Beli ketika Harga Anggota diisi
    const inpHargaAnggota = document.getElementById('inpHargaAnggota');
    const inpHargaBeli = document.getElementById('inpHargaBeli');
    if (inpHargaAnggota && inpHargaBeli) {
        inpHargaAnggota.addEventListener('input', function() {
            if (!inpHargaBeli.value) {
                const v = parseFloat(this.value) || 0;
                if (v > 0) inpHargaBeli.placeholder = 'Est. Rp ' + Math.round(v * 0.85);
            }
        });
    }
    // Interaktif Tambah ke Keranjang via AJAX
    document.querySelectorAll('.form-tambah-keranjang').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menambah...';
            try {
                const formData = new FormData(this);
                const res = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    btn.classList.remove('btn-add-cart');
                    btn.classList.add('btn', 'btn-success', 'btn-sm');
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Masuk!';
                    // Update badge keranjang di layar
                    const totalCart = data.total_cart || 0;
                    document.querySelectorAll('.cart-count-badge').forEach(el => el.textContent = totalCart);
                    const teksCart = document.getElementById('teksJmlKeranjang');
                    if (teksCart) teksCart.textContent = totalCart;
                    // Tampilkan toast notifikasi
                    const toastMsg = document.getElementById('cartToastMsg');
                    if (toastMsg) toastMsg.textContent = data.message;
                    const toastEl = document.getElementById('cartToast');
                    if (toastEl && window.bootstrap) {
                        const bsToast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 });
                        bsToast.show();
                    }
                    setTimeout(() => {
                        btn.classList.remove('btn', 'btn-success', 'btn-sm');
                        btn.classList.add('btn-add-cart');
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                    }, 1400);
                } else {
                    // Fallback submit standar bila respons bukan JSON valid
                    this.submit();
                }
            } catch (err) {
                // Fallback jika fetch bermasalah
                this.submit();
            }
        });
    });
    // ===== Realtime Upload Foto Produk dari Galeri Laptop (Khusus Petugas) =====
    document.querySelectorAll('.input-upload-toko').forEach(inp => {
        inp.addEventListener('change', async function () {
            const id = this.dataset.id;
            const file = this.files[0];
            if (!file) return;
            const label = document.querySelector(`label[for="${this.id}"]`);
            const oldHtml = label ? label.innerHTML : '';
            if (label) label.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ...';
            const fd = new FormData();
            fd.append('gambar', file);
            fd.append('_token', '<?php echo e(csrf_token()); ?>');
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
                    // Update realtime semua foto produk dengan id ini
                    document.querySelectorAll(`.img-produk-target-${id}`).forEach(img => {
                        img.src = data.gambar_url + '?t=' + Date.now();
                    });
                    if (label) {
                        label.classList.add('bg-success');
                        label.innerHTML = '<i class="fa-solid fa-check"></i>';
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
                alert('Terjadi kesalahan koneksi saat mengunggah foto produk.');
                if (label) label.innerHTML = oldHtml;
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php if(\App\Helpers\AkunHelper::isPetugas()): ?>
<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    'use strict';
    // ===== MODE ATUR URUTAN PRODUK (khusus Mode Petugas, dasbor utama) =====
    var btnMode = document.getElementById('btnModeUrutan');
    var grid = document.getElementById('productGridContainer');
    var barMode = document.getElementById('barModeUrutan');
    if (!btnMode || !grid) return;

    var teksBtn = document.getElementById('teksBtnModeUrutan');
    var offset = parseInt(grid.getAttribute('data-offset') || '0', 10); // indeks global kartu pertama halaman ini
    var urlUrutan = grid.getAttribute('data-url-urutan') || '';
    var semuaId = [];
    try { semuaId = JSON.parse((barMode && barMode.getAttribute('data-semua')) || '[]'); } catch (e) { semuaId = []; }

    var modeAktif = false;
    var kolomAsli = null; // urutan kartu saat mode dibuka (untuk tombol Batal)

    function daftarKolom() {
        return Array.prototype.slice.call(grid.querySelectorAll('.product-item-col'));
    }
    // Nomor kecil di kartu = posisi global (offset halaman + indeks DOM)
    function nomorKartu() {
        daftarKolom().forEach(function (col, i) {
            var lbl = col.querySelector('[data-slot-label]');
            if (lbl) lbl.textContent = offset + i;
            col.setAttribute('data-urutan', offset + i);
        });
    }
    function setMode(on) {
        modeAktif = on;
        document.body.classList.toggle('mode-urutan', on);
        btnMode.classList.toggle('btn-dark', on);
        btnMode.classList.toggle('btn-outline-dark', !on);
        btnMode.setAttribute('aria-pressed', on ? 'true' : 'false');
        if (teksBtn) teksBtn.textContent = on ? 'Selesai Atur' : 'Atur Urutan Produk';
        daftarKolom().forEach(function (col) { col.draggable = on; });
        if (on) {
            kolomAsli = daftarKolom();
            nomorKartu();
        }
    }
    btnMode.addEventListener('click', function () { setMode(!modeAktif); });

    var btnBatal = document.getElementById('btnBatalUrutan');
    if (btnBatal) btnBatal.addEventListener('click', function () {
        // Kembalikan urutan kartu seperti sebelum mode (tanpa menyimpan)
        if (kolomAsli) kolomAsli.forEach(function (col) { grid.appendChild(col); });
        nomorKartu();
        setMode(false);
    });
    // ---- Tombol aksi cepat Naikkan / Turunkan (delegasi, hanya mode aktif) ----
    grid.addEventListener('click', function (e) {
        if (!modeAktif) return;
        var naik = e.target.closest ? e.target.closest('.urutan-naik') : null;
        var turun = e.target.closest ? e.target.closest('.urutan-turun') : null;
        if (!naik && !turun) return;
        e.preventDefault();
        var col = e.target.closest('.product-item-col');
        if (!col) return;
        if (naik) {
            var prev = col.previousElementSibling;
            if (prev && prev.classList.contains('product-item-col')) grid.insertBefore(col, prev);
        } else {
            var next = col.nextElementSibling;
            if (next && next.classList.contains('product-item-col')) grid.insertBefore(next, col);
        }
        nomorKartu();
    });

    // ---- Drag & drop HTML5 antar kartu (hanya mode aktif) ----
    var kolomDrag = null;
    grid.addEventListener('dragstart', function (e) {
        if (!modeAktif) return;
        var col = e.target.closest ? e.target.closest('.product-item-col') : null;
        if (!col) return;
        kolomDrag = col;
        col.classList.add('dragging');
        try { e.dataTransfer.setData('text/plain', ''); e.dataTransfer.effectAllowed = 'move'; } catch (err) {}
    });
    grid.addEventListener('dragend', function () {
        if (kolomDrag) kolomDrag.classList.remove('dragging');
        kolomDrag = null;
        nomorKartu();
    });
    grid.addEventListener('dragover', function (e) {
        if (!modeAktif || !kolomDrag) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var kolom = daftarKolom().filter(function (c) { return c !== kolomDrag; });
        var terbaik = null, jarakTerbaik = Infinity, bestDx = 0, bestDy = 0;
        kolom.forEach(function (c) {
            var r = c.getBoundingClientRect();
            var dx = e.clientX - (r.left + r.width / 2);
            var dy = e.clientY - (r.top + r.height / 2);
            var d = Math.sqrt(dx * dx + dy * dy);
            if (d < jarakTerbaik) { jarakTerbaik = d; terbaik = c; bestDx = dx; bestDy = dy; }
        });
        if (!terbaik || terbaik === kolomDrag) return;
        // Kursor di kiri/atas pusat kartu target -> sisipkan sebelum, selain itu sesudah
        var sebelum = (Math.abs(bestDy) >= Math.abs(bestDx) && bestDy < 0) || bestDx < 0;
        if (sebelum) grid.insertBefore(kolomDrag, terbaik);
        else grid.insertBefore(kolomDrag, terbaik.nextElementSibling);
    });
    grid.addEventListener('drop', function (e) {
        if (!modeAktif || !kolomDrag) return;
        e.preventDefault();
        nomorKartu();
    });

    // ---- Simpan: urutan GLOBAL 0..n-1 (blok halaman ini dipindah, sisanya dinormalkan) ----
    var btnSimpan = document.getElementById('btnSimpanUrutan');
    if (btnSimpan) btnSimpan.addEventListener('click', function () {
        if (!semuaId.length || !urlUrutan) { alert('Data urutan tidak tersedia. Muat ulang halaman lalu coba lagi.'); return; }
        var pageIds = daftarKolom().map(function (c) { return parseInt(c.getAttribute('data-id'), 10); });
        if (!pageIds.length) { setMode(false); return; }
        // Peta posisi global: blok [offset..offset+n) diisi kartu sesuai urutan DOM baru,
        // sisanya menempati indeks globalnya -> hasil 0..n-1 tanpa duplikat.
        var target = {};
        var pi = 0;
        semuaId.forEach(function (id, i) {
            var diHalaman = (i >= offset && i < offset + pageIds.length);
            target[diHalaman ? pageIds[pi++] : id] = i;
        });
        if (pi !== pageIds.length) { alert('Pencarian/kategori aktif — nonaktifkan dulu sebelum menyimpan urutan.'); return; }

        var fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        semuaId.forEach(function (id) { fd.append('urutan[' + id + ']', target[id]); });

        btnSimpan.disabled = true;
        fetch(urlUrutan, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd
        }).then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            daftarKolom().forEach(function (col, i) { col.setAttribute('data-urutan', offset + i); });
            var lama = btnSimpan.innerHTML;
            btnSimpan.innerHTML = '<i class="fa-solid fa-check me-1"></i> Tersimpan';
            btnSimpan.classList.replace('btn-danger', 'btn-success');
            setTimeout(function () {
                btnSimpan.innerHTML = lama;
                btnSimpan.classList.replace('btn-success', 'btn-danger');
                setMode(false);
            }, 900);
        }).catch(function (err) {
            alert('Gagal menyimpan urutan: ' + err.message);
        }).finally(function () {
            btnSimpan.disabled = false;
        });
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/toko/index.blade.php ENDPATH**/ ?>