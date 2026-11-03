@extends('layouts.app')

@php
    $namaBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $rp = fn($n) => 'Rp ' . number_format((float)$n, 0, ',', '.');
    $pct = fn($n) => ($n > 0 ? '+' : '') . number_format((float)$n, 1, ',', '.') . '%';
    $tglNav = fn($c) => $c->format('Y-m');
    $maksHarian = max(1, max(array_column($harian, 'pemasukan')));
@endphp

@section('content')
<div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark"><i class="fa-solid fa-chart-line text-primary me-2"></i> Analisis &amp; Proyeksi Laporan</h3>
        <p class="text-secondary mb-0">Data rinci periode <strong>{{ $namaBulan[$bulan] }} {{ $tahun }}</strong> lengkap dengan tren, rencana restock, dan rekomendasi ke depan.</p>
    </div>
    <div class="col-md-4 text-md-end mt-2 mt-md-0">
        <a href="{{ route('laporan.index', ['bulan' => $tglNav($awalBulan)]) }}" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fa-solid fa-calendar-days me-1"></i> Kalender Keuangan
        </a>
        <div class="btn-group ms-1">
            <a href="{{ route('laporan.analisis', ['bulan' => $tglNav($prev)]) }}" class="btn btn-outline-secondary btn-sm fw-bold"><i class="fa-solid fa-chevron-left"></i></a>
            <a href="{{ route('laporan.analisis', ['bulan' => $tglNav($next)]) }}" class="btn btn-outline-secondary btn-sm fw-bold"><i class="fa-solid fa-chevron-right"></i></a>
        </div>
    </div>
</div>

