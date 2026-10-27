<?php
/**
 * Static Page Templates (Tentang, Kontak, Syarat & Ketentuan, Kebijakan Privasi)
 *
 * Menyediakan helper data kontak, pemuatan aset khusus template halaman statis,
 * serta pembuatan otomatis halaman bawaan satu kali saat admin membuka dasbor.
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Daftar template halaman statis beserta slug & judul bawaan.
 *
 * @return array<string, array{slug:string,title:string}>
 */
function tokoku_static_page_templates() {
    return array(
        'page-templates/template-tentang.php' => array(
            'slug'  => 'tentang-kami',
            'title' => 'Tentang Kami',
        ),
        'page-templates/template-kontak.php'  => array(
            'slug'  => 'kontak',
            'title' => 'Kontak',
        ),
        'page-templates/template-terms.php'   => array(
            'slug'  => 'syarat-dan-ketentuan',
            'title' => 'Syarat & Ketentuan',
        ),
        'page-templates/template-privacy.php' => array(
            'slug'  => 'kebijakan-privasi',
            'title' => 'Kebijakan Privasi',
        ),
    );
}

/**
 * Ambil nama domain aktif dari instalasi website saat ini.
 *
 * @return string
 */
function tokoku_get_site_domain() {
    $host = wp_parse_url( home_url(), PHP_URL_HOST );
    if ( empty( $host ) && ! empty( $_SERVER['HTTP_HOST'] ) ) {
        $host = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
    }
    if ( $host && strpos( $host, ':' ) !== false ) {
        $parts = explode( ':', $host );
        $host = $parts[0];
    }
    if ( $host && 0 === strpos( $host, 'www.' ) ) {
        $host = substr( $host, 4 );
    }
    return ! empty( $host ) ? $host : 'localhost';
}

/**
 * Nama brand yang ditampilkan pada halaman statis & teks tujuan tombol.
 * Secara otomatis mengikuti domain website aktif pada instalasi tema.
 *
 * @return string
 */
function tokoku_page_brand() {
    $domain = tokoku_get_site_domain();
    return (string) apply_filters( 'tokoku_page_brand', $domain );
}

/**
 * Data kontak toko terpusat (diambil dari pengaturan tema).
 *
 * @return array
 */
function tokoku_page_contact() {
    $wa = tokoku_get_wa_number();

    // Format tampilan: +62 812-3456-7890
    $display = $wa;
    if ( 0 === strpos( $wa, '62' ) && strlen( $wa ) >= 10 ) {
        $local   = substr( $wa, 2 );
        $display = '+62 ' . substr( $local, 0, 3 ) . '-' . substr( $local, 3, 4 ) . '-' . substr( $local, 7 );
    }

    $hours = array();
    for ( $i = 1; $i <= 3; $i++ ) {
        $jam = trim( (string) get_theme_mod( "tokoku_jam_op_{$i}", '' ) );
        if ( '' !== $jam ) {
            $hours[] = $jam;
        }
    }
    if ( empty( $hours ) ) {
        $hours = array( 'Senin – Jumat: 08.00 – 17.00 WIB', 'Sabtu: 08.00 – 15.00 WIB', 'Minggu & Libur Nasional: Tutup' );
    }

    $agents = array();
    for ( $i = 1; $i <= 5; $i++ ) {
        $name = trim( (string) get_theme_mod( "tokoku_contact_name_{$i}", '' ) );
        $num  = preg_replace( '/\D+/', '', (string) get_theme_mod( "tokoku_contact_wa_{$i}", '' ) );
        $role = trim( (string) get_theme_mod( "tokoku_contact_role_{$i}", '' ) );
        if ( '' !== $name && '' !== $num ) {
            $agents[] = array( 
                'name' => $name, 
                'wa'   => $num,
                'role' => $role,
            );
        }
    }

    $socials = array();
    foreach ( array( 'instagram', 'facebook', 'tiktok', 'youtube', 'linkedin', 'twitter' ) as $key ) {
        $link = (string) get_theme_mod( "tokoku_social_{$key}", '' );
        if ( '' !== $link ) {
            $socials[ $key ] = $link;
        }
    }
    // Fallback default social media links jika admin belum mengisinya
    if ( empty( $socials ) ) {
        $socials = array(
            'instagram' => 'https://www.instagram.com/',
            'tiktok'    => 'https://www.tiktok.com/',
            'facebook'  => 'https://www.facebook.com/',
            'youtube'   => 'https://www.youtube.com/',
        );
    }

    $active_domain = tokoku_get_site_domain();
    $email         = sanitize_email( (string) get_theme_mod( 'tokoku_store_email', '' ) );
    $phone         = sanitize_text_field( (string) get_theme_mod( 'tokoku_store_phone', '' ) );
    $maps          = esc_url_raw( (string) get_theme_mod( 'tokoku_store_maps_url', '' ) );
    $address       = trim( wp_strip_all_tags( (string) get_theme_mod( 'tokoku_store_address', '' ) ) );

    return array(
        'wa'         => $wa,
        'wa_display' => $display,
        'email'      => ! empty( $email ) ? $email : 'halo@' . $active_domain,
        'phone'      => ! empty( $phone ) ? $phone : '+62 812-3456-7890',
        'maps_url'   => $maps,
        'address'    => ! empty( $address ) ? $address : 'Jl. Percetakan Negara No. 12, Johar Baru, Jakarta Pusat 10560',
        'hours'      => $hours,
        'agents'     => $agents,
        'socials'    => $socials,
    );
}

