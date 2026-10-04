<?php
/**
 * Template Name: Kontak Kami
 * Template Post Type: page
 *
 * Halaman Kontak JualPlakat.com: jalur WhatsApp resmi, tim Customer Service,
 * alamat workshop, jam operasional, form pembuat pesan WhatsApp instan, serta FAQ.
 * Konten dari editor halaman (jika ada) akan tampil sebagai informasi tambahan.
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$brand    = tokoku_page_brand();
$contact  = tokoku_page_contact();
$wa_raw   = $contact['wa'];
$wa_url   = 'https://wa.me/' . $wa_raw;
$email    = $contact['email'];
$address  = $contact['address'];
$hours    = $contact['hours'];
$agents   = $contact['agents'];

// Jika tidak ada data agen custom di pengaturan tema, sediakan kontak CRO utama yang profesional
if ( empty( $agents ) ) {
    $agents = array(
        array(
            'name' => 'Customer Care 1 (Konsultasi & Desain)',
            'wa'   => $wa_raw,
            'role' => 'Desain & Rekomendasi Plakat',
        ),
        array(
            'name' => 'Customer Care 2 (Pemesanan & Tender)',
            'wa'   => $wa_raw,
            'role' => 'Pemesanan Instansi & Korporasi',
        ),
    );
}

$faq_items = array(
    array(
        'q' => 'Berapa lama proses pembuatan plakat custom?',
        'a' => 'Rata-rata pengerjaan berkisar antara 1 hingga 3 hari kerja setelah mockup desain disetujui (ACC) dan pembayaran terkonfirmasi. Untuk kebutuhan mendesak (urgent/ekspres), silakan konsultasikan dengan tim kami agar dapat dijadwalkan secara prioritas.',
    ),
    array(
        'q' => 'Apakah bisa membuat plakat dengan desain sendiri atau tanpa desain?',
        'a' => 'Tentu saja! Jika Anda sudah memiliki file desain (AI, CorelDraw, PDF, atau PNG beresolusi tinggi), Anda dapat langsung mengirimkannya. Jika belum memiliki desain, tim desainer kreatif kami siap membuatkan mockup gratis sesuai konsep, logo, dan tulisan yang Anda inginkan.',
    ),
    array(
        'q' => 'Apakah ada jumlah minimal pemesanan (MOQ)?',
        'a' => 'Kami melayani pemesanan mulai dari 1 buah (satuan) untuk kado personal atau penghargaan khusus, hingga ribuan buah untuk kebutuhan wisuda, turnamen olahraga, dan corporate event besar dengan harga grosir kompetitif.',
    ),
    array(
        'q' => 'Bagaimana pengemasan dan keamanan pengiriman ke luar kota?',
        'a' => 'Setiap produk kami lindungi dengan lapisan bubble wrap tebal, busa pelindung khusus, dan kardus kokoh atau packing kayu ekstra. Kami bermitra dengan ekspedisi tepercaya (JNE, J&T, SiCepat, Cargo, dsb.) lengkap dengan nomor resi pelacakan dan opsi asuransi pengiriman.',
    ),
    array(
        'q' => 'Bagaimana cara mendapatkan surat penawaran harga resmi (invoice/quotation)?',
        'a' => 'Cukup kirimkan detail pesanan (jenis produk, estimasi jumlah, spesifikasi, dan nama instansi) melalui formulir kontak di bawah atau via WhatsApp. Tim administrasi kami akan menerbitkan invoice / surat penawaran resmi bertanda tangan.',
    ),
);
?>

<main id="main-content" class="site-main jp-page jp-contact">

    <!-- Hero -->
    <section class="jp-hero">
        <div class="jp-hero__glow" aria-hidden="true"></div>
        <div class="container">
            <nav class="jp-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?php echo esc_html( get_the_title() ); ?></span>
            </nav>

            <div class="jp-hero__inner jp-reveal">
                <span class="jp-badge"><?php echo tokoku_page_icon( 'chat', 15 ); // phpcs:ignore ?> Layanan Pelanggan</span>
                <h1 class="jp-hero__title">Mari Bicarakan <span class="jp-text-gradient">Penghargaan Terbaik Anda</span></h1>
                <p class="jp-hero__lead">
                    Punya pertanyaan, butuh konsultasi desain gratis, atau ingin meminta penawaran harga untuk instansi? Tim Customer Care <strong><?php echo esc_html( $brand ); ?></strong> siap mendengarkan dan mendampingi Anda dengan ramah, cepat, dan profesional.
                </p>

                <ul class="jp-meta-chips">
                    <li><?php echo tokoku_page_icon( 'zap', 15 ); // phpcs:ignore ?> Respons Cepat WhatsApp</li>
                    <li><?php echo tokoku_page_icon( 'shield', 15 ); // phpcs:ignore ?> Konsultasi & Mockup Gratis</li>
                    <li><?php echo tokoku_page_icon( 'truck', 15 ); // phpcs:ignore ?> Pengiriman Seluruh Indonesia</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Contact Cards Grid -->
    <section class="jp-section jp-section--tight">
        <div class="container">
            <div class="jp-contact-cards jp-reveal">
                <!-- Card 1: WhatsApp Utama -->
                <div class="jp-contact-card jp-contact-card--featured">
                    <div class="jp-contact-card__icon-wrap jp-contact-card__icon-wrap--wa">
                        <?php echo tokoku_page_icon( 'whatsapp', 26 ); // phpcs:ignore ?>
                    </div>
                    <span class="jp-contact-card__badge">Respons Paling Cepat</span>
                    <h3 class="jp-contact-card__title">WhatsApp Hotline</h3>
                    <p class="jp-contact-card__desc">Konsultasi langsung, kirim file logo, dan terima mockup desain visual dalam hitungan menit.</p>
                    <div class="jp-contact-card__value"><?php echo esc_html( $contact['wa_display'] ); ?></div>
                    <a class="jp-btn jp-btn--wa jp-btn--block" href="<?php echo esc_url( $wa_url . '?text=' . rawurlencode( 'Halo ' . $brand . ', saya ingin konsultasi plakat.' ) ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo tokoku_page_icon( 'whatsapp', 18 ); // phpcs:ignore ?> Chat WhatsApp Sekarang
                    </a>
                </div>

                <!-- Card 2: Email Resmi -->
                <div class="jp-contact-card">
                    <div class="jp-contact-card__icon-wrap jp-contact-card__icon-wrap--email">
                        <?php echo tokoku_page_icon( 'mail', 26 ); // phpcs:ignore ?>
                    </div>
                    <span class="jp-contact-card__badge">Instansi & Tender</span>
                    <h3 class="jp-contact-card__title">Email Resmi</h3>
                    <p class="jp-contact-card__desc">Kirim berkas penawaran, dokumen tender, PO perusahaan, atau lampiran file desain berukuran besar.</p>
                    <?php if ( $email ) : ?>
                        <div class="jp-contact-card__value">
                            <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
                        </div>
                        <a class="jp-btn jp-btn--ghost jp-btn--block" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
                            <?php echo tokoku_page_icon( 'mail', 18 ); // phpcs:ignore ?> Kirim Email
                        </a>
                    <?php else : ?>
                        <div class="jp-contact-card__value">Silakan hubungi WhatsApp</div>
                        <a class="jp-btn jp-btn--ghost jp-btn--block" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
                            Tanya Email via WA
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Card 3: Jam Kerja & Layanan -->
                <div class="jp-contact-card">
                    <div class="jp-contact-card__icon-wrap jp-contact-card__icon-wrap--clock">
                        <?php echo tokoku_page_icon( 'clock', 26 ); // phpcs:ignore ?>
                    </div>
                    <span class="jp-contact-card__badge">Pelayanan Prima</span>
                    <h3 class="jp-contact-card__title">Jam Operasional</h3>
                    <p class="jp-contact-card__desc">Tim kami aktif melayani konsultasi dan proses produksi pada jam kerja berikut:</p>
                    <ul class="jp-contact-card__hours">
                        <?php foreach ( $hours as $hour_line ) : ?>
                            <li><?php echo tokoku_page_icon( 'calendar', 14 ); // phpcs:ignore ?> <span><?php echo esc_html( $hour_line ); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <span class="jp-contact-card__note">Pesan di luar jam kerja tetap dapat dikirim dan akan dibalas pada kesempatan pertama.</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive WA Message Builder + Workshop Section -->
    <section class="jp-section jp-section--soft">
        <div class="container">
            <div class="jp-split jp-split--contact">

                <!-- Form Pembuat Pesan WhatsApp Instan -->
                <div class="jp-contact-form-box jp-reveal">
                    <div class="jp-contact-form-box__header">
                        <span class="jp-eyebrow">Formulir Interaktif</span>
                        <h2 class="jp-contact-form-box__title">Kirim Rincian Kebutuhan Anda</h2>
                        <p class="jp-contact-form-box__sub">
                            Isi formulir ringkas di bawah. Sistem kami akan merangkumnya secara otomatis menjadi format pesan rapi yang langsung terkirim ke WhatsApp Customer Service kami.
                        </p>
                    </div>

                    <form id="jp-wa-builder-form" class="jp-form" novalidate data-wa="<?php echo esc_attr( $wa_raw ); ?>" data-brand="<?php echo esc_attr( $brand ); ?>">
                        <div class="jp-form__row">
                            <div class="jp-form__field">
                                <label for="jp_client_name" class="jp-label">Nama Lengkap Anda <span class="jp-required">*</span></label>
                                <input type="text" id="jp_client_name" name="client_name" class="jp-input" placeholder="Contoh: Budi Santoso" required autocomplete="name">
                            </div>
                            <div class="jp-form__field">
                                <label for="jp_client_instansi" class="jp-label">Instansi / Perusahaan / Komunitas</label>
                                <input type="text" id="jp_client_instansi" name="client_instansi" class="jp-input" placeholder="Contoh: PT Sumber Makmur / Universitas...">
                            </div>
                        </div>

                        <div class="jp-form__row">
                            <div class="jp-form__field">
                                <label for="jp_product_type" class="jp-label">Jenis Produk yang Diminati <span class="jp-required">*</span></label>
                                <select id="jp_product_type" name="product_type" class="jp-select" required>
                                    <option value="" disabled selected>-- Pilih Jenis Plakat / Produk --</option>
                                    <option value="Plakat Akrilik Custom">Plakat Akrilik (Bening, Elegan & Modern)</option>
                                    <option value="Plakat Kayu Solid / Kombinasi Logam">Plakat Kayu (Eksklusif & Bernuansa Klasik)</option>
                                    <option value="Plakat Resin / Kristal Bening">Plakat Resin / Kristal (3D & Bentuk Unik)</option>
                                    <option value="Piala & Trophy Penghargaan">Piala & Trophy (Kejuaraan & Lomba)</option>
                                    <option value="Medali Custom">Medali Penghargaan / Wisuda</option>
                                    <option value="Souvenir & Merchandise Lainnya">Souvenir / Merchandise Lainnya</option>
                                    <option value="Konsultasi Desain & Penawaran">Belum Yakin (Ingin Rekomendasi)</option>
                                </select>
                            </div>
                            <div class="jp-form__field">
                                <label for="jp_quantity" class="jp-label">Estimasi Jumlah (Pcs) <span class="jp-required">*</span></label>
                                <input type="number" id="jp_quantity" name="quantity" class="jp-input" placeholder="Contoh: 1, 10, 50..." min="1" required>
                            </div>
                        </div>

                        <div class="jp-form__field">
                            <label for="jp_deadline" class="jp-label">Target Tanggal Acara / Kebutuhan</label>
                            <input type="text" id="jp_deadline" name="deadline" class="jp-input" placeholder="Contoh: 25 Oktober 2026 atau Mendesak 3 hari">
                        </div>

                        <div class="jp-form__field">
                            <label for="jp_notes" class="jp-label">Keterangan / Tulisan Plakat / Catatan Khusus</label>
                            <textarea id="jp_notes" name="notes" class="jp-textarea" rows="4" placeholder="Tuliskan nama acara, teks penghargaan, logo yang akan dipakai, atau pertanyaan khusus yang ingin Anda tanyakan..."></textarea>
                        </div>

                        <button type="submit" id="jp-wa-submit-btn" class="jp-btn jp-btn--wa jp-btn--lg jp-btn--block">
                            <?php echo tokoku_page_icon( 'send', 18 ); // phpcs:ignore ?>
                            <span>Kirim ke WhatsApp Customer Service</span>
                        </button>

                        <p class="jp-form__disclaimer">
                            <?php echo tokoku_page_icon( 'shield', 14 ); // phpcs:ignore ?>
                            Informasi Anda aman dan hanya digunakan untuk keperluan konsultasi serta proses pesanan plakat.
                        </p>
                    </form>
                </div>

                <!-- Info Workshop & Jalur Agen -->
                <div class="jp-contact-side jp-reveal">

                    <!-- Info Alamat Workshop -->
                    <?php if ( $address ) : ?>
                        <div class="jp-card jp-card--bordered">
                            <span class="jp-card__icon"><?php echo tokoku_page_icon( 'pin', 24 ); // phpcs:ignore ?></span>
                            <span class="jp-eyebrow">Lokasi & Workshop</span>
                            <h3 class="jp-card__title">Alamat Workshop & Kantor</h3>
                            <p class="jp-card__text"><?php echo nl2br( esc_html( $address ) ); ?></p>
                            <div class="jp-card__actions">
                                <button type="button" class="jp-btn jp-btn--xs jp-btn--ghost jp-copy-btn" data-copy="<?php echo esc_attr( $address ); ?>">
                                    <?php echo tokoku_page_icon( 'copy', 14 ); // phpcs:ignore ?> <span>Salin Alamat</span>
                                </button>
                                <a class="jp-btn jp-btn--xs jp-btn--primary" href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address ) ); ?>" target="_blank" rel="noopener noreferrer">
                                    Buka di Google Maps <?php echo tokoku_page_icon( 'arrow', 14 ); // phpcs:ignore ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Jalur Kontak Customer Relation Officer -->
                    <?php if ( ! empty( $agents ) ) : ?>
                        <div class="jp-card jp-card--bordered">
                            <span class="jp-card__icon"><?php echo tokoku_page_icon( 'users', 24 ); // phpcs:ignore ?></span>
                            <span class="jp-eyebrow">Jalur Konsultasi</span>
                            <h3 class="jp-card__title">Customer Relation Officer (CRO)</h3>
                            <p class="jp-card__text">Hubungi petugas kami yang siap melayani dengan pendekatan personal dan responsif:</p>
                            
                            <ul class="jp-agents-list">
                                <?php foreach ( $agents as $agent ) : 
                                    $a_wa  = preg_replace( '/\D+/', '', $agent['wa'] );
                                    $a_url = 'https://wa.me/' . $a_wa . '?text=' . rawurlencode( 'Halo ' . $agent['name'] . ', saya ingin konsultasi plakat.' );
                                ?>
                                    <li class="jp-agent-item">
                                        <div class="jp-agent-item__info">
                                            <strong class="jp-agent-item__name"><?php echo esc_html( $agent['name'] ); ?></strong>
                                            <?php if ( ! empty( $agent['role'] ) ) : ?>
                                                <span class="jp-agent-item__role"><?php echo esc_html( $agent['role'] ); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?php echo esc_url( $a_url ); ?>" class="jp-agent-item__btn" target="_blank" rel="noopener noreferrer" aria-label="Chat WhatsApp dengan <?php echo esc_attr( $agent['name'] ); ?>">
                                            <?php echo tokoku_page_icon( 'whatsapp', 16 ); // phpcs:ignore ?> Chat
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Media Sosial -->
                    <?php if ( ! empty( $contact['socials'] ) ) : ?>
                        <div class="jp-card jp-card--bordered">
                            <span class="jp-eyebrow">Terhubung di Media Sosial</span>
                            <h3 class="jp-card__title">Ikuti Portofolio & Karya Terbaru</h3>
                            <div class="jp-social-badges">
                                <?php foreach ( $contact['socials'] as $network => $url ) : ?>
                                    <a href="<?php echo esc_url( $url ); ?>" class="jp-social-badge" target="_blank" rel="noopener noreferrer">
                                        <?php echo esc_html( ucfirst( $network ) ); ?> <?php echo tokoku_page_icon( 'arrow', 13 ); // phpcs:ignore ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion -->
    <section class="jp-section">
        <div class="container jp-container-narrow">
            <div class="jp-section__head jp-reveal">
                <span class="jp-eyebrow">Pertanyaan Sering Diajukan</span>
                <h2 class="jp-section__title">Hal yang Sering Ditanyakan Sebelum Memesan</h2>
                <p class="jp-section__sub">Jawaban cepat untuk pertanyaan paling umum seputar pemesanan plakat di <?php echo esc_html( $brand ); ?>.</p>
            </div>

            <div class="jp-faq-accordion jp-reveal">
                <?php foreach ( $faq_items as $idx => $faq ) : ?>
                    <details class="jp-faq-item" <?php echo 0 === $idx ? 'open' : ''; ?>>
                        <summary class="jp-faq-question">
                            <span><?php echo esc_html( $faq['q'] ); ?></span>
                            <span class="jp-faq-arrow" aria-hidden="true"><?php echo tokoku_page_icon( 'arrow', 16 ); // phpcs:ignore ?></span>
                        </summary>
                        <div class="jp-faq-answer">
                            <p><?php echo esc_html( $faq['a'] ); ?></p>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Extra Content from Editor (if any) -->
    <?php
    while ( have_posts() ) :
        the_post();
        if ( '' !== trim( get_the_content() ) ) :
            ?>
            <section class="jp-section jp-section--soft">
                <div class="container jp-container-narrow">
                    <div class="jp-prose jp-reveal"><?php the_content(); ?></div>
                </div>
            </section>
            <?php
        endif;
    endwhile;
    ?>

    <!-- Bottom CTA -->
    <section class="jp-section jp-section--tight">
        <div class="container">
            <div class="jp-cta jp-reveal">
                <div class="jp-cta__glow" aria-hidden="true"></div>
                <div class="jp-cta__text">
                    <h2>Sudah Punya Tanggal Acara?</h2>
                    <p>Jangan tunda hingga hari mendekati acara. Hubungi kami sekarang untuk memastikan antrean produksi Anda siap tepat pada waktunya.</p>
                </div>
                <div class="jp-cta__actions">
                    <a class="jp-btn jp-btn--light" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo tokoku_page_icon( 'whatsapp', 18 ); // phpcs:ignore ?> Hubungi via WhatsApp
                    </a>
                    <a class="jp-btn jp-btn--outline-light" href="<?php echo esc_url( tokoku_static_page_url( 'page-templates/template-tentang.php' ) ); ?>">
                        Pelajari Profil Kami <?php echo tokoku_page_icon( 'arrow', 18 ); // phpcs:ignore ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
