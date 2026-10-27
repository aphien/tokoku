/**
 * TokoKu WhatsApp Order JS
 * Handles product selection, populating product details (Name, SKU, Link),
 * quick prompt pills, and sending the formatted order to WhatsApp.
 */

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.querySelector('#wa-modal');
    const closeModal = document.querySelector('.close-modal');
    const waForm = document.querySelector('#wa-order-form');
    
    if (!modal) return;

    let currentProduct = {};

    // Delegated click for all WhatsApp order buttons (supports dynamic & sticky buttons)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-whatsapp-order');
        if (!btn) return;
        e.preventDefault();

        // Grab product image from data attribute or DOM fallback
        let productImgUrl = btn.dataset.productImage || '';
        if (!productImgUrl) {
            const cardImg = btn.closest('.product-card') ? (btn.closest('.product-card').querySelector('img') ? btn.closest('.product-card').querySelector('img').src : '') : '';
            const singleImgEl = document.querySelector('.product-slide.active img, #main-product-image-wrap img, .product-gallery img');
            const singleImg = singleImgEl ? singleImgEl.src : '';
            productImgUrl = cardImg || singleImg || '';
        }

        currentProduct = {
            name: btn.dataset.productName || document.title,
            price: btn.dataset.productPrice || 'Hubungi Kami',
            sku: btn.dataset.productSku || '-',
            url: btn.dataset.productUrl || window.location.href,
            image: productImgUrl,
            id: btn.dataset.productId || ''
        };
        
        // Update product summary preview in modal
        const namePreview = document.querySelector('#wa-product-name-preview');
        const skuPreview  = document.querySelector('#wa-product-sku-preview');
        const imgPreview  = document.querySelector('#wa-product-img-preview');
        const placeholder = document.querySelector('#wa-product-placeholder');

        if (namePreview) {
            namePreview.textContent = currentProduct.name;
        }
        if (skuPreview) {
            const skuVal = (currentProduct.sku && currentProduct.sku !== '-') ? currentProduct.sku : '—';
            skuPreview.textContent = 'SKU: ' + skuVal;
        }
        if (imgPreview) {
            if (currentProduct.image) {
                imgPreview.src = currentProduct.image;
                imgPreview.alt = currentProduct.name;
                imgPreview.style.display = 'block';
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            } else {
                imgPreview.style.display = 'none';
                if (placeholder) {
                    placeholder.style.display = 'flex';
                }
            }
        }

        modal.classList.add('active');
        
        // Focus first field
        setTimeout(() => {
            const nameInput = document.querySelector('#buyer-name');
            if (nameInput) nameInput.focus();
        }, 150);
    });

    // Quick Prompt Pills
    document.addEventListener('click', (e) => {
        const pill = e.target.closest('.wa-prompt-pill');
        if (!pill) return;
        e.preventDefault();

        const noteInput = document.querySelector('#order-note');
        if (!noteInput) return;

        const promptText = pill.dataset.prompt || pill.textContent.trim();
        
        if (pill.classList.contains('selected')) {
            pill.classList.remove('selected');
            // Remove prompt text
            let currentVal = noteInput.value;
            currentVal = currentVal.replace(promptText, '').replace(/^\s*,\s*|\s*,\s*$/g, '').replace(/,\s*,/g, ',').trim();
            noteInput.value = currentVal;
        } else {
            pill.classList.add('selected');
            if (noteInput.value.trim().length > 0) {
                noteInput.value = noteInput.value.trim() + ', ' + promptText;
            } else {
                noteInput.value = promptText;
            }
        }
    });

    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }

    // Close modal on outside click
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });

    // Close on Escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            modal.classList.remove('active');
        }
    });

    if (waForm) {
        waForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const nameInput = document.querySelector('#buyer-name');
            const noteInput = document.querySelector('#order-note');

            const name = nameInput ? nameInput.value.trim() : '';
            const note = noteInput ? noteInput.value.trim() : '';
            
            let message = (typeof tokokuWA !== 'undefined' && tokokuWA.message) 
                ? tokokuWA.message 
                : "Halo Admin,\n\nSaya ingin memesan produk berikut:\n\n👤 *Nama Pemesan :* {nama}\n📦 *Produk :* {produk}\n🏷️ *SKU :* {sku}\n📝 *Catatan :* {catatan}\n🔗 *Link Produk :* {link}\n\nMohon segera diproses. Terima kasih!";
            
            // Convert HTML to plain text for WhatsApp with Markdown support
            const htmlToWA = (html) => {
                let text = html;
                text = text.replace(/<(strong|b)>(.*?)<\/\1>/gi, '*$2*');
                text = text.replace(/<(em|i)>(.*?)<\/\1>/gi, '_$2_');
                text = text.replace(/<br\s*\/?>/gi, '\n');
                text = text.replace(/<\/p>/gi, '\n');
                text = text.replace(/<\/div>/gi, '\n');
                text = text.replace(/<(?:.|\n)*?>/gm, '');
                const doc = new DOMParser().parseFromString(text, 'text/html');
                return doc.documentElement.textContent.trim();
            };

            message = htmlToWA(message);

            // Clean up any {jumlah} placeholder and its line since form only has Nama & Catatan
            message = message.replace(/^[^\n]*\{jumlah\}[^\n]*\n?/gim, '');
            // Clean up any multiple consecutive line breaks
            message = message.replace(/\n{3,}/g, '\n\n');

            const prodName = currentProduct.name || '';
            const prodSku  = (currentProduct.sku && currentProduct.sku !== '-') ? currentProduct.sku : '-';
            const prodUrl  = currentProduct.url || window.location.href;
            const prodNote = note || '-';

            // Replace standard placeholders
            message = message.replace(/{produk}/g, prodName);
            message = message.replace(/{sku}/g, prodSku);
            message = message.replace(/{link}/g, prodUrl);
            message = message.replace(/{harga}/g, currentProduct.price || '');
            message = message.replace(/{nama}/g, name);
            message = message.replace(/{catatan}/g, prodNote);

            // Ensure SKU & Link are included if not present in custom message
            if (message.indexOf(prodSku) === -1 && prodSku !== '-') {
                message += `\n🏷️ *SKU:* ${prodSku}`;
            }
            if (message.indexOf(prodUrl) === -1) {
                message += `\n🔗 *Link Produk:* ${prodUrl}`;
            }
            
            const rawWa = (typeof tokokuWA !== 'undefined' && tokokuWA.number) ? String(tokokuWA.number) : '';
            const waNumber = rawWa.replace(/\D+/g, '').replace(/^0/, '62') || '6281234567890';
            const encodedMessage = encodeURIComponent(message.trim());
            const waUrl = `https://wa.me/${waNumber}?text=${encodedMessage}`;
            
            window.open(waUrl, '_blank');
            modal.classList.remove('active');
            waForm.reset();
            document.querySelectorAll('.wa-prompt-pill').forEach(p => p.classList.remove('selected'));
        });
    }
});
