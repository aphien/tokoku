<?php
/**
 * Dedicated Dashboard & AJAX Controller for TokoKu Bulk Product Editor
 *
 * @package TokoKu_Bulk_Editor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Tokoku_Bulk_Editor_Admin_Page' ) ) :

class Tokoku_Bulk_Editor_Admin_Page {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // AJAX endpoints
        add_action( 'wp_ajax_tokoku_bulk_get_products', array( $this, 'ajax_get_products' ) );
        add_action( 'wp_ajax_tokoku_bulk_update_batch', array( $this, 'ajax_update_batch' ) );
        add_action( 'wp_ajax_tokoku_bulk_inline_update', array( $this, 'ajax_inline_update' ) );
    }

    /**
     * Register Submenu Page di bawah menu 'Produk'
     */
    public function register_menu_page() {
        add_submenu_page(
            'edit.php?post_type=produk',
            __( 'Edit Masal Produk', 'tokoku-bulk-editor' ),
            __( 'Edit Masal Produk', 'tokoku-bulk-editor' ),
            'edit_posts',
            'tokoku-bulk-product-editor',
            array( $this, 'render_dashboard' )
        );

        // Backward compatibility slug handler
        if ( isset( $_GET['page'] ) && 'tokoku-bulk-stock-editor' === $_GET['page'] ) {
            wp_safe_redirect( admin_url( 'edit.php?post_type=produk&page=tokoku-bulk-product-editor' ) );
            exit;
        }
    }

    /**
     * Enqueue CSS & JS untuk Dashboard
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'tokoku-bulk-product-editor' ) === false && strpos( $hook, 'tokoku-bulk-stock-editor' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'tokoku-bulk-admin-css',
            TOKOKU_BULK_EDITOR_URL . 'assets/css/admin-bulk.css',
            array( 'dashicons' ),
            TOKOKU_BULK_EDITOR_VERSION
        );

        // Inject active theme accent colors for bulk editor buttons
        $theme_primary   = get_theme_mod( 'tokoku_primary_color', '#2563eb' );
        $theme_secondary = get_theme_mod( 'tokoku_secondary_color', '#1d4ed8' );
        $admin_accent_css = "
            :root {
                --tb-primary: " . esc_attr( $theme_primary ) . ";
                --tb-primary-dark: " . esc_attr( $theme_secondary ) . ";
            }
            #btn-apply-bulk,
            .wp-core-ui #btn-apply-bulk.button.button-primary.button-hero {
                background: linear-gradient(135deg, " . esc_attr( $theme_primary ) . ", " . esc_attr( $theme_secondary ) . ") !important;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18) !important;
            }
            #btn-apply-bulk:hover,
            .wp-core-ui #btn-apply-bulk.button.button-primary.button-hero:hover {
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25) !important;
            }
        ";
        wp_add_inline_style( 'tokoku-bulk-admin-css', $admin_accent_css );

        wp_enqueue_script(
            'tokoku-bulk-admin-js',
            TOKOKU_BULK_EDITOR_URL . 'assets/js/admin-bulk.js',
            array( 'jquery' ),
            TOKOKU_BULK_EDITOR_VERSION,
            true
        );

        wp_localize_script( 'tokoku-bulk-admin-js', 'tokokuBulkData', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'tokoku_bulk_editor_nonce' ),
            'i18n'     => array(
                'loading'          => __( 'Memuat data produk...', 'tokoku-bulk-editor' ),
                'no_products'      => __( 'Tidak ada produk yang sesuai dengan kriteria filter.', 'tokoku-bulk-editor' ),
                'select_prompt'    => __( 'Silakan centang setidaknya satu produk terlebih dahulu.', 'tokoku-bulk-editor' ),
                'action_prompt'    => __( 'Pilih setidaknya satu opsi perubahan masal (Harga, Stok, Kategori, atau Label).', 'tokoku-bulk-editor' ),
                'confirm_title'    => __( 'Konfirmasi Perubahan Masal', 'tokoku-bulk-editor' ),
                'confirm_msg'      => __( 'Apakah Anda yakin ingin menerapkan perubahan ini ke %d produk yang dipilih?', 'tokoku-bulk-editor' ),
                'processing_batch' => __( 'Memproses batch %d dari %d (%s%%)...', 'tokoku-bulk-editor' ),
                'success_title'    => __( 'Berhasil Diperbarui!', 'tokoku-bulk-editor' ),
                'success_msg'      => __( '%d produk berhasil diperbarui secara masal.', 'tokoku-bulk-editor' ),
                'saved'            => __( 'Tersimpan!', 'tokoku-bulk-editor' ),
                'error'            => __( 'Terjadi kesalahan saat memproses data.', 'tokoku-bulk-editor' ),
            ),
        ) );
    }

    /**
     * AJAX: Mengambil daftar produk berdasarkan filter dan halaman
     */
    public function ajax_get_products() {
        check_ajax_referer( 'tokoku_bulk_editor_nonce', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( array( 'message' => __( 'Akses ditolak.', 'tokoku-bulk-editor' ) ) );
        }

        $paged    = isset( $_POST['paged'] ) ? max( 1, intval( $_POST['paged'] ) ) : 1;
        $per_page = isset( $_POST['per_page'] ) ? intval( $_POST['per_page'] ) : 30;
        if ( -1 !== $per_page && ( $per_page < 5 || $per_page > 100 ) ) {
            $per_page = 30;
        }

        $search       = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
        $category     = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
        $stock_filter = isset( $_POST['stock_status'] ) ? sanitize_text_field( wp_unslash( $_POST['stock_status'] ) ) : '';
        $disc_filter  = isset( $_POST['discount_status'] ) ? sanitize_text_field( wp_unslash( $_POST['discount_status'] ) ) : '';
        $post_status  = isset( $_POST['post_status'] ) ? sanitize_text_field( wp_unslash( $_POST['post_status'] ) ) : '';
        $orderby_req  = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'date_desc';

        $args = array(
            'post_type'      => 'produk',
            'posts_per_page' => $per_page,
            'paged'          => $paged,
        );

        // Status Post
        if ( ! empty( $post_status ) && 'all' !== $post_status ) {
            $args['post_status'] = $post_status;
        } else {
            $args['post_status'] = array( 'publish', 'draft', 'pending' );
        }

        // Sorting
        switch ( $orderby_req ) {
            case 'date_asc':
                $args['orderby'] = 'date';
                $args['order']   = 'ASC';
                break;
            case 'title_asc':
                $args['orderby'] = 'title';
                $args['order']   = 'ASC';
                break;
            case 'title_desc':
                $args['orderby'] = 'title';
                $args['order']   = 'DESC';
                break;
            case 'price_asc':
                $args['meta_key'] = '_produk_harga';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'price_desc':
                $args['meta_key'] = '_produk_harga';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'stock_asc':
                $args['meta_key'] = '_produk_jumlah_stok';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'date_desc':
            default:
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
        }

        // Filter Kategori
        if ( ! empty( $category ) && 'all' !== $category ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'kategori_produk',
                    'field'    => is_numeric( $category ) ? 'term_id' : 'slug',
                    'terms'    => $category,
                ),
            );
        }

        $meta_queries = array();

        // Filter Status Stok
        if ( ! empty( $stock_filter ) && 'all' !== $stock_filter ) {
            if ( 'tersedia' === $stock_filter ) {
                $meta_queries[] = array(
                    'relation' => 'OR',
                    array(
                        'key'     => '_produk_stok',
                        'value'   => 'tersedia',
                        'compare' => '=',
                    ),
                    array(
                        'key'     => '_produk_stok',
                        'compare' => 'NOT EXISTS',
                    ),
                );
            } else {
                $meta_queries[] = array(
                    'key'     => '_produk_stok',
                    'value'   => $stock_filter,
                    'compare' => '=',
                );
            }
        }

        // Filter Diskon
        if ( ! empty( $disc_filter ) && 'all' !== $disc_filter ) {
            if ( 'discounted' === $disc_filter ) {
                $meta_queries[] = array(
                    'key'     => '_produk_harga_diskon',
                    'value'   => '0',
                    'compare' => '>',
                    'type'    => 'NUMERIC',
                );
            } elseif ( 'regular' === $disc_filter ) {
                $meta_queries[] = array(
                    'relation' => 'OR',
                    array(
                        'key'     => '_produk_harga_diskon',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'     => '_produk_harga_diskon',
                        'value'   => '',
                        'compare' => '=',
                    ),
                    array(
                        'key'     => '_produk_harga_diskon',
                        'value'   => '0',
                        'compare' => '=',
                    ),
                );
            }
        }

        if ( ! empty( $meta_queries ) ) {
            $args['meta_query'] = array_merge( array( 'relation' => 'AND' ), $meta_queries );
        }

        // Pencarian Nama / SKU
        if ( ! empty( $search ) ) {
            global $wpdb;
            $search_like = '%' . $wpdb->esc_like( $search ) . '%';

            $matched_ids = $wpdb->get_col( $wpdb->prepare( "
                SELECT DISTINCT p.ID 
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id AND pm.meta_key = '_produk_sku')
                WHERE p.post_type = 'produk'
                  AND (p.post_title LIKE %s OR pm.meta_value LIKE %s)
            ", $search_like, $search_like ) );

            if ( ! empty( $matched_ids ) ) {
                $args['post__in'] = $matched_ids;
            } else {
                $args['post__in'] = array( 0 );
            }
        }

        $query    = new WP_Query( $args );
        $products = array();
        $prod_mgr = Tokoku_Product_Manager::get_instance();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id    = get_the_ID();
                $products[] = $prod_mgr->get_product_full_info( $post_id );
            }
            wp_reset_postdata();
        }

        // Hitung Ringkasan Statistik
        $stats = $this->get_quick_product_stats();

        wp_send_json_success( array(
            'products'     => $products,
            'total'        => $query->found_posts,
            'total_pages'  => $query->max_num_pages,
            'current_page' => $paged,
            'per_page'     => $per_page,
            'stats'        => $stats,
        ) );
    }

    /**
     * AJAX: Pembaruan masal batch produk
     */
    public function ajax_update_batch() {
        check_ajax_referer( 'tokoku_bulk_editor_nonce', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( array( 'message' => __( 'Akses ditolak.', 'tokoku-bulk-editor' ) ) );
        }

        $post_ids = isset( $_POST['post_ids'] ) ? array_map( 'intval', (array) $_POST['post_ids'] ) : array();
        if ( empty( $post_ids ) ) {
            wp_send_json_error( array( 'message' => __( 'Tidak ada produk yang dipilih.', 'tokoku-bulk-editor' ) ) );
        }

        // Kumpulkan parameter dari request
        $update_payload = array();

        // 1. Harga
        if ( isset( $_POST['price_mode'] ) && 'no_change' !== $_POST['price_mode'] ) {
            $update_payload['price_mode'] = sanitize_key( wp_unslash( $_POST['price_mode'] ) );
            $update_payload['price_val']  = isset( $_POST['price_val'] ) ? floatval( wp_unslash( $_POST['price_val'] ) ) : 0;
        }

        // 2. Diskon
        if ( isset( $_POST['sale_mode'] ) && 'no_change' !== $_POST['sale_mode'] ) {
            $update_payload['sale_mode'] = sanitize_key( wp_unslash( $_POST['sale_mode'] ) );
            $update_payload['sale_val']  = isset( $_POST['sale_val'] ) ? floatval( wp_unslash( $_POST['sale_val'] ) ) : 0;
        }

        // 3. Stok & Ketersediaan
        if ( isset( $_POST['stok_status'] ) && 'no_change' !== $_POST['stok_status'] ) {
            $update_payload['stok_status'] = sanitize_key( wp_unslash( $_POST['stok_status'] ) );
        }
        if ( isset( $_POST['stok_mode'] ) && 'no_change' !== $_POST['stok_mode'] ) {
            $update_payload['stok_mode']  = sanitize_key( wp_unslash( $_POST['stok_mode'] ) );
            $update_payload['stok_value'] = isset( $_POST['stok_value'] ) ? intval( wp_unslash( $_POST['stok_value'] ) ) : 0;
        }
        $update_payload['auto_sync_status'] = ! empty( $_POST['auto_sync'] );

        // 4. Kategori
        if ( isset( $_POST['category_mode'] ) && 'no_change' !== $_POST['category_mode'] ) {
            $update_payload['category_mode'] = sanitize_key( wp_unslash( $_POST['category_mode'] ) );
            $update_payload['category_ids']  = isset( $_POST['category_ids'] ) ? array_map( 'intval', (array) $_POST['category_ids'] ) : array();
        }

        // 5. Label Khusus Promo
        if ( isset( $_POST['label_mode'] ) && 'no_change' !== $_POST['label_mode'] ) {
            $update_payload['label_mode'] = sanitize_key( wp_unslash( $_POST['label_mode'] ) );
            $update_payload['label_text'] = isset( $_POST['label_text'] ) ? sanitize_text_field( wp_unslash( $_POST['label_text'] ) ) : '';
        }

        // 6. Berat
        if ( isset( $_POST['berat_mode'] ) && 'no_change' !== $_POST['berat_mode'] ) {
            $update_payload['berat_mode'] = sanitize_key( wp_unslash( $_POST['berat_mode'] ) );
            $update_payload['berat_val']  = isset( $_POST['berat_val'] ) ? intval( wp_unslash( $_POST['berat_val'] ) ) : 0;
        }

        // 7. Status Post
        if ( isset( $_POST['post_status'] ) && 'no_change' !== $_POST['post_status'] ) {
            $update_payload['post_status'] = sanitize_key( wp_unslash( $_POST['post_status'] ) );
        }

        $prod_mgr = Tokoku_Product_Manager::get_instance();
        $updated  = array();
        $errors   = array();

        foreach ( $post_ids as $id ) {
            $res = $prod_mgr->update_product( $id, $update_payload );
            if ( $res['success'] ) {
                $updated[] = $res;
            } else {
                $errors[] = $res;
            }
        }

        wp_send_json_success( array(
            'updated_count' => count( $updated ),
            'error_count'   => count( $errors ),
            'updated_items' => $updated,
            'stats'         => $this->get_quick_product_stats(),
        ) );
    }

    /**
     * AJAX: Simpan inline cepat untuk 1 baris produk
     */
    public function ajax_inline_update() {
        check_ajax_referer( 'tokoku_bulk_editor_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Akses ditolak.', 'tokoku-bulk-editor' ) ) );
        }

        $update_payload = array();

        // Harga Normal
        if ( isset( $_POST['price'] ) ) {
            $price_raw = sanitize_text_field( wp_unslash( $_POST['price'] ) );
            $price_clean = preg_replace( '/[^0-9]/', '', $price_raw );
            $update_payload['price_mode'] = 'set';
            $update_payload['price_val']  = '' !== $price_clean ? floatval( $price_clean ) : 0;
        }

        // Harga Diskon
        if ( isset( $_POST['sale_price'] ) ) {
            $sale_raw = sanitize_text_field( wp_unslash( $_POST['sale_price'] ) );
            $sale_clean = preg_replace( '/[^0-9]/', '', $sale_raw );
            if ( '' !== $sale_clean && floatval( $sale_clean ) > 0 ) {
                $update_payload['sale_mode'] = 'set';
                $update_payload['sale_val']  = floatval( $sale_clean );
            } else {
                $update_payload['sale_mode'] = 'remove';
            }
        }

        // Status Stok
        if ( isset( $_POST['stok_status'] ) ) {
            $update_payload['stok_status'] = sanitize_key( wp_unslash( $_POST['stok_status'] ) );
        }

        // Kuantitas Stok
        if ( isset( $_POST['stok_qty'] ) ) {
            $qty_raw = sanitize_text_field( wp_unslash( $_POST['stok_qty'] ) );
            if ( '' !== $qty_raw ) {
                $update_payload['stok_mode']  = 'set';
                $update_payload['stok_value'] = intval( $qty_raw );
            } else {
                $update_payload['stok_mode'] = 'empty';
            }
        }

        $res = Tokoku_Product_Manager::get_instance()->update_product( $post_id, $update_payload );

        if ( $res['success'] ) {
            wp_send_json_success( array(
                'info'  => $res['updated_info'],
                'stats' => $this->get_quick_product_stats(),
            ) );
        } else {
            wp_send_json_error( array( 'message' => $res['message'] ) );
        }
    }

    /**
     * Hitung ringkasan statistik produk
     */
    private function get_quick_product_stats() {
        global $wpdb;

        $total_active = (int) $wpdb->get_var( "
            SELECT COUNT(ID) 
            FROM {$wpdb->posts} 
            WHERE post_type = 'produk' 
              AND post_status IN ('publish', 'draft', 'pending')
        " );

        $habis_count = (int) $wpdb->get_var( "
            SELECT COUNT(p.ID) 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'produk' 
              AND p.post_status IN ('publish', 'draft', 'pending')
              AND pm.meta_key = '_produk_stok'
              AND pm.meta_value = 'habis'
        " );

        $preorder_count = (int) $wpdb->get_var( "
            SELECT COUNT(p.ID) 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'produk' 
              AND p.post_status IN ('publish', 'draft', 'pending')
              AND pm.meta_key = '_produk_stok'
              AND pm.meta_value = 'preorder'
        " );

        $discount_count = (int) $wpdb->get_var( "
            SELECT COUNT(p.ID) 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'produk' 
              AND p.post_status IN ('publish', 'draft', 'pending')
              AND pm.meta_key = '_produk_harga_diskon'
              AND CAST(pm.meta_value AS DECIMAL(10,2)) > 0
        " );

        $tersedia_count = max( 0, $total_active - ( $habis_count + $preorder_count ) );

        return array(
            'total'      => $total_active,
            'tersedia'   => $tersedia_count,
            'habis'      => $habis_count,
            'preorder'   => $preorder_count,
            'discounted' => $discount_count,
        );
    }

    /**
     * Render Tampilan Dashboard
     */
    public function render_dashboard() {
        $stats = $this->get_quick_product_stats();

        $categories = get_terms( array(
            'taxonomy'   => 'kategori_produk',
            'hide_empty' => false,
        ) );
        ?>
        <div class="wrap tokoku-bulk-wrap">
            <!-- Header Card -->
            <div class="tokoku-bulk-header">
                <div class="tokoku-bulk-header__title">
                    <div class="tokoku-bulk-header__icon">
                        <span class="dashicons dashicons-archive" aria-hidden="true"></span>
                    </div>
                    <div>
                        <h1><?php esc_html_e( 'Edit Masal Produk TokoKu', 'tokoku-bulk-editor' ); ?></h1>
                        <p class="tokoku-bulk-header__subtitle">
                            <?php esc_html_e( 'Kelola harga, diskon, stok ketersediaan, kategori, label promo, dan status puluhan hingga ribuan produk secara instan dan aman.', 'tokoku-bulk-editor' ); ?>
                        </p>
                    </div>
                </div>

                <!-- Stats Chips -->
                <div class="tokoku-bulk-stats">
                    <div class="tokoku-stat-pill tokoku-stat-pill--total">
                        <span class="tokoku-stat-pill__label"><?php esc_html_e( 'Total Produk', 'tokoku-bulk-editor' ); ?></span>
                        <strong class="tokoku-stat-pill__val" id="stat-total"><?php echo esc_html( number_format_i18n( $stats['total'] ) ); ?></strong>
                    </div>
                    <div class="tokoku-stat-pill tokoku-stat-pill--tersedia">
                        <span class="tokoku-stat-pill__label"><?php esc_html_e( 'Tersedia', 'tokoku-bulk-editor' ); ?></span>
                        <strong class="tokoku-stat-pill__val" id="stat-tersedia"><?php echo esc_html( number_format_i18n( $stats['tersedia'] ) ); ?></strong>
                    </div>
                    <div class="tokoku-stat-pill tokoku-stat-pill--habis">
                        <span class="tokoku-stat-pill__label"><?php esc_html_e( 'Habis', 'tokoku-bulk-editor' ); ?></span>
                        <strong class="tokoku-stat-pill__val" id="stat-habis"><?php echo esc_html( number_format_i18n( $stats['habis'] ) ); ?></strong>
                    </div>
                    <div class="tokoku-stat-pill tokoku-stat-pill--discount">
                        <span class="tokoku-stat-pill__label"><?php esc_html_e( 'Diskon Aktif', 'tokoku-bulk-editor' ); ?></span>
                        <strong class="tokoku-stat-pill__val" id="stat-discount"><?php echo esc_html( number_format_i18n( $stats['discounted'] ) ); ?></strong>
                    </div>
                </div>
            </div>

            <!-- Bulk Action Controller Panel (Sticky Toolbar) -->
            <div class="tokoku-action-panel">
                <div class="tokoku-action-panel__top">
                    <!-- Selection Indicator -->
                    <div class="tokoku-selection-badge">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <span id="selected-counter-text"><?php esc_html_e( '0 produk dipilih', 'tokoku-bulk-editor' ); ?></span>
                    </div>
                    <button type="button" class="button button-small" id="btn-select-all-page"><?php esc_html_e( 'Pilih Semua di Halaman', 'tokoku-bulk-editor' ); ?></button>
                    <button type="button" class="button button-small" id="btn-deselect-all" style="display:none;"><?php esc_html_e( 'Batal Pilih', 'tokoku-bulk-editor' ); ?></button>

                    <!-- Tab Switchers -->
                    <div class="tokoku-action-tabs">
                        <button type="button" class="tokoku-action-tab active" data-tab="tab-price">
                            <span class="dashicons dashicons-tag"></span> <?php esc_html_e( 'Harga & Diskon', 'tokoku-bulk-editor' ); ?>
                        </button>
                        <button type="button" class="tokoku-action-tab" data-tab="tab-stock">
                            <span class="dashicons dashicons-archive"></span> <?php esc_html_e( 'Stok & Ketersediaan', 'tokoku-bulk-editor' ); ?>
                        </button>
                        <button type="button" class="tokoku-action-tab" data-tab="tab-category">
                            <span class="dashicons dashicons-category"></span> <?php esc_html_e( 'Kategori & Label', 'tokoku-bulk-editor' ); ?>
                        </button>
                        <button type="button" class="tokoku-action-tab" data-tab="tab-status">
                            <span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e( 'Status & Lainnya', 'tokoku-bulk-editor' ); ?>
                        </button>
                    </div>

                    <!-- Tombol Eksekusi Masal -->
                    <button type="button" class="button button-primary button-hero" id="btn-apply-bulk">
                        <span class="dashicons dashicons-saved" aria-hidden="true"></span>
                        <span><?php esc_html_e( 'Terapkan Masal', 'tokoku-bulk-editor' ); ?></span>
                    </button>
                </div>

                <!-- Tab Contents Container -->
                <div class="tokoku-action-panel__body">
                    <!-- TAB 1: HARGA & DISKON -->
                    <div class="tokoku-tab-pane active" id="tab-price">
                        <div class="tokoku-form-grid">
                            <!-- Ubah Harga Normal -->
                            <div class="tokoku-control-group">
                                <label for="bulk-price-mode"><?php esc_html_e( 'Harga Normal:', 'tokoku-bulk-editor' ); ?></label>
                                <select id="bulk-price-mode" class="tokoku-select">
                                    <option value="no_change"><?php esc_html_e( '— Tidak Diubah —', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="set"><?php esc_html_e( 'Tetapkan Harga Pasti (Rp)', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="add_nominal"><?php esc_html_e( 'Naikkan Harga (+) Nominal Rp', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="sub_nominal"><?php esc_html_e( 'Turunkan Harga (-) Nominal Rp', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="add_percent"><?php esc_html_e( 'Naikkan Harga (+) Persen %', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="sub_percent"><?php esc_html_e( 'Turunkan Harga (-) Persen %', 'tokoku-bulk-editor' ); ?></option>
                                </select>
                                <input type="number" min="0" id="bulk-price-val" class="tokoku-input-number" placeholder="Nilai..." style="display:none; width: 130px;">
                            </div>

                            <!-- Ubah Harga Diskon -->
                            <div class="tokoku-control-group">
                                <label for="bulk-sale-mode"><?php esc_html_e( 'Harga Diskon / Coret:', 'tokoku-bulk-editor' ); ?></label>
                                <select id="bulk-sale-mode" class="tokoku-select">
                                    <option value="no_change"><?php esc_html_e( '— Tidak Diubah —', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="set"><?php esc_html_e( 'Tetapkan Harga Diskon Pasti (Rp)', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="percent_of_regular"><?php esc_html_e( 'Diskon % dari Harga Normal', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="remove"><?php esc_html_e( 'Hapus Diskon (Harga Coret Hilang)', 'tokoku-bulk-editor' ); ?></option>
                                </select>
                                <input type="number" min="0" id="bulk-sale-val" class="tokoku-input-number" placeholder="Nilai / %" style="display:none; width: 130px;">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: STOK & KETERSEDIAAN -->
                    <div class="tokoku-tab-pane" id="tab-stock">
                        <div class="tokoku-form-grid">
                            <!-- Status Ketersediaan -->
                            <div class="tokoku-control-group">
                                <label for="bulk-stock-status"><?php esc_html_e( 'Status Ketersediaan:', 'tokoku-bulk-editor' ); ?></label>
                                <select id="bulk-stock-status" class="tokoku-select">
                                    <option value="no_change"><?php esc_html_e( '— Tidak Diubah —', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="tersedia"><?php esc_html_e( '🟢 Tersedia', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="habis"><?php esc_html_e( '🔴 Habis', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="preorder"><?php esc_html_e( '🟡 Pre-Order', 'tokoku-bulk-editor' ); ?></option>
                                </select>
                            </div>

                            <!-- Mode Jumlah Stok -->
                            <div class="tokoku-control-group">
                                <label for="bulk-stock-mode"><?php esc_html_e( 'Jumlah Stok:', 'tokoku-bulk-editor' ); ?></label>
                                <select id="bulk-stock-mode" class="tokoku-select">
                                    <option value="no_change"><?php esc_html_e( '— Tidak Diubah —', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="set"><?php esc_html_e( 'Tetapkan Nilai Pasti', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="add"><?php esc_html_e( 'Tambah Stok (+)', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="subtract"><?php esc_html_e( 'Kurangi Stok (-)', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="empty"><?php esc_html_e( 'Kosongkan (Tak Terbatas)', 'tokoku-bulk-editor' ); ?></option>
                                </select>
                                <input type="number" min="0" id="bulk-stock-val" class="tokoku-input-number" placeholder="0" value="10" style="display:none; width: 90px;">
                            </div>

                            <!-- Auto Sync Checkbox -->
                            <div class="tokoku-control-group tokoku-control-group--checkbox">
                                <label title="<?php esc_attr_e( 'Bila jumlah stok diset ke 0, otomatis status ketersediaan diubah ke Habis', 'tokoku-bulk-editor' ); ?>">
                                    <input type="checkbox" id="bulk-auto-sync" checked value="1">
                                    <span><?php esc_html_e( 'Auto Habis jika 0', 'tokoku-bulk-editor' ); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: KATEGORI & LABEL -->
                    <div class="tokoku-tab-pane" id="tab-category">
                        <div class="tokoku-form-grid">
                            <!-- Kategori Produk -->
                            <div class="tokoku-control-group">
                                <label for="bulk-category-mode"><?php esc_html_e( 'Kategori Produk:', 'tokoku-bulk-editor' ); ?></label>
                                <select id="bulk-category-mode" class="tokoku-select">
                                    <option value="no_change"><?php esc_html_e( '— Tidak Diubah —', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="append"><?php esc_html_e( 'Tambahkan Kategori ke Produk', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="replace"><?php esc_html_e( 'Ganti Total Kategori Produk', 'tokoku-bulk-editor' ); ?></option>
                                </select>

                                <select id="bulk-category-ids" class="tokoku-select" style="display:none; min-width: 180px;">
                                    <option value=""><?php esc_html_e( '— Pilih Kategori —', 'tokoku-bulk-editor' ); ?></option>
                                    <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                                        <?php foreach ( $categories as $cat ) : ?>
                                            <option value="<?php echo esc_attr( $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Label Khusus Promo (Badge) -->
                            <div class="tokoku-control-group">
                                <label for="bulk-label-mode"><?php esc_html_e( 'Label Promo (Badge):', 'tokoku-bulk-editor' ); ?></label>
                                <select id="bulk-label-mode" class="tokoku-select">
                                    <option value="no_change"><?php esc_html_e( '— Tidak Diubah —', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="set"><?php esc_html_e( 'Tetapkan Label Promo', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="remove"><?php esc_html_e( 'Hapus Label Promo', 'tokoku-bulk-editor' ); ?></option>
                                </select>
                                <input type="text" id="bulk-label-text" class="tokoku-select" placeholder="Contoh: PROMO / BEST SELLER" style="display:none; width: 170px;">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: STATUS POST & BERAT -->
                    <div class="tokoku-tab-pane" id="tab-status">
                        <div class="tokoku-form-grid">
                            <!-- Status Post -->
                            <div class="tokoku-control-group">
                                <label for="bulk-post-status"><?php esc_html_e( 'Status Post:', 'tokoku-bulk-editor' ); ?></label>
                                <select id="bulk-post-status" class="tokoku-select">
                                    <option value="no_change"><?php esc_html_e( '— Tidak Diubah —', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="publish"><?php esc_html_e( 'Terbitkan (Publish)', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="draft"><?php esc_html_e( 'Jadikan Draf (Draft)', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="pending"><?php esc_html_e( 'Menunggu Peninjauan (Pending)', 'tokoku-bulk-editor' ); ?></option>
                                </select>
                            </div>

                            <!-- Berat Produk -->
                            <div class="tokoku-control-group">
                                <label for="bulk-berat-mode"><?php esc_html_e( 'Berat Produk (gram):', 'tokoku-bulk-editor' ); ?></label>
                                <select id="bulk-berat-mode" class="tokoku-select">
                                    <option value="no_change"><?php esc_html_e( '— Tidak Diubah —', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="set"><?php esc_html_e( 'Tetapkan Berat (Gram)', 'tokoku-bulk-editor' ); ?></option>
                                    <option value="remove"><?php esc_html_e( 'Kosongkan Berat', 'tokoku-bulk-editor' ); ?></option>
                                </select>
                                <input type="number" min="0" id="bulk-berat-val" class="tokoku-input-number" placeholder="500" style="display:none; width: 100px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="tokoku-filter-bar">
                <div class="tokoku-filter-bar__left">
                    <!-- Cari Produk -->
                    <div class="tokoku-search-box">
                        <span class="dashicons dashicons-search"></span>
                        <input type="search" id="filter-search" placeholder="<?php esc_attr_e( 'Cari nama produk atau SKU...', 'tokoku-bulk-editor' ); ?>">
                    </div>

                    <!-- Filter Kategori -->
                    <select id="filter-category" class="tokoku-select">
                        <option value="all"><?php esc_html_e( 'Semua Kategori Produk', 'tokoku-bulk-editor' ); ?></option>
                        <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                            <?php foreach ( $categories as $cat ) : ?>
                                <option value="<?php echo esc_attr( $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?> (<?php echo esc_html( $cat->count ); ?>)</option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>

                    <!-- Filter Status Stok -->
                    <select id="filter-stock-status" class="tokoku-select">
                        <option value="all"><?php esc_html_e( 'Semua Status Stok', 'tokoku-bulk-editor' ); ?></option>
                        <option value="tersedia"><?php esc_html_e( 'Status: Tersedia', 'tokoku-bulk-editor' ); ?></option>
                        <option value="habis"><?php esc_html_e( 'Status: Habis', 'tokoku-bulk-editor' ); ?></option>
                        <option value="preorder"><?php esc_html_e( 'Status: Pre-Order', 'tokoku-bulk-editor' ); ?></option>
                    </select>

                    <!-- Filter Status Diskon -->
                    <select id="filter-discount-status" class="tokoku-select">
                        <option value="all"><?php esc_html_e( 'Semua Harga', 'tokoku-bulk-editor' ); ?></option>
                        <option value="discounted"><?php esc_html_e( 'Hanya Produk Berdiskon', 'tokoku-bulk-editor' ); ?></option>
                        <option value="regular"><?php esc_html_e( 'Tanpa Diskon', 'tokoku-bulk-editor' ); ?></option>
                    </select>

                    <!-- Filter Status Post -->
                    <select id="filter-post-status" class="tokoku-select">
                        <option value="all"><?php esc_html_e( 'Semua Status Post', 'tokoku-bulk-editor' ); ?></option>
                        <option value="publish"><?php esc_html_e( 'Terbit (Publish)', 'tokoku-bulk-editor' ); ?></option>
                        <option value="draft"><?php esc_html_e( 'Draf (Draft)', 'tokoku-bulk-editor' ); ?></option>
                    </select>

                    <!-- Urutan -->
                    <select id="filter-orderby" class="tokoku-select">
                        <option value="date_desc"><?php esc_html_e( 'Urut: Terbaru', 'tokoku-bulk-editor' ); ?></option>
                        <option value="date_asc"><?php esc_html_e( 'Urut: Terlama', 'tokoku-bulk-editor' ); ?></option>
                        <option value="title_asc"><?php esc_html_e( 'Urut: Nama A-Z', 'tokoku-bulk-editor' ); ?></option>
                        <option value="price_asc"><?php esc_html_e( 'Urut: Harga Termurah', 'tokoku-bulk-editor' ); ?></option>
                        <option value="price_desc"><?php esc_html_e( 'Urut: Harga Tertinggi', 'tokoku-bulk-editor' ); ?></option>
                        <option value="stock_asc"><?php esc_html_e( 'Urut: Stok Terendah', 'tokoku-bulk-editor' ); ?></option>
                    </select>

                    <!-- Reset Filter -->
                    <button type="button" class="button" id="btn-reset-filters" title="<?php esc_attr_e( 'Reset Filter', 'tokoku-bulk-editor' ); ?>">
                        <span class="dashicons dashicons-image-rotate"></span>
                    </button>
                </div>

                <div class="tokoku-filter-bar__right">
                    <!-- Items per page -->
                    <label for="filter-per-page"><?php esc_html_e( 'Tampilkan:', 'tokoku-bulk-editor' ); ?></label>
                    <select id="filter-per-page" class="tokoku-select tokoku-select--sm">
                        <option value="15">15</option>
                        <option value="30" selected>30</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- Table Container -->
            <div class="tokoku-table-container">
                <div class="tokoku-table-overlay" id="table-overlay">
                    <div class="tokoku-spinner-card">
                        <span class="spinner is-active"></span>
                        <span class="tokoku-spinner-text"><?php esc_html_e( 'Memuat produk...', 'tokoku-bulk-editor' ); ?></span>
                    </div>
                </div>

                <table class="wp-list-table widefat fixed striped table-view-list tokoku-products-table">
                    <thead>
                        <tr>
                            <td class="manage-column column-cb check-column">
                                <label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e( 'Pilih Semua', 'tokoku-bulk-editor' ); ?></label>
                                <input id="cb-select-all-1" type="checkbox">
                            </td>
                            <th class="column-thumb" style="width: 60px;"><?php esc_html_e( 'Foto', 'tokoku-bulk-editor' ); ?></th>
                            <th class="column-title"><?php esc_html_e( 'Nama Produk & SKU', 'tokoku-bulk-editor' ); ?></th>
                            <th class="column-category" style="width: 140px;"><?php esc_html_e( 'Kategori', 'tokoku-bulk-editor' ); ?></th>
                            <th class="column-price" style="width: 140px;"><?php esc_html_e( 'Harga Normal (Rp)', 'tokoku-bulk-editor' ); ?></th>
                            <th class="column-sale-price" style="width: 140px;"><?php esc_html_e( 'Harga Diskon (Rp)', 'tokoku-bulk-editor' ); ?></th>
                            <th class="column-status" style="width: 145px;"><?php esc_html_e( 'Ketersediaan', 'tokoku-bulk-editor' ); ?></th>
                            <th class="column-qty" style="width: 100px;"><?php esc_html_e( 'Stok', 'tokoku-bulk-editor' ); ?></th>
                            <th class="column-action" style="width: 80px; text-align: center;"><?php esc_html_e( 'Simpan', 'tokoku-bulk-editor' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="tokoku-product-rows">
                        <!-- Rendered via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="tokoku-pagination-bar" id="tokoku-pagination">
                <div class="tokoku-pagination-bar__info" id="pagination-info">
                    <!-- Info text rendered via JS -->
                </div>
                <div class="tokoku-pagination-bar__controls" id="pagination-controls">
                    <!-- Buttons rendered via JS -->
                </div>
            </div>
        </div>

        <!-- Progress Modal (Batch Processing) -->
        <div class="tokoku-modal-backdrop" id="bulk-progress-modal" style="display: none;">
            <div class="tokoku-modal">
                <div class="tokoku-modal__header">
                    <h3 id="modal-title"><?php esc_html_e( 'Memproses Perubahan Masal...', 'tokoku-bulk-editor' ); ?></h3>
                </div>
                <div class="tokoku-modal__body">
                    <div class="tokoku-progress">
                        <div class="tokoku-progress__bar" id="progress-bar-fill" style="width: 0%;"></div>
                    </div>
                    <div class="tokoku-progress__status">
                        <span id="progress-text"><?php esc_html_e( 'Menyiapkan batch...', 'tokoku-bulk-editor' ); ?></span>
                        <strong id="progress-percent">0%</strong>
                    </div>
                    <div class="tokoku-progress__log" id="progress-log"></div>
                </div>
                <div class="tokoku-modal__footer">
                    <button type="button" class="button button-primary" id="btn-close-modal" style="display:none;"><?php esc_html_e( 'Selesai & Muat Ulang', 'tokoku-bulk-editor' ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
}

endif;
