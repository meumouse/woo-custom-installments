/**
 * Replace range price
 * 
 * @since 2.8.0
 * @version 4.5.1
 * @package MeuMouse.com
 */
jQuery(document).ready( function($) {
    var product_price_container = $('#woo-custom-installments-product-price');
    var siblings_price = product_price_container.siblings('.woo-custom-installments-group.variable-range-price');
    var original_price = get_original_price();

    function get_original_price() {
        return product_price_container.html();
    }

    // on load page
    update_price_html(original_price);

    // when a variation is finded
    $(document).on('found_variation', 'form.variations_form', function(e, variation) {
        if (variation.variation_id) {
            update_price_via_ajax(variation.variation_id);
        }
    });

    // when a variation is selected
    $('form.variations_form').on('show_variation', function(e, variation) {
        if (variation.variation_id) {
            update_price_via_ajax(variation.variation_id);
        }
    });

    $('form.variations_form').on('change', 'select', function() {
        if ($(this).val() === '') {
            update_price_html(original_price);
        }
    });

    $('a.reset_variations').click( function(e) {
        e.preventDefault();
        
        product_price_container.addClass('d-none');
        siblings_price.removeClass('d-none');
    });

    /**
     * Get price HTML in AJAX
     * 
     * @since 4.5.1
     * @param {int} variation_id | Product variation ID
     */
    function update_price_via_ajax(variation_id) {
        product_price_container.addClass('wci-loading-price');
        siblings_price.addClass('wci-loading-price');

        $.ajax({
            url: wci_range_params.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_updated_price_html',
                product_id: variation_id,
            },
            success: function(response) {
                if (response.success) {
                    update_price_html(response.data.price_html);
                    product_price_container.removeClass('wci-loading-price');
                    siblings_price.removeClass('wci-loading-price');
                    siblings_price.addClass('d-none');
                }
            },
            error: function() {
                update_price_html(original_price);
                product_price_container.removeClass('wci-loading-price');
                siblings_price.removeClass('wci-loading-price');
            }
        });
    }

    /**
     * Update variation price with selected variation 
     * 
     * @since 2.8.0
     * @version 4.5.1
     * @param {string} price_html | Product price HTML
     */
    function update_price_html(price_html) {
        product_price_container.html('');
        product_price_container.html(price_html).removeClass('d-none').addClass('active');
    }
});