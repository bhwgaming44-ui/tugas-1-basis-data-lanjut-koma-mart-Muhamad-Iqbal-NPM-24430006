@php
    use Illuminate\Support\Str;

    $g = trim((string) ($b->gambar ?? ''));
    if ($g === '') {
        $src = null;
    } elseif (Str::startsWith($g, ['http://', 'https://', '//', 'data:'])) {
        $src = $g;
    } else {
        $g = ltrim($g, '/');
        $src = file_exists(public_path($g))
            ? asset($g)
            : asset('storage/' . preg_replace('#^(public/|storage/)#', '', $g));
    }

    $placeholder = 'data:image/svg+xml;utf8,' . rawurlencode(
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><rect width="200" height="200" fill="#fff5f5"/>'
        . '<path d="M60 80l40-20 40 20v50l-40 20-40-20z" fill="none" stroke="#e11d2e" stroke-width="6" stroke-linejoin="round"/>'
        . '<path d="M60 80l40 20 40-20M100 100v50" fill="none" stroke="#e11d2e" stroke-width="6" stroke-linejoin="round"/></svg>'
    );

    $disc = $b->harga_jual_umum > 0
        ? (int) round(($b->harga_jual_umum - $b->harga_jual_anggota) / $b->harga_jual_umum * 100)
        : 0;
    $habis = $b->stok < 1;
    $menipis = !$habis && $b->stok <= ($b->stok_minimum ?? 5);
@endphp

<article class="km-card product-item-km" data-id="{{ $b->id_barang }}">
    @if($disc > 0)
        <span class="km-disc">{{ $disc }}%</span>
    @endif

    {{-- Tombol ganti foto produk realtime (hanya untuk petugas, sembunyikan untuk user/kurir) --}}
    @if(\App\Helpers\AkunHelper::isPetugas())
        <div class="km-petugas-edit-badge" title="Ganti foto produk langsung dari laptop">
            <label for="input-foto-km-{{ $b->id_barang }}" class="km-btn-edit-foto">
                <i class="fa-solid fa-camera"></i> <span>Ubah Foto</span>
            </label>
            <input type="file" id="input-foto-km-{{ $b->id_barang }}" class="d-none input-upload-km"
                   data-id="{{ $b->id_barang }}" accept="image/*">
        </div>
    @endif

    <div class="km-img">
        <img src="{{ $src ?? $placeholder }}" alt="{{ $b->nama_barang }}" loading="lazy"
             class="img-produk-target-{{ $b->id_barang }}"
             onerror="this.onerror=null;this.src='{{ $placeholder }}';">
    </div>

    <div class="km-body">
        <div class="km-cat-name">{{ $b->kategori ? Str::limit($b->kategori->nama_kategori, 20) : 'Kopma Mart' }}</div>
        <div class="km-name" title="{{ $b->nama_barang }}">{{ $b->nama_barang }}</div>

        <div class="km-price">
            <span class="now">Rp {{ number_format($b->harga_jual_anggota, 0, ',', '.') }}</span>
            @if($disc > 0)
                <span class="old">Rp {{ number_format($b->harga_jual_umum, 0, ',', '.') }}</span>
            @endif
            <div class="km-member-tag"><i class="fa-solid fa-id-card me-1"></i>Harga member</div>
        </div>

        <div class="km-foot">
            <span class="km-stok {{ $menipis || $habis ? 'low' : '' }}">
                {{ $habis ? 'Stok habis' : ($menipis ? 'Sisa ' . $b->stok : 'Stok ' . $b->stok) }}
            </span>
            <form action="{{ route('belanja.tambah') }}" method="POST">
                @csrf
                <input type="hidden" name="id_barang" value="{{ $b->id_barang }}">
                <button type="submit" class="km-add" @disabled($habis)>
                    <i class="fa-solid fa-plus"></i> Tambah
                </button>
            </form>
        </div>
    </div>
</article>