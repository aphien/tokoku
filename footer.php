<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <h3 class="footer-title"><?php bloginfo('name'); ?></h3>
                    <p><?php echo esc_html(get_theme_mod('tokoku_site_description', get_bloginfo('description'))); ?></p>
                </div>

                <div class="footer-menu">
                    <h4 class="footer-title">Navigasi</h4>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'footer-nav-list',
                        'fallback_cb'    => false,
                    ));
                    ?>
                </div>

                <div class="footer-contact">
                    <h4 class="footer-title">Kontak</h4>
                    <p><?php echo esc_html(get_theme_mod('tokoku_store_address')); ?></p>
                    <p><?php echo esc_html(get_theme_mod('tokoku_store_email')); ?></p>
                </div>

                <div class="footer-social">
                    <h4 class="footer-title">Ikuti Kami</h4>
                    <div class="social-links">
                        <?php
                        $socials = array('instagram', 'facebook', 'tiktok', 'youtube');
                        foreach ($socials as $social) {
                            $url = get_theme_mod("tokoku_social_{$social}");
                            if ($url) {
                                echo '<a href="' . esc_url($url) . '" class="social-link ' . $social . '" target="_blank">' . ucfirst($social) . '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>
                    <?php
                    $copyright = get_theme_mod('tokoku_footer_copyright', '© {year} TokoKu. All rights reserved.');
                    echo str_replace('{year}', date('Y'), esc_html($copyright));
                    ?>
                </p>
            </div>
        </div>
    </footer>

        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('tokoku_wa_number', '6281234567890')); ?>" class="wa-float__btn" target="_blank" aria-label="WhatsApp">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12.012 2c-5.508 0-9.987 4.479-9.987 9.987 0 1.763.459 3.42 1.261 4.864l-1.286 4.698 4.814-1.263c1.402.766 2.998 1.199 4.698 1.199 5.508 0 9.988-4.479 9.988-9.987s-4.48-9.987-9.988-9.987zm5.541 14.22c-.226.639-1.318 1.171-1.812 1.233-.493.062-1.008.093-2.909-.643-2.316-.902-3.805-3.238-3.921-3.393-.116-.154-.949-1.264-.949-2.41 0-1.147.604-1.711.821-1.942.217-.231.472-.288.63-.288.157 0 .315.002.45.011.145.009.341-.054.534.412.193.466.66 1.603.718 1.72.059.117.098.252.02.408-.079.157-.118.255-.236.39-.118.136-.248.303-.354.407-.117.117-.24.244-.103.48.137.236.608 1.002 1.306 1.623.897.799 1.654 1.045 1.891 1.162.236.117.375.098.514-.06.139-.158.597-.696.757-.932.159-.236.319-.199.54-.117.221.083 1.401.66 1.641.779.24.118.399.176.458.277.059.102.059.589-.167 1.228z" />
            </svg>
        </a>
    </div>

    <?php get_template_part('template-parts/whatsapp-modal'); ?>

    <!-- Mobile Bottom Navigation -->
    <div class="bottom-nav">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-item <?php echo is_front_page() ? 'active' : ''; ?>">
            <span class="dashicons dashicons-admin-home"></span>
            <span>Home</span>
        </a>
        <a href="<?php echo esc_url(home_url('/#categories')); ?>" class="nav-item">
            <span class="dashicons dashicons-category"></span>
            <span>Kategori</span>
        </a>
        <a href="#" id="mobile-search-nav-trigger" class="nav-item">
            <span class="dashicons dashicons-search"></span>
            <span>Pencarian</span>
        </a>
        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('tokoku_wa_number', '6281234567890')); ?>" class="nav-item" target="_blank">
            <span class="dashicons dashicons-whatsapp"></span>
            <span>WhatsApp</span>
        </a>
    </div>

    <!-- Product Image Lightbox -->
    <div id="tokoku-lightbox" class="tokoku-lightbox" style="display: none;">
        <span class="tokoku-lightbox-close">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </span>
        <div class="tokoku-lightbox-content">
            <img id="tokoku-lightbox-img" src="" alt="">
        </div>
    </div>

    <?php wp_footer(); ?>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?php echo get_template_directory_uri(); ?>/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>

    </body>

    </html>