<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mode === 'daftar' ? 'Daftar' : 'Masuk' }} | KOPMA MART</title>
    <meta name="theme-color" content="#c8102e">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            min-height: 100vh; margin: 0; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, #7f1d1d 0%, #c8102e 45%, #ef4444 100%);
            padding: 20px;
        }
        .join-card { background: #fff; border-radius: 22px; width: 100%; max-width: 430px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,.35); }
        .join-head { background: linear-gradient(135deg, #c8102e, #ef4444); color: #fff; padding: 28px 28px 22px; position: relative; overflow: hidden; }
        .join-head::after { content: ""; position: absolute; right: -40px; top: -40px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,.12); }
        .join-head h4 { font-weight: 800; margin: 10px 0 4px; }
        .brand-badge { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.2); font-size: 26px; border: 1px solid rgba(255,255,255,.35); }
        .join-body { padding: 26px 28px 30px; }
        .form-control { border-radius: 12px; padding: 12px 14px; border: 2px solid #e2e8f0; }
        .form-control:focus { border-color: #c8102e; box-shadow: 0 0 0 3px rgba(200,16,46,.12); }
        .btn-join { background: linear-gradient(135deg, #c8102e, #ef4444); color: #fff; border: none; border-radius: 12px; padding: 12px; font-weight: 800; width: 100%; }
        .btn-join:hover { transform: translateY(-2px); color: #fff; }
        .tab-switch a { font-weight: 700; color: #c8102e; text-decoration: none; }
        .avatar-pick { display: flex; gap: 10px; justify-content: center; margin: 6px 0 14px; }
        .avatar-pick span { width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #fef2f2; border: 2px solid #c8102e; color: #c8102e; font-size: 18px; }
        .avatar-pick span.on { background: #c8102e; color: #fff; }
    </style>
</head>
<body>
    <div class="join-card">
        <div class="join-head">
            <div class="brand-badge"><i class="fa-solid fa-store"></i></div>
            <h4>{{ $mode === 'daftar' ? 'Yuk, Gabung KOPMA MART!' : 'Selamat Datang Kembali' }}</h4>
            <div style="font-size:14px; opacity:.9;">
                {{ $mode === 'daftar' ? 'Daftar gratis, belanja & antar ke rumah tanpa ribet.' : 'Masuk untuk lanjut belanja & lacak pesananmu.' }}
            </div>
        </div>

        <div class="join-body">
            @if($errors->any())
                <div class="alert alert-danger py-2 small mb-3">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            @if($mode === 'daftar')
                <form action="{{ url('/daftar') }}" method="POST">
                    @csrf
                    <div class="avatar-pick">
                        <span class="on"><i class="fa-solid fa-user-astronaut"></i></span>
                        <span><i class="fa-solid fa-user"></i></span>
                        <span><i class="fa-solid fa-face-smile"></i></span>
                        <span><i class="fa-solid fa-graduation-cap"></i></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="cth: Budi Santoso" value="{{ old('nama') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">No. HP (WhatsApp)</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('no_hp') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Email (opsional)</label>
                        <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alamat Rumah (untuk antar)</label>
                        <input type="text" name="alamat" class="form-control" placeholder="cth: Jl. Merdeka No. 10, Kost RT 03" value="{{ old('alamat') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Titik Lokasi Rumah (GPS)</label>
                        <input type="hidden" name="latitude"  id="inpLat" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="inpLng" value="{{ old('longitude') }}">
                        <input type="text" class="form-control" id="teksTitik" readonly
                               value="{{ old('latitude') && old('longitude') ? old('latitude') . ', ' . old('longitude') : '' }}"
                               placeholder="Opsional — tekan tombol di bawah">
                        <button type="button" class="btn btn-outline-danger btn-sm fw-bold w-100 mt-2" onclick="tandaiLokasi()">
                            <i class="fa-solid fa-location-crosshairs me-1"></i> Tandai Lokasi Saya
                        </button>
                        <div id="statusLokasi" class="small mt-2 d-none"></div>
                        <small class="text-muted d-block mt-1" style="font-size:11px">
                            Nyalakan Lokasi/GPS perangkat lalu izinkan akses. Bisa juga ditandai nanti di menu Profil.
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="min. 6 karakter" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Ulangi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-join"><i class="fa-solid fa-user-plus me-1"></i> Daftar & Belanja Sekarang</button>
                </form>
                <div class="text-center mt-3 small tab-switch">
                    Sudah punya akun? <a href="{{ route('pelanggan.login') }}">Masuk di sini</a>
                </div>
            @else
                <form action="{{ url('/login/pelanggan') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama lengkap akun member" value="{{ old('nama') }}" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password kamu" required>
                    </div>
                    <button type="submit" class="btn btn-join"><i class="fa-solid fa-right-to-bracket me-1"></i> Masuk</button>
                </form>
                <div class="text-center mt-3 small tab-switch">
                    Belum punya akun? <a href="{{ route('pelanggan.daftar') }}">Daftar gratis</a>
                </div>
            @endif

            <div class="text-center mt-3">
                <a href="{{ route('toko.index') }}" class="small text-muted text-decoration-none">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Toko
                </a>
            </div>
        </div>
    </div>

    @if($mode === 'daftar')
    <script>
        // ====== TITIK LOKASI RUMAH (GPS perangkat) — opsional saat daftar ======
        // Memakai Geolocation API standar (enableHighAccuracy): titik diambil dari
        // GPS perangkat, jadi bukan alamat teks yang bisa ngawur.
        function pesanLokasi(teks, jenis) {
            var el = document.getElementById('statusLokasi');
            el.className = 'small mt-2 text-' + (jenis || 'muted');
            el.innerHTML = teks;
            el.classList.remove('d-none');
        }

        function tandaiLokasi() {
            if (!navigator.geolocation) {
                pesanLokasi('<i class="fa-solid fa-circle-xmark text-danger me-1"></i> Perangkat ini tidak mendukung GPS. Coba lewat HP.', 'danger');
                return;
            }

            pesanLokasi('<i class="fa-solid fa-spinner fa-spin me-1"></i> Mengambil titik GPS... izinkan akses lokasi bila diminta.', 'muted');

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    var lat = pos.coords.latitude.toFixed(7);
                    var lng = pos.coords.longitude.toFixed(7);
                    document.getElementById('inpLat').value = lat;
                    document.getElementById('inpLng').value = lng;
                    document.getElementById('teksTitik').value =
                        lat + ', ' + lng + '  (±' + Math.round(pos.coords.accuracy) + ' m)';
                    pesanLokasi('<i class="fa-solid fa-circle-check text-danger me-1"></i> Titik GPS terpasang.', 'success');
                },
                function (err) {
                    var pesan = {
                        1: 'Izin lokasi ditolak. Izinkan akses "Lokasi" di browser lalu coba lagi.',
                        2: 'Lokasi tidak tersedia. Nyalakan GPS/Lokasi di perangkat lalu coba lagi.',
                        3: 'Pengambilan lokasi terlalu lama. Coba lagi.'
                    }[err.code] || 'Gagal mengambil lokasi.';
                    pesanLokasi('<i class="fa-solid fa-circle-xmark text-danger me-1"></i> ' + pesan, 'danger');
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        }
    </script>
    @endif
</body>
</html>

