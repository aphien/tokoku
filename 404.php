<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package TokoKu
 */

get_header(); ?>

<main id="main-content" class="site-main error-404">
    <div class="container">
        
        <div class="error-container">
            <h1 class="error-code">404</h1>
            <h2 class="error-title">Halaman Tidak Ditemukan</h2>
            <p class="error-text">Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary btn-lg">Kembali ke Beranda</a>
        </div>

    </div>
</main>

<style>
.error-404 { display: flex; align-items: center; justify-content: center; text-align: center; min-height: 70vh; }
.error-code { font-size: 10rem; font-weight: 900; color: var(--accent); opacity: 0.1; line-height: 1; }
.error-title { font-size: 3rem; margin-top: -50px; margin-bottom: 20px; font-weight: 800; }
.error-text { font-size: 1.25rem; color: var(--text-secondary); margin-bottom: 40px; }
</style>

<?php get_footer(); ?>
