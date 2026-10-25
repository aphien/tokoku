<?php
/**
 * Template Name: Kontak Kami
 * Template Post Type: page
 *
 * Halaman Kontak: jalur WhatsApp resmi, tim Customer Service (CRO),
 * alamat workshop, jam operasional, form pembuat pesan WhatsApp instan, serta FAQ.
 * Semua bagian dapat dikelola langsung dari menu admin TokoKu > Template Kontak & Workshop.
 * Konten dari editor halaman (jika ada) akan tampil sebagai informasi tambahan.
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$brand        = tokoku_page_brand();
$contact      = tokoku_page_contact();
$contact_data = tokoku_get_contact_page_data();

$wa_raw   = $contact['wa'];
$wa_url   = 'https://wa.me/' . $wa_raw;
$email    = $contact['email'];
$address  = $contact['address'];
$hours    = $contact['hours'];
$agents   = $contact['agents'];
$faq_items = $contact_data['faqs'];

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

            <div class="jp-hero__inner">
                <span class="jp-badge"><?php echo tokoku_page_icon( 'chat', 15 ); // phpcs:ignore ?> <?php echo esc_html( $contact_data['hero_badge'] ); ?></span>
                <h1 class="jp-hero__title"><?php echo wp_kses_post( $contact_data['hero_title'] ); ?></h1>
                <p class="jp-hero__lead">
                    <?php echo wp_kses_post( $contact_data['hero_desc'] ); ?>
                </p>

                <ul class="jp-meta-chips">
                    <?php if ( ! empty( $contact_data['chip_1'] ) ) : ?>
                        <li><?php echo tokoku_page_icon( 'zap', 15 ); // phpcs:ignore ?> <?php echo esc_html( $contact_data['chip_1'] ); ?></li>
                    <?php endif; ?>
                    <?php if ( ! empty( $contact_data['chip_2'] ) ) : ?>
                        <li><?php echo tokoku_page_icon( 'shield', 15 ); // phpcs:ignore ?> <?php echo esc_html( $contact_data['chip_2'] ); ?></li>
                    <?php endif; ?>
                    <?php if ( ! empty( $contact_data['chip_3'] ) ) : ?>
                        <li><?php echo tokoku_page_icon( 'truck', 15 ); // phpcs:ignore ?> <?php echo esc_html( $contact_data['chip_3'] ); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </section>

    <!-- Contact Cards Grid -->
    <section class="jp-section jp-section--tight">
        <div class="container">
            <div class="jp-contact-cards">
                <!-- Card 1: WhatsApp Utama -->
                <div class="jp-contact-card jp-contact-card--featured">
                    <div class="jp-contact-card__icon-wrap jp-contact-card__icon-wrap--wa">
                        <?php echo tokoku_page_icon( 'whatsapp', 26 ); // phpcs:ignore ?>
                    </div>
                    <?php if ( ! empty( $contact_data['card1_badge'] ) ) : ?>
                        <span class="jp-contact-card__badge"><?php echo esc_html( $contact_data['card1_badge'] ); ?></span>
                    <?php endif; ?>
                    <h3 class="jp-contact-card__title"><?php echo esc_html( $contact_data['card1_title'] ); ?></h3>
                    <p class="jp-contact-card__desc"><?php echo esc_html( $contact_data['card1_desc'] ); ?></p>
                    <div class="jp-contact-card__value"><?php echo esc_html( $contact['wa_display'] ); ?></div>
                    <a class="jp-btn jp-btn--wa jp-btn--block" href="<?php echo esc_url( $wa_url . '?text=' . rawurlencode( 'Halo ' . $brand . ', saya ingin konsultasi plakat.' ) ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo tokoku_page_icon( 'whatsapp', 18 ); // phpcs:ignore ?> <?php echo esc_html( $contact_data['card1_btn'] ); ?>
                    </a>
                </div>

                <!-- Card 2: Email Resmi -->
                <div class="jp-contact-card">
                    <div class="jp-contact-card__icon-wrap jp-contact-card__icon-wrap--email">
                        <?php echo tokoku_page_icon( 'mail', 26 ); // phpcs:ignore ?>
                    </div>
                    <?php if ( ! empty( $contact_data['card2_badge'] ) ) : ?>
                        <span class="jp-contact-card__badge"><?php echo esc_html( $contact_data['card2_badge'] ); ?></span>
                    <?php endif; ?>
                    <h3 class="jp-contact-card__title"><?php echo esc_html( $contact_data['card2_title'] ); ?></h3>
                    <p class="jp-contact-card__desc"><?php echo esc_html( $contact_data['card2_desc'] ); ?></p>
                    <?php if ( $email ) : ?>
                        <div class="jp-contact-card__value">
                            <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
                            <?php if ( ! empty( $contact['phone'] ) ) : ?>
                                <div style="margin-top: 6px; font-size: 0.825rem; font-weight: 600; color: var(--text2, #64748b);">
                                    Hotline: <a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $contact['phone'] ) ); ?>" style="color: var(--text, #1f2937);"><?php echo esc_html( $contact['phone'] ); ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a class="jp-btn jp-btn--ghost jp-btn--block" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
                            <?php echo tokoku_page_icon( 'mail', 18 ); // phpcs:ignore ?> <?php echo esc_html( $contact_data['card2_btn'] ); ?>
                        </a>
                    <?php else : ?>
                        <div class="jp-contact-card__value">
                            <?php _e( 'Silakan hubungi WhatsApp', 'tokoku' ); ?>
                            <?php if ( ! empty( $contact['phone'] ) ) : ?>
                                <div style="margin-top: 6px; font-size: 0.825rem; font-weight: 600; color: var(--text2, #64748b);">
                                    Hotline: <a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $contact['phone'] ) ); ?>" style="color: var(--text, #1f2937);"><?php echo esc_html( $contact['phone'] ); ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a class="jp-btn jp-btn--ghost jp-btn--block" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php _e( 'Tanya Email via WA', 'tokoku' ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Card 3: Jam Kerja & Layanan -->
                <div class="jp-contact-card">
                    <div class="jp-contact-card__icon-wrap jp-contact-card__icon-wrap--clock">
                        <?php echo tokoku_page_icon( 'clock', 26 ); // phpcs:ignore ?>
                    </div>
                    <?php if ( ! empty( $contact_data['card3_badge'] ) ) : ?>
                        <span class="jp-contact-card__badge"><?php echo esc_html( $contact_data['card3_badge'] ); ?></span>
                    <?php endif; ?>
                    <h3 class="jp-contact-card__title"><?php echo esc_html( $contact_data['card3_title'] ); ?></h3>
                    <p class="jp-contact-card__desc"><?php echo esc_html( $contact_data['card3_desc'] ); ?></p>
                    <ul class="jp-contact-card__hours">
                        <?php foreach ( $hours as $hour_line ) : ?>
                            <li><?php echo tokoku_page_icon( 'calendar', 14 ); // phpcs:ignore ?> <span><?php echo esc_html( $hour_line ); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ( ! empty( $contact_data['card3_note'] ) ) : ?>
                        <span class="jp-contact-card__note"><?php echo esc_html( $contact_data['card3_note'] ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive WA Message Builder + Workshop Section -->
    <section class="jp-section jp-section--soft">
        <div class="container">
            <div class="jp-split jp-split--contact">

                <!-- Form Pembuat Pesan WhatsApp Instan -->
                <div class="jp-contact-form-box">
                    <div class="jp-contact-form-box__header">
                        <span class="jp-eyebrow"><?php echo esc_html( $contact_data['form_eyebrow'] ); ?></span>
                        <h2 class="jp-contact-form-box__title"><?php echo esc_html( $contact_data['form_title'] ); ?></h2>
                        <p class="jp-contact-form-box__sub">
                            <?php echo esc_html( $contact_data['form_sub'] ); ?>
                        </p>
                    </div>

                    <form id="jp-wa-builder-form" class="jp-form" novalidate data-wa="<?php echo esc_attr( $wa_raw ); ?>" data-brand="<?php echo esc_attr( $brand ); ?>">
                        <div class="jp-form__row">
                            <div class="jp-form__field">
                                <label for="jp_client_name" class="jp-label"><?php _e( 'Nama Lengkap Anda', 'tokoku' ); ?> <span class="jp-required">*</span></label>
                                <input type="text" id="jp_client_name" name="client_name" class="jp-input" placeholder="Contoh: Budi Santoso" required autocomplete="name">
                            </div>
                            <div class="jp-form__field">
                                <label for="jp_client_instansi" class="jp-label"><?php _e( 'Instansi / Perusahaan / Komunitas', 'tokoku' ); ?></label>
                                <input type="text" id="jp_client_instansi" name="client_instansi" class="jp-input" placeholder="Contoh: PT Sumber Makmur / Universitas...">
                            </div>
                        </div>

                        <div class="jp-form__row">
                            <div class="jp-form__field">
                                <label for="jp_product_type" class="jp-label"><?php _e( 'Jenis Produk yang Diminati', 'tokoku' ); ?> <span class="jp-required">*</span></label>
                                <select id="jp_product_type" name="product_type" class="jp-select" required>
                                    <option value="" disabled selected><?php _e( '-- Pilih Jenis Plakat / Produk --', 'tokoku' ); ?></option>
                                    <option value="Plakat Akrilik Custom"><?php _e( 'Plakat Akrilik (Bening, Elegan & Modern)', 'tokoku' ); ?></option>
                                    <option value="Plakat Kayu Solid / Kombinasi Logam"><?php _e( 'Plakat Kayu (Eksklusif & Bernuansa Klasik)', 'tokoku' ); ?></option>
                                    <option value="Plakat Resin / Kristal Bening"><?php _e( 'Plakat Resin / Kristal (3D & Bentuk Unik)', 'tokoku' ); ?></option>
                                    <option value="Piala & Trophy Penghargaan"><?php _e( 'Piala & Trophy (Kejuaraan & Lomba)', 'tokoku' ); ?></option>
                                    <option value="Medali Custom"><?php _e( 'Medali Penghargaan / Wisuda', 'tokoku' ); ?></option>
                                    <option value="Souvenir & Merchandise Lainnya"><?php _e( 'Souvenir / Merchandise Lainnya', 'tokoku' ); ?></option>
                                    <option value="Konsultasi Desain & Penawaran"><?php _e( 'Belum Yakin (Ingin Rekomendasi)', 'tokoku' ); ?></option>
                                </select>
                            </div>
                            <div class="jp-form__field">
                                <label for="jp_quantity" class="jp-label"><?php _e( 'Estimasi Jumlah (Pcs)', 'tokoku' ); ?> <span class="jp-required">*</span></label>
                                <input type="number" id="jp_quantity" name="quantity" class="jp-input" placeholder="Contoh: 1, 10, 50..." min="1" required>
                            </div>
                        </div>

                        <div class="jp-form__field">
                            <label for="jp_deadline" class="jp-label"><?php _e( 'Target Tanggal Acara / Kebutuhan', 'tokoku' ); ?></label>
                            <input type="text" id="jp_deadline" name="deadline" class="jp-input" placeholder="Contoh: 25 Oktober 2026 atau Mendesak 3 hari">
                        </div>

                        <div class="jp-form__field">
                            <label for="jp_notes" class="jp-label"><?php _e( 'Keterangan / Tulisan Plakat / Catatan Khusus', 'tokoku' ); ?></label>
                            <textarea id="jp_notes" name="notes" class="jp-textarea" rows="4" placeholder="Tuliskan nama acara, teks penghargaan, logo yang akan dipakai, atau pertanyaan khusus yang ingin Anda tanyakan..."></textarea>
                        </div>

                        <button type="submit" id="jp-wa-submit-btn" class="jp-btn jp-btn--wa jp-btn--lg jp-btn--block">
                            <?php echo tokoku_page_icon( 'send', 18 ); // phpcs:ignore ?>
                            <span><?php echo esc_html( $contact_data['form_btn'] ); ?></span>
                        </button>

                        <?php if ( ! empty( $contact_data['form_disclaimer'] ) ) : ?>
                            <p class="jp-form__disclaimer">
                                <?php echo tokoku_page_icon( 'shield', 14 ); // phpcs:ignore ?>
                                <?php echo esc_html( $contact_data['form_disclaimer'] ); ?>
                            </p>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Info Workshop & Jalur Agen -->
                <div class="jp-contact-side">

                    <!-- Info Alamat Workshop -->
                    <?php if ( $address ) : 
                        $maps_link = ! empty( $contact['maps_url'] ) ? $contact['maps_url'] : 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address );
                    ?>
                        <div class="jp-card jp-card--bordered">
                            <span class="jp-card__icon"><?php echo tokoku_page_icon( 'pin', 24 ); // phpcs:ignore ?></span>
                            <span class="jp-eyebrow"><?php _e( 'Lokasi & Workshop', 'tokoku' ); ?></span>
                            <h3 class="jp-card__title"><?php _e( 'Alamat Workshop & Kantor', 'tokoku' ); ?></h3>
                            <p class="jp-card__text"><?php echo nl2br( esc_html( $address ) ); ?></p>
                            <div class="jp-card__actions">
                                <button type="button" class="jp-btn jp-btn--xs jp-btn--ghost jp-copy-btn" data-copy="<?php echo esc_attr( $address ); ?>">
                                    <?php echo tokoku_page_icon( 'copy', 14 ); // phpcs:ignore ?> <span><?php _e( 'Salin Alamat', 'tokoku' ); ?></span>
                                </button>
                                <a class="jp-btn jp-btn--xs jp-btn--primary" href="<?php echo esc_url( $maps_link ); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php _e( 'Buka di Google Maps', 'tokoku' ); ?> <?php echo tokoku_page_icon( 'arrow', 14 ); // phpcs:ignore ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Jalur Kontak Customer Relation Officer -->
                    <?php if ( ! empty( $agents ) ) : ?>
                        <div class="jp-card jp-card--bordered">
                            <span class="jp-card__icon"><?php echo tokoku_page_icon( 'users', 24 ); // phpcs:ignore ?></span>
                            <span class="jp-eyebrow"><?php _e( 'Jalur Konsultasi', 'tokoku' ); ?></span>
                            <h3 class="jp-card__title"><?php _e( 'Customer Relation Officer (CRO)', 'tokoku' ); ?></h3>
                            <p class="jp-card__text"><?php echo esc_html( get_theme_mod( 'tokoku_hubungi_kami_desc', __( 'Hubungi petugas kami yang siap melayani dengan pendekatan personal dan responsif:', 'tokoku' ) ) ); ?></p>
                            
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
                                            <?php echo tokoku_page_icon( 'whatsapp', 16 ); // phpcs:ignore ?> <?php _e( 'Chat', 'tokoku' ); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Media Sosial -->
                    <?php if ( ! empty( $contact['socials'] ) ) : ?>
                        <div class="jp-card jp-card--bordered">
                            <span class="jp-eyebrow"><?php _e( 'Terhubung di Media Sosial', 'tokoku' ); ?></span>
                            <h3 class="jp-card__title"><?php _e( 'Ikuti Portofolio & Karya Terbaru', 'tokoku' ); ?></h3>
                            <div class="jp-social-badges">
                                <?php foreach ( $contact['socials'] as $network => $url ) : 
                                    $net_label = 'twitter' === $network ? 'X (Twitter)' : ucfirst( $network );
                                ?>
                                    <a href="<?php echo esc_url( $url ); ?>" class="jp-social-badge jp-social-badge--<?php echo esc_attr( $network ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $net_label ); ?>">
                                        <span class="jp-social-badge__icon"><?php echo tokoku_page_icon( $network, 18 ); // phpcs:ignore ?></span>
                                        <span class="jp-social-badge__name"><?php echo esc_html( $net_label ); ?></span>
                                        <span class="jp-social-badge__arrow"><?php echo tokoku_page_icon( 'arrow', 13 ); // phpcs:ignore ?></span>
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
    <?php if ( ! empty( $faq_items ) ) : ?>
        <section class="jp-section">
            <div class="container jp-container-narrow">
                <div class="jp-section__head">
                    <span class="jp-eyebrow"><?php echo esc_html( $contact_data['faq_eyebrow'] ); ?></span>
                    <h2 class="jp-section__title"><?php echo esc_html( $contact_data['faq_title'] ); ?></h2>
                    <p class="jp-section__sub"><?php echo esc_html( $contact_data['faq_sub'] ); ?></p>
                </div>

                <div class="jp-faq-accordion">
                    <?php foreach ( $faq_items as $idx => $faq ) : ?>
                        <details class="jp-faq-item" <?php echo 0 === $idx ? 'open' : ''; ?>>
                            <summary class="jp-faq-question">
                                <span><?php echo esc_html( $faq['q'] ); ?></span>
                                <span class="jp-faq-arrow" aria-hidden="true"><?php echo tokoku_page_icon( 'chevron', 16 ); // phpcs:ignore ?></span>
                            </summary>
                            <div class="jp-faq-answer">
                                <p><?php echo nl2br( esc_html( $faq['a'] ) ); ?></p>
                            </div>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Extra Content from Editor (if any) -->
    <?php
    while ( have_posts() ) :
        the_post();
        if ( '' !== trim( get_the_content() ) ) :
            ?>
            <section class="jp-section jp-section--soft">
                <div class="container jp-container-narrow">
                    <div class="jp-prose"><?php the_content(); ?></div>
                </div>
            </section>
            <?php
        endif;
    endwhile;
    ?>

    <!-- Bottom CTA -->
    <section class="jp-section jp-section--tight">
        <div class="container">
            <div class="jp-cta">
                <div class="jp-cta__glow" aria-hidden="true"></div>
                <div class="jp-cta__text">
                    <h2><?php echo esc_html( $contact_data['cta_title'] ); ?></h2>
                    <p><?php echo esc_html( $contact_data['cta_sub'] ); ?></p>
                </div>
                <div class="jp-cta__actions">
                    <a class="jp-btn jp-btn--light" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo tokoku_page_icon( 'whatsapp', 18 ); // phpcs:ignore ?> <?php echo esc_html( $contact_data['cta_btn1_text'] ); ?>
                    </a>
                    <a class="jp-btn jp-btn--outline-light" href="<?php echo esc_url( tokoku_static_page_url( 'page-templates/template-tentang.php' ) ); ?>">
                        <?php echo esc_html( $contact_data['cta_btn2_text'] ); ?> <?php echo tokoku_page_icon( 'arrow', 18 ); // phpcs:ignore ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
