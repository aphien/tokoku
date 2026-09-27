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

    // 🖼️ Gallery Switching
    const mainImg = document.querySelector('.main-image img');
    const thumbnails = document.querySelectorAll('.gallery-thumbs .thumb img');

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            if (mainImg) {
                mainImg.src = this.src.replace('-150x150', ''); // Try to get larger version if it's a thumbnail
                // Update active state of thumbnails if needed
                document.querySelectorAll('.gallery-thumbs .thumb').forEach(t => t.style.borderColor = 'transparent');
                this.parentElement.style.borderColor = 'var(--primary)';
            }
        });
    });

    // 🔍 Product Image Zoom (Hover & Lightbox)
    const mainImgContainer = document.querySelector('.main-image');
    const lightbox = document.getElementById('tokoku-lightbox');
    const lightboxImg = document.getElementById('tokoku-lightbox-img');
    const lightboxClose = document.querySelector('.tokoku-lightbox-close');

    if (mainImg && mainImgContainer) {
        // Hover Zoom (Desktop)
        mainImgContainer.addEventListener('mousemove', function(e) {
            if (window.innerWidth > 768) {
                const rect = this.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                
                mainImg.style.transformOrigin = `${x}% ${y}%`;
                mainImg.style.transform = 'scale(2)';
            }
        });

        mainImgContainer.addEventListener('mouseleave', function() {
            mainImg.style.transform = 'scale(1)';
            mainImg.style.transformOrigin = 'center center';
        });

        // Click to Lightbox (Mobile & Desktop)
        mainImg.addEventListener('click', () => {
            lightboxImg.src = mainImg.src;
            lightbox.style.display = 'flex';
            document.body.style.overflow = 'hidden';
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
