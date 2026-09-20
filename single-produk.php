<?php

if ( ! defined( 'ABSPATH' ) ) exit;
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
                    <nav class="breadcrumb" aria-label="Breadcrumb">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
                        <?php
                        $terms = get_the_terms( get_the_ID(), 'kategori_produk' );
                        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                            echo ' &raquo; <a href="' . esc_url( get_term_link( $terms[0] ) ) . '">' . esc_html( $terms[0]->name ) . '</a>';
                        }
                        echo ' &raquo; <span class="current" aria-current="page">' . esc_html( get_the_title() ) . '</span>';
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
                    $label_khusus_icon = get_post_meta( get_the_ID(), '_produk_label_khusus_icon', true );
                    $mata_uang      = get_theme_mod( 'tokoku_currency', 'Rp' );
                    $show_price     = get_theme_mod( 'tokoku_show_price', 'yes' );
                    ?>

                    <h1 class="product-title">
                        <?php 
                        if ( $label_khusus ) {
                            $icon_class = $label_khusus_icon ? esc_attr( $label_khusus_icon ) : 'dashicons-star-filled';
                            echo '<span class="special-label"><span class="dashicons ' . $icon_class . '" style="vertical-align: middle; margin-top: -3px; font-size: 16px; width: 16px; height: 16px;"></span> ' . esc_html( $label_khusus ) . '</span>';
                        }
                        the_title(); 
                        ?>
                    </h1>

                    <?php if ( $show_price === 'yes' && $harga ) : ?>
                    <div class="product-price-display">
                        <?php if ( $harga_diskon && (float)$harga_diskon > (float)$harga ) : ?>
                            <span class="price-current"><?php echo esc_html( $mata_uang . ' ' . number_format( $harga, 0, ',', '.' ) ); ?></span>
                            <span class="price-original"><?php echo esc_html( $mata_uang . ' ' . number_format( $harga_diskon, 0, ',', '.' ) ); ?></span>
                            <?php 
                            $diskon_persen = round( ( ( (float)$harga_diskon - (float)$harga ) / (float)$harga_diskon ) * 100 );
                            echo '<span class="price-discount-badge">-' . $diskon_persen . '%</span>';
                            ?>
                        <?php else : ?>
                            <span class="price-current"><?php echo esc_html( $mata_uang . ' ' . number_format( $harga, 0, ',', '.' ) ); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php elseif ( $show_price === 'yes' ) : ?>
                    <div class="product-price-display">
                        <span class="price-current"><?php esc_html_e( 'Hubungi Kami', 'tokoku' ); ?></span>
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
                            $price_val = $harga ? $mata_uang . ' ' . number_format( $harga, 0, ',', '.' ) : 'Hubungi Kami';
                        } else {
                            $price_val = 'Tanyakan Harga';
                        }
                        ?>
                        <button class="btn btn-primary btn-lg btn-block btn-whatsapp-order btn-contact-us"
                                data-product-id="<?php the_ID(); ?>"
                                data-product-name="<?php the_title(); ?>"
                                data-product-sku="<?php echo esc_attr( get_post_meta( get_the_ID(), '_produk_sku', true ) ); ?>"
                                data-product-url="<?php the_permalink(); ?>"
                                data-product-price="<?php echo esc_attr( $price_val ); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px; vertical-align: middle; display: inline-block;"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/></svg>
                            Pesan via WhatsApp
                        </button>
                        
                        <?php if ( $has_marketplace ) : ?>
                        <div class="marketplace-links">
                            <span class="marketplace-title">Atau Beli di Marketplace:</span>
                            <div class="marketplace-buttons">
                                <?php if ( $marketplace_shopee ) : ?>
                                <a href="<?php echo esc_url( $marketplace_shopee ); ?>" target="_blank" class="btn-marketplace mp-shopee">
                                    Shopee
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_tokopedia ) : ?>
                                <a href="<?php echo esc_url( $marketplace_tokopedia ); ?>" target="_blank" class="btn-marketplace mp-tokopedia">
                                    Tokopedia
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_lazada ) : ?>
                                <a href="<?php echo esc_url( $marketplace_lazada ); ?>" target="_blank" class="btn-marketplace mp-lazada">
                                    Lazada
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_tiktok ) : ?>
                                <a href="<?php echo esc_url( $marketplace_tiktok ); ?>" target="_blank" class="btn-marketplace mp-tiktok">
                                    TikTok
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_bukalapak ) : ?>
                                <a href="<?php echo esc_url( $marketplace_bukalapak ); ?>" target="_blank" class="btn-marketplace mp-bukalapak">
                                    Bukalapak
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_blibli ) : ?>
                                <a href="<?php echo esc_url( $marketplace_blibli ); ?>" target="_blank" class="btn-marketplace mp-blibli">
                                    Blibli
                                </a>
                                <?php endif; ?>

                                <?php if ( $marketplace_lainnya ) : ?>
                                <a href="<?php echo esc_url( $marketplace_lainnya ); ?>" target="_blank" class="btn-marketplace mp-lainnya">
                                    Lainnya
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-share">
                        <span class="share-label">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                            Bagikan ke:
                        </span>
                        <div class="share-icons">
                            <?php 
                            $current_url   = urlencode( get_permalink() ); 
                            $raw_url       = esc_url( get_permalink() );
                            $current_title = urlencode( get_the_title() ); 
                            ?>
                            <!-- WhatsApp -->
                            <a href="https://api.whatsapp.com/send?text=<?php echo $current_title . '%20' . $current_url; ?>" target="_blank" rel="noopener" class="share-icon wa" aria-label="Bagikan ke WhatsApp" title="WhatsApp">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/></svg>
                            </a>
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $current_url; ?>" target="_blank" rel="noopener" class="share-icon fb" aria-label="Bagikan ke Facebook" title="Facebook">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <!-- 𝕏 (Twitter) -->
                            <a href="https://twitter.com/intent/tweet?url=<?php echo $current_url; ?>&text=<?php echo $current_title; ?>" target="_blank" rel="noopener" class="share-icon tw" aria-label="Bagikan ke X" title="X (Twitter)">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <!-- Telegram -->
                            <a href="https://t.me/share/url?url=<?php echo $current_url; ?>&text=<?php echo $current_title; ?>" target="_blank" rel="noopener" class="share-icon tg" aria-label="Bagikan ke Telegram" title="Telegram">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                            </a>
                            <!-- Pinterest -->
                            <a href="https://pinterest.com/pin/create/button/?url=<?php echo $current_url; ?>&description=<?php echo $current_title; ?>" target="_blank" rel="noopener" class="share-icon pin" aria-label="Bagikan ke Pinterest" title="Pinterest">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.171-2.911 1.024 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.627 0 12-5.373 12-12 0-6.62-5.373-11.987-11.983-11.987z"/></svg>
                            </a>
                            <!-- Salin Tautan (Copy Link) -->
                            <button type="button" class="share-icon link-share copy-link-btn" data-url="<?php echo $raw_url; ?>" aria-label="Salin Tautan" title="Salin Tautan">
                                <svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
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

                        <?php
                        $tags = get_the_terms( get_the_ID(), 'tag_produk' );
                        if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) : ?>
                        <div class="product-tags-bottom" style="margin-top: 40px; padding-top: 20px; border-top: 1.5px dashed var(--border);">
                            <span style="display: block; font-weight: 700; color: var(--text2); margin-bottom: 15px; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Tag Produk:</span>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                <?php
                                foreach ( $tags as $tag ) {
                                    echo '<a href="' . esc_url( get_term_link( $tag ) ) . '" class="product-tag-badge">' . esc_html( $tag->name ) . '</a>';
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <?php
            if ( ! empty( $terms ) ) :
                $related = new WP_Query( array(
                    'post_type'      => 'produk',
                    'posts_per_page' => 4,
                    'post__not_in'   => array( get_the_ID() ),
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'kategori_produk',
                            'field'    => 'term_id',
                            'terms'    => $terms[0]->term_id,
                        ),
                    ),
                ) );

                if ( $related->have_posts() ) : ?>
                    <section class="related-products-section">
                        <div class="related-header">
                            <div class="related-header__left">
                                <span class="related-badge">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                    Rekomendasi Pilihan
                                </span>
                                <h2 class="related-title">Produk Terkait</h2>
                            </div>
                            <a href="<?php echo esc_url( get_term_link( $terms[0] ) ); ?>" class="related-view-all">
                                <span>Lihat Semua <?php echo esc_html( $terms[0]->name ); ?></span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                        <div class="product-grid related-grid">
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

