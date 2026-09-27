<?php
/**
 * Template Name: Cara Pemesanan
 * Template Post Type: page
 * @package TokoKu
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
$wa_number = get_theme_mod( 'tokoku_wa_number', '6281234567890' );
$site_name = get_bloginfo( 'name' );
?>

<main id="main-content" class="site-main how-to-order-page">

    <!-- ═══ HERO SECTION ═══ -->
    <section class="hto-hero">
        <div class="hto-hero-bg-decor">
            <div class="hto-hero-blob hto-hero-blob--1"></div>
            <div class="hto-hero-blob hto-hero-blob--2"></div>
        </div>
        <div class="container hto-hero-inner">
            <div class="hto-hero-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Proses Mudah & Terpercaya
            </div>
            <h1 class="hto-hero-title">Cara Pemesanan <br><span class="hto-hero-title-accent">Plakat Custom</span></h1>
            <p class="hto-hero-desc">Dari konsultasi desain hingga pengiriman ke tangan Anda — kami hadirkan proses yang mudah, transparan, dan memuaskan dalam 6 langkah sederhana.</p>
            <div class="hto-hero-stats">
                <div class="hto-stat">
                    <strong>5.000+</strong>
                    <span>Pesanan Selesai</span>
                </div>
                <div class="hto-stat-div"></div>
                <div class="hto-stat">
                    <strong>4.9 ⭐</strong>
                    <span>Rating Pelanggan</span>
                </div>
                <div class="hto-stat-div"></div>
                <div class="hto-stat">
                    <strong>2–5 Hari</strong>
                    <span>Estimasi Selesai</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ STEPS SECTION ═══ -->
    <section class="hto-steps-section">
        <div class="container">
            <div class="hto-section-label">Alur Pemesanan</div>
            <h2 class="hto-section-title">6 Langkah Mudah Pesan Plakat</h2>
            <p class="hto-section-desc">Setiap langkah dirancang untuk memastikan kepuasan Anda dari awal hingga akhir.</p>

            <div class="hto-steps-grid">

                <?php
                $steps = [
                    [
                        'num'   => '01',
                        'emoji' => '💬',
                        'color' => '#6366f1',
                        'title' => 'Konsultasi & Pilih Produk',
                        'desc'  => 'Hubungi kami via WhatsApp atau browse katalog produk. Ceritakan kebutuhan Anda: jenis plakat, ukuran, jumlah, dan batas waktu pengerjaan.',
                        'tips'  => 'Tips: Siapkan referensi gambar yang Anda suka untuk mempercepat diskusi.',
                    ],
                    [
                        'num'   => '02',
                        'emoji' => '🎨',
                        'color' => '#ec4899',
                        'title' => 'Kirim Desain / Brief Kreatif',
                        'desc'  => 'Kirimkan file desain (CDR/AI/PNG/JPG) atau cukup jelaskan konsep Anda. Tim desainer kami siap membantu membuat desain dari nol secara GRATIS.',
                        'tips'  => 'Tips: Resolusi foto minimal 300 DPI untuk hasil cetak terbaik.',
                    ],
                    [
                        'num'   => '03',
                        'emoji' => '✅',
                        'color' => '#10b981',
                        'title' => 'Setujui Preview Desain',
                        'desc'  => 'Kami kirimkan mockup digital preview desain Anda. Revisi gratis tanpa batas hingga Anda 100% puas, sebelum proses produksi dimulai.',
                        'tips'  => 'Tips: Periksa detail tulisan, ejaan, dan posisi logo dengan seksama.',
                    ],
                    [
                        'num'   => '04',
                        'emoji' => '💳',
                        'color' => '#f59e0b',
                        'title' => 'Konfirmasi & Bayar DP',
                        'desc'  => 'Setelah desain disetujui, lakukan pembayaran uang muka (DP) 50%. Kami menerima transfer bank, QRIS, GoPay, OVO, dan Dana.',
                        'tips'  => 'Tips: Kirim bukti pembayaran via WhatsApp agar produksi segera dimulai.',
                    ],
                    [
                        'num'   => '05',
                        'emoji' => '⚙️',
                        'color' => '#8b5cf6',
                        'title' => 'Proses Produksi',
                        'desc'  => 'Tim pengrajin kami mengerjakan pesanan Anda dengan presisi tinggi. Estimasi pengerjaan 2–5 hari kerja tergantung jumlah dan kompleksitas produk.',
                        'tips'  => 'Tips: Anda bisa meminta foto progres produksi kapan saja.',
                    ],
                    [
                        'num'   => '06',
                        'emoji' => '🚚',
                        'color' => '#0ea5e9',
                        'title' => 'Kemas & Kirim ke Seluruh Indonesia',
                        'desc'  => 'Setiap produk dikemas dengan bubble wrap berlapis dan kardus tebal. Dikirim via JNE, J&T, Sicepat, atau ekspedisi pilihan Anda ke seluruh Indonesia.',
                        'tips'  => 'Tips: Pelunasan sisa pembayaran dilakukan sebelum pengiriman.',
                    ],
                ];
                foreach ($steps as $i => $step) : ?>
                <div class="hto-step-card" style="--step-color: <?php echo esc_attr($step['color']); ?>;">
                    <div class="hto-step-num-wrap">
                        <span class="hto-step-num"><?php echo esc_html($step['num']); ?></span>
                        <span class="hto-step-emoji"><?php echo $step['emoji']; ?></span>
                    </div>
                    <div class="hto-step-connector" aria-hidden="true"></div>
                    <div class="hto-step-body">
                        <h3 class="hto-step-title"><?php echo esc_html($step['title']); ?></h3>
                        <p class="hto-step-desc"><?php echo esc_html($step['desc']); ?></p>
                        <div class="hto-step-tip">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?php echo esc_html($step['tips']); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <!-- ═══ PAYMENT METHODS ═══ -->
    <section class="hto-payment-section">
        <div class="container">
            <div class="hto-payment-card">
                <div class="hto-payment-left">
                    <h3>💳 Metode Pembayaran</h3>
                    <p>Kami menerima berbagai metode pembayaran untuk kenyamanan Anda</p>
                    <div class="hto-payment-methods">
                        <?php
                        $methods = [
                            ['icon'=>'🏦','label'=>'Transfer Bank (BCA, BRI, BNI, Mandiri)'],
                            ['icon'=>'📱','label'=>'QRIS — Semua E-Wallet'],
                            ['icon'=>'💚','label'=>'GoPay / OVO / Dana / ShopeePay'],
                            ['icon'=>'💵','label'=>'COD (Area Tertentu)'],
                        ];
                        foreach ($methods as $m) {
                            echo '<div class="hto-payment-method"><span>' . $m['icon'] . '</span>' . esc_html($m['label']) . '</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="hto-payment-right">
                    <div class="hto-dp-info">
                        <div class="hto-dp-num">50%</div>
                        <div class="hto-dp-label">Uang Muka (DP)</div>
                        <div class="hto-dp-desc">Sisa 50% dilunasi sebelum pengiriman</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ FAQ ═══ -->
    <section class="hto-faq-section">
        <div class="container">
            <div class="hto-section-label">Pertanyaan Umum</div>
            <h2 class="hto-section-title">Sering Ditanyakan</h2>
            <div class="hto-faq-list">
                <?php
                $faqs = [
                    ['q'=>'Berapa lama waktu pengerjaan?','a'=>'Estimasi 2–5 hari kerja setelah desain disetujui dan DP lunas. Untuk pesanan urgent/deadline mepet, hubungi kami lebih dulu — kami akan usahakan semaksimal mungkin.'],
                    ['q'=>'Apakah bisa pesan tanpa desain?','a'=>'Tentu! Tim desainer kami siap membuatkan desain dari nol secara GRATIS. Cukup ceritakan konsep, tema acara, dan informasi yang ingin ditampilkan.'],
                    ['q'=>'Berapa minimal pemesanan?','a'=>'Untuk plakat akrilik, minimal 1 pcs. Untuk beberapa produk tertentu seperti medali, minimal bisa berbeda — hubungi kami untuk konfirmasi.'],
                    ['q'=>'Apakah ada garansi jika barang rusak saat pengiriman?','a'=>'Ya! Kami menjamin penggantian produk baru jika terjadi kerusakan akibat proses pengiriman. Simpan foto kondisi paket sebelum dibuka sebagai bukti klaim.'],
                    ['q'=>'Bagaimana cara mengirim desain?','a'=>'Kirim langsung via WhatsApp, email, atau Google Drive. Format yang diterima: CDR, AI, PSD, PNG, JPG, atau PDF dengan resolusi minimal 300 DPI.'],
                    ['q'=>'Apakah ada diskon untuk pemesanan dalam jumlah banyak?','a'=>'Ya! Kami memberikan harga spesial grosir mulai dari 10 pcs ke atas. Hubungi kami untuk penawaran harga khusus sesuai jumlah pesanan Anda.'],
                ];
                foreach ($faqs as $i => $faq) :
                ?>
                <div class="hto-faq-item" id="faq-<?php echo $i; ?>">
                    <button class="hto-faq-q" aria-expanded="false" aria-controls="faq-ans-<?php echo $i; ?>">
                        <span><?php echo esc_html($faq['q']); ?></span>
                        <svg class="hto-faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="hto-faq-a" id="faq-ans-<?php echo $i; ?>" hidden>
                        <p><?php echo esc_html($faq['a']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══ CTA SECTION ═══ -->
    <section class="hto-cta-section">
        <div class="container">
            <div class="hto-cta-card">
                <div class="hto-cta-decor" aria-hidden="true">
                    <span>🏆</span><span>🎖️</span><span>🥇</span><span>📦</span><span>✨</span>
                </div>
                <h2 class="hto-cta-title">Siap Memesan Plakat Impian Anda?</h2>
                <p class="hto-cta-desc">Tim kami siap merespons dalam hitungan menit, 7 hari seminggu. Konsultasi gratis, tanpa komitmen.</p>
                <div class="hto-cta-actions">
                    <a href="https://wa.me/<?php echo esc_attr($wa_number); ?>?text=<?php echo urlencode('Halo ' . $site_name . '! Saya ingin memesan plakat. Bisa bantu saya?'); ?>"
                       target="_blank" rel="noopener" class="hto-cta-btn-primary">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2z"/></svg>
                        Mulai Order via WhatsApp
                    </a>
                    <a href="<?php echo esc_url( get_post_type_archive_link('produk') ); ?>" class="hto-cta-btn-secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Lihat Katalog Produk
                    </a>
                </div>
                <p class="hto-cta-note">⚡ Rata-rata respon < 5 menit di jam kerja</p>
            </div>
        </div>
    </section>

</main>

<!-- WA Floating Button -->
<a href="https://wa.me/<?php echo esc_attr($wa_number); ?>" target="_blank" rel="noopener" class="wa-float-btn" aria-label="Chat WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2z"/></svg>
    <span class="wa-float-tooltip">Chat WhatsApp</span>
</a>

<?php get_template_part( 'template-parts/catalog-popup' ); ?>

<style>
/* ─── How To Order Page ─── */
.how-to-order-page { overflow-x: hidden; }

