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
    wp_enqueue_style( 'tokoku-admin-css', TOKOKU_URI . '/assets/css/admin.css', array(), TOKOKU_VERSION );
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
        'tokoku_show_price'       => 'sanitize_text_field',
        'tokoku_currency'         => 'sanitize_text_field',
        
        // Footer & SEO
        'tokoku_footer_copyright' => 'wp_kses_post',
        'tokoku_store_address'    => 'wp_kses_post',
        'tokoku_store_email'      => 'sanitize_email',
        'tokoku_hubungi_kami_desc'=> 'sanitize_text_field',
        'tokoku_jam_op_1'         => 'sanitize_text_field',
        'tokoku_jam_op_2'         => 'sanitize_text_field',
        'tokoku_jam_op_3'         => 'sanitize_text_field',
        'tokoku_seo_desc'         => 'sanitize_textarea_field',
        'tokoku_seo_keywords'     => 'sanitize_text_field',
        'tokoku_seo_og_image'     => 'esc_url_raw',

        // Typography
        'tokoku_font_body'        => 'sanitize_text_field',
        'tokoku_font_headings'    => 'sanitize_text_field',
        'tokoku_font_size_base'   => 'absint',
        'tokoku_font_size_h1'     => 'sanitize_text_field',

        // FAQ
        'tokoku_faq_title'        => 'sanitize_text_field',
        'tokoku_faq_subtitle'     => 'sanitize_text_field',

        // Menu Order
        'tokoku_admin_menu_order' => 'sanitize_text_field',
    );

    // FAQ Repeater
    for ( $i = 1; $i <= 10; $i++ ) {
        $settings_schema["tokoku_faq_q_{$i}"] = 'sanitize_text_field';
        $settings_schema["tokoku_faq_a_{$i}"] = 'wp_kses_post';
    }

    // Contacts Repeater
    for ( $i = 1; $i <= 5; $i++ ) {
        $settings_schema["tokoku_contact_name_{$i}"] = 'sanitize_text_field';
        $settings_schema["tokoku_contact_wa_{$i}"]   = 'sanitize_text_field';
    }

    // 4. Handle Repeater Settings (Slides, Socials, Testimonials, Logos)
    
    // Banner Slider
    for ( $i = 1; $i <= 10; $i++ ) {
        $settings_schema["tokoku_slide_image_{$i}"] = 'esc_url_raw';
        $settings_schema["tokoku_slide_link_{$i}"]  = 'esc_url_raw';
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
 * Helper: Mengembalikan SVG Icon Crisp untuk Admin Tabs
 */
function tokoku_get_admin_icon( $icon_key ) {
    switch ( $icon_key ) {
        case 'general':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58a.49.49 0 0 0 .12-.61l-1.92-3.32a.488.488 0 0 0-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 0 0-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58a.49.49 0 0 0-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>';
        case 'whatsapp':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.4-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.11-.23-.17-.48-.29z"/></svg>';
        case 'appearance':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L4.35 19.4c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.9-1.9C9.22 19.57 10.57 20 12 20c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9 8 9.67 8 10.5 7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>';
        case 'slider':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>';
        case 'testimonials':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
        case 'social':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>';
        case 'footer':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M4 3h16c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2zm0 2v8h16V5H4zm0 10v4h16v-4H4z"/></svg>';
        case 'typography':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M9.93 13.5h4.14L12 7.98zM20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-4.05 16.5l-1.14-3H9.17l-1.12 3H5.96l5.11-13h1.86l5.11 13h-2.09z"/></svg>';
        case 'seo':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/><path d="M12 10l-2-2-2 2v2h4z"/></svg>';
        case 'faq':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 12h-2v-2h2v2zm1.07-4.75l-.9.92C12.45 10.9 12 11.5 12 13h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.67z"/></svg>';
        case 'update':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>';
        case 'import-export':
            return '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>';
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
        wp_send_json_error( 'Akses ditolak. Hanya administrator yang dapat memperbarui tema.' );
    }

    $download_url = isset( $_POST['download_url'] ) ? esc_url_raw( trim( $_POST['download_url'] ) ) : '';
    if ( empty( $download_url ) ) {
        wp_send_json_error( 'URL unduhan tidak ditemukan. Coba klik "Cek Pembaruan" terlebih dahulu.' );
    }

    // 🛡️ Security: Only allow downloads from GitHub
    $allowed_hosts = array( 'codeload.github.com', 'api.github.com', 'objects.githubusercontent.com', 'github.com', 'raw.githubusercontent.com' );
    $url_host = parse_url( $download_url, PHP_URL_HOST );
    $is_allowed_host = in_array( $url_host, $allowed_hosts, true );
    $allowed_path = 'aphien/tokoku';

    if ( ! $is_allowed_host || strpos( $download_url, $allowed_path ) === false ) {
        wp_send_json_error( 'Sumber pembaruan tidak diizinkan. Hanya repositori resmi aphien/tokoku yang diperbolehkan.' );
    }

    // Load WordPress file/upgrade APIs
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/misc.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    // Force 'direct' filesystem method to avoid FTP prompts on local/shared servers
    add_filter( 'filesystem_method', function() { return 'direct'; } );

    if ( ! WP_Filesystem() ) {
        wp_send_json_error( 'Gagal menginisialisasi sistem file WordPress. Pastikan direktori tema dapat ditulis (writable).' );
    }

    global $wp_filesystem;

    // Step 1: Download ZIP
    $temp_file = download_url( $download_url, 300 );
    if ( is_wp_error( $temp_file ) ) {
        wp_send_json_error( 'Gagal mengunduh file: ' . $temp_file->get_error_message() . ' | URL: ' . esc_url( $download_url ) );
    }

    // Step 2: Prepare temp extraction directory
    $upgrade_dir = WP_CONTENT_DIR . '/upgrade';
    if ( ! is_dir( $upgrade_dir ) ) {
        wp_mkdir_p( $upgrade_dir );
    }

    $unzip_dir = $upgrade_dir . '/tokoku_temp_' . time();
    wp_mkdir_p( $unzip_dir );

    // Step 3: Unzip
    $unzipped = unzip_file( $temp_file, $unzip_dir );
    @unlink( $temp_file ); // Clean up temp download

    if ( is_wp_error( $unzipped ) ) {
        $wp_filesystem->delete( $unzip_dir, true );
        wp_send_json_error( 'Gagal mengekstrak ZIP: ' . $unzipped->get_error_message() );
    }

    // Step 4: Find inner folder (GitHub ZIPs wrap content in a subfolder)
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
        wp_send_json_error( 'Struktur ZIP tidak valid — folder tema tidak ditemukan di dalam arsip.' );
    }

    $source      = trailingslashit( $unzip_dir ) . $inner_folder;
    $destination = get_template_directory();

    // Step 5: Check destination is writable
    if ( ! $wp_filesystem->is_writable( $destination ) ) {
        $wp_filesystem->delete( $unzip_dir, true );
        wp_send_json_error( 'Direktori tema tidak dapat ditulis: ' . esc_html( $destination ) . '. Periksa permission folder (chmod 755).' );
    }

    // Step 6: Copy extracted files to theme directory
    $copy_result = copy_dir( $source, $destination );

    // Clean up extraction temp dir
    $wp_filesystem->delete( $unzip_dir, true );

    if ( is_wp_error( $copy_result ) ) {
        wp_send_json_error( 'Gagal menyalin file tema: ' . $copy_result->get_error_message() );
    }

    // Step 7: Flush opcache & theme mods cache so new version is active immediately
    if ( function_exists( 'opcache_reset' ) ) {
        opcache_reset();
    }
    wp_clean_themes_cache();

    wp_send_json_success( 'Tema TokoKu berhasil diperbarui ke versi terbaru. Halaman akan dimuat ulang.' );
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
                    <h1>Tokoku by M.alfiandi Ismet</h1>
                </div>
                <div class="tokoku-admin-header-actions">
                    <button type="submit" class="button button-primary tokoku-submit-update-btn tokoku-top-save-btn" id="tokoku-top-save">
                        <span class="tokoku-btn-icon">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm2 16H5V5h11.17L19 7.83V19zm-7-7c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zM6 6h9v4H6z"/></svg>
                        </span>
                        <span class="tokoku-btn-text"><?php _e( 'Perbarui Pengaturan', 'tokoku' ); ?></span>
                    </button>
                    <div class="tokoku-admin-version">v<?php echo TOKOKU_VERSION; ?></div>
                </div>
            </div>

            <div class="tokoku-settings-container">
                <div class="tokoku-settings-nav tokoku-sortable-nav">
                    <?php
                    // Define all tabs with modern icons
                    $all_tabs = array(
                        'tab-general'      => array( 'icon' => 'general',      'label' => __( 'General', 'tokoku' ) ),
                        'tab-whatsapp'     => array( 'icon' => 'whatsapp',     'label' => __( 'WhatsApp', 'tokoku' ) ),
                        'tab-appearance'   => array( 'icon' => 'appearance',   'label' => __( 'Appearance', 'tokoku' ) ),
                        'tab-slider'       => array( 'icon' => 'slider',       'label' => __( 'Banner Slider', 'tokoku' ) ),
                        'tab-testimonials' => array( 'icon' => 'testimonials', 'label' => __( 'Testimoni & Logo', 'tokoku' ) ),
                        'tab-social'       => array( 'icon' => 'social',       'label' => __( 'Social Media', 'tokoku' ) ),
                        'tab-footer'       => array( 'icon' => 'footer',       'label' => __( 'Footer', 'tokoku' ) ),
                        'tab-typography'   => array( 'icon' => 'typography',   'label' => __( 'Typography', 'tokoku' ) ),
                        'tab-seo'          => array( 'icon' => 'seo',          'label' => __( 'SEO & Meta', 'tokoku' ) ),
                        'tab-faq'          => array( 'icon' => 'faq',          'label' => __( 'FAQ', 'tokoku' ) ),
                        'tab-update'       => array( 'icon' => 'update',       'label' => __( 'Pembaruan Tema', 'tokoku' ) ),
                        'tab-import-export'=> array( 'icon' => 'import-export','label' => __( 'Import & Ekspor', 'tokoku' ) ),
                    );

                    // Get saved order or use default
                    $saved_order = get_theme_mod( 'tokoku_admin_menu_order', 'tab-general,tab-whatsapp,tab-appearance,tab-slider,tab-testimonials,tab-social,tab-footer,tab-typography,tab-seo,tab-faq,tab-update,tab-import-export' );
                    $order_array = explode( ',', $saved_order );
                    
                    // Filter out any tabs that no longer exist
                    $order_array = array_filter( $order_array, function($tab_id) use ($all_tabs) {
                        return isset( $all_tabs[$tab_id] );
                    });

                    // Add any new tabs that are missing from saved order
                    foreach ( $all_tabs as $tab_id => $data ) {
                        if ( ! in_array( $tab_id, $order_array ) ) {
                            $order_array[] = $tab_id;
                        }
                    }

                    $first_tab = true;
                    foreach ( $order_array as $tab_id ) : 
                        $tab = $all_tabs[$tab_id];
                        $active_class = $first_tab ? 'active' : '';
                        ?>
                        <div class="tokoku-nav-item <?php echo esc_attr( $active_class ); ?>" data-tab="<?php echo esc_attr( $tab_id ); ?>">
                            <span class="tokoku-nav-icon">
                                <?php echo tokoku_get_admin_icon( $tab['icon'] ); ?>
                            </span>
                            <span class="tokoku-nav-label"><?php echo esc_html( $tab['label'] ); ?></span>
                            <span class="tokoku-drag-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Geser untuk mengatur urutan', 'tokoku' ); ?>"></span>
                        </div>
                    <?php 
                        $first_tab = false;
                    endforeach; ?>
                </div>
                <input type="hidden" name="tokoku_admin_menu_order" id="tokoku_admin_menu_order" value="<?php echo esc_attr( $saved_order ); ?>">

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
                                <label><?php echo ucfirst( $social ); ?></label>
                                <input type="url" name="tokoku_social_<?php echo $social; ?>" value="<?php echo esc_url( get_theme_mod( "tokoku_social_{$social}" ) ); ?>" placeholder="https://...">
                            </div>
                        <?php endforeach; ?>
                        <p class="tokoku-tip"><?php _e( 'Kegunaan: Membangun kepercayaan (Trust) pelanggan dengan menunjukkan eksistensi toko Anda di berbagai platform.', 'tokoku' ); ?></p>

                        <?php tokoku_render_tab_save_button(); ?>
                    </div>

                    <!-- Tab: Footer -->
                    <div id="tab-footer" class="tokoku-tab-panel">
                        <h2><?php _e( 'Konten Footer', 'tokoku' ); ?></h2>
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
                                'Plus Jakarta Sans' => 'Plus Jakarta Sans',
                                'Inter'             => 'Inter',
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
                                        <option value="<?php echo esc_attr( $val ); ?>" <?php selected( get_theme_mod( 'tokoku_font_body', 'Plus Jakarta Sans' ), $val ); ?>><?php echo esc_html( $lbl ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="tokoku-tip"><?php _e( 'Kegunaan: Font ini akan digunakan untuk seluruh teks deskripsi, artikel, dan informasi produk.', 'tokoku' ); ?></p>
                            </div>
                            <div class="tokoku-field">
                                <label><?php _e( 'Font Judul (Headings)', 'tokoku' ); ?></label>
                                <select name="tokoku_font_headings">
                                    <?php foreach ( $fonts as $val => $lbl ) : ?>
                                        <option value="<?php echo esc_attr( $val ); ?>" <?php selected( get_theme_mod( 'tokoku_font_headings', 'Plus Jakarta Sans' ), $val ); ?>><?php echo esc_html( $lbl ); ?></option>
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
                        <div class="tokoku-update-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; margin-top: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: flex-start; gap: 20px;">
                                <div style="width: 60px; height: 60px; background: #eff6ff; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #007bff; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,123,255,0.15);">
                                    <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
                                </div>
                                <div>
                                    <h3 style="margin: 0 0 5px 0; font-size: 1.25rem; font-weight: 800; color: #0f172a;"><?php _e( 'Versi Tema Saat Ini:', 'tokoku' ); ?> <span style="color: #007bff;">v<?php echo TOKOKU_VERSION; ?></span></h3>
                                    <p style="margin: 0; color: #64748b; font-size: 0.95rem;"><?php _e( 'Pastikan tema Anda selalu menggunakan versi terbaru untuk fitur dan keamanan terbaik.', 'tokoku' ); ?></p>
                                </div>
                            </div>

                            <hr style="margin: 25px 0; border: none; border-top: 1px solid #e2e8f0;">

                            <div id="tokoku-update-status" style="margin-bottom: 20px; padding: 15px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; display: none;">
                                <!-- Status will be injected here -->
                            </div>

                            <button type="button" id="tokoku-check-update" class="button button-primary tokoku-check-update-btn">
                                <span class="dashicons dashicons-search"></span><span class="tokoku-btn-text"><?php _e( 'Cek Pembaruan Sekarang', 'tokoku' ); ?></span>
                            </button>
                            
                            <div id="tokoku-update-loader" style="display: none; margin-top: 15px; align-items: center; gap: 10px; color: #007bff; font-weight: 600;">
                                <span class="spinner is-active" style="float: none; margin: 0;"></span>
                                <span><?php _e( 'Menghubungkan ke server pembaruan GitHub...', 'tokoku' ); ?></span>
                            </div>
                        </div>

                        <div style="margin-top: 30px; background: #fff8e1; border-left: 4px solid #ffc107; padding: 20px; border-radius: 4px;">
                            <h4 style="margin: 0 0 10px 0; color: #856404;"><span class="dashicons dashicons-warning" style="vertical-align: middle;"></span> <?php _e( 'Penting:', 'tokoku' ); ?></h4>
                            <p style="margin: 0; font-size: 0.9rem; color: #856404; line-height: 1.5;">
                                <?php _e( 'Selalu lakukan backup pengaturan tema Anda di tab "Impor & Ekspor" sebelum melakukan pembaruan besar untuk mencegah kehilangan konfigurasi.', 'tokoku' ); ?>
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
