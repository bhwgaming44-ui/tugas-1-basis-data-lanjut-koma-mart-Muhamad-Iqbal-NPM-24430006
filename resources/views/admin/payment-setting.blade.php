
@extends('layouts.app')

@section('content')
<div class="pb-5">
    <!-- HEADER -->
    <div class="card shadow-sm border-0 rounded-3 mb-4" style="background: linear-gradient(135deg, #1a0505 0%, #7f1d1d 60%, #c8102e 100%);">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="bg-white/15 rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
                    <i class="fa-solid fa-credit-card text-white" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1 fs-1" style="color: #fff;">Pengaturan Midtrans</h2>
                    <p class="text-white-50 mb-0 small-5" style="opacity: 0.9;">
                        Konfigurasi payment gateway Midtrans untuk fitur QRIS &amp; Transfer Bank.
                        Mudah dari sini, tanpa edit file .env.
                    </p>
                </div>
            </div>
            <div class="mt-3 d-flex flex-wrap gap-2">
                <a href="{{ route('laporan.analisis') }}" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-chart-line me-1"></i> Analisis Laporan
                </a>
                <a href="{{ route('kasir.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-cash-register me-1"></i> Kasir (POS)
                </a>
@php $cfg = \App\Models\PaymentSetting::getMidtransConfig(); @endphp

                <a href="{{ route('admin.payment-setting') }}" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-rotate me-1"></i> Reset Ke Default
                </a>
            </div>
        </div>
    </div>

    <!-- ALERT TES KONEKSI -->
    @if(session('tes_gateway'))
    <div class="alert {{ session('tes_gateway.ok') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show position-relative shadow">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid {{ session('tes_gateway.ok') ? 'fa-circle-check text-success' : 'fa-circle-xmark text-danger' }} fs-4"></i>
            <div>
                {{ session('tes_gateway.pesan') }}
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2" data-bs-dismiss="alert"></button>
    </div>
    @endif


