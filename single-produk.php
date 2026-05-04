<?php
/**
 * The template for displaying single products
 *
 * @package TokoKu
 */

get_header(); ?>

<main id="main-content" class="site-main single-product">
    <div class="container">
        
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="product-details">
                <div class="product-gallery">
                    <div class="main-image">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'tokoku-product-large' ); ?>
                        <?php elseif ( get_post_meta( get_the_ID(), '_produk_dummy_img', true ) ) : ?>
                            <img src="<?php echo esc_url( get_post_meta( get_the_ID(), '_produk_dummy_img', true ) ); ?>" alt="<?php the_title(); ?>">
                        <?php else : ?>
                            <img src="<?php echo esc_url( TOKOKU_URI . '/assets/images/placeholder.svg' ); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                    
                    <?php
                    $gallery_ids = get_post_meta( get_the_ID(), '_produk_gallery', true );
                    if ( $gallery_ids ) :
                        $ids = explode( ',', $gallery_ids );
                        ?>
                        <div class="gallery-thumbs">
                            <?php foreach ( $ids as $id ) : ?>
                                <div class="thumb">
                                    <?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php 
                    $video_url = get_post_meta( get_the_ID(), '_produk_video', true );
                    if ( $video_url ) : 
                    ?>
                        <a href="<?php echo esc_url( $video_url ); ?>" target="_blank" class="btn-watch-video">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                            Tonton Video Produk
                        </a>
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <nav class="breadcrumb">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
                        <?php
                        $terms = get_the_terms( get_the_ID(), 'kategori_produk' );
                        if ( ! empty( $terms ) ) {
                            echo ' &raquo; <a href="' . esc_url( get_term_link( $terms[0] ) ) . '">' . esc_html( $terms[0]->name ) . '</a>';
                        }
                        ?>
                    </nav>

                    <?php
                    // Get all new meta values
                    $harga          = get_post_meta( get_the_ID(), '_produk_harga', true );
                    $harga_diskon   = get_post_meta( get_the_ID(), '_produk_harga_diskon', true );
                    $multi_pilihan  = get_post_meta( get_the_ID(), '_produk_multi_pilihan', true );
                    $multi_harga    = get_post_meta( get_the_ID(), '_produk_multi_harga', true );
                    $warna          = get_post_meta( get_the_ID(), '_produk_pilihan_warna', true );
                    $catatan        = get_post_meta( get_the_ID(), '_produk_catatan', true );
                    $jumlah_stok    = get_post_meta( get_the_ID(), '_produk_jumlah_stok', true );
                    $berat          = get_post_meta( get_the_ID(), '_produk_berat', true );
                    $marketplace_shopee    = get_post_meta( get_the_ID(), '_produk_marketplace_shopee', true );
                    $marketplace_tokopedia = get_post_meta( get_the_ID(), '_produk_marketplace_tokopedia', true );
                    $marketplace_lazada    = get_post_meta( get_the_ID(), '_produk_marketplace_lazada', true );
                    $marketplace_tiktok    = get_post_meta( get_the_ID(), '_produk_marketplace_tiktok', true );
                    $marketplace_bukalapak = get_post_meta( get_the_ID(), '_produk_marketplace_bukalapak', true );
                    $marketplace_blibli    = get_post_meta( get_the_ID(), '_produk_marketplace_blibli', true );
                    $marketplace_lainnya   = get_post_meta( get_the_ID(), '_produk_marketplace_lainnya', true );

                    $has_marketplace = ($marketplace_shopee || $marketplace_tokopedia || $marketplace_lazada || $marketplace_tiktok || $marketplace_bukalapak || $marketplace_blibli || $marketplace_lainnya);
                    $label_khusus   = get_post_meta( get_the_ID(), '_produk_label_khusus', true );
                    $mata_uang      = get_theme_mod( 'tokoku_currency', 'Rp' );
                    $show_price     = get_theme_mod( 'tokoku_show_price', 'yes' );
                    ?>

                    <h1 class="product-title">
                        <?php 
                        if ( $label_khusus ) {
                            echo '<span class="special-label">' . esc_html( $label_khusus ) . '</span>';
                        }
                        the_title(); 
                        ?>
                    </h1>

                    <?php if ( $show_price === 'yes' && ( $harga || $harga_diskon ) ) : ?>
                    <div class="product-price-display">
                        <?php if ( $harga_diskon && $harga_diskon < $harga ) : ?>
                            <span class="price-current"><?php echo esc_html( $mata_uang . ' ' . number_format( $harga_diskon, 0, ',', '.' ) ); ?></span>
                            <span class="price-original"><?php echo esc_html( $mata_uang . ' ' . number_format( $harga, 0, ',', '.' ) ); ?></span>
                            <?php 
                            $diskon_persen = round( ( ( $harga - $harga_diskon ) / $harga ) * 100 );
                            echo '<span class="price-discount-badge">-' . $diskon_persen . '%</span>';
                            ?>
                        <?php else : ?>
                            <span class="price-current"><?php echo esc_html( $mata_uang . ' ' . number_format( $harga, 0, ',', '.' ) ); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( $multi_pilihan ) : 
                        $pilihan_arr = array_map( 'trim', explode( ',', $multi_pilihan ) );
                        $harga_arr   = $multi_harga ? array_map( 'trim', explode( ',', $multi_harga ) ) : array();
                    ?>
                    <div class="product-variations">
                        <span class="variations-label">Pilihan:</span>
                        <div class="variations-list">
                            <?php foreach ( $pilihan_arr as $index => $pilihan ) : 
                                $harga_varian = isset( $harga_arr[$index] ) && is_numeric($harga_arr[$index]) ? $harga_arr[$index] : '';
                            ?>
                                <button class="btn-variation" <?php if($harga_varian) echo 'data-price="' . esc_attr( $mata_uang . ' ' . number_format($harga_varian, 0, ',', '.') ) . '"'; ?>>
                                    <?php echo esc_html( $pilihan ); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="product-specs-table">
                        <?php
                        $sku = get_post_meta( get_the_ID(), '_produk_sku', true );
                        $stok = tokoku_get_stok_status();
                        ?>
                        
                        <?php if ( $sku ) : ?>
                        <div class="spec-row">
                            <div class="spec-label">Kode</div>
                            <div class="spec-value"><?php echo esc_html( $sku ); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="spec-row">
                            <div class="spec-label">Stok</div>
                            <div class="spec-value <?php echo esc_attr( $stok['class'] ); ?> <?php echo $stok['class'] == 'stok-preorder' ? 'is-preorder' : ''; ?>">
                                <?php if ( $stok['class'] == 'stok-preorder' ) : ?>
                                    <span class="dashicons dashicons-clock" style="font-size: 16px; width: 16px; height: 16px;"></span>
                                <?php endif; ?>
                                <?php echo $stok['label']; ?>
                                <?php if ( $jumlah_stok ) echo ' <span class="stock-count">(' . esc_html( $jumlah_stok ) . ')</span>'; ?>
                            </div>
                        </div>
                        
                        <?php if ( $berat ) : ?>
                        <div class="spec-row">
                            <div class="spec-label">Berat</div>
                            <div class="spec-value"><?php echo esc_html( $berat ); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ( $warna ) : ?>
                        <div class="spec-row">
                            <div class="spec-label">Warna</div>
                            <div class="spec-value"><?php echo esc_html( $warna ); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $terms ) ) : ?>
                        <div class="spec-row">
                            <div class="spec-label">Kategori</div>
                            <div class="spec-value">
                                <a href="<?php echo esc_url( get_term_link( $terms[0] ) ); ?>" style="display: flex; align-items: center; gap: 8px;">
                                    <?php
                                    $icon_id = get_term_meta( $terms[0]->term_id, 'tokoku_kategori_icon', true );
                                    $icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'thumbnail' ) : '';
                                    if ( $icon_url ) {
                                        echo '<img src="' . esc_url( $icon_url ) . '" style="width: 20px; height: 20px; object-fit: contain; border-radius: 4px;">';
                                    }
                                    ?>
                                    <?php echo esc_html( $terms[0]->name ); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ( $catatan ) : ?>
                    <div class="product-note-box">
                        <span class="dashicons dashicons-info" style="color: #ffb300;"></span>
                        <div class="note-content">
                            <strong>Catatan:</strong> 
                            <div class="note-text-content" style="margin-top: 5px;">
                                <?php echo wpautop( wp_kses_post( $catatan ) ); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ( $stok['class'] == 'stok-preorder' ) : ?>
                    <div class="preorder-notice">
                        <div class="notice-title">
                            <span class="dashicons dashicons-clock" style="font-size: 24px; width: 24px; height: 24px;"></span>
                            PRE ORDER
                        </div>
                        <p>Hubungi kami untuk informasi lebih lanjut mengenai pemesanan produk ini.</p>
                    </div>
                    <?php endif; ?>

                    <div class="product-actions">
                        <?php
                        if ( $show_price === 'yes' ) {
                            $price_val = $harga_diskon ? $mata_uang . ' ' . number_format( $harga_diskon, 0, ',', '.' ) : ($harga ? $mata_uang . ' ' . number_format( $harga, 0, ',', '.' ) : 'Hubungi Kami');
                        } else {
                            $price_val = 'Tanyakan Harga';
                        }
                        ?>
                        <button class="btn btn-primary btn-lg btn-block btn-whatsapp-order btn-contact-us"
                                data-product-id="<?php the_ID(); ?>"
                                data-product-name="<?php the_title(); ?>"
                                data-product-price="<?php echo esc_attr( $price_val ); ?>">
                            <span class="dashicons dashicons-whatsapp" style="margin-right: 8px;"></span>
                            Pesan via WhatsApp
                        </button>
                        
                        <?php if ( $has_marketplace ) : ?>
                        <div class="marketplace-links">
                            <span class="marketplace-title">Atau Beli di Marketplace:</span>
                            <div class="marketplace-buttons">
                                <?php if ( $marketplace_shopee ) : ?>
                                <a href="<?php echo esc_url( $marketplace_shopee ); ?>" target="_blank" class="btn-marketplace mp-shopee">
                                    <span class="dashicons dashicons-cart" style="margin-right: 8px;"></span> Shopee
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_tokopedia ) : ?>
                                <a href="<?php echo esc_url( $marketplace_tokopedia ); ?>" target="_blank" class="btn-marketplace mp-tokopedia">
                                    <span class="dashicons dashicons-cart" style="margin-right: 8px;"></span> Tokopedia
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_lazada ) : ?>
                                <a href="<?php echo esc_url( $marketplace_lazada ); ?>" target="_blank" class="btn-marketplace mp-lazada">
                                    <span class="dashicons dashicons-cart" style="margin-right: 8px;"></span> Lazada
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_tiktok ) : ?>
                                <a href="<?php echo esc_url( $marketplace_tiktok ); ?>" target="_blank" class="btn-marketplace mp-tiktok">
                                    <span class="dashicons dashicons-cart" style="margin-right: 8px;"></span> TikTok
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_bukalapak ) : ?>
                                <a href="<?php echo esc_url( $marketplace_bukalapak ); ?>" target="_blank" class="btn-marketplace mp-bukalapak">
                                    <span class="dashicons dashicons-cart" style="margin-right: 8px;"></span> Bukalapak
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_blibli ) : ?>
                                <a href="<?php echo esc_url( $marketplace_blibli ); ?>" target="_blank" class="btn-marketplace mp-blibli">
                                    <span class="dashicons dashicons-cart" style="margin-right: 8px;"></span> Blibli
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_lainnya ) : ?>
                                <a href="<?php echo esc_url( $marketplace_lainnya ); ?>" target="_blank" class="btn-marketplace mp-lainnya">
                                    <span class="dashicons dashicons-admin-links" style="margin-right: 8px;"></span> Lainnya
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-share">
                        <span class="share-label">Bagikan ke</span>
                        <div class="share-icons">
                            <?php $current_url = urlencode(get_permalink()); $current_title = urlencode(get_the_title()); ?>
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $current_url; ?>" target="_blank" class="share-icon fb" aria-label="Facebook">
                                <span class="dashicons dashicons-facebook-alt"></span>
                            </a>
                            <!-- Twitter -->
                            <a href="https://twitter.com/intent/tweet?url=<?php echo $current_url; ?>&text=<?php echo $current_title; ?>" target="_blank" class="share-icon tw" aria-label="Twitter">
                                <span class="dashicons dashicons-twitter"></span>
                            </a>
                            <!-- WhatsApp -->
                            <a href="https://api.whatsapp.com/send?text=<?php echo $current_title . ' ' . $current_url; ?>" target="_blank" class="share-icon wa" aria-label="WhatsApp">
                                <span class="dashicons dashicons-whatsapp"></span>
                            </a>
                            <!-- Instagram -->
                            <a href="https://www.instagram.com/" target="_blank" class="share-icon ig" aria-label="Instagram">
                                <span class="dashicons dashicons-instagram"></span>
                            </a>
                            <!-- Copy Link -->
                            <button type="button" onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan berhasil disalin!');" class="share-icon link-share" aria-label="Copy Link" title="Salin Tautan" style="border:none; cursor:pointer;">
                                <span class="dashicons dashicons-admin-links"></span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="product-description-wrapper">
                <div class="product-description">
                    <h3>Deskripsi Produk</h3>
                    <div class="content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <?php
            if ( ! empty( $terms ) ) :
                $related = new WP_Query( array(
                    'post_type' => 'produk',
                    'posts_per_page' => 4,
                    'post__not_in' => array( get_the_ID() ),
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'kategori_produk',
                            'field' => 'term_id',
                            'terms' => $terms[0]->term_id,
                        ),
                    ),
                ) );

                if ( $related->have_posts() ) : ?>
                    <section class="related-products section-padding">
                        <h2 class="section-title">Produk Terkait</h2>
                        <div class="product-grid">
                            <?php while ( $related->have_posts() ) : $related->the_post();
                                get_template_part( 'template-parts/product-card' );
                            endwhile; ?>
                        </div>
                    </section>
                <?php wp_reset_postdata(); endif;
            endif; ?>

        <?php endwhile; ?>

    </div>
