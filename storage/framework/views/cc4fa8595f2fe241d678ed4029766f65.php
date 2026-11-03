
<?php
    use App\Models\ChatPesan;

    // SISTEM TERPISAH: bottom-nav ikut MODE AKTIF (1 tab = 1 peran).
    $modeBnav = \App\Helpers\AkunHelper::modeAktif();
    $sebagaiMitra   = $modeBnav === 'kurir';
    $sebagaiPetugas = $modeBnav === 'petugas';
    $sebagaiUser    = (bool) session('pelanggan_id');
    $jmlKeranjang   = array_sum(session('keranjang', []));
    $chatBaru       = 0;

    try {
        if ($modeBnav === 'kurir') {
            // KURIR: badge hanya dari pesanan yang IA ANTAR (bukan semua chat user).
            $ef = \App\Helpers\AkunHelper::kurirEfektif();
            if ($ef && $ef['tipe'] === 'anggota') {
                $idsKurir = \App\Models\Penjualan::where('id_kurir_anggota', $ef['id'])
                    ->where('status_pesanan', '!=', 'selesai')->pluck('id_penjualan');
            } elseif ($ef) {
                $idsKurir = \App\Models\Penjualan::where('id_kurir', $ef['id'])
                    ->where('status_pesanan', '!=', 'selesai')->pluck('id_penjualan');
            } else {
                $idsKurir = collect();
            }
            $chatBaru = $idsKurir->isNotEmpty()
                ? ChatPesan::whereIn('id_penjualan', $idsKurir)->where('pengirim', 'user')->where('dibaca', false)->count()
                : 0;
        } elseif ($modeBnav === 'petugas') {
            // PETUGAS: tidak diberi menu chat kurir (disembunyikan), badge nol.
            $chatBaru = 0;
        } elseif (session('pelanggan_id')) {
            $chatBaru = ChatPesan::where('id_anggota', session('pelanggan_id'))
                ->where('pengirim', 'kurir')->where('dibaca', false)->count();
        }
    } catch (\Throwable $e) {
        $chatBaru = 0;
    }
?>

<style>
    .bnav{position:fixed;left:0;right:0;bottom:0;z-index:9997;background:rgba(255,255,255,.97);
        backdrop-filter:blur(12px);border-top:1px solid #e5e7eb;
        box-shadow:0 -6px 20px rgba(15,23,42,.08);padding-bottom:env(safe-area-inset-bottom)}
    .bnav-inner{display:flex;align-items:stretch;justify-content:space-around;max-width:760px;margin:0 auto}
    .bnav-item{position:relative;flex:1 1 0;display:flex;flex-direction:column;align-items:center;
        justify-content:center;gap:2px;padding:8px 2px 7px;text-decoration:none;color:#64748b;
        font-size:10px;font-weight:700;line-height:1.1;transition:color .15s}
    .bnav-item i{font-size:17px}
    .bnav-item:hover{color:#c8102e}
    .bnav-item.active{color:#c8102e}
    .bnav-item.active::before{content:"";position:absolute;top:0;width:26px;height:3px;border-radius:0 0 4px 4px;
        background:linear-gradient(90deg,#dc2626,#ef4444)}
    .bnav-badge{position:absolute;top:4px;right:calc(50% - 24px);min-width:17px;height:17px;padding:0 4px;
        border-radius:9px;background:#ef4444;color:#fff;font-size:10px;font-weight:800;
        display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(239,68,68,.45)}
    .bnav-badge.kuning{background:#dc2626;box-shadow:0 2px 6px rgba(245,158,11,.45)}
    /* Menu bawah tampil di SEMUA layar (laptop = Android/iOS) */
    .bnav-inner{max-width:760px}
    /* Ruang agar konten tidak tertutup menu bawah */
    body{padding-bottom:78px}
    /* Tombol chatbot naik sedikit supaya tidak menabrak menu bawah */
    #chatFab{bottom:88px !important}
    #chatPanel{bottom:152px !important}
</style>

<nav class="bnav d-print-none" aria-label="Menu bawah">
    <div class="bnav-inner">
        <a href="<?php echo e(route('toko.index')); ?>"
           class="bnav-item <?php echo e(request()->routeIs('toko.*', 'dashboard') ? 'active' : ''); ?>">
            <i class="fa-solid fa-store"></i><span>Beranda</span>
        </a>

        <a href="<?php echo e(route('keranjang.index')); ?>"
           class="bnav-item <?php echo e(request()->routeIs('keranjang.*') ? 'active' : ''); ?>">
            <i class="fa-solid fa-cart-shopping"></i><span>Keranjang</span>
            <?php if($jmlKeranjang > 0): ?><span class="bnav-badge kuning"><?php echo e($jmlKeranjang); ?></span><?php endif; ?>
        </a>

        <a href="<?php echo e(session('kasir_id') ? route('petugas.topup') : route('kopma.point.index')); ?>"
           class="bnav-item <?php echo e(request()->routeIs('petugas.topup*', 'kopma.point.*', 'topup.*') ? 'active' : ''); ?>">
            <i class="fa-solid fa-circle-arrow-up"></i><span>Top Up</span>
        </a>

        <a href="<?php echo e(route('kasir.index')); ?>"
           class="bnav-item <?php echo e(request()->routeIs('kasir.index') ? 'active' : ''); ?>">
            <i class="fa-solid fa-cash-register"></i><span>Kasir</span>
        </a>
    </div>
</nav>
<?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/partials/bottom-nav.blade.php ENDPATH**/ ?>