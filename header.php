<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#ffffff">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/manifest.json">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/icon-192x192.png">
    
    <?php wp_head(); ?>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('tokoku-theme');
            const configDefault = '<?php echo esc_attr( get_theme_mod( "tokoku_default_mode", "dark" ) ); ?>';
            let theme = savedTheme || configDefault;
            
            if (theme === 'auto') {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            
            if (theme === 'dark') {
                document.documentElement.classList.add('theme-dark');
            }
        })();
    </script>
    <style>
        :root {
            --primary: <?php echo get_theme_mod( 'tokoku_primary_color', '#007bff' ); ?>;
            --primary-dark: <?php echo get_theme_mod( 'tokoku_secondary_color', '#0056b3' ); ?>;
            
            /* Custom Element Colors */
            --header-bg: <?php echo get_theme_mod( 'tokoku_header_bg', '#ffffff' ); ?>;
            --header-text: <?php echo get_theme_mod( 'tokoku_header_text', '#0f172a' ); ?>;
            --footer-bg: <?php echo get_theme_mod( 'tokoku_footer_bg', '#f1f5f9' ); ?>;
            --footer-text: <?php echo get_theme_mod( 'tokoku_footer_text', '#475569' ); ?>;
            --card-bg: <?php echo get_theme_mod( 'tokoku_card_bg', '#ffffff' ); ?>;
            --card-text: <?php echo get_theme_mod( 'tokoku_card_text', '#0f172a' ); ?>;
            --price-color: <?php echo get_theme_mod( 'tokoku_price_color', '#007bff' ); ?>;
        }
        .theme-dark {
            --bg: <?php echo get_theme_mod( 'tokoku_dark_bg', '#0b0f1a' ); ?>;
            --bg2: <?php echo get_theme_mod( 'tokoku_dark_bg2', '#151b2d' ); ?>;
            --text: <?php echo get_theme_mod( 'tokoku_dark_text', '#f1f5f9' ); ?>;
            
            /* Dark Mode Overrides for Elements */
            --header-bg: var(--bg);
            --header-text: var(--text);
            --footer-bg: var(--bg2);
            --footer-text: var(--text2);
            --card-bg: var(--bg2);
            --card-text: var(--text);
        }
        /* Critical Hiding - Bulletproof */
        .mobile-menu-drawer, .search-modal-overlay, .mobile-menu-overlay { display: none !important; }
        .mobile-menu-drawer.active, .search-modal-overlay.active, .mobile-menu-overlay.active { display: flex !important; }
        .logo-dark { display: none !important; }
        .theme-dark .logo-light { display: none !important; }
        .theme-dark .logo-dark { display: block !important; }
        /* Apply to HTML for early styling */
        html.theme-dark { background: var(--bg); }
    </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ============================================
     HEADER
     ============================================ -->
<header class="site-header">
    <div class="container header-flex">
        <!-- Logo -->
        <div class="site-logo">
            <?php
            $logo_light = get_theme_mod( 'tokoku_logo_light' );
            $logo_dark  = get_theme_mod( 'tokoku_logo_dark' );
            
            // Fallback for light logo
            if ( ! $logo_light ) {
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                if ( $custom_logo_id ) {
                    $logo_light = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                }
            }
            
            if ( $logo_light || $logo_dark ) {
                echo '<a href="' . esc_url( home_url( '/' ) ) . '">';
                if ( $logo_light ) {
                    echo '<img src="' . esc_url( $logo_light ) . '" class="logo-light" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
                }
                if ( $logo_dark ) {
                    echo '<img src="' . esc_url( $logo_dark ) . '" class="logo-dark" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
                }
                echo '</a>';
            } else {
                echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
            }
            ?>
        </div>

        <!-- Desktop Search -->
        <div class="header-search-centered">
            <div class="search-form">
                <span class="search-icon"><span class="dashicons dashicons-search"></span></span>
                <input type="text" class="search-input" placeholder="Masukan kata kunci ...">
                <button type="button" class="search-clear" style="display:none;">&times;</button>
                <div class="search-results"></div>
            </div>
        </div>

        <!-- Actions -->
        <div class="header-actions">
            <?php if ( get_theme_mod( 'tokoku_enable_dark_mode', 'yes' ) === 'yes' ) : ?>
                <button id="mode-toggle" class="mode-toggle" aria-label="Toggle Theme">
                    <svg class="sun-icon" viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                    <svg class="moon-icon" viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </button>
            <?php endif; ?>
            <button id="menu-toggle" class="menu-toggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<!-- ============================================
     MOBILE SEARCH MODAL (body level, NOT inside header)
     ============================================ -->
<div id="search-modal-overlay" class="search-modal-overlay">
    <div class="search-modal-header">
        <button type="button" id="search-modal-back" class="search-modal-back" aria-label="Kembali">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
        </button>
        <div class="search-modal-input-wrap">
            <span class="dashicons dashicons-search" style="color: #888;"></span>
            <input type="text" id="search-modal-input" class="search-modal-input" placeholder="Cari Produk...">
            <button type="button" id="search-modal-clear" class="search-modal-clear" style="display:none;">&times;</button>
        </div>
    </div>
    <div class="search-modal-body">
        <div class="search-popular-section">
            <div class="search-section-title">PENCARIAN POPULER</div>
            <div class="search-popular-tags">
                <?php
                $popular_tags = get_terms( array( 'taxonomy' => 'kategori_produk', 'hide_empty' => false, 'number' => 3, 'orderby' => 'count', 'order' => 'DESC' ) );
                if ( ! empty( $popular_tags ) && ! is_wp_error( $popular_tags ) ) {
                    foreach ( $popular_tags as $tag ) {
                        echo '<span class="popular-tag">' . esc_html( $tag->name ) . '</span>';
                    }
                } else {
                    echo '<span class="popular-tag">Terbaru</span>';
                }
                ?>
            </div>
        </div>
        <div class="search-results-section">
            <div class="search-section-title" id="search-results-title">SEMUA PRODUK</div>
            <div id="search-modal-results" class="search-modal-results"></div>
        </div>
    </div>
</div>

<!-- ============================================
     MOBILE MENU DRAWER (body level, NOT inside header)
     ============================================ -->
<div id="mobile-menu-overlay" class="mobile-menu-overlay"></div>
<div id="mobile-menu-drawer" class="mobile-menu-drawer">
    <div class="mobile-menu-header">
        <span class="mobile-menu-title">Menu</span>
        <button type="button" id="mobile-menu-close" class="mobile-menu-close">&times;</button>
    </div>
    <nav class="mobile-primary-menu">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'mobile-nav-list',
            'fallback_cb'    => false,
        ) );
        ?>
    </nav>
</div>
