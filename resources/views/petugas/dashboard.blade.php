@extends('layouts.app')

@section('content')
<div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark"><i class="fa-solid fa-gauge-high text-danger me-2"></i> Dashboard Petugas</h3>
        <p class="text-secondary mb-0">Ringkasan internal KOPMA MART. Hanya dapat dilihat oleh petugas yang sedang bertugas.</p>
    </div>
</div>

<!-- STATISTIK INTERNAL (dipindah dari dashboard publik) -->
<div class="row g-3 mb-4">
    <!-- Stat 1: Member Aktif -->
    <div class="col-sm-6 col-xl">
        <div class="card card-kopma p-3 border-start border-4 border-danger h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Member Aktif</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $totalAnggota }}</h3>
                    <small class="text-danger"><i class="fa-solid fa-arrow-trend-up"></i> Terverifikasi</small>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-circle">
                    <i class="fa-solid fa-users fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Penjualan Bulan Ini -->
    <div class="col-sm-6 col-xl">
        <div class="card card-kopma p-3 border-start border-4 border-danger h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Penjualan Bulan Ini</span>
                    <h4 class="fw-bold text-danger mt-1 mb-0">Rp {{ number_format($totalPenjualanBulanIni, 0, ',', '.') }}</h4>
                    <small class="text-muted">Akumulasi Semua Transaksi</small>
                </div>
                <div class="bg-danger-subtle text-danger p-3 rounded-circle">
                    <i class="fa-solid fa-sack-dollar fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Penjualan Hari Ini -->
    <div class="col-sm-6 col-xl">
        <div class="card card-kopma p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Penjualan Kasir</span>
                    <h4 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</h4>
                    <small class="text-primary"><i class="fa-solid fa-receipt"></i> Hari Ini</small>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                    <i class="fa-solid fa-cash-register fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Jumlah Barang -->
    <div class="col-sm-6 col-xl">
        <div class="card card-kopma p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Jumlah Barang</span>
                    <h3 class="fw-bold text-primary mt-1 mb-0">{{ $totalBarang }} Item</h3>
                    <small class="text-muted">{{ $stokMenipis }} menipis &bull; {{ $stokHabis }} habis</small>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                    <i class="fa-solid fa-boxes-stacked fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 5: Jumlah Pegawai -->
    <div class="col-sm-6 col-xl">
        <div class="card card-kopma p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Staf Pegawai</span>
                    <h3 class="fw-bold text-info mt-1 mb-0">{{ $totalPegawai }} Staf</h3>
                    <small class="text-muted">Jabatan Terstruktur</small>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle">
                    <i class="fa-solid fa-id-badge fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- WIDGET BARANG KOSONG (khusus petugas) — pantau produk habis dari katalog utama -->
<div class="card card-kopma mb-4 border-start border-4 border-warning">
    <div class="card-kopma-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-box-open text-warning me-2"></i>Barang Kosong
            <span class="badge {{ $stokHabis > 0 ? 'bg-warning text-dark' : 'bg-success' }} ms-2">{{ $stokHabis }} produk</span>
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('kasir.index') }}" class="btn btn-sm btn-outline-danger text-decoration-none">
                <i class="fa-solid fa-cash-register me-1"></i> Kelola di Kasir
            </a>
            @if($stokHabis > 0)
            <form action="{{ route('kasir.barang.hapus-kosong') }}" method="POST" class="m-0"
                  onsubmit="return confirm('Hapus SEMUA {{ $stokHabis }} produk berstok habis dari katalog?\nRiwayat transaksi produk tersebut ikut terhapus.');" >
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-warning text-dark">
                    <i class="fa-solid fa-broom me-1"></i> Hapus Otomatis Semua
                </button>
            </form>
            @endif
        </div>
    </div>
    <div class="card-body">
        @forelse($barangKosong as $bk)
        <div class="d-flex align-items-center justify-content-between gap-2 px-2 py-2 border-bottom">
            <div class="flex-grow-1">
                <div class="fw-bold small">{{ $bk->nama_barang }}</div>
                <small class="text-muted font-monospace">{{ $bk->kode_barcode }}</small>
                <span class="text-muted small ms-1">&middot; {{ $bk->kategori->nama_kategori ?? '-' }}</span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <span class="badge bg-danger">Stok Habis</span>
                <a href="{{ route('toko.index', ['q' => $bk->nama_barang]) }}" class="btn btn-sm btn-outline-success text-decoration-none" title="Restock produk ini">
                    <i class="fa-solid fa-box-archive me-1"></i>Restock
                </a>
                <form action="{{ route('kasir.barang.hapus', $bk->id_barang) }}" method="POST" class="m-0"
                      onsubmit="return confirm('Hapus produk \"{{ $bk->nama_barang }}\" permanen dari katalog?');" >
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('form').submit()">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-3">
            <i class="fa-solid fa-circle-check fa-2x text-success mb-2"></i>
            <div class="fw-bold">Semua produk tersedia — tidak ada barang kosong.</div>
        </div>
        @endforelse
    </div>
</div>

<!-- MENU CEPAT PETUGAS -->
<div class="card card-kopma">
    <div class="card-kopma-header">
        <h5 class="fw-bold text-danger mb-0"><i class="fa-solid fa-bolt me-2"></i> Menu Cepat Petugas</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('kasir.index') }}" class="btn btn-danger w-100 py-3 fw-bold">
                    <i class="fa-solid fa-cash-register fa-lg mb-1 d-block"></i> Kasir (POS)
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('laporan.index') }}" class="btn btn-primary w-100 py-3 fw-bold">
                    <i class="fa-solid fa-calendar-days fa-lg mb-1 d-block"></i> Laporan Keuangan
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('kasir.riwayat') }}" class="btn btn-danger w-100 py-3 fw-bold">
                    <i class="fa-solid fa-print fa-lg mb-1 d-block"></i> Cetak Struk
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('toko.index') }}" class="btn btn-warning w-100 py-3 fw-bold text-dark">
                    <i class="fa-solid fa-boxes-stacked fa-lg mb-1 d-block"></i> Kelola Stok
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('laporan.analisis') }}" class="btn btn-primary w-100 py-3 fw-bold">
                    <i class="fa-solid fa-chart-line fa-lg mb-1 d-block"></i> Analisis &amp; Proyeksi
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('admin.payment-setting') }}" class="btn btn-dark w-100 py-3 fw-bold">
                    <i class="fa-solid fa-credit-card fa-lg mb-1 d-block"></i> Pengaturan Bayar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
