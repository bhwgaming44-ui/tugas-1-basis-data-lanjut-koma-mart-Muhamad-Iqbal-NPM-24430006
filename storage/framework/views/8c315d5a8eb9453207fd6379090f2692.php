

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-3 px-lg-4 py-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold text-danger mb-0"><i class="fa-solid fa-bolt me-2"></i>Kelola Promo</h4>
            <small class="text-muted">Tambah, ubah, dan hapus slide promo yang tampil di halaman toko.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('toko.index')); ?>" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Toko
            </a>
            <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahPromo">
                <i class="fa-solid fa-plus me-1"></i> Tambah Promo
            </button>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
        <div class="input-group input-group-sm" style="max-width:400px;">
            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="text" id="cariPromo" class="form-control" placeholder="Cari judul promo, badge, atau produk terkait..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="bersihkanCariPromo" title="Bersihkan pencarian"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <small class="text-muted" id="infoCariPromo">Menampilkan <?php echo e($promo->count()); ?> promo</small>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">Urutan</th>
                        <th>Promo</th>
                        <th>Produk Terkait</th>
                        <th style="width:110px;">Status</th>
                        <th style="width:170px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $promo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="baris-promo">
                        <td class="fw-bold text-center"><?php echo e($p->urutan); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if($p->gambar): ?>
                                <img src="<?php echo e(asset($p->gambar)); ?>" alt="promo" style="width:64px;height:40px;object-fit:cover;border-radius:8px;border:1px solid #eef1f4;">
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold small"><?php echo e($p->judul); ?></div>
                                    <?php if($p->badge): ?><span class="badge bg-danger-subtle text-danger" style="font-size:10px;"><?php echo e($p->badge); ?></span><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="small"><?php echo e($p->barang->nama_barang ?? '-'); ?></td>
                        <td>
                            <?php if($p->aktif): ?>
                            <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalEditPromo<?php echo e($p->id); ?>">
                                <i class="fa-solid fa-pen"></i> Ubah
                            </button>
                            <form action="<?php echo e(route('petugas.promo.destroy', $p->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus promo ini?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada promo. Klik "Tambah Promo" untuk membuat slide baru.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Tambah Promo -->
<div class="modal fade" id="modalTambahPromo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?php echo e(route('petugas.promo.store')); ?>" method="POST" enctype="multipart/form-data" class="modal-content">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Promo Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label fw-bold small">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" required maxlength="150">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">Sub Judul</label>
                    <input type="text" name="sub" class="form-control">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Badge</label>
                        <input type="text" name="badge" class="form-control" maxlength="60">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Teks Tombol</label>
                        <input type="text" name="cta" class="form-control" maxlength="60">
                    </div>
                </div>
                <div class="mb-2 mt-2">
                    <label class="form-label fw-bold small">Link Tujuan</label>
                    <input type="url" name="url" class="form-control" maxlength="300">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">Gambar Promo</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">Kaitkan ke Produk</label>
                    <select name="id_barang" class="form-select">
                        <option value="">-- Tanpa produk --</option>
                        <?php $__currentLoopData = $barangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($b->id_barang); ?>"><?php echo e($b->nama_barang); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Urutan</label>
                        <input type="number" name="urutan" class="form-control" min="0" value="0">
                    </div>
                    <div class="col-6 d-flex align-items-end pb-2">
                        <div class="form-check">
                            <input type="checkbox" name="aktif" value="1" class="form-check-input" id="promoAktifBaru" checked>
                            <label class="form-check-label small fw-bold" for="promoAktifBaru">Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold">Simpan Promo</button>
            </div>
        </form>
    </div>
</div>
<?php $__currentLoopData = $promo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<!-- Modal Ubah Promo -->
<div class="modal fade" id="modalEditPromo<?php echo e($p->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?php echo e(route('petugas.promo.update', $p->id)); ?>" method="POST" enctype="multipart/form-data" class="modal-content">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ubah Promo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label fw-bold small">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" required maxlength="150" value="<?php echo e($p->judul); ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">Sub Judul</label>
                    <input type="text" name="sub" class="form-control" value="<?php echo e($p->sub); ?>">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Badge</label>
                        <input type="text" name="badge" class="form-control" maxlength="60" value="<?php echo e($p->badge); ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Teks Tombol</label>
                        <input type="text" name="cta" class="form-control" maxlength="60" value="<?php echo e($p->cta); ?>">
                    </div>
                </div>
                <div class="mb-2 mt-2">
                    <label class="form-label fw-bold small">Link Tujuan</label>
                    <input type="url" name="url" class="form-control" maxlength="300" value="<?php echo e($p->url); ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">Ganti Gambar</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Urutan</label>
                        <input type="number" name="urutan" class="form-control" min="0" value="<?php echo e($p->urutan); ?>">
                    </div>
                    <div class="col-6 d-flex align-items-end pb-2">
                        <div class="form-check">
                            <input type="checkbox" name="aktif" value="1" class="form-check-input" id="promoAktif<?php echo e($p->id); ?>" <?php echo e($p->aktif ? 'checked' : ''); ?>>
                            <label class="form-check-label small fw-bold" for="promoAktif<?php echo e($p->id); ?>">Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script>
(function () {
    // ===== Pencarian realtime (tanpa tekan Enter) =====
    var inputCari = document.getElementById('cariPromo');
    var infoCari = document.getElementById('infoCariPromo');
    var tombolBersih = document.getElementById('bersihkanCariPromo');
    if (!inputCari) return;
    var barisPromo = document.querySelectorAll('.baris-promo');
    var totalAwal = barisPromo.length;
    var saringPromo = function () {
        var kunci = inputCari.value.trim().toLowerCase();
        var jumlah = 0;
        barisPromo.forEach(function (tr) {
            var cocok = kunci === '' || tr.textContent.toLowerCase().indexOf(kunci) !== -1;
            tr.classList.toggle('d-none', !cocok);
            if (cocok) jumlah++;
        });
        if (kunci === '') {
            infoCari.textContent = 'Menampilkan ' + totalAwal + ' promo';
            infoCari.classList.remove('text-danger', 'fw-bold');
        } else if (jumlah === 0) {
            infoCari.textContent = 'Tidak ada promo yang cocok dengan "' + inputCari.value.trim() + '"';
            infoCari.classList.add('text-danger', 'fw-bold');
        } else {
            infoCari.textContent = jumlah + ' dari ' + totalAwal + ' promo cocok dengan "' + inputCari.value.trim() + '"';
            infoCari.classList.remove('text-danger');
        }
    };
    inputCari.addEventListener('input', saringPromo);
    if (tombolBersih) tombolBersih.addEventListener('click', function () {
        inputCari.value = '';
        saringPromo();
        inputCari.focus();
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/petugas/promo/index.blade.php ENDPATH**/ ?>