</main>

<style>
.single-product { padding: 40px 0; }
.product-details { display: grid; grid-template-columns: 5fr 7fr; gap: 50px; margin-bottom: 60px; }
.main-image { border-radius: var(--radius); overflow: hidden; margin-bottom: 20px; border: 1px solid var(--border); background: var(--bg2); }
.main-image img { width: 100%; height: auto; display: block; transition: transform 0.15s ease-out; transform-origin: center center; }
.gallery-thumbs { display: flex; gap: 12px; }
.gallery-thumbs .thumb { cursor: pointer; border-radius: 8px; overflow: hidden; border: 2px solid transparent; transition: var(--ease); }
.gallery-thumbs .thumb:hover { border-color: var(--primary); }
.gallery-thumbs img { width: 80px; height: 80px; object-fit: cover; display: block; }

.breadcrumb { font-size: 0.85rem; color: var(--text2); margin-bottom: 25px; display: flex; align-items: center; gap: 8px; }
.breadcrumb a { color: var(--text2); text-decoration: none; transition: var(--ease); }
.breadcrumb a:hover { color: var(--primary); }

.product-title { font-size: 2.2rem; font-weight: 800; color: var(--text); margin-bottom: 20px; line-height: 1.2; letter-spacing: -0.5px; }

/* Specs Table */
.product-specs-table { border-top: 1.5px solid var(--border); margin-bottom: 30px; }
.spec-row { display: flex; border-bottom: 1px solid var(--border); padding: 14px 0; align-items: center; }
.spec-label { width: 130px; font-weight: 700; color: var(--text2); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0; }
.spec-value { flex: 1; color: var(--text); font-weight: 600; }
.spec-value a { color: var(--primary); text-decoration: none; }
.spec-value a:hover { text-decoration: underline; }

