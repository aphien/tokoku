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
    $final_args = array(
        'post_type'      => 'produk',
        'post_status'    => 'publish',
        'posts_per_page' => 3,
    );

    if ( ! empty( $keyword ) ) {
        // 1. Search by SKU (Increased limit to improve "total" count accuracy)
        $args_sku = array(
            'post_type'      => 'produk',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => '_produk_sku',
                    'value'   => $keyword,
                    'compare' => 'LIKE',
                ),
            ),
        );
        $query_sku = new WP_Query( $args_sku );
        $ids_sku = $query_sku->posts;

        // 2. Search by Title (Increased limit)
        $args_title = array(
            'post_type'      => 'produk',
            'post_status'    => 'publish',
            's'              => $keyword,
            'posts_per_page' => 50,
            'fields'         => 'ids',
        );
        $query_title = new WP_Query( $args_title );
        $ids_title = $query_title->posts;

        // 3. Combine and filter
        $combined_ids = array_unique( array_merge( $ids_sku, $ids_title ) );
        
        if ( empty( $combined_ids ) ) {
            $combined_ids = array( -1 ); // No results
        }

        $final_args['post__in'] = $combined_ids;
        $final_args['orderby']  = 'post__in';
    } else {
        // Empty keyword: show latest products
        $final_args['orderby'] = 'date';
        $final_args['order']   = 'DESC';
    }

    $query = new WP_Query( $final_args );
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
    if ( ! is_wp_error( $categories ) ) {
        foreach ( $categories as $cat ) {
            $link = get_term_link( $cat );
            $cat_results[] = array(
                'name' => $cat->name,
                'link' => is_wp_error( $link ) ? '#' : $link,
            );
        }
    }

    // Get Tags (using tag_produk taxonomy)
    $tags = get_terms( array(
        'taxonomy' => 'tag_produk',
        'search'   => $keyword,
        'number'   => 3,
    ) );
    $tag_results = array();
    if ( ! is_wp_error( $tags ) ) {
        foreach ( $tags as $tag ) {
            $link = get_term_link( $tag );
            $tag_results[] = array(
                'name' => $tag->name,
                'link' => is_wp_error( $link ) ? '#' : $link,
            );
        }
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

