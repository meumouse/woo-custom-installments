var replace_range_price = ( function($) {
    var product_price_container;
    var siblings_price;
    var original_price;
    var get_quantity;

    return {
        /**
         * Initialize the module
         * 
         * @since 2.8.0
         */
        init: function () {
            product_price_container = $('#woo-custom-installments-product-price');
            siblings_price = product_price_container.siblings('.woo-custom-installments-group');
            original_price = this.get_original_price();
            get_quantity = $('input[name="quantity"]').val();

            this.bind_events();
        },

        /**
         * Bind events to elements
         * 
         * @since 2.8.0
         */
        bind_events: function () {
            $(document).on('change', 'input[name="variation_id"]', this.on_variation_change.bind(this));
            $(document).on('click', 'a.reset_variation, a.reset_variations', this.on_clear_variations.bind(this));
            $(document).on('change', 'input[name="quantity"]', this.on_quantity_change.bind(this));

            // Iterate over configured triggers
            var triggers = (wci_range_params.element_triggers || '').split(',');

            triggers.forEach( function(trigger) {
                $(trigger.trim()).on('click touchstart change', this.on_trigger_event.bind(this));
            }.bind(this));
        },

        /**
         * Get the original price container
         * 
         * @since 2.8.0
         * @returns {string} Original price
         */
        get_original_price: function () {
            var container_price = product_price_container;

            if (product_price_container && product_price_container.html() && product_price_container.html().trim() === '') {
                container_price = siblings_price;
            }

            return container_price ? container_price.html() : '';
        },

        /**
         * Handler for variation change
         * 
         * @since 2.8.0
         * @param {object} event Event object
         */
        on_variation_change: function (event) {
            var variation_id = $(event.target).val();

            if (wci_range_params.debug_mode) {
                console.log('Variation ID:', variation_id);
            }

            if (wci_range_params.update_method === 'ajax') {
                this.update_price_via_ajax(null, get_quantity, variation_id);
            } else {
                this.update_price_html(variation_id);
            }
        },

        /**
         * Handler for clearing variations
         * 
         * @since 2.8.0
         * @param {object} event Event object
         */
        on_clear_variations: function (event) {
            event.preventDefault();

            product_price_container.removeClass('active').addClass('d-none').html('');
            siblings_price.html(original_price).removeClass('d-none');
        },

        /**
         * Handler for quantity change
         * 
         * @since 2.8.0
         */
        on_quantity_change: function () {
            get_quantity = $('input[name="quantity"]').val() || 1;

            $('form.variations_form').on('found_variation show_variation', this.handle_variation_event.bind(this));

            this.prevent_duplicate_prices();
        },

        /**
         * Handler for trigger events
         * 
         * @since 2.8.0
         */
        on_trigger_event: function () {
            $('form.variations_form').on('found_variation show_variation', this.handle_variation_event.bind(this));
        },

        /**
         * Handler for variation events
         * 
         * @since 2.8.0
         * @param {object} e | Event object
         * @param {object} variation | Variation object
         */
        handle_variation_event: function (e, variation) {
            if (variation && variation.variation_id) {
                if (wci_range_params.update_method === 'ajax') {
                    this.update_price_via_ajax(variation.display_price, get_quantity, variation.variation_id);
                }
            }
        },

        /**
         * Update the price via AJAX
         * 
         * @since 2.8.0
         * @param {float} price Product price
         * @param {int} quantity Product quantity
         * @param {int} product_id Product or variation ID
         */
        update_price_via_ajax: function (price, quantity, product_id) {
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
                success: function (response) {
                    if (response.success) {
                        replace_range_price.update_price_html_ajax(response.data.price_html);
                    } else {
                        replace_range_price.update_price_html_ajax(original_price);
                    }

                    product_price_container.removeClass('wci-loading-price');
                    siblings_price.removeClass('wci-loading-price');
                },
                error: function () {
                    replace_range_price.update_price_html_ajax(original_price);
                    product_price_container.removeClass('wci-loading-price');
                    siblings_price.removeClass('wci-loading-price');
                    product_price_container.removeClass('active').html('');
                    replace_range_price.prevent_duplicate_prices();
                },
            });
        },

        /**
         * Update the price HTML for a selected variation
         * 
         * @since 2.8.0
         * @param {int} variation_id Variation ID
         */
        update_price_html: function (variation_id) {
            if (wci_range_params.update_method !== 'dynamic') return;

            var price_html = $(`#wci-variation-prices .wci-variation-item[data-variation-id="${variation_id}"]`).html();
            product_price_container.html(price_html).addClass('active').removeClass('d-none');

            this.prevent_duplicate_prices();
        },

        /**
         * Update the price HTML via AJAX response
         * 
         * @since 2.8.0
         * @param {string} price_html Price HTML
         */
        update_price_html_ajax: function (price_html) {
            product_price_container.html(price_html).removeClass('d-none').addClass('active');
        },

        /**
         * Prevent duplication of prices
         * 
         * @since 2.8.0
         */
        prevent_duplicate_prices: function () {
            if (product_price_container && product_price_container.hasClass('active') && product_price_container.html().trim() !== '') {
                siblings_price.addClass('d-none');
            } else {
                product_price_container.removeClass('active');
                siblings_price.removeClass('d-none');
            }
        },
    };
})(jQuery);

// Initialize the module after the page is loaded
jQuery(document).ready( function() {
    replace_range_price.init();
});