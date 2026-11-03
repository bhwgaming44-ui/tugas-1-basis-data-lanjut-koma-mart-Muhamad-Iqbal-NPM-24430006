<?php
    $modeAktifNav = \App\Helpers\AkunHelper::modeAktif();
    $chatUserBaru = $chatBelumDibaca ?? 0;
?>
<?php if($modeAktifNav === 'kurir'): ?>
<li class="nav-item">
    <a class="nav-link-kopma" href="<?php echo e(route('kurir.index')); ?>">
        <i class="fa-solid fa-motorcycle"></i> Antar Pesanan
    </a>
</li>
<li class="nav-item">
    <a class="nav-link-kopma" href="<?php echo e(route('kurir.chat')); ?>">
        <i class="fa-solid fa-comments"></i> Chat Member
    </a>
</li>
<li class="nav-item d-flex align-items-center gap-2 ms-lg-2">
    <span class="badge bg-success fw-semibold px-2 py-2">
        <i class="fa-solid fa-motorcycle me-1"></i><?php echo e(session('mitra_nama') ?? session('pelanggan_nama')); ?>

    </span>
    <form action="<?php echo e(route('mitra.logout')); ?>" method="POST" class="m-0"><?php echo csrf_field(); ?>
        <button type="submit" class="btn-logout-sm" title="Logout Kurir">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </form>
</li>
<?php elseif($modeAktifNav === 'petugas'): ?>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma <?php echo e(request()->routeIs('petugas.promo.*') ? 'active' : ''); ?>"
       href="<?php echo e(route('petugas.promo.index')); ?>">
        <i class="fa-solid fa-bolt"></i> Promo
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma <?php echo e(request()->routeIs('petugas.produk.*') ? 'active' : ''); ?>"
       href="<?php echo e(route('petugas.produk.index')); ?>">
        <i class="fa-solid fa-boxes-stacked"></i> Kelola Produk
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma" href="<?php echo e(route('kasir.index')); ?>">
        <i class="fa-solid fa-cash-register"></i> Kasir (POS)
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma" href="<?php echo e(route('petugas.topup')); ?>">
        <i class="fa-solid fa-circle-arrow-up"></i> Top Up
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma" href="<?php echo e(route('laporan.index')); ?>">
        <i class="fa-solid fa-calendar-days"></i> Laporan
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma <?php echo e(request()->routeIs('admin.payment-setting') ? 'active' : ''); ?>"
       href="<?php echo e(route('admin.payment-setting')); ?>">
        <i class="fa-solid fa-credit-card"></i> Pengaturan Bayar
    </a>
</li>
<li class="nav-item d-flex align-items-center gap-2 ms-lg-2">
    <span class="badge-mode-petugas">
        <i class="fa-solid fa-user-tie me-1"></i><?php echo e(session('kasir_nama')); ?>

    </span>
    <form action="<?php echo e(route('kasir.logout')); ?>" method="POST" class="m-0"><?php echo csrf_field(); ?>
        <button type="submit" class="btn-logout-sm" title="Logout Petugas">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </form>
</li>
<?php else: ?>
<li class="nav-item">
    <a class="nav-link-kopma" href="<?php echo e(route('toko.index')); ?>">
        <i class="fa-solid fa-boxes-stacked"></i> Toko
    </a>
</li>
<li class="nav-item">
    <a class="nav-link-kopma" href="<?php echo e(route('keranjang.index')); ?>">
        <i class="fa-solid fa-cart-shopping"></i> Keranjang
    </a>
</li>
<li class="nav-item">
    <a class="nav-link-kopma" href="<?php echo e(route('pesanan.index')); ?>">
        <i class="fa-solid fa-box"></i> Pesanan
    </a>
</li>
<?php if(session('pelanggan_id')): ?>
<li class="nav-item d-flex align-items-center gap-2 ms-lg-2">
    <span class="badge-mode-petugas">
        <i class="fa-solid fa-user me-1"></i><?php echo e(session('pelanggan_nama')); ?>

    </span>
    <form action="<?php echo e(route('pelanggan.logout')); ?>" method="POST" class="m-0"><?php echo csrf_field(); ?>
        <button type="submit" class="btn-logout-sm" title="Logout User">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </form>
</li>
<?php else: ?>
<li class="nav-item ms-lg-2">
    <a class="btn btn-sm btn-danger fw-bold px-3 py-2" href="<?php echo e(route('masuk')); ?>#user">
        <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk
    </a>
</li>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/partials/nav-mode.blade.php ENDPATH**/ ?>