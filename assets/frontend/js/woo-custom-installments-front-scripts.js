(function($) {
    "use strict";

    /**
     * Front scripts params
     * 
     * @since 3.0.0
     * @version 5.4.0
     * @returns {Object}
     */
    const front_params = window.wci_front_params || {};

    /**
     * Range price params
     * 
     * @since 2.8.0
     * @version 5.4.0
     * @returns {Object}
     */
    const price_range_params = window.wci_range_params || {};

    /**
     * Table installments params
     * 
     * @since 2.3.5
     * @version 5.4.0
     * @returns {Object}
     */
    const table_params = window.wci_update_table_params || {};

    /**
     * Woo Custom Installments object variable
     * 
     * @since 5.4.0
     * @package MeuMouse.com
     */
    var Woo_Custom_Installments = {

        /**
         * Initialize accordion functionality
         * 
         * @since 2.0.0
         * @version 5.4.0
         */
        initAccordion: function() {
            $(document).on( 'click', '.wci-accordion-header', Woo_Custom_Installments.toggleAccordion.bind(this) );
        },

        /**
         * Toggle accordion content
         * 
         * @since 2.0.0
         * @param {object} event | Click event
         */
        toggleAccordion: function(event) {
            var header = $(event.currentTarget);
            var content = header.next('.wci-accordion-content');

            if ( content.css('max-height') === '0px' ) {
                content.css('max-height', content.prop('scrollHeight') + 'px');
                content.parent('.wci-accordion-item').addClass('active');

                $('body').addClass('wci-accordion-active');
            } else {
                content.slideUp(350, function() {
                    content.parent('.wci-accordion-item').removeClass('active');
                    content.css('display', '');
                    content.css('max-height', '0px');

                    $('body').removeClass('wci-accordion-active');
                });
            }
        },

		/**
         * Function for display popups based on Bootstrap
         * 
         * @since 1.3.0
         * @param {object} trigger | jQuery object for trigger button
         * @param {object} container | jQuery object for modal container
         * @param {object} close | jQuery object for close button
         * @package MeuMouse.com
         */
        displayModal: function(trigger, container, close) {
            // open modal on click to trigger
            trigger.on('click touchstart', function(e) {
                e.preventDefault();
                container.addClass('show');
                $('body').addClass('wci-modal-active');
            });

            // close modal on click outside container
            container.on('click', function(e) {
                if (e.target === this) {
                    container.removeClass('show');
                    $('body').removeClass('wci-modal-active');
                }
            });

            // close modal on click close button
            close.on('click touchstart', function(e) {
                e.preventDefault();
                container.removeClass('show');
                $('body').removeClass('wci-modal-active');
            });
        },

        /**
         * Initialize modal functionality
         * 
         * @since 2.0.0
         * @version 5.4.0
         */
        initModal: function() {
			let trigger = $('button.wci-open-popup');
            let container = $('.wci-popup-container');
            let close = $('button.wci-close-popup');

            Woo_Custom_Installments.displayModal( trigger, container, close );
        },

        /**
         * Initialize front scripts for discount per unit
         * 
         * @since 3.0.0
         * @version 5.4.0
         */
        initFrontScripts: function() {
            $(document).on( 'found_variation', 'form.cart', Woo_Custom_Installments.handleDiscountVariation.bind(this) );
        },

        /**
         * Handle variation found event to update discount per unit
         * 
         * @since 3.0.0
         * @param {object} e | Event object
         * @param {object} variation | Variation product object
         */
        handleDiscountVariation: function(e, variation) {
            if (front_params.enable_discount_per_unit === 'yes') {
                var variation_price = variation.display_price;
                var original_price = parseFloat(variation_price);
                var discount_amount = parseFloat(front_params.unit_discount_amount);
                var currency_symbol = front_params.currency_symbol;
                var price_element = $('.woo-custom-installments-offer .discounted-price');
                var custom_price;

                if ( front_params.discount_per_unit_method === 'percentage' ) {
                    custom_price = original_price - ( original_price * ( discount_amount / 100 ) );
                } else {
                    custom_price = original_price - discount_amount;
                }

                var formatted_price = currency_symbol + custom_price.toFixed(2).replace('.', ',');
                price_element.text(formatted_price);
            }
        },

        /**
         * Initialize range price replacement
         * 
         * @since 2.8.0
         * @version 5.4.0
         */
        initRange: function() {
            Woo_Custom_Installments.rangeGetOriginalPrice();
            Woo_Custom_Installments.rangeBindEvents();
        },

        /**
         * Bind events related to range price
         * 
         * @since 2.8.0
         */
        rangeBindEvents: function() {
            $(document).on('change', 'input[name="variation_id"]', Woo_Custom_Installments.rangeOnVariationChange.bind(this));
            $(document).on('click', 'a.reset_variation, a.reset_variations', Woo_Custom_Installments.rangeOnClearVariations.bind(this));
            $(document).on('change', 'input[name="quantity"]', Woo_Custom_Installments.rangeOnQuantityChanges.bind(this));

            var triggers = ( price_range_params.element_triggers || '' ).split(',');

            triggers.forEach( function(trigger) {
                $(trigger.trim()).on('click touchstart change', Woo_Custom_Installments.rangeOnTriggerEvent.bind(this));
            }.bind(this));
        },

        /**
         * Get the original price HTML
         * 
         * @since 2.8.0
         * @returns {string} Original price HTML
         */
        rangeGetOriginalPrice: function() {
            this.product_price_container = $('#woo-custom-installments-product-price');
            this.siblings_price = this.product_price_container.siblings('.woo-custom-installments-group');
            this.original_price = this.product_price_container && this.product_price_container.html().trim() === ''
                ? this.siblings_price.html()
                : this.product_price_container.html();
            this.current_quantity = $('input[name="quantity"]').val();
        },

        /**
         * Handler for variation change (range price)
         * 
         * @since 2.8.0
         * @param {object} e | Event object
         */
        rangeOnVariationChange: function(e) {
            var variation_id = $(e.target).val();

            if (price_range_params.debug_mode) {
                console.log('Variation ID:', variation_id);
            }

            Woo_Custom_Installments.rangeUpdatePriceHtml( variation_id );
        },

        /**
         * Handler for clearing variations (range price)
         * 
         * @since 2.8.0
         * @param {object} e | Event object
         */
        rangeOnClearVariations: function(e) {
            e.preventDefault();

            this.product_price_container.removeClass('active').addClass('d-none').html('');
            this.siblings_price.html(this.original_price).removeClass('d-none');
        },

        /**
         * Handler for quantity changes (range price)
         * 
         * @since 2.8.0
         */
        rangeOnQuantityChanges: function() {
            this.current_quantity = $('input[name="quantity"]').val() || 1;
            $('form.variations_form').on( 'found_variation show_variation', Woo_Custom_Installments.rangeHandleVariationEvent.bind(this) );

            Woo_Custom_Installments.rangePreventDuplicatePrices();
        },

        /**
         * Handler for trigger events (range price)
         * 
         * @since 2.8.0
		 * @version 5.4.0
         */
        rangeOnTriggerEvent: function() {
            $('form.variations_form').on( 'found_variation show_variation', Woo_Custom_Installments.rangeHandleVariationEvent.bind(this) );
        },

        /**
         * Handler for variation events (range price)
         * 
         * @since 2.8.0
		 * @version 5.4.0
         * @param {object} e | Event object
         * @param {object} variation | Variation object
         */
        rangeHandleVariationEvent: function(e, variation) {
             if ( variation && variation.variation_id ) {
                Woo_Custom_Installments.rangeUpdatePriceHtml( variation.variation_id );
            }
        },

        /**
         * Update the price HTML for a selected variation (dynamic method)
         * 
         * @since 2.8.0
		 * @version 5.4.0
         * @param {int} variation_id | Variation ID
         */
        rangeUpdatePriceHtml: function(variation_id) {
            var price_html = $('#wci-variation-prices').find(`.wci-variation-item[data-variation-id="${variation_id}"]`).html();

            this.product_price_container.html(price_html).addClass('active').removeClass('d-none');

            Woo_Custom_Installments.rangePreventDuplicatePrices();
        },

        /**
         * Update the price HTML
         * 
         * @since 2.8.0
		 * @version 5.4.0
         * @param {string} price_html | Price HTML
         */
        rangeUpdatePriceHtmlAjax: function(price_html) {
            this.product_price_container.html(price_html).removeClass('d-none').addClass('active');
        },

        /**
         * Prevent duplication of prices display (range price)
         * 
         * @since 2.8.0
         */
        rangePreventDuplicatePrices: function() {
            if ( this.product_price_container.hasClass('active') && this.product_price_container.html().trim() !== '' ) {
                this.siblings_price.addClass('d-none');
            } else {
                this.product_price_container.removeClass('active');
                this.siblings_price.removeClass('d-none');
            }
        },

        /**
         * Initialize table installments functionality
         * 
         * @since 2.3.5
         * @version 5.4.0
         */
        initTableInstallments: function() {
            $(document.body).on('show_variation found_variation', function(e, variation) {
                Woo_Custom_Installments.updateTableInstallments(e, variation, false);
            });

            /**
             * Compat with Tiered Price Table plugin
             * 
             * @since 5.1.0
             * @param {object} e | Event type
             * @param {object} variation | Variation product object
             */
            $(document).on('tiered_price_update', function(e, variation) {
                Woo_Custom_Installments.updateTableInstallments(e, variation.price, true);
            });
        },

        /**
         * Update table installments based on variation or direct price
         * 
         * @since 2.3.5
         * @version 5.4.0
         * @param {object} e | Event object
         * @param {object|number} variation | Variation object or direct price
         * @param {boolean} direct_price | If true, variation is a direct price
         */
        updateTableInstallments: function(e, variation, direct_price) {
            var get_price = variation.display_price || variation;
            var tbody = $('.woo-custom-installments-table').find('tbody');
            var default_text = tbody.data('default-text');

            tbody.html('<tr style="display: none !important;"></tr>');

            var i = 1;
            var fees = table_params.fees;
            var last_no_fee_installment = null;
            var last_fee_installment = null;

            while ( i <= table_params.max_installments ) {
                var fee = fees.hasOwnProperty(i) ? fees[i] : table_params.fee;
                var price, final_cost;

                if ( i <= table_params.max_installments_no_fee ) {
                    price = get_price / i;

                    if ( price < table_params.min_installment ) {
                        break;
                    }

                    // Append row without fee (no interest)
                    if (default_text) {
                        tbody.append( '<tr class="no-fee"><th>' +
							default_text
								.replace('{{ parcelas }}', i)
								.replace('{{ valor }}', Woo_Custom_Installments.getFormattedPrice(price))
								.replace('{{ juros }}', table_params.without_fee_label) +
							'</th><th>' +
							Woo_Custom_Installments.getFormattedPrice(get_price) +
							'</th></tr>' );
                    }

                    last_no_fee_installment = {
                        installments: i,
                        price: Woo_Custom_Installments.getFormattedPrice(price)
                    };
                } else {
                    fee = fee.toString().replace(',', '.') / 100;

                    if ( table_params.fee !== fee ) {
                        final_cost = get_price + ( get_price * fee );
                        price = final_cost / i;
                    } else {
                        var exp = Math.pow(1 + fee, i);
						
                        price = get_price * fee * exp / (exp - 1);
                        final_cost = price * i;
                    }

                    if ( price < table_params.min_installment ) {
                        break;
                    }

                    // Append row with fee (with interest)
                    if ( default_text ) {
                        tbody.append( '<tr class="fee-included"><th>' +
							default_text
								.replace('{{ parcelas }}', i)
								.replace('{{ valor }}', Woo_Custom_Installments.getFormattedPrice(price))
								.replace('{{ juros }}', table_params.with_fee_label) +
							'</th><th>' +
							Woo_Custom_Installments.getFormattedPrice(final_cost) +
							'</th></tr>' );
                    }

                    last_fee_installment = {
                        installments: i,
                        price: Woo_Custom_Installments.getFormattedPrice(price)
                    };
                }

                i++;
            }

            // Update main container price elements if tiered plugin is active
            if (last_no_fee_installment && table_params.check_tiered_plugin === '1') {
                $('.woo-custom-installments-group.variable-range-price')
                    .find('.woo-custom-installments-details-without-fee .best-value.no-fee .amount')
                    .html(last_no_fee_installment.price);
                $('.woocommerce-variation-price')
                    .find('.woo-custom-installments-details-without-fee .best-value.no-fee .amount')
                    .html(last_no_fee_installment.price);

                if ($('#woo-custom-installments-product-price').hasClass('active')) {
                    $('#woo-custom-installments-product-price')
                        .find('.woo-custom-installments-details-without-fee .best-value.no-fee .amount')
                        .html(last_no_fee_installment.price);
                }
            }

            if (last_fee_installment && table_params.check_tiered_plugin === '1') {
                $('.woo-custom-installments-group.variable-range-price')
                    .find('.woo-custom-installments-details-with-fee .best-value.fee-included .amount')
                    .html(last_fee_installment.price);
                $('.woocommerce-variation-price')
                    .find('.woo-custom-installments-details-with-fee .best-value.fee-included .amount')
                    .html(last_fee_installment.price);

                if ($('#woo-custom-installments-product-price').hasClass('active')) {
                    $('#woo-custom-installments-product-price')
                        .find('.woo-custom-installments-details-with-fee .best-value.fee-included .amount')
                        .html(last_fee_installment.price);
                }
            }
        },

        /**
         * Get formatted price string
         * 
         * @since 2.3.5
         * @param {number} price | Price value
         * @returns {string} Formatted price
         */
        getFormattedPrice: function(price) {
            return accounting.formatMoney(price, {
                symbol: table_params.currency_format_symbol,
                decimal: table_params.currency_format_decimal_sep,
                thousand: table_params.currency_format_thousand_sep,
                precision: table_params.currency_format_num_decimals,
                format: table_params.currency_format
            });
        },

		/**
         * Initialize all modules
         * 
         * @since 5.4.0
         */
        init: function() {
            this.initAccordion();
            this.initModal();
            this.initFrontScripts();
            this.initRange();
            this.initTableInstallments();
        },
    };

	/**
	 * Initialize all modules
	 * 
	 * @since 5.4.0
	 * @package MeuMouse.com
	 */
    jQuery(document).ready( function($) {
        Woo_Custom_Installments.init();
    });
})(jQuery);