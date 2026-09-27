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
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <h4 class="footer-title" style="margin-bottom: 15px; font-size: 1rem; text-transform: uppercase;">SOSIAL MEDIA</h4>
                    <div class="social-links">
                        <?php
                        $socials = array(
                            'facebook'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
                            'twitter'   => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
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
                                echo '<a href="https://wa.me/' . esc_attr($wa) . '" target="_blank" rel="noopener" style="display:flex; align-items:center; gap:8px; color:var(--text2); font-size:0.9rem; text-decoration:none;">';
                                echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="#25D366" style="flex-shrink: 0;"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/></svg>'; 
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
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
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
            <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('tokoku_wa_number', '6281234567890')); ?>" class="wa-float__btn" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="#ffffff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/>
                </svg>
            </a>
        </div>
    </div>

    <?php get_template_part('template-parts/whatsapp-modal'); ?>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav" aria-label="Navigasi Bawah">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-item nav-home <?php echo is_front_page() ? 'active' : ''; ?>">
            <div class="nav-icon-wrap">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <span>Home</span>
        </a>
        <a href="<?php echo esc_url(home_url('/#categories')); ?>" class="nav-item nav-kategori">
            <div class="nav-icon-wrap">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>
            </div>
            <span>Kategori</span>
        </a>
        <a href="https://wa.me/<?php echo esc_attr(get_theme_mod('tokoku_wa_number', '6281234567890')); ?>" class="nav-item nav-whatsapp" target="_blank" rel="noopener">
            <div class="nav-icon-wrap">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="#25D366" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm.01 1.67c4.55 0 8.25 3.7 8.25 8.24 0 2.2-.86 4.27-2.42 5.82a8.196 8.196 0 0 1-5.83 2.42c-1.48 0-2.93-.39-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.188 8.188 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24zm4.58 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.12-.14.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.12-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43l-.48-.01c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.09 0 1.24.9 2.43 1.03 2.6.12.17 1.77 2.7 4.28 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.12-.23-.19-.48-.31z"/>
                </svg>
            </div>
            <span>WhatsApp</span>
        </a>
        <a href="javascript:void(0)" id="bottom-menu-toggle" class="nav-item nav-menu" aria-label="Menu">
            <div class="nav-icon-wrap">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" y1="6" x2="20" y2="6"></line>
                    <line x1="4" y1="12" x2="16" y2="12"></line>
                    <line x1="4" y1="18" x2="20" y2="18"></line>
                </svg>
            </div>
            <span>Menu</span>
        </a>
    </nav>

    <!-- Global Toast Notification -->
    <div id="tokoku-toast" class="tokoku-toast" role="alert" aria-live="assertive">
        <svg class="toast-check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span id="tokoku-toast-text">Tautan berhasil disalin!</span>
    </div>

    <?php wp_footer(); ?>
</body>
</html>