<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The template for displaying product archives
 *
 * @package TokoKu
 */

get_header(); 

global $wp_query;
$total_products = $wp_query->found_posts;
$current_term   = get_queried_object();
$is_cat_archive = is_tax( 'kategori_produk' );
$page_title     = $is_cat_archive ? single_term_title( '', false ) : __( 'Katalog Semua Produk', 'tokoku' );
$page_desc      = $is_cat_archive && ! empty( $current_term->description ) ? $current_term->description : __( 'Temukan koleksi plakat akrilik, kayu, kristal, piala & souvenir berkualitas dengan pengerjaan presisi dan pengiriman cepat ke seluruh Indonesia.', 'tokoku' );
$all_cats       = get_terms( array( 'taxonomy' => 'kategori_produk', 'hide_empty' => false ) );
?>

<main id="main-content" class="site-main product-archive">
    <div class="container">
        
        <!-- Breadcrumbs -->
        <nav class="archive-breadcrumbs" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
            <span class="sep">/</span>
            <?php if ( $is_cat_archive ) : ?>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>">Katalog Produk</a>
                <span class="sep">/</span>
                <span class="current"><?php echo esc_html( $page_title ); ?></span>
            <?php else : ?>
                <span class="current">Katalog Produk</span>
            <?php endif; ?>
        </nav>

        <!-- Archive Hero / Header -->
        <header class="archive-header-hero">
            <div class="archive-hero-content">
                <div class="archive-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    <span>Koleksi Percetakan & Plakat</span>
                </div>
                <h1 class="archive-main-title"><?php echo esc_html( $page_title ); ?></h1>
                <p class="archive-subtitle"><?php echo esc_html( $page_desc ); ?></p>
            </div>
            <div class="archive-count-badge">
                <span class="count-num"><?php echo esc_html( $total_products ); ?></span>
                <span class="count-label">Produk Tersedia</span>
            </div>
        </header>

        <!-- Category Horizontal Filter Pill Bar & Sort -->
        <div class="archive-filter-bar">
            <div class="category-pill-bar" role="tablist" aria-label="<?php esc_attr_e( 'Filter Kategori', 'tokoku' ); ?>">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>" 
                   class="category-pill <?php echo ! $is_cat_archive ? 'active' : ''; ?>" 
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
                    <?php foreach ( $all_cats as $cat ) : 
                        $is_active = ( $is_cat_archive && isset( $current_term->term_id ) && $current_term->term_id == $cat->term_id );
                    ?>
                        <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" 
                           class="category-pill <?php echo $is_active ? 'active' : ''; ?>" 
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
                <form action="<?php echo esc_url( $is_cat_archive ? get_term_link( $current_term ) : get_post_type_archive_link( 'produk' ) ); ?>" method="get" class="sort-form">
                    <svg class="sort-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"></line>
                        <line x1="6" y1="12" x2="18" y2="12"></line>
                        <line x1="8" y1="18" x2="16" y2="18"></line>
                    </svg>
                    <?php if ( ! $is_cat_archive ) : ?>
                        <input type="hidden" name="post_type" value="produk">
                    <?php endif; ?>
                    <select name="orderby" onchange="this.form.submit()" aria-label="Urutkan Produk">
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

        <!-- Main Catalog Layout -->
        <div class="archive-container">
            <!-- Sidebar Desktop Filters -->
            <aside class="archive-sidebar">
                <div class="sidebar-widget">
                    <h4 class="widget-title">Kategori Produk</h4>
                    <ul class="category-list">
                        <li class="<?php echo ! $is_cat_archive ? 'current-cat' : ''; ?>">
                            <a href="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>">
                                <span class="cat-link-inner">
                                    <span class="cat-bullet"></span>
                                    Semua Kategori
                                </span>
                                <span class="count"><?php echo esc_html( wp_count_posts( 'produk' )->publish ?? 0 ); ?></span>
                            </a>
                        </li>
                        <?php
                        if ( ! empty( $all_cats ) && ! is_wp_error( $all_cats ) ) {
                            foreach ( $all_cats as $cat ) {
                                $is_curr = ( $is_cat_archive && isset( $current_term->term_id ) && $current_term->term_id == $cat->term_id );
                                $icon_id = get_term_meta( $cat->term_id, 'tokoku_kategori_icon', true );
                                $icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'thumbnail' ) : '';
                                
                                echo '<li class="' . ( $is_curr ? 'current-cat' : '' ) . '">';
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
                    <h5>Butuh Desain Custom?</h5>
                    <p>Konsultasikan kebutuhan plakat & souvenir perusahaan Anda langsung dengan tim kami via WhatsApp.</p>
                    <a href="https://wa.me/<?php echo esc_attr( get_theme_mod( 'tokoku_wa_number', '6281234567890' ) ); ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm btn-block">
                        Chat Sekarang
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
                        <h3>Produk Tidak Ditemukan</h3>
                        <p>Belum ada produk pada kategori ini. Silakan jelajahi kategori lain atau hubungi admin untuk pemesanan khusus.</p>
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
