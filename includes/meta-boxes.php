<?php
/**
 * Custom Meta Boxes for Produk CPT
 * Fields: harga, harga diskon, berat, stok
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add meta box to produk edit screen
 */
function tokoku_add_produk_meta_boxes() {
    add_meta_box(
        'tokoku_produk_details',
        __( 'Detail Produk', 'tokoku' ),
        'tokoku_produk_meta_box_callback',
        'produk',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'tokoku_add_produk_meta_boxes' );

/**
 * Meta box callback - render fields
 */
function tokoku_produk_meta_box_callback( $post ) {
    wp_nonce_field( 'tokoku_save_produk_meta', 'tokoku_produk_nonce' );
    wp_nonce_field( 'tokoku_save_gallery', 'tokoku_gallery_nonce' );

    // Get all values
    $harga          = get_post_meta( $post->ID, '_produk_harga', true );
    $harga_diskon   = get_post_meta( $post->ID, '_produk_harga_diskon', true );
    $multi_pilihan  = get_post_meta( $post->ID, '_produk_multi_pilihan', true );
    $multi_harga    = get_post_meta( $post->ID, '_produk_multi_harga', true );
    
    $sku            = get_post_meta( $post->ID, '_produk_sku', true );
    $warna          = get_post_meta( $post->ID, '_produk_pilihan_warna', true );
    $catatan        = get_post_meta( $post->ID, '_produk_catatan', true );
    $stok           = get_post_meta( $post->ID, '_produk_stok', true );
    $jumlah_stok    = get_post_meta( $post->ID, '_produk_jumlah_stok', true );
    $label_khusus   = get_post_meta( $post->ID, '_produk_label_khusus', true );
    $berat          = get_post_meta( $post->ID, '_produk_berat', true );
    $wa_text        = get_post_meta( $post->ID, '_produk_whatsapp_text', true );

    $gallery_ids    = get_post_meta( $post->ID, '_produk_gallery', true );
    $video_url      = get_post_meta( $post->ID, '_produk_video', true );
    
    $marketplace_shopee    = get_post_meta( $post->ID, '_produk_marketplace_shopee', true );
    $marketplace_tokopedia = get_post_meta( $post->ID, '_produk_marketplace_tokopedia', true );
    $marketplace_lazada    = get_post_meta( $post->ID, '_produk_marketplace_lazada', true );
    $marketplace_tiktok    = get_post_meta( $post->ID, '_produk_marketplace_tiktok', true );
    $marketplace_lainnya   = get_post_meta( $post->ID, '_produk_marketplace_lainnya', true );

    ?>
    <style>
        .tokoku-tabs-container {
            margin: -12px -12px -12px -12px;
            display: flex;
            background: #fff;
            min-height: 450px;
        }
        .tokoku-tabs-nav {
            width: 180px;
            background: #f0f0f1;
            border-right: 1px solid #dcdcde;
            flex-shrink: 0;
        }
        .tokoku-tab-item {
            padding: 15px;
            cursor: pointer;
            border-bottom: 1px solid #dcdcde;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #50575e;
            transition: all 0.2s;
        }
        .tokoku-tab-item:hover {
            background: #e0e0e0;
            color: #2271b1;
        }
        .tokoku-tab-item.active {
            background: #fff;
            color: #2271b1;
            border-right: 4px solid #2271b1;
            margin-right: -1px;
        }
        .tokoku-tabs-content {
            flex-grow: 1;
            padding: 20px;
        }
        .tokoku-tab-panel {
            display: none;
        }
        .tokoku-tab-panel.active {
            display: block;
        }
        .tokoku-meta-field {
            margin-bottom: 15px;
        }
        .tokoku-meta-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .tokoku-meta-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .tokoku-meta-icon {
            position: absolute;
            left: 10px;
            color: #949494;
        }
        .tokoku-meta-field input[type="text"],
        .tokoku-meta-field input[type="number"],
        .tokoku-meta-field input[type="url"],
        .tokoku-meta-field select,
        .tokoku-meta-field textarea {
            width: 100%;
            padding: 8px 10px 8px 35px;
            border: 1px solid #dcdcde;
            border-radius: 4px;
        }
        .tokoku-meta-field textarea {
            padding-left: 10px;
            min-height: 80px;
        }
        .tokoku-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .tokoku-gallery-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .tokoku-gallery-item {
            position: relative;
            width: 70px;
            height: 70px;
        }
        .tokoku-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #dcdcde;
        }
        .tokoku-remove-gallery-img {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #d63638;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
    </style>

    <div class="tokoku-tabs-container">
        <div class="tokoku-tabs-nav">
            <div class="tokoku-tab-item active" data-tab="tab-harga">
                <span class="dashicons dashicons-money-alt"></span> <?php _e( 'Harga Produk', 'tokoku' ); ?>
            </div>
            <div class="tokoku-tab-item" data-tab="tab-detail">
                <span class="dashicons dashicons-admin-generic"></span> <?php _e( 'Detail', 'tokoku' ); ?>
            </div>
            <div class="tokoku-tab-item" data-tab="tab-galeri">
                <span class="dashicons dashicons-images-alt2"></span> <?php _e( 'Galeri', 'tokoku' ); ?>
            </div>
            <div class="tokoku-tab-item" data-tab="tab-marketplace">
                <span class="dashicons dashicons-cart"></span> <?php _e( 'Marketplace', 'tokoku' ); ?>
            </div>
        </div>

        <div class="tokoku-tabs-content">
            <!-- Tab 1: Harga -->
            <div id="tab-harga" class="tokoku-tab-panel active">
                <div class="tokoku-meta-grid">
                    <div class="tokoku-meta-field">
                        <label for="tokoku_harga"><?php _e( 'Harga Terkini', 'tokoku' ); ?></label>
                        <div class="tokoku-meta-input-wrapper">
                            <span class="tokoku-meta-icon"><span class="dashicons dashicons-tag"></span></span>
                            <input type="number" id="tokoku_harga" name="_produk_harga" value="<?php echo esc_attr( $harga ); ?>">
                        </div>
                    </div>
                    <div class="tokoku-meta-field">
                        <label for="tokoku_harga_diskon"><?php _e( 'Harga Coret', 'tokoku' ); ?></label>
                        <div class="tokoku-meta-input-wrapper">
                            <span class="tokoku-meta-icon"><span class="dashicons dashicons-dismiss"></span></span>
                            <input type="number" id="tokoku_harga_diskon" name="_produk_harga_diskon" value="<?php echo esc_attr( $harga_diskon ); ?>">
                        </div>
                    </div>
                </div>
                <div class="tokoku-meta-grid">
                    <div class="tokoku-meta-field">
                        <label for="tokoku_multi_pilihan"><?php _e( 'Multi Pilihan', 'tokoku' ); ?></label>
                        <textarea id="tokoku_multi_pilihan" name="_produk_multi_pilihan" placeholder="Contoh: S, M, L"><?php echo esc_textarea( $multi_pilihan ); ?></textarea>
                    </div>
                    <div class="tokoku-meta-field">
                        <label for="tokoku_multi_harga"><?php _e( 'Multi Harga', 'tokoku' ); ?></label>
                        <textarea id="tokoku_multi_harga" name="_produk_multi_harga" placeholder="Contoh: 1000, 2000"><?php echo esc_textarea( $multi_harga ); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Detail -->
            <div id="tab-detail" class="tokoku-tab-panel">
                <div class="tokoku-meta-grid">
                    <div class="tokoku-meta-field">
                        <label for="tokoku_warna"><?php _e( 'Pilihan Warna', 'tokoku' ); ?></label>
                        <div class="tokoku-meta-input-wrapper">
                            <span class="tokoku-meta-icon"><span class="dashicons dashicons-art"></span></span>
                            <input type="text" id="tokoku_warna" name="_produk_pilihan_warna" value="<?php echo esc_attr( $warna ); ?>">
                        </div>
                    </div>
                    <div class="tokoku-meta-field">
                        <label for="tokoku_sku"><?php _e( 'Kode Produk / SKU', 'tokoku' ); ?></label>
                        <div class="tokoku-meta-input-wrapper">
                            <span class="tokoku-meta-icon"><span class="dashicons dashicons-barcode"></span></span>
                            <input type="text" id="tokoku_sku" name="_produk_sku" value="<?php echo esc_attr( $sku ); ?>">
                        </div>
                    </div>
                </div>
                <div class="tokoku-meta-grid">
                    <div class="tokoku-meta-field">
                        <label for="tokoku_stok"><?php _e( 'Ketersediaan', 'tokoku' ); ?></label>
                        <div class="tokoku-meta-input-wrapper">
                            <span class="tokoku-meta-icon"><span class="dashicons dashicons-archive"></span></span>
                            <select id="tokoku_stok" name="_produk_stok">
                                <option value="tersedia" <?php selected( $stok, 'tersedia' ); ?>><?php _e( 'Tersedia', 'tokoku' ); ?></option>
                                <option value="habis" <?php selected( $stok, 'habis' ); ?>><?php _e( 'Habis', 'tokoku' ); ?></option>
                                <option value="preorder" <?php selected( $stok, 'preorder' ); ?>><?php _e( 'Pre Order', 'tokoku' ); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="tokoku-meta-field">
                        <label for="tokoku_jumlah_stok"><?php _e( 'Jumlah Stok', 'tokoku' ); ?></label>
                        <div class="tokoku-meta-input-wrapper">
                            <span class="tokoku-meta-icon"><span class="dashicons dashicons-chart-bar"></span></span>
                            <input type="number" id="tokoku_jumlah_stok" name="_produk_jumlah_stok" value="<?php echo esc_attr( $jumlah_stok ); ?>">
                        </div>
                    </div>
                </div>
                <div class="tokoku-meta-grid">
                    <div class="tokoku-meta-field">
                        <label for="tokoku_berat"><?php _e( 'Berat Produk', 'tokoku' ); ?></label>
                        <div class="tokoku-meta-input-wrapper">
                            <span class="tokoku-meta-icon"><span class="dashicons dashicons-performance"></span></span>
                            <input type="text" id="tokoku_berat" name="_produk_berat" value="<?php echo esc_attr( $berat ); ?>">
                        </div>
                    </div>
                    <div class="tokoku-meta-field">
                        <label for="tokoku_label_khusus"><?php _e( 'Label Khusus', 'tokoku' ); ?></label>
                        <div class="tokoku-meta-input-wrapper">
                            <span class="tokoku-meta-icon"><span class="dashicons dashicons-star-filled"></span></span>
                            <input type="text" id="tokoku_label_khusus" name="_produk_label_khusus" value="<?php echo esc_attr( $label_khusus ); ?>">
                        </div>
                    </div>
                </div>
                <div class="tokoku-meta-field">
                    <label for="tokoku_catatan"><?php _e( 'Catatan', 'tokoku' ); ?></label>
                    <textarea id="tokoku_catatan" name="_produk_catatan"><?php echo esc_textarea( $catatan ); ?></textarea>
                </div>
                <div class="tokoku-meta-field">
                    <label for="tokoku_wa_text"><?php _e( 'Pesan WhatsApp Khusus', 'tokoku' ); ?></label>
                    <textarea id="tokoku_wa_text" name="_produk_whatsapp_text"><?php echo esc_textarea( $wa_text ); ?></textarea>
                </div>
            </div>

            <!-- Tab 3: Galeri -->
            <div id="tab-galeri" class="tokoku-tab-panel">
                <div class="tokoku-meta-field">
                    <label><?php _e( 'Gambar Galeri', 'tokoku' ); ?></label>
                    <div id="tokoku-gallery-images" class="tokoku-gallery-grid">
                        <?php
                        if ( $gallery_ids ) {
                            $ids = explode( ',', $gallery_ids );
                            foreach ( $ids as $id ) {
                                $img = wp_get_attachment_image_src( $id, 'thumbnail' );
                                if ( $img ) {
                                    echo '<div class="tokoku-gallery-item">';
                                    echo '<img src="' . esc_url( $img[0] ) . '">';
                                    echo '<button type="button" class="tokoku-remove-gallery-img" data-id="' . esc_attr( $id ) . '">&times;</button>';
                                    echo '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                    <input type="hidden" id="tokoku_gallery_ids" name="_produk_gallery" value="<?php echo esc_attr( $gallery_ids ); ?>">
                    <button type="button" id="tokoku-add-gallery-btn" class="button"><?php _e( 'Tambah Gambar', 'tokoku' ); ?></button>
                </div>
                <div class="tokoku-meta-field">
                    <label for="tokoku_video"><?php _e( 'Link Video', 'tokoku' ); ?></label>
                    <div class="tokoku-meta-input-wrapper">
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-video-alt3"></span></span>
                        <input type="text" id="tokoku_video" name="_produk_video" value="<?php echo esc_url( $video_url ); ?>">
                    </div>
                </div>
            </div>

            <!-- Tab 4: Marketplace -->
            <div id="tab-marketplace" class="tokoku-tab-panel">
                <p class="description" style="margin-bottom: 15px;"><?php _e( 'Masukkan link produk Anda di berbagai marketplace.', 'tokoku' ); ?></p>
                
                <div class="tokoku-meta-field">
                    <label for="tokoku_marketplace_shopee"><?php _e( 'Shopee', 'tokoku' ); ?></label>
                    <div class="tokoku-meta-input-wrapper">
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-cart"></span></span>
                        <input type="url" id="tokoku_marketplace_shopee" name="_produk_marketplace_shopee" value="<?php echo esc_url( $marketplace_shopee ); ?>" placeholder="https://shopee.co.id/nama-produk-i.123.456">
                    </div>
                </div>

                <div class="tokoku-meta-field">
                    <label for="tokoku_marketplace_tokopedia"><?php _e( 'Tokopedia', 'tokoku' ); ?></label>
                    <div class="tokoku-meta-input-wrapper">
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-store"></span></span>
                        <input type="url" id="tokoku_marketplace_tokopedia" name="_produk_marketplace_tokopedia" value="<?php echo esc_url( $marketplace_tokopedia ); ?>" placeholder="https://www.tokopedia.com/tokoanda/produk-pilihan">
                    </div>
                </div>

                <div class="tokoku-meta-field">
                    <label for="tokoku_marketplace_lazada"><?php _e( 'Lazada', 'tokoku' ); ?></label>
                    <div class="tokoku-meta-input-wrapper">
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-archive"></span></span>
                        <input type="url" id="tokoku_marketplace_lazada" name="_produk_marketplace_lazada" value="<?php echo esc_url( $marketplace_lazada ); ?>" placeholder="https://www.lazada.co.id/products/nama-produk.html">
                    </div>
                </div>

                <div class="tokoku-meta-field">
                    <label for="tokoku_marketplace_tiktok"><?php _e( 'TikTok Shop', 'tokoku' ); ?></label>
                    <div class="tokoku-meta-input-wrapper">
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-smartphone"></span></span>
                        <input type="url" id="tokoku_marketplace_tiktok" name="_produk_marketplace_tiktok" value="<?php echo esc_url( $marketplace_tiktok ); ?>" placeholder="https://shop.tiktok.com/view/product/123456789">
                    </div>
                </div>

                <div class="tokoku-meta-field">
                    <label for="tokoku_marketplace_lainnya"><?php _e( 'Marketplace Lainnya', 'tokoku' ); ?></label>
                    <div class="tokoku-meta-input-wrapper">
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-admin-links"></span></span>
                        <input type="url" id="tokoku_marketplace_lainnya" name="_produk_marketplace_lainnya" value="<?php echo esc_url( $marketplace_lainnya ); ?>" placeholder="https://bukalapak.com/... atau link lainnya">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('.tokoku-tab-item').on('click', function() {
            var tabId = $(this).data('tab');
            $('.tokoku-tab-item').removeClass('active');
            $('.tokoku-tab-panel').removeClass('active');
            $(this).addClass('active');
            $('#' + tabId).addClass('active');
        });

        var frame;
        $('#tokoku-add-gallery-btn').on('click', function(e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: '<?php _e( 'Pilih Gambar Galeri', 'tokoku' ); ?>',
                multiple: true,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var selection = frame.state().get('selection');
                var currentIds = $('#tokoku_gallery_ids').val();
                var ids = currentIds ? currentIds.split(',') : [];
                selection.each(function(attachment) {
                    attachment = attachment.toJSON();
                    ids.push(attachment.id);
                    var thumb = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                    var html = '<div class="tokoku-gallery-item">';
                    html += '<img src="' + thumb + '">';
                    html += '<button type="button" class="tokoku-remove-gallery-img" data-id="' + attachment.id + '">&times;</button>';
                    html += '</div>';
                    $('#tokoku-gallery-images').append(html);
                });
                $('#tokoku_gallery_ids').val(ids.join(','));
            });
            frame.open();
        });
        $(document).on('click', '.tokoku-remove-gallery-img', function() {
            var removeId = $(this).data('id').toString();
            var currentIds = $('#tokoku_gallery_ids').val().split(',');
            currentIds = currentIds.filter(function(id) { return id !== removeId; });
            $('#tokoku_gallery_ids').val(currentIds.join(','));
            $(this).closest('.tokoku-gallery-item').remove();
        });
    });
    </script>
    <?php
}

