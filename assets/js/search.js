/**
 * TokoKu AJAX Search JS
 */

document.addEventListener('DOMContentLoaded', function() {
    // --- Elements ---
    const desktopInput = document.querySelector('.search-input');
    const desktopResults = document.querySelector('.search-results');
    const desktopClear = document.querySelector('.search-clear');
    
    const mobileTrigger = document.getElementById('mobile-search-nav-trigger');
    const modalOverlay = document.getElementById('search-modal-overlay');
    const modalBack = document.getElementById('search-modal-back');
    const modalInput = document.getElementById('search-modal-input');
    const modalClear = document.getElementById('search-modal-clear');
    const modalResults = document.getElementById('search-modal-results');
    const popularSection = document.querySelector('.search-popular-section'); // Fixed selector
    const resultsTitle = document.getElementById('search-results-title');
    const popularTags = document.querySelectorAll('.popular-tag');

    let debounceTimer;
    let initialMobileLoaded = false;

    // --- Helper: Update Title ---
    function updateMobileSearchUI(keyword) {
        if (!resultsTitle) return;
        if (keyword.length > 0) {
            if (popularSection) popularSection.style.display = 'none';
            resultsTitle.innerText = 'HASIL PENCARIAN';
        } else {
            if (popularSection) popularSection.style.display = 'block';
            resultsTitle.innerText = 'SEMUA PRODUK';
        }
    }

    // --- Desktop Logic ---
    if (desktopInput && desktopResults) {
        desktopInput.addEventListener('input', function() {
            const keyword = this.value.trim();
            if (desktopClear) desktopClear.style.display = keyword.length > 0 ? 'block' : 'none';
            
            clearTimeout(debounceTimer);
            if (keyword.length < 2) {
                desktopResults.style.display = 'none';
                return;
            }
            debounceTimer = setTimeout(() => {
                performSearch(keyword, desktopResults, 'desktop');
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.search-clear')) {
                desktopInput.value = '';
                desktopResults.style.display = 'none';
                e.target.closest('.search-clear').style.display = 'none';
            }
            if (!desktopInput.contains(e.target) && !desktopResults.contains(e.target)) {
                desktopResults.style.display = 'none';
            }
        });
    }

    // --- Mobile Logic ---
    if (mobileTrigger && modalOverlay) {
        mobileTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            modalOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(() => modalInput && modalInput.focus(), 200);

            if (!initialMobileLoaded) {
                performSearch('', modalResults, 'mobile');
                initialMobileLoaded = true;
            }
        });

        if (modalBack) {
            modalBack.addEventListener('click', () => {
                modalOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        if (modalInput) {
            modalInput.addEventListener('input', function() {
                const keyword = this.value.trim();
                if (modalClear) modalClear.style.display = keyword.length > 0 ? 'block' : 'none';
                
                updateMobileSearchUI(keyword);

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    performSearch(keyword, modalResults, 'mobile');
                }, 300);
            });
        }

        if (modalClear) {
            modalClear.addEventListener('click', () => {
                modalInput.value = '';
                modalClear.style.display = 'none';
                updateMobileSearchUI('');
                performSearch('', modalResults, 'mobile');
                modalInput.focus();
            });
        }

        popularTags.forEach(tag => {
            tag.addEventListener('click', () => {
                const keyword = tag.innerText.trim();
                if (modalInput) {
                    modalInput.value = keyword;
                    if (modalClear) modalClear.style.display = 'block';
                    updateMobileSearchUI(keyword);
                    performSearch(keyword, modalResults, 'mobile');
                }
            });
        });
    }

    // --- Core Search AJAX ---
    async function performSearch(keyword, container, mode) {
        if (!container) return;

        const loadingHtml = `<div style="padding:40px 20px;text-align:center;color:var(--text2);">
            <div style="width:24px;height:24px;border:2px solid var(--border);border-top-color:var(--primary);border-radius:50%;margin:0 auto 10px;animation:spin 1s linear infinite;display:inline-block;"></div>
            <div style="font-size:0.9rem;opacity:0.8;">Mencari produk...</div>
        </div>`;
        
        container.innerHTML = loadingHtml;
        if (mode === 'desktop') container.style.display = 'block';

        try {
            const url = `${tokokuSearch.ajaxUrl}?action=tokoku_search&keyword=${encodeURIComponent(keyword)}&nonce=${tokokuSearch.nonce}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                renderResults(data.data, container, mode);
            } else {
                container.innerHTML = '<div style="padding:40px 20px;text-align:center;color:var(--red);">Terjadi kesalahan koneksi</div>';
            }
        } catch (error) {
            console.error('Search error:', error);
            container.innerHTML = '<div style="padding:40px 20px;text-align:center;color:var(--red);">Gagal memuat hasil</div>';
        }
    }

    function renderResults(data, container, mode) {
        const { products, categories, tags, total } = data;
        let html = '';

        if (mode === 'desktop') {
            if (categories && categories.length > 0) {
                html += '<div class="search-section-header">KATEGORI</div>';
                categories.forEach(cat => { html += `<a href="${cat.link}" class="search-cat-item"><strong>${cat.name}</strong></a>`; });
            }
            if (tags && tags.length > 0) {
                html += '<div class="search-section-header">TAG</div>';
                tags.forEach(tag => { html += `<a href="${tag.link}" class="search-tag-item">${tag.name}</a>`; });
            }
        }

        if (products && products.length > 0) {
            if (mode === 'desktop') html += '<div class="search-section-header">PRODUK</div>';
            
            products.forEach(product => {
                let priceDisplay = product.price_html || '';
                html += `
                    <a href="${product.permalink}" class="search-product-item">
                        <div class="item-thumb">
                            <img src="${product.thumbnail || ''}" alt="${product.title}">
                        </div>
                        <div class="item-info">
                            <div class="item-title">${product.title}</div>
                            <div class="item-price">${priceDisplay}</div>
                        </div>
                        ${mode === 'mobile' ? '<div class="search-product-arrow"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></div>' : ''}
                    </a>
                `;
            });
        }

        if (total > 0) {
            html += `<div class="search-results-footer"><a href="${tokokuSearch.homeUrl}produk/">LIHAT SEMUA PRODUK... (${total})</a></div>`;
        }

        if (html === '') {
            html = '<div style="padding:40px 20px;text-align:center;color:var(--text2);font-size:0.9rem;">Produk tidak ditemukan</div>';
        }

        container.innerHTML = html;
    }
});