/* Hero */
.hto-hero {
    position: relative;
    padding: 80px 0 60px;
    text-align: center;
    overflow: hidden;
}
.hto-hero-bg-decor { position: absolute; inset: 0; z-index: 0; pointer-events: none; }
.hto-hero-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.18;
}
.hto-hero-blob--1 {
    width: 500px; height: 500px;
    background: var(--primary);
    top: -100px; left: -100px;
}
.hto-hero-blob--2 {
    width: 400px; height: 400px;
    background: #ec4899;
    bottom: -80px; right: -80px;
}
html.theme-dark .hto-hero-blob { opacity: 0.12; }
.hto-hero-inner { position: relative; z-index: 1; max-width: 700px; margin: 0 auto; }
.hto-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(var(--primary-rgb), 0.12);
    color: var(--primary);
    border: 1.5px solid rgba(var(--primary-rgb), 0.25);
    padding: 6px 18px;
    border-radius: 30px;
    font-size: 0.82rem;
    font-weight: 700;
    margin-bottom: 24px;
}
.hto-hero-title {
    font-size: clamp(2rem, 5vw, 3.2rem);
    font-weight: 900;
    color: var(--text);
    line-height: 1.15;
    margin-bottom: 20px;
}
.hto-hero-title-accent {
    background: linear-gradient(135deg, var(--primary), #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.hto-hero-desc {
    font-size: 1.05rem;
    color: var(--text2);
    line-height: 1.7;
    max-width: 560px;
    margin: 0 auto 36px;
}
.hto-hero-stats {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 28px;
    flex-wrap: wrap;
}
.hto-stat { text-align: center; }
.hto-stat strong {
    display: block;
    font-size: 1.4rem;
    font-weight: 900;
    color: var(--text);
    line-height: 1;
    margin-bottom: 4px;
}
.hto-stat span { font-size: 0.8rem; color: var(--text2); font-weight: 600; }
.hto-stat-div { width: 1.5px; height: 36px; background: var(--border); }

/* Steps Section */
.hto-steps-section { padding: 80px 0; }
.hto-section-label {
    display: inline-block;
    background: rgba(var(--primary-rgb), 0.1);
    color: var(--primary);
    font-size: 0.75rem;
    font-weight: 800;
    padding: 4px 14px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 14px;
}
.hto-section-title {
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    font-weight: 900;
    color: var(--text);
    margin-bottom: 12px;
}
.hto-section-desc {
    font-size: 1rem;
    color: var(--text2);
    max-width: 500px;
    margin-bottom: 52px;
    line-height: 1.6;
}
.hto-steps-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
}
.hto-step-card {
    background: var(--card-bg, #fff);
    border: 1.5px solid var(--border);
    border-radius: 20px;
    padding: 28px 24px;
    position: relative;
    transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.3s ease;
    overflow: hidden;
}
.hto-step-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--step-color, var(--primary));
    border-radius: 20px 20px 0 0;
}
.hto-step-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 48px rgba(0,0,0,0.1);
    border-color: var(--step-color, var(--primary));
}
html.theme-dark .hto-step-card { background: var(--bg2); }
.hto-step-num-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
}
.hto-step-num {
    font-size: 2.2rem;
    font-weight: 900;
    color: var(--step-color, var(--primary));
    line-height: 1;
    opacity: 0.2;
}
.hto-step-emoji { font-size: 2rem; }
.hto-step-title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 10px;
    line-height: 1.3;
}
.hto-step-desc {
    font-size: 0.87rem;
    color: var(--text2);
    line-height: 1.7;
    margin-bottom: 14px;
}
.hto-step-tip {
    display: flex;
    align-items: flex-start;
    gap: 7px;
    background: rgba(var(--primary-rgb), 0.06);
    border-left: 3px solid var(--step-color, var(--primary));
    border-radius: 0 8px 8px 0;
    padding: 8px 12px;
    font-size: 0.78rem;
    color: var(--text2);
    font-weight: 600;
    line-height: 1.5;
}
.hto-step-tip svg { flex-shrink: 0; margin-top: 1px; color: var(--step-color, var(--primary)); }
.hto-step-connector { display: none; }

