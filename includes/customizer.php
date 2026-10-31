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
        'default'           => "Halo Admin,\n\nSaya ingin memesan produk berikut:\n\n👤 *Nama Pemesan :* {nama}\n📦 *Produk :* {produk}\n🏷️ *SKU :* {sku}\n📝 *Catatan :* {catatan}\n🔗 *Link Produk :* {link}\n\nMohon segera diproses. Terima kasih!",
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'tokoku_wa_message', array(
        'label'       => __( 'Template Pesan WhatsApp', 'tokoku' ),
        'description' => __( 'Placeholder: {nama}, {produk}, {sku}, {catatan}, {link}, {harga}', 'tokoku' ),
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

        // Slider Alt Text (SEO)
        $wp_customize->add_setting( "tokoku_slide_alt_{$i}", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "tokoku_slide_alt_{$i}", array(
            'label'   => sprintf( __( 'Teks Alt Slide %d (SEO)', 'tokoku' ), $i ),
            'section' => 'tokoku_slider',
            'type'    => 'text',
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
 * Helper function: Sesuaikan kecerahan warna hex untuk hover/active state
 */
if ( ! function_exists( 'tokoku_adjust_color_brightness' ) ) {
    function tokoku_adjust_color_brightness( $hex, $steps ) {
        $hex = ltrim( $hex, '#' );
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if ( strlen( $hex ) !== 6 ) {
            return '#' . $hex;
        }
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        $r = max( 0, min( 255, (int) ( $r + $steps ) ) );
        $g = max( 0, min( 255, (int) ( $g + $steps ) ) );
        $b = max( 0, min( 255, (int) ( $b + $steps ) ) );
        return '#' . sprintf( '%02x%02x%02x', $r, $g, $b );
    }
}

/**
 * Menghasilkan CSS Kustom dari Pengaturan Customizer
 * Mengambil nilai warna dari customizer dan mencetaknya sebagai variabel CSS root (--primary, dll)
 * serta mengaplikasikan efek gradiasi elegan pada seluruh tombol situs.
 */
function tokoku_customizer_css() {
    $primary   = get_theme_mod( 'tokoku_primary_color', '#007bff' );
    $secondary = get_theme_mod( 'tokoku_secondary_color', '#0056b3' );
    $accent    = get_theme_mod( 'tokoku_accent_color', '#6c5ce7' );
    
    $primary_hover = tokoku_adjust_color_brightness( $primary, 18 );
    
    // Additional Colors from Admin Page
    $header_bg   = get_theme_mod( 'tokoku_header_bg', '#ffffff' );
    $header_text = get_theme_mod( 'tokoku_header_text', '#0f172a' );
    $footer_bg   = get_theme_mod( 'tokoku_footer_bg', '#f1f5f9' );
    $footer_text = get_theme_mod( 'tokoku_footer_text', '#475569' );
    $card_bg     = get_theme_mod( 'tokoku_card_bg', '#ffffff' );
    $card_text   = get_theme_mod( 'tokoku_card_text', '#0f172a' );
    $price_color = get_theme_mod( 'tokoku_price_color', '#007bff' );
    
    // Dark Mode Customizations
    $dark_bg    = get_theme_mod( 'tokoku_dark_bg', '#0b0f1a' );
    $dark_bg2   = get_theme_mod( 'tokoku_dark_bg2', '#151b2d' );
    $dark_text  = get_theme_mod( 'tokoku_dark_text', '#f1f5f9' );
    
    // Convert hex to RGB for rgba usage
    $clean_primary = ltrim( $primary, '#' );
    if ( strlen( $clean_primary ) === 3 ) {
        $clean_primary = $clean_primary[0] . $clean_primary[0] . $clean_primary[1] . $clean_primary[1] . $clean_primary[2] . $clean_primary[2];
    }
    $r_p = hexdec( substr( $clean_primary, 0, 2 ) );
    $g_p = hexdec( substr( $clean_primary, 2, 2 ) );
    $b_p = hexdec( substr( $clean_primary, 4, 2 ) );
    $primary_rgb = "{$r_p}, {$g_p}, {$b_p}";
    
    ?>
    <style id="tokoku-customizer-css">
        :root,
        html.theme-light,
        body.theme-light {
            --primary: <?php echo esc_attr( $primary ); ?>;
            --primary-dark: <?php echo esc_attr( $secondary ); ?>;
            --secondary: <?php echo esc_attr( $secondary ); ?>;
            --primary-rgb: <?php echo esc_attr( $primary_rgb ); ?>;
            --accent: <?php echo esc_attr( $accent ); ?>;
            --gradient: linear-gradient(135deg, <?php echo esc_attr( $primary ); ?> 0%, <?php echo esc_attr( $secondary ); ?> 100%);
            --gradient-hover: linear-gradient(135deg, <?php echo esc_attr( $primary_hover ); ?> 0%, <?php echo esc_attr( $primary ); ?> 100%);
            --gradient-primary: linear-gradient(135deg, <?php echo esc_attr( $primary ); ?> 0%, <?php echo esc_attr( $secondary ); ?> 100%);
            --gradient-primary-hover: linear-gradient(135deg, <?php echo esc_attr( $primary_hover ); ?> 0%, <?php echo esc_attr( $primary ); ?> 100%);
            --gradient-wa: linear-gradient(135deg, #25D366 0%, #1ebe5d 38%, #128C7E 100%);
            --gradient-wa-hover: linear-gradient(135deg, #34e77a 0%, #25D366 45%, #149c8c 100%);
            --wa-rgb: 37, 211, 102;
            
            --bg: #ffffff;
            --bg2: #f8fafc;
            --text: #0f172a;
            --text2: #64748b;
            --border: #e2e8f0;
            --glass: rgba(255, 255, 255, 0.92);
            --glass-border: rgba(226, 232, 240, 0.85);
            --glass-blur: blur(16px);
            
            --header-bg: <?php echo esc_attr( $header_bg ); ?>;
            --header-text: <?php echo esc_attr( $header_text ); ?>;
            --footer-bg: <?php echo esc_attr( $footer_bg ); ?>;
            --footer-text: <?php echo esc_attr( $footer_text ); ?>;
            --card-bg: <?php echo esc_attr( $card_bg ); ?>;
            --card-text: <?php echo esc_attr( $card_text ); ?>;
            --price-color: <?php echo esc_attr( $price_color ); ?>;
            
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 16px 40px rgba(0, 0, 0, 0.08);
            --orange: #f59e0b;
        }
        
        :root.theme-dark,
        html.theme-dark,
        body.theme-dark,
        .theme-dark {
            --primary: <?php echo esc_attr( $primary ); ?>;
            --primary-dark: <?php echo esc_attr( $secondary ); ?>;
            --secondary: <?php echo esc_attr( $secondary ); ?>;
            --primary-rgb: <?php echo esc_attr( $primary_rgb ); ?>;
            --accent: <?php echo esc_attr( $accent ); ?>;
            --gradient: linear-gradient(135deg, <?php echo esc_attr( $primary ); ?> 0%, <?php echo esc_attr( $secondary ); ?> 100%);
            --gradient-hover: linear-gradient(135deg, <?php echo esc_attr( $primary_hover ); ?> 0%, <?php echo esc_attr( $primary ); ?> 100%);
            --gradient-primary: linear-gradient(135deg, <?php echo esc_attr( $primary ); ?> 0%, <?php echo esc_attr( $secondary ); ?> 100%);
            --gradient-primary-hover: linear-gradient(135deg, <?php echo esc_attr( $primary_hover ); ?> 0%, <?php echo esc_attr( $primary ); ?> 100%);
            --gradient-wa: linear-gradient(135deg, #25D366 0%, #1ebe5d 38%, #128C7E 100%);
            --gradient-wa-hover: linear-gradient(135deg, #34e77a 0%, #25D366 45%, #149c8c 100%);
            --wa-rgb: 37, 211, 102;
            
            --bg: <?php echo esc_attr( $dark_bg ); ?>;
            --bg2: <?php echo esc_attr( $dark_bg2 ); ?>;
            --text: <?php echo esc_attr( $dark_text ); ?>;
            --text2: #94a3b8;
            --text3: #64748b;
            --border: rgba(255, 255, 255, 0.09);
            --glass: rgba(15, 23, 42, 0.92);
            --glass-border: rgba(255, 255, 255, 0.09);
            --glass-blur: blur(16px);
            
            --header-bg: <?php echo in_array( strtolower( $dark_bg ), array( '#0b0f1a', '#0f172a' ), true ) ? 'rgba(11, 15, 26, 0.90)' : esc_attr( $dark_bg ); ?>;
            --header-text: <?php echo esc_attr( $dark_text ); ?>;
            --footer-bg: <?php echo esc_attr( $dark_bg2 ); ?>;
            --footer-text: #94a3b8;
            --card-bg: #141b2d;
            --card-text: <?php echo esc_attr( $dark_text ); ?>;
            --price-color: <?php echo ( strtolower( $price_color ) === '#007bff' ) ? '#60a5fa' : esc_attr( $price_color ); ?>;
            
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.35);
            --shadow-lg: 0 16px 40px rgba(0, 0, 0, 0.45);
            --orange: #f59e0b;
        }

        /* ==========================================================================
           ELEGANT THEME ACCENT GRADIENT BUTTONS
           Menyesuaikan seluruh tombol situs dengan aksen tema aktif di pengaturan
           ========================================================================== */
        .btn-primary,
        .btn-whatsapp-order,
        .btn-sticky-order-elegant,
        .btn-wa-submit,
        .btn-contact-us,
        .btn-consult-wa,
        .jp-btn--primary,
        .jp-btn--wa,
        #jp-wa-submit-btn,
        .btn-submit-comment,
        #commentform #submit,
        .comment-form .submit,
        .wp-block-button__link,
        .search-submit,
        .btn.btn-primary {
            background: linear-gradient(135deg, <?php echo esc_attr( $primary ); ?> 0%, <?php echo esc_attr( $secondary ); ?> 100%) !important;
            background-image: linear-gradient(135deg, <?php echo esc_attr( $primary ); ?> 0%, <?php echo esc_attr( $secondary ); ?> 100%) !important;
            color: #ffffff !important;
            border-color: transparent !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28), 0 4px 16px rgba(<?php echo esc_attr( $primary_rgb ); ?>, 0.35) !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.18) !important;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), background 0.25s ease !important;
        }

        .btn-primary:hover,
        .btn-whatsapp-order:hover,
        .btn-sticky-order-elegant:hover,
        .btn-wa-submit:hover,
        .btn-contact-us:hover,
        .btn-consult-wa:hover,
        .jp-btn--primary:hover,
        .jp-btn--wa:hover,
        #jp-wa-submit-btn:hover,
        .btn-submit-comment:hover,
        #commentform #submit:hover,
        .comment-form .submit:hover,
        .wp-block-button__link:hover,
        .search-submit:hover,
        .btn.btn-primary:hover {
            background: linear-gradient(135deg, <?php echo esc_attr( $primary_hover ); ?> 0%, <?php echo esc_attr( $primary ); ?> 100%) !important;
            background-image: linear-gradient(135deg, <?php echo esc_attr( $primary_hover ); ?> 0%, <?php echo esc_attr( $primary ); ?> 100%) !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4), 0 8px 24px rgba(<?php echo esc_attr( $primary_rgb ); ?>, 0.48) !important;
        }

        .btn-primary:active,
        .btn-whatsapp-order:active,
        .btn-sticky-order-elegant:active,
        .btn-wa-submit:active,
        .btn-contact-us:active,
        .btn-consult-wa:active,
        .jp-btn--primary:active,
        .jp-btn--wa:active,
        #jp-wa-submit-btn:active,
        .btn-submit-comment:active,
        #commentform #submit:active,
        .comment-form .submit:active,
        .wp-block-button__link:active,
        .search-submit:active,
        .btn.btn-primary:active {
            transform: translateY(0) scale(0.98) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 2px 8px rgba(<?php echo esc_attr( $primary_rgb ); ?>, 0.35) !important;
        }

        /* ==========================================================================
           KHUSUS WA-FLOAT: Hijau WhatsApp Elegan (Official Emerald Gradient)
           ========================================================================== */
        .wa-float__btn,
        .wa-float .wa-float__btn,
        a.wa-float__btn {
            background: linear-gradient(135deg, #25D366 0%, #1ebe5d 38%, #128C7E 100%) !important;
            background-image: linear-gradient(135deg, #25D366 0%, #1ebe5d 38%, #128C7E 100%) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45), 0 8px 24px rgba(37, 211, 102, 0.42) !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15) !important;
        }

        .wa-float__btn:hover,
        .wa-float .wa-float__btn:hover,
        a.wa-float__btn:hover {
            background: linear-gradient(135deg, #34e77a 0%, #25D366 45%, #149c8c 100%) !important;
            background-image: linear-gradient(135deg, #34e77a 0%, #25D366 45%, #149c8c 100%) !important;
            color: #ffffff !important;
            transform: translateY(-3px) scale(1.08) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), 0 14px 32px rgba(37, 211, 102, 0.55), 0 0 0 5px rgba(37, 211, 102, 0.2) !important;
        }

        .wa-float__btn:active,
        .wa-float .wa-float__btn:active,
        a.wa-float__btn:active {
            background: linear-gradient(135deg, #1ebe5d 0%, #128C7E 100%) !important;
            transform: scale(0.95) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25), 0 4px 14px rgba(37, 211, 102, 0.35) !important;
        }

        .wa-float__btn::before {
            border-color: rgba(37, 211, 102, 0.65) !important;
        }

        .wa-float__btn svg {
            fill: #ffffff !important;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'tokoku_customizer_css' );
