<?php
/**
 * Taxonomy Meta: Product Category Icons
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Menambahkan Input Gambar pada Halaman "Tambah Kategori Baru"
 * Menampilkan tombol untuk mengunggah gambar (Ikon Kategori) saat admin membuat kategori baru.
 */
function tokoku_kategori_produk_add_form_fields() {
    wp_nonce_field( 'tokoku_save_kategori_meta', 'tokoku_kategori_nonce' );
    ?>
    <div class="form-field term-group">
        <label for="tokoku_kategori_icon"><?php _e( 'Ikon Kategori', 'tokoku' ); ?></label>
        <div class="tokoku-taxonomy-media-upload">
            <input type="hidden" id="tokoku_kategori_icon" name="tokoku_kategori_icon" value="">
            <div id="tokoku_kategori_icon_preview" style="margin-bottom:10px;"></div>
            <button type="button" class="button tokoku-tax-upload-btn"><?php _e( 'Unggah Ikon', 'tokoku' ); ?></button>
            <button type="button" class="button tokoku-tax-remove-btn" style="display:none;"><?php _e( 'Hapus', 'tokoku' ); ?></button>
        </div>
        <p class="description"><?php _e( 'Unggah gambar ikon untuk kategori ini. Rekomendasi ukuran 100x100 pixel.', 'tokoku' ); ?></p>
    </div>
    <?php
}
add_action( 'kategori_produk_add_form_fields', 'tokoku_kategori_produk_add_form_fields', 10 );

/**
 * Menambahkan Input Gambar pada Halaman "Edit Kategori"
 * Menampilkan preview gambar yang sudah tersimpan beserta tombol ubah/hapus ikon saat admin mengedit kategori.
 */
function tokoku_kategori_produk_edit_form_fields( $term ) {
    $icon_id = get_term_meta( $term->term_id, 'tokoku_kategori_icon', true );
    $icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'thumbnail' ) : '';
    wp_nonce_field( 'tokoku_save_kategori_meta', 'tokoku_kategori_nonce' );
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="tokoku_kategori_icon"><?php _e( 'Ikon Kategori', 'tokoku' ); ?></label></th>
        <td>
            <div class="tokoku-taxonomy-media-upload">
                <input type="hidden" id="tokoku_kategori_icon" name="tokoku_kategori_icon" value="<?php echo esc_attr( $icon_id ); ?>">
                <div id="tokoku_kategori_icon_preview" style="margin-bottom:10px;">
                    <?php if ( $icon_url ) : ?>
                        <img src="<?php echo esc_url( $icon_url ); ?>" style="max-width:100px; height:auto; border-radius:8px; border:1px solid #ddd;">
                    <?php endif; ?>
                </div>
                <button type="button" class="button tokoku-tax-upload-btn"><?php _e( 'Unggah Ikon', 'tokoku' ); ?></button>
                <button type="button" class="button tokoku-tax-remove-btn" style="<?php echo $icon_id ? '' : 'display:none;'; ?>"><?php _e( 'Hapus', 'tokoku' ); ?></button>
            </div>
            <p class="description"><?php _e( 'Unggah gambar ikon untuk kategori ini. Rekomendasi ukuran 100x100 pixel.', 'tokoku' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'kategori_produk_edit_form_fields', 'tokoku_kategori_produk_edit_form_fields', 10 );

/**
 * Menyimpan Data Tambahan Taksonomi (Ikon Kategori)
 * Dilengkapi dengan verifikasi keamanan (Nonce) dan hak akses (Capability).
 */
function tokoku_save_kategori_produk_meta( $term_id ) {
    // 1. Check Nonce
    if ( ! isset( $_POST['tokoku_kategori_nonce'] ) || ! wp_verify_nonce( $_POST['tokoku_kategori_nonce'], 'tokoku_save_kategori_meta' ) ) {
        return;
    }

    // 2. Check Permissions
    if ( ! current_user_can( 'manage_categories' ) ) {
        return;
    }

    if ( isset( $_POST['tokoku_kategori_icon'] ) ) {
        update_term_meta( $term_id, 'tokoku_kategori_icon', absint( $_POST['tokoku_kategori_icon'] ) );
    }
}
add_action( 'created_kategori_produk', 'tokoku_save_kategori_produk_meta', 10 );
add_action( 'edited_kategori_produk', 'tokoku_save_kategori_produk_meta', 10 );

