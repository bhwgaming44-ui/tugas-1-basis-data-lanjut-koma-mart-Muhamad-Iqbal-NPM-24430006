

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">

    <!-- ================= FLASH MESSAGE (login / tambah produk) ================= -->
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i>
        <ul class="mb-0 ps-3">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($err); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- ================= HEADER ================= -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-cash-register me-2 text-danger"></i>Kasir</h4>
            <small class="text-muted">Pilih produk di kiri, selesaikan pembayaran di kanan.</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAturUrutan">
                <i class="fa-solid fa-arrow-down-wide-short me-1"></i> Atur Urutan
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                <i class="fa-solid fa-plus me-1"></i> Tambah Produk
            </button>
            <a href="<?php echo e(route('toko.index')); ?>" class="btn btn-outline-primary btn-sm text-decoration-none">
                <i class="fa-solid fa-box me-1"></i> Restock
            </a>
            <a href="<?php echo e(route('kasir.riwayat')); ?>" class="btn btn-outline-warning btn-sm text-decoration-none">
                <i class="fa-solid fa-print me-1"></i> Cetak Struk
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- ================= KIRI: KATALOG PRODUK (sederhana, tanpa gambar) ================= -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-sm-5">
                            <h6 class="fw-bold mb-0"><i class="fa-solid fa-boxes-stacked me-2 text-danger"></i>Katalog Produk</h6>
                        </div>
                        <div class="col-sm-7">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                                <input type="text" id="cariProduk" class="form-control" autocomplete="off"
                                       placeholder="Cari nama produk...">
                            </div>
                            <div id="infoCari" class="small text-muted mt-1 d-none"></div>
                        </div>
                    </div>
                    
                    <ul class="nav nav-pills mt-3" id="tabStok" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="tab-ready-btn" data-bs-toggle="pill"
                                    data-bs-target="#panelReady" type="button" role="tab" aria-controls="panelReady" aria-selected="true">
                                <i class="fa-solid fa-circle-check me-1 text-success"></i> Stok Ready
                                <span class="badge bg-success ms-1"><?php echo e(count($barangs)); ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="tab-kosong-btn" data-bs-toggle="pill"
                                    data-bs-target="#panelKosong" type="button" role="tab" aria-controls="panelKosong" aria-selected="false">
                                <i class="fa-solid fa-box-open me-1 text-danger"></i> Stok Kosong
                                <span class="badge <?php echo e(count($barangsKosong) > 0 ? 'bg-danger' : 'bg-secondary'); ?> ms-1"><?php echo e(count($barangsKosong)); ?></span>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" id="tabStokContent">
                <div class="tab-pane fade show active card-body p-0" id="panelReady" role="tabpanel" aria-labelledby="tab-ready-btn" tabindex="0">
                <div id="gridProduk" style="max-height: 72vh; overflow-y: auto;">
                    <?php $__empty_1 = true; $__currentLoopData = $barangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $adaDiskon = $b->harga_jual_umum > $b->harga_jual_anggota && $b->harga_jual_anggota > 0;
                    ?>
                    <div class="product-item d-flex align-items-center justify-content-between gap-3 px-3 py-2 border-bottom"
                         data-nama="<?php echo e($b->nama_barang); ?>"
                         data-kode="<?php echo e($b->kode_barcode); ?>"
                         data-kat="<?php echo e($b->kategori->nama_kategori ?? ''); ?>">
                        <div class="flex-grow-1">
                            <div class="fw-bold small mb-0"><?php echo e($b->nama_barang); ?></div>
                            <small class="text-muted"><i class="fa-solid fa-barcode me-1"></i><span class="font-monospace"><?php echo e($b->kode_barcode); ?></span> &middot; <?php echo e($b->kategori->nama_kategori ?? '-'); ?></small>
                            <div class="mt-1">
                                <span class="fw-bold text-danger">Rp <?php echo e(number_format($b->harga_jual_umum, 0, ',', '.')); ?></span>
                                <?php if($adaDiskon): ?>
                                <small class="text-success ms-1"><i class="fa-solid fa-tag me-1"></i>Member Rp <?php echo e(number_format($b->harga_jual_anggota, 0, ',', '.')); ?></small>
                                <?php endif; ?>
                                <span class="badge <?php echo e($b->stok <= $b->stok_minimum ? 'bg-warning text-dark' : 'bg-light text-muted border'); ?> ms-1"
                                      id="stok-<?php echo e($b->id_barang); ?>" data-stok="<?php echo e($b->stok); ?>" data-min="<?php echo e($b->stok_minimum); ?>"><?php echo e($b->stok <= $b->stok_minimum ? 'Sisa' : 'Stok'); ?> <?php echo e($b->stok); ?></span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger add-to-cart flex-shrink-0"
                            data-id="<?php echo e($b->id_barang); ?>"
                            data-nama="<?php echo e($b->nama_barang); ?>"
                            data-harga-umum="<?php echo e($b->harga_jual_umum); ?>"
                            data-harga-anggota="<?php echo e($b->harga_jual_anggota); ?>"
                            data-stok="<?php echo e($b->stok); ?>">
                            <i class="fa-solid fa-cart-plus me-1"></i>Tambah
                        </button>
                        <div class="input-group input-group-sm flex-shrink-0" style="width: 116px;">
                            <input type="number" id="restok-<?php echo e($b->id_barang); ?>" class="form-control form-control-sm"
                                   value="1" min="1" step="1" aria-label="Jumlah restock <?php echo e($b->nama_barang); ?>">
                            <button type="button" class="btn btn-outline-success btn-restock"
                                    data-id="<?php echo e($b->id_barang); ?>" title="Restock / tambah stok produk ini">
                                <i class="fa-solid fa-box-archive"></i>
                            </button>
                        </div>
                        
                        <form action="<?php echo e(route('kasir.barang.hapus', $b->id_barang)); ?>" method="POST" class="m-0 flex-shrink-0"
                              onsubmit="return confirm('Hapus produk \"<?php echo e($b->nama_barang); ?>\" permanen dari katalog toko & kasir?\nRiwayat penjualan produk ini ikut terhapus.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus produk dari katalog">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fa-solid fa-box-open fa-3x opacity-25 mb-3"></i>
                        <div class="fw-bold">Tidak ada produk tersedia</div>
                        <small>Stok produk sedang habis atau kata kunci tidak ditemukan.</small>
                    </div>
                    <?php endif; ?>
                    <div id="kosongCari" class="text-center text-muted py-5 d-none">
                        <i class="fa-solid fa-magnifying-glass-minus fa-3x opacity-25 mb-3"></i>
                        <div class="fw-bold" id="kosongCariJudul">Produk tidak ditemukan</div>
                        <small id="kosongCariHint">Coba kata yang lebih pendek atau periksa ejaan nama produk.</small>
                    </div>
                </div>
                </div><!-- /.panelReady -->

                
                <div class="tab-pane fade card-body p-0" id="panelKosong" role="tabpanel" aria-labelledby="tab-kosong-btn" tabindex="0">
                    <?php if(count($barangsKosong) > 0): ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light-subtle">
                        <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i><?php echo e(count($barangsKosong)); ?> produk habis — restock atau hapus dari katalog.</small>
                        <form action="<?php echo e(route('kasir.barang.hapus-kosong')); ?>" method="POST" class="m-0"
                              onsubmit="return confirm('Hapus SEMUA produk berstok habis dari katalog?\nRiwayat transaksi produk tersebut ikut terhapus.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-warning text-dark">
                                <i class="fa-solid fa-broom me-1"></i> Hapus Otomatis Semua
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                    <div id="gridKosong" style="max-height: 72vh; overflow-y: auto;">
                        <?php $__empty_1 = true; $__currentLoopData = $barangsKosong; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="product-item-kosong d-flex align-items-center justify-content-between gap-3 px-3 py-2 border-bottom">
                            <div class="flex-grow-1">
                                <div class="fw-bold small mb-0"><?php echo e($bk->nama_barang); ?></div>
                                <small class="text-muted"><i class="fa-solid fa-barcode me-1"></i><span class="font-monospace"><?php echo e($bk->kode_barcode); ?></span> &middot; <?php echo e($bk->kategori->nama_kategori ?? '-'); ?></small>
                                <div class="mt-1">
                                    <span class="fw-bold text-danger">Rp <?php echo e(number_format($bk->harga_jual_umum, 0, ',', '.')); ?></span>
                                    <span class="badge bg-danger ms-1" id="stok-<?php echo e($bk->id_barang); ?>" data-stok="<?php echo e($bk->stok); ?>" data-min="<?php echo e($bk->stok_minimum); ?>">HABIS <?php echo e($bk->stok); ?></span>
                                </div>
                            </div>
                            <div class="input-group input-group-sm flex-shrink-0" style="width: 116px;">
                                <input type="number" id="restok-<?php echo e($bk->id_barang); ?>" class="form-control form-control-sm"
                                       value="1" min="1" step="1" aria-label="Jumlah restock <?php echo e($bk->nama_barang); ?>">
                                <button type="button" class="btn btn-outline-success btn-restock"
                                        data-id="<?php echo e($bk->id_barang); ?>" title="Restock / tambah stok produk ini">
                                    <i class="fa-solid fa-box-archive"></i>
                                </button>
                            </div>
                            <form action="<?php echo e(route('kasir.barang.hapus', $bk->id_barang)); ?>" method="POST" class="m-0 flex-shrink-0"
                                  onsubmit="return confirm('Hapus produk \"<?php echo e($bk->nama_barang); ?>\" permanen dari katalog?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus produk dari katalog">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center text-muted py-5">
                            <i class="fa-solid fa-circle-check fa-3x text-success opacity-75 mb-3"></i>
                            <div class="fw-bold">Tidak ada produk kosong</div>
                            <small>Semua produk di katalog masih punya stok.</small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div><!-- /.panelKosong -->
                </div><!-- /.tab-content -->
            </div>
        </div>
        <!-- ================= KANAN: KERANJANG & PEMBAYARAN ================= -->
        <div class="col-lg-4">
            <div class="position-sticky" style="top: 1rem;">
                <!-- Keranjang -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-danger text-white py-3 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0"><i class="fa-solid fa-cart-shopping me-2"></i>Keranjang</h6>
                        <span class="badge bg-light text-danger" id="cartBadge">0</span>
                    </div>
                    <div class="card-body p-0" id="daftarKeranjang" style="max-height: 34vh; overflow-y: auto;">
                        <div class="text-center text-muted py-4">
                            <i class="fa-solid fa-cart-arrow-down fa-2x opacity-25 mb-2"></i>
                            <div class="small">Belum ada produk dalam keranjang</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between py-3">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold text-danger fs-5" id="totalKeranjang">Rp 0</span>
                    </div>
                </div>

                <!-- Pembayaran -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0"><i class="fa-solid fa-wallet me-2 text-primary"></i>Pembayaran</h6>
                    </div>
                    <div class="card-body p-3">
                        <label class="form-label fw-bold small mb-1">Pelanggan / Member</label>
                        
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-muted"></i></span>
                            <input type="text" id="cariNoHpAnggota" class="form-control" autocomplete="off"
                                   placeholder="Cari member: ketik No. HP...">
                        </div>
                        <div id="infoCariAnggota" class="small mb-2 d-none"></div>
                        <select id="idAnggota" class="form-select form-select-sm mb-3">
                            <option value="">Pembeli Umum</option>
                            <?php $__currentLoopData = $anggotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($a->id_anggota); ?>"
                                    data-nama="<?php echo e(e($a->nama)); ?>"
                                    data-nohp="<?php echo e(e($a->no_hp ?? '')); ?>">
                                <?php echo e(e($a->kode_member)); ?> - <?php echo e(e($a->nama)); ?><?php echo e($a->no_hp ? ' (' . e($a->no_hp) . ')' : ''); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Total Belanja</label>
                                <input type="text" id="totalBelanja" class="form-control form-control-sm" readonly value="Rp 0">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Bayar</label>
                                <input type="number" id="inputBayar" class="form-control form-control-sm" placeholder="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Kembalian</label>
                                <input type="text" id="kembalian" class="form-control form-control-sm" readonly value="Rp 0">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Metode</label>
                                <select id="metodeBayar" class="form-select form-select-sm">
                                    <option value="tunai">Tunai</option>
                                    <option value="qris">QRIS</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-text mt-2">
                            <i class="fa-solid fa-bolt text-danger me-1"></i>Bayar &amp; kembalian terhitung otomatis — isi nominal hanya jika pelanggan membayar uang lebih.
                        </div>

                        <button type="button" class="btn btn-danger w-100 fw-bold mt-3 opacity-50" id="btnCheckout" disabled>
                            <i class="fa-solid fa-check-double me-1"></i> Bayar &amp; Cetak Struk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- ================= MODAL TAMBAH PRODUK ================= -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2"></i>Tambah Produk Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambahProduk" action="<?php echo e(route('toko.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" class="form-control" required placeholder="Contoh: Beras Merah Putih 5kg">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kode Barcode</label>
                            <div class="input-group">
                                <input type="text" name="kode_barcode" class="form-control" placeholder="Biarkan kosong untuk auto-generate">
                                <button type="button" class="btn btn-outline-secondary" id="btnAutoBarcode">
                                    <i class="fa-solid fa-magic me-1"></i> Auto
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">Tanpa Kategori</option>
                                <?php
                                    $kategorisModal = App\Models\KategoriBarang::withCount('barangs')->get();
                                ?>
                                <?php $__currentLoopData = $kategorisModal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($kat->id_kategori); ?>"><?php echo e(e($kat->nama_kategori)); ?> (<?php echo e($kat->barangs_count); ?> produk)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Harga Beli <span class="text-danger">*</span></label>
                            <input type="number" name="harga_beli" class="form-control" required placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Harga Jual Umum <span class="text-danger">*</span></label>
                            <input type="number" name="harga_jual_umum" class="form-control" required placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Harga Jual Anggota <span class="text-danger">*</span></label>
                            <input type="number" name="harga_jual_anggota" class="form-control" required placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Stok</label>
                            <input type="number" name="stok" class="form-control" value="0" required min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-control" min="0" placeholder="Kosong = paling bawah">
                            <small class="text-muted" style="font-size: 11px;">Nomor kecil tampil lebih dulu — bisa diubah lewat tombol "Atur Urutan".</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Foto Produk (Dari Galeri Laptop)</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                            <small class="text-muted" style="font-size: 11px;">Pilih foto produk dari galeri laptop (JPG, PNG, WEBP, maks 5MB).</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="fa-solid fa-save me-1"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL ATUR URUTAN KATALOG (gaya kelola Promo) ================= -->
