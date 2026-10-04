<?php
/**
 * TokoKu Theme Functions
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


define( 'TOKOKU_VERSION', '2.5.7' );
define( 'TOKOKU_DIR', get_template_directory() );
define( 'TOKOKU_URI', get_template_directory_uri() );

/**
 * Pengaturan Awal Tema (Theme Setup)
 * Fungsi ini dijalankan setelah tema diaktifkan. Berfungsi mendaftarkan fitur-fitur dasar tema
 * seperti dukungan thumbnail, title tag, menu navigasi, dan ukuran gambar kustom.
 */
function tokoku_setup() {
    load_theme_textdomain( 'tokoku', TOKOKU_DIR . '/languages' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'tokoku-product-card', 400, 400, true );
    add_image_size( 'tokoku-product-large', 800, 800, true );
    add_image_size( 'tokoku-hero', 1920, 1080, true );

    register_nav_menus( array(
        'primary'      => esc_html__( 'Menu Utama', 'tokoku' ),
        'footer_about' => esc_html__( 'Menu Footer Tentang', 'tokoku' ),
        'footer_help'  => esc_html__( 'Menu Footer Bantuan', 'tokoku' ),
    ) );

    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 250, 'flex-height' => true, 'flex-width' => true ) );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
}
add_action( 'after_setup_theme', 'tokoku_setup' );

/**
 * Memuat File CSS dan JavaScript
 * Fungsi ini digunakan untuk menghubungkan (enqueue) semua stylesheet dan script
 * yang dibutuhkan tema pada sisi frontend (tampilan publik). Termasuk Google Fonts,
 * style utama, script pencarian AJAX, dan WhatsApp.
 */
function tokoku_scripts() {
    // Dynamic Google Fonts: Defaults to Merriweather (Body) and Inter (Headings)
    $body_font    = get_theme_mod( 'tokoku_font_body' );
    if ( empty( $body_font ) || 'Plus Jakarta Sans' === $body_font ) {
        $body_font = 'Merriweather';
    }
    $heading_font = get_theme_mod( 'tokoku_font_headings' );
    if ( empty( $heading_font ) || 'Plus Jakarta Sans' === $heading_font ) {
        $heading_font = 'Inter';
    }
    $fonts_to_load = array_unique( array( $body_font, $heading_font ) );
    $font_query = array();
    
    foreach ( $fonts_to_load as $font ) {
        if ( 'Merriweather' === $font ) {
            $font_query[] = 'Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700';
        } elseif ( 'Inter' === $font ) {
            $font_query[] = 'Inter:wght@400;500;600;700;800;900';
        } else {
            $font_query[] = str_replace( ' ', '+', $font ) . ':ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400';
        }
    }
    
    $google_fonts_url = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $font_query ) . '&display=swap';
    wp_enqueue_style( 'tokoku-google-fonts', $google_fonts_url, array(), null );
    wp_enqueue_style( 'tokoku-main-style', TOKOKU_URI . '/assets/css/main.css', array( 'tokoku-google-fonts' ), TOKOKU_VERSION );
    wp_enqueue_style( 'tokoku-style', get_stylesheet_uri(), array( 'tokoku-main-style' ), TOKOKU_VERSION );

    wp_enqueue_script( 'tokoku-main-js', TOKOKU_URI . '/assets/js/main.js', array(), TOKOKU_VERSION, true );
    wp_enqueue_script( 'tokoku-search-js', TOKOKU_URI . '/assets/js/search.js', array(), TOKOKU_VERSION, true );
    wp_enqueue_script( 'tokoku-whatsapp-js', TOKOKU_URI . '/assets/js/whatsapp.js', array(), TOKOKU_VERSION, true );

    if ( is_singular( 'produk' ) ) {
        wp_enqueue_style( 'tokoku-single-product-style', TOKOKU_URI . '/assets/css/single-product.css', array( 'tokoku-main-style' ), TOKOKU_VERSION );
        wp_enqueue_script( 'tokoku-single-product-js', TOKOKU_URI . '/assets/js/single-product.js', array(), TOKOKU_VERSION, true );
    }

    $wa_number  = get_theme_mod( 'tokoku_wa_number', '6281234567890' );
    $wa_message = get_theme_mod( 'tokoku_wa_message', "✧━━━━━━[ DETAIL PESANAN PLAKAT ]━━━━━━✧\n\nTerima kasih telah mempercayakan momen spesial Anda bersama kami. Berikut adalah rincian pesanan Anda:\n\n👤 Nama Pemesan : {nama}\n📦 Produk       : {produk}\n🏷️ SKU          : {sku}\n🔗 Link Produk  : {link}\n\n💰 Harga Satuan : {harga}\n🔢 Jumlah       : {jumlah}\n\n📝 Catatan / Detail Grafir:\n{catatan}\n\n✧━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━✧\nMohon periksa kembali detail di atas. Jika semua data sudah benar, silakan balas \"CONFIRM\" agar pesanan dapat segera kami proses. Terima kasih! ✨" );

    wp_localize_script( 'tokoku-search-js', 'tokokuSearch', array(
        'ajaxUrl'  => admin_url( 'admin-ajax.php', 'relative' ),
        'nonce'    => wp_create_nonce( 'tokoku_search_nonce' ),
        'homeUrl'  => home_url( '/' ),
        'themeUrl' => get_template_directory_uri(),
    ) );

    wp_localize_script( 'tokoku-whatsapp-js', 'tokokuWA', array(
        'number'  => $wa_number,
        'message' => $wa_message,
    ) );
}
add_action( 'wp_enqueue_scripts', 'tokoku_scripts' );