/**
 * URL halaman statis berdasarkan template (fallback ke slug bawaan).
 *
 * @param string $template Path template relatif.
 * @return string
 */
function tokoku_static_page_url( $template ) {
    $pages = get_pages( array(
        'meta_key'    => '_wp_page_template',
        'meta_value'  => $template,
        'number'      => 1,
        'post_status' => 'publish',
    ) );
    if ( ! empty( $pages ) ) {
        return get_permalink( $pages[0] );
    }
    $map = tokoku_static_page_templates();
    return isset( $map[ $template ] ) ? home_url( '/' . $map[ $template ]['slug'] . '/' ) : home_url( '/' );
}

/**
 * Apakah halaman saat ini memakai salah satu template statis.
 *
 * @return bool
 */
function tokoku_is_static_page_template() {
    return is_page() && is_page_template( array_keys( tokoku_static_page_templates() ) );
}

/**
 * Memuat CSS & JS khusus hanya pada halaman statis (tidak membebani halaman lain).
 */
function tokoku_static_pages_assets() {
    if ( ! tokoku_is_static_page_template() ) {
        return;
    }
    wp_enqueue_style( 'tokoku-pages-style', TOKOKU_URI . '/assets/css/pages.css', array( 'tokoku-main-style' ), TOKOKU_VERSION );
    wp_enqueue_script( 'tokoku-pages-js', TOKOKU_URI . '/assets/js/pages.js', array(), TOKOKU_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'tokoku_static_pages_assets', 20 );

/**
 * Menambahkan body class untuk halaman statis.
 *
 * @param array $classes Body classes.
 * @return array
 */
function tokoku_static_pages_body_class( $classes ) {
    if ( tokoku_is_static_page_template() ) {
        $classes[] = 'jp-static-page';
    }
    return $classes;
}
add_filter( 'body_class', 'tokoku_static_pages_body_class' );

/**
 * Membuat halaman Tentang, Kontak, Syarat & Ketentuan, dan Kebijakan Privasi
 * secara otomatis satu kali. Halaman yang sudah ada tidak akan ditimpa.
 */
function tokoku_maybe_create_static_pages() {
    if ( ! current_user_can( 'manage_options' ) || get_option( 'tokoku_static_pages_v1' ) ) {
        return;
    }

    foreach ( tokoku_static_page_templates() as $template => $data ) {
        $existing = get_page_by_path( $data['slug'], OBJECT, 'page' );

        if ( $existing ) {
            $current = get_post_meta( $existing->ID, '_wp_page_template', true );
            if ( empty( $current ) || 'default' === $current ) {
                update_post_meta( $existing->ID, '_wp_page_template', $template );
            }
            $page_id = $existing->ID;
        } else {
            $page_id = wp_insert_post( array(
                'post_title'     => $data['title'],
                'post_name'      => $data['slug'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_content'   => '',
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
                'meta_input'     => array( '_wp_page_template' => $template ),
            ) );
        }

        // Hubungkan halaman privasi ke pengaturan Privasi bawaan WordPress.
        if ( 'page-templates/template-privacy.php' === $template && $page_id && ! is_wp_error( $page_id ) ) {
            $privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
            if ( ! $privacy_id || 'publish' !== get_post_status( $privacy_id ) ) {
                update_option( 'wp_page_for_privacy_policy', (int) $page_id );
            }
        }
    }

    update_option( 'tokoku_static_pages_v1', TOKOKU_VERSION, false );
}
add_action( 'admin_init', 'tokoku_maybe_create_static_pages' );

/**
 * Ikon SVG presisi untuk halaman statis.
 * Mendukung ikon solid (seperti WhatsApp) dan ikon outline (Lucide/Feather).
 *
 * @param string $name Nama ikon.
 * @param int    $size Ukuran px.
 * @return string
 */
function tokoku_page_icon( $name, $size = 22 ) {
    $size = absint( $size );

    // Sinkronisasi otomatis ke Font Awesome 6 resmi jika tersedia
    if ( function_exists( 'tokoku_icon' ) ) {
        $icon = tokoku_icon( $name, $size, 'jp-icon jp-icon--' . sanitize_key( $name ) );
        if ( ! empty( $icon ) ) {
            return $icon;
        }
    }

    // Ikon solid khusus seperti WhatsApp resmi
    if ( 'whatsapp' === $name ) {
        return sprintf(
            '<svg class="jp-icon jp-icon--wa" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/></svg>',
            $size
        );
    }

    // Ikon Brand Sosial Media Resmi
    if ( 'instagram' === $name ) {
        return sprintf(
            '<svg class="jp-icon jp-icon--instagram" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
            $size
        );
    }

    if ( 'facebook' === $name ) {
        return sprintf(
            '<svg class="jp-icon jp-icon--facebook" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
            $size
        );
    }

    if ( 'tiktok' === $name ) {
        return sprintf(
            '<svg class="jp-icon jp-icon--tiktok" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>',
            $size
        );
    }

    if ( 'youtube' === $name ) {
        return sprintf(
            '<svg class="jp-icon jp-icon--youtube" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.42a2.78 2.78 0 0 0-1.94 2C1 8.11 1 12 1 12s0 3.89.46 5.58a2.78 2.78 0 0 0 1.94 2c1.72.42 8.6.42 8.6.42s6.88 0 8.6-.42a2.78 2.78 0 0 0 1.94-2C23 15.89 23 12 23 12s0-3.89-.46-5.58z"></path><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"></polygon></svg>',
            $size
        );
    }

    if ( 'linkedin' === $name ) {
        return sprintf(
            '<svg class="jp-icon jp-icon--linkedin" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>',
            $size
        );
    }

    if ( 'twitter' === $name || 'x' === $name ) {
        return sprintf(
            '<svg class="jp-icon jp-icon--twitter" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true" focusable="false"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
            $size
        );
    }

    $paths = array(
        'mail'     => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
        'pin'      => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'clock'    => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
        'file'     => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'award'    => '<circle cx="12" cy="8" r="6"/><path d="M15.5 13.2 17 22l-5-3-5 3 1.5-8.8"/>',
        'target'   => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
        'gem'      => '<path d="M6 3h12l4 6-10 13L2 9Z"/><path d="M11 3 8 9l4 13 4-13-3-6"/><path d="M2 9h20"/>',
        'pen'      => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'zap'      => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
        'chat'     => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'tag'      => '<path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
        'truck'    => '<rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'layers'   => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
        'eye'      => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'arrow'    => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
        'chevron'  => '<polyline points="9 18 15 12 9 6"/>',
        'check'    => '<polyline points="20 6 9 17 4 12"/>',
        'copy'     => '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'book'     => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
        'list'     => '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>',
        'users'    => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'heart'    => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/>',
        'send'     => '<line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>',
    );

    if ( ! isset( $paths[ $name ] ) ) {
        return '';
    }

    return sprintf(
        '<svg class="jp-icon" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
        $size,
        $paths[ $name ]
    );
}

/**
 * Helper fallback untuk mengambil pengaturan theme mod.
 * Jika opsi bernilai string kosong ("") atau belum disetel, mengembalikan nilai bawaan ($default).
 *
 * @param string $key     Nama setting theme mod.
 * @param mixed  $default Nilai default jika kosong.
 * @return mixed
 */
function tokoku_get_theme_mod_fallback( $key, $default = '' ) {
    $val = get_theme_mod( $key, $default );
    if ( is_string( $val ) && '' === trim( $val ) ) {
        return $default;
    }
    return $val;
}

/**
 * Data konten dinamis untuk Template Halaman Tentang Kami.
 *
 * @return array
 */
function tokoku_get_about_page_data() {
    $brand       = tokoku_page_brand();
    $catalog_url = get_post_type_archive_link( 'produk' ) ? get_post_type_archive_link( 'produk' ) : home_url( '/' );

    $product_count = 0;
    if ( post_type_exists( 'produk' ) ) {
        $counts        = wp_count_posts( 'produk' );
        $product_count = isset( $counts->publish ) ? (int) $counts->publish : 0;
    }
    $category_count = taxonomy_exists( 'kategori_produk' ) ? (int) wp_count_terms( array( 'taxonomy' => 'kategori_produk', 'hide_empty' => true ) ) : 0;

    // Statistik Prestasi (4 item)
    $stats = array(
        array(
            'value' => tokoku_get_theme_mod_fallback( 'tokoku_about_stat1_val', $product_count > 0 ? $product_count . '+' : '100%' ),
            'label' => tokoku_get_theme_mod_fallback( 'tokoku_about_stat1_lbl', $product_count > 0 ? __( 'Pilihan Desain Produk', 'tokoku' ) : __( 'Desain Custom', 'tokoku' ) ),
        ),
        array(
            'value' => tokoku_get_theme_mod_fallback( 'tokoku_about_stat2_val', $category_count > 0 ? (string) $category_count : '5+' ),
            'label' => tokoku_get_theme_mod_fallback( 'tokoku_about_stat2_lbl', __( 'Kategori Material', 'tokoku' ) ),
        ),
        array(
            'value' => tokoku_get_theme_mod_fallback( 'tokoku_about_stat3_val', __( 'Gratis', 'tokoku' ) ),
            'label' => tokoku_get_theme_mod_fallback( 'tokoku_about_stat3_lbl', __( 'Konsultasi & Mockup', 'tokoku' ) ),
        ),
        array(
            'value' => tokoku_get_theme_mod_fallback( 'tokoku_about_stat4_val', '34' ),
            'label' => tokoku_get_theme_mod_fallback( 'tokoku_about_stat4_lbl', __( 'Provinsi Terjangkau', 'tokoku' ) ),
        ),
    );

    // Nilai & Komitmen (6 item)
    $default_values = array(
        1 => array( 'icon' => 'target', 'title' => __( 'Presisi di Setiap Detail', 'tokoku' ), 'text' => __( 'Setiap huruf, garis grafir, dan sudut potongan kami periksa berlapis. Karena sebuah penghargaan layak tampil sempurna.', 'tokoku' ) ),
        2 => array( 'icon' => 'gem',    'title' => __( 'Material Pilihan', 'tokoku' ),         'text' => __( 'Akrilik bening berkualitas, kayu solid, resin jernih, hingga logam premium — dipilih agar tahan lama dan tetap memukau.', 'tokoku' ) ),
        3 => array( 'icon' => 'pen',    'title' => __( 'Desain yang Personal', 'tokoku' ),     'text' => __( 'Tidak ada template asal jadi. Tim desain kami merancang setiap plakat agar mencerminkan identitas dan cerita Anda.', 'tokoku' ) ),
        4 => array( 'icon' => 'zap',    'title' => __( 'Tepat Waktu', 'tokoku' ),              'text' => __( 'Kami memahami bahwa acara tidak bisa ditunda. Jadwal produksi disusun realistis dan dikomunikasikan sejak awal.', 'tokoku' ) ),
        5 => array( 'icon' => 'chat',   'title' => __( 'Komunikasi Responsif', 'tokoku' ),     'text' => __( 'Konsultasi langsung melalui WhatsApp dengan tim yang ramah, sigap, dan siap memberi rekomendasi terbaik.', 'tokoku' ) ),
        6 => array( 'icon' => 'tag',    'title' => __( 'Harga Transparan', 'tokoku' ),         'text' => __( 'Rincian biaya disampaikan jelas di awal — tanpa biaya tersembunyi, tanpa kejutan di akhir.', 'tokoku' ) ),
    );

    $values = array();
    for ( $i = 1; $i <= 6; $i++ ) {
        $icon  = tokoku_get_theme_mod_fallback( "tokoku_about_val_icon_{$i}", $default_values[$i]['icon'] );
        $title = tokoku_get_theme_mod_fallback( "tokoku_about_val_title_{$i}", $default_values[$i]['title'] );
        $text  = tokoku_get_theme_mod_fallback( "tokoku_about_val_text_{$i}", $default_values[$i]['text'] );
        if ( '' !== trim( $title ) ) {
            $values[] = array( 'icon' => $icon, 'title' => $title, 'text' => $text );
        }
    }

    // Langkah Kerja (4 item)
    $default_steps = array(
        1 => array( 'icon' => 'chat',   'title' => __( 'Konsultasi', 'tokoku' ),        'text' => __( 'Ceritakan kebutuhan Anda: jenis plakat, jumlah, teks, logo, dan tanggal acara.', 'tokoku' ) ),
        2 => array( 'icon' => 'layers', 'title' => __( 'Desain & Mockup', 'tokoku' ),   'text' => __( 'Kami kirimkan pratinjau desain gratis dan siap direvisi hingga Anda benar-benar puas.', 'tokoku' ) ),
        3 => array( 'icon' => 'award',  'title' => __( 'Produksi Presisi', 'tokoku' ),  'text' => __( 'Setelah ACC, plakat diproduksi dengan mesin modern dan sentuhan akhir tangan terampil.', 'tokoku' ) ),
        4 => array( 'icon' => 'truck',  'title' => __( 'Kirim dengan Aman', 'tokoku' ), 'text' => __( 'Dikemas berlapis dan dikirim ke seluruh Indonesia dengan nomor resi yang bisa dilacak.', 'tokoku' ) ),
    );

    $steps = array();
    for ( $i = 1; $i <= 4; $i++ ) {
        $icon  = tokoku_get_theme_mod_fallback( "tokoku_about_step_icon_{$i}", $default_steps[$i]['icon'] );
        $title = tokoku_get_theme_mod_fallback( "tokoku_about_step_title_{$i}", $default_steps[$i]['title'] );
        $text  = tokoku_get_theme_mod_fallback( "tokoku_about_step_text_{$i}", $default_steps[$i]['text'] );
        if ( '' !== trim( $title ) ) {
            $steps[] = array( 'icon' => $icon, 'title' => $title, 'text' => $text );
        }
    }

    // Poin Misi
    $misi_raw = get_theme_mod( 'tokoku_about_misi_points', '' );
    if ( '' !== trim( $misi_raw ) ) {
        $misi_lines = array_filter( array_map( 'trim', explode( "\n", str_replace( "\r", '', $misi_raw ) ) ) );
    } else {
        $misi_lines = array(
            __( 'Menghadirkan produk penghargaan berkualitas tinggi dengan harga yang adil dan transparan.', 'tokoku' ),
            __( 'Memberikan pengalaman pemesanan yang mudah, cepat, dan menyenangkan dari mana saja.', 'tokoku' ),
            __( 'Terus berinovasi dalam desain, material, dan teknik produksi.', 'tokoku' ),
            __( 'Membangun hubungan jangka panjang yang dilandasi kepercayaan dengan setiap pelanggan.', 'tokoku' ),
        );
    }

    // Chips Kategori Klien
    $clients_raw = get_theme_mod( 'tokoku_about_clients_chips', '' );
    if ( '' !== trim( $clients_raw ) ) {
        $clients = array_filter( array_map( 'trim', explode( ',', $clients_raw ) ) );
    } else {
        $clients = array( 'Instansi Pemerintah', 'BUMN & Korporasi', 'Sekolah & Universitas', 'Organisasi & Komunitas', 'Event Olahraga', 'Rumah Sakit & Klinik', 'Perbankan', 'Kado Personal' );
    }

    return array(
        'hero_badge'     => tokoku_get_theme_mod_fallback( 'tokoku_about_hero_badge', sprintf( __( 'Tentang %s', 'tokoku' ), $brand ) ),
        'hero_title'     => tokoku_get_theme_mod_fallback( 'tokoku_about_hero_title', __( 'Mengabadikan Setiap Pencapaian dalam <span class="jp-text-gradient">Karya yang Bermakna</span>', 'tokoku' ) ),
        'hero_desc'      => tokoku_get_theme_mod_fallback( 'tokoku_about_hero_desc', sprintf( __( '%s adalah mitra terpercaya untuk pembuatan plakat, piala, dan souvenir penghargaan custom. Kami percaya setiap prestasi, dedikasi, dan momen berharga layak dikenang dalam wujud yang indah, berkelas, dan tahan lama.', 'tokoku' ), $brand ) ),
        'hero_btn1_text' => tokoku_get_theme_mod_fallback( 'tokoku_about_hero_btn1_text', __( 'Jelajahi Katalog', 'tokoku' ) ),
        'hero_btn1_url'  => tokoku_get_theme_mod_fallback( 'tokoku_about_hero_btn1_url', $catalog_url ),
        'hero_btn2_text' => tokoku_get_theme_mod_fallback( 'tokoku_about_hero_btn2_text', __( 'Konsultasi Gratis', 'tokoku' ) ),
        'plaque_eyebrow' => tokoku_get_theme_mod_fallback( 'tokoku_about_plaque_eyebrow', __( 'Penghargaan', 'tokoku' ) ),
        'plaque_title'   => tokoku_get_theme_mod_fallback( 'tokoku_about_plaque_title', __( 'Apresiasi Terbaik', 'tokoku' ) ),
        'plaque_text'    => tokoku_get_theme_mod_fallback( 'tokoku_about_plaque_text', __( 'Diberikan atas dedikasi, kerja keras, dan prestasi yang menginspirasi.', 'tokoku' ) ),
        'stats'          => $stats,
        'quote_text'     => tokoku_get_theme_mod_fallback( 'tokoku_about_quote_text', __( 'Sebuah plakat bukan sekadar benda. Ia adalah pengingat bahwa kerja keras seseorang pernah dilihat, dihargai, dan dirayakan.', 'tokoku' ) ),
        'quote_author'   => tokoku_get_theme_mod_fallback( 'tokoku_about_quote_author', '— Filosofi ' . $brand ),
        'story_eyebrow'  => tokoku_get_theme_mod_fallback( 'tokoku_about_story_eyebrow', __( 'Cerita Kami', 'tokoku' ) ),
        'story_title'    => tokoku_get_theme_mod_fallback( 'tokoku_about_story_title', __( 'Berawal dari Keyakinan Sederhana: Setiap Apresiasi Layak Istimewa', 'tokoku' ) ),
        'story_content'  => get_theme_mod( 'tokoku_about_story_content', '' ),
        'values_eyebrow' => tokoku_get_theme_mod_fallback( 'tokoku_about_values_eyebrow', __( 'Mengapa Memilih Kami', 'tokoku' ) ),
        'values_title'   => tokoku_get_theme_mod_fallback( 'tokoku_about_values_title', __( 'Komitmen yang Kami Pegang di Setiap Pesanan', 'tokoku' ) ),
        'values_sub'     => tokoku_get_theme_mod_fallback( 'tokoku_about_values_sub', sprintf( __( 'Enam prinsip yang menjadikan setiap plakat dari %s layak dipercaya untuk momen paling penting Anda.', 'tokoku' ), $brand ) ),
        'values'         => $values,
        'steps_eyebrow'  => tokoku_get_theme_mod_fallback( 'tokoku_about_steps_eyebrow', __( 'Cara Kami Bekerja', 'tokoku' ) ),
        'steps_title'    => tokoku_get_theme_mod_fallback( 'tokoku_about_steps_title', __( 'Dari Ide Menjadi Karya dalam 4 Langkah Mudah', 'tokoku' ) ),
        'steps'          => $steps,
        'visi_eyebrow'   => tokoku_get_theme_mod_fallback( 'tokoku_about_visi_eyebrow', __( 'Visi', 'tokoku' ) ),
        'visi_text'      => tokoku_get_theme_mod_fallback( 'tokoku_about_visi_text', __( 'Menjadi destinasi utama pembuatan plakat dan penghargaan custom di Indonesia yang dikenal karena kualitas, kreativitas, dan pelayanan sepenuh hati.', 'tokoku' ) ),
        'misi_eyebrow'   => tokoku_get_theme_mod_fallback( 'tokoku_about_misi_eyebrow', __( 'Misi', 'tokoku' ) ),
        'misi_points'    => $misi_lines,
        'clients_eyebrow'=> tokoku_get_theme_mod_fallback( 'tokoku_about_clients_eyebrow', __( 'Dipercaya Beragam Kalangan', 'tokoku' ) ),
        'clients_title'  => tokoku_get_theme_mod_fallback( 'tokoku_about_clients_title', __( 'Partner & Klien Apresiasi Kami', 'tokoku' ) ),
        'clients_sub'    => tokoku_get_theme_mod_fallback( 'tokoku_about_clients_sub', __( 'Dipercaya oleh instansi pemerintah, BUMN, perguruan tinggi ternama, korporasi swasta, hingga ribuan komunitas di seluruh Indonesia.', 'tokoku' ) ),
        'clients'        => $clients,
        'cta_title'      => tokoku_get_theme_mod_fallback( 'tokoku_about_cta_title', __( 'Siap Mewujudkan Plakat Penghargaan Anda?', 'tokoku' ) ),
        'cta_sub'        => tokoku_get_theme_mod_fallback( 'tokoku_about_cta_sub', __( 'Konsultasikan kebutuhan Anda sekarang. Gratis desain dan mockup — Anda hanya membayar ketika sudah yakin.', 'tokoku' ) ),
        'cta_btn1_text'  => tokoku_get_theme_mod_fallback( 'tokoku_about_cta_btn1_text', __( 'Chat WhatsApp', 'tokoku' ) ),
        'cta_btn2_text'  => tokoku_get_theme_mod_fallback( 'tokoku_about_cta_btn2_text', __( 'Hubungi Kami', 'tokoku' ) ),
    );
}

/**
 * Data konten dinamis untuk Template Halaman Kontak.
 *
 * @return array
 */
function tokoku_get_contact_page_data() {
    $brand = tokoku_page_brand();

    $default_faqs = array(
        1 => array(
            'q' => __( 'Berapa lama proses pembuatan plakat custom?', 'tokoku' ),
            'a' => __( 'Rata-rata pengerjaan berkisar antara 1 hingga 3 hari kerja setelah mockup desain disetujui (ACC) dan pembayaran terkonfirmasi. Untuk kebutuhan mendesak (urgent/ekspres), silakan konsultasikan dengan tim kami agar dapat dijadwalkan secara prioritas.', 'tokoku' ),
        ),
        2 => array(
            'q' => __( 'Apakah bisa membuat plakat dengan desain sendiri atau tanpa desain?', 'tokoku' ),
            'a' => __( 'Tentu saja! Jika Anda sudah memiliki file desain (AI, CorelDraw, PDF, atau PNG beresolusi tinggi), Anda dapat langsung mengirimkannya. Jika belum memiliki desain, tim desainer kreatif kami siap membuatkan mockup gratis sesuai konsep, logo, dan tulisan yang Anda inginkan.', 'tokoku' ),
        ),
        3 => array(
            'q' => __( 'Apakah ada jumlah minimal pemesanan (MOQ)?', 'tokoku' ),
            'a' => __( 'Kami melayani pemesanan mulai dari 1 buah (satuan) untuk kado personal atau penghargaan khusus, hingga ribuan buah untuk kebutuhan wisuda, turnamen olahraga, dan corporate event besar dengan harga grosir kompetitif.', 'tokoku' ),
        ),
        4 => array(
            'q' => __( 'Bagaimana pengemasan dan keamanan pengiriman ke luar kota?', 'tokoku' ),
            'a' => __( 'Setiap produk kami lindungi dengan lapisan bubble wrap tebal, busa pelindung khusus, dan kardus kokoh atau packing kayu ekstra. Kami bermitra dengan ekspedisi tepercaya (JNE, J&T, SiCepat, Cargo, dsb.) lengkap dengan nomor resi pelacakan dan opsi asuransi pengiriman.', 'tokoku' ),
        ),
        5 => array(
            'q' => __( 'Bagaimana cara mendapatkan surat penawaran harga resmi (invoice/quotation)?', 'tokoku' ),
            'a' => __( 'Cukup kirimkan detail pesanan (jenis produk, estimasi jumlah, spesifikasi, dan nama instansi) melalui formulir kontak di bawah atau via WhatsApp. Tim administrasi kami akan menerbitkan invoice / surat penawaran resmi bertanda tangan.', 'tokoku' ),
        ),
    );

    $faqs = array();
    for ( $i = 1; $i <= 5; $i++ ) {
        $q = tokoku_get_theme_mod_fallback( "tokoku_contact_faq_q_{$i}", $default_faqs[$i]['q'] );
        $a = tokoku_get_theme_mod_fallback( "tokoku_contact_faq_a_{$i}", $default_faqs[$i]['a'] );
        if ( '' !== trim( $q ) && '' !== trim( $a ) ) {
            $faqs[] = array( 'q' => $q, 'a' => $a );
        }
    }

    return array(
        'hero_badge'      => tokoku_get_theme_mod_fallback( 'tokoku_contact_hero_badge', __( 'Layanan Pelanggan', 'tokoku' ) ),
        'hero_title'      => tokoku_get_theme_mod_fallback( 'tokoku_contact_hero_title', __( 'Mari Bicarakan <span class="jp-text-gradient">Penghargaan Terbaik Anda</span>', 'tokoku' ) ),
        'hero_desc'       => tokoku_get_theme_mod_fallback( 'tokoku_contact_hero_desc', sprintf( __( 'Punya pertanyaan, butuh konsultasi desain gratis, atau ingin meminta penawaran harga untuk instansi? Tim Customer Care %s siap mendengarkan dan mendampingi Anda dengan ramah, cepat, dan profesional.', 'tokoku' ), '<strong>' . esc_html( $brand ) . '</strong>' ) ),
        'chip_1'          => tokoku_get_theme_mod_fallback( 'tokoku_contact_chip_1', __( 'Respons Cepat WhatsApp', 'tokoku' ) ),
        'chip_2'          => tokoku_get_theme_mod_fallback( 'tokoku_contact_chip_2', __( 'Konsultasi & Mockup Gratis', 'tokoku' ) ),
        'chip_3'          => tokoku_get_theme_mod_fallback( 'tokoku_contact_chip_3', __( 'Pengiriman Seluruh Indonesia', 'tokoku' ) ),
        'card1_badge'     => tokoku_get_theme_mod_fallback( 'tokoku_contact_card1_badge', __( 'Respons Paling Cepat', 'tokoku' ) ),
        'card1_title'     => tokoku_get_theme_mod_fallback( 'tokoku_contact_card1_title', __( 'WhatsApp Hotline', 'tokoku' ) ),
        'card1_desc'      => tokoku_get_theme_mod_fallback( 'tokoku_contact_card1_desc', __( 'Konsultasi langsung, kirim file logo, dan terima mockup desain visual dalam hitungan menit.', 'tokoku' ) ),
        'card1_btn'       => tokoku_get_theme_mod_fallback( 'tokoku_contact_card1_btn', __( 'Chat WhatsApp Sekarang', 'tokoku' ) ),
        'card2_badge'     => tokoku_get_theme_mod_fallback( 'tokoku_contact_card2_badge', __( 'Instansi & Tender', 'tokoku' ) ),
        'card2_title'     => tokoku_get_theme_mod_fallback( 'tokoku_contact_card2_title', __( 'Email Resmi', 'tokoku' ) ),
        'card2_desc'      => tokoku_get_theme_mod_fallback( 'tokoku_contact_card2_desc', __( 'Kirim berkas penawaran, dokumen tender, PO perusahaan, atau lampiran file desain berukuran besar.', 'tokoku' ) ),
        'card2_btn'       => tokoku_get_theme_mod_fallback( 'tokoku_contact_card2_btn', __( 'Kirim Email', 'tokoku' ) ),
        'card3_badge'     => tokoku_get_theme_mod_fallback( 'tokoku_contact_card3_badge', __( 'Pelayanan Prima', 'tokoku' ) ),
        'card3_title'     => tokoku_get_theme_mod_fallback( 'tokoku_contact_card3_title', __( 'Jam Operasional', 'tokoku' ) ),
        'card3_desc'      => tokoku_get_theme_mod_fallback( 'tokoku_contact_card3_desc', __( 'Tim kami aktif melayani konsultasi dan proses produksi pada jam kerja berikut:', 'tokoku' ) ),
        'card3_note'      => tokoku_get_theme_mod_fallback( 'tokoku_contact_card3_note', __( 'Pesan di luar jam kerja tetap dapat dikirim dan akan dibalas pada kesempatan pertama.', 'tokoku' ) ),
        'form_eyebrow'    => tokoku_get_theme_mod_fallback( 'tokoku_contact_form_eyebrow', __( 'Formulir Interaktif', 'tokoku' ) ),
        'form_title'      => tokoku_get_theme_mod_fallback( 'tokoku_contact_form_title', __( 'Kirim Rincian Kebutuhan Anda', 'tokoku' ) ),
        'form_sub'        => tokoku_get_theme_mod_fallback( 'tokoku_contact_form_sub', __( 'Isi formulir ringkas di bawah. Sistem kami akan merangkumnya secara otomatis menjadi format pesan rapi yang langsung terkirim ke WhatsApp Customer Service kami.', 'tokoku' ) ),
        'form_btn'        => tokoku_get_theme_mod_fallback( 'tokoku_contact_form_btn', __( 'Kirim ke WhatsApp Customer Service', 'tokoku' ) ),
        'form_disclaimer' => tokoku_get_theme_mod_fallback( 'tokoku_contact_form_disclaimer', __( 'Informasi Anda aman dan hanya digunakan untuk keperluan konsultasi serta proses pesanan plakat.', 'tokoku' ) ),
        'faq_eyebrow'     => tokoku_get_theme_mod_fallback( 'tokoku_contact_faq_eyebrow', __( 'Pertanyaan Sering Diajukan', 'tokoku' ) ),
        'faq_title'       => tokoku_get_theme_mod_fallback( 'tokoku_contact_faq_title', __( 'Hal yang Sering Ditanyakan Sebelum Memesan', 'tokoku' ) ),
        'faq_sub'         => tokoku_get_theme_mod_fallback( 'tokoku_contact_faq_sub', sprintf( __( 'Jawaban cepat untuk pertanyaan paling umum seputar pemesanan plakat di %s.', 'tokoku' ), $brand ) ),
        'faqs'            => $faqs,
        'cta_title'       => tokoku_get_theme_mod_fallback( 'tokoku_contact_cta_title', __( 'Sudah Punya Tanggal Acara?', 'tokoku' ) ),
        'cta_sub'         => tokoku_get_theme_mod_fallback( 'tokoku_contact_cta_sub', __( 'Jangan tunda hingga hari mendekati acara. Hubungi kami sekarang untuk memastikan antrean produksi Anda siap tepat pada waktunya.', 'tokoku' ) ),
        'cta_btn1_text'   => tokoku_get_theme_mod_fallback( 'tokoku_contact_cta_btn1_text', __( 'Hubungi via WhatsApp', 'tokoku' ) ),
        'cta_btn2_text'   => tokoku_get_theme_mod_fallback( 'tokoku_contact_cta_btn2_text', __( 'Pelajari Profil Kami', 'tokoku' ) ),
    );
}

