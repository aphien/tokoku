<?php
/**
 * TokoKu Theme Functions
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TOKOKU_VERSION', '1.5.4' );
define( 'TOKOKU_DIR', get_template_directory() );
define( 'TOKOKU_URI', get_template_directory_uri() );

/**
 * Theme Setup
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
        'primary' => esc_html__( 'Menu Utama', 'tokoku' ),
        'footer'  => esc_html__( 'Menu Footer', 'tokoku' ),
    ) );

    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 250, 'flex-height' => true, 'flex-width' => true ) );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
}
add_action( 'after_setup_theme', 'tokoku_setup' );

/**
 * Enqueue Styles and Scripts
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
        'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
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
 * Output Dynamic Typography CSS
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
    </style>
    <?php
}
add_action( 'wp_head', 'tokoku_typography_css', 100 );

/**
 * Register Widget Areas
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
require_once TOKOKU_DIR . '/includes/dummy-data.php';
require_once TOKOKU_DIR . '/includes/taxonomy-meta.php';

/**
 * Add footer credit in admin area
 */
function tokoku_admin_footer_credit( $text ) {
    return 'Theme <span style="font-weight:bold;color:#007bff;">TokoKu</span> by <a href="https://github.com/m-alfiandiismet" target="_blank" style="text-decoration:none;font-weight:bold;">m.alfiandiismet</a>';
}
add_filter( 'admin_footer_text', 'tokoku_admin_footer_credit' );

/**
 * Add body classes
 */
function tokoku_body_classes( $classes ) {
    $classes[] = 'theme-' . get_theme_mod( 'tokoku_default_mode', 'dark' );
    if ( is_singular( 'produk' ) ) $classes[] = 'single-product-page';
    if ( is_post_type_archive( 'produk' ) || is_tax( 'kategori_produk' ) ) $classes[] = 'product-archive-page';
    return $classes;
}
add_filter( 'body_class', 'tokoku_body_classes' );

/**
 * Modify archive title
 */
function tokoku_archive_title( $title ) {
    if ( is_post_type_archive( 'produk' ) ) return __( 'Semua Produk', 'tokoku' );
    if ( is_tax( 'kategori_produk' ) ) return single_term_title( '', false );
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
 * Products per page + sorting
 */
function tokoku_modify_product_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( ! is_post_type_archive( 'produk' ) && ! is_tax( 'kategori_produk' ) ) return;

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
