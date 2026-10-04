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
 * Nama brand yang ditampilkan pada halaman statis.
 *
 * @return string
 */
function tokoku_page_brand() {
    return (string) apply_filters( 'tokoku_page_brand', 'JualPlakat.com' );
}

/**
 * Data kontak toko terpusat (diambil dari pengaturan tema).
 *
 * @return array
 */
function tokoku_page_contact() {
    $wa_raw = (string) get_theme_mod( 'tokoku_wa_number', '6281234567890' );
    $wa     = preg_replace( '/\D+/', '', $wa_raw );
    if ( 0 === strpos( $wa, '0' ) ) {
        $wa = '62' . substr( $wa, 1 );
    }

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

    return array(
        'wa'         => $wa,
        'wa_display' => $display,
        'email'      => sanitize_email( (string) get_theme_mod( 'tokoku_store_email', '' ) ),
        'phone'      => sanitize_text_field( (string) get_theme_mod( 'tokoku_store_phone', '' ) ),
        'maps_url'   => esc_url_raw( (string) get_theme_mod( 'tokoku_store_maps_url', '' ) ),
        'address'    => trim( wp_strip_all_tags( (string) get_theme_mod( 'tokoku_store_address', '' ) ) ),
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
