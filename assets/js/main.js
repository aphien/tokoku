/**
 * TokoKu Main JavaScript
 * 
 * TABLE OF CONTENTS:
 * 1. SHARED LOGIC (Theme Toggle, Sticky Header)
 * 2. DESKTOP SPECIFIC LOGIC
 * 3. SHARED COMPONENTS LOGIC (Hero Slider, etc)
 * 4. MOBILE SPECIFIC LOGIC (Drawer, Bottom Nav, Testimonials)
 * 5. PWA & UTILITIES
 */

document.addEventListener('DOMContentLoaded', function() {

    /* ==========================================================================
       1. SHARED LOGIC
       ========================================================================== */
    
    // 🌓 Theme Toggle (Light/Dark Mode)
    const modeToggles = document.querySelectorAll('.mode-toggle, #mode-toggle');
    const body = document.body;
    const html = document.documentElement;
    
    function applyTheme(theme) {
        if (theme === 'auto') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        body.classList.remove('theme-dark', 'theme-light');
        body.classList.add('theme-' + theme);
        html.classList.remove('theme-dark', 'theme-light');
        html.classList.add('theme-' + theme);
        if (typeof updateThemeColor === 'function') updateThemeColor();
    }

    const savedTheme = localStorage.getItem('tokoku-theme');
    if (savedTheme) {
        applyTheme(savedTheme);
    } else if (html.classList.contains('theme-dark')) {
        applyTheme('dark');
    }

    modeToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const isDark = body.classList.contains('theme-dark') || html.classList.contains('theme-dark');
            const nextTheme = isDark ? 'light' : 'dark';
            applyTheme(nextTheme);
            localStorage.setItem('tokoku-theme', nextTheme);
        });
    });

    // 🕒 Unified High-Performance Scroll Handler (Sticky Header & Scroll to Top)
    const header = document.querySelector('.site-header');
    const scrollTopBtn = document.getElementById('scroll-to-top');
    let scrollTicking = false;

    function handleScrollUpdates() {
        const currentScrollY = window.scrollY || window.pageYOffset;

        // Sticky Header
        if (header) {
            if (currentScrollY > 50) {
                header.classList.add('sticky');
            } else {
                header.classList.remove('sticky');
            }
        }

        // Scroll to Top Button
        if (scrollTopBtn) {
            if (currentScrollY > 300) {
                scrollTopBtn.classList.add('active');
            } else {
                scrollTopBtn.classList.remove('active');
            }
        }

        scrollTicking = false;
    }

    window.addEventListener('scroll', () => {
        if (!scrollTicking) {
            window.requestAnimationFrame(handleScrollUpdates);
            scrollTicking = true;
        }
    }, { passive: true });

    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /* ==========================================================================
       3. SHARED COMPONENTS LOGIC
       ========================================================================== */
    
    // 🎡 Hero Slider (Touch, Swipe, Mouse Drag, Keyboard & Responsive)
    const heroSlider = document.getElementById('home-slider') || document.querySelector('.hero-slider-section .slider-container');
    if (heroSlider) {
        const wrapper = heroSlider.querySelector('.slider-wrapper');
        const slides = heroSlider.querySelectorAll('.slide');
        const prevBtn = heroSlider.querySelector('.slider-prev');
        const nextBtn = heroSlider.querySelector('.slider-next');
        const dotsContainer = heroSlider.querySelector('.slider-dots');
        const progressBar = heroSlider.querySelector('.slider-progress-bar');
        
        const slideCount = slides.length;
        
        if (wrapper && slideCount > 0) {
            let currentIndex = 0;
            let slideInterval = null;
            const INTERVAL_TIME = 5000;
            let isPaused = false;
            let progressStartTime = 0;
            let progressReqId = null;

            // Otomatis menyesuaikan tinggi slider sesuai ukuran banner aktual
            function autoFitBannerRatio() {
                const firstImg = slides[0]?.querySelector('img');
                if (!firstImg) return;

                const applySize = (imgEl) => {
                    const w = imgEl.naturalWidth;
                    const h = imgEl.naturalHeight;
                    if (w && h && w > 0 && h > 0) {
                        const ratioStr = `${w} / ${h}`;
                        heroSlider.style.setProperty('--slider-ratio', ratioStr);
                        heroSlider.style.aspectRatio = ratioStr;
                        slides.forEach(s => {
                            s.style.setProperty('--slider-ratio', ratioStr);
                            s.style.aspectRatio = ratioStr;
                        });
                    }
                };

                if (firstImg.complete && firstImg.naturalWidth) {
                    applySize(firstImg);
                } else {
                    firstImg.addEventListener('load', () => applySize(firstImg));
                }
            }
            autoFitBannerRatio();
            window.addEventListener('resize', autoFitBannerRatio, { passive: true });

            // Jika hanya 1 slide atau kurang, sembunyikan navigasi
            if (slideCount <= 1) {
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
                if (dotsContainer) dotsContainer.style.display = 'none';
                const progressWrap = heroSlider.querySelector('.slider-progress');
                if (progressWrap) progressWrap.style.display = 'none';
                wrapper.style.cursor = 'default';
            } else {
                // Buat dot navigasi
                if (dotsContainer) {
                    dotsContainer.innerHTML = '';
                    slides.forEach((_, i) => {
                        const dot = document.createElement('button');
                        dot.type = 'button';
                        dot.classList.add('dot');
                        dot.setAttribute('role', 'tab');
                        dot.setAttribute('aria-label', `Slide ${i + 1}`);
                        dot.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
                        if (i === 0) dot.classList.add('active');
                        dot.addEventListener('click', (e) => {
                            e.preventDefault();
                            goToSlide(i);
                        });
                        dotsContainer.appendChild(dot);
                    });
                }

                const dots = dotsContainer ? dotsContainer.querySelectorAll('.dot') : [];

                function updateSlider(animate = true) {
                    if (animate) {
                        wrapper.style.transition = 'transform 0.45s cubic-bezier(0.25, 1, 0.5, 1)';
                    } else {
                        wrapper.style.transition = 'none';
                    }
                    wrapper.style.transform = `translate3d(-${currentIndex * 100}%, 0, 0)`;

                    dots.forEach((dot, i) => {
                        const isActive = (i === currentIndex);
                        dot.classList.toggle('active', isActive);
                        dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });

                    startProgressBar();
                }

                function nextSlide() {
                    currentIndex = (currentIndex + 1) % slideCount;
                    updateSlider(true);
                }

                function prevSlide() {
                    currentIndex = (currentIndex - 1 + slideCount) % slideCount;
                    updateSlider(true);
                }

                function goToSlide(index) {
                    if (index === currentIndex) return;
                    currentIndex = index;
                    updateSlider(true);
                    resetInterval();
                }

                function startProgressBar() {
                    if (!progressBar) return;
                    cancelAnimationFrame(progressReqId);
                    progressStartTime = performance.now();
                    progressBar.style.width = '0%';

                    function animateProgress(now) {
                        if (isPaused) {
                            progressReqId = requestAnimationFrame(animateProgress);
                            return;
                        }
                        const elapsed = now - progressStartTime;
                        const progress = Math.min((elapsed / INTERVAL_TIME) * 100, 100);
                        progressBar.style.width = `${progress}%`;
                        if (elapsed < INTERVAL_TIME) {
                            progressReqId = requestAnimationFrame(animateProgress);
                        }
                    }
                    progressReqId = requestAnimationFrame(animateProgress);
                }

                function resetInterval() {
                    clearInterval(slideInterval);
                    if (!isPaused) {
                        startProgressBar();
                        slideInterval = setInterval(nextSlide, INTERVAL_TIME);
                    }
                }

                function pauseSlider() {
                    isPaused = true;
                    clearInterval(slideInterval);
                }

                function resumeSlider() {
                    if (!isPaused) return;
                    isPaused = false;
                    resetInterval();
                }

                // Tombol Navigasi
                prevBtn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    prevSlide();
                    resetInterval();
                });

                nextBtn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    nextSlide();
                    resetInterval();
                });

                // Pause saat kursor berada di slider (desktop)
                heroSlider.addEventListener('mouseenter', pauseSlider);
                heroSlider.addEventListener('mouseleave', resumeSlider);

                // Pause jika tab browser diminimalkan/berpindah tab
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        pauseSlider();
                    } else {
                        resumeSlider();
                    }
                });

                // Navigasi Keyboard
                heroSlider.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft') {
                        prevSlide();
                        resetInterval();
                    } else if (e.key === 'ArrowRight') {
                        nextSlide();
                        resetInterval();
                    }
                });

                // 👆 Touch Gestures (Mobile Swipe)
                let touchStartX = 0;
                let touchStartY = 0;
                let touchDeltaX = 0;
                let touchDeltaY = 0;
                let isSwiping = false;
                let isHorizontalSwipe = null;
                let touchStartTime = 0;

                heroSlider.addEventListener('touchstart', (e) => {
                    if (e.touches.length !== 1) return;
                    touchStartX = e.touches[0].clientX;
                    touchStartY = e.touches[0].clientY;
                    touchDeltaX = 0;
                    touchDeltaY = 0;
                    isSwiping = true;
                    isHorizontalSwipe = null;
                    touchStartTime = Date.now();
                    pauseSlider();
                    wrapper.style.transition = 'none';
                }, { passive: true });

                heroSlider.addEventListener('touchmove', (e) => {
                    if (!isSwiping || e.touches.length !== 1) return;
                    touchDeltaX = e.touches[0].clientX - touchStartX;
                    touchDeltaY = e.touches[0].clientY - touchStartY;

                    if (isHorizontalSwipe === null) {
                        if (Math.abs(touchDeltaX) > 7 || Math.abs(touchDeltaY) > 7) {
                            isHorizontalSwipe = Math.abs(touchDeltaX) >= Math.abs(touchDeltaY);
                        }
                    }

                    if (isHorizontalSwipe) {
                        if (e.cancelable) e.preventDefault();
                        let resistance = 1;
                        if ((currentIndex === 0 && touchDeltaX > 0) || (currentIndex === slideCount - 1 && touchDeltaX < 0)) {
                            resistance = 0.35;
                        }
                        const offset = touchDeltaX * resistance;
                        wrapper.style.transform = `translate3d(calc(-${currentIndex * 100}% + ${offset}px), 0, 0)`;
                    }
                }, { passive: false });

                const handleSwipeEnd = () => {
                    if (!isSwiping) return;
                    isSwiping = false;
                    wrapper.style.transition = 'transform 0.45s cubic-bezier(0.25, 1, 0.5, 1)';

                    if (isHorizontalSwipe) {
                        const elapsed = Date.now() - touchStartTime;
                        const velocity = Math.abs(touchDeltaX) / elapsed;

                        if (Math.abs(touchDeltaX) > 40 || velocity > 0.35) {
                            if (touchDeltaX < 0) {
                                nextSlide();
                            } else {
                                prevSlide();
                            }
                        } else {
                            updateSlider(true);
                        }
                    } else {
                        updateSlider(true);
                    }

                    isHorizontalSwipe = null;
                    resumeSlider();
                };

                heroSlider.addEventListener('touchend', handleSwipeEnd, { passive: true });
                heroSlider.addEventListener('touchcancel', handleSwipeEnd, { passive: true });

                // 🖱️ Mouse Drag (Desktop)
                let isMouseDown = false;
                let mouseStartX = 0;
                let mouseDeltaX = 0;
                let mouseStartTime = 0;
                let draggedFar = false;

                wrapper.addEventListener('mousedown', (e) => {
                    if (e.target.closest('.slider-btn') || e.target.closest('.slider-dots')) return;
                    isMouseDown = true;
                    mouseStartX = e.clientX;
                    mouseDeltaX = 0;
                    mouseStartTime = Date.now();
                    draggedFar = false;
                    pauseSlider();
                    wrapper.classList.add('is-dragging');
                    wrapper.style.transition = 'none';
                });

                window.addEventListener('mousemove', (e) => {
                    if (!isMouseDown) return;
                    mouseDeltaX = e.clientX - mouseStartX;
                    if (Math.abs(mouseDeltaX) > 6) {
                        draggedFar = true;
                    }
                    let resistance = 1;
                    if ((currentIndex === 0 && mouseDeltaX > 0) || (currentIndex === slideCount - 1 && mouseDeltaX < 0)) {
                        resistance = 0.35;
                    }
                    const offset = mouseDeltaX * resistance;
                    wrapper.style.transform = `translate3d(calc(-${currentIndex * 100}% + ${offset}px), 0, 0)`;
                });

                window.addEventListener('mouseup', () => {
                    if (!isMouseDown) return;
                    isMouseDown = false;
                    wrapper.classList.remove('is-dragging');
                    wrapper.style.transition = 'transform 0.45s cubic-bezier(0.25, 1, 0.5, 1)';

                    const elapsed = Date.now() - mouseStartTime;
                    const velocity = Math.abs(mouseDeltaX) / elapsed;

                    if (Math.abs(mouseDeltaX) > 50 || velocity > 0.4) {
                        if (mouseDeltaX < 0) {
                            nextSlide();
                        } else {
                            prevSlide();
                        }
                    } else {
                        updateSlider(true);
                    }
                    resumeSlider();
                });

                // Mencegah klik tautan terpicu saat menggeser banner dengan mouse
                wrapper.addEventListener('click', (e) => {
                    if (draggedFar) {
                        e.preventDefault();
                        e.stopPropagation();
                        draggedFar = false;
                    }
                }, true);

                // Inisialisasi awal
                updateSlider(false);
                resetInterval();
            }
        }
    }

    /* ==========================================================================
       4. MOBILE SPECIFIC LOGIC
       ========================================================================== */
    
    // 📱 Mobile Menu Drawer
    const menuToggle = document.getElementById('menu-toggle');
    const bottomMenuToggle = document.getElementById('bottom-menu-toggle');
    const menuDrawer = document.getElementById('mobile-menu-drawer');
    const menuOverlay = document.getElementById('mobile-menu-overlay');
    const menuClose = document.getElementById('mobile-menu-close');
    
    function closeMenu() {
        menuDrawer?.classList.remove('active');
        menuOverlay?.classList.remove('active');
        menuToggle?.classList.remove('active');
        bottomMenuToggle?.classList.remove('active');
        menuToggle?.setAttribute('aria-expanded', 'false');
        bottomMenuToggle?.setAttribute('aria-expanded', 'false');
        body.classList.remove('menu-open');
    }

    function toggleMenu(e) {
        if (e) e.preventDefault();
        if (menuDrawer?.classList.contains('active')) {
            closeMenu();
        } else {
            menuDrawer?.classList.add('active');
            menuOverlay?.classList.add('active');
            menuToggle?.classList.add('active');
            bottomMenuToggle?.classList.add('active');
            menuToggle?.setAttribute('aria-expanded', 'true');
            bottomMenuToggle?.setAttribute('aria-expanded', 'true');
            body.classList.add('menu-open');
        }
    }

    if (menuDrawer) {
        menuToggle?.addEventListener('click', toggleMenu);
        bottomMenuToggle?.addEventListener('click', toggleMenu);
        menuClose?.addEventListener('click', closeMenu);
        menuOverlay?.addEventListener('click', closeMenu);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menuDrawer.classList.contains('active')) {
                closeMenu();
            }
        });

        // 📂 Mobile Submenu Accordion Toggle
        const subMenuToggles = menuDrawer.querySelectorAll('.menu-item-has-children > a');
        subMenuToggles.forEach((link) => {
            link.addEventListener('click', (e) => {
                const parent = link.parentElement;
                const subMenu = parent?.querySelector('.sub-menu');
                if (subMenu) {
                    e.preventDefault();
                    parent.classList.toggle('active');
                }
            });
        });

        // 👆 Swipe right to close menu drawer
        let touchStartX = 0;
        let touchStartY = 0;
        let touchDiffX = 0;
        let isHorizontalSwipe = false;

        menuDrawer.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                touchDiffX = 0;
                isHorizontalSwipe = false;
            }
        }, { passive: true });

        menuDrawer.addEventListener('touchmove', (e) => {
            if (e.touches.length === 1) {
                const currentX = e.touches[0].clientX;
                const currentY = e.touches[0].clientY;
                touchDiffX = currentX - touchStartX;
                const touchDiffY = Math.abs(currentY - touchStartY);

                // Detect horizontal swipe to right
                if (touchDiffX > 15 && touchDiffX > touchDiffY * 1.2) {
                    isHorizontalSwipe = true;
                }
            }
        }, { passive: true });

        menuDrawer.addEventListener('touchend', () => {
            if (isHorizontalSwipe && touchDiffX > 50) {
                closeMenu();
            }
            isHorizontalSwipe = false;
            touchDiffX = 0;
        }, { passive: true });

        // 🔗 Auto-close menu when clicking links that navigate or jump to page anchors
        const navLinks = menuDrawer.querySelectorAll('.mobile-nav-list a');
        navLinks.forEach((link) => {
            link.addEventListener('click', () => {
                const isParentToggle = link.parentElement?.classList.contains('menu-item-has-children') && (link.getAttribute('href') === '#' || link.getAttribute('href') === 'javascript:void(0)');
                if (isParentToggle) return;

                const href = link.getAttribute('href') || '';
                if (href.startsWith('#') || href.includes('#')) {
                    closeMenu();
                }
            });
        });
    }

    // 🏷️ Mobile Bottom Nav Kategori Smooth Scroll
    const bottomNavKategori = document.querySelector('.bottom-nav .nav-kategori');
    if (bottomNavKategori) {
        bottomNavKategori.addEventListener('click', (e) => {
            if (menuDrawer?.classList.contains('active')) {
                closeMenu();
            }
            const href = bottomNavKategori.getAttribute('href') || '';
            if (href.includes('#categories') && (window.location.pathname === '/' || window.location.pathname === '')) {
                const target = document.getElementById('categories');
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    }

    // 💬 Testimonials Slider
    const testiWrapper = document.querySelector('.testimonials-wrapper');
    const testiSlides = document.querySelectorAll('.testimonial-slide');
    const testiDotsContainer = document.querySelector('.testimonial-dots');
    
    if (testiWrapper && testiSlides.length > 0) {
        let currentTesti = 0;
        let startX = 0;
        let isDragging = false;
        
        testiSlides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('testi-dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                goToTesti(index);
                resetTestiTimer();
            });
            testiDotsContainer?.appendChild(dot);
        });
        
        const testiDots = document.querySelectorAll('.testi-dot');
        
        function goToTesti(index) {
            currentTesti = index;
            testiWrapper.style.transform = `translateX(-${index * 100}%)`;
            testiDots.forEach((d, i) => d.classList.toggle('active', i === index));
        }

        function nextTesti() {
            if (testiSlides.length <= 1) return;
            currentTesti = (currentTesti + 1) % testiSlides.length;
            goToTesti(currentTesti);
        }
        
        function prevTesti() {
            if (testiSlides.length <= 1) return;
            currentTesti = (currentTesti - 1 + testiSlides.length) % testiSlides.length;
            goToTesti(currentTesti);
        }

        const prevArrow = document.getElementById('testi-prev-btn');
        const nextArrow = document.getElementById('testi-next-btn');
        if (prevArrow) {
            prevArrow.addEventListener('click', () => {
                prevTesti();
                resetTestiTimer();
            });
        }
        if (nextArrow) {
            nextArrow.addEventListener('click', () => {
                nextTesti();
                resetTestiTimer();
            });
        }

        let testiTimer = null;
        function startTestiTimer() {
            if (testiSlides.length <= 1) return;
            clearInterval(testiTimer);
            testiTimer = setInterval(nextTesti, 4500);
        }

        function resetTestiTimer() {
            clearInterval(testiTimer);
            startTestiTimer();
        }

        function pauseTestiTimer() {
            clearInterval(testiTimer);
        }

        // Start autoplay for testimonials
        startTestiTimer();

        // Pause testimonials slider on hover
        const testiContainer = document.querySelector('.testimonials-slider-container') || testiWrapper;
        if (testiContainer) {
            testiContainer.addEventListener('mouseenter', pauseTestiTimer);
            testiContainer.addEventListener('mouseleave', startTestiTimer);
        }

        testiWrapper.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
            pauseTestiTimer();
        }, { passive: true });

        testiWrapper.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            const endX = e.changedTouches[0].clientX;
            const diff = startX - endX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) nextTesti();
                else prevTesti();
            }
            isDragging = false;
            resetTestiTimer();
        }, { passive: true });
    }

    // 📰 Article Slider
    const articleSlider = document.querySelector('.articles-slider-container');
    if (articleSlider) {
        const track = articleSlider.querySelector('.article-track');
        const slides = articleSlider.querySelectorAll('.article-slide');
        const prevBtn = articleSlider.querySelector('#article-prev');
        const nextBtn = articleSlider.querySelector('#article-next');
        const dotsContainer = articleSlider.querySelector('.article-slider-dots');
        
        if (track && slides.length > 0) {
            let currentIndex = 0;
            let slideInterval;
            
            // Calculate how many slides visible
            function getVisibleSlides() {
                if (window.innerWidth <= 768) return 2; // Mobile
                return 4; // Desktop
            }
            
            function updateSlider() {
                const slideWidth = 100 / getVisibleSlides();
                track.style.transform = `translateX(-${currentIndex * slideWidth}%)`;
                
                const dots = dotsContainer?.querySelectorAll('.article-dot');
                dots?.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
            }
            
            function getMaxIndex() {
                return Math.max(0, slides.length - getVisibleSlides());
            }

            // Create dots
            function initDots() {
                if (dotsContainer) {
                    dotsContainer.innerHTML = '';
                    const maxDots = getMaxIndex() + 1;
                    if (maxDots <= 1) return; // No dots if not enough items
                    
                    for (let i = 0; i < maxDots; i++) {
                        const dot = document.createElement('div');
                        dot.classList.add('article-dot');
                        if (i === currentIndex) dot.classList.add('active');
                        dot.addEventListener('click', () => {
                            currentIndex = i;
                            updateSlider();
                            resetInterval();
                        });
                        dotsContainer.appendChild(dot);
                    }
                }
            }
            initDots();
            
            function nextSlide() {
                const maxIndex = getMaxIndex();
                if (maxIndex <= 0) return;
                currentIndex = currentIndex >= maxIndex ? 0 : currentIndex + 1;
                updateSlider();
            }
            
            function prevSlide() {
                const maxIndex = getMaxIndex();
                if (maxIndex <= 0) return;
                currentIndex = currentIndex <= 0 ? maxIndex : currentIndex - 1;
                updateSlider();
            }
            
            function resetInterval() {
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000);
            }
            
            prevBtn?.addEventListener('click', () => { prevSlide(); resetInterval(); });
            nextBtn?.addEventListener('click', () => { nextSlide(); resetInterval(); });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                const maxIndex = getMaxIndex();
                if (currentIndex > maxIndex) currentIndex = maxIndex;
                initDots();
                updateSlider();
            });
            
            // Touch support
            let startX = 0;
            let isDragging = false;
            
            track.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
                clearInterval(slideInterval);
            }, { passive: true });
            
            track.addEventListener('touchend', (e) => {
                if (!isDragging) return;
                const diff = startX - e.changedTouches[0].clientX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) nextSlide();
                    else prevSlide();
                }
                isDragging = false;
                resetInterval();
            }, { passive: true });

            // Pause on hover for articles slider
            articleSlider.addEventListener('mouseenter', () => clearInterval(slideInterval));
            articleSlider.addEventListener('mouseleave', resetInterval);
            
            // Start autoplay for articles slider
            resetInterval();
        }
    }

    // 📱 Mobile Slider for Related Articles (single.php)
    const relatedSlider = document.getElementById('related-articles-slider');
    const relatedDotsContainer = document.getElementById('related-slider-dots');

    if (relatedSlider && relatedDotsContainer) {
        const items = relatedSlider.querySelectorAll('.related-article-item');
        if (items.length > 1) {
            // Build pagination dots
            items.forEach((item, index) => {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'related-slider-dot' + (index === 0 ? ' active' : '');
                dot.setAttribute('aria-label', `Artikel ke-${index + 1}`);
                dot.addEventListener('click', () => {
                    item.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                });
                relatedDotsContainer.appendChild(dot);
            });

            // Update active dot on scroll
            let scrollRaf;
            relatedSlider.addEventListener('scroll', () => {
                cancelAnimationFrame(scrollRaf);
                scrollRaf = requestAnimationFrame(() => {
                    const sliderCenter = relatedSlider.scrollLeft + (relatedSlider.offsetWidth / 2);
                    let closestIndex = 0;
                    let closestDistance = Infinity;

                    items.forEach((item, index) => {
                        const itemCenter = item.offsetLeft + (item.offsetWidth / 2);
                        const distance = Math.abs(sliderCenter - itemCenter);
                        if (distance < closestDistance) {
                            closestDistance = distance;
                            closestIndex = index;
                        }
                    });

                    const dots = relatedDotsContainer.querySelectorAll('.related-slider-dot');
                    dots.forEach((dot, idx) => {
                        dot.classList.toggle('active', idx === closestIndex);
                    });
                });
            }, { passive: true });

            // Mouse drag support for testing desktop/mobile view
            let isDown = false;
            let startX, scrollLeft;
            relatedSlider.addEventListener('mousedown', (e) => {
                if (window.innerWidth > 768) return;
                isDown = true;
                startX = e.pageX - relatedSlider.offsetLeft;
                scrollLeft = relatedSlider.scrollLeft;
            });
            window.addEventListener('mouseup', () => { isDown = false; });
            relatedSlider.addEventListener('mouseleave', () => { isDown = false; });
            relatedSlider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - relatedSlider.offsetLeft;
                const walk = (x - startX) * 1.5;
                relatedSlider.scrollLeft = scrollLeft - walk;
            });
        }
    }

    // 🏎️ Logo Marquee Pause on Hover
    const logoTrack = document.querySelector('.logo-track');
    if (logoTrack) {
        logoTrack.addEventListener('mouseenter', () => logoTrack.style.animationPlayState = 'paused');
        logoTrack.addEventListener('mouseleave', () => logoTrack.style.animationPlayState = 'running');
    }

    // 🙋 FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close other items
            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
            });
            
            // Toggle current item
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    /* ==========================================================================
       5. PWA & UTILITIES
       ========================================================================== */
    
    if ('serviceWorker' in navigator && typeof tokokuSearch !== 'undefined') {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register(tokokuSearch.themeUrl + '/sw.js')
                .then(reg => console.log('TokoKu SW registered'))
                .catch(err => console.log('SW registration failed:', err));
        });
    }

    function updateThemeColor() {
        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            meta.setAttribute('content', body.classList.contains('theme-dark') ? '#0f172a' : '#ffffff');
        }
    }
    updateThemeColor();

    // 🔗 Modern Copy Link with Toast
    let toastTimer = null;
    function showToast(message) {
        const toast = document.getElementById('tokoku-toast');
        if (!toast) return;
        const textEl = document.getElementById('tokoku-toast-text');
        if (textEl && message) textEl.textContent = message;
        
        toast.classList.add('active');
        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.remove('active');
        }, 2500);
    }

    document.addEventListener('click', (e) => {
        const copyBtn = e.target.closest('.copy-link-btn');
        if (!copyBtn) return;
        e.preventDefault();
        
        const urlToCopy = copyBtn.getAttribute('data-url') || window.location.href;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(urlToCopy).then(() => {
                showToast('Tautan berhasil disalin!');
            }).catch(() => {
                fallbackCopyText(urlToCopy);
            });
        } else {
            fallbackCopyText(urlToCopy);
        }
    });

    function fallbackCopyText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        textArea.style.top = '0';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showToast('Tautan berhasil disalin!');
        } catch (err) {
            showToast('Gagal menyalin tautan');
        }
        document.body.removeChild(textArea);
    }

    // 📱 Sticky Mobile Order Bar for Single Product
    const stickyBar = document.getElementById('product-sticky-bar');
    if (stickyBar) {
        const triggerElement = document.querySelector('.single-product .main-image') || document.querySelector('.single-product .product-info');

        if (triggerElement && 'IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    // Tampilkan saat foto/header produk sudah dilewati ke atas
                    const isVisible = (!entry.isIntersecting && entry.boundingClientRect.top < 0);
                    stickyBar.classList.toggle('visible', isVisible);
                    stickyBar.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
                    document.body.classList.toggle('has-sticky-order-bar', isVisible);
                });
            }, { threshold: 0.1 });
            observer.observe(triggerElement);
        } else {
            // Fallback scroll listener untuk browser lama tanpa IntersectionObserver
            let barTicking = false;
            window.addEventListener('scroll', () => {
                if (!barTicking) {
                    window.requestAnimationFrame(() => {
                        if (window.innerWidth <= 768) {
                            const isVisible = (window.scrollY > 350);
                            stickyBar.classList.toggle('visible', isVisible);
                            stickyBar.setAttribute('aria-hidden', isVisible ? 'false' : 'true');
                            document.body.classList.toggle('has-sticky-order-bar', isVisible);
                        }
                        barTicking = false;
                    });
                    barTicking = true;
                }
            }, { passive: true });
        }
    }

    // 🛒 Product Card Full-Click Navigation
    // Memungkinkan klik di area mana pun pada kartu produk untuk langsung membuka halaman detail produk
    document.addEventListener('click', (e) => {
        const card = e.target.closest('.product-card');
        if (!card) return;
        
        // Jangan navigasi jika user mengklik tombol WhatsApp order, link tag/kategori, atau tombol lain
        if (e.target.closest('.btn-whatsapp-order') || e.target.closest('a') || e.target.closest('button')) {
            return;
        }

        const link = card.querySelector('.product-card__title a') || card.querySelector('.product-card__image-link');
        if (link && link.href) {
            window.location.href = link.href;
        }
    });

});
