<?php $__env->startSection('content'); ?>
<!-- Header Manajemen Petugas -->
<div class="kopma-hero-plang mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-warning text-dark fw-bold px-3 py-1 mb-2">
                <i class="fa-solid fa-id-card-clip me-1"></i> STRUKTUR ORGANISASI & SUMBER DAYA MANUSIA
            </span>
            <h2 class="fw-bold mb-1">MANAJEMEN PETUGAS KOPMA MART</h2>
            <p class="text-white-50 mb-0">
                Kelola data petugas: tambah, ubah, dan hapus akun. Username &amp; password tersimpan aman di sistem.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="d-inline-flex flex-column gap-2">
                <div class="bg-white text-dark p-3 rounded-3 shadow text-start">
                    <div class="small text-muted fw-semibold text-uppercase">Total Tenaga Kerja:</div>
                    <div class="fs-4 fw-bold text-danger"><?php echo e($pegawais->count()); ?> Pegawai</div>
                </div>
                <button class="btn btn-light fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahPetugas">
                    <i class="fa-solid fa-user-plus me-1"></i> Tambah Petugas
                </button>
            </div>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa-solid fa-circle-check me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa-solid fa-triangle-exclamation me-1"></i>
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Grid 20 Pegawai Card -->
<div class="row g-3">
    <?php $__currentLoopData = $pegawais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-6 col-xl-3">
            <div class="card card-kopma h-100 p-3 border-top border-4 <?php echo e($p->role === 'admin' ? 'border-danger' : ($p->role === 'manajer' ? 'border-warning' : 'border-danger')); ?>">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; font-size: 20px; background: <?php echo e($p->role === 'admin' ? 'linear-gradient(135deg, #c8102e, #ef4444)' : ($p->role === 'manajer' ? 'linear-gradient(135deg, #d97706, #dc2626)' : 'linear-gradient(135deg, #059669, #10b981)')); ?>;">
                        <?php echo e(strtoupper(substr($p->nama_lengkap, 0, 2))); ?>

                    </div>
                    <div>
                        <div class="badge <?php echo e($p->role === 'admin' ? 'bg-danger-subtle text-danger' : ($p->role === 'manajer' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger')); ?> fw-semibold mb-1">
                            #<?php echo e($p->id_user); ?> &bull; <?php echo e(strtoupper($p->role)); ?>

                        </div>
                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 170px;" title="<?php echo e($p->nama_lengkap); ?>">
                            <?php echo e($p->nama_lengkap); ?>

                        </h6>
                    </div>
                </div>

                <div class="bg-light p-2 rounded-3 mb-3 flex-grow-1">
                    <small class="text-muted d-block" style="font-size: 11px;">Jabatan Resmi:</small>
                    <span class="fw-bold text-dark" style="font-size: 13px;">
                        <?php echo e($p->jabatan ?? 'Staf Operasional'); ?>

                    </span>
                </div>

                <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top">
                    <span><i class="fa-solid fa-user-circle me-1"></i> <code><?php echo e($p->username); ?></code></span>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary" title="Ubah"
                            data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo e($p->id_user); ?>">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <form action="<?php echo e(route('pegawai.destroy', $p->id_user)); ?>" method="POST" class="m-0"
                              onsubmit="return confirm('Hapus petugas <?php echo e($p->nama_lengkap); ?>? Tindakan ini tidak bisa dibatalkan.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT PETUGAS -->
        <div class="modal fade" id="modalEdit<?php echo e($p->id_user); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="<?php echo e(route('pegawai.update', $p->id_user)); ?>" method="POST" class="modal-content">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid fa-user-pen me-2"></i> Ubah Petugas #<?php echo e($p->id_user); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?php echo e($p->nama_lengkap); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo e($p->username); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" value="<?php echo e($p->jabatan); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" class="form-select">
                                <option value="kasir" <?php echo e($p->role === 'kasir' ? 'selected' : ''); ?>>Kasir</option>
                                <option value="manajer" <?php echo e($p->role === 'manajer' ? 'selected' : ''); ?>>Manajer</option>
                                <option value="mitra" <?php echo e($p->role === 'mitra' ? 'selected' : ''); ?>>Mitra / Kurir</option>
                                <option value="admin" <?php echo e($p->role === 'admin' ? 'selected' : ''); ?>>Admin</option>
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold">Password Baru <small class="text-muted">(kosongkan jika tidak diganti)</small></label>
                            <input type="password" name="password" class="form-control" minlength="4" autocomplete="new-password" placeholder="••••">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<!-- MODAL TAMBAH PETUGAS -->
<div class="modal fade" id="modalTambahPetugas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?php echo e(route('pegawai.store')); ?>" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i> Tambah Petugas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Contoh: budi_kasir" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Kasir Shift Pagi">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Role</label>
                    <select name="role" class="form-select">
                        <option value="kasir">Kasir</option>
                        <option value="manajer">Manajer</option>
                        <option value="mitra">Mitra / Kurir</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" minlength="4" autocomplete="new-password" placeholder="Minimal 4 karakter" required>
                    <small class="text-muted">Password disimpan ter-enkripsi (aman).</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold"><i class="fa-solid fa-user-plus me-1"></i> Tambah Petugas</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/pegawai/index.blade.php ENDPATH**/ ?>