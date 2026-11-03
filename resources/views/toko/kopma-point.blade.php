@extends('layouts.app')

@section('content')
<style>
    .kp-hero{background:linear-gradient(135deg,#7f1d1d,#c8102e,#ef4444);border-radius:18px;padding:24px 28px;color:#fff;position:relative;overflow:hidden}
    .kp-hero::after{content:"";position:absolute;right:-50px;top:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.10)}
    .kp-card{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:20px}
    .kp-nominal{border:1px solid #d1d5db;border-radius:12px;background:#fff;padding:10px 16px;font-size:14px;font-weight:700;color:#374151;cursor:pointer;transition:.15s}
    .kp-nominal:hover{border-color:#c8102e;color:#c8102e}
    .kp-nominal.sel{background:#fef2f2;border-color:#c8102e;color:#c8102e;box-shadow:0 0 0 2px rgba(200,16,46,.18)}
    .kp-mutasi{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px dashed #e5e7eb}
    .kp-mutasi:last-child{border-bottom:none}
</style>

<div class="kp-hero mb-4">
    <h4 class="fw-bold mb-1"><i class="fa-solid fa-coins me-2"></i>Kopma Point</h4>
    <small style="opacity:.9">Top up saldo sekali, belanja online jadi instan &mdash; bayar otomatis dari saldo tanpa transfer manual.</small>
</div>

<div class="row g-4">
    {{-- KIRI: Saldo + Form Top Up --}}
    <div class="col-lg-6">
        <div class="kp-card mb-4" style="background:linear-gradient(135deg,#7f1d1d 0%,#c8102e 65%,#ef4444 130%);color:#fff;border:none">
            <small style="opacity:.85">Saldo Kopma Point kamu</small>
            <div class="fw-bold" style="font-size:30px;line-height:1.2">Rp {{ number_format($anggota->saldo_kopma ?? 0, 0, ',', '.') }}</div>
            <small style="opacity:.8">Member {{ $anggota->kode_member }} &bull; Poin loyalitas: {{ $anggota->poin }}</small>
        </div>

        <div class="kp-card">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-circle-arrow-up me-1 text-danger"></i> Top Up Saldo</h6>

            @if(session('success'))
                <div class="alert alert-danger py-2 small"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('kopma.point.topup') }}" method="POST">
                @csrf
                <label class="form-label fw-bold small">Pilih Nominal</label>
                <div class="d-flex flex-wrap gap-2 mb-3" id="kpNominalBox">
                    @foreach([10000, 25000, 50000, 100000, 250000, 500000] as $n)
                        <button type="button" class="kp-nominal {{ $n === 50000 ? 'sel' : '' }}" data-nominal="{{ $n }}">
                            Rp {{ number_format($n, 0, ',', '.') }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="nominal" id="kpNominalInput" value="50000">

                <div class="mb-3">
                    <label class="form-label fw-bold small">Nominal Lain (opsional)</label>
                    <input type="number" name="nominal_custom" id="kpNominalCustom" class="form-control"
                           min="10000" max="1000000" placeholder="cth: 75000" oninput="kpSyncCustom()">
                    <small class="text-muted">Minimal Rp10.000, maksimal Rp1.000.000 per top up.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Metode Pembayaran Top Up</label>
                    <select name="metode" class="form-select" required>
                        <option value="tunai">Tunai (bayar di kasir toko)</option>
                        <option value="transfer">Transfer Bank (BCA/BRI/Mandiri)</option>
                        <option value="qris">QRIS (Semua E-Wallet)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                    <i class="fa-solid fa-plus me-1"></i> Top Up Sekarang
                </button>
                <small class="text-muted d-block mt-2 text-center">Simulasi: saldo langsung masuk setelah top up dikonfirmasi.</small>
            </form>
        </div>
    </div>

    {{-- KANAN: Cara pakai + Riwayat Mutasi --}}
    <div class="col-lg-6">
        <div class="kp-card mb-4">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-circle-question me-1 text-danger"></i> Cara Pakai</h6>
            <div class="d-flex gap-3 mb-2">
                <div style="width:28px;height:28px;border-radius:50%;background:#c8102e;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex:0 0 auto">1</div>
                <div class="small">Top up saldo lewat form di sebelah kiri (pilih nominal + metode).</div>
            </div>
            <div class="d-flex gap-3 mb-2">
                <div style="width:28px;height:28px;border-radius:50%;background:#c8102e;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex:0 0 auto">2</div>
                <div class="small">Saat checkout di keranjang, pilih metode bayar <strong>"Kopma Point (Saldo)"</strong>.</div>
            </div>
            <div class="d-flex gap-3">
                <div style="width:28px;height:28px;border-radius:50%;background:#c8102e;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex:0 0 auto">3</div>
                <div class="small">Saldo otomatis terpotong & pesanan langsung <strong>lunas</strong> &mdash; tinggal tunggu diantar kurir.</div>
            </div>
        </div>

        <div class="kp-card">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-clock-rotate-left me-1 text-danger"></i> Riwayat Mutasi</h6>
            <small class="text-muted d-block mb-2">30 transaksi terakhir</small>

            @if($mutasi->isEmpty())
                <div class="text-center py-4 text-muted small">
                    <div style="font-size:38px">🪙</div>
                    Belum ada mutasi Kopma Point.
                </div>
            @else
                @foreach($mutasi as $m)
                    <div class="kp-mutasi">
                        <div>
                            <div class="fw-bold small">{{ $m->keterangan ?: ($m->jenis === 'topup' ? 'Top up saldo' : 'Pembayaran') }}</div>
                            <small class="text-muted">{{ $m->created_at->format('d M Y H:i') }}@if($m->metode) &bull; {{ strtoupper($m->metode) }}@endif</small>
                        </div>
                        <span class="fw-bold" style="color:{{ $m->jenis === 'topup' ? '#c8102e' : '#dc2626' }}">
                            {{ $m->jenis === 'topup' ? '+' : '-' }} Rp {{ number_format($m->nominal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pilih chip nominal -> isi hidden input, hapus input custom
    document.querySelectorAll('#kpNominalBox .kp-nominal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#kpNominalBox .kp-nominal').forEach(b => b.classList.remove('sel'));
            btn.classList.add('sel');
            document.getElementById('kpNominalInput').value = btn.dataset.nominal;
            document.getElementById('kpNominalCustom').value = '';
        });
    });
    // Input custom override chip yang dipilih
    function kpSyncCustom() {
        const custom = document.getElementById('kpNominalCustom').value;
        if (custom && parseInt(custom) >= 10000) {
            document.getElementById('kpNominalInput').value = custom;
            document.querySelectorAll('#kpNominalBox .kp-nominal').forEach(b => b.classList.remove('sel'));
        }
    }
</script>
@endpush