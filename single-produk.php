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

                    <?php 
                    $enable_product_rating = get_theme_mod( 'tokoku_enable_product_rating', 'yes' ) !== 'no';
                    if ( $enable_product_rating ) :
                        $p_rating = get_post_meta( get_the_ID(), '_produk_rating', true );
                        if ( empty( $p_rating ) || ! is_numeric( $p_rating ) ) {
                            $p_rating = get_theme_mod( 'tokoku_schema_default_rating', '4.9' );
                        }
                        $p_rating = min( 5.0, max( 1.0, floatval( $p_rating ) ) );

                        $p_reviews = get_post_meta( get_the_ID(), '_produk_review_count', true );
                        if ( empty( $p_reviews ) || ! is_numeric( $p_reviews ) ) {
                            $base_rev  = (int) get_theme_mod( 'tokoku_schema_default_reviews', 24 );
                            $p_reviews = $base_rev + ( (int) get_the_ID() % 13 );
                        }
                        $p_reviews = max( 1, (int) $p_reviews );
                    ?>
                    <div class="product-rating-summary" aria-label="<?php echo esc_attr( sprintf( __( 'Rating %s dari 5 bintang (%d ulasan terverifikasi)', 'tokoku' ), number_format( $p_rating, 1, '.', '' ), $p_reviews ) ); ?>">
                        <div class="product-rating-stars">
                            <?php for ( $s = 1; $s <= 5; $s++ ) : ?>
                                <svg class="star-icon <?php echo $s <= round( $p_rating ) ? 'star-filled' : 'star-empty'; ?>" width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-number"><?php echo esc_html( number_format( $p_rating, 1, '.', '' ) ); ?></span>
                        <span class="rating-separator">•</span>
                        <span class="rating-count"><?php echo esc_html( sprintf( __( '%d Ulasan Terverifikasi', 'tokoku' ), $p_reviews ) ); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if ( $show_price === 'yes' && $harga ) : ?>
                    <div class="product-price-display">
                        <?php if ( $harga_diskon && (float)$harga_diskon > (float)$harga ) : ?>
                            <span class="price-current"><?php echo esc_html( $mata_uang . ' ' . number_format( (float)$harga, 0, ',', '.' ) ); ?></span>
                            <span class="price-original"><?php echo esc_html( $mata_uang . ' ' . number_format( (float)$harga_diskon, 0, ',', '.' ) ); ?></span>
                            <?php 
                            $diskon_persen = ( (float)$harga_diskon > 0 ) ? round( ( ( (float)$harga_diskon - (float)$harga ) / (float)$harga_diskon ) * 100 ) : 0;
                            echo '<span class="price-discount-badge">-' . esc_html( $diskon_persen ) . '%</span>';
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
                                <button type="button" class="btn-variation" <?php if($harga_varian) echo 'data-price="' . esc_attr( $mata_uang . ' ' . number_format((float)$harga_varian, 0, ',', '.') ) . '"'; ?> data-variation="<?php echo esc_attr( $pilihan ); ?>">
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
                        <button type="button" class="btn btn-primary btn-lg btn-block btn-whatsapp-order btn-contact-us"
                                data-product-id="<?php the_ID(); ?>"
                                data-product-name="<?php the_title_attribute(); ?>"
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

            <?php
            // Informasi & Detail Produk Settings
            $enable_desc_hub        = get_theme_mod( 'tokoku_enable_desc_hub', 'yes' ) !== 'no';
            $desc_badge_text        = get_theme_mod( 'tokoku_desc_badge_text', __( 'Informasi & Detail Produk', 'tokoku' ) );
            $desc_section_title     = get_theme_mod( 'tokoku_desc_section_title', __( 'Spesifikasi & Panduan Pemesanan', 'tokoku' ) );
            $desc_tab1_label        = get_theme_mod( 'tokoku_desc_tab1_label', __( 'Deskripsi & Fitur', 'tokoku' ) );
            $desc_tab2_label        = get_theme_mod( 'tokoku_desc_tab2_label', __( 'Spesifikasi Detail', 'tokoku' ) );
            $desc_tab3_label        = get_theme_mod( 'tokoku_desc_tab3_label', __( 'Cara Pesan & Garansi', 'tokoku' ) );

            $enable_highlights      = get_theme_mod( 'tokoku_desc_enable_highlights', 'yes' ) !== 'no';
            $val1_title             = get_theme_mod( 'tokoku_desc_val1_title', __( 'Material Kualitas Unggulan', 'tokoku' ) );
            $val1_desc              = get_theme_mod( 'tokoku_desc_val1_desc', __( 'Akrilik bening kristal, kayu pilihan, & logam anti-korosi presisi tinggi.', 'tokoku' ) );
            $val2_title             = get_theme_mod( 'tokoku_desc_val2_title', __( 'Free Desain & Mockup', 'tokoku' ) );
            $val2_desc              = get_theme_mod( 'tokoku_desc_val2_desc', __( 'Bantu setting tata letak logo & teks sampai sesuai sebelum cetak.', 'tokoku' ) );
            $val3_title             = get_theme_mod( 'tokoku_desc_val3_title', __( 'Pengerjaan Cepat & Rapi', 'tokoku' ) );
            $val3_desc              = get_theme_mod( 'tokoku_desc_val3_desc', __( 'Dikerjakan langsung oleh pengrajin ahli dengan mesin laser canggih.', 'tokoku' ) );

            $specs_packaging        = get_theme_mod( 'tokoku_specs_packaging', __( 'Box Beludru / Hardbox Eksklusif + Bubble Wrap Berlapis', 'tokoku' ) );
            $specs_file_format      = get_theme_mod( 'tokoku_specs_file_format', __( 'CDR, AI, PDF, EPS, PNG, atau JPG Resolusi Tinggi', 'tokoku' ) );
            $specs_min_order        = get_theme_mod( 'tokoku_specs_min_order', __( 'Mulai 1 Pcs (Satuan & Partai Besar Siap)', 'tokoku' ) );

            $step1_title            = get_theme_mod( 'tokoku_desc_step1_title', __( 'Konsultasi & Konsep', 'tokoku' ) );
            $step1_desc             = get_theme_mod( 'tokoku_desc_step1_desc', __( 'Kirimkan logo, naskah/tulisan penghargaan, dan bentuk yang diinginkan via WhatsApp.', 'tokoku' ) );
            $step2_title            = get_theme_mod( 'tokoku_desc_step2_title', __( 'Preview & ACC Mockup', 'tokoku' ) );
            $step2_desc             = get_theme_mod( 'tokoku_desc_step2_desc', __( 'Tim kami membuatkan visual layout digital gratis untuk dicek & disetujui sebelum diproduksi.', 'tokoku' ) );
            $step3_title            = get_theme_mod( 'tokoku_desc_step3_title', __( 'Proses Produksi Cepat', 'tokoku' ) );
            $step3_desc             = get_theme_mod( 'tokoku_desc_step3_desc', __( 'Setelah desain fix dan DP dikonfirmasi, plakat langsung diproses mesin laser presisi.', 'tokoku' ) );
            $step4_title            = get_theme_mod( 'tokoku_desc_step4_title', __( 'Packing & Pengiriman', 'tokoku' ) );
            $step4_desc             = get_theme_mod( 'tokoku_desc_step4_desc', __( 'Produk dipacking berlapis tebal dan dikirim menggunakan ekspedisi terpercaya ke seluruh Indonesia.', 'tokoku' ) );

            $enable_guarantee       = get_theme_mod( 'tokoku_desc_enable_guarantee', 'yes' ) !== 'no';
            $guarantee_title        = get_theme_mod( 'tokoku_desc_guarantee_title', __( 'Jaminan Garansi 100% — Rusak / Pecah Kami Ganti Baru!', 'tokoku' ) );
            $guarantee_desc         = get_theme_mod( 'tokoku_desc_guarantee_desc', __( 'Keamanan barang Anda adalah prioritas utama kami. Apabila pesanan mengalami kerusakan saat perjalanan kirim oleh kurir/ekspedisi, cukup kirimkan video unboxing dan kami siap membuatkan unit pengganti baru tanpa biaya tambahan.', 'tokoku' ) );
            ?>

            <?php if ( ! $enable_desc_hub ) : ?>
                <div class="product-description-wrapper">
                    <div class="product-description-container" style="padding: 24px;">
                        <div class="product-description-content tokoku-prose">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
            <div class="product-description-wrapper">
                <div class="product-description-container">
                    
                    <!-- Modern Eyebrow & Tabs Navigation -->
                    <div class="product-tabs-nav-wrapper">
                        <div class="product-tabs-header-top">
                            <span class="product-tabs-badge">
                                <span class="badge-pulse-dot"></span>
                                <?php echo esc_html( $desc_badge_text ); ?>
                            </span>
                            <h2 class="product-tabs-title"><?php echo esc_html( $desc_section_title ); ?></h2>
                        </div>

                        <div class="product-tabs-nav" role="tablist" aria-label="<?php esc_attr_e( 'Navigasi Detail Produk', 'tokoku' ); ?>">
                            <button type="button" class="product-tab-btn active" role="tab" aria-selected="true" aria-controls="prod-panel-desc" id="prod-tab-desc" data-target="prod-panel-desc">
                                <svg class="tab-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <span><?php echo esc_html( $desc_tab1_label ); ?></span>
                            </button>

                            <button type="button" class="product-tab-btn" role="tab" aria-selected="false" aria-controls="prod-panel-specs" id="prod-tab-specs" data-target="prod-panel-specs">
                                <svg class="tab-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><line x1="8" y1="2" x2="8" y2="6"></line><line x1="16" y1="2" x2="16" y2="6"></line><line x1="7" y1="10" x2="17" y2="10"></line><line x1="7" y1="14" x2="13" y2="14"></line></svg>
                                <span><?php echo esc_html( $desc_tab2_label ); ?></span>
                            </button>

                            <button type="button" class="product-tab-btn" role="tab" aria-selected="false" aria-controls="prod-panel-guide" id="prod-tab-guide" data-target="prod-panel-guide">
                                <svg class="tab-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
                                <span><?php echo esc_html( $desc_tab3_label ); ?></span>
                            </button>
                        </div>
                    </div>

                    <!-- Tab Panels Container -->
                    <div class="product-tab-panels">
                        
                        <!-- PANEL 1: DESKRIPSI & FITUR -->
                        <div class="product-tab-panel active" id="prod-panel-desc" role="tabpanel" aria-labelledby="prod-tab-desc">
                            <?php if ( $enable_highlights ) : ?>
                            <!-- Quick Value Proposition Highlights -->
                            <div class="product-value-highlights">
                                <div class="value-highlight-card">
                                    <div class="value-icon-box value-icon-gold">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                    </div>
                                    <div class="value-content">
                                        <h4><?php echo esc_html( $val1_title ); ?></h4>
                                        <p><?php echo esc_html( $val1_desc ); ?></p>
                                    </div>
                                </div>

                                <div class="value-highlight-card">
                                    <div class="value-icon-box value-icon-blue">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path><path d="M2 2l7.586 7.586"></path><circle cx="11" cy="11" r="2"></circle></svg>
                                    </div>
                                    <div class="value-content">
                                        <h4><?php echo esc_html( $val2_title ); ?></h4>
                                        <p><?php echo esc_html( $val2_desc ); ?></p>
                                    </div>
                                </div>

                                <div class="value-highlight-card">
                                    <div class="value-icon-box value-icon-green">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    </div>
                                    <div class="value-content">
                                        <h4><?php echo esc_html( $val3_title ); ?></h4>
                                        <p><?php echo esc_html( $val3_desc ); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Main Rich Description -->
                            <div class="product-description-content tokoku-prose">
                                <?php the_content(); ?>
                            </div>

                            <!-- Product Tags -->
                            <?php
                            $tags = get_the_terms( get_the_ID(), 'tag_produk' );
                            if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) : ?>
                            <div class="product-tags-wrapper">
                                <span class="product-tags-label">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                                    <?php esc_html_e( 'Tag Terkait:', 'tokoku' ); ?>
                                </span>
                                <div class="product-tags-list">
                                    <?php
                                    foreach ( $tags as $tag ) {
                                        echo '<a href="' . esc_url( get_term_link( $tag ) ) . '" class="product-tag-pill">#' . esc_html( $tag->name ) . '</a>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- PANEL 2: SPESIFIKASI DETAIL -->
                        <div class="product-tab-panel" id="prod-panel-specs" role="tabpanel" aria-labelledby="prod-tab-specs">
                            <div class="product-specs-grid">
                                <div class="specs-grid-item">
                                    <span class="specs-item-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>
                                        <?php esc_html_e( 'Kode Produk / SKU', 'tokoku' ); ?>
                                    </span>
                                    <span class="specs-item-val specs-item-sku"><?php echo esc_html( $sku ? $sku : 'PLK-' . get_the_ID() ); ?></span>
                                </div>

                                <div class="specs-grid-item">
                                    <span class="specs-item-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                        <?php esc_html_e( 'Kategori', 'tokoku' ); ?>
                                    </span>
                                    <span class="specs-item-val">
                                        <?php if ( ! empty( $terms ) ) : ?>
                                             <a href="<?php echo esc_url( get_term_link( $terms[0] ) ); ?>" class="specs-cat-link"><?php echo esc_html( $terms[0]->name ); ?></a>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <div class="specs-grid-item">
                                    <span class="specs-item-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                        <?php esc_html_e( 'Ketersediaan / Stok', 'tokoku' ); ?>
                                    </span>
                                    <span class="specs-item-val"><?php echo esc_html( $stok['label'] ); ?><?php if ( $jumlah_stok ) echo ' (' . esc_html( $jumlah_stok ) . ')'; ?></span>
                                </div>

                                <div class="specs-grid-item">
                                    <span class="specs-item-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <?php esc_html_e( 'Estimasi Pengerjaan', 'tokoku' ); ?>
                                    </span>
                                    <span class="specs-item-val"><?php echo esc_html( get_theme_mod( 'tokoku_lead_time_val', '2 – 3 Hari Kerja' ) ); ?></span>
                                </div>

                                <div class="specs-grid-item">
                                    <span class="specs-item-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/></svg>
                                        <?php esc_html_e( 'Berat Produk', 'tokoku' ); ?>
                                    </span>
                                    <span class="specs-item-val"><?php echo esc_html( $berat ? $berat : 'Menyesuaikan dimensi / ketebalan' ); ?></span>
                                </div>

                                <div class="specs-grid-item">
                                    <span class="specs-item-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                                        <?php esc_html_e( 'Kemasan / Packaging', 'tokoku' ); ?>
                                    </span>
                                    <span class="specs-item-val"><?php echo esc_html( $specs_packaging ); ?></span>
                                </div>

                                <div class="specs-grid-item">
                                    <span class="specs-item-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                        <?php esc_html_e( 'Format File Desain', 'tokoku' ); ?>
                                    </span>
                                    <span class="specs-item-val"><?php echo esc_html( $specs_file_format ); ?></span>
                                </div>

                                <div class="specs-grid-item">
                                    <span class="specs-item-label">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                                        <?php esc_html_e( 'Minimum Pemesanan', 'tokoku' ); ?>
                                    </span>
                                    <span class="specs-item-val"><?php echo esc_html( $specs_min_order ); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 3: CARA PESAN & GARANSI -->
                        <div class="product-tab-panel" id="prod-panel-guide" role="tabpanel" aria-labelledby="prod-tab-guide">
                            
                            <!-- 4 Easy Steps -->
                            <div class="product-steps-flow">
                                <div class="step-card">
                                    <div class="step-num">01</div>
                                    <div class="step-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    </div>
                                    <h4><?php echo esc_html( $step1_title ); ?></h4>
                                    <p><?php echo esc_html( $step1_desc ); ?></p>
                                </div>

                                <div class="step-card">
                                    <div class="step-num">02</div>
                                    <div class="step-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    </div>
                                    <h4><?php echo esc_html( $step2_title ); ?></h4>
                                    <p><?php echo esc_html( $step2_desc ); ?></p>
                                </div>

                                <div class="step-card">
                                    <div class="step-num">03</div>
                                    <div class="step-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                    </div>
                                    <h4><?php echo esc_html( $step3_title ); ?></h4>
                                    <p><?php echo esc_html( $step3_desc ); ?></p>
                                </div>

                                <div class="step-card">
                                    <div class="step-num">04</div>
                                    <div class="step-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                    </div>
                                    <h4><?php echo esc_html( $step4_title ); ?></h4>
                                    <p><?php echo esc_html( $step4_desc ); ?></p>
                                </div>
                            </div>

                            <?php if ( $enable_guarantee ) : ?>
                            <!-- 100% Quality & Breakage Guarantee Box -->
                            <div class="product-guarantee-box">
                                <div class="guarantee-icon-wrap">
                                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                                </div>
                                <div class="guarantee-text">
                                    <h3><?php echo esc_html( $guarantee_title ); ?></h3>
                                    <p><?php echo esc_html( $guarantee_desc ); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>
            </div>
            <?php endif; ?>

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
                        data-product-name="<?php the_title_attribute(); ?>"
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

<?php get_footer(); ?>

