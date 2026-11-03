{{-- MODAL GANTI FOTO PROFIL (sekali include di layout): file laptop / album HP / link Google Drive --}}
<style>
    .foto-profil-bulat {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid rgba(255, 255, 255, .85);
        box-shadow: 0 2px 6px rgba(0, 0, 0, .25);
        background: #c8102e;
        flex: 0 0 auto;
    }
    .foto-profil-inisial { color: #fff; font-weight: 700; font-size: 15px; line-height: 1; user-select: none; }
    .foto-profil-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
</style>

<div class="modal fade" id="modalFotoProfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="fa-solid fa-camera me-2"></i>Ganti Foto Profil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <span class="foto-profil-bulat" style="width:96px;height:96px;border-color:#e9ecef">
                        <span class="foto-profil-inisial" id="fotoPreviewInisial" style="font-size:34px;background:#c8102e">
                            {{ mb_strtoupper(mb_substr(trim(session('pelanggan_nama') ?? session('kasir_nama') ?? session('mitra_nama') ?? 'A'), 0, 1)) }}
                        </span>
                        <img id="fotoPreviewImg" class="foto-profil-img" style="display:none" alt="Preview foto">
                    </span>
                    <div class="small text-muted mt-2">Preview foto baru</div>
                </div>

                <form id="formFotoProfil" action="{{ route('foto.profil.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label class="form-label fw-bold small mb-1">1. Pilih dari File Laptop / Album HP</label>
                    <input type="file" name="foto_album" id="fotoAlbumInput" class="form-control mb-1" accept="image/*">
                    <div class="form-text mb-3">
                        <i class="fa-solid fa-folder-open me-1"></i>Di HP langsung membuka album/kamera, di laptop membuka File Explorer.
                        Format JPG/PNG/WEBP, maksimal 2 MB.
                    </div>

                    <label class="form-label fw-bold small mb-1">2. Atau tempel link Google Drive</label>
                    <div class="input-group mb-1">
                        <span class="input-group-text bg-white"><i class="fa-brands fa-google-drive"></i></span>
                        <input type="text" name="foto_url" id="fotoUrlInput" class="form-control"
                               placeholder="https://drive.google.com/file/d/.../view" maxlength="500">
                    </div>
                    <div class="form-text mb-3">
                        Set file di Drive ke bagikan <strong>"Siapa saja yang memiliki link"</strong>.
                        URL gambar biasa (https://.../foto.jpg) juga diterima.
                    </div>

                    <div id="fotoNamaFile" class="small text-success mb-2"></div>
                    <div id="fotoPesan" class="text-danger small mb-2"></div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger fw-bold py-2" id="fotoSimpanBtn">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i>Simpan Foto
                        </button>
                    </div>
                </form>

                <hr class="my-3">
                <label class="form-label fw-bold small mb-1"><i class="fa-solid fa-user-pen me-1 text-danger"></i>Ganti Nama Tampilan</label>
                <form action="{{ route('profil.nama.update') }}" method="POST">
                    @csrf
                    <div class="input-group mb-1">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-pen"></i></span>
                        <input type="text" name="nama" class="form-control" maxlength="100" required
                               value="{{ session('pelanggan_nama') ?? session('kasir_nama') ?? session('mitra_nama') ?? '' }}"
                               placeholder="Nama baru Anda">
                    </div>
                    <div class="form-text mb-2">Berlaku untuk peran yang sedang aktif (user / petugas / kurir).</div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-danger fw-bold py-2">
                            <i class="fa-solid fa-check me-1"></i>Simpan Nama
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const form = document.getElementById('formFotoProfil');
        if (!form) return;
        const fileIn = document.getElementById('fotoAlbumInput');
        const urlIn = document.getElementById('fotoUrlInput');
        const prevImg = document.getElementById('fotoPreviewImg');
        const namaFile = document.getElementById('fotoNamaFile');
        const pesan = document.getElementById('fotoPesan');
        const btnSimpan = document.getElementById('fotoSimpanBtn');

        // Pilih file -> tampilkan preview langsung, kosongkan kolom link
        fileIn.addEventListener('change', function () {
            pesan.textContent = '';
            if (fileIn.files[0]) {
                namaFile.textContent = 'Dipilih: ' + fileIn.files[0].name;
                urlIn.value = '';
                const fr = new FileReader();
                fr.onload = function (e) { prevImg.src = e.target.result; prevImg.style.display = 'block'; };
                fr.readAsDataURL(fileIn.files[0]);
            } else {
                namaFile.textContent = '';
                prevImg.style.display = 'none';
            }
        });

        // Tempel link Drive -> reset pemilihan file
        urlIn.addEventListener('input', function () {
            if (urlIn.value.trim()) { fileIn.value = ''; namaFile.textContent = ''; prevImg.style.display = 'none'; }
            pesan.textContent = '';
        });

        form.addEventListener('submit', async function (ev) {
            ev.preventDefault();
            pesan.textContent = '';

            if (!fileIn.files[0] && !urlIn.value.trim()) {
                pesan.textContent = 'Pilih foto dari file/album atau tempel link Google Drive dulu.';
                return;
            }

            btnSimpan.disabled = true;
            btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new FormData(form)
                });
                const data = await res.json().catch(function () { return {}; });

                if (res.ok && data.ok) {
                    // Perbarui semua avatar di halaman tanpa reload.
                    // Kalau sebelumnya belum ada <img> (belum pernah pasang foto),
                    // buat elemennya agar foto langsung tampil tanpa refresh.
                    document.querySelectorAll('.foto-profil-bulat').forEach(function (wadah) {
                        let img = wadah.querySelector('.foto-profil-img');
                        if (!img) {
                            img = document.createElement('img');
                            img.className = 'foto-profil-img';
                            img.alt = 'Foto profil';
                            wadah.appendChild(img);
                        }
                        let urlFoto = data.foto || '';
                        if (urlFoto && !/^(https?:\/\/|data:|\/)/i.test(urlFoto)) urlFoto = '/' + urlFoto;
                        img.src = urlFoto + (urlFoto.indexOf('?') > -1 ? '&' : '?') + 'v=' + Date.now();
                        img.style.display = 'block';
                    });
                    const m = bootstrap.Modal.getInstance(document.getElementById('modalFotoProfil'));
                    if (m) m.hide();

                    const toast = document.createElement('div');
                    toast.textContent = 'Foto profil berhasil diperbarui ✔';
                    toast.style.cssText = 'position:fixed;bottom:84px;left:50%;transform:translateX(-50%);background:#198754;color:#fff;padding:10px 18px;border-radius:999px;box-shadow:0 4px 14px rgba(0,0,0,.25);z-index:2000;font-weight:600';
                    document.body.appendChild(toast);
                    setTimeout(function () { toast.remove(); }, 2600);
                } else {
                    pesan.textContent = data.message
                        || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Gagal menyimpan foto (HTTP ' + res.status + ').');
                }
            } catch (e) {
                pesan.textContent = 'Terjadi kesalahan jaringan: ' + e.message;
            } finally {
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = '<i class="fa-solid fa-cloud-arrow-up me-1"></i>Simpan Foto';
            }
        });
    })();
</script>
