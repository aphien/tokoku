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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        
        <form id="wa-order-form" class="wa-form">
            <div class="form-group">
                <label for="buyer-name">Nama Lengkap</label>
                <input type="text" id="buyer-name" name="buyer_name" required placeholder="Contoh: Budi Santoso">
            </div>
            
            <div class="form-group">
                <label for="order-qty">Jumlah</label>
                <input type="number" id="order-qty" name="order_qty" value="1" min="1" required>
            </div>
            
            <div class="form-group">
                <label for="order-note">Catatan Tambahan (Opsional)</label>
                <!-- Quick Prompts / Pilihan Cepat -->
                <div class="wa-quick-prompts" aria-label="Pilihan Catatan Cepat">
                    <button type="button" class="wa-prompt-pill" data-prompt="Mau custom logo & tulisan sendiri.">🎨 Custom Desain</button>
                    <button type="button" class="wa-prompt-pill" data-prompt="Tanya harga grosir untuk jumlah banyak.">📦 Pesan Grosir</button>
                    <button type="button" class="wa-prompt-pill" data-prompt="Butuh pengerjaan cepat / deadline mepet.">⚡ Butuh Cepat</button>
                    <button type="button" class="wa-prompt-pill" data-prompt="Bisa minta preview desain dan katalog?">📋 Minta Katalog</button>
                </div>
                <textarea id="order-note" name="order_note" rows="3" placeholder="Contoh: Tulisan: Juara 1 Turnamen, Logo terlampir via WA..."></textarea>
            </div>
            
            <div class="form-footer">
                <button type="submit" class="btn btn-wa-submit btn-block">
                    Kirim ke WhatsApp
                </button>
            </div>
        </form>
    </div>
</div>

<style>
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
    animation: modalIn 0.3s ease-out;
}

@keyframes modalIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
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
    background: var(--green);
    color: #fff;
    padding: 14px;
    font-weight: 800;
    font-size: 1rem;
    border-radius: 50px;
    width: 100%;
    border: none;
    cursor: pointer;
    transition: var(--ease);
}
.btn-wa-submit:hover {
    filter: brightness(1.1);
    transform: translateY(-2px);
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
</style>
