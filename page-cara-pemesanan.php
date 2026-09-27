<?php
/**
 * Template Name: Cara Pemesanan
 * Template Post Type: page
 *
 * @package TokoKu
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
$wa_number = get_theme_mod( 'tokoku_wa_number', '6281234567890' );
?>

<main id="main-content" class="site-main">
    <div class="container">
        <!-- Hero -->
        <div style="text-align:center; padding: 60px 0 40px;">
            <span style="display:inline-block; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color:#fff; font-size:0.8rem; font-weight:800; padding:5px 16px; border-radius:20px; letter-spacing:0.5px; margin-bottom:16px; text-transform:uppercase;">Panduan Lengkap</span>
            <h1 style="font-size:clamp(1.8rem,4vw,2.8rem); font-weight:900; color:var(--text); margin-bottom:16px;">Cara Pemesanan Plakat</h1>
            <p style="font-size:1rem; color:var(--text2); max-width:560px; margin:0 auto; line-height:1.7;">Proses pemesanan mudah, cepat, dan transparan. Dari konsultasi hingga pengiriman — kami siap membantu setiap langkahnya.</p>
        </div>

        <!-- Steps -->
        <div class="order-steps how-to-order-section" style="padding:0 0 60px;">
            <?php
            $steps = [
                ['num'=>'1','icon'=>'💬','title'=>'Konsultasi & Pilih Produk','desc'=>'Hubungi kami via WhatsApp atau browse katalog. Ceritakan kebutuhan Anda — jenis plakat, ukuran, jumlah, dan deadline.'],
                ['num'=>'2','icon'=>'🎨','title'=>'Kirim Desain / Briefing','desc'=>'Kirimkan file desain (CDR/AI/PNG/JPG) atau brief desain. Tim kami siap membantu membuat desain dari nol secara gratis.'],
                ['num'=>'3','icon'=>'✅','title'=>'Konfirmasi Preview Desain','desc'=>'Kami kirimkan preview desain digital untuk disetujui. Revisi gratis hingga Anda puas sebelum proses produksi dimulai.'],
                ['num'=>'4','icon'=>'⚙️','title'=>'Proses Produksi','desc'=>'Setelah desain disetujui dan DP lunas, produksi dimulai. Estimasi 2–5 hari kerja tergantung jumlah dan kompleksitas.'],
                ['num'=>'5','icon'=>'📦','title'=>'Pengemasan Premium','desc'=>'Setiap plakat dikemas dengan bubble wrap dan kardus tebal. Kami pastikan produk sampai dalam kondisi sempurna.'],
                ['num'=>'6','icon'=>'🚚','title'=>'Pengiriman ke Seluruh Indonesia','desc'=>'Kami kirim via JNE, J&T, Sicepat, atau ekspedisi pilihan Anda. Tersedia juga pengambilan langsung (COD area tertentu).'],
            ];
            foreach ($steps as $step) {
                echo '<div class="order-step">';
                echo '<div class="order-step-num">' . esc_html($step['num']) . '</div>';
                echo '<span class="order-step-icon">' . $step['icon'] . '</span>';
                echo '<h3>' . esc_html($step['title']) . '</h3>';
                echo '<p>' . esc_html($step['desc']) . '</p>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- CTA -->
        <div style="text-align:center; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius:20px; padding:48px 32px; margin-bottom:60px;">
            <h2 style="color:#fff; font-size:1.8rem; font-weight:900; margin-bottom:12px;">Siap Memesan Sekarang?</h2>
            <p style="color:rgba(255,255,255,0.85); margin-bottom:24px; font-size:1rem;">Tim kami siap merespon dalam hitungan menit, 7 hari seminggu.</p>
            <a href="https://wa.me/<?php echo esc_attr($wa_number); ?>?text=<?php echo urlencode('Halo! Saya ingin memesan plakat. Bisa bantu saya?'); ?>"
               target="_blank" rel="noopener"
               style="display:inline-flex; align-items:center; gap:10px; background:#fff; color:var(--primary); font-weight:900; padding:14px 32px; border-radius:50px; text-decoration:none; font-size:1rem; box-shadow:0 8px 24px rgba(0,0,0,0.2); transition: transform 0.2s;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2z"/></svg>
                Mulai Order via WhatsApp
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
