@php
    $modeAktifNav = \App\Helpers\AkunHelper::modeAktif();
    $chatUserBaru = $chatBelumDibaca ?? 0;
@endphp
@if($modeAktifNav === 'kurir')
<li class="nav-item">
    <a class="nav-link-kopma" href="{{ route('kurir.index') }}">
        <i class="fa-solid fa-motorcycle"></i> Antar Pesanan
    </a>
</li>
<li class="nav-item">
    <a class="nav-link-kopma" href="{{ route('kurir.chat') }}">
        <i class="fa-solid fa-comments"></i> Chat Member
    </a>
</li>
<li class="nav-item d-flex align-items-center gap-2 ms-lg-2">
    <span class="badge bg-success fw-semibold px-2 py-2">
        <i class="fa-solid fa-motorcycle me-1"></i>{{ session('mitra_nama') ?? session('pelanggan_nama') }}
    </span>
    <form action="{{ route('mitra.logout') }}" method="POST" class="m-0">@csrf
        <button type="submit" class="btn-logout-sm" title="Logout Kurir">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </form>
</li>
@elseif($modeAktifNav === 'petugas')
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma {{ request()->routeIs('petugas.promo.*') ? 'active' : '' }}"
       href="{{ route('petugas.promo.index') }}">
        <i class="fa-solid fa-bolt"></i> Promo
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma {{ request()->routeIs('petugas.produk.*') ? 'active' : '' }}"
       href="{{ route('petugas.produk.index') }}">
        <i class="fa-solid fa-boxes-stacked"></i> Kelola Produk
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma" href="{{ route('kasir.index') }}">
        <i class="fa-solid fa-cash-register"></i> Kasir (POS)
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma" href="{{ route('petugas.topup') }}">
        <i class="fa-solid fa-circle-arrow-up"></i> Top Up
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma" href="{{ route('laporan.index') }}">
        <i class="fa-solid fa-calendar-days"></i> Laporan
    </a>
</li>
<li class="nav-item km-hide-petugas">
    <a class="nav-link-kopma {{ request()->routeIs('admin.payment-setting') ? 'active' : '' }}"
       href="{{ route('admin.payment-setting') }}">
        <i class="fa-solid fa-credit-card"></i> Pengaturan Bayar
    </a>
</li>
<li class="nav-item d-flex align-items-center gap-2 ms-lg-2">
    <span class="badge-mode-petugas">
        <i class="fa-solid fa-user-tie me-1"></i>{{ session('kasir_nama') }}
    </span>
    <form action="{{ route('kasir.logout') }}" method="POST" class="m-0">@csrf
        <button type="submit" class="btn-logout-sm" title="Logout Petugas">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </form>
</li>
@else
<li class="nav-item">
    <a class="nav-link-kopma" href="{{ route('toko.index') }}">
        <i class="fa-solid fa-boxes-stacked"></i> Toko
    </a>
</li>
<li class="nav-item">
    <a class="nav-link-kopma" href="{{ route('keranjang.index') }}">
        <i class="fa-solid fa-cart-shopping"></i> Keranjang
    </a>
</li>
<li class="nav-item">
    <a class="nav-link-kopma" href="{{ route('pesanan.index') }}">
        <i class="fa-solid fa-box"></i> Pesanan
    </a>
</li>
@if(session('pelanggan_id'))
<li class="nav-item d-flex align-items-center gap-2 ms-lg-2">
    <span class="badge-mode-petugas">
        <i class="fa-solid fa-user me-1"></i>{{ session('pelanggan_nama') }}
    </span>
    <form action="{{ route('pelanggan.logout') }}" method="POST" class="m-0">@csrf
        <button type="submit" class="btn-logout-sm" title="Logout User">
            <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </form>
</li>
@else
<li class="nav-item ms-lg-2">
    <a class="btn btn-sm btn-danger fw-bold px-3 py-2" href="{{ route('masuk') }}#user">
        <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk
    </a>
</li>
@endif
@endif
