@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold text-danger mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i>Kelola Produk</h4>
            <small class="text-muted">Tambah, ubah, hapus, dan atur urutan tampil produk di halaman utama toko &amp; POS kasir.</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('toko.index') }}" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Toko
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAturUrutan">
                <i class="fa-solid fa-arrow-down-wide-short me-1"></i> Atur Urutan
            </button>
            <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                <i class="fa-solid fa-plus me-1"></i> Tambah Produk
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
        <div class="input-group input-group-sm" style="max-width:400px;">
            <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="text" id="cariProduk" class="form-control" placeholder="Cari nama produk, barcode, atau kategori..." autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="bersihkanCariProduk" title="Bersihkan pencarian"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <small class="text-muted" id="infoCariProduk">Menampilkan {{ $barangs->count() }} produk</small>
    </div>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">Urutan</th>
                        <th>Produk</th>
                        <th style="width:170px;">Harga</th>
                        <th style="width:80px;" class="text-center">Stok</th>
                        <th style="width:130px;">Kategori</th>
                        <th style="width:110px;">Status</th>
                        <th style="width:180px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $b)
                    @php $adaDiskon = $b->harga_jual_umum > $b->harga_jual_anggota && $b->harga_jual_anggota > 0; @endphp
                    <tr class="baris-produk">
                        <td class="fw-bold text-center">{{ $b->urutan }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($b->gambar)
                                <img src="{{ asset($b->gambar) }}" alt="{{ $b->nama_barang }}"
                                     style="width:42px;height:42px;object-fit:cover;border-radius:8px;">
                                @else
                                <span class="d-inline-flex align-items-center justify-content-center bg-light rounded" style="width:42px;height:42px;">
                                    <i class="fa-solid fa-box text-muted"></i>
                                </span>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $b->nama_barang }}</div>
                                    <small class="text-muted">{{ $b->kode_barcode ?: 'Tanpa barcode' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-danger">Rp{{ number_format($b->harga_jual_umum, 0, ',', '.') }}</div>
                            @if($adaDiskon)
                            <small class="text-success"><i class="fa-solid fa-tag me-1"></i>Member Rp{{ number_format($b->harga_jual_anggota, 0, ',', '.') }}</small>
                            @else
                            <small class="text-muted">Member Rp{{ number_format($b->harga_jual_anggota, 0, ',', '.') }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $b->stok <= $b->stok_minimum ? 'bg-danger' : 'bg-success' }}">{{ $b->stok }}</span>
                        </td>
                        <td>
                            @if($b->kategori)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $b->kategori->nama_kategori }}</span>
                            @else
                            <span class="text-muted small">Tanpa kategori</span>
                            @endif
                        </td>
                        <td>
                            @if($b->stok > 0)
                            <span class="badge bg-success bg-opacity-10 text-success"><i class="fa-solid fa-circle-check me-1"></i>Siap Jual</span>
                            @else
                            <span class="badge bg-danger bg-opacity-10 text-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Stok Habis</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-ubah"
                                    data-bs-toggle="modal" data-bs-target="#modalUbahProduk"
                                    data-id="{{ $b->id_barang }}"
                                    data-nama="{{ $b->nama_barang }}"
                                    data-barcode="{{ $b->kode_barcode }}"
                                    data-kat="{{ $b->id_kategori }}"
                                    data-beli="{{ $b->harga_beli }}"
                                    data-umum="{{ $b->harga_jual_umum }}"
                                    data-anggota="{{ $b->harga_jual_anggota }}"
                                    data-stok="{{ $b->stok }}"
                                    data-stok-min="{{ $b->stok_minimum }}"
                                    data-urutan="{{ $b->urutan }}"
                                    data-gambar="{{ $b->gambar ? asset($b->gambar) : '' }}">
                                <i class="fa-solid fa-pen"></i> Ubah
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                    data-bs-toggle="modal" data-bs-target="#modalHapusProduk"
                                    data-id="{{ $b->id_barang }}"
                                    data-nama="{{ $b->nama_barang }}">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-box-open fa-2x mb-2 d-block"></i>Belum ada produk. Klik <b>Tambah Produk</b> untuk memasukkan produk baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- ================= MODAL TAMBAH PRODUK ================= -->
