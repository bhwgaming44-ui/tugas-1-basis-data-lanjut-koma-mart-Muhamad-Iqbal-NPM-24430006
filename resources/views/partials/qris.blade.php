{{--
  PARTIAL: render payload QRIS menjadi QR code yang BENAR-BENAR bisa discan.
  ------------------------------------------------------------------
  Server ini tidak punya ekstensi GD, jadi gambar QR dibuat di sisi browser
  memakai pustaka qrcodejs (dimuat dari CDN di layouts/app.blade.php).
  Payload-nya sendiri tetap dihasilkan server lewat App\Services\QrisService
  (EMVCo TLV + CRC16), jadi isinya standar QRIS.

  Pemakaian:
      @include('partials.qris', ['payload' => $payloadQris, 'ukuran' => 240])
--}}
@php
    $qrisId     = 'qris-' . substr(md5(($payload ?? '') . microtime()), 0, 8);
    $qrisUkuran = $ukuran ?? 220;
@endphp

<div class="qris-box text-center">
    <div class="qris-frame mx-auto" style="width:{{ $qrisUkuran }}px;height:{{ $qrisUkuran }}px">
        <div id="{{ $qrisId }}" class="qris-target" data-payload="{{ $payload }}"></div>
    </div>

    <div class="mt-2 small text-muted">
        <i class="fa-solid fa-qrcode me-1 text-danger"></i>
        QRIS dinamis &bull; nominal otomatis muncul di aplikasi pembayar
    </div>

    <details class="mt-2 text-start">
        <summary class="small text-danger fw-bold" style="cursor:pointer">Lihat isi payload (EMVCo)</summary>
        <textarea readonly rows="4" class="form-control form-control-sm mt-2"
                  style="font-family:monospace;font-size:11px">{{ $payload }}</textarea>
    </details>
</div>

<style>
    .qris-frame{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:10px;
        display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(200,16,46,.10)}
    .qris-target{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
    .qris-target img,.qris-target canvas{max-width:100%;height:auto !important}
</style>

@push('scripts')
<script>
    // Gambar QR dari payload QRIS. Dipanggil setelah qrcodejs selesai dimuat.
    (function () {
        function gambar(target) {
            const payload = target.dataset.payload;
            if (!payload) return;

            if (typeof QRCode === 'undefined') {
                // CDN belum siap -> coba lagi sebentar.
                return setTimeout(function () { gambar(target); }, 250);
            }

            const lebar = target.parentElement.clientWidth || 220;
            target.innerHTML = '';
            new QRCode(target, {
                text: payload,
                width: lebar,
                height: lebar,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        document.querySelectorAll('.qris-target').forEach(gambar);
    })();
</script>
@endpush