/**
 * Save meta box data
 */
function tokoku_save_produk_meta( $post_id ) {
    // Verify nonces
    if ( ! isset( $_POST['tokoku_produk_nonce'] ) || 
         ! wp_verify_nonce( $_POST['tokoku_produk_nonce'], 'tokoku_save_produk_meta' ) ) {
        return;
    }

    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save fields
    $fields = array(
        '_produk_harga'          => 'intval',
        '_produk_harga_diskon'   => 'intval',
        '_produk_sku'            => 'sanitize_text_field',
        '_produk_berat'          => 'sanitize_text_field',
        '_produk_stok'           => 'sanitize_text_field',
        '_produk_whatsapp_text'  => 'sanitize_textarea_field',
        '_produk_multi_pilihan'  => 'sanitize_textarea_field',
        '_produk_multi_harga'    => 'sanitize_textarea_field',
        '_produk_pilihan_warna'  => 'sanitize_text_field',
        '_produk_catatan'        => 'sanitize_textarea_field',
        '_produk_jumlah_stok'    => 'intval',
        '_produk_label_khusus'   => 'sanitize_text_field',
        '_produk_video'                 => 'esc_url_raw',
        '_produk_marketplace_shopee'    => 'esc_url_raw',
        '_produk_marketplace_tokopedia' => 'esc_url_raw',
        '_produk_marketplace_lazada'    => 'esc_url_raw',
        '_produk_marketplace_tiktok'    => 'esc_url_raw',
        '_produk_marketplace_lainnya'   => 'esc_url_raw',
    );

    foreach ( $fields as $key => $sanitize_fn ) {
        if ( isset( $_POST[ $key ] ) ) {
            $value = call_user_func( $sanitize_fn, $_POST[ $key ] );
            update_post_meta( $post_id, $key, $value );
        }
    }

    // Save gallery
    if ( isset( $_POST['tokoku_gallery_nonce'] ) && 
         wp_verify_nonce( $_POST['tokoku_gallery_nonce'], 'tokoku_save_gallery' ) ) {
        if ( isset( $_POST['_produk_gallery'] ) ) {
            $gallery = sanitize_text_field( $_POST['_produk_gallery'] );
            update_post_meta( $post_id, '_produk_gallery', $gallery );
        }
    }
}
add_action( 'save_post_produk', 'tokoku_save_produk_meta' );

