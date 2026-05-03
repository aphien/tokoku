<?php
/**
 * Template part for WhatsApp Order Modal
 */
?>

<div id="wa-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Konfirmasi Pesanan</h3>
            <button type="button" class="close-modal" aria-label="Tutup">&times;</button>
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
                <label for="order-note">Catatan (Opsional)</label>
                <textarea id="order-note" name="order_note" rows="3" placeholder="Contoh: Warna merah, ukuran L"></textarea>
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
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
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
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: var(--text2);
    line-height: 1;
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
</style>
