<?php
/**
 * Register Custom Post Type: Produk
 * Register Custom Taxonomy: Kategori Produk
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Produk CPT
 */
function tokoku_register_produk_cpt() {
    $labels = array(
        'name'                  => _x( 'Produk', 'Post type general name', 'tokoku' ),
        'singular_name'         => _x( 'Produk', 'Post type singular name', 'tokoku' ),
        'menu_name'             => _x( 'Produk', 'Admin Menu text', 'tokoku' ),
        'name_admin_bar'        => _x( 'Produk', 'Add New on Toolbar', 'tokoku' ),
        'add_new'               => __( 'Tambah Baru', 'tokoku' ),
        'add_new_item'          => __( 'Tambah Produk Baru', 'tokoku' ),
        'new_item'              => __( 'Produk Baru', 'tokoku' ),
        'edit_item'             => __( 'Edit Produk', 'tokoku' ),
        'view_item'             => __( 'Lihat Produk', 'tokoku' ),
        'all_items'             => __( 'Semua Produk', 'tokoku' ),
        'search_items'          => __( 'Cari Produk', 'tokoku' ),
        'not_found'             => __( 'Produk tidak ditemukan.', 'tokoku' ),
        'not_found_in_trash'    => __( 'Produk tidak ditemukan di Tong Sampah.', 'tokoku' ),
        'featured_image'        => __( 'Gambar Produk', 'tokoku' ),
        'set_featured_image'    => __( 'Set Gambar Produk', 'tokoku' ),
        'remove_featured_image' => __( 'Hapus Gambar Produk', 'tokoku' ),
        'use_featured_image'    => __( 'Gunakan sebagai Gambar Produk', 'tokoku' ),
        'archives'              => __( 'Arsip Produk', 'tokoku' ),
        'filter_items_list'     => __( 'Filter daftar produk', 'tokoku' ),
        'items_list_navigation' => __( 'Navigasi daftar produk', 'tokoku' ),
        'items_list'            => __( 'Daftar Produk', 'tokoku' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'produk', 'with_front' => false ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-cart',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'show_in_rest'       => false,
    );

    register_post_type( 'produk', $args );
}
add_action( 'init', 'tokoku_register_produk_cpt' );

/**
 * Register Kategori Produk Taxonomy
 */
function tokoku_register_kategori_taxonomy() {
    $labels = array(
        'name'              => _x( 'Kategori Produk', 'taxonomy general name', 'tokoku' ),
        'singular_name'     => _x( 'Kategori', 'taxonomy singular name', 'tokoku' ),
        'search_items'      => __( 'Cari Kategori', 'tokoku' ),
        'all_items'         => __( 'Semua Kategori', 'tokoku' ),
        'parent_item'       => __( 'Kategori Induk', 'tokoku' ),
        'parent_item_colon' => __( 'Kategori Induk:', 'tokoku' ),
        'edit_item'         => __( 'Edit Kategori', 'tokoku' ),
        'update_item'       => __( 'Update Kategori', 'tokoku' ),
        'add_new_item'      => __( 'Tambah Kategori Baru', 'tokoku' ),
        'new_item_name'     => __( 'Nama Kategori Baru', 'tokoku' ),
        'menu_name'         => __( 'Kategori', 'tokoku' ),
        'not_found'         => __( 'Kategori tidak ditemukan.', 'tokoku' ),
        'back_to_items'     => __( 'Kembali ke Kategori', 'tokoku' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'kategori' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'kategori_produk', array( 'produk' ), $args );
}
add_action( 'init', 'tokoku_register_kategori_taxonomy' );

/**
 * Add custom columns to Produk admin list
 */
function tokoku_produk_columns( $columns ) {
    $new_columns = array();
    foreach( $columns as $key => $value ) {
        if ( $key == 'title' ) {
            $new_columns['thumbnail'] = __( 'Foto', 'tokoku' );
            $new_columns[$key] = $value;
            $new_columns['kode_produk'] = __( 'Kode Produk', 'tokoku' );
        } else {
            $new_columns[$key] = $value;
        }
    }
    return $new_columns;
}
add_filter( 'manage_produk_posts_columns', 'tokoku_produk_columns' );

/**
 * Display content for custom columns
 */
function tokoku_produk_custom_column( $column, $post_id ) {
    switch ( $column ) {
        case 'thumbnail':
            if ( has_post_thumbnail( $post_id ) ) {
                echo get_the_post_thumbnail( $post_id, array( 50, 50 ) );
            } else {
                echo '<div style="width:50px;height:50px;background:#eee;border-radius:4px;"></div>';
            }
            break;
        case 'kode_produk':
            $sku = get_post_meta( $post_id, '_produk_sku', true );
            echo $sku ? '<strong>' . esc_html( $sku ) . '</strong>' : '<span style="color:#999;">—</span>';
            break;
    }
}
add_action( 'manage_produk_posts_custom_column', 'tokoku_produk_custom_column', 10, 2 );

/**
 * Flush rewrite rules on theme activation
 */
function tokoku_rewrite_flush() {
    tokoku_register_produk_cpt();
    tokoku_register_kategori_taxonomy();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'tokoku_rewrite_flush' );
