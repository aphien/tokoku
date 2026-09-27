/**
 * TokoKu WhatsApp Order JS
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

        currentProduct = {
            name: btn.dataset.productName || document.title,
            price: btn.dataset.productPrice || 'Hubungi Kami',
            sku: btn.dataset.productSku || '-',
            url: btn.dataset.productUrl || window.location.href,
            id: btn.dataset.productId || ''
        };
        
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
            
            const name = document.querySelector('#buyer-name').value;
            const qty = document.querySelector('#order-qty').value;
            const note = document.querySelector('#order-note').value;
            
            let message = (typeof tokokuWA !== 'undefined' && tokokuWA.message) ? tokokuWA.message : 'Halo, saya ingin memesan {produk}';
            
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
            
            message = message.replace('{produk}', currentProduct.name);
            message = message.replace('{sku}', currentProduct.sku);
            message = message.replace('{link}', currentProduct.url);
            message = message.replace('{harga}', currentProduct.price);
            message = message.replace('{jumlah}', qty);
            message = message.replace('{nama}', name);
            message = message.replace('{catatan}', note || '-');
            
            const waNumber = (typeof tokokuWA !== 'undefined' && tokokuWA.number) ? tokokuWA.number : '6281234567890';
            const encodedMessage = encodeURIComponent(message);
            const waUrl = `https://wa.me/${waNumber}?text=${encodedMessage}`;
            
            window.open(waUrl, '_blank');
            modal.classList.remove('active');
            waForm.reset();
            document.querySelectorAll('.wa-prompt-pill').forEach(p => p.classList.remove('selected'));
        });
    }
});
