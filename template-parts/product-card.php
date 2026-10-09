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

        <!-- Badge Kiri Atas: Diskon, Habis, Pre-Order -->
        <div class="product-card__badges">
            <?php if ( $harga && $harga_diskon && (float)$harga_diskon > (float)$harga ) : ?>
                <?php 
                $diskon_persen = ( (float)$harga_diskon > 0 ) ? round( ( ( (float)$harga_diskon - (float)$harga ) / (float)$harga_diskon ) * 100 ) : 0;
                ?>
                <span class="product-card__badge badge-discount">-<?php echo esc_html( $diskon_persen ); ?>%</span>
            <?php endif; ?>

            <?php if ( $stok_status['class'] === 'stok-habis' ) : ?>
                <span class="product-card__badge badge-soldout">Habis</span>
            <?php elseif ( $stok_status['class'] === 'stok-preorder' ) : ?>
                <span class="product-card__badge badge-preorder">Pre-Order</span>
            <?php endif; ?>
        </div>

        <!-- Badge Kanan Atas: Label Khusus / Special Label -->
        <?php if ( $label_khusus ) : ?>
        <div class="product-card__special-badge">
            <span><?php echo esc_html( $label_khusus ); ?></span>
        </div>
        <?php endif; ?>
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

        <button type="button" class="btn btn-primary btn-block btn-whatsapp-order" 
                data-product-id="<?php the_ID(); ?>"
                data-product-name="<?php the_title_attribute(); ?>"
                data-product-sku="<?php echo esc_attr( get_post_meta( get_the_ID(), '_produk_sku', true ) ); ?>"
                data-product-url="<?php the_permalink(); ?>"
                data-product-price="<?php echo esc_attr( $harga ? $mata_uang . ' ' . number_format( (float)$harga, 0, ',', '.' ) : 'Hubungi Kami' ); ?>">
            <?php echo tokoku_icon( 'whatsapp', 18, '', 'style="margin-right:7px; flex-shrink:0;"' ); ?>
            <span>Pesan Sekarang</span>
        </button>
    </div>
</article>
