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
            $current_url   = urlencode( get_permalink() );
            $raw_url       = esc_url( get_permalink() );
            $current_title = urlencode( get_the_title() );
        ?>
            <!-- Breadcrumbs -->
            <nav class="archive-breadcrumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
                <span class="sep">/</span>
                <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>">Blog</a>
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
                <div class="single-post-share">
                    <div class="share-label-group">
                        <div class="share-badge-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                            </svg>
                        </div>
                        <div class="share-text-wrap">
                            <span class="share-heading">Bagikan Artikel Ini:</span>
                            <span class="share-subheading">Sebarkan artikel bermanfaat ini ke media sosial Anda</span>
                        </div>
                    </div>
                    <div class="share-icons">
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.171-2.911 1.024 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.627 0 12-5.373 12-12 0-6.62-5.373-11.987-11.983-11.983z"/></svg>
                        </a>
                        <!-- Salin Tautan (Copy Link) -->
                        <button type="button" class="share-icon link-share copy-link-btn" data-url="<?php echo $raw_url; ?>" aria-label="Salin Tautan" title="Salin Tautan">
                            <svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
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
                if ( ! empty( $post_cats ) ) {
                    $related_args = array(
                        'category__in'   => array( $post_cats[0]->term_id ),
                        'post__not_in'   => array( get_the_ID() ),
                        'posts_per_page' => 3,
                        'orderby'        => 'rand',
                    );
                    $related_query = new WP_Query( $related_args );

                    if ( $related_query->have_posts() ) : ?>
                        <div class="related-posts-section">
                            <h3 class="related-posts-heading">Artikel Terkait Lainnya</h3>
                            <div class="blog-grid related-grid">
                                <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
                                    <article class="blog-card">
                                        <div class="blog-card__image-wrap">
                                            <a href="<?php the_permalink(); ?>" class="blog-card__image-link">
                                                <?php if ( has_post_thumbnail() ) : ?>
                                                    <?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?>
                                                <?php else : ?>
                                                    <div class="blog-card__placeholder">
                                                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="blog-card__body">
                                            <div class="blog-card__meta">
                                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                                            </div>
                                            <h4 class="blog-card__title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                        </div>
                                    </article>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                        </div>
                    <?php endif;
                }
                ?>

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
