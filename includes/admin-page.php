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

    // 7. Redirect with Success Parameter
    $redirect_url = add_query_arg( 
        array( 
            'page'             => 'tokoku-settings', 
            'settings-updated' => 'true' 
        ), 
        admin_url( 'admin.php' ) 
    );
    
    wp_safe_redirect( $redirect_url );
    exit;
}
add_action( 'admin_post_tokoku_save_settings', 'tokoku_save_admin_settings' );


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
    $allowed_hosts = array( 'codeload.github.com', 'api.github.com', 'objects.githubusercontent.com' );
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
    
    WP_Filesystem();
    global $wp_filesystem;

    $temp_file = download_url( $download_url );
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
    unlink( $temp_file );

    if ( is_wp_error( $unzipped ) ) {
        $wp_filesystem->delete( $unzip_dir, true );
        wp_send_json_error( $unzipped->get_error_message() );
    }

    // Find the inner folder
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

    $source = $unzip_dir . '/' . $inner_folder;
    $destination = get_template_directory();

    // Copy files
    $copy_result = copy_dir( $source, $destination );

    // Clean up
    $wp_filesystem->delete( $unzip_dir, true );

    if ( is_wp_error( $copy_result ) ) {
        wp_send_json_error( $copy_result->get_error_message() );
    }

    wp_send_json_success( 'Theme updated' );
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

            <div class="tokoku-admin-header">
                <div class="tokoku-admin-logo">
                    <span class="dashicons dashicons-store"></span>
                    <h1>Tokoku by M.alfiandi Ismet</h1>
                </div>
                <div class="tokoku-admin-header-actions">
                    <button type="submit" class="button button-primary tokoku-top-save-btn">
                        <span class="dashicons dashicons-saved"></span> <?php _e( 'Simpan Semua Perubahan', 'tokoku' ); ?>
                    </button>
                    <div class="tokoku-admin-version">v<?php echo TOKOKU_VERSION; ?></div>
                </div>
            </div>

            <div class="tokoku-settings-container">
                <div class="tokoku-settings-nav tokoku-sortable-nav">
                    <?php
                    // Define all tabs
                    $all_tabs = array(
                        'tab-general'      => array( 'icon' => 'admin-generic', 'label' => __( 'General', 'tokoku' ) ),
                        'tab-whatsapp'     => array( 'icon' => 'whatsapp',      'label' => __( 'WhatsApp', 'tokoku' ) ),
                        'tab-appearance'   => array( 'icon' => 'art',           'label' => __( 'Appearance', 'tokoku' ) ),
                        'tab-slider'       => array( 'icon' => 'images-alt2',   'label' => __( 'Banner Slider', 'tokoku' ) ),
                        'tab-testimonials' => array( 'icon' => 'testimonial',   'label' => __( 'Testimoni & Logo', 'tokoku' ) ),
                        'tab-social'       => array( 'icon' => 'share',         'label' => __( 'Social Media', 'tokoku' ) ),
                        'tab-footer'       => array( 'icon' => 'editor-insertmore', 'label' => __( 'Footer', 'tokoku' ) ),
                        'tab-typography'   => array( 'icon' => 'editor-paragraph',  'label' => __( 'Typography', 'tokoku' ) ),
                        'tab-seo'          => array( 'icon' => 'google',        'label' => __( 'SEO & Meta', 'tokoku' ) ),
                        'tab-faq'          => array( 'icon' => 'editor-help',    'label' => __( 'FAQ', 'tokoku' ) ),
                        'tab-update'       => array( 'icon' => 'update',         'label' => __( 'Pembaruan Tema', 'tokoku' ) ),
                        'tab-import-export'=> array( 'icon' => 'migrate',       'label' => __( 'Import & Ekspor', 'tokoku' ) ),
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
                            <span class="dashicons dashicons-<?php echo esc_attr( $tab['icon'] ); ?>"></span> <?php echo esc_html( $tab['label'] ); ?>
                            <span class="tokoku-drag-handle dashicons dashicons-menu" style="margin-left: auto; color: #ccc; opacity: 0.5;"></span>
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
                                <button type="button" class="button tokoku-upload-btn-id"><?php _e( 'Pilih Ikon', 'tokoku' ); ?></button>
                                <button type="button" class="button tokoku-remove-btn" style="<?php echo $icon_url ? '' : 'display:none;'; ?>"><?php _e( 'Hapus', 'tokoku' ); ?></button>
                            </div>
                            <p class="tokoku-tip"><?php _e( 'Rekomendasi: Gambar persegi, minimal 512x512 pixel. Ikon ini akan muncul di tab browser dan ikon aplikasi mobile.', 'tokoku' ); ?></p>
                        </div>
                        
                        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">
                        <div class="tokoku-field">
                            <label><?php _e( 'Logo Light Mode', 'tokoku' ); ?></label>
                            <div class="tokoku-media-upload">
                                <img src="<?php echo esc_url( get_theme_mod( 'tokoku_logo_light' ) ); ?>" class="tokoku-preview-img" style="<?php echo get_theme_mod( 'tokoku_logo_light' ) ? '' : 'display:none;'; ?>">
                                <input type="hidden" name="tokoku_logo_light" value="<?php echo esc_attr( get_theme_mod( 'tokoku_logo_light' ) ); ?>">
                                <button type="button" class="button tokoku-upload-btn"><?php _e( 'Pilih Gambar', 'tokoku' ); ?></button>
                                <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( 'tokoku_logo_light' ) ? '' : 'display:none;'; ?>"><?php _e( 'Hapus', 'tokoku' ); ?></button>
                            </div>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Logo yang akan muncul saat website berada dalam Mode Terang (Light Mode).', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Logo Dark Mode', 'tokoku' ); ?></label>
                            <div class="tokoku-media-upload">
                                <img src="<?php echo esc_url( get_theme_mod( 'tokoku_logo_dark' ) ); ?>" class="tokoku-preview-img" style="<?php echo get_theme_mod( 'tokoku_logo_dark' ) ? '' : 'display:none;'; ?>">
                                <input type="hidden" name="tokoku_logo_dark" value="<?php echo esc_attr( get_theme_mod( 'tokoku_logo_dark' ) ); ?>">
                                <button type="button" class="button tokoku-upload-btn"><?php _e( 'Pilih Gambar', 'tokoku' ); ?></button>
                                <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( 'tokoku_logo_dark' ) ? '' : 'display:none;'; ?>"><?php _e( 'Hapus', 'tokoku' ); ?></button>
                            </div>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Logo yang akan muncul saat website berada dalam Mode Gelap (Dark Mode). Pastikan menggunakan logo warna terang.', 'tokoku' ); ?></p>
                        </div>
                        <div class="tokoku-field">
                            <label><?php _e( 'Deskripsi Singkat Toko', 'tokoku' ); ?></label>
                            <textarea name="tokoku_site_description"><?php echo esc_textarea( get_theme_mod( 'tokoku_site_description', get_bloginfo( 'description' ) ) ); ?></textarea>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Kalimat ini akan muncul di Google dan Footer; buatlah semenarik mungkin untuk meningkatkan kepercayaan pelanggan.', 'tokoku' ); ?></p>
                        </div>

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
                    </div>

                    <!-- Tab: Testimonials & Logos -->
                    <div id="tab-testimonials" class="tokoku-tab-panel">
                        <h2><?php _e( 'Testimoni & Logo Klien', 'tokoku' ); ?></h2>
                        
                        <!-- Testimonials Vertical Tabs -->
                        <div class="tokoku-settings-group" style="margin-bottom: 30px; padding: 20px; background: #f0f0f1; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h3 style="margin:0;"><?php _e( 'Ulasan Klien (Testimoni)', 'tokoku' ); ?></h3>
                                <button type="button" class="button button-primary tokoku-add-testi" style="display: inline-flex; align-items: center; gap: 8px;"><span class="dashicons dashicons-plus-alt2"></span> <?php _e( 'Tambah Testimoni', 'tokoku' ); ?></button>
                            </div>
                            
                            <div class="tokoku-vtabs-container">
                                <div class="tokoku-vtabs-nav testi-nav" style="max-height: 500px; overflow-y: auto;">
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
                                                    <button type="button" class="button tokoku-upload-btn"><?php _e( 'Pilih Foto', 'tokoku' ); ?></button>
                                                    <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( "tokoku_testi_img_{$i}" ) ? '' : 'display:none;'; ?>"><?php _e( 'Hapus', 'tokoku' ); ?></button>
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
                        <div class="tokoku-settings-group" style="padding: 20px; background: #f0f0f1; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h3 style="margin:0;"><?php _e( 'Logo Klien / Partner', 'tokoku' ); ?></h3>
                                <button type="button" class="button button-primary tokoku-add-logo" style="display: inline-flex; align-items: center; gap: 8px;"><span class="dashicons dashicons-plus-alt2"></span> <?php _e( 'Tambah Logo', 'tokoku' ); ?></button>
                            </div>
                            
                            <div class="tokoku-vtabs-container">
                                <div class="tokoku-vtabs-nav logo-nav" style="max-height: 500px; overflow-y: auto;">
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
                                                    <button type="button" class="button tokoku-upload-btn"><?php _e( 'Pilih Logo', 'tokoku' ); ?></button>
                                                    <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( "tokoku_client_logo_{$i}" ) ? '' : 'display:none;'; ?>"><?php _e( 'Hapus', 'tokoku' ); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Slider -->
                    <div id="tab-slider" class="tokoku-tab-panel">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h2 style="margin:0;"><?php _e( 'Banner Slider', 'tokoku' ); ?></h2>
                            <button type="button" class="button button-primary tokoku-add-slider" style="display: inline-flex; align-items: center; gap: 8px;"><span class="dashicons dashicons-plus-alt2"></span> <?php _e( 'Tambah Banner', 'tokoku' ); ?></button>
                        </div>
                        <p class="description" style="margin-bottom:20px;"><?php _e( 'Atur banner promosi utama yang tampil di halaman depan.', 'tokoku' ); ?></p>
                        
                        <div class="tokoku-vtabs-container">
                            <div class="tokoku-vtabs-nav slider-nav" style="max-height: 500px; overflow-y: auto;">
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
                                                <button type="button" class="button tokoku-upload-btn"><?php _e( 'Pilih Banner', 'tokoku' ); ?></button>
                                                <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( "tokoku_slide_image_{$i}" ) ? '' : 'display:none;'; ?>"><?php _e( 'Hapus', 'tokoku' ); ?></button>
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
                    </div>

                    <!-- Tab: SEO -->
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
                                <button type="button" class="button tokoku-upload-btn"><?php _e( 'Pilih Gambar', 'tokoku' ); ?></button>
                                <button type="button" class="button tokoku-remove-btn" style="<?php echo get_theme_mod( 'tokoku_seo_og_image' ) ? '' : 'display:none;'; ?>"><?php _e( 'Hapus', 'tokoku' ); ?></button>
                            </div>
                            <p class="tokoku-tip"><?php _e( 'Kegunaan: Memberikan tampilan profesional saat link website dibagikan ke calon pembeli.', 'tokoku' ); ?></p>
                        </div>
                    </div>

                    <!-- Tab: Import/Export -->
                    <div id="tab-import-export" class="tokoku-tab-panel">
                        <h2><?php _e( 'Backup & Restore Pengaturan', 'tokoku' ); ?></h2>
                        <p class="description" style="margin-bottom: 20px;">
                            <?php _e( 'Gunakan fitur ini untuk mencadangkan seluruh konfigurasi tema Anda atau memindahkannya ke website lain.', 'tokoku' ); ?>
                        </p>

                        <div class="tokoku-settings-group" style="padding: 25px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 30px;">
                            <h3 style="margin-top:0; color: #1e293b;"><span class="dashicons dashicons-download"></span> <?php _e( 'Ekspor Pengaturan', 'tokoku' ); ?></h3>
                            <p><?php _e( 'Klik tombol di bawah untuk mengunduh file cadangan (.json) berisi semua pengaturan tema Anda saat ini.', 'tokoku' ); ?></p>
                            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=tokoku_export_settings' ), 'tokoku_export_action' ) ); ?>" class="button button-secondary">
                                <?php _e( 'Unduh File Cadangan', 'tokoku' ); ?>
                            </a>
                        </div>

                        <div class="tokoku-settings-group" style="padding: 25px; background: #fff7ed; border: 1px solid #ffedd5; border-radius: 12px;">
                            <h3 style="margin-top:0; color: #9a3412;"><span class="dashicons dashicons-upload"></span> <?php _e( 'Impor Pengaturan', 'tokoku' ); ?></h3>
                            <p><?php _e( 'Pilih file cadangan (.json) yang telah Anda unduh sebelumnya untuk memulihkan pengaturan.', 'tokoku' ); ?></p>
                            
                            <div style="margin-top: 15px; display: flex; gap: 10px; align-items: center;">
                                <input type="file" name="tokoku_import_file" id="tokoku_import_file" accept=".json">
                                <button type="submit" name="tokoku_trigger_import" value="1" class="button button-primary" onclick="return confirm('Peringatan: Seluruh pengaturan saat ini akan ditimpa oleh data dari file impor. Lanjutkan?');">
                                    <?php _e( 'Mulai Impor', 'tokoku' ); ?>
                                </button>
                            </div>
                            <p class="tokoku-tip" style="color: #c2410c; border-left-color: #f97316;">
                                <?php _e( 'PENTING: Proses impor akan menimpa pengaturan yang ada sekarang. Pastikan Anda sudah memiliki cadangan jika diperlukan.', 'tokoku' ); ?>
                            </p>
                        </div>
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
                                <div class="tokoku-collapsible-item faq-item-row" data-index="<?php echo $i; ?>" style="margin-bottom: 10px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; <?php echo $style; ?>">
                                    <div class="tokoku-collapsible-header" style="padding: 12px 20px; background: #f8fafc; cursor: pointer; display: flex; align-items: center; justify-content: space-between; font-weight: 600;">
                                        <span><span class="dashicons dashicons-editor-help" style="margin-right:8px; color:#007bff;"></span> <span><?php echo esc_html( $display_q ); ?></span></span>
                                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                                    </div>
                                    <div class="tokoku-collapsible-content" style="padding: 20px; background: #fff; display: none; border-top: 1px solid #e2e8f0;">
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
                                        <button type="button" class="tokoku-remove-faq button-link-delete" style="color: #d63638; text-decoration: none; font-size: 12px; margin-top: 10px; display: inline-block;"><?php _e( 'Hapus Item Ini', 'tokoku' ); ?></button>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <div style="margin-top: 20px;">
                            <button type="button" id="tokoku-add-faq" class="button button-secondary" style="height: 40px; padding: 0 20px; display: flex; align-items: center; gap: 8px;">
                                <span class="dashicons dashicons-plus-alt2"></span> <?php _e( 'Tambah Pertanyaan', 'tokoku' ); ?>
                            </button>
                        </div>
                    </div>


                    <!-- Tab: Update -->
                    <div id="tab-update" class="tokoku-tab-panel">
                        <h2><?php _e( 'Pembaruan Tema TokoKu', 'tokoku' ); ?></h2>
                        <div class="tokoku-update-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; margin-top: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: flex-start; gap: 20px;">
                                <div style="width: 60px; height: 60px; background: #f0f7ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #007bff; flex-shrink: 0;">
                                    <span class="dashicons dashicons-update" style="font-size: 32px; width: 32px; height: 32px;"></span>
                                </div>
                                <div>
                                    <h3 style="margin: 0 0 5px 0; font-size: 1.2rem;"><?php _e( 'Versi Saat Ini:', 'tokoku' ); ?> <span style="color: #007bff;">v<?php echo TOKOKU_VERSION; ?></span></h3>
                                    <p style="margin: 0; color: #64748b; font-size: 0.95rem;"><?php _e( 'Pastikan tema Anda selalu menggunakan versi terbaru untuk fitur dan keamanan terbaik.', 'tokoku' ); ?></p>
                                </div>
                            </div>

                            <hr style="margin: 25px 0; border: none; border-top: 1px solid #eee;">

                            <div id="tokoku-update-status" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: none;">
                                <!-- Status will be injected here -->
                            </div>

                            <button type="button" id="tokoku-check-update" class="button button-primary" style="height: 44px; padding: 0 25px; font-weight: 600; border-radius: 6px; display: flex; align-items: center; gap: 10px;">
                                <span class="dashicons dashicons-search"></span> <?php _e( 'Cek Pembaruan Sekarang', 'tokoku' ); ?>
                            </button>
                            
                            <div id="tokoku-update-loader" style="display: none; margin-top: 15px; align-items: center; gap: 10px; color: #64748b;">
                                <div class="tokoku-spinner"></div>
                                <span><?php _e( 'Menghubungkan ke server pembaruan...', 'tokoku' ); ?></span>
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
                                <div style="flex: 1; min-width: 250px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    <h4 style="margin: 0 0 10px 0;"><span class="dashicons dashicons-download"></span> <?php _e( 'Ekspor Pengaturan', 'tokoku' ); ?></h4>
                                    <p style="font-size: 0.9rem; color: #64748b;"><?php _e( 'Unduh file JSON yang berisi semua pengaturan tema Anda saat ini.', 'tokoku' ); ?></p>
                                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=tokoku_export_settings' ), 'tokoku_export_action' ) ); ?>" class="button button-primary">
                                        <?php _e( 'Ekspor File .JSON', 'tokoku' ); ?>
                                    </a>
                                </div>
                                
                                <div style="flex: 1; min-width: 250px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    <h4 style="margin: 0 0 10px 0;"><span class="dashicons dashicons-upload"></span> <?php _e( 'Impor Pengaturan', 'tokoku' ); ?></h4>
                                    <p style="font-size: 0.9rem; color: #64748b;"><?php _e( 'Pilih file JSON dari komputer Anda untuk memulihkan pengaturan tema.', 'tokoku' ); ?></p>
                                    
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <input type="file" name="tokoku_import_file" id="tokoku_import_file" accept=".json" style="max-width: 200px;">
                                        <button type="submit" name="tokoku_import_action" value="import_settings" class="button button-secondary" onclick="return confirm('Peringatan: Pengaturan tema Anda saat ini akan tertimpa. Lanjutkan?');">
                                            <?php _e( 'Mulai Impor', 'tokoku' ); ?>
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
                                    <a href="<?php echo esc_url( admin_url( 'export.php' ) ); ?>" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <span class="dashicons dashicons-media-archive"></span> <?php _e( 'Ekspor Seluruh Data (XML)', 'tokoku' ); ?>
                                    </a>
                                </div>
                                <div style="flex: 1; min-width: 250px;">
                                    <a href="<?php echo esc_url( admin_url( 'import.php' ) ); ?>" class="button button-secondary" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <span class="dashicons dashicons-database-import"></span> <?php _e( 'Alat Impor WordPress', 'tokoku' ); ?>
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
