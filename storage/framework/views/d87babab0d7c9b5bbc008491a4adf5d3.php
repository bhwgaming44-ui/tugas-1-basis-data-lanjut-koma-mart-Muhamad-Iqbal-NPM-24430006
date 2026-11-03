<?php
    $modeAktifP = \App\Helpers\AkunHelper::modeAktif();
    $peranP = \App\Helpers\AkunHelper::peranLogin();
?>
<?php if(count($peranP) > 1): ?>
<ul class="navbar-nav me-auto align-items-lg-center gap-1 mt-3 mt-lg-0">
    <li class="nav-item d-flex align-items-center gap-1 flex-wrap">
        <span class="small fw-bold text-muted me-1">Mode:</span>
        <?php $__currentLoopData = $peranP; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kode => $nama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($kode === $modeAktifP): ?>
                <span class="badge bg-danger px-2 py-2"><?php echo e(ucfirst($kode)); ?>: <?php echo e($nama); ?></span>
            <?php else: ?>
                <a href="<?php echo e(route('mode.ganti', $kode)); ?>" class="btn btn-sm btn-outline-secondary fw-bold"><?php echo e(ucfirst($kode)); ?></a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </li>
</ul>
<?php endif; ?>
<?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/partials/pindah-peran.blade.php ENDPATH**/ ?>