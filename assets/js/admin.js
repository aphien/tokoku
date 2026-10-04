/**
 * TokoKu Admin Settings Interactivity & Update Handler
 */
jQuery(document).ready(function($) {
    // 1. Tab Switching & Persistence
    function activateTab(tabId) {
        if (!tabId || !$('#' + tabId).length) {
            tabId = 'tab-general';
        }
        $('.tokoku-nav-item').removeClass('active');
        $('.tokoku-tab-panel').removeClass('active');
        
        $('.tokoku-nav-item[data-tab="' + tabId + '"]').addClass('active');
        $('#' + tabId).addClass('active');
        $('#tokoku_active_tab').val(tabId);
        localStorage.setItem('tokoku_active_tab', tabId);

        // 🔄 Fix TinyMCE display when switching tabs
        if (typeof tinyMCE !== 'undefined' && (tabId === 'tab-whatsapp' || tabId === 'tab-faq')) {
            setTimeout(function() {
                tinyMCE.editors.forEach(function(editor) {
                    if (editor.id === 'tokoku_wa_message' || editor.id.startsWith('tokokufaqa')) {
                        editor.theme.resizeTo('100%', '100%');
                    }
                });
            }, 100);
        }
    }

    // Determine initial active tab from URL param or localStorage
    var urlParams = new URLSearchParams(window.location.search);
    var urlTab = urlParams.get('tab');
    var savedTab = localStorage.getItem('tokoku_active_tab');
    var startTab = urlTab || savedTab || 'tab-general';
    activateTab(startTab);

    // Tab click handler
    $('.tokoku-nav-item').on('click', function(e) {
        var tabId = $(this).data('tab');
        activateTab(tabId);

        // Update URL query parameter without page reload
        if (history.replaceState) {
            var url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', url);
        }
    });

    // 2. Settings Save Button Feedback
    $('.tokoku-settings-form').on('submit', function() {
        var btns = $(this).find('.tokoku-submit-update-btn');
        btns.prop('disabled', true).addClass('is-loading');
        btns.find('.tokoku-btn-text').text('Menyimpan Perubahan...');
    });

    // 3. Floating Toast on Settings Saved
    if (urlParams.get('settings-updated') === 'true') {
        var toast = $('<div class="tokoku-toast-notice"><span class="dashicons dashicons-yes-alt"></span> Pengaturan TokoKu berhasil disimpan & diperbarui!</div>');
        $('body').append(toast);
        setTimeout(function() {
            toast.addClass('show');
        }, 150);
        setTimeout(function() {
            toast.removeClass('show');
            setTimeout(function() { toast.remove(); }, 400);
        }, 3500);
    }

    // 4. Quick Save Keyboard Shortcut (Cmd/Ctrl + S)
    $(document).on('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 's') {
            var $form = $('.tokoku-settings-form');
            if ($form.length > 0) {
                e.preventDefault();
                $form.first().submit();
            }
        }
    });

    // Collapsible FAQ in Admin
    $(document).on('click', '.tokoku-collapsible-header', function() {
        var item = $(this).closest('.tokoku-collapsible-item');
        var content = item.find('.tokoku-collapsible-content');
        var icon = $(this).find('.dashicons-arrow-down-alt2, .dashicons-arrow-up-alt2');
        
        content.slideToggle(200);
        item.toggleClass('is-open');
        
        if (item.hasClass('is-open')) {
            icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
        } else {
            icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        }
    });

    // Add FAQ Item
    $('#tokoku-add-faq').on('click', function() {
        var hiddenItems = $('.faq-item-row:hidden');
        if (hiddenItems.length > 0) {
            var nextItem = $(hiddenItems[0]);
            nextItem.fadeIn(300);
            nextItem.find('.tokoku-collapsible-header').trigger('click');
        } else {
            alert('Maksimal 10 pertanyaan FAQ.');
        }
    });

    // Remove FAQ Item
    $(document).on('click', '.tokoku-remove-faq', function() {
        if (confirm('Hapus item FAQ ini? Data akan benar-benar hilang setelah Anda menyimpan perubahan.')) {
            var item = $(this).closest('.tokoku-collapsible-item');
            item.find('input, textarea').val('');
            item.find('.tokoku-collapsible-header span:last-child span').text('Item FAQ #' + item.data('index'));
            item.hide();
        }
    });

    // Sync Question Input to Header Label
    $(document).on('input', '.tokoku-faq-input-q', function() {
        var val = $(this).val();
        var header = $(this).closest('.tokoku-collapsible-item').find('.tokoku-collapsible-header span:last-child span');
        if (val) {
            header.text(val);
        } else {
            header.text('Item FAQ #' + $(this).closest('.tokoku-collapsible-item').data('index'));
        }
    });

    if ($.fn.wpColorPicker) {
        $('.color-picker').wpColorPicker();
    }

    // 🔄 Semantic Version Comparison Helper
    function compareVersions(v1, v2) {
        var p1 = (v1 || '').replace(/[^0-9.]/g, '').split('.').map(Number);
        var p2 = (v2 || '').replace(/[^0-9.]/g, '').split('.').map(Number);
        var len = Math.max(p1.length, p2.length);
        for (var i = 0; i < len; i++) {
            var n1 = p1[i] || 0;
            var n2 = p2[i] || 0;
            if (n1 > n2) return 1;
            if (n1 < n2) return -1;
        }
        return 0;
    }

    // 🔄 Theme Update Checker (GitHub Releases & Tags)
    $('#tokoku-check-update').on('click', function() {
        var btn = $(this);
        var status = $('#tokoku-update-status');
        var loader = $('#tokoku-update-loader');
        var currentVersion = tokokuAdmin.version;
        var repo = 'aphien/tokoku'; 
        
        btn.prop('disabled', true).css('opacity', '0.7');
        status.hide();
        loader.css('display', 'flex');
        
        // Advanced Markdown parser for changelog
        var formatLog = function(text) {
            if (!text) return '<p style="color:#64748b; font-style:italic;">Tidak ada catatan rilis detail.</p>';
            
            var src = text.trim();

            // Escape raw HTML entities
            src = src
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Fenced code blocks
            src = src.replace(/```([a-z0-9_-]*)\n([\s\S]*?)```/gim, function(match, lang, code) {
                return '<pre style="background:#0f172a; color:#f8fafc; padding:12px 16px; border-radius:8px; font-size:0.85rem; overflow-x:auto; margin:12px 0; font-family:Consolas,Monaco,monospace;"><code>' + code.trim() + '</code></pre>';
            });

            // Horizontal rules
            src = src.replace(/^---$/gim, '<hr style="border:none; border-top:1px solid #e2e8f0; margin:16px 0;">');

            // Headers
            src = src.replace(/^#### (.*$)/gim, '<h5 style="margin:14px 0 6px 0; color:#334155; font-size:1rem; font-weight:700;">$1</h5>');
            src = src.replace(/^### (.*$)/gim, '<h4 style="margin:18px 0 8px 0; color:#1e293b; font-size:1.15rem; font-weight:800; display:flex; align-items:center; gap:6px;">$1</h4>');
            src = src.replace(/^## (.*$)/gim, '<h3 style="margin:22px 0 10px 0; color:#0f172a; font-size:1.3rem; font-weight:800; border-bottom:1.5px solid #e2e8f0; padding-bottom:6px;">$1</h3>');
            src = src.replace(/^# (.*$)/gim, '<h2 style="margin:24px 0 12px 0; color:#0f172a; font-size:1.5rem; font-weight:800; border-bottom:2px solid #3b82f6; padding-bottom:8px;">$1</h2>');

            // Blockquotes
            src = src.replace(/^&gt; (.*$)/gim, '<blockquote style="border-left:4px solid #3b82f6; background:#eff6ff; padding:10px 16px; color:#1e40af; margin:12px 0; border-radius:0 8px 8px 0; font-size:0.92rem;">$1</blockquote>');

            // Bold and Italic
            src = src.replace(/\*\*\*(.*?)\*\*\*/gim, '<strong><em>$1</em></strong>');
            src = src.replace(/\*\*(.*?)\*\*/gim, '<strong style="color:#0f172a; font-weight:700;">$1</strong>');
            src = src.replace(/\*(.*?)\*/gim, '<em>$1</em>');

            // Inline Code
            src = src.replace(/`([^`]+)`/g, '<code style="background:#f1f5f9; color:#0284c7; padding:2px 6px; border-radius:4px; font-size:0.88em; font-family:Consolas,monospace; border:1px solid #e2e8f0;">$1</code>');

            // Markdown Links [text](url)
            src = src.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" style="color:#2563eb; text-decoration:underline; font-weight:600;">$1</a>');

            // Bullet Lists (Nested & Top Level)
            src = src.replace(/^(\s*)[-*]\s(.*$)/gim, function(match, spaces, content) {
                var indent = spaces.length >= 4 ? 'margin-left:36px; list-style-type:circle;' : 'margin-left:18px; list-style-type:disc;';
                return '<li style="margin-bottom:6px; ' + indent + ' color:#334155; line-height:1.5;">' + content + '</li>';
            });

            return '<div class="tokoku-changelog-content" style="font-family:system-ui,-apple-system,sans-serif; line-height:1.65; color:#334155; font-size:0.92rem;">' +
                   src.replace(/\n\n+/g, '<br/><br/>')
                      .replace(/\n/g, '<br/>')
                      .replace(/(<br\/>)+<li/g, '<li')
                      .replace(/<\/li>(<br\/>)+/g, '</li>')
                      .replace(/(<br\/>)+<h/g, '<h')
                      .replace(/(<br\/>)+<hr/g, '<hr')
                      .replace(/(<br\/>)+<blockquote/g, '<blockquote')
                      .replace(/(<br\/>)+<pre/g, '<pre') +
                   '</div>';
        };

        // Call GitHub API for latest release (with cache-busting)
        var apiUrl = 'https://api.github.com/repos/' + repo + '/releases/latest?_=' + Date.now();
        fetch(apiUrl, { headers: { 'Accept': 'application/vnd.github.v3+json' } })
            .then(function(response) {
                if (response.status === 404 || response.status === 204) {
                    // No formal release — fallback to tags
                    return fetch('https://api.github.com/repos/' + repo + '/tags?per_page=1&_=' + Date.now(), {
                        headers: { 'Accept': 'application/vnd.github.v3+json' }
                    }).then(function(res) {
                        if (!res.ok) throw new Error('GitHub API error: ' + res.status + ' ' + res.statusText);
                        return res.json().then(function(tags) {
                            if (!tags || tags.length === 0) throw new Error('Tidak ditemukan rilis atau tag di repositori GitHub.');
                            var tag = tags[0];
                            return {
                                tag_name:    tag.name,
                                name:        tag.name,
                                zipball_url: tag.zipball_url || ('https://github.com/' + repo + '/archive/refs/tags/' + tag.name + '.zip'),
                                body:        'Pembaruan dari repositori GitHub (tag: ' + tag.name + ')'
                            };
                        });
                    });
                }
                if (response.status === 403) {
                    throw new Error('GitHub API rate limit tercapai. Tunggu beberapa menit lalu coba lagi.');
                }
                if (!response.ok) throw new Error('GitHub API error: ' + response.status + ' ' + response.statusText);
                return response.json();
            })
            .then(function(data) {
                loader.hide();
                btn.prop('disabled', false).css('opacity', '1');

                var rawTag       = data.tag_name || '';
                var latestVersion = rawTag.replace(/[^0-9.]/g, '');
                var releaseName  = data.name || ('v' + latestVersion);
                var downloadUrl  = data.zipball_url || ('https://github.com/' + repo + '/archive/refs/tags/' + rawTag + '.zip');
                var logHtml      = formatLog(data.body);

                if (!latestVersion) {
                    status.html('<div style="color:#991b1b; background:#fef2f2; padding:20px; border-radius:12px; border:1px solid #fecaca; display:flex; align-items:center; gap:12px;">' +
                                '<span class="dashicons dashicons-warning" style="font-size:26px; width:26px; height:26px; color:#dc2626;"></span>' +
                                '<div><strong style="display:block; margin-bottom:4px;">Tidak dapat membaca nomor versi dari GitHub.</strong>' +
                                '<span style="font-size:0.9rem; color:#b91c1c;">Tag ditemukan: ' + (rawTag || '(kosong)') + ' — pastikan tag menggunakan format vX.Y.Z</span></div>' +
                                '</div>').fadeIn();
                    return;
                }

                var comparison = compareVersions(latestVersion, currentVersion);

                if (comparison > 0) {
                    status.html('<div style="background: #fff; border-radius: 12px; border: 1px solid #bfdbfe; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.1); overflow: hidden;">' +
                                '<div style="background: #eff6ff; padding: 25px; border-bottom: 1px solid #bfdbfe;">' +
                                    '<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">' +
                                        '<div style="background: #007bff; color: #fff; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,123,255,0.3);">' +
                                            '<span class="dashicons dashicons-download" style="font-size: 22px; width: 22px; height: 22px;"></span>' +
                                        '</div>' +
                                        '<div>' +
                                            '<h3 style="margin: 0 0 4px 0; color: #1e3a8a; font-size: 1.3rem; font-weight: 800;">Versi Baru Tersedia: ' + releaseName + '</h3>' +
                                            '<p style="margin: 0; color: #2563eb; font-weight: 600; font-size: 0.95rem;">Pembaruan sangat disarankan untuk toko Anda</p>' +
                                        '</div>' +
                                    '</div>' +
                                    '<p style="margin: 0 0 20px 0; color: #1e40af; font-size: 0.95rem;">Tingkatkan tema TokoKu Anda sekarang untuk menikmati fitur terbaru dan peningkatan performa.</p>' +
                                    '<div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">' +
                                        '<button type="button" id="tokoku-install-update" data-url="' + downloadUrl + '" class="button button-primary tokoku-install-update-btn">' +
                                            '<span class="dashicons dashicons-update-alt"></span><span class="tokoku-btn-text">Perbarui Otomatis ke v' + latestVersion + '</span>' +
                                        '</button>' +
                                        '<div id="tokoku-install-loader" style="display: none; align-items: center; gap: 10px; color: #007bff; font-weight: 700;">' +
                                            '<span class="spinner is-active" style="float: none; margin: 0;"></span> Memproses pengunduhan dan instalasi...' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div style="padding: 25px; background: #f8fafc;">' +
                                    '<h4 style="margin: 0 0 15px 0; color: #0f172a; display: flex; align-items: center; gap: 8px; font-size: 1.1rem; font-weight: 700;">' +
                                        '<span class="dashicons dashicons-media-text" style="color:#007bff;"></span> Catatan Rilis & Log Pembaruan (Changelog) ' + releaseName +
                                    '</h4>' +
                                    '<div style="background: #fff; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; max-height: 400px; overflow-y: auto;">' +
                                        logHtml +
                                    '</div>' +
                                '</div>' +
                                '</div>');
                } else {
                    status.html('<div style="background: #fff; border-radius: 12px; border: 1px solid #a7f3d0; box-shadow: 0 10px 25px rgba(5, 150, 105, 0.08); overflow: hidden;">' +
                                '<div style="background: #ecfdf5; padding: 22px; border-bottom: 1px solid #a7f3d0;">' +
                                    '<div style="display: flex; align-items: center; gap: 12px; color: #065f46; margin-bottom: 8px;">' +
                                        '<span class="dashicons dashicons-yes-alt" style="font-size: 26px; width: 26px; height: 26px; color: #059669;"></span> ' +
                                        '<strong style="font-size: 1.2rem; font-weight: 800;">TokoKu v' + currentVersion + ' adalah versi terbaru.</strong>' +
                                    '</div>' +
                                    '<p style="margin: 0 0 16px 0; font-size: 0.92rem; color: #047857;">Tema Anda sudah menggunakan kode, optimasi kecepatan, dan fitur termutakhir dari rilis resmi TokoKu.</p>' +
                                    '<div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">' +
                                        '<button type="button" id="tokoku-install-update" data-url="' + downloadUrl + '" class="button button-secondary tokoku-reinstall-btn">' +
                                            '<span class="dashicons dashicons-update"></span><span class="tokoku-btn-text">Instal Ulang / Paksa Sinkronkan Versi Ini</span>' +
                                        '</button>' +
                                        '<div id="tokoku-install-loader" style="display: none; align-items: center; gap: 10px; color: #007bff; font-weight: 700;">' +
                                            '<span class="spinner is-active" style="float: none; margin: 0;"></span> Memproses pengunduhan dan instalasi...' +
                                        '</div>' +
                                    '</div>' +
                                    '<p style="margin: 12px 0 0 0; font-size: 0.8rem; color: #059669; opacity: 0.85;">Terakhir diperiksa: ' + new Date().toLocaleString() + '</p>' +
                                '</div>' +
                                '<div style="padding: 22px; background: #f8fafc;">' +
                                    '<h4 style="margin: 0 0 14px 0; color: #0f172a; display: flex; align-items: center; gap: 8px; font-size: 1.05rem; font-weight: 700;">' +
                                        '<span class="dashicons dashicons-media-text" style="color:#059669;"></span> Catatan Rilis & Log Pembaruan (Changelog) ' + releaseName +
                                    '</h4>' +
                                    '<div style="background: #fff; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; max-height: 400px; overflow-y: auto;">' +
                                        logHtml +
                                    '</div>' +
                                '</div>' +
                                '</div>');
                }
                status.fadeIn();
            })
            .catch(function(error) {
                loader.hide();
                btn.prop('disabled', false).css('opacity', '1');
                status.html('<div style="color: #991b1b; display: flex; align-items: flex-start; gap: 12px; background: #fef2f2; padding: 20px; border-radius: 12px; border: 1px solid #fecaca;">' +
                            '<span class="dashicons dashicons-warning" style="font-size: 26px; width: 26px; height: 26px; color:#dc2626; flex-shrink:0; margin-top:2px;"></span> ' +
                            '<div>' +
                                '<strong style="font-size: 1.05rem; display:block; margin-bottom:4px;">Gagal memeriksa pembaruan dari GitHub.</strong>' +
                                '<span style="font-size: 0.9rem; color: #b91c1c;">' + error.message + '</span>' +
                                '<p style="margin: 10px 0 0 0; font-size: 0.85rem; color: #64748b;">Tips: Periksa koneksi internet Anda, atau coba lagi dalam beberapa menit. Jika tetap gagal, unduh manual dari <a href="https://github.com/aphien/tokoku/releases" target="_blank" style="color:#007bff;">GitHub Releases</a>.</p>' +
                            '</div>' +
                            '</div>');
                status.fadeIn();
            });
    });

    // 🚀 One-Click Install & Update Logic
    $(document).on('click', '#tokoku-install-update', function() {
        var btn = $(this);
        var zipUrl = btn.data('url');
        var loader = $('#tokoku-install-loader');
        
        if (!confirm('Apakah Anda yakin ingin memperbarui/menginstal tema sekarang? Proses ini akan mengunduh dan menimpa file tema dengan versi terbaru.')) {
            return;
        }

        btn.hide();
        loader.css('display', 'flex');

        $.post(ajaxurl, {
            action: 'tokoku_handle_update',
            download_url: zipUrl,
            nonce: tokokuAdmin.updateNonce
        }, function(response) {
            if (response.success) {
                loader.html('<span class="dashicons dashicons-yes-alt" style="color: #059669; font-size:22px; width:22px; height:22px;"></span> <span style="color: #059669; font-weight:800;">Berhasil diperbarui! Memuat ulang halaman...</span>');
                setTimeout(function() { location.reload(); }, 1200);
            } else {
                alert('Gagal memperbarui: ' + (response.data || 'Terjadi kesalahan sistem'));
                loader.hide();
                btn.show();
            }
        }).fail(function() {
            alert('Terjadi kesalahan jaringan/server. Coba lagi nanti.');
            loader.hide();
            btn.show();
        });
    });


    // Media Uploader (Delegated Events for Dynamic Items)
    $(document).on('click', '.tokoku-upload-btn', function(e) {
        e.preventDefault();
        var button = $(this);
        var container = button.closest('.tokoku-media-upload');
        var custom_uploader = wp.media({
            title: 'Pilih Gambar',
            button: { text: 'Gunakan Gambar' },
            multiple: false
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            container.find('.tokoku-preview-img').attr('src', attachment.url).show();
            container.find('input').val(attachment.url);
            container.find('.tokoku-remove-btn').show();
        }).open();
    });

    $(document).on('click', '.tokoku-upload-btn-id', function(e) {
        e.preventDefault();
        var button = $(this);
        var container = button.closest('.tokoku-media-upload');
        var custom_uploader = wp.media({
            title: 'Pilih Ikon',
            button: { text: 'Gunakan Ikon' },
            multiple: false
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            container.find('.tokoku-preview-img').attr('src', attachment.url).show();
            container.find('input').val(attachment.id);
            container.find('.tokoku-remove-btn').show();
        }).open();
    });

    $(document).on('click', '.tokoku-remove-btn', function(e) {
        e.preventDefault();
        var container = $(this).closest('.tokoku-media-upload');
        container.find('.tokoku-preview-img').hide();
        container.find('input').val('');
        $(this).hide();
    });

    // Vertical Tab Switching
    $(document).on('click', '.tokoku-vtab-link', function() {
        var target = $(this).data('target');
        var container = $(this).closest('.tokoku-vtabs-container');
        
        container.find('.tokoku-vtab-link').removeClass('active');
        container.find('.tokoku-vtab-panel').removeClass('active');
        
        $(this).addClass('active');
        $('#' + target).addClass('active');
    });

    // 🚀 Dynamic Add/Remove Handlers
    $(document).on('click', '.tokoku-add-testi', function() {
        var nextItem = $('.testi-nav .tokoku-vtab-link:hidden').first();
        if (nextItem.length) {
            nextItem.fadeIn(200).trigger('click');
        } else {
            alert('Maksimal 20 testimoni.');
        }
    });

    $(document).on('click', '.tokoku-add-logo', function() {
        var nextItem = $('.logo-nav .tokoku-vtab-link:hidden').first();
        if (nextItem.length) {
            nextItem.fadeIn(200).trigger('click');
        } else {
            alert('Maksimal 50 logo.');
        }
    });

    $(document).on('click', '.tokoku-add-slider', function() {
        var nextItem = $('.slider-nav .tokoku-vtab-link:hidden').first();
        if (nextItem.length) {
            nextItem.fadeIn(200).trigger('click');
        } else {
            alert('Maksimal 10 banner.');
        }
    });

    $(document).on('click', '.tokoku-remove-unit-v', function(e) {
        e.stopPropagation();
        if (confirm('Hapus unit ini? Konten akan dikosongkan setelah disimpan.')) {
            var link = $(this).closest('.tokoku-vtab-link');
            var targetId = link.data('target');
            var panel = $('#' + targetId);
            
            panel.find('input, textarea').val('');
            panel.find('.tokoku-preview-img').hide();
            link.hide();
            
            // Switch to first visible tab
            link.siblings(':visible').first().trigger('click');
        }
    });
});
