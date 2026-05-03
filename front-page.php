<?php
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
    <section class="hero-slider-section">
        <div class="slider-container" id="home-slider">
            <div class="slider-wrapper">
                <?php
                $has_slides = false;
                for ( $i = 1; $i <= 10; $i++ ) {
                    $img  = get_theme_mod( "tokoku_slide_image_{$i}" );
                    $link = get_theme_mod( "tokoku_slide_link_{$i}" );
                    
                    if ( $img ) {
                        $has_slides = true;
                        echo '<div class="slide">';
                        if ( $link ) echo '<a href="' . esc_url( $link ) . '">';
                        echo '<img src="' . esc_url( $img ) . '" alt="Banner ' . $i . '">';
                        if ( $link ) echo '</a>';
                        echo '</div>';
                    }
                }
                
                if ( ! $has_slides ) {
                    echo '<div class="slide slide--placeholder">
                        <div class="slide-placeholder-inner">
                            <h2>' . sprintf( esc_html__( 'Selamat Datang di %s', 'tokoku' ), esc_html( get_bloginfo( 'name' ) ) ) . '</h2>
                            <p>' . esc_html__( 'Atur banner di Tampilan &rarr; Kustomisasi &rarr; Banner Slider', 'tokoku' ) . '</p>
                        </div>
                    </div>';
                }
                ?>
            </div>
            
            <button class="slider-btn slider-prev" aria-label="Previous">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
            </button>
            <button class="slider-btn slider-next" aria-label="Next">
                <span class="dashicons dashicons-arrow-right-alt2"></span>
            </button>
            <div class="slider-dots"></div>
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
                    'number'     => 6,
                ) );

                if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
                    foreach ( $categories as $cat ) :
                        $icon_id = get_term_meta( $cat->term_id, 'tokoku_kategori_icon', true );
                        $icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'thumbnail' ) : '';
                ?>
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="category-item">
                        <div class="category-icon">
                            <?php if ( $icon_url ) : ?>
                                <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" style="width: 72px; height: 72px; object-fit: contain;">
                            <?php else : ?>
                                <span class="dashicons dashicons-archive" style="font-size: 48px; width: 48px; height: 48px;"></span>
                            <?php endif; ?>
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
                    $logos_found = false;
                    for ( $i = 1; $i <= 50; $i++ ) {
                        $logo = get_theme_mod( "tokoku_client_logo_{$i}" );
                        if ( $logo ) {
                            $logos_found = true;
                            echo '<div class="logo-slide"><img src="' . esc_url( $logo ) . '" alt="Client Logo ' . $i . '"></div>';
                        }
                    }
                    
                    // Duplicate for seamless loop if logos exist
                    if ( $logos_found ) {
                        for ( $i = 1; $i <= 50; $i++ ) {
                            $logo = get_theme_mod( "tokoku_client_logo_{$i}" );
                            if ( $logo ) {
                                echo '<div class="logo-slide"><img src="' . esc_url( $logo ) . '" alt="Client Logo ' . $i . '"></div>';
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
                                <article class="article-card">
                                    <div class="article-card__image">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <?php the_post_thumbnail( 'medium_large' ); ?>
                                        <?php else : ?>
                                            <div class="article-placeholder">
                                                <span class="dashicons dashicons-media-text" style="font-size: 40px; width: 40px; height: 40px; opacity: 0.3;"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="article-card__content">
                                        <div class="article-card__meta">
                                            <span class="article-date"><?php echo get_the_date(); ?></span>
                                        </div>
                                        <h3 class="article-card__title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        <div class="article-card__excerpt">
                                            <?php echo wp_trim_words( get_the_excerpt(), 12 ); ?>
                                        </div>
                                        <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm article-read-more">
                                            Baca Selengkapnya
                                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                                        </a>
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
        <div class="container container--narrow">
            <div class="section-header text-center">
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
                                    <span class="dashicons dashicons-arrow-down-alt2"></span>
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
