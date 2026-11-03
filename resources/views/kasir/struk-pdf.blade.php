<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk KOPMA MART — {{ $penjualan->no_struk }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #eef1f5; color: #1e293b; }
        .struk {
            max-width: 400px; margin: 24px auto; background: #fff; padding: 28px 26px;
            box-shadow: 0 10px 40px rgba(0,0,0,.12); border-radius: 8px; border-top: 8px solid #c8102e;
            font-size: 14px;
        }
        .struk-head { text-align: center; border-bottom: 2px dashed #94a3b8; padding-bottom: 12px; margin-bottom: 14px; }
        .struk table { width: 100%; font-size: 13px; }
        .struk td { padding: 3px 0; vertical-align: top; }
        .dashed { border-top: 2px dashed #94a3b8; margin: 12px 0; }
        .total-row td { font-weight: 800; font-size: 15px; }
        .no-print { position: fixed; top: 18px; right: 18px; z-index: 50; display: flex; gap: 8px; }
        @media print {
            body { background: #fff; }
            .struk { box-shadow: none; margin: 0; padding: 0; max-width: 100%; border-radius: 0; }
            .no-print { display: none !important; }
            @page { margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-danger fw-bold shadow-sm"><i class="fa-solid fa-print me-1"></i> Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn btn-outline-secondary fw-bold">Tutup</button>
    </div>

    <div class="struk">
        <div class="struk-head">
            <div style="font-size:22px; color:#c8102e;"><i class="fa-solid fa-store"></i></div>
            <h5 class="fw-bold mb-0 text-danger">KOPMA MART</h5>
            <div class="small text-muted">Minimarket Koperasi Mahasiswa</div>
            <div class="small text-muted mt-1">No. Struk: <strong>{{ $penjualan->no_struk }}</strong></div>
            <div class="small text-muted">{{ \Carbon\Carbon::parse($penjualan->tanggal_transaksi)->format('d/m/Y H:i') }}</div>
        </div>

        <table>
            <tr><td>Kasir</td><td>: {{ $penjualan->kasir->nama_lengkap ?? '-' }}</td></tr>
            <tr><td>Pelanggan</td><td>: {{ $penjualan->anggota->nama ?? 'Pelanggan Umum' }}</td></tr>
        </table>

        <div class="dashed"></div>

        <table>
            @foreach($penjualan->details as $d)
            <tr>
                <td>{{ $loop->iteration }}. {{ $d->barang->nama_barang ?? '-' }}<br>
                    <span class="text-muted">&nbsp;&nbsp;&nbsp;{{ $d->jumlah }} x Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</span>
                </td>
                <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>

        <div class="dashed"></div>

        <table>
            <tr><td>Total</td><td class="text-end">Rp {{ number_format($penjualan->total_bruto, 0, ',', '.') }}</td></tr>
            @if($penjualan->total_diskon > 0)
            <tr><td>Diskon Member</td><td class="text-end text-danger">- Rp {{ number_format($penjualan->total_diskon, 0, ',', '.') }}</td></tr>
            @endif
            <tr class="total-row"><td>TAGIHAN</td><td class="text-end text-danger">Rp {{ number_format($penjualan->total_netto, 0, ',', '.') }}</td></tr>
            <tr><td>Bayar ({{ strtoupper($penjualan->metode_bayar) }})</td><td class="text-end">Rp {{ number_format($penjualan->nominal_bayar, 0, ',', '.') }}</td></tr>
            <tr><td>Kembali</td><td class="text-end">Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</td></tr>
        </table>

        <div class="dashed"></div>
        <div class="text-center small text-muted">
            Terima kasih sudah belanja di KOPMA MART.<br>Struk ini dapat dibagikan ke WhatsApp tanpa nomor tujuan.
        </div>
    </div>
</body>
</html>