<div class="modal fade" id="modalAturUrutan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-arrow-down-wide-short me-2"></i>Atur Urutan Katalog Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAturUrutan" action="<?php echo e(route('kasir.atur-urutan')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Geser tombol <i class="fa-solid fa-arrow-up"></i> / <i class="fa-solid fa-arrow-down"></i> untuk menaikkan atau menurunkan posisi produk,
                        lalu klik <b>Simpan Urutan</b>. Nomor kecil tampil lebih dulu di halaman toko &amp; katalog kasir — sama seperti fitur Promo.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" id="tabelAturUrutan">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:70px;" class="text-center">No.</th>
                                    <th>Produk</th>
                                    <th style="width:110px;" class="text-center">Urutan</th>
                                    <th style="width:110px;" class="text-center">Geser</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $daftarUrut = $barangs->concat($barangsKosong)->sortBy([['urutan', 'asc'], ['nama_barang', 'asc']])->values(); ?>
                                <?php $__currentLoopData = $daftarUrut; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-id="<?php echo e($b->id_barang); ?>">
                                    <td class="text-center fw-bold nomor-urut"><?php echo e($i + 1); ?></td>
                                    <td>
                                        <?php echo e($b->nama_barang); ?>

                                        <?php if($b->stok <= 0): ?><span class="badge bg-secondary ms-1" style="font-size:10px;">Stok Habis</span><?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="urutan[<?php echo e($b->id_barang); ?>]" class="form-control form-control-sm input-urutan" min="0" value="<?php echo e($b->urutan); ?>">
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-naik-urutan" title="Naikkan"><i class="fa-solid fa-arrow-up"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-turun-urutan" title="Turunkan"><i class="fa-solid fa-arrow-down"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2 mt-3 pt-3 border-top" id="pagerUrutanKasir">
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3" id="prevUrutanKasir">&larr; Sebelumnya</button>
                        <small class="text-muted fw-semibold text-center" id="infoPagerUrutanKasir">Halaman 1</small>
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3" id="nextUrutanKasir">Berikutnya &rarr;</button>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary fw-bold">
                        <i class="fa-solid fa-save me-1"></i> Simpan Urutan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= TOAST NOTIFICATION ================= -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
    <div id="toastProduk" class="toast bg-danger text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
            <i class="fa-solid fa-cart-plus me-2"></i>
            <strong class="me-auto">Produk Masuk Keranjang!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            Produk <span id="toastNamaProduk" class="fw-bold"></span> berhasil ditambahkan ke keranjang.
        </div>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<style>
    .product-item { transition: background-color 0.15s ease; }
    .product-item:hover { background-color: #f8f9fa; }
    #gridProduk .product-item:last-child { border-bottom: none !important; }
    /* Highlight kata kunci hasil pencarian cerdas */
    #gridProduk .product-item mark {
        background: linear-gradient(180deg, transparent 55%, #ffd54a 55%);
        color: inherit; padding: 0; font-weight: 800;
    }
    #infoCari b { color: #c8102e; }
    .keranjang-row .qty-btn { width: 26px; height: 26px; padding: 0; line-height: 1; }
</style>
<script>
// ===== STATE KERANJANG =====
let keranjang = [];
let daftarKeranjangEl = document.getElementById('daftarKeranjang');
let totalKeranjangEl = document.getElementById('totalKeranjang');
let btnCheckout = document.getElementById('btnCheckout');
let inputBayar = document.getElementById('inputBayar');
let totalBelanjaEl = document.getElementById('totalBelanja');
let kembalianEl = document.getElementById('kembalian');
let idAnggota = document.getElementById('idAnggota');
let metodeBayar = document.getElementById('metodeBayar');
let cartBadgeEl = document.getElementById('cartBadge');
let isMember = false;

// ===== FORMAT RUPIAH =====
function rupiah(n) {
    return 'Rp ' + (n || 0).toLocaleString('id-ID');
}

// ===== RENDER KERANJANG (panel kanan) =====
function renderKeranjang() {
    bayarManual = false; // keranjang berubah → bayar kembali otomatis = total baru
    const totalQty = keranjang.reduce((sum, it) => sum + it.qty, 0);
    cartBadgeEl.textContent = totalQty;

    if (keranjang.length === 0) {
        daftarKeranjangEl.innerHTML = '<div class="text-center text-muted py-4"><i class="fa-solid fa-cart-arrow-down fa-2x opacity-25 mb-2"></i><div class="small">Belum ada produk dalam keranjang</div></div>';
        totalKeranjangEl.textContent = rupiah(0);
        hitungTotal();
        return;
    }

    let html = '';
    keranjang.forEach((it, idx) => {
        const harga = isMember ? it.hargaAnggota : it.hargaUmum;
        const subtotal = harga * it.qty;
        html += '<div class="keranjang-row d-flex align-items-center gap-2 px-3 py-2 border-bottom">'
            + '<div class="flex-grow-1">'
            + '<div class="fw-bold small">' + it.nama + '</div>'
            + '<small class="text-muted">' + rupiah(harga) + ' x ' + it.qty + ' = ' + rupiah(subtotal) + '</small>'
            + '</div>'
            + '<button type="button" class="btn btn-sm btn-outline-danger qty-btn" onclick="ubahQty(' + idx + ', -1)">-</button>'
            + '<span class="fw-bold small">' + it.qty + '</span>'
            + '<button type="button" class="btn btn-sm btn-outline-danger qty-btn" onclick="ubahQty(' + idx + ', 1)">+</button>'
            + '<button type="button" class="btn btn-sm btn-outline-danger ms-1" title="Hapus" onclick="hapusItem(' + idx + ')"><i class="fa-solid fa-trash"></i></button>'
            + '</div>';
    });
    daftarKeranjangEl.innerHTML = html;
    totalKeranjangEl.textContent = rupiah(keranjang.reduce((sum, it) => sum + (isMember ? it.hargaAnggota : it.hargaUmum) * it.qty, 0));
    hitungTotal();
}

// ===== UBAH / HAPUS QTY =====
function ubahQty(idx, delta) {
    const it = keranjang[idx];
    if (!it) return;
    if (delta > 0 && it.qty >= it.stok) { alert('Stok "' + it.nama + '" tersisa ' + it.stok + '.'); return; }
    it.qty += delta;
    if (it.qty <= 0) keranjang.splice(idx, 1);
    renderKeranjang();
}
function hapusItem(idx) {
    keranjang.splice(idx, 1);
    renderKeranjang();
}
// ===== TAMBAH KE KERANJANG =====
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = parseInt(this.dataset.id);
        const nama = this.dataset.nama;
        const hargaUmum = parseFloat(this.dataset.hargaUmum);
        const hargaAnggota = parseFloat(this.dataset.hargaAnggota);
        const stok = parseInt(this.dataset.stok);
        const existing = keranjang.find(it => it.id === id);
        if (existing) {
            if (existing.qty >= stok) { alert('Stok "' + nama + '" tersisa ' + stok + '.'); return; }
            existing.qty++;
        } else {
            keranjang.push({ id, nama, hargaUmum, hargaAnggota, stok, qty: 1 });
        }
        renderKeranjang();
        // Toast notifikasi
        document.getElementById('toastNamaProduk').textContent = nama;
        bootstrap.Toast.getOrCreateInstance(document.getElementById('toastProduk'), { delay: 2500 }).show();
    });
});

