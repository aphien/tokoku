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
    const modeToggle = document.getElementById('mode-toggle');
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
    if (savedTheme) applyTheme(savedTheme);

    if (modeToggle) {
        modeToggle.addEventListener('click', () => {
            const isDark = body.classList.contains('theme-dark');
            applyTheme(isDark ? 'light' : 'dark');
            localStorage.setItem('tokoku-theme', isDark ? 'light' : 'dark');
        });
    }

    // 🕒 Sticky Header
    const header = document.querySelector('.site-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header?.classList.add('sticky');
        } else {
            header?.classList.remove('sticky');
        }
    });

    /* ==========================================================================
       2. DESKTOP SPECIFIC LOGIC
       ========================================================================== */
    
    // 🚀 Scroll to Top (Elegant Version)
    const scrollTopBtn = document.getElementById('scroll-to-top');
    if (scrollTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('active');
            } else {
                scrollTopBtn.classList.remove('active');
            }
        });

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
    
    // 🎡 Hero Slider
    const slider = document.querySelector('.hero-slider-section');
    if (slider) {
        const wrapper = slider.querySelector('.slider-wrapper');
        const slides = slider.querySelectorAll('.slide');
        const prevBtn = slider.querySelector('.slider-prev');
        const nextBtn = slider.querySelector('.slider-next');
        const dotsContainer = slider.querySelector('.slider-dots');
        
        if (wrapper && slides.length > 0) {
            let currentIndex = 0;
            let slideInterval;
            
            slides.forEach((_, i) => {
                const dot = document.createElement('div');
                dot.classList.add('dot');
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToSlide(i));
                dotsContainer?.appendChild(dot);
            });
            
            const dots = dotsContainer?.querySelectorAll('.dot');
            
            function updateSlider() {
                wrapper.style.transform = `translateX(-${currentIndex * 100}%)`;
                dots?.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
            }
            
            function nextSlide() {
                currentIndex = (currentIndex + 1) % slides.length;
                updateSlider();
            }
            
            function prevSlide() {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                updateSlider();
            }
            
            function goToSlide(index) {
                currentIndex = index;
                updateSlider();
                resetInterval();
            }
            
            function resetInterval() {
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000);
            }
            
            prevBtn?.addEventListener('click', () => { prevSlide(); resetInterval(); });
            nextBtn?.addEventListener('click', () => { nextSlide(); resetInterval(); });
            
            slideInterval = setInterval(nextSlide, 5000);
        }
    }

    /* ==========================================================================
       4. MOBILE SPECIFIC LOGIC
       ========================================================================== */
    
    // 📱 Mobile Menu Drawer
    const menuToggle = document.getElementById('menu-toggle');
    const menuDrawer = document.getElementById('mobile-menu-drawer');
    const menuOverlay = document.getElementById('mobile-menu-overlay');
    const menuClose = document.getElementById('mobile-menu-close');
    
    function closeMenu() {
        menuDrawer?.classList.remove('active');
        menuOverlay?.classList.remove('active');
        menuToggle?.classList.remove('active');
        body.classList.remove('menu-open');
    }

    if (menuToggle && menuDrawer) {
        menuToggle.addEventListener('click', () => {
            menuDrawer.classList.add('active');
            menuOverlay?.classList.add('active');
            menuToggle.classList.add('active');
            body.classList.add('menu-open');
        });
        
        menuClose?.addEventListener('click', closeMenu);
        menuOverlay?.addEventListener('click', closeMenu);
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
            currentTesti = (currentTesti + 1) % testiSlides.length;
            goToTesti(currentTesti);
        }

        let testiTimer = setInterval(nextTesti, 5000);
        
        function resetTestiTimer() {
            clearInterval(testiTimer);
            testiTimer = setInterval(nextTesti, 5000);
        }

        testiWrapper.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
            clearInterval(testiTimer);
        }, { passive: true });

        testiWrapper.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            const endX = e.changedTouches[0].clientX;
            const diff = startX - endX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) nextTesti();
                else {
                    currentTesti = (currentTesti - 1 + testiSlides.length) % testiSlides.length;
                    goToTesti(currentTesti);
                }
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
            
            slideInterval = setInterval(nextSlide, 5000);
        }
    }

    // 🏎️ Logo Marquee Pause on Hover
    const logoTrack = document.querySelector('.logo-track');
    if (logoTrack) {
        logoTrack.addEventListener('mouseenter', () => logoTrack.style.animationPlayState = 'paused');
        logoTrack.addEventListener('mouseleave', () => logoTrack.style.animationPlayState = 'running');
    }

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
});
