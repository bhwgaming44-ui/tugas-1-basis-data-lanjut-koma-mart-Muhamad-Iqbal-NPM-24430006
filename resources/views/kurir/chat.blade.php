@extends('layouts.app')

@section('content')
<style>
    .kc-hero{background:linear-gradient(135deg,#7f1d1d,#c8102e 60%,#ef4444);border-radius:18px;
        padding:20px 24px;color:#fff}
    .kc-grid{display:grid;grid-template-columns:minmax(240px,320px) 1fr;gap:14px;align-items:start}
    @media(max-width:860px){.kc-grid{grid-template-columns:1fr}}
    .kc-list{background:#fff;border:1px solid #eef1f5;border-radius:16px;overflow:hidden;
        max-height:min(70vh,600px);overflow-y:auto}
    .kc-item{display:flex;gap:10px;padding:11px 13px;border-bottom:1px solid #f1f5f9;cursor:pointer;
        text-decoration:none;color:inherit;transition:background .15s}
    .kc-item:hover{background:#f8fafc}
    .kc-item.aktif{background:#fef2f2;border-left:3px solid #dc2626}
    .kc-ava{width:40px;height:40px;border-radius:50%;background:#fee2e2;color:#c8102e;display:flex;
        align-items:center;justify-content:center;font-weight:800;flex:0 0 auto;font-size:15px}
    .kc-nama{font-weight:700;font-size:13px;line-height:1.2}
    .kc-struk{font-size:10px;color:#94a3b8}
    .kc-pesan{font-size:12px;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:170px}
    .kc-badge{min-width:20px;height:20px;padding:0 6px;border-radius:10px;background:#ef4444;color:#fff;
        font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center;flex:0 0 auto}
    .kc-panel{background:#efeae2;border:1px solid #e2ded2;border-radius:16px;overflow:hidden;
        display:flex;flex-direction:column;height:min(70vh,600px)}
    .kc-head{background:#7f1d1d;color:#fff;padding:10px 14px;display:flex;align-items:center;gap:10px}
    .kc-body{flex:1;overflow-y:auto;padding:14px 12px;display:flex;flex-direction:column;gap:8px;
        background-image:radial-gradient(rgba(0,0,0,.04) 1px,transparent 1px);background-size:18px 18px}
    .kc-msg{max-width:78%;padding:8px 11px;border-radius:10px;line-height:1.45;white-space:pre-wrap;
        word-break:break-word;box-shadow:0 1px 1px rgba(0,0,0,.08);font-size:14px}
    .kc-msg.kurir{background:#fee2e2;align-self:flex-end;border-top-right-radius:2px;color:#111b21}
    .kc-msg.user{background:#fff;align-self:flex-start;border-top-left-radius:2px;color:#111b21}
    .kc-msg .meta{display:block;font-size:10px;color:#667781;margin-top:4px}
    .kc-form{display:flex;gap:8px;padding:10px;background:#f0f2f5;border-top:1px solid #e2ded2}
    .kc-form input{flex:1;border:none;border-radius:20px;padding:10px 15px;font-size:14px;outline:none}
    .kc-send{width:42px;height:42px;border:none;border-radius:50%;background:#c8102e;color:#fff;
        font-size:16px;flex:0 0 auto;cursor:pointer}
    .kc-send:disabled{background:#b0b6ba;cursor:not-allowed}
    .kc-status{display:inline-block;border-radius:20px;padding:2px 10px;font-size:10px;font-weight:800}
    /* Template picker kurir */
    .tpl-pill{border:1px solid #d9d2c5;background:#fff;border-radius:20px;padding:4px 10px;font-size:11px;color:#4b5563;cursor:pointer;margin:2px;display:inline-block;transition:.15s}
    .tpl-pill:hover{border-color:#c8102e;color:#7f1d1d;background:#fef2f2}
    .tpl-pill.aktif{border-color:#c8102e;background:#fee2e2;color:#7f1d1d;font-weight:700}
    .tpl-btn{width:34px;height:34px;border:none;border-radius:50%;background:#c8102e;color:#fff;font-size:14px;cursor:pointer;flex:0 0 auto}
    .tpl-btn:disabled{background:#b0b6ba;cursor:not-allowed}
    /* Lokasi kurir */
    .lokasi-kurir-box{background:#fff;border:1px solid #eef1f5;border-radius:12px;padding:10px 14px;margin-bottom:12px;display:flex;align-items:center;gap:10px}
    .lokasi-kurir-box .status-dot{width:10px;height:10px;border-radius:50%;background:#22c55e;flex:0 0 auto;animation:blinkDot 1.5s infinite}
    @keyframes blinkDot{0%,100%{opacity:1}50%{opacity:.4}}
</style>

<div class="kc-hero mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1"><i class="fa-solid fa-comments me-2"></i>Chat Pelanggan</h5>
        <small style="opacity:.9">Balas pertanyaan member soal pesanan yang Anda antar. Hanya pesanan milik Anda yang tampil di sini.</small>
    </div>
    <span class="badge bg-white text-danger fw-bold px-3 py-2">
        <i class="fa-solid fa-motorcycle me-1"></i> {{ $namaKurir ?? session('mitra_nama') ?? session('kasir_nama') ?? session('pelanggan_nama') ?? 'Kurir' }}
    </span>
</div>

@if($errors->any())
<div class="alert alert-warning py-2 small mb-3"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}</div>
@endif

<!-- Status & lokasi kurir -->
<div class="lokasi-kurir-box mb-3">
    <div class="status-dot" title="GPS aktif"></div>
    <div class="flex-grow-1">
        <div class="fw-bold small" id="kcLokasiStatus">Mengirim lokasi…</div>
        <div class="text-muted" style="font-size:11px" id="kcLokasiWaktu">Belum ada data lokasi</div>
    </div>
    <button type="button" class="tpl-btn" id="btnKirimLokasi" title="Kirim lokasi saya sekarang">📍</button>
</div>

<div class="kc-grid">
    {{-- ================= KOLOM KIRI: PESANAN YANG DIANTAR ================= --}}
    <div class="kc-list" id="kcList">
        @forelse($percakapan as $c)
            @php
                $wStatus = ['menunggu' => '#fef3c7;color:#b45309','diproses' => '#dbeafe;color:#1d4ed8','diantar' => '#e0e7ff;color:#4338ca'];
            @endphp
            <a href="#" class="kc-item {{ $loop->first ? 'aktif' : '' }}"
               data-id="{{ $c['id_penjualan'] }}" data-nama="{{ $c['nama'] }}"
               data-struk="{{ $c['no_struk'] }}" data-status="{{ $c['status_pesanan'] }}">
                <div class="kc-ava">{{ strtoupper(mb_substr($c['nama'], 0, 1)) }}</div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="kc-nama">{{ $c['nama'] }}</div>
                        <small class="text-muted" style="font-size:10px">{{ $c['waktu'] }}</small>
                    </div>
                    <div class="kc-struk">{{ $c['no_struk'] }} &bull;
                        <span class="kc-status" style="background:{{ $wStatus[$c['status_pesanan']] ?? '#f3f4f6;color:#374151' }}">
                            {{ strtoupper(str_replace('_',' ',$c['status_pesanan'])) }}
                        </span>
                    </div>
                    <div class="kc-pesan">
                        @if($c['pengirim'] === 'kurir')<span class="text-danger">Anda: </span>@endif
                        {{ $c['pesan_terakhir'] }}
                    </div>
                </div>
                @if($c['belum_dibaca'] > 0)
                    <span class="kc-badge">{{ $c['belum_dibaca'] }}</span>
                @endif
            </a>
        @empty
            <div class="text-center py-5 px-3 text-muted small">
                <div style="font-size:40px">💬</div>
                Belum ada pesanan yang Anda antar.<br>Ambil pesanan dulu di menu “Antar Pesanan”.
            </div>
        @endforelse
    </div>
    {{-- ================= KOLOM KANAN: RUANG CHAT ================= --}}
    <div class="kc-panel">
        <div class="kc-head">
            <div class="kc-ava bg-white text-danger" id="kcAva">?</div>
            <div class="flex-grow-1">
                <div class="fw-bold" style="font-size:14px" id="kcNama">Pilih pesanan</div>
                <small style="opacity:.85;font-size:11px" id="kcInfo">Belum ada pesanan dipilih</small>
            </div>
            <button type="button" class="btn btn-sm btn-light fw-bold" onclick="kcMuat(true)" title="Muat ulang">
                <i class="fa-solid fa-rotate"></i>
            </button>
        </div>
        <div class="kc-body" id="kcBody">
            <div class="text-center text-muted small py-4">Pilih pesanan di sebelah kiri untuk membalas.</div>
        </div>
        <form class="kc-form" id="kcForm" autocomplete="off">
            <input type="text" id="kcInput" maxlength="500" placeholder="Tulis balasan untuk pelanggan…">
            <!-- Template picker kurir -->
            <div id="tplPickerKurir" style="display:flex;gap:4px;overflow-x:auto;padding:2px 0;max-height:34px;align-items:center">
                @foreach($templatesKurir as $tpl)
                    <span class="tpl-pill" data-kode="{{ $tpl->kode }}" data-isi="{{ e($tpl->isi) }}" title="{{ $tpl->nama }}">{{ $tpl->kode }}</span>
                @endforeach
            </div>
            <button type="button" class="tpl-btn" id="tplAktifKurir" title="Pilih template">💬</button>
            <button type="submit" class="kc-send" id="kcKirim" title="Kirim">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ===== CHAT MITRA <-> PELANGGAN (per pesanan, polling 3 detik) =====
    (function () {
        const KC_PESAN        = "{{ route('kurir.chat.pesan') }}";
        const KC_KIRIM        = "{{ route('kurir.chat.kirim') }}";
        const KC_PERCAKAPAN   = "{{ route('kurir.chat.percakapan') }}";
        const KC_KIRIM_LOKASI = "{{ route('kurir.lokasi') }}";
        const KC_CSRF         = "{{ csrf_token() }}";

        const kcBody       = document.getElementById('kcBody');
        const kcInput      = document.getElementById('kcInput');
        const kcKirim      = document.getElementById('kcKirim');
        const kcList       = document.getElementById('kcList');
        const tplPickerKur = document.getElementById('tplPickerKurir');
        const tplAktifKur  = document.getElementById('tplAktifKurir');
        const btnKirimLok  = document.getElementById('btnKirimLokasi');
        const kcLokStatus  = document.getElementById('kcLokasiStatus');
        const kcLokWaktu   = document.getElementById('kcLokasiWaktu');

        let kcAktif        = null;
        let kcJumlahTerakhir = -1;
        let kcSedangKirim  = false;
        let kcTplTerpilih  = null;
        let kcWatchId      = null;

        function kcEscape(teks) {
            return String(teks).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function kcBubble(p) {
            const kelas = p.pengirim === 'kurir' ? 'kurir' : 'user';
            const nama  = p.pengirim === 'kurir' ? 'Anda' : (p.nama_pengirim || 'Pelanggan');
            let isi = kcEscape(p.pesan);
            if (p.jenis === 'template') isi = '<i class="fa-solid fa-tag text-warning me-1" style="font-size:10px"></i>' + isi;
            if (p.jenis === 'lokasi' && p.lokasi_lat && p.lokasi_lng) {
                isi += '<br><small><i class="fa-solid fa-location-dot me-1"></i> Lokasi ' + nama + '</small>';
            }
            return '<div class="kc-msg ' + kelas + '">' + isi
                 + '<span class="meta">' + nama + ' &bull; ' + p.waktu + '</span></div>';
        }

        function kcMuat(paksa) {
            if (!kcAktif) return Promise.resolve();
            return fetch(KC_PESAN + '?id_penjualan=' + kcAktif,
                    { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => {
                    if (!d.ok) return;
                    if (!paksa && d.pesan.length === kcJumlahTerakhir) return;
                    kcJumlahTerakhir = d.pesan.length;
                    if (!d.pesan.length) {
                        kcBody.innerHTML = '<div class="text-center text-muted small py-4">Belum ada pesan di pesanan ini.</div>';
                        return;
                    }
                    const diBawah = kcBody.scrollHeight - kcBody.scrollTop - kcBody.clientHeight < 80;
                    kcBody.innerHTML = d.pesan.map(kcBubble).join('');
                    if (diBawah || paksa) kcBody.scrollTop = kcBody.scrollHeight;
                })
                .catch(() => {});
        }

        function kcBuka(item) {
            kcList.querySelectorAll('.kc-item').forEach(el => el.classList.remove('aktif'));
            item.classList.add('aktif');
            const badge = item.querySelector('.kc-badge');
            if (badge) badge.remove();
            kcAktif = item.dataset.id;
            kcJumlahTerakhir = -1;
            document.getElementById('kcNama').textContent = item.dataset.nama || '';
            document.getElementById('kcInfo').textContent = item.dataset.struk || '';
            document.getElementById('kcAva').textContent = (item.dataset.nama || '?').charAt(0).toUpperCase();
            kcInput.disabled = false;
            kcKirim.disabled = false;
            kcMuat(true);
            kcInput.focus();
        }

        function kcPasangKlik() {
            kcList.querySelectorAll('.kc-item').forEach(function (item) {
                item.addEventListener('click', function (e) { e.preventDefault(); kcBuka(item); });
            });
        }

        // ---- Template picker kurir ----
        tplPickerKur.querySelectorAll('.tpl-pill').forEach(function (pill) {
            pill.addEventListener('click', function () {
                tplPickerKur.querySelectorAll('.tpl-pill').forEach(p => p.classList.remove('aktif'));
                pill.classList.add('aktif');
                kcTplTerpilih = { kode: pill.dataset.kode, isi: pill.dataset.isi };
                kcInput.value = kcTplTerpilih.isi;
                kcInput.focus();
            });
        });
        tplAktifKur.addEventListener('click', function () {
            if (kcTplTerpilih) {
                kcInput.value = kcTplTerpilih.isi;
                kcInput.focus();
            }
        });

        // ---- Kirim lokasi kurir (GPS) ----
        btnKirimLok.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Geolocation tidak didukung di browser ini.');
                return;
            }
            btnKirimLok.disabled = true; btnKirimLok.textContent = '⏳';
            kcLokStatus.textContent = 'Mengambil lokasi…';
            navigator.geolocation.getCurrentPosition(function (pos) {
                const lat = pos.coords.latitude, lng = pos.coords.longitude;
                kirimLokasiKeServer(lat, lng);
                btnKirimLok.disabled = false; btnKirimLok.textContent = '📍';
            }, function () {
                alert('Gagal ambil lokasi. Nyalakan GPS/Lokasi lalu coba lagi.');
                btnKirimLok.disabled = false; btnKirimLok.textContent = '📍';
            }, { enableHighAccuracy: true, timeout: 15000 });
        });

        // Auto-update lokasi kurir setiap 30 detik
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function (pos) {
                kirimLokasiKeServer(pos.coords.latitude, pos.coords.longitude, false);
            }, function () {}, { enableHighAccuracy: true, timeout: 20000, maximumAge: 30000 });
        }

        function kirimLokasiKeServer(lat, lng, tampilStatus = true) {
            fetch(KC_KIRIM_LOKASI, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': KC_CSRF,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ latitude: lat, longitude: lng })
            })
            .then(r => r.json())
            .then(d => {
                if (d.ok && tampilStatus) {
                    const now = new Date();
                    kcLokStatus.textContent = 'Lokasi terkirim';
                    kcLokWaktu.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
            })
            .catch(() => {});
        }

        function kirimPesan(jenis, isi, lat, lng) {
            if (!kcAktif || kcSedangKirim) return;
            kcSedangKirim = true;
            kcKirim.disabled = true;

            const body = { id_penjualan: kcAktif, pesan: isi, jenis: jenis };
            if (jenis === 'template' && kcTplTerpilih) body.template_kode = kcTplTerpilih.kode;
            if (lat !== undefined && lng !== undefined) { body.lokasi_lat = lat; body.lokasi_lng = lng; }

            fetch(KC_KIRIM, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': KC_CSRF,
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
                        kcInput.value = '';
                        kcMuat(true);
                        if (jenis === 'template') {
                            tplPickerKur.querySelectorAll('.tpl-pill').forEach(function (p) { p.classList.remove('aktif'); });
                            kcTplTerpilih = null;
                        }
                    });
            })
            .catch(function (err) {
                alert(err.message || 'Gagal mengirim pesan. Silakan coba lagi.');
            })
            .finally(function () { kcSedangKirim = false; kcKirim.disabled = false; kcInput.focus(); });
        }

        document.getElementById('kcForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const isi = kcInput.value.trim();
            if (!isi || !kcAktif || kcSedangKirim) return;
            const jenis = kcTplTerpilih ? 'template' : 'teks';
            kirimPesan(jenis, isi);
        });

        kcPasangKlik();

        function kcMuatDaftar() {
            fetch(KC_PERCAKAPAN, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => {
                    if (!d.ok) return;
                    const wStatus = { menunggu: '#fef3c7;color:#b45309', diproses: '#dbeafe;color:#1d4ed8', diantar: '#e0e7ff;color:#4338ca' };
                    kcList.innerHTML = d.percakapan.length
                        ? d.percakapan.map(function (c) {
                            const aktif = String(c.id_penjualan) === String(kcAktif) ? ' aktif' : '';
                            const badge = c.belum_dibaca > 0 ? '<span class="kc-badge">' + c.belum_dibaca + '</span>' : '';
                            const awalan = c.pengirim === 'kurir' ? '<span class="text-danger">Anda: </span>' : '';
                            const st = (c.status_pesanan || '').replace('_', ' ');
                            const stWarna = wStatus[c.status_pesanan] || '#f3f4f6;color:#374151';
                            return '<a href="#" class="kc-item' + aktif + '"'
                                + ' data-id="' + c.id_penjualan + '"'
                                + ' data-nama="' + kcEscape(c.nama) + '"'
                                + ' data-struk="' + kcEscape(c.no_struk) + '"'
                                + ' data-status="' + kcEscape(c.status_pesanan) + '">'
                                + '<div class="kc-ava">' + kcEscape((c.nama || '?').charAt(0).toUpperCase()) + '</div>'
                                + '<div class="flex-grow-1 min-w-0">'
                                + '<div class="d-flex justify-content-between align-items-start gap-2">'
                                + '<div class="kc-nama">' + kcEscape(c.nama) + '</div>'
                                + '<small class="text-muted" style="font-size:10px">' + c.waktu + '</small></div>'
                                + '<div class="kc-struk">' + kcEscape(c.no_struk) + ' &bull; '
                                + '<span class="kc-status" style="background:' + stWarna + '">' + st.toUpperCase() + '</span></div>'
                                + '<div class="kc-pesan">' + awalan + kcEscape(c.pesan_terakhir) + '</div></div>'
                                + badge + '</a>';
                        }).join('')
                        : '<div class="text-center py-5 px-3 text-muted small"><div style="font-size:40px">💬</div>Belum ada pesanan yang Anda antar.</div>';
                    kcPasangKlik();
                })
                .catch(() => {});
        }

        // Buka pesanan pertama, atau pesanan dari ?pesanan= (tombol Chat di app kurir).
        const mintaId = new URLSearchParams(location.search).get('pesanan');
        const target = mintaId ? kcList.querySelector('.kc-item[data-id="' + mintaId + '"]') : null;
        const awal = target || kcList.querySelector('.kc-item');
        if (awal) kcBuka(awal);

        setInterval(kcMuat, 3000);
        setInterval(kcMuatDaftar, 6000);
    })();
</script>
@endpush