// ===== PENCARIAN CERDAS KATALOG (multi-kata, toleran salah ketik, urut relevansi) =====
(function() {
    const inp = document.getElementById('cariProduk');
    const grid = document.getElementById('gridProduk');
    const infoCari = document.getElementById('infoCari');
    const kosong = document.getElementById('kosongCari');
    if (!inp || !grid) return;

    const items = Array.from(grid.querySelectorAll('.product-item'));
    items.forEach((el, i) => { el.dataset.urut = i; }); // urutan asli untuk tie-break & reset
    const totalItems = items.length;

    // Normalisasi teks: huruf kecil, buang diakritik, spasi jadi satu
    function norm(s) {
        return (s || '').toString().toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, ' ').trim();
    }
    // Levenshtein dengan batas (early-stop) untuk toleransi typo
    function lev(a, b, max) {
        if (Math.abs(a.length - b.length) > max) return max + 1;
        let prev = []; for (let j = 0; j <= b.length; j++) prev[j] = j;
        for (let i = 1; i <= a.length; i++) {
            let cur = [i], rowMin = i;
            for (let j = 1; j <= b.length; j++) {
                cur[j] = Math.min(prev[j] + 1, cur[j - 1] + 1, prev[j - 1] + (a[i - 1] === b[j - 1] ? 0 : 1));
                if (cur[j] < rowMin) rowMin = cur[j];
            }
            if (rowMin > max) return max + 1; // baris sudah lewat batas
            prev = cur;
        }
        return prev[b.length];
    }
    // Skor 1 kata kueri terhadap 1 produk (0 = tidak cocok)
    // PENCARIAN BERBASIS NAMA SAJA (sesuai permintaan: tanpa sistem barcode/kategori) —
    // hanya nama barang yang mirip yang lolos filter; sisanya disembunyikan.
    function skorKata(qw, nama, words) {
        let s = 0;
        // Nama: cocok penuh, awalan, sisipan, lalu fuzzy per kata
        if (nama === qw) s = Math.max(s, 150);
        else if (nama.startsWith(qw)) s = Math.max(s, 130);
        else if (nama.includes(qw)) s = Math.max(s, 110);
        else {
            for (const w of words) {
                if (w === qw) { s = Math.max(s, 140); break; }
                if (w.startsWith(qw)) { s = Math.max(s, 125); break; }
                if (qw.length >= 4) {
                    const tol = qw.length >= 6 ? 2 : 1; // typo: 1-2 huruf salah
                    if (lev(qw, w, tol) <= tol) { s = Math.max(s, 70); break; }
                }
            }
        }
        // (Pencocokan barcode DIHAPUS — pencarian murni berbasis nama barang)
        return s;
    }
    // Highlight kata kunci pada nama (aman: pakai text nodes, bukan innerHTML)
    function highlight(el, kws) {
        const namaEl = el.querySelector('.flex-grow-1 > .fw-bold');
        if (!namaEl) return;
        const asli = el.dataset.nama || namaEl.textContent;
        if (!kws.length) { namaEl.textContent = asli; return; }
        const lower = asli.toLowerCase();
        let ranges = [];
        kws.forEach(kw => {
            if (kw.length < 2) return;
            let i = lower.indexOf(kw);
            while (i !== -1) { ranges.push([i, i + kw.length]); i = lower.indexOf(kw, i + 1); }
        });
        if (!ranges.length) { namaEl.textContent = asli; return; }
        ranges.sort((a, b) => a[0] - b[0]);
        const merged = [ranges[0]];
        for (const r of ranges.slice(1)) {
            const last = merged[merged.length - 1];
            if (r[0] <= last[1]) last[1] = Math.max(last[1], r[1]); else merged.push(r);
        }
        namaEl.textContent = '';
        let pos = 0;
        merged.forEach(([s, e]) => {
            if (s > pos) namaEl.appendChild(document.createTextNode(asli.slice(pos, s)));
            const m = document.createElement('mark'); m.textContent = asli.slice(s, e);
            namaEl.appendChild(m); pos = e;
        });
        if (pos < asli.length) namaEl.appendChild(document.createTextNode(asli.slice(pos)));
    }
    // Kembalikan urutan asli katalog
    function resetUrut() {
        items.slice().sort((a, b) => a.dataset.urut - b.dataset.urut)
             .forEach(el => grid.appendChild(el));
    }
    let lastMatched = [];
    function cari() {
        const q = norm(inp.value);
        const kws = q ? q.split(' ').filter(Boolean) : [];
        if (!kws.length) { // query kosong: tampilkan semua, urut asli
            items.forEach(el => { el.style.removeProperty('display'); highlight(el, []); });
            resetUrut();
            grid.scrollTop = 0; // kembali ke atas agar daftar asli terlihat dari awal
            infoCari.classList.add('d-none');
            kosong.classList.add('d-none');
            lastMatched = [];
            return;
        }
        const qGabung = kws.join(' ');
        const hasil = [];
        items.forEach(el => {
            const nama = norm(el.dataset.nama);
            const words = nama ? nama.split(' ') : [];
            let total = 0, lolos = true;
            for (const kw of kws) {
                const s = skorKata(kw, nama, words);
                if (s <= 0) { lolos = false; break; }
                total += s;
            }
            if (!lolos) {
                // '!important' WAJIB: item punya class Bootstrap .d-flex (display:flex !important)
                // yang mengalahkan display:none biasa — akibatnya barang tak pernah tersembunyi.
                el.style.setProperty('display', 'none', 'important');
                highlight(el, []); return;
            }
            // Bonus frasa penuh & urutan kata
            if (nama === qGabung) total += 200;
            else if (nama.startsWith(qGabung)) total += 80;
            else if (nama.includes(qGabung)) total += 50;
            el.style.removeProperty('display'); // tampilkan lagi (kembali ke .d-flex)
            highlight(el, kws);
            hasil.push({ el, total });
        });
        // Urutkan: skor tertinggi dulu, tie-break = urutan katalog asli
        hasil.sort((a, b) => (b.total - a.total) || (a.el.dataset.urut - b.el.dataset.urut));
        hasil.forEach(h => grid.appendChild(h.el)); // hasil relevan naik ke atas
        grid.scrollTop = 0; // PENTING: paksa daftar ke posisi paling atas agar hasil
                            // yang baru diurutkan langsung terlihat (bukan tertinggal
                            // di luar viewport saat panel sudah digulir ke bawah)
        lastMatched = hasil.map(h => h.el);
        if (hasil.length) {
            const top = (hasil[0].el.dataset.nama || '').replace(/</g, '&lt;');
            infoCari.innerHTML = 'Menampilkan <b>' + hasil.length + '</b> dari <b>' + totalItems +
                '</b> produk · teratas: <b>' + top + '</b> (urut relevansi)';
            infoCari.classList.remove('d-none');
            kosong.classList.add('d-none');
        } else {
            infoCari.classList.add('d-none');
            // Pesan persis ketentuan fitur: "Produk tidak ditemukan"
            document.getElementById('kosongCariJudul').textContent = 'Produk tidak ditemukan';
            kosong.classList.remove('d-none');
        }
    }

    let jeda;
    inp.addEventListener('input', function() { clearTimeout(jeda); jeda = setTimeout(cari, 120); });
    inp.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { inp.value = ''; cari(); return; }
        // Enter = langsung tambahkan produk hasil teratas di daftar
        if (e.key === 'Enter') {
            e.preventDefault();
            if (lastMatched.length) {
                const btn = lastMatched[0].querySelector('.add-to-cart');
                if (btn) { btn.click(); inp.select(); }
            }
        }
    });
})();

