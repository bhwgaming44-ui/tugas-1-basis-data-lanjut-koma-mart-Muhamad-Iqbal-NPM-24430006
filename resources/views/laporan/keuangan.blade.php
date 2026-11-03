@extends('layouts.app')

@php
    $namaBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $hariNama = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
    $rp = fn($n) => 'Rp ' . number_format((float)$n, 0, ',', '.');
@endphp

@section('content')
<div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark"><i class="fa-solid fa-calendar-days text-primary me-2"></i> Laporan Keuangan</h3>
        <p class="text-secondary mb-0">Kalender pemasukan, pengeluaran (modal barang), dan penghasilan (laba) per hari.</p>
    </div>
    <div class="col-md-4 text-md-end mt-2 mt-md-0">
        <a href="{{ route('laporan.analisis', ['bulan' => $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT)]) }}" class="btn btn-primary fw-bold">
            <i class="fa-solid fa-chart-line me-1"></i> Analisis &amp; Proyeksi
        </a>
    </div>
</div>

<!-- PEMILIH 2 PERIODE MANUAL -->
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('laporan.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold mb-1">Periode Utama (A)</label>
                <input type="month" name="bulan" class="form-control" value="{{ $tahun }}-{{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold mb-1">Bandingkan Dengan (B)</label>
                <input type="month" name="banding" class="form-control" value="{{ $banding['input'] ?? '' }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-danger fw-bold flex-grow-1">Tampilkan</button>
                @if(!empty($banding))
                <a href="{{ route('laporan.index', ['bulan' => $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT)]) }}" class="btn btn-outline-secondary fw-bold"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </div>
        </form>
        <div class="small text-muted mt-2">Bulan &amp; tahun manual — boleh beda bulan atau beda tahun. Selisih menyesuaikan otomatis.</div>
    </div>
</div>

@if(!empty($banding))
@php
    $dMasuk  = $totalPemasukan - $banding['pemasukan'];
    $dKeluar = $totalPengeluaran - $banding['pengeluaran'];
    $dHasil  = $totalPenghasilan - $banding['penghasilan'];
    $dTrx    = $jumlahTransaksi - $banding['transaksi'];
    $pctHit = function ($a, $b) { if ((float) $b == 0) return $a == 0 ? 0 : 100; return round(($a - $b) / abs($b) * 100, 1); };
@endphp
<div class="card shadow-sm border-0 rounded-3 mb-4 border-start border-4 border-primary">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Hasil Perbandingan Periode</h6>
        <small class="text-muted">A: <strong>{{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}</strong> vs B: <strong>{{ $namaBulan[$banding['bulan']] ?? '' }} {{ $banding['tahun'] }}</strong></small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Indikator</th><th class="text-end">A (Utama)</th><th class="text-end">B (Banding)</th><th class="text-end">Selisih</th><th class="text-end">%</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-semibold">Pemasukan</td>
                    <td class="text-end fw-bold">{{ $rp($totalPemasukan) }}</td>
                    <td class="text-end text-muted">{{ $rp($banding['pemasukan']) }}</td>
                    <td class="text-end fw-bold {{ $dMasuk >= 0 ? 'text-success' : 'text-danger' }}">{{ $rp($dMasuk) }}</td>
                    <td class="text-end"><span class="badge {{ $dMasuk >= 0 ? 'bg-success' : 'bg-danger' }}">{{ number_format($pctHit($totalPemasukan, $banding['pemasukan']), 1, ',', '.') }}%</span></td>
                </tr>
                <tr>
                    <td class="fw-semibold">Pengeluaran</td>
                    <td class="text-end fw-bold">{{ $rp($totalPengeluaran) }}</td>
                    <td class="text-end text-muted">{{ $rp($banding['pengeluaran']) }}</td>
                    <td class="text-end fw-bold {{ $dKeluar <= 0 ? 'text-success' : 'text-danger' }}">{{ $rp($dKeluar) }}</td>
                    <td class="text-end"><span class="badge bg-secondary">{{ number_format($pctHit($totalPengeluaran, $banding['pengeluaran']), 1, ',', '.') }}%</span></td>
                </tr>
                <tr class="table-primary">
                    <td class="fw-bold">Penghasilan (Laba)</td>
                    <td class="text-end fw-bold">{{ $rp($totalPenghasilan) }}</td>
                    <td class="text-end text-muted">{{ $rp($banding['penghasilan']) }}</td>
                    <td class="text-end fw-bold {{ $dHasil >= 0 ? 'text-success' : 'text-danger' }}">{{ $rp($dHasil) }}</td>
                    <td class="text-end"><span class="badge {{ $dHasil >= 0 ? 'bg-success' : 'bg-danger' }}">{{ number_format($pctHit($totalPenghasilan, $banding['penghasilan']), 1, ',', '.') }}%</span></td>
                </tr>
                <tr>
                    <td class="fw-semibold">Transaksi</td>
                    <td class="text-end fw-bold">{{ $jumlahTransaksi }}</td>
                    <td class="text-end text-muted">{{ $banding['transaksi'] }}</td>
                    <td class="text-end fw-bold">{{ $dTrx }}</td>
                    <td class="text-end"><span class="badge bg-secondary">{{ number_format($pctHit($jumlahTransaksi, $banding['transaksi']), 1, ',', '.') }}%</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- RINGKASAN BULAN INI -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-danger">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold">Pemasukan</span>
                <h4 class="fw-bold text-danger mb-0 mt-1">{{ $rp($totalPemasukan) }}</h4>
                <small class="text-muted">Total penjualan {{ $awalBulan->translatedFormat('F Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-danger">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold">Pengeluaran</span>
                <h4 class="fw-bold text-danger mb-0 mt-1">{{ $rp($totalPengeluaran) }}</h4>
                <small class="text-muted">Modal pembelian barang terjual</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-primary">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold">Penghasilan (Laba)</span>
                <h4 class="fw-bold {{ $totalPenghasilan >= 0 ? 'text-primary' : 'text-danger' }} mb-0 mt-1">{{ $rp($totalPenghasilan) }}</h4>
                <small class="text-muted">{{ $jumlahTransaksi }} transaksi bulan ini</small>
            </div>
        </div>
    </div>
</div>

<!-- KALENDER -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <a href="{{ route('laporan.index', ['bulan' => $prev->format('Y-m')]) }}" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fa-solid fa-chevron-left"></i> Bulan Lalu
        </a>
        <h5 class="fw-bold mb-0">{{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}</h5>
        <a href="{{ route('laporan.index', ['bulan' => $next->format('Y-m')]) }}" class="btn btn-outline-secondary btn-sm fw-bold">
            Bulan Depan <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 kalender-tabel">
                <thead class="table-light text-center">
                    <tr>
                        @foreach($hariNama as $h)
                            <th class="py-2">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($kalender as $minggu)
                        <tr>
                            @foreach($minggu as $sel)
                                @if($sel === null)
                                    <td class="kalender-kosong">&nbsp;</td>
                                @else
                                    <td class="kalender-sel {{ $sel['hari_ini'] ? 'hari-ini' : '' }}" style="height:96px; vertical-align:top;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold {{ $sel['hari_ini'] ? 'badge bg-danger rounded-circle' : '' }}" style="{{ $sel['hari_ini'] ? 'width:26px;height:26px;line-height:18px;' : '' }}">{{ $sel['hari'] }}</span>
                                            @if($sel['pemasukan'] > 0)
                                                <span class="badge bg-danger-subtle text-danger" style="font-size:10px;">{{ $rp($sel['penghasilan']) }}</span>
                                            @endif
                                        </div>
                                        <div class="small lh-sm">
                                            <div class="text-danger"><i class="fa-solid fa-arrow-down"></i> {{ $rp($sel['pemasukan']) }}</div>
                                            <div class="text-danger"><i class="fa-solid fa-arrow-up"></i> {{ $rp($sel['pengeluaran']) }}</div>
                                        </div>
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- LEGEND -->
<div class="mt-3 small text-muted">
    <i class="fa-solid fa-arrow-down text-danger"></i> Pemasukan (penjualan) &nbsp;&bull;&nbsp;
    <i class="fa-solid fa-arrow-up text-danger"></i> Pengeluaran (modal barang) &nbsp;&bull;&nbsp;
    Badge hijau di kanan tanggal = <strong>Penghasilan</strong> harian.
</div>
@endsection