<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambahProduk" action="{{ route('petugas.produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label fw-bold small">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Indomie Goreng" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Barcode</label>
                            <input type="text" name="kode_barcode" class="form-control" placeholder="Kosongkan = otomatis">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach($kategoris as $kat)
                                <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Harga Beli</label>
                            <input type="number" name="harga_beli" class="form-control" min="0" placeholder="Otomatis 85% jual">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Harga Jual Umum <span class="text-danger">*</span></label>
                            <input type="number" name="harga_jual_umum" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Harga Member <span class="text-danger">*</span></label>
                            <input type="number" name="harga_jual_anggota" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Stok Minimum</label>
                            <input type="number" name="stok_minimum" class="form-control" min="0" value="5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-control" min="0" placeholder="Kosongkan = paling bawah">
                            <small class="text-muted">Nomor kecil tampil lebih atas di halaman utama. Bisa diketik langsung.</small>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small">Foto Produk</label>
                        <input type="file" name="gambar" class="form-control" accept="image/jpg,image/jpeg,image/png,image/webp">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ================= MODAL UBAH PRODUK ================= -->
<div class="modal fade" id="modalUbahProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ubah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUbahProduk" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label fw-bold small">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" id="editNama" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Barcode</label>
                            <input type="text" id="editBarcode" name="kode_barcode" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Kategori</label>
                            <select id="editKategori" name="id_kategori" class="form-select">
                                <option value="">— Tanpa Kategori —</option>
                                @foreach($kategoris as $kat)
                                <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Harga Beli</label>
                            <input type="number" id="editHargaBeli" name="harga_beli" class="form-control" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Harga Jual Umum <span class="text-danger">*</span></label>
                            <input type="number" id="editHargaUmum" name="harga_jual_umum" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Harga Member <span class="text-danger">*</span></label>
                            <input type="number" id="editHargaAnggota" name="harga_jual_anggota" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Stok <span class="text-danger">*</span></label>
                            <input type="number" id="editStok" name="stok" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Stok Minimum</label>
                            <input type="number" id="editStokMin" name="stok_minimum" class="form-control" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Urutan Tampil</label>
                            <input type="number" id="editUrutan" name="urutan" class="form-control" min="0">
                            <small class="text-muted">Bisa diketik langsung — nomor kecil tampil lebih atas di halaman utama.</small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">Ganti Foto (opsional)</label>
                        <input type="file" name="gambar" class="form-control" accept="image/jpg,image/jpeg,image/png,image/webp">
                    </div>
                    <div class="text-center mt-2">
                        <img id="editPreviewGambar" src="" alt="Preview produk" class="d-none rounded" style="max-height:150px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ================= MODAL HAPUS PRODUK ================= -->
<div class="modal fade" id="modalHapusProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Yakin ingin menghapus produk</p>
                <p class="fw-bold text-danger fs-5 mb-2" id="namaProdukHapus">?</p>
                <p class="text-muted small mb-0">Produk akan dihapus permanen dari katalog toko &amp; POS kasir beserta riwayat penjualan detailnya (FK CASCADE).</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusProduk" method="POST" class="d-inline">
                    @method('DELETE')
                    @csrf
                    <button type="submit" class="btn btn-danger fw-bold">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===== Modal Atur Urutan: list kompak, drag-and-drop, scroll halus ===== -->
<style>
    #modalAturUrutan .modal-dialog { max-width: 640px; margin: 1.75rem auto; }
    #modalAturUrutan .modal-content {
        border: 0; border-radius: 16px; overflow: hidden;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .22);
    }
    #modalAturUrutan .modal-header { border-bottom: 1px solid #eef1f4; padding: 14px 18px; }
    #modalAturUrutan .modal-title { font-size: 16.5px; }
    /* Badan modal: tinggi terbatas + gulir vertikal halus (tanpa gulir horizontal) */
    #modalAturUrutan .modal-body {
        padding: 12px 14px;
        max-height: min(64vh, 540px);
        overflow-y: auto; overflow-x: hidden;
        scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;
        -webkit-overflow-scrolling: touch;
    }
    #modalAturUrutan .modal-body::-webkit-scrollbar { width: 8px; }
    #modalAturUrutan .modal-body::-webkit-scrollbar-track { background: transparent; }
    #modalAturUrutan .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1; border-radius: 8px; border: 2px solid #fff;
    }
    #modalAturUrutan .modal-body::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    #modalAturUrutan .modal-footer {
        border-top: 1px solid #eef1f4; padding: 11px 16px;
        flex-wrap: wrap; gap: 8px; justify-content: flex-end;
    }
    #modalAturUrutan .petunjuk-urutan {
        font-size: 12px; line-height: 1.45; color: #6b7280;
        background: #f8fafc; border: 1px solid #eef1f4;
        border-radius: 9px; padding: 7px 11px; margin-bottom: 10px;
    }
    /* Daftar kompak: satu kolom, baris ramping, jarak rapat tapi rapi */
    #daftarUrutan {
        display: flex; flex-direction: column; gap: 6px;
        min-width: 0; overflow-x: hidden;
    }
    #daftarUrutan .urutan-item {
        display: flex; align-items: center; gap: 10px;
        background: #fff; border: 1px solid #e5e9ef; border-radius: 10px;
        padding: 6px 10px; margin: 0; min-width: 0;
        cursor: grab; user-select: none;
        transition: border-color .12s ease, box-shadow .12s ease, opacity .12s ease;
    }
    #daftarUrutan .urutan-item:hover { border-color: #f2b8c0; }
    #daftarUrutan .urutan-item.dragging { opacity: .4; border-style: dashed; }
    #daftarUrutan .urutan-grip { color: #9aa4b2; font-size: 13px; line-height: 1; flex: 0 0 auto; }
    #daftarUrutan .urutan-thumb {
        width: 34px; height: 34px; flex: 0 0 auto;
        object-fit: contain; background: #f8fafc;
        border: 1px solid #eef1f4; border-radius: 8px; padding: 2px;
    }
    /* Nama + stok: satu blok kiri, ellipsis — tidak pernah menabrak kontrol */
    #daftarUrutan .urutan-info {
        flex: 1 1 auto; min-width: 0;
        display: flex; flex-direction: column; gap: 1px;
    }
    #daftarUrutan .urutan-nama {
        font-size: 13px; font-weight: 700; color: #1f2937; line-height: 1.3;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    #daftarUrutan .urutan-stok { font-size: 11px; color: #6b7280; }
    /* Kotak nomor urut: kecil, bisa diketik langsung — baris pindah posisi otomatis */
    #daftarUrutan .urutan-nomor {
        flex: 0 0 auto; width: 54px; height: 28px;
        text-align: center; font-size: 13px; font-weight: 800; color: #0f172a;
        background: #f8fafc; border: 1px solid #d7dce2; border-radius: 8px;
        padding: 0 4px; margin: 0; -moz-appearance: textfield;
    }
    #daftarUrutan .urutan-nomor::-webkit-outer-spin-button,
    #daftarUrutan .urutan-nomor::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    #daftarUrutan .urutan-nomor:focus {
        outline: none; background: #fff; border-color: #c8102e;
        box-shadow: 0 0 0 3px rgba(200, 16, 46, .12);
    }
    /* Tombol panah cepat di kanan */
    #daftarUrutan .urutan-aksi { flex: 0 0 auto; display: flex; gap: 4px; }
    #daftarUrutan .urutan-aksi button {
        width: 26px; height: 26px; padding: 0; border: 1px solid #d7dce2;
        background: #fff; color: #475569; border-radius: 7px; font-size: 10px;
        display: inline-flex; align-items: center; justify-content: center;
    }
    #daftarUrutan .urutan-aksi button:hover:not(:disabled) {
        background: #c8102e; border-color: #c8102e; color: #fff;
    }
    #daftarUrutan .urutan-aksi button:disabled { opacity: .35; cursor: not-allowed; }
    @media (max-width: 575.98px) {
        #modalAturUrutan .modal-dialog { max-width: none; margin: .75rem; }
        #modalAturUrutan .modal-body { max-height: 70vh; padding: 10px 10px; }
        #daftarUrutan .urutan-item { gap: 7px; padding: 5px 8px; }
        #daftarUrutan .urutan-thumb { width: 28px; height: 28px; }
        #daftarUrutan .urutan-nama { font-size: 12.5px; }
    }
    /* Backdrop lembut */
    .modal-backdrop.show { backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px); }
