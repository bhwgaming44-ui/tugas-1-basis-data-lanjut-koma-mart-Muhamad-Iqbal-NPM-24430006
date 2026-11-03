@extends('layouts.app')

@section('content')
<div class="row mb-3 align-items-center">
    <div class="col-md-8">
        <h3 class="fw-bold text-dark"><i class="fa-solid fa-receipt text-danger me-2"></i> Struk Penjualan</h3>
        <p class="text-secondary mb-0">Cetak otomatis langsung ke printer thermal (ESC/POS via USB/Bluetooth) atau cetak via browser.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="{{ route('kasir.riwayat') }}" class="btn btn-outline-secondary fw-semibold"><i class="fa-solid fa-clock-rotate-left me-1"></i> Riwayat</a>
        <a href="{{ route('kasir.index') }}" class="btn btn-danger fw-semibold"><i class="fa-solid fa-cash-register me-1"></i> Kasir (POS)</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-danger py-2"><i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}</div>
@endif

<div class="row g-4">
    <!-- Kolom Kiri: Aksi Cetak -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-print me-2"></i> Cetak Struk Otomatis</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    Struk dikirim <strong>langsung ke mesin printer thermal</strong> memakai perintah ESC/POS.
                    Setelah printer dipilih pertama kali, transaksi berikutnya tercetak <strong>otomatis</strong> tanpa klik.
                </p>
                <div class="d-grid gap-2">
                    <button type="button" id="btnPrintUSB" class="btn btn-danger fw-bold">
                        <i class="fa-brands fa-usb me-2"></i> Pilih &amp; Cetak via USB
                    </button>
                    <button type="button" id="btnPrintBLE" class="btn btn-primary fw-bold">
                        <i class="fa-brands fa-bluetooth-b me-2"></i> Pilih &amp; Cetak via Bluetooth
                    </button>
                    <button type="button" id="btnPrintShare" class="btn btn-danger fw-bold">
                        <i class="fa-solid fa-share-nodes me-2"></i> Cetak / Bagikan (HP Android &amp; iOS)
                    </button>
                    <button type="button" id="btnPrintBrowser" class="btn btn-outline-dark fw-bold">
                        <i class="fa-solid fa-print me-2"></i> Cetak via Browser (Dialog Print)
                    </button>
                    <button type="button" id="btnForgetPrinter" class="btn btn-link text-muted small">
                        Lupakan printer tersimpan
                    </button>
                </div>

                <div class="bg-light border rounded-3 p-3 mt-3">
                    <div class="small text-muted fw-bold text-uppercase mb-1">Status Printer:</div>
                    <div id="printLog" class="small" style="font-family: monospace;">Memuat…</div>
                </div>

                <div class="mt-3 small text-muted">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    <strong>USB &amp; Bluetooth:</strong> Chrome/Edge (Windows &amp; Android) — cetak langsung ke mesin struk thermal.<br>
                    <strong>HP Android/iOS:</strong> pakai tombol <em>Cetak / Bagikan</em> (pilih printer di share sheet).<br>
                    <strong>Cadangan:</strong> tombol Browser (dialog print biasa).
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Preview Struk -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4 bg-white">
                <div class="text-center" id="strukArea">
                    <h5 class="fw-bold mb-0">KOPMA MART</h5>
                    <div class="small text-muted">Minimarket &bull; Belanja Hemat &bull; Buka 07.00-22.00</div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small">
                        <span>No. Struk</span><strong>{{ $penjualan->no_struk }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span>Tanggal</span><span>{{ \Carbon\Carbon::parse($penjualan->tanggal_transaksi)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span>Kasir</span><span>{{ $penjualan->kasir->nama_lengkap ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span>Member</span><span>{{ $penjualan->anggota ? $penjualan->anggota->nama . ' (' . $penjualan->anggota->kode_member . ')' : 'Pelanggan Umum' }}</span>
                    </div>
                    <hr class="my-2">
                    <table class="table table-sm mb-0 small">
                        <thead>
                            <tr><th>Barang</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($penjualan->details as $d)
                            <tr>
                                <td>{{ $d->barang->nama_barang ?? '-' }}</td>
                                <td class="text-center">{{ $d->jumlah }}</td>
                                <td class="text-end">{{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small"><span>Bruto</span><span>Rp {{ number_format($penjualan->total_bruto, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between small text-danger"><span>Diskon Member</span><span>- Rp {{ number_format($penjualan->total_diskon, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between fs-6 fw-bold"><span>TOTAL</span><span>Rp {{ number_format($penjualan->total_netto, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between small"><span>Bayar ({{ strtoupper($penjualan->metode_bayar) }})</span><span>Rp {{ number_format($penjualan->nominal_bayar, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between small"><span>Kembalian</span><span>Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</span></div>
                    <hr class="my-2">
                    <div class="small text-muted">Terima kasih sudah belanja di KOPMA MART!<br>Barang yang sudah dibeli tidak dapat ditukar.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
const STRUK = {!! json_encode([
    'no'        => $penjualan->no_struk,
    'tanggal'   => \Carbon\Carbon::parse($penjualan->tanggal_transaksi)->format('d/m/Y H:i'),
    'kasir'     => $penjualan->kasir->nama_lengkap ?? '-',
    'member'    => $penjualan->anggota ? $penjualan->anggota->nama . ' (' . $penjualan->anggota->kode_member . ')' : 'Pelanggan Umum',
    'items'     => $penjualan->details->map(fn ($d) => [
        'nama' => $d->barang->nama_barang ?? '-', 'qty' => (int) $d->jumlah,
        'harga' => (float) $d->harga_satuan, 'subtotal' => (float) $d->subtotal,
    ])->values()->all(),
    'bruto'     => (float) $penjualan->total_bruto,
    'diskon'    => (float) $penjualan->total_diskon,
    'netto'     => (float) $penjualan->total_netto,
    'bayar'     => (float) $penjualan->nominal_bayar,
    'kembalian' => (float) $penjualan->kembalian,
    'metode'    => strtoupper($penjualan->metode_bayar),
]) !!};

const logEl = document.getElementById('printLog');
const log = (m) => { logEl.textContent = m; console.log('[struk] ' + m); };
const rp  = (n) => Number(n).toLocaleString('id-ID');

/** Susun perintah ESC/POS lengkap (init, teks, potong kertas) */
function buildEscPos() {
    const enc = new TextEncoder();
    const parts = [];
    const push = (s) => parts.push(enc.encode(s));

    parts.push(new Uint8Array([0x1B, 0x40]));   // ESC @  : initialize
    push('\x1B\x61\x01');                       // ESC a 1: center align
    push('\x1B\x45\x01');                       // ESC E 1: bold ON
    push('KOPMA MART\n');
    push('\x1B\x45\x00');                       // bold OFF
    push('Minimarket - Belanja Hemat\nBuka 07.00 - 22.00\n');
    push('--------------------------------\n');
    push('No    : ' + STRUK.no + '\n');
    push('Tgl   : ' + STRUK.tanggal + '\n');
    push('Kasir : ' + STRUK.kasir + '\n');
    push('Member: ' + STRUK.member + '\n');
    push('--------------------------------\n');

    STRUK.items.forEach((it) => {
        push(it.nama.substring(0, 30) + '\n');
        push('  ' + it.qty + ' x ' + rp(it.harga) + '  =  Rp ' + rp(it.subtotal) + '\n');
    });

    push('--------------------------------\n');
    push('BRUTO   : Rp ' + rp(STRUK.bruto) + '\n');
    push('DISKON  : Rp ' + rp(STRUK.diskon) + '\n');
    push('\x1B\x45\x01TOTAL   : Rp ' + rp(STRUK.netto) + '\x1B\x45\x00\n');
    push('BAYAR(' + STRUK.metode + '): Rp ' + rp(STRUK.bayar) + '\n');
    push('KEMBALI : Rp ' + rp(STRUK.kembalian) + '\n');
    push('--------------------------------\n');
    push('Terima kasih sudah belanja\ndi KOPMA MART!\n\n\n\n');
    parts.push(new Uint8Array([0x1D, 0x56, 0x42, 0x00])); // GS V B 0: full cut

    const total = parts.reduce((s, p) => s + p.length, 0);
    const out = new Uint8Array(total);
    let off = 0;
    parts.forEach((p) => { out.set(p, off); off += p.length; });
    return out;
}
/* ========================= PRINTER USB (WebUSB) ========================= */
async function printViaUSB(device) {
    log('Menghubungkan printer USB…');
    await device.open();
    if (!device.configuration) await device.selectConfiguration(1);

    let terkirim = false;
    for (const iface of device.configuration.interfaces) {
        for (const alt of iface.alternates) {
            const out = alt.endpoints.find(e => e.direction === 'out' && e.type === 'bulk');
            if (!out) continue;
            await device.claimInterface(iface.interfaceNumber);
            const bytes = buildEscPos();
            const CH = 16384;
            for (let i = 0; i < bytes.length; i += CH) {
                await device.transferOut(out.endpointNumber, bytes.slice(i, i + CH));
            }
            terkirim = true;
            break;
        }
        if (terkirim) break;
    }
    if (!terkirim) { await device.close(); throw new Error('Endpoint printer USB tidak ditemukan'); }
    await device.close();
    log('✔ Struk terkirim langsung ke printer USB!');
}

document.getElementById('btnPrintUSB').addEventListener('click', async () => {
    try {
        if (!navigator.usb) throw new Error('WebUSB tidak didukung browser ini (pakai Chrome/Edge).');
        const device = await navigator.usb.requestDevice({ filters: [] });
        localStorage.setItem('kopma_printer', 'usb');
        localStorage.setItem('kopma_printer_name', device.productName || 'USB');
        await printViaUSB(device);
    } catch (e) {
        log('✖ USB: ' + e.message);
    }
});

/* ====================== PRINTER BLUETOOTH (Web BLE) ====================== */
const BLE_SERVICES = [
    '49535343-fe1d-48da-b091-151e47488355',   // Serial Port Profile (ESC/POS BLE umum)
    '000018f0-0000-1000-8000-00805f9b34fb',   // Vendor OOB
];

async function sendOverGatt(server, bytes) {
    let service = null;
    for (const uuid of BLE_SERVICES) {
        try { service = await server.getPrimaryService(uuid); if (service) break; } catch (e) { /* coba berikutnya */ }
    }
    // Fallback: coba service pertama yang tersedia (beberapa printer pakai UUID unik)
    if (!service) {
        const services = await server.getPrimaryServices();
        service = services.find(s => s.uuid.includes('49535343') || s.uuid.includes('18f0')) || services[0];
    }
    if (!service) throw new Error('Service printer Bluetooth tidak ditemukan');
    const chars = await service.getCharacteristics();
    const w = chars.find(c => c.properties.write || c.properties.writeWithoutResponse);
    if (!w) throw new Error('Karakteristik tulis tidak ditemukan');
    const CH = 180; // chunk kecil agar stabil di banyak printer thermal BLE
    for (let i = 0; i < bytes.length; i += CH) {
        const chunk = bytes.slice(i, i + CH);
        if (w.properties.writeWithoutResponse) await w.writeValueWithoutResponse(chunk);
        else await w.writeValue(chunk);
        await new Promise(r => setTimeout(r, 20)); // beri jeda agar printer tidak kehilangan data
    }
}

document.getElementById('btnPrintBLE').addEventListener('click', async () => {
    try {
        if (!navigator.bluetooth) throw new Error('Web Bluetooth tidak didukung browser ini (pakai Chrome/Edge).');
        log('Mencari printer Bluetooth…');
        const device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: BLE_SERVICES,
        });
        localStorage.setItem('kopma_printer', 'ble');
        localStorage.setItem('kopma_printer_name', device.name || '');
        log('Menghubungkan ' + (device.name || 'printer') + '…');
        let server = device.gatt.connect ? await device.gatt.connect() : null;
        if (!server || !device.gatt.connected) {
            server = await device.gatt.connect();
        }
        await sendOverGatt(server, buildEscPos());
        log('✔ Struk terkirim langsung ke printer Bluetooth!');
    } catch (e) {
        log('✖ Bluetooth: ' + e.message);
    }
});

/* ============ CETAK / BAGIKAN UNIVERSAL (HP Android & iOS) ============ */
/* Membuat struk berbentuk teks polos yang rapi, lalu:
   - Jika perangkat mendukung Web Share (kebanyakan HP) -> buka share sheet
     (pilih printer / WhatsApp / simpan). Ini jalur paling andal untuk Android & iOS.
   - Jika tidak -> buka halaman struk bersih siap cetak (fallback).                    */
function strukText() {
    let t = '        KOPMA MART\n';
    t += '  Minimarket - Belanja Hemat\n';
    t += '     Buka 07.00 - 22.00\n';
    t += '--------------------------------\n';
    t += 'No    : ' + STRUK.no + '\n';
    t += 'Tgl   : ' + STRUK.tanggal + '\n';
    t += 'Kasir : ' + STRUK.kasir + '\n';
    t += 'Member: ' + STRUK.member + '\n';
    t += '--------------------------------\n';
    STRUK.items.forEach((it) => {
        t += it.nama + '\n';
        t += '  ' + it.qty + ' x ' + rp(it.harga) + '  =  Rp ' + rp(it.subtotal) + '\n';
    });
    t += '--------------------------------\n';
    t += 'BRUTO   : Rp ' + rp(STRUK.bruto) + '\n';
    t += 'DISKON  : Rp ' + rp(STRUK.diskon) + '\n';
    t += 'TOTAL   : Rp ' + rp(STRUK.netto) + '\n';
    t += 'BAYAR(' + STRUK.metode + '): Rp ' + rp(STRUK.bayar) + '\n';
    t += 'KEMBALI : Rp ' + rp(STRUK.kembalian) + '\n';
    t += '--------------------------------\n';
    t += 'Terima kasih sudah belanja\ndi KOPMA MART!\n';
    return t;
}

function bukaStrukBersihLaluPrint() {
    const html = document.getElementById('strukArea').outerHTML;
    const w = window.open('', '_blank', 'width=420,height=760');
    if (!w) { log('✖ Pop-up diblokir. Izinkan pop-up untuk mencetak.'); return; }
    w.document.write(
        '<html><head><title>Struk ' + STRUK.no + '</title>' +
        '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">' +
        '<style>body{font-family:monospace;} @media print{body{-webkit-print-color-adjust:exact;}}</style>' +
        '</head><body class="p-3" style="font-family: monospace;">' + html + '</body></html>'
    );
    w.document.close();
    w.focus();
    setTimeout(() => w.print(), 400);
    log('✔ Halaman struk bersih dibuka untuk dicetak.');
}

document.getElementById('btnPrintShare').addEventListener('click', async () => {
    const teks = strukText();

    // 1) Coba Web Share API (share sheet HP: printer, WhatsApp, simpan file)
    if (navigator.share) {
        try {
            await navigator.share({ title: 'Struk ' + STRUK.no, text: teks });
            log('✔ Struk dibagikan/dicetak lewat menu share.');
            return;
        } catch (e) {
            if (e && e.name === 'AbortError') { log('Dibatalkan.'); return; }
            // Abaikan error lain -> lanjut fallback
        }
    }

    // 2) Coba salin ke clipboard (praktis untuk tempel di WhatsApp/printer app)
    try {
        await navigator.clipboard.writeText(teks);
        log('✔ Struk disalin ke clipboard. Tempel di aplikasi/printer anda.');
        return;
    } catch (e) { /* lanjut */ }

    // 3) Fallback: buka halaman struk bersih siap cetak
    bukaStrukBersihLaluPrint();
});

/* ====================== CETAK VIA BROWSER (CADANGAN) ====================== */
document.getElementById('btnPrintBrowser').addEventListener('click', () => {
    const html = document.getElementById('strukArea').outerHTML;
    const w = window.open('', '_blank', 'width=420,height=760');
    if (!w) { log('✖ Pop-up diblokir browser. Izinkan pop-up untuk mencetak.'); return; }
    w.document.write(
        '<html><head><title>Struk ' + STRUK.no + '</title>' +
        '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">' +
        '</head><body class="p-3" style="font-family: monospace;">' + html + '</body></html>'
    );
    w.document.close();
    w.focus();
    setTimeout(() => w.print(), 600);
    log('✔ Dialog print browser dibuka.');
});

/* ====================== AUTO-CETAK & PRINTER TERSIMPAN ==================== */
document.getElementById('btnForgetPrinter').addEventListener('click', () => {
    localStorage.removeItem('kopma_printer');
    localStorage.removeItem('kopma_printer_name');
    log('Printer tersimpan dilupakan. Pilih printer lagi untuk transaksi berikutnya.');
});

async function autoPrint() {
    const t = localStorage.getItem('kopma_printer');
    try {
        if (t === 'usb' && navigator.usb) {
            const devices = await navigator.usb.getDevices();   // printer yang pernah diizinkan
            if (devices.length) {
                log('Auto-cetak: printer USB tersimpan "' + (devices[0].productName || 'USB') + '"…');
                await printViaUSB(devices[0]);
                return;
            }
        }
        if (t === 'ble' && navigator.bluetooth && navigator.bluetooth.getDevices) {
            const devices = await navigator.bluetooth.getDevices();
            if (devices.length) {
                log('Auto-cetak: mencoba koneksi ' + (devices[0].name || 'printer') + '…');
                let server = devices[0].gatt.connected ? devices[0].gatt : await devices[0].gatt.connect();
                await sendOverGatt(server, buildEscPos());
                log('✔ Struk terkirim otomatis ke printer Bluetooth!');
                return;
            }
        }
        log(t ? 'Printer tersimpan tidak ditemukan — pilih printer ulang.' :
                'Belum ada printer tersimpan — klik tombol pilih printer (sekali saja, berikutnya otomatis).');
    } catch (e) {
        log('Auto-cetak gagal: ' + e.message + ' — klik tombol pilih printer.');
    }
}

document.addEventListener('DOMContentLoaded', autoPrint);
</script>
@endpush
@endsection