// ===== AUTO BARCODE =====
document.getElementById('btnAutoBarcode').addEventListener('click', function() {
    const now = new Date();
    const str = now.getFullYear() + String(now.getMonth() + 1).padStart(2, '0') + String(now.getDate()).padStart(2, '0') + String(now.getHours()).padStart(2, '0') + String(now.getMinutes()).padStart(2, '0') + String(now.getSeconds()).padStart(2, '0');
    const random = Math.random().toString(36).substring(2, 5).toUpperCase();
    document.querySelector('input[name="kode_barcode"]').value = 'BRG-' + str + '-' + random;
});

// ===== HITUNG TOTAL & KEMBALIAN (bayar OTOMATIS = total belanja) =====
let bayarManual = false; // true = kasir mengetik nominal sendiri (uang lebih)
function hitungTotal() {
    let total = 0;
    keranjang.forEach(it => { const harga = isMember ? it.hargaAnggota : it.hargaUmum; total += harga * it.qty; });
    // Bayar terisi otomatis sesuai total — tidak perlu ketik manual
    if (!bayarManual) inputBayar.value = total;
    const bayar = parseFloat(inputBayar.value) || 0;
    const kurang = metodeBayar.value === 'tunai' && bayar < total;
    const kembali = Math.max(0, bayar - total);
    totalBelanjaEl.value = rupiah(total);
    kembalianEl.value = rupiah(kembali);
    btnCheckout.disabled = keranjang.length === 0 || (total > 0 && kurang);
    btnCheckout.classList.toggle('opacity-50', btnCheckout.disabled);
}
inputBayar.addEventListener('input', function() { bayarManual = true; hitungTotal(); });
metodeBayar.addEventListener('change', function() {
    if (metodeBayar.value !== 'tunai') bayarManual = false; // QRIS/transfer selalu pas = total
    hitungTotal();
});
idAnggota.addEventListener('change', function() { isMember = this.value !== ''; bayarManual = false; renderKeranjang(); });
// ===== CHECKOUT (AJAX) =====
btnCheckout.addEventListener('click', function() {
    if (keranjang.length === 0) { alert('Keranjang masih kosong!'); return; }
    let total = 0;
    keranjang.forEach(it => { const harga = isMember ? it.hargaAnggota : it.hargaUmum; total += harga * it.qty; });
    const metode = metodeBayar.value;
    const bayar = parseFloat(inputBayar.value) || 0;
    if (metode === 'tunai' && bayar < total) { alert('Nominal bayar kurang dari total belanja!'); return; }
    // Non-tunai (QRIS/transfer): nominal dianggap pas sebesar total
    const nominal = metode === 'tunai' ? bayar : total;
    const items = keranjang.map(it => ({ id: it.id, qty: it.qty }));
    btnCheckout.disabled = true;
    fetch('<?php echo e(route('kasir.checkout')); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ id_anggota: idAnggota.value || null, metode_bayar: metode, nominal_bayar: nominal, items: items })
    }).then(r => r.json().then(data => ({ status: r.status, data: data }))).then(res => {
        if (res.data && res.data.success) {
            window.location.href = '<?php echo e(route('kasir.struk', '__ID__')); ?>'.replace('__ID__', res.data.penjualan_id);
        } else {
            alert('Transaksi gagal: ' + ((res.data && res.data.message) || 'Periksa kembali data transaksi.'));
            hitungTotal();
        }
    }).catch(err => {
        console.error('Checkout gagal:', err);
        alert('Gagal menghubungi server (' + err.message + ').\n\n' +
              'Kemungkinan server aplikasi tidak berjalan. Jalankan ulang:\n' +
              'php artisan serve --port=8000\n\nlalu ulangi pembayaran.');
        hitungTotal();
    });
});

