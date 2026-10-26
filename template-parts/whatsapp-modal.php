<?php

if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Template part for WhatsApp Order Modal
 */
?>

<div id="wa-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Konfirmasi Pesanan</h3>
            <button type="button" class="close-modal" aria-label="Tutup">
                <?php echo tokoku_icon( 'xmark', 18 ); ?>
            </button>
        </div>
        
        <!-- Preview Produk yang Dipesan -->
        <div class="wa-product-summary">
            <div class="wa-product-summary__image">
                <img src="" id="wa-product-img-preview" alt="Foto Produk" class="wa-product-img" style="display: none;">
                <div class="wa-product-placeholder" id="wa-product-placeholder">
                    <?php echo tokoku_icon( 'image', 22 ); ?>
                </div>
            </div>
            <div class="wa-product-summary__details">
                <strong class="wa-product-summary__name" id="wa-product-name-preview">—</strong>
                <span class="wa-product-summary__sku" id="wa-product-sku-preview">SKU: —</span>
            </div>
        </div>
        
        <form id="wa-order-form" class="wa-form">
            <div class="form-group">
                <label for="buyer-name"><?php esc_html_e( 'Nama Lengkap', 'tokoku' ); ?> <span class="required" style="color:#ef4444;">*</span></label>
                <input type="text" id="buyer-name" name="buyer_name" required placeholder="<?php esc_attr_e( 'Contoh: Budi Santoso', 'tokoku' ); ?>" autocomplete="name">
            </div>
            
            <div class="form-group">
                <label for="order-note"><?php esc_html_e( 'Catatan Tambahan (Opsional)', 'tokoku' ); ?></label>
                <!-- Quick Prompts / Pilihan Cepat -->
                <div class="wa-quick-prompts" aria-label="<?php esc_attr_e( 'Pilihan Catatan Cepat', 'tokoku' ); ?>">
                    <button type="button" class="wa-prompt-pill" data-prompt="Mau custom logo & tulisan sendiri.">🎨 Custom Desain</button>
                    <button type="button" class="wa-prompt-pill" data-prompt="Tanya harga grosir untuk jumlah banyak.">📦 Pesan Grosir</button>
                    <button type="button" class="wa-prompt-pill" data-prompt="Butuh pengerjaan cepat / deadline mepet.">⚡ Butuh Cepat</button>
                    <button type="button" class="wa-prompt-pill" data-prompt="Bisa minta preview desain dan katalog?">📋 Minta Katalog</button>
                </div>
                <textarea id="order-note" name="order_note" rows="3" placeholder="<?php esc_attr_e( 'Contoh: Tulisan: Juara 1 Turnamen, Logo terlampir via WA...', 'tokoku' ); ?>"></textarea>
            </div>
            
            <div class="form-footer">
                <button type="submit" class="btn btn-wa-submit btn-block">
                    <?php echo tokoku_icon( 'whatsapp', 18, '', 'style="margin-right:8px;"' ); ?>
                    <?php esc_html_e( 'Pesan Sekarang ke WhatsApp', 'tokoku' ); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Product Summary inside Modal */
