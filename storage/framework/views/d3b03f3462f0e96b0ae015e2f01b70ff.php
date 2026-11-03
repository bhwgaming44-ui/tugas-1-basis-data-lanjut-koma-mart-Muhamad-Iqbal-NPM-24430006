

<?php $__env->startSection('content'); ?>
<style>
    .cart-head{background:linear-gradient(135deg,#c8102e,#ef4444);border-radius:18px;padding:22px 26px;color:#fff}
    .cart-item{display:flex;gap:14px;align-items:center;background:#fff;border:1px solid #eef1f5;border-radius:14px;padding:12px 14px;margin-bottom:10px}
    .cart-thumb{width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;flex:0 0 auto}
    .qty-input{width:64px;text-align:center;border-radius:8px;border:1px solid #d1d5db;padding:4px}
    .checkout-box{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:20px;position:sticky;top:90px}
    .metode-opt input:checked+label{border-color:#c8102e;background:#fef2f2;box-shadow:0 0 0 2px rgba(200,16,46,.2)}
</style>

<div class="cart-head mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-cart-shopping me-2"></i>Keranjang Belanja</h4>
        <small style="opacity:.9">Belanja langsung dari website &bull; Tanpa WhatsApp</small>
    </div>
    <a href="<?php echo e(route('toko.index')); ?>" class="btn btn-light btn-sm fw-bold"><i class="fa-solid fa-plus me-1"></i> Tambah Produk</a>
</div>

<?php if(empty($barangs)): ?>
    <div class="text-center py-5 bg-white rounded-4 border">
        <div style="font-size:54px">🛒</div>
        <h5 class="fw-bold mt-2">Keranjang masih kosong</h5>
        <p class="text-muted small">Yuk pilih produk favoritmu di toko!</p>
        <a href="<?php echo e(route('toko.index')); ?>" class="btn btn-danger fw-bold px-4">Mulai Belanja</a>
    </div>
<?php else: ?>
<div class="row g-4">
    <div class="col-lg-7">
        <form action="<?php echo e(route('keranjang.update')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php $__currentLoopData = $barangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $warna = ['#ef4444','#dc2626','#7f1d1d','#dc2626','#991b1b','#dc2626'][min(5, max(0, (int)(($b->id_kategori ?? 1) - 1)))];
                ?>
                <div class="cart-item">
                    <div class="cart-thumb" style="background:linear-gradient(135deg,<?php echo e($warna); ?>,<?php echo e($warna); ?>aa)">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold small"><?php echo e($b->nama_barang); ?></div>
                        <small class="text-muted">#<?php echo e($b->kode_barcode); ?> &bull; Stok <?php echo e($b->stok); ?></small>
                        <div class="fw-bold text-danger">Rp <?php echo e(number_format($b->harga_jual_anggota, 0, ',', '.')); ?></div>
                    </div>
                    <div class="text-end">
                        <input type="number" name="qty[<?php echo e($b->id_barang); ?>]" value="<?php echo e($cart[$b->id_barang]); ?>" min="0" max="99" class="qty-input form-control-sm">
                        <button type="submit" form="hapus-<?php echo e($b->id_barang); ?>" class="btn btn-link text-danger btn-sm p-0 mt-1" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <button type="submit" class="btn btn-outline-danger fw-bold btn-sm"><i class="fa-solid fa-rotate me-1"></i> Perbarui Jumlah</button>
        </form>
        <?php $__currentLoopData = $barangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <form id="hapus-<?php echo e($b->id_barang); ?>" action="<?php echo e(route('keranjang.hapus')); ?>" method="POST" class="d-none">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id_barang" value="<?php echo e($b->id_barang); ?>">
            </form>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="col-lg-5">
        <div class="checkout-box">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-file-invoice me-1"></i> Checkout</h6>
            <form action="<?php echo e(route('belanja.checkout')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Pengantaran</label>
                    <div class="d-flex gap-2 metode-opt">
                        <input type="radio" name="metode_antar" id="antar" value="antar" class="d-none" checked onchange="toggleAlamat(true)">
                        <label for="antar" class="border rounded-3 p-2 text-center small flex-fill" style="cursor:pointer">
                            <i class="fa-solid fa-motorcycle d-block fs-5 text-danger mb-1"></i> Antar ke Rumah
                        </label>
                        <input type="radio" name="metode_antar" id="ambil" value="ambil" class="d-none" onchange="toggleAlamat(false)">
                        <label for="ambil" class="border rounded-3 p-2 text-center small flex-fill" style="cursor:pointer">
                            <i class="fa-solid fa-store d-block fs-5 text-danger mb-1"></i> Ambil di Toko
                        </label>
                    </div>
                </div>

                <div class="mb-3" id="boxAlamat">
                    <label class="form-label fw-bold small">Patokan / Alamat (opsional)</label>
<textarea name="alamat" class="form-control" rows="2"
          placeholder="cth: Jl. Merdeka No. 10, Kost RT 03, pagar hijau"><?php echo e(old('alamat', $anggota->alamat ?? '')); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Metode Pembayaran</label>
                    <select name="metode_bayar" class="form-select" required>
                        <option value="transfer">Transfer Bank (BCA/BRI/Mandiri)</option>
                        <option value="qris">QRIS (Semua E-Wallet)</option>
                        <option value="tunai">Tunai (Bayar di Tempat)</option>
                        <?php $mtEnabled = \App\Models\PaymentSetting::getMidtransConfig()['is_enabled'] ?? false; ?>
                        <?php if($mtEnabled): ?>
                            <option value="midtrans">Bayar Online (Midtrans: QRIS, VA, E-Wallet, Kartu)</option>
                        <?php endif; ?>
                        <?php if(session('pelanggan_id')): ?>
                            <option value="kopma_point" <?php echo e((float) ($anggota->saldo_kopma ?? 0) >= $total ? '' : 'disabled'); ?>>
                                Kopma Point (Saldo: Rp <?php echo e(number_format($anggota->saldo_kopma ?? 0, 0, ',', '.')); ?>)<?php echo e((float) ($anggota->saldo_kopma ?? 0) < $total ? ' — Saldo kurang' : ''); ?>

                            </option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Catatan (opsional)</label>
                    <input type="text" name="catatan" class="form-control" placeholder="cth: tolong bungkus rapat">
                </div>

                <hr>
                <div class="d-flex justify-content-between fw-bold mb-1">
                    <span>Total Bayar</span>
                    <span class="text-danger fs-5">Rp <?php echo e(number_format($total, 0, ',', '.')); ?></span>
                </div>
                <small class="text-muted d-block mb-3">Harga member sudah dipakai. Poin: +<?php echo e((int) floor($total / 1000)); ?></small>

                <?php if(!session('pelanggan_id')): ?>
                    <a href="<?php echo e(route('pelanggan.login')); ?>" class="btn btn-danger w-100 fw-bold py-2">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Login / Daftar dulu untuk Checkout
                    </a>
                <?php else: ?>
                    <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                        <i class="fa-solid fa-lock me-1"></i> Bayar Sekarang
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>

<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function toggleAlamat(wajib) {
        const box = document.getElementById('boxAlamat');
        box.style.display = wajib ? '' : 'none';
    }
    toggleAlamat(true);
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/toko/keranjang.blade.php ENDPATH**/ ?>