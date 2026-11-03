@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="text-center mb-4">
    <h2 class="fw-black text-danger"><i class="fa-solid fa-money-bill-wave me-2"></i>METODE PEMBAYARAN</h2>
    <p class="text-muted">KOPMA MART &bull; Pilih metode pembayaran yang paling mudah untuk Anda.</p>
  </div>

  <div class="row g-3 justify-content-center">
    <!-- Transfer Bank -->
    <div class="col-md-6 col-lg-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius:16px;border-top:4px solid #0061a8">
        <div class="card-body text-center p-4">
          <div class="mb-2" style="font-size:38px;color:#0061a8"><i class="fa-solid fa-building-columns"></i></div>
          <h5 class="fw-bold">Transfer Bank</h5>
          <div class="text-start mt-3 small">
            <div class="d-flex justify-content-between border-bottom py-2"><span class="fw-semibold">BCA</span><span class="text-muted">123 456 7890</span></div>
            <div class="d-flex justify-content-between border-bottom py-2"><span class="fw-semibold">BRI</span><span class="text-muted">0345 01 000 999</span></div>
            <div class="d-flex justify-content-between py-2"><span class="fw-semibold">Mandiri</span><span class="text-muted">127 00 99 888 777</span></div>
          </div>
          <small class="text-muted d-block mt-2">a/n KOPMA MART</small>
        </div>
      </div>
    </div>
    <!-- QRIS -->
    <div class="col-md-6 col-lg-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius:16px;border-top:4px solid #ef4444">
        <div class="card-body text-center p-4">
          <div class="mb-2" style="font-size:38px;color:#ef4444"><i class="fa-solid fa-qrcode"></i></div>
          <h5 class="fw-bold">QRIS</h5>
          <p class="text-muted small mb-3">Scan pakai aplikasi bank / e-wallet apa pun.</p>
          <div class="d-inline-block p-2 bg-white border rounded">
            <div style="width:120px;height:120px;background:repeating-conic-gradient(#000 0% 25%, #fff 0% 50%) 50%/16px 16px"></div>
          </div>
          <small class="text-muted d-block mt-2">NMID: 9360001234567</small>
        </div>
      </div>
    </div>

    <!-- E-Wallet DANA -->
    <div class="col-md-6 col-lg-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius:16px;border-top:4px solid #118eea">
        <div class="card-body text-center p-4">
          <div class="mb-2" style="font-size:38px;color:#118eea"><i class="fa-solid fa-wallet"></i></div>
          <h5 class="fw-bold">DANA</h5>
          <div class="mt-3">
            <div class="bg-light rounded py-2 fw-bold" style="letter-spacing:1px">0812 3456 7890</div>
            <small class="text-muted d-block mt-2">a/n KOPMA MART</small>
          </div>
        </div>
      </div>
    </div>

    <!-- PayPal -->
    <div class="col-md-6 col-lg-4">
      <div class="card border-0 shadow-sm h-100" style="border-radius:16px;border-top:4px solid #003087">
        <div class="card-body text-center p-4">
          <div class="mb-2" style="font-size:38px;color:#003087"><i class="fa-brands fa-paypal"></i></div>
          <h5 class="fw-bold">PayPal</h5>
          <div class="mt-3">
            <div class="bg-light rounded py-2 fw-bold" style="font-size:13px">kopma.hijauputih@paypal.com</div>
            <small class="text-muted d-block mt-2">Untuk pembayaran luar negeri</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Konfirmasi via WhatsApp Bot -->
  <div class="card border-0 shadow-sm mt-4 mx-auto" style="max-width:640px;border-radius:16px;background:linear-gradient(135deg,#128C7E,#075E54)">
    <div class="card-body text-center text-white p-4">
      <div style="font-size:40px"><i class="fa-brands fa-whatsapp"></i></div>
      <h5 class="fw-bold mt-2">Konfirmasi Pembayaran Otomatis</h5>
      <p class="mb-3 small" style="opacity:.9">Konfirmasi lewat chatbot KOPMA MART (tanpa nomor HP), atau kirim bukti transfer via WhatsApp — pilih kontak tujuanmu sendiri.</p>
      <a href="https://wa.me/?text=Halo%20Admin%20KOPMA%2C%20saya%20sudah%20bayar.%20Berikut%20bukti%20transfernya"
         target="_blank" class="btn btn-light fw-bold px-4" style="color:#128C7E;border-radius:30px">
        <i class="fa-brands fa-whatsapp me-1"></i> Chat Bot WhatsApp
      </a>
      <div class="mt-3 small" style="opacity:.85">
        <i class="fa-solid fa-shield-halved me-1"></i> Semua metode pembayaran resmi &amp; aman.
      </div>
    </div>
  </div>

  <div class="text-center mt-3">
    <a href="/" class="btn btn-outline-danger"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda</a>
  </div>
</div>
@endsection