.wa-product-summary {
    display: flex;
    align-items: center;
    gap: 14px;
    background: var(--bg2, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 20px;
    transition: all 0.2s ease;
}
.wa-product-summary__image {
    width: 54px;
    height: 54px;
    min-width: 54px;
    border-radius: 10px;
    background: var(--bg, #ffffff);
    border: 1px solid var(--border, #e2e8f0);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}
.wa-product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.wa-product-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text2, #94a3b8);
    background: var(--bg2, #f1f5f9);
}
.wa-product-placeholder svg {
    opacity: 0.6;
}
.wa-product-summary__details {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    gap: 2px;
}
.wa-product-summary__name {
    font-size: 0.96rem;
    font-weight: 800;
    color: var(--text, #1e293b);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}
.wa-product-summary__sku {
    font-size: 0.78rem;
    color: var(--text2, #64748b);
    font-family: monospace;
    font-weight: 700;
}
/* Modal Base */
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 2000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal.active {
    display: flex;
}

/* Modal Content */
.modal-content {
    background: var(--bg);
    width: 100%;
    max-width: 450px;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: 0 20px 50px rgba(0,0,0,0.35);
    padding: 30px;
    animation: modalIn 0.35s var(--ease-smooth, cubic-bezier(0.16, 1, 0.3, 1));
}

@keyframes modalIn {
    from {
        opacity: 0;
        transform: translateY(18px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.modal-title {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--text);
}
.close-modal {
    background: var(--bg2);
    border: 1px solid var(--border);
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--text2);
    transition: all 0.2s ease;
}
.close-modal:hover {
    background: #fee2e2;
    color: #ef4444;
    border-color: #fca5a5;
    transform: rotate(90deg);
}
:is(.theme-dark, html.theme-dark, body.theme-dark) .close-modal:hover {
    background: rgba(239, 68, 68, 0.2);
    color: #f87171;
    border-color: rgba(239, 68, 68, 0.4);
}

/* Form Styles */
.wa-form .form-group {
    margin-bottom: 20px;
}
.wa-form label {
    display: block;
    margin-bottom: 8px;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text);
}
.wa-form input,
.wa-form textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    background: var(--bg2);
    color: var(--text);
    font-family: inherit;
    font-size: 0.95rem;
    transition: var(--ease);
}
.wa-form input:focus,
.wa-form textarea:focus {
    border-color: var(--primary);
    background: var(--bg);
    outline: none;
}

.btn-wa-submit {
    background: var(--gradient-wa, linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%));
    color: #fff;
    padding: 14px 24px;
    font-weight: 800;
    font-size: 1rem;
    border-radius: 50px;
    width: 100%;
    border: none;
    cursor: pointer;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28), 0 6px 20px rgba(16, 185, 129, 0.35);
    transition: transform 0.25s var(--ease-smooth, cubic-bezier(0.16, 1, 0.3, 1)),
                box-shadow 0.25s var(--ease-smooth, cubic-bezier(0.16, 1, 0.3, 1)),
                background 0.25s ease;
}
.btn-wa-submit:hover {
    background: var(--gradient-wa-hover, linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%));
    transform: translateY(-2px);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4), 0 10px 28px rgba(16, 185, 129, 0.5);
}
.btn-wa-submit:active {
    transform: translateY(0) scale(0.98);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 3px 10px rgba(16, 185, 129, 0.3);
}

/* Quick Prompts Pills */
.wa-quick-prompts {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 10px;
}
.wa-prompt-pill {
    background: var(--bg2);
    border: 1px solid var(--border);
    color: var(--text);
    font-size: 0.76rem;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    outline: none;
    -webkit-tap-highlight-color: transparent;
    user-select: none;
}
.wa-prompt-pill:hover,
.wa-prompt-pill.selected {
    background: var(--primary);
    color: #ffffff !important;
    border-color: var(--primary);
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(var(--primary-rgb, 0, 123, 255), 0.3);
}
.wa-prompt-pill:active {
    transform: scale(0.94);
}
:is(.theme-dark, html.theme-dark, body.theme-dark) .wa-prompt-pill {
    background: rgba(30, 41, 59, 0.7);
    border-color: rgba(255, 255, 255, 0.12);
    color: #e2e8f0;
}
:is(.theme-dark, html.theme-dark, body.theme-dark) .wa-product-summary {
    background: rgba(30, 41, 59, 0.7);
    border-color: rgba(255, 255, 255, 0.12);
}
:is(.theme-dark, html.theme-dark, body.theme-dark) .wa-product-summary__image {
    background: rgba(15, 23, 42, 0.8);
    border-color: rgba(255, 255, 255, 0.12);
}
:is(.theme-dark, html.theme-dark, body.theme-dark) .wa-product-placeholder {
    background: rgba(15, 23, 42, 0.8);
    color: #64748b;
}
:is(.theme-dark, html.theme-dark, body.theme-dark) .wa-product-summary__name {
    color: #f1f5f9;
}
:is(.theme-dark, html.theme-dark, body.theme-dark) .wa-product-summary__sku {
    color: #94a3b8;
}

@media (max-width: 480px) {
    .modal-content {
        padding: 22px 18px;
        margin: 12px;
        max-height: 92vh;
        overflow-y: auto;
    }
    .wa-product-summary {
        padding: 8px 12px;
        margin-bottom: 16px;
        gap: 10px;
    }
    .wa-product-summary__image {
        width: 46px;
        height: 46px;
        min-width: 46px;
        border-radius: 8px;
    }
    .wa-product-summary__name {
        font-size: 0.88rem;
    }
}
</style>
