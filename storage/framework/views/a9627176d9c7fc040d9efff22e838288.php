

<?php $__env->startSection('content'); ?>
<div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark"><i class="fa-solid fa-users text-primary me-2"></i> Data Member KOPMA MART</h3>
        <p class="text-secondary mb-0">Kelola informasi member/pelanggan terdaftar, poin loyalitas, dan status keaktifan.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <!-- Trigger Modal Tambah Member -->
        <button type="button" class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahAnggota">
            <i class="fa-solid fa-user-plus me-1"></i> Daftar Member Baru
        </button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Member</th>
                        <th>Nama Pelanggan</th>
                        <th>No HP</th>
                        <th>Status</th>
                        <th>Poin Loyalitas</th>
                        <th>Akumulasi Belanja</th>
                        <th>Tgl Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $anggotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><code><?php echo e($agt->kode_member); ?></code></td>
                            <td class="fw-semibold"><?php echo e($agt->nama); ?></td>
                            <td><?php echo e($agt->no_hp ?? '-'); ?></td>
                            <td>
                                <?php if($agt->status_aktif === 'aktif'): ?>
                                    <span class="badge bg-danger-subtle text-danger">Aktif</span>
                                <?php elseif($agt->status_aktif === 'cuti'): ?>
                                    <span class="badge bg-warning-subtle text-warning">Non-Fungsional</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-warning">
                                <i class="fa-solid fa-star"></i> <?php echo e(number_format($agt->poin, 0, ',', '.')); ?>

                            </td>
                            <td class="fw-bold text-primary">
                                Rp <?php echo e(number_format($agt->total_belanja, 0, ',', '.')); ?>

                            </td>
                            <td><small class="text-muted"><?php echo e(date('d M Y', strtotime($agt->tanggal_daftar))); ?></small></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data member.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($anggotas->hasPages()): ?>
        <div class="card-footer bg-white border-0 py-3">
            <?php echo e($anggotas->links()); ?>

        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Member -->
<div class="modal fade" id="modalTambahAnggota" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('anggota.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> Pendaftaran Member KOPMA MART</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Member:</label>
                        <input type="text" name="kode_member" class="form-control" placeholder="Contoh: MBR-001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap:</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama pelanggan..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat:</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Jalan, No RT/RW, Kelurahan...">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">No HP / WhatsApp:</label>
                            <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Email:</label>
                            <input type="email" name="email" class="form-control" placeholder="nama@email.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Daftar:</label>
                        <input type="date" name="tanggal_daftar" value="<?php echo e(date('Y-m-d')); ?>" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Simpan Data Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/anggota/index.blade.php ENDPATH**/ ?>