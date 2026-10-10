<?php
/**
 * Template part for Universal Social Share Modal (Popup Berbagi)
 *
 * Menampilkan preview gambar, judul/teks, kategori, tautan URL,
 * tombol berbagi instan (WhatsApp, FB, X, TG, Pinterest, LinkedIn, Email),
 * serta QR Code dinamis untuk scan langsung dari kamera HP.
 *
 * @package TokoKu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div id="tokoku-share-modal" class="tokoku-share-modal" role="dialog" aria-modal="true" aria-labelledby="share-modal-title" style="display: none;">
    <div class="share-modal-backdrop" id="share-modal-backdrop"></div>
    
    <div class="share-modal-dialog">
        <!-- Header Modal -->
        <div class="share-modal-header">
            <div class="share-modal-title-wrap">
                <span class="share-modal-header-icon">
                    <?php echo tokoku_icon( 'share-nodes', 20 ); ?>
                </span>
                <h3 id="share-modal-title" class="share-modal-title">Bagikan Halaman</h3>
            </div>
            <button type="button" class="share-modal-close" id="share-modal-close" aria-label="Tutup Modal Berbagi" title="Tutup">
                <?php echo tokoku_icon( 'xmark', 18 ); ?>
            </button>
        </div>

        <div class="share-modal-body">
            <!-- 📸 Live Preview Card (Gambar + Teks Judul + Badge) -->
            <div class="share-preview-card" id="share-preview-card">
                <div class="share-preview-img-wrap" id="share-preview-img-wrap">
                    <img src="" alt="" id="share-preview-img" class="share-preview-img" loading="lazy">
                </div>
                <div class="share-preview-info">
                    <span class="share-preview-badge" id="share-preview-badge" style="display:none;"></span>
                    <h4 class="share-preview-title" id="share-preview-title">Judul</h4>
                    <p class="share-preview-desc" id="share-preview-desc" style="display:none;"></p>
                    <div class="share-preview-image-actions" id="share-preview-image-actions">
                        <button type="button" class="btn-share-img-direct" id="btn-share-img-direct" title="Bagikan file gambar langsung ke aplikasi">
                            <?php echo tokoku_icon( 'share-nodes', 14 ); ?>
                            <span>Kirim Foto</span>
                        </button>
                        <a href="#" download id="btn-download-img-direct" class="btn-download-img-direct" title="Simpan gambar ke perangkat">
                            <?php echo tokoku_icon( 'download', 14 ); ?>
                            <span>Unduh Foto</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 🔗 Kotak Tautan & Salin Cepat -->
            <div class="share-link-group">
                <label for="share-modal-url-input" class="share-link-label">Tautan Link Halaman:</label>
                <div class="share-link-input-wrap">
                    <input type="text" id="share-modal-url-input" class="share-modal-url-input" readonly value="">
                    <button type="button" class="btn-copy-modal" id="share-modal-copy-btn" aria-label="Salin Tautan">
                        <span class="copy-modal-default">
                            <?php echo tokoku_icon( 'link', 16 ); ?>
                            <span>Salin</span>
                        </span>
                        <span class="copy-modal-success" style="display: none;">
                            <?php echo tokoku_icon( 'check', 16 ); ?>
                            <span>Tersalin!</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- 📱 Tombol-Tombol Berbagi ke Media Sosial -->
            <div class="share-channels-wrapper">
                <span class="share-channels-label">Bagikan Langsung ke:</span>
                <div class="share-channels-grid">
                    <!-- WhatsApp -->
                    <a href="#" id="share-modal-wa" target="_blank" rel="noopener noreferrer" class="channel-card channel-wa" title="WhatsApp">
                        <div class="channel-icon-circle wa">
                            <?php echo tokoku_icon( 'whatsapp', 22 ); ?>
                        </div>
                        <span class="channel-name">WhatsApp</span>
                    </a>

                    <!-- Facebook -->
                    <a href="#" id="share-modal-fb" target="_blank" rel="noopener noreferrer" class="channel-card channel-fb" title="Facebook">
                        <div class="channel-icon-circle fb">
                            <?php echo tokoku_icon( 'facebook', 22 ); ?>
                        </div>
                        <span class="channel-name">Facebook</span>
                    </a>

                    <!-- 𝕏 Twitter -->
                    <a href="#" id="share-modal-tw" target="_blank" rel="noopener noreferrer" class="channel-card channel-tw" title="X (Twitter)">
                        <div class="channel-icon-circle tw">
                            <?php echo tokoku_icon( 'x-twitter', 19 ); ?>
                        </div>
                        <span class="channel-name">X (Twitter)</span>
                    </a>

                    <!-- Telegram -->
                    <a href="#" id="share-modal-tg" target="_blank" rel="noopener noreferrer" class="channel-card channel-tg" title="Telegram">
                        <div class="channel-icon-circle tg">
                            <?php echo tokoku_icon( 'telegram', 22 ); ?>
                        </div>
                        <span class="channel-name">Telegram</span>
                    </a>

                    <!-- Pinterest -->
                    <a href="#" id="share-modal-pin" target="_blank" rel="noopener noreferrer" class="channel-card channel-pin" title="Pinterest">
                        <div class="channel-icon-circle pin">
                            <?php echo tokoku_icon( 'pinterest', 22 ); ?>
                        </div>
                        <span class="channel-name">Pinterest</span>
                    </a>

                    <!-- LinkedIn -->
                    <a href="#" id="share-modal-in" target="_blank" rel="noopener noreferrer" class="channel-card channel-in" title="LinkedIn">
                        <div class="channel-icon-circle in">
                            <?php echo tokoku_icon( 'linkedin', 19 ); ?>
                        </div>
                        <span class="channel-name">LinkedIn</span>
                    </a>

                    <!-- Email -->
                    <a href="#" id="share-modal-mail" class="channel-card channel-mail" title="Email">
                        <div class="channel-icon-circle mail">
                            <?php echo tokoku_icon( 'envelope', 19 ); ?>
                        </div>
                        <span class="channel-name">Email</span>
                    </a>

                    <!-- Native Device Share -->
                    <button type="button" id="share-modal-native" class="channel-card channel-native" title="Aplikasi Lainnya">
                        <div class="channel-icon-circle native">
                            <?php echo tokoku_icon( 'share-nodes', 19 ); ?>
                        </div>
                        <span class="channel-name">Lainnya</span>
                    </button>
                </div>
            </div>

            <!-- 📷 QR Code Generator Section -->
            <div class="share-qrcode-section">
                <button type="button" class="btn-toggle-qrcode" id="btn-toggle-qrcode" aria-expanded="false">
                    <?php echo tokoku_icon( 'grid', 16 ); ?>
                    <span id="btn-toggle-qrcode-text">Tampilkan QR Code untuk Scan HP</span>
                </button>
                <div class="share-qrcode-box" id="share-qrcode-box" style="display: none;">
                    <div class="share-qrcode-card">
                        <img id="share-qrcode-img" src="" alt="QR Code Halaman" width="160" height="160" loading="lazy">
                        <p class="share-qrcode-hint">Buka aplikasi kamera smartphone Anda dan arahkan ke QR Code ini untuk membuka halaman secara instan.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
