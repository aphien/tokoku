<?php
/**
 * Template Name: Tentang Kami
 * Template Post Type: page
 *
 * Halaman profil brand: cerita, nilai, proses kerja, dan visi-misi.
 * Semua bagian dapat dikelola langsung dari menu admin TokoKu > Template Tentang Kami.
 * Konten dari editor halaman WordPress (jika diisi) akan tampil sebagai bagian "Cerita Lengkap".
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$brand       = tokoku_page_brand();
$contact     = tokoku_page_contact();
$about_data  = tokoku_get_about_page_data();

$catalog_url = ! empty( $about_data['hero_btn1_url'] ) ? $about_data['hero_btn1_url'] : ( get_post_type_archive_link( 'produk' ) ? get_post_type_archive_link( 'produk' ) : home_url( '/' ) );
$wa_message  = rawurlencode( 'Halo ' . $brand . ', saya ingin berkonsultasi mengenai pembuatan plakat custom.' );
$wa_url      = 'https://wa.me/' . $contact['wa'] . '?text=' . $wa_message;

$stats   = $about_data['stats'];
$values  = $about_data['values'];
$steps   = $about_data['steps'];
$clients = $about_data['clients'];
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
                    <span class="jp-badge"><?php echo tokoku_page_icon( 'award', 15 ); // phpcs:ignore ?> <?php echo esc_html( $about_data['hero_badge'] ); ?></span>
                    <h1 class="jp-hero__title"><?php echo wp_kses_post( $about_data['hero_title'] ); ?></h1>
                    <p class="jp-hero__lead">
                        <?php echo esc_html( $about_data['hero_desc'] ); ?>
                    </p>
                    <div class="jp-hero__actions">
                        <a class="jp-btn jp-btn--primary" href="<?php echo esc_url( $catalog_url ); ?>">
                            <?php echo esc_html( $about_data['hero_btn1_text'] ); ?> <?php echo tokoku_page_icon( 'arrow', 18 ); // phpcs:ignore ?>
                        </a>
                        <a class="jp-btn jp-btn--ghost" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo tokoku_page_icon( 'whatsapp', 18 ); // phpcs:ignore ?> <?php echo esc_html( $about_data['hero_btn2_text'] ); ?>
                        </a>
                    </div>
                </div>

                <div class="jp-plaque jp-reveal" aria-hidden="true">
                    <div class="jp-plaque__shine"></div>
                    <div class="jp-plaque__ribbon"><?php echo tokoku_page_icon( 'award', 30 ); // phpcs:ignore ?></div>
                    <span class="jp-plaque__eyebrow"><?php echo esc_html( $about_data['plaque_eyebrow'] ); ?></span>
                    <strong class="jp-plaque__title"><?php echo esc_html( $about_data['plaque_title'] ); ?></strong>
                    <span class="jp-plaque__line"></span>
                    <span class="jp-plaque__text"><?php echo esc_html( $about_data['plaque_text'] ); ?></span>
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
                        <p><?php echo esc_html( $about_data['quote_text'] ); ?></p>
                        <span class="jp-quote-card__author"><?php echo esc_html( $about_data['quote_author'] ); ?></span>
                    </div>
                </div>
                <div class="jp-split__content jp-reveal">
                    <span class="jp-eyebrow"><?php echo esc_html( $about_data['story_eyebrow'] ); ?></span>
                    <h2 class="jp-section__title"><?php echo wp_kses_post( $about_data['story_title'] ); ?></h2>
                    <div class="jp-prose">
                        <?php if ( ! empty( $about_data['story_content'] ) ) : ?>
                            <?php echo wpautop( wp_kses_post( $about_data['story_content'] ) ); ?>
                        <?php else : ?>
                            <p><?php echo esc_html( $brand ); ?> lahir dari pengalaman melihat betapa berartinya sebuah penghargaan bagi penerimanya — senyum bangga saat menerima plakat di atas panggung, atau rasa haru ketika sebuah dedikasi akhirnya diakui.</p>
                            <p>Dari situ kami bertekad menghadirkan layanan pembuatan plakat yang <strong>mudah, cepat, dan berkualitas tinggi</strong>. Tanpa proses berbelit, tanpa harus datang ke workshop — cukup konsultasi melalui WhatsApp, dan tim kami akan mewujudkan ide Anda menjadi karya nyata yang siap dibanggakan.</p>
                            <p>Hari ini, kami melayani beragam kebutuhan: plakat akrilik, kayu, resin, hingga piala dan trophy untuk instansi, perusahaan, institusi pendidikan, komunitas, dan hadiah personal ke seluruh penjuru Indonesia.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="jp-section jp-section--soft">
        <div class="container">
            <div class="jp-section__head jp-reveal">
                <span class="jp-eyebrow"><?php echo esc_html( $about_data['values_eyebrow'] ); ?></span>
                <h2 class="jp-section__title"><?php echo esc_html( $about_data['values_title'] ); ?></h2>
                <p class="jp-section__sub"><?php echo esc_html( $about_data['values_sub'] ); ?></p>
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
                <span class="jp-eyebrow"><?php echo esc_html( $about_data['steps_eyebrow'] ); ?></span>
                <h2 class="jp-section__title"><?php echo esc_html( $about_data['steps_title'] ); ?></h2>
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
                    <span class="jp-eyebrow"><?php echo esc_html( $about_data['visi_eyebrow'] ); ?></span>
                    <h2 class="jp-vm__title"><?php echo esc_html( $about_data['visi_text'] ); ?></h2>
                </div>
                <div class="jp-vm__card jp-reveal">
                    <span class="jp-card__icon"><?php echo tokoku_page_icon( 'target', 22 ); // phpcs:ignore ?></span>
                    <span class="jp-eyebrow"><?php echo esc_html( $about_data['misi_eyebrow'] ); ?></span>
                    <ul class="jp-checklist">
                        <?php foreach ( $about_data['misi_points'] as $misi_point ) : ?>
                            <li><?php echo tokoku_page_icon( 'check', 16 ); // phpcs:ignore ?><span><?php echo esc_html( $misi_point ); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Client Logos Slider (Marquee) -->
    <?php
    $site_name    = esc_attr( get_bloginfo( 'name' ) );
    $client_logos = array();
    for ( $i = 1; $i <= 50; $i++ ) {
        $logo = get_theme_mod( "tokoku_client_logo_{$i}" );
        if ( $logo ) {
            $client_logos[] = array(
                'url' => $logo,
                'alt' => sprintf( esc_attr__( 'Klien & Mitra %s - Logo %d', 'tokoku' ), $site_name, $i ),
            );
        }
    }
    ?>
    <section class="logos-section jp-clients-section">
        <div class="container">
            <div class="jp-section__head jp-reveal">
                <span class="jp-eyebrow"><?php echo esc_html( $about_data['clients_eyebrow'] ); ?></span>
                <h2 class="jp-section__title"><?php echo esc_html( $about_data['clients_title'] ); ?></h2>
                <p class="jp-section__sub"><?php echo esc_html( $about_data['clients_sub'] ); ?></p>
            </div>
            
            <div class="logo-carousel-wrapper">
                <div class="logo-track">
                    <?php
                    if ( ! empty( $client_logos ) ) {
                        // Render original list
                        foreach ( $client_logos as $clogo ) {
                            echo '<div class="logo-slide"><img src="' . esc_url( $clogo['url'] ) . '" alt="' . esc_attr( $clogo['alt'] ) . '" width="160" height="60" loading="lazy" decoding="async"></div>';
                        }
                        // Duplicate for seamless infinite loop
                        foreach ( $client_logos as $clogo ) {
                            echo '<div class="logo-slide"><img src="' . esc_url( $clogo['url'] ) . '" alt="' . esc_attr( $clogo['alt'] ) . '" width="160" height="60" loading="lazy" decoding="async"></div>';
                        }
                    } else {
                        $sample_partners = array(
                            'Kementerian RI',
                            'BUMN Indonesia',
                            'Universitas Negeri',
                            'Pemerintah Daerah',
                            'Bank Nasional',
                            'Pertamina / PLN',
                            'Telkom Group',
                            'Perusahaan Swasta & Komunitas',
                        );
                        $sample_marquee = array_merge( $sample_partners, $sample_partners );
                        foreach ( $sample_marquee as $partner ) {
                            echo '<div class="logo-slide logo-slide--text"><span class="logo-slide-name">' . esc_html( $partner ) . '</span></div>';
                        }
                    }
                    ?>
                </div>
            </div>

            <?php if ( ! empty( $clients ) ) : ?>
                <div style="margin-top: 36px;">
                    <ul class="jp-chips jp-reveal">
                        <?php foreach ( $clients as $client ) : ?>
                            <li class="jp-chip"><?php echo tokoku_page_icon( 'check', 14 ); // phpcs:ignore ?> <?php echo esc_html( $client ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
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
                        <span class="jp-eyebrow"><?php _e( 'Cerita Lengkap', 'tokoku' ); ?></span>
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
                    <h2><?php echo esc_html( $about_data['cta_title'] ); ?></h2>
                    <p><?php echo esc_html( $about_data['cta_sub'] ); ?></p>
                </div>
                <div class="jp-cta__actions">
                    <a class="jp-btn jp-btn--light" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo tokoku_page_icon( 'whatsapp', 18 ); // phpcs:ignore ?> <?php echo esc_html( $about_data['cta_btn1_text'] ); ?>
                    </a>
                    <a class="jp-btn jp-btn--outline-light" href="<?php echo esc_url( tokoku_static_page_url( 'page-templates/template-kontak.php' ) ); ?>">
                        <?php echo esc_html( $about_data['cta_btn2_text'] ); ?> <?php echo tokoku_page_icon( 'arrow', 18 ); // phpcs:ignore ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
