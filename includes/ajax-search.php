<?php
/**
 * AJAX Search Handler
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle AJAX search request
 */
function tokoku_ajax_search() {
    // Verify nonce
    if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'tokoku_search_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
    }

    $keyword = isset( $_GET['keyword'] ) ? sanitize_text_field( $_GET['keyword'] ) : '';

    // If keyword is empty or less than 2, we just don't search specifically, but we return latest products.
    // So we don't abort here.

    $args = array(
        'post_type'      => 'produk',
        'post_status'    => 'publish',
        's'              => $keyword,
        'posts_per_page' => 3,
        'orderby'        => 'relevance',
    );

    $query = new WP_Query( $args );
    $products = array();

    if ( $query->have_posts() ) {
        $show_price = get_theme_mod( 'tokoku_show_price', 'yes' );
        
        while ( $query->have_posts() ) {
            $query->the_post();
            
            $products[] = array(
                'id'         => get_the_ID(),
                'title'      => get_the_title(),
                'permalink'  => get_the_permalink(),
                'thumbnail'  => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
                'sku'        => get_post_meta( get_the_ID(), '_produk_sku', true ),
                'price_html' => ( $show_price === 'yes' ) ? tokoku_get_harga( get_the_ID() ) : '',
            );
        }
        wp_reset_postdata();
    }

    // Get Categories
    $categories = get_terms( array(
        'taxonomy' => 'kategori_produk',
        'search'   => $keyword,
        'number'   => 3,
    ) );
    $cat_results = array();
    foreach ( $categories as $cat ) {
        $cat_results[] = array(
            'name' => $cat->name,
            'link' => get_term_link( $cat ),
        );
    }

    // Get Tags (using post_tag or custom)
    $tags = get_terms( array(
        'taxonomy' => 'post_tag', // Or your custom tag taxonomy
        'search'   => $keyword,
        'number'   => 3,
    ) );
    $tag_results = array();
    foreach ( $tags as $tag ) {
        $tag_results[] = array(
            'name' => $tag->name,
            'link' => get_term_link( $tag ),
        );
    }

    wp_send_json_success( array(
        'products'   => $products,
        'categories' => $cat_results,
        'tags'       => $tag_results,
        'total'      => $query->found_posts,
    ) );
}
add_action( 'wp_ajax_tokoku_search', 'tokoku_ajax_search' );
add_action( 'wp_ajax_nopriv_tokoku_search', 'tokoku_ajax_search' );
