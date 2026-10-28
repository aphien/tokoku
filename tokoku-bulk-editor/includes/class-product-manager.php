<?php
/**
 * Product Manager Core Business Logic
 * Handles comprehensive bulk and single updates for TokoKu products:
 * - Pricing & Discounts
 * - Stock Availability & Quantity
 * - Categories & Tags
 * - Promotional Labels & Badges
 * - Weight & Post Status
 *
 * @package TokoKu_Bulk_Editor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Tokoku_Product_Manager' ) ) :

class Tokoku_Product_Manager {

    /**
     * Singleton instance
     */
    protected static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    protected function __construct() {}

    /**
     * Supported Stock Statuses
     */
    public function get_supported_statuses() {
        return array(
            'tersedia' => array(
                'label' => __( 'Tersedia', 'tokoku-bulk-editor' ),
                'class' => 'status-tersedia',
                'color' => '#10b981',
            ),
            'habis'    => array(
                'label' => __( 'Habis', 'tokoku-bulk-editor' ),
                'class' => 'status-habis',
                'color' => '#ef4444',
            ),
            'preorder' => array(
                'label' => __( 'Pre-Order', 'tokoku-bulk-editor' ),
                'class' => 'status-preorder',
                'color' => '#f59e0b',
            ),
        );
    }

    /**
     * Get comprehensive product information
     *
     * @param int $post_id Product ID
     * @return array|false
     */
    public function get_product_full_info( $post_id ) {
        $post = get_post( $post_id );
        if ( ! $post || 'produk' !== $post->post_type ) {
            return false;
        }

        $raw_status = get_post_meta( $post_id, '_produk_stok', true );
        if ( empty( $raw_status ) ) {
            $raw_status = 'tersedia';
        }

        $raw_qty        = get_post_meta( $post_id, '_produk_jumlah_stok', true );
        $sku            = get_post_meta( $post_id, '_produk_sku', true );
        $raw_price      = get_post_meta( $post_id, '_produk_harga', true );
        $raw_sale_price = get_post_meta( $post_id, '_produk_harga_diskon', true );
        $label_khusus   = get_post_meta( $post_id, '_produk_label_khusus', true );
        $berat          = get_post_meta( $post_id, '_produk_berat', true );

        $statuses  = $this->get_supported_statuses();
        $meta_stat = isset( $statuses[ $raw_status ] ) ? $statuses[ $raw_status ] : $statuses['tersedia'];

        // Categories
        $terms = get_the_terms( $post_id, 'kategori_produk' );
        $cat_names = array();
        $cat_ids   = array();
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $t ) {
                $cat_names[] = $t->name;
                $cat_ids[]   = $t->term_id;
            }
        }

        // Thumbnail
        $thumb_url = '';
        if ( has_post_thumbnail( $post_id ) ) {
            $thumb_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), array( 70, 70 ) );
            $thumb_url = $thumb_src ? $thumb_src[0] : '';
        }

        // Numerical price parsing
        $num_price      = '' !== $raw_price && null !== $raw_price ? floatval( $raw_price ) : null;
        $num_sale_price = '' !== $raw_sale_price && null !== $raw_sale_price ? floatval( $raw_sale_price ) : null;

        $has_discount = ( null !== $num_sale_price && $num_sale_price > 0 && null !== $num_price && $num_sale_price < $num_price );
        $discount_pct = 0;
        if ( $has_discount && $num_price > 0 ) {
            $discount_pct = round( ( ( $num_price - $num_sale_price ) / $num_price ) * 100 );
        }

        return array(
            'id'               => $post_id,
            'title'            => get_the_title( $post_id ),
            'sku'              => $sku ? $sku : '—',
            'status'           => $raw_status,
            'status_label'     => $meta_stat['label'],
            'status_class'     => $meta_stat['class'],
            'status_color'     => $meta_stat['color'],
            'qty'              => ( '' !== $raw_qty && null !== $raw_qty ) ? intval( $raw_qty ) : null,
            'raw_price'        => null !== $num_price ? $num_price : '',
            'price'            => null !== $num_price ? number_format( $num_price, 0, ',', '.' ) : '',
            'raw_sale_price'   => null !== $num_sale_price ? $num_sale_price : '',
            'sale_price'       => null !== $num_sale_price ? number_format( $num_sale_price, 0, ',', '.' ) : '',
            'has_discount'     => $has_discount,
            'discount_percent' => $discount_pct,
            'categories'       => ! empty( $cat_names ) ? implode( ', ', $cat_names ) : '—',
            'category_ids'     => $cat_ids,
            'label_khusus'     => $label_khusus ? $label_khusus : '',
            'berat'            => $berat ? $berat : '',
            'post_status'      => get_post_status( $post_id ),
            'thumb'            => $thumb_url,
            'edit_url'         => get_edit_post_link( $post_id, 'raw' ),
            'view_url'         => get_permalink( $post_id ),
        );
    }

    /**
     * Backward-compatible stock info helper
     */
    public function get_product_stock_info( $post_id ) {
        return $this->get_product_full_info( $post_id );
    }

    /**
     * Backward-compatible update stock helper
     */
    public function update_product_stock( $post_id, $args = array() ) {
        return $this->update_product( $post_id, $args );
    }

    /**
     * Update product with full multi-attribute support
     *
     * @param int   $post_id Product ID
     * @param array $args Options
     * @return array Result
     */
    public function update_product( $post_id, $args = array() ) {
        $post_id = intval( $post_id );
        $post    = get_post( $post_id );

        if ( ! $post || 'produk' !== $post->post_type ) {
            return array(
                'success' => false,
                'id'      => $post_id,
                'message' => __( 'Produk tidak ditemukan atau tipe post salah.', 'tokoku-bulk-editor' ),
            );
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return array(
                'success' => false,
                'id'      => $post_id,
                'message' => __( 'Anda tidak memiliki hak akses mengedit produk ini.', 'tokoku-bulk-editor' ),
            );
        }

        $changes_made = array();

        // ----------------------------------------------------
        // 1. UPDATE HARGA NORMAL
        // ----------------------------------------------------
        if ( isset( $args['price_mode'] ) && 'no_change' !== $args['price_mode'] ) {
            $p_mode = sanitize_key( $args['price_mode'] );
            $p_val  = isset( $args['price_val'] ) ? floatval( $args['price_val'] ) : 0;
            $cur_price = floatval( get_post_meta( $post_id, '_produk_harga', true ) );

            switch ( $p_mode ) {
                case 'set':
                    $new_price = max( 0, $p_val );
                    update_post_meta( $post_id, '_produk_harga', $new_price );
                    $changes_made[] = sprintf( __( 'Harga normal diatur menjadi Rp %s', 'tokoku-bulk-editor' ), number_format( $new_price, 0, ',', '.' ) );
                    break;

                case 'add_nominal':
                    $new_price = max( 0, $cur_price + $p_val );
                    update_post_meta( $post_id, '_produk_harga', $new_price );
                    $changes_made[] = sprintf( __( 'Harga normal ditambah Rp %s (menjadi Rp %s)', 'tokoku-bulk-editor' ), number_format( $p_val, 0, ',', '.' ), number_format( $new_price, 0, ',', '.' ) );
                    break;

                case 'sub_nominal':
                    $new_price = max( 0, $cur_price - $p_val );
                    update_post_meta( $post_id, '_produk_harga', $new_price );
                    $changes_made[] = sprintf( __( 'Harga normal dikurangi Rp %s (menjadi Rp %s)', 'tokoku-bulk-editor' ), number_format( $p_val, 0, ',', '.' ), number_format( $new_price, 0, ',', '.' ) );
                    break;

                case 'add_percent':
                    $new_price = max( 0, round( $cur_price * ( 1 + ( $p_val / 100 ) ) ) );
                    update_post_meta( $post_id, '_produk_harga', $new_price );
                    $changes_made[] = sprintf( __( 'Harga normal dinaikkan %s%% (menjadi Rp %s)', 'tokoku-bulk-editor' ), $p_val, number_format( $new_price, 0, ',', '.' ) );
                    break;

                case 'sub_percent':
                    $new_price = max( 0, round( $cur_price * ( 1 - ( $p_val / 100 ) ) ) );
                    update_post_meta( $post_id, '_produk_harga', $new_price );
                    $changes_made[] = sprintf( __( 'Harga normal diturunkan %s%% (menjadi Rp %s)', 'tokoku-bulk-editor' ), $p_val, number_format( $new_price, 0, ',', '.' ) );
                    break;
            }
        }

        // ----------------------------------------------------
        // 2. UPDATE HARGA DISKON
        // ----------------------------------------------------
        if ( isset( $args['sale_mode'] ) && 'no_change' !== $args['sale_mode'] ) {
            $s_mode = sanitize_key( $args['sale_mode'] );
            $s_val  = isset( $args['sale_val'] ) ? floatval( $args['sale_val'] ) : 0;
            $cur_reg_price = floatval( get_post_meta( $post_id, '_produk_harga', true ) );

            switch ( $s_mode ) {
                case 'set':
                    $new_sale = max( 0, $s_val );
                    update_post_meta( $post_id, '_produk_harga_diskon', $new_sale );
                    $changes_made[] = sprintf( __( 'Harga diskon diatur menjadi Rp %s', 'tokoku-bulk-editor' ), number_format( $new_sale, 0, ',', '.' ) );
                    break;

                case 'percent_of_regular':
                    // Diskon sebesar X% dari harga normal
                    if ( $cur_reg_price > 0 && $s_val > 0 && $s_val <= 100 ) {
                        $new_sale = max( 0, round( $cur_reg_price * ( 1 - ( $s_val / 100 ) ) ) );
                        update_post_meta( $post_id, '_produk_harga_diskon', $new_sale );
                        $changes_made[] = sprintf( __( 'Diskon %s%% dari harga normal diterapkan (Harga diskon: Rp %s)', 'tokoku-bulk-editor' ), $s_val, number_format( $new_sale, 0, ',', '.' ) );
                    }
                    break;

                case 'remove':
                    delete_post_meta( $post_id, '_produk_harga_diskon' );
                    $changes_made[] = __( 'Harga diskon dihapus (kembali ke harga normal)', 'tokoku-bulk-editor' );
                    break;
            }
        }

        // ----------------------------------------------------
        // 3. UPDATE STATUS KETERSEDIAAN
        // ----------------------------------------------------
        $old_status = get_post_meta( $post_id, '_produk_stok', true );
        if ( ! $old_status ) {
            $old_status = 'tersedia';
        }
        $new_status = $old_status;

        if ( isset( $args['stok_status'] ) && 'no_change' !== $args['stok_status'] ) {
            $allowed_statuses = array( 'tersedia', 'habis', 'preorder' );
            $target_status    = sanitize_key( $args['stok_status'] );

            if ( in_array( $target_status, $allowed_statuses, true ) ) {
                update_post_meta( $post_id, '_produk_stok', $target_status );
                $new_status = $target_status;
                $changes_made[] = sprintf( __( 'Status ketersediaan diubah ke "%s"', 'tokoku-bulk-editor' ), $target_status );
            }
        }

        // ----------------------------------------------------
        // 4. UPDATE JUMLAH KUANTITAS STOK
        // ----------------------------------------------------
        $old_qty = get_post_meta( $post_id, '_produk_jumlah_stok', true );
        $new_qty = $old_qty;

        if ( isset( $args['stok_mode'] ) && 'no_change' !== $args['stok_mode'] ) {
            $mode = sanitize_key( $args['stok_mode'] );
            $val  = isset( $args['stok_value'] ) ? intval( $args['stok_value'] ) : 0;

            switch ( $mode ) {
                case 'set':
                    $calculated_qty = max( 0, $val );
                    update_post_meta( $post_id, '_produk_jumlah_stok', $calculated_qty );
                    $new_qty = $calculated_qty;
                    $changes_made[] = sprintf( __( 'Jumlah stok diset menjadi %d', 'tokoku-bulk-editor' ), $calculated_qty );
                    break;

                case 'add':
                    $current_val    = ( '' !== $old_qty && null !== $old_qty ) ? intval( $old_qty ) : 0;
                    $calculated_qty = max( 0, $current_val + $val );
                    update_post_meta( $post_id, '_produk_jumlah_stok', $calculated_qty );
                    $new_qty = $calculated_qty;
                    $changes_made[] = sprintf( __( 'Jumlah stok ditambah %d (total: %d)', 'tokoku-bulk-editor' ), $val, $calculated_qty );
                    break;

                case 'subtract':
                    $current_val    = ( '' !== $old_qty && null !== $old_qty ) ? intval( $old_qty ) : 0;
                    $calculated_qty = max( 0, $current_val - $val );
                    update_post_meta( $post_id, '_produk_jumlah_stok', $calculated_qty );
                    $new_qty = $calculated_qty;
                    $changes_made[] = sprintf( __( 'Jumlah stok dikurangi %d (sisa: %d)', 'tokoku-bulk-editor' ), $val, $calculated_qty );
                    break;

                case 'empty':
                    delete_post_meta( $post_id, '_produk_jumlah_stok' );
                    $new_qty = '';
                    $changes_made[] = __( 'Jumlah stok dikosongkan (tanpa pelacakan angka)', 'tokoku-bulk-editor' );
                    break;
            }
        }

        // Auto-sync status ketersediaan berdasarkan kuantitas
        if ( ! empty( $args['auto_sync_status'] ) ) {
            if ( isset( $new_qty ) && '' !== $new_qty && 0 === intval( $new_qty ) && 'habis' !== $new_status && ( ! isset( $args['stok_status'] ) || 'no_change' === $args['stok_status'] ) ) {
                update_post_meta( $post_id, '_produk_stok', 'habis' );
                $new_status = 'habis';
                $changes_made[] = __( 'Otomatis diubah ke "Habis" karena stok 0', 'tokoku-bulk-editor' );
            } elseif ( isset( $new_qty ) && '' !== $new_qty && intval( $new_qty ) > 0 && 'habis' === $old_status && ( ! isset( $args['stok_status'] ) || 'no_change' === $args['stok_status'] ) ) {
                update_post_meta( $post_id, '_produk_stok', 'tersedia' );
                $new_status = 'tersedia';
                $changes_made[] = __( 'Otomatis diubah ke "Tersedia" karena stok bertambah', 'tokoku-bulk-editor' );
            }
        }

        // ----------------------------------------------------
        // 5. UPDATE KATEGORI PRODUK
        // ----------------------------------------------------
        if ( isset( $args['category_mode'] ) && 'no_change' !== $args['category_mode'] ) {
            $cat_mode = sanitize_key( $args['category_mode'] );
            $cat_ids  = isset( $args['category_ids'] ) ? array_map( 'intval', (array) $args['category_ids'] ) : array();

            if ( ! empty( $cat_ids ) ) {
                if ( 'replace' === $cat_mode ) {
                    wp_set_object_terms( $post_id, $cat_ids, 'kategori_produk', false );
                    $changes_made[] = __( 'Kategori produk diganti', 'tokoku-bulk-editor' );
                } elseif ( 'append' === $cat_mode ) {
                    wp_set_object_terms( $post_id, $cat_ids, 'kategori_produk', true );
                    $changes_made[] = __( 'Kategori baru ditambahkan ke produk', 'tokoku-bulk-editor' );
                }
            }
        }

        // ----------------------------------------------------
        // 6. UPDATE LABEL PROMO / BADGE
        // ----------------------------------------------------
        if ( isset( $args['label_mode'] ) && 'no_change' !== $args['label_mode'] ) {
            $lbl_mode = sanitize_key( $args['label_mode'] );
            $lbl_text = isset( $args['label_text'] ) ? sanitize_text_field( wp_unslash( $args['label_text'] ) ) : '';

            if ( 'set' === $lbl_mode && ! empty( $lbl_text ) ) {
                update_post_meta( $post_id, '_produk_label_khusus', $lbl_text );
                $changes_made[] = sprintf( __( 'Label promo diset ke "%s"', 'tokoku-bulk-editor' ), $lbl_text );
            } elseif ( 'remove' === $lbl_mode ) {
                delete_post_meta( $post_id, '_produk_label_khusus' );
                $changes_made[] = __( 'Label promo dihapus', 'tokoku-bulk-editor' );
            }
        }

        // ----------------------------------------------------
        // 7. UPDATE BERAT PRODUK
        // ----------------------------------------------------
        if ( isset( $args['berat_mode'] ) && 'no_change' !== $args['berat_mode'] ) {
            $b_mode = sanitize_key( $args['berat_mode'] );
            $b_val  = isset( $args['berat_val'] ) ? intval( $args['berat_val'] ) : 0;

            if ( 'set' === $b_mode ) {
                update_post_meta( $post_id, '_produk_berat', max( 0, $b_val ) );
                $changes_made[] = sprintf( __( 'Berat diset menjadi %d gram', 'tokoku-bulk-editor' ), $b_val );
            } elseif ( 'remove' === $b_mode ) {
                delete_post_meta( $post_id, '_produk_berat' );
                $changes_made[] = __( 'Data berat dikosongkan', 'tokoku-bulk-editor' );
            }
        }

        // ----------------------------------------------------
        // 8. UPDATE STATUS POST (PUBLISH / DRAFT / PENDING)
        // ----------------------------------------------------
        if ( isset( $args['post_status'] ) && 'no_change' !== $args['post_status'] ) {
            $allowed_post_statuses = array( 'publish', 'draft', 'pending' );
            $target_pstatus = sanitize_key( $args['post_status'] );

            if ( in_array( $target_pstatus, $allowed_post_statuses, true ) && $target_pstatus !== $post->post_status ) {
                wp_update_post( array(
                    'ID'          => $post_id,
                    'post_status' => $target_pstatus,
                ) );
                $changes_made[] = sprintf( __( 'Status produk diubah menjadi %s', 'tokoku-bulk-editor' ), $target_pstatus );
            }
        }

        // Clean post cache
        clean_post_cache( $post_id );

        // Action hook for external integrations
        do_action( 'tokoku_bulk_product_updated', $post_id, $changes_made, $args );
        do_action( 'tokoku_product_stock_updated', $post_id, $new_status, $new_qty, $old_status, $old_qty );

        return array(
            'success'      => true,
            'id'           => $post_id,
            'title'        => get_the_title( $post_id ),
            'changes'      => $changes_made,
            'updated_info' => $this->get_product_full_info( $post_id ),
        );
    }
}

endif;