/**
 * Helper: Get formatted price
 */
function tokoku_get_harga( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $harga        = get_post_meta( $post_id, '_produk_harga', true );
    $harga_diskon = get_post_meta( $post_id, '_produk_harga_diskon', true );
    $mata_uang    = get_theme_mod( 'tokoku_currency', 'Rp' );

    $output = '';

    if ( $harga_diskon && $harga_diskon < $harga ) {
        $diskon_persen = round( ( ( $harga - $harga_diskon ) / $harga ) * 100 );
        $output .= '<span class="price-discount-badge">-' . $diskon_persen . '%</span> ';
        $output .= '<span class="price-original">' . $mata_uang . ' ' . number_format( $harga, 0, ',', '.' ) . '</span> ';
        $output .= '<span class="price-current">' . $mata_uang . ' ' . number_format( $harga_diskon, 0, ',', '.' ) . '</span>';
    } elseif ( $harga ) {
        $output .= '<span class="price-current">' . $mata_uang . ' ' . number_format( $harga, 0, ',', '.' ) . '</span>';
    } else {
        $output .= '<span class="price-current price-contact">' . esc_html__( 'Hubungi Kami', 'tokoku' ) . '</span>';
    }

    return $output;
}

/**
 * Helper: Get stock status
 */
function tokoku_get_stok_status( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    
    $stok = get_post_meta( $post_id, '_produk_stok', true );
    
    if ( ! $stok ) {
        $stok = 'tersedia';
    }

    $statuses = array(
        'tersedia' => array( 'label' => __( 'Tersedia', 'tokoku' ), 'class' => 'stok-tersedia' ),
        'habis'    => array( 'label' => __( 'Habis', 'tokoku' ), 'class' => 'stok-habis' ),
        'preorder' => array( 'label' => __( 'Pre-Order', 'tokoku' ), 'class' => 'stok-preorder' ),
    );

    return isset( $statuses[ $stok ] ) ? $statuses[ $stok ] : $statuses['tersedia'];
}
