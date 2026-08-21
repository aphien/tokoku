<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$harga        = get_post_meta( get_the_ID(), '_produk_harga', true );
$harga_diskon = get_post_meta( get_the_ID(), '_produk_harga_diskon', true );
$mata_uang    = get_theme_mod( 'tokoku_currency', 'Rp' );
$stok_status  = tokoku_get_stok_status( get_the_ID() );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'product-card' ); ?>>
    <div class="product-card__image">
        <a href="<?php the_permalink(); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'tokoku-product-card' ); ?>
            <?php else : ?>
                <img src="<?php echo esc_url( TOKOKU_URI . '/assets/images/placeholder.svg' ); ?>" alt="<?php the_title(); ?>">
            <?php endif; ?>
        </a>
        
        <?php if ( $harga && $harga_diskon && (float)$harga_diskon > (float)$harga ) : ?>
            <?php 
            $diskon_persen = round( ( ( (float)$harga_diskon - (float)$harga ) / (float)$harga_diskon ) * 100 );
            ?>
            <span class="product-card__badge badge-discount">-<?php echo $diskon_persen; ?>%</span>
        <?php endif; ?>

        <?php if ( $stok_status['class'] === 'stok-habis' ) : ?>
            <span class="product-card__badge badge-soldout">Habis</span>
        <?php endif; ?>
    </div>

    <div class="product-card__content">
        <div class="product-card__category">
            <?php
            $terms = get_the_terms( get_the_ID(), 'kategori_produk' );
            if ( ! empty( $terms ) ) {
                echo '<a href="' . esc_url( get_term_link( $terms[0] ) ) . '">' . esc_html( $terms[0]->name ) . '</a>';
            }
            ?>
        </div>
        
        <h3 class="product-card__title">
            <a href="<?php the_permalink(); ?>">
                <?php 
                $title = get_the_title();
                $limit = 35;
                echo esc_html( mb_strimwidth( $title, 0, $limit, '...' ) ); 
                ?>
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
                <span class="price-current"><?php esc_html_e( 'Hubungi Kami', 'tokoku' ); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <button class="btn btn-primary btn-block btn-whatsapp-order" 
                data-product-id="<?php the_ID(); ?>"
                data-product-name="<?php the_title(); ?>"
                data-product-sku="<?php echo esc_attr( get_post_meta( get_the_ID(), '_produk_sku', true ) ); ?>"
                data-product-url="<?php the_permalink(); ?>"
                data-product-price="<?php echo esc_attr( $harga ? $mata_uang . ' ' . number_format( (float)$harga, 0, ',', '.' ) : 'Hubungi Kami' ); ?>">
            Pesan Sekarang
        </button>
    </div>
</article>
