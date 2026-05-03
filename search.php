<?php
/**
 * The template for displaying search results pages
 *
 * @package TokoKu
 */

get_header(); ?>

<main id="main-content" class="site-main search-page">
    <div class="container">
        
        <header class="page-header">
            <h1 class="page-title">
                <?php printf( esc_html__( 'Hasil Pencarian: %s', 'tokoku' ), '<span>' . get_search_query() . '</span>' ); ?>
            </h1>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="product-grid">
                <?php
                while ( have_posts() ) : the_post();
                    if ( get_post_type() === 'produk' ) {
                        get_template_part( 'template-parts/product-card' );
                    } else {
                        // Fallback for regular posts in search
                        get_template_part( 'template-parts/content', 'search' );
                    }
                endwhile;
                ?>
            </div>
            
            <div class="pagination">
                <?php the_posts_pagination(); ?>
            </div>
        <?php else : ?>
            <div class="no-results">
                <p>Maaf, tidak ada hasil yang cocok dengan kata kunci Anda.</p>
                <?php get_search_form(); ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
