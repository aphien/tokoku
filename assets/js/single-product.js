document.addEventListener('DOMContentLoaded', function() {
    const variationBtns = document.querySelectorAll('.btn-variation');
    const priceCurrent = document.querySelector('.product-price-display .price-current');
    
    variationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all
            variationBtns.forEach(b => b.classList.remove('active'));
            // Add to clicked
            this.classList.add('active');
            
            // Update price if available
            const newPrice = this.getAttribute('data-price');
            if (newPrice) {
                if (priceCurrent) priceCurrent.textContent = newPrice;
                const stickyPricePill = document.querySelector('.sticky-order-price-pill');
                if (stickyPricePill) stickyPricePill.textContent = newPrice;
                const stickyBtn = document.querySelector('.btn-sticky-order-elegant');
                if (stickyBtn) stickyBtn.setAttribute('data-product-price', newPrice);
                const mainBtn = document.querySelector('.btn-contact-us');
                if (mainBtn) mainBtn.setAttribute('data-product-price', newPrice);
            }
        });
    });

    // 🖼️ Interactive Gallery Slider System
    const sliderTrack = document.getElementById('product-slider-track');
    const slides = document.querySelectorAll('.product-slide');
    const thumbs = document.querySelectorAll('.gallery-thumbs .thumb');
    const prevBtn = document.getElementById('product-slider-prev');
    const nextBtn = document.getElementById('product-slider-next');
    const counterCurrent = document.querySelector('.slider-counter-current');
    const lightbox = document.getElementById('tokoku-lightbox');
    const lightboxImg = document.getElementById('tokoku-lightbox-img');
    const lightboxClose = document.querySelector('.tokoku-lightbox-close');
    const mainImgWrap = document.getElementById('main-product-image-wrap');

    let currentSlide = 0;
    const totalSlides = slides.length;

    function goToSlide(index) {
        if (!sliderTrack || totalSlides <= 0) return;
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;

        currentSlide = index;
        sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;

        // Update active slide state and reset zoom transform on all slides
        slides.forEach((slide, i) => {
            slide.classList.toggle('is-active', i === currentSlide);
            const img = slide.querySelector('img');
            if (img) {
                img.style.transform = 'scale(1)';
                img.style.transformOrigin = 'center center';
            }
        });

        // Update active thumbnail state and auto-scroll thumbnail into view
        thumbs.forEach((thumb, i) => {
            const isActive = (i === currentSlide);
            thumb.classList.toggle('is-active', isActive);
            thumb.setAttribute('aria-selected', isActive ? 'true' : 'false');
            if (isActive) {
                thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
            }
        });

        // Update counter (1 / N)
        if (counterCurrent) {
            counterCurrent.textContent = currentSlide + 1;
        }
    }

    // ⏱️ Autoplay System for Product Gallery
    const isAutoplayEnabled = mainImgWrap?.getAttribute('data-autoplay') !== 'false';
    const autoplayDelay = parseInt(mainImgWrap?.getAttribute('data-autoplay-delay'), 10) || 4000;
    let autoplayTimer = null;
    let isPaused = false;

    function isModalActive() {
        const lbOpen = lightbox && lightbox.style.display !== 'none' && lightbox.style.display !== '';
        const mobileZoom = document.getElementById('mobile-zoom-overlay');
        const mzOpen = mobileZoom && mobileZoom.classList.contains('is-open');
        return Boolean(lbOpen || mzOpen);
    }

    function startAutoplay() {
        if (!isAutoplayEnabled || totalSlides <= 1) return;
        stopAutoplay();
        autoplayTimer = setInterval(() => {
            if (!isPaused && !isModalActive() && !document.hidden) {
                goToSlide(currentSlide + 1);
            }
        }, autoplayDelay);
    }

    function stopAutoplay() {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    function resetAutoplay() {
        if (!isAutoplayEnabled || totalSlides <= 1) return;
        stopAutoplay();
        startAutoplay();
    }

    if (totalSlides > 1) {
        prevBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            goToSlide(currentSlide - 1);
            resetAutoplay();
        });

        nextBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            goToSlide(currentSlide + 1);
            resetAutoplay();
        });

        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function(e) {
                e.preventDefault();
                const idx = parseInt(this.getAttribute('data-slide-index'), 10);
                if (!isNaN(idx)) {
                    goToSlide(idx);
                    resetAutoplay();
                }
            });
        });

        // Keyboard arrow navigation
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            if (e.key === 'ArrowLeft') {
                goToSlide(currentSlide - 1);
                resetAutoplay();
            } else if (e.key === 'ArrowRight') {
                goToSlide(currentSlide + 1);
                resetAutoplay();
            }
        });

        // Pause on mouse hover (Desktop)
        mainImgWrap?.addEventListener('mouseenter', () => { isPaused = true; });
        mainImgWrap?.addEventListener('mouseleave', () => { isPaused = false; });
        const thumbsContainer = document.getElementById('product-gallery-thumbs');
        thumbsContainer?.addEventListener('mouseenter', () => { isPaused = true; });
        thumbsContainer?.addEventListener('mouseleave', () => { isPaused = false; });

        // Touch Swipe Gestures (Mobile & Tablet)
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let isTouching = false;

        sliderTrack.addEventListener('touchstart', (e) => {
            if (e.touches.length !== 1) return;
            isPaused = true;
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            touchEndX = touchStartX;
            isTouching = true;
        }, { passive: true });

        sliderTrack.addEventListener('touchmove', (e) => {
            if (!isTouching || e.touches.length !== 1) return;
            touchEndX = e.touches[0].clientX;
            if (Math.abs(touchEndX - touchStartX) > 10 && mainImgWrap) {
                mainImgWrap.setAttribute('data-swiped', 'true');
            }
        }, { passive: true });

        sliderTrack.addEventListener('touchend', (e) => {
            if (!isTouching) return;
            isTouching = false;
            const diffX = touchEndX - touchStartX;
            const diffY = (e.changedTouches && e.changedTouches[0] ? e.changedTouches[0].clientY : touchStartY) - touchStartY;

            // Only swipe if horizontal movement is greater than vertical movement & threshold >= 38px
            if (Math.abs(diffX) > 38 && Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX < 0) {
                    goToSlide(currentSlide + 1); // Swipe left -> Next
                } else {
                    goToSlide(currentSlide - 1); // Swipe right -> Prev
                }
            }

            setTimeout(() => {
                if (mainImgWrap) mainImgWrap.removeAttribute('data-swiped');
            }, 300);

            // Resume autoplay after touch swipe
            setTimeout(() => {
                isPaused = false;
                resetAutoplay();
            }, 2500);
        }, { passive: true });

        // Pause autoplay when browser tab is inactive
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoplay();
            } else {
                startAutoplay();
            }
        });

        // Initialize autoplay
        startAutoplay();
    }

    // 🔍 Product Image Zoom (Hover Desktop & Lightbox)
    if (mainImgWrap) {
        // Desktop Hover Zoom
        mainImgWrap.addEventListener('mousemove', function(e) {
            if (window.innerWidth > 768) {
                const activeImg = this.querySelector('.product-slide.is-active img') || this.querySelector('img');
                if (!activeImg) return;
                const rect = this.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;

                activeImg.style.transformOrigin = `${x}% ${y}%`;
                activeImg.style.transform = 'scale(1.85)';
            }
        });

        mainImgWrap.addEventListener('mouseleave', function() {
            const activeImg = this.querySelector('.product-slide.is-active img') || this.querySelector('img');
            if (activeImg) {
                activeImg.style.transform = 'scale(1)';
                activeImg.style.transformOrigin = 'center center';
            }
        });

        // Click to Lightbox (Desktop only, mobile has dedicated full-screen pinch zoom)
        mainImgWrap.addEventListener('click', (e) => {
            if (e.target.closest('.product-slider-nav') || e.target.closest('.mobile-zoom-btn')) return;
            if (window.innerWidth > 768 && lightbox && lightboxImg) {
                const activeImg = mainImgWrap.querySelector('.product-slide.is-active img') || mainImgWrap.querySelector('img');
                if (activeImg) {
                    lightboxImg.src = activeImg.src;
                    lightbox.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            }
        });
    }

    if (lightbox) {
        function closeLightbox() {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        }

        lightboxClose?.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target === document.querySelector('.tokoku-lightbox-content')) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
        });
    }
});
