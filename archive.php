<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The template for displaying general archives (Categories, Tags, Authors, Dates)
 *
 * @package TokoKu
 */

get_header(); 

$archive_title = get_the_archive_title();
$archive_desc  = get_the_archive_description();
$blog_cats     = get_categories( array( 'hide_empty' => false ) );
?>

<main id="main-content" class="site-main blog-page">
    <div class="container">
        
        <!-- Breadcrumbs -->
        <nav class="archive-breadcrumbs" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
            <span class="sep">/</span>
            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>">Blog</a>
            <span class="sep">/</span>
            <span class="current"><?php echo wp_strip_all_tags( $archive_title ); ?></span>
        </nav>

        <!-- Blog Hero Header -->
        <header class="blog-hero-header">
            <div class="blog-hero-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <span>Arsip Blog</span>
            </div>
            <h1 class="blog-hero-title"><?php echo wp_strip_all_tags( $archive_title ); ?></h1>
            <?php if ( $archive_desc ) : ?>
                <div class="blog-hero-desc"><?php echo wp_kses_post( $archive_desc ); ?></div>
            <?php else : ?>
                <p class="blog-hero-desc">Menampilkan seluruh artikel dan pembahasan pada topik ini.</p>
            <?php endif; ?>
        </header>

        <!-- Blog Category Pills Bar -->
        <?php if ( ! empty( $blog_cats ) && ! is_wp_error( $blog_cats ) ) : ?>
        <div class="blog-category-bar">
            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>" class="blog-category-pill">
                Semua Artikel
            </a>
            <?php foreach ( $blog_cats as $bcat ) : ?>
                <a href="<?php echo esc_url( get_category_link( $bcat->term_id ) ); ?>" class="blog-category-pill <?php echo ( is_category( $bcat->term_id ) ) ? 'active' : ''; ?>">
                    <?php echo esc_html( $bcat->name ); ?>
                    <?php if ( $bcat->count > 0 ) : ?>
                        <span class="pill-count"><?php echo esc_html( $bcat->count ); ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Blog Posts Grid -->
        <?php if ( have_posts() ) : ?>
            <div class="blog-grid">
                <?php while ( have_posts() ) : the_post(); 
                    $word_count   = str_word_count( strip_tags( get_the_content() ) );
                    $reading_time = max( 1, ceil( $word_count / 200 ) );
                    $post_cats    = get_the_category();
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-card' ); ?>>
                        <div class="blog-card__image-wrap">
                            <a href="<?php the_permalink(); ?>" class="blog-card__image-link" aria-label="<?php the_title_attribute(); ?>">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?>
                                <?php else : ?>
                                    <div class="blog-card__placeholder">
                                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <?php if ( ! empty( $post_cats ) ) : ?>
                                <span class="blog-card__category-badge">
                                    <?php echo esc_html( $post_cats[0]->name ); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="blog-card__body">
                            <div class="blog-card__meta">
                                <span class="meta-date">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                                </span>
                                <span class="meta-dot">&bull;</span>
                                <span class="meta-reading-time">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <?php echo esc_html( $reading_time ); ?> mnt baca
                                </span>
                            </div>

                            <h2 class="blog-card__title">
                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="blog-card__excerpt">
                                <?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
                            </div>

                            <div class="blog-card__footer">
                                <a href="<?php the_permalink(); ?>" class="blog-card__read-more">
                                    <span>Baca Selengkapnya</span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php
                the_posts_pagination( array(
                    'prev_text' => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg> <span>Sebelumnya</span>',
                    'next_text' => '<span>Selanjutnya</span> <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    'mid_size'  => 2,
                ) );
                ?>
            </div>

        <?php else : ?>
            
            <div class="no-articles-found">
                <div class="empty-icon">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <h3>Tidak Ada Artikel Ditemukan</h3>
                <p>Belum ada artikel yang masuk ke dalam arsip ini. Silakan periksa kategori lain atau kembali ke beranda.</p>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                    Kembali ke Beranda
                </a>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
