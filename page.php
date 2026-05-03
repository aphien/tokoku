<?php
/**
 * The template for displaying all pages
 *
 * @package TokoKu
 */

get_header(); ?>

<main id="main-content" class="site-main default-page">
    <div class="container container-small">
        
        <?php
        while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header>

                <div class="entry-content">
                    <?php
                    the_content();
                    wp_link_pages();
                    ?>
                </div>
            </article>
        <?php endwhile; ?>

    </div>
</main>

<style>
.container-small { max-width: 800px; }
.entry-header { margin-bottom: 40px; text-align: center; }
.entry-title { font-size: 3rem; font-weight: 800; }
.entry-content { line-height: 1.8; font-size: 1.1rem; }
.entry-content p { margin-bottom: 20px; }
</style>

<?php get_footer(); ?>