// ===== RESTOCK ANGKA (per produk — semua barang) =====
document.querySelectorAll('.btn-restock').forEach(btn => {
    btn.addEventListener('click', async function() {
        const id = this.dataset.id;
        const input = document.getElementById('restok-' + id);
        const jumlah = parseInt(input ? input.value : 0, 10) || 0;
        if (jumlah < 1) { alert('Jumlah restock minimal 1.'); return; }
        const badge = document.getElementById('stok-' + id);
        const cartBtn = document.querySelector('.add-to-cart[data-id="' + id + '"]');
        this.disabled = true;
        try {
            const res = await fetch('/kasir/stok/' + id + '/tambah', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ jumlah: jumlah })
            });
            if (res.status === 422) {
                const err = await res.json();
                alert((err.message || 'Gagal restock.') + (err.errors && err.errors.jumlah ? ' ' + err.errors.jumlah.join(' ') : ''));
                return;
            }
            if (!res.ok) throw new Error('HTTP ' + res.status);
            if (badge) {
                const baru = (parseInt(badge.dataset.stok, 10) || 0) + jumlah;
                const min = parseInt(badge.dataset.min, 10) || 0;
                badge.dataset.stok = baru;
                badge.classList.toggle('bg-warning', baru <= min);
                badge.classList.toggle('text-dark', baru <= min);
                badge.classList.toggle('bg-light', baru > min);
                badge.classList.toggle('text-muted', baru > min);
                badge.classList.toggle('border', baru > min);
                badge.textContent = (baru <= min ? 'Sisa ' : 'Stok ') + baru;
                badge.style.backgroundColor = '#198754';
                badge.style.color = '#ffffff';
                setTimeout(() => { badge.style.backgroundColor = ''; badge.style.color = ''; }, 1200);
            }
            if (cartBtn) cartBtn.dataset.stok = ((parseInt(cartBtn.dataset.stok, 10) || 0) + jumlah);
            input.value = 1;
            // Restock dari tab "Stok Kosong": reload agar produk pindah ke tab Ready
            if (this.closest('#gridKosong')) {
                location.reload();
            }
        } catch (e) {
            alert('Restock gagal: ' + e.message);
        } finally {
            this.disabled = false;
        }
    });
});

