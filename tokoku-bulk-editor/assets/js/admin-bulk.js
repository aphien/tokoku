/**
 * TokoKu Bulk Product Editor Dashboard Interactive Controller
 * Handles live filtering, selection, tabs, inline quick edits, and asynchronous batch updates.
 */
(function($) {
    'use strict';

    var BulkEditor = {
        currentPage: 1,
        perPage: 30,
        selectedIds: [],
        isProcessing: false,
        batchQueue: [],
        totalToProcess: 0,
        processedCount: 0,
        currentBatchIndex: 0,

        init: function() {
            this.bindEvents();
            this.loadProducts(1);
        },

        bindEvents: function() {
            var self = this;

            // Tab Navigation
            $('.tokoku-action-tab').on('click', function() {
                var targetTab = $(this).data('tab');
                $('.tokoku-action-tab').removeClass('active');
                $(this).addClass('active');

                $('.tokoku-tab-pane').removeClass('active');
                $('#' + targetTab).addClass('active');
            });

            // Dynamic Inputs Toggle inside Tabs
            $('#bulk-price-mode').on('change', function() {
                var mode = $(this).val();
                if (mode === 'no_change') {
                    $('#bulk-price-val').hide().val('');
                } else {
                    $('#bulk-price-val').show();
                    if (mode.indexOf('percent') !== -1) {
                        $('#bulk-price-val').attr('placeholder', 'Persen % (e.g. 10)');
                    } else {
                        $('#bulk-price-val').attr('placeholder', 'Nominal Rp');
                    }
                }
            });

            $('#bulk-sale-mode').on('change', function() {
                var mode = $(this).val();
                if (mode === 'no_change' || mode === 'remove') {
                    $('#bulk-sale-val').hide().val('');
                } else {
                    $('#bulk-sale-val').show();
                    if (mode === 'percent_of_regular') {
                        $('#bulk-sale-val').attr('placeholder', 'Diskon % (e.g. 20)');
                    } else {
                        $('#bulk-sale-val').attr('placeholder', 'Harga Diskon (Rp)');
                    }
                }
            });

            $('#bulk-stock-mode').on('change', function() {
                var mode = $(this).val();
                if (mode === 'no_change' || mode === 'empty') {
                    $('#bulk-stock-val').hide();
                } else {
                    $('#bulk-stock-val').show();
                }
            });

            $('#bulk-category-mode').on('change', function() {
                var mode = $(this).val();
                if (mode === 'no_change') {
                    $('#bulk-category-ids').hide();
                } else {
                    $('#bulk-category-ids').show();
                }
            });

            $('#bulk-label-mode').on('change', function() {
                var mode = $(this).val();
                if (mode === 'set') {
                    $('#bulk-label-text').show();
                } else {
                    $('#bulk-label-text').hide().val('');
                }
            });

            $('#bulk-berat-mode').on('change', function() {
                var mode = $(this).val();
                if (mode === 'set') {
                    $('#bulk-berat-val').show();
                } else {
                    $('#bulk-berat-val').hide().val('');
                }
            });

            // Filter Events
            $('#filter-category, #filter-stock-status, #filter-discount-status, #filter-post-status, #filter-orderby, #filter-per-page').on('change', function() {
                self.loadProducts(1);
            });

            var searchTimer = null;
            $('#filter-search').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    self.loadProducts(1);
                }, 400);
            });

            $('#btn-reset-filters').on('click', function() {
                $('#filter-search').val('');
                $('#filter-category').val('all');
                $('#filter-stock-status').val('all');
                $('#filter-discount-status').val('all');
                $('#filter-post-status').val('all');
                $('#filter-orderby').val('date_desc');
                $('#filter-per-page').val('30');
                self.loadProducts(1);
            });

            // Selection Events
            $('#cb-select-all-1, #btn-select-all-page').on('click', function() {
                var isChecked = $(this).is(':checked') || $(this).attr('id') === 'btn-select-all-page';
                $('.tokoku-row-cb').prop('checked', isChecked);
                $('#cb-select-all-1').prop('checked', isChecked);
                self.updateSelectedCount();
            });

            $('#btn-deselect-all').on('click', function() {
                $('.tokoku-row-cb').prop('checked', false);
                $('#cb-select-all-1').prop('checked', false);
                self.selectedIds = [];
                self.updateSelectedCount();
            });

            $(document).on('change', '.tokoku-row-cb', function() {
                self.updateSelectedCount();
            });

            // Apply Bulk Update Button
            $('#btn-apply-bulk').on('click', function() {
                self.initiateBulkUpdate();
            });

            // Close Modal & Reload
            $('#btn-close-modal').on('click', function() {
                $('#bulk-progress-modal').fadeOut(200);
                self.loadProducts(self.currentPage);
            });

            // Inline Row Save Button
            $(document).on('click', '.tokoku-btn-save-inline', function() {
                var $btn = $(this);
                var $row = $btn.closest('tr');
                self.saveInlineRow($row, $btn);
            });

            // Inline Enter key in price / qty input
            $(document).on('keypress', '.tokoku-inline-price-input, .tokoku-inline-qty-input', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    var $row = $(this).closest('tr');
                    $row.find('.tokoku-btn-save-inline').trigger('click');
                }
            });
        },

        updateSelectedCount: function() {
            var selected = [];
            $('.tokoku-row-cb:checked').each(function() {
                selected.push(parseInt($(this).val(), 10));
            });

            this.selectedIds = selected;
            var count = selected.length;

            $('#selected-counter-text').text(count + ' produk dipilih');

            if (count > 0) {
                $('#btn-deselect-all').show();
                $('.tokoku-selection-badge').css('background', '#dbeafe');
            } else {
                $('#btn-deselect-all').hide();
                $('.tokoku-selection-badge').css('background', '#eff6ff');
            }
        },

        loadProducts: function(page) {
            var self = this;
            this.currentPage = page || 1;
            this.perPage = parseInt($('#filter-per-page').val(), 10) || 30;

            $('#table-overlay').css('display', 'flex');

            var postData = {
                action: 'tokoku_bulk_get_products',
                nonce: tokokuBulkData.nonce,
                paged: this.currentPage,
                per_page: this.perPage,
                search: $('#filter-search').val(),
                category: $('#filter-category').val(),
                stock_status: $('#filter-stock-status').val(),
                discount_status: $('#filter-discount-status').val(),
                post_status: $('#filter-post-status').val(),
                orderby: $('#filter-orderby').val()
            };

            $.post(tokokuBulkData.ajax_url, postData, function(response) {
                $('#table-overlay').hide();

                if (!response.success) {
                    alert(response.data && response.data.message ? response.data.message : tokokuBulkData.i18n.error);
                    return;
                }

                var data = response.data;
                $('#cb-select-all-1').prop('checked', false);
                self.renderStats(data.stats);
                self.renderTable(data.products);
                self.renderPagination(data.total, data.total_pages, data.current_page);
                self.updateSelectedCount();
            }).fail(function() {
                $('#table-overlay').hide();
                alert(tokokuBulkData.i18n.error);
            });
        },

        renderStats: function(stats) {
            if (!stats) return;
            $('#stat-total').text(stats.total);
            $('#stat-tersedia').text(stats.tersedia);
            $('#stat-habis').text(stats.habis);
            $('#stat-discount').text(stats.discounted);
        },

        renderTable: function(products) {
            var $tbody = $('#tokoku-product-rows');
            $tbody.empty();

            if (!products || !products.length) {
                $tbody.append(
                    '<tr><td colspan="9" style="text-align:center; padding: 40px 20px; color: #64748b;">' +
                    '<span class="dashicons dashicons-warning" style="font-size:32px; width:32px; height:32px; margin-bottom:8px; display:inline-block;"></span><br>' +
                    tokokuBulkData.i18n.no_products +
                    '</td></tr>'
                );
                return;
            }

            $.each(products, function(i, item) {
                var thumbHtml = item.thumb
                    ? '<img src="' + item.thumb + '" alt="" class="tokoku-thumb-img">'
                    : '<div class="tokoku-thumb-placeholder"><span class="dashicons dashicons-format-image"></span></div>';

                var promoBadgeHtml = item.label_khusus ? '<span class="tokoku-promo-badge">' + item.label_khusus + '</span>' : '';
                var postStatusBadgeHtml = item.post_status !== 'publish' ? '<span class="tokoku-post-status-badge tokoku-post-status-badge--' + item.post_status + '">' + item.post_status + '</span>' : '';

                var rowHtml = '<tr id="product-row-' + item.id + '" data-id="' + item.id + '">' +
                    '<th scope="row" class="check-column">' +
                        '<input type="checkbox" class="tokoku-row-cb" value="' + item.id + '">' +
                    '</th>' +
                    '<td class="column-thumb">' + thumbHtml + '</td>' +
                    '<td class="column-title">' +
                        '<div class="tokoku-product-info">' +
                            '<div><a href="' + item.edit_url + '" target="_blank" class="tokoku-product-title">' + item.title + '</a> ' + promoBadgeHtml + ' ' + postStatusBadgeHtml + '</div>' +
                            '<div class="tokoku-product-meta">' +
                                '<span class="tokoku-sku-tag">SKU: ' + item.sku + '</span>' +
                                (item.berat ? '<span>' + item.berat + 'g</span>' : '') +
                            '</div>' +
                            '<div class="tokoku-row-actions">' +
                                '<a href="' + item.edit_url + '" target="_blank">Edit Produk</a> | ' +
                                '<a href="' + item.view_url + '" target="_blank">Lihat</a>' +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                    '<td class="column-category">' + item.categories + '</td>' +
                    '<td class="column-price">' +
                        '<div class="tokoku-inline-input-wrapper">' +
                            '<span class="prefix">Rp</span>' +
                            '<input type="text" class="tokoku-inline-price-input" data-field="price" value="' + (item.raw_price || '') + '" placeholder="0">' +
                        '</div>' +
                    '</td>' +
                    '<td class="column-sale-price">' +
                        '<div class="tokoku-inline-input-wrapper">' +
                            '<span class="prefix">Rp</span>' +
                            '<input type="text" class="tokoku-inline-price-input" data-field="sale_price" value="' + (item.raw_sale_price || '') + '" placeholder="Kosong">' +
                        '</div>' +
                    '</td>' +
                    '<td class="column-status">' +
                        '<select class="tokoku-inline-status-select" data-id="' + item.id + '">' +
                            '<option value="tersedia"' + (item.status === 'tersedia' ? ' selected' : '') + '>🟢 Tersedia</option>' +
                            '<option value="habis"' + (item.status === 'habis' ? ' selected' : '') + '>🔴 Habis</option>' +
                            '<option value="preorder"' + (item.status === 'preorder' ? ' selected' : '') + '>🟡 Pre-Order</option>' +
                        '</select>' +
                    '</td>' +
                    '<td class="column-qty">' +
                        '<input type="number" min="0" class="tokoku-inline-qty-input" value="' + (item.qty !== '' && item.qty !== null ? item.qty : '') + '" placeholder="∞">' +
                    '</td>' +
                    '<td class="column-action" style="text-align:center;">' +
                        '<button type="button" class="tokoku-btn-save-inline" data-id="' + item.id + '" title="Simpan Baris Ini">' +
                            '<span class="dashicons dashicons-yes"></span>' +
                        '</button>' +
                    '</td>' +
                '</tr>';

                $tbody.append(rowHtml);
            });
        },

        renderPagination: function(total, totalPages, currentPage) {
            var $info = $('#pagination-info');
            var $controls = $('#pagination-controls');

            $info.empty();
            $controls.empty();

            if (total === 0) return;

            var from = ((currentPage - 1) * this.perPage) + 1;
            var to   = Math.min(currentPage * this.perPage, total);
            $info.text('Menampilkan ' + from + ' - ' + to + ' dari ' + total + ' produk');

            if (totalPages <= 1) return;

            var self = this;

            var $prevBtn = $('<button type="button" class="tokoku-page-btn">&laquo;</button>');
            if (currentPage <= 1) {
                $prevBtn.prop('disabled', true);
            } else {
                $prevBtn.on('click', function() { self.loadProducts(currentPage - 1); });
            }
            $controls.append($prevBtn);

            var startPage = Math.max(1, currentPage - 2);
            var endPage   = Math.min(totalPages, currentPage + 2);

            for (var p = startPage; p <= endPage; p++) {
                (function(pageNumber) {
                    var $pageBtn = $('<button type="button" class="tokoku-page-btn' + (pageNumber === currentPage ? ' active' : '') + '">' + pageNumber + '</button>');
                    $pageBtn.on('click', function() { self.loadProducts(pageNumber); });
                    $controls.append($pageBtn);
                })(p);
            }

            var $nextBtn = $('<button type="button" class="tokoku-page-btn">&raquo;</button>');
            if (currentPage >= totalPages) {
                $nextBtn.prop('disabled', true);
            } else {
                $nextBtn.on('click', function() { self.loadProducts(currentPage + 1); });
            }
            $controls.append($nextBtn);
        },

        saveInlineRow: function($row, $btn) {
            var self = this;
            var postId = $row.data('id');
            var price = $row.find('input[data-field="price"]').val();
            var salePrice = $row.find('input[data-field="sale_price"]').val();
            var status = $row.find('.tokoku-inline-status-select').val();
            var qty    = $row.find('.tokoku-inline-qty-input').val();

            $btn.prop('disabled', true).html('<span class="spinner is-active" style="float:none; margin:0;"></span>');

            var postData = {
                action: 'tokoku_bulk_inline_update',
                nonce: tokokuBulkData.nonce,
                post_id: postId,
                price: price,
                sale_price: salePrice,
                stok_status: status,
                stok_qty: qty
            };

            $.post(tokokuBulkData.ajax_url, postData, function(response) {
                $btn.prop('disabled', false);
                if (response.success) {
                    $btn.addClass('saved').html('<span class="dashicons dashicons-yes"></span>');
                    setTimeout(function() {
                        $btn.removeClass('saved');
                    }, 1500);

                    if (response.data && response.data.stats) {
                        self.renderStats(response.data.stats);
                    }
                } else {
                    $btn.html('<span class="dashicons dashicons-no"></span>');
                    alert(response.data && response.data.message ? response.data.message : tokokuBulkData.i18n.error);
                }
            }).fail(function() {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-no"></span>');
                alert(tokokuBulkData.i18n.error);
            });
        },

        initiateBulkUpdate: function() {
            var self = this;

            if (!this.selectedIds || !this.selectedIds.length) {
                alert(tokokuBulkData.i18n.select_prompt);
                return;
            }

            // Gather inputs
            var priceMode   = $('#bulk-price-mode').val();
            var priceVal    = $('#bulk-price-val').val();
            var saleMode    = $('#bulk-sale-mode').val();
            var saleVal     = $('#bulk-sale-val').val();
            var stockStatus = $('#bulk-stock-status').val();
            var stockMode   = $('#bulk-stock-mode').val();
            var stockVal    = $('#bulk-stock-val').val();
            var autoSync    = $('#bulk-auto-sync').is(':checked') ? 1 : 0;
            var catMode     = $('#bulk-category-mode').val();
            var catId       = $('#bulk-category-ids').val();
            var labelMode   = $('#bulk-label-mode').val();
            var labelText   = $('#bulk-label-text').val();
            var beratMode   = $('#bulk-berat-mode').val();
            var beratVal    = $('#bulk-berat-val').val();
            var postStatus  = $('#bulk-post-status').val();

            // Validate that at least one action is chosen
            var hasAction = (priceMode !== 'no_change') ||
                            (saleMode !== 'no_change') ||
                            (stockStatus !== 'no_change') ||
                            (stockMode !== 'no_change') ||
                            (catMode !== 'no_change' && catId) ||
                            (labelMode !== 'no_change') ||
                            (beratMode !== 'no_change') ||
                            (postStatus !== 'no_change');

            if (!hasAction) {
                alert(tokokuBulkData.i18n.action_prompt);
                return;
            }

            var confirmMsg = tokokuBulkData.i18n.confirm_msg.replace('%d', this.selectedIds.length);
            if (!confirm(confirmMsg)) {
                return;
            }

            // Split into batches of 25 products
            var batchSize = 25;
            this.batchQueue = [];
            for (var i = 0; i < this.selectedIds.length; i += batchSize) {
                this.batchQueue.push(this.selectedIds.slice(i, i + batchSize));
            }

            this.totalToProcess    = this.selectedIds.length;
            this.processedCount    = 0;
            this.currentBatchIndex = 0;
            this.isProcessing      = true;

            // Open Progress Modal
            $('#modal-title').text(tokokuBulkData.i18n.processing_batch.replace('%d', 1).replace('%d', this.batchQueue.length).replace('%s', '0'));
            $('#progress-bar-fill').css('width', '0%');
            $('#progress-percent').text('0%');
            $('#progress-text').text('Menyiapkan pembaruan...');
            $('#progress-log').empty();
            $('#btn-close-modal').hide();
            $('#bulk-progress-modal').fadeIn(200);

            this.processNextBatch({
                price_mode: priceMode,
                price_val: priceVal,
                sale_mode: saleMode,
                sale_val: saleVal,
                stok_status: stockStatus,
                stok_mode: stockMode,
                stok_value: stockVal,
                auto_sync: autoSync,
                category_mode: catMode,
                category_ids: catId ? [catId] : [],
                label_mode: labelMode,
                label_text: labelText,
                berat_mode: beratMode,
                berat_val: beratVal,
                post_status: postStatus
            });
        },

        processNextBatch: function(payload) {
            var self = this;

            if (this.currentBatchIndex >= this.batchQueue.length) {
                // Done!
                this.isProcessing = false;
                $('#progress-bar-fill').css('width', '100%');
                $('#progress-percent').text('100%');
                $('#progress-text').text(tokokuBulkData.i18n.success_msg.replace('%d', self.processedCount));
                $('#modal-title').text(tokokuBulkData.i18n.success_title);
                $('#btn-close-modal').show();
                return;
            }

            var batchIds = this.batchQueue[this.currentBatchIndex];
            var batchNum = this.currentBatchIndex + 1;
            var totalBatches = this.batchQueue.length;

            var postData = $.extend({}, payload, {
                action: 'tokoku_bulk_update_batch',
                nonce: tokokuBulkData.nonce,
                post_ids: batchIds
            });

            $.post(tokokuBulkData.ajax_url, postData, function(response) {
                if (response.success) {
                    var data = response.data;
                    self.processedCount += data.updated_count;

                    if (data.updated_items && data.updated_items.length) {
                        $.each(data.updated_items, function(k, item) {
                            var logMsg = '✓ #' + item.id + ' ' + item.title + (item.changes && item.changes.length ? ' (' + item.changes.join(', ') + ')' : '');
                            $('#progress-log').prepend('<div style="color:#059669;">' + logMsg + '</div>');
                        });
                    }

                    if (data.stats) {
                        self.renderStats(data.stats);
                    }

                    var percent = Math.min(100, Math.round((self.processedCount / self.totalToProcess) * 100));
                    $('#progress-bar-fill').css('width', percent + '%');
                    $('#progress-percent').text(percent + '%');
                    $('#progress-text').text(tokokuBulkData.i18n.processing_batch.replace('%d', batchNum).replace('%d', totalBatches).replace('%s', percent));

                    self.currentBatchIndex++;
                    self.processNextBatch(payload);
                } else {
                    $('#progress-log').prepend('<div style="color:#dc2626;">✗ Gagal memproses batch ' + batchNum + ': ' + (response.data ? response.data.message : 'Error') + '</div>');
                    self.currentBatchIndex++;
                    self.processNextBatch(payload);
                }
            }).fail(function() {
                $('#progress-log').prepend('<div style="color:#dc2626;">✗ Gagal koneksi pada batch ' + batchNum + '</div>');
                self.currentBatchIndex++;
                self.processNextBatch(payload);
            });
        }
    };

    $(document).ready(function() {
        BulkEditor.init();
    });

})(jQuery);
