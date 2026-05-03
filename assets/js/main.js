/**
 * TokoKu Main JavaScript
 * 
 * TABLE OF CONTENTS:
 * 1. SHARED LOGIC (Theme Toggle, Sticky Header)
 * 2. DESKTOP SPECIFIC LOGIC
 * 3. MOBILE SPECIFIC LOGIC (Drawer, Bottom Nav, Search Modal)
 * 4. PWA & UTILITIES
 */

document.addEventListener('DOMContentLoaded', function() {

    /* ==========================================================================
       1. SHARED LOGIC
       ========================================================================== */
    
    // 🌓 Theme Toggle (Light/Dark Mode)
    const modeToggle = document.getElementById('mode-toggle');
    const body = document.body;
    const html = document.documentElement;
    
    // Sync preference from localStorage
    const savedTheme = localStorage.getItem('tokoku-theme');
    const configDefault = document.body.getAttribute('data-config-default') || 'dark'; // Placeholder for default if needed
    
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

    if (savedTheme) {
        applyTheme(savedTheme);
    }

    if (modeToggle) {
        modeToggle.addEventListener('click', () => {
            if (body.classList.contains('theme-dark')) {
                body.classList.remove('theme-dark');
                body.classList.add('theme-light');
                html.classList.remove('theme-dark');
                html.classList.add('theme-light');
                localStorage.setItem('tokoku-theme', 'light');
            } else {
                body.classList.remove('theme-light');
                body.classList.add('theme-dark');
                html.classList.remove('theme-light');
                html.classList.add('theme-dark');
                localStorage.setItem('tokoku-theme', 'dark');
            }
            if (typeof updateThemeColor === 'function') updateThemeColor();
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
    
    // Desktop specific scripts can go here

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
            
            // Create Dots
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
                dots?.forEach(dot => dot.classList.remove('active'));
                if (dots) dots[currentIndex].classList.add('active');
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
            
            // Start Auto Slide
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

    // 📱 Mobile Sub-menu Toggle (Accordion)
    const menuItemsWithChildren = document.querySelectorAll('.mobile-nav-list .menu-item-has-children > a');
    menuItemsWithChildren.forEach(item => {
        item.addEventListener('click', (e) => {
            const parent = item.parentElement;
            const href = item.getAttribute('href');
            if (href === '#' || href === '') {
                e.preventDefault();
                parent.classList.toggle('active');
            } else {
                parent.classList.toggle('active');
            }
        });
        // 💬 Testimonials Slider
    const testiWrapper = document.querySelector('.testimonials-wrapper');
    const testiSlides = document.querySelectorAll('.testimonial-slide');
    const testiDotsContainer = document.querySelector('.testimonial-dots');
    
    if (testiWrapper && testiSlides.length > 0) {
        let currentTesti = 0;
        
        // Create dots
        testiSlides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('testi-dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                goToTesti(index);
                resetTestiTimer();
            });
            testiDotsContainer.appendChild(dot);
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
    }

    // 🏎️ Logo Marquee Pause on Hover
    const logoTrack = document.querySelector('.logo-track');
    if (logoTrack) {
        logoTrack.addEventListener('mouseenter', () => logoTrack.style.animationPlayState = 'paused');
        logoTrack.addEventListener('mouseleave', () => logoTrack.style.animationPlayState = 'running');
    }

    // 📰 Article Slider
    const articleTrack = document.querySelector('.article-track');
    const articleSlides = document.querySelectorAll('.article-slide');
    const articlePrev = document.getElementById('article-prev');
    const articleNext = document.getElementById('article-next');
    const articleDotsContainer = document.querySelector('.article-slider-dots');
    
    if (articleTrack && articleSlides.length > 0) {
        let currentPos = 0;
        let perView = window.innerWidth > 1024 ? 3 : (window.innerWidth > 768 ? 2 : 1);
        let maxPos = Math.max(0, articleSlides.length - perView);
        
        // Hide slider controls if not needed
        const checkControls = () => {
            if (articleSlides.length <= perView) {
                if (articlePrev) articlePrev.style.display = 'none';
                if (articleNext) articleNext.style.display = 'none';
                if (articleDotsContainer) articleDotsContainer.style.display = 'none';
                articleTrack.style.justifyContent = 'center';
            } else {
                if (articlePrev) articlePrev.style.display = 'flex';
                if (articleNext) articleNext.style.display = 'flex';
                if (articleDotsContainer) articleDotsContainer.style.display = 'flex';
                articleTrack.style.justifyContent = 'flex-start';
            }
        };
        
        // Create dots
        const updateDots = () => {
            if (!articleDotsContainer) return;
            articleDotsContainer.innerHTML = '';
            const numDots = Math.ceil(articleSlides.length / perView);
            for (let i = 0; i < numDots; i++) {
                const dot = document.createElement('div');
                dot.classList.add('article-dot');
                if (i === Math.floor(currentPos / perView)) dot.classList.add('active');
                dot.addEventListener('click', () => {
                    currentPos = i * perView;
                    if (currentPos > maxPos) currentPos = maxPos;
                    updateArticleSlider();
                });
                articleDotsContainer.appendChild(dot);
            }
        };
        
        function updateArticleSlider() {
            const slideWidth = articleSlides[0].offsetWidth;
            articleTrack.style.transform = `translateX(-${currentPos * slideWidth}px)`;
            
            // Update dots
            const dots = document.querySelectorAll('.article-dot');
            const activeDot = Math.floor(currentPos / perView);
            dots.forEach((d, i) => {
                d.classList.toggle('active', i === activeDot);
            });
            
            // Disable buttons if at ends
            if (articlePrev) articlePrev.style.opacity = currentPos === 0 ? '0.3' : '1';
            if (articleNext) articleNext.style.opacity = currentPos >= maxPos ? '0.3' : '1';
        }
        
        if (articlePrev) {
            articlePrev.addEventListener('click', () => {
                if (currentPos > 0) {
                    currentPos--;
                    updateArticleSlider();
                }
            });
        }
        
        if (articleNext) {
            articleNext.addEventListener('click', () => {
                if (currentPos < maxPos) {
                    currentPos++;
                    updateArticleSlider();
                }
            });
        }
        
        window.addEventListener('resize', () => {
            perView = window.innerWidth > 1024 ? 3 : (window.innerWidth > 768 ? 2 : 1);
            maxPos = Math.max(0, articleSlides.length - perView);
            if (currentPos > maxPos) currentPos = maxPos;
            checkControls();
            updateDots();
            updateArticleSlider();
        });
        
        checkControls();
        updateDots();
        updateArticleSlider();
    }

    // ❓ FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        
        question?.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                    const otherAnswer = otherItem.querySelector('.faq-answer');
                    if (otherAnswer) otherAnswer.style.maxHeight = null;
                }
            });
            
            // Toggle current item
            item.classList.toggle('active');
            if (!isActive) {
                answer.style.maxHeight = answer.scrollHeight + "px";
            } else {
                answer.style.maxHeight = null;
            }
        });
    });
});

    /* ==========================================================================
       4. PWA & UTILITIES
       ========================================================================== */
    
    // 🛡️ Register Service Worker for PWA
    if ('serviceWorker' in navigator && typeof tokokuSearch !== 'undefined') {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register(tokokuSearch.themeUrl + '/sw.js')
                .then(reg => console.log('TokoKu SW registered'))
                .catch(err => console.log('SW registration failed:', err));
        });
    }

    // 🎨 Dynamic Theme Color for PWA
    function updateThemeColor() {
        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            meta.setAttribute('content', body.classList.contains('theme-dark') ? '#0f172a' : '#ffffff');
        }
    }
    updateThemeColor();
});