/**
 * Resource Hints untuk Optimasi Kecepatan (Preconnect Google Fonts)
 */
function tokoku_resource_hints( $urls, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
        );
        $urls[] = array(
            'href'        => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        );
    }
    return $urls;
}
add_filter( 'wp_resource_hints', 'tokoku_resource_hints', 10, 2 );

/**
 * Asynchronous Loading untuk Google Fonts (Eliminasi Render-Blocking CSS)
 */
function tokoku_async_styles( $tag, $handle, $href, $media ) {
    if ( is_admin() ) return $tag;
    if ( 'tokoku-google-fonts' === $handle ) {
        return '<link rel="preload" as="style" href="' . esc_url( $href ) . '">' . "\n" .
               '<link rel="stylesheet" id="tokoku-google-fonts-css" href="' . esc_url( $href ) . '" media="print" onload="this.media=\'all\'">' . "\n" .
               '<noscript><link rel="stylesheet" href="' . esc_url( $href ) . '"></noscript>';
    }
    return $tag;
}
add_filter( 'style_loader_tag', 'tokoku_async_styles', 10, 4 );

/**
 * Defer Non-Critical Frontend JavaScript
 */
function tokoku_defer_scripts( $tag, $handle, $src ) {
    if ( is_admin() ) return $tag;
    $defer_handles = array( 'tokoku-main-js', 'tokoku-search-js', 'tokoku-whatsapp-js', 'tokoku-single-product-js' );
    if ( in_array( $handle, $defer_handles, true ) ) {
        if ( false === strpos( $tag, ' defer' ) ) {
            return str_replace( ' src=', ' defer src=', $tag );
        }
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'tokoku_defer_scripts', 10, 3 );

/**
 * Pembersihan Aset Tidak Terpakai pada Frontend (Gutenberg Block CSS, Classic Styles, Dashicons)
 */
function tokoku_optimize_frontend_assets() {
    if ( ! is_admin() ) {
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
        wp_dequeue_style( 'wc-blocks-style' );
        wp_dequeue_style( 'classic-theme-styles' );
        wp_dequeue_style( 'global-styles' );
        
        if ( ! is_user_logged_in() ) {
            wp_dequeue_style( 'dashicons' );
            wp_deregister_style( 'dashicons' );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'tokoku_optimize_frontend_assets', 100 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

/**
 * Nonaktifkan Emoji WordPress untuk Mempercepat Loading Halaman
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

/**
 * Mengeluarkan CSS Tipografi Dinamis
 * Menyisipkan kode CSS khusus ke dalam <head> berdasarkan pengaturan font
 * yang dipilih pengguna di menu Customizer.
 */
function tokoku_typography_css() {
    $body_font    = get_theme_mod( 'tokoku_font_body' );
    if ( empty( $body_font ) || 'Plus Jakarta Sans' === $body_font ) {
        $body_font = 'Merriweather';
    }
    $heading_font = get_theme_mod( 'tokoku_font_headings' );
    if ( empty( $heading_font ) || 'Plus Jakarta Sans' === $heading_font ) {
        $heading_font = 'Inter';
    }
    $base_size    = get_theme_mod( 'tokoku_font_size_base', 16 );
    $h1_size      = get_theme_mod( 'tokoku_font_size_h1', 2.5 );

    $is_serif_body    = in_array( $body_font, array( 'Merriweather', 'Playfair Display' ), true );
    $body_fallback    = $is_serif_body ? "Georgia, Cambria, 'Times New Roman', Times, serif" : "system-ui, -apple-system, sans-serif";
    $heading_fallback = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    ?>
    <style id="tokoku-typography-custom">
        :root {
            --font-body: '<?php echo esc_attr( $body_font ); ?>', <?php echo $body_fallback; ?>;
            --font-heading: '<?php echo esc_attr( $heading_font ); ?>', <?php echo $heading_fallback; ?>;
            --font-size-base: <?php echo absint( $base_size ); ?>px;

            /* Fluid Typography Scale */
            --fs-h1: clamp(2rem, 1.35rem + 2.6vw, 3.25rem);
            --fs-h2: clamp(1.6rem, 1.15rem + 1.8vw, 2.35rem);
            --fs-h3: clamp(1.3rem, 1rem + 1.2vw, 1.75rem);
            --fs-h4: clamp(1.1rem, 0.95rem + 0.6vw, 1.35rem);
            --fs-h5: clamp(0.95rem, 0.88rem + 0.35vw, 1.15rem);
            --fs-h6: clamp(0.85rem, 0.8rem + 0.2vw, 0.95rem);
            --fs-body: clamp(0.975rem, 0.92rem + 0.25vw, 1.0625rem);

            /* Typography Line Heights */
            --lh-tight: 1.15;
            --lh-snug: 1.25;
            --lh-base: 1.6;
            --lh-relaxed: 1.75;
        }

        body {
            font-family: var(--font-body);
            font-size: var(--fs-body);
            font-weight: 400;
            line-height: var(--lh-base);
            color: var(--text);
            background-color: var(--bg);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Headings - Bold / Heavy Weights & Tight Crisp Line-Height */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            color: var(--text);
            margin-bottom: 0.6em;
            letter-spacing: -0.025em;
        }

        h1 {
            font-size: var(--fs-h1);
            font-weight: 800;
            line-height: var(--lh-tight);
            letter-spacing: -0.03em;
        }

        h2 {
            font-size: var(--fs-h2);
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.025em;
        }

        h3 {
            font-size: var(--fs-h3);
            font-weight: 700;
            line-height: var(--lh-snug);
            letter-spacing: -0.02em;
        }

        h4 {
            font-size: var(--fs-h4);
            font-weight: 600;
            line-height: 1.3;
        }

        h5 {
            font-size: var(--fs-h5);
            font-weight: 600;
            line-height: 1.35;
        }

        h6 {
            font-size: var(--fs-h6);
            font-weight: 600;
            line-height: 1.4;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Paragraphs - Optimal Reading Flow & Generous Spacing */
        p {
            font-family: var(--font-body);
            font-weight: 400;
            line-height: var(--lh-base);
            margin-bottom: 1.5em;
            color: var(--text);
        }

        p:last-child {
            margin-bottom: 0;
        }

        /* UI Elements - Crisp Geometric Sans-Serif (Inter) */
        button,
        input,
        select,
        textarea,
        .btn,
        .button,
        .nav-link,
        .menu-item,
        .section-badge,
        .badge,
        .chip,
        .product-tag-pill,
        .product-price,
        .price,
        .specs-item-label,
        .specs-item-sku {
            font-family: var(--font-heading);
        }
    </style>
    <?php
}
add_action( 'wp_head', 'tokoku_typography_css', 100 );

/**
 * Mendaftarkan Area Widget
 * Mendefinisikan area di mana pengguna bisa menambahkan widget, seperti
 * Sidebar utama dan dua area di bagian Footer.
 */
function tokoku_widgets_init() {
    register_sidebar( array( 'name' => 'Sidebar', 'id' => 'sidebar-1', 'before_widget' => '<section id="%1$s" class="widget %2$s">', 'after_widget' => '</section>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>' ) );
    register_sidebar( array( 'name' => 'Footer Widget 1', 'id' => 'footer-1', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4 class="widget-title">', 'after_title' => '</h4>' ) );
    register_sidebar( array( 'name' => 'Footer Widget 2', 'id' => 'footer-2', 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<h4 class="widget-title">', 'after_title' => '</h4>' ) );
}
add_action( 'widgets_init', 'tokoku_widgets_init' );

// Include required files
require_once TOKOKU_DIR . '/includes/custom-post-type.php';
require_once TOKOKU_DIR . '/includes/meta-boxes.php';
require_once TOKOKU_DIR . '/includes/customizer.php';
require_once TOKOKU_DIR . '/includes/admin-page.php';
require_once TOKOKU_DIR . '/includes/ajax-search.php';
require_once TOKOKU_DIR . '/includes/seo.php';
require_once TOKOKU_DIR . '/includes/taxonomy-meta.php';

/**
 * Menambahkan teks hak cipta/kredit di bagian bawah halaman Admin WordPress.
 */
function tokoku_admin_footer_credit( $text ) {
    return 'Theme <span style="font-weight:bold;color:#007bff;">TokoKu</span> by <a href="https://github.com/aphien" target="_blank" style="text-decoration:none;font-weight:bold;">TokoKu Team</a>';
}
add_filter( 'admin_footer_text', 'tokoku_admin_footer_credit' );

/**
 * TokoKu Dashboard Widget di Halaman Utama Admin (Dashboard)
 */
function tokoku_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'tokoku_dashboard_widget',
        '⚡ TokoKu Store Dashboard & Ringkasan Toko',
        'tokoku_dashboard_widget_render'
    );
}
add_action( 'wp_dashboard_setup', 'tokoku_add_dashboard_widgets' );

function tokoku_admin_global_assets( $hook ) {
    if ( 'index.php' === $hook ) {
        wp_enqueue_style( 'tokoku-admin-css', TOKOKU_URI . '/assets/css/admin.css', array(), TOKOKU_VERSION );
    }
}
add_action( 'admin_enqueue_scripts', 'tokoku_admin_global_assets' );

function tokoku_dashboard_widget_render() {
    $count_produk   = wp_count_posts( 'produk' ) ? wp_count_posts( 'produk' )->publish : 0;
    $count_kategori = wp_count_terms( array( 'taxonomy' => 'kategori_produk', 'hide_empty' => false ) );
    $count_posts    = wp_count_posts( 'post' ) ? wp_count_posts( 'post' )->publish : 0;
    $wa_number      = get_theme_mod( 'tokoku_wa_number', '6281234567890' );
    ?>
    <div class="tokoku-dash-widget">
        <div class="tokoku-dash-header">
            <div class="tokoku-dash-title">
                <span class="tokoku-dash-header-icon">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"/></svg>
                </span>
                <span>Ringkasan Katalog & Toko Online</span>
            </div>
            <span class="tokoku-admin-version">v<?php echo TOKOKU_VERSION; ?></span>
        </div>
        <div class="tokoku-dash-stats">
            <div class="tokoku-dash-stat-card">
                <div class="tokoku-dash-stat-icon blue">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-4.86 8.86l-3 3.87L9 13.14 6 17h12l-3.86-5.14z"/></svg>
                </div>
                <div class="tokoku-dash-stat-info">
                    <span class="tokoku-dash-stat-num"><?php echo esc_html( $count_produk ); ?></span>
                    <span class="tokoku-dash-stat-label">Total Produk Aktif</span>
                </div>
            </div>
            <div class="tokoku-dash-stat-card">
                <div class="tokoku-dash-stat-icon green">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.4-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.11-.23-.17-.48-.29z"/></svg>
                </div>
                <div class="tokoku-dash-stat-info">
                    <span class="tokoku-dash-stat-num"><?php echo esc_html( $wa_number ); ?></span>
                    <span class="tokoku-dash-stat-label">WhatsApp Pemesanan</span>
                </div>
            </div>
            <div class="tokoku-dash-stat-card">
                <div class="tokoku-dash-stat-icon amber">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg>
                </div>
                <div class="tokoku-dash-stat-info">
                    <span class="tokoku-dash-stat-num"><?php echo esc_html( $count_kategori ); ?></span>
                    <span class="tokoku-dash-stat-label">Kategori Produk</span>
                </div>
            </div>
            <div class="tokoku-dash-stat-card">
                <div class="tokoku-dash-stat-icon purple">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                </div>
                <div class="tokoku-dash-stat-info">
                    <span class="tokoku-dash-stat-num"><?php echo esc_html( $count_posts ); ?></span>
                    <span class="tokoku-dash-stat-label">Artikel Blog</span>
                </div>
            </div>
        </div>
        <div class="tokoku-dash-actions">
            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=produk' ) ); ?>" class="tokoku-dash-btn primary">
                <span class="dashicons dashicons-plus-alt2"></span><span class="tokoku-btn-text"><?php _e( 'Tambah Produk Baru', 'tokoku' ); ?></span>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=tokoku-settings' ) ); ?>" class="tokoku-dash-btn secondary">
                <span class="dashicons dashicons-admin-generic"></span><span class="tokoku-btn-text"><?php _e( 'Pengaturan Tokoku', 'tokoku' ); ?></span>
            </a>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="tokoku-dash-btn secondary">
                <span class="dashicons dashicons-external"></span><span class="tokoku-btn-text"><?php _e( 'Kunjungi Website', 'tokoku' ); ?></span>
            </a>
        </div>
    </div>
    <?php
}

/**
 * Menambahkan kelas CSS tambahan pada tag <body>.
 * Berguna untuk menargetkan gaya CSS berdasarkan mode (dark/light) atau jenis halaman (single/archive).
 */
function tokoku_body_classes( $classes ) {
    $classes[] = 'theme-' . get_theme_mod( 'tokoku_default_mode', 'dark' );
    if ( is_singular( 'produk' ) ) $classes[] = 'single-product-page';
    if ( is_post_type_archive( 'produk' ) || is_tax( 'kategori_produk' ) || is_tax( 'tag_produk' ) ) $classes[] = 'product-archive-page';
    return $classes;
}
add_filter( 'body_class', 'tokoku_body_classes' );

/**
 * Modify archive title
 */
function tokoku_archive_title( $title ) {
    if ( is_post_type_archive( 'produk' ) ) return __( 'Semua Produk', 'tokoku' );
    if ( is_tax( 'kategori_produk' ) || is_tax( 'tag_produk' ) ) return single_term_title( '', false );
    return $title;
}
add_filter( 'get_the_archive_title', 'tokoku_archive_title' );

// Custom excerpt
add_filter( 'excerpt_length', function( $l ) { return is_admin() ? $l : 20; } );
add_filter( 'excerpt_more', function() { return '&hellip;'; } );

// Disable Gutenberg Editor
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

// Disable Gutenberg Styles
add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-block-style' );
}, 100 );


/**
 * Modifikasi Query Produk
 * Mengatur jumlah produk yang ditampilkan per halaman (12 produk) 
 * dan fitur pengurutan (sorting) berdasarkan harga, tanggal, atau nama.
 */
function tokoku_modify_product_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    
    $is_product_search = $query->is_search() && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'produk';
    
    if ( ! is_post_type_archive( 'produk' ) && ! is_tax( 'kategori_produk' ) && ! is_tax( 'tag_produk' ) && ! $is_product_search ) return;

    $query->set( 'posts_per_page', 12 );
    $orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'terbaru';

    switch ( $orderby ) {
        case 'termurah':
            $query->set( 'meta_key', '_produk_harga' );
            $query->set( 'orderby', 'meta_value_num' );
            $query->set( 'order', 'ASC' );
            break;
        case 'termahal':
            $query->set( 'meta_key', '_produk_harga' );
            $query->set( 'orderby', 'meta_value_num' );
            $query->set( 'order', 'DESC' );
            break;
        case 'nama':
            $query->set( 'orderby', 'title' );
            $query->set( 'order', 'ASC' );
            break;
        default:
            $query->set( 'orderby', 'date' );
            $query->set( 'order', 'DESC' );
    }
}
add_action( 'pre_get_posts', 'tokoku_modify_product_query' );

/**
 * ====================================================
 * SECURITY HARDENING
 * ====================================================
 */

/**
 * Menyembunyikan Versi WordPress dari elemen <head>
 * Ini penting untuk keamanan agar penyerang tidak mudah mengetahui versi WP yang digunakan.
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

function tokoku_remove_wp_version_strings( $src ) {
    if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'style_loader_src', 'tokoku_remove_wp_version_strings', 999 );
add_filter( 'script_loader_src', 'tokoku_remove_wp_version_strings', 999 );

/**
 * Menambahkan Header Keamanan (Security Headers)
 * Berguna untuk melindungi situs dari Clickjacking, XSS, dan serangan sniffing tipe konten.
 * Hanya diaplikasikan pada frontend (bukan di dalam dashboard admin).
 */
function tokoku_add_security_headers() {
    if ( ! is_admin() ) {
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'X-XSS-Protection: 1; mode=block' );
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    }
}
add_action( 'send_headers', 'tokoku_add_security_headers' );

/**
 * Menonaktifkan XML-RPC
 * Mencegah serangan DDoS dan brute-force yang sering memanfaatkan file xmlrpc.php.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

/**
 * Menonaktifkan Editor File di Dashboard Admin
 * Mencegah admin mengubah file plugin atau tema secara langsung dari dashboard,
 * mengamankan kode jika sewaktu-waktu akun admin diretas.
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}

/**
 * Membuat Manifest PWA Dinamis
 * Menyediakan file manifest.json agar situs dapat diinstal sebagai Progressive Web App (PWA) di perangkat seluler.
 */
function tokoku_generate_manifest() {
    header( 'Content-Type: application/manifest+json' );
    $icon_192 = get_site_icon_url( 192 );
    $icon_512 = get_site_icon_url( 512 );
    
    if ( ! $icon_192 ) $icon_192 = TOKOKU_URI . '/assets/images/icon-192x192.png';
    if ( ! $icon_512 ) $icon_512 = TOKOKU_URI . '/assets/images/icon-512x512.png';

    $manifest = array(
        'name'             => get_bloginfo( 'name' ),
        'short_name'       => get_bloginfo( 'name' ),
        'description'      => get_bloginfo( 'description' ),
        'start_url'        => '/',
        'display'          => 'standalone',
        'background_color' => '#ffffff',
        'theme_color'      => '#ffffff',
        'orientation'      => 'portrait',
        'icons'            => array(
            array(
                'src'   => $icon_192,
                'sizes' => '192x192',
                'type'  => 'image/png'
            ),
            array(
                'src'   => $icon_512,
                'sizes' => '512x512',
                'type'  => 'image/png'
            )
        )
    );
    
    echo wp_json_encode( $manifest );
    exit;
}
add_action( 'wp_ajax_tokoku_manifest', 'tokoku_generate_manifest' );
add_action( 'wp_ajax_nopriv_tokoku_manifest', 'tokoku_generate_manifest' );

/**
 * Mendapatkan HTML Ikon Kategori Produk
 *
 * Mengambil ikon attachment yang diunggah di term meta 'tokoku_kategori_icon'.
 * Jika belum ada, menyediakan fallback vektor SVG tematik modern.
 *
 * @param int $term_id
 * @param int $size
 * @return string HTML
 */
function tokoku_get_category_icon_html( $term_id = 0, $size = 20 ) {
    if ( $term_id ) {
        $icon_id = get_term_meta( $term_id, 'tokoku_kategori_icon', true );
        if ( $icon_id ) {
            $icon_url = wp_get_attachment_image_url( $icon_id, 'thumbnail' );
            if ( $icon_url ) {
                return '<img src="' . esc_url( $icon_url ) . '" class="pill-icon-img" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" alt="" loading="lazy" decoding="async">';
            }
        }
        
        $term = get_term( $term_id, 'kategori_produk' );
        $slug = ( $term && ! is_wp_error( $term ) ) ? $term->slug : '';
        
        if ( strpos( $slug, 'plakat' ) !== false ) {
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>';
        } elseif ( strpos( $slug, 'souvenir' ) !== false ) {
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>';
        } elseif ( strpos( $slug, 'trophy' ) !== false || strpos( $slug, 'piala' ) !== false ) {
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.45 1-1 1H8c-.55 0-1 .45-1 1v1h10v-1c0-.55-.45-1-1-1h-1c-.55 0-1-.45-1-1v-2.34"></path><path d="M6 4h12v7a6 6 0 0 1-12 0V4z"></path></svg>';
        } elseif ( strpos( $slug, 'vandel' ) !== false || strpos( $slug, 'kayu' ) !== false ) {
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>';
        } elseif ( strpos( $slug, 'medali' ) !== false || strpos( $slug, 'medal' ) !== false ) {
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path></svg>';
        }
    }
    
    // Default fallback icon
    return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>';
}

/**
 * Sanitasi Tag SVG untuk Keamanan Admin
 */
function tokoku_sanitize_svg( $svg ) {
    if ( empty( $svg ) ) {
        return '';
    }
    $allowed_tags = array(
        'svg' => array(
            'class'           => true,
            'aria-hidden'     => true,
            'aria-label'      => true,
            'role'            => true,
            'viewbox'         => true,
            'width'           => true,
            'height'          => true,
            'fill'            => true,
            'stroke'          => true,
            'stroke-width'    => true,
            'stroke-linecap'  => true,
            'stroke-linejoin' => true,
            'xmlns'           => true,
        ),
        'g' => array(
            'fill'   => true,
            'stroke' => true,
        ),
        'path' => array(
            'd'               => true,
            'fill'            => true,
            'stroke'          => true,
            'stroke-width'    => true,
            'stroke-linecap'  => true,
            'stroke-linejoin' => true,
        ),
        'circle' => array(
            'cx'           => true,
            'cy'           => true,
            'r'            => true,
            'fill'         => true,
            'stroke'       => true,
            'stroke-width' => true,
        ),
        'rect' => array(
            'x'            => true,
            'y'            => true,
            'width'        => true,
            'height'       => true,
            'rx'           => true,
            'ry'           => true,
            'fill'         => true,
            'stroke'       => true,
            'stroke-width' => true,
        ),
        'line' => array(
            'x1'           => true,
            'y1'           => true,
            'x2'           => true,
            'y2'           => true,
            'stroke'       => true,
            'stroke-width' => true,
            'stroke-linecap' => true,
        ),
        'polyline' => array(
            'points'          => true,
            'fill'            => true,
            'stroke'          => true,
            'stroke-width'    => true,
            'stroke-linecap'  => true,
            'stroke-linejoin' => true,
        ),
        'polygon' => array(
            'points'          => true,
            'fill'            => true,
            'stroke'          => true,
            'stroke-width'    => true,
            'stroke-linecap'  => true,
            'stroke-linejoin' => true,
        ),
    );
    return wp_kses( $svg, $allowed_tags );
}

/**
 * Render Ikon Lead Time Bar Halaman Produk
 */
function tokoku_get_lead_time_icon_html( $size = 18 ) {
    $custom_img = get_theme_mod( 'tokoku_lead_time_icon_img' );
    if ( ! empty( $custom_img ) ) {
        return '<img src="' . esc_url( $custom_img ) . '" alt="" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" style="object-fit:contain; display:block;">';
    }

    $custom_svg = get_theme_mod( 'tokoku_lead_time_icon_svg' );
    if ( ! empty( $custom_svg ) ) {
        return tokoku_sanitize_svg( $custom_svg );
    }

    $preset = get_theme_mod( 'tokoku_lead_time_icon', 'clock' );
    switch ( $preset ) {
        case 'lightning':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>';
        case 'truck':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>';
        case 'calendar':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
        case 'shield':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>';
        case 'award':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>';
        case 'star':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
        case 'clock':
        default:
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
    }
}

/**
 * Render Ikon Trust Badge Halaman Produk
 */
function tokoku_get_trust_badge_icon_html( $index = 1, $size = 20 ) {
    $custom_img = get_theme_mod( "tokoku_trust_badge_icon_img_{$index}" );
    if ( ! empty( $custom_img ) ) {
        return '<img src="' . esc_url( $custom_img ) . '" alt="" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" style="object-fit:contain; display:block;">';
    }

    $custom_svg = get_theme_mod( "tokoku_trust_badge_icon_svg_{$index}" );
    if ( ! empty( $custom_svg ) ) {
        return tokoku_sanitize_svg( $custom_svg );
    }

    // Default presets for each badge index
    $default_presets = array(
        1 => 'design',
        2 => 'shield',
        3 => 'lightning',
        4 => 'craftsman',
    );
    $default_preset = isset( $default_presets[$index] ) ? $default_presets[$index] : 'design';
    $preset = get_theme_mod( "tokoku_trust_badge_icon_{$index}", $default_preset );

    switch ( $preset ) {
        case 'design':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>';
        case 'shield':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>';
        case 'lightning':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>';
        case 'craftsman':
        case 'factory':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path><path d="M9 9v.01"></path><path d="M9 13v.01"></path><path d="M9 17v.01"></path></svg>';
        case 'award':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>';
        case 'check':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
        case 'heart':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
        case 'box':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"></polyline><line x1="1" y1="3" x2="23" y2="3"></line><line x1="10" y1="12" x2="14" y2="12"></line></svg>';
        case 'thumbs-up':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>';
        case 'star':
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
        default:
            return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
    }
}

/**
 * ==========================================================================
 * WEBP IMAGE UPLOAD & OPTIMIZATION SUPPORT (CORE WEB VITALS BOOST)
 * ==========================================================================
 */

/**
 * Aktifkan dukungan upload file WebP di Media Library WordPress
 */
function tokoku_enable_webp_upload( $mimes ) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter( 'upload_mimes', 'tokoku_enable_webp_upload' );

/**
 * Pastikan WordPress dapat menghasilkan dan menampilkan thumbnail WebP
 */
function tokoku_webp_is_displayable( $result, $path ) {
    if ( false === $result ) {
        $displayable_image_types = array( IMAGETYPE_WEBP );
        $info = @getimagesize( $path );
        if ( ! empty( $info ) && in_array( $info[2], $displayable_image_types, true ) ) {
            $result = true;
        }
    }
    return $result;
}
add_filter( 'file_is_displayable_image', 'tokoku_webp_is_displayable', 10, 2 );

/**
 * Optimasi rasio kompresi gambar WordPress (85% optimal untuk PageSpeed & ketajaman foto)
 */
add_filter( 'wp_editor_set_quality', function( $quality ) {
    return 85;
} );

/**
 * Otomatis tambahkan atribut decoding="async" pada semua gambar WordPress untuk render non-blocking
 */
function tokoku_add_decoding_async_attribute( $attr ) {
    if ( ! isset( $attr['decoding'] ) ) {
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'tokoku_add_decoding_async_attribute' );