</style>
<!-- ================= MODAL ATUR URUTAN (sederhana, gaya modal Promo) ================= -->
<div class="modal fade" id="modalAturUrutan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Atur Urutan Tampil Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formAturUrutan" action="{{ route('petugas.produk.atur-urutan') }}" method="POST">
                @csrf
                <div class="modal-body pt-2">
                    <p class="petunjuk-urutan mb-0">
                        <b>Seret baris</b>, tekan <b>▲ / ▼</b>, atau <b>ketik langsung angka</b> pada kotak
                        nomor — posisi baris langsung menyesuaikan (angka kecil = tampil paling atas di halaman
                        utama toko &amp; POS kasir). Lalu tekan <b>Simpan Urutan</b>.
                    </p>
                    <div id="daftarUrutan" class="mt-2">
                        @forelse($barangs as $b)
                        <div class="urutan-item">
                            <span class="urutan-grip" aria-hidden="true"><i class="fa-solid fa-grip-vertical"></i></span>
                            @if($b->gambar)
                            <img class="urutan-thumb" src="{{ asset($b->gambar) }}" alt=""
                                 onerror="this.onerror=null;this.src='{{ asset('images/logo-kopma.svg') }}';">
                            @else
                            <img class="urutan-thumb" src="{{ asset('images/logo-kopma.svg') }}" alt="">
                            @endif
                            <div class="urutan-info">
                                <span class="urutan-nama" title="{{ $b->nama_barang }}">{{ $b->nama_barang }}</span>
                                <span class="urutan-stok">Stok {{ $b->stok }}</span>
                            </div>
                            <input type="number" min="0" step="1" class="urutan-nomor input-urutan"
                                   name="urutan[{{ $b->id_barang }}]" value="{{ (int) $b->urutan }}"
                                   data-nomor aria-label="Nomor urut {{ $b->nama_barang }}" title="Ketik nomor lalu Enter — baris langsung pindah posisi">
                            <div class="urutan-aksi">
                                <button type="button" class="urutan-naik" title="Naikkan" aria-label="Naikkan {{ $b->nama_barang }}"><i class="fa-solid fa-arrow-up"></i></button>
                                <button type="button" class="urutan-turun" title="Turunkan" aria-label="Turunkan {{ $b->nama_barang }}"><i class="fa-solid fa-arrow-down"></i></button>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small mb-0">Belum ada produk untuk diatur.</p>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-warning fw-bold me-auto" id="btnKembalikanUrutan">
                        <i class="fa-solid fa-rotate-left me-1"></i> Kembalikan
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Urutan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
(function () {
    'use strict';

    // ===== Modal Ubah: isi otomatis dari data-* baris tabel =====
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-ubah');
        if (!btn) return;
        var d = btn.dataset;
        var form = document.getElementById('formUbahProduk');
        form.action = '{{ url('petugas/produk') }}/' + d.id;
        document.getElementById('editNama').value = d.nama || '';
        document.getElementById('editBarcode').value = d.barcode || '';
        document.getElementById('editKategori').value = d.kat || '';
        document.getElementById('editHargaBeli').value = d.beli || '';
        document.getElementById('editHargaUmum').value = d.umum || '';
        document.getElementById('editHargaAnggota').value = d.anggota || '';
        document.getElementById('editStok').value = d.stok || 0;
        document.getElementById('editStokMin').value = d.stokMin || 0;
        document.getElementById('editUrutan').value = d.urutan || 0;
        var preview = document.getElementById('editPreviewGambar');
        if (d.gambar) { preview.src = d.gambar; preview.classList.remove('d-none'); }
        else { preview.src = ''; preview.classList.add('d-none'); }
        form.querySelectorAll('input[type="file"]').forEach(function (f) { f.value = ''; });
    });

    // Preview foto terpilih di modal Ubah tanpa simpan dulu
    var inputGambarUbah = document.querySelector('#formUbahProduk input[name="gambar"]');
    if (inputGambarUbah) {
        inputGambarUbah.addEventListener('change', function () {
            var f = this.files && this.files[0];
            var preview = document.getElementById('editPreviewGambar');
            if (!f) return;
            var fr = new FileReader();
            fr.onload = function (ev) { preview.src = ev.target.result; preview.classList.remove('d-none'); };
            fr.readAsDataURL(f);
        });
    }

    // Validasi ringan: stok minimum tidak boleh melebihi stok
    var formUbah = document.getElementById('formUbahProduk');
    if (formUbah) formUbah.addEventListener('submit', function () {
        var stok = parseInt(document.getElementById('editStok').value || '0', 10);
        var stokMin = parseInt(document.getElementById('editStokMin').value || '5', 10);
        if (stokMin > stok) document.getElementById('editStokMin').value = stok;
    });
    var formTambah = document.getElementById('formTambahProduk');
    if (formTambah) formTambah.addEventListener('submit', function () {
        var stok = parseInt(formTambah.querySelector('[name="stok"]').value || '0', 10);
        var stokMinInput = formTambah.querySelector('[name="stok_minimum"]');
        var stokMin = parseInt(stokMinInput.value || '5', 10);
        if (stokMin > stok) stokMinInput.value = stok;
    });

    // ===== Modal Hapus: isi nama produk + action form =====
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-hapus');
        if (!btn) return;
        var d = btn.dataset;
        document.getElementById('namaProdukHapus').textContent = d.nama || '?';
        document.getElementById('formHapusProduk').action = '{{ url('petugas/produk') }}/' + d.id;
    });

    // ===== Atur Urutan kompak: penomoran otomatis + drag-and-drop + panah cepat =====
    var daftarUrutan = document.getElementById('daftarUrutan');
    if (daftarUrutan) {
        var itemUrutan = function () {
            return Array.prototype.slice.call(daftarUrutan.querySelectorAll('.urutan-item'));
        };
        // Snapshot urutan baris saat halaman dimuat (untuk tombol Kembalikan)
        var urutanAsli = itemUrutan();

        // Nomor urut otomatis mengikuti posisi baris; kotak nomor = input submit sekaligus
        var nomorOtomatis = function () {
            var daftar = itemUrutan();
            daftar.forEach(function (item, i) {
                var inp = item.querySelector('.input-urutan');
                if (inp && document.activeElement !== inp) inp.value = i;
            });
            daftar.forEach(function (item, i) {
                var naik = item.querySelector('.urutan-naik');
                var turun = item.querySelector('.urutan-turun');
                if (naik) naik.disabled = (i === 0);
                if (turun) turun.disabled = (i === daftar.length - 1);
            });
        };
        nomorOtomatis(); // penomoran awal = urutan saat ini (bisa beda angka asli, urutannya sama)

        // ---- Ketik angka langsung -> baris pindah ke posisi tersebut ----
        daftarUrutan.addEventListener('change', function (e) {
            var inp = e.target;
            if (!inp.classList || !inp.classList.contains('input-urutan')) return;
            var item = inp.closest ? inp.closest('.urutan-item') : null;
            if (!item) return;
            var daftar = itemUrutan();
            var idx = parseInt(inp.value, 10);
            if (isNaN(idx) || idx < 0) idx = 0;
            if (idx > daftar.length - 1) idx = daftar.length - 1;
            var posisi = daftar.indexOf(item);
            if (posisi !== idx) {
                daftar.splice(posisi, 1);
                daftar.splice(idx, 0, item);
                daftar.forEach(function (el) { daftarUrutan.appendChild(el); }); // susun ulang DOM
            }
            inp.value = idx; // koreksi nilai yang diketik (clamp 0..n-1) walau masih fokus
            nomorOtomatis();
        });
        // Enter di kotak nomor -> selesaikan edit (blur memicu change) tanpa submit form
        daftarUrutan.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.classList && e.target.classList.contains('input-urutan')) {
                e.preventDefault();
                e.target.blur();
            }
        });

        // ---- Aksi cepat Naik / Turun (delegasi klik) ----
        daftarUrutan.addEventListener('click', function (e) {
            var naik = e.target.closest ? e.target.closest('.urutan-naik') : null;
            var turun = e.target.closest ? e.target.closest('.urutan-turun') : null;
            if (!naik && !turun) return;
            var item = e.target.closest('.urutan-item');
            if (!item) return;
            if (naik) {
                var prev = item.previousElementSibling;
                if (prev && prev.classList.contains('urutan-item')) {
                    daftarUrutan.insertBefore(item, prev);
                    nomorOtomatis();
                }
            } else {
                var next = item.nextElementSibling;
                if (next && next.classList.contains('urutan-item')) {
                    daftarUrutan.insertBefore(next, item);
                    nomorOtomatis();
                }
            }
        });

        // ---- Drag & drop HTML5 antar baris ----
        var itemDrag = null;
        itemUrutan().forEach(function (item) { item.draggable = true; });
        daftarUrutan.addEventListener('dragstart', function (e) {
            var item = e.target.closest ? e.target.closest('.urutan-item') : null;
            if (!item) return;
            itemDrag = item;
            item.classList.add('dragging');
            try { e.dataTransfer.setData('text/plain', ''); e.dataTransfer.effectAllowed = 'move'; } catch (err) {}
        });
        daftarUrutan.addEventListener('dragend', function () {
            if (itemDrag) itemDrag.classList.remove('dragging');
            itemDrag = null;
            nomorOtomatis();
        });
        daftarUrutan.addEventListener('dragover', function (e) {
            if (!itemDrag) return;
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            var item = e.target.closest ? e.target.closest('.urutan-item') : null;
            if (!item || item === itemDrag) return;
            var r = item.getBoundingClientRect();
            var sebelum = (e.clientY - r.top) < r.height / 2;
            if (sebelum) daftarUrutan.insertBefore(itemDrag, item);
            else daftarUrutan.insertBefore(itemDrag, item.nextElementSibling);
        });
        daftarUrutan.addEventListener('drop', function (e) {
            if (!itemDrag) return;
            e.preventDefault();
            nomorOtomatis();
        });
    }

    // ===== Kembalikan: kembalikan urutan baris ke keadaan awal (tanpa simpan) =====
    var btnKembalikan = document.getElementById('btnKembalikanUrutan');
    if (btnKembalikan && daftarUrutan) {
        btnKembalikan.addEventListener('click', function () {
            urutanAsli.forEach(function (item) { daftarUrutan.appendChild(item); });
            if (typeof nomorOtomatis === 'function') nomorOtomatis();
            var lama = btnKembalikan.innerHTML;
            btnKembalikan.innerHTML = '<i class="fa-solid fa-check me-1"></i> Urutan dikembalikan';
            btnKembalikan.disabled = true;
            setTimeout(function () {
                btnKembalikan.innerHTML = lama;
                btnKembalikan.disabled = false;
            }, 1400);
        });
    }

    // ===== Pencarian realtime (tanpa tekan Enter) =====
    var inputCari = document.getElementById('cariProduk');
    var infoCari = document.getElementById('infoCariProduk');
    var tombolBersih = document.getElementById('bersihkanCariProduk');
    if (inputCari) {
        var barisProduk = document.querySelectorAll('.baris-produk');
        var totalAwal = barisProduk.length;
        var saringProduk = function () {
            var kunci = inputCari.value.trim().toLowerCase();
            var jumlah = 0;
            barisProduk.forEach(function (tr) {
                var cocok = kunci === '' || tr.textContent.toLowerCase().indexOf(kunci) !== -1;
                tr.classList.toggle('d-none', !cocok);
                if (cocok) jumlah++;
            });
            if (kunci === '') {
                infoCari.textContent = 'Menampilkan ' + totalAwal + ' produk';
                infoCari.classList.remove('text-danger', 'fw-bold');
            } else if (jumlah === 0) {
                infoCari.textContent = 'Tidak ada produk yang cocok dengan "' + inputCari.value.trim() + '"';
                infoCari.classList.add('text-danger', 'fw-bold');
            } else {
                infoCari.textContent = jumlah + ' dari ' + totalAwal + ' produk cocok dengan "' + inputCari.value.trim() + '"';
                infoCari.classList.remove('text-danger');
            }
        };
        inputCari.addEventListener('input', saringProduk);
        if (tombolBersih) tombolBersih.addEventListener('click', function () {
            inputCari.value = '';
            saringProduk();
            inputCari.focus();
        });
    }
})();
</script>
@endsection






