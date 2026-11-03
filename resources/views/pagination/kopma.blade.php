{{-- Paginasi KOPMA: tombol kecil ijo-putih profesional (chevron ‹ ›), gaya Bootstrap 5 --}}
@if ($paginator->hasPages())
<nav class="kopma-pagination" role="navigation" aria-label="Navigasi halaman">
    <ul class="pagination mb-0">
        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true"><span class="page-link kp-nav">&lsaquo;</span></li>
        @else
            <li class="page-item"><a class="page-link kp-nav" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">&lsaquo;</a></li>
        @endif

        {{-- Nomor halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link kp-nav" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">&rsaquo;</a></li>
        @else
            <li class="page-item disabled" aria-disabled="true"><span class="page-link kp-nav">&rsaquo;</span></li>
        @endif
    </ul>
</nav>
@endif