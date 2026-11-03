@extends('layouts.app')

@section('content')
<style>
    .tb-hero{background:linear-gradient(135deg,#7f1d1d,#c8102e 60%,#ef4444);border-radius:18px;
        padding:22px 26px;color:#fff;display:flex;justify-content:space-between;align-items:center;
        flex-wrap:wrap;gap:12px}
    .tb-card{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:22px}
    .tb-step{display:flex;gap:12px;align-items:flex-start;padding:10px 0;border-bottom:1px dashed #e5e7eb}
    .tb-step:last-child{border-bottom:none}
    .tb-step .n{width:30px;height:30px;border-radius:50%;background:#c8102e;color:#fff;display:flex;
        align-items:center;justify-content:center;font-weight:800;font-size:13px;flex:0 0 auto}
    .tb-rek{display:flex;justify-content:space-between;background:#f8fafc;border-radius:10px;
        padding:10px 14px;margin-bottom:8px;font-size:14px}
    .tb-pill{display:inline-block;border-radius:20px;padding:5px 14px;font-size:12px;font-weight:800}
</style>

<div class="tb-hero mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-circle-arrow-up me-2"></i>Pembayaran Top Up</h4>
        <small style="opacity:.9">
            Kode: <strong>{{ $topup->kode_bayar }}</strong> &bull; Metode:
            <strong class="text-uppercase">{{ $topup->metode }}</strong>
        </small>
    </div>
    <div class="text-end">
        <small style="opacity:.85">Nominal top up</small>
        <div class="fw-bold" style="font-size:26px">Rp {{ number_format($topup->nominal, 0, ',', '.') }}</div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-danger"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row g-4">
    {{-- ================= KIRI: INSTRUKSI PEMBAYARAN ================= --}}
    <div class="col-lg-7">
        <div class="tb-card">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0">Instruksi Pembayaran</h6>
                <span class="tb-pill bg-{{ $topup->warnaStatus() }} text-white" id="tbStatus">
                    {{ $topup->labelStatus() }}
                </span>
            </div>

            @if($topup->status === 'ditolak')
                <div class="alert alert-danger mb-0">
                    <strong>Pengajuan ditolak.</strong><br>
                    <small>{{ $topup->catatan }}</small><br>
                    <a href="{{ route('kopma.point.index') }}" class="btn btn-sm btn-danger mt-2 fw-bold">
                        <i class="fa-solid fa-rotate-left me-1"></i> Ajukan Top Up Lagi
                    </a>
                </div>
            @elseif($topup->status === 'disetujui')
                <div class="text-center py-3">
                    <div style="font-size:52px">✅</div>
                    <h5 class="fw-bold mt-2">Saldo Sudah Masuk!</h5>
                    <p class="text-muted small mb-1">
                        Saldo Kopma Point kamu sekarang:
                        <strong class="text-danger" id="tbSaldo">
                            Rp {{ number_format($anggota->saldo_kopma ?? 0, 0, ',', '.') }}
                        </strong>
                    </p>
                    <a href="{{ route('kopma.point.index') }}" class="btn btn-danger fw-bold px-4 mt-2">
                        <i class="fa-solid fa-coins me-1"></i> Buka Kopma Point
                    </a>
                </div>
            @else
                {{-- -------------------- QRIS DINAMIS -------------------- --}}
                @if($topup->metode === 'qris')
                    <div class="tb-step">
                        <div class="n">1</div>
                        <div class="text-center w-100">
                            @include('partials.qris', ['payload' => $payloadQris, 'ukuran' => 240])
                        </div>
                    </div>
                    <div class="tb-step">
                        <div class="n">2</div>
                        <div>
                            Scan lalu bayar tepat
                            <strong class="text-danger">Rp {{ number_format($topup->nominal, 0, ',', '.') }}</strong>.
                            Nominal otomatis terbaca aplikasi pembayar karena QR-nya <strong>dinamis</strong>.
                        </div>
                    </div>
                    <div class="tb-step">
                        <div class="n">3</div>
                        <div>Setelah uang terkirim, tekan tombol <strong>"Saya Sudah Bayar"</strong> di bawah.</div>
                    </div>

                {{-- -------------------- TRANSFER BANK -------------------- --}}
                @elseif($topup->metode === 'transfer')
                    <div class="tb-step">
                        <div class="n">1</div>
                        <div class="w-100">
                            <strong>Transfer ke salah satu rekening:</strong>
                            <div class="tb-rek mt-2"><span>BCA</span><strong>123 456 7890</strong></div>
                            <div class="tb-rek"><span>BRI</span><strong>0345 01 000 999</strong></div>
                            <div class="tb-rek"><span>Mandiri</span><strong>127 00 99 888 777</strong></div>
                            <small class="text-muted">a/n KOPMA MART</small>
                        </div>
                    </div>
                    <div class="tb-step">
                        <div class="n">2</div>
                        <div>
                            Transfer tepat <strong class="text-danger">
                                Rp {{ number_format($topup->nominal, 0, ',', '.') }}</strong>
                            dengan berita <strong>{{ $topup->kode_bayar }}</strong>.
                        </div>
                    </div>
                    <div class="tb-step">
                        <div class="n">3</div>
                        <div>Tekan tombol <strong>"Saya Sudah Bayar"</strong> di bawah.</div>
                    </div>

                {{-- -------------------- TUNAI DI KASIR -------------------- --}}
                @else
                    <div class="tb-step">
                        <div class="n">1</div>
                        <div>
                            Datang ke kasir KOPMA MART dan sebutkan kode bayar
                            <strong class="text-danger">{{ $topup->kode_bayar }}</strong>.
                        </div>
                    </div>
                    <div class="tb-step">
                        <div class="n">2</div>
                        <div>
                            Bayar tunai <strong class="text-danger">
                                Rp {{ number_format($topup->nominal, 0, ',', '.') }}</strong> ke petugas.
                        </div>
                    </div>
                    <div class="tb-step">
                        <div class="n">3</div>
                        <div>Petugas akan memverifikasi, lalu saldo masuk otomatis.</div>
                    </div>
                @endif

                @if($topup->status === 'menunggu_pembayaran')
                    <form action="{{ route('topup.konfirmasi', $topup->kode_bayar) }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="catatan" value="konfirmasi-website">
                        <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                            <i class="fa-solid fa-circle-check me-1"></i> Saya Sudah Bayar
                        </button>
                    </form>
                    <small class="text-muted d-block mt-2 text-center">
                        Saldo <strong>belum</strong> bertambah sampai petugas memverifikasi pembayaranmu.
                    </small>
                @else
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fa-solid fa-hourglass-half me-1"></i>
                        Konfirmasi sudah dikirim. Menunggu petugas memverifikasi pembayaran —
                        halaman ini terbarui otomatis.
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ================= KANAN: RINGKASAN + PANDUAN ================= --}}
    <div class="col-lg-5">
        <div class="tb-card mb-3">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-receipt me-1"></i> Ringkasan Pengajuan</h6>
            <div class="d-flex justify-content-between small mb-2">
                <span>Kode Bayar</span><strong>{{ $topup->kode_bayar }}</strong>
            </div>
            <div class="d-flex justify-content-between small mb-2">
                <span>Member</span><strong>{{ $anggota->nama }}</strong>
            </div>
            <div class="d-flex justify-content-between small mb-2">
                <span>Metode</span><strong class="text-uppercase">{{ $topup->metode }}</strong>
            </div>
            <div class="d-flex justify-content-between small mb-2">
                <span>Diajukan</span><span>{{ $topup->created_at->format('d M Y H:i') }}</span>
            </div>
            @if($topup->dibayar_pada)
                <div class="d-flex justify-content-between small mb-2">
                    <span>Konfirmasi Bayar</span><span>{{ $topup->dibayar_pada->format('d M Y H:i') }}</span>
                </div>
            @endif
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-6">
                <span>Nominal Top Up</span>
                <span class="text-danger">Rp {{ number_format($topup->nominal, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between small mt-2">
                <span>Saldo saat ini</span>
                <strong id="tbSaldoKanan">Rp {{ number_format($anggota->saldo_kopma ?? 0, 0, ',', '.') }}</strong>
            </div>
            @if($topup->catatan)
                <hr>
                <small class="text-muted d-block">{{ $topup->catatan }}</small>
            @endif
        </div>

        <div class="tb-card">
            <h6 class="fw-bold mb-2"><i class="fa-solid fa-shield-halved me-1 text-danger"></i> Penting</h6>
            <ul class="small text-muted mb-0 ps-3">
                <li>Saldo <strong>baru bertambah setelah petugas memverifikasi</strong> pembayaran.</li>
                <li>Nominal QRIS sudah terkunci di QR dinamis — tidak bisa diubah dari sisi pembeli.</li>
                <li>Pembayaran ditolak? Kamu bisa mengajukan top up ulang dari halaman Kopma Point.</li>
            </ul>
            <a href="{{ route('kopma.point.index') }}" class="btn btn-outline-danger btn-sm fw-bold w-100 mt-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Kopma Point
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ============ POLLING STATUS PEMBAYARAN TOP UP (tiap 4 detik) ============
    const TB_STATUS = "{{ route('topup.status', $topup->kode_bayar) }}";
    let   tbSelesai  = {{ $topup->status === 'disetujui' || $topup->status === 'ditolak' ? 'true' : 'false' }};

    function tbMuatStatus() {
        if (tbSelesai) return;

        fetch(TB_STATUS, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(d => {
                if (!d.ok) return;

                const pill = document.getElementById('tbStatus');
                if (pill) {
                    pill.textContent = d.label;
                    pill.className = 'tb-pill bg-' + d.warna + ' text-white';
                }

                const saldo = document.getElementById('tbSaldoKanan');
                if (saldo) saldo.textContent = d.saldo_teks;

                // Begitu petugas memutuskan, gambar ulang halaman agar instruksi hilang.
                if (d.selesai || d.ditolak) {
                    tbSelesai = true;
                    window.location.reload();
                }
            })
            .catch(() => {});
    }

    setInterval(tbMuatStatus, 4000);
</script>
@endpush
