<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mitra / Kurir | KOPMA MART</title>
    <meta name="theme-color" content="#7f1d1d">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            min-height: 100vh; margin: 0; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #7f1d1d 0%, #c8102e 50%, #dc2626 100%);
            padding: 20px;
        }
        .login-card {
            background: #fff; border-radius: 20px; padding: 40px 36px; width: 100%; max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35); border-top: 6px solid #dc2626;
        }
        .brand-badge {
            width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #7f1d1d, #ef4444); color: #fff; font-size: 30px; margin: 0 auto 16px;
            box-shadow: 0 8px 20px rgba(200,16,46,0.4);
        }
        .form-control { border-radius: 12px; padding: 12px 14px; border: 2px solid #e2e8f0; }
        .form-control:focus { border-color: #c8102e; box-shadow: 0 0 0 3px rgba(200,16,46,.12); }
        .btn-login {
            background: linear-gradient(135deg, #7f1d1d, #ef4444); color: #fff; border: none; border-radius: 12px;
            padding: 12px; font-weight: 700; width: 100%; transition: transform .15s;
        }
        .btn-login:hover { transform: translateY(-2px); color: #fff; }
        .hint { background: #fef2f2; border: 1px dashed #dc2626; border-radius: 10px; padding: 10px 12px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-3">
            <div class="brand-badge"><i class="fa-solid fa-motorcycle"></i></div>
            <h4 class="fw-bold mb-1">Login Mitra / Kurir</h4>
            <p class="text-muted small mb-0">KOPMA MART &bull; Antar Pesanan ke Rumah</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-danger py-2 small">
                <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('mitra.login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold small">Username Kurir</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" name="username" class="form-control border-start-0" value="{{ old('username') }}" placeholder="masukkan username kurir" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="masukkan password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-login mb-3">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk sebagai Kurir
            </button>
        </form>

        <div class="hint text-center">
            <i class="fa-solid fa-circle-info text-danger me-1"></i>
            Halaman ini <strong>khusus kurir</strong> dan terpisah dari Login Petugas — keduanya bisa login bersamaan.
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('toko.index') }}" class="text-decoration-none small text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Toko
            </a>
            <span class="text-muted small mx-1">&bull;</span>
            <a href="{{ route('kasir.login') }}" class="text-decoration-none small text-muted">
                Login Petugas
            </a>
        </div>
    </div>
</body>
</html>