/* Payment */
.hto-payment-section { padding: 0 0 80px; }
.hto-payment-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark, #4f46e5) 100%);
    border-radius: 24px;
    padding: 48px 52px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 40px;
    align-items: center;
}
.hto-payment-left h3 {
    font-size: 1.5rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 8px;
}
.hto-payment-left > p { color: rgba(255,255,255,0.8); margin-bottom: 24px; font-size: 0.95rem; }
.hto-payment-methods { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.hto-payment-method {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #fff;
}
.hto-payment-method span { font-size: 1.2rem; }
.hto-payment-right { text-align: center; }
.hto-dp-info {
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 20px;
    padding: 28px 36px;
    backdrop-filter: blur(10px);
}
.hto-dp-num {
    font-size: 3.5rem;
    font-weight: 900;
    color: #fff;
    line-height: 1;
}
.hto-dp-label {
    font-size: 0.9rem;
    font-weight: 800;
    color: rgba(255,255,255,0.9);
    margin: 6px 0 4px;
}
.hto-dp-desc { font-size: 0.75rem; color: rgba(255,255,255,0.7); }

/* FAQ */
.hto-faq-section { padding: 0 0 80px; }
.hto-faq-list { max-width: 780px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
.hto-faq-item {
    background: var(--card-bg, #fff);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    transition: border-color 0.2s;
}
html.theme-dark .hto-faq-item { background: var(--bg2); }
.hto-faq-item.open { border-color: var(--primary); }
.hto-faq-q {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 22px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text);
    text-align: left;
    -webkit-tap-highlight-color: transparent;
}
.hto-faq-icon { flex-shrink: 0; transition: transform 0.3s cubic-bezier(0.16,1,0.3,1); color: var(--primary); }
.hto-faq-item.open .hto-faq-icon { transform: rotate(180deg); }
.hto-faq-a { padding: 0 22px 20px; }
.hto-faq-a p { font-size: 0.9rem; color: var(--text2); line-height: 1.75; margin: 0; }

/* CTA */
.hto-cta-section { padding: 0 0 80px; }
.hto-cta-card {
    background: var(--card-bg, #fff);
    border: 1.5px solid var(--border);
    border-radius: 28px;
    padding: 64px 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,0.06);
}
html.theme-dark .hto-cta-card { background: var(--bg2); }
.hto-cta-decor {
    position: absolute;
    top: 0; left: 0; right: 0;
    display: flex;
    justify-content: space-around;
    padding: 16px 40px;
    opacity: 0.12;
    font-size: 2rem;
    pointer-events: none;
}
.hto-cta-title {
    font-size: clamp(1.5rem, 3vw, 2.2rem);
    font-weight: 900;
    color: var(--text);
    margin-bottom: 12px;
}
.hto-cta-desc {
    font-size: 1rem;
    color: var(--text2);
    max-width: 500px;
    margin: 0 auto 32px;
    line-height: 1.6;
}
.hto-cta-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.hto-cta-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 32px;
    background: #25d366;
    color: #fff;
    border-radius: 50px;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 800;
    box-shadow: 0 8px 24px rgba(37,211,102,0.4);
    transition: transform 0.2s, filter 0.2s;
    -webkit-tap-highlight-color: transparent;
}
.hto-cta-btn-primary:hover { filter: brightness(1.1); transform: translateY(-3px); }
.hto-cta-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: var(--bg2);
    color: var(--text);
    border: 1.5px solid var(--border);
    border-radius: 50px;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 700;
    transition: border-color 0.2s, background 0.2s;
}
.hto-cta-btn-secondary:hover { border-color: var(--primary); color: var(--primary); }
.hto-cta-note { font-size: 0.82rem; color: var(--text2); }

