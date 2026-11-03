<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struktur Organisasi KOPMA MART</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #eef1f5; color: #1e293b; }
        .sheet {
            max-width: 900px; margin: 24px auto; background: #fff; padding: 48px 52px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12); border-radius: 8px; border-top: 8px solid #c8102e;
        }
        .kopma-head { border-bottom: 3px solid #c8102e; padding-bottom: 16px; margin-bottom: 24px; }
        .kopma-logo {
            width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #c8102e, #ef4444); color: #fff; font-size: 28px;
        }
        table thead th { background: #c8102e; color: #fff; font-size: 13px; text-transform: uppercase; letter-spacing: .5px; }
        table td, table th { vertical-align: middle; }
        .role-badge { font-size: 11px; padding: 3px 9px; border-radius: 20px; font-weight: 700; }
        .no-print { position: fixed; top: 18px; right: 18px; z-index: 50; }
        .footer-note { border-top: 1px dashed #cbd5e1; margin-top: 28px; padding-top: 14px; font-size: 12px; color: #64748b; }
        @media print {
            body { background: #fff; }
            .sheet { box-shadow: none; margin: 0; padding: 24px; border-radius: 0; max-width: 100%; }
            .no-print { display: none !important; }
            @page { margin: 14mm; }
        }
    </style>
</head>
<body>
    <div class="no-print d-flex gap-2">
        <button onclick="window.print()" class="btn btn-danger fw-bold shadow-sm"><i class="fa-solid fa-print me-1"></i> Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn btn-outline-secondary fw-bold">Tutup</button>
    </div>

    <div class="sheet">
        <div class="kopma-head d-flex align-items-center gap-3">
            <div class="kopma-logo"><i class="fa-solid fa-store"></i></div>
            <div>
                <h4 class="fw-bold mb-0 text-danger">KOPMA MART - MINIMARKET</h4>
                <div class="text-muted small">Struktur Organisasi &amp; Sumber Daya Manusia &bull; kopma_npm</div>
            </div>
        </div>

        <p class="small text-muted mb-3">
            Dokumen resmi daftar staf dan pegawai pengelola KOPMA MART yang mengelola
            operasional minimarket, pergudangan, keuangan, dan kasir POS.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th style="width:110px;">Role</th>
                        <th style="width:110px;">Username</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawais as $i => $p)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $p->nama_lengkap }}</td>
                        <td>{{ $p->jabatan ?? 'Staf Operasional' }}</td>
                        <td class="text-center">
                            @php
                                $map = ['admin' => 'bg-danger', 'manajer' => 'bg-warning text-dark', 'kasir' => 'bg-danger'];
                            @endphp
                            <span class="role-badge {{ $map[$p->role] ?? 'bg-secondary' }}">{{ strtoupper($p->role) }}</span>
                        </td>
                        <td class="text-center"><code>{{ $p->username }}</code></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer-note d-flex justify-content-between">
            <span>Total: <strong>{{ $pegawais->count() }} orang</strong></span>
            <span>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</span>
        </div>
    </div>

    <script>
        // Auto-buka dialog print saat halaman dibuka (opsional, bisa ditutup user)
        // window.addEventListener('load', () => setTimeout(() => window.print(), 400));
    </script>
</body>
</html>
