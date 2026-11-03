{{--
    AVATAR PROFIL DI NAVBAR (pojok kanan) — tampil untuk semua peran:
    member (user), petugas, kurir (anggota/mitra).
    Pakai: @include('partials.foto-profil-menu', ['as_li' => true])  di dalam <ul class="navbar-nav">
           @include('partials.foto-profil-menu', ['as_li' => false]) di luar <ul> (mis. hero katalog).
--}}
@php
    $modeFoto = \App\Helpers\AkunHelper::modeAktif();
    $fotoUrl = null;
    $namaFoto = 'Akun';
    $labelRole = 'Tamu';
    $logoutRoute = null;

    if ($modeFoto === 'petugas' && session('kasir_id')) {
        $u = \App\Models\User::find(session('kasir_id'));
        $fotoMentah = session('kasir_foto') ?? optional($u)->foto;
        // Normalisasi ke URL absolut: path relatif (storage/foto/...) dibungkus asset()
        // agar tidak 404 di halaman dalam (/petugas/promo, /kurir, ...).
        $fotoUrl = $fotoMentah
            ? (preg_match('#^(https?://|data:|/)#', $fotoMentah) ? $fotoMentah : asset($fotoMentah))
            : null;
        $namaFoto = session('kasir_nama') ?? optional($u)->nama_lengkap ?? 'Petugas';
        $labelRole = 'Petugas' . (optional($u)->jabatan ? ' — ' . $u->jabatan : '');
        $logoutRoute = route('kasir.logout');
    } elseif ($modeFoto === 'kurir' && session('kurir_aktif') && session('pelanggan_id')) {
        $a = \App\Models\Anggota::find(session('pelanggan_id'));
        $fotoKurirMentah = optional($a)->foto;
        $fotoUrl = $fotoKurirMentah
            ? (preg_match('#^(https?://|data:|/)#', $fotoKurirMentah) ? $fotoKurirMentah : asset($fotoKurirMentah))
            : null;
        $namaFoto = session('pelanggan_nama') ?? 'Kurir';
        $labelRole = 'Kurir (akun member)';
        $logoutRoute = route('pelanggan.logout');
    } elseif ($modeFoto === 'kurir' && session('mitra_id')) {
        $u = \App\Models\User::find(session('mitra_id'));
        $fotoMitraMentah = session('mitra_foto') ?? optional($u)->foto;
        $fotoUrl = $fotoMitraMentah
            ? (preg_match('#^(https?://|data:|/)#', $fotoMitraMentah) ? $fotoMitraMentah : asset($fotoMitraMentah))
            : null;
        $namaFoto = session('mitra_nama') ?? 'Kurir Mitra';
        $labelRole = 'Kurir Mitra';
        $logoutRoute = route('mitra.logout');
    } elseif (session('pelanggan_id')) {
        $a = \App\Models\Anggota::find(session('pelanggan_id'));
        $fotoMemberMentah = session('pelanggan_foto') ?? optional($a)->foto;
        $fotoUrl = $fotoMemberMentah
            ? (preg_match('#^(https?://|data:|/)#', $fotoMemberMentah) ? $fotoMemberMentah : asset($fotoMemberMentah))
            : null;
        $namaFoto = session('pelanggan_nama') ?? 'Member';
        $labelRole = 'Member KOPMA';
        $logoutRoute = route('pelanggan.logout');
    }

    $tampil = (bool) ($modeFoto || session('pelanggan_id'));
    $inisial = mb_strtoupper(mb_substr(trim((string) $namaFoto), 0, 1)) ?: '?';
    $warna = ['#c8102e', '#059669', '#1d4ed8', '#d97706', '#7c3aed'][abs(crc32((string) $namaFoto)) % 5];
    $pakaiLi = $as_li ?? true;
@endphp

@if($tampil)
<{{ $pakaiLi ? 'li' : 'div' }} class="{{ $pakaiLi ? 'nav-item dropdown ms-lg-2' : 'dropdown flex-shrink-0' }}">
    <a class="nav-link dropdown-toggle border-0 bg-transparent d-flex align-items-center gap-2 p-0"
       href="#" data-bs-toggle="dropdown" aria-expanded="false"
       title="Menu profil {{ $namaFoto }}">
        <span class="foto-profil-bulat">
            <span class="foto-profil-inisial" style="background:{{ $warna }}">{{ $inisial }}</span>
            @if($fotoUrl)
                <img src="{{ $fotoUrl }}" alt="Foto profil {{ $namaFoto }}"
                     class="foto-profil-img" onerror="this.remove()">
            @endif
        </span>
        <span class="d-none d-lg-inline small fw-semibold text-dark text-truncate" style="max-width:140px">{{ $namaFoto }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width:250px">
        <li class="px-3 py-2 border-bottom">
            <div class="fw-bold text-dark text-truncate">{{ $namaFoto }}</div>
            <div class="small text-muted">{{ $labelRole }}</div>
        </li>
        <li>
            <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#modalFotoProfil">
                <i class="fa-solid fa-camera me-2 text-danger"></i>Ganti Foto Profil
            </button>
        </li>
        <li>
            <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#modalFotoProfil">
                <i class="fa-solid fa-user-pen me-2 text-danger"></i>Ganti Nama
            </button>
        </li>
        @if(session('pelanggan_id'))
            <li>
                <a class="dropdown-item" href="{{ route('pelanggan.profil') }}">
                    <i class="fa-solid fa-id-card me-2 text-secondary"></i>Profil Saya
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('kopma.point.index') }}">
                    <i class="fa-solid fa-coins me-2 text-warning"></i>Kopma Point
                </a>
            </li>
        @endif
        @if($logoutRoute)
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ $logoutRoute }}" method="POST" class="m-0">@csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                    </button>
                </form>
            </li>
        @endif
    </ul>
</{{ $pakaiLi ? 'li' : 'div' }}>
@endif
