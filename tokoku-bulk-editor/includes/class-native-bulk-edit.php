<?php
/**
 * Native WordPress Bulk Edit & Quick Edit Integration for TokoKu Produk
 *
 * @package TokoKu_Bulk_Editor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Tokoku_Native_Bulk_Edit' ) ) :

class Tokoku_Native_Bulk_Edit {

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
        // Kolom Admin & Filter List Table
        add_filter( 'manage_produk_posts_columns', array( $this, 'add_admin_columns' ), 20 );
        add_action( 'manage_produk_posts_custom_column', array( $this, 'render_admin_columns' ), 20, 2 );
        add_action( 'restrict_manage_posts', array( $this, 'render_stock_filter_dropdown' ) );
        add_action( 'pre_get_posts', array( $this, 'filter_posts_by_stock_status' ) );

        // Quick Edit & Bulk Edit Form Fields
        add_action( 'bulk_edit_custom_box', array( $this, 'render_bulk_edit_fields' ), 10, 2 );
        add_action( 'quick_edit_custom_box', array( $this, 'render_quick_edit_fields' ), 10, 2 );

        // Simpan Data Bulk & Quick Edit
        add_action( 'save_post_produk', array( $this, 'save_bulk_and_quick_edit' ), 10, 2 );

        // Enqueue Assets di Halaman List Produk
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_list_assets' ) );
    }

    /**
     * Enqueue asset di edit.php?post_type=produk
     */
    public function enqueue_list_assets( $hook ) {
        if ( 'edit.php' !== $hook ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || 'produk' !== $screen->post_type ) {
            return;
        }

        wp_enqueue_style(
            'tokoku-native-bulk-edit-css',
            TOKOKU_BULK_EDITOR_URL . 'assets/css/admin-native-edit.css',
            array(),
            TOKOKU_BULK_EDITOR_VERSION
        );

        wp_enqueue_script(
            'tokoku-native-bulk-edit-js',
            TOKOKU_BULK_EDITOR_URL . 'assets/js/admin-native-edit.js',
            array( 'jquery', 'inline-edit-post' ),
            TOKOKU_BULK_EDITOR_VERSION,
            true
        );

        wp_localize_script( 'tokoku-native-bulk-edit-js', 'tokokuNativeData', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'tokoku_native_bulk_nonce' ),
        ) );
    }

    /**
     * Tambahkan Kolom Harga & Stok di Tabel Admin Produk
     */
    public function add_admin_columns( $columns ) {
        $new_columns = array();

        foreach ( $columns as $key => $title ) {
            $new_columns[ $key ] = $title;
            // Tempatkan setelah kolom Foto atau Kode Produk
            if ( 'kode_produk' === $key || ( 'title' === $key && ! isset( $columns['kode_produk'] ) ) ) {
                $new_columns['tokoku_price'] = __( 'Harga', 'tokoku-bulk-editor' );
                $new_columns['tokoku_stock'] = __( 'Ketersediaan & Stok', 'tokoku-bulk-editor' );
            }
        }

        if ( ! isset( $new_columns['tokoku_price'] ) ) {
            $new_columns['tokoku_price'] = __( 'Harga', 'tokoku-bulk-editor' );
        }
        if ( ! isset( $new_columns['tokoku_stock'] ) ) {
            $new_columns['tokoku_stock'] = __( 'Ketersediaan & Stok', 'tokoku-bulk-editor' );
        }

        return $new_columns;
    }

    /**
     * Render Isi Kolom Kustom
     */
    public function render_admin_columns( $column, $post_id ) {
        $info = Tokoku_Product_Manager::get_instance()->get_product_full_info( $post_id );
        if ( ! $info ) {
            return;
        }

        if ( 'tokoku_price' === $column ) {
            printf(
                '<div class="tokoku-price-cell" data-post-id="%d" data-price="%s" data-sale-price="%s">',
                esc_attr( $post_id ),
                esc_attr( $info['raw_price'] ),
                esc_attr( $info['raw_sale_price'] )
            );

            if ( ! empty( $info['price'] ) ) {
                if ( $info['has_discount'] ) {
                    echo '<div class="tokoku-price-sale">Rp ' . esc_html( $info['sale_price'] ) . '</div>';
                    echo '<div class="tokoku-price-reg-del"><del>Rp ' . esc_html( $info['price'] ) . '</del> <span class="tokoku-discount-badge">-' . esc_html( $info['discount_percent'] ) . '%</span></div>';
                } else {
                    echo '<div class="tokoku-price-normal">Rp ' . esc_html( $info['price'] ) . '</div>';
                }
            } else {
                echo '<span style="color:#94a3b8;">—</span>';
            }

            echo '</div>';
        }

        if ( 'tokoku_stock' === $column ) {
            $status = $info['status'];
            $qty    = $info['qty'];

            // Output data hidden untuk dibaca Quick Edit JS
            printf(
                '<div class="tokoku-stock-cell" data-post-id="%d" data-status="%s" data-qty="%s">',
                esc_attr( $post_id ),
                esc_attr( $status ),
                esc_attr( null !== $qty ? $qty : '' )
            );

            switch ( $status ) {
                case 'tersedia':
                    echo '<span class="tokoku-stock-badge tokoku-stock-badge--tersedia">';
                    echo '<span class="tokoku-dot"></span> ' . esc_html__( 'Tersedia', 'tokoku-bulk-editor' );
                    if ( null !== $qty ) {
                        echo ' <strong class="tokoku-stock-qty">(' . esc_html( $qty ) . ')</strong>';
                    }
                    echo '</span>';
                    break;

                case 'habis':
                    echo '<span class="tokoku-stock-badge tokoku-stock-badge--habis">';
                    echo '<span class="tokoku-dot"></span> ' . esc_html__( 'Habis', 'tokoku-bulk-editor' );
                    if ( null !== $qty ) {
                        echo ' <strong class="tokoku-stock-qty">(' . esc_html( $qty ) . ')</strong>';
                    }
                    echo '</span>';
                    break;

                case 'preorder':
                    echo '<span class="tokoku-stock-badge tokoku-stock-badge--preorder">';
                    echo '<span class="tokoku-dot"></span> ' . esc_html__( 'Pre-Order', 'tokoku-bulk-editor' );
                    if ( null !== $qty ) {
                        echo ' <strong class="tokoku-stock-qty">(' . esc_html( $qty ) . ')</strong>';
                    }
                    echo '</span>';
                    break;

                default:
                    echo '<span class="tokoku-stock-badge">' . esc_html( $info['status_label'] ) . '</span>';
                    break;
            }

            echo '</div>';
        }
    }

    /**
     * Filter Dropdown Status Stok di atas tabel produk
     */
    public function render_stock_filter_dropdown() {
        $screen = get_current_screen();
        if ( ! $screen || 'produk' !== $screen->post_type ) {
            return;
        }

        $current = isset( $_GET['filter_produk_stok'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_produk_stok'] ) ) : '';
        ?>
        <select name="filter_produk_stok" id="filter-produk-stok">
            <option value=""><?php esc_html_e( 'Semua Status Stok', 'tokoku-bulk-editor' ); ?></option>
            <option value="tersedia" <?php selected( $current, 'tersedia' ); ?>><?php esc_html_e( 'Tersedia', 'tokoku-bulk-editor' ); ?></option>
            <option value="habis" <?php selected( $current, 'habis' ); ?>><?php esc_html_e( 'Habis', 'tokoku-bulk-editor' ); ?></option>
            <option value="preorder" <?php selected( $current, 'preorder' ); ?>><?php esc_html_e( 'Pre-Order', 'tokoku-bulk-editor' ); ?></option>
        </select>
        <?php
    }

    /**
     * Terapkan Filter Query berdasarkan status stok
     */
    public function filter_posts_by_stock_status( $query ) {
        global $pagenow;

        if ( is_admin() && 'edit.php' === $pagenow && $query->is_main_query() && 'produk' === $query->get( 'post_type' ) ) {
            if ( ! empty( $_GET['filter_produk_stok'] ) ) {
                $status = sanitize_key( wp_unslash( $_GET['filter_produk_stok'] ) );
                $meta_query = $query->get( 'meta_query' );
                if ( ! is_array( $meta_query ) ) {
                    $meta_query = array();
                }

                if ( 'tersedia' === $status ) {
                    $meta_query[] = array(
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
                    $meta_query[] = array(
                        'key'     => '_produk_stok',
                        'value'   => $status,
                        'compare' => '=',
                    );
                }

                $query->set( 'meta_query', $meta_query );
            }
        }
    }

    /**
     * Render Input Tambahan di Form Quick Edit (Baris Tunggal)
     */
    public function render_quick_edit_fields( $column_name, $post_type ) {
        if ( 'produk' !== $post_type || 'tokoku_stock' !== $column_name ) {
            return;
        }

        wp_nonce_field( 'tokoku_native_quick_edit_action', 'tokoku_native_quick_nonce' );
        ?>
        <fieldset class="inline-edit-col-right inline-edit-tokoku-custom">
            <div class="inline-edit-col">
                <span class="title" style="font-weight: 700; color: #1e293b;"><?php esc_html_e( 'Pengaturan Produk TokoKu', 'tokoku-bulk-editor' ); ?></span>
                
                <!-- Harga Normal & Diskon -->
                <div class="tokoku-qe-row" style="margin-top: 8px;">
                    <label class="alignleft" style="margin-right: 15px;">
                        <span class="title"><?php esc_html_e( 'Harga Normal (Rp)', 'tokoku-bulk-editor' ); ?></span>
                        <input type="number" min="0" name="tokoku_quick_harga" class="tokoku-quick-price-input" value="" placeholder="150000" style="width: 130px;">
                    </label>
                    <label class="alignleft">
                        <span class="title"><?php esc_html_e( 'Harga Diskon (Rp)', 'tokoku-bulk-editor' ); ?></span>
                        <input type="number" min="0" name="tokoku_quick_harga_diskon" class="tokoku-quick-sale-input" value="" placeholder="120000" style="width: 130px;">
                    </label>
                </div>

                <!-- Ketersediaan & Stok -->
                <div class="tokoku-qe-row" style="margin-top: 8px;">
                    <label class="alignleft" style="margin-right: 15px;">
                        <span class="title"><?php esc_html_e( 'Ketersediaan', 'tokoku-bulk-editor' ); ?></span>
                        <select name="tokoku_quick_stok_status" class="tokoku-quick-status-select">
                            <option value="tersedia"><?php esc_html_e( 'Tersedia', 'tokoku-bulk-editor' ); ?></option>
                            <option value="habis"><?php esc_html_e( 'Habis', 'tokoku-bulk-editor' ); ?></option>
                            <option value="preorder"><?php esc_html_e( 'Pre-Order', 'tokoku-bulk-editor' ); ?></option>
                        </select>
                    </label>
                    <label class="alignleft">
                        <span class="title"><?php esc_html_e( 'Jumlah Stok', 'tokoku-bulk-editor' ); ?></span>
                        <input type="number" min="0" name="tokoku_quick_jumlah_stok" class="tokoku-quick-qty-input" value="" placeholder="<?php esc_attr_e( 'Tak Terbatas', 'tokoku-bulk-editor' ); ?>" style="width: 130px;">
                    </label>
                </div>
            </div>
        </fieldset>
        <?php
    }

    /**
     * Render Input Tambahan di Form Bulk Edit (Banyak Produk Sekaligus)
     */
    public function render_bulk_edit_fields( $column_name, $post_type ) {
        if ( 'produk' !== $post_type || 'tokoku_stock' !== $column_name ) {
            return;
        }

        wp_nonce_field( 'tokoku_native_bulk_edit_action', 'tokoku_native_bulk_nonce' );
        ?>
        <fieldset class="inline-edit-col-right inline-edit-tokoku-bulk">
            <div class="inline-edit-col" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px; margin-top: 8px;">
                <strong style="display:block; margin-bottom: 10px; color: #0f172a; font-size: 13px;">
                    ⚡ <?php esc_html_e( 'Edit Masal Produk TokoKu (Harga & Stok)', 'tokoku-bulk-editor' ); ?>
                </strong>

                <!-- Harga Masal -->
                <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 10px;">
                    <label class="alignleft" style="min-width: 180px;">
                        <span class="title" style="display: block; margin-bottom: 4px;"><?php esc_html_e( 'Harga Normal', 'tokoku-bulk-editor' ); ?></span>
                        <select name="tokoku_bulk_price_mode" id="tokoku-bulk-price-mode-select" style="width: 100%;">
                            <option value="no_change"><?php esc_html_e( '— Tidak Berubah —', 'tokoku-bulk-editor' ); ?></option>
                            <option value="set"><?php esc_html_e( 'Tetapkan Nilai Pasti (Rp)', 'tokoku-bulk-editor' ); ?></option>
                            <option value="add_nominal"><?php esc_html_e( 'Naikkan (+) Nominal Rp', 'tokoku-bulk-editor' ); ?></option>
                            <option value="sub_nominal"><?php esc_html_e( 'Turunkan (-) Nominal Rp', 'tokoku-bulk-editor' ); ?></option>
                            <option value="add_percent"><?php esc_html_e( 'Naikkan (+) Persen %', 'tokoku-bulk-editor' ); ?></option>
                            <option value="sub_percent"><?php esc_html_e( 'Turunkan (-) Persen %', 'tokoku-bulk-editor' ); ?></option>
                        </select>
                    </label>

                    <label class="alignleft" id="tokoku-bulk-price-val-wrapper" style="min-width: 130px; display: none;">
                        <span class="title" style="display: block; margin-bottom: 4px;"><?php esc_html_e( 'Nilai Harga', 'tokoku-bulk-editor' ); ?></span>
                        <input type="number" min="0" name="tokoku_bulk_price_val" value="" placeholder="0" style="width: 100%;">
                    </label>

                    <label class="alignleft" style="min-width: 180px;">
                        <span class="title" style="display: block; margin-bottom: 4px;"><?php esc_html_e( 'Harga Diskon', 'tokoku-bulk-editor' ); ?></span>
                        <select name="tokoku_bulk_sale_mode" id="tokoku-bulk-sale-mode-select" style="width: 100%;">
                            <option value="no_change"><?php esc_html_e( '— Tidak Berubah —', 'tokoku-bulk-editor' ); ?></option>
                            <option value="set"><?php esc_html_e( 'Tetapkan Harga Diskon (Rp)', 'tokoku-bulk-editor' ); ?></option>
                            <option value="percent_of_regular"><?php esc_html_e( 'Diskon % dari Harga Normal', 'tokoku-bulk-editor' ); ?></option>
                            <option value="remove"><?php esc_html_e( 'Hapus Diskon', 'tokoku-bulk-editor' ); ?></option>
                        </select>
                    </label>

                    <label class="alignleft" id="tokoku-bulk-sale-val-wrapper" style="min-width: 130px; display: none;">
                        <span class="title" style="display: block; margin-bottom: 4px;"><?php esc_html_e( 'Nilai Diskon', 'tokoku-bulk-editor' ); ?></span>
                        <input type="number" min="0" name="tokoku_bulk_sale_val" value="" placeholder="0" style="width: 100%;">
                    </label>
                </div>

                <!-- Stok Masal -->
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <label class="alignleft" style="min-width: 180px;">
                        <span class="title" style="display: block; margin-bottom: 4px;"><?php esc_html_e( 'Status Ketersediaan', 'tokoku-bulk-editor' ); ?></span>
                        <select name="tokoku_bulk_stok_status" style="width: 100%;">
                            <option value="no_change"><?php esc_html_e( '— Tidak Berubah —', 'tokoku-bulk-editor' ); ?></option>
                            <option value="tersedia"><?php esc_html_e( 'Tersedia', 'tokoku-bulk-editor' ); ?></option>
                            <option value="habis"><?php esc_html_e( 'Habis', 'tokoku-bulk-editor' ); ?></option>
                            <option value="preorder"><?php esc_html_e( 'Pre-Order', 'tokoku-bulk-editor' ); ?></option>
                        </select>
                    </label>

                    <label class="alignleft" style="min-width: 180px;">
                        <span class="title" style="display: block; margin-bottom: 4px;"><?php esc_html_e( 'Mode Jumlah Stok', 'tokoku-bulk-editor' ); ?></span>
                        <select name="tokoku_bulk_stok_mode" id="tokoku-bulk-stok-mode-select" style="width: 100%;">
                            <option value="no_change"><?php esc_html_e( '— Tidak Berubah —', 'tokoku-bulk-editor' ); ?></option>
                            <option value="set"><?php esc_html_e( 'Tetapkan Nilai Pasti', 'tokoku-bulk-editor' ); ?></option>
                            <option value="add"><?php esc_html_e( 'Tambah Stok (+)', 'tokoku-bulk-editor' ); ?></option>
                            <option value="subtract"><?php esc_html_e( 'Kurangi Stok (-)', 'tokoku-bulk-editor' ); ?></option>
                            <option value="empty"><?php esc_html_e( 'Kosongkan (Tanpa Angka)', 'tokoku-bulk-editor' ); ?></option>
                        </select>
                    </label>

                    <label class="alignleft" id="tokoku-bulk-qty-wrapper" style="min-width: 130px; display: none;">
                        <span class="title" style="display: block; margin-bottom: 4px;"><?php esc_html_e( 'Nilai Stok', 'tokoku-bulk-editor' ); ?></span>
                        <input type="number" min="0" name="tokoku_bulk_stok_value" value="0" style="width: 100%;">
                    </label>
                </div>
            </div>
        </fieldset>
        <?php
    }

    /**
     * Simpan Perubahan dari Quick Edit dan Bulk Edit Standar WP
     */
    public function save_bulk_and_quick_edit( $post_id, $post ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'produk' !== $post->post_type ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $prod_mgr = Tokoku_Product_Manager::get_instance();

        // 1. Simpan dari Quick Edit
        if ( isset( $_POST['tokoku_native_quick_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tokoku_native_quick_nonce'] ) ), 'tokoku_native_quick_edit_action' ) ) {
            $payload = array();

            // Harga
            if ( isset( $_POST['tokoku_quick_harga'] ) ) {
                $p_val = sanitize_text_field( wp_unslash( $_POST['tokoku_quick_harga'] ) );
                $payload['price_mode'] = 'set';
                $payload['price_val']  = '' !== $p_val ? floatval( $p_val ) : 0;
            }

            // Diskon
            if ( isset( $_POST['tokoku_quick_harga_diskon'] ) ) {
                $s_val = sanitize_text_field( wp_unslash( $_POST['tokoku_quick_harga_diskon'] ) );
                if ( '' !== $s_val && floatval( $s_val ) > 0 ) {
                    $payload['sale_mode'] = 'set';
                    $payload['sale_val']  = floatval( $s_val );
                } else {
                    $payload['sale_mode'] = 'remove';
                }
            }

            // Stok Status & Kuantitas
            if ( isset( $_POST['tokoku_quick_stok_status'] ) ) {
                $payload['stok_status'] = sanitize_key( wp_unslash( $_POST['tokoku_quick_stok_status'] ) );
            }
            if ( isset( $_POST['tokoku_quick_jumlah_stok'] ) ) {
                $qty_raw = sanitize_text_field( wp_unslash( $_POST['tokoku_quick_jumlah_stok'] ) );
                $payload['stok_mode']  = ( '' !== $qty_raw ) ? 'set' : 'empty';
                $payload['stok_value'] = intval( $qty_raw );
            }

            $prod_mgr->update_product( $post_id, $payload );
            return;
        }

        // 2. Simpan dari Bulk Edit
        if ( isset( $_REQUEST['tokoku_native_bulk_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['tokoku_native_bulk_nonce'] ) ), 'tokoku_native_bulk_edit_action' ) ) {
            $payload = array();

            if ( isset( $_REQUEST['tokoku_bulk_price_mode'] ) && 'no_change' !== $_REQUEST['tokoku_bulk_price_mode'] ) {
                $payload['price_mode'] = sanitize_key( wp_unslash( $_REQUEST['tokoku_bulk_price_mode'] ) );
                $payload['price_val']  = isset( $_REQUEST['tokoku_bulk_price_val'] ) ? floatval( wp_unslash( $_REQUEST['tokoku_bulk_price_val'] ) ) : 0;
            }

            if ( isset( $_REQUEST['tokoku_bulk_sale_mode'] ) && 'no_change' !== $_REQUEST['tokoku_bulk_sale_mode'] ) {
                $payload['sale_mode'] = sanitize_key( wp_unslash( $_REQUEST['tokoku_bulk_sale_mode'] ) );
                $payload['sale_val']  = isset( $_REQUEST['tokoku_bulk_sale_val'] ) ? floatval( wp_unslash( $_REQUEST['tokoku_bulk_sale_val'] ) ) : 0;
            }

            if ( isset( $_REQUEST['tokoku_bulk_stok_status'] ) && 'no_change' !== $_REQUEST['tokoku_bulk_stok_status'] ) {
                $payload['stok_status'] = sanitize_key( wp_unslash( $_REQUEST['tokoku_bulk_stok_status'] ) );
            }

            if ( isset( $_REQUEST['tokoku_bulk_stok_mode'] ) && 'no_change' !== $_REQUEST['tokoku_bulk_stok_mode'] ) {
                $payload['stok_mode']  = sanitize_key( wp_unslash( $_REQUEST['tokoku_bulk_stok_mode'] ) );
                $payload['stok_value'] = isset( $_REQUEST['tokoku_bulk_stok_value'] ) ? intval( wp_unslash( $_REQUEST['tokoku_bulk_stok_value'] ) ) : 0;
            }

            if ( ! empty( $payload ) ) {
                $prod_mgr->update_product( $post_id, $payload );
            }
        }
    }
}

endif;