{{-- KPI UTAMA + PERBANDINGAN BULAN LALU --}}
<div class="row g-3 mb-4">
    @php
        $kartu = [
            ['label' => 'Pemasukan', 'nilai' => $rp($totalPemasukan), 'delta' => $deltaPemasukan, 'sub' => 'vs ' . $rp($prevPemasukan) . ' bulan lalu', 'warna' => 'danger'],
            ['label' => 'Laba Kotor', 'nilai' => $rp($totalPenghasilan), 'delta' => $deltaPenghasilan, 'sub' => 'margin ' . ($totalPemasukan > 0 ? number_format($totalPenghasilan / $totalPemasukan * 100, 1, ',', '.') : '0') . '%', 'warna' => 'primary'],
            ['label' => 'Transaksi', 'nilai' => $jumlahTransaksi, 'delta' => $deltaTransaksi, 'sub' => $jumlahTransaksi . ' vs ' . $prevTransaksi . ' bulan lalu', 'warna' => 'dark'],
            ['label' => 'Rata-rata/Hari', 'nilai' => $rp($rataHarian), 'delta' => null, 'sub' => $hariTerpakai . ' hari terpakai dari ' . $akhirBulan->daysInMonth, 'warna' => 'success'],
        ];
    @endphp
    @foreach($kartu as $k)
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-start border-4 border-{{ $k['warna'] }}">
            <div class="card-body py-3">
                <span class="text-muted small text-uppercase fw-semibold">{{ $k['label'] }}</span>
                <h5 class="fw-bold text-{{ $k['warna'] }} mb-0 mt-1">{{ $k['nilai'] }}</h5>
                <small class="text-muted">{{ $k['sub'] }}</small>
                @if($k['delta'] !== null)
                    <span class="badge {{ $k['delta'] >= 0 ? 'bg-success' : 'bg-danger' }} ms-1" style="font-size:11px;">
                        <i class="fa-solid fa-arrow-{{ $k['delta'] >= 0 ? 'up' : 'down' }}"></i> {{ $pct($k['delta']) }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- GRAFIK HARIAN + PROYEKSI --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-column me-2 text-danger"></i>Pemasukan Harian</h6></div>
            <div class="card-body">
                <div class="d-flex align-items-end gap-1" style="height:140px;">
                    @foreach($harian as $key => $d)
                        @php $tinggi = $maksHarian > 0 ? max(2, (int) round($d['pemasukan'] / $maksHarian * 100)) : 2; @endphp
                        <div class="flex-fill rounded-top {{ $d['pemasukan'] > 0 ? 'bg-danger' : 'bg-secondary bg-opacity-25' }}"
                             style="height:{{ $tinggi }}%; min-width:4px;"
                             title="{{ \Carbon\Carbon::parse($key)->translatedFormat('d M') }}: {{ $rp($d['pemasukan']) }}"></div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between small text-muted mt-1">
                    <span>1 {{ $namaBulan[$bulan] }}</span>
                    <span>{{ $akhirBulan->daysInMonth }} {{ $namaBulan[$bulan] }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 h-100 border-start border-4 border-success">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold">Proyeksi Bulan Depan</span>
                <h4 class="fw-bold text-success mt-1 mb-1">{{ $rp($proyeksiPemasukan) }}</h4>
                <small class="text-muted d-block mb-2">Perkiraan pemasukan {{ $bulanDepan->translatedFormat('F Y') }}</small>
                <div class="small">
                    <div class="d-flex justify-content-between mb-1"><span>Hari kosong bulan ini</span><strong>{{ $hariKosong }} hari</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span>Rata-rata transaksi/hari</span><strong>{{ $hariTerpakai > 0 ? number_format($jumlahTransaksi / $hariTerpakai, 1, ',', '.') : '0' }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Hari ramai / sepi</span><strong>{{ $hariRamai['nama'] ?? '-' }} / {{ $hariSepi['nama'] ?? '-' }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- PRODUK TERLARIS + KONTRIBUSI KATEGORI --}}
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-trophy me-2 text-warning"></i>10 Produk Terlaris</h6></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr><th class="text-center" style="width:36px;">#</th><th>Produk</th><th class="text-end">Terjual</th><th class="text-end">Omzet</th><th class="text-end">Kontribusi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topProduk as $i => $p)
                        <tr>
                            <td class="text-center fw-bold {{ $i < 3 ? 'text-danger' : 'text-muted' }}">{{ $i + 1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $p->nama_barang }}</div>
                                <small class="text-muted">{{ $p->nama_kategori ?? 'Tanpa kategori' }} &bull; stok {{ $p->stok }}</small>
                            </td>
                            <td class="text-end">{{ $p->terjual }} pcs</td>
                            <td class="text-end fw-semibold">{{ $rp($p->omzet) }}</td>
                            <td class="text-end">
                                @php $kontribusi = $omzetTeratas > 0 ? $p->omzet / $omzetTeratas * 100 : 0; @endphp
                                <div class="progress {{ $kontribusi >= 15 ? 'bg-danger-subtle' : '' }}" style="height:16px; min-width:70px;">
                                    <div class="progress-bar {{ $kontribusi >= 15 ? 'bg-danger' : 'bg-secondary' }} fw-semibold" style="width:{{ max(3, $kontribusi) }}%;">{{ number_format($kontribusi, 0) }}%</div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada penjualan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-layer-group me-2 text-primary"></i>Kontribusi Kategori</h6></div>
            <div class="card-body">
                @forelse($perKategori as $k)
                @php $pc = $omzetTeratas > 0 ? min(100, $k->omzet / max(1, $perKategori->sum('omzet')) * 100) : 0; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-semibold">{{ $k->nama_kategori }}</span>
                        <span class="text-muted">{{ $rp($k->omzet) }} ({{ number_format($pc, 0) }}%)</span>
                    </div>
                    <div class="progress" style="height:10px;">
                        <div class="progress-bar bg-primary" style="width:{{ max(2, $pc) }}%;"></div>
                    </div>
                    <small class="text-muted">{{ $k->terjual }} pcs terjual</small>
                </div>
                @empty
                <p class="text-muted small mb-0">Belum ada data kategori untuk periode ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
{{-- RENCANA RESTOCK --}}
<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-box-open me-2 text-danger"></i>Rencana Restock — Proyeksi Kapan Stok Habis</h6>
        <small class="text-muted">Dihitung dari laju jual 30 hari terakhir. Siapkan pesanan sebelum batas waktu!</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Produk</th><th class="text-end">Stok</th><th class="text-end">Min.</th>
                    <th class="text-end">Laju/Hari</th><th class="text-center">Stok Habis ±</th>
                    <th class="text-end">Saran Beli</th><th class="text-end">Estimasi Modal</th><th class="text-center">Prioritas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rencanaRestock as $r)
                @php
                    $badge = $r['sisa_hari'] <= 7 ? ['bg-danger', 'SANGAT DARURAT'] : ($r['sisa_hari'] <= 14 ? ['bg-warning text-dark', 'SEGERA'] : ['bg-secondary', 'PANTAU']);
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $r['nama'] }}</td>
                    <td class="text-end {{ $r['stok'] <= $r['minimum'] ? 'text-danger fw-bold' : '' }}">{{ $r['stok'] }}</td>
                    <td class="text-end text-muted">{{ $r['minimum'] }}</td>
                    <td class="text-end">{{ $r['laju'] }}</td>
                    <td class="text-center">
                        @if($r['sisa_hari'] >= 999)
                            <span class="text-muted">—</span>
                        @else
                            <strong>{{ $r['sisa_hari'] }} hari</strong>
                            <div class="small text-muted">{{ \Carbon\Carbon::today()->addDays(max(1, $r['sisa_hari']))->translatedFormat('d M') }}</div>
                        @endif
                    </td>
                    <td class="text-end fw-bold text-primary">{{ $r['saran_qty'] > 0 ? '+' . $r['saran_qty'] . ' unit' : '—' }}</td>
                    <td class="text-end">{{ $r['est_modal'] > 0 ? $rp($r['est_modal']) : '—' }}</td>
                    <td class="text-center"><span class="badge {{ $badge[0] }}" style="font-size:10px;">{{ $badge[1] }}</span></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada produk yang perlu dipantau — semua stok aman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- POLA HARI + METODE BAYAR --}}
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-calendar-week me-2 text-primary"></i>Pola Hari Ramai &amp; Sepi</h6></div>
            <div class="card-body">
                <div class="row g-2 text-center">
                    @foreach($polaHari as $ph)
                    <div class="col">
                        <div class="border rounded-3 py-2 h-100 {{ $hariRamai && $ph['nama'] === $hariRamai['nama'] ? 'border-danger border-2' : '' }}">
                            <div class="small fw-bold {{ $hariRamai && $ph['nama'] === $hariRamai['nama'] ? 'text-danger' : 'text-dark' }}">{{$ph['nama']}}</div>
                            <div class="small fw-bold">{{ $rp($ph['rata']) }}</div>
                            <small class="text-muted" style="font-size:10px;">{{ number_format($ph['rata_trx'], 1, ',', '.') }} trx/hari</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="alert alert-light border small mt-3 mb-0">
                    <i class="fa-solid fa-lightbulb text-warning me-1"></i>
                    Hari <strong class="text-danger">{{ $hariRamai['nama'] ?? '-' }}</strong> paling ramai (rata-rata {{ $rp($hariRamai['rata'] ?? 0) }})
                    — pasok &amp; siapkan tenaga maksimal di hari tersebut.
                    {{ $hariSepi ? ' Hari ' . $hariSepi['nama'] . ' paling sepi — waktu tepat untuk promo atau cek stok gudang.' : '' }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="fa-solid fa-wallet me-2 text-success"></i>Metode Pembayaran</h6></div>
            <div class="card-body">
                @php $totalMetode = max(1, $perMetode->sum('omzet')); @endphp
                @forelse($perMetode as $m)
                @php $pm = $m->omzet / $totalMetode * 100; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-semibold text-uppercase">{{ $m->metode_bayar ?? 'lainnya' }}</span>
                        <span class="text-muted">{{ $m->jml }} trx &bull; {{ $rp($m->omzet) }} ({{ number_format($pm, 0) }}%)</span>
                    </div>
                    <div class="progress" style="height:10px;">
                        <div class="progress-bar {{ in_array($m->metode_bayar, ['qris', 'transfer']) ? 'bg-info' : 'bg-success' }}" style="width:{{ max(2, $pm) }}%;"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted small mb-0">Belum ada transaksi periode ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
{{-- RENCANA & REKOMENDASI --}}
<div class="card shadow-sm border-0 rounded-3 mb-4 border-start border-4 border-dark">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-list-check me-2 text-dark"></i>Rencana &amp; Rekomendasi ke Depan</h6>
        <small class="text-muted">Diurutkan berdasarkan prioritas — tindakan yang sebaiknya dipersiapkan sekarang.</small>
    </div>
    <div class="card-body">
        @php
            $warnaPrioritas = [
                'tinggi' => ['bg-danger', 'PRIORITAS TINGGI', 'fa-triangle-exclamation'],
                'sedang' => ['bg-warning text-dark', 'PERHATIAN', 'fa-circle-exclamation'],
                'rendah' => ['bg-success', 'INFO', 'fa-circle-info'],
            ];
        @endphp
        <div class="list-group list-group-flush">
            @foreach($rekomendasi as $rec)
            @php $w = $warnaPrioritas[$rec['prioritas']] ?? $warnaPrioritas['rendah']; @endphp
            <div class="list-group-item px-0 py-3 d-flex gap-3">
                <div class="flex-shrink-0">
                    <span class="badge {{ $w[0] }} rounded-circle d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                        <i class="fa-solid {{ $rec['ikon'] }}"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <strong>{{ $rec['judul'] }}</strong>
                        <span class="badge {{ $w[0] }} align-self-start" style="font-size:10px;">{{ $w[1] }}</span>
                    </div>
                    <div class="text-muted small mt-1">{!! nl2br(e($rec['pesan'])) !!}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="small text-muted mb-2">
    <i class="fa-solid fa-circle-info"></i> Analisis dibangun dari data penjualan, detail barang (harga beli &amp; stok),
    dan konfigurasi payment gateway berjalan. Proyeksi memakai rata-rata bergerak — gunakan sebagai acuan perencanaan, bukan kepastian.
</div>
@endsection