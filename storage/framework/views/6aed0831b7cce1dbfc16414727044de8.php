<?php if(count($slides) > 0): ?>
<style>
    .promo-slider-wrap { position: relative; overflow: hidden; border-radius: 20px; box-shadow: 0 10px 26px rgba(0,0,0,0.14); border: 2px solid rgba(255,255,255,0.4); }
    .promo-track { display: flex; transition: transform .6s cubic-bezier(.4,0,.2,1); }
    /* Satu "halaman" slide = 2 promo pendek berdampingan seperti referensi (model awal, jangan panjang) */
    .promo-slide { min-width: 100%; display: flex; gap: 16px; padding: 14px; box-sizing: border-box; }
    .promo-slide-inner { flex: 1 1 0; min-width: 0; height: 230px; display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 22px 30px; color: #fff; position: relative; overflow: hidden; border-radius: 16px; }
    @media (max-width: 767.98px) {
        .promo-slide { flex-direction: column; }
        .promo-slide-inner { height: 170px; padding: 16px 20px; }
        .promo-slide-inner h4 { font-size: 17px; }
        .promo-slide-txt { max-width: 78%; }
    }
    .promo-slide-inner.has-img { background-size: cover; background-position: center; background-repeat: no-repeat; }
    .promo-slide-inner.has-img::before { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(12,8,8,0.78) 0%, rgba(12,8,8,0.45) 36%, rgba(12,8,8,0.10) 60%, rgba(12,8,8,0) 78%); z-index: 1; }
    .promo-slide-inner::after { content: ""; position: absolute; right: -60px; top: -60px; width: 260px; height: 260px; border-radius: 50%; background: rgba(255,255,255,0.14); }
    .promo-slide-txt { position: relative; z-index: 2; max-width: 72%; }
    .promo-badge { display: inline-block; background: rgba(255,255,255,0.94); color: #c8102e; font-weight: 800; font-size: 11px; letter-spacing: 1px; padding: 4px 12px; border-radius: 50px; margin-bottom: 8px; }
    .promo-slide-inner h4 { font-weight: 900; margin: 0 0 4px; font-size: 20px; line-height: 1.2; }
    .promo-slide-inner p { margin: 0; font-size: 13px; opacity: 0.92; }
    .promo-cta { display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; background: #fff; color: #b91c1c; font-weight: 800; font-size: 13px; padding: 8px 18px; border-radius: 50px; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.18); transition: transform .15s ease; }
    .promo-cta:hover { transform: translateY(-2px); color: #7f1d1d; }
    .promo-ikon { position: relative; z-index: 2; font-size: 54px; opacity: 1; }
    .promo-ikon i { opacity: 0.45; }
    .promo-ikon img { opacity: 1; }
    .promo-dots { display: flex; justify-content: center; gap: 8px; margin-top: 12px; }
    .promo-dot { width: 34px; height: 34px; border-radius: 10px; border: 1.5px solid #e5e7eb; background: #fff; color: #374151; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; transition: all .2s ease; padding: 0; }
    .promo-dot.on { background: linear-gradient(135deg,#c8102e,#ef4444); border-color: transparent; color: #fff; box-shadow: 0 4px 10px rgba(200,16,46,0.35); }
</style>

<div class="promo-slider-wrap mb-4" id="promoSlider">
    <div class="promo-track" id="promoTrack">
        <?php
            // Dikelompokkan 2 promo per halaman slide (geser 2 gambar tiap swipe)
            $__promoPages = collect($slides)->chunk(2);
        ?>
        <?php $__currentLoopData = $__promoPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $__pi => $__page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="promo-slide">
            <?php $__currentLoopData = $__page; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j => $sl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $__fokus  = $sl instanceof \App\Models\PromoSlide ? $sl->posisiCrop() : ($sl['fokus'] ?? 'center');
                $__bg = ($sl['gambar'] ?? null)
                    ? "background-image:url('".asset($sl['gambar'])."');background-size:cover;background-position:".$__fokus.";"
                    : ("background:".($sl['warna'] ?? $warnaDefault[($__pi * 2 + $j) % count($warnaDefault)]).";");
            ?>
            <div class="promo-slide-inner <?php echo e(($sl['gambar'] ?? null) ? 'has-img' : ''); ?>" style="<?php echo e($__bg); ?>">
                <div class="promo-slide-txt">
                    <?php if($sl['badge']): ?> <span class="promo-badge"><i class="fa-solid fa-bolt me-1"></i><?php echo e($sl['badge']); ?></span> <?php endif; ?>
                    <h4><?php echo e($sl['judul']); ?></h4>
                    <p><?php echo e($sl['sub'] ?? ''); ?></p>
                    <a href="<?php echo e($sl['url'] ?? route('toko.index')); ?>" class="promo-cta">
                        <?php echo e($sl['cta'] ?? 'Lihat Promo'); ?> <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <?php if(!($sl['gambar'] ?? null)): ?>
                <div class="promo-ikon d-none d-md-block"><i class="fa-solid <?php echo e($sl['ikon'] ?? 'fa-tags'); ?>"></i></div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<div class="promo-dots mb-4" id="promoDots">
    <?php $__currentLoopData = $__promoPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $__pi => $__page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <button type="button" class="promo-dot <?php echo e($__pi === 0 ? 'on' : ''); ?>" data-idx="<?php echo e($__pi); ?>" aria-label="Slide promo halaman <?php echo e($__pi + 1); ?>"><?php echo e($__pi + 1); ?></button>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<script>
    (function() {
        var track = document.getElementById('promoTrack');
        var dotsWrap = document.getElementById('promoDots');
        var slider = document.getElementById('promoSlider');
        if (!track || !dotsWrap || !slider) return;
        var total = track.children.length;
        if (total === 0) return;
        var idx = 0, timer = null;
        function goSlide(i) {
            idx = ((i % total) + total) % total;
            track.style.transform = 'translateX(-' + (idx * 100) + '%)';
            dotsWrap.querySelectorAll('.promo-dot').forEach(function(d, k) { d.classList.toggle('on', k === idx); });
        }
        function startAuto() { stopAuto(); timer = setInterval(function() { goSlide(idx + 1); }, 5000); }
        function stopAuto() { if (timer) { clearInterval(timer); timer = null; } }
        dotsWrap.querySelectorAll('.promo-dot').forEach(function(d) {
            d.addEventListener('click', function() { goSlide(parseInt(this.dataset.idx, 10)); startAuto(); });
        });
        slider.addEventListener('mouseenter', stopAuto);
        slider.addEventListener('mouseleave', startAuto);
        goSlide(0);
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) startAuto();
    })();
</script>
<?php endif; ?>
<?php /**PATH C:\Users\ASUS\Documents\Dokumen\semester 5 file pdf spada\basis data lanjut semester 5\Dokumen coding python\kopma_npm\laravel_kopma\resources\views/petugas/promo/_slider.blade.php ENDPATH**/ ?>