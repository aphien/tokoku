/**
 * TokoKu Native Quick Edit & Bulk Edit JavaScript Helper
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Quick Edit Pre-population
        if (typeof inlineEditPost !== 'undefined') {
            var originalEdit = inlineEditPost.edit;

            inlineEditPost.edit = function(id) {
                // Call original WordPress function
                originalEdit.apply(this, arguments);

                var postId = 0;
                if (typeof id === 'object') {
                    postId = parseInt(this.getId(id), 10);
                } else {
                    postId = parseInt(id, 10);
                }

                if (postId > 0) {
                    var $editRow   = $('#edit-' + postId);
                    var $postRow   = $('#post-' + postId);
                    var $priceCell = $postRow.find('.tokoku-price-cell');
                    var $stockCell = $postRow.find('.tokoku-stock-cell');

                    // Prepopulate Price & Sale Price
                    if ($priceCell.length) {
                        var price     = $priceCell.data('price');
                        var salePrice = $priceCell.data('sale-price');

                        $editRow.find('input[name="tokoku_quick_harga"]').val(price !== undefined && price !== null ? price : '');
                        $editRow.find('input[name="tokoku_quick_harga_diskon"]').val(salePrice !== undefined && salePrice !== null ? salePrice : '');
                    }

                    // Prepopulate Stock Status & Quantity
                    if ($stockCell.length) {
                        var status = $stockCell.data('status') || 'tersedia';
                        var qty    = $stockCell.data('qty');

                        $editRow.find('select[name="tokoku_quick_stok_status"]').val(status);
                        $editRow.find('input[name="tokoku_quick_jumlah_stok"]').val(qty !== undefined && qty !== null ? qty : '');
                    }
                }
            };
        }

        // Toggle Bulk Edit Price Values
        $(document).on('change', '#tokoku-bulk-price-mode-select', function() {
            var mode = $(this).val();
            var $wrapper = $('#tokoku-bulk-price-val-wrapper');
            if (mode === 'no_change') {
                $wrapper.hide().find('input').val('');
            } else {
                $wrapper.show();
            }
        });

        $(document).on('change', '#tokoku-bulk-sale-mode-select', function() {
            var mode = $(this).val();
            var $wrapper = $('#tokoku-bulk-sale-val-wrapper');
            if (mode === 'no_change' || mode === 'remove') {
                $wrapper.hide().find('input').val('');
            } else {
                $wrapper.show();
            }
        });

        // Toggle Quantity input in Bulk Edit
        $(document).on('change', '#tokoku-bulk-stok-mode-select', function() {
            var mode = $(this).val();
            var $wrapper = $('#tokoku-bulk-qty-wrapper');
            if (mode === 'no_change' || mode === 'empty') {
                $wrapper.hide().find('input').val('');
            } else {
                $wrapper.show();
            }
        });

        // Ensure Bulk Edit custom fields are sent when clicking Update in Bulk Edit
        $(document).on('click', '#bulk_edit input[type="submit"], #bulk_edit .save', function() {
            var $bulkRow    = $('#bulk-edit');
            var priceMode   = $bulkRow.find('select[name="tokoku_bulk_price_mode"]').val();
            var priceVal    = $bulkRow.find('input[name="tokoku_bulk_price_val"]').val();
            var saleMode    = $bulkRow.find('select[name="tokoku_bulk_sale_mode"]').val();
            var saleVal     = $bulkRow.find('input[name="tokoku_bulk_sale_val"]').val();
            var status      = $bulkRow.find('select[name="tokoku_bulk_stok_status"]').val();
            var mode        = $bulkRow.find('select[name="tokoku_bulk_stok_mode"]').val();
            var val         = $bulkRow.find('input[name="tokoku_bulk_stok_value"]').val();
            var nonce       = $bulkRow.find('input[name="tokoku_native_bulk_nonce"]').val();

            var $form = $(this).closest('form');
            if ($form.length) {
                if (!$form.find('input[name="tokoku_bulk_stok_status_hidden"]').length) {
                    $form.append('<input type="hidden" class="tokoku_bulk_stok_status_hidden" name="tokoku_bulk_stok_status" value="' + (status || 'no_change') + '">');
                    $form.append('<input type="hidden" name="tokoku_bulk_stok_mode" value="' + (mode || 'no_change') + '">');
                    $form.append('<input type="hidden" name="tokoku_bulk_stok_value" value="' + (val || '0') + '">');
                    $form.append('<input type="hidden" name="tokoku_bulk_price_mode" value="' + (priceMode || 'no_change') + '">');
                    $form.append('<input type="hidden" name="tokoku_bulk_price_val" value="' + (priceVal || '0') + '">');
                    $form.append('<input type="hidden" name="tokoku_bulk_sale_mode" value="' + (saleMode || 'no_change') + '">');
                    $form.append('<input type="hidden" name="tokoku_bulk_sale_val" value="' + (saleVal || '0') + '">');
                    $form.append('<input type="hidden" name="tokoku_native_bulk_nonce" value="' + (nonce || '') + '">');
                } else {
                    $form.find('input[name="tokoku_bulk_stok_status"]').val(status || 'no_change');
                    $form.find('input[name="tokoku_bulk_stok_mode"]').val(mode || 'no_change');
                    $form.find('input[name="tokoku_bulk_stok_value"]').val(val || '0');
                    $form.find('input[name="tokoku_bulk_price_mode"]').val(priceMode || 'no_change');
                    $form.find('input[name="tokoku_bulk_price_val"]').val(priceVal || '0');
                    $form.find('input[name="tokoku_bulk_sale_mode"]').val(saleMode || 'no_change');
                    $form.find('input[name="tokoku_bulk_sale_val"]').val(saleVal || '0');
                    $form.find('input[name="tokoku_native_bulk_nonce"]').val(nonce || '');
                }
            }
        });
    });

})(jQuery);
