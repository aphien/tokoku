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
 * Add Icon field to "Add New Category" screen
 */
function tokoku_kategori_produk_add_form_fields() {
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
 * Add Icon field to "Edit Category" screen
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
 * Save Taxonomy Meta
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
 * Enqueue Admin Scripts for Taxonomy
 */
function tokoku_taxonomy_admin_scripts( $hook ) {
    $screen = get_current_screen();
    if ( $screen->taxonomy == 'kategori_produk' ) {
        wp_enqueue_media();
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.tokoku-tax-upload-btn').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var container = button.closest('.tokoku-taxonomy-media-upload');
                var custom_uploader = wp.media({
                    title: 'Pilih Ikon Kategori',
                    button: { text: 'Gunakan Ikon' },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    container.find('#tokoku_kategori_icon').val(attachment.id);
                    container.find('#tokoku_kategori_icon_preview').html('<img src="' + attachment.url + '" style="max-width:100px; height:auto; border-radius:8px; border:1px solid #ddd;">');
                    container.find('.tokoku-tax-remove-btn').show();
                }).open();
            });

            $('.tokoku-tax-remove-btn').on('click', function(e) {
                e.preventDefault();
                var container = $(this).closest('.tokoku-taxonomy-media-upload');
                container.find('#tokoku_kategori_icon').val('');
                container.find('#tokoku_kategori_icon_preview').empty();
                $(this).hide();
            });
        });
        </script>
        <?php
    }
}
add_action( 'admin_enqueue_scripts', 'tokoku_taxonomy_admin_scripts' );
