@extends('layouts.app')

@section('content')
<style>
    .chat-wrap{max-width:820px;margin:0 auto}
    .chat-hero{background:linear-gradient(135deg,#7f1d1d,#c8102e 60%,#ef4444);border-radius:18px;
        padding:18px 22px;color:#fff}
    .ord-list{display:flex;gap:10px;overflow-x:auto;padding:4px 2px 10px}
    .ord-chip{flex:0 0 auto;min-width:210px;background:#fff;border:2px solid #eef1f5;border-radius:14px;
        padding:10px 12px;cursor:pointer;transition:.15s}
    .ord-chip:hover{border-color:#fecaca}
    .ord-chip.aktif{border-color:#dc2626;background:#fef2f2}
    .ord-chip .struk{font-weight:800;font-size:12px}
    .ord-chip .sub{font-size:11px;color:#64748b}
    .driver-card{background:#fff;border:1px solid #eef1f5;border-radius:16px;padding:14px 16px;margin-bottom:12px;
        display:flex;align-items:center;gap:12px}
    .driver-ava{width:52px;height:52px;border-radius:50%;background:#fee2e2;color:#c8102e;display:flex;
        align-items:center;justify-content:center;font-size:22px;flex:0 0 auto;position:relative}
    .driver-ava .dot{position:absolute;right:0;bottom:2px;width:13px;height:13px;border-radius:50%;
        background:#ef4444;border:2px solid #fff}
    .chat-panel{background:#efeae2;border:1px solid #e2ded2;border-radius:16px;overflow:hidden;
        display:flex;flex-direction:column;height:min(58vh,520px)}
    .chat-head{background:#7f1d1d;color:#fff;padding:10px 14px;display:flex;align-items:center;gap:10px}
    .chat-ava{width:38px;height:38px;border-radius:50%;background:#c8102e;display:flex;align-items:center;
        justify-content:center;font-size:17px;flex:0 0 auto}
    .chat-body{flex:1;overflow-y:auto;padding:14px 12px;display:flex;flex-direction:column;gap:8px;
        background-image:radial-gradient(rgba(0,0,0,.04) 1px,transparent 1px);background-size:18px 18px}
    .chat-msg{max-width:80%;padding:8px 11px;border-radius:10px;line-height:1.45;white-space:pre-wrap;
        word-break:break-word;box-shadow:0 1px 1px rgba(0,0,0,.08);font-size:14px}
    .chat-msg.user{background:#fee2e2;align-self:flex-end;border-top-right-radius:2px;color:#111b21}
    .chat-msg.kurir{background:#fff;align-self:flex-start;border-top-left-radius:2px;color:#111b21}
    .chat-msg .meta{display:block;font-size:10px;color:#667781;margin-top:4px}
    .chat-form{display:flex;gap:8px;padding:10px;background:#f0f2f5;border-top:1px solid #e2ded2}
    .chat-form input{flex:1;border:none;border-radius:20px;padding:10px 15px;font-size:14px;outline:none}
    .chat-send{width:42px;height:42px;border:none;border-radius:50%;background:#c8102e;color:#fff;
        font-size:16px;flex:0 0 auto;cursor:pointer}
    .chat-send:disabled{background:#b0b6ba;cursor:not-allowed}
    .empty-note{background:#fffbeb;border:1px solid #fde68a;border-radius:14px;padding:16px;text-align:center}
    /* Template picker */
    .tpl-pill{border:1px solid #d9d2c5;background:#fff;border-radius:20px;padding:4px 10px;font-size:11px;color:#4b5563;cursor:pointer;margin:2px;display:inline-block;transition:.15s}
    .tpl-pill:hover{border-color:#c8102e;color:#7f1d1d;background:#fef2f2}
    .tpl-pill.aktif{border-color:#c8102e;background:#fee2e2;color:#7f1d1d;font-weight:700}
    .tpl-btn{width:34px;height:34px;border:none;border-radius:50%;background:#c8102e;color:#fff;font-size:14px;cursor:pointer;flex:0 0 auto}
    .tpl-btn:disabled{background:#b0b6ba;cursor:not-allowed}
    /* Peta pelacakan kurir */
    #petaKurir{width:100%;height:200px;border-radius:12px;border:1px solid #e2ded2;margin-top:10px;background:#fff}
    .lokasi-info{font-size:11px;color:#64748b;margin-top:4px;text-align:center}
    .lokasi-badge{display:inline-block;background:#fee2e2;color:#c8102e;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;margin-top:6px}
</style>

<div class="chat-wrap">
    <div class="chat-hero mb-3 d-flex justify-content-between align-items-center gap-2">
        <div>
            <h5 class="fw-bold mb-1"><i class="fa-solid fa-comments me-2"></i>Chat dengan Mitra Kurir</h5>
            <small style="opacity:.9">Ngobrol langsung dengan kurir yang mengantar pesananmu.</small>
        </div>
        <span class="badge bg-white text-danger fw-bold px-3 py-2">
            {{ $anggota->nama }} &bull; {{ $anggota->kode_member }}
        </span>
    </div>

    @if($pesananChat->isEmpty())
        <div class="empty-note">
            <div style="font-size:40px">🛵</div>
            <h6 class="fw-bold mt-2 mb-1">Belum ada kurir untuk dihubungi</h6>
            <p class="text-muted small mb-2">
                Chat aktif otomatis saat ada <strong>mitra kurir yang mengambil pesanan “Antar ke Rumah”-mu</strong>.
            </p>
            @if($menungguKurir->isNotEmpty())
                <div class="text-muted small">
                    <i class="fa-solid fa-hourglass-half me-1"></i>
                    {{ $menungguKurir->count() }} pesananmu masih menunggu diambil mitra:
                    @foreach($menungguKurir as $m)
                        <span class="badge bg-light text-dark border">{{ $m->no_struk }}</span>
                    @endforeach
                </div>
            @endif
            <a href="{{ route('pesanan.index') }}" class="btn btn-outline-danger btn-sm fw-bold mt-3">
                <i class="fa-solid fa-box me-1"></i> Lihat Pesanan Saya
            </a>
        </div>
    @else
        {{-- Pilih pesanan (tiap pesanan = 1 kurir berbeda) --}}
        <div class="ord-list" id="ordList">
            @foreach($pesananChat as $i => $p)
                <div class="ord-chip {{ $i === 0 ? 'aktif' : '' }}"
                     data-id="{{ $p->id_penjualan }}"
                     data-nama="{{ $p->kurir->nama_lengkap ?? 'Mitra Kurir' }}"
                     data-struk="{{ $p->no_struk }}"
                     data-status="{{ $p->status_pesanan }}">
                    <div class="struk">{{ $p->no_struk }}</div>
                    <div class="sub">Kurir: {{ $p->kurir->nama_lengkap ?? 'Mitra Kurir' }}</div>
                    <div class="sub text-uppercase">{{ str_replace('_',' ',$p->status_pesanan) }}</div>
                </div>
            @endforeach
        </div>

        {{-- Kartu kurir/mitra --}}
        <div class="driver-card">
            <div class="driver-ava"><i class="fa-solid fa-motorcycle"></i><span class="dot"></span></div>
            <div class="flex-grow-1">
                <div class="fw-bold" style="font-size:14px" id="dNama">{{ $pesananChat->first()->kurir->nama_lengkap ?? 'Mitra Kurir' }}</div>
                <div class="text-muted" style="font-size:12px" id="dInfo">Mengantar pesanan {{ $pesananChat->first()->no_struk }}</div>
            </div>
        </div>

        {{-- Peta pelacakan lokasi kurir (real-time) --}}
        <div id="bagianPeta" style="display:none">
            <div class="lokasi-badge"><i class="fa-solid fa-truck-moving me-1"></i>Lokasi Kurir Sekarang</div>
            <div id="petaKurir"></div>
            <div class="lokasi-info" id="infoLokasi">Mengambil lokasi kurir…</div>
        </div>

        {{-- Panel chat --}}
        <div class="chat-panel">
            <div class="chat-head">
                <div class="chat-ava"><i class="fa-solid fa-motorcycle"></i></div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="font-size:14px" id="chatNama">Mitra Kurir</div>
                </div>
            </div>
            <div class="chat-body" id="chatBody">
                <div class="text-center text-muted small py-4">Memuat pesan…</div>
            </div>
            <form class="chat-form" id="chatForm" autocomplete="off">
                <input type="text" id="chatInput" maxlength="500" placeholder="Tulis pesan untuk kurir…">
                <!-- Template picker (user) -->
                <div id="tplPicker" style="display:flex;gap:4px;overflow-x:auto;padding:2px 0;max-height:34px;align-items:center">
                    @foreach($templatesUser as $tpl)
                        <span class="tpl-pill" data-kode="{{ $tpl->kode }}" data-isi="{{ e($tpl->isi) }}" title="{{ $tpl->nama }}">{{ $tpl->kode }}</span>
                    @endforeach
                </div>
                <button type="button" class="tpl-btn" id="tplAktif" title="Pilih template">💬</button>
                <button type="button" class="tpl-btn" id="btnLokasi" title="Kirim lokasi saya">📍</button>
                <button type="submit" class="chat-send" id="chatKirim" title="Kirim">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    @endif
</div>
@endsection


@push('scripts')
<script>
    // ===== CHAT USER <-> MITRA KURIR (per pesanan, polling 3 detik) =====
    (function () {
        const ordList = document.getElementById('ordList');
        if (!ordList) return; // tidak ada pesanan yang bisa dichat

        const CHAT_URL_PESAN     = "{{ route('chat.pesan') }}";
        const CHAT_URL_KIRIM     = "{{ route('chat.kirim') }}";
        const KURIR_LOKASI_URL   = "{{ route('kurir.lokasi-terbaru') }}";
        const CSRF               = "{{ csrf_token() }}";

        const chatBody    = document.getElementById('chatBody');
        const chatInput   = document.getElementById('chatInput');
        const chatKirim   = document.getElementById('chatKirim');
        const dNama       = document.getElementById('dNama');
        const dInfo       = document.getElementById('dInfo');
        const chatNama    = document.getElementById('chatNama');
        const tplPicker   = document.getElementById('tplPicker');
        const tplAktifBtn = document.getElementById('tplAktif');
        const btnLokasi   = document.getElementById('btnLokasi');
        const bagianPeta  = document.getElementById('bagianPeta');
        const infoLokasi  = document.getElementById('infoLokasi');

        let pesananAktif   = null;
        let jumlahTerakhir = -1;
        let sedangKirim    = false;
        let tplTerpilih    = null;
        let peta           = null;
        let penandaKurir   = null;
        let penandaUser    = null;
        let lokasiUser     = null;

        function chatEscape(teks) {
            return String(teks).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function chatBubble(p) {
            const kelas = p.pengirim === 'user' ? 'user' : 'kurir';
            const nama  = p.pengirim === 'user' ? 'Saya' : (p.nama_pengirim || 'Kurir');
            let isi = chatEscape(p.pesan);
            if (p.jenis === 'template') isi = '<i class="fa-solid fa-tag text-warning me-1" style="font-size:10px"></i>' + isi;
            if (p.jenis === 'lokasi' && p.lokasi_lat && p.lokasi_lng) {
                isi += '<br><small><i class="fa-solid fa-location-dot me-1"></i> Lokasi ' + nama + '</small>';
            }
            return '<div class="chat-msg ' + kelas + '">' + isi
                 + '<span class="meta">' + nama + ' &bull; ' + p.waktu + '</span></div>';
        }

        function chatMuat(paksa) {
            if (!pesananAktif) return Promise.resolve();
            return fetch(CHAT_URL_PESAN + '?id_penjualan=' + pesananAktif,
                    { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => {
                    if (!d.ok) return;
                    if (!paksa && d.pesan.length === jumlahTerakhir) return;
                    jumlahTerakhir = d.pesan.length;

                    if (!d.pesan.length) {
                        chatBody.innerHTML = '<div class="text-center text-muted small py-4">'
                            + 'Belum ada pesan. Sapa kurirmu di bawah ini.</div>';
                        return;
                    }
                    const diBawah = chatBody.scrollHeight - chatBody.scrollTop - chatBody.clientHeight < 80;
                    chatBody.innerHTML = d.pesan.map(chatBubble).join('');
                    if (diBawah || paksa) chatBody.scrollTop = chatBody.scrollHeight;
                })
                .catch(() => {});
        }

        function bukaPesanan(chip) {
            ordList.querySelectorAll('.ord-chip').forEach(el => el.classList.remove('aktif'));
            chip.classList.add('aktif');
            pesananAktif = chip.dataset.id;
            jumlahTerakhir = -1;
            dNama.textContent = chip.dataset.nama || 'Mitra Kurir';
            dInfo.textContent = 'Mengantar pesanan ' + chip.dataset.struk;
            chatNama.textContent = chip.dataset.nama || 'Mitra Kurir';
            chatMuat(true);

            bagianPeta.style.display = 'block';
            infoLokasi.textContent = 'Mengambil lokasi kurir…';
            muatLokasiKurir(pesananAktif);
        }

        ordList.querySelectorAll('.ord-chip').forEach(function (chip) {
            chip.addEventListener('click', function () { bukaPesanan(chip); });
        });

        // ---- Template picker ----
        tplPicker.querySelectorAll('.tpl-pill').forEach(function (pill) {
            pill.addEventListener('click', function () {
                tplPicker.querySelectorAll('.tpl-pill').forEach(p => p.classList.remove('aktif'));
                pill.classList.add('aktif');
                tplTerpilih = { kode: pill.dataset.kode, isi: pill.dataset.isi };
                chatInput.value = tplTerpilih.isi;
                chatInput.focus();
            });
        });
        tplAktifBtn.addEventListener('click', function () {
            if (tplTerpilih) {
                chatInput.value = tplTerpilih.isi;
                chatInput.focus();
            }
        });

        // ---- Kirim lokasi pengguna (GPS) ----
        btnLokasi.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Geolocation tidak didukung di browser ini.');
                return;
            }
            btnLokasi.disabled = true; btnLokasi.textContent = '⏳';
            navigator.geolocation.getCurrentPosition(function (pos) {
                lokasiUser = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                kirimPesan('lokasi', '', lokasiUser.lat, lokasiUser.lng);
                btnLokasi.disabled = false; btnLokasi.textContent = '📍';
            }, function () {
                alert('Gagal ambil lokasi. Nyalakan GPS/Lokasi lalu coba lagi.');
                btnLokasi.disabled = false; btnLokasi.textContent = '📍';
            }, { enableHighAccuracy: true, timeout: 15000 });
        });

        function kirimPesan(jenis, isi, lat, lng) {
            if (!pesananAktif || sedangKirim) return;
            sedangKirim = true;
            chatKirim.disabled = true;

            const body = { id_penjualan: pesananAktif, pesan: isi, jenis: jenis };
            if (jenis === 'template' && tplTerpilih) body.template_kode = tplTerpilih.kode;
            if (lat !== undefined && lng !== undefined) { body.lokasi_lat = lat; body.lokasi_lng = lng; }

            fetch(CHAT_URL_KIRIM, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(body)
            })
            .then(function (r) {
                return r.json()
                    .catch(function () { return { ok: false, message: 'Respons server tidak valid.' }; })
                    .then(function (d) {
                        if (!r.ok || !d.ok) {
                            throw new Error(d.message || 'Gagal mengirim pesan (HTTP ' + r.status + ').');
                        }
                        chatInput.value = '';
                        chatMuat(true);
                        if (jenis === 'template') {
                            tplPicker.querySelectorAll('.tpl-pill').forEach(function (p) { p.classList.remove('aktif'); });
                            tplTerpilih = null;
                        }
                    });
            })
            .catch(function (err) {
                alert(err.message || 'Gagal mengirim pesan. Silakan coba lagi.');
            })
            .finally(function () { sedangKirim = false; chatKirim.disabled = false; chatInput.focus(); });
        }

        document.getElementById('chatForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const isi = chatInput.value.trim();
            if (!isi || !pesananAktif || sedangKirim) return;
            const jenis = tplTerpilih ? 'template' : 'teks';
            kirimPesan(jenis, isi);
        });

        // ---- Peta pelacakan kurir (Leaflet) ----
        function muatLokasiKurir(idPenjualan) {
            fetch(KURIR_LOKASI_URL + '?id_penjualan=' + idPenjualan,
                    { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => {
                    if (!d.ok) {
                        infoLokasi.textContent = d.message || 'Lokasi kurir belum tersedia.';
                        return;
                    }
                    infoLokasi.innerHTML = '<strong>' + (d.nama_kurir || 'Kurir') + '</strong> • '
                        + (d.waktu ? new Date(d.waktu).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'}) : '');
                    tampilkanPeta(d.latitude, d.longitude);
                })
                .catch(() => { infoLokasi.textContent = 'Gagal memuat lokasi kurir.'; });
        }

        function tampilkanPeta(lat, lng) {
            if (!peta) {
                peta = L.map('petaKurir', { zoomControl: false }).setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(peta);
                penandaUser = L.marker([lat, lng], {
                    icon: L.divIcon({ className: '', html: '<div style="width:26px;height:26px;border-radius:50%;background:#7f1d1d;border:3px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff">🏠</div>', iconSize: [26,26] })
                }).addTo(peta).bindPopup('Anda (pelanggan)').openPopup();
            }
            if (penandaKurir) penandaKurir.setLatLng([lat, lng]);
            else {
                penandaKurir = L.marker([lat, lng], {
                    icon: L.divIcon({ className: '', html: '<div style="width:26px;height:26px;border-radius:50%;background:#c8102e;border:3px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff">🛵</div>', iconSize: [26,26] })
                }).addTo(peta).bindPopup('Kurir').openPopup();
            }
            peta.setView([lat, lng], 15, { animate: true });
            if (lokasiUser) {
                if (window.__garisPeta) peta.removeLayer(window.__garisPeta);
                window.__garisPeta = L.polyline([[lokasiUser.lat, lokasiUser.lng], [lat, lng]], {color:'#c8102e', weight:2, opacity:0.6, dashArray:'6, 6'}).addTo(peta);
                peta.fitBounds(window.__garisPeta.getBounds(), {padding:[40,40]});
            }
        }

        // Polling lokasi kurir setiap 5 detik saat peta tampil
        setInterval(function () {
            if (pesananAktif && bagianPeta && bagianPeta.style.display !== 'none') muatLokasiKurir(pesananAktif);
        }, 5000);

        // Buka pesanan pertama lalu mulai polling chat.
        const pertama = ordList.querySelector('.ord-chip');
        if (pertama) bukaPesanan(pertama);
        setInterval(chatMuat, 3000);
    })();
</script>
@endpush