.breadcrumb { font-size: 0.85rem; color: var(--text2); margin-bottom: 25px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.breadcrumb a { color: var(--text2); text-decoration: none; transition: var(--ease); }
.breadcrumb a:hover { color: var(--primary); }
.breadcrumb .current { color: var(--text); font-weight: 600; }

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
    box-shadow: var(--shadow-md);
}
.btn-contact-us:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); opacity: 0.9; color: #fff; }

/* Modern Share Bar */
.product-share { 
    display: flex; 
    align-items: center; 
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px; 
    padding: 16px 20px; 
    background: var(--bg2); 
    border-radius: 16px; 
    margin-bottom: 24px; 
    border: 1px solid var(--border); 
}
.share-label { 
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.88rem; 
    color: var(--text); 
    font-weight: 700; 
}
.share-label svg { color: var(--primary); }
.share-icons { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.share-icon { 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
    color: #fff !important; 
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    border: none !important;
    outline: none !important;
    cursor: pointer;
    text-decoration: none;
}
.share-icon:hover { 
    transform: translateY(-3px) scale(1.08); 
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.22); 
    color: #fff !important;
}
.share-icon:active { transform: scale(0.95); }
.share-icon.wa { background-color: #25d366; }
.share-icon.wa:hover { box-shadow: 0 6px 18px rgba(37, 211, 102, 0.45); }
.share-icon.fb { background-color: #1877f2; }
.share-icon.fb:hover { box-shadow: 0 6px 18px rgba(24, 119, 242, 0.45); }
.share-icon.tw { background-color: #0f1419; }
.share-icon.tw:hover { box-shadow: 0 6px 18px rgba(15, 20, 25, 0.45); }
.share-icon.tg { background-color: #229ed9; }
.share-icon.tg:hover { box-shadow: 0 6px 18px rgba(34, 158, 217, 0.45); }
.share-icon.pin { background-color: #e60023; }
.share-icon.pin:hover { box-shadow: 0 6px 18px rgba(230, 0, 35, 0.45); }
.share-icon.link-share { background-color: #475569; }
.share-icon.link-share:hover { background-color: var(--primary); box-shadow: 0 6px 18px rgba(var(--primary-rgb), 0.45); }

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
    border: 1px solid var(--border);
    box-shadow: var(--shadow-md);
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
.product-price-display .price-discount-badge { background: #fee2e2; color: #dc2626; font-weight: 700; font-size: 0.85rem; padding: 4px 8px; border-radius: 4px; }
:is(.theme-dark, html.theme-dark, body.theme-dark) .product-price-display .price-discount-badge { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

.product-variations { margin-bottom: 30px; }
.variations-label { display: block; font-weight: 700; color: var(--text2); margin-bottom: 10px; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
.variations-list { display: flex; flex-wrap: wrap; gap: 10px; }
.btn-variation { background: var(--bg2); border: 1.5px solid var(--border); padding: 8px 16px; border-radius: 8px; font-weight: 600; color: var(--text); cursor: pointer; transition: var(--ease); font-size: 0.95rem; }
.btn-variation:hover, .btn-variation.active { border-color: var(--primary); color: var(--primary); background: var(--bg); }

.stock-count { font-size: 0.85rem; color: var(--text2); font-weight: normal; margin-left: 4px; }

.product-note-box { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; border-radius: 8px; display: flex; gap: 12px; margin-bottom: 30px; color: #92400e; }
.product-note-box svg { flex-shrink: 0; color: #f59e0b; }
.product-note-box .note-content { font-size: 0.95rem; line-height: 1.5; }
:is(.theme-dark, html.theme-dark, body.theme-dark) .product-note-box { background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25); border-left: 4px solid #f59e0b; color: #fde68a; }

.product-tag-badge { background: var(--bg2); border: 1px solid var(--border); padding: 4px 10px; border-radius: 4px; font-size: 0.82rem; font-weight: 600; color: var(--text2); text-decoration: none; transition: var(--ease); }
.product-tag-badge:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

.btn-watch-video { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; margin-top: 15px; padding: 12px; background: var(--bg2); border: 1.5px solid var(--border); border-radius: 8px; font-weight: 700; color: var(--text); cursor: pointer; transition: var(--ease); text-decoration: none; }
.btn-watch-video:hover { border-color: #ef4444; color: #ef4444; background: rgba(239, 68, 68, 0.08); }

.marketplace-links { margin-bottom: 30px; padding-top: 20px; border-top: 1.5px dashed var(--border); }
.marketplace-title { display: block; font-size: 0.9rem; font-weight: 700; color: var(--text2); margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; }
.marketplace-buttons { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.btn-marketplace { 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    text-align: center; 
    width: 100%; 
    padding: 12px 15px; 
    border-radius: 10px; 
    font-weight: 800; 
    color: #fff; 
    text-decoration: none; 
    transition: var(--ease); 
    font-size: 0.85rem; 
    box-shadow: var(--shadow-sm);
}
.btn-marketplace:hover { transform: translateY(-2px); opacity: 0.9; color: #fff; }
.mp-shopee { background: #ee4d2d; }
.mp-tokopedia { background: #00aa5b; }
.mp-lazada { background: #0f146d; }
.mp-tiktok { background: #000000; }
.mp-bukalapak { background: #e31e52; }
.mp-blibli { background: #0095da; }
.mp-lainnya { background: #6c757d; }

@media (max-width: 768px) {
    .marketplace-buttons { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .btn-marketplace { padding: 14px 10px; font-size: 0.8rem; }
    .special-label { display: block; width: fit-content; margin-bottom: 12px; margin-right: 0; padding: 4px 10px; font-size: 0.65rem; line-height: 1.2; }
    .product-tag-badge { padding: 8px 14px; font-size: 0.8rem; border-radius: 6px; }
}

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
