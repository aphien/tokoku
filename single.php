<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The template for displaying all single blog posts
 *
 * @package TokoKu
 */

get_header(); ?>

<main id="main-content" class="site-main single-post-page">
    <div class="container container-narrow">

        <?php while ( have_posts() ) : the_post(); 
            $word_count   = str_word_count( strip_tags( get_the_content() ) );
            $reading_time = max( 1, ceil( $word_count / 200 ) );
            $post_cats    = get_the_category();
            $share_permalink = get_permalink();
            $share_title_raw = get_the_title();
            $current_url   = rawurlencode( $share_permalink );
            $raw_url       = esc_url( $share_permalink );
            $current_title = rawurlencode( $share_title_raw );
            $post_thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: '';
            $post_thumb_enc = rawurlencode( $post_thumb_url );
            $post_cats_name = ! empty( $post_cats ) ? $post_cats[0]->name : 'Artikel Blog';
            $post_desc      = wp_trim_words( wp_strip_all_tags( get_the_excerpt() ?: get_the_content() ), 16 );
            $wa_message_lines = array();
            $wa_message_lines[] = '*' . $share_title_raw . '*';
            $wa_message_lines[] = 'Kategori: ' . $post_cats_name . ' (' . $reading_time . ' menit baca)';
            $wa_message_lines[] = 'Baca artikel selengkapnya di TokoKu:';
            $wa_message_lines[] = $share_permalink;
            $wa_share_text  = rawurlencode( implode( "\n", $wa_message_lines ) );
            $email_subject  = rawurlencode( 'Artikel Menarik: ' . $share_title_raw );
            $email_body     = rawurlencode( "Halo,\n\nSaya merekomendasikan artikel ini: " . $share_title_raw . "\n\nBaca selengkapnya di TokoKu:\n" . $share_permalink );
        ?>
            <!-- Breadcrumbs -->
            <nav class="archive-breadcrumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
                <span class="sep">/</span>
                <?php 
                $page_for_posts_id = (int) get_option( 'page_for_posts' );
                $blog_url          = ( $page_for_posts_id > 0 ) ? get_permalink( $page_for_posts_id ) : home_url( '/blog/' );
                ?>
                <a href="<?php echo esc_url( $blog_url ); ?>">Blog</a>
                <span class="sep">/</span>
                <span class="current"><?php the_title(); ?></span>
            </nav>

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post-article' ); ?>>
                
                <!-- Article Header -->
                <header class="single-post-header">
                    <?php if ( ! empty( $post_cats ) ) : ?>
                        <div class="single-post-categories">
                            <?php foreach ( $post_cats as $pcat ) : ?>
                                <a href="<?php echo esc_url( get_category_link( $pcat->term_id ) ); ?>" class="single-post-cat-badge">
                                    <?php echo esc_html( $pcat->name ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <h1 class="single-post-title"><?php the_title(); ?></h1>

                    <div class="single-post-meta">
                        <div class="meta-author">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 40, '', get_the_author(), array( 'class' => 'author-avatar' ) ); ?>
                            <div class="author-info">
                                <span class="author-name"><?php the_author(); ?></span>
                                <span class="author-role"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> Contributor</span>
                            </div>
                        </div>

                        <div class="meta-details">
                            <span class="meta-item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                            </span>
                            <span class="meta-item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?php echo esc_html( $reading_time ); ?> menit baca
                            </span>
                        </div>
                    </div>
                </header>

                <!-- Featured Image -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="single-post-featured-image">
                        <?php the_post_thumbnail( 'full', array( 'loading' => 'eager' ) ); ?>
                    </div>
                <?php endif; ?>

                <!-- Article Content -->
                <div class="single-post-content entry-content">
                    <?php the_content(); ?>
                </div>

                <!-- Tags -->
                <?php
                $post_tags = get_the_tags();
                if ( ! empty( $post_tags ) ) : ?>
                    <div class="single-post-tags">
                        <span class="tags-label">Tag Terkait:</span>
                        <div class="tags-list">
                            <?php foreach ( $post_tags as $ptag ) : ?>
                                <a href="<?php echo esc_url( get_tag_link( $ptag->term_id ) ); ?>" class="tag-badge">
                                    #<?php echo esc_html( $ptag->name ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Social Share Bar -->
                <div class="single-post-share"
                     data-share-title="<?php echo esc_attr( $share_title_raw ); ?>"
                     data-share-url="<?php echo $raw_url; ?>"
                     data-share-image="<?php echo esc_url( $post_thumb_url ); ?>"
                     data-share-badge="<?php echo esc_attr( $post_cats_name ); ?>"
                     data-share-desc="<?php echo esc_attr( $post_desc ); ?>">
                    <div class="share-label-group share-modal-trigger-btn" style="cursor:pointer;" title="Buka Pratinjau & Opsi Berbagi Lengkap">
                        <div class="share-badge-icon">
                            <?php echo tokoku_icon( 'share-nodes', 20 ); ?>
                        </div>
                        <div class="share-text-wrap">
                            <span class="share-heading">Bagikan Artikel Ini:</span>
                            <span class="share-subheading">Sebarkan artikel bermanfaat ini ke media sosial Anda</span>
                        </div>
                    </div>
                    <div class="share-icons">
                        <!-- Tombol Modal Pratinjau & Berbagi -->
                        <button type="button" class="share-icon modal-trigger-icon share-modal-trigger-btn" data-url="<?php echo $raw_url; ?>" data-title="<?php echo esc_attr( $share_title_raw ); ?>" data-text="<?php echo esc_attr( $share_title_raw ); ?>" aria-label="Buka Opsi Berbagi Lengkap" title="Buka Opsi Berbagi Lengkap">
                            <?php echo tokoku_icon( 'share-nodes', 18 ); ?>
                        </button>

                        <!-- WhatsApp -->
                        <a href="https://api.whatsapp.com/send?text=<?php echo $wa_share_text; ?>" target="_blank" rel="noopener noreferrer" class="share-icon wa" aria-label="Bagikan ke WhatsApp" title="WhatsApp">
                            <?php echo tokoku_icon( 'whatsapp', 20 ); ?>
                        </a>

                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $current_url; ?>" target="_blank" rel="noopener noreferrer" class="share-icon fb" aria-label="Bagikan ke Facebook" title="Facebook">
                            <?php echo tokoku_icon( 'facebook', 20 ); ?>
                        </a>

                        <!-- 𝕏 (Twitter) -->
                        <a href="https://twitter.com/intent/tweet?url=<?php echo $current_url; ?>&text=<?php echo $current_title; ?>" target="_blank" rel="noopener noreferrer" class="share-icon tw" aria-label="Bagikan ke X" title="X (Twitter)">
                            <?php echo tokoku_icon( 'x-twitter', 18 ); ?>
                        </a>

                        <!-- Telegram -->
                        <a href="https://t.me/share/url?url=<?php echo $current_url; ?>&text=<?php echo $current_title; ?>" target="_blank" rel="noopener noreferrer" class="share-icon tg" aria-label="Bagikan ke Telegram" title="Telegram">
                            <?php echo tokoku_icon( 'telegram', 20 ); ?>
                        </a>

                        <!-- Pinterest (Dilengkapi parameter media gambar) -->
                        <a href="https://pinterest.com/pin/create/button/?url=<?php echo $current_url; ?>&description=<?php echo $current_title; ?><?php if ( ! empty( $post_thumb_enc ) ) : ?>&media=<?php echo $post_thumb_enc; ?><?php endif; ?>" target="_blank" rel="noopener noreferrer" class="share-icon pin" aria-label="Bagikan ke Pinterest" title="Pinterest">
                            <?php echo tokoku_icon( 'pinterest', 20 ); ?>
                        </a>

                        <!-- LinkedIn -->
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $current_url; ?>" target="_blank" rel="noopener noreferrer" class="share-icon in" aria-label="Bagikan ke LinkedIn" title="LinkedIn">
                            <?php echo tokoku_icon( 'linkedin', 18 ); ?>
                        </a>

                        <!-- Email -->
                        <a href="mailto:?subject=<?php echo $email_subject; ?>&body=<?php echo $email_body; ?>" class="share-icon mail" aria-label="Bagikan lewat Email" title="Email">
                            <?php echo tokoku_icon( 'envelope', 18 ); ?>
                        </a>

                        <!-- Salin Tautan (Copy Link) -->
                        <button type="button" class="share-icon link-share copy-link-btn" data-url="<?php echo $raw_url; ?>" aria-label="Salin Tautan" title="Salin Tautan">
                            <span class="copy-icon-wrap default-icon"><?php echo tokoku_icon( 'link', 18 ); ?></span>
                            <span class="copy-icon-wrap success-icon" style="display:none;"><?php echo tokoku_icon( 'check', 18 ); ?></span>
                        </button>
                    </div>
                </div>

                <!-- Author Box -->
                <div class="single-post-author-box">
                    <?php echo get_avatar( get_the_author_meta( 'ID' ), 64, '', get_the_author(), array( 'class' => 'author-box-avatar' ) ); ?>
                    <div class="author-box-content">
                        <span class="author-box-label">Penulis</span>
                        <h4 class="author-box-name"><?php the_author(); ?></h4>
                        <p class="author-box-bio">
                            <?php echo get_the_author_meta( 'description' ) ?: 'Penulis dan tim kreatif yang berdedikasi membagikan wawasan seputar plakat kustom, piala, dan solusi merchandise eksklusif.'; ?>
                        </p>
                    </div>
                </div>

                <!-- Previous / Next Navigation -->
                <nav class="single-post-nav" aria-label="Navigasi Artikel">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>
                    <div class="post-nav-col prev-col">
                        <?php if ( $prev_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="post-nav-card">
                                <span class="nav-direction">&larr; Artikel Sebelumnya</span>
                                <span class="nav-title"><?php echo esc_html( $prev_post->post_title ); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="post-nav-col next-col">
                        <?php if ( $next_post ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="post-nav-card text-right">
                                <span class="nav-direction">Artikel Selanjutnya &rarr;</span>
                                <span class="nav-title"><?php echo esc_html( $next_post->post_title ); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </nav>

                <!-- Related Posts -->
                <?php
                $related_post_ids = array();
                if ( ! empty( $post_cats ) ) {
                    $cat_ids = wp_list_pluck( $post_cats, 'term_id' );
                    $cat_query = new WP_Query( array(
                        'category__in'        => $cat_ids,
                        'post__not_in'        => array( get_the_ID() ),
                        'posts_per_page'      => 6,
                        'fields'              => 'ids',
                        'no_found_rows'       => true,
                        'ignore_sticky_posts' => true,
                    ) );
                    if ( $cat_query->have_posts() ) {
                        $related_post_ids = $cat_query->posts;
                    }
                }

                // If fewer than 6 posts in current category, supplement with recent posts
                if ( count( $related_post_ids ) < 6 ) {
                    $needed = 6 - count( $related_post_ids );
                    $fallback_query = new WP_Query( array(
                        'post__not_in'        => array_merge( array( get_the_ID() ), $related_post_ids ),
                        'posts_per_page'      => $needed,
                        'fields'              => 'ids',
                        'no_found_rows'       => true,
                        'orderby'             => 'date',
                        'order'               => 'DESC',
                        'ignore_sticky_posts' => true,
                    ) );
                    if ( $fallback_query->have_posts() ) {
                        $related_post_ids = array_merge( $related_post_ids, $fallback_query->posts );
                    }
                }

                if ( ! empty( $related_post_ids ) ) :
                    $related_query = new WP_Query( array(
                        'post__in'            => $related_post_ids,
                        'orderby'             => 'post__in',
                        'posts_per_page'      => 6,
                        'no_found_rows'       => true,
                        'ignore_sticky_posts' => true,
                    ) );

                    if ( $related_query->have_posts() ) :
                ?>
                    <section class="related-posts-section" aria-label="<?php esc_attr_e( 'Artikel Terkait Lainnya', 'tokoku' ); ?>">
                        <div class="section-header text-center">
                            <h2 class="section-title"><?php _e( 'Artikel Terkait Lainnya', 'tokoku' ); ?></h2>
                        </div>

                        <div class="related-slider-wrapper">
                            <div class="related-articles-grid" id="related-articles-slider">
                                <?php 
                                $card_idx = 0;
                                while ( $related_query->have_posts() ) : $related_query->the_post(); 
                                ?>
                                    <div class="related-article-item" data-index="<?php echo esc_attr( $card_idx++ ); ?>">
                                        <article class="article-card has-bg-image">
                                            <div class="article-card__bg" style="background-image: url('<?php echo get_the_post_thumbnail_url(null, 'medium_large') ? esc_url(get_the_post_thumbnail_url(null, 'medium_large')) : esc_url(TOKOKU_URI . '/assets/images/placeholder.svg'); ?>');"></div>
                                            <div class="article-card__overlay"></div>
                                            <a href="<?php the_permalink(); ?>" class="article-card__link-overlay" aria-label="<?php the_title_attribute(); ?>"></a>
                                            
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
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>

                            <div class="related-slider-dots" id="related-slider-dots" aria-hidden="true"></div>
                        </div>
                    </section>
                <?php endif; endif; ?>

                <!-- Comments -->
                <?php
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>

            </article>

        <?php endwhile; ?>

    </div>
</main>

<?php get_footer(); ?>