/**
 * Menambahkan Kolom Kustom di Tabel Daftar Kategori
 * Membuat kolom baru bernama "Ikon" agar admin bisa melihat ikon masing-masing kategori.
 */
function tokoku_kategori_produk_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        if ( $key == 'cb' ) {
            $new_columns['cb'] = $value;
            $new_columns['icon'] = __( 'Ikon', 'tokoku' );
        } else {
            $new_columns[$key] = $value;
        }
    }
    return $new_columns;
}
add_filter( 'manage_edit-kategori_produk_columns', 'tokoku_kategori_produk_columns' );

/**
 * Merender Isi Kolom "Ikon" pada Tabel Kategori
 * Mengambil ID gambar dari meta kategori dan menampilkannya sebagai tag <img>.
 */
function tokoku_kategori_produk_column_content( $content, $column_name, $term_id ) {
    if ( $column_name == 'icon' ) {
        $icon_id = get_term_meta( $term_id, 'tokoku_kategori_icon', true );
        if ( $icon_id ) {
            $icon_url = wp_get_attachment_image_url( $icon_id, 'thumbnail' );
            if ( $icon_url ) {
                return '<img src="' . esc_url( $icon_url ) . '" style="width:40px; height:40px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">';
            }
        }
        return '<span class="dashicons dashicons-image-alt2" style="color:#ccc; font-size:30px; width:30px; height:30px;"></span>';
    }
    return $content;
}
add_filter( 'manage_kategori_produk_custom_column', 'tokoku_kategori_produk_column_content', 10, 3 );

/**
 * Enqueue Admin Scripts for Taxonomy
 */
function tokoku_taxonomy_admin_scripts( $hook ) {
    $screen = get_current_screen();
    if ( $screen && $screen->taxonomy == 'kategori_produk' ) {
        wp_enqueue_media();
        add_action( 'admin_footer', 'tokoku_taxonomy_inline_js' );
    }
}
add_action( 'admin_enqueue_scripts', 'tokoku_taxonomy_admin_scripts' );

/**
 * Inline JS for Taxonomy Meta
 */
function tokoku_taxonomy_inline_js() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Use delegated events for better reliability, especially with AJAX
        $(document).on('click', '.tokoku-tax-upload-btn', function(e) {
            e.preventDefault();
            
            if (typeof wp === 'undefined' || !wp.media) {
                console.error('WordPress Media Uploader not found');
                return;
            }

            var button = $(this);
            var container = button.closest('.tokoku-taxonomy-media-upload');
            
            var custom_uploader = wp.media({
                title: 'Pilih Ikon Kategori',
                button: { text: 'Gunakan Ikon' },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                var thumb = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
                
                container.find('#tokoku_kategori_icon').val(attachment.id);
                container.find('#tokoku_kategori_icon_preview').html('<img src="' + thumb + '" style="max-width:100px; height:auto; border-radius:8px; border:1px solid #ddd;">');
                container.find('.tokoku-tax-remove-btn').show();
            }).open();
        });

        $(document).on('click', '.tokoku-tax-remove-btn', function(e) {
            e.preventDefault();
            var container = $(this).closest('.tokoku-taxonomy-media-upload');
            container.find('#tokoku_kategori_icon').val('');
            container.find('#tokoku_kategori_icon_preview').empty();
            $(this).hide();
        });

        // Clear fields after AJAX term addition
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.data && typeof settings.data === 'string' && settings.data.indexOf('action=add-tag') !== -1 && settings.data.indexOf('taxonomy=kategori_produk') !== -1) {
                $('#tokoku_kategori_icon').val('');
                $('#tokoku_kategori_icon_preview').empty();
                $('.tokoku-tax-remove-btn').hide();
            }
        });
    });
    </script>
    <?php
}