// ===== ATUR URUTAN KATALOG (tombol naik/turun + paginasi 10 baris/halaman) =====
const PER_URUTAN_KASIR = 10;
const barisUrutSemua = () => Array.from(document.querySelectorAll('#tabelAturUrutan tbody tr'));
let halUrutanAktif = 0;
function jmlHalUrutan() {
    return Math.max(1, Math.ceil(barisUrutSemua().length / PER_URUTAN_KASIR));
}
function nomorUrutkanUlang() {
    document.querySelectorAll('#tabelAturUrutan tbody tr').forEach((tr, i) => {
        tr.querySelector('.nomor-urut').textContent = i + 1;
    });
}
function renderPagerUrutan() {
    const baris = barisUrutSemua();
    const jmlHal = jmlHalUrutan();
    if (halUrutanAktif > jmlHal - 1) halUrutanAktif = jmlHal - 1;
    baris.forEach((tr, i) => {
        const tampil = Math.floor(i / PER_URUTAN_KASIR) === halUrutanAktif;
        tr.classList.toggle('d-none', !tampil);
        // required hanya baris halaman aktif agar validasi browser tidak
        // memblokir submit diam-diam dari input tersembunyi
        const inp = tr.querySelector('.input-urutan');
        if (inp) inp.required = tampil;
    });
    const info = document.getElementById('infoPagerUrutanKasir');
    if (info) info.textContent = 'Halaman ' + (halUrutanAktif + 1) + ' dari ' + jmlHal + ' (10 produk/halaman)';
    const prev = document.getElementById('prevUrutanKasir');
    const next = document.getElementById('nextUrutanKasir');
    if (prev) prev.disabled = halUrutanAktif === 0;
    if (next) next.disabled = halUrutanAktif >= jmlHal - 1;
}
function halDariBaris(tr) {
    return Math.floor(barisUrutSemua().indexOf(tr) / PER_URUTAN_KASIR);
}
document.querySelectorAll('.btn-naik-urutan').forEach(btn => {
    btn.addEventListener('click', function () {
        const tr = this.closest('tr');
        const prev = tr.previousElementSibling;
        if (prev) {
            tr.parentNode.insertBefore(tr, prev);
            nomorUrutkanUlang();
            halUrutanAktif = halDariBaris(tr); // ikuti barisnya agar tetap terlihat
            renderPagerUrutan();
        }
    });
});
document.querySelectorAll('.btn-turun-urutan').forEach(btn => {
    btn.addEventListener('click', function () {
        const tr = this.closest('tr');
        const next = tr.nextElementSibling;
        if (next) {
            tr.parentNode.insertBefore(next, tr);
            nomorUrutkanUlang();
            halUrutanAktif = halDariBaris(tr);
            renderPagerUrutan();
        }
    });
});
const prevUrutanKasir = document.getElementById('prevUrutanKasir');
const nextUrutanKasir = document.getElementById('nextUrutanKasir');
if (prevUrutanKasir) prevUrutanKasir.addEventListener('click', () => { if (halUrutanAktif > 0) { halUrutanAktif--; renderPagerUrutan(); } });
if (nextUrutanKasir) nextUrutanKasir.addEventListener('click', () => { if (halUrutanAktif < jmlHalUrutan() - 1) { halUrutanAktif++; renderPagerUrutan(); } });
renderPagerUrutan();
// Isi kolom angka otomatis mengikuti posisi baris (1..n) sebelum form disimpan
const formAturUrutan = document.getElementById('formAturUrutan');
if (formAturUrutan) {
    formAturUrutan.addEventListener('submit', function () {
        document.querySelectorAll('#tabelAturUrutan tbody tr').forEach((tr, i) => {
            tr.querySelector('.input-urutan').value = i;
        });
    });
}

