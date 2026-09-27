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
                <?php the_post_thumbnail( 'tokoku-product-card', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
            <?php else : ?>
                <img src="<?php echo esc_url( TOKOKU_URI . '/assets/images/placeholder.svg' ); ?>" alt="<?php the_title_attribute(); ?>" width="400" height="400" loading="lazy" decoding="async">
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

        <!-- Centered Overlay on Image (Judul & Harga Produk Rata Tengah) -->
        <div class="product-card__overlay">
            <a href="<?php the_permalink(); ?>" class="product-card__stretch-link" aria-label="<?php the_title_attribute(); ?>"></a>
            <div class="product-card__overlay-inner">
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
            </div>
        </div>
    </div>
</article>