<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 {{$cfg['is_enabled'] ? 'border border-5 border-success bg-white' : 'border border-5 border-secondary bg-soft-mist'}}">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                        <i class="fa-solid fa-wifi {{ $cfg['is_enabled'] ? 'text-success fs-4' : 'text-secondary fs-3' }}"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Status Gateway</span>
                        <h5 class="fw-bold mb-0 mt-1">
                            <span class="badge {{ $cfg['is_enabled'] ? 'bg-success-subtle text-success border-success' : 'bg-secondary-subtle text-secondary border-secondary' }} px-3 py-2 rounded-pill fs-6">
                                {{ $cfg['is_enabled'] ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                        </h5>
                        <small class="text-muted">
                            {{ $cfg['is_enabled'] ? 'Pembayaran online Midtrans siap digunakan.' : 'Pembayaran manual (tunai) saja.' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 border border-5 {{ $cfg['environment'] === 'production' ? 'border-danger bg-soft-mist' : 'border-warning bg-white' }}">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                        <i class="fa-solid {{ $cfg['environment'] === 'production' ? 'fa-microchip text-danger fs-4' : 'fa-flask text-warning fs-4' }}"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Lingkungan</span>
                        <h5 class="fw-bold mb-0 mt-1">
                            <span class="badge {{ $cfg['environment'] === 'production' ? 'bg-danger-subtle text-danger border-danger' : 'bg-warning-subtle text-warning border-warning' }} px-3 py-2 rounded-pill fs-6">
                                {{ strtoupper($cfg['environment']) }}
                            </span>
                        </h5>
                        <small class="text-muted">
                            {{ $cfg['environment'] === 'production'
                                ? 'Transaksi nyata - biaya gateway berlaku.'
                                : 'Mode uji coba - tanpa biaya.' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 border border-5 border-primary bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                        <i class="fa-solid fa-lock text-primary fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Kredensial Tersimpan</span>
                        <h5 class="fw-bold mb-0 mt-1">
                            {{ $setting && $setting->server_key
                                ? '.....' . substr($setting->server_key, -4)
                                : 'Belum diisi' }}
                        </h5>
                        <small class="text-muted">
                            Merchant: {{ $setting?->merchant_id ?? '-' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- SET AUTORISASI FORM -->
    <div class="card shadow border-0 rounded-3 overflow-hidden">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-key me-2"></i>Setelan Otentikasi (Server &amp; Client Key)
            </h5>
        </div>
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('admin.payment-setting.update') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small-6 text-muted">
                            <i class="fa-solid fa-terminal me-1"></i>Server Key
                        </label>
                        <input type="text" name="server_key" value="{{ old('server_key', $setting?->server_key ?? '') }}"
                               class="form-control form-control-lg border-2 rounded-2"
                               placeholder="Masukkan Server Key dari Dashboard Midtrans" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small-6 text-muted">
                            <i class="fa-solid fa-user me-1"></i>Client Key
                        </label>
                        <input type="text" name="client_key" value="{{ old('client_key', $setting?->client_key ?? '') }}"
                               class="form-control form-control-lg border-2 rounded-2"
                               placeholder="Masukkan Client Key dari Dashboard Midtrans" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small-6 text-muted">
                            <i class="fa-solid fa-tags me-1"></i>Merchant ID
                        </label>
                        <input type="text" name="merchant_id" value="{{ old('merchant_id', $setting?->merchant_id ?? '') }}"
                               class="form-control form-control-lg border-2 rounded-2"
                               placeholder="Masukkan Merchant ID" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-uppercase small-6 text-muted">
                            <i class="fa-solid fa-cube me-1"></i>Lingkungan
                        </label>
                        <select name="environment" class="form-select form-select-lg border-2 rounded-2">
                            <option value="sandbox" {{ (old('environment', $setting?->environment ?? 'sandbox')) === 'sandbox' ? 'selected' : '' }}>
                                <i class="fa-solid fa-flask me-1"></i> Sandbox (Mode Uji Coba)
                            </option>
                            <option value="production" {{ (old('environment', $setting?->environment ?? 'sandbox')) === 'production' ? 'selected' : '' }}>
                                <i class="fa-solid fa-microchip me-1"></i> Production (Mode Nyata)
                            </option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_enabled" id="isEnabled" value="1"
                                   {{ $setting?->is_enabled ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isEnabled">
                                <i class="fa-solid fa-toggle-on me-1 me-2" style="color: #c8102e;"></i>
                                Aktifkan Midtrans
                            </label>
                            <div class="form-text">
                                Matikan jika ingin menggunakan pembayaran tunai saja.
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        @if($bolehUbah)
                            <button type="submit" class="btn btn-lg btn-danger fw-bold px-5">
                                <i class="fa-solid fa-save me-2"></i>Simpan Pengaturan
                            </button>
                        @else
                            <button type="submit" class="btn btn-lg btn-outline-secondary fw-bold px-5" disabled>
                                <i class="fa-solid fa-lock me-2"></i>Simpan (khusus admin/manajer)
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- UJI KONEKSI -->
    <div class="card shadow border-0 rounded-3 mt-4 overflow-hidden">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-plug text-success me-2"></i>Uji Koneksi ke API Midtrans
            </h5>
        </div>
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('admin.payment-setting.test') }}" id="formTesKoneksi">
                @csrf
                <input type="hidden" name="server_key" id="tesServerKey" value="{{ old('server_key', $setting?->server_key ?? '') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <select name="environment" id="tesEnvironment" class="form-select border-2 rounded-2">
                            <option value="sandbox">Sandbox (Uji Coba)</option>
                            <option value="production">Production (Nyata)</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary fw-bold px-5">
                            <i class="fa-solid fa-plug me-2"></i>Uji Koneksi
                            <span class="spinner-border spinner-border-sm" role="status" id="tesSpinner" style="display: none;"></span>
                        </button>
                    </div>
                </div>
            </form>
            <p class="text-muted small-6 mt-2">
                <i class="fa-solid fa-info-circle me-1"></i>
                Memvalidasi Server Key langsung ke server Midtrans. Mode <strong>Sandbox</strong> gratis tanpa biaya.
            </p>
        </div>
    </div>

    <!-- INSTRUKSI -->
    <div class="card shadow border-0 rounded-3 mt-4 overflow-hidden">
        <div class="card-header bg-white border-bottom">
            <h5 class="fw-bold mb-0">
                <i class="fa-solid fa-compass me-2" style="color: #c8102e;"></i>Cara Mendapatkan Kunci Midtrans
            </h5>
        </div>
        <div class="card-body p-4 p-md-5">
            <ol class="list-group list-group-flush list-group-numbered">
                <li class="list-group-item border-0 px-0 bg-transparent">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center mt-1" style="width: 36px; height: 36px;">
                            <i class="fa-solid fa-id-card text-primary fs-6"></i>
                        </div>
                        <div>
                            <strong class="fw-bold">Daftar akun</strong> di <a href="https://dashboard.midtrans.com" target="_blank" class="text-primary text-decoration-underline">dashboard.midtrans.com</a> (gratis).
                        </div>
                    </div>
                </li>
                <li class="list-group-item border-0 px-0 bg-transparent">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center mt-1" style="width: 36px; height: 36px;">
                            <i class="fa-solid fa-key text-primary fs-6"></i>
                        </div>
                        <div>
                            <strong class="fw-bold">Ambil Access Keys:</strong> menu <em>Settings -> Access Keys</em>,
                            salin Server Key, Client Key, dan Merchant ID ke formulir di atas.
                        </div>
                    </div>
                </li>
                <li class="list-group-item border-0 px-0 bg-transparent">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center mt-1" style="width: 36px; height: 36px;">
                            <i class="fa-solid fa-flask text-primary fs-6"></i>
                        </div>
                        <div>
                            <strong class="fw-bold">Uji Koneksi</strong> dengan mode Sandbox dulu (tanpa biaya).
                            Setelah berhasil, pindah ke Production saat siap transaksi nyata.
                        </div>
                    </div>
                </li>
                <li class="list-group-item border-0 px-0 bg-transparent">
                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center mt-1" style="width: 36px; height: 36px;">
                            <i class="fa-solid fa-hand-holding-usd text-primary fs-6"></i>
                        </div>
                        <div>
                            <strong class="fw-bold">Hemat biaya:</strong> gunakan SNAP token hanya saat checkout,
                            jangan generate berulang-ulang.
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </div>


    <!-- RESET MODAL -->
    <div class="modal fade" id="resetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Konfirmasi Reset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda akan menghapus semua pengaturan Midtrans yang ada. Apakah Anda yakin ingin melanjutkan?</p>
                    <p class="text-danger small-6 fw-bold"><i class="fa-solid fa-exclamation-triangle me-1"></i>
                        Aksi ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="{{ route('admin.payment-setting.update') }}" style="display: contents;">
                        @csrf
                        <button type="submit" class="btn btn-danger">Ya, Reset Semua</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Copy-to-clipboard for credential badges
document.querySelectorAll('.credential-copy').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        var text = this.dataset.copyText || '';
        if (!text) return;
        navigator.clipboard.writeText(text).then(function() {
            var orig = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-check text-success"></i> Tersalin!';
            setTimeout(function() { this.innerHTML = orig; }, 2000);
        }).catch(function() {
            alert('Gagal menyalin. Silakan salin manual.');
        });
    });
});

// Saved dropdown value to hidden fields for test form
document.querySelectorAll('select[name="environment"]').forEach(function(sel) {
    sel.addEventListener('change', function() {
        var val = this.value;
        document.getElementById('tesEnvironment').value = val;
        var sk = document.querySelector('input[name="server_key"]');
        if (sk) document.getElementById('tesServerKey').value = sk.value;
    });
});

// Form test button loading
var tesForm = document.getElementById('formTesKoneksi');
if (tesForm) {
    tesForm.addEventListener('submit', function() {
        var btn = this.querySelector('button[type="submit"]');
        var spinner = document.getElementById('tesSpinner');
        if (btn && spinner) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Memvalidasi...';
            spinner.style.display = 'inline-block';
        }
    });
}
</script>
@endsection
