<?php

if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The front page template file
 *
 * @package TokoKu
 */

get_header(); ?>

<main id="main-content" class="site-main">
    
    <!-- ====================================================
         HERO BANNER SLIDER
         ==================================================== -->
    <?php
    $slides_data = array();
    for ( $i = 1; $i <= 10; $i++ ) {
        $img  = get_theme_mod( "tokoku_slide_image_{$i}" );
        $link = get_theme_mod( "tokoku_slide_link_{$i}" );
        $alt  = get_theme_mod( "tokoku_slide_alt_{$i}" );
        
        if ( ! empty( $img ) ) {
            if ( empty( $alt ) ) {
                $alt = sprintf( __( 'Banner Promosi %s - Slide %d', 'tokoku' ), get_bloginfo( 'name' ), $i );
            }
            $slides_data[] = array(
                'index' => $i,
                'img'   => $img,
                'link'  => $link,
                'alt'   => $alt,
            );
        }
    }
    $slide_count = count( $slides_data );

    // Deteksi rasio aspek banner secara otomatis dengan transient cache (zero layout shift, zero redundant disk I/O)
    $banner_ratio = '2.735 / 1';
    if ( ! empty( $slides_data[0]['img'] ) ) {
        $first_img_url = $slides_data[0]['img'];
        $ratio_cache_key = 'tokoku_banner_ratio_' . md5( $first_img_url );
        $cached_ratio    = get_transient( $ratio_cache_key );
        if ( false !== $cached_ratio ) {
            $banner_ratio = $cached_ratio;
        } else {
            $upload_dir = wp_upload_dir();
            if ( strpos( $first_img_url, $upload_dir['baseurl'] ) !== false ) {
                $local_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $first_img_url );
                if ( file_exists( $local_path ) ) {
                    $img_size = @getimagesize( $local_path );
                    if ( ! empty( $img_size[0] ) && ! empty( $img_size[1] ) ) {
                        $banner_ratio = $img_size[0] . ' / ' . $img_size[1];
                    }
                }
            }
            set_transient( $ratio_cache_key, $banner_ratio, DAY_IN_SECONDS );
        }
    }
    ?>
    <section class="hero-slider-section" aria-label="<?php esc_attr_e( 'Banner Promosi', 'tokoku' ); ?>">
        <div class="container hero-slider-container">
            <div class="slider-container" id="home-slider" style="--slider-ratio: <?php echo esc_attr( $banner_ratio ); ?>;" role="region" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Galeri Banner Promosi', 'tokoku' ); ?>" data-slides="<?php echo esc_attr( $slide_count ); ?>">
                <div class="slider-wrapper">
                    <?php
                    if ( $slide_count > 0 ) {
                        foreach ( $slides_data as $idx => $slide ) {
                            $is_first = ( $idx === 0 );
                            $loading_attr = $is_first ? 'fetchpriority="high" loading="eager" decoding="async"' : 'loading="lazy" decoding="async"';
                            
                            echo '<div class="slide" role="group" aria-roledescription="slide" aria-label="' . esc_attr( sprintf( __( 'Slide %d dari %d', 'tokoku' ), $idx + 1, $slide_count ) ) . '">';
                            if ( ! empty( $slide['link'] ) ) {
                                echo '<a href="' . esc_url( $slide['link'] ) . '" class="slide-link" draggable="false">';
                            }
                            echo '<img src="' . esc_url( $slide['img'] ) . '" alt="' . esc_attr( $slide['alt'] ) . '" width="1200" height="438" sizes="(max-width: 768px) 100vw, 1200px" draggable="false" ' . $loading_attr . '>';
                            if ( ! empty( $slide['link'] ) ) {
                                echo '</a>';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="slide slide--placeholder" role="group" aria-roledescription="slide">
                            <div class="slide-placeholder-inner">
                                <span class="slide-placeholder-pill">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    ' . esc_html( get_bloginfo( 'name' ) ) . '
                                </span>
                                <h2>' . sprintf( esc_html__( 'Solusi Plakat & Cinderamata Berkualitas di %s', 'tokoku' ), esc_html( get_bloginfo( 'name' ) ) ) . '</h2>
                                <p>' . esc_html__( 'Pusat pembuatan plakat akrilik, kristal, kayu, resin, & medali custom dengan pengerjaan presisi dan pengiriman aman.', 'tokoku' ) . '</p>
                                <div class="slide-placeholder-actions">
                                    <a href="#categories" class="btn-slide-cta">
                                        <span>' . esc_html__( 'Jelajahi Kategori', 'tokoku' ) . '</span>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                    </a>
                                    ' . ( current_user_can( 'edit_theme_options' ) ? '<a href="' . esc_url( admin_url( 'admin.php?page=tokoku-settings#tab-slider' ) ) . '" class="btn-slide-admin">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                        <span>' . esc_html__( 'Atur Banner Slider', 'tokoku' ) . '</span>
                                    </a>' : '' ) . '
                                </div>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
                
                <?php if ( $slide_count > 1 ) : ?>
                    <button type="button" class="slider-btn slider-prev" aria-label="<?php esc_attr_e( 'Slide Sebelumnya', 'tokoku' ); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                    </button>
                    <button type="button" class="slider-btn slider-next" aria-label="<?php esc_attr_e( 'Slide Berikutnya', 'tokoku' ); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                    <div class="slider-dots" role="tablist" aria-label="<?php esc_attr_e( 'Navigasi Banner', 'tokoku' ); ?>"></div>
                    <div class="slider-progress" aria-hidden="true"><div class="slider-progress-bar"></div></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ====================================================
         CATEGORY GRID
         ==================================================== -->
    <section id="categories" class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Kategori Produk</h2>
            </div>
            <div class="category-grid">
                <?php
                $categories = get_terms( array(
                    'taxonomy'   => 'kategori_produk',
                    'hide_empty' => false,
                    'number'     => 8,
                ) );

                if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
                    foreach ( $categories as $cat ) :
                ?>
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="category-item">
                        <div class="category-icon">
                            <?php echo tokoku_get_category_icon_html( $cat->term_id, 58 ); ?>
                        </div>
                        <span class="category-name"><?php echo esc_html( $cat->name ); ?></span>
                    </a>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </section>

    <!-- ====================================================
         FEATURED PRODUCTS
         ==================================================== -->
    <section class="products-section">
        <div class="container">
            <div class="section-header product-section-header">
                <h2 class="section-title">Produk Terbaru</h2>
                
                <!-- Mobile & Tablet Category Filter -->
                <div class="product-category-filter">
                    <div class="category-scroll-wrapper">
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>" class="cat-filter-item active"><?php _e( 'Semua', 'tokoku' ); ?></a>
                        <?php
                        $filter_cats = get_terms( array(
                            'taxonomy'   => 'kategori_produk',
                            'hide_empty' => true,
                        ) );
                        if ( ! empty( $filter_cats ) && ! is_wp_error( $filter_cats ) ) :
                            foreach ( $filter_cats as $fcat ) :
                        ?>
                            <a href="<?php echo esc_url( get_term_link( $fcat ) ); ?>" class="cat-filter-item"><?php echo esc_html( $fcat->name ); ?></a>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>

            <div class="product-grid">
                <?php
                $latest_products = new WP_Query( array(
                    'post_type'      => 'produk',
                    'posts_per_page' => 20,
                    'no_found_rows'  => true,
                ) );

                if ( $latest_products->have_posts() ) :
                    while ( $latest_products->have_posts() ) : $latest_products->the_post();
                        get_template_part( 'template-parts/product-card' );
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<div class="empty-products">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto 15px;" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        <p>' . sprintf( wp_kses_post( __( 'Belum ada produk. <a href="%s">Tambah produk</a>', 'tokoku' ) ), esc_url( admin_url('post-new.php?post_type=produk') ) ) . '</p>
                    </div>';
                endif;
                ?>
            </div>

            <div class="section-footer">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>" class="btn-view-all">
                    <span>Lihat Semua Produk</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ====================================================
         CLIENT LOGOS (MARQUEE)
         ==================================================== -->
    <section class="logos-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php _e( 'Partner & Klien Kami', 'tokoku' ); ?></h2>
            </div>
            
            <div class="logo-carousel-wrapper">
                <div class="logo-track">
                    <?php
                    $site_name    = esc_attr( get_bloginfo( 'name' ) );
                    $client_logos = array();
                    for ( $i = 1; $i <= 50; $i++ ) {
                        $logo = get_theme_mod( "tokoku_client_logo_{$i}" );
                        if ( $logo ) {
                            $client_logos[] = array(
                                'url' => $logo,
                                'alt' => sprintf( esc_attr__( 'Klien & Mitra %s - Logo %d', 'tokoku' ), $site_name, $i ),
                            );
                        }
                    }
                    
                    if ( ! empty( $client_logos ) ) {
                        // Render original list
                        foreach ( $client_logos as $clogo ) {
                            echo '<div class="logo-slide"><img src="' . esc_url( $clogo['url'] ) . '" alt="' . $clogo['alt'] . '" width="160" height="60" loading="lazy" decoding="async"></div>';
                        }
                        // Duplicate for seamless infinite loop
                        foreach ( $client_logos as $clogo ) {
                            echo '<div class="logo-slide"><img src="' . esc_url( $clogo['url'] ) . '" alt="' . $clogo['alt'] . '" width="160" height="60" loading="lazy" decoding="async"></div>';
                        }
                    } else {
                        echo '<div class="logo-slide-placeholder">' . esc_html__( 'Tambahkan logo partner di admin panel.', 'tokoku' ) . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ====================================================
         GOOGLE MAPS REVIEWS SLIDER
         ==================================================== -->
    <?php
    $gmap_sec_title    = get_theme_mod( 'tokoku_gmap_section_title', __( 'Apa Kata Klien Tentang Toko Kami', 'tokoku' ) );
    $gmap_sec_subtitle = get_theme_mod( 'tokoku_gmap_section_subtitle', __( 'Ulasan autentik dari pelanggan yang telah memesan plakat berkualitas kami.', 'tokoku' ) );
    $gmap_place_photo  = get_theme_mod( 'tokoku_gmap_place_photo', '' );
    $gmap_place_name   = get_theme_mod( 'tokoku_gmap_place_name', get_bloginfo( 'name' ) );
    if ( empty( $gmap_place_name ) ) {
        $gmap_place_name = get_bloginfo( 'name' );
    }
    $gmap_place_rating  = (float) get_theme_mod( 'tokoku_gmap_place_rating', '5.0' );
    if ( $gmap_place_rating <= 0 || $gmap_place_rating > 5 ) {
        $gmap_place_rating = 5.0;
    }
    $gmap_total_reviews = (int) get_theme_mod( 'tokoku_gmap_total_reviews', 317 );
    if ( $gmap_total_reviews <= 0 ) {
        $gmap_total_reviews = 317;
    }
    $gmap_btn_text   = get_theme_mod( 'tokoku_gmap_btn_text', __( 'Menulis ulasan', 'tokoku' ) );
    $gmap_place_id   = get_theme_mod( 'tokoku_gmap_place_id', '' );
    $gmap_review_url = get_theme_mod( 'tokoku_gmap_review_url', '' );
    if ( empty( $gmap_review_url ) ) {
        if ( ! empty( $gmap_place_id ) ) {
            $gmap_review_url = 'https://search.google.com/local/writereview?placeid=' . rawurlencode( $gmap_place_id );
        } else {
            $gmap_review_url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $gmap_place_name );
        }
    }

    // Kumpulkan daftar ulasan dari pengaturan Admin (1 s/d 20)
    $gmap_reviews = array();
    $default_dates = array(
        1 => '28 Juli 2026',
        2 => '22 Juni 2026',
        3 => '14 Mei 2026',
        4 => '03 Mei 2026',
        5 => '19 April 2026',
    );
    for ( $i = 1; $i <= 20; $i++ ) {
        $r_name = get_theme_mod( "tokoku_testi_name_{$i}" );
        $r_text = get_theme_mod( "tokoku_testi_text_{$i}" );
        $r_img  = get_theme_mod( "tokoku_testi_img_{$i}" );
        $r_date = get_theme_mod( "tokoku_testi_date_{$i}" );

        if ( $r_name || $r_text ) {
            $r_rating = (int) get_theme_mod( "tokoku_testi_rating_{$i}", 5 );
            if ( $r_rating < 1 || $r_rating > 5 ) {
                $r_rating = 5;
            }
            if ( empty( $r_date ) ) {
                $r_date = isset( $default_dates[ $i ] ) ? $default_dates[ $i ] : wp_date( 'j F Y', strtotime( "-{$i} weeks" ) );
            }
            $r_local_guide = (int) get_theme_mod( "tokoku_testi_local_guide_{$i}", ( ! empty( $r_img ) ? 1 : 0 ) );

            $gmap_reviews[] = array(
                'name'        => $r_name ? $r_name : __( 'Pelanggan Google', 'tokoku' ),
                'text'        => $r_text,
                'img'         => $r_img,
                'date'        => $r_date,
                'rating'      => $r_rating,
                'local_guide' => $r_local_guide,
            );
        }
    }

    // Fallback ulasan default jika belum ada ulasan yang diisi di Admin
    if ( empty( $gmap_reviews ) ) {
        $gmap_reviews = array(
            array(
                'name'        => 'Refni Ifani',
                'date'        => '28 Juli 2026',
                'rating'      => 5,
                'img'         => '',
                'local_guide' => 0,
                'text'        => 'Beberapa kali pesan momento di ' . $gmap_place_name . '. Kualitas sangat bagus, bisa konsultasi sampe puas sebelum tahap produksi. Pengerjaan rapi, tepat waktu, dan pelayanan admin sangat ramah.',
            ),
            array(
                'name'        => 'Ardi Dirgantoro',
                'date'        => '22 Juni 2026',
                'rating'      => 5,
                'img'         => '',
                'local_guide' => 1,
                'text'        => 'Nyari informasi tentang pin, name tag, dan plakat custom. Dibantu tim CS dengan sangat ramah. Diterangkan dengan baik perihal kebutuhan saya sampai hasil akhirnya memuaskan.',
            ),
            array(
                'name'        => 'Budi Santoso',
                'date'        => '14 Mei 2026',
                'rating'      => 5,
                'img'         => '',
                'local_guide' => 1,
                'text'        => 'Pesan plakat akrilik dan kristal untuk acara penghargaan kantor. Hasil grafir laser sangat detail dan presisi, box beludrunya juga mewah. Pengiriman luar kota aman dengan bubble wrap tebal.',
            ),
            array(
                'name'        => 'Siti Rahmawati',
                'date'        => '03 Mei 2026',
                'rating'      => 5,
                'img'         => '',
                'local_guide' => 0,
                'text'        => 'Recommended banget buat yang butuh souvenir kantor atau plakat wisuda cepat. Desain dibantu dari nol sampai cocok, kualitas bahan tebal dan kelihatan eksklusif.',
            ),
        );
    }

    // Palet warna avatar inisial khas Google Maps
    $gmap_avatar_colors = array( '#7b1fa2', '#00897b', '#1976d2', '#e65100', '#c62828', '#2e7d32', '#512da8', '#0277bd' );
    ?>
    <section class="testimonials-section gmap-reviews-section" aria-label="<?php esc_attr_e( 'Ulasan Pelanggan Google Maps', 'tokoku' ); ?>">
        <div class="container">
            <?php if ( ! empty( $gmap_sec_title ) || ! empty( $gmap_sec_subtitle ) ) : ?>
                <div class="section-header">
                    <?php if ( ! empty( $gmap_sec_title ) ) : ?>
                        <h2 class="section-title"><?php echo esc_html( $gmap_sec_title ); ?></h2>
                    <?php endif; ?>
                    <?php if ( ! empty( $gmap_sec_subtitle ) ) : ?>
                        <p class="section-subtitle"><?php echo esc_html( $gmap_sec_subtitle ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="gmap-widget-layout">
                <!-- Sisi Kiri: Ringkasan Bisnis Google Maps -->
                <div class="gmap-summary-col">
                    <div class="gmap-summary-card">
                        <div class="gmap-summary-top">
                            <div class="gmap-place-thumb">
                                <?php if ( ! empty( $gmap_place_photo ) ) : ?>
                                    <img src="<?php echo esc_url( $gmap_place_photo ); ?>" alt="<?php echo esc_attr( $gmap_place_name ); ?>" width="84" height="60" loading="lazy" decoding="async">
                                <?php elseif ( has_custom_logo() ) : 
                                    $custom_logo_id = get_theme_mod( 'custom_logo' );
                                    $logo_src       = wp_get_attachment_image_url( $custom_logo_id, 'medium' );
                                ?>
                                    <img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php echo esc_attr( $gmap_place_name ); ?>" class="gmap-place-thumb--logo" width="84" height="60" loading="lazy" decoding="async">
                                <?php else : ?>
                                    <div class="gmap-place-thumb-placeholder" aria-hidden="true">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="gmap-place-info">
                                <h3 class="gmap-place-name"><?php echo esc_html( $gmap_place_name ); ?></h3>

                                <div class="gmap-place-stars" aria-label="<?php echo sprintf( esc_attr__( 'Rating %s dari 5', 'tokoku' ), number_format( $gmap_place_rating, 1 ) ); ?>">
                                    <?php
                                    $full_stars = (int) round( $gmap_place_rating );
                                    for ( $s = 1; $s <= 5; $s++ ) :
                                        $star_color = ( $s <= $full_stars ) ? '#fbbc04' : '#e2e8f0';
                                    ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="<?php echo esc_attr( $star_color ); ?>" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    <?php endfor; ?>
                                </div>

                                <div class="gmap-place-count">
                                    <strong><?php echo esc_html( number_format_i18n( $gmap_total_reviews ) ); ?></strong> <?php esc_html_e( 'Google ulasan', 'tokoku' ); ?>
                                </div>

                                <div class="gmap-place-actions">
                                    <a href="<?php echo esc_url( $gmap_review_url ); ?>" target="_blank" rel="noopener noreferrer" class="gmap-write-btn">
                                        <?php echo esc_html( $gmap_btn_text ? $gmap_btn_text : __( 'Menulis ulasan', 'tokoku' ) ); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan: Carousel Kartu Ulasan Google Maps -->
                <div class="gmap-slider-col testimonials-slider-container">
                    <button type="button" class="testi-slider-arrow testi-slider-prev" id="testi-prev-btn" aria-label="<?php esc_attr_e( 'Ulasan Sebelumnya', 'tokoku' ); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <button type="button" class="testi-slider-arrow testi-slider-next" id="testi-next-btn" aria-label="<?php esc_attr_e( 'Ulasan Berikutnya', 'tokoku' ); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>

                    <div class="testimonials-slider gmap-reviews-viewport" id="testimonials-slider">
                        <div class="testimonials-wrapper gmap-reviews-track">
                            <?php foreach ( $gmap_reviews as $idx => $rev ) : 
                                $avatar_bg = $gmap_avatar_colors[ $idx % count( $gmap_avatar_colors ) ];
                                $initial   = mb_strtoupper( mb_substr( trim( $rev['name'] ), 0, 1 ) );
                                if ( empty( $initial ) ) {
                                    $initial = 'G';
                                }
                            ?>
                                <div class="testimonial-slide gmap-review-slide">
                                    <article class="testimonial-card gmap-review-card">
                                        <!-- Header Kartu: Avatar + Nama & Tanggal + Logo Google -->
                                        <div class="gmap-card-header">
                                            <div class="gmap-reviewer-profile">
                                                <div class="gmap-avatar-wrap">
                                                    <?php if ( ! empty( $rev['img'] ) ) : ?>
                                                        <img src="<?php echo esc_url( $rev['img'] ); ?>" alt="<?php echo esc_attr( $rev['name'] ); ?>" class="gmap-avatar-img" width="44" height="44" loading="lazy" decoding="async">
                                                    <?php else : ?>
                                                        <div class="gmap-avatar-initial" style="background-color: <?php echo esc_attr( $avatar_bg ); ?>;">
                                                            <?php echo esc_html( $initial ); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ( ! empty( $rev['local_guide'] ) ) : ?>
                                                        <span class="gmap-local-guide-badge" title="<?php esc_attr_e( 'Google Local Guide', 'tokoku' ); ?>" aria-hidden="true">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="11" fill="#f97316" stroke="#ffffff" stroke-width="2"/>
                                                                <path d="M12 6.5l1.65 3.34 3.69.54-2.67 2.6.63 3.68L12 14.92l-3.3 1.74.63-3.68-2.67-2.6 3.69-.54L12 6.5z" fill="#ffffff"/>
                                                            </svg>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="gmap-author-meta">
                                                    <h4 class="gmap-author-name"><?php echo esc_html( $rev['name'] ); ?></h4>
                                                    <time class="gmap-review-date"><?php echo esc_html( $rev['date'] ); ?></time>
                                                </div>
                                            </div>

                                            <!-- Google "G" Multicolor Icon -->
                                            <div class="gmap-google-logo" aria-label="Google" title="Ulasan Google Maps">
                                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Baris Bintang + Badge Centang Biru Terverifikasi Google -->
                                        <div class="gmap-rating-row">
                                            <div class="gmap-stars" aria-label="<?php echo sprintf( esc_attr__( 'Rating %d dari 5', 'tokoku' ), $rev['rating'] ); ?>">
                                                <?php for ( $r = 1; $r <= 5; $r++ ) : 
                                                    $fill_color = ( $r <= $rev['rating'] ) ? '#fbbc04' : '#e2e8f0';
                                                ?>
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo esc_attr( $fill_color ); ?>" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="gmap-verified-badge" title="<?php esc_attr_e( 'Ulasan Terverifikasi Google', 'tokoku' ); ?>" aria-label="<?php esc_attr_e( 'Terverifikasi', 'tokoku' ); ?>">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M23 12l-2.44-2.79.34-3.69-3.61-.82-1.89-3.2L12 2.96 8.6 1.5 6.71 4.69 3.1 5.5l.34 3.7L1 12l2.44 2.79-.34 3.7 3.61.82L8.6 22.5l3.4-1.47 3.4 1.46 1.89-3.19 3.61-.82-.34-3.69L23 12z" fill="#3b82f6"/>
                                                    <path d="M10.09 15.58l-3.3-3.3 1.41-1.41 1.89 1.88 5.3-5.3 1.41 1.41-6.71 6.72z" fill="#ffffff"/>
                                                </svg>
                                            </span>
                                        </div>

                                        <!-- Isi Ulasan + Tombol Baca Lebih Lanjut -->
                                        <div class="gmap-review-body">
                                            <p class="gmap-review-text"><?php echo esc_html( $rev['text'] ); ?></p>
                                            <button type="button" class="gmap-read-more" data-more="<?php esc_attr_e( 'Baca lebih lanjut', 'tokoku' ); ?>" data-less="<?php esc_attr_e( 'Sembunyikan', 'tokoku' ); ?>">
                                                <?php esc_html_e( 'Baca lebih lanjut', 'tokoku' ); ?>
                                            </button>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="testimonial-dots"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ====================================================
         LATEST ARTICLES
         ==================================================== -->
    <section class="articles-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Artikel Terbaru</h2>
            </div>

            <div class="articles-slider-container">
                <div class="article-slider" id="article-slider">
                    <div class="article-track">
                        <?php
                        $latest_posts = new WP_Query( array(
                            'post_type'      => 'post',
                            'posts_per_page' => 6,
                            'no_found_rows'  => true,
                        ) );

                        if ( $latest_posts->have_posts() ) :
                            while ( $latest_posts->have_posts() ) : $latest_posts->the_post();
                        ?>
                            <div class="article-slide">
                                <article class="article-card has-bg-image">
                                    <div class="article-card__bg" style="background-image: url('<?php echo get_the_post_thumbnail_url(null, 'medium_large') ? esc_url(get_the_post_thumbnail_url(null, 'medium_large')) : esc_url(TOKOKU_URI . '/assets/images/placeholder.svg'); ?>');"></div>
                                    <div class="article-card__overlay"></div>
                                    <a href="<?php the_permalink(); ?>" class="article-card__link-overlay"></a>
                                    
                                    <div class="article-card__content">
                                        <?php 
                                        $categories = get_the_category();
                                        if ( ! empty( $categories ) ) {
                                            echo '<span class="article-category">' . esc_html( $categories[0]->name ) . '</span>';
                                        }
                                        ?>
                                        <h3 class="article-card__title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        <div class="article-card__meta">
                                            <span class="article-author">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-1px;margin-right:3px;" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> BY <?php echo esc_html(strtoupper(get_the_author())); ?>
                                            </span>
                                            <span class="article-date">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-1px;margin-right:3px;" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> <?php echo esc_html(strtoupper(get_the_date('j F Y'))); ?>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<div class="empty-msg">' . esc_html__( 'Belum ada artikel yang dipublikasikan.', 'tokoku' ) . '</div>';
                        endif;
                        ?>
                    </div>
                </div>
                
                <button class="article-slider-btn prev" id="article-prev" aria-label="<?php esc_attr_e( 'Artikel Sebelumnya', 'tokoku' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button class="article-slider-btn next" id="article-next" aria-label="<?php esc_attr_e( 'Artikel Berikutnya', 'tokoku' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
                
                <div class="article-slider-dots"></div>
            </div>
        </div>
    </section>
    
    <!-- ====================================================
         FAQ SECTION
         ==================================================== -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header text-center">
                <span class="section-badge"><?php _e( 'FAQ', 'tokoku' ); ?></span>
                <h2 class="section-title"><?php echo esc_html( get_theme_mod( 'tokoku_faq_title', 'Pertanyaan Umum' ) ); ?></h2>
                <p class="section-subtitle"><?php echo esc_html( get_theme_mod( 'tokoku_faq_subtitle', 'Temukan jawaban dari pertanyaan yang paling sering ditanyakan oleh pelanggan kami.' ) ); ?></p>
            </div>

            <div class="faq-accordion">
                <?php
                $faq_found = false;
                for ( $i = 1; $i <= 10; $i++ ) {
                    $question = get_theme_mod( "tokoku_faq_q_{$i}" );
                    $answer   = get_theme_mod( "tokoku_faq_a_{$i}" );
                    
                    if ( $question && $answer ) {
                        $faq_found = true;
                        ?>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3><?php echo esc_html( $question ); ?></h3>
                                <span class="faq-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </span>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <?php echo wpautop( wp_kses_post( $answer ) ); ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                
                if ( ! $faq_found ) {
                    echo '<p class="empty-msg text-center">' . __( 'Belum ada FAQ yang ditambahkan.', 'tokoku' ) . '</p>';
                }
                ?>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
