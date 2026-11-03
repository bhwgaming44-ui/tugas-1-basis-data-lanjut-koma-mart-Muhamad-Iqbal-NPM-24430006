

<?php $__env->startSection('content'); ?>
<style>
    .ord-hero{background:linear-gradient(135deg,#c8102e,#ef4444);border-radius:18px;padding:22px 26px;color:#fff}
    .ord-card{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:18px;margin-bottom:14px}
    .pill{display:inline-block;border-radius:20px;padding:4px 12px;font-size:11px;font-weight:800}
    .track{display:flex;align-items:center;margin-top:14px}
    .track .st{flex:1;text-align:center;font-size:10px;color:#9ca3af;position:relative}
    .track .st::before{content:"";display:block;width:16px;height:16px;border-radius:50%;background:#e5e7eb;margin:0 auto 5px;position:relative;z-index:2}
    .track .st::after{content:"";position:absolute;top:7px;left:-50%;width:100%;height:3px;background:#e5e7eb;z-index:1}
    .track .st:first-child::after{display:none}
    .track .st.done{color:#c8102e;font-weight:700}
    .track .st.done::before{background:#c8102e}
    .track .st.done::after{background:#c8102e}
    /* ===== SIDEBAR GOOGLE MAPS (pengantaran ke rumah) ===== */
    .map-side{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:18px;position:sticky;top:90px}
    .map-frame{width:100%;height:280px;border:0;border-radius:12px;display:block;background:#e5e7eb}
    .map-pick{width:100%;border:1px solid #d1d5db;border-radius:10px;padding:8px 10px;font-size:13px;font-weight:600;color:#374151;background:#fff}
    .map-pick:focus{outline:2px solid rgba(200,16,46,.3);border-color:#c8102e}
</style>

<div class="ord-hero mb-4">
    <h4 class="fw-bold mb-1"><i class="fa-solid fa-box me-2"></i>Pesanan Saya</h4>
    <small style="opacity:.9">Lacak pesanan & status pengantaran ke rumah secara realtime</small>
</div>

<?php if($pesanan->isEmpty()): ?>
    <div class="text-center py-5 bg-white rounded-4 border">
        <div style="font-size:54px">📦</div>
        <h5 class="fw-bold mt-2">Belum ada pesanan</h5>
        <p class="text-muted small">Belanja dulu di toko, deh!</p>
        <a href="<?php echo e(route('toko.index')); ?>" class="btn btn-danger fw-bold px-4">Mulai Belanja</a>
    </div>
<?php else: ?>
    <?php
        // Pesanan "antar ke rumah" yang punya titik tujuan -> sumber marker peta sidebar.
        // `titik_antar` berisi "lat,lng" hasil GPS bila ada; jika belum, jatuh ke alamat teks.
        $antarList = $pesanan->filter(fn ($p) => $p->metode_antar === 'antar' && filled($p->titik_antar));
        $mapAktif  = $antarList->first();
    ?>
    <div class="row g-4">
    <div class="col-lg-7">
    <?php $__currentLoopData = $pesanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $urut = ['menunggu', 'diproses', 'diantar', 'selesai'];
            if ($p->metode_antar === 'ambil') $urut = ['menunggu', 'diproses', 'selesai'];
            $idx = array_search($p->status_pesanan, $urut);
            $label = ['menunggu' => 'Menunggu Pembayaran', 'diproses' => 'Diproses', 'diantar' => 'Diantar Kurir', 'selesai' => 'Sampai di Rumah'];
            $warnaP = ['menunggu' => '#fef3c7;color:#b45309', 'diproses' => '#dbeafe;color:#1d4ed8', 'diantar' => '#e0e7ff;color:#4338ca', 'selesai' => '#fee2e2;color:#c8102e'];
        ?>
        <div class="ord-card">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <strong class="small"><?php echo e($p->no_struk); ?></strong>
                    <div class="text-muted" style="font-size:12px"><?php echo e($p->tanggal_transaksi); ?> &bull; <?php echo e($p->metode_antar === 'antar' ? '🚚 Antar ke Rumah' : '🏪 Ambil di Toko'); ?> &bull; <span class="text-uppercase"><?php echo e($p->metode_bayar); ?></span></div>
                </div>
                <div class="text-end">
                    <span class="pill" style="background:<?php echo e($warnaP[$p->status_pesanan]); ?>"><?php echo e(strtoupper($label[$p->status_pesanan])); ?></span>
                    <span class="pill" style="background:<?php echo e($p->status_bayar === 'lunas' ? '#fee2e2;color:#c8102e' : '#fef3c7;color:#b45309'); ?>"><?php echo e($p->status_bayar === 'lunas' ? 'LUNAS' : 'BELUM BAYAR'); ?></span>
                </div>
            </div>

            <div class="small text-muted mt-2">
                <?php $__currentLoopData = $p->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($d->barang->nama_barang ?? '-'); ?> x<?php echo e($d->jumlah); ?><?php echo e(!$loop->last ? ',' : ''); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if($p->alamat_antar): ?>
                <div class="small mt-1"><i class="fa-solid fa-location-dot text-danger"></i> <?php echo e($p->alamat_antar); ?></div>
            <?php endif; ?>
            <?php if($p->catatan): ?>
                <div class="small mt-1 fst-italic">"<?php echo e($p->catatan); ?>"</div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mt-2">
                <strong class="text-danger">Rp <?php echo e(number_format($p->total_netto, 0, ',', '.')); ?></strong>
                <div class="d-flex gap-2">
                    <?php if($p->status_bayar === 'belum_bayar'): ?>
                        <a href="<?php echo e(route('belanja.bayar', $p->id_penjualan)); ?>" class="btn btn-warning btn-sm fw-bold">
                            <i class="fa-solid fa-credit-card me-1"></i> Bayar Sekarang
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('belanja.bayar', $p->id_penjualan)); ?>" class="btn btn-outline-danger btn-sm fw-bold">Detail</a>
                    <?php if($p->metode_antar === 'antar' && $p->sudahAdaKurir() && $p->status_pesanan !== 'selesai'): ?>
                        <a href="<?php echo e(route('chat.index')); ?>" class="btn btn-danger btn-sm fw-bold" title="Chat mitra kurir pesanan ini">
                            <i class="fa-solid fa-comments me-1"></i> Chat Kurir
                        </a>
                    <?php endif; ?>
                    <?php if(!in_array($p->status_pesanan, ['diantar', 'selesai'])): ?>
                        <form action="<?php echo e(route('pesanan.batalkan', $p->id_penjualan)); ?>" method="POST" class="m-0"
                              onsubmit="return confirm('Batalkan pesanan <?php echo e($p->no_struk); ?>? Stok produk akan dikembalikan.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold">
                                <i class="fa-solid fa-trash me-1"></i> Batalkan
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="track">
                <?php $__currentLoopData = $urut; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="st <?php echo e(array_search($u, $urut) <= $idx ? 'done' : ''); ?>"><?php echo e($label[$u]); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="col-lg-5">
        <div class="map-side">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-map-location-dot me-1 text-danger"></i> Lacak Pengantaran</h6>
            <small class="text-muted d-block mb-3">Peta alamat tujuan kurir KOPMA untuk pesanan antar ke rumah.</small>

            <?php if($antarList->isEmpty()): ?>
                <div class="text-center py-4 text-muted small rounded-3" style="background:#f8fafc">
                    <div style="font-size:38px">🗺️</div>
                    Belum ada pesanan <strong>antar ke rumah</strong>.<br>
                    Checkout dengan metode "Antar ke Rumah" agar alammu muncul di peta.
                    <div class="mt-2">
                        <a href="<?php echo e(route('keranjang.index')); ?>" class="btn btn-danger btn-sm fw-bold"><i class="fa-solid fa-cart-shopping me-1"></i> Belanja Sekarang</a>
                    </div>
                </div>
            <?php else: ?>
                <?php if($antarList->count() > 1): ?>
                    <select class="map-pick mb-3" id="mapPick" onchange="mapPilih(this.value)">
                        <?php $__currentLoopData = $antarList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id_penjualan); ?>"
                                    data-titik="<?php echo e($p->titik_antar); ?>"
                                    data-struk="<?php echo e($p->no_struk); ?>"
                                    data-status="<?php echo e($p->status_pesanan); ?>"
                                    <?php echo e($p->id_penjualan === $mapAktif->id_penjualan ? 'selected' : ''); ?>>
                                <?php echo e($p->no_struk); ?> — <?php echo e(\Illuminate\Support\Str::limit($p->alamat_antar ?: $p->titik_antar, 34)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>

                <iframe id="mapFrame" class="map-frame" loading="lazy" allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps?q=<?php echo e(rawurlencode($mapAktif->titik_antar)); ?>&z=17&output=embed"></iframe>

                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <small class="text-muted d-block" style="font-size:12px">Tujuan</small>
                            <strong class="small" id="mapAlamat"><?php echo e($mapAktif->alamat_antar ?: 'Titik GPS: ' . $mapAktif->titik_antar); ?></strong>
                            <?php if($mapAktif->sudahPunyaTitikAntar()): ?>
                                <div class="text-danger" style="font-size:11px">
                                    <i class="fa-solid fa-location-dot me-1"></i> Titik GPS: <?php echo e($mapAktif->titik_antar); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <span class="pill" id="mapStatus" style="background:<?php echo e(['menunggu' => '#fef3c7;color:#b45309', 'diproses' => '#dbeafe;color:#1d4ed8', 'diantar' => '#e0e7ff;color:#4338ca', 'selesai' => '#fee2e2;color:#c8102e'][$mapAktif->status_pesanan] ?? '#f3f4f6;color:#374151'); ?>">
                            <?php echo e(strtoupper(['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'diantar' => 'Diantar', 'selesai' => 'Selesai'][$mapAktif->status_pesanan] ?? $mapAktif->status_pesanan)); ?>

                        </span>
                    </div>
                    <a id="mapLink" class="btn btn-outline-danger btn-sm fw-bold w-100 mt-3"
                       href="https://www.google.com/maps/search/?api=1&query=<?php echo e(rawurlencode($mapAktif->titik_antar)); ?>"
                       target="_blank" rel="noopener">
                        <i class="fa-solid fa-diamond-turn-right me-1"></i> Buka Rute di Google Maps
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Ganti peta sidebar sesuai pesanan antar yang dipilih (jika ada >1)
    function mapPilih(idPenjualan) {
        const opt = document.querySelector('#mapPick option[value="' + idPenjualan + '"]');
        if (!opt) return;
        const alamat = opt.dataset.alamat;
        document.getElementById('mapFrame').src =
            'https://www.google.com/maps?q=' + encodeURIComponent(alamat) + '&output=embed';
        document.getElementById('mapAlamat').textContent = alamat;
        document.getElementById('mapLink').href =
            'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(alamat);
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/toko/pesanan.blade.php ENDPATH**/ ?>