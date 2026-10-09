    <?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Column 1: Brand & Social -->
                <div class="footer-column footer-brand">
                    <div class="footer-logo">
                        <?php 
                        if ( has_custom_logo() ) {
                            the_custom_logo();
                        } else {
                            echo '<h2 class="footer-site-title">' . get_bloginfo('name') . '</h2>';
                        }
                        ?>
                    </div>
                    
                    <ul class="contact-info" style="margin-bottom: 25px;">
                        <?php 
                        $address = get_theme_mod('tokoku_store_address');
                        if ($address) : ?>
                            <li style="margin-bottom: 10px; align-items: flex-start;">
                                <span><?php echo nl2br(esc_html($address)); ?></span>
                            </li>
                        <?php endif; ?>
                        
                        <?php 
                        $email = get_theme_mod('tokoku_store_email');
                        if ($email) : ?>
                            <li style="display: flex; gap: 8px; align-items: center;">
                                <?php echo tokoku_icon( 'envelope', 18, '', 'style="color: var(--primary); flex-shrink: 0;"' ); ?>
                                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <h4 class="footer-title" style="margin-bottom: 15px; font-size: 1rem; text-transform: uppercase;">SOSIAL MEDIA</h4>
                    <div class="social-links">
                        <?php
                        $social_keys = array( 'facebook', 'twitter', 'linkedin', 'youtube', 'instagram', 'tiktok' );
                        foreach ( $social_keys as $key ) {
                            $link = get_theme_mod( "tokoku_social_$key" );
                            if ( $link ) {
                                $label = ( 'twitter' === $key ) ? 'Twitter' : ucfirst( $key );
                                echo '<a href="' . esc_url( $link ) . '" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $label ) . '">';
                                echo tokoku_brand_icon( $key, 20 );
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Column 2: Hubungi Kami -->
                <div class="footer-column footer-contact">
                    <h4 class="footer-title" style="text-transform: uppercase;">HUBUNGI KAMI</h4>
                    <?php 
                    $desc = get_theme_mod('tokoku_hubungi_kami_desc', 'Customer Relation Officer (CRO) kami siap membantu Anda.');
                    if ($desc) : ?>
                        <p style="font-size: 0.85rem; color: var(--text2); margin-bottom: 20px; font-style: italic;">
                            <?php echo esc_html($desc); ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="contact-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 30px;">
                        <?php 
                        for ($i = 1; $i <= 5; $i++) {
                            $name = get_theme_mod("tokoku_contact_name_{$i}");
                            $wa = get_theme_mod("tokoku_contact_wa_{$i}");
                            if ($name && $wa) {
                                echo '<a href="https://wa.me/' . esc_attr($wa) . '" target="_blank" rel="noopener" style="display:flex; align-items:center; gap:8px; color:var(--text2); font-size:0.9rem; text-decoration:none;">';
                                echo tokoku_icon( 'whatsapp', 18, '', 'style="color: #25D366; flex-shrink: 0;"' ); 
                                echo esc_html($name);
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>

                    <h4 class="footer-title" style="margin-bottom: 15px; text-transform: uppercase;">JAM OPERASIONAL</h4>
                    <ul class="contact-info" style="font-size: 0.85rem;">
                        <?php for ($j=1; $j<=3; $j++) : 
                            $jam = get_theme_mod("tokoku_jam_op_{$j}");
                            if ($jam) : ?>
                                <li style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
                                    <?php echo tokoku_icon( 'clock', 18, '', 'style="color: var(--primary); flex-shrink: 0;"' ); ?>
                                    <span><?php echo esc_html($jam); ?></span>
                                </li>
                        <?php endif; endfor; ?>
                    </ul>
                </div>

                <!-- Column 3: Tentang 1Souvenir -->
                <div class="footer-column">
                    <h4 class="footer-title" style="text-transform: uppercase;">TENTANG KAMI</h4>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer_about',
                        'container'      => false,
                        'menu_class'     => 'footer-nav-list',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    ));
                    ?>
                </div>

                <!-- Column 4: Pusat Bantuan -->
                <div class="footer-column">
                    <h4 class="footer-title" style="text-transform: uppercase;">PUSAT BANTUAN</h4>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer_help',
                        'container'      => false,
                        'menu_class'     => 'footer-nav-list',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    ));
                    ?>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="copyright">
                    <?php
                    $copyright = get_theme_mod('tokoku_footer_copyright', '© {year} TokoKu. All rights reserved.');
                    echo str_replace('{year}', date('Y'), esc_html($copyright));
                    ?>
                </p>
            </div>
        </div>
    </footer>

    <!-- Floating Action Buttons (Desktop) -->
    <div class="floating-actions-desktop">
        <button id="scroll-to-top" class="scroll-to-top" aria-label="Scroll to Top">
            <?php echo tokoku_icon( 'chevron-up', 20 ); ?>
        </button>
        <div class="wa-float">
            <?php 
            $wa_float_text = get_theme_mod( 'tokoku_wa_float_text', 'Chat dengan kami' );
            ?>
            <a href="https://wa.me/<?php echo esc_attr( get_theme_mod( 'tokoku_wa_number', '6281234567890' ) ); ?>" class="wa-float__btn" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ! empty( $wa_float_text ) ? $wa_float_text : 'Chat WhatsApp' ); ?>">
                <?php if ( ! empty( $wa_float_text ) ) : ?>
                    <span class="wa-float__tooltip"><?php echo esc_html( $wa_float_text ); ?></span>
                <?php endif; ?>
                <?php echo tokoku_icon( 'whatsapp', 32, '', 'style="color: #ffffff;"' ); ?>
            </a>
        </div>
    </div>

    <?php get_template_part('template-parts/whatsapp-modal'); ?>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav" aria-label="Navigasi Bawah">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-item nav-home">
            <div class="nav-icon-wrap">
                <?php echo tokoku_icon( 'house', 22 ); ?>
            </div>
            <span>Home</span>
        </a>
        <a href="<?php echo esc_url(home_url('/#categories')); ?>" class="nav-item nav-kategori">
            <div class="nav-icon-wrap">
                <?php echo tokoku_icon( 'border-all', 22 ); ?>
            </div>
            <span>Kategori</span>
        </a>
        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('tokoku_wa_number', '6281234567890')); ?>" class="nav-item nav-whatsapp" target="_blank" rel="noopener">
            <div class="nav-icon-wrap">
                <?php echo tokoku_icon( 'whatsapp', 22, '', 'style="color: #25D366;"' ); ?>
            </div>
            <span>WhatsApp</span>
        </a>
        <a href="javascript:void(0)" id="bottom-menu-toggle" class="nav-item nav-menu" aria-label="Menu">
            <div class="nav-icon-wrap">
                <?php echo tokoku_icon( 'bars', 22 ); ?>
            </div>
            <span>Menu</span>
        </a>
    </nav>

    <!-- Global Toast Notification -->
    <div id="tokoku-toast" class="tokoku-toast" role="alert" aria-live="assertive">
        <?php echo tokoku_icon( 'check', 20, 'toast-check-icon', 'style="color: #22c55e;"' ); ?>
        <span id="tokoku-toast-text">Tautan berhasil disalin!</span>
    </div>

    <?php wp_footer(); ?>
</body>
</html>