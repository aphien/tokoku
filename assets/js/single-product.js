document.addEventListener('DOMContentLoaded', function() {
    const variationBtns = document.querySelectorAll('.btn-variation');
    const priceCurrent = document.querySelector('.product-price-display .price-current');
    const mainBtn = document.querySelector('.btn-contact-us');
    const stickyBtn = document.querySelector('.btn-sticky-order-elegant');
    const baseProductName = (mainBtn ? mainBtn.getAttribute('data-product-name') : '') || document.querySelector('.product-title')?.textContent.trim() || document.title;
    
    variationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all
            variationBtns.forEach(b => b.classList.remove('active'));
            // Add to clicked
            this.classList.add('active');

            // Update product name with selected variation for WhatsApp order
            const varName = this.getAttribute('data-variation') || this.textContent.trim();
            const fullTitle = (baseProductName && varName) ? `${baseProductName} (${varName})` : (varName || baseProductName);
            if (mainBtn) mainBtn.setAttribute('data-product-name', fullTitle);
            if (stickyBtn) stickyBtn.setAttribute('data-product-name', fullTitle);
            
            // Update price if available
            const newPrice = this.getAttribute('data-price');
            if (newPrice) {
                if (priceCurrent) priceCurrent.textContent = newPrice;
                const stickyPricePill = document.querySelector('.sticky-order-price-pill');
                if (stickyPricePill) stickyPricePill.textContent = newPrice;
                if (stickyBtn) stickyBtn.setAttribute('data-product-price', newPrice);
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

        // Update active thumbnail state without scrolling page window
        const thumbsContainer = document.getElementById('product-gallery-thumbs');
        thumbs.forEach((thumb, i) => {
            const isActive = (i === currentSlide);
            thumb.classList.toggle('is-active', isActive);
            thumb.setAttribute('aria-selected', isActive ? 'true' : 'false');
            if (isActive && thumbsContainer) {
                // Scroll ONLY inside the horizontal thumbnail container, NEVER scroll window/page
                const thumbLeft = thumb.offsetLeft;
                const thumbWidth = thumb.offsetWidth;
                const containerWidth = thumbsContainer.clientWidth;
                thumbsContainer.scrollTo({
                    left: thumbLeft - (containerWidth / 2) + (thumbWidth / 2),
                    behavior: 'smooth'
                });
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
                // Do not autoplay if gallery is scrolled out of viewport
                if (mainImgWrap) {
                    const rect = mainImgWrap.getBoundingClientRect();
                    const isVisible = (rect.bottom > 50 && rect.top < window.innerHeight);
                    if (!isVisible) return;
                }
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

    // 📱 Mobile Touch Pinch-to-Zoom & Pan System
    (function initMobileZoom() {
        if (window.innerWidth > 768) return;

        const openBtn  = document.getElementById('open-zoom-btn');
        const closeBtn = document.getElementById('close-zoom-btn');
        const overlay  = document.getElementById('mobile-zoom-overlay');
        const zoomImg  = document.getElementById('zoom-overlay-img');
        const srcImg   = document.getElementById('main-product-img');

        if (!overlay || !zoomImg) return;

        let scale = 1, minScale = 1, maxScale = 5;
        let tx = 0, ty = 0;
        let lastTapTime = 0;
        let rafPending = false;
        let needsApply = false;

        let pinching = false;
        let initDist = 0, initScale = 1;
        let initTx = 0, initTy = 0;
        let pinchMidX = 0, pinchMidY = 0;

        let panning = false;
        let panStartX = 0, panStartY = 0;
        let panInitTx = 0, panInitTy = 0;

        let swipeDY = 0;

        function dist(a, b) {
            const dx = a.clientX - b.clientX, dy = a.clientY - b.clientY;
            return Math.sqrt(dx * dx + dy * dy);
        }

        function clamp() {
            if (scale <= 1) { tx = 0; ty = 0; return; }
            const visW = zoomImg.offsetWidth, visH = zoomImg.offsetHeight;
            const maxTx = Math.max(0, (visW * scale - visW) / (2 * scale));
            const maxTy = Math.max(0, (visH * scale - visH) / (2 * scale));
            tx = Math.max(-maxTx, Math.min(maxTx, tx));
            ty = Math.max(-maxTy, Math.min(maxTy, ty));
        }

        function scheduleApply() {
            needsApply = true;
            if (rafPending) return;
            rafPending = true;
            requestAnimationFrame(() => {
                rafPending = false;
                if (!needsApply) return;
                needsApply = false;
                zoomImg.style.transform = `scale(${scale}) translate(${tx}px,${ty}px)`;
            });
        }

        function applyNow(animated) {
            zoomImg.style.transition = animated ? 'transform 0.28s cubic-bezier(0.25,0.46,0.45,0.94)' : 'none';
            zoomImg.style.transform  = `scale(${scale}) translate(${tx}px,${ty}px)`;
        }

        function reset(animated) {
            scale = 1; tx = 0; ty = 0;
            applyNow(animated);
        }

        function openZoom() {
            const activeSlide = document.querySelector('.product-slide.is-active img') || srcImg;
            if (!activeSlide) return;
            let best = activeSlide.src;
            if (activeSlide.srcset) {
                const parts = activeSlide.srcset.split(',').map(s => s.trim().split(/\s+/));
                const sorted = parts.sort((a, b) => (parseInt(b[1], 10) || 0) - (parseInt(a[1], 10) || 0));
                if (sorted[0] && sorted[0][0]) best = sorted[0][0];
            }
            zoomImg.src = best;
            reset(false);
            overlay.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }

        function closeZoom() {
            overlay.classList.remove('is-open');
            document.body.style.overflow = '';
            overlay.style.transform = '';
            overlay.style.opacity   = '';
        }

        if (openBtn) {
            openBtn.addEventListener('click', (e) => { e.preventDefault(); openZoom(); });
        }

        const trackWrap = document.getElementById('main-product-image-wrap') || srcImg;
        if (trackWrap) {
            trackWrap.addEventListener('click', (e) => {
                if (e.target.closest('.product-slider-nav') || e.target.closest('.mobile-zoom-btn')) return;
                if (trackWrap.getAttribute('data-swiped') === 'true') return;
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    openZoom();
                }
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => { e.stopPropagation(); closeZoom(); });
            closeBtn.addEventListener('touchend', (e) => { e.preventDefault(); e.stopPropagation(); closeZoom(); }, { passive: false });
        }

        overlay.addEventListener('click', (e) => { if (e.target === overlay) closeZoom(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeZoom(); });

        zoomImg.addEventListener('touchstart', (e) => {
            if (e.touches.length === 2) {
                e.preventDefault();
                pinching   = true; panning = false;
                initDist   = dist(e.touches[0], e.touches[1]);
                initScale  = scale;
                initTx     = tx; initTy = ty;
                pinchMidX  = (e.touches[0].clientX + e.touches[1].clientX) / 2;
                pinchMidY  = (e.touches[0].clientY + e.touches[1].clientY) / 2;
                swipeDY    = 0;
            } else if (e.touches.length === 1) {
                const now = Date.now();
                if (now - lastTapTime < 280) {
                    e.preventDefault();
                    if (scale > 1) { reset(true); } else { scale = 2.5; tx = 0; ty = 0; applyNow(true); }
                    lastTapTime = 0; return;
                }
                lastTapTime = now;
                panning = true; pinching = false;
                panStartX = e.touches[0].clientX;
                panStartY = e.touches[0].clientY;
                panInitTx = tx; panInitTy = ty;
                swipeDY = 0;
            }
        }, { passive: false });

        zoomImg.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (pinching && e.touches.length === 2) {
                const newDist = dist(e.touches[0], e.touches[1]);
                const newScale = Math.max(minScale, Math.min(maxScale, initScale * (newDist / initDist)));
                tx = initTx + (pinchMidX / window.innerWidth - 0.5) * (initScale - newScale) * zoomImg.offsetWidth / newScale;
                ty = initTy + (pinchMidY / window.innerHeight - 0.5) * (initScale - newScale) * zoomImg.offsetHeight / newScale;
                scale = newScale;
                clamp();
                scheduleApply();
            } else if (panning && e.touches.length === 1) {
                const dx = e.touches[0].clientX - panStartX;
                const dy = e.touches[0].clientY - panStartY;
                swipeDY = dy;
                if (scale <= 1) {
                    if (dy > 0) {
                        overlay.style.transition = 'none';
                        overlay.style.transform  = `translateY(${dy * 0.4}px)`;
                        overlay.style.opacity    = Math.max(0.4, 1 - dy / 300);
                    }
                } else {
                    tx = panInitTx + dx / scale;
                    ty = panInitTy + dy / scale;
                    clamp();
                    scheduleApply();
                }
            }
        }, { passive: false });

        zoomImg.addEventListener('touchend', () => {
            if (scale <= 1 && swipeDY > 90) {
                overlay.style.transition = 'transform 0.25s ease, opacity 0.25s ease';
                overlay.style.transform  = 'translateY(100%)';
                overlay.style.opacity    = '0';
                setTimeout(closeZoom, 260);
            } else {
                overlay.style.transition = 'transform 0.2s ease, opacity 0.2s ease';
                overlay.style.transform  = '';
                overlay.style.opacity    = '';
            }
            pinching = false; panning = false; swipeDY = 0;
            if (scale < minScale) { scale = minScale; tx = 0; ty = 0; applyNow(true); }
            clamp(); applyNow(false);
        }, { passive: true });
    })();

    // 📑 Interactive Product Details Tabs
    const productTabBtns = document.querySelectorAll('.product-tab-btn');
    const productTabPanels = document.querySelectorAll('.product-tab-panel');

    if (productTabBtns.length > 0 && productTabPanels.length > 0) {
        productTabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                if (!targetId) return;

                // Update active button state & accessibility attributes
                productTabBtns.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');

                // Switch active panel with smooth animation
                productTabPanels.forEach(panel => {
                    if (panel.id === targetId) {
                        panel.classList.add('active');
                    } else {
                        panel.classList.remove('active');
                    }
                });

                // Smoothly center the clicked tab in horizontal scroll on mobile/narrow viewports
                const parentNav = this.closest('.product-tabs-nav');
                if (parentNav && parentNav.scrollWidth > parentNav.clientWidth) {
                    this.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            });
        });
    }
});