// ===== CARI MEMBER OTOMATIS LEWAT NO. HP =====
const cariNoHpEl = document.getElementById('cariNoHpAnggota');
if (cariNoHpEl) {
    const infoCari = document.getElementById('infoCariAnggota');
    const selAnggota = document.getElementById('idAnggota');
    // Normalkan format nomor: 628xx -> 08xx, buang spasi / tanda +
    const normHp = s => (s || '').replace(/[\s\-+()]/g, '').replace(/^62/, '0');
    cariNoHpEl.addEventListener('input', function () {
        const q = normHp(this.value);
        if (q.length < 4) {
            infoCari.classList.add('d-none');
            return;
        }
        const cocok = Array.from(selAnggota.options).filter(o =>
            o.value && normHp(o.dataset.nohp).startsWith(q)
        );
        if (cocok.length === 1) {
            selAnggota.value = cocok[0].value;
            selAnggota.dispatchEvent(new Event('change'));
            infoCari.textContent = '✓ Member terpilih otomatis: ' + cocok[0].dataset.nama;
            infoCari.className = 'small fw-bold text-success mb-2';
        } else if (cocok.length > 1) {
            infoCari.textContent = cocok.length + ' member cocok — pilih manual di daftar.';
            infoCari.className = 'small text-muted mb-2';
        } else {
            infoCari.textContent = 'Tidak ada member dengan No. HP "' + this.value + '".';
            infoCari.className = 'small text-danger mb-2';
        }
        infoCari.classList.remove('d-none');
    });
}

// Initialize
renderKeranjang();
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>





<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/kasir/index.blade.php ENDPATH**/ ?>