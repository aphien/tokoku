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

    // Deteksi rasio aspek banner secara otomatis agar tampilan tidak terpotong (zero layout shift)
    $banner_ratio = '2.735 / 1';
    if ( ! empty( $slides_data[0]['img'] ) ) {
        $first_img_url = $slides_data[0]['img'];
        $upload_dir    = wp_upload_dir();
        if ( strpos( $first_img_url, $upload_dir['baseurl'] ) !== false ) {
            $local_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $first_img_url );
            if ( file_exists( $local_path ) ) {
                $img_size = @getimagesize( $local_path );
                if ( ! empty( $img_size[0] ) && ! empty( $img_size[1] ) ) {
                    $banner_ratio = $img_size[0] . ' / ' . $img_size[1];
                }
            }
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
                            $loading_attr = $is_first ? 'fetchpriority="high" loading="eager"' : 'loading="lazy" decoding="async"';
                            
                            echo '<div class="slide" role="group" aria-roledescription="slide" aria-label="' . esc_attr( sprintf( __( 'Slide %d dari %d', 'tokoku' ), $idx + 1, $slide_count ) ) . '">';
                            if ( ! empty( $slide['link'] ) ) {
                                echo '<a href="' . esc_url( $slide['link'] ) . '" class="slide-link" draggable="false">';
                            }
                            echo '<img src="' . esc_url( $slide['img'] ) . '" alt="' . esc_attr( $slide['alt'] ) . '" draggable="false" ' . $loading_attr . '>';
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
                ) );

                if ( $latest_products->have_posts() ) :
                    while ( $latest_products->have_posts() ) : $latest_products->the_post();
                        get_template_part( 'template-parts/product-card' );
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<div class="empty-products">
                        <span class="dashicons dashicons-cart" style="font-size: 60px; width: 60px; height: 60px; color: #ccc; display: block; margin: 0 auto 15px;"></span>
                        <p>' . sprintf( wp_kses_post( __( 'Belum ada produk. <a href="%s">Tambah produk</a>', 'tokoku' ) ), esc_url( admin_url('post-new.php?post_type=produk') ) ) . '</p>
                    </div>';
                endif;
                ?>
            </div>

            <div class="section-footer">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>" class="btn-view-all">
                    Lihat Semua Produk
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
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
                    $site_name   = esc_attr( get_bloginfo( 'name' ) );
                    $logos_found = false;
                    for ( $i = 1; $i <= 50; $i++ ) {
                        $logo = get_theme_mod( "tokoku_client_logo_{$i}" );
                        if ( $logo ) {
                            $logos_found = true;
                            $logo_alt    = sprintf( esc_attr__( 'Klien & Mitra %s - Logo %d', 'tokoku' ), $site_name, $i );
                            echo '<div class="logo-slide"><img src="' . esc_url( $logo ) . '" alt="' . $logo_alt . '"></div>';
                        }
                    }
                    
                    // Duplicate for seamless loop if logos exist
                    if ( $logos_found ) {
                        for ( $i = 1; $i <= 50; $i++ ) {
                            $logo = get_theme_mod( "tokoku_client_logo_{$i}" );
                            if ( $logo ) {
                                $logo_alt = sprintf( esc_attr__( 'Klien & Mitra %s - Logo %d', 'tokoku' ), $site_name, $i );
                                echo '<div class="logo-slide"><img src="' . esc_url( $logo ) . '" alt="' . $logo_alt . '"></div>';
                            }
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
                <h2 class="section-title">Ulasan Klien</h2>
            </div>
            
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
                            ?>
                            <div class="testimonial-slide">
                                <div class="testimonial-card">
                                    <div class="testimonial-quote">
                                        <span class="dashicons dashicons-format-quote" style="font-size: 40px; width: 40px; height: 40px; opacity: 0.2;"></span>
                                    </div>
                                    <p class="testimonial-text">"<?php echo esc_html( $text ); ?>"</p>
                                    <div class="testimonial-author">
                                        <?php if ( $img ) : ?>
                                            <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="author-img">
                                        <?php endif; ?>
                                        <div class="author-info">
                                            <h4 class="author-name"><?php echo esc_html( $name ); ?></h4>
                                            <div class="author-rating">
                                                <?php 
                                                $rating = get_theme_mod( "tokoku_testi_rating_{$i}", 5 );
                                                for ($r = 1; $r <= 5; $r++) {
                                                    $star_class = $r <= $rating ? 'dashicons-star-filled' : 'dashicons-star-empty';
                                                    echo '<span class="dashicons '.$star_class.'"></span>';
                                                }
                                                ?>
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
                                                <span class="dashicons dashicons-admin-users" style="font-size: 14px; margin-top: 3px;"></span> BY <?php echo esc_html(strtoupper(get_the_author())); ?>
                                            </span>
                                            <span class="article-date">
                                                <span class="dashicons dashicons-calendar-alt" style="font-size: 14px; margin-top: 3px;"></span> <?php echo esc_html(strtoupper(get_the_date('j F Y'))); ?>
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
                
                <button class="article-slider-btn prev" id="article-prev">
                    <span class="dashicons dashicons-arrow-left-alt2"></span>
                </button>
                <button class="article-slider-btn next" id="article-next">
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
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
