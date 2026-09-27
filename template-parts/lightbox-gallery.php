<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Lightbox Gallery Modal — Contoh Desain Produk
 */

global $post;
$main_id     = get_post_thumbnail_id( $post->ID );
$gallery_ids = get_post_meta( $post->ID, '_produk_gallery', true );
$ids         = array();

if ( $main_id )    $ids[] = $main_id;
if ( $gallery_ids ) {
    foreach ( explode( ',', $gallery_ids ) as $gid ) {
        $gid = trim( $gid );
        if ( $gid && $gid !== $main_id ) $ids[] = $gid;
    }
}

if ( empty( $ids ) ) return;
?>

<div id="design-lightbox" class="design-lightbox" role="dialog" aria-modal="true" aria-label="Galeri Desain Produk" hidden>
    <div class="lightbox-overlay" id="lightbox-overlay"></div>
    <div class="lightbox-container">
        <button class="lightbox-close" id="lightbox-close" aria-label="Tutup galeri">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <div class="lightbox-main">
            <button class="lightbox-nav lightbox-prev" id="lightbox-prev" aria-label="Foto sebelumnya">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>

            <div class="lightbox-img-wrap" id="lightbox-img-wrap">
                <img src="" alt="" id="lightbox-img" class="lightbox-img" loading="eager">
                <div class="lightbox-counter" id="lightbox-counter">1 / <?php echo count( $ids ); ?></div>
            </div>

            <button class="lightbox-nav lightbox-next" id="lightbox-next" aria-label="Foto berikutnya">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

        <div class="lightbox-thumbs" id="lightbox-thumbs">
            <?php foreach ( $ids as $i => $id ) :
                $src  = wp_get_attachment_image_url( $id, 'medium' );
                $full = wp_get_attachment_image_url( $id, 'large' );
                $alt  = get_post_field( 'post_excerpt', $id ) ?: get_the_title( $post->ID ) . ' - Foto ' . ( $i + 1 );
                if ( ! $src ) continue;
            ?>
            <button class="lightbox-thumb<?php echo $i === 0 ? ' active' : ''; ?>"
                    data-full="<?php echo esc_url( $full ); ?>"
                    data-alt="<?php echo esc_attr( $alt ); ?>"
                    data-index="<?php echo $i; ?>"
                    aria-label="Lihat foto <?php echo $i + 1; ?>">
                <img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
(function() {
    var lightbox  = document.getElementById('design-lightbox');
    var trigger   = document.getElementById('btn-view-designs');
    var closeBtn  = document.getElementById('lightbox-close');
    var overlay   = document.getElementById('lightbox-overlay');
    var mainImg   = document.getElementById('lightbox-img');
    var counter   = document.getElementById('lightbox-counter');
    var prevBtn   = document.getElementById('lightbox-prev');
    var nextBtn   = document.getElementById('lightbox-next');
    var thumbBtns = document.querySelectorAll('.lightbox-thumb');
    var total     = thumbBtns.length;
    var current   = 0;

    if (!lightbox || !trigger) return;

    function goTo(idx) {
        if (idx < 0) idx = total - 1;
        if (idx >= total) idx = 0;
        current = idx;
        var btn = thumbBtns[idx];
        mainImg.style.opacity = '0';
        setTimeout(function() {
            mainImg.src = btn.dataset.full;
            mainImg.alt = btn.dataset.alt;
            mainImg.style.opacity = '1';
        }, 150);
        counter.textContent = (idx + 1) + ' / ' + total;
        thumbBtns.forEach(function(b) { b.classList.remove('active'); });
        btn.classList.add('active');
        btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    function open() {
        goTo(0);
        lightbox.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
        closeBtn.focus();
    }

    function close() {
        lightbox.setAttribute('hidden','');
        document.body.style.overflow = '';
        if (trigger) trigger.focus();
    }

    trigger.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', close);
    prevBtn.addEventListener('click', function() { goTo(current - 1); });
    nextBtn.addEventListener('click', function() { goTo(current + 1); });

    thumbBtns.forEach(function(btn) {
        btn.addEventListener('click', function() { goTo(parseInt(btn.dataset.index)); });
    });

    document.addEventListener('keydown', function(e) {
        if (lightbox.hasAttribute('hidden')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft')  goTo(current - 1);
        if (e.key === 'ArrowRight') goTo(current + 1);
    });

    var startX = 0;
    lightbox.addEventListener('touchstart', function(e) { startX = e.touches[0].clientX; }, { passive: true });
    lightbox.addEventListener('touchend', function(e) {
        var diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) diff > 0 ? goTo(current + 1) : goTo(current - 1);
    });
})();
</script>
