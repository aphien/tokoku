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
                            <li>
                                <span class="dashicons dashicons-email" style="color: #c07a3c;"></span>
                                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <h4 class="footer-title" style="margin-bottom: 15px; font-size: 1rem; text-transform: uppercase;">SOSIAL MEDIA</h4>
                    <div class="social-links">
                        <?php
                        $socials = array(
                            'facebook'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
                            'twitter'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>',
                            'linkedin'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>',
                            'youtube'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.42a2.78 2.78 0 0 0-1.94 2C1 8.11 1 12 1 12s0 3.89.46 5.58a2.78 2.78 0 0 0 1.94 2c1.72.42 8.6.42 8.6.42s6.88 0 8.6-.42a2.78 2.78 0 0 0 1.94-2C23 15.89 23 12 23 12s0-3.89-.46-5.58z"></path><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"></polygon></svg>',
                            'instagram' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
                            'tiktok'    => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>'
                        );
                        foreach ($socials as $key => $svg) {
                            $link = get_theme_mod("tokoku_social_$key");
                            if ($link) {
                                echo '<a href="' . esc_url($link) . '" class="social-link" target="_blank" aria-label="' . esc_attr(ucfirst($key)) . '">';
                                echo $svg;
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
                                echo '<a href="https://wa.me/' . esc_attr($wa) . '" target="_blank" style="display:flex; align-items:center; gap:8px; color:var(--text2); font-size:0.9rem; text-decoration:none;">';
                                echo '<span class="dashicons dashicons-whatsapp" style="color: #c07a3c;"></span>'; 
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
                                <li style="margin-bottom: 8px;">
                                    <span class="dashicons dashicons-portfolio" style="color: #c07a3c;"></span>
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
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        </button>
        <div class="wa-float">
            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('tokoku_wa_number', '6281234567890')); ?>" class="wa-float__btn" target="_blank" aria-label="WhatsApp">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.012 2c-5.508 0-9.987 4.479-9.987 9.987 0 1.763.459 3.42 1.261 4.864l-1.286 4.698 4.814-1.263c1.402.766 2.998 1.199 4.698 1.199 5.508 0 9.988-4.479 9.988-9.987s-4.48-9.987-9.988-9.987zm5.541 14.22c-.226.639-1.318 1.171-1.812 1.233-.493.062-1.008.093-2.909-.643-2.316-.902-3.805-3.238-3.921-3.393-.116-.154-.949-1.264-.949-2.41 0-1.147.604-1.711.821-1.942.217-.231.472-.288.63-.288.157 0 .315.002.45.011.145.009.341-.054.534.412.193.466.66 1.603.718 1.72.059.117.098.252.02.408-.079.157-.118.255-.236.39-.118.136-.248.303-.354.407-.117.117-.24.244-.103.48.137.236.608 1.002 1.306 1.623.897.799 1.654 1.045 1.891 1.162.236.117.375.098.514-.06.139-.158.597-.696.757-.932.159-.236.319-.199.54-.117.221.083 1.401.66 1.641.779.24.118.399.176.458.277.059.102.059.589-.167 1.228z" />
                </svg>
            </a>
        </div>
    </div>

    <?php get_template_part('template-parts/whatsapp-modal'); ?>

    <!-- Mobile Bottom Navigation (v1.6.7 Style) -->
    <div class="bottom-nav">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-item <?php echo is_front_page() ? 'active' : ''; ?>">
            <span class="dashicons dashicons-admin-home"></span>
            <span>Home</span>
        </a>
        <a href="<?php echo esc_url(home_url('/#categories')); ?>" class="nav-item">
            <span class="dashicons dashicons-category"></span>
            <span>Kategori</span>
        </a>
        <a href="javascript:void(0)" id="mobile-search-nav-trigger" class="nav-item">
            <span class="dashicons dashicons-search"></span>
            <span>Pencarian</span>
        </a>
        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('tokoku_wa_number', '6281234567890')); ?>" class="nav-item nav-whatsapp" target="_blank">
            <span class="dashicons dashicons-whatsapp"></span>
            <span>WhatsApp</span>
        </a>
    </div>

    <?php wp_footer(); ?>
</body>
</html>