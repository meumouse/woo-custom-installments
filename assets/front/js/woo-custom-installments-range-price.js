/**
 * Replace range price
 * 
 * @since 2.8.0
 * @version 4.5.0
 */
jQuery(document).ready( function($) {
    var product_price_container = $('#woo-custom-installments-product-price');
    var siblings_price = product_price_container.siblings('.woo-custom-installments-group.variable-range-price');
    var original_price = get_original_price();

    /**
     * Get original price
     * 
     * @since 2.8.0
     * @version 4.5.0
     * @returns string
     */
    function get_original_price() {
        var container_price = product_price_container.closest('.woo-custom-installments-group');
        container_price.closest('div').addClass('range-price');

        return container_price.html();
    }

    // on load page
    update_price_html(original_price);

    /**
     * When a variation is found
     */
    $(document).on('found_variation', 'form.variations_form', function(e, variation) {
        update_price_html(variation.price_html);
    });

    /**
     * When a variation is selected
     */
    $('form.variations_form').on('show_variation', function(e, variation) {
        update_price_html(variation.price_html);
    });

    /**
     * On change variation
     */
    $('form.variations_form').on('change', 'select', function() {
        if ($(this).val() === '') {
            update_price_html(original_price);
        }
    });

    /**
     * On reset variations
     */
    $('a.reset_variations').click( function(e) {
        e.preventDefault();

        update_price_html(original_price);
    });

    /**
     * Update product price for selected variation
     * 
     * @since 2.8.0
     * @version 4.5.0
     * @param {string} price_html | Product price
     */
    function update_price_html(price_html) {
        // Remove any existing nested .price elements
        product_price_container.find('.price').children().unwrap();
        
        if ( ! product_price_container.find('.woo-custom-installments-group').length ) {
            product_price_container.html(price_html);
        } else {
            product_price_container.find('.woo-custom-installments-group').replaceWith(price_html);
        }

        if ( product_price_container.find('.woo-custom-installments-group').length ) {
            product_price_container.addClass('active');
        } else {
            product_price_container.html(siblings_price);
        }
    }
});