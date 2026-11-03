@extends('layouts.app')

@section('content')
<style>
    .pay-hero{background:linear-gradient(135deg,#c8102e,#ef4444);border-radius:18px;padding:22px 26px;color:#fff}
    .pay-card{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:22px}
    .pay-step{display:flex;gap:12px;align-items:flex-start;padding:10px 0;border-bottom:1px dashed #e5e7eb}
    .pay-step .n{width:30px;height:30px;border-radius:50%;background:#c8102e;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex:0 0 auto}
    .rekening{display:flex;justify-content:space-between;background:#f8fafc;border-radius:10px;padding:10px 14px;margin-bottom:8px;font-size:14px}
    .qr-box{width:150px;height:150px;background:repeating-conic-gradient(#000 0% 25%, #fff 0% 50%) 50%/18px 18px;border-radius:10px;margin:0 auto}
    .status-pill{display:inline-block;border-radius:20px;padding:5px 14px;font-size:12px;font-weight:800}
</style>

<div class="pay-hero mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="fa-solid fa-credit-card me-2"></i>Pembayaran Pesanan</h4>
        <small style="opacity:.9">No. Struk: <strong>{{ $penjualan->no_struk }}</strong> &bull; Bayar langsung lewat website, tanpa WhatsApp</small>
    </div>
    <a href="#" class="btn btn-outline-light btn-sm fw-bold"
       onclick="if (history.length > 1) { history.back(); } else { window.location.href = '{{ route('dashboard') }}'; } return false;">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="pay-card">
            @if($penjualan->status_bayar === 'lunas')
                <div class="text-center py-3">
                    <div class="status-pill" style="background:#fee2e2;color:#c8102e"><i class="fa-solid fa-circle-check me-1"></i> SUDAH LUNAS</div>
                    <h5 class="fw-bold mt-3">Terima kasih! Pembayaran diterima.</h5>
                    <p class="text-muted small">Pesanan diproses{{ $penjualan->metode_antar === 'antar' ? ' & diantar ke rumah kamu' : '' }}. Lacak statusnya di "Pesanan Saya".</p>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-danger fw-bold px-4"><i class="fa-solid fa-box me-1"></i> Lacak Pesanan</a>
                </div>
            @else
                <h6 class="fw-bold mb-3">Instruksi Pembayaran</h6>

                @if($penjualan->metode_bayar === 'transfer')
                    <div class="pay-step"><div class="n">1</div><div><strong>Transfer ke salah satu rekening:</strong>
                        <div class="rekening mt-2"><span>BCA</span><strong>123 456 7890</strong></div>
                        <div class="rekening"><span>BRI</span><strong>0345 01 000 999</strong></div>
                        <div class="rekening"><span>Mandiri</span><strong>127 00 99 888 777</strong></div>
                        <small class="text-muted">a/n KOPMA MART</small>
                    </div></div>
                    <div class="pay-step"><div class="n">2</div><div>Transfer sejumlah <strong class="text-danger">Rp {{ number_format($penjualan->total_netto, 0, ',', '.') }}</strong></div></div>
                    <div class="pay-step"><div class="n">3</div><div>Klik tombol <strong>"Saya Sudah Bayar"</strong> di bawah.</div></div>
                @elseif($penjualan->metode_bayar === 'qris')
                    <div class="pay-step"><div class="n">1</div><div class="text-center w-100">
                        <div class="qr-box mb-2"></div>
                        <small class="text-muted">Scan QRIS dengan aplikasi bank / e-wallet apa pun (GoPay, OVO, DANA, ShopeePay).</small>
                    </div></div>
                    <div class="pay-step"><div class="n">2</div><div>Bayar <strong class="text-danger">Rp {{ number_format($penjualan->total_netto, 0, ',', '.') }}</strong> lalu klik <strong>"Saya Sudah Bayar"</strong>.</div></div>
                @elseif($penjualan->metode_bayar === 'midtrans')
                    <div class="pay-step"><div class="n">1</div><div>Bayar <strong class="text-danger">Rp {{ number_format($penjualan->total_netto, 0, ',', '.') }}</strong> lewat popup Midtrans (QRIS, Virtual Account, GoPay, OVO, DANA, kartu kredit, Indomaret/Alfamart).</div></div>
                    <div class="pay-step"><div class="n">2</div><div>Klik tombol <strong>"Bayar Sekarang (Midtrans)"</strong> di bawah untuk membuka popup pembayaran.</div></div>
                    <div class="pay-step"><div class="n">3</div><div>Selesaikan pembayaran. Status otomatis diperbarui oleh Midtrans — tidak perlu konfirmasi manual.</div></div>
                @else
                    <div class="pay-step"><div class="n">1</div><div>Siapkan <strong class="text-danger">Rp {{ number_format($penjualan->total_netto, 0, ',', '.') }}</strong> tunai.</div></div>
                    <div class="pay-step"><div class="n">2</div><div>{{ $penjualan->metode_antar === 'antar' ? 'Bayar ke kurir saat pesanan sampai di rumah.' : 'Bayar saat ambil pesanan di toko.' }}</div></div>
                @endif
                {{-- rangkaian langkah per metode sudah ditutup; blok aksi di bawah berlaku untuk SEMUA metode --}}

                @if($penjualan->metode_bayar === 'midtrans')
                    {{-- Midtrans Snap: bayar lewat popup, status otomatis via webhook --}}
                    <div class="mt-3" id="midtrans-box">
                        <button type="button" id="btn-bayar-midtrans" class="btn btn-success w-100 fw-bold py-2">
                            <i class="fa-solid fa-wallet me-1"></i> Bayar Sekarang (Midtrans)
                        </button>
                        <div id="midtrans-msg" class="small mt-2" style="display:none"></div>
                        <button type="button" id="btn-cek-status" class="btn btn-outline-secondary btn-sm w-100 mt-2" style="display:none">
                            <i class="fa-solid fa-rotate me-1"></i> Sudah Bayar? Cek Status Pembayaran
                        </button>
                    </div>
                @elseif($penjualan->metode_bayar !== 'tunai')
                    <form action="{{ route('belanja.konfirmasi', $penjualan->id_penjualan) }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="bukti" value="konfirmasi-website">
                        <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                            <i class="fa-solid fa-circle-check me-1"></i> Saya Sudah Bayar
                        </button>
                    </form>
                @else
                    <a href="{{ route('pesanan.index') }}" class="btn btn-danger w-100 fw-bold py-2 mt-3">
                        <i class="fa-solid fa-box me-1"></i> Selesai & Lacak Pesanan
                    </a>
                @endif

                {{-- ===== GANTI METODE PEMBAYARAN (tanpa input ulang) ===== --}}
                <hr class="my-4">
                <h6 class="fw-bold mb-1"><i class="fa-solid fa-arrow-right-arrow-left me-1 text-danger"></i> Ganti Metode Pembayaran</h6>
                <p class="small text-muted mb-3">Item, alamat & data pesanan tetap tersimpan — pilih metode lain langsung di sini, tanpa perlu input ulang checkout.</p>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (['tunai' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'kopma_point' => 'Kopma Point', 'midtrans' => 'Midtrans'] as $val => $lbl)
                        @if($penjualan->metode_bayar === $val || ($val === 'kopma_point' && $penjualan->metode_bayar === 'koppay'))
                            <span class="btn btn-danger btn-sm fw-bold" style="opacity:1;cursor:default">
                                <i class="fa-solid fa-check me-1"></i>{{ $lbl }} <small>(aktif)</small>
                            </span>
                        @else
                            <form action="{{ route('belanja.metode', $penjualan->id_penjualan) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" name="metode_bayar" value="{{ $val }}"
                                        class="btn btn-outline-danger btn-sm fw-bold"
                                        @if($val === 'kopma_point') onclick="return confirm('Bayar pesanan ini dengan Kopma Point? Saldo akan dipotong Rp {{ number_format($penjualan->total_netto, 0, ',', '.') }} dan pesanan langsung LUNAS.')" @endif>
                                    {{ $lbl }}
                                </button>
                            </form>
                        @endif
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <form action="{{ route('pesanan.batalkan', $penjualan->id_penjualan) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Batalkan pesanan ini? Produk kembali ke stok dan data pesanan dihapus. Tidak ada pembayaran yang dilakukan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-xmark me-1"></i> Batalkan Pesanan
                        </button>
                    </form>
                </div>
            @endif {{-- tutup @if(lunas)/@else: kolom kanan (rincian + peta) selalu tampil --}}
        </div>
    </div>

    <div class="col-lg-5">
        <div class="pay-card">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-receipt me-1"></i> Rincian Pesanan</h6>
            @foreach($penjualan->details as $d)
                <div class="d-flex justify-content-between small mb-1">
                    <span>{{ $d->barang->nama_barang ?? '-' }} x{{ $d->jumlah }}</span>
                    <span>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <hr>
            <div class="d-flex justify-content-between small mb-1"><span>Metode Antar</span><strong>{{ $penjualan->metode_antar === 'antar' ? 'Ke Rumah' : 'Ambil di Toko' }}</strong></div>
            @if($penjualan->alamat_antar)
                <div class="d-flex justify-content-between small mb-1"><span>Alamat</span><span class="text-end" style="max-width:60%">{{ $penjualan->alamat_antar }}</span></div>
            @endif
            <div class="d-flex justify-content-between small mb-1"><span>Pembayaran</span><strong class="text-uppercase">{{ $penjualan->metode_bayar }}</strong></div>
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-6">
                <span>Total</span>
                <span class="text-danger">Rp {{ number_format($penjualan->total_netto, 0, ',', '.') }}</span>
            </div>
            <div class="mt-2">
                <span class="status-pill" style="background:{{ $penjualan->status_bayar === 'lunas' ? '#fee2e2;color:#c8102e' : '#fef3c7;color:#b45309' }}">
                    {{ $penjualan->status_bayar === 'lunas' ? 'LUNAS' : 'BELUM BAYAR' }}
                </span>
            </div>

            @if($penjualan->metode_antar === 'antar' && filled($penjualan->titik_antar))
                <hr>
                <h6 class="fw-bold mb-2"><i class="fa-solid fa-map-location-dot me-1 text-danger"></i> Tujuan Pengantaran</h6>
                <iframe loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                        style="width:100%;height:190px;border:0;border-radius:12px;display:block;background:#e5e7eb"
                        src="https://www.google.com/maps?q={{ rawurlencode($penjualan->titik_antar) }}&output=embed"></iframe>
                @if($penjualan->sudahPunyaTitikAntar())
                    <div class="text-danger mt-1" style="font-size:11px">
                        <i class="fa-solid fa-location-dot me-1"></i> Titik GPS: {{ $penjualan->titik_antar }}
                    </div>
                @endif
                <a class="btn btn-outline-danger btn-sm fw-bold w-100 mt-2"
                   href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($penjualan->titik_antar) }}"
                   target="_blank" rel="noopener">
                    <i class="fa-solid fa-diamond-turn-right me-1"></i> Buka Rute di Google Maps
                </a>
            @endif
        </div>
    </div>
</div>

{{-- ===== MIDTRANS SNAP ===== --}}
@php $mtCfg = \App\Models\PaymentSetting::getMidtransConfig(); @endphp
@if($penjualan->metode_bayar === 'midtrans' && $penjualan->status_bayar !== 'lunas')
<script src="https://app.{{ $mtCfg['environment'] === 'production' ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
        data-client-key="{{ $mtCfg['client_key'] }}"></script>
<script>
(function () {
    const btn  = document.getElementById('btn-bayar-midtrans');
    const msg  = document.getElementById('midtrans-msg');
    const url  = @json(route('midtrans.snap', $penjualan->id_penjualan));
    const csrf = @json(csrf_token());

    function showMsg(text, ok) {
        msg.style.display = 'block';
        msg.className = 'small mt-2 ' + (ok ? 'text-success' : 'text-danger');
        msg.innerHTML = text;
    }

    const statusUrl = @json(route('midtrans.status', $penjualan->id_penjualan));
    const btnCek = document.getElementById('btn-cek-status');
    let pollTimer = null;
    let pollCount = 0;
    // ~6 menit: pembayaran QRIS/VA sering selesai jauh setelah popup ditutup.
    const POLL_MAX = 72;

    function cekSekali() {
        return fetch(statusUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (d) { if (d.paid) { window.location.reload(); } return d; })
            .catch(function () { return null; });
    }

    function pollStatus() {
        // Polling cek status ke API Midtrans tiap 5 detik (bukan simulasi) —
        // status jalan walau webhook tidak terjangkau (misal server lokal).
        if (pollTimer) { return; } // cegah interval ganda (onPending + onClose)
        pollTimer = setInterval(function () {
            if (++pollCount > POLL_MAX) {
                clearInterval(pollTimer); pollTimer = null;
                showMsg('Pembayaran belum terdeteksi otomatis. Kalau kamu SUDAH bayar, klik tombol <strong>Cek Status</strong> di bawah.', false);
                btnCek.style.display = 'block';
                return;
            }
            cekSekali();
        }, 5000);
    }

    btnCek.addEventListener('click', function () {
        btnCek.disabled = true;
        btnCek.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengecek ke Midtrans...';
        cekSekali().then(function (d) {
            btnCek.disabled = false;
            btnCek.innerHTML = '<i class="fa-solid fa-rotate me-1"></i> Cek Lagi';
            if (!d || !d.success) {
                showMsg('Gagal mengecek status. Coba lagi beberapa saat.', false);
            } else {
                showMsg('Masih menunggu pembayaran (<code>' + (d.transaction_status || 'pending') + '</code>). Kalau sudah bayar, tunggu ±1 menit lalu klik Cek Lagi.', false);
            }
        });
    });

    btn.addEventListener('click', function () {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyiapkan pembayaran...';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.paid) {
                // Token lama dipakai ulang tapi transaksinya sudah lunas ->
                // langsung tampilkan kartu LUNAS.
                showMsg('Pembayaran sudah lunas! Memuat ulang...', true);
                setTimeout(function () { window.location.reload(); }, 800);
                return;
            }
            if (!data.success || !data.snap_token) {
                showMsg('Gagal: ' + (data.message || 'token tidak diterima'), false);
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-wallet me-1"></i> Coba Lagi';
                return;
            }
            btn.innerHTML = '<i class="fa-solid fa-wallet me-1"></i> Buka Popup Pembayaran';
            btn.disabled = false;

            if (typeof snap === 'undefined') {
                showMsg('Snap.js belum termuat. Muat ulang halaman.', false);
                return;
            }

            snap.pay(data.snap_token, {
                onSuccess: function (result) {
                    showMsg('Pembayaran berhasil! Memuat ulang...', true);
                    pollStatus();
                },
                onPending: function (result) {
                    showMsg('Menunggu pembayaran. Selesaikan sesuai instruksi — status diperbarui otomatis.', true);
                    btnCek.style.display = 'block';
                    pollStatus();
                },
                onClose: function () {
                    showMsg('Popup ditutup. Klik tombol lagi untuk melanjutkan bayar, atau <strong>Cek Status</strong> bila sudah membayar.', false);
                    btnCek.style.display = 'block';
                    pollStatus();
                },
                onError: function (result) {
                    showMsg('Pembayaran gagal: ' + (result.status_message || 'kesalahan tidak diketahui'), false);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-wallet me-1"></i> Coba Lagi';
                }
            });
        })
        .catch(function (e) {
            showMsg('Gagal menghubungi server: ' + e.message, false);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-wallet me-1"></i> Coba Lagi';
        });
    });

    // "Bayar Sekarang" di keranjang -> popup pembayaran Midtrans otomatis terbuka
    // begitu halaman ini dimuat. URL dibersihkan agar refresh tidak memicu ulang.
    if (new URLSearchParams(window.location.search).get('auto_bayar') === '1') {
        history.replaceState({}, '', window.location.pathname);
        setTimeout(function () { btn.click(); }, 400);
    }
})();
</script>
@endif
@endsection
