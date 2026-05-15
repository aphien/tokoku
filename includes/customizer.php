<?php
/**
 * Theme Customizer Settings
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registrasi Pengaturan Customizer
 * Menambahkan panel, seksi (section), dan pengaturan (setting) ke dalam fitur
 * Appearance > Customize di WordPress.
 */
function tokoku_customize_register( $wp_customize ) {

    // ─── Panel: Pengaturan Toko ───
    $wp_customize->add_panel( 'tokoku_panel', array(
        'title'       => __( 'Pengaturan Toko', 'tokoku' ),
        'description' => __( 'Konfigurasi tema toko online TokoKu', 'tokoku' ),
        'priority'    => 30,
    ) );

    // ═══ Section: Identitas & Logo ═══
    $wp_customize->add_section( 'tokoku_branding', array(
        'title'    => __( 'Identitas & Logo', 'tokoku' ),
        'panel'    => 'tokoku_panel',
        'priority' => 5,
    ) );

    // Info: Logo can be managed in Site Identity
    $wp_customize->add_setting( 'tokoku_branding_info', array( 'sanitize_callback' => 'sanitize_text_field' ) );
    $wp_customize->add_control( 'tokoku_branding_info', array(
        'label'       => __( 'Tips Logo', 'tokoku' ),
        'description' => __( 'Anda juga dapat mengatur Logo dan Favicon melalui menu "Site Identity" bawaan WordPress.', 'tokoku' ),
        'section'     => 'tokoku_branding',
        'type'        => 'hidden',
    ) );

    // Site Description / Tagline (Custom)
    $wp_customize->add_setting( 'tokoku_site_description', array(
        'default'           => get_bloginfo( 'description' ),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'tokoku_site_description', array(
        'label'       => __( 'Deskripsi Singkat Toko', 'tokoku' ),
        'description' => __( 'Muncul di bawah judul atau di footer.', 'tokoku' ),
        'section'     => 'tokoku_branding',
        'type'        => 'textarea',
    ) );

    // Dark Mode Logo
    $wp_customize->add_setting( 'tokoku_logo_dark', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'tokoku_logo_dark', array(
        'label'       => __( 'Logo Dark Mode (Opsional)', 'tokoku' ),
        'description' => __( 'Gunakan logo versi terang untuk latar belakang gelap.', 'tokoku' ),
        'section'     => 'tokoku_branding',
    ) ) );


    // ═══ Section: WhatsApp ═══
    $wp_customize->add_section( 'tokoku_whatsapp', array(
        'title'    => __( 'WhatsApp', 'tokoku' ),
        'panel'    => 'tokoku_panel',
        'priority' => 10,
    ) );

    // WhatsApp Number
    $wp_customize->add_setting( 'tokoku_wa_number', array(
        'default'           => '6281234567890',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'tokoku_wa_number', array(
        'label'       => __( 'Nomor WhatsApp', 'tokoku' ),
        'description' => __( 'Format internasional tanpa +. Contoh: 6281234567890', 'tokoku' ),
        'section'     => 'tokoku_whatsapp',
        'type'        => 'text',
    ) );

    // WhatsApp Default Message Template
    $wp_customize->add_setting( 'tokoku_wa_message', array(
        'default'           => "Halo, saya ingin memesan:\n\nProduk: {produk}\nHarga: {harga}\nJumlah: {jumlah}\n\nNama: {nama}\nCatatan: {catatan}\n\nTerima kasih!",
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'tokoku_wa_message', array(
        'label'       => __( 'Template Pesan WhatsApp', 'tokoku' ),
        'description' => __( 'Placeholder: {produk}, {harga}, {jumlah}, {nama}, {catatan}', 'tokoku' ),
        'section'     => 'tokoku_whatsapp',
        'type'        => 'textarea',
    ) );

    // WhatsApp Floating Button Text
    $wp_customize->add_setting( 'tokoku_wa_float_text', array(
        'default'           => __( 'Chat dengan kami', 'tokoku' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tokoku_wa_float_text', array(
        'label'   => __( 'Teks Tombol WhatsApp Floating', 'tokoku' ),
        'section' => 'tokoku_whatsapp',
        'type'    => 'text',
    ) );

    // ═══ Section: Tampilan ═══
    $wp_customize->add_section( 'tokoku_appearance', array(
        'title'    => __( 'Tampilan', 'tokoku' ),
        'panel'    => 'tokoku_panel',
        'priority' => 20,
    ) );

    // Primary Color (Blue)
    $wp_customize->add_setting( 'tokoku_primary_color', array(
        'default'           => '#007bff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tokoku_primary_color', array(
        'label'   => __( 'Warna Utama (Primary)', 'tokoku' ),
        'section' => 'tokoku_appearance',
    ) ) );

    // Secondary Color (for Gradient)
    $wp_customize->add_setting( 'tokoku_secondary_color', array(
        'default'           => '#0056b3',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tokoku_secondary_color', array(
        'label'   => __( 'Warna Gradasi (Secondary)', 'tokoku' ),
        'section' => 'tokoku_appearance',
    ) ) );

    // Accent Color
    $wp_customize->add_setting( 'tokoku_accent_color', array(
        'default'           => '#6c5ce7',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tokoku_accent_color', array(
        'label'   => __( 'Warna Aksen', 'tokoku' ),
        'section' => 'tokoku_appearance',
    ) ) );

    // Default Theme Mode
    $wp_customize->add_setting( 'tokoku_default_mode', array(
        'default'           => 'dark',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tokoku_default_mode', array(
        'label'   => __( 'Mode Default', 'tokoku' ),
        'section' => 'tokoku_appearance',
        'type'    => 'radio',
        'choices' => array(
            'dark'  => __( 'Dark Mode', 'tokoku' ),
            'light' => __( 'Light Mode', 'tokoku' ),
        ),
    ) );

    // Show Price Toggle
    $wp_customize->add_setting( 'tokoku_show_price', array(
        'default'           => 'yes',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tokoku_show_price', array(
        'label'   => __( 'Tampilkan Harga Produk', 'tokoku' ),
        'section' => 'tokoku_appearance',
        'type'    => 'radio',
        'choices' => array(
            'yes' => __( 'Aktif', 'tokoku' ),
            'no'  => __( 'Nonaktif', 'tokoku' ),
        ),
    ) );

    // Currency
    $wp_customize->add_setting( 'tokoku_currency', array(
        'default'           => 'Rp',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tokoku_currency', array(
        'label'   => __( 'Simbol Mata Uang', 'tokoku' ),
        'section' => 'tokoku_appearance',
        'type'    => 'text',
    ) );

    // ═══ Section: Banner Slider ═══
    $wp_customize->add_section( 'tokoku_slider', array(
        'title'    => __( 'Banner Slider', 'tokoku' ),
        'panel'    => 'tokoku_panel',
        'priority' => 30,
        'description' => __( 'Unggah hingga 10 gambar banner untuk slider di halaman depan. Ukuran rekomendasi: 1200x500px.', 'tokoku' ),
    ) );

    for ( $i = 1; $i <= 10; $i++ ) {
        // Slider Image
        $wp_customize->add_setting( "tokoku_slide_image_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "tokoku_slide_image_{$i}", array(
            'label'   => sprintf( __( 'Gambar Slide %d', 'tokoku' ), $i ),
            'section' => 'tokoku_slider',
        ) ) );

        // Slider Link
        $wp_customize->add_setting( "tokoku_slide_link_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( "tokoku_slide_link_{$i}", array(
            'label'   => sprintf( __( 'Tautan/Link Slide %d (Opsional)', 'tokoku' ), $i ),
            'section' => 'tokoku_slider',
            'type'    => 'url',
        ) );
    }

    // ═══ Section: Social Media ═══
    $wp_customize->add_section( 'tokoku_social', array(
        'title'    => __( 'Social Media', 'tokoku' ),
        'panel'    => 'tokoku_panel',
        'priority' => 40,
    ) );

    $socials = array(
        'instagram' => 'Instagram',
        'facebook'  => 'Facebook',
        'tiktok'    => 'TikTok',
        'youtube'   => 'YouTube',
        'twitter'   => 'Twitter / X',
    );

    foreach ( $socials as $key => $label ) {
        $wp_customize->add_setting( "tokoku_social_{$key}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( "tokoku_social_{$key}", array(
            'label'   => $label,
            'section' => 'tokoku_social',
            'type'    => 'url',
        ) );
    }

    // ═══ Section: Footer ═══
    $wp_customize->add_section( 'tokoku_footer', array(
        'title'    => __( 'Footer', 'tokoku' ),
        'panel'    => 'tokoku_panel',
        'priority' => 50,
    ) );

    // Footer About Text
    $wp_customize->add_setting( 'tokoku_footer_about', array(
        'default'           => __( 'TokoKu adalah toko online terpercaya yang menyediakan produk berkualitas dengan harga terbaik.', 'tokoku' ),
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'tokoku_footer_about', array(
        'label'   => __( 'Tentang Toko (Footer)', 'tokoku' ),
        'section' => 'tokoku_footer',
        'type'    => 'textarea',
    ) );

    // Footer Copyright
    $wp_customize->add_setting( 'tokoku_footer_copyright', array(
        'default'           => '© {year} TokoKu. All rights reserved.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tokoku_footer_copyright', array(
        'label'       => __( 'Teks Copyright', 'tokoku' ),
        'description' => __( 'Gunakan {year} untuk tahun otomatis', 'tokoku' ),
        'section'     => 'tokoku_footer',
        'type'        => 'text',
    ) );

    // Store Address
    $wp_customize->add_setting( 'tokoku_store_address', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'tokoku_store_address', array(
        'label'   => __( 'Alamat Toko', 'tokoku' ),
        'section' => 'tokoku_footer',
        'type'    => 'textarea',
    ) );

    // Store Email
    $wp_customize->add_setting( 'tokoku_store_email', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_email',
    ) );
    $wp_customize->add_control( 'tokoku_store_email', array(
        'label'   => __( 'Email Toko', 'tokoku' ),
        'section' => 'tokoku_footer',
        'type'    => 'email',
    ) );

    // ═══ Section: SEO Settings ═══
    $wp_customize->add_section( 'tokoku_seo', array(
        'title'       => __( 'SEO & Metadata', 'tokoku' ),
        'panel'       => 'tokoku_panel',
        'priority'    => 60,
        'description' => __( 'Pengaturan optimasi mesin pencari (Google, Bing, dll)', 'tokoku' ),
    ) );

    // Meta Description
    $wp_customize->add_setting( 'tokoku_seo_desc', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'tokoku_seo_desc', array(
        'label'       => __( 'Meta Description (Home)', 'tokoku' ),
        'description' => __( 'Deskripsi singkat yang muncul di hasil pencarian Google.', 'tokoku' ),
        'section'     => 'tokoku_seo',
        'type'        => 'textarea',
    ) );

    // Meta Keywords
    $wp_customize->add_setting( 'tokoku_seo_keywords', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tokoku_seo_keywords', array(
        'label'       => __( 'Meta Keywords', 'tokoku' ),
        'description' => __( 'Pisahkan dengan koma (contoh: plakat, trophy, kado custom)', 'tokoku' ),
        'section'     => 'tokoku_seo',
        'type'        => 'text',
    ) );

    // Social Share Image
    $wp_customize->add_setting( 'tokoku_seo_og_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'tokoku_seo_og_image', array(
        'label'       => __( 'Social Share Image (OG)', 'tokoku' ),
        'description' => __( 'Gambar yang muncul saat link website dibagikan di WA/FB/IG. Ukuran rekomendasi: 1200x630px.', 'tokoku' ),
        'section'     => 'tokoku_seo',
    ) ) );

}
add_action( 'customize_register', 'tokoku_customize_register' );

