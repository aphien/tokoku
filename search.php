<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The template for displaying search results pages
 *
 * @package TokoKu
 */

get_header(); 

global $wp_query;
$total_found       = $wp_query->found_posts;
$is_product_search = isset( $_GET['post_type'] ) && $_GET['post_type'] === 'produk';
$search_query      = get_search_query();
$all_cats          = get_terms( array( 'taxonomy' => 'kategori_produk', 'hide_empty' => false ) );
?>

<main id="main-content" class="site-main product-archive search-page">
    <div class="container">
        
        <!-- Breadcrumbs -->
        <nav class="archive-breadcrumbs" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
            <span class="sep">/</span>
            <span class="current">Hasil Pencarian</span>
        </nav>

        <!-- Search Header Hero -->
        <header class="archive-header-hero">
            <div class="archive-hero-content">
                <div class="archive-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Hasil Pencarian</span>
                </div>
                <h1 class="archive-main-title">
                    Pencarian: &ldquo;<span class="search-highlight"><?php echo esc_html( $search_query ); ?></span>&rdquo;
                </h1>
                <p class="archive-subtitle">
                    <?php if ( $total_found > 0 ) : ?>
                        Menampilkan <?php echo esc_html( $total_found ); ?> produk yang sesuai dengan kata kunci Anda.
                    <?php else : ?>
                        Tidak ditemukan produk dengan kata kunci tersebut. Silakan coba kata kunci lain atau pilih kategori di bawah.
                    <?php endif; ?>
                </p>
            </div>
            <div class="archive-count-badge">
                <span class="count-num"><?php echo esc_html( $total_found ); ?></span>
                <span class="count-label">Hasil Ditemukan</span>
            </div>
        </header>

        <!-- Category Horizontal Filter Pill Bar & Sort -->
        <div class="archive-filter-bar">
            <div class="category-pill-bar" role="tablist" aria-label="<?php esc_attr_e( 'Filter Kategori', 'tokoku' ); ?>">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>" 
                   class="category-pill" 
                   title="<?php esc_attr_e( 'Semua Produk', 'tokoku' ); ?>" 
                   aria-label="<?php esc_attr_e( 'Semua Produk', 'tokoku' ); ?>">
                    <span class="pill-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                        </svg>
                    </span>
                    <span class="pill-text"><?php esc_html_e( 'Semua Produk', 'tokoku' ); ?></span>
                </a>
                <?php if ( ! empty( $all_cats ) && ! is_wp_error( $all_cats ) ) : ?>
                    <?php foreach ( $all_cats as $cat ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" 
                           class="category-pill" 
                           title="<?php echo esc_attr( $cat->name ); ?>" 
                           aria-label="<?php echo esc_attr( $cat->name ); ?>">
                            <span class="pill-icon">
                                <?php echo tokoku_get_category_icon_html( $cat->term_id, 20 ); ?>
                            </span>
                            <span class="pill-text"><?php echo esc_html( $cat->name ); ?></span>
                            <?php if ( $cat->count > 0 ) : ?>
                                <span class="pill-count"><?php echo esc_html( $cat->count ); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Sort Dropdown -->
            <div class="archive-sort-wrap">
                <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="sort-form">
                    <svg class="sort-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"></line>
                        <line x1="6" y1="12" x2="18" y2="12"></line>
                        <line x1="8" y1="18" x2="16" y2="18"></line>
                    </svg>
                    <input type="hidden" name="s" value="<?php echo esc_attr( $search_query ); ?>">
                    <input type="hidden" name="post_type" value="produk">
                    <select name="orderby" onchange="this.form.submit()" aria-label="Urutkan Hasil">
                        <?php
                        $orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'terbaru';
                        $options = array(
                            'terbaru'  => 'Urutkan: Terbaru',
                            'termurah' => 'Harga: Termurah',
                            'termahal' => 'Harga: Termahal',
                            'nama'     => 'Nama Produk (A-Z)',
                        );
                        foreach ( $options as $val => $label ) {
                            echo '<option value="' . esc_attr( $val ) . '" ' . selected( $orderby, $val, false ) . '>' . esc_html( $label ) . '</option>';
                        }
                        ?>
                    </select>
                </form>
            </div>
        </div>

        <!-- Main Search Results Layout -->
        <div class="archive-container">
            <!-- Sidebar Desktop Filters -->
            <aside class="archive-sidebar">
                <div class="sidebar-widget">
                    <h4 class="widget-title">Kategori Produk</h4>
                    <ul class="category-list">
                        <li>
                            <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>">
                                <span class="cat-link-inner">
                                    <span class="cat-bullet"></span>
                                    Semua Kategori
                                </span>
                                <span class="count"><?php echo esc_html( wp_count_posts( 'produk' )->publish ); ?></span>
                            </a>
                        </li>
                        <?php
                        if ( ! empty( $all_cats ) && ! is_wp_error( $all_cats ) ) {
                            foreach ( $all_cats as $cat ) {
                                $icon_id = get_term_meta( $cat->term_id, 'tokoku_kategori_icon', true );
                                $icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'thumbnail' ) : '';
                                
                                echo '<li>';
                                echo '<a href="' . esc_url( get_term_link( $cat ) ) . '">';
                                echo '<span class="cat-link-inner">';
                                if ( $icon_url ) {
                                    echo '<img src="' . esc_url( $icon_url ) . '" class="cat-link-icon" style="width:20px; height:20px; object-fit:contain; margin-right:10px; border-radius:4px;" alt="">';
                                } else {
                                    echo '<span class="cat-bullet"></span>';
                                }
                                echo esc_html( $cat->name );
                                echo '</span>';
                                echo '<span class="count">' . esc_html( $cat->count ) . '</span>';
                                echo '</a>';
                                echo '</li>';
                            }
                        }
                        ?>
                    </ul>
                </div>

                <!-- Assistance Card -->
                <div class="sidebar-help-card">
                    <div class="help-card-icon">
                        <?php echo tokoku_icon( 'whatsapp', 24, '', 'style="color: #25D366;"' ); ?>
                    </div>
                    <h5>Tidak Menemukan Produk?</h5>
                    <p>Tim kami dapat membuatkan desain kustom plakat, piala, atau medali sesuai konsep dan anggaran Anda.</p>
                    <a href="https://wa.me/<?php echo esc_attr( get_theme_mod( 'tokoku_wa_number', '6281234567890' ) ); ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm btn-block">
                        Konsultasi Gratis
                    </a>
                </div>
            </aside>

            <!-- Product Grid -->
            <div class="archive-content">
                <?php if ( have_posts() ) : ?>
                    <div class="product-grid">
                        <?php
                        while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/product-card' );
                        endwhile;
                        ?>
                    </div>
                    
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
                    <div class="no-products-found">
                        <div class="empty-icon">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                        </div>
                        <h3>Tidak Ada Produk yang Cocok</h3>
                        <p>Kami tidak menemukan produk dengan kata kunci &ldquo;<strong><?php echo esc_html( $search_query ); ?></strong>&rdquo;. Silakan cek ejaan kata kunci atau telusuri kategori produk kami.</p>
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>" class="btn btn-primary">
                            Lihat Semua Produk
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<?php get_footer(); ?>
