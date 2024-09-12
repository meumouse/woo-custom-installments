/**
 * Replace range price
 * 
 * @since 2.8.0
 * @version 5.1.2
 * @package MeuMouse.com
 */
jQuery(document).ready( function($) {
    var product_price_container = $('#woo-custom-installments-product-price');
    var siblings_price = product_price_container.siblings('.woo-custom-installments-group.variable-range-price');
    var original_price = get_original_price();
    var get_quantity = $('input[name="quantity"]').val();

    /**
     * Get original price container
     * 
     * @since 2.8.0
     * @version 5.1.0
     * @returns string
     */
    function get_original_price() {
        var container_price = product_price_container;

        if ( product_price_container && product_price_container.html() && product_price_container.html().trim() === '' ) {
            container_price = siblings_price;
        }

        return container_price.html();
    }

    /**
     * On found or show product variation event
     * 
     * @since 2.8.0
     * @version 5.1.2
     * @param {object} e | Event type
     * @param {object} variation | Product variation object
     */
    $(document.body).on('found_variation show_variation', function(e, variation) {
        if (variation && variation.variation_id) {
            if (wci_range_params.update_method === 'ajax') {
                update_price_via_ajax(variation.display_price, get_quantity, variation.variation_id);
            } else {
                update_price_html(variation.price_html);
            }
        }
    });

    // clear variations
    $(document.body).on('click', 'a.reset_variation, a.reset_variations', function(e) {
        e.preventDefault();

        product_price_container.removeClass('active').addClass('d-none').html('');
        siblings_price.html(original_price).removeClass('d-none');
    });

    // on change quantity
    $(document.body).on('change', 'input[name="quantity"]', function() {
        $('form.variations_form').on('found_variation show_variation', function(e, variation) {
            if (variation && variation.variation_id) {
                if (wci_range_params.update_method === 'ajax') {
                    update_price_via_ajax(variation.display_price, get_quantity, variation.variation_id);
                }
            }
        });

        prevent_duplicate_prices();
    });

    /**
     * Get price HTML in AJAX
     * 
     * @since 4.5.1
     * @version 5.1.0
     * @param {float} price | Product price
     * @param {int} quantity | Product quantity
     * @param {int} product_id | Product ID or variation ID
     */
    function update_price_via_ajax(price, quantity, product_id) {
        product_price_container.addClass('wci-loading-price');
        siblings_price.addClass('wci-loading-price');

        $.ajax({
            url: wci_range_params.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'get_updated_price_html',
                price: price,
                quantity: quantity,
                product_id: product_id,
            },
            success: function(response) {
                if (response.success) {
                    update_price_html_ajax(response.data.price_html);
                } else {
                    update_price_html_ajax(original_price);
                }

                product_price_container.removeClass('wci-loading-price');
                siblings_price.removeClass('wci-loading-price');
            },
            error: function() {
                update_price_html_ajax(original_price);
                product_price_container.removeClass('wci-loading-price');
                siblings_price.removeClass('wci-loading-price');
                product_price_container.removeClass('active').html('');

                prevent_duplicate_prices();
            }
        });
    }

    // get triggers list from backend
    var triggers = wci_range_params.element_triggers.split(',');

    // Iterate over each trigger and associate a click event
    triggers.forEach( function(trigger) {
        $(trigger.trim()).on('click touchstart', function() {
            $('form.variations_form').on('found_variation show_variation', function(e, variation) {
                if (variation && variation.variation_id) {
                    if (wci_range_params.update_method === 'ajax') {
                        update_price_via_ajax(variation.display_price, get_quantity, variation.variation_id);
                    } else {
                        update_price_html(variation.price_html);
                    }
                }
            });
        });
    });

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

        prevent_duplicate_prices();
    }

    /**
     * Update variation price with selected variation on AJAX response
     * 
     * @since 2.8.0
     * @version 5.1.0
     * @param {string} price_html | Product price HTML
     */
    function update_price_html_ajax(price_html) {
        product_price_container.html('');
        product_price_container.html(price_html).removeClass('d-none').addClass('active');
        prevent_duplicate_prices();
    }
    
    /**
     * Check if main price container has content and prevent duplicate info
     * 
     * @since 5.1.0
     */
    function prevent_duplicate_prices() {
        if (product_price_container && product_price_container.hasClass('active') && product_price_container.html().trim() !== '' ) {
            siblings_price.addClass('d-none');
        } else {
            product_price_container.removeClass('active');
            siblings_price.removeClass('d-none');
        }
    }
});