/* Pre Order styles */
.is-preorder { color: var(--orange); font-weight: 700; display: flex; align-items: center; gap: 6px; }
.preorder-notice { margin-bottom: 30px; padding: 20px; background: var(--bg2); border-left: 4px solid var(--orange); border-radius: 8px; }
.preorder-notice .notice-title { color: var(--orange); font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
.preorder-notice p { color: var(--text2); margin: 0; font-size: 0.9rem; line-height: 1.5; }

/* Button */
.btn-contact-us {
    background: var(--gradient);
    color: #fff;
    font-size: 1.1rem;
    font-weight: 800;
    padding: 16px 30px;
    border-radius: 10px;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    border: none;
    cursor: pointer;
    transition: var(--ease);
    margin-bottom: 20px;
    box-shadow: 0 10px 20px var(--shadow);
}
.btn-contact-us:hover { transform: translateY(-3px); box-shadow: 0 15px 30px var(--shadow); opacity: 0.9; color: #fff; }

/* Share */
.product-share { display: flex; align-items: center; gap: 20px; padding: 20px; background: var(--bg2); border-radius: 12px; margin-bottom: 20px; }
.share-label { font-size: 0.85rem; color: var(--text2); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
.share-icons { display: flex; gap: 12px; }
.share-icon { display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; color: #fff; transition: var(--ease); }
.share-icon:hover { transform: scale(1.1); color: #fff; }
.share-icon.fb { background-color: #3b5998; }
.share-icon.tw { background-color: #000000; }
.share-icon.wa { background-color: #25d366; }
.share-icon.ig { background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); }
.share-icon.link-share { background-color: #64748b; }

.product-description-wrapper { 
    border-top: 1.5px solid var(--border); 
    padding: 60px 0 80px; 
    margin-top: 20px;
    background: var(--bg2);
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    width: 100vw;
}
.product-description { 
    width: 100%;
    max-width: 1400px; 
    margin: 0 auto; 
    padding: 0 40px;
}
.product-description h3 { 
    font-size: 2.2rem; 
    font-weight: 800; 
    margin-bottom: 50px; 
    color: var(--text); 
    text-align: center; 
    display: block; 
    position: relative;
}
.product-description h3::after { 
    content: ''; 
    position: absolute; 
    bottom: -15px; 
    left: 50%; 
    transform: translateX(-50%); 
    width: 80px; 
    height: 4px; 
    background: var(--primary); 
    border-radius: 2px; 
}
.product-description .content { 
    color: var(--text); 
    line-height: 1.8; 
    font-size: 1.15rem; 
    background: var(--bg);
    padding: 60px;
    border-radius: 24px;
    box-shadow: 0 10px 40px var(--shadow);
    width: 100%;
}
.product-description .content p { margin-bottom: 25px; }
.product-description .content p:last-child { margin-bottom: 0; }

@media (max-width: 768px) {
    .single-product { padding: 20px 0; }
    .product-gallery { display: flex; flex-direction: column; align-items: center; width: 100%; margin-bottom: 25px; }
    .main-image { width: 95% !important; margin: 0 auto 15px !important; }
    .gallery-thumbs { justify-content: center; width: 100%; }
    .btn-contact-us { width: 90% !important; margin: 0 auto 20px !important; }
    .product-description-wrapper { padding: 40px 0 55px; margin-top: 30px; }
    .product-description { padding: 0 20px; }
    .product-description h3 { font-size: 1.5rem; margin-bottom: 35px; }
    .product-description .content { padding: 30px 20px; font-size: 1rem; border-radius: 0; box-shadow: none; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: transparent; }
}

@media (max-width: 992px) {
    .product-details { grid-template-columns: 1fr; gap: 30px; }
    .product-title { font-size: 1.45rem; }
    .product-price-display .price-current { font-size: 1.55rem; }
    .breadcrumb { font-size: 0.78rem; }
    .spec-label { font-size: 0.82rem; }
    .spec-value { font-size: 0.9rem; }
    .variations-label { font-size: 0.85rem; }
    .btn-variation { font-size: 0.88rem; }
}

/* New Frontend Details CSS */
.special-label { 
    display: inline-block; 
    background: linear-gradient(135deg, #ff9800, #f44336); 
    color: #fff; 
    font-size: 0.72rem; 
    padding: 5px 12px; 
    border-radius: 50px; 
    vertical-align: middle; 
    margin-right: 12px; 
    text-transform: uppercase; 
    letter-spacing: 1.2px; 
    font-weight: 800;
    box-shadow: 0 4px 10px rgba(244, 67, 54, 0.3);
    border: 1px solid rgba(255,255,255,0.1);
    line-height: 1;
}

.product-price-display { margin-bottom: 25px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.product-price-display .price-current { font-size: 1.8rem; font-weight: 800; color: var(--primary); }
.product-price-display .price-original { font-size: 1.1rem; color: var(--text2); text-decoration: line-through; }
.product-price-display .price-discount-badge { background: #ffebee; color: #d32f2f; font-weight: 700; font-size: 0.85rem; padding: 4px 8px; border-radius: 4px; }

.product-variations { margin-bottom: 30px; }
.variations-label { display: block; font-weight: 700; color: var(--text2); margin-bottom: 10px; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
.variations-list { display: flex; flex-wrap: wrap; gap: 10px; }
.btn-variation { background: #fff; border: 1px solid var(--border); padding: 8px 16px; border-radius: 6px; font-weight: 600; color: var(--text); cursor: pointer; transition: var(--ease); font-size: 0.95rem; }
.btn-variation:hover, .btn-variation.active { border-color: var(--primary); color: var(--primary); background: var(--bg2); }

.stock-count { font-size: 0.85rem; color: var(--text2); font-weight: normal; margin-left: 4px; }

.product-note-box { background: #fff8e1; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; display: flex; gap: 12px; margin-bottom: 30px; color: #5c4e16; }
.product-note-box svg { flex-shrink: 0; color: #ffb300; }
.product-note-box .note-content { font-size: 0.95rem; line-height: 1.5; }

.btn-watch-video { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; margin-top: 15px; padding: 12px; background: #fff; border: 1.5px solid var(--border); border-radius: 8px; font-weight: 700; color: var(--text); cursor: pointer; transition: var(--ease); text-decoration: none; }
.btn-watch-video:hover { border-color: #ff0000; color: #ff0000; }

.marketplace-links { margin-bottom: 30px; padding-top: 20px; border-top: 1.5px dashed var(--border); }
.marketplace-title { display: block; font-size: 0.9rem; font-weight: 700; color: var(--text2); margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; }
.marketplace-buttons { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.btn-marketplace { 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    gap: 8px; 
    width: 100%; 
    padding: 13px 24px; 
    border-radius: 10px; 
    font-weight: 800; 
    color: #fff; 
    text-decoration: none; 
    transition: var(--ease); 
    font-size: 0.9rem; 
}
.btn-marketplace:hover { transform: translateY(-2px); opacity: 0.9; color: #fff; }
.mp-shopee { background: #ee4d2d; }
.mp-tokopedia { background: #00aa5b; }
.mp-lazada { background: #000080; }
.mp-tiktok { background: #000000; }
.mp-bukalapak { background: #e31e52; }
.mp-blibli { background: #0095da; }
.mp-lainnya { background: #6c757d; }

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const variationBtns = document.querySelectorAll('.btn-variation');
    const priceCurrent = document.querySelector('.product-price-display .price-current');
    
    variationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all
            variationBtns.forEach(b => b.classList.remove('active'));
            // Add to clicked
            this.classList.add('active');
            
            // Update price if available
            const newPrice = this.getAttribute('data-price');
            if (newPrice && priceCurrent) {
                priceCurrent.textContent = newPrice;
            }
        });
    });

    // 🖼️ Gallery Switching
    const mainImg = document.querySelector('.main-image img');
    const thumbnails = document.querySelectorAll('.gallery-thumbs .thumb img');

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            if (mainImg) {
                mainImg.src = this.src.replace('-150x150', ''); // Try to get larger version if it's a thumbnail
                // Update active state of thumbnails if needed
                document.querySelectorAll('.gallery-thumbs .thumb').forEach(t => t.style.borderColor = 'transparent');
                this.parentElement.style.borderColor = 'var(--primary)';
            }
        });
    });

    // 🔍 Product Image Zoom (Hover & Lightbox)
    const mainImgContainer = document.querySelector('.main-image');
    const lightbox = document.getElementById('tokoku-lightbox');
    const lightboxImg = document.getElementById('tokoku-lightbox-img');
    const lightboxClose = document.querySelector('.tokoku-lightbox-close');

    if (mainImg && mainImgContainer) {
        // Hover Zoom (Desktop)
        mainImgContainer.addEventListener('mousemove', function(e) {
            if (window.innerWidth > 768) {
                const rect = this.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                
                mainImg.style.transformOrigin = `${x}% ${y}%`;
                mainImg.style.transform = 'scale(2)';
            }
        });

        mainImgContainer.addEventListener('mouseleave', function() {
            mainImg.style.transform = 'scale(1)';
            mainImg.style.transformOrigin = 'center center';
        });

        // Click to Lightbox (Mobile & Desktop)
        mainImg.addEventListener('click', () => {
            lightboxImg.src = mainImg.src;
            lightbox.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    }

    if (lightbox) {
        function closeLightbox() {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        }

        lightboxClose?.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target === document.querySelector('.tokoku-lightbox-content')) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
        });
    }
});
</script>

<?php get_footer(); ?>
