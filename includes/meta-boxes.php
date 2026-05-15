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
 * Menambahkan Meta Box ke Halaman Edit Produk
 * Meta box adalah area input khusus di bawah/samping editor utama.
 * Ini mendaftarkan meta box "Detail Produk" pada CPT "produk".
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
 * Callback Meta Box - Menampilkan Form Input
 * Fungsi ini merender struktur HTML (tab dan input field) untuk meta box.
 * Mengambil data meta yang sudah ada (jika ada) dan menampilkannya di form.
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
    $marketplace_bukalapak = get_post_meta( $post->ID, '_produk_marketplace_bukalapak', true );
    $marketplace_blibli    = get_post_meta( $post->ID, '_produk_marketplace_blibli', true );
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
        /* Colorful Special Label Styling */
        #tokoku_label_khusus {
            background-color: #fff9eb !important;
            border: 2px solid #ffa000 !important;
            color: #7a5100 !important;
            font-weight: 800 !important;
            padding-left: 35px !important;
        }
        .special-label-field .tokoku-meta-icon {
            color: #ffa000 !important;
            z-index: 5;
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
                    <div class="tokoku-meta-field special-label-field">
                        <label for="tokoku_label_khusus"><?php _e( 'Label Khusus', 'tokoku' ); ?></label>
                        <div class="tokoku-meta-input-wrapper">
                            <span class="tokoku-meta-icon"><span class="dashicons dashicons-star-filled"></span></span>
                            <input type="text" id="tokoku_label_khusus" name="_produk_label_khusus" value="<?php echo esc_attr( $label_khusus ); ?>">
                        </div>
                    </div>
                </div>
                <div class="tokoku-meta-field">
                    <label for="tokoku_catatan"><?php _e( 'Catatan', 'tokoku' ); ?></label>
                    <?php 
                    wp_editor( $catatan, 'tokokucatatan', array(
                        'textarea_name' => '_produk_catatan',
                        'media_buttons' => false,
                        'textarea_rows' => 6,
                        'tinymce'       => array(
                            'toolbar1' => 'bold,italic,underline,separator,bullist,numlist,separator,link,unlink',
                        ),
                    ) ); 
                    ?>
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
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-video-alt3"></span></span>
                        <input type="url" id="tokoku_marketplace_tiktok" name="_produk_marketplace_tiktok" value="<?php echo esc_url( $marketplace_tiktok ); ?>" placeholder="https://shop.tiktok.com/view/product/...">
                    </div>
                </div>

                <div class="tokoku-meta-field">
                    <label for="tokoku_marketplace_bukalapak"><?php _e( 'Bukalapak', 'tokoku' ); ?></label>
                    <div class="tokoku-meta-input-wrapper">
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-cart"></span></span>
                        <input type="url" id="tokoku_marketplace_bukalapak" name="_produk_marketplace_bukalapak" value="<?php echo esc_url( $marketplace_bukalapak ); ?>" placeholder="https://www.bukalapak.com/p/...">
                    </div>
                </div>

                <div class="tokoku-meta-field">
                    <label for="tokoku_marketplace_blibli"><?php _e( 'Blibli', 'tokoku' ); ?></label>
                    <div class="tokoku-meta-input-wrapper">
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-cart"></span></span>
                        <input type="url" id="tokoku_marketplace_blibli" name="_produk_marketplace_blibli" value="<?php echo esc_url( $marketplace_blibli ); ?>" placeholder="https://www.blibli.com/p/...">
                    </div>
                </div>

                <div class="tokoku-meta-field">
                    <label for="tokoku_marketplace_lainnya"><?php _e( 'Lainnya', 'tokoku' ); ?></label>
                    <div class="tokoku-meta-input-wrapper">
                        <span class="tokoku-meta-icon"><span class="dashicons dashicons-admin-links"></span></span>
                        <input type="url" id="tokoku_marketplace_lainnya" name="_produk_marketplace_lainnya" value="<?php echo esc_url( $marketplace_lainnya ); ?>" placeholder="https://link-lainnya.com">
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
 * Menyimpan Data Meta Box Produk
 * Dijalankan saat tombol "Simpan/Perbarui" produk diklik.
 * Termasuk pengecekan keamanan (nonce & user capability) serta sanitasi data.
 */
function tokoku_save_produk_meta( $post_id ) {
    // 1. Verifikasi Nonce (Security Check)
    // Mencegah CSRF (Cross-Site Request Forgery). Pastikan request berasal dari form wp-admin.
    if ( ! isset( $_POST['tokoku_produk_nonce'] ) || 
         ! wp_verify_nonce( $_POST['tokoku_produk_nonce'], 'tokoku_save_produk_meta' ) ) {
        return;
    }

    // 2. Mencegah Autosave
    // Jangan simpan meta kustom jika WordPress sedang melakukan penyimpanan otomatis (autosave).
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // 3. Cek Hak Akses Pengguna (Capability Check)
    // Pastikan user saat ini punya hak (izin) untuk mengedit post ini.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // 4. Daftar Field dan Fungsi Sanitasinya
    // Ini sangat penting untuk mencegah XSS. Setiap tipe data memiliki cara sanitasi berbeda.
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
        '_produk_catatan'        => 'wp_kses_post',
        '_produk_jumlah_stok'    => 'intval',
        '_produk_label_khusus'   => 'sanitize_text_field',
        '_produk_video'                 => 'esc_url_raw',
        '_produk_marketplace_shopee'    => 'esc_url_raw',
        '_produk_marketplace_tokopedia' => 'esc_url_raw',
        '_produk_marketplace_lazada'    => 'esc_url_raw',
        '_produk_marketplace_tiktok'    => 'esc_url_raw',
        '_produk_marketplace_bukalapak' => 'esc_url_raw',
        '_produk_marketplace_blibli'    => 'esc_url_raw',
        '_produk_marketplace_lainnya'   => 'esc_url_raw',
    );

    foreach ( $fields as $key => $sanitize_fn ) {
        if ( isset( $_POST[ $key ] ) ) {
            $value = call_user_func( $sanitize_fn, $_POST[ $key ] );
            update_post_meta( $post_id, $key, $value );
        }
    }

    // 5. Simpan Galeri secara terpisah karena menggunakan nonce berbeda
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
 * Fungsi Bantuan (Helper): Mengambil Format Harga
 * Menghitung dan merender HTML untuk harga, termasuk membandingkan harga normal (coret)
 * dengan harga diskon (terkini).
 */
function tokoku_get_harga( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $harga        = get_post_meta( $post_id, '_produk_harga', true );
    $harga_diskon = get_post_meta( $post_id, '_produk_harga_diskon', true ); // Harga Coret (Original)
    $mata_uang    = get_theme_mod( 'tokoku_currency', 'Rp' );

    if ( ! $harga ) {
        return '<span class="price-contact">' . esc_html__( 'Hubungi Kami', 'tokoku' ) . '</span>';
    }

    $output = '';
    
    // If Harga Coret (original) exists and is higher than current price
    if ( $harga_diskon && (float)$harga_diskon > (float)$harga ) {
        $output .= '<span class="price-current">' . esc_html( $mata_uang ) . ' ' . esc_html( number_format( (float)$harga, 0, ',', '.' ) ) . '</span>';
        $output .= ' <span class="price-original">' . esc_html( $mata_uang ) . ' ' . esc_html( number_format( (float)$harga_diskon, 0, ',', '.' ) ) . '</span>';
    } else {
        $output .= '<span class="price-current">' . esc_html( $mata_uang ) . ' ' . esc_html( number_format( (float)$harga, 0, ',', '.' ) ) . '</span>';
    }

    return $output;
}


/**
 * Fungsi Bantuan (Helper): Mengambil Status Stok
 * Menerjemahkan nilai stok (tersedia, habis, preorder) menjadi array yang berisi label
 * bahasa Indonesia dan kelas CSS-nya.
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