/**
 * Menghasilkan CSS Kustom dari Pengaturan Customizer
 * Mengambil nilai warna dari customizer dan mencetaknya sebagai variabel CSS root (--primary, dll)
 * di dalam tag <style> pada elemen <head>.
 */
function tokoku_customizer_css() {
    $primary   = get_theme_mod( 'tokoku_primary_color', '#007bff' );
    $secondary = get_theme_mod( 'tokoku_secondary_color', '#0056b3' );
    $accent    = get_theme_mod( 'tokoku_accent_color', '#6c5ce7' );
    
    // Convert hex to RGB for rgba usage
    $r_p = hexdec( substr( $primary, 1, 2 ) );
    $g_p = hexdec( substr( $primary, 3, 2 ) );
    $b_p = hexdec( substr( $primary, 5, 2 ) );
    
    ?>
    <style id="tokoku-customizer-css">
        :root {
            --primary: <?php echo esc_attr( $primary ); ?>;
            --secondary: <?php echo esc_attr( $secondary ); ?>;
            --primary-rgb: <?php echo esc_attr( "{$r_p}, {$g_p}, {$b_p}" ); ?>;
            --accent: <?php echo esc_attr( $accent ); ?>;
            --gradient: linear-gradient(135deg, <?php echo esc_attr( $primary ); ?> 0%, <?php echo esc_attr( $secondary ); ?> 100%);
        }
    </style>
    <?php
}
add_action( 'wp_head', 'tokoku_customizer_css' );
