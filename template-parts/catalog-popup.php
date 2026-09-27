<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Catalog Popup — "Dapatkan Katalog Gratis"
 * Ditampilkan setelah 8 detik atau saat exit-intent (mouse ke atas)
 */
$wa_number = get_theme_mod( 'tokoku_wa_number', '6281234567890' );
$site_name = get_bloginfo( 'name' );
?>

<div id="catalog-popup" class="catalog-popup" role="dialog" aria-modal="true" aria-labelledby="catalog-popup-title" hidden>
    <div class="catalog-popup-overlay" id="catalog-popup-overlay"></div>
    <div class="catalog-popup-card">
        <button class="catalog-popup-close" id="catalog-popup-close" aria-label="Tutup popup">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        <div class="catalog-popup-icon">🎁</div>
        <h3 class="catalog-popup-title" id="catalog-popup-title">Dapatkan Katalog Plakat Gratis!</h3>
        <p class="catalog-popup-desc">Ratusan referensi desain plakat akrilik, kayu, resin & kristal — langsung di WhatsApp Anda, tanpa biaya.</p>

        <div class="catalog-popup-features">
            <span>✅ 100+ Desain Terbaru</span>
            <span>✅ Harga Grosir Spesial</span>
            <span>✅ Konsultasi Gratis</span>
        </div>

        <a href="https://wa.me/<?php echo esc_attr( $wa_number ); ?>?text=<?php echo urlencode( 'Halo ' . $site_name . '! Saya ingin minta katalog plakat gratis 🙏' ); ?>"
           target="_blank" rel="noopener"
           class="catalog-popup-btn"
           id="catalog-popup-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2z"/></svg>
            Minta Katalog via WhatsApp
        </a>
        <button type="button" class="catalog-popup-skip" id="catalog-popup-skip">Tidak, terima kasih</button>
    </div>
</div>

<script>
(function() {
    var popup    = document.getElementById('catalog-popup');
    var closeBtn = document.getElementById('catalog-popup-close');
    var overlay  = document.getElementById('catalog-popup-overlay');
    var skipBtn  = document.getElementById('catalog-popup-skip');
    if (!popup) return;

    // Don't show if dismissed in this session
    if (sessionStorage.getItem('catalogPopupDismissed')) return;

    function show() {
        popup.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
    }

    function dismiss() {
        popup.setAttribute('hidden', '');
        document.body.style.overflow = '';
        sessionStorage.setItem('catalogPopupDismissed', '1');
    }

    // Show after 10 seconds
    var timer = setTimeout(show, 10000);

    // Exit intent (desktop)
    document.addEventListener('mouseleave', function handler(e) {
        if (e.clientY < 5) {
            clearTimeout(timer);
            show();
            document.removeEventListener('mouseleave', handler);
        }
    });

    closeBtn.addEventListener('click', dismiss);
    overlay.addEventListener('click', dismiss);
    skipBtn.addEventListener('click', dismiss);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !popup.hasAttribute('hidden')) dismiss();
    });
})();
</script>
