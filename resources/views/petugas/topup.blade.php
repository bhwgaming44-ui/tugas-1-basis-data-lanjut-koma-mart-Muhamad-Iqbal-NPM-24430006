@extends('layouts.app')

@section('content')
<style>
    .pt-hero{background:linear-gradient(135deg,#7f1d1d,#c8102e 60%,#ef4444);border-radius:18px;
        padding:22px 26px;color:#fff;display:flex;justify-content:space-between;align-items:center;
        flex-wrap:wrap;gap:12px}
    .pt-card{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:20px}
    .pt-item{border:1px solid #eef1f5;border-radius:14px;padding:14px 16px;margin-bottom:10px;
        background:#fff;transition:.15s}
    .pt-item:hover{border-color:#fecaca;box-shadow:0 6px 18px rgba(200,16,46,.07)}
    .pt-nominal{font-size:20px;font-weight:900;color:#c8102e;line-height:1}
    .pt-pill{display:inline-block;border-radius:20px;padding:4px 12px;font-size:11px;font-weight:800}
    .pt-tab{display:flex;gap:6px;flex-wrap:wrap}
    .pt-tab button{border:1px solid #d1d5db;background:#fff;border-radius:10px;padding:7px 14px;
        font-size:13px;font-weight:700;color:#4b5563;cursor:pointer}
    .pt-tab button.aktif{background:#fef2f2;border-color:#c8102e;color:#c8102e}
</style>

<div class="pt-hero mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-circle-arrow-up me-2"></i>Verifikasi Top Up</h4>
        <small style="opacity:.9">Setujui pengajuan member, atau catat top up tunai yang saldonya masuk instan.</small>
    </div>
    <div class="text-end">
        <small style="opacity:.85">Menunggu diproses</small>
        <div class="fw-bold" style="font-size:26px">{{ $menunggu->count() }}</div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-danger"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row g-4">
    {{-- ================= DAFTAR PENGAJUAN ================= --}}
    <div class="col-lg-7">
        <div class="pt-card">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-inbox me-1 text-danger"></i> Pengajuan Masuk</h6>
                <div class="pt-tab" id="ptTab">
                    <button type="button" class="aktif" data-filter="semua">Semua</button>
                    <button type="button" data-filter="menunggu_pembayaran">Belum Dibayar</button>
                    <button type="button" data-filter="menunggu_verifikasi">Perlu Diverifikasi</button>
                </div>
            </div>

            @forelse($menunggu as $t)
                <div class="pt-item" data-status="{{ $t->status }}">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div>
                            <div class="fw-bold">{{ $t->anggota->nama ?? 'Member #' . $t->id_anggota }}</div>
                            <small class="text-muted">
                                {{ $t->anggota->kode_member ?? '-' }} &bull;
                                {{ $t->kode_bayar }} &bull;
                                {{ $t->created_at->format('d M Y H:i') }}
                            </small>
                            <div class="mt-2">
                                <span class="pt-pill bg-{{ $t->warnaStatus() }} text-white">{{ $t->labelStatus() }}</span>
                                <span class="pt-pill bg-light text-dark text-uppercase">{{ $t->metode }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="pt-nominal">Rp {{ number_format($t->nominal, 0, ',', '.') }}</div>
                            <small class="text-muted d-block mt-1">
                                @if($t->anggota)
                                    Saldo: Rp {{ number_format($t->anggota->saldo_kopma ?? 0, 0, ',', '.') }}
                                @endif
                            </small>
                        </div>
                    </div>

                    @if($t->catatan)
                        <small class="text-muted d-block mt-2"><i class="fa-solid fa-note-sticky me-1"></i>{{ $t->catatan }}</small>
                    @endif

                    <div class="d-flex gap-2 mt-3 flex-wrap">
                        <form action="{{ route('petugas.topup.setujui', $t->id) }}" method="POST" class="d-flex gap-2 flex-grow-1">
                            @csrf
                            <input type="text" name="catatan" class="form-control form-control-sm"
                                   placeholder="Catatan verifikasi (opsional)" maxlength="200">
                            <button type="submit" class="btn btn-danger btn-sm fw-bold text-nowrap">
                                <i class="fa-solid fa-check me-1"></i> Setujui
                            </button>
                        </form>
                        <form action="{{ route('petugas.topup.tolak', $t->id) }}" method="POST"
                              onsubmit="return confirm('Tolak pengajuan {{ $t->kode_bayar }}?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold">
                                <i class="fa-solid fa-xmark me-1"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    <div style="font-size:40px">✅</div>
                    Tidak ada pengajuan yang menunggu. Semua beres.
                </div>
            @endforelse
        </div>
    </div>

    {{-- ================= KANAN: TOP UP TUNAI INSTAN + RIWAYAT ================= --}}
    <div class="col-lg-5">
        <div class="pt-card mb-3">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-money-bill-wave me-1 text-danger"></i> Top Up Tunai Instan</h6>
            <small class="text-muted d-block mb-3">
                Untuk member yang bayar tunai langsung di toko. Saldo masuk <strong>seketika</strong>
                tanpa verifikasi, tapi tetap tercatat di mutasi Kopma Point.
            </small>

            <form action="{{ route('petugas.topup.tunai') }}" method="POST"
                  onsubmit="return confirm('Tambahkan saldo seketika ke member ini?')">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small">Member</label>
                    <select name="id_anggota" class="form-select" required>
                        <option value="">— pilih member —</option>
                        @foreach($anggota as $a)
                            <option value="{{ $a->id_anggota }}">
                                {{ $a->nama }} ({{ $a->kode_member }}) — saldo Rp {{ number_format($a->saldo_kopma ?? 0, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Nominal</label>
                    <div class="d-flex flex-wrap gap-2 mb-2" id="ptNominalBox">
                        @foreach([10000, 25000, 50000, 100000, 200000, 500000] as $n)
                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold"
                                    data-nominal="{{ $n }}">
                                {{ number_format($n, 0, ',', '.') }}
                            </button>
                        @endforeach
                    </div>
                    <input type="number" name="nominal" id="ptNominal" class="form-control"
                           min="10000" max="1000000" placeholder="cth: 75000" required>
                    <small class="text-muted">Min Rp10.000 &bull; Maks Rp1.000.000.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Catatan (opsional)</label>
                    <input type="text" name="catatan" class="form-control" maxlength="200"
                           placeholder="cth: bayar tunai di kasir">
                </div>

                <button type="submit" class="btn btn-danger w-100 fw-bold">
                    <i class="fa-solid fa-bolt me-1"></i> Tambah Saldo Seketika
                </button>
            </form>
        </div>

        <div class="pt-card">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-clock-rotate-left me-1 text-danger"></i> Riwayat Terakhir</h6>

            @forelse($riwayat as $r)
                <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                    <div>
                        <div class="fw-bold small">{{ $r->anggota->nama ?? 'Member #' . $r->id_anggota }}</div>
                        <small class="text-muted d-block">
                            {{ $r->kode_bayar }} &bull; {{ $r->updated_at->format('d M Y H:i') }}
                            @if($r->petugas) &bull; oleh {{ $r->petugas->nama_lengkap ?? $r->petugas->username }}@endif
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="pt-pill bg-{{ $r->warnaStatus() }} text-white">{{ $r->labelStatus() }}</span>
                        <div class="small fw-bold mt-1">Rp {{ number_format($r->nominal, 0, ',', '.') }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted small">Belum ada riwayat verifikasi.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chip nominal -> isi input nominal top up tunai.
    document.querySelectorAll('#ptNominalBox button').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('ptNominal').value = btn.dataset.nominal;
        });
    });

    // Filter daftar pengajuan berdasarkan status.
    document.querySelectorAll('#ptTab button').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#ptTab button').forEach(b => b.classList.remove('aktif'));
            btn.classList.add('aktif');

            const filter = btn.dataset.filter;
            document.querySelectorAll('.pt-item').forEach(function (item) {
                const tampil = (filter === 'semua') || (item.dataset.status === filter);
                item.style.display = tampil ? '' : 'none';
            });
        });
    });
</script>
@endpush
