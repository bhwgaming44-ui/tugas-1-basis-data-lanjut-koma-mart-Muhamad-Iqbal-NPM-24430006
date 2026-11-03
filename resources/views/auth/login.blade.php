<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk | KOPMA MART</title><meta name="theme-color" content="#c8102e">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*{font-family:'Plus Jakarta Sans',sans-serif;box-sizing:border-box}
body{min-height:100vh;margin:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#7f1d1d,#c8102e 50%,#ef4444);padding:20px}
.login-card{background:#fff;border-radius:24px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 30px 70px rgba(0,0,0,.4)}
.login-head{background:linear-gradient(135deg,#7f1d1d,#ef4444);color:#fff;padding:28px 28px 20px;text-align:center;position:relative;overflow:hidden}
.login-head::after{content:"";position:absolute;right:-40px;top:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.08)}
.mode-slider{display:flex;background:rgba(255,255,255,.18);border-radius:30px;padding:4px;gap:4px;margin-top:16px;backdrop-filter:blur(10px)}
.mode-slider button{flex:1;border:none;border-radius:26px;padding:10px 4px;font-size:12px;font-weight:800;background:transparent;color:#fee2e2;cursor:pointer;transition:all .25s ease}
.mode-slider button.on{background:#fff;color:#c8102e;box-shadow:0 2px 10px rgba(0,0,0,.15)}
.login-body{padding:24px 28px 28px}
.pane{display:none}.pane.on{display:block}
.form-control{border-radius:12px;padding:12px 14px;border:2px solid #e2e8f0;font-size:14px;transition:border-color .2s}
.form-control:focus{border-color:#c8102e;box-shadow:0 0 0 3px rgba(200,16,46,.12)}
.btn-login{background:linear-gradient(135deg,#c8102e,#ef4444);color:#fff;border:none;border-radius:12px;padding:13px;font-weight:800;width:100%;font-size:14px;transition:all .2s;box-shadow:0 4px 15px rgba(200,16,46,.3)}
.btn-login:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(200,16,46,.4);background:linear-gradient(135deg,#991b1b,#dc2626)}

/* Quick Login Cards */
.quick-section{margin-top:16px;padding-top:14px;border-top:2px dashed #fee2e2}
.quick-title{font-size:11px;font-weight:800;color:#991b1b;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;text-align:center}
.quick-card{display:flex;align-items:center;gap:12px;background:#fef2f2;border:1.5px solid #fecaca;border-radius:14px;padding:10px 14px;cursor:pointer;transition:all .2s ease;margin-bottom:8px}
.quick-card:hover{background:#fee2e2;border-color:#f87171;transform:translateX(4px);box-shadow:0 4px 12px rgba(200,16,46,.12)}
.quick-card:active{transform:scale(.97)}
.quick-avatar{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:900;color:#fff;flex-shrink:0}
.quick-avatar.admin{background:linear-gradient(135deg,#7f1d1d,#c8102e)}
.quick-avatar.kasir{background:linear-gradient(135deg,#1d4ed8,#3b82f6)}
.quick-avatar.mitra{background:linear-gradient(135deg,#059669,#10b981)}
.quick-avatar.user{background:linear-gradient(135deg,#d97706,#f59e0b)}
.quick-info{flex:1;min-width:0}
.quick-info .name{font-size:13px;font-weight:800;color:#1f2937;margin:0}
.quick-info .role{font-size:11px;color:#6b7280;margin:0}
.quick-arrow{color:#c8102e;font-size:14px;opacity:.6;transition:all .2s}
.quick-card:hover .quick-arrow{opacity:1;transform:translateX(3px)}
.quick-loading{display:none;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#c8102e}
.quick-loading.show{display:inline-flex}
.quick-loading .spinner-border{width:14px;height:14px;border-width:2px}
</style></head><body><div class="login-card"><div class="login-head">
<h4 class="fw-bold mb-1" style="position:relative;z-index:1"><img src="{{ asset('images/logo-kopma.svg') }}" alt="KOPMA MART" style="width:52px;height:52px;border-radius:50%;background:#fff;padding:2px;vertical-align:middle;margin-right:8px">Masuk KOPMA MART</h4>
<div style="font-size:13px;opacity:.9;position:relative;z-index:1">Pilih mode login — atau klik akun uji coba untuk masuk langsung.</div>
<div class="mode-slider"><button type="button" data-mode="user" class="on"><i class="fa-solid fa-user me-1"></i>User</button><button type="button" data-mode="petugas"><i class="fa-solid fa-user-tie me-1"></i>Petugas</button><button type="button" data-mode="kurir"><i class="fa-solid fa-motorcycle me-1"></i>Kurir</button></div>
</div><div class="login-body">
{{-- PENJELAS SISTEM TERPISAH: 1 tab = 1 peran aktif, pindah via tombol Mode --}}
@php
    $loginPetugas = (bool) session('kasir_id');
    $loginKurir = (bool) session('mitra_id');
    $loginUser = (bool) session('pelanggan_id');
    $modeAktifLogin = session('mode_aktif');
@endphp
@if($loginPetugas || $loginKurir || $loginUser)
<div class="alert alert-info py-2 small mb-3">
    <div class="fw-bold mb-1">Akun tersimpan di device ini:</div>
    <div class="d-flex flex-wrap gap-1">
        @if($loginPetugas)<span class="badge bg-primary">Petugas: {{ session('kasir_nama') }}</span>@endif
        @if($loginKurir)<span class="badge bg-success">Kurir: {{ session('mitra_nama') }}</span>@endif
        @if($loginUser)<span class="badge bg-warning text-dark">User: {{ session('pelanggan_nama') }}</span>@endif
    </div>
    <div class="mt-1 text-muted">Login di bawah akan LANJUT di tab ini &amp; pindah mode otomatis — tanpa buka tab baru, tanpa logout peran lain.</div>
</div>
@endif
@if($errors->any())<div class="alert alert-danger py-2 small mb-3"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}</div>@endif
@if(session('success'))<div class="alert alert-success py-2 small mb-3"><i class="fa-solid fa-check-circle me-1"></i>{{ session('success') }}</div>@endif

{{-- =================== PANE: USER =================== --}}
<div class="pane on" id="pane-user">
<form method="POST" action="{{ url('/login/pelanggan') }}" id="form-user">@csrf
<div class="mb-3"><label class="form-label fw-bold small"><i class="fa-solid fa-user me-1 text-danger"></i>Nama Lengkap</label>
<input type="text" name="nama" id="inp-nama-user" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('nama') }}" required></div>
<div class="mb-3"><label class="form-label fw-bold small"><i class="fa-solid fa-lock me-1 text-danger"></i>Password</label>
<input type="password" name="password" id="inp-pass-user" class="form-control" placeholder="Masukkan password" required></div>
<button type="submit" class="btn btn-login"><i class="fa-solid fa-right-to-bracket me-1"></i> Masuk sebagai User</button></form>
<div class="text-center mt-3 small">Belum punya akun? <a href="{{ route('pelanggan.daftar') }}" class="text-danger fw-bold text-decoration-none">Daftar gratis</a></div>

<div class="quick-section">
    <div class="quick-title"><i class="fa-solid fa-bolt me-1"></i> Akun Uji Coba — Klik untuk Masuk Otomatis</div>
    <div class="quick-card" onclick="quickLogin('user','Muhamad Iqbal','password')">
        <div class="quick-avatar user">MI</div>
        <div class="quick-info"><p class="name">Muhamad Iqbal</p><p class="role">Member MBR-001 · Pelanggan · pass: password</p></div>
        <i class="fa-solid fa-arrow-right quick-arrow"></i>
    </div>
    <div class="quick-card" onclick="quickLogin('user','Nabila Putri','password')">
        <div class="quick-avatar user">NP</div>
        <div class="quick-info"><p class="name">Nabila Putri</p><p class="role">Member MBR-002 · Pelanggan · pass: password</p></div>
        <i class="fa-solid fa-arrow-right quick-arrow"></i>
    </div>
    <div class="quick-card" onclick="quickLogin('user','Reza Pratama','password')">
        <div class="quick-avatar user">RP</div>
        <div class="quick-info"><p class="name">Reza Pratama</p><p class="role">Member MBR-003 · Pelanggan · pass: password</p></div>
        <i class="fa-solid fa-arrow-right quick-arrow"></i>
    </div>
    <div id="quick-loading-user" class="quick-loading mt-2 justify-content-center"><span class="spinner-border text-danger"></span> Sedang masuk...</div>
</div>
</div>

{{-- =================== PANE: PETUGAS =================== --}}
<div class="pane" id="pane-petugas">
<form method="POST" action="{{ route('kasir.login.post') }}" id="form-petugas">@csrf
<div class="mb-3"><label class="form-label fw-bold small"><i class="fa-solid fa-id-badge me-1 text-danger"></i>Username Petugas</label>
<input type="text" name="username" id="inp-user-petugas" class="form-control" placeholder="Username petugas" value="{{ old('username') }}" required></div>
<div class="mb-3"><label class="form-label fw-bold small"><i class="fa-solid fa-lock me-1 text-danger"></i>Password</label>
<input type="password" name="password" id="inp-pass-petugas" class="form-control" placeholder="Password petugas" required></div>
<button type="submit" class="btn btn-login"><i class="fa-solid fa-right-to-bracket me-1"></i> Masuk sebagai Petugas</button></form>

<div class="quick-section">
    <div class="quick-title"><i class="fa-solid fa-bolt me-1"></i> Akun Uji Coba — Klik untuk Masuk Otomatis</div>
    <div class="quick-card" onclick="quickLogin('petugas','admin','admin123')">
        <div class="quick-avatar admin">A</div>
        <div class="quick-info"><p class="name">admin</p><p class="role">Direktur Utama · Role Admin</p></div>
        <i class="fa-solid fa-arrow-right quick-arrow"></i>
    </div>
    <div class="quick-card" onclick="quickLogin('petugas','kasir1','kasir123')">
        <div class="quick-avatar kasir">K1</div>
        <div class="quick-info"><p class="name">kasir1</p><p class="role">Kasir Utama Shift Pagi · Role Kasir</p></div>
        <i class="fa-solid fa-arrow-right quick-arrow"></i>
    </div>
    <div class="quick-card" onclick="quickLogin('petugas','manajer_toko','admin123')">
        <div class="quick-avatar admin">MT</div>
        <div class="quick-info"><p class="name">manajer_toko</p><p class="role">Manajer Operasional · Role Admin</p></div>
        <i class="fa-solid fa-arrow-right quick-arrow"></i>
    </div>
    <div class="quick-card" onclick="quickLogin('petugas','keuangan','keuangan123')">
        <div class="quick-avatar kasir">KU</div>
        <div class="quick-info"><p class="name">keuangan</p><p class="role">Manajer Keuangan · Role Manajer</p></div>
        <i class="fa-solid fa-arrow-right quick-arrow"></i>
    </div>
    <div id="quick-loading-petugas" class="quick-loading mt-2 justify-content-center"><span class="spinner-border text-danger"></span> Sedang masuk...</div>
</div>
</div>

{{-- =================== PANE: KURIR =================== --}}
<div class="pane" id="pane-kurir">
<form method="POST" action="{{ route('mitra.login.post') }}" id="form-kurir">@csrf
<div class="mb-3"><label class="form-label fw-bold small"><i class="fa-solid fa-motorcycle me-1 text-danger"></i>Username Kurir</label>
<input type="text" name="username" id="inp-user-kurir" class="form-control" placeholder="Username kurir" value="{{ old('username') }}" required></div>
<div class="mb-3"><label class="form-label fw-bold small"><i class="fa-solid fa-lock me-1 text-danger"></i>Password</label>
<input type="password" name="password" id="inp-pass-kurir" class="form-control" placeholder="Password kurir" required></div>
<button type="submit" class="btn btn-login"><i class="fa-solid fa-right-to-bracket me-1"></i> Masuk sebagai Kurir</button></form>

<div class="quick-section">
    <div class="quick-title"><i class="fa-solid fa-bolt me-1"></i> Akun Uji Coba — Klik untuk Masuk Otomatis</div>
    <div class="quick-card" onclick="quickLogin('kurir','mitra','mitra123')">
        <div class="quick-avatar mitra">MK</div>
        <div class="quick-info"><p class="name">mitra</p><p class="role">Mitra Kurir · Pengantar Pesanan</p></div>
        <i class="fa-solid fa-arrow-right quick-arrow"></i>
    </div>
    <div id="quick-loading-kurir" class="quick-loading mt-2 justify-content-center"><span class="spinner-border text-danger"></span> Sedang masuk...</div>
</div>
</div>

<div class="text-center mt-3"><a href="{{ route('toko.index') }}" class="small text-muted text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Toko</a></div>
</div></div>
<script>
// ===== MODE SLIDER =====
(function(){
    var btns = document.querySelectorAll('.mode-slider button');
    function activate(m){
        btns.forEach(function(x){x.classList.toggle('on', x.dataset.mode===m)});
        document.querySelectorAll('.pane').forEach(function(p){p.classList.remove('on')});
        document.getElementById('pane-'+m).classList.add('on');
        try{localStorage.setItem('kopma_login_mode',m)}catch(e){}
    }
    btns.forEach(function(x){x.addEventListener('click',function(){activate(x.dataset.mode)})});
    var hash=(location.hash||'').replace('#','');
    if(['user','petugas','kurir'].indexOf(hash)<0){try{hash=localStorage.getItem('kopma_login_mode')||'user'}catch(e){hash='user'}}
    activate(hash);
})();

// ===== QUICK LOGIN — 1 KLIK LANGSUNG MASUK, LANJUT DI TAB YANG SAMA =====
// Sistem terpisah 1 tab = 1 peran: TIDAK buka tab baru. Login langsung
// pindah mode aktif (user/petugas/kurir) di tab ini; sesi peran lain tetap
// tersimpan sehingga bisa pindah via tombol Mode tanpa logout.
function quickLogin(mode, username, password) {
    var loader = document.getElementById('quick-loading-' + mode);
    if(loader) loader.classList.add('show');
    var form, userInp, passInp;
    if (mode === 'user') {
        form = document.getElementById('form-user');
        userInp = document.getElementById('inp-nama-user');
        passInp = document.getElementById('inp-pass-user');
    } else if (mode === 'petugas') {
        form = document.getElementById('form-petugas');
        userInp = document.getElementById('inp-user-petugas');
        passInp = document.getElementById('inp-pass-petugas');
    } else if (mode === 'kurir') {
        form = document.getElementById('form-kurir');
        userInp = document.getElementById('inp-user-kurir');
        passInp = document.getElementById('inp-pass-kurir');
    }
    if (!form || !userInp || !passInp) return;
    userInp.value = username;
    passInp.value = password;
    form.removeAttribute('target');
    setTimeout(function(){ form.submit(); }, 200);
}
</script></body></html>
