<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$harga        = get_post_meta( get_the_ID(), '_produk_harga', true );
$harga_diskon = get_post_meta( get_the_ID(), '_produk_harga_diskon', true );
$mata_uang    = get_theme_mod( 'tokoku_currency', 'Rp' );
$stok_status  = tokoku_get_stok_status( get_the_ID() );
$label_khusus = get_post_meta( get_the_ID(), '_produk_label_khusus', true );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'product-card' ); ?>>
    <div class="product-card__image">
        <a href="<?php the_permalink(); ?>" class="product-card__image-link" aria-label="<?php the_title_attribute(); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'tokoku-product-card', array( 'loading' => 'lazy' ) ); ?>
            <?php else : ?>
                <img src="<?php echo esc_url( TOKOKU_URI . '/assets/images/placeholder.svg' ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
            <?php endif; ?>
        </a>
        
        <div class="product-card__badges">
            <?php if ( $label_khusus ) : ?>
                <span class="product-card__badge badge-featured"><?php echo esc_html( $label_khusus ); ?></span>
            <?php endif; ?>

            <?php if ( $harga && $harga_diskon && (float)$harga_diskon > (float)$harga ) : ?>
                <?php 
                $diskon_persen = round( ( ( (float)$harga_diskon - (float)$harga ) / (float)$harga_diskon ) * 100 );
                ?>
                <span class="product-card__badge badge-discount">-<?php echo $diskon_persen; ?>%</span>
            <?php endif; ?>

            <?php if ( $stok_status['class'] === 'stok-habis' ) : ?>
                <span class="product-card__badge badge-soldout">Habis</span>
            <?php elseif ( $stok_status['class'] === 'stok-preorder' ) : ?>
                <span class="product-card__badge badge-preorder">Pre-Order</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-card__content">
        <div class="product-card__category">
            <?php
            $terms = get_the_terms( get_the_ID(), 'kategori_produk' );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                echo '<a href="' . esc_url( get_term_link( $terms[0] ) ) . '">' . esc_html( $terms[0]->name ) . '</a>';
            } else {
                echo '<span>Produk</span>';
            }
            ?>
        </div>
        
        <h3 class="product-card__title">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                <?php the_title(); ?>
            </a>
        </h3>

        <?php if ( get_theme_mod( 'tokoku_show_price', 'yes' ) === 'yes' ) : ?>
        <div class="product-card__price">
            <?php if ( $harga_diskon && (float)$harga_diskon > (float)$harga ) : ?>
                <span class="price-current"><?php echo esc_html( $mata_uang . ' ' . number_format( (float)$harga, 0, ',', '.' ) ); ?></span>
                <span class="price-original"><?php echo esc_html( $mata_uang . ' ' . number_format( (float)$harga_diskon, 0, ',', '.' ) ); ?></span>
            <?php elseif ( $harga ) : ?>
                <span class="price-current"><?php echo esc_html( $mata_uang . ' ' . number_format( (float)$harga, 0, ',', '.' ) ); ?></span>
            <?php else : ?>
                <span class="price-current price-call"><?php esc_html_e( 'Hubungi Kami', 'tokoku' ); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <button class="btn btn-primary btn-block btn-whatsapp-order" 
                data-product-id="<?php the_ID(); ?>"
                data-product-name="<?php the_title_attribute(); ?>"
                data-product-sku="<?php echo esc_attr( get_post_meta( get_the_ID(), '_produk_sku', true ) ); ?>"
                data-product-url="<?php the_permalink(); ?>"
                data-product-price="<?php echo esc_attr( $harga ? $mata_uang . ' ' . number_format( (float)$harga, 0, ',', '.' ) : 'Hubungi Kami' ); ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" style="margin-right:6px; flex-shrink:0;">
                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/>
            </svg>
            <span>Pesan Sekarang</span>
        </button>
    </div>
</article>
