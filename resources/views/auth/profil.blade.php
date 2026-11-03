@extends('layouts.app')

@section('content')
<style>
    .profil-hero{background:linear-gradient(135deg,#450a0a 0%,#c8102e 60%,#ef4444 120%);border-radius:20px;color:#fff;padding:30px 24px;position:relative;overflow:hidden}
    .profil-hero::after{content:"";position:absolute;right:-50px;top:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.10)}
    .foto-box{width:110px;height:110px;border-radius:50%;border:4px solid #fff;object-fit:cover;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:#c8102e;font-size:40px;overflow:hidden;box-shadow:0 10px 24px rgba(0,0,0,.25)}
    .foto-box img{width:100%;height:100%;object-fit:cover}
    .tab-pil{border:2px solid #f5d0d0;background:#fff;border-radius:14px;padding:14px;cursor:pointer;transition:.15s}
    .tab-pil:hover{border-color:#c8102e}
    .tab-pil.aktif{border-color:#c8102e;background:#fee2e2}
    .tab-pil .ic{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px}
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- HERO PROFIL --}}
        <div class="profil-hero shadow mb-4">
            <div class="d-flex align-items-center gap-4 flex-wrap" style="position:relative;z-index:2">
                <div class="foto-box">
                    @if($anggota->foto)
                        <img src="{{ $anggota->foto }}" alt="Foto Profil {{ $anggota->nama }}">
                    @else
                        <i class="fa-solid fa-user"></i>
                    @endif
                </div>
                <div>
                    <h4 class="fw-bold mb-1">{{ $anggota->nama }}</h4>
                    <div class="mb-1" style="font-size:13px;opacity:.9">
                        <i class="fa-solid fa-id-card me-1"></i> {{ $anggota->kode_member }} &bull;
                        <i class="fa-solid fa-calendar me-1"></i> Member sejak {{ \Illuminate\Support\Carbon::parse($anggota->tanggal_daftar)->translatedFormat('d F Y') }}
                    </div>
                    <div class="d-flex gap-2 flex-wrap" style="font-size:12px">
                        <span class="badge bg-warning text-dark fw-bold"><i class="fa-solid fa-star me-1"></i> {{ $anggota->poin }} poin</span>
                        <span class="badge bg-light text-dark fw-bold">Belanja Rp {{ number_format($anggota->total_belanja, 0, ',', '.') }}</span>
                        <span class="badge bg-light text-dark fw-bold"><i class="fa-solid fa-envelope me-1"></i> {{ $anggota->email ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- GANTI FOTO PROFIL --}}
        <div class="card card-kopma mb-4">
            <div class="card-kopma-header">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-camera text-danger me-2"></i> Ganti Foto Profil</h5>
            </div>
            <div class="card-body p-4">

                {{-- Pilihan sumber foto --}}
                <div class="row g-3 mb-4" id="pilihSumber">
                    <div class="col-md-6">
                        <div class="tab-pil aktif" data-sumber="album">
                            <div class="d-flex align-items-center gap-3">
                                <span class="ic" style="background:linear-gradient(135deg,#dc2626,#c8102e)"><i class="fa-solid fa-images"></i></span>
                                <div>
                                    <div class="fw-bold" style="font-size:14px">Dari Album</div>
                                    <small class="text-muted" style="font-size:12px">Pilih foto dari galeri / album HP atau laptop</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="tab-pil" data-sumber="drive">
                            <div class="d-flex align-items-center gap-3">
                                <span class="ic" style="background:linear-gradient(135deg,#991b1b,#b91c1c)"><i class="fa-brands fa-google-drive"></i></span>
                                <div>
                                    <div class="fw-bold" style="font-size:14px">Dari Google Drive</div>
                                    <small class="text-muted" style="font-size:12px">Tempel link gambar yang dibagikan dari Drive</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('pelanggan.foto') }}" method="POST" enctype="multipart/form-data" id="formFoto">
                    @csrf

                    {{-- Opsi 1: album --}}
                    <div id="panelAlbum">
                        <label class="form-label fw-semibold">Pilih foto (JPG/PNG/WEBP, maks 2 MB)</label>
                        <input type="file" name="foto_album" id="foto_album" class="form-control" accept="image/*">
                        <div class="mt-3 text-center">
                            <img id="previewAlbum" class="d-none" alt="Preview" style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:4px solid #c8102e">
                        </div>
                    </div>

                    {{-- Opsi 2: Google Drive --}}
                    <div id="panelDrive" class="d-none">
                        <label class="form-label fw-semibold">Link Google Drive / URL gambar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-brands fa-google-drive"></i></span>
                            <input type="text" name="foto_url" id="foto_url" class="form-control"
                                   placeholder="https://drive.google.com/file/d/XXXX/view?usp=sharing"
                                   value="{{ old('foto_url') }}">
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size:12px">
                            Pastikan file di Drive disetel <strong>"Siapa saja yang memiliki link"</strong> agar fotonya bisa tampil.
                        </small>
                        @error('foto_url')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-danger fw-bold px-4">
                            <i class="fa-solid fa-check me-1"></i> Simpan Foto
                        </button>
                        <a href="{{ route('toko.index') }}" class="btn btn-outline-secondary fw-bold">Kembali ke Toko</a>
                    </div>
                    @error('foto_album')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                </form>
            </div>
        </div>

        {{-- GANTI NAMA TAMPILAN (user / petugas / kurir — mengikuti mode aktif) --}}
        <div class="card card-kopma mb-4">
            <div class="card-kopma-header">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-user-pen text-danger me-2"></i> Ganti Nama</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profil.nama.update') }}" method="POST">
                    @csrf
                    <label class="form-label fw-bold small mb-1">Nama tampilan baru</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-pen"></i></span>
                        <input type="text" name="nama" class="form-control" maxlength="100" required
                               value="{{ old('nama', session('pelanggan_nama') ?? $anggota->nama) }}"
                               placeholder="Nama baru Anda">
                    </div>
                    <div class="form-text mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Berlaku untuk peran yang sedang aktif (user / petugas / kurir) dan tampil di topbar.
                    </div>
                    @error('nama')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                    <div class="d-grid d-md-block text-md-end">
                        <button type="submit" class="btn btn-danger fw-bold px-4 py-2">
                            <i class="fa-solid fa-check me-1"></i> Simpan Nama
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- DATA AKUN --}}
        <div class="card card-kopma mb-4">
            <div class="card-kopma-header">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-user-gear text-danger me-2"></i> Data Akun</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        <tr><th style="width:40%">Nama</th><td>{{ $anggota->nama }}</td></tr>
                        <tr><th>Kode Member</th><td><code>{{ $anggota->kode_member }}</code></td></tr>
                        <tr><th>No. HP</th><td>{{ $anggota->no_hp ?: '-' }}</td></tr>
                        <tr><th>Email</th><td>{{ $anggota->email ?: '-' }}</td></tr>
                        <tr><th>Alamat</th><td>{{ $anggota->alamat ?: '-' }}</td></tr>
                        <tr><th>Status</th><td><span class="badge bg-{{ $anggota->status_aktif === 'aktif' ? 'success' : 'secondary' }}">{{ $anggota->status_aktif }}</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Pilih sumber foto: album vs Google Drive
    document.querySelectorAll('#pilihSumber .tab-pil').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('#pilihSumber .tab-pil').forEach(function (t) { t.classList.remove('aktif'); });
            tab.classList.add('aktif');
            var drive = tab.dataset.sumber === 'drive';
            document.getElementById('panelDrive').classList.toggle('d-none', !drive);
            document.getElementById('panelAlbum').classList.toggle('d-none', drive);
            // bersihkan input yang tidak dipakai agar validasi server lolos
            document.getElementById('foto_album').required = !drive;
            document.getElementById('foto_url').required = drive;
            if (!drive) document.getElementById('foto_url').value = '';
            if (drive) document.getElementById('foto_album').value = '';
        });
    });
    document.getElementById('foto_album').required = true;
    document.getElementById('foto_url').required = false;

    // Preview foto dari album
    document.getElementById('foto_album').addEventListener('change', function (e) {
        var f = e.target.files[0];
        if (!f) return;
        var img = document.getElementById('previewAlbum');
        img.src = URL.createObjectURL(f);
        img.classList.remove('d-none');
    });

</script>
@endpush
@endsection

