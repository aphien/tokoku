<?php
/**
 * The template for displaying product archives
 *
 * @package TokoKu
 */

get_header(); ?>

<main id="main-content" class="site-main product-archive">
    <div class="container">
        
        <header class="archive-header">
            <h1 class="archive-title"><?php the_archive_title(); ?></h1>
            
            <div class="archive-filters">
                <div class="filters-flex">
                    <!-- Category Dropdown -->
                    <div class="category-filter-dropdown">
                        <select onchange="location = this.value;">
                            <option value="<?php echo esc_url( get_post_type_archive_link( 'produk' ) ); ?>"><?php _e( 'Semua Kategori', 'tokoku' ); ?></option>
                            <?php
                            $current_cat = get_queried_object();
                            $all_cats = get_terms( array( 'taxonomy' => 'kategori_produk' ) );
                            foreach ( $all_cats as $cat ) {
                                $selected = ( isset($current_cat->term_id) && $current_cat->term_id == $cat->term_id ) ? 'selected' : '';
                                echo '<option value="' . esc_url( get_term_link( $cat ) ) . '" ' . $selected . '>' . esc_html( $cat->name ) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Sort Dropdown -->
                    <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="sort-form">
                        <select name="orderby" onchange="this.form.submit()">
                            <?php
                            $orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'terbaru';
                            $options = array(
                                'terbaru' => 'Terbaru',
                                'termurah' => 'Termurah',
                                'termahal' => 'Termahal',
                                'nama' => 'Nama (A-Z)',
                            );
                            foreach ( $options as $val => $label ) {
                                echo '<option value="' . esc_attr( $val ) . '" ' . selected( $orderby, $val, false ) . '>' . esc_html( $label ) . '</option>';
                            }
                            ?>
                        </select>
                    </form>
                </div>
            </div>
        </header>

        <div class="archive-container">
            <aside class="archive-sidebar">
                <div class="sidebar-widget">
                    <h4 class="widget-title">Kategori</h4>
                    <ul class="category-list">
                        <?php
                        $categories = get_terms( array( 'taxonomy' => 'kategori_produk' ) );
                        foreach ( $categories as $cat ) {
                            echo '<li><a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . ' <span class="count">(' . $cat->count . ')</span></a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </aside>

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
                            'prev_text' => '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                            'next_text' => '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                            'mid_size'  => 2,
                        ) ); 
                        ?>
                    </div>
                <?php else : ?>
                    <p>Produk tidak ditemukan.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<style>
.product-archive { padding: 40px 0 80px; background: var(--bg); }
.archive-header { 
    margin-bottom: 40px; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    padding-bottom: 25px;
    border-bottom: 1.5px solid var(--border);
}
.filters-flex { display: flex; gap: 10px; }

/* Category Dropdown Styling */
.category-filter-dropdown select,
.sort-form select {
    background: var(--bg2);
    color: var(--text);
    border: 1.5px solid var(--border);
    padding: 10px 35px 10px 15px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    transition: var(--ease);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
}
.category-filter-dropdown { display: none; }

@media (max-width: 992px) {
    .archive-header { flex-direction: column; align-items: stretch; gap: 20px; }
    .filters-flex { display: grid; grid-template-columns: 1fr 1fr; width: 100%; }
    .category-filter-dropdown { display: block; }
    .category-filter-dropdown select, .sort-form select { width: 100%; }
    .archive-sidebar { display: none; }
}

.archive-title { font-size: 2.2rem; font-weight: 800; color: var(--text); margin-bottom: 0; }

/* Sort Form Styling */
.sort-form select {
    background: var(--bg2);
    color: var(--text);
    border: 1.5px solid var(--border);
    padding: 10px 35px 10px 15px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    transition: var(--ease);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
}
body.theme-dark .sort-form select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23f8fafc' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
}
.sort-form select:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1); }

/* Sidebar Categories */
.archive-container { display: grid; grid-template-columns: 280px 1fr; gap: 40px; }
.sidebar-widget { 
    background: var(--bg2); 
    padding: 30px; 
    border-radius: 20px; 
    margin-bottom: 30px; 
    border: 1px solid var(--border);
    box-shadow: 0 4px 15px var(--shadow);
}
.widget-title { 
    font-size: 1.1rem; 
    font-weight: 800; 
    margin-bottom: 20px; 
    color: var(--text);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.widget-title::before { content: ''; width: 4px; height: 16px; background: var(--primary); border-radius: 2px; }

.category-list { display: flex; flex-direction: column; gap: 8px; }
.category-list li { margin-bottom: 0; }
.category-list a { 
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    padding: 12px 16px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 12px;
    color: var(--text2); 
    font-weight: 600;
    font-size: 0.95rem;
    transition: var(--ease);
}
.category-list a:hover { 
    color: var(--primary); 
    border-color: var(--primary); 
    transform: translateX(5px);
    background: var(--bg2);
}
.category-list li.current-cat a {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
}
.count { 
    background: var(--bg2);
    color: var(--text2);
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}
.category-list li.current-cat a .count { background: rgba(255,255,255,0.2); color: #fff; }

@media (max-width: 992px) {
    .archive-header { flex-direction: column; align-items: flex-start; gap: 20px; }
    .archive-container { grid-template-columns: 1fr; }
    .archive-sidebar { order: 2; margin-top: 20px; }
    .sidebar-widget { padding: 20px; }
}

/* Pagination Styling */
.pagination { 
    margin-top: 60px; 
    display: flex; 
    justify-content: center; 
}
.nav-links { display: flex; align-items: center; gap: 10px; }
.page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 45px;
    height: 45px;
    padding: 0 10px;
    background: var(--bg2);
    border: 1px solid var(--border);
    border-radius: 50%;
    color: var(--text2);
    font-weight: 700;
    font-size: 0.95rem;
    transition: var(--ease);
}
.page-numbers.current {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
}
.page-numbers:not(.dots):hover {
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-2px);
}
.page-numbers.current:hover { color: #fff; }
.page-numbers.prev, .page-numbers.next { border-radius: 12px; }
.page-numbers svg { display: block; }
</style>

<?php get_footer(); ?>
