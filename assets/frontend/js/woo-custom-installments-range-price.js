( function($) {
	"use strict";

    var product_price_container;
    var siblings_price;
    var original_price;
    var get_quantity;

    /**
     * Get range params
     * 
     * @since 5.4.0
     * @returns {object}
     */
    const params = window.wci_range_params || {};

    /**
     * Object variable for replace range price
     * 
     * @since 2.8.0
     * @version 5.4.0
     * @package MeuMouse.com
     */
    var Replace_Range_Price = {

        /**
         * Bind events to elements
         * 
         * @since 2.8.0
         * @version 5.4.0
         */
        bindEvents: function () {
            $(document).on('change', 'input[name="variation_id"]', this.onVariationChange.bind(this));
            $(document).on('click', 'a.reset_variation, a.reset_variations', this.onClearVariations.bind(this));
            $(document).on('change', 'input[name="quantity"]', this.onQuantityChanges.bind(this));

            // Iterate over configured triggers
            var triggers = (params.element_triggers || '').split(',');

            triggers.forEach( function(trigger) {
                $(trigger.trim()).on('click touchstart change', this.onTriggerEvent.bind(this));
            }.bind(this));
        },

        /**
         * Get the original price container
         * 
         * @since 2.8.0
         * @returns {string} Original price
         */
        getOriginalPrice: function () {
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
        onVariationChange: function (event) {
            var variation_id = $(event.target).val();

            if ( params.debug_mode ) {
                console.log('Variation ID:', variation_id);
            }

            if ( params.update_method === 'ajax' ) {
                this.sendUpdatePriceRequest( null, get_quantity, variation_id );
            } else {
                this.updatePriceHtml( variation_id );
            }
        },

        /**
         * Handler for clearing variations
         * 
         * @since 2.8.0
         * @param {object} event Event object
         */
        onClearVariations: function (event) {
            event.preventDefault();

            product_price_container.removeClass('active').addClass('d-none').html('');
            siblings_price.html(original_price).removeClass('d-none');
        },

        /**
         * Handler for quantity change
         * 
         * @since 2.8.0
         */
        onQuantityChanges: function () {
            get_quantity = $('input[name="quantity"]').val() || 1;

            $('form.variations_form').on('found_variation show_variation', this.handleVariationEvent.bind(this));

            this.preventDuplicatePrices();
        },

        /**
         * Handler for trigger events
         * 
         * @since 2.8.0
         */
        onTriggerEvent: function () {
            $('form.variations_form').on('found_variation show_variation', this.handleVariationEvent.bind(this));
        },

        /**
         * Handler for variation events
         * 
         * @since 2.8.0
         * @param {object} e | Event object
         * @param {object} variation | Variation object
         */
        handleVariationEvent: function (e, variation) {
            if (variation && variation.variation_id) {
                if (params.update_method === 'ajax') {
                    this.sendUpdatePriceRequest(variation.display_price, get_quantity, variation.variation_id);
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
        sendUpdatePriceRequest: function (price, quantity, product_id) {
            product_price_container.addClass('wci-loading-price');
            siblings_price.addClass('wci-loading-price');

            $.ajax({
                url: params.ajax_url,
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
                        replace_range_price.updatePriceHtmlAjax(response.data.price_html);
                    } else {
                        replace_range_price.updatePriceHtmlAjax(original_price);
                    }

                    product_price_container.removeClass('wci-loading-price');
                    siblings_price.removeClass('wci-loading-price');
                },
                error: function () {
                    replace_range_price.updatePriceHtmlAjax(original_price);
                    product_price_container.removeClass('wci-loading-price');
                    siblings_price.removeClass('wci-loading-price');
                    product_price_container.removeClass('active').html('');
                    replace_range_price.preventDuplicatePrices();
                },
            });
        },

        /**
         * Update the price HTML for a selected variation
         * 
         * @since 2.8.0
         * @param {int} variation_id Variation ID
         */
        updatePriceHtml: function (variation_id) {
            if (params.update_method !== 'dynamic') return;

            var price_html = $(`#wci-variation-prices .wci-variation-item[data-variation-id="${variation_id}"]`).html();
            product_price_container.html(price_html).addClass('active').removeClass('d-none');

            this.preventDuplicatePrices();
        },

        /**
         * Update the price HTML via AJAX response
         * 
         * @since 2.8.0
         * @param {string} price_html Price HTML
         */
        updatePriceHtmlAjax: function (price_html) {
            product_price_container.html(price_html).removeClass('d-none').addClass('active');
        },

        /**
         * Prevent duplication of prices
         * 
         * @since 2.8.0
         */
        preventDuplicatePrices: function () {
            if (product_price_container && product_price_container.hasClass('active') && product_price_container.html().trim() !== '') {
                siblings_price.addClass('d-none');
            } else {
                product_price_container.removeClass('active');
                siblings_price.removeClass('d-none');
            }
        },

        /**
         * Initialize the module
         * 
         * @since 2.8.0
         */
        init: function () {
            product_price_container = $('#woo-custom-installments-product-price');
            siblings_price = product_price_container.siblings('.woo-custom-installments-group');
            original_price = this.getOriginalPrice();
            get_quantity = $('input[name="quantity"]').val();

            this.bindEvents();
        },
    };

    // Initialize the module after the page is loaded
    jQuery(document).ready( function() {
        Replace_Range_Price.init();
    });
})(jQuery);