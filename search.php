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
            <div class="category-pill-bar">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>" class="category-pill">
                    <span class="pill-icon">✨</span>
                    <span class="pill-text">Semua Produk</span>
                </a>
                <?php if ( ! empty( $all_cats ) && ! is_wp_error( $all_cats ) ) : ?>
                    <?php foreach ( $all_cats as $cat ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="category-pill">
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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#25D366"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/></svg>
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
