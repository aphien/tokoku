<?php
/**
 * Template Name: Tentang Kami
 * Template Post Type: page
 *
 * Halaman profil brand JualPlakat.com: cerita, nilai, proses kerja, dan visi-misi.
 * Konten dari editor halaman (jika diisi) akan tampil sebagai bagian "Cerita Lengkap".
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$brand       = tokoku_page_brand();
$contact     = tokoku_page_contact();
$catalog_url = get_post_type_archive_link( 'produk' ) ? get_post_type_archive_link( 'produk' ) : home_url( '/' );
$wa_message  = rawurlencode( 'Halo ' . $brand . ', saya ingin berkonsultasi mengenai pembuatan plakat custom.' );
$wa_url      = 'https://wa.me/' . $contact['wa'] . '?text=' . $wa_message;

$product_count = 0;
if ( post_type_exists( 'produk' ) ) {
    $counts        = wp_count_posts( 'produk' );
    $product_count = isset( $counts->publish ) ? (int) $counts->publish : 0;
}
$category_count = taxonomy_exists( 'kategori_produk' ) ? (int) wp_count_terms( array( 'taxonomy' => 'kategori_produk', 'hide_empty' => true ) ) : 0;

$stats = array(
    array( 'value' => $product_count > 0 ? $product_count . '+' : '100%', 'label' => $product_count > 0 ? 'Pilihan Desain Produk' : 'Desain Custom' ),
    array( 'value' => $category_count > 0 ? (string) $category_count : '5+', 'label' => 'Kategori Material' ),
    array( 'value' => 'Gratis', 'label' => 'Konsultasi & Mockup' ),
    array( 'value' => '34', 'label' => 'Provinsi Terjangkau' ),
);

$values = array(
    array( 'icon' => 'target', 'title' => 'Presisi di Setiap Detail', 'text' => 'Setiap huruf, garis grafir, dan sudut potongan kami periksa berlapis. Karena sebuah penghargaan layak tampil sempurna.' ),
    array( 'icon' => 'gem', 'title' => 'Material Pilihan', 'text' => 'Akrilik bening berkualitas, kayu solid, resin jernih, hingga logam premium — dipilih agar tahan lama dan tetap memukau.' ),
    array( 'icon' => 'pen', 'title' => 'Desain yang Personal', 'text' => 'Tidak ada template asal jadi. Tim desain kami merancang setiap plakat agar mencerminkan identitas dan cerita Anda.' ),
    array( 'icon' => 'zap', 'title' => 'Tepat Waktu', 'text' => 'Kami memahami bahwa acara tidak bisa ditunda. Jadwal produksi disusun realistis dan dikomunikasikan sejak awal.' ),
    array( 'icon' => 'chat', 'title' => 'Komunikasi Responsif', 'text' => 'Konsultasi langsung melalui WhatsApp dengan tim yang ramah, sigap, dan siap memberi rekomendasi terbaik.' ),
    array( 'icon' => 'tag', 'title' => 'Harga Transparan', 'text' => 'Rincian biaya disampaikan jelas di awal — tanpa biaya tersembunyi, tanpa kejutan di akhir.' ),
);

$steps = array(
    array( 'icon' => 'chat', 'title' => 'Konsultasi', 'text' => 'Ceritakan kebutuhan Anda: jenis plakat, jumlah, teks, logo, dan tanggal acara.' ),
    array( 'icon' => 'layers', 'title' => 'Desain & Mockup', 'text' => 'Kami kirimkan pratinjau desain gratis dan siap direvisi hingga Anda benar-benar puas.' ),
    array( 'icon' => 'award', 'title' => 'Produksi Presisi', 'text' => 'Setelah ACC, plakat diproduksi dengan mesin modern dan sentuhan akhir tangan terampil.' ),
    array( 'icon' => 'truck', 'title' => 'Kirim dengan Aman', 'text' => 'Dikemas berlapis dan dikirim ke seluruh Indonesia dengan nomor resi yang bisa dilacak.' ),
);

$clients = array( 'Instansi Pemerintah', 'BUMN & Korporasi', 'Sekolah & Universitas', 'Organisasi & Komunitas', 'Event Olahraga', 'Rumah Sakit & Klinik', 'Perbankan', 'Kado Personal' );
?>

<main id="main-content" class="site-main jp-page jp-about">

    <!-- Hero -->
    <section class="jp-hero">
        <div class="jp-hero__glow" aria-hidden="true"></div>
        <div class="container">
            <nav class="jp-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?php echo esc_html( get_the_title() ); ?></span>
            </nav>

            <div class="jp-about__hero-grid">
                <div class="jp-hero__inner jp-reveal">
                    <span class="jp-badge"><?php echo tokoku_page_icon( 'award', 15 ); // phpcs:ignore ?> Tentang <?php echo esc_html( $brand ); ?></span>
                    <h1 class="jp-hero__title">Mengabadikan Setiap Pencapaian dalam <span class="jp-text-gradient">Karya yang Bermakna</span></h1>
                    <p class="jp-hero__lead">
                        <?php echo esc_html( $brand ); ?> adalah mitra terpercaya untuk pembuatan plakat, piala, dan souvenir penghargaan custom. Kami percaya setiap prestasi, dedikasi, dan momen berharga layak dikenang dalam wujud yang indah, berkelas, dan tahan lama.
                    </p>
                    <div class="jp-hero__actions">
                        <a class="jp-btn jp-btn--primary" href="<?php echo esc_url( $catalog_url ); ?>">
                            Jelajahi Katalog <?php echo tokoku_page_icon( 'arrow', 18 ); // phpcs:ignore ?>
                        </a>
                        <a class="jp-btn jp-btn--ghost" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo tokoku_page_icon( 'whatsapp', 18 ); // phpcs:ignore ?> Konsultasi Gratis
                        </a>
                    </div>
                </div>

                <div class="jp-plaque jp-reveal" aria-hidden="true">
                    <div class="jp-plaque__shine"></div>
                    <div class="jp-plaque__ribbon"><?php echo tokoku_page_icon( 'award', 30 ); // phpcs:ignore ?></div>
                    <span class="jp-plaque__eyebrow">Penghargaan</span>
                    <strong class="jp-plaque__title">Apresiasi Terbaik</strong>
                    <span class="jp-plaque__line"></span>
                    <span class="jp-plaque__text">Diberikan atas dedikasi, kerja keras, dan prestasi yang menginspirasi.</span>
                    <span class="jp-plaque__brand"><?php echo esc_html( $brand ); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="jp-section jp-section--tight">
        <div class="container">
            <ul class="jp-stats jp-reveal">
                <?php foreach ( $stats as $stat ) : ?>
                    <li class="jp-stats__item">
                        <strong class="jp-stats__value"><?php echo esc_html( $stat['value'] ); ?></strong>
                        <span class="jp-stats__label"><?php echo esc_html( $stat['label'] ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <!-- Story -->
    <section class="jp-section">
        <div class="container">
            <div class="jp-split">
                <div class="jp-split__media jp-reveal">
                    <div class="jp-quote-card">
                        <span class="jp-quote-card__mark" aria-hidden="true">&ldquo;</span>
                        <p>Sebuah plakat bukan sekadar benda. Ia adalah pengingat bahwa kerja keras seseorang pernah dilihat, dihargai, dan dirayakan.</p>
                        <span class="jp-quote-card__author">— Filosofi <?php echo esc_html( $brand ); ?></span>
                    </div>
                </div>
                <div class="jp-split__content jp-reveal">
                    <span class="jp-eyebrow">Cerita Kami</span>
                    <h2 class="jp-section__title">Berawal dari Keyakinan Sederhana: Setiap Apresiasi Layak Istimewa</h2>
                    <div class="jp-prose">
                        <p><?php echo esc_html( $brand ); ?> lahir dari pengalaman melihat betapa berartinya sebuah penghargaan bagi penerimanya — senyum bangga saat menerima plakat di atas panggung, atau rasa haru ketika sebuah dedikasi akhirnya diakui.</p>
                        <p>Dari situ kami bertekad menghadirkan layanan pembuatan plakat yang <strong>mudah, cepat, dan berkualitas tinggi</strong>. Tanpa proses berbelit, tanpa harus datang ke workshop — cukup konsultasi melalui WhatsApp, dan tim kami akan mewujudkan ide Anda menjadi karya nyata yang siap dibanggakan.</p>
                        <p>Hari ini, kami melayani beragam kebutuhan: plakat akrilik, kayu, resin, hingga piala dan trophy untuk instansi, perusahaan, institusi pendidikan, komunitas, dan hadiah personal ke seluruh penjuru Indonesia.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="jp-section jp-section--soft">
        <div class="container">
            <div class="jp-section__head jp-reveal">
                <span class="jp-eyebrow">Mengapa Memilih Kami</span>
                <h2 class="jp-section__title">Komitmen yang Kami Pegang di Setiap Pesanan</h2>
                <p class="jp-section__sub">Enam prinsip yang menjadikan setiap plakat dari <?php echo esc_html( $brand ); ?> layak dipercaya untuk momen paling penting Anda.</p>
            </div>
            <div class="jp-cards">
                <?php foreach ( $values as $value ) : ?>
                    <article class="jp-card jp-reveal">
                        <span class="jp-card__icon"><?php echo tokoku_page_icon( $value['icon'], 22 ); // phpcs:ignore ?></span>
                        <h3 class="jp-card__title"><?php echo esc_html( $value['title'] ); ?></h3>
                        <p class="jp-card__text"><?php echo esc_html( $value['text'] ); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Process -->
    <section class="jp-section">
        <div class="container">
            <div class="jp-section__head jp-reveal">
                <span class="jp-eyebrow">Cara Kami Bekerja</span>
                <h2 class="jp-section__title">Dari Ide Menjadi Karya dalam 4 Langkah Mudah</h2>
            </div>
            <ol class="jp-steps">
                <?php foreach ( $steps as $i => $step ) : ?>
                    <li class="jp-step jp-reveal">
                        <span class="jp-step__num"><?php echo esc_html( $i + 1 ); ?></span>
                        <span class="jp-step__icon"><?php echo tokoku_page_icon( $step['icon'], 22 ); // phpcs:ignore ?></span>
                        <h3 class="jp-step__title"><?php echo esc_html( $step['title'] ); ?></h3>
                        <p class="jp-step__text"><?php echo esc_html( $step['text'] ); ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- Vision & Mission -->
    <section class="jp-section jp-section--soft">
        <div class="container">
            <div class="jp-vm">
                <div class="jp-vm__card jp-vm__card--vision jp-reveal">
                    <span class="jp-card__icon"><?php echo tokoku_page_icon( 'eye', 22 ); // phpcs:ignore ?></span>
                    <span class="jp-eyebrow">Visi</span>
                    <h2 class="jp-vm__title">Menjadi destinasi utama pembuatan plakat dan penghargaan custom di Indonesia yang dikenal karena kualitas, kreativitas, dan pelayanan sepenuh hati.</h2>
                </div>
                <div class="jp-vm__card jp-reveal">
                    <span class="jp-card__icon"><?php echo tokoku_page_icon( 'target', 22 ); // phpcs:ignore ?></span>
                    <span class="jp-eyebrow">Misi</span>
                    <ul class="jp-checklist">
                        <li><?php echo tokoku_page_icon( 'check', 16 ); // phpcs:ignore ?><span>Menghadirkan produk penghargaan berkualitas tinggi dengan harga yang adil dan transparan.</span></li>
                        <li><?php echo tokoku_page_icon( 'check', 16 ); // phpcs:ignore ?><span>Memberikan pengalaman pemesanan yang mudah, cepat, dan menyenangkan dari mana saja.</span></li>
                        <li><?php echo tokoku_page_icon( 'check', 16 ); // phpcs:ignore ?><span>Terus berinovasi dalam desain, material, dan teknik produksi.</span></li>
                        <li><?php echo tokoku_page_icon( 'check', 16 ); // phpcs:ignore ?><span>Membangun hubungan jangka panjang yang dilandasi kepercayaan dengan setiap pelanggan.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Clients -->
    <section class="jp-section">
        <div class="container">
            <div class="jp-section__head jp-reveal">
                <span class="jp-eyebrow">Dipercaya Beragam Kalangan</span>
                <h2 class="jp-section__title">Melayani Setiap Momen Apresiasi</h2>
            </div>
            <ul class="jp-chips jp-reveal">
                <?php foreach ( $clients as $client ) : ?>
                    <li class="jp-chip"><?php echo tokoku_page_icon( 'check', 14 ); // phpcs:ignore ?> <?php echo esc_html( $client ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <?php
    while ( have_posts() ) :
        the_post();
        if ( '' !== trim( get_the_content() ) ) :
            ?>
            <section class="jp-section jp-section--soft">
                <div class="container jp-container-narrow">
                    <div class="jp-section__head jp-reveal">
                        <span class="jp-eyebrow">Cerita Lengkap</span>
                    </div>
                    <div class="jp-prose jp-reveal"><?php the_content(); ?></div>
                </div>
            </section>
            <?php
        endif;
    endwhile;
    ?>

    <!-- CTA -->
    <section class="jp-section">
        <div class="container">
            <div class="jp-cta jp-reveal">
                <div class="jp-cta__glow" aria-hidden="true"></div>
                <div class="jp-cta__text">
                    <h2>Siap Mewujudkan Plakat Penghargaan Anda?</h2>
                    <p>Konsultasikan kebutuhan Anda sekarang. Gratis desain dan mockup — Anda hanya membayar ketika sudah yakin.</p>
                </div>
                <div class="jp-cta__actions">
                    <a class="jp-btn jp-btn--light" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo tokoku_page_icon( 'whatsapp', 18 ); // phpcs:ignore ?> Chat WhatsApp
                    </a>
                    <a class="jp-btn jp-btn--outline-light" href="<?php echo esc_url( tokoku_static_page_url( 'page-templates/template-kontak.php' ) ); ?>">
                        Hubungi Kami <?php echo tokoku_page_icon( 'arrow', 18 ); // phpcs:ignore ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
