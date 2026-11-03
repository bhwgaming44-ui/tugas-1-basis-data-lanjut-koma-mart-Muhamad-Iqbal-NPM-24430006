@php
    $modeAktifP = \App\Helpers\AkunHelper::modeAktif();
    $peranP = \App\Helpers\AkunHelper::peranLogin();
@endphp
@if(count($peranP) > 1)
<ul class="navbar-nav me-auto align-items-lg-center gap-1 mt-3 mt-lg-0">
    <li class="nav-item d-flex align-items-center gap-1 flex-wrap">
        <span class="small fw-bold text-muted me-1">Mode:</span>
        @foreach($peranP as $kode => $nama)
            @if($kode === $modeAktifP)
                <span class="badge bg-danger px-2 py-2">{{ ucfirst($kode) }}: {{ $nama }}</span>
            @else
                <a href="{{ route('mode.ganti', $kode) }}" class="btn btn-sm btn-outline-secondary fw-bold">{{ ucfirst($kode) }}</a>
            @endif
        @endforeach
    </li>
</ul>
@endif
