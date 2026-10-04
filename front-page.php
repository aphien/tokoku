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
                            $loading_attr = $is_first ? 'fetchpriority="high" loading="eager" decoding="sync"' : 'loading="lazy" decoding="async"';
                            
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
                            <?php echo tokoku_get_category_icon_html( $cat->term_id, 48 ); ?>
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
         TESTIMONIALS SLIDER
         ==================================================== -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php esc_html_e( 'Apa Kata Klien Tentang Toko Kami', 'tokoku' ); ?></h2>
                <p class="section-subtitle"><?php esc_html_e( 'Ulasan autentik dari pelanggan yang telah memesan plakat berkualitas kami.', 'tokoku' ); ?></p>
            </div>
            
            <div class="testimonials-slider-container">
                <!-- Navigation Arrows -->
                <button type="button" class="testi-slider-arrow testi-slider-prev" id="testi-prev-btn" aria-label="<?php esc_attr_e( 'Testimoni Sebelumnya', 'tokoku' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button type="button" class="testi-slider-arrow testi-slider-next" id="testi-next-btn" aria-label="<?php esc_attr_e( 'Testimoni Berikutnya', 'tokoku' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>

                <div class="testimonials-slider" id="testimonials-slider">
                    <div class="testimonials-wrapper">
                        <?php
                        $testis_found = false;
                        for ( $i = 1; $i <= 20; $i++ ) {
                            $name = get_theme_mod( "tokoku_testi_name_{$i}" );
                            $text = get_theme_mod( "tokoku_testi_text_{$i}" );
                            $img  = get_theme_mod( "tokoku_testi_img_{$i}" );
                            
                            if ( $name || $text ) {
                                $testis_found = true;
                                $rating = (int) get_theme_mod( "tokoku_testi_rating_{$i}", 5 );
                                if ( $rating < 1 || $rating > 5 ) $rating = 5;
                                ?>
                                <div class="testimonial-slide">
                                    <div class="testimonial-card">
                                        <!-- Modern Floating Quote Icon Badge (Ala Quote Kekinian) -->
                                        <div class="testi-quote-badge" aria-hidden="true">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                        </div>

                                        <!-- Testimonial Quote (Merriweather, Tebal & Diperbesar 20%) -->
                                        <blockquote class="testi-quote">
                                            “<?php echo esc_html( $text ); ?>”
                                        </blockquote>

                                        <!-- Bagian Bawah: Foto + Badge Centang / Verifikasi, Sebelahnya Nama, Dibawahnya Bintang / Ulasan -->
                                        <div class="testi-bottom">
                                            <div class="testi-avatar-wrap">
                                                <?php if ( $img ) : ?>
                                                    <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="testi-avatar-img" width="56" height="56" loading="lazy" decoding="async">
                                                <?php else : ?>
                                                    <div class="testi-avatar-initial">
                                                        <?php echo esc_html( mb_substr( $name ? $name : 'U', 0, 1 ) ); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="testi-avatar-badge" title="<?php esc_attr_e( 'Pelanggan Terverifikasi', 'tokoku' ); ?>">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                                </span>
                                            </div>

                                            <div class="testi-author-info">
                                                <h4 class="testi-author-name"><?php echo esc_html( $name ); ?></h4>

                                                <div class="testi-rating-wrap">
                                                    <div class="testi-stars" aria-label="<?php echo sprintf( esc_attr__( 'Rating %d dari 5', 'tokoku' ), $rating ); ?>">
                                                        <?php for ( $r = 1; $r <= 5; $r++ ) : 
                                                            $fill_color = $r <= $rating ? '#f59e0b' : '#e2e8f0';
                                                        ?>
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="<?php echo esc_attr( $fill_color ); ?>" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="testi-rating-score"><?php echo number_format( $rating, 1 ); ?></span>
                                                    <span class="testi-rating-label">• <?php esc_html_e( 'Ulasan Pembeli', 'tokoku' ); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        
                        if ( ! $testis_found ) {
                            echo '<p class="empty-msg">' . esc_html__( 'Belum ada testimoni klien.', 'tokoku' ) . '</p>';
                        }
                        ?>
                    </div>
                    <div class="testimonial-dots"></div>
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
                                    <div class="article-card__bg" style="background-image: url('<?php echo get_the_post_thumbnail_url(null, 'medium_large') ? esc_url(get_the_post_thumbnail_url(null, 'medium_large')) : esc_url(TOKOKU_URI . '/assets/images/placeholder.png'); ?>');"></div>
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
