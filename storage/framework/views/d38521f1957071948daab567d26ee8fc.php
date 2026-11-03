

<?php $__env->startSection('content'); ?>
<div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Riwayat &amp; Cetak Struk</h3>
        <p class="text-secondary mb-0">Pilih transaksi untuk mencetak ulang struk langsung ke printer thermal (ESC/POS).</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="<?php echo e(route('kasir.index')); ?>" class="btn btn-danger fw-semibold"><i class="fa-solid fa-cash-register me-1"></i> Kembali ke Kasir</a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No Struk</th>
                        <th>Tanggal</th>
                        <th>Member</th>
                        <th>Kasir</th>
                        <th class="text-end">Total Netto</th>
                        <th>Metode</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $penjualans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><code class="fw-bold"><?php echo e($trx->no_struk); ?></code></td>
                            <td><small><?php echo e(\Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y H:i')); ?></small></td>
                            <td><?php echo e($trx->anggota ? $trx->anggota->nama : 'Pelanggan Umum'); ?></td>
                            <td><small><?php echo e($trx->kasir->nama_lengkap ?? '-'); ?></small></td>
                            <td class="text-end fw-bold text-danger">Rp <?php echo e(number_format($trx->total_netto, 0, ',', '.')); ?></td>
                            <td><span class="badge bg-secondary text-uppercase"><?php echo e($trx->metode_bayar); ?></span></td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?php echo e(route('kasir.struk', $trx->id_penjualan)); ?>" class="btn btn-sm btn-outline-danger fw-semibold" title="Cetak Struk">
                                        <i class="fa-solid fa-print me-1"></i> Cetak
                                    </a>
                                    <form action="<?php echo e(route('kasir.riwayat.hapus', $trx->id_penjualan)); ?>" method="POST" class="m-0"
                                          onsubmit="return confirm('Hapus struk <?php echo e($trx->no_struk); ?>? Tindakan ini tidak bisa dibatalkan.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary fw-semibold" title="Hapus Struk">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($penjualans->hasPages()): ?>
        <div class="card-footer bg-white border-0 py-3">
            <?php echo e($penjualans->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/kasir/riwayat.blade.php ENDPATH**/ ?>