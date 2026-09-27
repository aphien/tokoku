<?php
/**
 * Template Name: Portofolio / Hasil Karya
 * Template Post Type: page
 *
 * @package TokoKu
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$wa_number = get_theme_mod( 'tokoku_wa_number', '6281234567890' );

// Get all products with gallery images as portfolio items
$portfolio_query = new WP_Query([
    'post_type'      => 'produk',
    'posts_per_page' => 24,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => [
        'relation' => 'OR',
        ['key' => '_produk_gallery', 'compare' => 'EXISTS'],
        ['key' => '_thumbnail_id',   'compare' => 'EXISTS'],
    ],
]);
?>

<main id="main-content" class="site-main">
    <div class="container">
        <!-- Hero -->
        <div style="text-align:center; padding: 60px 0 40px;">
            <span style="display:inline-block; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color:#fff; font-size:0.8rem; font-weight:800; padding:5px 16px; border-radius:20px; letter-spacing:0.5px; margin-bottom:16px; text-transform:uppercase;">Hasil Karya Kami</span>
            <h1 style="font-size:clamp(1.8rem,4vw,2.8rem); font-weight:900; color:var(--text); margin-bottom:16px;">Portofolio Plakat</h1>
            <p style="font-size:1rem; color:var(--text2); max-width:560px; margin:0 auto; line-height:1.7;">Ribuan plakat telah kami kerjakan dengan penuh dedikasi. Lihat hasil karya terbaik kami dan jadikan inspirasi pesanan Anda.</p>
        </div>

        <!-- Filter Kategori -->
        <div style="display:flex; flex-wrap:wrap; gap:8px; justify-content:center; margin-bottom:32px;">
            <a href="<?php echo esc_url( get_post_type_archive_link('produk') ); ?>" class="cat-filter-item active" style="padding:8px 20px; background:var(--primary); color:#fff; border-radius:20px; text-decoration:none; font-weight:700; font-size:0.85rem;">Semua</a>
            <?php
            $cats = get_terms(['taxonomy'=>'kategori_produk','hide_empty'=>true]);
            if (!is_wp_error($cats)) foreach($cats as $cat) {
                echo '<a href="' . esc_url(get_term_link($cat)) . '" class="cat-filter-item" style="padding:8px 20px; background:var(--bg2); color:var(--text); border:1.5px solid var(--border); border-radius:20px; text-decoration:none; font-weight:700; font-size:0.85rem;">' . esc_html($cat->name) . '</a>';
            }
            ?>
        </div>

        <!-- Portfolio Grid -->
        <div class="portfolio-grid portfolio-section" style="padding:0 0 60px;">
            <?php if ($portfolio_query->have_posts()) : while ($portfolio_query->have_posts()) : $portfolio_query->the_post(); ?>
                <?php
                $thumb = get_the_post_thumbnail_url(null, 'large');
                if (!$thumb) continue;
                $gallery_ids = get_post_meta(get_the_ID(), '_produk_gallery', true);
                $count = $gallery_ids ? count(explode(',', $gallery_ids)) + 1 : 1;
                $title = get_the_title();
                ?>
                <a href="<?php the_permalink(); ?>" class="portfolio-item" title="<?php echo esc_attr($title); ?>">
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy">
                    <div class="portfolio-overlay">
                        <div class="portfolio-overlay-text">
                            <?php echo esc_html($title); ?>
                            <?php if ($count > 1) echo ' <span style="opacity:.75; font-size:0.78rem;">+' . ($count-1) . ' foto</span>'; ?>
                        </div>
                    </div>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p style="grid-column:1/-1; text-align:center; color:var(--text2); padding:40px 0;">Belum ada portofolio. Tambahkan produk dengan foto terlebih dahulu.</p>
            <?php endif; ?>
        </div>

        <!-- CTA -->
        <div style="text-align:center; margin-bottom:60px;">
            <a href="https://wa.me/<?php echo esc_attr($wa_number); ?>?text=<?php echo urlencode('Halo! Saya tertarik memesan plakat seperti di portofolio. Bisa konsultasi dulu?'); ?>"
               target="_blank" rel="noopener"
               style="display:inline-flex; align-items:center; gap:10px; background:var(--primary); color:#fff; font-weight:900; padding:14px 32px; border-radius:50px; text-decoration:none; font-size:1rem; box-shadow:0 8px 24px rgba(var(--primary-rgb),0.35);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2z"/></svg>
                Konsultasi & Pesan Sekarang
            </a>
        </div>
    </div>
</main>

<!-- WA Floating Button -->
<a href="https://wa.me/<?php echo esc_attr($wa_number); ?>" target="_blank" rel="noopener" class="wa-float-btn" aria-label="Chat WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2z"/></svg>
    <span class="wa-float-tooltip">Chat WhatsApp</span>
</a>

<?php get_template_part( 'template-parts/catalog-popup' ); ?>
<?php get_footer(); ?>
