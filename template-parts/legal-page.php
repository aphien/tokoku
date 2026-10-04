<?php
/**
 * Template Part: Tata letak dokumen legal (Syarat & Ketentuan / Kebijakan Privasi)
 *
 * Argumen ($args):
 * - badge    (string) Label kecil di atas judul.
 * - icon     (string) Nama ikon hero.
 * - title    (string) Judul H1.
 * - lead     (string) Paragraf pembuka.
 * - updated  (string) Tanggal pembaruan terakhir.
 * - summary  (array)  Poin ringkasan singkat.
 * - sections (array)  Daftar bagian: [ 'id' => '', 'title' => '', 'content' => '' ].
 * - related  (array)  Tautan dokumen terkait: [ 'label' => '', 'url' => '', 'icon' => '' ].
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$args     = wp_parse_args( $args ?? array(), array(
    'badge'    => '',
    'icon'     => 'file',
    'title'    => get_the_title(),
    'lead'     => '',
    'updated'  => '',
    'summary'  => array(),
    'sections' => array(),
    'related'  => array(),
) );
$contact  = tokoku_page_contact();
$sections = (array) $args['sections'];

// Estimasi waktu baca (±200 kata/menit).
$word_total = 0;
foreach ( $sections as $section ) {
    $word_total += str_word_count( wp_strip_all_tags( $section['content'] ) );
}
$read_minutes = max( 1, (int) ceil( $word_total / 200 ) );
$extra        = trim( get_post_field( 'post_content', get_the_ID() ) );
?>

<main id="main-content" class="site-main jp-page jp-legal">
    <div class="jp-progress" aria-hidden="true"><span class="jp-progress__bar"></span></div>

    <!-- Hero -->
    <section class="jp-hero jp-hero--compact">
        <div class="jp-hero__glow" aria-hidden="true"></div>
        <div class="container">
            <nav class="jp-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?php echo esc_html( $args['title'] ); ?></span>
            </nav>

            <div class="jp-hero__inner jp-reveal">
                <span class="jp-badge"><?php echo tokoku_page_icon( $args['icon'], 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $args['badge'] ); ?></span>
                <h1 class="jp-hero__title"><?php echo esc_html( $args['title'] ); ?></h1>
                <?php if ( $args['lead'] ) : ?>
                    <p class="jp-hero__lead"><?php echo esc_html( $args['lead'] ); ?></p>
                <?php endif; ?>

                <ul class="jp-meta-chips">
                    <?php if ( $args['updated'] ) : ?>
                        <li><?php echo tokoku_page_icon( 'calendar', 15 ); // phpcs:ignore ?> Berlaku sejak <?php echo esc_html( $args['updated'] ); ?></li>
                    <?php endif; ?>
                    <li><?php echo tokoku_page_icon( 'book', 15 ); // phpcs:ignore ?> ± <?php echo esc_html( $read_minutes ); ?> menit membaca</li>
                    <li><?php echo tokoku_page_icon( 'list', 15 ); // phpcs:ignore ?> <?php echo esc_html( count( $sections ) ); ?> bagian</li>
                </ul>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="jp-legal__layout">

            <!-- Daftar Isi -->
            <aside class="jp-toc" aria-label="Daftar isi">
                <details class="jp-toc__box" open>
                    <summary class="jp-toc__title">
                        <?php echo tokoku_page_icon( 'list', 16 ); // phpcs:ignore ?>
                        <span>Daftar Isi</span>
                    </summary>
                    <ol class="jp-toc__list">
                        <?php foreach ( $sections as $i => $section ) : ?>
                            <li>
                                <a href="#<?php echo esc_attr( $section['id'] ); ?>" class="jp-toc__link" data-target="<?php echo esc_attr( $section['id'] ); ?>">
                                    <span class="jp-toc__num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
                                    <span class="jp-toc__text"><?php echo esc_html( $section['title'] ); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </details>

                <?php if ( ! empty( $args['related'] ) ) : ?>
                    <div class="jp-toc__related">
                        <span class="jp-toc__related-label">Dokumen Terkait</span>
                        <?php foreach ( $args['related'] as $link ) : ?>
                            <a href="<?php echo esc_url( $link['url'] ); ?>" class="jp-toc__related-link">
                                <?php echo tokoku_page_icon( $link['icon'], 16 ); // phpcs:ignore ?>
                                <span><?php echo esc_html( $link['label'] ); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </aside>

            <!-- Konten Dokumen -->
            <article class="jp-legal__content">

                <?php if ( ! empty( $args['summary'] ) ) : ?>
                    <div class="jp-summary jp-reveal">
                        <div class="jp-summary__head">
                            <span class="jp-summary__icon"><?php echo tokoku_page_icon( 'eye', 18 ); // phpcs:ignore ?></span>
                            <div>
                                <h2 class="jp-summary__title">Ringkasan Singkat</h2>
                                <p class="jp-summary__sub">Poin penting yang perlu Anda ketahui dalam 30 detik.</p>
                            </div>
                        </div>
                        <ul class="jp-summary__list">
                            <?php foreach ( $args['summary'] as $point ) : ?>
                                <li><?php echo tokoku_page_icon( 'check', 16 ); // phpcs:ignore ?><span><?php echo esc_html( $point ); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php foreach ( $sections as $i => $section ) : ?>
                    <section id="<?php echo esc_attr( $section['id'] ); ?>" class="jp-legal__section">
                        <h2 class="jp-legal__heading">
                            <span class="jp-legal__num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
                            <?php echo esc_html( $section['title'] ); ?>
                        </h2>
                        <div class="jp-prose">
                            <?php echo wp_kses_post( $section['content'] ); ?>
                        </div>
                    </section>
                <?php endforeach; ?>

                <?php if ( '' !== $extra ) : ?>
                    <section id="ketentuan-tambahan" class="jp-legal__section">
                        <h2 class="jp-legal__heading">
                            <span class="jp-legal__num">+</span>
                            Ketentuan Tambahan
                        </h2>
                        <div class="jp-prose">
                            <?php the_content(); ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- CTA -->
                <div class="jp-help-card jp-reveal">
                    <div class="jp-help-card__text">
                        <h2>Masih ada yang ingin ditanyakan?</h2>
                        <p>Tim kami siap menjelaskan setiap poin dengan bahasa yang sederhana. Jangan ragu untuk menghubungi kami.</p>
                    </div>
                    <div class="jp-help-card__actions">
                        <a class="jp-btn jp-btn--wa" href="<?php echo esc_url( 'https://wa.me/' . $contact['wa'] ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo tokoku_page_icon( 'whatsapp', 18 ); // phpcs:ignore ?> Tanya via WhatsApp
                        </a>
                        <a class="jp-btn jp-btn--ghost" href="<?php echo esc_url( tokoku_static_page_url( 'page-templates/template-kontak.php' ) ); ?>">
                            Halaman Kontak <?php echo tokoku_page_icon( 'arrow', 16 ); // phpcs:ignore ?>
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </div>
</main>
