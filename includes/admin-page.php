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
    
    // Sub-menu Navigasi Cepat di Dasbor Admin WordPress
    add_submenu_page( $menu_slug, __( 'Pengaturan Umum', 'tokoku' ), __( 'Pengaturan Umum', 'tokoku' ), $capability, 'tokoku-settings', $callback );
    add_submenu_page( $menu_slug, __( 'Kontak & Workshop', 'tokoku' ), __( 'Kontak & Workshop', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-contact', $callback );
    add_submenu_page( $menu_slug, __( 'WhatsApp & CS', 'tokoku' ), __( 'WhatsApp & CS', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-whatsapp', $callback );
    add_submenu_page( $menu_slug, __( 'Warna & Tampilan', 'tokoku' ), __( 'Warna & Tampilan', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-appearance', $callback );
    add_submenu_page( $menu_slug, __( 'Halaman Produk', 'tokoku' ), __( 'Halaman Produk', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-single-product', $callback );
    add_submenu_page( $menu_slug, __( 'SEO & Metadata', 'tokoku' ), __( 'SEO & Metadata', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-seo', $callback );
    add_submenu_page( $menu_slug, __( 'Pembaruan Tema', 'tokoku' ), __( 'Pembaruan Tema', 'tokoku' ), $capability, 'tokoku-settings&tab=tab-update', $callback );

    // Enqueue scripts for our settings page
    add_action( 'admin_print_scripts-' . $hook, 'tokoku_admin_settings_assets' );
}
add_action( 'admin_menu', 'tokoku_admin_menu' );

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

        // Typography
        'tokoku_font_body'        => 'sanitize_text_field',
        'tokoku_font_headings'    => 'sanitize_text_field',
        'tokoku_font_size_base'   => 'absint',
        'tokoku_font_size_h1'     => 'sanitize_text_field',

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
    );

    // Trust Badges Repeater
    for ( $i = 1; $i <= 4; $i++ ) {
        $settings_schema["tokoku_trust_badge_title_{$i}"]    = 'sanitize_text_field';
        $settings_schema["tokoku_trust_badge_desc_{$i}"]     = 'sanitize_text_field';
        $settings_schema["tokoku_trust_badge_icon_{$i}"]     = 'sanitize_text_field';
        $settings_schema["tokoku_trust_badge_icon_img_{$i}"] = 'esc_url_raw';
        $settings_schema["tokoku_trust_badge_icon_svg_{$i}"] = 'tokoku_sanitize_svg';
    }

    // FAQ Repeater
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

    // Testimonials
    for ( $i = 1; $i <= 20; $i++ ) {
        $settings_schema["tokoku_testi_img_{$i}"]    = 'esc_url_raw';
        $settings_schema["tokoku_testi_name_{$i}"]   = 'sanitize_text_field';
        $settings_schema["tokoku_testi_text_{$i}"]   = 'sanitize_textarea_field';
        $settings_schema["tokoku_testi_rating_{$i}"] = 'absint';
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
        case 'typography':
            return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg>';
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

    // Flush opcache so new PHP files (including version constant) are loaded immediately
    if ( function_exists( 'opcache_reset' ) ) {
        opcache_reset();
    }
    wp_clean_themes_cache();

    wp_send_json_success( 'Theme updated successfully' );
}
add_action( 'wp_ajax_tokoku_handle_update', 'tokoku_ajax_handle_update' );

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
                                'tab-contact'    => array( 
                                    'icon'  => 'contact',
                                    'color' => 'teal',
                                    'title' => __( 'Kontak & Workshop', 'tokoku' ),
                                    'desc'  => __( 'Alamat, Maps, Email & CRO', 'tokoku' ),
                                ),
                                'tab-appearance' => array( 
                                    'icon'  => 'appearance',
                                    'color' => 'purple',
                                    'title' => __( 'Warna & Tampilan', 'tokoku' ),
                                    'desc'  => __( 'Warna Brand & Mode Gelap', 'tokoku' ),
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
                                'tab-typography' => array( 
                                    'icon'  => 'typography',
                                    'color' => 'blue',
                                    'title' => __( 'Font & Tipografi', 'tokoku' ),
                                    'desc'  => __( 'Pilihan Font & Ukuran Teks', 'tokoku' ),
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
                    <!-- Tab: General -->
                    <div id="tab-general" class="tokoku-tab-panel active">
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
                    <div id="tab-whatsapp" class="tokoku-tab-panel">
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
                                    get_theme_mod( 'tokoku_wa_message', "Halo Admin,\n\nSaya ingin memesan produk berikut:\n\n*Produk:* {produk}\n*Harga:* {harga}\n*Jumlah:* {jumlah}\n*Nama:* {nama}\n*Catatan:* {catatan}\n\nTerima kasih." ), 
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
                            <p class="description"><?php _e( 'Placeholder: {produk}, {sku}, {link}, {harga}, {jumlah}, {nama}, {catatan}', 'tokoku' ); ?></p>
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
                    <div id="tab-appearance" class="tokoku-tab-panel">
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

                    <!-- Tab: Kontak & Workshop -->
                    <div id="tab-contact" class="tokoku-tab-panel">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                            <div>
                                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #0f172a;"><?php _e( 'Kontak, Workshop & Layanan Pelanggan', 'tokoku' ); ?></h2>
                                <p class="description" style="margin-top: 6px; font-size: 0.95rem;"><?php _e( 'Kelola alamat fisik workshop, link navigasi Google Maps, nomor telepon kantor, jam kerja, serta tim Customer Relation Officer (CRO). Data ini otomatis terhubung ke Halaman Kontak dan Footer toko Anda.', 'tokoku' ); ?></p>
                            </div>
                            <div>
                                <a href="<?php echo esc_url( home_url( '/kontak/' ) ); ?>" target="_blank" rel="noopener" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                                    <span class="dashicons dashicons-external" style="margin-top: 2px;"></span>
                                    <?php _e( 'Pratinjau Halaman Kontak', 'tokoku' ); ?>
                                </a>
                            </div>
                        </div>

                        <!-- 1. Alamat Fisik & Peta Workshop -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-location" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( 'Lokasi & Alamat Fisik Workshop / Kantor', 'tokoku' ); ?>
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
                                    <input type="email" name="tokoku_store_email" value="<?php echo esc_attr( get_theme_mod( 'tokoku_store_email' ) ); ?>" placeholder="Contoh: halo@jualplakat.com">
                                    <p class="tokoku-tip"><?php _e( 'Email untuk menerima berkas PO tender, invoice resmi, dan surat penawaran harga.', 'tokoku' ); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Jam Operasional -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-clock" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( 'Jadwal & Jam Operasional Pelayanan', 'tokoku' ); ?>
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

                        <!-- 3. Tim Customer Relation Officer (CRO) -->
                        <div class="tokoku-settings-group">
                            <h3 style="display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-groups" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php _e( 'Tim Customer Relation Officer (CRO) & Spesialisasi Layanan', 'tokoku' ); ?>
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
                                                    <span class="dashicons dashicons-whatsapp" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span> Uji Chat WA
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

                        <?php tokoku_render_tab_save_button( __( 'Simpan Informasi Kontak & Workshop', 'tokoku' ) ); ?>
                    </div>

                    <!-- Tab: Single Product (Lead Time & Trust Badges) -->
                    <div id="tab-single-product" class="tokoku-tab-panel">
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
                    <div id="tab-testimonials" class="tokoku-tab-panel">
                        <h2><?php _e( 'Testimoni & Logo Klien', 'tokoku' ); ?></h2>
                        
                        <!-- Testimonials Vertical Tabs -->
                        <div class="tokoku-settings-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h3 style="margin:0;"><?php _e( 'Ulasan Klien (Testimoni)', 'tokoku' ); ?></h3>
                                <button type="button" class="tokoku-btn-add tokoku-add-testi"><span class="dashicons dashicons-plus-alt2"></span><span class="tokoku-btn-text"><?php _e( 'Tambah Testimoni', 'tokoku' ); ?></span></button>
                            </div>
                            
                            <div class="tokoku-vtabs-container">
                                <div class="tokoku-vtabs-nav testi-nav">
                                    <?php for ( $i = 1; $i <= 20; $i++ ) : 
                                        $has_content = get_theme_mod( "tokoku_testi_name_{$i}" ) || get_theme_mod( "tokoku_testi_text_{$i}" );
                                        $display = $i === 1 || $has_content ? '' : 'display:none;';
                                        $active = $i === 1 ? 'active' : '';
                                    ?>
                                        <div class="tokoku-vtab-link <?php echo $active; ?>" data-target="testi-panel-<?php echo $i; ?>" style="<?php echo $display; ?>">
                                            <span><?php printf( __( 'Testimoni #%d', 'tokoku' ), $i ); ?></span>
                                            <?php if ($i > 1): ?><i class="tokoku-remove-unit-v" title="Hapus">×</i><?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <div class="tokoku-vtabs-content testi-content">
                                    <?php for ( $i = 1; $i <= 20; $i++ ) : 
                                        $active = $i === 1 ? 'active' : '';
                                    ?>
                                        <div class="tokoku-vtab-panel <?php echo $active; ?>" id="testi-panel-<?php echo $i; ?>">
                                            <div class="tokoku-field">
                                                <label><?php _e( 'Foto Klien', 'tokoku' ); ?></label>
                                                <div class="tokoku-media-upload">
                                                    <img src="<?php echo esc_url( get_theme_mod( "tokoku_testi_img_{$i}" ) ); ?>" class="tokoku-preview-img" style="<?php echo get_theme_mod( "tokoku_testi_img_{$i}" ) ? '' : 'display:none;'; ?>">
                                                    <input type="hidden" name="tokoku_testi_img_<?php echo $i; ?>" value="<?php echo esc_attr( get_theme_mod( "tokoku_testi_img_{$i}" ) ); ?>">
                                                    <button type="button" class="button tokoku-upload-btn"><span class="dashicons dashicons-upload"></span><span class="tokoku-btn-text"><?php _e( 'Pilih Foto', 'tokoku' ); ?></span></button>
                                                    <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( "tokoku_testi_img_{$i}" ) ? '' : 'display:none;'; ?>"><span class="dashicons dashicons-trash"></span><span class="tokoku-btn-text"><?php _e( 'Hapus', 'tokoku' ); ?></span></button>
                                                </div>
                                            </div>
                                            <div class="tokoku-field">
                                                <label><?php _e( 'Nama Klien', 'tokoku' ); ?></label>
                                                <input type="text" name="tokoku_testi_name_<?php echo $i; ?>" value="<?php echo esc_attr( get_theme_mod( "tokoku_testi_name_{$i}" ) ); ?>">
                                            </div>
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
                                            <div class="tokoku-field">
                                                <label><?php _e( 'Ulasan/Pesan', 'tokoku' ); ?></label>
                                                <textarea name="tokoku_testi_text_<?php echo $i; ?>" rows="4"><?php echo esc_textarea( get_theme_mod( "tokoku_testi_text_{$i}" ) ); ?></textarea>
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
                    <div id="tab-slider" class="tokoku-tab-panel">
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
                    <div id="tab-social" class="tokoku-tab-panel">
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
                    <div id="tab-footer" class="tokoku-tab-panel">
                        <h2><?php _e( 'Konten Footer', 'tokoku' ); ?></h2>

                        <div style="background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 12px; padding: 14px 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="dashicons dashicons-location" style="color: #0d9488; font-size: 22px; width: 22px; height: 22px;"></span>
                                <span style="font-size: 13px; color: #134e4a;">
                                    <?php _e( 'Alamat, Link Google Maps, Nomor Telepon, Jam Kerja & CRO kini memiliki pengaturan terpusat di tab <strong>Kontak & Workshop</strong>.', 'tokoku' ); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=tokoku-settings&tab=tab-contact' ) ); ?>" class="button button-small" style="font-weight: 600; color: #0d9488; border-color: #99f6e4; background: #ffffff;">
                                <?php _e( 'Buka Kontak & Workshop', 'tokoku' ); ?> &rarr;
                            </a>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Teks Copyright', 'tokoku' ); ?></label>
                            <input type="text" name="tokoku_footer_copyright" value="<?php echo esc_attr( get_theme_mod( 'tokoku_footer_copyright', '© {year} TokoKu. All rights reserved.' ) ); ?>">
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Alamat Toko', 'tokoku' ); ?></label>
                            <textarea name="tokoku_store_address"><?php echo esc_textarea( get_theme_mod( 'tokoku_store_address' ) ); ?></textarea>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Memberikan rasa aman bagi pelanggan dengan mengetahui lokasi fisik operasional toko Anda.', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Email Toko', 'tokoku' ); ?></label>
                            <input type="email" name="tokoku_store_email" value="<?php echo esc_attr( get_theme_mod( 'tokoku_store_email' ) ); ?>">
                        </div>

                        <div class="tokoku-settings-group" style="margin-top: 30px; padding: 20px; background: #f0f0f1; border-radius: 8px;">
                            <h3><?php _e( 'Hubungi Kami & Jam Operasional', 'tokoku' ); ?></h3>
                            <div class="tokoku-field">
                                <label><?php _e( 'Teks Deskripsi', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_hubungi_kami_desc" value="<?php echo esc_attr( get_theme_mod( 'tokoku_hubungi_kami_desc', 'Customer Relation Officer (CRO) kami siap membantu Anda.' ) ); ?>">
                            </div>
                            
                            <div class="tokoku-field">
                                <label><?php _e( 'Kontak WhatsApp', 'tokoku' ); ?></label>
                                <?php for ($i=1; $i<=5; $i++) : ?>
                                    <div style="display:flex; gap:10px; margin-bottom:10px;">
                                        <input type="text" name="tokoku_contact_name_<?php echo $i; ?>" placeholder="Nama <?php echo $i; ?>" value="<?php echo esc_attr( get_theme_mod("tokoku_contact_name_{$i}") ); ?>" style="flex:1;">
                                        <input type="text" name="tokoku_contact_wa_<?php echo $i; ?>" placeholder="No. WA (628...)" value="<?php echo esc_attr( get_theme_mod("tokoku_contact_wa_{$i}") ); ?>" style="flex:1;">
                                    </div>
                                <?php endfor; ?>
                            </div>

                            <div class="tokoku-field">
                                <label><?php _e( 'Jam Operasional', 'tokoku' ); ?></label>
                                <input type="text" name="tokoku_jam_op_1" placeholder="Senin - Jumat: 08.30 - 16.30" value="<?php echo esc_attr( get_theme_mod('tokoku_jam_op_1', 'Senin - Jumat: 08.30 - 16.30') ); ?>" style="margin-bottom:10px; width:100%;">
                                <input type="text" name="tokoku_jam_op_2" placeholder="Sabtu: 08.30 - 16.00" value="<?php echo esc_attr( get_theme_mod('tokoku_jam_op_2', 'Sabtu: 08.30 - 16.00') ); ?>" style="margin-bottom:10px; width:100%;">
                                <input type="text" name="tokoku_jam_op_3" placeholder="Minggu / Tanggal Merah: Libur" value="<?php echo esc_attr( get_theme_mod('tokoku_jam_op_3', 'Minggu / Tanggal Merah: Libur') ); ?>" style="width:100%;">
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: Typography -->
                    <div id="tab-typography" class="tokoku-tab-panel">
                        <h2><?php _e( 'Pengaturan Tipografi', 'tokoku' ); ?></h2>
                        <p class="description"><?php _e( 'Sesuaikan jenis dan ukuran font untuk seluruh website Anda.', 'tokoku' ); ?></p>
                        
                        <div class="tokoku-settings-group" style="margin-top: 20px; padding: 20px; background: #f0f0f1; border-radius: 8px;">
                            <h3><?php _e( 'Jenis Font (Typography)', 'tokoku' ); ?></h3>
                            <?php 
                            $fonts = array(
                                'Merriweather'      => 'Merriweather (Editorial Serif — Rekomendasi Body)',
                                'Inter'             => 'Inter (Clean & Modern — Rekomendasi Headings)',
                                'Plus Jakarta Sans' => 'Plus Jakarta Sans',
                                'Poppins'           => 'Poppins',
                                'Roboto'            => 'Roboto',
                                'Montserrat'        => 'Montserrat',
                                'Open Sans'         => 'Open Sans',
                                'Lato'              => 'Lato',
                                'Quicksand'         => 'Quicksand',
                                'Nunito'            => 'Nunito',
                                'Playfair Display'  => 'Playfair Display',
                            );
                            ?>
                            <div class="tokoku-field">
                                <label><?php _e( 'Font Utama (Body)', 'tokoku' ); ?></label>
                                <select name="tokoku_font_body">
                                    <?php foreach ( $fonts as $val => $lbl ) : ?>
                                        <option value="<?php echo esc_attr( $val ); ?>" <?php selected( get_theme_mod( 'tokoku_font_body', 'Merriweather' ), $val ); ?>><?php echo esc_html( $lbl ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="tokoku-tip"><?php _e( 'Kegunaan: Font ini akan digunakan untuk seluruh teks deskripsi, artikel, dan informasi produk.', 'tokoku' ); ?></p>
                            </div>
                            <div class="tokoku-field">
                                <label><?php _e( 'Font Judul (Headings)', 'tokoku' ); ?></label>
                                <select name="tokoku_font_headings">
                                    <?php foreach ( $fonts as $val => $lbl ) : ?>
                                        <option value="<?php echo esc_attr( $val ); ?>" <?php selected( get_theme_mod( 'tokoku_font_headings', 'Inter' ), $val ); ?>><?php echo esc_html( $lbl ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="tokoku-tip"><?php _e( 'Kegunaan: Font khusus untuk Judul Section (H1, H2, H3) agar terlihat lebih menonjol dan berkarakter.', 'tokoku' ); ?></p>
                            </div>
                        </div>

                        <div class="tokoku-settings-group" style="margin-top: 20px; padding: 20px; background: #f0f0f1; border-radius: 8px;">
                            <h3><?php _e( 'Ukuran Font', 'tokoku' ); ?></h3>
                            <div class="tokoku-field">
                                <label><?php _e( 'Ukuran Teks Dasar (Desktop)', 'tokoku' ); ?></label>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <input type="number" name="tokoku_font_size_base" value="<?php echo esc_attr( get_theme_mod( 'tokoku_font_size_base', 16 ) ); ?>" min="12" max="24" style="width:80px;">
                                    <span>px</span>
                                </div>
                                <p class="tokoku-tip"><?php _e( 'Standar: 16px. Semakin besar ukuran font, website akan semakin mudah dibaca.', 'tokoku' ); ?></p>
                            </div>
                            <div class="tokoku-field">
                                <label><?php _e( 'Skala Judul (H1 Size)', 'tokoku' ); ?></label>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <input type="number" name="tokoku_font_size_h1" value="<?php echo esc_attr( get_theme_mod( 'tokoku_font_size_h1', 2.5 ) ); ?>" step="0.1" min="1" max="5" style="width:80px;">
                                    <span>rem</span>
                                </div>
                            </div>
                        </div>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: SEO -->
                    <div id="tab-seo" class="tokoku-tab-panel">
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
                    <div id="tab-faq" class="tokoku-tab-panel">
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
                    <div id="tab-update" class="tokoku-tab-panel">
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
                    <div id="tab-import-export" class="tokoku-tab-panel">
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
