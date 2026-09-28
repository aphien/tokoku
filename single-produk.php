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
                    <?php
                    // Kumpulkan semua gambar galeri (Foto Utama + Galeri Tambahan)
                    $all_gallery = array();
                    $featured_id = get_post_thumbnail_id();
                    if ( $featured_id ) {
                        $all_gallery[] = array(
                            'id'     => $featured_id,
                            'large'  => wp_get_attachment_image_url( $featured_id, 'tokoku-product-large' ) ?: wp_get_attachment_image_url( $featured_id, 'full' ),
                            'thumb'  => wp_get_attachment_image_url( $featured_id, 'thumbnail' ) ?: wp_get_attachment_image_url( $featured_id, 'medium' ),
                            'srcset' => wp_get_attachment_image_srcset( $featured_id, 'tokoku-product-large' ),
                            'alt'    => get_post_meta( $featured_id, '_wp_attachment_image_alt', true ) ?: get_the_title(),
                        );
                    }

                    $gallery_ids = get_post_meta( get_the_ID(), '_produk_gallery', true );
                    if ( $gallery_ids ) {
                        $raw_ids = is_array( $gallery_ids ) ? $gallery_ids : explode( ',', $gallery_ids );
                        foreach ( $raw_ids as $gid ) {
                            $gid = (int) trim( $gid );
                            if ( ! $gid || ( $featured_id && $gid === (int) $featured_id ) ) {
                                continue;
                            }
                            $large_url = wp_get_attachment_image_url( $gid, 'tokoku-product-large' ) ?: wp_get_attachment_image_url( $gid, 'full' );
                            if ( $large_url ) {
                                $all_gallery[] = array(
                                    'id'     => $gid,
                                    'large'  => $large_url,
                                    'thumb'  => wp_get_attachment_image_url( $gid, 'thumbnail' ) ?: wp_get_attachment_image_url( $gid, 'medium' ),
                                    'srcset' => wp_get_attachment_image_srcset( $gid, 'tokoku-product-large' ),
                                    'alt'    => get_post_meta( $gid, '_wp_attachment_image_alt', true ) ?: get_the_title(),
                                );
                            }
                        }
                    }

                    // Fallback jika belum ada foto
                    if ( empty( $all_gallery ) ) {
                        $placeholder_url = esc_url( TOKOKU_URI . '/assets/images/placeholder.svg' );
                        $all_gallery[] = array(
                            'id'     => 0,
                            'large'  => $placeholder_url,
                            'thumb'  => $placeholder_url,
                            'srcset' => '',
                            'alt'    => get_the_title(),
                        );
                    }
                    $total_slides = count( $all_gallery );
                    $slider_autoplay = get_theme_mod( 'tokoku_product_slider_autoplay', 'yes' ) !== 'no';
                    $slider_delay    = (int) get_theme_mod( 'tokoku_product_slider_delay', 4 );
                    if ( $slider_delay < 2 ) $slider_delay = 4;
                    ?>

                    <div class="main-image <?php echo $total_slides > 1 ? 'has-slider' : ''; ?>" 
                         id="main-product-image-wrap"
                         data-autoplay="<?php echo $slider_autoplay ? 'true' : 'false'; ?>"
                         data-autoplay-delay="<?php echo esc_attr( $slider_delay * 1000 ); ?>">
                        <div class="product-slider-track" id="product-slider-track">
                            <?php foreach ( $all_gallery as $i => $img ) : ?>
                                <div class="product-slide <?php echo $i === 0 ? 'is-active' : ''; ?>" data-slide-index="<?php echo $i; ?>">
                                    <img src="<?php echo esc_url( $img['large'] ); ?>"
                                         <?php if ( ! empty( $img['srcset'] ) ) : ?>srcset="<?php echo esc_attr( $img['srcset'] ); ?>"<?php endif; ?>
                                         alt="<?php echo esc_attr( $img['alt'] ); ?>"
                                         width="800" height="800"
                                         loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>"
                                         <?php if ( $i === 0 ) : ?>fetchpriority="high" decoding="sync" id="main-product-img"<?php else : ?>decoding="async"<?php endif; ?>
                                         class="product-slide-img">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ( $total_slides > 1 ) : ?>
                            <!-- Navigasi Panah Slider (Desktop & Tablet) -->
                            <button type="button" class="product-slider-nav slider-nav-prev" id="product-slider-prev" aria-label="Gambar Sebelumnya">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                            </button>
                            <button type="button" class="product-slider-nav slider-nav-next" id="product-slider-next" aria-label="Gambar Berikutnya">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                            </button>

                            <!-- Badge Indikator Slide (e.g. 1 / 4) -->
                            <div class="product-slider-counter" id="product-slider-counter" aria-hidden="true">
                                <span class="slider-counter-current">1</span> / <span class="slider-counter-total"><?php echo $total_slides; ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Tombol Zoom (hanya mobile) -->
                        <button class="mobile-zoom-btn" id="open-zoom-btn" aria-label="Perbesar Gambar" title="Perbesar Gambar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                <line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
                            </svg>
                        </button>
                    </div>

                    <?php if ( $total_slides > 1 ) : ?>
                        <!-- Thumbnail Bar Galeri Produk -->
                        <div class="gallery-thumbs" id="product-gallery-thumbs" role="tablist" aria-label="Galeri Gambar Produk">
                            <?php foreach ( $all_gallery as $i => $img ) : ?>
                                <button type="button" 
                                        class="thumb <?php echo $i === 0 ? 'is-active' : ''; ?>" 
                                        data-slide-index="<?php echo $i; ?>" 
                                        role="tab" 
                                        aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                                        aria-label="Lihat gambar <?php echo $i + 1; ?>">
                                    <img src="<?php echo esc_url( $img['thumb'] ); ?>" 
                                         alt="<?php echo esc_attr( $img['alt'] ); ?>" 
                                         width="150" height="150" 
                                         loading="lazy" decoding="async">
                                </button>
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

                    <!-- Trust Badges (Desktop: Di Bawah Foto Produk, Teks & Ikon Rata Kiri) -->
                    <?php if ( get_theme_mod( 'tokoku_enable_trust_badges', 'yes' ) !== 'no' ) : 
                        $badge_defaults = array(
                            1 => array( 'title' => 'Gratis Preview Desain',     'desc' => 'Konsultasi & revisi sebelum cetak' ),
                            2 => array( 'title' => 'Garansi Pengiriman Aman',   'desc' => 'Ganti baru jika barang rusak/pecah' ),
                            3 => array( 'title' => 'Pengerjaan Presisi & Cepat','desc' => 'Tepat waktu untuk deadline acara' ),
                            4 => array( 'title' => 'Tangan Pertama Pengrajin',  'desc' => 'Kualitas terjamin, harga terbaik' ),
                        );
                    ?>
                    <div class="product-trust-badges">
                        <?php for ( $bi = 1; $bi <= 4; $bi++ ) : 
                            $btitle = get_theme_mod( "tokoku_trust_badge_title_{$bi}", $badge_defaults[$bi]['title'] );
                            $bdesc  = get_theme_mod( "tokoku_trust_badge_desc_{$bi}", $badge_defaults[$bi]['desc'] );
                            if ( empty( $btitle ) ) continue;
                        ?>
                        <div class="trust-badge-item">
                            <div class="trust-icon">
                                <?php echo tokoku_get_trust_badge_icon_html( $bi, 20 ); ?>
                            </div>
                            <div class="trust-content">
                                <strong><?php echo esc_html( $btitle ); ?></strong>
                                <span><?php echo esc_html( $bdesc ); ?></span>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
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

                    <!-- Lead Time & Fast Production Badge (Desktop: Di Atas Judul Produk) -->
                    <?php if ( get_theme_mod( 'tokoku_enable_lead_time', 'yes' ) !== 'no' ) : 
                        $lt_label = get_theme_mod( 'tokoku_lead_time_label', 'Estimasi Pengerjaan:' );
                        $lt_val   = get_theme_mod( 'tokoku_lead_time_val', '2 – 3 Hari Kerja' );
                        $lt_sub   = get_theme_mod( 'tokoku_lead_time_sub', '(Tergantung Qty & Desain)' );
                        $lt_chip  = get_theme_mod( 'tokoku_lead_time_chip', 'Siap Kirim Cepat' );
                    ?>
                    <div class="product-lead-time-bar">
                        <div class="lead-time-icon-wrap">
                            <?php echo tokoku_get_lead_time_icon_html( 18 ); ?>
                        </div>
                        <div class="lead-time-info">
                            <?php if ( ! empty( $lt_label ) ) : ?><span class="lead-time-label"><?php echo esc_html( $lt_label ); ?></span><?php endif; ?>
                            <span class="lead-time-val">
                                <?php if ( ! empty( $lt_val ) ) : ?><strong><?php echo esc_html( $lt_val ); ?></strong><?php endif; ?>
                                <?php if ( ! empty( $lt_sub ) ) : ?> <?php echo esc_html( $lt_sub ); ?><?php endif; ?>
                            </span>
                        </div>
                        <?php if ( ! empty( $lt_chip ) ) : ?>
                            <span class="lead-time-chip"><?php echo esc_html( $lt_chip ); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

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
                    $mata_uang      = get_theme_mod( 'tokoku_currency', 'Rp' );
                    $show_price     = get_theme_mod( 'tokoku_show_price', 'yes' );
                    ?>

                    <h1 class="product-title">
                        <?php the_title(); ?>
                    </h1>

                    <?php if ( $show_price === 'yes' && $harga ) : ?>
                    <div class="product-price-display">
                        <?php if ( $harga_diskon && (float)$harga_diskon > (float)$harga ) : ?>
                            <span class="price-current"><?php echo esc_html( $mata_uang . ' ' . number_format( (float)$harga, 0, ',', '.' ) ); ?></span>
                            <span class="price-original"><?php echo esc_html( $mata_uang . ' ' . number_format( (float)$harga_diskon, 0, ',', '.' ) ); ?></span>
                            <?php 
                            $diskon_persen = round( ( ( (float)$harga_diskon - (float)$harga ) / (float)$harga_diskon ) * 100 );
                            echo '<span class="price-discount-badge">-' . $diskon_persen . '%</span>';
                            ?>
                        <?php else : ?>
                            <span class="price-current"><?php echo esc_html( $mata_uang . ' ' . number_format( (float)$harga, 0, ',', '.' ) ); ?></span>
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
                                $harga_varian = isset( $harga_arr[$index] ) && is_numeric($harga_arr[$index]) ? (float)$harga_arr[$index] : '';
                            ?>
                                <button class="btn-variation" <?php if($harga_varian) echo 'data-price="' . esc_attr( $mata_uang . ' ' . number_format((float)$harga_varian, 0, ',', '.') ) . '"'; ?>>
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
                            <div class="spec-value">
                                <?php
                                $stok_class = $stok['class'];
                                if ( $stok_class === 'stok-tersedia' ) :
                                ?>
                                    <span class="stok-badge stok-badge--tersedia">
                                        <span class="stok-badge__dot"></span>
                                        <?php echo esc_html( $stok['label'] ); ?>
                                        <?php if ( $jumlah_stok ) echo '<span class="stok-badge__count">(' . esc_html( $jumlah_stok ) . ')</span>'; ?>
                                    </span>
                                <?php elseif ( $stok_class === 'stok-preorder' ) : ?>
                                    <span class="stok-badge stok-badge--preorder">
                                        <span class="stok-badge__dot"></span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <?php echo esc_html( $stok['label'] ); ?>
                                    </span>
                                <?php elseif ( $stok_class === 'stok-habis' ) : ?>
                                    <span class="stok-badge stok-badge--habis">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        <?php echo esc_html( $stok['label'] ); ?>
                                    </span>
                                <?php endif; ?>
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

                    <?php 
                    $enable_stock_notice = get_theme_mod( 'tokoku_enable_stock_notice', 'yes' ) !== 'no';
                    if ( $enable_stock_notice ) :
                        $notice_tersedia_title = get_theme_mod( 'tokoku_notice_tersedia_title', 'STOK TERSEDIA' );
                        $notice_tersedia_desc  = get_theme_mod( 'tokoku_notice_tersedia_desc', 'Produk ini tersedia dan siap untuk dipesan sekarang.' );
                        $notice_habis_title    = get_theme_mod( 'tokoku_notice_habis_title', 'STOK HABIS' );
                        $notice_habis_desc     = get_theme_mod( 'tokoku_notice_habis_desc', 'Produk ini sedang tidak tersedia. Hubungi kami untuk informasi ketersediaan berikutnya.' );
                        $notice_preorder_title = get_theme_mod( 'tokoku_notice_preorder_title', 'PRE ORDER' );
                        $notice_preorder_desc  = get_theme_mod( 'tokoku_notice_preorder_desc', 'Hubungi kami untuk informasi lebih lanjut mengenai pemesanan produk ini.' );
                    ?>
                        <?php if ( $stok['class'] == 'stok-tersedia' ) : ?>
                        <div class="stok-notice stok-notice--tersedia">
                            <div class="notice-title">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <?php echo esc_html( $notice_tersedia_title ); ?>
                            </div>
                            <p><?php echo nl2br( esc_html( $notice_tersedia_desc ) ); ?></p>
                        </div>
                        <?php elseif ( $stok['class'] == 'stok-habis' ) : ?>
                        <div class="stok-notice stok-notice--habis">
                            <div class="notice-title">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <?php echo esc_html( $notice_habis_title ); ?>
                            </div>
                            <p><?php echo nl2br( esc_html( $notice_habis_desc ) ); ?></p>
                        </div>
                        <?php elseif ( $stok['class'] == 'stok-preorder' ) : ?>
                        <div class="stok-notice stok-notice--preorder">
                            <div class="notice-title">
                                <span class="dashicons dashicons-clock" style="font-size: 22px; width: 22px; height: 22px;"></span>
                                <?php echo esc_html( $notice_preorder_title ); ?>
                            </div>
                            <p><?php echo nl2br( esc_html( $notice_preorder_desc ) ); ?></p>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="product-actions">
                        <?php
                        if ( $show_price === 'yes' ) {
                            $price_val = $harga ? $mata_uang . ' ' . number_format( (float)$harga, 0, ',', '.' ) : 'Hubungi Kami';
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

                    </div>

                        
                    <?php if ( $has_marketplace ) : ?>
                    <div class="marketplace-links">
                        <span class="marketplace-title">Atau Beli di Marketplace:</span>
                        <div class="marketplace-buttons">
                            <?php if ( $marketplace_shopee ) : ?>
                            <a href="<?php echo esc_url( $marketplace_shopee ); ?>" target="_blank" rel="noopener noreferrer" class="btn-marketplace mp-shopee">
                                Shopee
                            </a>
                            <?php endif; ?>

                            <?php if ( $marketplace_tokopedia ) : ?>
                            <a href="<?php echo esc_url( $marketplace_tokopedia ); ?>" target="_blank" rel="noopener noreferrer" class="btn-marketplace mp-tokopedia">
                                Tokopedia
                            </a>
                            <?php endif; ?>

                            <?php if ( $marketplace_lazada ) : ?>
                            <a href="<?php echo esc_url( $marketplace_lazada ); ?>" target="_blank" rel="noopener noreferrer" class="btn-marketplace mp-lazada">
                                Lazada
                            </a>
                            <?php endif; ?>

                            <?php if ( $marketplace_tiktok ) : ?>
                            <a href="<?php echo esc_url( $marketplace_tiktok ); ?>" target="_blank" rel="noopener noreferrer" class="btn-marketplace mp-tiktok">
                                TikTok Shop
                            </a>
                            <?php endif; ?>

                            <?php if ( $marketplace_bukalapak ) : ?>
                            <a href="<?php echo esc_url( $marketplace_bukalapak ); ?>" target="_blank" rel="noopener noreferrer" class="btn-marketplace mp-bukalapak">
                                Bukalapak
                            </a>
                            <?php endif; ?>

                            <?php if ( $marketplace_blibli ) : ?>
                            <a href="<?php echo esc_url( $marketplace_blibli ); ?>" target="_blank" rel="noopener noreferrer" class="btn-marketplace mp-blibli">
                                Blibli
                            </a>
                            <?php endif; ?>

                            <?php if ( $marketplace_lainnya ) : ?>
                            <a href="<?php echo esc_url( $marketplace_lainnya ); ?>" target="_blank" rel="noopener noreferrer" class="btn-marketplace mp-lainnya">
                                Lainnya
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

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
                    'no_found_rows'  => true,
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

        <!-- Mobile Sticky Order Bar (Appears when scrolling down) -->
        <div id="product-sticky-bar" class="single-product-sticky-bar" aria-hidden="true">
            <div class="sticky-bar-centered-wrap">
                <button type="button" class="btn-sticky-order-elegant btn-whatsapp-order"
                        data-product-id="<?php the_ID(); ?>"
                        data-product-name="<?php the_title(); ?>"
                        data-product-sku="<?php echo esc_attr( get_post_meta( get_the_ID(), '_produk_sku', true ) ); ?>"
                        data-product-url="<?php the_permalink(); ?>"
                        data-product-price="<?php echo esc_attr( $price_val ); ?>"
                        aria-label="Pesan via WhatsApp">
                    <svg class="sticky-wa-icon" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/>
                    </svg>
                    <span class="sticky-order-label">Pesan Sekarang via WhatsApp</span>
                    <?php if ( $price_val && $price_val !== 'Tanyakan Harga' && $price_val !== 'Hubungi Kami' ) : ?>
                        <span class="sticky-order-price-pill"><?php echo esc_html( $price_val ); ?></span>
                    <?php endif; ?>
                </button>
            </div>
        </div>

    </div>
</main>

<!-- Desktop Lightbox Modal -->
<div class="tokoku-lightbox" id="tokoku-lightbox" style="display: none;" role="dialog" aria-modal="true" aria-label="Preview Gambar Produk">
    <button type="button" class="tokoku-lightbox-close" aria-label="Tutup Preview">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>
    <div class="tokoku-lightbox-content">
        <img id="tokoku-lightbox-img" src="" alt="Preview Produk">
    </div>
</div>

<?php get_footer(); ?>

<!-- Mobile Zoom Overlay -->
<div class="mobile-zoom-overlay" id="mobile-zoom-overlay" role="dialog" aria-modal="true" aria-label="Perbesar Gambar">
    <div class="mobile-zoom-overlay__inner" id="zoom-inner">
        <img class="mobile-zoom-overlay__img" id="zoom-overlay-img" src="" alt="Zoom Produk" draggable="false">
        <button class="mobile-zoom-overlay__close" id="close-zoom-btn" aria-label="Tutup" type="button">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <span class="mobile-zoom-overlay__hint">Cubit untuk zoom &bull; Geser untuk pindah</span>
    </div>
</div>

<script>
(function () {
    'use strict';
    if (window.innerWidth > 768) return;

    var openBtn  = document.getElementById('open-zoom-btn');
    var closeBtn = document.getElementById('close-zoom-btn');
    var overlay  = document.getElementById('mobile-zoom-overlay');
    var zoomImg  = document.getElementById('zoom-overlay-img');
    var srcImg   = document.getElementById('main-product-img');

    if (!openBtn || !overlay || !zoomImg || !srcImg) return;

    /* ── State ── */
    var scale      = 1, minScale = 1, maxScale = 5;
    var tx = 0, ty = 0;          // translate (dalam koordinat gambar)
    var lastTapTime = 0;
    var rafPending  = false;
    var needsApply  = false;

    /* ── Pinch state ── */
    var pinching  = false;
    var initDist  = 0, initScale = 1;
    var initTx    = 0, initTy    = 0;
    var pinchMidX = 0, pinchMidY = 0;  // midpoint di layar saat pinch mulai

    /* ── Pan state ── */
    var panning    = false;
    var panStartX  = 0, panStartY  = 0;
    var panInitTx  = 0, panInitTy  = 0;

    /* ── Swipe-down-to-close ── */
    var swipeStartY = 0, swipeDY = 0;

    function dist(a, b) {
        var dx = a.clientX - b.clientX, dy = a.clientY - b.clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }

    function clamp() {
        if (scale <= 1) { tx = 0; ty = 0; return; }
        var imgW = zoomImg.naturalWidth  || zoomImg.offsetWidth;
        var imgH = zoomImg.naturalHeight || zoomImg.offsetHeight;
        var visW = zoomImg.offsetWidth, visH = zoomImg.offsetHeight;
        var maxTx = Math.max(0, (visW * scale - visW) / (2 * scale));
        var maxTy = Math.max(0, (visH * scale - visH) / (2 * scale));
        tx = Math.max(-maxTx, Math.min(maxTx, tx));
        ty = Math.max(-maxTy, Math.min(maxTy, ty));
    }

    function scheduleApply() {
        needsApply = true;
        if (rafPending) return;
        rafPending = true;
        requestAnimationFrame(function () {
            rafPending = false;
            if (!needsApply) return;
            needsApply = false;
            zoomImg.style.transform = 'scale(' + scale + ') translate(' + tx + 'px,' + ty + 'px)';
        });
    }

    function applyNow(animated) {
        zoomImg.style.transition = animated ? 'transform 0.28s cubic-bezier(0.25,0.46,0.45,0.94)' : 'none';
        zoomImg.style.transform  = 'scale(' + scale + ') translate(' + tx + 'px,' + ty + 'px)';
    }

    function reset(animated) {
        scale = 1; tx = 0; ty = 0;
        applyNow(animated);
    }

    /* ── Open / Close ── */
    function openZoom() {
        // Gunakan gambar dari slide yang sedang aktif
        var activeSlide = document.querySelector('.product-slide.is-active img') || srcImg;
        var best = activeSlide.src;
        if (activeSlide.srcset) {
            var parts = activeSlide.srcset.split(',').map(function(s) { return s.trim().split(/\s+/); });
            var sorted = parts.sort(function(a,b) { return (parseInt(b[1]) || 0) - (parseInt(a[1]) || 0); });
            if (sorted[0] && sorted[0][0]) best = sorted[0][0];
        }
        zoomImg.src = best;
        reset(false);
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeZoom() {
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
        overlay.style.transform = '';
        overlay.style.opacity   = '';
    }

    /* ── Event bindings ── */
    openBtn.addEventListener('click', function (e) { e.preventDefault(); openZoom(); });

    // Tap pada gambar utama di mobile untuk buka pinch zoom (kecuali saat swipe)
    var trackWrap = document.getElementById('main-product-image-wrap') || srcImg;
    trackWrap.addEventListener('click', function (e) {
        if (e.target.closest('.product-slider-nav') || e.target.closest('.mobile-zoom-btn')) return;
        if (trackWrap.getAttribute('data-swiped') === 'true') return;
        if (window.innerWidth <= 768) {
            e.preventDefault();
            openZoom();
        }
    });

    // Close button — area klik lebih besar via padding trick
    closeBtn.addEventListener('click', function(e) { e.stopPropagation(); closeZoom(); });
    closeBtn.addEventListener('touchend', function(e) { e.preventDefault(); e.stopPropagation(); closeZoom(); }, { passive: false });

    overlay.addEventListener('click', function(e) { if (e.target === overlay) closeZoom(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeZoom(); });

    /* ── Touch on overlay image ── */
    zoomImg.addEventListener('touchstart', function (e) {
        if (e.touches.length === 2) {
            e.preventDefault();
            pinching   = true; panning = false;
            initDist   = dist(e.touches[0], e.touches[1]);
            initScale  = scale;
            initTx     = tx; initTy = ty;
            pinchMidX  = (e.touches[0].clientX + e.touches[1].clientX) / 2;
            pinchMidY  = (e.touches[0].clientY + e.touches[1].clientY) / 2;
            swipeDY    = 0;
        } else if (e.touches.length === 1) {
            var now = Date.now();
            if (now - lastTapTime < 280) {
                e.preventDefault();
                if (scale > 1) { reset(true); } else { scale = 2.5; tx = 0; ty = 0; applyNow(true); }
                lastTapTime = 0; return;
            }
            lastTapTime = now;
            panning = true; pinching = false;
            panStartX = e.touches[0].clientX;
            panStartY = e.touches[0].clientY;
            panInitTx = tx; panInitTy = ty;
            swipeStartY = e.touches[0].clientY; swipeDY = 0;
        }
    }, { passive: false });

    zoomImg.addEventListener('touchmove', function (e) {
        e.preventDefault();
        if (pinching && e.touches.length === 2) {
            var newDist = dist(e.touches[0], e.touches[1]);
            var newScale = Math.max(minScale, Math.min(maxScale, initScale * (newDist / initDist)));
            // Pertahankan pusat pinch sebagai titik tetap
            var ratio = newScale / initScale;
            tx = pinchMidX / newScale - pinchMidX / initScale + initTx * (initScale / newScale) * ratio;
            tx = initTx + (pinchMidX / window.innerWidth - 0.5) * (initScale - newScale) * zoomImg.offsetWidth / newScale;
            ty = initTy + (pinchMidY / window.innerHeight - 0.5) * (initScale - newScale) * zoomImg.offsetHeight / newScale;
            scale = newScale;
            clamp();
            scheduleApply();
        } else if (panning && e.touches.length === 1) {
            var dx = e.touches[0].clientX - panStartX;
            var dy = e.touches[0].clientY - panStartY;
            swipeDY = dy;
            if (scale <= 1) {
                // Swipe down to close
                if (dy > 0) {
                    overlay.style.transition = 'none';
                    overlay.style.transform  = 'translateY(' + (dy * 0.4) + 'px)';
                    overlay.style.opacity    = Math.max(0.4, 1 - dy / 300);
                }
            } else {
                tx = panInitTx + dx / scale;
                ty = panInitTy + dy / scale;
                clamp();
                scheduleApply();
            }
        }
    }, { passive: false });

    zoomImg.addEventListener('touchend', function (e) {
        if (scale <= 1 && swipeDY > 90) {
            // Swipe down confirmed: close
            overlay.style.transition = 'transform 0.25s ease, opacity 0.25s ease';
            overlay.style.transform  = 'translateY(100%)';
            overlay.style.opacity    = '0';
            setTimeout(closeZoom, 260);
        } else {
            overlay.style.transition = 'transform 0.2s ease, opacity 0.2s ease';
            overlay.style.transform  = '';
            overlay.style.opacity    = '';
        }
        pinching = false; panning = false; swipeDY = 0;
        if (scale < minScale) { scale = minScale; tx = 0; ty = 0; applyNow(true); }
        clamp(); applyNow(false);
    }, { passive: true });
})();
</script>

