{{-- ========== CHATBOT KOPMA MART — gaya WhatsApp / Meta AI (TANPA nomor HP) ========== --}}
<style>
    #chatFab { position: fixed; right: 14px; bottom: 64px; z-index: 9998; width: 56px; height: 56px;
        border: none; border-radius: 50%; background: #c8102e; color: #fff; font-size: 24px; cursor: pointer;
        box-shadow: 0 8px 24px rgba(37,211,102,.45); transition: transform .2s; }
    #chatFab:hover { transform: scale(1.07); }
    #chatPanel { position: fixed; right: 14px; bottom: 128px; z-index: 9998; width: 370px; max-width: calc(100vw - 28px);
        height: 560px; max-height: calc(100vh - 150px); background: #efeae2; border-radius: 14px; overflow: hidden;
        display: none; flex-direction: column; box-shadow: 0 18px 50px rgba(0,0,0,.30);
        border: 1px solid #d1d7db; font-size: 14px; }
    #chatPanel.open { display: flex; }
    .chat-head { background: #7f1d1d; color: #fff; padding: 10px 12px; display: flex; align-items: center;
        justify-content: space-between; gap: 8px; }
    .chat-ava { width: 38px; height: 38px; border-radius: 50%; background: #c8102e; color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 18px; flex: 0 0 auto; }
    .chat-cart { background: #c8102e; color: #fff; font-size: 11px; font-weight: 700; border-radius: 20px;
        padding: 3px 9px; }
    .chat-close { background: transparent; border: none; color: #fff; font-size: 22px; line-height: 1; cursor: pointer; }
    .chat-body { flex: 1; overflow-y: auto; padding: 12px 10px; display: flex; flex-direction: column; gap: 8px;
        background: #efeae2;
        background-image: radial-gradient(rgba(0,0,0,.04) 1px, transparent 1px); background-size: 18px 18px; }
    .chat-msg { max-width: 82%; padding: 8px 11px; border-radius: 10px; line-height: 1.45; white-space: pre-wrap;
        word-break: break-word; box-shadow: 0 1px 1px rgba(0,0,0,.08); position: relative; }
    .chat-msg.bot { background: #fff; align-self: flex-start; border-top-left-radius: 2px; color: #111b21; }
    .chat-msg.user { background: #fee2e2; align-self: flex-end; border-top-right-radius: 2px; color: #111b21; }
    .chat-src { display: block; font-size: 10px; color: #667781; margin-top: 4px; }
    .chat-msg.chat-act { display: flex; flex-direction: column; gap: 6px; }
    .chat-act-row { display: flex; flex-wrap: wrap; gap: 6px; }
    .chat-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700;
        padding: 7px 12px; border-radius: 8px; text-decoration: none; border: 1px solid #c8102e; color: #c8102e; background: #fff; }
    .chat-btn:hover { background: #c8102e; color: #fff; }
    .chat-chips { display: flex; gap: 6px; overflow-x: auto; padding: 8px 10px; background: #efeae2;
        border-top: 1px solid #d9d2c5; scrollbar-width: thin; }
    .chat-chip { flex: 0 0 auto; font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 20px;
        background: #fff; border: 1px solid #c8102e; color: #991b1b; cursor: pointer; }
    .chat-chip:hover { background: #fee2e2; }
    .chat-input { display: flex; gap: 8px; padding: 8px 10px; background: #f0f2f5; }
    .chat-input input { flex: 1; border: none; border-radius: 20px; padding: 9px 14px; font-size: 13px; outline: none; }
    .chat-send { width: 40px; height: 40px; border: none; border-radius: 50%; background: #c8102e; color: #fff;
        font-size: 15px; cursor: pointer; flex: 0 0 auto; }
    .chat-typing span { display: inline-block; width: 7px; height: 7px; margin: 0 1.5px; border-radius: 50%;
        background: #8696a0; animation: chatBlink 1.2s infinite both; }
    .chat-typing span:nth-child(2) { animation-delay: .2s; }
    .chat-typing span:nth-child(3) { animation-delay: .4s; }
    @keyframes chatBlink { 0%, 80%, 100% { opacity: .25; transform: translateY(0); } 40% { opacity: 1; transform: translateY(-3px); } }

    /* ===== Kartu Produk (LIST BARANG) di dalam chat ===== */
    .chat-card { background:#fff; align-self:stretch; border-radius:10px; padding:9px 10px;
        box-shadow:0 1px 1px rgba(0,0,0,.08); border:1px solid #e6e6e6; }
    .chat-card-head { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; }
    .chat-card-name { font-weight:700; font-size:13px; color:#111b21; line-height:1.3; }
    .chat-card-kat { display:inline-block; font-size:10px; font-weight:700; color:#991b1b;
        background:#fee2e2; border-radius:10px; padding:2px 8px; margin-top:3px; }
    .chat-card-price { text-align:right; flex:0 0 auto; }
    .chat-card-price .umum { display:block; font-weight:800; color:#c8102e; font-size:13px; }
    .chat-card-price .member { display:block; font-size:10px; color:#667781; }
    .chat-card-foot { display:flex; justify-content:space-between; align-items:center; margin-top:7px; gap:8px; }
    .chat-stok { font-size:11px; font-weight:700; }
    .chat-stok.ada { color:#0a7d34; }
    .chat-stok.habis { color:#dc2626; }
    .chat-order { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700;
        text-decoration:none; color:#fff; background:#c8102e; border-radius:8px; padding:6px 11px; }
    .chat-order:hover { background:#1eb85a; color:#fff; }
    .chat-order.disabled { background:#b0b6ba; pointer-events:none; }
    .chat-cat-row { display:flex; flex-wrap:wrap; gap:6px; align-self:stretch; }
    .chat-cat { font-size:12px; font-weight:600; padding:5px 11px; border-radius:20px; background:#fff;
        border:1px solid #c8102e; color:#991b1b; cursor:pointer; }
    .chat-cat:hover { background:#fee2e2; }
    .chat-cat small { color:#8696a0; font-weight:600; }

    /* Tombol "Tambah" ke keranjang */
    .chat-add { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:700;
        text-decoration:none; color:#fff; background:#0a7d34; border:none; border-radius:8px; padding:6px 11px; cursor:pointer; }
    .chat-add:hover { background:#08612a; }
    .chat-add.disabled { background:#b0b6ba; cursor:not-allowed; }

    /* Kotak ringkasan keranjang (multi-barang) */
    .chat-cart-box { background:#fff; align-self:stretch; border:1px solid #d1d7db; border-radius:10px;
        padding:9px 11px; box-shadow:0 1px 1px rgba(0,0,0,.06); }
    .chat-cart-title { font-weight:800; font-size:13px; color:#111b21; margin-bottom:6px; }
    .chat-cart-row { display:flex; justify-content:space-between; font-size:12px; color:#3b4a54; padding:2px 0; }
    .chat-cart-total { display:flex; justify-content:space-between; font-weight:800; font-size:13px;
        color:#7f1d1d; border-top:1px dashed #cbd5d0; margin-top:6px; padding-top:6px; }
    .chat-cart-actions { display:flex; gap:8px; margin-top:9px; }
    .chat-clear { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:700;
        color:#dc2626; background:#fff; border:1px solid #dc2626; border-radius:8px; padding:6px 11px; cursor:pointer; }
    .chat-clear:hover { background:#fdecea; }
    .chat-cart.chat-cart-pop { animation: cartPop .3s ease; }
    @keyframes cartPop { 0%{transform:scale(1);} 50%{transform:scale(1.25);} 100%{transform:scale(1);} }
</style>

<button id="chatFab" title="Chatbot KOPMA MART" aria-label="Buka chatbot"><i class="fa-brands fa-whatsapp"></i></button>

<div id="chatPanel">
    <div class="chat-head">
        <div class="d-flex align-items-center gap-2">
            <div class="chat-ava"><i class="fa-solid fa-robot"></i></div>
            <div>
                <div class="fw-bold" style="font-size:14px;">KOPMA MART Bot</div>
                <div style="font-size:11px; opacity:.9;"><span style="color:#7bf1a8;">&#9679;</span> online &bull; belanja, bayar &amp; struk tanpa nomor</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span id="chatCart" class="chat-cart" style="display:none;">&#128722; 0</span>
            <button id="chatClose" class="chat-close" aria-label="Tutup chatbot">&times;</button>
        </div>
    </div>

    <div class="chat-body" id="chatBody">
        <div class="chat-msg bot">Halo! &#128075; Saya <b>KOPMA MART Bot</b> seperti Meta AI — semua urusan toko beres lewat chat:
            lihat <b>daftar barang</b>, kategori &amp; stok, lalu pesan langsung ke <b>WhatsApp Business</b> kami.
            Mau lihat produk apa?</div>
    </div>

    <div class="chat-chips" id="chatChips"></div>

    <div class="chat-input">
        <input type="text" id="chatInput" maxlength="400" placeholder="Ketik pesan, mis. &quot;belanja&quot;..." autocomplete="off">
        <button id="chatSend" class="chat-send" aria-label="Kirim"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
</div>
<script>
(function () {
    const panel = document.getElementById('chatPanel');
    const body = document.getElementById('chatBody');
    const input = document.getElementById('chatInput');
    const send = document.getElementById('chatSend');
    const chipsBox = document.getElementById('chatChips');
    const cartBadge = document.getElementById('chatCart');

    const DEFAULT_CHIPS = ['Belanja sekarang', 'Lihat kategori', 'Keranjang saya', 'Pesan sekarang', 'Hubungi admin'];
    let busy = false;
    let katalogDimuat = false;

    document.getElementById('chatFab').addEventListener('click', () => {
        panel.classList.toggle('open');
        // Saat panel pertama kali dibuka, langsung muat LIST BARANG (katalog)
        if (panel.classList.contains('open') && !katalogDimuat) {
            katalogDimuat = true;
            muatKatalog();
        }
    });
    document.getElementById('chatClose').addEventListener('click', () => panel.classList.remove('open'));

    function renderChips(list) {
        chipsBox.innerHTML = '';
        (list && list.length ? list : DEFAULT_CHIPS).forEach(label => {
            const c = document.createElement('span');
            c.className = 'chat-chip';
            c.textContent = label;
            c.addEventListener('click', () => kirim(label));
            chipsBox.appendChild(c);
        });
    }

    function addMsg(text, who, source) {
        const div = document.createElement('div');
        div.className = 'chat-msg ' + who;
        // Tampilkan baris baru (\n) dari balasan database
        div.style.whiteSpace = 'pre-wrap';
        div.textContent = text;
        if (source) {
            const s = document.createElement('span');
            s.className = 'chat-src';
            s.textContent = source === 'ai' ? 'AI' : 'dijawab dari database toko';
            div.appendChild(s);
        }
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
        return div;
    }

    function rupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }

    // Render daftar kategori (chip dengan jumlah produk)
    function renderCategories(list) {
        if (!list || !list.length) return;
        const row = document.createElement('div');
        row.className = 'chat-cat-row';
        list.forEach(c => {
            const b = document.createElement('span');
            b.className = 'chat-cat';
            b.appendChild(document.createTextNode(c.nama + ' '));
            const small = document.createElement('small');
            small.textContent = '(' + c.jumlah + ')';
            b.appendChild(small);
            b.addEventListener('click', () => kirim(c.nama));
            row.appendChild(b);
        });
        body.appendChild(row);
        body.scrollTop = body.scrollHeight;
    }

    // Render LIST BARANG sebagai kartu produk + tombol "Pesan via WA"
    function renderProducts(list) {
        if (!list || !list.length) return;
        list.forEach(p => {
            const card = document.createElement('div');
            card.className = 'chat-card';

            const head = document.createElement('div');
            head.className = 'chat-card-head';

            const left = document.createElement('div');
            const name = document.createElement('div');
            name.className = 'chat-card-name';
            name.textContent = p.nama;
            left.appendChild(name);
            if (p.kategori) {
                const kat = document.createElement('span');
                kat.className = 'chat-card-kat';
                kat.textContent = p.kategori;
                left.appendChild(kat);
            }

            const price = document.createElement('div');
            price.className = 'chat-card-price';
            const umum = document.createElement('span');
            umum.className = 'umum';
            umum.textContent = rupiah(p.harga_umum);
            const member = document.createElement('span');
            member.className = 'member';
            member.textContent = 'member ' + rupiah(p.harga_anggota);
            price.appendChild(umum);
            price.appendChild(member);

            head.appendChild(left);
            head.appendChild(price);

            const foot = document.createElement('div');
            foot.className = 'chat-card-foot';
            const stok = document.createElement('span');
            stok.className = 'chat-stok ' + (p.habis ? 'habis' : 'ada');
            stok.textContent = p.habis ? 'Stok habis' : ('Stok: ' + p.stok);
            foot.appendChild(stok);

            // Tombol "Tambah" -> masukkan produk ke keranjang (bisa banyak barang)
            const add = document.createElement('button');
            add.type = 'button';
            add.className = 'chat-add' + (p.habis ? ' disabled' : '');
            add.title = p.habis ? 'Stok habis' : 'Tambah ke keranjang';
            if (p.habis) add.disabled = true;
            const ic = document.createElement('i');
            ic.className = 'fa-solid fa-cart-plus';
            add.appendChild(ic);
            add.appendChild(document.createTextNode(' Tambah'));
            add.addEventListener('click', () => kirim('tambah ' + p.nama));
            foot.appendChild(add);

            card.appendChild(head);
            card.appendChild(foot);
            body.appendChild(card);
        });
        body.scrollTop = body.scrollHeight;
    }

    // Render ringkasan keranjang + tombol "Pesan Sekarang" (kirim semua via WA)
    function renderCart(items) {
        // Hapus tampilan keranjang lama agar tidak menumpuk
        document.querySelectorAll('.chat-cart-box').forEach(el => el.remove());
        if (!items || !items.length) return;

        const box = document.createElement('div');
        box.className = 'chat-cart-box';

        const title = document.createElement('div');
        title.className = 'chat-cart-title';
        title.innerHTML = '<i class="fa-solid fa-basket-shopping"></i> Keranjang (' + items.length + ')';
        box.appendChild(title);

        let total = 0;
        items.forEach(it => {
            total += it.subtotal;
            const row = document.createElement('div');
            row.className = 'chat-cart-row';
            const label = document.createElement('span');
            label.textContent = it.nama + ' x' + it.qty;
            const sub = document.createElement('span');
            sub.textContent = rupiah(it.subtotal);
            row.appendChild(label);
            row.appendChild(sub);
            box.appendChild(row);
        });

        const totalRow = document.createElement('div');
        totalRow.className = 'chat-cart-total';
        totalRow.innerHTML = '<span>Total</span><span>' + rupiah(total) + '</span>';
        box.appendChild(totalRow);

        const actions = document.createElement('div');
        actions.className = 'chat-cart-actions';

        const checkout = document.createElement('button');
        checkout.type = 'button';
        checkout.className = 'chat-order';
        checkout.innerHTML = '<i class="fa-brands fa-whatsapp"></i> Pesan Sekarang via WA';
        checkout.addEventListener('click', () => kirim('pesan sekarang'));
        actions.appendChild(checkout);

        const clear = document.createElement('button');
        clear.type = 'button';
        clear.className = 'chat-clear';
        clear.innerHTML = '<i class="fa-solid fa-trash-can"></i> Kosongkan';
        clear.addEventListener('click', () => kirim('kosongkan'));
        actions.appendChild(clear);

        box.appendChild(actions);
        body.appendChild(box);
        body.scrollTop = body.scrollHeight;
    }

    // Perbarui badge jumlah item di header chatbot
    function updateCartBadge(count) {
        if (!cartBadge) return;
        if (count > 0) {
            cartBadge.style.display = '';
            cartBadge.innerHTML = '&#128722; ' + count;
            cartBadge.classList.remove('chat-cart-pop');
            void cartBadge.offsetWidth; // restart animasi
            cartBadge.classList.add('chat-cart-pop');
        } else {
            cartBadge.style.display = 'none';
        }
    }

    function addTyping() {
        const div = document.createElement('div');
        div.className = 'chat-msg bot chat-typing';
        div.innerHTML = '<span></span><span></span><span></span>';
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
        return div;
    }

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content
            || (decodeURIComponent((document.cookie.match(/XSRF-TOKEN=([^;]+)/) || [])[1] || '') || '');
    }

    // Muat LIST BARANG (katalog) + kategori dari server (tanpa chat pengguna)
    async function muatKatalog() {
        try {
            const res = await fetch('{{ route('chatbot.katalog') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;
            const data = await res.json();
            renderCategories(data.categories);
            renderProducts(data.products);
        } catch (e) { /* diamkan jika gagal */ }
    }

    async function kirim(pesan) {
        const teks = (pesan || '').trim();
        if (!teks || busy) return;
        busy = true;
        input.value = '';
        addMsg(teks, 'user');
        const typing = addTyping();

        try {
            const res = await fetch('{{ route('chatbot.ask') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    'X-XSRF-TOKEN': csrf(),
                },
                body: JSON.stringify({ message: teks }),
            });
            const data = await res.json();
            typing.remove();
            if (res.ok && data.reply) {
                addMsg(data.reply, 'bot', data.source);
                // Tampilkan kategori (jika ada)
                if (data.categories) renderCategories(data.categories);
                // Tampilkan LIST BARANG sebagai kartu produk
                if (data.products) renderProducts(data.products);
                // Tombol aksi WhatsApp (kirim keranjang / hubungi admin)
                if (data.wa_link) {
                    const div = document.createElement('div');
                    div.className = 'chat-act-row';
                    const a = document.createElement('a');
                    a.className = 'chat-order';
                    a.href = data.wa_link;
                    a.target = '_blank';
                    a.rel = 'noopener';
                    const ic = document.createElement('i');
                    ic.className = 'fa-brands fa-whatsapp';
                    a.appendChild(ic);
                    a.appendChild(document.createTextNode(data.wa_cart_count ? ' Kirim Pesanan via WhatsApp' : ' Chat Admin via WhatsApp'));
                    div.appendChild(a);
                    body.appendChild(div);
                    body.scrollTop = body.scrollHeight;
                }
                // Perbarui ringkasan keranjang (multi-barang)
                if (data.cart_items !== undefined) renderCart(data.cart_items);
                // Perbarui badge jumlah item di header
                updateCartBadge(data.cart || 0);
                renderChips(data.chips);
            } else {
                addMsg('Maaf, terjadi gangguan. Coba lagi ya.', 'bot');
            }
        } catch (e) {
            typing.remove();
            addMsg('Koneksi bermasalah — silakan coba lagi.', 'bot');
        } finally {
            busy = false;
        }
    }

    send.addEventListener('click', () => kirim(input.value));
    input.addEventListener('keydown', e => { if (e.key === 'Enter') kirim(input.value); });

    renderChips(DEFAULT_CHIPS);
})();
</script>
