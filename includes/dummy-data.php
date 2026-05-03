<?php
/**
 * Dummy Data Generator for TokoKu
 */

function tokoku_generate_dummy_data() {
    // 1. Security Check: Nonce & Capability
    if ( ! isset( $_GET['generate_dummy_data'] ) ) {
        return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to perform this action.', 'tokoku' ) );
    }

    if ( ! check_admin_referer( 'tokoku_generate_dummy_action', '_wpnonce' ) ) {
        wp_die( esc_html__( 'Security check failed. Please try again.', 'tokoku' ) );
    }

    // 1. Create Categories
    $categories = array( 'Plakat Akrilik', 'Trophy Crystal', 'Vandel Kayu', 'Souvenir Kantor' );
    $cat_ids = array();
    foreach ( $categories as $cat ) {
        $term = wp_insert_term( $cat, 'kategori_produk' );
        $cat_ids[] = ! is_wp_error( $term ) ? $term['term_id'] : get_term_by( 'name', $cat, 'kategori_produk' )->term_id;
    }

    // 2. Sample Products
    $products = array(
        array(
            'title' => 'Plakat Akrilik Exclusive Diamond',
            'price' => 150000,
            'disc'  => 125000,
            'desc'  => 'Plakat akrilik dengan desain berlian yang mewah. Cocok untuk penghargaan formal dan kenang-kenangan eksklusif.',
            'img'   => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?w=800&q=80'
        ),
        array(
            'title' => 'Trophy Crystal Champion Gold',
            'price' => 250000,
            'disc'  => 0,
            'desc'  => 'Piala kristal bening dengan aksen emas. Tinggi 25cm, sangat elegan untuk pemenang turnamen atau event bergengsi.',
            'img'   => 'https://images.unsplash.com/photo-1589487391730-58f20eb2c308?w=800&q=80'
        ),
        array(
            'title' => 'Vandel Kayu Jati Klasik',
            'price' => 85000,
            'disc'  => 75000,
            'desc'  => 'Kenang-kenangan dari kayu jati asli dengan ukiran halus. Tahan lama dan memberikan kesan natural yang mendalam.',
            'img'   => 'https://images.unsplash.com/photo-1590013330462-094d45598124?w=800&q=80'
        ),
        array(
            'title' => 'Souvenir Mug Custom Premium',
            'price' => 45000,
            'disc'  => 0,
            'desc'  => 'Mug keramik berkualitas tinggi yang bisa di-custom dengan logo atau foto sesuai keinginan Anda.',
            'img'   => 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=800&q=80'
        ),
        array(
            'title' => 'Plakat Kayu Kombinasi Logam',
            'price' => 175000,
            'disc'  => 150000,
            'desc'  => 'Perpaduan sempurna antara kehangatan kayu dan ketajaman logam. Desain modern untuk apresiasi kerja keras tim.',
            'img'   => 'https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?w=800&q=80'
        ),
        array(
            'title' => 'Medali Logam Custom Finish',
            'price' => 35000,
            'disc'  => 25000,
            'desc'  => 'Medali untuk berbagai kompetisi. Tersedia dalam warna Gold, Silver, dan Bronze dengan tali ribbon custom.',
            'img'   => 'https://images.unsplash.com/photo-1589487391730-58f20eb2c308?w=800&q=80'
        ),
    );

    foreach ( $products as $index => $p ) {
        // Create Post
        $post_id = wp_insert_post( array(
            'post_title'   => $p['title'],
            'post_content' => $p['desc'],
            'post_status'  => 'publish',
            'post_type'    => 'produk',
        ) );

        if ( $post_id ) {
            // Set Category
            wp_set_object_terms( $post_id, $cat_ids[ $index % count($cat_ids) ], 'kategori_produk' );

            // Set Meta
            update_post_meta( $post_id, '_produk_harga', $p['price'] );
            update_post_meta( $post_id, '_produk_harga_diskon', $p['disc'] );
            update_post_meta( $post_id, '_produk_berat', '500 gram' );
            update_post_meta( $post_id, '_produk_stok', 'tersedia' );
            update_post_meta( $post_id, '_produk_dummy_img', $p['img'] );
            
            // Note: Setting featured image from URL is tricky, 
            // but for dummy purposes, we'll just log it or skip for now.
            // In a real scenario, we'd sideload the image.
        }
    }

    // 3. Set Customizer Defaults
    set_theme_mod( 'tokoku_wa_number', '6281234567890' );
    set_theme_mod( 'tokoku_hero_title', 'Pusat Plakat & Trophy Berkualitas' );
    set_theme_mod( 'tokoku_hero_subtitle', 'Temukan berbagai pilihan plakat, trophy, dan souvenir kustom dengan kualitas terbaik dan pengerjaan cepat.' );
    set_theme_mod( 'tokoku_site_description', 'TokoKu adalah spesialis pembuatan plakat akrilik, trophy crystal, dan vandel kayu terpercaya sejak 2010.' );

    // 4. Create Dummy Menu
    $menu_name = 'Menu Utama';
    $menu_exists = wp_get_nav_menu_object( $menu_name );

    if ( ! $menu_exists ) {
        $menu_id = wp_create_nav_menu( $menu_name );

        // Home
        wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'  => 'Home',
            'menu-item-url'    => home_url( '/' ),
            'menu-item-status' => 'publish',
        ) );

        // Semua Produk
        wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'  => 'Semua Produk',
            'menu-item-url'    => get_post_type_archive_link( 'produk' ),
            'menu-item-status' => 'publish',
        ) );

        // Kategori (Parent)
        $cat_parent_id = wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'  => 'Kategori',
            'menu-item-url'    => '#',
            'menu-item-status' => 'publish',
        ) );

        // Add Category Submenus
        foreach ( $cat_ids as $index => $cid ) {
            $term = get_term( $cid, 'kategori_produk' );
            wp_update_nav_menu_item( $menu_id, 0, array(
                'menu-item-title'     => $term->name,
                'menu-item-url'       => get_term_link( $term ),
                'menu-item-parent-id' => $cat_parent_id,
                'menu-item-status'    => 'publish',
            ) );
        }

        // Tentang Kami
        wp_update_nav_menu_item( $menu_id, 0, array(
            'menu-item-title'  => 'Tentang Kami',
            'menu-item-url'    => '#footer',
            'menu-item-status' => 'publish',
        ) );

        // Assign to location
        $locations = get_theme_mod( 'nav_menu_locations' );
        $locations['primary'] = $menu_id;
        set_theme_mod( 'nav_menu_locations', $locations );
    }

    wp_die( 'Dummy data & Menu berhasil dibuat! Silakan kembali ke <a href="' . admin_url() . '">Dashboard</a>.' );
}
add_action( 'admin_init', 'tokoku_generate_dummy_data' );
