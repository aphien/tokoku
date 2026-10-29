<?php
/**
 * TokoKu Dashboard Page
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Mendaftarkan Menu Admin
 * Membuat menu "Tokoku" di sidebar kiri dashboard WordPress.
 */
function tokoku_admin_menu() {
    $page_title = __( 'Tokoku by M.alfiandi Ismet', 'tokoku' );
    $menu_title = 'Tokoku';
    $capability = 'manage_options';
    $menu_slug  = 'tokoku-settings';
    $callback   = 'tokoku_settings_page_html';
    $icon_url   = 'dashicons-store';
    $position   = 2;

    $hook = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position );
    
    // Sub-menu Navigasi Cepat di Dasbor Admin WordPress (14 Panel Terstruktur)
    add_submenu_page( $menu_slug, __( 'Pengaturan Umum', 'tokoku' ), __( 'Pengaturan Umum', 'tokoku' ), $capability, 'tokoku-settings', $callback );
    add_submenu_page( $menu_slug, __( 'WhatsApp & CS', 'tokoku' ), __( 'WhatsApp & CS', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-whatsapp', $callback );
    add_submenu_page( $menu_slug, __( 'Warna & Tampilan', 'tokoku' ), __( 'Warna & Tampilan', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-appearance', $callback );
    add_submenu_page( $menu_slug, __( 'Template Tentang Kami', 'tokoku' ), __( 'Tentang Kami', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-page-about', $callback );
    add_submenu_page( $menu_slug, __( 'Template Kontak & Workshop', 'tokoku' ), __( 'Kontak Kami', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-contact', $callback );
    add_submenu_page( $menu_slug, __( 'Halaman Produk', 'tokoku' ), __( 'Halaman Produk', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-single-product', $callback );
    add_submenu_page( $menu_slug, __( 'Banner Slider', 'tokoku' ), __( 'Banner Slider', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-slider', $callback );
    add_submenu_page( $menu_slug, __( 'Ulasan & Klien', 'tokoku' ), __( 'Ulasan & Klien', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-testimonials', $callback );
    add_submenu_page( $menu_slug, __( 'Tanya Jawab (FAQ)', 'tokoku' ), __( 'Tanya Jawab (FAQ)', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-faq', $callback );
    add_submenu_page( $menu_slug, __( 'SEO & Metadata', 'tokoku' ), __( 'SEO & Metadata', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-seo', $callback );
    add_submenu_page( $menu_slug, __( 'Footer Website', 'tokoku' ), __( 'Footer Website', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-footer', $callback );
    add_submenu_page( $menu_slug, __( 'Media Sosial', 'tokoku' ), __( 'Media Sosial', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-social', $callback );
    add_submenu_page( $menu_slug, __( 'Pembaruan Tema', 'tokoku' ), __( 'Pembaruan Tema', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-update', $callback );
    add_submenu_page( $menu_slug, __( 'Cadangan & Impor', 'tokoku' ), __( 'Cadangan & Impor', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-import-export', $callback );

    // Enqueue scripts for our settings page
    add_action( 'admin_print_scripts-' . $hook, 'tokoku_admin_settings_assets' );
}
add_action( 'admin_menu', 'tokoku_admin_menu' );

/**
 * Menyorot (highlight) submenu admin WordPress yang aktif sesuai parameter ?tab=...
 *
 * @param string $submenu_file Slug submenu saat ini.
 * @return string
 */
function tokoku_admin_submenu_filter( $submenu_file ) {
    global $plugin_page;
    if ( 'tokoku-settings' === $plugin_page && ! empty( $_GET['tab'] ) ) {
        $tab = sanitize_key( $_GET['tab'] );
        if ( 'tab-general' !== $tab ) {
            return 'tokoku-settings&tab=' . $tab;
        }
    }
    return $submenu_file;
}
add_filter( 'submenu_file', 'tokoku_admin_submenu_filter' );

/**
 * Memuat Aset (CSS/JS) untuk Halaman Pengaturan
 * Hanya memuat script seperti Color Picker dan Drag-and-Drop (Sortable)
 * pada halaman pengaturan TokoKu untuk menghemat resource.
 */
function tokoku_admin_settings_assets() {
    wp_enqueue_media();
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'jquery-ui-sortable' );



    // External CSS & JS
    wp_enqueue_style( 'tokoku-font-awesome', TOKOKU_URI . '/assets/vendor/fontawesome/css/all.min.css', array(), '6.7.2' );
    wp_enqueue_style( 'tokoku-admin-css', TOKOKU_URI . '/assets/css/admin.css', array( 'tokoku-font-awesome' ), TOKOKU_VERSION );
    wp_enqueue_script( 'tokoku-admin-js', TOKOKU_URI . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), TOKOKU_VERSION, true );

    // Localize data for AJAX
    wp_localize_script( 'tokoku-admin-js', 'tokokuAdmin', array(
        'updateNonce'  => wp_create_nonce( 'tokoku_update_nonce' ),
        'version'      => TOKOKU_VERSION,
    ) );
}

/**
 * Ekspor Pengaturan Tema ke Format JSON
 * Memungkinkan admin untuk mengunduh (backup) semua pengaturan tema (warna, teks, dll)
 * ke dalam file JSON yang bisa disimpan di komputer lokal.
 */
function tokoku_export_settings() {
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'tokoku_export_action' ) ) {
        wp_die( 'Security check failed.' );
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Permission denied.' );
    }

    $theme_mods = get_theme_mods();
    $export_data = array(
        'source'     => 'Tokoku Theme',
        'version'    => TOKOKU_VERSION,
        'timestamp'  => time(),
        'theme_mods' => $theme_mods,
    );

    $json_data = json_encode( $export_data, JSON_PRETTY_PRINT );
    $filename  = 'tokoku-settings-backup-' . date('Y-m-d') . '.json';

    if ( ob_get_level() ) {
        ob_end_clean();
    }

    header( 'Content-Description: File Transfer' );
    header( 'Content-Type: application/json; charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Expires: 0' );
    header( 'Cache-Control: must-revalidate' );
    header( 'Pragma: public' );
    header( 'Content-Length: ' . strlen( $json_data ) );

    echo $json_data;
    exit;
}
add_action( 'admin_post_tokoku_export_settings', 'tokoku_export_settings' );

/**
 * Menyimpan Pengaturan dari Halaman Admin
 * Ini adalah fungsi inti untuk memproses form pengaturan. Dilengkapi dengan
 * verifikasi keamanan tingkat tinggi (Nonce & Capability check).
 */
function tokoku_save_admin_settings() {
    // 1. Security Check: Nonce Verification
    if ( ! isset( $_POST['tokoku_settings_nonce'] ) || ! wp_verify_nonce( $_POST['tokoku_settings_nonce'], 'tokoku_save_settings_action' ) ) {
        wp_die( esc_html__( 'Security check failed. Please refresh the page and try again.', 'tokoku' ) );
    }

    // 2. Authorization Check: Capability Verification
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to modify these settings.', 'tokoku' ) );
    }

    // 2b. Handle Import Settings Action
    if ( isset( $_POST['tokoku_import_action'] ) && $_POST['tokoku_import_action'] === 'import_settings' ) {
        if ( isset( $_FILES['tokoku_import_file'] ) && $_FILES['tokoku_import_file']['error'] == UPLOAD_ERR_OK ) {
            $file_contents = file_get_contents( $_FILES['tokoku_import_file']['tmp_name'] );
            $import_data = json_decode( $file_contents, true );
            
            if ( is_array( $import_data ) && isset( $import_data['theme_mods'] ) && is_array( $import_data['theme_mods'] ) ) {
                foreach ( $import_data['theme_mods'] as $key => $value ) {
                    set_theme_mod( $key, $value );
                }
                
                $redirect_url = add_query_arg( 
                    array( 
                        'page'             => 'tokoku-settings', 
                        'settings-updated' => 'true' 
                    ), 
                    admin_url( 'admin.php' ) 
                );
                wp_safe_redirect( $redirect_url );
                exit;
            } else {
                wp_die( esc_html__( 'Format file JSON tidak valid.', 'tokoku' ) );
            }
        } else {
            wp_die( esc_html__( 'Gagal mengunggah file. Pastikan Anda memilih file JSON yang valid.', 'tokoku' ) );
        }
    }
    // 3. Define Settings Schema with specific sanitization
    $settings_schema = array(
        // General
        'tokoku_site_description' => 'sanitize_textarea_field',
        'tokoku_logo_light'       => 'esc_url_raw',
        'tokoku_logo_dark'        => 'esc_url_raw',
        'tokoku_wa_number'        => 'sanitize_text_field',
        'tokoku_wa_message'       => 'wp_kses_post',
        'tokoku_wa_float_text'    => 'sanitize_text_field',
        
        // Colors & Theme
        'tokoku_primary_color'    => 'sanitize_hex_color',
        'tokoku_secondary_color'  => 'sanitize_hex_color',
        'tokoku_accent_color'     => 'sanitize_hex_color',
        'tokoku_dark_bg'          => 'sanitize_hex_color',
        'tokoku_dark_bg2'         => 'sanitize_hex_color',
        'tokoku_dark_text'        => 'sanitize_hex_color',
        'tokoku_default_mode'     => 'sanitize_text_field',
        'tokoku_enable_dark_mode' => 'sanitize_text_field',
        'tokoku_header_bg'        => 'sanitize_hex_color',
        'tokoku_header_text'      => 'sanitize_hex_color',
        'tokoku_footer_bg'        => 'sanitize_hex_color',
        'tokoku_footer_text'      => 'sanitize_hex_color',
        'tokoku_card_bg'          => 'sanitize_hex_color',
        'tokoku_card_text'        => 'sanitize_hex_color',
        'tokoku_price_color'      => 'sanitize_hex_color',
        
        // Shop Config
        'tokoku_currency'         => 'sanitize_text_field',
        
        // Footer & Contact & SEO
        'tokoku_footer_copyright' => 'wp_kses_post',
        'tokoku_store_address'    => 'wp_kses_post',
        'tokoku_store_maps_url'   => 'esc_url_raw',
        'tokoku_store_phone'      => 'sanitize_text_field',
        'tokoku_store_email'      => 'sanitize_email',
        'tokoku_hubungi_kami_desc'=> 'sanitize_text_field',
        'tokoku_jam_op_1'         => 'sanitize_text_field',
        'tokoku_jam_op_2'         => 'sanitize_text_field',
        'tokoku_jam_op_3'         => 'sanitize_text_field',
        'tokoku_seo_desc'         => 'sanitize_textarea_field',
        'tokoku_seo_keywords'     => 'sanitize_text_field',
        'tokoku_seo_og_image'     => 'esc_url_raw',
        'tokoku_schema_rating_enable'   => 'sanitize_text_field',
        'tokoku_schema_default_rating'  => 'sanitize_text_field',
        'tokoku_schema_default_reviews' => 'absint',



        // FAQ
        'tokoku_faq_title'        => 'sanitize_text_field',
        'tokoku_faq_subtitle'     => 'sanitize_text_field',

        // Single Product (Gallery Slider, Lead Time Bar, Trust Badges, Stock Notice)
        'tokoku_product_slider_autoplay' => 'sanitize_text_field',
        'tokoku_product_slider_delay'    => 'absint',
        'tokoku_enable_lead_time'        => 'sanitize_text_field',
        'tokoku_lead_time_icon'          => 'sanitize_text_field',
        'tokoku_lead_time_icon_img'      => 'esc_url_raw',
        'tokoku_lead_time_icon_svg'      => 'tokoku_sanitize_svg',
        'tokoku_lead_time_label'         => 'sanitize_text_field',
        'tokoku_lead_time_val'           => 'sanitize_text_field',
        'tokoku_lead_time_sub'           => 'sanitize_text_field',
        'tokoku_lead_time_chip'          => 'sanitize_text_field',

        'tokoku_enable_trust_badges'     => 'sanitize_text_field',
        'tokoku_enable_product_rating'   => 'sanitize_text_field',

        // Stock Notice
        'tokoku_enable_stock_notice'     => 'sanitize_text_field',
        'tokoku_notice_tersedia_title'   => 'sanitize_text_field',
        'tokoku_notice_tersedia_desc'    => 'sanitize_textarea_field',
        'tokoku_notice_habis_title'      => 'sanitize_text_field',
        'tokoku_notice_habis_desc'       => 'sanitize_textarea_field',
        'tokoku_notice_preorder_title'   => 'sanitize_text_field',
        'tokoku_notice_preorder_desc'    => 'sanitize_textarea_field',

        // Informasi & Detail Produk (Hub Deskripsi & Panduan)
        'tokoku_enable_desc_hub'         => 'sanitize_text_field',
        'tokoku_desc_badge_text'         => 'sanitize_text_field',
        'tokoku_desc_section_title'      => 'sanitize_text_field',
        'tokoku_desc_tab1_label'         => 'sanitize_text_field',
        'tokoku_desc_tab3_label'         => 'sanitize_text_field',

        'tokoku_desc_step1_title'        => 'sanitize_text_field',
        'tokoku_desc_step1_desc'         => 'sanitize_textarea_field',
        'tokoku_desc_step2_title'        => 'sanitize_text_field',
        'tokoku_desc_step2_desc'         => 'sanitize_textarea_field',
        'tokoku_desc_step3_title'        => 'sanitize_text_field',
        'tokoku_desc_step3_desc'         => 'sanitize_textarea_field',
        'tokoku_desc_step4_title'        => 'sanitize_text_field',
        'tokoku_desc_step4_desc'         => 'sanitize_textarea_field',

        'tokoku_desc_enable_guarantee'   => 'sanitize_text_field',
        'tokoku_desc_guarantee_title'    => 'sanitize_text_field',
        'tokoku_desc_guarantee_desc'     => 'sanitize_textarea_field',

        // Template: Tentang Kami (About Us)
        'tokoku_about_hero_badge'        => 'sanitize_text_field',
        'tokoku_about_hero_title'        => 'wp_kses_post',
        'tokoku_about_hero_desc'         => 'sanitize_textarea_field',
        'tokoku_about_hero_btn1_text'    => 'sanitize_text_field',
        'tokoku_about_hero_btn1_url'     => 'esc_url_raw',
        'tokoku_about_hero_btn2_text'    => 'sanitize_text_field',
        'tokoku_about_plaque_eyebrow'    => 'sanitize_text_field',
        'tokoku_about_plaque_title'      => 'sanitize_text_field',
        'tokoku_about_plaque_text'       => 'sanitize_textarea_field',

        'tokoku_about_stat1_val'         => 'sanitize_text_field',
        'tokoku_about_stat1_lbl'         => 'sanitize_text_field',
        'tokoku_about_stat2_val'         => 'sanitize_text_field',
        'tokoku_about_stat2_lbl'         => 'sanitize_text_field',
        'tokoku_about_stat3_val'         => 'sanitize_text_field',
        'tokoku_about_stat3_lbl'         => 'sanitize_text_field',
        'tokoku_about_stat4_val'         => 'sanitize_text_field',
        'tokoku_about_stat4_lbl'         => 'sanitize_text_field',

        'tokoku_about_quote_text'        => 'sanitize_textarea_field',
        'tokoku_about_quote_author'      => 'sanitize_text_field',
        'tokoku_about_story_eyebrow'     => 'sanitize_text_field',
        'tokoku_about_story_title'       => 'wp_kses_post',
        'tokoku_about_story_content'     => 'wp_kses_post',

        'tokoku_about_values_eyebrow'    => 'sanitize_text_field',
        'tokoku_about_values_title'      => 'sanitize_text_field',
        'tokoku_about_values_sub'        => 'sanitize_textarea_field',

        'tokoku_about_steps_eyebrow'     => 'sanitize_text_field',
        'tokoku_about_steps_title'       => 'sanitize_text_field',

        'tokoku_about_visi_eyebrow'      => 'sanitize_text_field',
        'tokoku_about_visi_text'         => 'sanitize_textarea_field',
        'tokoku_about_misi_eyebrow'      => 'sanitize_text_field',
        'tokoku_about_misi_points'       => 'sanitize_textarea_field',

        'tokoku_about_clients_eyebrow'   => 'sanitize_text_field',
        'tokoku_about_clients_title'     => 'sanitize_text_field',
        'tokoku_about_clients_sub'       => 'sanitize_textarea_field',
        'tokoku_about_clients_chips'     => 'sanitize_textarea_field',

        'tokoku_about_cta_title'         => 'sanitize_text_field',
        'tokoku_about_cta_sub'           => 'sanitize_textarea_field',
        'tokoku_about_cta_btn1_text'     => 'sanitize_text_field',
        'tokoku_about_cta_btn2_text'     => 'sanitize_text_field',

        // Template: Kontak Kami (Contact Page)
        'tokoku_contact_hero_badge'      => 'sanitize_text_field',
        'tokoku_contact_hero_title'      => 'wp_kses_post',
        'tokoku_contact_hero_desc'       => 'sanitize_textarea_field',
        'tokoku_contact_chip_1'          => 'sanitize_text_field',
        'tokoku_contact_chip_2'          => 'sanitize_text_field',
        'tokoku_contact_chip_3'          => 'sanitize_text_field',

        'tokoku_contact_card1_badge'     => 'sanitize_text_field',
        'tokoku_contact_card1_title'     => 'sanitize_text_field',
        'tokoku_contact_card1_desc'      => 'sanitize_textarea_field',
        'tokoku_contact_card1_btn'       => 'sanitize_text_field',

        'tokoku_contact_card2_badge'     => 'sanitize_text_field',
        'tokoku_contact_card2_title'     => 'sanitize_text_field',
        'tokoku_contact_card2_desc'      => 'sanitize_textarea_field',
        'tokoku_contact_card2_btn'       => 'sanitize_text_field',

        'tokoku_contact_card3_badge'     => 'sanitize_text_field',
        'tokoku_contact_card3_title'     => 'sanitize_text_field',
        'tokoku_contact_card3_desc'      => 'sanitize_textarea_field',
        'tokoku_contact_card3_note'      => 'sanitize_textarea_field',

        'tokoku_contact_form_eyebrow'    => 'sanitize_text_field',
        'tokoku_contact_form_title'      => 'sanitize_text_field',
        'tokoku_contact_form_sub'        => 'sanitize_textarea_field',
        'tokoku_contact_form_btn'        => 'sanitize_text_field',
        'tokoku_contact_form_disclaimer' => 'sanitize_textarea_field',

        'tokoku_contact_faq_eyebrow'     => 'sanitize_text_field',
        'tokoku_contact_faq_title'       => 'sanitize_text_field',
        'tokoku_contact_faq_sub'         => 'sanitize_textarea_field',

        'tokoku_contact_cta_title'       => 'sanitize_text_field',
        'tokoku_contact_cta_sub'         => 'sanitize_textarea_field',
        'tokoku_contact_cta_btn1_text'   => 'sanitize_text_field',
        'tokoku_contact_cta_btn2_text'   => 'sanitize_text_field',

        // Google Maps Reviews & Business Summary
        'tokoku_gmap_section_title'      => 'sanitize_text_field',
        'tokoku_gmap_section_subtitle'   => 'sanitize_text_field',
        'tokoku_gmap_place_photo'        => 'esc_url_raw',
        'tokoku_gmap_place_name'         => 'sanitize_text_field',
        'tokoku_gmap_place_rating'       => 'sanitize_text_field',
        'tokoku_gmap_total_reviews'      => 'absint',
        'tokoku_gmap_review_url'         => 'esc_url_raw',
        'tokoku_gmap_btn_text'           => 'sanitize_text_field',
        'tokoku_gmap_api_key'            => 'sanitize_text_field',
        'tokoku_gmap_place_id'           => 'sanitize_text_field',
    );

    // Trust Badges Repeater
    for ( $i = 1; $i <= 4; $i++ ) {
        $settings_schema["tokoku_trust_badge_title_{$i}"]    = 'sanitize_text_field';
        $settings_schema["tokoku_trust_badge_desc_{$i}"]     = 'sanitize_text_field';
        $settings_schema["tokoku_trust_badge_icon_{$i}"]     = 'sanitize_text_field';
        $settings_schema["tokoku_trust_badge_icon_img_{$i}"] = 'esc_url_raw';
        $settings_schema["tokoku_trust_badge_icon_svg_{$i}"] = 'tokoku_sanitize_svg';
    }

    // FAQ Repeater (Toko Utama)
    for ( $i = 1; $i <= 10; $i++ ) {
        $settings_schema["tokoku_faq_q_{$i}"] = 'sanitize_text_field';
        $settings_schema["tokoku_faq_a_{$i}"] = 'wp_kses_post';
    }

    // Contacts Repeater (Customer Relation Officer / CRO)
    for ( $i = 1; $i <= 5; $i++ ) {
        $settings_schema["tokoku_contact_name_{$i}"] = 'sanitize_text_field';
        $settings_schema["tokoku_contact_wa_{$i}"]   = 'sanitize_text_field';
        $settings_schema["tokoku_contact_role_{$i}"] = 'sanitize_text_field';
    }

    // Template Tentang Kami: Values Repeater (1-6) & Steps Repeater (1-4)
    for ( $i = 1; $i <= 6; $i++ ) {
        $settings_schema["tokoku_about_val_icon_{$i}"]  = 'sanitize_text_field';
        $settings_schema["tokoku_about_val_title_{$i}"] = 'sanitize_text_field';
        $settings_schema["tokoku_about_val_text_{$i}"]  = 'sanitize_textarea_field';
    }
    for ( $i = 1; $i <= 4; $i++ ) {
        $settings_schema["tokoku_about_step_icon_{$i}"]  = 'sanitize_text_field';
        $settings_schema["tokoku_about_step_title_{$i}"] = 'sanitize_text_field';
        $settings_schema["tokoku_about_step_text_{$i}"]  = 'sanitize_textarea_field';
    }

    // Template Kontak: FAQ Repeater (1-5)
    for ( $i = 1; $i <= 5; $i++ ) {
        $settings_schema["tokoku_contact_faq_q_{$i}"] = 'sanitize_text_field';
        $settings_schema["tokoku_contact_faq_a_{$i}"] = 'sanitize_textarea_field';
    }

    // 4. Handle Repeater Settings (Slides, Socials, Testimonials, Logos)
    
    // Banner Slider
    for ( $i = 1; $i <= 10; $i++ ) {
        $settings_schema["tokoku_slide_image_{$i}"] = 'esc_url_raw';
        $settings_schema["tokoku_slide_link_{$i}"]  = 'esc_url_raw';
        $settings_schema["tokoku_slide_alt_{$i}"]   = 'sanitize_text_field';
    }

    // Social Media
    $socials = array( 'instagram', 'facebook', 'tiktok', 'youtube', 'twitter' );
    foreach ( $socials as $social ) {
        $settings_schema["tokoku_social_{$social}"] = 'esc_url_raw';
    }

    // Testimonials / Google Maps Reviews
    for ( $i = 1; $i <= 20; $i++ ) {
        $settings_schema["tokoku_testi_img_{$i}"]         = 'esc_url_raw';
        $settings_schema["tokoku_testi_name_{$i}"]        = 'sanitize_text_field';
        $settings_schema["tokoku_testi_date_{$i}"]        = 'sanitize_text_field';
        $settings_schema["tokoku_testi_text_{$i}"]        = 'sanitize_textarea_field';
        $settings_schema["tokoku_testi_rating_{$i}"]      = 'absint';
        $settings_schema["tokoku_testi_local_guide_{$i}"] = 'absint';
    }

    // Client Logos
    for ( $i = 1; $i <= 50; $i++ ) {
        $settings_schema["tokoku_client_logo_{$i}"] = 'esc_url_raw';
    }

    // 5. Process and Save Settings
    foreach ( $settings_schema as $option_key => $sanitize_callback ) {
        if ( isset( $_POST[$option_key] ) ) {
            $raw_value = $_POST[$option_key];
            
            // Apply sanitization
            if ( is_callable( $sanitize_callback ) ) {
                $safe_value = call_user_func( $sanitize_callback, $raw_value );
            } else {
                $safe_value = sanitize_text_field( $raw_value );
            }
            
            set_theme_mod( $option_key, $safe_value );
        }
    }

    // 6. Handle Core WordPress Options
    if ( isset( $_POST['blogname'] ) ) {
        update_option( 'blogname', sanitize_text_field( $_POST['blogname'] ) );
    }
    if ( isset( $_POST['blogdescription'] ) ) {
        update_option( 'blogdescription', sanitize_text_field( $_POST['blogdescription'] ) );
    }
    if ( isset( $_POST['site_icon'] ) ) {
        update_option( 'site_icon', absint( $_POST['site_icon'] ) );
    }

    // 7. Redirect with Success Parameter & Active Tab
    $active_tab = isset( $_POST['tokoku_active_tab'] ) ? sanitize_key( $_POST['tokoku_active_tab'] ) : 'tab-general';
    $redirect_url = add_query_arg( 
        array( 
            'page'             => 'tokoku-settings', 
            'settings-updated' => 'true',
            'tab'              => $active_tab,
        ), 
        admin_url( 'admin.php' ) 
    );
    
    wp_safe_redirect( $redirect_url );
    exit;
}
add_action( 'admin_post_tokoku_save_settings', 'tokoku_save_admin_settings' );


/**
 * Helper: Mengembalikan SVG Icon Crisp & Modern untuk Admin Tabs
 */
function tokoku_get_admin_icon( $icon_key ) {
    switch ( $icon_key ) {
        case 'general':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
        case 'about':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"></circle><path d="M15.5 13.2 17 22l-5-3-5 3 1.5-8.8"></path></svg>';
        case 'whatsapp':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/></svg>';
        case 'contact':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>';
        case 'appearance':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.5-.7 1.5-1.5 0-.4-.2-.8-.4-1.1-.3-.4-.4-.8-.4-1.4 0-1.1.9-2 2-2h2.4c3.6 0 6.5-2.9 6.5-6.5C23 5.8 18 2 12 2z"/></svg>';
        case 'single-product':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>';
        case 'slider':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line><polygon points="10 8 15 10 10 12 10 8" fill="currentColor"></polygon></svg>';
        case 'testimonials':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="currentColor"></polygon></svg>';
        case 'social':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>';
        case 'footer':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="15" x2="9" y2="21"></line></svg>';
        case 'seo':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><path d="m11 8 3 3-3 3"></path></svg>';
        case 'faq':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
        case 'update':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>';
        case 'import-export':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 3 21 3 21 8"></polyline><line x1="4" y1="20" x2="21" y2="3"></line><polyline points="21 16 21 21 16 21"></polyline><line x1="15" y1="15" x2="21" y2="21"></line><line x1="4" y1="4" x2="9" y2="9"></line></svg>';
        default:
            return '<span class="dashicons dashicons-' . esc_attr( $icon_key ) . '"></span>';
    }
}

/**
 * Helper: Render Tombol Update / Simpan Pengaturan di Bawah Tab Panel
 */
function tokoku_render_tab_save_button( $label = 'Perbarui Pengaturan' ) {
    ?>
    <div class="tokoku-tab-footer-actions">
        <button type="submit" class="button button-primary tokoku-submit-update-btn">
            <span class="tokoku-btn-icon">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm2 16H5V5h11.17L19 7.83V19zm-7-7c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zM6 6h9v4H6z"/></svg>
            </span>
            <span class="tokoku-btn-text"><?php echo esc_html( $label ); ?></span>
        </button>
        <span class="tokoku-tab-footer-note"><?php _e( 'Semua perubahan akan langsung diterapkan ke website toko Anda.', 'tokoku' ); ?></span>
    </div>
    <?php
}

/**
 * Handle Theme Update via AJAX
 */
function tokoku_ajax_handle_update() {
    check_ajax_referer( 'tokoku_update_nonce', 'nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Permission denied' );
    }

    $download_url = isset( $_POST['download_url'] ) ? esc_url_raw( $_POST['download_url'] ) : '';
    if ( empty( $download_url ) ) {
        wp_send_json_error( 'Missing download URL' );
    }

    // 🛡️ Security Check: Ensure URL is from authorized GitHub repo
    $allowed_hosts = array( 'codeload.github.com', 'api.github.com', 'objects.githubusercontent.com', 'github.com', 'raw.githubusercontent.com' );
    $is_allowed_host = false;
    foreach ( $allowed_hosts as $host ) {
        if ( strpos( $download_url, $host ) !== false ) {
            $is_allowed_host = true;
            break;
        }
    }

    $allowed_path = 'aphien/tokoku';
    if ( ! $is_allowed_host || strpos( $download_url, $allowed_path ) === false ) {
        wp_send_json_error( 'Unauthorized update source' );
    }

    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/misc.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

    // Force direct filesystem to bypass FTP credential prompt on local/shared servers
    add_filter( 'filesystem_method', function() { return 'direct'; } );
    WP_Filesystem();
    global $wp_filesystem;

    $temp_file = download_url( $download_url, 300 );
    if ( is_wp_error( $temp_file ) ) {
        wp_send_json_error( $temp_file->get_error_message() );
    }

    $upgrade_dir = WP_CONTENT_DIR . '/upgrade';
    if ( ! is_dir( $upgrade_dir ) ) {
        wp_mkdir_p( $upgrade_dir );
    }

    $unzip_dir = $upgrade_dir . '/tokoku_temp_' . time();
    wp_mkdir_p( $unzip_dir );

    $unzipped = unzip_file( $temp_file, $unzip_dir );
    @unlink( $temp_file );

    if ( is_wp_error( $unzipped ) ) {
        $wp_filesystem->delete( $unzip_dir, true );
        wp_send_json_error( $unzipped->get_error_message() );
    }

    // Find the inner folder (GitHub ZIPs wrap content in subfolder)
    $files = $wp_filesystem->dirlist( $unzip_dir );
    $inner_folder = '';
    if ( $files ) {
        foreach ( $files as $file_name => $file_info ) {
            if ( $file_info['type'] === 'd' ) {
                $inner_folder = $file_name;
                break;
            }
        }
    }

    if ( empty( $inner_folder ) ) {
        $wp_filesystem->delete( $unzip_dir, true );
        wp_send_json_error( 'Could not find theme folder in ZIP' );
    }

    $source      = trailingslashit( $unzip_dir ) . $inner_folder;
    $destination = get_template_directory();

    // Copy files
    $copy_result = copy_dir( $source, $destination );

    // Clean up temp dir
    $wp_filesystem->delete( $unzip_dir, true );

    if ( is_wp_error( $copy_result ) ) {
        wp_send_json_error( $copy_result->get_error_message() );
    }

    // Auto-install & auto-update bundled plugin (tokoku-bulk-editor) to wp-content/plugins
    $bundled_plugin_src = trailingslashit( $destination ) . 'tokoku-bulk-editor';
    if ( is_dir( $bundled_plugin_src ) && defined( 'WP_PLUGIN_DIR' ) ) {
        $plugin_dest = trailingslashit( WP_PLUGIN_DIR ) . 'tokoku-bulk-editor';
        if ( ! is_dir( $plugin_dest ) ) {
            wp_mkdir_p( $plugin_dest );
        }
        if ( function_exists( 'copy_dir' ) ) {
            copy_dir( $bundled_plugin_src, $plugin_dest );
        }

        if ( ! function_exists( 'activate_plugin' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugin_rel = 'tokoku-bulk-editor/tokoku-bulk-editor.php';
        if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_rel ) && ! is_plugin_active( $plugin_rel ) ) {
            activate_plugin( $plugin_rel, '', false, true );
        }
        update_option( 'tokoku_bulk_editor_installed_ver', '2.0.0', true );
    }

    // Flush opcache so new PHP files (including version constant) are loaded immediately
    if ( function_exists( 'opcache_reset' ) ) {
        opcache_reset();
    }
    wp_clean_themes_cache();

    wp_send_json_success( 'Theme and bundled plugins updated successfully' );
}
add_action( 'wp_ajax_tokoku_handle_update', 'tokoku_ajax_handle_update' );

/**
 * Helper: Format timestamp menjadi tanggal Bahasa Indonesia (contoh: 28 Juli 2026)
 */
function tokoku_format_indo_date( $timestamp ) {
    $months = array(
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    );
    $ts = is_numeric( $timestamp ) ? (int) $timestamp : strtotime( $timestamp );
    if ( ! $ts || $ts <= 0 ) {
        $ts = current_time( 'timestamp' );
    }
    $day   = gmdate( 'j', $ts );
    $month = isset( $months[ (int) gmdate( 'n', $ts ) ] ) ? $months[ (int) gmdate( 'n', $ts ) ] : gmdate( 'F', $ts );
    $year  = gmdate( 'Y', $ts );
    return "{$day} {$month} {$year}";
}

/**
 * AJAX Handler: Sinkronisasi Profil Bisnis & Ulasan dari Google Places API
 */
function tokoku_ajax_sync_google_reviews() {
    check_ajax_referer( 'tokoku_update_nonce', 'nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( __( 'Anda tidak memiliki izin untuk melakukan tindakan ini.', 'tokoku' ) );
    }

    $api_key  = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '';
    $place_id = isset( $_POST['place_id'] ) ? sanitize_text_field( wp_unslash( $_POST['place_id'] ) ) : '';

    if ( empty( $api_key ) || empty( $place_id ) ) {
        wp_send_json_error( __( 'Harap masukkan Google Maps API Key dan Place ID terlebih dahulu.', 'tokoku' ) );
    }

    // Simpan kredensial ke theme_mod
    set_theme_mod( 'tokoku_gmap_api_key', $api_key );
    set_theme_mod( 'tokoku_gmap_place_id', $place_id );

    $place_name    = '';
    $place_rating  = '5.0';
    $total_reviews = 0;
    $review_url    = 'https://search.google.com/local/writereview?placeid=' . rawurlencode( $place_id );
    $reviews_list  = array();
    $api_error_msg = '';

    // 1. Coba Google Places API (Legacy Place Details) dengan bahasa Indonesia
    $legacy_url = add_query_arg(
        array(
            'place_id' => $place_id,
            'fields'   => 'name,rating,user_ratings_total,url,reviews',
            'language' => 'id',
            'key'      => $api_key,
        ),
        'https://maps.googleapis.com/maps/api/place/details/json'
    );

    $response = wp_remote_get( $legacy_url, array( 'timeout' => 15 ) );

    if ( ! is_wp_error( $response ) ) {
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( isset( $body['status'] ) && 'OK' === $body['status'] && ! empty( $body['result'] ) ) {
            $res           = $body['result'];
            $place_name    = isset( $res['name'] ) ? sanitize_text_field( $res['name'] ) : '';
            $place_rating  = isset( $res['rating'] ) ? number_format( (float) $res['rating'], 1, '.', '' ) : '5.0';
            $total_reviews = isset( $res['user_ratings_total'] ) ? absint( $res['user_ratings_total'] ) : 0;
            if ( ! empty( $res['url'] ) ) {
                $review_url = esc_url_raw( $res['url'] );
            }
            if ( ! empty( $res['reviews'] ) && is_array( $res['reviews'] ) ) {
                foreach ( $res['reviews'] as $rev ) {
                    $reviews_list[] = array(
                        'name'        => isset( $rev['author_name'] ) ? sanitize_text_field( $rev['author_name'] ) : 'Pelanggan Google',
                        'img'         => isset( $rev['profile_photo_url'] ) ? esc_url_raw( $rev['profile_photo_url'] ) : '',
                        'rating'      => isset( $rev['rating'] ) ? max( 1, min( 5, absint( $rev['rating'] ) ) ) : 5,
                        'text'        => isset( $rev['text'] ) ? sanitize_textarea_field( $rev['text'] ) : '',
                        'date'        => isset( $rev['time'] ) ? tokoku_format_indo_date( $rev['time'] ) : tokoku_format_indo_date( time() ),
                        'local_guide' => ! empty( $rev['profile_photo_url'] ) ? 1 : 0,
                    );
                }
            }
        } else {
            $api_error_msg = isset( $body['error_message'] ) ? $body['error_message'] : ( isset( $body['status'] ) ? $body['status'] : '' );
        }
    } else {
        $api_error_msg = $response->get_error_message();
    }

    // 2. Fallback ke Google Places API (New v1) jika Legacy belum diaktifkan
    if ( empty( $place_name ) && empty( $reviews_list ) ) {
        $v1_url      = 'https://places.googleapis.com/v1/places/' . rawurlencode( $place_id ) . '?languageCode=id';
        $v1_response = wp_remote_get(
            $v1_url,
            array(
                'timeout' => 15,
                'headers' => array(
                    'X-Goog-Api-Key'   => $api_key,
                    'X-Goog-FieldMask' => 'displayName,rating,userRatingCount,googleMapsUri,reviews',
                ),
            )
        );

        if ( ! is_wp_error( $v1_response ) ) {
            $v1_body = json_decode( wp_remote_retrieve_body( $v1_response ), true );
            if ( ! empty( $v1_body['displayName']['text'] ) ) {
                $place_name    = sanitize_text_field( $v1_body['displayName']['text'] );
                $place_rating  = isset( $v1_body['rating'] ) ? number_format( (float) $v1_body['rating'], 1, '.', '' ) : '5.0';
                $total_reviews = isset( $v1_body['userRatingCount'] ) ? absint( $v1_body['userRatingCount'] ) : 0;
                if ( ! empty( $v1_body['googleMapsUri'] ) ) {
                    $review_url = esc_url_raw( $v1_body['googleMapsUri'] );
                }
                if ( ! empty( $v1_body['reviews'] ) && is_array( $v1_body['reviews'] ) ) {
                    foreach ( $v1_body['reviews'] as $rev ) {
                        $reviews_list[] = array(
                            'name'        => isset( $rev['authorAttribution']['displayName'] ) ? sanitize_text_field( $rev['authorAttribution']['displayName'] ) : 'Pelanggan Google',
                            'img'         => isset( $rev['authorAttribution']['photoUri'] ) ? esc_url_raw( $rev['authorAttribution']['photoUri'] ) : '',
                            'rating'      => isset( $rev['rating'] ) ? max( 1, min( 5, absint( $rev['rating'] ) ) ) : 5,
                            'text'        => isset( $rev['text']['text'] ) ? sanitize_textarea_field( $rev['text']['text'] ) : '',
                            'date'        => isset( $rev['publishTime'] ) ? tokoku_format_indo_date( $rev['publishTime'] ) : tokoku_format_indo_date( time() ),
                            'local_guide' => ! empty( $rev['authorAttribution']['photoUri'] ) ? 1 : 0,
                        );
                    }
                }
            } elseif ( ! empty( $v1_body['error']['message'] ) ) {
                $api_error_msg = $v1_body['error']['message'];
            }
        }
    }

    if ( empty( $place_name ) && empty( $reviews_list ) ) {
        wp_send_json_error(
            sprintf(
                __( 'Gagal mengambil data dari Google Places API (%s). Pastikan Places API sudah aktif di Google Cloud Console dan Place ID benar, atau gunakan fitur input manual di bawah.', 'tokoku' ),
                $api_error_msg ? $api_error_msg : 'Unknown Error'
            )
        );
    }

    // Simpan ringkasan bisnis ke theme_mod
    if ( ! empty( $place_name ) ) {
        set_theme_mod( 'tokoku_gmap_place_name', $place_name );
    }
    set_theme_mod( 'tokoku_gmap_place_rating', $place_rating );
    if ( $total_reviews > 0 ) {
        set_theme_mod( 'tokoku_gmap_total_reviews', $total_reviews );
    }
    if ( ! empty( $review_url ) ) {
        set_theme_mod( 'tokoku_gmap_review_url', $review_url );
    }

    // Simpan ulasan yang ditarik ke slot #1 s/d jumlah ulasan yang didapat
    $synced_count = 0;
    foreach ( $reviews_list as $idx => $item ) {
        $slot = $idx + 1;
        if ( $slot > 20 ) {
            break;
        }
        set_theme_mod( "tokoku_testi_name_{$slot}", $item['name'] );
        set_theme_mod( "tokoku_testi_img_{$slot}", $item['img'] );
        set_theme_mod( "tokoku_testi_rating_{$slot}", $item['rating'] );
        set_theme_mod( "tokoku_testi_text_{$slot}", $item['text'] );
        set_theme_mod( "tokoku_testi_date_{$slot}", $item['date'] );
        set_theme_mod( "tokoku_testi_local_guide_{$slot}", $item['local_guide'] );
        $synced_count++;
    }

    wp_send_json_success(
        array(
            'message'       => sprintf( __( 'Berhasil menyinkronkan profil "%s" (%d total ulasan) dan mengimpor %d ulasan terbaru dari Google Maps!', 'tokoku' ), $place_name, $total_reviews, $synced_count ),
            'place_name'    => $place_name,
            'place_rating'  => $place_rating,
            'total_reviews' => $total_reviews,
            'review_url'    => $review_url,
            'reviews'       => $reviews_list,
        )
    );
}
add_action( 'wp_ajax_tokoku_sync_google_reviews', 'tokoku_ajax_sync_google_reviews' );

/**
 * Settings Page HTML
 */
function tokoku_settings_page_html() {
    if ( isset( $_GET['settings-updated'] ) ) {
        add_settings_error( 'tokoku_messages', 'tokoku_message', __( 'Settings Saved', 'tokoku' ), 'updated' );
    }
    settings_errors( 'tokoku_messages' );
    ?>
    <div class="wrap tokoku-admin-wrap">
        <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" class="tokoku-settings-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="tokoku_save_settings">
            <?php wp_nonce_field( 'tokoku_save_settings_action', 'tokoku_settings_nonce' ); ?>
            <input type="hidden" name="tokoku_active_tab" id="tokoku_active_tab" value="<?php echo esc_attr( isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'tab-general' ); ?>">

            <div class="tokoku-admin-header">
                <div class="tokoku-admin-logo">
                    <span class="dashicons dashicons-store"></span>
                    <div class="tokoku-admin-heading-group">
                        <h1>Tokoku <span class="tokoku-brand-sub">by M.alfiandi Ismet</span></h1>
                        <span class="tokoku-admin-subhead"><?php _e( 'Panel Kontrol & Kustomisasi Tema Toko Online', 'tokoku' ); ?></span>
                    </div>
                </div>
                <div class="tokoku-admin-header-actions">
                    <div class="tokoku-admin-version-pill" title="Versi Tema Aktif">
                        <span class="tokoku-status-dot"></span>
                        <span class="tokoku-version-text">v<?php echo TOKOKU_VERSION; ?></span>
                        <span class="tokoku-version-badge-tag"><?php _e( 'Optimal', 'tokoku' ); ?></span>
                    </div>
                    <button type="button" class="button button-secondary tokoku-nav-toggle-btn" id="tokoku-nav-toggle" title="<?php esc_attr_e( 'Sembunyikan / Tampilkan Menu Navigasi (Shortcut: M)', 'tokoku' ); ?>">
                        <span class="dashicons dashicons-menu-alt3"></span>
                        <span class="tokoku-nav-toggle-text"><?php _e( 'Sembunyikan Menu', 'tokoku' ); ?></span>
                    </button>
                    <button type="submit" class="button button-primary tokoku-submit-update-btn tokoku-top-save-btn" id="tokoku-top-save" title="<?php esc_attr_e( 'Simpan perubahan (Shortcut: Ctrl/Cmd + S)', 'tokoku' ); ?>">
                        <span class="tokoku-btn-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm2 16H5V5h11.17L19 7.83V19zm-7-7c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zM6 6h9v4H6z"/></svg>
                        </span>
                        <span class="tokoku-btn-text"><?php _e( 'Simpan Pengaturan', 'tokoku' ); ?></span>
                        <span class="tokoku-kbd-hint">⌘S</span>
                    </button>
                </div>
            </div>

            <div class="tokoku-settings-container">
                <div class="tokoku-settings-nav">
                    <div class="tokoku-nav-top-header">
                        <div class="tokoku-nav-top-label-wrap">
                            <span class="dashicons dashicons-menu-alt3" style="font-size: 16px; width: 16px; height: 16px; color: #2563eb;"></span>
                            <span class="tokoku-nav-top-label"><?php _e( 'Menu Navigasi', 'tokoku' ); ?></span>
                        </div>
                        <button type="button" class="tokoku-nav-close-trigger" id="tokoku-nav-close-trigger" title="<?php esc_attr_e( 'Sembunyikan Menu Navigasi', 'tokoku' ); ?>">
                            <span class="dashicons dashicons-arrow-left-alt2"></span>
                            <span class="tokoku-close-tip"><?php _e( 'Tutup', 'tokoku' ); ?></span>
                        </button>
                    </div>
                    <?php
                    // Define categorized tab groups for clear logical organization
                    $tab_groups = array(
                        'utama' => array(
                            'title' => __( 'PENGATURAN UTAMA', 'tokoku' ),
                            'tabs'  => array(
                                'tab-general'    => array( 
                                    'icon'  => 'general',
                                    'color' => 'emerald',
                                    'title' => __( 'Identitas Toko', 'tokoku' ),
                                    'desc'  => __( 'Logo, Nama & Slogan Toko', 'tokoku' ),
                                ),
                                'tab-whatsapp'   => array( 
                                    'icon'  => 'whatsapp',
                                    'color' => 'whatsapp',
                                    'title' => __( 'WhatsApp & CS', 'tokoku' ),
                                    'desc'  => __( 'Nomor WA & Pesan Otomatis', 'tokoku' ),
                                ),
                                'tab-appearance' => array( 
                                    'icon'  => 'appearance',
                                    'color' => 'purple',
                                    'title' => __( 'Warna & Tampilan', 'tokoku' ),
                                    'desc'  => __( 'Warna Brand & Mode Gelap', 'tokoku' ),
                                ),
                            ),
                        ),
                        'halaman' => array(
                            'title' => __( 'TEMPLATE HALAMAN', 'tokoku' ),
                            'tabs'  => array(
                                'tab-page-about' => array( 
                                    'icon'  => 'about',
                                    'color' => 'indigo',
                                    'title' => __( 'Tentang Kami', 'tokoku' ),
                                    'desc'  => __( 'Profil, Cerita, Visi & Nilai', 'tokoku' ),
                                ),
                                'tab-contact'    => array( 
                                    'icon'  => 'contact',
                                    'color' => 'teal',
                                    'title' => __( 'Kontak Kami', 'tokoku' ),
                                    'desc'  => __( 'Hero, Form WA, FAQ & Workshop', 'tokoku' ),
                                ),
                            ),
                        ),
                        'konten' => array(
                            'title' => __( 'KATALOG & KONTEN ETALASE', 'tokoku' ),
                            'tabs'  => array(
                                'tab-single-product' => array( 
                                    'icon'  => 'single-product',
                                    'color' => 'amber',
                                    'title' => __( 'Halaman Produk', 'tokoku' ),
                                    'desc'  => __( 'Layout, Badges & Estimasi', 'tokoku' ),
                                ),
                                'tab-slider'         => array( 
                                    'icon'  => 'slider',
                                    'color' => 'rose',
                                    'title' => __( 'Banner Slider', 'tokoku' ),
                                    'desc'  => __( 'Slide Promo & Kecepatan', 'tokoku' ),
                                ),
                                'tab-testimonials'   => array( 
                                    'icon'  => 'testimonials',
                                    'color' => 'gold',
                                    'title' => __( 'Ulasan & Klien', 'tokoku' ),
                                    'desc'  => __( 'Testimoni & Logo Mitra', 'tokoku' ),
                                ),
                                'tab-faq'            => array( 
                                    'icon'  => 'faq',
                                    'color' => 'cyan',
                                    'title' => __( 'Tanya Jawab (FAQ)', 'tokoku' ),
                                    'desc'  => __( 'Daftar Tanya Jawab Toko', 'tokoku' ),
                                ),
                            ),
                        ),
                        'optimasi' => array(
                            'title' => __( 'DESAIN & OPTIMASI', 'tokoku' ),
                            'tabs'  => array(
                                'tab-seo'        => array( 
                                    'icon'  => 'seo',
                                    'color' => 'indigo',
                                    'title' => __( 'SEO & Metadata', 'tokoku' ),
                                    'desc'  => __( 'Google Rich Snippet & Meta', 'tokoku' ),
                                ),

                            ),
                        ),
                        'footer' => array(
                            'title' => __( 'NAVIGASI & FOOTER', 'tokoku' ),
                            'tabs'  => array(
                                'tab-footer' => array( 
                                    'icon'  => 'footer',
                                    'color' => 'slate',
                                    'title' => __( 'Footer Website', 'tokoku' ),
                                    'desc'  => __( 'Kolom Bawah & Alamat', 'tokoku' ),
                                ),
                                'tab-social' => array( 
                                    'icon'  => 'social',
                                    'color' => 'pink',
                                    'title' => __( 'Media Sosial', 'tokoku' ),
                                    'desc'  => __( 'Instagram, TikTok, FB, dll', 'tokoku' ),
                                ),
                            ),
                        ),
                        'sistem' => array(
                            'title' => __( 'SISTEM & CADANGAN', 'tokoku' ),
                            'tabs'  => array(
                                'tab-update'        => array( 
                                    'icon'  => 'update',
                                    'color' => 'sky',
                                    'title' => __( 'Pembaruan Tema', 'tokoku' ),
                                    'desc'  => __( 'Cek & Pasang Versi Terbaru', 'tokoku' ),
                                ),
                                'tab-import-export' => array( 
                                    'icon'  => 'import-export',
                                    'color' => 'zinc',
                                    'title' => __( 'Cadangan & Impor', 'tokoku' ),
                                    'desc'  => __( 'Ekspor / Impor Pengaturan', 'tokoku' ),
                                ),
                            ),
                        ),
                    );

                    $active_tab_param = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'tab-general';
                    $first_tab_rendered = true;

                    foreach ( $tab_groups as $group_key => $group ) : ?>
                        <div class="tokoku-nav-group">
                            <div class="tokoku-nav-group-header">
                                <span class="tokoku-nav-group-title"><?php echo esc_html( $group['title'] ); ?></span>
                            </div>
                            <div class="tokoku-nav-group-items">
                                <?php foreach ( $group['tabs'] as $tab_id => $tab ) : 
                                    $is_active = ( $tab_id === $active_tab_param ) || ( empty( $active_tab_param ) && $first_tab_rendered );
                                    $active_class = $is_active ? 'active' : '';
                                ?>
                                    <div class="tokoku-nav-item <?php echo esc_attr( $active_class ); ?>" data-tab="<?php echo esc_attr( $tab_id ); ?>">
                                        <span class="tokoku-nav-icon tokoku-icon--<?php echo esc_attr( $tab['color'] ); ?>">
                                            <?php echo tokoku_get_admin_icon( $tab['icon'] ); ?>
                                        </span>
                                        <div class="tokoku-nav-label-box">
                                            <strong class="tokoku-nav-title"><?php echo esc_html( $tab['title'] ); ?></strong>
                                            <span class="tokoku-nav-desc"><?php echo esc_html( $tab['desc'] ); ?></span>
                                        </div>
                                        <span class="tokoku-nav-chevron" aria-hidden="true">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </span>
                                    </div>
                                <?php 
                                    $first_tab_rendered = false;
                                endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="tokoku-settings-content">
                    <button type="button" class="tokoku-nav-floating-btn" id="tokoku-nav-floating-btn" title="<?php esc_attr_e( 'Buka / Tampilkan Menu Navigasi (Shortcut: M)', 'tokoku' ); ?>">
                        <span class="dashicons dashicons-menu-alt3"></span>
                        <span><?php _e( 'Buka Menu', 'tokoku' ); ?></span>
                    </button>

                    <!-- Tab: General -->
                    <div id="tab-general" class="tokoku-tab-panel <?php echo ( 'tab-general' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Branding & Identitas', 'tokoku' ); ?></h2>
                        
                        <div class="tokoku-field">
                            <label><?php _e( 'Judul Website', 'tokoku' ); ?></label>
                            <input type="text" name="blogname" value="<?php echo esc_attr( get_option( 'blogname' ) ); ?>" placeholder="Contoh: Tokoku - Toko Online Terpercaya">
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Nama utama website Anda yang akan muncul di tab browser dan hasil pencarian Google.', 'tokoku' ); ?></p>
                        </div>

                        <div class="tokoku-field">
                            <label><?php _e( 'Tagline Website', 'tokoku' ); ?></label>
                            <input type="text" name="blogdescription" value="<?php echo esc_attr( get_option( 'blogdescription' ) ); ?>" placeholder="Slogan atau deskripsi singkat">
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Menjelaskan secara singkat apa yang Anda jual untuk menarik perhatian pengunjung.', 'tokoku' ); ?></p>
                        </div>

                        <div class="tokoku-field">
                            <label><?php _e( 'Ikon Website (Favicon)', 'tokoku' ); ?></label>
                            <div class="tokoku-media-upload">
                                <?php 
                                $icon_id = get_option( 'site_icon' );
                                $icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'full' ) : '';
                                ?>
                                <img src="<?php echo esc_url( $icon_url ); ?>" class="tokoku-preview-img" style="<?php echo $icon_url ? '' : 'display:none;'; ?>">
                                <input type="hidden" name="site_icon" value="<?php echo esc_attr( $icon_id ); ?>">
                                <button type="button" class="button tokoku-upload-btn-id"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Ikon', 'tokoku' ); ?></span></button>
                                <button type="button" class="button tokoku-remove-btn" style="<?php echo $icon_url ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                            </div>
                            <p class="tokoku-tip"><?php _e( 'Rekomendasi: Gambar persegi, minimal 512x512 pixel. Ikon ini akan muncul di tab browser dan ikon aplikasi mobile.', 'tokoku' ); ?></p>
                        </div>
                        
                        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">
                        <div class="tokoku-field">
                            <label><?php _e( 'Logo Light Mode', 'tokoku' ); ?></label>
                            <div class="tokoku-media-upload">
                                <img src="<?php echo esc_url( get_theme_mod( 'tokoku_logo_light' ) ); ?>" class="tokoku-preview-img" style="<?php echo get_theme_mod( 'tokoku_logo_light' ) ? '' : 'display:none;'; ?>">
                                <input type="hidden" name="tokoku_logo_light" value="<?php echo esc_attr( get_theme_mod( 'tokoku_logo_light' ) ); ?>">
                                <button type="button" class="button tokoku-upload-btn"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Gambar', 'tokoku' ); ?></span></button>
                                <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( 'tokoku_logo_light' ) ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                            </div>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Logo yang akan muncul saat website berada dalam Mode Terang (Light Mode).', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Logo Dark Mode', 'tokoku' ); ?></label>
                            <div class="tokoku-media-upload">
                                <img src="<?php echo esc_url( get_theme_mod( 'tokoku_logo_dark' ) ); ?>" class="tokoku-preview-img" style="<?php echo get_theme_mod( 'tokoku_logo_dark' ) ? '' : 'display:none;'; ?>">
                                <input type="hidden" name="tokoku_logo_dark" value="<?php echo esc_attr( get_theme_mod( 'tokoku_logo_dark' ) ); ?>">
                                <button type="button" class="button tokoku-upload-btn"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Gambar', 'tokoku' ); ?></span></button>
                                <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( 'tokoku_logo_dark' ) ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                            </div>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Logo yang akan muncul saat website berada dalam Mode Gelap (Dark Mode). Pastikan menggunakan logo warna terang.', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Deskripsi Singkat Toko', 'tokoku' ); ?></label>
                            <textarea name="tokoku_site_description"><?php echo esc_textarea( get_theme_mod( 'tokoku_site_description', get_bloginfo( 'description' ) ) ); ?></textarea>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Kalimat ini akan muncul di Google dan Footer; buatlah semenarik mungkin untuk meningkatkan kepercayaan pelanggan.', 'tokoku' ); ?></p>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: WhatsApp -->
                    <div id="tab-whatsapp" class="tokoku-tab-panel <?php echo ( 'tab-whatsapp' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Pengaturan WhatsApp', 'tokoku' ); ?></h2>
                        <div class="tokoku-field">
                            <label><?php _e( 'Nomor WhatsApp', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_wa_number" value="<?php echo esc_attr( get_theme_mod( 'tokoku_wa_number', '6281234567890' ) ); ?>" placeholder="6281234567890">
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Jalur komunikasi utama untuk menerima pesanan dan pertanyaan dari pelanggan secara langsung.', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Template Pesan', 'tokoku' ); ?></label>
                            <div class="tokoku-editor-wrap">
                                <?php 
                                wp_editor( 
                                    get_theme_mod( 'tokoku_wa_message', "Halo Admin,\n\nSaya ingin memesan produk berikut:\n\n👤 *Nama Pemesan :* {nama}\n📦 *Produk :* {produk}\n🏷️ *SKU :* {sku}\n📝 *Catatan :* {catatan}\n🔗 *Link Produk :* {link}\n\nMohon segera diproses. Terima kasih!" ), 
                                    'tokoku_wa_message', 
                                    array(
                                        'textarea_name' => 'tokoku_wa_message',
                                        'textarea_rows' => 10,
                                        'media_buttons' => false,
                                        'tinymce'       => array(
                                            'toolbar1' => 'bold,italic,underline,separator,bullist,numlist,separator,undo,redo',
                                            'toolbar2' => '',
                                        ),
                                        'quicktags'     => true
                                    ) 
                                ); 
                                ?>
                            </div>
                            <p class="description"><?php _e( 'Placeholder: {nama}, {produk}, {sku}, {catatan}, {link}, {harga}', 'tokoku' ); ?></p>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Template yang rapi membantu Anda memproses data pesanan dengan lebih cepat dan akurat.', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Teks Tombol Floating', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_wa_float_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_wa_float_text', 'Chat dengan kami' ) ); ?>">
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Memberikan ajakan bertindak (CTA) yang jelas agar pengunjung tidak ragu untuk menghubungi Anda.', 'tokoku' ); ?></p>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: Appearance -->
                    <div id="tab-appearance" class="tokoku-tab-panel <?php echo ( 'tab-appearance' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Tampilan & Warna', 'tokoku' ); ?></h2>
                        <div class="tokoku-field">
                            <label><?php _e( 'Fitur Dark Mode', 'tokoku' ); ?></label>
                            <select name="tokoku_enable_dark_mode">
                                <option value="yes" <?php selected( get_theme_mod( 'tokoku_enable_dark_mode', 'yes' ), 'yes' ); ?>><?php _e( 'Aktif', 'tokoku' ); ?></option>
                                <option value="no" <?php selected( get_theme_mod( 'tokoku_enable_dark_mode', 'yes' ), 'no' ); ?>><?php _e( 'Nonaktif', 'tokoku' ); ?></option>
                            </select>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Aktifkan jika Anda ingin memberikan pilihan kepada pengunjung untuk beralih ke mode gelap.', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Warna Utama', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_primary_color" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_primary_color', '#007bff' ) ); ?>">
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Membentuk identitas visual toko Anda agar mudah diingat oleh pelanggan.', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Warna Gradasi', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_secondary_color" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_secondary_color', '#0056b3' ) ); ?>">
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Memberikan kesan mewah dan modern pada elemen-elemen tombol di website.', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Mode Default Website', 'tokoku' ); ?></label>
                            <select name="tokoku_default_mode">
                                <option value="auto" <?php selected( get_theme_mod( 'tokoku_default_mode', 'dark' ), 'auto' ); ?>><?php _e( 'Otomatis (Ikuti Sistem)', 'tokoku' ); ?></option>
                                <option value="dark" <?php selected( get_theme_mod( 'tokoku_default_mode', 'dark' ), 'dark' ); ?>><?php _e( 'Dark Mode (Direkomendasikan)', 'tokoku' ); ?></option>
                                <option value="light" <?php selected( get_theme_mod( 'tokoku_default_mode', 'dark' ), 'light' ); ?>><?php _e( 'Light Mode', 'tokoku' ); ?></option>
                            </select>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Menentukan tampilan awal saat pengunjung pertama kali membuka website Anda.', 'tokoku' ); ?></p>
                        </div>

                        <div class="tokoku-settings-group" style="margin-top: 30px; padding: 20px; background: #f0f0f1; border-radius: 8px;">
                            <h3 style="margin-top: 0;"><?php _e( 'Kustomisasi Dark Mode', 'tokoku' ); ?></h3>
                            <div class="tokoku-field">
                                <label><?php _e( 'Warna Background Dark', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_dark_bg" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_dark_bg', '#0b0f1a' ) ); ?>">
                            </div>
                            <div class="tokoku-field">
                                <label><?php _e( 'Warna Background Sekunder', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_dark_bg2" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_dark_bg2', '#151b2d' ) ); ?>">
                            </div>
                            <div class="tokoku-field">
                                <label><?php _e( 'Warna Teks Dark', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_dark_text" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_dark_text', '#f1f5f9' ) ); ?>">
                            </div>
                        </div>
                        <!-- Pengaturan Tampilkan Harga dihapus agar otomatis mengikuti input -->
                        <div class="tokoku-field">
                            <label><?php _e( 'Simbol Mata Uang', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_currency" value="<?php echo esc_attr( get_theme_mod( 'tokoku_currency', 'Rp' ) ); ?>">
                        </div>

                        <!-- Consolidated Element Styling -->
                        <div class="tokoku-settings-group" style="margin-top: 30px; padding: 20px; background: #f0f0f1; border-radius: 8px;">
                            <h3><?php _e( 'Kustomisasi Elemen Spesifik', 'tokoku' ); ?></h3>
                            
                            <div class="tokoku-settings-section" style="margin-bottom: 20px;">
                                <h4><?php _e( 'Header & Navigasi', 'tokoku' ); ?></h4>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Background Header', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_header_bg" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_header_bg', '#ffffff' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Warna Teks/Menu Header', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_header_text" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_header_text', '#0f172a' ) ); ?>">
                                </div>
                            </div>

                            <div class="tokoku-settings-section" style="margin-bottom: 20px;">
                                <h4><?php _e( 'Bagian Produk (Cards)', 'tokoku' ); ?></h4>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Background Kartu Produk', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_card_bg" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_card_bg', '#ffffff' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Warna Judul Produk', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_card_text" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_card_text', '#0f172a' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Warna Harga', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_price_color" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_price_color', '#007bff' ) ); ?>">
                                </div>
                            </div>

                            <div class="tokoku-settings-section">
                                <h4><?php _e( 'Bagian Footer', 'tokoku' ); ?></h4>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Background Footer', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_footer_bg" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_footer_bg', '#f1f5f9' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Warna Teks Footer', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_footer_text" class="color-picker" value="<?php echo esc_attr( get_theme_mod( 'tokoku_footer_text', '#475569' ) ); ?>">
                                </div>
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: Template Tentang Kami -->
                    <div id="tab-page-about" class="tokoku-tab-panel <?php echo ( 'tab-page-about' === $active_tab_param ) ? 'active' : ''; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                            <div>
                                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #0f172a;"><?php _e( 'Template Halaman: Tentang Kami', 'tokoku' ); ?></h2>
                                <p class="description" style="margin-top: 6px; font-size: 0.95rem;"><?php _e( 'Kelola seluruh isi dan tampilan konten halaman Tentang Kami (Hero, statistik prestasi, cerita kami, 6 komitmen & nilai layanan, 4 langkah alur kerja, visi misi, partner klien, serta banner ajakan bertindak / CTA).', 'tokoku' ); ?></p>
                            </div>
                            <div>
                                <a href="<?php echo esc_url( function_exists( 'tokoku_static_page_url' ) ? tokoku_static_page_url( 'page-templates/template-tentang.php' ) : home_url( '/tentang-kami/' ) ); ?>" target="_blank" rel="noopener" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                                    <span class="dashicons dashicons-external" style="margin-top: 2px;"></span>
                                    <?php _e( 'Pratinjau Halaman Tentang Kami', 'tokoku' ); ?>
                                </a>
                            </div>
                        </div>

                        <?php
                        $about_brand       = function_exists( 'tokoku_page_brand' ) ? tokoku_page_brand() : ( function_exists( 'tokoku_get_site_domain' ) ? tokoku_get_site_domain() : 'domain.com' );
                        $about_catalog_url = get_post_type_archive_link( 'produk' ) ? get_post_type_archive_link( 'produk' ) : home_url( '/' );
                        $icon_choices = array(
                            'target'   => __( 'Target / Presisi', 'tokoku' ),
                            'gem'      => __( 'Permata / Material Pilihan', 'tokoku' ),
                            'pen'      => __( 'Pena / Desain Personal', 'tokoku' ),
                            'zap'      => __( 'Petir / Cepat & Tepat Waktu', 'tokoku' ),
                            'chat'     => __( 'Chat / Komunikasi Responsif', 'tokoku' ),
                            'tag'      => __( 'Label / Harga Transparan', 'tokoku' ),
                            'award'    => __( 'Piala / Kualitas Penghargaan', 'tokoku' ),
                            'layers'   => __( 'Layers / Mockup & Desain', 'tokoku' ),
                            'truck'    => __( 'Truk / Pengiriman Aman', 'tokoku' ),
                            'shield'   => __( 'Perisai / Keamanan & Garansi', 'tokoku' ),
                            'heart'    => __( 'Hati / Sepenuh Hati', 'tokoku' ),
                            'eye'      => __( 'Mata / Visi & Ketelitian', 'tokoku' ),
                            'clock'    => __( 'Jam / Tepat Waktu', 'tokoku' ),
                            'users'    => __( 'Tim / Pelayanan', 'tokoku' ),
                            'check'    => __( 'Centang / Terpercaya', 'tokoku' ),
                        );
                        ?>

                        <!-- 1. Hero & Identitas Halaman -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-cover-image" style="color: #4f46e5; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '1. Bagian Hero & Identitas Halaman', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Sesuaikan judul utama, teks pengantar, tombol ajakan bertindak, serta kartu mockup plakat yang tampil di bagian paling atas halaman Tentang Kami.', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Badge Kecil Hero', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_hero_badge" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_hero_badge', sprintf( 'Tentang %s', $about_brand ) ) ); ?>" placeholder="Contoh: Tentang <?php echo esc_attr( $about_brand ); ?>">
                                    <p class="tokoku-tip"><?php _e( 'Teks label kecil berikon piala di atas judul utama.', 'tokoku' ); ?></p>
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Judul Utama Hero', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_hero_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_hero_title', 'Mengabadikan Setiap Pencapaian dalam <span class="jp-text-gradient">Karya yang Bermakna</span>' ) ); ?>">
                                    <p class="tokoku-tip"><?php _e( 'Tip: Gunakan tag &lt;span class="jp-text-gradient"&gt;Teks&lt;/span&gt; untuk efek warna gradien modern.', 'tokoku' ); ?></p>
                                </div>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Deskripsi / Paragraf Pengantar Hero', 'tokoku' ); ?></label>
                                <textarea name="tokoku_about_hero_desc" rows="3"><?php echo esc_textarea( get_theme_mod( 'tokoku_about_hero_desc', sprintf( '%s adalah mitra terpercaya untuk pembuatan plakat, piala, dan souvenir penghargaan custom. Kami percaya setiap prestasi, dedikasi, dan momen berharga layak dikenang dalam wujud yang indah, berkelas, dan tahan lama.', $about_brand ) ) ); ?></textarea>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1.5fr 1fr; gap: 16px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Tombol 1 (Katalog)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_hero_btn1_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_hero_btn1_text', 'Jelajahi Katalog' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'URL Tombol 1 (Katalog)', 'tokoku' ); ?></label>
                                    <input type="url" name="tokoku_about_hero_btn1_url" value="<?php echo esc_url( get_theme_mod( 'tokoku_about_hero_btn1_url', $about_catalog_url ) ); ?>" placeholder="<?php echo esc_url( $about_catalog_url ); ?>">
                                    <p class="tokoku-tip"><?php _e( 'Kosongkan untuk otomatis mengarah ke arsip katalog produk.', 'tokoku' ); ?></p>
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Tombol 2 (Konsultasi WA)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_hero_btn2_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_hero_btn2_text', 'Konsultasi Gratis' ) ); ?>">
                                </div>
                            </div>

                            <!-- Mockup Plakat Kanan -->
                            <div class="tokoku-settings-section" style="margin-top: 15px; border-left: 3px solid #4f46e5;">
                                <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 12px;">
                                    <span class="dashicons dashicons-awards" style="color: #4f46e5; margin-right: 4px;"></span>
                                    <?php _e( 'Kartu Ilustrasi Plakat Penghargaan (Kanan Hero)', 'tokoku' ); ?>
                                </strong>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div class="tokoku-field">
                                        <label><?php _e( 'Teks Pita / Eyebrow', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_about_plaque_eyebrow" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_plaque_eyebrow', 'Penghargaan' ) ); ?>" placeholder="Penghargaan">
                                    </div>
                                    <div class="tokoku-field">
                                        <label><?php _e( 'Judul Apresiasi Plakat', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_about_plaque_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_plaque_title', 'Apresiasi Terbaik' ) ); ?>" placeholder="Apresiasi Terbaik">
                                    </div>
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Kutipan Plakat', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_plaque_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_plaque_text', 'Diberikan atas dedikasi, kerja keras, dan prestasi yang menginspirasi.' ) ); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- 2. Bar Statistik Prestasi -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-chart-bar" style="color: #4f46e5; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '2. Bar Statistik Prestasi & Bukti Kinerja', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Tampilkan 4 angka pencapaian penting untuk membangun rasa percaya calon pembeli:', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                                <?php
                                $default_stats_config = array(
                                    1 => array( 'val' => '100+', 'lbl' => 'Pilihan Desain Produk' ),
                                    2 => array( 'val' => '5+',   'lbl' => 'Kategori Material' ),
                                    3 => array( 'val' => 'Gratis', 'lbl' => 'Konsultasi & Mockup' ),
                                    4 => array( 'val' => '34',   'lbl' => 'Provinsi Terjangkau' ),
                                );
                                for ( $i = 1; $i <= 4; $i++ ) :
                                    $s_val = get_theme_mod( "tokoku_about_stat{$i}_val", $default_stats_config[$i]['val'] );
                                    $s_lbl = get_theme_mod( "tokoku_about_stat{$i}_lbl", $default_stats_config[$i]['lbl'] );
                                ?>
                                    <div class="tokoku-settings-section" style="border-left: 3px solid #6366f1;">
                                        <strong style="color: #0f172a; font-size: 0.9rem; margin-bottom: 8px; display: block;">Statistik #<?php echo $i; ?></strong>
                                        <div class="tokoku-field" style="margin-bottom: 8px;">
                                            <label style="font-size: 11px;"><?php _e( 'Angka / Nilai', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_about_stat<?php echo $i; ?>_val" value="<?php echo esc_attr( $s_val ); ?>" placeholder="<?php echo esc_attr( $default_stats_config[$i]['val'] ); ?>">
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 0;">
                                            <label style="font-size: 11px;"><?php _e( 'Label Keterangan', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_about_stat<?php echo $i; ?>_lbl" value="<?php echo esc_attr( $s_lbl ); ?>" placeholder="<?php echo esc_attr( $default_stats_config[$i]['lbl'] ); ?>">
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 3. Cerita Kami & Filosofi Toko -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-format-quote" style="color: #4f46e5; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '3. Cerita Kami & Filosofi Brand', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Bagikan kisah awal mula berdiri, latar belakang, dan filosofi dasar toko plakat Anda:', 'tokoku' ); ?></p>

                            <div class="tokoku-settings-section" style="border-left: 3px solid #818cf8; margin-bottom: 20px;">
                                <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 12px;">
                                    <?php _e( 'Kartu Kutipan Filosofi (Sisi Kiri)', 'tokoku' ); ?>
                                </strong>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Kutipan', 'tokoku' ); ?></label>
                                    <textarea name="tokoku_about_quote_text" rows="2"><?php echo esc_textarea( get_theme_mod( 'tokoku_about_quote_text', 'Sebuah plakat bukan sekadar benda. Ia adalah pengingat bahwa kerja keras seseorang pernah dilihat, dihargai, dan dirayakan.' ) ); ?></textarea>
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Nama Penulis Kutipan / Label', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_quote_author" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_quote_author', '— Filosofi ' . $about_brand ) ); ?>">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Subjudul / Eyebrow Cerita', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_story_eyebrow" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_story_eyebrow', 'Cerita Kami' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Judul Bagian Cerita', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_story_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_story_title', 'Berawal dari Keyakinan Sederhana: Setiap Apresiasi Layak Istimewa' ) ); ?>">
                                </div>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Isi Paragraf Cerita Lengkap', 'tokoku' ); ?></label>
                                <textarea name="tokoku_about_story_content" rows="6" placeholder="Tuliskan cerita brand toko Anda..."><?php echo esc_textarea( get_theme_mod( 'tokoku_about_story_content', '' ) ); ?></textarea>
                                <p class="tokoku-tip"><?php _e( 'Jika dikosongkan, tema akan otomatis menggunakan teks narasi default 3 paragraf.', 'tokoku' ); ?></p>
                            </div>
                        </div>

                        <!-- 4. Nilai & Komitmen Layanan (Mengapa Memilih Kami) -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-heart" style="color: #4f46e5; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '4. Komitmen & Nilai Layanan (Mengapa Memilih Kami)', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Kelola 6 prinsip keunggulan yang membuat layanan plakat Anda menonjol:', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Subjudul / Eyebrow', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_values_eyebrow" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_values_eyebrow', 'Mengapa Memilih Kami' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Judul Bagian', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_values_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_values_title', 'Komitmen yang Kami Pegang di Setiap Pesanan' ) ); ?>">
                                </div>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Deskripsi Singkat di Bawah Judul', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_about_values_sub" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_values_sub', sprintf( 'Enam prinsip yang menjadikan setiap plakat dari %s layak dipercaya untuk momen paling penting Anda.', $about_brand ) ) ); ?>">
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 15px;">
                                <?php
                                $def_values = array(
                                    1 => array( 'icon' => 'target', 'title' => 'Presisi di Setiap Detail', 'text' => 'Setiap huruf, garis grafir, dan sudut potongan kami periksa berlapis. Karena sebuah penghargaan layak tampil sempurna.' ),
                                    2 => array( 'icon' => 'gem',    'title' => 'Material Pilihan',         'text' => 'Akrilik bening berkualitas, kayu solid, resin jernih, hingga logam premium — dipilih agar tahan lama dan tetap memukau.' ),
                                    3 => array( 'icon' => 'pen',    'title' => 'Desain yang Personal',     'text' => 'Tidak ada template asal jadi. Tim desain kami merancang setiap plakat agar mencerminkan identitas dan cerita Anda.' ),
                                    4 => array( 'icon' => 'zap',    'title' => 'Tepat Waktu',              'text' => 'Kami memahami bahwa acara tidak bisa ditunda. Jadwal produksi disusun realistis dan dikomunikasikan sejak awal.' ),
                                    5 => array( 'icon' => 'chat',   'title' => 'Komunikasi Responsif',     'text' => 'Konsultasi langsung melalui WhatsApp dengan tim yang ramah, sigap, dan siap memberi rekomendasi terbaik.' ),
                                    6 => array( 'icon' => 'tag',    'title' => 'Harga Transparan',         'text' => 'Rincian biaya disampaikan jelas di awal — tanpa biaya tersembunyi, tanpa kejutan di akhir.' ),
                                );
                                for ( $i = 1; $i <= 6; $i++ ) :
                                    $v_icon  = get_theme_mod( "tokoku_about_val_icon_{$i}", $def_values[$i]['icon'] );
                                    $v_title = get_theme_mod( "tokoku_about_val_title_{$i}", $def_values[$i]['title'] );
                                    $v_text  = get_theme_mod( "tokoku_about_val_text_{$i}", $def_values[$i]['text'] );
                                ?>
                                    <div class="tokoku-settings-section" style="border-left: 3px solid #6366f1;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <strong style="color: #0f172a; font-size: 0.9rem;">Nilai Keunggulan #<?php echo $i; ?></strong>
                                            <select name="tokoku_about_val_icon_<?php echo $i; ?>" style="font-size: 12px; font-weight: 600;">
                                                <?php foreach ( $icon_choices as $ikey => $ilabel ) : ?>
                                                    <option value="<?php echo esc_attr( $ikey ); ?>" <?php selected( $v_icon, $ikey ); ?>><?php echo esc_html( $ilabel ); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 8px;">
                                            <label style="font-size: 11px;"><?php _e( 'Judul Nilai', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_about_val_title_<?php echo $i; ?>" value="<?php echo esc_attr( $v_title ); ?>" placeholder="<?php echo esc_attr( $def_values[$i]['title'] ); ?>">
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 0;">
                                            <label style="font-size: 11px;"><?php _e( 'Penjelasan Singkat', 'tokoku' ); ?></label>
                                            <textarea name="tokoku_about_val_text_<?php echo $i; ?>" rows="2" placeholder="<?php echo esc_attr( $def_values[$i]['text'] ); ?>"><?php echo esc_textarea( $v_text ); ?></textarea>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 5. Alur Kerja (Cara Kami Bekerja) -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-randomize" style="color: #4f46e5; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '5. Alur & Proses Kerja (Cara Kami Bekerja)', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Kelola 4 tahapan alur pemesanan hingga barang tiba di tujuan:', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Subjudul / Eyebrow', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_steps_eyebrow" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_steps_eyebrow', 'Cara Kami Bekerja' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Judul Bagian Alur Kerja', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_steps_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_steps_title', 'Dari Ide Menjadi Karya dalam 4 Langkah Mudah' ) ); ?>">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <?php
                                $def_steps = array(
                                    1 => array( 'icon' => 'chat',   'title' => 'Konsultasi',        'text' => 'Ceritakan kebutuhan Anda: jenis plakat, jumlah, teks, logo, dan tanggal acara.' ),
                                    2 => array( 'icon' => 'layers', 'title' => 'Desain & Mockup',   'text' => 'Kami kirimkan pratinjau desain gratis dan siap direvisi hingga Anda benar-benar puas.' ),
                                    3 => array( 'icon' => 'award',  'title' => 'Produksi Presisi',  'text' => 'Setelah ACC, plakat diproduksi dengan mesin modern dan sentuhan akhir tangan terampil.' ),
                                    4 => array( 'icon' => 'truck',  'title' => 'Kirim dengan Aman', 'text' => 'Dikemas berlapis dan dikirim ke seluruh Indonesia dengan nomor resi yang bisa dilacak.' ),
                                );
                                for ( $i = 1; $i <= 4; $i++ ) :
                                    $st_icon  = get_theme_mod( "tokoku_about_step_icon_{$i}", $def_steps[$i]['icon'] );
                                    $st_title = get_theme_mod( "tokoku_about_step_title_{$i}", $def_steps[$i]['title'] );
                                    $st_text  = get_theme_mod( "tokoku_about_step_text_{$i}", $def_steps[$i]['text'] );
                                ?>
                                    <div class="tokoku-settings-section" style="border-left: 3px solid #6366f1;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <strong style="color: #0f172a; font-size: 0.9rem;">Langkah #<?php echo $i; ?></strong>
                                            <select name="tokoku_about_step_icon_<?php echo $i; ?>" style="font-size: 12px; font-weight: 600;">
                                                <?php foreach ( $icon_choices as $ikey => $ilabel ) : ?>
                                                    <option value="<?php echo esc_attr( $ikey ); ?>" <?php selected( $st_icon, $ikey ); ?>><?php echo esc_html( $ilabel ); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 8px;">
                                            <label style="font-size: 11px;"><?php _e( 'Judul Langkah', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_about_step_title_<?php echo $i; ?>" value="<?php echo esc_attr( $st_title ); ?>" placeholder="<?php echo esc_attr( $def_steps[$i]['title'] ); ?>">
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 0;">
                                            <label style="font-size: 11px;"><?php _e( 'Penjelasan Langkah', 'tokoku' ); ?></label>
                                            <textarea name="tokoku_about_step_text_<?php echo $i; ?>" rows="2" placeholder="<?php echo esc_attr( $def_steps[$i]['text'] ); ?>"><?php echo esc_textarea( $st_text ); ?></textarea>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 6. Visi & Misi Toko -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-visibility" style="color: #4f46e5; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '6. Visi & Misi Perusahaan', 'tokoku' ); ?>
                            </h3>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="tokoku-settings-section" style="border-left: 3px solid #4f46e5;">
                                    <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 12px;"><?php _e( 'Visi', 'tokoku' ); ?></strong>
                                    <div class="tokoku-field" style="margin-bottom: 8px;">
                                        <label><?php _e( 'Subjudul / Eyebrow Visi', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_about_visi_eyebrow" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_visi_eyebrow', 'Visi' ) ); ?>">
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 0;">
                                        <label><?php _e( 'Pernyataan Visi', 'tokoku' ); ?></label>
                                        <textarea name="tokoku_about_visi_text" rows="4"><?php echo esc_textarea( get_theme_mod( 'tokoku_about_visi_text', 'Menjadi destinasi utama pembuatan plakat dan penghargaan custom di Indonesia yang dikenal karena kualitas, kreativitas, dan pelayanan sepenuh hati.' ) ); ?></textarea>
                                    </div>
                                </div>

                                <div class="tokoku-settings-section" style="border-left: 3px solid #4f46e5;">
                                    <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 12px;"><?php _e( 'Misi', 'tokoku' ); ?></strong>
                                    <div class="tokoku-field" style="margin-bottom: 8px;">
                                        <label><?php _e( 'Subjudul / Eyebrow Misi', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_about_misi_eyebrow" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_misi_eyebrow', 'Misi' ) ); ?>">
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 0;">
                                        <label><?php _e( 'Poin-poin Misi (1 baris per poin)', 'tokoku' ); ?></label>
                                        <textarea name="tokoku_about_misi_points" rows="4" placeholder="Satu poin per baris..."><?php 
                                            $def_misi_text = "Menghadirkan produk penghargaan berkualitas tinggi dengan harga yang adil dan transparan.\nMemberikan pengalaman pemesanan yang mudah, cepat, dan menyenangkan dari mana saja.\nTerus berinovasi dalam desain, material, dan teknik produksi.\nMembangun hubungan jangka panjang yang dilandasi kepercayaan dengan setiap pelanggan.";
                                            echo esc_textarea( get_theme_mod( 'tokoku_about_misi_points', $def_misi_text ) ); 
                                        ?></textarea>
                                        <p class="tokoku-tip"><?php _e( 'Setiap baris baru otomatis dijadikan poin checklist dengan ikon centang.', 'tokoku' ); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 7. Mitra & Kategori Klien -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-businessman" style="color: #4f46e5; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '7. Mitra, Klien & Kategori Pelanggan', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Logo mitra klien otomatis menggunakan daftar logo dari tab "Ulasan & Klien". Di sini Anda dapat mengatur judul bagian dan daftar kategori pelanggan (chips):', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 16px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Subjudul / Eyebrow', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_clients_eyebrow" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_clients_eyebrow', 'Dipercaya Beragam Kalangan' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Judul Bagian Mitra', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_clients_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_clients_title', 'Partner & Klien Apresiasi Kami' ) ); ?>">
                                </div>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Deskripsi Singkat', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_about_clients_sub" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_clients_sub', 'Dipercaya oleh instansi pemerintah, BUMN, perguruan tinggi ternama, korporasi swasta, hingga ribuan komunitas di seluruh Indonesia.' ) ); ?>">
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Daftar Kategori Pelanggan (Chips, pisahkan dengan koma)', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_about_clients_chips" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_clients_chips', 'Instansi Pemerintah, BUMN & Korporasi, Sekolah & Universitas, Organisasi & Komunitas, Event Olahraga, Rumah Sakit & Klinik, Perbankan, Kado Personal' ) ); ?>">
                                <p class="tokoku-tip"><?php _e( 'Contoh: Instansi Pemerintah, BUMN & Korporasi, Sekolah & Universitas, Organisasi & Komunitas', 'tokoku' ); ?></p>
                            </div>
                        </div>

                        <!-- 8. Ajakan Bertindak (Bottom CTA) -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-megaphone" style="color: #4f46e5; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '8. Banner Ajakan Bertindak (Bottom CTA)', 'tokoku' ); ?>
                            </h3>

                            <div class="tokoku-field">
                                <label><?php _e( 'Judul Banner CTA', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_about_cta_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_cta_title', 'Siap Mewujudkan Plakat Penghargaan Anda?' ) ); ?>">
                            </div>
                            <div class="tokoku-field">
                                <label><?php _e( 'Subjudul / Keterangan Banner CTA', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_about_cta_sub" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_cta_sub', 'Konsultasikan kebutuhan Anda sekarang. Gratis desain dan mockup — Anda hanya membayar ketika sudah yakin.' ) ); ?>">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Tombol 1 (WhatsApp)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_cta_btn1_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_cta_btn1_text', 'Chat WhatsApp' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Tombol 2 (Hubungi Kami)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_about_cta_btn2_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_about_cta_btn2_text', 'Hubungi Kami' ) ); ?>">
                                </div>
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button( __( 'Simpan Perubahan Template Tentang Kami', 'tokoku' ) ); ?>
                    </div>

                    <!-- Tab: Template Kontak & Workshop -->
                    <div id="tab-contact" class="tokoku-tab-panel <?php echo ( 'tab-contact' === $active_tab_param ) ? 'active' : ''; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                            <div>
                                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #0f172a;"><?php _e( 'Template Halaman: Kontak Kami & Workshop', 'tokoku' ); ?></h2>
                                <p class="description" style="margin-top: 6px; font-size: 0.95rem;"><?php _e( 'Kelola seluruh isi dan susunan konten halaman Kontak: Hero & keunggulan, 3 kartu kontak (WhatsApp, Email, Jam Kerja), formulir WhatsApp interaktif, alamat fisik workshop & Google Maps, tim Customer Service (CRO), serta FAQ khusus dan ajakan bertindak (CTA).', 'tokoku' ); ?></p>
                            </div>
                            <div>
                                <a href="<?php echo esc_url( function_exists( 'tokoku_static_page_url' ) ? tokoku_static_page_url( 'page-templates/template-kontak.php' ) : home_url( '/kontak/' ) ); ?>" target="_blank" rel="noopener" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                                    <span class="dashicons dashicons-external" style="margin-top: 2px;"></span>
                                    <?php _e( 'Pratinjau Halaman Kontak', 'tokoku' ); ?>
                                </a>
                            </div>
                        </div>

                        <?php
                        $contact_brand = function_exists( 'tokoku_page_brand' ) ? tokoku_page_brand() : ( function_exists( 'tokoku_get_site_domain' ) ? tokoku_get_site_domain() : 'domain.com' );
                        ?>

                        <!-- 1. Hero & Keunggulan Halaman Kontak -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-cover-image" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '1. Bagian Hero & Keunggulan Kontak', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Sesuaikan judul utama, teks pengantar, serta 3 poin keunggulan respon cepat di bagian atas halaman Kontak.', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Badge Kecil Hero', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_hero_badge" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_hero_badge', 'Layanan Pelanggan' ) ); ?>" placeholder="Layanan Pelanggan">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Judul Utama Hero Kontak', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_hero_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_hero_title', 'Mari Bicarakan <span class="jp-text-gradient">Penghargaan Terbaik Anda</span>' ) ); ?>">
                                    <p class="tokoku-tip"><?php _e( 'Tip: Gunakan tag &lt;span class="jp-text-gradient"&gt;Teks&lt;/span&gt; untuk efek warna gradien modern.', 'tokoku' ); ?></p>
                                </div>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Deskripsi / Paragraf Pengantar Hero', 'tokoku' ); ?></label>
                                <textarea name="tokoku_contact_hero_desc" rows="3"><?php echo esc_textarea( get_theme_mod( 'tokoku_contact_hero_desc', sprintf( 'Punya pertanyaan, butuh konsultasi desain gratis, atau ingin meminta penawaran harga untuk instansi? Tim Customer Care %s siap mendengarkan dan mendampingi Anda dengan ramah, cepat, dan profesional.', $contact_brand ) ) ); ?></textarea>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Keunggulan 1 (Petir)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_chip_1" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_chip_1', 'Respons Cepat WhatsApp' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Keunggulan 2 (Perisai)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_chip_2" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_chip_2', 'Konsultasi & Mockup Gratis' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Keunggulan 3 (Truk)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_chip_3" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_chip_3', 'Pengiriman Seluruh Indonesia' ) ); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- 2. Tiga Kartu Kontak Utama -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-id" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '2. Tiga Kartu Kontak Utama (WhatsApp, Email & Jam Operasional)', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Sesuaikan badge, judul, penjelasan dan teks tombol pada 3 kartu kontak yang tampil menonjol di halaman Kontak:', 'tokoku' ); ?></p>

                            <!-- Card 1: WhatsApp -->
                            <div class="tokoku-settings-section" style="border-left: 3px solid #16a34a; margin-bottom: 16px;">
                                <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 10px;">
                                    <span class="dashicons dashicons-whatsapp" style="color: #16a34a; margin-right: 4px;"></span>
                                    <?php _e( 'Kartu 1: WhatsApp Hotline (Unggulan)', 'tokoku' ); ?>
                                </strong>
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 10px;">
                                    <div>
                                        <label style="font-size: 11px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;"><?php _e( 'Badge Kartu', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_contact_card1_badge" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card1_badge', 'Respons Paling Cepat' ) ); ?>" style="width: 100%;">
                                    </div>
                                    <div>
                                        <label style="font-size: 11px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;"><?php _e( 'Judul Kartu', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_contact_card1_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card1_title', 'WhatsApp Hotline' ) ); ?>" style="width: 100%;">
                                    </div>
                                    <div>
                                        <label style="font-size: 11px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;"><?php _e( 'Teks Tombol Aksi', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_contact_card1_btn" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card1_btn', 'Chat WhatsApp Sekarang' ) ); ?>" style="width: 100%;">
                                    </div>
                                </div>
                                <div class="tokoku-field" style="margin-bottom: 0;">
                                    <label style="font-size: 11px;"><?php _e( 'Penjelasan Kartu', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_card1_desc" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card1_desc', 'Konsultasi langsung, kirim file logo, dan terima mockup desain visual dalam hitungan menit.' ) ); ?>">
                                </div>
                            </div>

                            <!-- Card 2: Email Resmi -->
                            <div class="tokoku-settings-section" style="border-left: 3px solid #0284c7; margin-bottom: 16px;">
                                <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 10px;">
                                    <span class="dashicons dashicons-email" style="color: #0284c7; margin-right: 4px;"></span>
                                    <?php _e( 'Kartu 2: Email Resmi & Hotline Telepon', 'tokoku' ); ?>
                                </strong>
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 10px;">
                                    <div>
                                        <label style="font-size: 11px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;"><?php _e( 'Badge Kartu', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_contact_card2_badge" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card2_badge', 'Instansi & Tender' ) ); ?>" style="width: 100%;">
                                    </div>
                                    <div>
                                        <label style="font-size: 11px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;"><?php _e( 'Judul Kartu', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_contact_card2_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card2_title', 'Email Resmi' ) ); ?>" style="width: 100%;">
                                    </div>
                                    <div>
                                        <label style="font-size: 11px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;"><?php _e( 'Teks Tombol Aksi', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_contact_card2_btn" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card2_btn', 'Kirim Email' ) ); ?>" style="width: 100%;">
                                    </div>
                                </div>
                                <div class="tokoku-field" style="margin-bottom: 0;">
                                    <label style="font-size: 11px;"><?php _e( 'Penjelasan Kartu', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_card2_desc" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card2_desc', 'Kirim berkas penawaran, dokumen tender, PO perusahaan, atau lampiran file desain berukuran besar.' ) ); ?>">
                                </div>
                            </div>

                            <!-- Card 3: Jam Operasional -->
                            <div class="tokoku-settings-section" style="border-left: 3px solid #d97706;">
                                <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 10px;">
                                    <span class="dashicons dashicons-clock" style="color: #d97706; margin-right: 4px;"></span>
                                    <?php _e( 'Kartu 3: Jam Kerja & Pelayanan', 'tokoku' ); ?>
                                </strong>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px;">
                                    <div>
                                        <label style="font-size: 11px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;"><?php _e( 'Badge Kartu', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_contact_card3_badge" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card3_badge', 'Pelayanan Prima' ) ); ?>" style="width: 100%;">
                                    </div>
                                    <div>
                                        <label style="font-size: 11px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px;"><?php _e( 'Judul Kartu', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_contact_card3_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card3_title', 'Jam Operasional' ) ); ?>" style="width: 100%;">
                                    </div>
                                </div>
                                <div class="tokoku-field" style="margin-bottom: 8px;">
                                    <label style="font-size: 11px;"><?php _e( 'Penjelasan Pengantar Jam Kerja', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_card3_desc" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card3_desc', 'Tim kami aktif melayani konsultasi dan proses produksi pada jam kerja berikut:' ) ); ?>">
                                </div>
                                <div class="tokoku-field" style="margin-bottom: 0;">
                                    <label style="font-size: 11px;"><?php _e( 'Catatan Tambahan di Bawah Jadwal', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_card3_note" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_card3_note', 'Pesan di luar jam kerja tetap dapat dikirim dan akan dibalas pada kesempatan pertama.' ) ); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Formulir Interaktif WhatsApp -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-forms" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '3. Formulir Interaktif Pembuat Pesan WhatsApp', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Sesuaikan judul, instruksi, dan teks tombol pada form pesan otomatis yang terhubung ke WhatsApp CS:', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Subjudul / Eyebrow Form', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_form_eyebrow" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_form_eyebrow', 'Formulir Interaktif' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Judul Form Pemesanan', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_form_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_form_title', 'Kirim Rincian Kebutuhan Anda' ) ); ?>">
                                </div>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Deskripsi Pengantar Form', 'tokoku' ); ?></label>
                                <textarea name="tokoku_contact_form_sub" rows="2"><?php echo esc_textarea( get_theme_mod( 'tokoku_contact_form_sub', 'Isi formulir ringkas di bawah. Sistem kami akan merangkumnya secara otomatis menjadi format pesan rapi yang langsung terkirim ke WhatsApp Customer Service kami.' ) ); ?></textarea>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Tombol Kirim ke WhatsApp', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_form_btn" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_form_btn', 'Kirim ke WhatsApp Customer Service' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Disclaimer Keamanan Data', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_form_disclaimer" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_form_disclaimer', 'Informasi Anda aman dan hanya digunakan untuk keperluan konsultasi serta proses pesanan plakat.' ) ); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- 4. Alamat Fisik & Peta Workshop -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-location" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '4. Lokasi & Alamat Fisik Workshop / Kantor', 'tokoku' ); ?>
                            </h3>

                            <div class="tokoku-field">
                                <label><?php _e( 'Alamat Lengkap Workshop & Kantor', 'tokoku' ); ?></label>
                                <textarea name="tokoku_store_address" rows="3" placeholder="Contoh: Jl. Percetakan Negara No. 12, Johar Baru, Jakarta Pusat 10560"><?php echo esc_textarea( get_theme_mod( 'tokoku_store_address' ) ); ?></textarea>
                                <p class="tokoku-tip"><?php _e( 'Tampilkan alamat fisik lengkap workshop untuk meningkatkan kredibilitas toko di mata pelanggan instansi, korporat, dan Google Local SEO.', 'tokoku' ); ?></p>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'URL Google Maps / Titik Lokasi Peta', 'tokoku' ); ?></label>
                                <input type="url" name="tokoku_store_maps_url" value="<?php echo esc_url( get_theme_mod( 'tokoku_store_maps_url' ) ); ?>" placeholder="Contoh: https://maps.app.goo.gl/... atau https://maps.google.com/?q=...">
                                <p class="tokoku-tip"><?php _e( 'Tempelkan link share Google Maps workshop Anda agar tombol "Buka di Google Maps" di website langsung membuka aplikasi navigasi pelanggan.', 'tokoku' ); ?></p>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Nomor Telepon Kantor / Hotline', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_store_phone" value="<?php echo esc_attr( get_theme_mod( 'tokoku_store_phone' ) ); ?>" placeholder="Contoh: (021) 1234567 atau 0812-3456-7890">
                                    <p class="tokoku-tip"><?php _e( 'Telepon kantor resmi untuk kebutuhan verifikasi pesanan instansi dan administrasi.', 'tokoku' ); ?></p>
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Email Resmi Toko', 'tokoku' ); ?></label>
                                    <input type="email" name="tokoku_store_email" value="<?php echo esc_attr( get_theme_mod( 'tokoku_store_email' ) ); ?>" placeholder="Contoh: halo@<?php echo esc_attr( function_exists( 'tokoku_get_site_domain' ) ? tokoku_get_site_domain() : 'domain.com' ); ?>">
                                    <p class="tokoku-tip"><?php _e( 'Email untuk menerima berkas PO tender, invoice resmi, dan surat penawaran harga.', 'tokoku' ); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Jam Operasional -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-clock" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '5. Jadwal & Jam Operasional Pelayanan', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Tuliskan jadwal kerja proses produksi dan jam aktif konsultasi pelanggan:', 'tokoku' ); ?></p>

                            <div class="tokoku-field">
                                <label><?php _e( 'Hari Kerja (Senin – Jumat)', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_jam_op_1" value="<?php echo esc_attr( get_theme_mod( 'tokoku_jam_op_1', 'Senin – Jumat: 08.00 – 17.00 WIB' ) ); ?>" placeholder="Senin – Jumat: 08.00 – 17.00 WIB">
                            </div>
                            <div class="tokoku-field">
                                <label><?php _e( 'Akhir Pekan (Sabtu)', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_jam_op_2" value="<?php echo esc_attr( get_theme_mod( 'tokoku_jam_op_2', 'Sabtu: 08.00 – 15.00 WIB' ) ); ?>" placeholder="Sabtu: 08.00 – 15.00 WIB">
                            </div>
                            <div class="tokoku-field">
                                <label><?php _e( 'Hari Libur / Tanggal Merah', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_jam_op_3" value="<?php echo esc_attr( get_theme_mod( 'tokoku_jam_op_3', 'Minggu & Libur Nasional: Tutup (Konsultasi via WA tetap diterima)' ) ); ?>" placeholder="Minggu & Libur Nasional: Tutup">
                            </div>
                        </div>

                        <!-- 6. Tim Customer Relation Officer (CRO) -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-groups" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '6. Tim Customer Relation Officer (CRO) & Spesialisasi Layanan', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 20px;"><?php _e( 'Daftarkan hingga 5 petugas layanan pelanggan lengkap dengan nomor WhatsApp dan spesialisasi produk agar calon pembeli dapat diarahkan ke tim yang paling tepat:', 'tokoku' ); ?></p>

                            <div class="tokoku-field">
                                <label><?php _e( 'Teks Pengantar CRO', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_hubungi_kami_desc" value="<?php echo esc_attr( get_theme_mod( 'tokoku_hubungi_kami_desc', 'Customer Relation Officer (CRO) kami siap membantu Anda dengan ramah dan cepat.' ) ); ?>">
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 15px;">
                                <?php for ( $i = 1; $i <= 5; $i++ ) : 
                                    $default_names = array( 1 => 'Customer Care 1', 2 => 'Customer Care 2', 3 => 'Customer Care 3', 4 => 'Customer Care 4', 5 => 'Customer Care 5' );
                                    $default_roles = array( 1 => 'Konsultasi Plakat Akrilik & Mockup Cepat', 2 => 'Pemesanan Instansi & Tender B2B', 3 => 'Medali, Trophy & Piala Kejuaraan', 4 => 'Souvenir & Merchandise Custom', 5 => 'Layanan Pelanggan Umum' );
                                    $val_name = get_theme_mod( "tokoku_contact_name_{$i}", 1 === $i ? 'Customer Care 1' : '' );
                                    $val_wa   = get_theme_mod( "tokoku_contact_wa_{$i}", 1 === $i ? get_theme_mod( 'tokoku_wa_number', '6281234567890' ) : '' );
                                    $val_role = get_theme_mod( "tokoku_contact_role_{$i}", 1 === $i ? 'Konsultasi Desain & Plakat Akrilik' : '' );
                                ?>
                                    <div class="tokoku-settings-section" style="border-left: 3px solid #0d9488;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                            <strong style="color: #0f172a; font-size: 0.95rem;">
                                                <span class="dashicons dashicons-businessman" style="color: #0d9488; margin-right: 4px;"></span>
                                                Petugas CRO #<?php echo $i; ?>
                                            </strong>
                                            <?php if ( ! empty( $val_wa ) ) : 
                                                $wa_digits = preg_replace( '/\D+/', '', $val_wa );
                                            ?>
                                                <a href="https://wa.me/<?php echo esc_attr( $wa_digits ); ?>" target="_blank" rel="noopener" style="font-size: 12px; color: #16a34a; text-decoration: none; font-weight: 600;">
                                                    <span class="dashicons dashicons-whatsapp" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span> <?php _e( 'Uji Chat WA', 'tokoku' ); ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 12px;">
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;"><?php _e( 'Nama Petugas', 'tokoku' ); ?></label>
                                                <input type="text" name="tokoku_contact_name_<?php echo $i; ?>" placeholder="<?php echo esc_attr( $default_names[$i] ); ?>" value="<?php echo esc_attr( $val_name ); ?>" style="width: 100%;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;"><?php _e( 'Nomor WhatsApp (628...)', 'tokoku' ); ?></label>
                                                <input type="text" name="tokoku_contact_wa_<?php echo $i; ?>" placeholder="6281234567890" value="<?php echo esc_attr( $val_wa ); ?>" style="width: 100%;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;"><?php _e( 'Peran / Spesialisasi Produk', 'tokoku' ); ?></label>
                                                <input type="text" name="tokoku_contact_role_<?php echo $i; ?>" placeholder="<?php echo esc_attr( $default_roles[$i] ); ?>" value="<?php echo esc_attr( $val_role ); ?>" style="width: 100%;">
                                            </div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 7. FAQ Khusus Halaman Kontak -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-editor-help" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '7. Tanya Jawab (FAQ) Khusus Halaman Kontak', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;"><?php _e( 'Kelola 5 pertanyaan umum yang sering ditanyakan calon pembeli sebelum memesan plakat:', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 16px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Subjudul / Eyebrow FAQ', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_faq_eyebrow" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_faq_eyebrow', 'Pertanyaan Sering Diajukan' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Judul Bagian FAQ', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_faq_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_faq_title', 'Hal yang Sering Ditanyakan Sebelum Memesan' ) ); ?>">
                                </div>
                            </div>

                            <div class="tokoku-field" style="margin-bottom: 20px;">
                                <label><?php _e( 'Deskripsi Singkat di Bawah Judul', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_contact_faq_sub" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_faq_sub', sprintf( 'Jawaban cepat untuk pertanyaan paling umum seputar pemesanan plakat di %s.', $contact_brand ) ) ); ?>">
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 16px;">
                                <?php
                                $def_cfaqs = array(
                                    1 => array(
                                        'q' => 'Berapa lama proses pembuatan plakat custom?',
                                        'a' => 'Rata-rata pengerjaan berkisar antara 1 hingga 3 hari kerja setelah mockup desain disetujui (ACC) dan pembayaran terkonfirmasi. Untuk kebutuhan mendesak (urgent/ekspres), silakan konsultasikan dengan tim kami agar dapat dijadwalkan secara prioritas.',
                                    ),
                                    2 => array(
                                        'q' => 'Apakah bisa membuat plakat dengan desain sendiri atau tanpa desain?',
                                        'a' => 'Tentu saja! Jika Anda sudah memiliki file desain (AI, CorelDraw, PDF, atau PNG beresolusi tinggi), Anda dapat langsung mengirimkannya. Jika belum memiliki desain, tim desainer kreatif kami siap membuatkan mockup gratis sesuai konsep, logo, dan tulisan yang Anda inginkan.',
                                    ),
                                    3 => array(
                                        'q' => 'Apakah ada jumlah minimal pemesanan (MOQ)?',
                                        'a' => 'Kami melayani pemesanan mulai dari 1 buah (satuan) untuk kado personal atau penghargaan khusus, hingga ribuan buah untuk kebutuhan wisuda, turnamen olahraga, dan corporate event besar dengan harga grosir kompetitif.',
                                    ),
                                    4 => array(
                                        'q' => 'Bagaimana pengemasan dan keamanan pengiriman ke luar kota?',
                                        'a' => 'Setiap produk kami lindungi dengan lapisan bubble wrap tebal, busa pelindung khusus, dan kardus kokoh atau packing kayu ekstra. Kami bermitra dengan ekspedisi tepercaya (JNE, J&T, SiCepat, Cargo, dsb.) lengkap dengan nomor resi pelacakan dan opsi asuransi pengiriman.',
                                    ),
                                    5 => array(
                                        'q' => 'Bagaimana cara mendapatkan surat penawaran harga resmi (invoice/quotation)?',
                                        'a' => 'Cukup kirimkan detail pesanan (jenis produk, estimasi jumlah, spesifikasi, dan nama instansi) melalui formulir kontak di bawah atau via WhatsApp. Tim administrasi kami akan menerbitkan invoice / surat penawaran resmi bertanda tangan.',
                                    ),
                                );
                                for ( $i = 1; $i <= 5; $i++ ) :
                                    $faq_q = get_theme_mod( "tokoku_contact_faq_q_{$i}", $def_cfaqs[$i]['q'] );
                                    $faq_a = get_theme_mod( "tokoku_contact_faq_a_{$i}", $def_cfaqs[$i]['a'] );
                                ?>
                                    <div class="tokoku-settings-section" style="border-left: 3px solid #0d9488;">
                                        <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin-bottom: 8px;">
                                            FAQ #<?php echo $i; ?>
                                        </strong>
                                        <div class="tokoku-field" style="margin-bottom: 8px;">
                                            <label style="font-size: 11px;"><?php _e( 'Pertanyaan', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_contact_faq_q_<?php echo $i; ?>" value="<?php echo esc_attr( $faq_q ); ?>" placeholder="<?php echo esc_attr( $def_cfaqs[$i]['q'] ); ?>">
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 0;">
                                            <label style="font-size: 11px;"><?php _e( 'Jawaban', 'tokoku' ); ?></label>
                                            <textarea name="tokoku_contact_faq_a_<?php echo $i; ?>" rows="3" placeholder="<?php echo esc_attr( $def_cfaqs[$i]['a'] ); ?>"><?php echo esc_textarea( $faq_a ); ?></textarea>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 8. Ajakan Bertindak (Bottom CTA) -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-megaphone" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( '8. Banner Ajakan Bertindak (Bottom CTA)', 'tokoku' ); ?>
                            </h3>

                            <div class="tokoku-field">
                                <label><?php _e( 'Judul Banner CTA', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_contact_cta_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_cta_title', 'Sudah Punya Tanggal Acara?' ) ); ?>">
                            </div>
                            <div class="tokoku-field">
                                <label><?php _e( 'Subjudul / Keterangan Banner CTA', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_contact_cta_sub" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_cta_sub', 'Jangan tunda hingga hari mendekati acara. Hubungi kami sekarang untuk memastikan antrean produksi Anda siap tepat pada waktunya.' ) ); ?>">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Tombol 1 (WhatsApp)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_cta_btn1_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_cta_btn1_text', 'Hubungi via WhatsApp' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Tombol 2 (Profil Kami)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_contact_cta_btn2_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_contact_cta_btn2_text', 'Pelajari Profil Kami' ) ); ?>">
                                </div>
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button( __( 'Simpan Perubahan Template Kontak & Workshop', 'tokoku' ) ); ?>
                    </div>

                    <!-- Tab: Single Product (Lead Time & Trust Badges) -->
                    <div id="tab-single-product" class="tokoku-tab-panel <?php echo ( 'tab-single-product' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Halaman Produk & Keunggulan', 'tokoku' ); ?></h2>
                        <p class="description" style="margin-bottom:25px;"><?php _e( 'Kelola tampilan Slider Galeri Foto, Estimasi Pengerjaan (Lead Time Bar), dan Badge Keunggulan Toko (Trust Badges) yang muncul di halaman produk.', 'tokoku' ); ?></p>

                        <!-- Social Proof & Star Rating Display -->
                        <div class="tokoku-settings-group" style="margin-bottom: 35px; padding: 24px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                                <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; font-size: 1.15rem; color: #0f172a;">
                                    <span class="dashicons dashicons-star-filled" style="color: #f59e0b; font-size: 22px; width: 22px; height: 22px;"></span>
                                    <?php _e( 'Badge Rating Bintang & Ulasan (Social Proof)', 'tokoku' ); ?>
                                </h3>
                                <div>
                                    <select name="tokoku_enable_product_rating" style="font-weight: 700;">
                                        <option value="yes" <?php selected( get_theme_mod( 'tokoku_enable_product_rating', 'yes' ), 'yes' ); ?>><?php _e( 'Tampilkan (Rekomendasi)', 'tokoku' ); ?></option>
                                        <option value="no" <?php selected( get_theme_mod( 'tokoku_enable_product_rating', 'yes' ), 'no' ); ?>><?php _e( 'Sembunyikan', 'tokoku' ); ?></option>
                                    </select>
                                </div>
                            </div>
                            <p class="tokoku-tip" style="margin-top: 10px; margin-bottom: 0;"><?php _e( 'Menampilkan badge rating bintang interaktif dan jumlah ulasan terverifikasi tepat di bawah judul produk. Memperkuat kepercayaan calon pelanggan dan menyelaraskan konten halaman dengan Schema Google Rich Snippets.', 'tokoku' ); ?></p>
                        </div>

                        <!-- Product Gallery Slider Settings -->
                        <div class="tokoku-settings-group" style="margin-bottom: 35px; padding: 24px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                                <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; font-size: 1.15rem; color: #0f172a;">
                                    <span class="dashicons dashicons-images-alt2" style="color: #007bff; font-size: 22px; width: 22px; height: 22px;"></span>
                                    <?php _e( 'Galeri & Slider Foto Produk', 'tokoku' ); ?>
                                </h3>
                                <div>
                                    <select name="tokoku_product_slider_autoplay" style="font-weight: 700;">
                                        <option value="yes" <?php selected( get_theme_mod( 'tokoku_product_slider_autoplay', 'yes' ), 'yes' ); ?>><?php _e( 'Slide Otomatis (Aktif)', 'tokoku' ); ?></option>
                                        <option value="no" <?php selected( get_theme_mod( 'tokoku_product_slider_autoplay', 'yes' ), 'no' ); ?>><?php _e( 'Manual (Nonaktif)', 'tokoku' ); ?></option>
                                    </select>
                                </div>
                            </div>
                            <p class="tokoku-tip" style="margin-bottom: 16px;"><?php _e( 'Jika produk memiliki lebih dari 1 foto/galeri, gambar produk akan berganti secara otomatis. Slider otomatis dijeda sementara saat kursor diarahkan atau layar disentuh agar pembeli nyaman mengamati detail produk.', 'tokoku' ); ?></p>

                            <div class="tokoku-field" style="max-width: 320px; margin-bottom: 0;">
                                <label><?php _e( 'Kecepatan / Durasi Berganti Slide', 'tokoku' ); ?></label>
                                <?php $ps_delay = (int) get_theme_mod( 'tokoku_product_slider_delay', 4 ); ?>
                                <select name="tokoku_product_slider_delay">
                                    <option value="3" <?php selected( $ps_delay, 3 ); ?>><?php _e( '3 Detik (Cepat)', 'tokoku' ); ?></option>
                                    <option value="4" <?php selected( $ps_delay, 4 ); ?>><?php _e( '4 Detik (Standar Rekomendasi)', 'tokoku' ); ?></option>
                                    <option value="5" <?php selected( $ps_delay, 5 ); ?>><?php _e( '5 Detik (Sedang)', 'tokoku' ); ?></option>
                                    <option value="6" <?php selected( $ps_delay, 6 ); ?>><?php _e( '6 Detik (Lambat)', 'tokoku' ); ?></option>
                                    <option value="8" <?php selected( $ps_delay, 8 ); ?>><?php _e( '8 Detik (Sangat Lambat)', 'tokoku' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <!-- Lead Time Bar Settings -->
                        <div class="tokoku-settings-group" style="margin-bottom: 35px; padding: 24px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                                <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; font-size: 1.15rem; color: #0f172a;">
                                    <span class="dashicons dashicons-clock" style="color: #007bff; font-size: 22px; width: 22px; height: 22px;"></span>
                                    <?php _e( 'Bar Estimasi Pengerjaan (Lead Time Bar)', 'tokoku' ); ?>
                                </h3>
                                <div>
                                    <select name="tokoku_enable_lead_time" style="font-weight: 700;">
                                        <option value="yes" <?php selected( get_theme_mod( 'tokoku_enable_lead_time', 'yes' ), 'yes' ); ?>><?php _e( 'Tampilkan', 'tokoku' ); ?></option>
                                        <option value="no" <?php selected( get_theme_mod( 'tokoku_enable_lead_time', 'yes' ), 'no' ); ?>><?php _e( 'Sembunyikan', 'tokoku' ); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Pilihan Preset Ikon', 'tokoku' ); ?></label>
                                <?php $lt_icon = get_theme_mod( 'tokoku_lead_time_icon', 'clock' ); ?>
                                <select name="tokoku_lead_time_icon">
                                    <option value="clock" <?php selected( $lt_icon, 'clock' ); ?>><?php _e( '🕒 Jam / Waktu (Default)', 'tokoku' ); ?></option>
                                    <option value="lightning" <?php selected( $lt_icon, 'lightning' ); ?>><?php _e( '⚡ Kilat / Cepat', 'tokoku' ); ?></option>
                                    <option value="truck" <?php selected( $lt_icon, 'truck' ); ?>><?php _e( '🚚 Truk / Ekspedisi Cepat', 'tokoku' ); ?></option>
                                    <option value="calendar" <?php selected( $lt_icon, 'calendar' ); ?>><?php _e( '📅 Kalender / Hari Kerja', 'tokoku' ); ?></option>
                                    <option value="shield" <?php selected( $lt_icon, 'shield' ); ?>><?php _e( '🛡️ Perisai / Jaminan Tepat Waktu', 'tokoku' ); ?></option>
                                    <option value="award" <?php selected( $lt_icon, 'award' ); ?>><?php _e( '🏆 Penghargaan / Terpercaya', 'tokoku' ); ?></option>
                                    <option value="star" <?php selected( $lt_icon, 'star' ); ?>><?php _e( '⭐ Bintang / Prioritas', 'tokoku' ); ?></option>
                                    <option value="custom" <?php selected( $lt_icon, 'custom' ); ?>><?php _e( '🎨 Custom (Gunakan Upload Gambar atau Kode SVG di Bawah)', 'tokoku' ); ?></option>
                                </select>
                                <p class="tokoku-tip"><?php _e( 'Pilih ikon preset siap pakai untuk bar estimasi pengerjaan produk.', 'tokoku' ); ?></p>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Upload Gambar Ikon Kustom (Opsional)', 'tokoku' ); ?></label>
                                <div class="tokoku-media-upload">
                                    <img src="<?php echo esc_url( get_theme_mod( 'tokoku_lead_time_icon_img' ) ); ?>" class="tokoku-preview-img" style="max-height: 48px; <?php echo get_theme_mod( 'tokoku_lead_time_icon_img' ) ? '' : 'display:none;'; ?>">
                                    <input type="hidden" name="tokoku_lead_time_icon_img" value="<?php echo esc_attr( get_theme_mod( 'tokoku_lead_time_icon_img' ) ); ?>">
                                    <button type="button" class="button tokoku-upload-btn"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Ikon Gambar', 'tokoku' ); ?></span></button>
                                    <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( 'tokoku_lead_time_icon_img' ) ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                                </div>
                                <p class="tokoku-tip"><?php _e( 'Jika diunggah, gambar ikon ini akan menggantikan ikon preset.', 'tokoku' ); ?></p>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Kode SVG Kustom (Opsional)', 'tokoku' ); ?></label>
                                <textarea name="tokoku_lead_time_icon_svg" rows="3" placeholder="<svg ...>...</svg>"><?php echo esc_textarea( get_theme_mod( 'tokoku_lead_time_icon_svg' ) ); ?></textarea>
                                <p class="tokoku-tip"><?php _e( 'Masukkan kode SVG jika Anda ingin menggunakan ikon vektor kustom sendiri.', 'tokoku' ); ?></p>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Label Teks', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_lead_time_label" value="<?php echo esc_attr( get_theme_mod( 'tokoku_lead_time_label', 'Estimasi Pengerjaan:' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Nilai / Durasi (Teks Tebal)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_lead_time_val" value="<?php echo esc_attr( get_theme_mod( 'tokoku_lead_time_val', '2 – 3 Hari Kerja' ) ); ?>">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Keterangan Tambahan (Subteks)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_lead_time_sub" value="<?php echo esc_attr( get_theme_mod( 'tokoku_lead_time_sub', '(Tergantung Qty & Desain)' ) ); ?>">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Chip / Badge Kanan', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_lead_time_chip" value="<?php echo esc_attr( get_theme_mod( 'tokoku_lead_time_chip', 'Siap Kirim Cepat' ) ); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Stock Status Notice Settings -->
                        <div class="tokoku-settings-group" style="margin-bottom: 35px; padding: 24px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                                <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; font-size: 1.15rem; color: #0f172a;">
                                    <span class="dashicons dashicons-bell" style="color: #f59e0b; font-size: 22px; width: 22px; height: 22px;"></span>
                                    <?php _e( 'Notice Status Stok Produk', 'tokoku' ); ?>
                                </h3>
                                <div>
                                    <select name="tokoku_enable_stock_notice" style="font-weight: 700;">
                                        <option value="yes" <?php selected( get_theme_mod( 'tokoku_enable_stock_notice', 'yes' ), 'yes' ); ?>><?php _e( 'Tampilkan', 'tokoku' ); ?></option>
                                        <option value="no" <?php selected( get_theme_mod( 'tokoku_enable_stock_notice', 'yes' ), 'no' ); ?>><?php _e( 'Sembunyikan', 'tokoku' ); ?></option>
                                    </select>
                                </div>
                            </div>
                            <p class="tokoku-tip" style="margin-bottom: 20px;"><?php _e( 'Kustomisasi judul dan keterangan teks notice status stok (Tersedia, Habis, dan Pre Order) yang tampil di bawah catatan produk.', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
                                <!-- 1. Stok Tersedia -->
                                <div class="tokoku-product-badge-card" style="background: #ffffff; border: 1.5px solid #10b981; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(16,185,129,0.06);">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                                        <strong style="color: #059669; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                            <span style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; background:#ecfdf5; color:#059669; font-size:12px; font-weight:800;">✓</span>
                                            <?php _e( 'Notice: Stok Tersedia', 'tokoku' ); ?>
                                        </strong>
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 14px;">
                                        <label style="font-size: 13px;"><?php _e( 'Judul Notice', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_notice_tersedia_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_notice_tersedia_title', 'STOK TERSEDIA' ) ); ?>">
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 0;">
                                        <label style="font-size: 13px;"><?php _e( 'Keterangan Teks Notice', 'tokoku' ); ?></label>
                                        <textarea name="tokoku_notice_tersedia_desc" rows="3" style="font-size: 13px; line-height: 1.45;"><?php echo esc_textarea( get_theme_mod( 'tokoku_notice_tersedia_desc', 'Produk ini tersedia dan siap untuk dipesan sekarang.' ) ); ?></textarea>
                                    </div>
                                </div>

                                <!-- 2. Stok Habis -->
                                <div class="tokoku-product-badge-card" style="background: #ffffff; border: 1.5px solid #ef4444; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(239,68,68,0.06);">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                                        <strong style="color: #dc2626; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                            <span style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; background:#fef2f2; color:#dc2626; font-size:12px; font-weight:800;">✕</span>
                                            <?php _e( 'Notice: Stok Habis', 'tokoku' ); ?>
                                        </strong>
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 14px;">
                                        <label style="font-size: 13px;"><?php _e( 'Judul Notice', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_notice_habis_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_notice_habis_title', 'STOK HABIS' ) ); ?>">
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 0;">
                                        <label style="font-size: 13px;"><?php _e( 'Keterangan Teks Notice', 'tokoku' ); ?></label>
                                        <textarea name="tokoku_notice_habis_desc" rows="3" style="font-size: 13px; line-height: 1.45;"><?php echo esc_textarea( get_theme_mod( 'tokoku_notice_habis_desc', 'Produk ini sedang tidak tersedia. Hubungi kami untuk informasi ketersediaan berikutnya.' ) ); ?></textarea>
                                    </div>
                                </div>

                                <!-- 3. Pre Order -->
                                <div class="tokoku-product-badge-card" style="background: #ffffff; border: 1.5px solid #f59e0b; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(245,158,11,0.06);">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                                        <strong style="color: #d97706; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                            <span style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; background:#fffbeb; color:#d97706; font-size:12px; font-weight:800;">⏳</span>
                                            <?php _e( 'Notice: Pre Order', 'tokoku' ); ?>
                                        </strong>
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 14px;">
                                        <label style="font-size: 13px;"><?php _e( 'Judul Notice', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_notice_preorder_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_notice_preorder_title', 'PRE ORDER' ) ); ?>">
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 0;">
                                        <label style="font-size: 13px;"><?php _e( 'Keterangan Teks Notice', 'tokoku' ); ?></label>
                                        <textarea name="tokoku_notice_preorder_desc" rows="3" style="font-size: 13px; line-height: 1.45;"><?php echo esc_textarea( get_theme_mod( 'tokoku_notice_preorder_desc', 'Hubungi kami untuk informasi lebih lanjut mengenai pemesanan produk ini.' ) ); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trust Badges Settings -->
                        <div class="tokoku-settings-group" style="padding: 24px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                                <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; font-size: 1.15rem; color: #0f172a;">
                                    <span class="dashicons dashicons-shield" style="color: #22c55e; font-size: 22px; width: 22px; height: 22px;"></span>
                                    <?php _e( 'Trust Badges (Keunggulan Toko)', 'tokoku' ); ?>
                                </h3>
                                <div>
                                    <select name="tokoku_enable_trust_badges" style="font-weight: 700;">
                                        <option value="yes" <?php selected( get_theme_mod( 'tokoku_enable_trust_badges', 'yes' ), 'yes' ); ?>><?php _e( 'Tampilkan', 'tokoku' ); ?></option>
                                        <option value="no" <?php selected( get_theme_mod( 'tokoku_enable_trust_badges', 'yes' ), 'no' ); ?>><?php _e( 'Sembunyikan', 'tokoku' ); ?></option>
                                    </select>
                                </div>
                            </div>
                            <p class="tokoku-tip" style="margin-bottom: 20px;"><?php _e( 'Trust Badges menampilkan 4 poin keunggulan utama toko Anda di bawah galeri foto (desktop) atau sebelum marketplace (mobile) untuk meningkatkan kepercayaan pembeli.', 'tokoku' ); ?></p>

                            <?php
                            $badge_defaults = array(
                                1 => array( 'preset' => 'design',    'title' => 'Gratis Preview Desain',     'desc' => 'Konsultasi & revisi sebelum cetak' ),
                                2 => array( 'preset' => 'shield',    'title' => 'Garansi Pengiriman Aman',   'desc' => 'Ganti baru jika barang rusak/pecah' ),
                                3 => array( 'preset' => 'lightning', 'title' => 'Pengerjaan Presisi & Cepat','desc' => 'Tepat waktu untuk deadline acara' ),
                                4 => array( 'preset' => 'craftsman', 'title' => 'Tangan Pertama Pengrajin',  'desc' => 'Kualitas terjamin, harga terbaik' ),
                            );

                            $preset_options = array(
                                'design'    => __( '✏️ Desain / Konsultasi', 'tokoku' ),
                                'shield'    => __( '🛡️ Garansi / Aman', 'tokoku' ),
                                'lightning' => __( '⚡ Kilat / Presisi', 'tokoku' ),
                                'craftsman' => __( '🏭 Pengrajin / Pabrik Sendiri', 'tokoku' ),
                                'award'     => __( '🏆 Kualitas Terbaik / Juara', 'tokoku' ),
                                'check'     => __( '✅ Terverifikasi / Pasti', 'tokoku' ),
                                'heart'     => __( '❤️ Pelayanan Ramah', 'tokoku' ),
                                'box'       => __( '📦 Packing Aman', 'tokoku' ),
                                'thumbs-up' => __( '👍 Kepuasan Terjamin', 'tokoku' ),
                                'star'      => __( '⭐ Ulasan Bintang 5', 'tokoku' ),
                                'custom'    => __( '🎨 Custom (Upload Gambar atau SVG)', 'tokoku' ),
                            );
                            ?>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
                                <?php for ( $i = 1; $i <= 4; $i++ ) : 
                                    $curr_preset = get_theme_mod( "tokoku_trust_badge_icon_{$i}", $badge_defaults[$i]['preset'] );
                                    $curr_title  = get_theme_mod( "tokoku_trust_badge_title_{$i}", $badge_defaults[$i]['title'] );
                                    $curr_desc   = get_theme_mod( "tokoku_trust_badge_desc_{$i}", $badge_defaults[$i]['desc'] );
                                    $curr_img    = get_theme_mod( "tokoku_trust_badge_icon_img_{$i}" );
                                    $curr_svg    = get_theme_mod( "tokoku_trust_badge_icon_svg_{$i}" );
                                ?>
                                    <div class="tokoku-product-badge-card" style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                                            <strong style="color: #0f172a; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                                <span style="display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%; background:#eff6ff; color:#007bff; font-size:12px; font-weight:800;">#<?php echo $i; ?></span>
                                                <?php printf( __( 'Badge Keunggulan %d', 'tokoku' ), $i ); ?>
                                            </strong>
                                        </div>

                                        <div class="tokoku-field" style="margin-bottom: 14px;">
                                            <label style="font-size: 13px;"><?php _e( 'Judul Badge', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_trust_badge_title_<?php echo $i; ?>" value="<?php echo esc_attr( $curr_title ); ?>">
                                        </div>

                                        <div class="tokoku-field" style="margin-bottom: 14px;">
                                            <label style="font-size: 13px;"><?php _e( 'Deskripsi Singkat', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_trust_badge_desc_<?php echo $i; ?>" value="<?php echo esc_attr( $curr_desc ); ?>">
                                        </div>

                                        <div class="tokoku-field" style="margin-bottom: 14px;">
                                            <label style="font-size: 13px;"><?php _e( 'Pilihan Preset Ikon', 'tokoku' ); ?></label>
                                            <select name="tokoku_trust_badge_icon_<?php echo $i; ?>" style="font-size: 13px;">
                                                <?php foreach ( $preset_options as $pval => $plabel ) : ?>
                                                    <option value="<?php echo esc_attr( $pval ); ?>" <?php selected( $curr_preset, $pval ); ?>><?php echo esc_html( $plabel ); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="tokoku-field" style="margin-bottom: 14px;">
                                            <label style="font-size: 13px;"><?php _e( 'Upload Gambar Ikon', 'tokoku' ); ?></label>
                                            <div class="tokoku-media-upload">
                                                <img src="<?php echo esc_url( $curr_img ); ?>" class="tokoku-preview-img" style="max-height: 40px; <?php echo $curr_img ? '' : 'display:none;'; ?>">
                                                <input type="hidden" name="tokoku_trust_badge_icon_img_<?php echo $i; ?>" value="<?php echo esc_attr( $curr_img ); ?>">
                                                <button type="button" class="button tokoku-upload-btn" style="font-size: 12px; padding: 4px 10px;"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Gambar', 'tokoku' ); ?></span></button>
                                                <button type="button" class="button tokoku-remove-btn" style="<?php echo $curr_img ? '' : 'display:none;'; ?> font-size: 12px; padding: 4px 10px;"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                                            </div>
                                        </div>

                                        <div class="tokoku-field" style="margin-bottom: 0;">
                                            <label style="font-size: 13px;"><?php _e( 'Kode SVG Kustom (Opsional)', 'tokoku' ); ?></label>
                                            <textarea name="tokoku_trust_badge_icon_svg_<?php echo $i; ?>" rows="2" style="font-size: 12px; font-family: monospace;" placeholder="<svg ...>...</svg>"><?php echo esc_textarea( $curr_svg ); ?></textarea>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Informasi & Detail Produk (Hub Deskripsi, Spesifikasi & Panduan) -->
                        <div class="tokoku-settings-group" style="margin-top: 35px; padding: 24px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                                <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; font-size: 1.15rem; color: #0f172a;">
                                    <span class="dashicons dashicons-text-page" style="color: #6366f1; font-size: 22px; width: 22px; height: 22px;"></span>
                                    <?php _e( 'Informasi & Detail Produk (Hub Deskripsi, Spesifikasi & Panduan)', 'tokoku' ); ?>
                                </h3>
                                <div>
                                    <select name="tokoku_enable_desc_hub" style="font-weight: 700;">
                                        <option value="yes" <?php selected( get_theme_mod( 'tokoku_enable_desc_hub', 'yes' ), 'yes' ); ?>><?php _e( 'Tampilkan Hub Tab (Aktif)', 'tokoku' ); ?></option>
                                        <option value="no" <?php selected( get_theme_mod( 'tokoku_enable_desc_hub', 'yes' ), 'no' ); ?>><?php _e( 'Sederhana (Hanya Konten Teks)', 'tokoku' ); ?></option>
                                    </select>
                                </div>
                            </div>
                            <p class="tokoku-tip" style="margin-bottom: 22px;"><?php _e( 'Kustomisasi seluruh konten teks, judul, label 2 tab navigasi (Deskripsi & Panduan Pesan), alur 4 langkah pemesanan, dan kotak komitmen garansi yang tampil di halaman produk.', 'tokoku' ); ?></p>

                            <!-- Sub 1: Header & Tab Labels -->
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                <h4 style="margin: 0 0 16px 0; color: #1e293b; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                                    <span class="dashicons dashicons-tag" style="color: #6366f1;"></span>
                                    <?php _e( '1. Judul Bagian & Label Tab Navigasi (2 Tab Aktif)', 'tokoku' ); ?>
                                </h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                                    <div class="tokoku-field" style="margin-bottom: 0;">
                                        <label><?php _e( 'Teks Badge / Eyebrow', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_desc_badge_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_desc_badge_text', 'Informasi & Detail Produk' ) ); ?>">
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 0;">
                                        <label><?php _e( 'Judul Utama Bagian', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_desc_section_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_desc_section_title', 'Deskripsi & Panduan Pemesanan' ) ); ?>">
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                                    <div class="tokoku-field" style="margin-bottom: 0;">
                                        <label><?php _e( 'Label Tab 1 (Deskripsi)', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_desc_tab1_label" value="<?php echo esc_attr( get_theme_mod( 'tokoku_desc_tab1_label', 'Deskripsi & Fitur' ) ); ?>">
                                    </div>
                                    <div class="tokoku-field" style="margin-bottom: 0;">
                                        <label><?php _e( 'Label Tab 2 (Cara Pesan & Garansi)', 'tokoku' ); ?></label>
                                        <input type="text" name="tokoku_desc_tab3_label" value="<?php echo esc_attr( get_theme_mod( 'tokoku_desc_tab3_label', 'Cara Pesan & Garansi' ) ); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Sub 2: 4 Easy Steps -->
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                <h4 style="margin: 0 0 16px 0; color: #1e293b; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                                    <span class="dashicons dashicons-randomize" style="color: #8b5cf6;"></span>
                                    <?php _e( '2. Panduan 4 Langkah Cara Pesan (Di Tab Cara Pesan & Garansi)', 'tokoku' ); ?>
                                </h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                                    <!-- Step 1 -->
                                    <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; background: #fafafa;">
                                        <strong style="color: #6366f1; font-size: 13px; display: block; margin-bottom: 10px;">Langkah 01</strong>
                                        <div class="tokoku-field" style="margin-bottom: 10px;">
                                            <label style="font-size: 12px;"><?php _e( 'Judul Langkah', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_desc_step1_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_desc_step1_title', 'Konsultasi & Konsep' ) ); ?>">
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 0;">
                                            <label style="font-size: 12px;"><?php _e( 'Keterangan', 'tokoku' ); ?></label>
                                            <textarea name="tokoku_desc_step1_desc" rows="3" style="font-size: 12px;"><?php echo esc_textarea( get_theme_mod( 'tokoku_desc_step1_desc', 'Kirimkan logo, naskah/tulisan penghargaan, dan bentuk yang diinginkan via WhatsApp.' ) ); ?></textarea>
                                        </div>
                                    </div>
                                    <!-- Step 2 -->
                                    <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; background: #fafafa;">
                                        <strong style="color: #6366f1; font-size: 13px; display: block; margin-bottom: 10px;">Langkah 02</strong>
                                        <div class="tokoku-field" style="margin-bottom: 10px;">
                                            <label style="font-size: 12px;"><?php _e( 'Judul Langkah', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_desc_step2_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_desc_step2_title', 'Preview & ACC Mockup' ) ); ?>">
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 0;">
                                            <label style="font-size: 12px;"><?php _e( 'Keterangan', 'tokoku' ); ?></label>
                                            <textarea name="tokoku_desc_step2_desc" rows="3" style="font-size: 12px;"><?php echo esc_textarea( get_theme_mod( 'tokoku_desc_step2_desc', 'Tim kami membuatkan visual layout digital gratis untuk dicek & disetujui sebelum diproduksi.' ) ); ?></textarea>
                                        </div>
                                    </div>
                                    <!-- Step 3 -->
                                    <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; background: #fafafa;">
                                        <strong style="color: #6366f1; font-size: 13px; display: block; margin-bottom: 10px;">Langkah 03</strong>
                                        <div class="tokoku-field" style="margin-bottom: 10px;">
                                            <label style="font-size: 12px;"><?php _e( 'Judul Langkah', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_desc_step3_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_desc_step3_title', 'Proses Produksi Cepat' ) ); ?>">
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 0;">
                                            <label style="font-size: 12px;"><?php _e( 'Keterangan', 'tokoku' ); ?></label>
                                            <textarea name="tokoku_desc_step3_desc" rows="3" style="font-size: 12px;"><?php echo esc_textarea( get_theme_mod( 'tokoku_desc_step3_desc', 'Setelah desain fix dan DP dikonfirmasi, plakat langsung diproses mesin laser presisi.' ) ); ?></textarea>
                                        </div>
                                    </div>
                                    <!-- Step 4 -->
                                    <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; background: #fafafa;">
                                        <strong style="color: #6366f1; font-size: 13px; display: block; margin-bottom: 10px;">Langkah 04</strong>
                                        <div class="tokoku-field" style="margin-bottom: 10px;">
                                            <label style="font-size: 12px;"><?php _e( 'Judul Langkah', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_desc_step4_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_desc_step4_title', 'Packing & Pengiriman' ) ); ?>">
                                        </div>
                                        <div class="tokoku-field" style="margin-bottom: 0;">
                                            <label style="font-size: 12px;"><?php _e( 'Keterangan', 'tokoku' ); ?></label>
                                            <textarea name="tokoku_desc_step4_desc" rows="3" style="font-size: 12px;"><?php echo esc_textarea( get_theme_mod( 'tokoku_desc_step4_desc', 'Produk dipacking berlapis tebal dan dikirim menggunakan ekspedisi terpercaya ke seluruh Indonesia.' ) ); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sub 3: 100% Guarantee Box -->
                            <div style="background: #ffffff; border: 1.5px solid #22c55e; border-radius: 12px; padding: 20px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                                    <h4 style="margin: 0; color: #15803d; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                                        <span class="dashicons dashicons-shield-alt" style="color: #22c55e;"></span>
                                        <?php _e( '3. Kotak Jaminan Garansi 100%', 'tokoku' ); ?>
                                    </h4>
                                    <div>
                                        <select name="tokoku_desc_enable_guarantee" style="font-size: 13px; font-weight: 700;">
                                            <option value="yes" <?php selected( get_theme_mod( 'tokoku_desc_enable_guarantee', 'yes' ), 'yes' ); ?>><?php _e( 'Tampilkan', 'tokoku' ); ?></option>
                                            <option value="no" <?php selected( get_theme_mod( 'tokoku_desc_enable_guarantee', 'yes' ), 'no' ); ?>><?php _e( 'Sembunyikan', 'tokoku' ); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="tokoku-field" style="margin-bottom: 14px;">
                                    <label><?php _e( 'Judul Jaminan Garansi', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_desc_guarantee_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_desc_guarantee_title', 'Jaminan Garansi 100% — Rusak / Pecah Kami Ganti Baru!' ) ); ?>">
                                </div>
                                <div class="tokoku-field" style="margin-bottom: 0;">
                                    <label><?php _e( 'Keterangan Komitmen Garansi', 'tokoku' ); ?></label>
                                    <textarea name="tokoku_desc_guarantee_desc" rows="3"><?php echo esc_textarea( get_theme_mod( 'tokoku_desc_guarantee_desc', 'Keamanan barang Anda adalah prioritas utama kami. Apabila pesanan mengalami kerusakan saat perjalanan kirim oleh kurir/ekspedisi, cukup kirimkan video unboxing dan kami siap membuatkan unit pengganti baru tanpa biaya tambahan.' ) ); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: Testimonials & Logos -->
                    <div id="tab-testimonials" class="tokoku-tab-panel <?php echo ( 'tab-testimonials' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Ulasan Google Maps & Logo Klien', 'tokoku' ); ?></h2>

                        <!-- 1. Pengaturan Profil Bisnis Google Maps (Panel Kiri Slider) -->
                        <div class="tokoku-settings-group">
                            <h3 style="margin-top:0; display:flex; align-items:center; gap:8px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                <?php _e( 'Profil Bisnis Google Maps (Sisi Kiri Slider)', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 18px;">
                                <?php _e( 'Atur informasi profil toko Google Maps yang tampil di sebelah kiri slider ulasan (Nama Bisnis, Foto Tempat, Total Ulasan, dan Tombol Menulis Ulasan).', 'tokoku' ); ?>
                            </p>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Judul Section (Opsional)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_gmap_section_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_section_title', 'Apa Kata Klien Tentang Toko Kami' ) ); ?>" placeholder="Kosongkan jika tanpa judul atas">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Subjudul Section (Opsional)', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_gmap_section_subtitle" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_section_subtitle', 'Ulasan autentik dari pelanggan yang telah memesan di toko kami melalui Google Maps.' ) ); ?>" placeholder="Kosongkan jika tanpa subjudul">
                                </div>
                            </div>

                            <div class="tokoku-field" style="margin-top: 10px;">
                                <label><?php _e( 'Foto Tempat / Toko (Thumbnail Sisi Kiri)', 'tokoku' ); ?></label>
                                <div class="tokoku-media-upload">
                                    <img src="<?php echo esc_url( get_theme_mod( 'tokoku_gmap_place_photo' ) ); ?>" class="tokoku-preview-img" style="max-width: 120px; border-radius: 8px; <?php echo get_theme_mod( 'tokoku_gmap_place_photo' ) ? '' : 'display:none;'; ?>">
                                    <input type="hidden" name="tokoku_gmap_place_photo" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_place_photo' ) ); ?>">
                                    <button type="button" class="button tokoku-upload-btn"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Foto Toko', 'tokoku' ); ?></span></button>
                                    <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( 'tokoku_gmap_place_photo' ) ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                                </div>
                                <p class="description"><?php _e( 'Foto tampak depan toko/workshop di samping nama bisnis (seperti pada Google Maps).', 'tokoku' ); ?></p>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-top: 10px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Nama Bisnis di Google Maps', 'tokoku' ); ?></label>
                                    <input type="text" id="tokoku_gmap_place_name" name="tokoku_gmap_place_name" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_place_name', get_bloginfo( 'name' ) ) ); ?>" placeholder="Contoh: 1Souvenir Indonesia">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Skor Rating Bisnis (1.0 - 5.0)', 'tokoku' ); ?></label>
                                    <input type="text" id="tokoku_gmap_place_rating" name="tokoku_gmap_place_rating" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_place_rating', '5.0' ) ); ?>" placeholder="5.0">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Total Jumlah Ulasan Google', 'tokoku' ); ?></label>
                                    <input type="number" id="tokoku_gmap_total_reviews" name="tokoku_gmap_total_reviews" min="1" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_total_reviews', 317 ) ); ?>" placeholder="317">
                                    <p class="description"><?php _e( 'Tampil sebagai: "317 Google ulasan"', 'tokoku' ); ?></p>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin-top: 10px;">
                                <div class="tokoku-field">
                                    <label><?php _e( 'Teks Tombol Tulis Ulasan', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_gmap_btn_text" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_btn_text', 'Menulis ulasan' ) ); ?>" placeholder="Menulis ulasan">
                                </div>
                                <div class="tokoku-field">
                                    <label><?php _e( 'Link Tombol "Menulis ulasan" (URL Google Maps)', 'tokoku' ); ?></label>
                                    <input type="url" id="tokoku_gmap_review_url" name="tokoku_gmap_review_url" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_review_url', '' ) ); ?>" placeholder="https://g.page/r/.../review atau link Google Maps toko">
                                </div>
                            </div>
                        </div>

                        <!-- 2. Sinkronisasi Otomatis Google Places API (Hybrid) -->
                        <div class="tokoku-settings-group" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                            <h3 style="margin-top:0; display:flex; align-items:center; gap:8px;">
                                <span class="dashicons dashicons-cloud-saved" style="color:#2563eb;"></span>
                                <?php _e( 'Sinkronisasi Otomatis Google Places API (Opsional)', 'tokoku' ); ?>
                            </h3>
                            <p class="description" style="margin-bottom: 16px;">
                                <?php _e( 'Jika Anda memiliki Google Cloud Places API Key & Place ID bisnis, Anda dapat menarik Nama Bisnis, Total Ulasan, dan 5 Ulasan Terbaru langsung dari Google Maps secara otomatis. Anda juga tetap bebas mengedit atau menambah hingga 20 ulasan secara manual di bawah.', 'tokoku' ); ?>
                            </p>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
                                <div class="tokoku-field" style="margin-bottom:0;">
                                    <label><?php _e( 'Google Maps / Places API Key', 'tokoku' ); ?></label>
                                    <input type="password" id="tokoku_gmap_api_key" name="tokoku_gmap_api_key" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_api_key', '' ) ); ?>" placeholder="AIzaSy..." autocomplete="off">
                                </div>
                                <div class="tokoku-field" style="margin-bottom:0;">
                                    <label><?php _e( 'Google Maps Place ID', 'tokoku' ); ?></label>
                                    <input type="text" id="tokoku_gmap_place_id" name="tokoku_gmap_place_id" value="<?php echo esc_attr( get_theme_mod( 'tokoku_gmap_place_id', '' ) ); ?>" placeholder="ChIJ...">
                                </div>
                            </div>

                            <div style="margin-top: 16px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <button type="button" class="button button-secondary" id="tokoku-sync-gmap-btn" style="display:inline-flex; align-items:center; gap:6px; font-weight:600;">
                                    <span class="dashicons dashicons-update" style="line-height:1; margin-top:2px;"></span>
                                    <span><?php _e( 'Tarik Data & Ulasan dari Google Maps', 'tokoku' ); ?></span>
                                </button>
                                <span id="tokoku-sync-gmap-status" style="font-size: 0.88rem; font-weight: 600;"></span>
                            </div>
                        </div>

                        <!-- 3. Testimonials / Google Reviews Vertical Tabs -->
                        <div class="tokoku-settings-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <div>
                                    <h3 style="margin:0 0 4px 0;"><?php _e( 'Daftar Kartu Ulasan Google Maps (Hingga 20 Ulasan)', 'tokoku' ); ?></h3>
                                    <p class="description" style="margin:0;"><?php _e( 'Kelola isi kartu ulasan yang tampil pada slider. Jika foto dikosongkan, sistem otomatis membuat lingkaran inisial warna-warni khas Google.', 'tokoku' ); ?></p>
                                </div>
                                <button type="button" class="tokoku-btn-add tokoku-add-testi"><span class="dashicons dashicons-plus-alt2"></span><span class="tokoku-btn-text"><?php _e( 'Tambah Ulasan', 'tokoku' ); ?></span></button>
                            </div>
                            
                            <div class="tokoku-vtabs-container">
                                <div class="tokoku-vtabs-nav testi-nav">
                                    <?php for ( $i = 1; $i <= 20; $i++ ) : 
                                        $has_content = get_theme_mod( "tokoku_testi_name_{$i}" ) || get_theme_mod( "tokoku_testi_text_{$i}" );
                                        $display = $i <= 2 || $has_content ? '' : 'display:none;';
                                        $active = $i === 1 ? 'active' : '';
                                    ?>
                                        <div class="tokoku-vtab-link <?php echo $active; ?>" data-target="testi-panel-<?php echo $i; ?>" style="<?php echo $display; ?>">
                                            <span><?php printf( __( 'Ulasan #%d', 'tokoku' ), $i ); ?></span>
                                            <?php if ($i > 1): ?><i class="tokoku-remove-unit-v" title="Hapus">×</i><?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <div class="tokoku-vtabs-content testi-content">
                                    <?php for ( $i = 1; $i <= 20; $i++ ) : 
                                        $active = $i === 1 ? 'active' : '';
                                        $is_local_guide = (int) get_theme_mod( "tokoku_testi_local_guide_{$i}", ( $i % 2 === 0 ? 1 : 0 ) );
                                    ?>
                                        <div class="tokoku-vtab-panel <?php echo $active; ?>" id="testi-panel-<?php echo $i; ?>">
                                            <div class="tokoku-field">
                                                <label><?php _e( 'Foto Profil Pengulas (Opsional)', 'tokoku' ); ?></label>
                                                <div class="tokoku-media-upload">
                                                    <img src="<?php echo esc_url( get_theme_mod( "tokoku_testi_img_{$i}" ) ); ?>" class="tokoku-preview-img" style="<?php echo get_theme_mod( "tokoku_testi_img_{$i}" ) ? '' : 'display:none;'; ?>">
                                                    <input type="hidden" name="tokoku_testi_img_<?php echo $i; ?>" value="<?php echo esc_attr( get_theme_mod( "tokoku_testi_img_{$i}" ) ); ?>">
                                                    <button type="button" class="button tokoku-upload-btn"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Foto', 'tokoku' ); ?></span></button>
                                                    <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( "tokoku_testi_img_{$i}" ) ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                                                </div>
                                                <p class="description"><?php _e( 'Kosongkan jika ingin menampilkan avatar huruf inisial bulat warna-warni seperti Google.', 'tokoku' ); ?></p>
                                            </div>
                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                                                <div class="tokoku-field">
                                                    <label><?php _e( 'Nama Pengulas', 'tokoku' ); ?></label>
                                                    <input type="text" name="tokoku_testi_name_<?php echo $i; ?>" value="<?php echo esc_attr( get_theme_mod( "tokoku_testi_name_{$i}" ) ); ?>" placeholder="Contoh: Refni Ifani">
                                                </div>
                                                <div class="tokoku-field">
                                                    <label><?php _e( 'Tanggal Ulasan', 'tokoku' ); ?></label>
                                                    <input type="text" name="tokoku_testi_date_<?php echo $i; ?>" value="<?php echo esc_attr( get_theme_mod( "tokoku_testi_date_{$i}" ) ); ?>" placeholder="Contoh: 28 Juli 2026">
                                                </div>
                                            </div>
                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                                                <div class="tokoku-field">
                                                    <label><?php _e( 'Rating Bintang', 'tokoku' ); ?></label>
                                                    <select name="tokoku_testi_rating_<?php echo $i; ?>">
                                                        <?php 
                                                        $current_rating = get_theme_mod( "tokoku_testi_rating_{$i}", 5 );
                                                        for ($r = 5; $r >= 1; $r--) {
                                                            echo '<option value="'.$r.'" '.selected($current_rating, $r, false).'>'.$r.' '.__('Bintang', 'tokoku').'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="tokoku-field" style="display:flex; flex-direction:column; justify-content:center;">
                                                    <label><?php _e( 'Badge Local Guide', 'tokoku' ); ?></label>
                                                    <label style="display:inline-flex; align-items:center; gap:8px; font-weight:500; cursor:pointer; margin-top:6px;">
                                                        <input type="hidden" name="tokoku_testi_local_guide_<?php echo $i; ?>" value="0">
                                                        <input type="checkbox" name="tokoku_testi_local_guide_<?php echo $i; ?>" value="1" <?php checked( $is_local_guide, 1 ); ?>>
                                                        <span><?php _e( 'Tampilkan ikon bintang Local Guide di avatar', 'tokoku' ); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="tokoku-field">
                                                <label><?php _e( 'Isi Ulasan Google Maps', 'tokoku' ); ?></label>
                                                <textarea name="tokoku_testi_text_<?php echo $i; ?>" rows="4" placeholder="Contoh: Beberapa kali pesan momento di toko ini. Kualitas sangat bagus, bisa konsultasi sampe puas sebelum tahap produksi..."><?php echo esc_textarea( get_theme_mod( "tokoku_testi_text_{$i}" ) ); ?></textarea>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Logos Vertical Tabs -->
                        <div class="tokoku-settings-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h3 style="margin:0;"><?php _e( 'Logo Klien / Partner', 'tokoku' ); ?></h3>
                                <button type="button" class="tokoku-btn-add tokoku-add-logo"><span class="dashicons dashicons-plus-alt2"></span><span class="tokoku-btn-text"><?php _e( 'Tambah Logo', 'tokoku' ); ?></span></button>
                            </div>
                            
                            <div class="tokoku-vtabs-container">
                                <div class="tokoku-vtabs-nav logo-nav">
                                    <?php for ( $i = 1; $i <= 50; $i++ ) : 
                                        $has_logo = get_theme_mod( "tokoku_client_logo_{$i}" );
                                        $display = $i <= 3 || $has_logo ? '' : 'display:none;';
                                        $active = $i === 1 ? 'active' : '';
                                    ?>
                                        <div class="tokoku-vtab-link <?php echo $active; ?>" data-target="logo-panel-<?php echo $i; ?>" style="<?php echo $display; ?>">
                                            <span><?php printf( __( 'Logo #%d', 'tokoku' ), $i ); ?></span>
                                            <?php if ($i > 3): ?><i class="tokoku-remove-unit-v" title="Hapus">×</i><?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <div class="tokoku-vtabs-content logo-content">
                                    <?php for ( $i = 1; $i <= 50; $i++ ) : 
                                        $active = $i === 1 ? 'active' : '';
                                    ?>
                                        <div class="tokoku-vtab-panel <?php echo $active; ?>" id="logo-panel-<?php echo $i; ?>">
                                            <div class="tokoku-field">
                                                <label><?php printf( __( 'Upload Logo Partner %d', 'tokoku' ), $i ); ?></label>
                                                <div class="tokoku-media-upload">
                                                    <img src="<?php echo esc_url( get_theme_mod( "tokoku_client_logo_{$i}" ) ); ?>" class="tokoku-preview-img" style="max-height: 80px; <?php echo get_theme_mod( "tokoku_client_logo_{$i}" ) ? '' : 'display:none;'; ?>">
                                                    <input type="hidden" name="tokoku_client_logo_<?php echo $i; ?>" value="<?php echo esc_attr( get_theme_mod( "tokoku_client_logo_{$i}" ) ); ?>">
                                                    <button type="button" class="button tokoku-upload-btn"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Logo', 'tokoku' ); ?></span></button>
                                                    <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( "tokoku_client_logo_{$i}" ) ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: Slider -->
                    <div id="tab-slider" class="tokoku-tab-panel <?php echo ( 'tab-slider' === $active_tab_param ) ? 'active' : ''; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h2 style="margin:0;"><?php _e( 'Banner Slider', 'tokoku' ); ?></h2>
                            <button type="button" class="tokoku-btn-add tokoku-add-slider"><span class="dashicons dashicons-plus-alt2"></span><span class="tokoku-btn-text"><?php _e( 'Tambah Banner', 'tokoku' ); ?></span></button>
                        </div>
                        <p class="description" style="margin-bottom:20px;"><?php _e( 'Atur banner promosi utama yang tampil di halaman depan.', 'tokoku' ); ?></p>
                        
                        <div class="tokoku-vtabs-container">
                            <div class="tokoku-vtabs-nav slider-nav">
                                <?php for ( $i = 1; $i <= 10; $i++ ) : 
                                    $has_img = get_theme_mod( "tokoku_slide_image_{$i}" );
                                    $display = $i === 1 || $has_img ? '' : 'display:none;';
                                    $active = $i === 1 ? 'active' : '';
                                ?>
                                    <div class="tokoku-vtab-link <?php echo $active; ?>" data-target="slider-panel-<?php echo $i; ?>" style="<?php echo $display; ?>">
                                        <span><?php printf( __( 'Banner #%d', 'tokoku' ), $i ); ?></span>
                                        <?php if ($i > 1): ?><i class="tokoku-remove-unit-v" title="Hapus">×</i><?php endif; ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <div class="tokoku-vtabs-content slider-content">
                                <?php for ( $i = 1; $i <= 10; $i++ ) : 
                                    $active = $i === 1 ? 'active' : '';
                                ?>
                                    <div class="tokoku-vtab-panel <?php echo $active; ?>" id="slider-panel-<?php echo $i; ?>">
                                        <div class="tokoku-field">
                                            <label><?php _e( 'Gambar Banner', 'tokoku' ); ?></label>
                                            <div class="tokoku-media-upload">
                                                <img src="<?php echo esc_url( get_theme_mod( "tokoku_slide_image_{$i}" ) ); ?>" class="tokoku-preview-img" style="<?php echo get_theme_mod( "tokoku_slide_image_{$i}" ) ? '' : 'display:none;'; ?>">
                                                <input type="hidden" name="tokoku_slide_image_<?php echo $i; ?>" value="<?php echo esc_attr( get_theme_mod( "tokoku_slide_image_{$i}" ) ); ?>">
                                                <button type="button" class="button tokoku-upload-btn"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Banner', 'tokoku' ); ?></span></button>
                                                <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( "tokoku_slide_image_{$i}" ) ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                                            </div>
                                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Menampilkan promo terbaru atau produk unggulan di halaman utama.', 'tokoku' ); ?></p>
                                        </div>
                                        <div class="tokoku-field">
                                            <label><?php _e( 'Link Tautan', 'tokoku' ); ?></label>
                                            <input type="url" name="tokoku_slide_link_<?php echo $i; ?>" value="<?php echo esc_url( get_theme_mod( "tokoku_slide_link_{$i}" ) ); ?>" placeholder="https://...">
                                        </div>
                                        <div class="tokoku-field">
                                            <label><?php _e( 'Teks Alt Banner (SEO)', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_slide_alt_<?php echo $i; ?>" value="<?php echo esc_attr( get_theme_mod( "tokoku_slide_alt_{$i}" ) ); ?>" placeholder="<?php esc_attr_e( 'Contoh: Promo Diskon Plakat Akrilik Custom', 'tokoku' ); ?>">
                                            <p class="tokoku-tip"><?php _e( 'Membantu Google memahami konten gambar banner agar terindeks lebih optimal di hasil pencarian gambar.', 'tokoku' ); ?></p>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: Social -->
                    <div id="tab-social" class="tokoku-tab-panel <?php echo ( 'tab-social' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Social Media Links', 'tokoku' ); ?></h2>
                        <p class="description" style="margin-bottom:20px;"><?php _e( 'Ikon sosial media akan muncul secara otomatis di bagian footer.', 'tokoku' ); ?></p>
                        <?php 
                        $socials = array( 'instagram', 'facebook', 'tiktok', 'youtube', 'twitter' );
                        foreach ( $socials as $social ) : ?>
                            <div class="tokoku-field">
                                <label style="display:inline-flex; align-items:center; gap:8px;">
                                    <?php echo function_exists( 'tokoku_icon' ) ? tokoku_icon( $social, 16 ) : ''; ?>
                                    <span><?php echo ( 'twitter' === $social ) ? 'X (Twitter)' : ucfirst( $social ); ?></span>
                                </label>
                                <input type="url" name="tokoku_social_<?php echo $social; ?>" value="<?php echo esc_url( get_theme_mod( "tokoku_social_{$social}" ) ); ?>" placeholder="https://...">
                            </div>
                        <?php endforeach; ?>
                        <p class="tokoku-tip"><?php _e( 'Kegunaan: Membangun kepercayaan (Trust) pelanggan dengan menunjukkan eksistensi toko Anda di berbagai platform.', 'tokoku' ); ?></p>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: Footer -->
                    <div id="tab-footer" class="tokoku-tab-panel <?php echo ( 'tab-footer' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Konten Footer', 'tokoku' ); ?></h2>

                        <div class="tokoku-field">
                            <label><?php _e( 'Teks Copyright', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_footer_copyright" value="<?php echo esc_attr( get_theme_mod( 'tokoku_footer_copyright', '© {year} TokoKu. All rights reserved.' ) ); ?>" placeholder="Contoh: © {year} TokoKu. All rights reserved.">
                            <p class="tokoku-tip"><?php _e( 'Gunakan {year} untuk otomatis menampilkan tahun saat ini di bagian bawah situs.', 'tokoku' ); ?></p>
                        </div>

                        <!-- Panel Sinkronisasi Informasi Kontak Footer -->
                        <div class="tokoku-settings-section" style="margin-top: 25px; border-left: 3px solid #0d9488; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px; margin-bottom: 16px;">
                                <div>
                                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                        <span class="dashicons dashicons-location-alt" style="color: #0d9488;"></span>
                                        <?php _e( 'Informasi Kontak & Jam Kerja Footer', 'tokoku' ); ?>
                                    </h3>
                                    <p class="description" style="margin: 4px 0 0; font-size: 13px;">
                                        <?php _e( 'Alamat workshop, email toko, jam kerja, dan daftar WhatsApp CRO yang tampil di footer dikelola terpusat pada menu <strong>Template Kontak & Workshop</strong> agar data toko selalu konsisten dan sinkron.', 'tokoku' ); ?>
                                    </p>
                                </div>
                                <button type="button" class="button button-secondary tokoku-switch-tab-btn" data-target-tab="tab-contact" style="font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                    <span class="dashicons dashicons-edit"></span>
                                    <?php _e( 'Kelola di Template Kontak & Workshop', 'tokoku' ); ?>
                                </button>
                            </div>

                            <!-- Pratinjau Nilai Aktif Saat Ini -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; background: #ffffff; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div>
                                    <strong style="display: block; font-size: 11px; text-transform: uppercase; color: #64748b; margin-bottom: 4px;"><?php _e( 'Alamat Toko / Workshop', 'tokoku' ); ?></strong>
                                    <span style="font-size: 13px; color: #1e293b; line-height: 1.5; display: block;">
                                        <?php 
                                        $footer_addr = get_theme_mod( 'tokoku_store_address', '' );
                                        echo ! empty( $footer_addr ) ? nl2br( esc_html( $footer_addr ) ) : '<em style="color:#94a3b8;">' . __( '(Belum diatur — klik tombol di atas untuk mengisi)', 'tokoku' ) . '</em>';
                                        ?>
                                    </span>
                                </div>
                                <div>
                                    <strong style="display: block; font-size: 11px; text-transform: uppercase; color: #64748b; margin-bottom: 4px;"><?php _e( 'Email & Telepon', 'tokoku' ); ?></strong>
                                    <span style="font-size: 13px; color: #1e293b; display: block; margin-bottom: 3px;">
                                        <strong>Email:</strong> <?php echo esc_html( get_theme_mod( 'tokoku_store_email', '-' ) ); ?>
                                    </span>
                                    <span style="font-size: 13px; color: #1e293b; display: block;">
                                        <strong>Telp:</strong> <?php echo esc_html( get_theme_mod( 'tokoku_store_phone', '-' ) ); ?>
                                    </span>
                                </div>
                                <div>
                                    <strong style="display: block; font-size: 11px; text-transform: uppercase; color: #64748b; margin-bottom: 4px;"><?php _e( 'Jam Operasional', 'tokoku' ); ?></strong>
                                    <span style="font-size: 12px; color: #334155; display: block; line-height: 1.4;">
                                        1. <?php echo esc_html( get_theme_mod( 'tokoku_jam_op_1', 'Senin - Jumat: 08.30 - 16.30' ) ); ?><br>
                                        2. <?php echo esc_html( get_theme_mod( 'tokoku_jam_op_2', 'Sabtu: 08.30 - 16.00' ) ); ?><br>
                                        3. <?php echo esc_html( get_theme_mod( 'tokoku_jam_op_3', 'Minggu / Tanggal Merah: Libur' ) ); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: SEO -->
                    <div id="tab-seo" class="tokoku-tab-panel <?php echo ( 'tab-seo' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'SEO & Metadata', 'tokoku' ); ?></h2>
                        <div class="tokoku-field">
                            <label><?php _e( 'Meta Description', 'tokoku' ); ?></label>
                            <textarea name="tokoku_seo_desc"><?php echo esc_textarea( get_theme_mod( 'tokoku_seo_desc' ) ); ?></textarea>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Membantu website Anda lebih mudah ditemukan oleh calon pelanggan melalui mesin pencari.', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Meta Keywords', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_seo_keywords" value="<?php echo esc_attr( get_theme_mod( 'tokoku_seo_keywords' ) ); ?>">
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Social Share Image (OG)', 'tokoku' ); ?></label>
                            <div class="tokoku-media-upload">
                                <img src="<?php echo esc_url( get_theme_mod( 'tokoku_seo_og_image' ) ); ?>" class="tokoku-preview-img" style="<?php echo get_theme_mod( 'tokoku_seo_og_image' ) ? '' : 'display:none;'; ?>">
                                <input type="hidden" name="tokoku_seo_og_image" value="<?php echo esc_attr( get_theme_mod( 'tokoku_seo_og_image' ) ); ?>">
                                <button type="button" class="button tokoku-upload-btn"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Gambar', 'tokoku' ); ?></span></button>
                                <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( 'tokoku_seo_og_image' ) ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                            </div>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Memberikan tampilan profesional saat link website dibagikan ke calon pembeli.', 'tokoku' ); ?></p>
                        </div>

                        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
                        
                        <!-- Schema Product Snippets & Rating -->
                        <div class="tokoku-settings-group" style="padding: 24px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                                <h3 style="margin: 0; display: flex; align-items: center; gap: 10px; font-size: 1.15rem; color: #0f172a;">
                                    <span class="dashicons dashicons-star-filled" style="color: #f59e0b; font-size: 22px; width: 22px; height: 22px;"></span>
                                    <?php _e( 'Schema.org Google Rich Snippets (Rating & Review Produk)', 'tokoku' ); ?>
                                </h3>
                                <div>
                                    <select name="tokoku_schema_rating_enable" style="font-weight: 700;">
                                        <option value="yes" <?php selected( get_theme_mod( 'tokoku_schema_rating_enable', 'yes' ), 'yes' ); ?>><?php _e( 'Aktif (Rekomendasi Google)', 'tokoku' ); ?></option>
                                        <option value="no" <?php selected( get_theme_mod( 'tokoku_schema_rating_enable', 'yes' ), 'no' ); ?>><?php _e( 'Nonaktif', 'tokoku' ); ?></option>
                                    </select>
                                </div>
                            </div>
                            <p class="tokoku-tip" style="margin-bottom: 18px;"><?php _e( 'Otomatis menyuntikkan structured data aggregateRating dan review ke schema Product. Fitur ini secara tuntas menyelesaikan pesan error Google Search Console: "Either offers, review, or aggregateRating should be specified" pada produk dengan harga custom/Hubungi Kami, sekaligus memunculkan bintang ulasan oranye di hasil pencarian Google.', 'tokoku' ); ?></p>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="tokoku-field" style="margin-bottom: 0;">
                                    <label><?php _e( 'Nilai Rating Bintang Bawaan', 'tokoku' ); ?></label>
                                    <input type="text" name="tokoku_schema_default_rating" value="<?php echo esc_attr( get_theme_mod( 'tokoku_schema_default_rating', '4.9' ) ); ?>" placeholder="Contoh: 4.9">
                                    <p class="tokoku-tip"><?php _e( 'Skala 1.0 – 5.0 (dapat disesuaikan spesifik per produk di halaman edit produk).', 'tokoku' ); ?></p>
                                </div>
                                <div class="tokoku-field" style="margin-bottom: 0;">
                                    <label><?php _e( 'Jumlah Ulasan Bawaan (Review Count)', 'tokoku' ); ?></label>
                                    <input type="number" name="tokoku_schema_default_reviews" value="<?php echo esc_attr( get_theme_mod( 'tokoku_schema_default_reviews', 24 ) ); ?>" min="1" placeholder="Contoh: 24">
                                    <p class="tokoku-tip"><?php _e( 'Jumlah ulasan yang tercatat di Google Rich Snippets.', 'tokoku' ); ?></p>
                                </div>
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: FAQ -->
                    <div id="tab-faq" class="tokoku-tab-panel <?php echo ( 'tab-faq' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Manajemen FAQ (Tanya Jawab)', 'tokoku' ); ?></h2>
                        <div class="tokoku-field">
                            <label><?php _e( 'Judul Section FAQ', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_faq_title" value="<?php echo esc_attr( get_theme_mod( 'tokoku_faq_title', 'Pertanyaan Umum' ) ); ?>" placeholder="Contoh: Pertanyaan yang Sering Diajukan">
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Sub-judul FAQ', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_faq_subtitle" value="<?php echo esc_attr( get_theme_mod( 'tokoku_faq_subtitle', 'Temukan jawaban dari pertanyaan yang paling sering ditanyakan oleh pelanggan kami.' ) ); ?>">
                        </div>

                        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

                        <div class="tokoku-faq-repeater">
                            <?php 
                            $visible_count = 0;
                            for ( $i = 1; $i <= 10; $i++ ) : 
                                $q = get_theme_mod( "tokoku_faq_q_{$i}" );
                                $a = get_theme_mod( "tokoku_faq_a_{$i}" );
                                $is_empty = empty($q) && empty($a);
                                $display_q = $q ? $q : sprintf( __( 'Item FAQ #%d', 'tokoku' ), $i );
                                
                                // Show first item always, others only if not empty
                                $style = ($i === 1 || !$is_empty) ? '' : 'display: none;';
                                if ($style === '') $visible_count++;
                            ?>
                                <div class="tokoku-collapsible-item faq-item-row" data-index="<?php echo $i; ?>" style="<?php echo $style; ?>">
                                    <div class="tokoku-collapsible-header">
                                        <span><span class="dashicons dashicons-editor-help" style="margin-right:8px; color:#007bff;"></span> <span><?php echo esc_html( $display_q ); ?></span></span>
                                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                                    </div>
                                    <div class="tokoku-collapsible-content" style="display: none;">
                                        <div class="tokoku-field">
                                            <label><?php _e( 'Pertanyaan', 'tokoku' ); ?></label>
                                            <input type="text" name="tokoku_faq_q_<?php echo $i; ?>" value="<?php echo esc_attr( $q ); ?>" placeholder="Contoh: Bagaimana cara memesan?" class="tokoku-faq-input-q">
                                        </div>
                                        <div class="tokoku-field">
                                            <label><?php _e( 'Jawaban', 'tokoku' ); ?></label>
                                            <div class="tokoku-editor-wrap">
                                                <?php 
                                                wp_editor( $a, "tokokufaqa{$i}", array(
                                                    'textarea_name' => "tokoku_faq_a_{$i}",
                                                    'textarea_rows' => 5,
                                                    'media_buttons' => false,
                                                    'tinymce'       => array(
                                                        'toolbar1' => 'bold,italic,underline,separator,bullist,numlist,separator,link,unlink',
                                                    ),
                                                    'quicktags'     => true
                                                ) ); 
                                                ?>
                                            </div>
                                        </div>
                                        <button type="button" class="tokoku-remove-faq"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus Item FAQ Ini', 'tokoku' ); ?></span></button>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <div style="margin-top: 20px;">
                            <button type="button" id="tokoku-add-faq" class="tokoku-btn-add">
                                <span class="dashicons dashicons-plus-alt2"></span><span class="tokoku-btn-text"><?php _e( 'Tambah Pertanyaan', 'tokoku' ); ?></span>
                            </button>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>


                    <!-- Tab: Update -->
                    <div id="tab-update" class="tokoku-tab-panel <?php echo ( 'tab-update' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Pembaruan Tema TokoKu', 'tokoku' ); ?></h2>
                        <div class="tokoku-update-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-top: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: flex-start; gap: 20px;">
                                <div style="width: 56px; height: 56px; background: #eff6ff; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #007bff; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,123,255,0.15);">
                                    <svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
                                </div>
                                <div>
                                    <h3 style="margin: 0 0 6px 0; font-size: 1.25rem; font-weight: 800; color: #0f172a;">
                                        <?php _e( 'Versi Tema Saat Ini:', 'tokoku' ); ?> 
                                        <span style="color: #007bff; background: #eff6ff; padding: 3px 10px; border-radius: 6px; border: 1px solid #bfdbfe; font-size: 1.15rem;">v<?php echo TOKOKU_VERSION; ?></span>
                                        <span style="color: #059669; background: #ecfdf5; padding: 3px 10px; border-radius: 6px; border: 1px solid #a7f3d0; font-size: 0.85rem; font-weight: 700; margin-left: 6px;">Aktif & Optimal</span>
                                    </h3>
                                    <p style="margin: 0; color: #64748b; font-size: 0.95rem;"><?php _e( 'Pastikan tema Anda selalu menggunakan versi terbaru untuk fitur dan performa kecepatan terbaik.', 'tokoku' ); ?></p>
                                </div>
                            </div>

                            <hr style="margin: 22px 0; border: none; border-top: 1px solid #e2e8f0;">

                            <div id="tokoku-update-status" style="margin-bottom: 20px; display: none;">
                                <!-- Live GitHub Status & Changelog will be injected here -->
                            </div>

                            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                                <button type="button" id="tokoku-check-update" class="button button-primary tokoku-check-update-btn">
                                    <span class="dashicons dashicons-search"></span><span class="tokoku-btn-text"><?php _e( 'Cek Pembaruan dari GitHub', 'tokoku' ); ?></span>
                                </button>
                                <a href="https://github.com/aphien/tokoku/releases" target="_blank" rel="noopener noreferrer" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
                                    <span class="dashicons dashicons-external" style="color: #64748b;"></span>
                                    <span><?php _e( 'Lihat Rilis di GitHub', 'tokoku' ); ?></span>
                                </a>
                            </div>
                            
                            <div id="tokoku-update-loader" style="display: none; margin-top: 15px; align-items: center; gap: 10px; color: #007bff; font-weight: 600;">
                                <span class="spinner is-active" style="float: none; margin: 0;"></span>
                                <span><?php _e( 'Menghubungkan ke GitHub & mengunduh catatan rilis...', 'tokoku' ); ?></span>
                            </div>
                        </div>

                        <!-- Catatan Rilis Default (Changelog Versi Aktif) -->
                        <div class="tokoku-changelog-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-top: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                                <h3 style="margin: 0; color: #0f172a; font-size: 1.15rem; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                                    <span class="dashicons dashicons-media-text" style="color:#007bff;"></span>
                                    <?php _e( 'Log Pembaruan & Fitur Rilis Tema (v' . TOKOKU_VERSION . ')', 'tokoku' ); ?>
                                </h3>
                                <span style="font-size: 0.8rem; background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-weight: 600;">Changelog Resmi</span>
                            </div>

                            <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; max-height: 420px; overflow-y: auto; line-height: 1.65; color: #334155; font-size: 0.92rem;">
                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.9.0 — Optimasi Core Web Vitals (LCP, CLS &amp; INP) Sesuai PageSpeed Insights &amp; Kalibrasi Tipografi Tombol Order WhatsApp
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.9.0 menerapkan optimasi menyeluruh pada tiga metrik <strong>Core Web Vitals</strong> sesuai panduan Google PageSpeed Insights tanpa mengubah tampilan visual website: <strong>LCP (Kecepatan Muat)</strong> melalui pemuatan asinkron Font Awesome, eliminasi enqueue <code>style.css</code> kosong, preload <code>main.css</code> &amp; gambar LCP lengkap dengan <code>imagesrcset</code>/<code>imagesizes</code>, serta prioritas <code>loading="eager" fetchpriority="high"</code> pada kartu produk di atas lipatan layar; <strong>CLS (Stabilitas Visual)</strong> melalui dimensi eksplisit <code>width</code>/<code>height</code> pada logo header, ikon kategori, dan thumbnail live search serta penguncian <code>aspect-ratio: 1 / 1</code>; serta <strong>INP (Responsivitas)</strong> melalui penjadwalan idle <code>requestIdleCallback</code> untuk komponen bawah layar, throttling <code>requestAnimationFrame</code> pada zoom &amp; resize slider, akselerasi GPU <code>translate3d</code>, dan <code>touch-action: manipulation</code>. Dilengkapi penyesuaian proporsi ukuran font tombol <strong>Pesan via WhatsApp</strong> (+5%) dan tombol <strong>Pesan Sekarang ke WhatsApp</strong> (-3%).
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.8.0 — Fitur Google Maps Review Slider Interaktif, Sinkronisasi Hybrid Places API &amp; Proteksi Anti-Bentrok Modul Plugin
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.8.0 menghadirkan <strong>Google Maps Review Slider</strong> autentik di halaman beranda lengkap dengan Panel Ringkasan Bisnis di sisi kiri (Foto Tempat/Toko, Nama Bisnis, Rating Bintang, Total Ulasan Google, dan tombol <em>Menulis ulasan</em>) serta Carousel Kartu Ulasan 2-Kolom di sisi kanan (Avatar inisial warna-warni khas Google / Foto Profil, Badge Bintang Oranye Local Guide, Logo <code>G</code> Google penuh warna, Centang Biru Terverifikasi Google, Tanggal Ulasan, dan fitur ekspansi teks <em>Baca lebih lanjut</em>). Dilengkapi pengaturan lengkap di Admin (<strong>Tokoku &rarr; Ulasan &amp; Klien</strong>) dengan metode <strong>Hybrid</strong> (sinkronisasi otomatis via Google Places API + kelola/edit manual hingga 20 ulasan) serta proteksi <code>class_exists</code> / <code>function_exists</code> penuh pada plugin mandiri <code>tokoku-bulk-editor</code>.
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.7.0 — Integrasi Plugin Edit Masal Produk v2.0.0, Harmonisasi Gradasi Aksen Tema Dinamis &amp; Pembaruan Hijau WhatsApp Elegan
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.7.0 menghadirkan integrasi penuh plugin <strong>TokoKu Edit Masal Produk v2.0.0</strong> dengan kemampuan edit harga, diskon, stok, kategori, label promo, dan status pos secara masal dengan AJAX batch processing serta instalasi otomatis. Menyelaraskan seluruh tombol di website secara dinamis dengan warna aksen tema aktif di Customizer dengan gradasi elegan dan specular highlight, menghapus ikon WhatsApp pada tombol order untuk tampilan lebih bersih, menerapkan deteksi domain web dinamis, serta mengkhususkan tombol melayang WhatsApp (<code>.wa-float</code>) dengan gradasi Hijau Emerald WhatsApp yang elegan.
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.6.1 — Standarisasi Sentral Nomor WhatsApp, Kalibrasi Ergonomi Header &amp; Penyempurnaan Aksesibilitas Ikon Antarmuka
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.6.1 menghadirkan standarisasi terpusat pemrosesan nomor WhatsApp melalui helper <code>tokoku_normalize_wa()</code>, <code>tokoku_get_wa_number()</code>, dan <code>tokoku_get_wa_url()</code> sebagai Single Source of Truth di seluruh template (mengeliminasi masalah awalan angka 0 dan format nomor lokal). Menyelaraskan skala ergonomis header (+5%) untuk touch-target yang lebih nyaman pada desktop dan mobile, melengkapi tombol tema gelap/terang dengan <code>aria-label</code> dan <code>title</code> dinamis ramah aksesibilitas, menyempurnakan viewBox ikon matahari &amp; bulan (512x512), serta mengharmonisasi token gradasi WhatsApp (<code>--gradient-wa</code> &amp; <code>--wa-rgb</code>) pada tombol melayang, formulir kontak, dan bilah sticky order produk.
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.6.0 — Harmonisasi Gradasi Master Elegan, Fisika Animasi Presisi &amp; Redesain Modal Pemesanan WhatsApp Cerdas
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.6.0 menghadirkan perombakan visual dan interaksi ke standar kemewahan (<em>luxury-tier</em>): arsitektur token gradasi terpadu (<code>--gradient-primary</code> Royal Sapphire, <code>--gradient-wa</code> Emerald WhatsApp, <code>--gradient-gold</code>, <code>--gradient-coral</code>, dan <code>--gradient-acrylic</code>) dengan specular highlight atas; kurva gerak presisi (<code>--ease-smooth</code> dan <code>--ease-spring</code>); efek refleksi kilau akrilik alami pada modul plakat 3D; redesain modal pemesanan WhatsApp cerdas yang kini dilengkapi foto pratinjau produk responsif, form ringkas (Nama &amp; Catatan), integrasi pilihan catatan cepat, serta perakitan pesan WhatsApp otomatis lengkap (Nama Produk, SKU, Link Produk, Pemesan, dan Catatan).
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.5.0 — Panel Pengaturan Dinamis Template Halaman (Tentang Kami &amp; Kontak Kami), Eliminasi Bentrok Data Form, Navigasi Submenu 14-Tab &amp; Sinkronisasi Editor Otomatis
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.5.0 menghadirkan panel kustomisasi dinamis terpusat untuk <strong>Template Halaman Tentang Kami</strong> (8 section lengkap: Hero, ilustrasi plakat, statistik prestasi, kutipan filosofi, cerita brand narasi, 6 nilai layanan, 4 alur kerja, visi misi, partner klien, dan CTA) serta <strong>Template Halaman Kontak &amp; Workshop</strong> (8 section: Hero &amp; keunggulan, 3 saluran cepat, WhatsApp Message Builder interaktif, alamat bengkel &amp; link Google Maps, jam kerja, 5 tim CRO WhatsApp dengan peran spesialisasi, FAQ kontak, dan banner CTA). Menyelesaikan bentrok form duplikasi data pada tab Footer, mengaktifkan sorotan dinamis 14 submenu dasbor WordPress (<code>tokoku_admin_submenu_filter</code>), sinkronisasi otomatis TinyMCE sebelum simpan, serta fallback string resilien.
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.4.0 — Tampilan 60 Produk Katalog, Efek Gradiasi Dinamis Tombol View All, Kalibrasi Proporsional Kartu Produk &amp; Tipografi Fluid Responsif
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.4.0 menghadirkan tampilan 60 produk per halaman pada arsip Katalog Produk resmi (<code>/produk/</code>). Tombol &ldquo;Lihat Semua Produk&rdquo; kini dilengkapi efek animasi gradiasi dinamis multi-color dengan glow aura memukau dan skala ukuran yang disesuaikan -5% agar lebih proporsional. Tombol pemesanan WhatsApp pada kartu produk (<code>.btn-whatsapp-order</code>) ditingkatkan ukurannya +5% untuk kenyamanan ketukan jari (touch-friendly), serta judul produk (<code>.product-card__title</code>) dikalibrasi -3% dengan sistem tipografi fluid responsif <code>clamp()</code> berbasis Golden Ratio (font Lato) yang menyesuaikan secara otomatis dengan layar desktop, tablet, maupun ponsel cerdas pengguna.
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.3.1 — Mobile Bottom Sheet Drawer Modal Berbagi, Quick-Action Share Bar Mobile &amp; Proporsi Harmonis Testimoni
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.3.1 mentransformasi tampilan modal berbagi pada layar ponsel menjadi Bottom Sheet Drawer modern yang meluncur mulus dari bawah layar, lengkap dengan handle bar dan gestur sentuh geser ke bawah (swipe-to-dismiss). Bilah bagikan (Share Bar) pada halaman produk dan blog versi mobile disederhanakan menjadi 2 tombol aksi ergonomis: tombol Salin Tautan interaktif dengan indikator teks, serta tombol opsi lengkap (Lainnya). Menyelaraskan skala kartu kutipan testimoni beranda agar lebih ringkas, proporsional (-20%), dan seimbang di seluruh perangkat.
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.3.0 — Universal Share Modal, Sinkronisasi Share Bar Produk &amp; Blog, serta Optimalisasi Open Graph Meta Tags
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.3.0 menghadirkan Universal Social Share Modal dengan kartu pratinjau dinamis, generator QR Code untuk pemindaian instan kamera HP, dan tombol Direct Image Sharing/Download. Memperbarui Share Bar pada Single Produk &amp; Blog dengan tombol modal responsif, 8 saluran media sosial lengkap, serta tombol salin link visual. Optimalisasi Open Graph &amp; Twitter Cards kini menyertakan dimensi gambar (width &amp; height) dan metadata artikel untuk pratinjau tautan sempurna di WhatsApp, Facebook, X, Telegram, dan LinkedIn tanpa tampilan harga.
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.2.1 — Navigasi Presisi Pencarian ke Katalog Produk &amp; Pembaruan Otomatis Tema
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.2.1 memperbaiki navigasi tombol pencarian &ldquo;Lihat Semua Produk&rdquo; agar mengarahkan pengguna langsung ke arsip resmi Katalog Produk (<code>/produk/</code>) baik pada dropdown desktop maupun modal pencarian mobile. Dilengkapi injeksi dinamis <code>catalogUrl</code> via <code>wp_localize_script()</code>, tombol CTA interaktif saat produk tidak ditemukan, serta kompatibilitas penuh sistem Pembaruan Otomatis Tema langsung dari GitHub.
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.2.0 — Integrasi Resmi Font Awesome 6, Sinkronisasi Seluruh Logo Brand &amp; Performa Core Web Vitals 100%
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v3.2.0 mengintegrasikan repositori resmi Font Awesome 6 (CSS &amp; Webfonts lokal tanpa CDN latency) serta modul helper terpusat <code>tokoku_icon()</code>. Seluruh logo media sosial (WhatsApp, Facebook, Instagram, TikTok, YouTube, X/Twitter, LinkedIn, Telegram, Pinterest) dan ikon antarmuka (Header, Footer, Single Produk, Bottom Nav, Lightbox) kini 100% tersinkronisasi dengan vektor resmi siluet yang tajam, presisi, dan responsif.
                                </p>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v3.0.0 — Harmonisasi Warna Tema, Ikon SVG Presisi &amp; Responsif Mobile Optimal
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan besar v3.0.0 menyelaraskan seluruh elemen visual halaman statis dengan variabel warna kustom tema (Customizer &amp; Admin), meningkatkan presisi ikon SVG (termasuk ikon solid resmi WhatsApp dan chevron accordion), menyempurnakan ergonomi tampilan mobile tanpa auto-zoom pada formulir (16px), serta membersihkan riwayat rilis lama di bawah v2.6.0.
                                </p>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">🎨 1. Harmonisasi Warna Tema Dinamis</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>Adaptasi Warna Penuh</strong>: Gradien teks, reading progress bar, border aktif, tombol CTA, dan efek glow hero kini 100% mengikuti variabel warna tema (<code>--primary</code>, <code>--secondary</code>, <code>--gradient</code>).</li>
                                    <li style="margin-bottom: 4px;"><strong>Zero Color Conflict</strong>: Menghilangkan seluruh warna hex statis yang berpotensi bentrok saat tema diganti warna dasarnya di menu kustomisasi.</li>
                                </ul>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">✨ 2. Sistem Ikon SVG Presisi &amp; Ikon WhatsApp Otentik</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>Ikon WhatsApp Solid Resmi</strong>: Seluruh tombol WhatsApp pada halaman Tentang, Kontak, dan Syarat/Ketentuan kini menampilkan logo WhatsApp solid resmi dengan fill tajam.</li>
                                    <li style="margin-bottom: 4px;"><strong>Chevron Dinamis untuk FAQ Accordion</strong>: Ikon panah FAQ digantikan dengan chevron anggun yang berotasi 90 derajat secara mulus saat accordion dibuka.</li>
                                    <li style="margin-bottom: 4px;"><strong>Base SVG Alignment</strong>: Standarisasi aturan <code>.jp-icon</code> dengan alignment tengah vertikal dan flex-shrink nol untuk mencegah distorsi ikon.</li>
                                </ul>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">📱 3. Optimasi Responsif Mobile &amp; Safari Viewport Protection</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>Anti Auto-Zoom iOS Safari</strong>: Ukuran font formulir disetel ke 16px (1rem) agar peramban Safari di iPhone tidak memperbesar layar otomatis saat mengetik.</li>
                                    <li style="margin-bottom: 4px;"><strong>Plakat 3D Mobile Reset</strong>: Transformasi 3D dinonaktifkan otomatis di layar ponsel untuk mencegah overflow horizontal dan rendering buram.</li>
                                    <li style="margin-bottom: 4px;"><strong>Media Query 480px Spesifik</strong>: Tombol hero dan kartu aksi diubah menjadi full-width dengan target sentuh min 44px yang sangat nyaman dijangkau ibu jari.</li>
                                </ul>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v2.9.0 — Template Halaman Tentang, Kontak, Syarat &amp; Ketentuan, dan Kebijakan Privasi
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v2.9.0 menghadirkan 4 template halaman statis kustom berstandar korporat modern untuk JualPlakat.com: profil Tentang Kami dengan visual plakat 3D interaktif &amp; metrik dinamis, halaman Kontak Kami dengan generator formulir pesan WhatsApp instan, serta dokumen Syarat &amp; Ketentuan dan Kebijakan Privasi sesuai UU PDP No. 27/2022 lengkap dengan indikator progres baca dan Table of Contents (TOC) otomatis.
                                </p>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">🏢 1. Template Profil Tentang Kami (Tentang JualPlakat)</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>Visual Plakat Akrilik 3D</strong>: Elemen visual plakat akrilik modern bergaya glassmorphism dengan efek kilauan cahaya (ambient shine) dan ribbon penghargaan.</li>
                                    <li style="margin-bottom: 4px;"><strong>Statistik Dinamis Real-Time</strong>: Menghitung jumlah produk dan kategori katalog secara otomatis dari database toko.</li>
                                    <li style="margin-bottom: 4px;"><strong>Narasi Nilai &amp; Alur Kerja 4 Langkah</strong>: Penjelasan filosofi presisi, komitmen waktu, transparansi harga, serta panduan proses pemesanan dari konsultasi hingga pengiriman aman.</li>
                                </ul>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">📞 2. Template Kontak Kami &amp; Generator WhatsApp Instan</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>Interactive WhatsApp Builder</strong>: Formulir interaktif cerdas yang merangkum nama, instansi, jenis produk, jumlah pesanan, deadline, dan catatan menjadi format pesan rapi langsung ke WhatsApp Customer Care.</li>
                                    <li style="margin-bottom: 4px;"><strong>Workshop &amp; CRO Contact Cards</strong>: Alamat fisik dengan tombol Salin Cepat satu sentuhan dan integrasi Google Maps, serta kartu kontak CRO resmi.</li>
                                    <li style="margin-bottom: 4px;"><strong>FAQ Accordion Interaktif</strong>: Jawaban pertanyaan umum seputar waktu produksi, file desain, minimal order, dan keamanan ekspedisi.</li>
                                </ul>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">⚖️ 3. Template Legalitas (Syarat &amp; Ketentuan + Kebijakan Privasi UU PDP)</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>Sticky Table of Contents (TOC) &amp; Scrollspy</strong>: Navigasi cepat bernomor urut dengan penanda bagian aktif otomatis saat pengguna menggulir halaman.</li>
                                    <li style="margin-bottom: 4px;"><strong>Reading Progress Bar</strong>: Bilah penanda persentase keterbacaan dokumen di bagian atas layar.</li>
                                    <li style="margin-bottom: 4px;"><strong>Klausul Hukum Komprehensif</strong>: 15 pasal Syarat &amp; Ketentuan transaksi custom plakat dan 12 pasal Kebijakan Privasi sesuai UU PDP No. 27/2022.</li>
                                    <li style="margin-bottom: 4px;"><strong>Pemuatan Aset Terisolasi</strong>: File <code>pages.css</code> dan <code>pages.js</code> hanya dimuat pada halaman yang menggunakan template ini guna menjaga kecepatan 100% pada beranda dan katalog.</li>
                                </ul>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v2.8.0 — Desain Majalah Artikel Terkait, Mobile Touch Slider &amp; Flat Admin Nav
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v2.8.0 menyamakan desain Artikel Terkait Lainnya di halaman artikel tunggal dengan tampilan majalah Artikel Terbaru di beranda, menyematkan fitur slider sentuh khusus tampilan ponsel (CSS Scroll-Snap dengan peek &amp; pagination dots interaktif), menonaktifkan gradasi ikon menu admin TokoKu dengan warna solid flat yang tegas, serta memperbaiki aset gambar placeholder.
                                </p>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">📰 1. Desain Artikel Terkait Samakan dengan Artikel Terbaru</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>Magazine Overlay Architecture</strong>: Kartu artikel menggunakan struktur identik dengan beranda — thumbnail latar beresolusi tajam, zoom hover halus (scale 1.08), gradient overlay kontras tinggi, pill kategori frosted glass, dan judul putih tebal.</li>
                                    <li style="margin-bottom: 4px;"><strong>Metadata Proporsional</strong>: Dilengkapi nama penulis dan tanggal terbit dengan ikon SVG kalender &amp; profil berukuran presisi.</li>
                                    <li style="margin-bottom: 4px;"><strong>Intelligent Category Query</strong>: Query artikel rekomendasi diperluas hingga 6 artikel dengan sistem pelengkap otomatis dari artikel terkini jika artikel dalam kategori yang sama kurang dari 6.</li>
                                </ul>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">📱 2. Fitur Slider Artikel Khusus Tampilan Mobile</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>CSS Scroll-Snap Touch Carousel</strong>: Pada perangkat mobile (&le;768px), tata letak otomatis beralih dari grid menjadi carousel horizontal yang sangat halus dan ringan dengan akselerasi perangkat keras asli.</li>
                                    <li style="margin-bottom: 4px;"><strong>Peek Effect Intuitif</strong>: Menampilkan ~18% kartu berikutnya di sisi kanan layar agar pengunjung langsung mengetahui bahwa kartu dapat digeser (swipe).</li>
                                    <li style="margin-bottom: 4px;"><strong>Interactive Pagination Dots</strong>: Indikator titik di bawah slider yang aktif dan bergerak dinamis mengikuti posisi scroll pengguna, serta dapat ditekan untuk langsung berpindah ke artikel yang diinginkan.</li>
                                    <li style="margin-bottom: 4px;"><strong>Mouse Drag Support</strong>: Memungkinkan pengembang dan pengelola toko menguji interaksi slider secara langsung menggunakan kursor mouse pada mode responsive browser.</li>
                                </ul>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">🎨 3. Admin Menu Navigasi Flat &amp; Nonaktifkan Gradasi Ikon</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;">Menonaktifkan efek gradasi dan bayangan blur pada seluruh ikon navigasi pengaturan dasbor (<code>.tokoku-nav-icon</code> dan <code>.tokoku-icon</code>).</li>
                                    <li style="margin-bottom: 4px;">Standardisasi warna solid flat murni untuk setiap kategori modul admin, menghadirkan estetika panel kontrol yang bersih, modern, dan profesional.</li>
                                    <li style="margin-bottom: 4px;">Memperbaiki path placeholder gambar ke <code>placeholder.svg</code> yang valid dan pembaruan Service Worker PWA v2.8.0.</li>
                                </ul>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v2.7.0 — Dasbor Admin Modern, Shortcut Simpan Cepat &amp; Pembersihan Fitur Usang
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v2.7.0 menghadirkan perombakan tampilan dasbor admin utama WordPress dengan antarmuka SaaS modern, penambahan pintasan keyboard simpan cepat (⌘S/Ctrl+S), perampingan halaman produk menjadi 2 tab navigasi terpusat, pengaktifan tooltip WhatsApp mengambang, dan pembersihan menyeluruh fitur yang tidak lagi berfungsi.
                                </p>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">📊 1. Dasbor Utama WordPress &amp; Header Admin Modern</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>Interactive SaaS Stat Cards</strong>: Widget dasbor TokoKu dilengkapi 4 kartu statistik berwarna gradien untuk Produk Aktif, Jalur WhatsApp, Kategori Produk, dan Artikel Blog dengan tautan langsung.</li>
                                    <li style="margin-bottom: 4px;"><strong>Quick Actions</strong>: Tombol akses cepat ke Tambah Produk, Pengaturan Tokoku, Pembaruan Tema, dan Kunjungi Website.</li>
                                    <li style="margin-bottom: 4px;"><strong>Header Glassmorphic Baru</strong>: Header panel admin dengan status pill (v2.7.0 • Optimal) dan hint pintasan keyboard.</li>
                                </ul>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">⚡ 2. Shortcut Keyboard Simpan Cepat (⌘S / Ctrl+S)</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;">Menyimpan seluruh pengaturan TokoKu secara instan di mana saja cukup dengan menekan <code>Cmd + S</code> (Mac) atau <code>Ctrl + S</code> (Windows/Linux) dengan animasi tombol feedback langsung.</li>
                                </ul>

                                <h5 style="margin: 14px 0 6px 0; color: #0f172a; font-size: 0.98rem; font-weight: 700;">🧹 3. Pembersihan Fitur Usang &amp; Halaman Produk 2 Tab</h5>
                                <ul style="margin: 0 0 12px 20px; list-style-type: disc;">
                                    <li style="margin-bottom: 4px;"><strong>Eliminasi Dead Code</strong>: Menghapus skema dan formulir kartu keunggulan/highlights serta input spesifikasi yang sudah tidak dipakai di antarmuka publik.</li>
                                    <li style="margin-bottom: 4px;"><strong>Pembersihan Drag-and-Drop Usang</strong>: Menghapus skema tersembunyi menu reorder dan script jQuery sortable yang tidak berfungsi.</li>
                                    <li style="margin-bottom: 4px;"><strong>2 Tab Terpusat</strong>: Halaman produk tunggal fokus pada Tab Deskripsi dan Tab Cara Pesan &amp; Garansi dengan perataan tengah presisi.</li>
                                    <li style="margin-bottom: 4px;"><strong>Floating WhatsApp Tooltip</strong>: Pengaturan teks tombol WhatsApp mengambang di admin kini aktif dan tampil anggun sebagai tooltip desktop.</li>
                                </ul>

                                <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">

                                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 1.1rem; font-weight: 800;">
                                    🚀 Rilis v2.6.0 — Standarisasi Ukuran Ikon &amp; Tampilan Profesional Elegan
                                </h4>
                                <p style="margin: 0 0 14px 0; color: #475569;">
                                    Pembaruan v2.6.0 menghadirkan standarisasi menyeluruh pada proporsi, ukuran, rasio kontainer, dan ketajaman tampilan seluruh ikon tema di semua perangkat.
                                </p>
                            </div>
                        </div>

                        <div style="margin-top: 25px; background: #fff8e1; border-left: 4px solid #ffc107; padding: 18px 20px; border-radius: 8px;">
                            <h4 style="margin: 0 0 6px 0; color: #856404; display: flex; align-items: center; gap: 6px;"><span class="dashicons dashicons-warning" style="vertical-align: middle;"></span> <?php _e( 'Tips Cadangan:', 'tokoku' ); ?></h4>
                            <p style="margin: 0; font-size: 0.88rem; color: #856404; line-height: 1.5;">
                                <?php _e( 'Anda dapat mengekspor atau mencadangkan seluruh konfigurasi toko Anda di tab "Impor & Ekspor" sebelum memperbarui tema.', 'tokoku' ); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Tab: Import & Ekspor -->
                    <div id="tab-import-export" class="tokoku-tab-panel <?php echo ( 'tab-import-export' === $active_tab_param ) ? 'active' : ''; ?>">
                        <h2><?php _e( 'Impor & Ekspor Data Tema', 'tokoku' ); ?></h2>
                        <div class="tokoku-settings-group" style="margin-top: 20px; padding: 25px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            
                            <h3 style="margin-top: 0; color: #1e293b;"><?php _e( 'Pengaturan Tema (Theme Settings)', 'tokoku' ); ?></h3>
                            <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 20px;">
                                <?php _e( 'Anda dapat mencadangkan (backup) seluruh pengaturan tema (Warna, Font, Footer, dsb) atau mengembalikannya jika terjadi kesalahan.', 'tokoku' ); ?>
                            </p>
                            
                            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 250px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                    <h4 style="margin: 0 0 10px 0; display:flex; align-items:center; gap:8px;"><span class="dashicons dashicons-download" style="color:#007bff;"></span> <?php _e( 'Ekspor Pengaturan', 'tokoku' ); ?></h4>
                                    <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 16px;"><?php _e( 'Unduh file JSON yang berisi semua pengaturan tema Anda saat ini.', 'tokoku' ); ?></p>
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=tokoku_export_settings' ), 'tokoku_export_action' ) ); ?>" class="button button-primary">
                                        <span class="dashicons dashicons-download"></span><span class="tokoku-btn-text"><?php _e( 'Ekspor File .JSON', 'tokoku' ); ?></span>
                                    </a>
                                </div>
                                
                                <div style="flex: 1; min-width: 250px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                    <h4 style="margin: 0 0 10px 0; display:flex; align-items:center; gap:8px;"><span class="dashicons dashicons-upload" style="color:#007bff;"></span> <?php _e( 'Impor Pengaturan', 'tokoku' ); ?></h4>
                                    <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 16px;"><?php _e( 'Pilih file JSON dari komputer Anda untuk memulihkan pengaturan tema.', 'tokoku' ); ?></p>
                                    
                                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                        <input type="file" name="tokoku_import_file" id="tokoku_import_file" accept=".json" style="max-width: 200px;">
                                        <button type="submit" name="tokoku_import_action" value="import_settings" class="button button-secondary" onclick="return confirm('Peringatan: Pengaturan tema Anda saat ini akan tertimpa. Lanjutkan?');">
                                            <span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Mulai Impor', 'tokoku' ); ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <hr style="margin: 30px 0; border: none; border-top: 1px solid #e2e8f0;">

                            <h3 style="margin-top: 0; color: #1e293b;"><?php _e( 'Seluruh Data Website (Produk, Pesanan & Kategori)', 'tokoku' ); ?></h3>
                            <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 20px;">
                                <?php _e( 'Gunakan fitur bawaan WordPress untuk mencadangkan (ekspor) atau memulihkan (impor) data konten Anda seperti produk, pesanan, gambar, dan kategori.', 'tokoku' ); ?>
                            </p>
                            
                            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 250px;">
                                    <a href="<?php echo esc_url( admin_url( 'export.php' ) ); ?>" class="button button-secondary">
                                        <span class="dashicons dashicons-media-archive"></span><span class="tokoku-btn-text"><?php _e( 'Ekspor Seluruh Data (XML)', 'tokoku' ); ?></span>
                                    </a>
                                </div>
                                <div style="flex: 1; min-width: 250px;">
                                    <a href="<?php echo esc_url( admin_url( 'import.php' ) ); ?>" class="button button-secondary">
                                        <span class="dashicons dashicons-database-import"></span><span class="tokoku-btn-text"><?php _e( 'Alat Impor WordPress', 'tokoku' ); ?></span>
                                    </a>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                </div><!-- .tokoku-settings-content -->
            </div><!-- .tokoku-settings-container -->
        </form>
    </div>

    <?php
}