/* Mobile */
@media (max-width: 768px) {
    .hto-steps-grid { grid-template-columns: 1fr; gap: 16px; }
    .hto-payment-card { grid-template-columns: 1fr; padding: 32px 24px; gap: 28px; }
    .hto-payment-methods { grid-template-columns: 1fr; }
    .hto-payment-right { border-top: 1px solid rgba(255,255,255,0.2); padding-top: 28px; }
    .hto-cta-card { padding: 48px 24px; }
    .hto-cta-actions { flex-direction: column; }
    .hto-cta-btn-primary, .hto-cta-btn-secondary { width: 100%; justify-content: center; }
    .hto-hero { padding: 50px 0 40px; }
    .hto-stat-div { display: none; }
    .hto-hero-stats { gap: 20px; }
}
</style>

<script>
// FAQ Accordion
document.querySelectorAll('.hto-faq-q').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var item = btn.closest('.hto-faq-item');
        var ans  = item.querySelector('.hto-faq-a');
        var open = !ans.hidden;
        // Close all
        document.querySelectorAll('.hto-faq-item').forEach(function(el) {
            el.classList.remove('open');
            el.querySelector('.hto-faq-a').hidden = true;
            el.querySelector('.hto-faq-q').setAttribute('aria-expanded', 'false');
        });
        if (!open) {
            item.classList.add('open');
            ans.hidden = false;
            btn.setAttribute('aria-expanded', 'true');
        }
    });
});
</script>

<?php get_footer(); ?>