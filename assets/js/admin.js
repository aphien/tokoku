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
        if ($(e.target).hasClass('tokoku-drag-handle')) {
            return; // Let sortable handle it
        }
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

    // Drag & Drop Menu Reordering
    $('.tokoku-sortable-nav').sortable({
        handle: '.tokoku-drag-handle',
        placeholder: 'ui-sortable-placeholder',
        axis: 'y',
        update: function(event, ui) {
            var order = [];
            $('.tokoku-nav-item').each(function() {
                order.push($(this).data('tab'));
            });
            $('#tokoku_admin_menu_order').val(order.join(','));
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
        
        // Markdown parser for changelog
        var formatLog = function(text) {
            if (!text) return 'Tidak ada catatan rilis.';
            var html = text
                .replace(/^### (.*$)/gim, '<h4 style="margin:15px 0 5px 0; color:#1e293b; font-size:1.1rem;">$1</h4>')
                .replace(/^## (.*$)/gim, '<h3 style="margin:20px 0 10px 0; color:#0f172a; border-bottom:1px solid #e2e8f0; padding-bottom:5px; font-size:1.3rem;">$1</h3>')
                .replace(/^# (.*$)/gim, '<h2 style="margin:20px 0 10px 0; color:#0f172a; border-bottom:2px solid #e2e8f0; padding-bottom:5px; font-size:1.5rem;">$1</h2>')
                .replace(/^\> (.*$)/gim, '<blockquote style="border-left:4px solid #cbd5e1; background:#f8fafc; padding:10px 15px; color:#64748b; margin:10px 0; border-radius:0 8px 8px 0;">$1</blockquote>')
                .replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/gim, '<em>$1</em>')
                .replace(/`([^`]+)`/g, '<code style="background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:0.9em; color:#db2777;">$1</code>')
                .replace(/^\s*[-*]\s(.*$)/gim, '<li style="margin-bottom:6px; margin-left:20px; list-style-type:disc;">$1</li>');
            return '<div style="font-family:system-ui,-apple-system,sans-serif; line-height:1.6; color:#475569;">' + html.replace(/\n/g, '<br/>').replace(/(<br\/>)+<li/g, '<li').replace(/<\/li>(<br\/>)+/g, '</li>') + '</div>';
        };

        // Call GitHub API for latest release
        fetch('https://api.github.com/repos/' + repo + '/releases/latest')
            .then(function(response) {
                if (response.ok) return response.json();
                // Fallback to tags if releases is empty or blocked
                return fetch('https://api.github.com/repos/' + repo + '/tags')
                    .then(function(res) {
                        if (!res.ok) throw new Error('Gagal menghubungi server GitHub');
                        return res.json().then(function(tags) {
                            if (tags && tags.length > 0) {
                                return {
                                    tag_name: tags[0].name,
                                    name: tags[0].name,
                                    zipball_url: tags[0].zipball_url,
                                    body: 'Pembaruan terbaru dari repositori GitHub ' + tags[0].name
                                };
                            }
                            throw new Error('Tidak ditemukan tag rilis');
                        });
                    });
            })
            .then(function(data) {
                loader.hide();
                btn.prop('disabled', false).css('opacity', '1');
                
                var latestVersion = (data.tag_name || '').replace(/[^0-9.]/g, ''); 
                var releaseName = data.name || data.tag_name;
                var downloadUrl = data.zipball_url || ('https://github.com/' + repo + '/archive/refs/tags/' + (data.tag_name || ('v' + latestVersion)) + '.zip');
                var logHtml = formatLog(data.body);

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
                                        '<button type="button" id="tokoku-install-update" data-url="' + downloadUrl + '" class="button button-primary" style="height: 46px; padding: 0 28px; font-size: 14px; font-weight: 800; border-radius: 50px; background: linear-gradient(135deg, #007bff, #0056b3); border: none; box-shadow: 0 4px 14px rgba(0, 123, 255, 0.35); cursor: pointer;">' +
                                            '<span class="dashicons dashicons-update-alt" style="vertical-align: middle; margin-right: 8px;"></span> Perbarui Otomatis ke v' + latestVersion +
                                        '</button>' +
                                        '<div id="tokoku-install-loader" style="display: none; align-items: center; gap: 10px; color: #007bff; font-weight: 700;">' +
                                            '<span class="spinner is-active" style="float: none; margin: 0;"></span> Memproses pengunduhan dan instalasi...' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div style="padding: 25px; background: #f8fafc;">' +
                                    '<h4 style="margin: 0 0 15px 0; color: #0f172a; display: flex; align-items: center; gap: 8px; font-size: 1.1rem; font-weight: 700;">' +
                                        '<span class="dashicons dashicons-media-text" style="color:#007bff;"></span> Log Pembaruan (Changelog)' +
                                    '</h4>' +
                                    '<div style="background: #fff; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; max-height: 350px; overflow-y: auto;">' +
                                        logHtml +
                                    '</div>' +
                                '</div>' +
                                '</div>');
                } else {
                    status.html('<div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 22px;">' +
                                '<div style="display: flex; align-items: center; gap: 12px; color: #065f46; margin-bottom: 10px;">' +
                                    '<span class="dashicons dashicons-yes-alt" style="font-size: 26px; width: 26px; height: 26px; color: #059669;"></span> ' +
                                    '<strong style="font-size: 1.15rem; font-weight: 800;">TokoKu v' + currentVersion + ' adalah versi terbaru.</strong>' +
                                '</div>' +
                                '<p style="margin: 0 0 15px 0; font-size: 0.92rem; color: #047857;">Tema Anda sudah menggunakan kode dan fitur termutakhir dari rilis resmi TokoKu.</p>' +
                                '<div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">' +
                                    '<button type="button" id="tokoku-install-update" data-url="' + downloadUrl + '" class="button button-secondary" style="height: 38px; padding: 0 18px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">' +
                                        '<span class="dashicons dashicons-update"></span> Instal Ulang / Paksa Perbarui Versi Ini' +
                                    '</button>' +
                                    '<div id="tokoku-install-loader" style="display: none; align-items: center; gap: 10px; color: #007bff; font-weight: 700;">' +
                                        '<span class="spinner is-active" style="float: none; margin: 0;"></span> Memproses pengunduhan dan instalasi...' +
                                    '</div>' +
                                '</div>' +
                                '<p style="margin: 12px 0 0 0; font-size: 0.82rem; color: #6b7280;">Terakhir diperiksa: ' + new Date().toLocaleString() + '</p>' +
                                '</div>');
                }
                status.fadeIn();
            })
            .catch(function(error) {
                loader.hide();
                btn.prop('disabled', false).css('opacity', '1');
                status.html('<div style="color: #991b1b; display: flex; align-items: center; gap: 12px; background: #fef2f2; padding: 20px; border-radius: 12px; border: 1px solid #fecaca;">' +
                            '<span class="dashicons dashicons-warning" style="font-size: 26px; width: 26px; height: 26px; color:#dc2626;"></span> ' +
                            '<div>' +
                                '<strong style="font-size: 1.05rem; display:block; margin-bottom:4px;">Gagal memeriksa pembaruan dari GitHub.</strong>' +
                                '<span style="font-size: 0.9rem; color: #b91c1c;">' + error.message + '</span>' +
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


    // Media Uploader
    $('.tokoku-upload-btn').on('click', function(e) {
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

    $('.tokoku-upload-btn-id').on('click', function(e) {
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

    $('.tokoku-remove-btn').on('click', function() {
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

    // 🚀 Dynamic Add/Remove
    $('.tokoku-add-testi').on('click', function() {
        var nextItem = $('.testi-nav .tokoku-vtab-link:hidden').first();
        if (nextItem.length) {
            nextItem.fadeIn().trigger('click');
        } else {
            alert('Maksimal 20 testimoni.');
        }
    });

    $('.tokoku-add-logo').on('click', function() {
        var nextItem = $('.logo-nav .tokoku-vtab-link:hidden').first();
        if (nextItem.length) {
            nextItem.fadeIn().trigger('click');
        } else {
            alert('Maksimal 50 logo.');
        }
    });

    $('.tokoku-add-slider').on('click', function() {
        var nextItem = $('.slider-nav .tokoku-vtab-link:hidden').first();
        if (nextItem.length) {
            nextItem.fadeIn().trigger('click');
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
