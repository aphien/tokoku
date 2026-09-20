<?php
/**
 * TokoKu Theme Functions
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


define( 'TOKOKU_VERSION', '2.3.7' );
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
    wp_enqueue_style( 'dashicons' );
    
    // Dynamic Google Fonts
    $body_font = get_theme_mod( 'tokoku_font_body', 'Plus Jakarta Sans' );
    $heading_font = get_theme_mod( 'tokoku_font_headings', 'Plus Jakarta Sans' );
    $fonts_to_load = array_unique( array( $body_font, $heading_font ) );
    $font_query = array();
    
    foreach ( $fonts_to_load as $font ) {
        $font_query[] = str_replace( ' ', '+', $font ) . ':ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400';
    }
    
    $google_fonts_url = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $font_query ) . '&display=swap';
    wp_enqueue_style( 'tokoku-google-fonts', $google_fonts_url, array(), null );
    wp_enqueue_style( 'tokoku-main-style', TOKOKU_URI . '/assets/css/main.css', array( 'tokoku-google-fonts', 'dashicons' ), TOKOKU_VERSION );
    wp_enqueue_style( 'tokoku-style', get_stylesheet_uri(), array( 'tokoku-main-style' ), TOKOKU_VERSION );

    wp_enqueue_script( 'tokoku-main-js', TOKOKU_URI . '/assets/js/main.js', array(), TOKOKU_VERSION, true );
    wp_enqueue_script( 'tokoku-search-js', TOKOKU_URI . '/assets/js/search.js', array(), TOKOKU_VERSION, true );
    wp_enqueue_script( 'tokoku-whatsapp-js', TOKOKU_URI . '/assets/js/whatsapp.js', array(), TOKOKU_VERSION, true );

    $wa_number  = get_theme_mod( 'tokoku_wa_number', '6281234567890' );
    $wa_message = get_theme_mod( 'tokoku_wa_message', "Halo, saya ingin memesan:\n\nProduk: {produk}\nHarga: {harga}\nJumlah: {jumlah}\n\nNama: {nama}\nCatatan: {catatan}\n\nTerima kasih!" );

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
 * Mengeluarkan CSS Tipografi Dinamis
 * Menyisipkan kode CSS khusus ke dalam <head> berdasarkan pengaturan font
 * yang dipilih pengguna di menu Customizer.
 */
function tokoku_typography_css() {
    $body_font    = get_theme_mod( 'tokoku_font_body', 'Plus Jakarta Sans' );
    $heading_font = get_theme_mod( 'tokoku_font_headings', 'Plus Jakarta Sans' );
    $base_size    = get_theme_mod( 'tokoku_font_size_base', 16 );
    $h1_size      = get_theme_mod( 'tokoku_font_size_h1', 2.5 );

    ?>
    <style id="tokoku-typography-custom">
        :root {
            --font-body: '<?php echo esc_attr( $body_font ); ?>', sans-serif;
            --font-heading: '<?php echo esc_attr( $heading_font ); ?>', sans-serif;
            --font-size-base: <?php echo absint( $base_size ); ?>px;
        }
        body { font-family: var(--font-body); font-size: var(--font-size-base); }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); }
        h1 { font-size: <?php echo esc_attr( $h1_size ); ?>rem; }
        h2 { font-size: calc(<?php echo esc_attr( $h1_size ); ?>rem * 0.8); }
        h3 { font-size: calc(<?php echo esc_attr( $h1_size ); ?>rem * 0.6); }
        
        /* Penyesuaian ukuran font maksimal untuk tampilan mobile agar lebih proporsional */
        @media (max-width: 768px) {
            body { font-size: calc(var(--font-size-base) * 0.95); }
            h1 { font-size: clamp(1.6rem, calc(<?php echo esc_attr( $h1_size ); ?>rem * 0.65), 2.2rem); }
            h2 { font-size: clamp(1.4rem, calc(<?php echo esc_attr( $h1_size ); ?>rem * 0.55), 1.8rem); }
            h3 { font-size: clamp(1.2rem, calc(<?php echo esc_attr( $h1_size ); ?>rem * 0.45), 1.5rem); }
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
