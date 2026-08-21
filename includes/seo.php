<?php
/**
 * SEO & Schema Markup Settings
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Output Product Schema Markup (JSON-LD) in <head> for Single Product Pages
 */
function tokoku_product_schema_markup() {
    // Hanya tampilkan di halaman single produk
    if ( ! is_singular( 'produk' ) ) {
        return;
    }

    global $post;
    
    // Ambil data meta produk
    $harga       = get_post_meta( $post->ID, '_produk_harga', true );
    $sku         = get_post_meta( $post->ID, '_produk_sku', true );
    $mata_uang   = get_theme_mod( 'tokoku_currency', 'IDR' ); // Schema butuh ISO code, misal IDR
    if ( $mata_uang == 'Rp' ) $mata_uang = 'IDR';
    
    // Stok Status
    $jumlah_stok = get_post_meta( $post->ID, '_produk_jumlah_stok', true );
    // Logika ketersediaan
    $availability = 'https://schema.org/InStock';
    $stok = tokoku_get_stok_status();
    if ( $stok['class'] == 'stok-preorder' ) {
        $availability = 'https://schema.org/PreOrder';
    } elseif ( $stok['class'] == 'stok-habis' || ( is_numeric($jumlah_stok) && $jumlah_stok <= 0 ) ) {
        $availability = 'https://schema.org/OutOfStock';
    }

    // Gambar Utama
    $image_url = get_the_post_thumbnail_url( $post->ID, 'full' );
    if ( ! $image_url ) {
        $image_url = esc_url( TOKOKU_URI . '/assets/images/placeholder.svg' );
    }

    // Deskripsi (hilangkan tag HTML)
    $description = wp_strip_all_tags( get_the_excerpt() );
    if ( empty( $description ) ) {
        $description = wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 );
    }
    if ( empty( $description ) ) {
        $description = get_the_title() . ' kualitas terbaik.';
    }

    // Membuat array Schema Markup
    $schema = array(
        '@context'    => 'https://schema.org/',
        '@type'       => 'Product',
        'name'        => get_the_title(),
        'image'       => $image_url,
        'description' => $description,
        'sku'         => $sku ? $sku : 'SKU-' . $post->ID,
        'brand'       => array(
            '@type' => 'Brand',
            'name'  => get_bloginfo( 'name' ),
        ),
        'offers'      => array(
            '@type'           => 'Offer',
            'url'             => get_permalink(),
            'priceCurrency'   => $mata_uang,
            'price'           => $harga ? $harga : '0',
            'availability'    => $availability,
            'itemCondition'   => 'https://schema.org/NewCondition',
        )
    );

    // Output JSON-LD
    echo "<!-- TokoKu Product Schema Markup -->\n";
    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . "\n";
    echo '</script>' . "\n";
}
add_action( 'wp_head', 'tokoku_product_schema_markup' );

/**
 * Output SEO Meta Tags in <head>
 */
function tokoku_seo_meta_tags() {
    global $post;

    // Defaults from Customizer
    $default_desc     = get_theme_mod( 'tokoku_seo_desc', get_bloginfo( 'description' ) );
    $default_keywords = get_theme_mod( 'tokoku_seo_keywords', '' );
    $default_image    = get_theme_mod( 'tokoku_seo_og_image', '' );
    if ( ! $default_image ) {
        $site_icon_id = get_option( 'site_icon' );
        if ( $site_icon_id ) {
            $default_image = wp_get_attachment_image_url( $site_icon_id, 'full' );
        }
    }

    $title       = '';
    $description = '';
    $keywords    = $default_keywords;
    $image       = $default_image;
    $url         = '';
    $type        = 'website';

    if ( is_singular() ) {
        $title = get_the_title();
        $url   = get_permalink();
        
        $excerpt = wp_strip_all_tags( get_the_excerpt() );
        if ( empty( $excerpt ) ) {
            $excerpt = wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 );
        }
        $description = !empty($excerpt) ? $excerpt : $default_desc;

        if ( has_post_thumbnail() ) {
            $image = get_the_post_thumbnail_url( null, 'full' );
        }
        
        if ( is_singular( 'produk' ) ) {
            $type = 'product';
        } else {
            $type = 'article';
        }
    } elseif ( is_post_type_archive( 'produk' ) || is_tax( 'kategori_produk' ) || is_tax( 'tag_produk' ) ) {
        if ( is_tax() ) {
            $title = single_term_title( '', false );
            $description = wp_strip_all_tags( term_description() );
        } else {
            $title = __( 'Semua Produk', 'tokoku' );
        }
        if ( empty($description) ) $description = $default_desc;
        
        global $wp;
        $url = home_url( add_query_arg( array(), $wp->request ) );
    } else {
        $title       = get_bloginfo( 'name' );
        $description = $default_desc;
        $url         = home_url( '/' );
    }

    $title = wp_strip_all_tags( $title );
    $description = wp_strip_all_tags( $description );

    // Output tags
    echo "<!-- TokoKu SEO Meta Tags -->\n";
    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    if ( $keywords ) {
        echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '">' . "\n";
    }

    // Open Graph & Twitter Cards
    if ( $title ) {
        echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
    }
    if ( $description ) {
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    }
    if ( $url ) {
        echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    }
    echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
}
add_action( 'wp_head', 'tokoku_seo_meta_tags', 5 );
