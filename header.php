<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#ffffff">
    <link rel="manifest" href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=tokoku_manifest' ) ); ?>">
    <?php
    $site_icon = get_site_icon_url( 192 );
    $apple_icon = $site_icon ? $site_icon : get_template_directory_uri() . '/assets/images/icon-192x192.png';
    ?>
    <link rel="apple-touch-icon" href="<?php echo esc_url( $apple_icon ); ?>">
    
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
                document.documentElement.classList.remove('theme-light');
            } else {
                document.documentElement.classList.add('theme-light');
                document.documentElement.classList.remove('theme-dark');
            }
        })();
    </script>
    <style>
        /* Critical Hiding & Structural Display */
        .search-modal-overlay, .mobile-menu-overlay { display: none !important; }
        .search-modal-overlay.active, .mobile-menu-overlay.active { display: flex !important; }
        .mobile-menu-drawer { visibility: hidden; }
        .mobile-menu-drawer.active { visibility: visible; }
        
        /* Logo Switching */
        .logo-dark { display: none !important; }
        .theme-dark .logo-light,
        html.theme-dark .logo-light,
        body.theme-dark .logo-light { display: none !important; }
        .theme-dark .logo-dark,
        html.theme-dark .logo-dark,
        body.theme-dark .logo-dark { display: block !important; }
        
        /* Apply to HTML for instant early styling */
        html { background: var(--bg, #ffffff); color: var(--text, #0f172a); }
        html.theme-dark { background: var(--bg, #0b0f1a); color: var(--text, #f1f5f9); }

        /* Absolute Zero Outline & Zero Border for Search Inputs */
        .search-form,
        .search-form:hover,
        .search-form:focus-within,
        .header-search-centered .search-form,
        .header-search-centered .search-form:hover,
        .header-search-centered .search-form:focus-within,
        .search-modal-input-wrap,
        .search-modal-input-wrap:focus-within {
            border: none !important;
            outline: none !important;
            outline-width: 0 !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
        }

        .search-input,
        .search-input:focus,
        .search-input:focus-visible,
        .search-input:active,
        .search-modal-input,
        .search-modal-input:focus,
        .search-modal-input:focus-visible,
        .search-modal-input:active,
        .search-form input,
        .search-form input:focus,
        .search-form input:focus-visible,
        .search-modal-input-wrap input,
        .search-modal-input-wrap input:focus,
        .search-modal-input-wrap input:focus-visible {
            border: none !important;
            border-width: 0 !important;
            border-color: transparent !important;
            outline: none !important;
            outline-width: 0 !important;
            outline-style: none !important;
            outline-color: transparent !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            background: transparent !important;
            -webkit-tap-highlight-color: transparent !important;
        }
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
                <span class="search-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                <input type="text" class="search-input" placeholder="Masukan kata kunci ..." autocomplete="off" spellcheck="false" style="outline: none !important; border: none !important; box-shadow: none !important; background: transparent !important;">
                <button type="button" class="search-clear" aria-label="Hapus Pencarian" style="display:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
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
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        </button>
        <div class="search-modal-input-wrap">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" style="color: #888; margin-right: 8px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="search-modal-input" class="search-modal-input" placeholder="Cari Produk..." autocomplete="off" spellcheck="false" style="outline: none !important; border: none !important; box-shadow: none !important; background: transparent !important;">
            <button type="button" id="search-modal-clear" class="search-modal-clear" aria-label="Hapus Pencarian" style="display:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
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
        <button type="button" id="mobile-menu-close" class="mobile-menu-close" aria-label="Tutup Menu">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
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
