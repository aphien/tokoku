/**
 * TokoKu Admin Settings Interactivity
 */
jQuery(document).ready(function($) {
    // Tab Switching
    $('.tokoku-nav-item').on('click', function() {
        var tabId = $(this).data('tab');
        $('.tokoku-nav-item').removeClass('active');
        $('.tokoku-tab-panel').removeClass('active');
        
        $(this).addClass('active');
        $('#' + tabId).addClass('active');

        // 🔄 Fix TinyMCE display when switching tabs
        if (typeof tinyMCE !== 'undefined' && (tabId === 'tab-whatsapp' || tabId === 'tab-faq')) {
            tinyMCE.editors.forEach(function(editor) {
                if (editor.id === 'tokoku_wa_message' || editor.id.startsWith('tokokufaqa')) {
                    editor.theme.resizeTo('100%', '100%');
                }
            });
        }
    });

    // Drag & Drop Menu Reordering
    $('.tokoku-sortable-nav').sortable({
        handle: '.tokoku-nav-item',
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
            // Automatically open it
            nextItem.find('.tokoku-collapsible-header').trigger('click');
        } else {
            alert('Maksimal 10 pertanyaan FAQ.');
        }
    });

    // Remove FAQ Item
    $(document).on('click', '.tokoku-remove-faq', function() {
        if (confirm('Hapus item FAQ ini? Data akan benar-benar hilang setelah Anda menyimpan perubahan.')) {
            var item = $(this).closest('.tokoku-collapsible-item');
            item.find('input, textarea').val(''); // Clear values
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

    // 🔄 Theme Update Checker (Real GitHub Connection)
    $('#tokoku-check-update').on('click', function() {
        var btn = $(this);
        var status = $('#tokoku-update-status');
        var loader = $('#tokoku-update-loader');
        var currentVersion = tokokuAdmin.version;
        var repo = 'aphien/tokoku'; 
        
        btn.prop('disabled', true).css('opacity', '0.7');
        status.hide();
        loader.css('display', 'flex');
        
        // Call GitHub API for latest release
        fetch('https://api.github.com/repos/' + repo + '/releases/latest')
            .then(response => {
                if (!response.ok) throw new Error('Repo not found or no releases');
                return response.json();
            })
            .then(data => {
                loader.hide();
                btn.prop('disabled', false).css('opacity', '1');
                
                var latestVersion = data.tag_name.replace(/[^0-9.]/g, ''); 
                var releaseUrl = data.html_url;
                var releaseName = data.name || data.tag_name;
                
                // Markdown parser for changelog
                var formatLog = function(text) {
                    if (!text) return 'Tidak ada log pembaruan yang dirilis.';
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
                
                var logHtml = formatLog(data.body);

                if (latestVersion > currentVersion) {
                    status.html('<div style="background: #fff; border-radius: 12px; border: 1px solid #bfdbfe; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.1); overflow: hidden;">' +
                                '<div style="background: #eff6ff; padding: 25px; border-bottom: 1px solid #bfdbfe;">' +
                                    '<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">' +
                                        '<div style="background: #2563eb; color: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">' +
                                            '<span class="dashicons dashicons-download" style="font-size: 20px; width: 20px; height: 20px;"></span>' +
                                        '</div>' +
                                        '<div>' +
                                            '<h3 style="margin: 0 0 5px 0; color: #1e3a8a; font-size: 1.3rem;">Versi Baru Tersedia: ' + releaseName + '</h3>' +
                                            '<p style="margin: 0; color: #3b82f6; font-weight: 600;">Pembaruan sangat disarankan</p>' +
                                        '</div>' +
                                    '</div>' +
                                    '<p style="margin: 0 0 20px 0; color: #1e40af; font-size: 1rem;">Tingkatkan tema TokoKu Anda sekarang untuk menikmati fitur terbaru dan peningkatan performa.</p>' +
                                    '<div style="display: flex; gap: 12px; align-items: center;">' +
                                        '<button type="button" id="tokoku-install-update" data-url="' + data.zipball_url + '" class="button button-primary" style="height: 48px; padding: 0 30px; font-size: 1.05rem; border-radius: 8px; background: #2563eb; border: none; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">' +
                                            '<span class="dashicons dashicons-update-alt" style="vertical-align: middle; margin-right: 8px; animation: spin 2s linear infinite;"></span> Perbarui Otomatis' +
                                        '</button>' +
                                        '<div id="tokoku-install-loader" style="display: none; align-items: center; gap: 10px; color: #2563eb; font-weight: 600;">' +
                                            '<span class="spinner is-active" style="float: none; margin: 0;"></span> Memproses instalasi...' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                                '<div style="padding: 25px; background: #f8fafc;">' +
                                    '<h4 style="margin: 0 0 15px 0; color: #0f172a; display: flex; align-items: center; gap: 8px; font-size: 1.1rem;">' +
                                        '<span class="dashicons dashicons-media-text"></span> Log Pembaruan (Changelog)' +
                                    '</h4>' +
                                    '<div style="background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #e2e8f0; max-height: 400px; overflow-y: auto;">' +
                                        logHtml +
                                    '</div>' +
                                '</div>' +
                                '</div>');
                } else {
                    status.html('<div style="display: flex; align-items: center; gap: 10px; color: #059669; background: #ecfdf5; padding: 20px; border-radius: 8px; border: 1px solid #a7f3d0;">' +
                                '<span class="dashicons dashicons-yes-alt" style="font-size: 24px; width: 24px; height: 24px;"></span> ' +
                                '<strong style="font-size: 1.1rem;">TokoKu v' + currentVersion + ' adalah versi terbaru.</strong>' +
                                '</div>' +
                                '<p style="margin: 10px 0 0 0; font-size: 0.9rem; color: #64748b;">Terakhir diperiksa: ' + new Date().toLocaleString() + '</p>');
                }
                status.fadeIn();
            })
            .catch(error => {
                loader.hide();
                btn.prop('disabled', false).css('opacity', '1');
                status.html('<div style="color: #dc2626; display: flex; align-items: center; gap: 10px; background: #fef2f2; padding: 20px; border-radius: 8px; border: 1px solid #fecaca;">' +
                            '<span class="dashicons dashicons-error" style="font-size: 24px; width: 24px; height: 24px;"></span> ' +
                            '<strong style="font-size: 1.1rem;">Gagal memeriksa pembaruan.</strong>' +
                            '</div>' +
                            '<p style="margin: 10px 0 0 0; font-size: 0.9rem; color: #64748b;">' + error.message + '</p>');
                status.fadeIn();
            });
    });

    // 🚀 One-Click Install Logic
    $(document).on('click', '#tokoku-install-update', function() {
        var btn = $(this);
        var zipUrl = btn.data('url');
        var loader = $('#tokoku-install-loader');
        
        if (!confirm('Apakah Anda yakin ingin memperbarui tema sekarang? Website Anda tidak dapat diakses selama beberapa detik saat proses instalasi.')) {
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
                loader.html('<span class="dashicons dashicons-yes-alt" style="color: #059669;"></span> <span style="color: #059669;">Berhasil! Memuat ulang halaman...</span>');
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                alert('Gagal memperbarui: ' + response.data);
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
