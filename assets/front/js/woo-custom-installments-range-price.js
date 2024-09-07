/**
 * Replace range price
 * 
 * @since 2.8.0
 * @version 5.0.0
 * @package MeuMouse.com
 */
jQuery(document).ready( function($) {
    var product_price_container = $('#woo-custom-installments-product-price');
    var siblings_price = product_price_container.siblings('.woo-custom-installments-group.variable-range-price');
    var original_price = get_original_price();

    /**
     * Get original price container
     * 
     * @since 2.8.0
     * @version 5.0.0
     * @returns string
     */
    function get_original_price() {
        var container_price = product_price_container;

        if ( product_price_container.html().trim() === '' ) {
            container_price = siblings_price;
        }

        return container_price.html();
    }

    // update price on load page
    update_price_html(original_price);

    // Variation events found or selected
    $('form.variations_form').on('found_variation show_variation', function (e, variation) {
        if (variation && variation.variation_id) {
            if (wci_range_params.update_method === 'ajax') {
                update_price_via_ajax(variation.variation_id);
            } else {
                update_price_html(variation.price_html);
            }
        }
    });

    // When changing the variation, if no value is selected, restore the original price
    $('form.variations_form').on('change', 'select', function () {
        if ($(this).val() === '') {
            update_price_html(original_price);
        }
    });

    // clear variations
    $('a.reset_variations').click( function(e) {
        e.preventDefault();
        product_price_container.removeClass('active').html(original_price);
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
            success: function (response) {
                if (response.success) {
                    update_price_html(response.data.price_html);
                    siblings_price.addClass('d-none');
                } else {
                    update_price_html(original_price);
                }

                product_price_container.removeClass('wci-loading-price');
                siblings_price.removeClass('wci-loading-price');
            },
            error: function () {
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
     * @version 5.0.0
     * @param {string} price_html | Product price HTML
     */
    function update_price_html(price_html) {
        if (wci_range_params.update_method !== 'dynamic') {
            return;
        }

        product_price_container.html(price_html).removeClass('d-none').addClass('active');

        if ( product_price_container.hasClass('active') && product_price_container.html().trim() !== '' ) {
            siblings_price.addClass('d-none');
        }
    }

    // Check for duplicates and prevent multiple displays
    var wci_siblings = $('.range-price .price').siblings('.woo-custom-installments-group');

    if (wci_siblings.length > 0) {
        wci_siblings.addClass('d-none');
    }

    var triggers = wci_range_params.element_triggers.split(',');

    // Iterate over each trigger and associate a click event
    triggers.forEach( function(trigger) {
        $(trigger.trim()).on('click', function () {
            update_price_html(original_price);
        });
    });
});