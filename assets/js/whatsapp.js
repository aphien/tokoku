/**
 * TokoKu WhatsApp Order JS
 */

document.addEventListener('DOMContentLoaded', function() {
    const waButtons = document.querySelectorAll('.btn-whatsapp-order');
    const modal = document.querySelector('#wa-modal');
    const closeModal = document.querySelector('.close-modal');
    const waForm = document.querySelector('#wa-order-form');
    
    if (!waButtons || !modal) return;

    let currentProduct = {};

    waButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            currentProduct = {
                name: btn.dataset.productName,
                price: btn.dataset.productPrice,
                id: btn.dataset.productId
            };
            
            modal.classList.add('active');
        });
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

    if (waForm) {
        waForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const name = document.querySelector('#buyer-name').value;
            const qty = document.querySelector('#order-qty').value;
            const note = document.querySelector('#order-note').value;
            
            let message = tokokuWA.message;
            
            // Convert HTML to plain text for WhatsApp with Markdown support
            const htmlToWA = (html) => {
                let text = html;
                
                // Convert Bold
                text = text.replace(/<(strong|b)>(.*?)<\/\1>/gi, '*$2*');
                
                // Convert Italic
                text = text.replace(/<(em|i)>(.*?)<\/\1>/gi, '_$2_');
                
                // Line breaks
                text = text.replace(/<br\s*\/?>/gi, '\n');
                text = text.replace(/<\/p>/gi, '\n');
                text = text.replace(/<\/div>/gi, '\n');
                
                // Strip all remaining tags
                text = text.replace(/<(?:.|\n)*?>/gm, '');
                
                // Decode HTML entities
                const doc = new DOMParser().parseFromString(text, 'text/html');
                return doc.documentElement.textContent.trim();
            };

            message = htmlToWA(message);
            
            message = message.replace('{produk}', currentProduct.name);
            message = message.replace('{harga}', currentProduct.price);
            message = message.replace('{jumlah}', qty);
            message = message.replace('{nama}', name);
            message = message.replace('{catatan}', note || '-');
            
            const encodedMessage = encodeURIComponent(message);
            const waUrl = `https://wa.me/${tokokuWA.number}?text=${encodedMessage}`;
            
            window.open(waUrl, '_blank');
            modal.classList.remove('active');
            waForm.reset();
        });
    }
});
