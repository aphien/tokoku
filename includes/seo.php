<?php
/**
 * SEO & Metadata logic
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Output SEO Meta Tags in wp_head
 */
function tokoku_seo_meta_tags() {
    global $wp;
    $description = '';
    $keywords    = get_theme_mod( 'tokoku_seo_keywords', '' );
    $og_image    = get_theme_mod( 'tokoku_seo_og_image', '' );
    $site_name   = get_bloginfo( 'name' );
    $url         = home_url( add_query_arg( array(), $wp->request ) );

    if ( is_home() || is_front_page() ) {
        $description = get_theme_mod( 'tokoku_seo_desc', get_bloginfo( 'description' ) );
    } elseif ( is_singular( 'produk' ) ) {
        $post = get_post();
        $description = wp_trim_words( $post->post_content, 25 );
        if ( has_post_thumbnail() ) {
            $og_image = get_the_post_thumbnail_url( $post->ID, 'large' );
        }
    } elseif ( is_archive() ) {
        $description = get_the_archive_description();
        if ( ! $description ) {
            $description = sprintf( __( 'Kumpulan produk dalam kategori %s', 'tokoku' ), get_the_archive_title() );
        }
    }

    // Basic Meta
    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    if ( $keywords ) {
        echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '">' . "\n";
    }

    // Open Graph
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    echo '<meta property="og:type" content="' . ( is_singular() ? 'article' : 'website' ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
    if ( $description ) {
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    if ( $og_image ) {
        echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
    if ( $description ) {
        echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    if ( $og_image ) {
        echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'tokoku_seo_meta_tags', 1 );
