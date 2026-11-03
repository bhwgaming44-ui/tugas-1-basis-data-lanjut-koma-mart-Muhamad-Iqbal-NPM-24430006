@extends('layouts.app')

@section('content')
<div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Riwayat &amp; Cetak Struk</h3>
        <p class="text-secondary mb-0">Pilih transaksi untuk mencetak ulang struk langsung ke printer thermal (ESC/POS).</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="{{ route('kasir.index') }}" class="btn btn-danger fw-semibold"><i class="fa-solid fa-cash-register me-1"></i> Kembali ke Kasir</a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No Struk</th>
                        <th>Tanggal</th>
                        <th>Member</th>
                        <th>Kasir</th>
                        <th class="text-end">Total Netto</th>
                        <th>Metode</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $trx)
                        <tr>
                            <td><code class="fw-bold">{{ $trx->no_struk }}</code></td>
                            <td><small>{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y H:i') }}</small></td>
                            <td>{{ $trx->anggota ? $trx->anggota->nama : 'Pelanggan Umum' }}</td>
                            <td><small>{{ $trx->kasir->nama_lengkap ?? '-' }}</small></td>
                            <td class="text-end fw-bold text-danger">Rp {{ number_format($trx->total_netto, 0, ',', '.') }}</td>
                            <td><span class="badge bg-secondary text-uppercase">{{ $trx->metode_bayar }}</span></td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('kasir.struk', $trx->id_penjualan) }}" class="btn btn-sm btn-outline-danger fw-semibold" title="Cetak Struk">
                                        <i class="fa-solid fa-print me-1"></i> Cetak
                                    </a>
                                    <form action="{{ route('kasir.riwayat.hapus', $trx->id_penjualan) }}" method="POST" class="m-0"
                                          onsubmit="return confirm('Hapus struk {{ $trx->no_struk }}? Tindakan ini tidak bisa dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary fw-semibold" title="Hapus Struk">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($penjualans->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $penjualans->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection