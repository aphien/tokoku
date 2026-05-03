<?php
/**
 * The main template file (fallback)
 *
 * @package TokoKu
 */

get_header(); ?>

<main id="main-content" class="site-main">
    <div class="container">
        
        <?php if ( have_posts() ) : ?>
            
            <div class="page-header">
                <h1 class="page-title">
                    <?php
                    if ( is_home() && ! is_front_page() ) {
                        single_post_title();
                    } else {
                        esc_html_e( 'Artikel Terbaru', 'tokoku' );
                    }
                    ?>
                </h1>
            </div>

            <div class="posts-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-card__image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium_large' ); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-card__content">
                            <h2 class="post-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="post-card__excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <div class="post-card__meta">
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                    <?php echo esc_html( get_the_date() ); ?>
                                </time>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => '&larr; Sebelumnya',
                    'next_text' => 'Selanjutnya &rarr;',
                ) );
                ?>
            </div>

        <?php else : ?>
            
            <div class="no-results">
                <h1><?php esc_html_e( 'Tidak ada konten', 'tokoku' ); ?></h1>
                <p><?php esc_html_e( 'Belum ada konten yang tersedia.', 'tokoku' ); ?></p>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
