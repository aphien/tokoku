    <footer class="site-footer">
        <div class="container">
            <div class="footer-widgets">
                <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                    <div class="footer-widget-area">
                        <?php dynamic_sidebar( 'footer-1' ); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                    <div class="footer-widget-area">
                        <?php dynamic_sidebar( 'footer-2' ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="footer-bottom">
                <p class="copyright">
                    <?php
                    $copyright = get_theme_mod('tokoku_footer_copyright', '© {year} TokoKu. All rights reserved.');
                    echo str_replace('{year}', date('Y'), esc_html($copyright));
                    ?>
                </p>
            </div>
        </div>
    </footer>

    <!-- Floating Action Buttons (Desktop) -->
    <div class="floating-actions-desktop">
        <button id="scroll-to-top" class="scroll-to-top" aria-label="Scroll to Top">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        </button>
        <div class="wa-float">
            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('tokoku_wa_number', '6281234567890')); ?>" class="wa-float__btn" target="_blank" aria-label="WhatsApp">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.012 2c-5.508 0-9.987 4.479-9.987 9.987 0 1.763.459 3.42 1.261 4.864l-1.286 4.698 4.814-1.263c1.402.766 2.998 1.199 4.698 1.199 5.508 0 9.988-4.479 9.988-9.987s-4.48-9.987-9.988-9.987zm5.541 14.22c-.226.639-1.318 1.171-1.812 1.233-.493.062-1.008.093-2.909-.643-2.316-.902-3.805-3.238-3.921-3.393-.116-.154-.949-1.264-.949-2.41 0-1.147.604-1.711.821-1.942.217-.231.472-.288.63-.288.157 0 .315.002.45.011.145.009.341-.054.534.412.193.466.66 1.603.718 1.72.059.117.098.252.02.408-.079.157-.118.255-.236.39-.118.136-.248.303-.354.407-.117.117-.24.244-.103.48.137.236.608 1.002 1.306 1.623.897.799 1.654 1.045 1.891 1.162.236.117.375.098.514-.06.139-.158.597-.696.757-.932.159-.236.319-.199.54-.117.221.083 1.401.66 1.641.779.24.118.399.176.458.277.059.102.059.589-.167 1.228z" />
                </svg>
            </a>
        </div>
    </div>

    <?php get_template_part('template-parts/whatsapp-modal'); ?>

    <!-- Mobile Bottom Navigation (v1.6.7 Style) -->
    <div class="bottom-nav">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-item <?php echo is_front_page() ? 'active' : ''; ?>">
            <span class="dashicons dashicons-admin-home"></span>
            <span>Home</span>
        </a>
        <a href="<?php echo esc_url(home_url('/#categories')); ?>" class="nav-item">
            <span class="dashicons dashicons-category"></span>
            <span>Kategori</span>
        </a>
        <a href="javascript:void(0)" id="mobile-search-nav-trigger" class="nav-item">
            <span class="dashicons dashicons-search"></span>
            <span>Pencarian</span>
        </a>
        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('tokoku_wa_number', '6281234567890')); ?>" class="nav-item nav-whatsapp" target="_blank">
            <span class="dashicons dashicons-whatsapp"></span>
            <span>WhatsApp</span>
        </a>
    </div>

    <?php wp_footer(); ?>
</body>
</html>