(function($) {
    "use strict";

    /**
     * Front scripts params
     * 
     * @since 3.0.0
     * @version 5.4.0
     * @returns {Object}
     */
    const params = window.wci_front_params || {};

	/**
	 * Save current price
	 * 
	 * @since 5.4.0
	 * @returns {Object}
	 */
	var current_price = {};

	/**
	 * Save current quantity
	 * 
	 * @since 5.4.0
	 * @returns {int}
	 */
	var current_quantity = 1;

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
		 * @version 5.4.0
         * @param {object} e | Click event
         */
        toggleAccordion: function(e) {
            var header = $(e.currentTarget);
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
         * Initialize range price replacement
         * 
         * @since 2.8.0
         * @version 5.4.0
         */
        replaceRangePrice: function() {
			if ( ! params.product_variation_with_range || ! params.license_valid ) {
				if ( params.debug_mode ) {
					console.log('Woo Custom Installments: Range price is disabled.');
				}

				return;
			}

			// on found or show variation
			$(document).on('found_variation show_variation', 'form.variations_form', function(e, variation) {
				Woo_Custom_Installments.rangeHandleVariationEvent( variation );
            });

			// select variation price html
           	$(document).on('change', 'input[name="variation_id"]', function() {
				let variation_id = $(this).val();

				Woo_Custom_Installments.rangeUpdatePrice( variation_id );
			});

			// hide variation price on reset
            $(document).on('click', 'a.reset_variation, a.reset_variations', function() {
                Woo_Custom_Installments.rangeOnClearVariations();
            });

			// get trigger selectors
            var triggers = ( params.element_triggers || '' ).split(',');

            triggers.forEach( function(trigger) {
                $(trigger.trim()).on('click touchstart change', Woo_Custom_Installments.rangeOnTriggerEvent.bind(this));
            }.bind(this));
        },

        /**
         * Handler for clearing variations (range price)
         * 
         * @since 2.8.0
		 * @version 5.4.0
         */
        rangeOnClearVariations: function() {
			let price_container = $('.woo-custom-installments-price-container');
			let siblings_price = price_container.siblings('.woo-custom-installments-group');

            price_container.removeClass('active').addClass('d-none').html('');
            siblings_price.removeClass('d-none');
        },

        /**
         * Handler for trigger events (range price)
         * 
         * @since 2.8.0
		 * @version 5.4.0
         */
        rangeOnTriggerEvent: function() {
            $(document).on('found_variation show_variation', 'form.variations_form', function(e, variation) {
				Woo_Custom_Installments.rangeHandleVariationEvent( variation );
            });
        },

        /**
         * Handler for variation events (range price)
         * 
         * @since 2.8.0
		 * @version 5.4.0
         * @param {object} variation | Variation object
         */
        rangeHandleVariationEvent: function( variation ) {
			if ( variation && variation.variation_id ) {
                Woo_Custom_Installments.rangeUpdatePrice( variation.variation_id );

				current_price = {
					old_price: variation.display_regular_price,
					new_price: variation.display_price,
				};

                Woo_Custom_Installments.updateAmounts( current_price, current_quantity );
            }
        },

        /**
         * Update the price HTML for a selected variation
         * 
         * @since 2.8.0
		 * @version 5.4.0
         * @param {int} variation_id | Variation ID
         */
        rangeUpdatePrice: function( variation_id ) {
            let price_html = $('#wci-variation-prices').find(`.wci-variation-item[data-variation-id="${variation_id}"]`).html();

			// display variation price on price container
            $('.woo-custom-installments-price-container').html(price_html).addClass('active').removeClass('d-none');

            Woo_Custom_Installments.rangePreventDuplicatePrices();
        },

        /**
         * Prevent duplication of prices display (range price)
         * 
         * @since 2.8.0
		 * @version 5.4.0
         */
        rangePreventDuplicatePrices: function() {
			let price_container = $('.woo-custom-installments-price-container');
			let siblings_price = price_container.siblings('.woo-custom-installments-group');

			// check if price container is active and not empty
            if ( price_container.hasClass('active') && price_container.html().trim() !== '' ) {
                siblings_price.addClass('d-none');
            } else {
                price_container.removeClass('active');
                siblings_price.removeClass('d-none');
            }
        },

		/**
		 * On update quantity for product
		 * 
		 * @since 5.4.0
		 */
		updateQuantity: function() {
			// Get quantity input value
			$(document).on( 'change', 'input[name="quantity"]', function() {
				current_quantity = parseInt( $(this).val() ) || 1;

				if ( params.product.type === 'simple' ) {
					current_price = {
						old_price: params.product.regular_price,
						new_price: params.product.current_price,
					};
				}

				Woo_Custom_Installments.updateAmounts( current_price, current_quantity );
			});
		},

		/**
         * Update all .amount elements under .woo-custom-installments-group
         * 
         * @since 5.4.0
         * @param {object} price | Price object
		 * @param {number} quantity | Quantity value
         */
        updateAmounts: function( price = {}, quantity = 1 ) {
			var price_container = Woo_Custom_Installments.getPriceSelector();
			var get_quantity = quantity;

            // update price with quantity
			if ( params.update_price_with_quantity === 'yes' ) {
				get_quantity = parseInt( quantity ) || $('input[name="quantity"]').val();
			}

			// update installments
			Woo_Custom_Installments.updateTableInstallments( price.new_price * get_quantity );

			// set product price object with quantity
			let product_price = {
				old_price: price.old_price * get_quantity,
				new_price: price.new_price * get_quantity,
			};

			// update main prices
			Woo_Custom_Installments.updateMainPriceElement( product_price );

			let enabled_discount_per_unit = params.discounts.enable_discount_per_unit;
			let discount_per_unit_method = params.discounts.discount_per_unit_method;
			let discount_per_unit_value = params.discounts.unit_discount_amount;
			let pix_discount = params.discounts.pix_discount;
			let pix_discount_method = params.discounts.pix_discount_method;
			let economy = 0;

			// update discount on pix
			if ( price_container.find('.woo-custom-installments-offer').length > 0 ) {
				if ( enabled_discount_per_unit === 'yes' ) {
					if ( discount_per_unit_method === 'percentage' ) {
						let pix_discount_percentage_per_unit = Woo_Custom_Installments.getPercentageDiscount( discount_per_unit_value, price.new_price );
						
						// set pix economy value
						economy = ( price.new_price - pix_discount_percentage_per_unit ) * get_quantity;

						Woo_Custom_Installments.updatePixDiscountElement( pix_discount_percentage_per_unit * get_quantity );
					} else if ( discount_per_unit_method === 'fixed' ) {
						let fixed_discount_pix_per_unit = ( price.new_price - discount_per_unit_value );

						// set pix economy value
						economy = ( price.new_price - fixed_discount_pix_per_unit ) * get_quantity;

						Woo_Custom_Installments.updatePixDiscountElement( fixed_discount_pix_per_unit * get_quantity );
					}
				} else if ( pix_discount ) {
					if ( pix_discount_method === 'percentage' ) {
						let pix_discount_percentage = Woo_Custom_Installments.getPercentageDiscount( pix_discount, price.new_price );
						
						// set pix economy value
						economy = ( price.new_price - pix_discount_percentage ) * get_quantity;

						Woo_Custom_Installments.updatePixDiscountElement( pix_discount_percentage * get_quantity );
					} else if ( pix_discount_method === 'fixed' ) {
						let fixed_discount_pix = ( price.new_price - pix_discount );
						
						// set pix economy value
						economy = ( price.new_price - fixed_discount_pix ) * get_quantity;

						Woo_Custom_Installments.updatePixDiscountElement( fixed_discount_pix * get_quantity );
					}
				}
			}

			let slip_bank_discount = params.discounts.slip_bank_discount;
			let slip_bank_discount_method = params.discounts.slip_bank_method;

			// update slip bank discount element
			if ( price_container.find('.woo-custom-installments-ticket-discount').length > 0 ) {
				if ( slip_bank_discount_method === 'percentage' ) {
					let slip_bank_discount_percentage = Woo_Custom_Installments.getPercentageDiscount( slip_bank_discount, price.new_price ) * get_quantity;

					Woo_Custom_Installments.updateSlipBankElement( slip_bank_discount_percentage );
				} else if ( slip_bank_discount_method === 'fixed' ) {
					Woo_Custom_Installments.updateSlipBankElement( ( price.new_price - slip_bank_discount ) * get_quantity );
				}
			}

			// update economy on Pix element
			if ( price_container.find('.woo-custom-installments-economy-pix-badge').length > 0 ) {
				Woo_Custom_Installments.updateEconomyElement( economy );
			}
        },

		/**
		 * Get price selector for display prices
		 * 
		 * @since 5.4.0
		 * @returns {jQuery-object}
		 */
		getPriceSelector: function() {
			if ( params.product.type === 'variable' || params.product.type === 'variation' ) {
				return $('.woo-custom-installments-price-container');
			} else {
				return $('.woo-custom-installments-price-container').siblings('.woo-custom-installments-group');
			}
		},

		/**
		 * Update main price element
		 * 
		 * @since 5.4.0
		 * @param {object} price | Price value
		 */
		updateMainPriceElement: function( price ) {
			let selector = Woo_Custom_Installments.getPriceSelector();

			// has discount
			if ( selector.find('.woo-custom-installments-price.has-discount').length > 0 ) {
				// update old price
				if ( price.old_price ) {
					selector.find('.woo-custom-installments-price.has-discount').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price.old_price ) );
				}
				
				// update new price
				if ( price.new_price ) {
					selector.find('.woo-custom-installments-price.sale-price').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price.new_price ) );
				}
			} else {
				if ( price.new_price ) {
					selector.find('.woo-custom-installments-price').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price.new_price ) );
				}
			}
		},

		/**
		 * Update pix discount element
		 * 
		 * @since 5.4.0
		 * @param {float} price | Price value
		 */
		updatePixDiscountElement: function( price ) {
			// get main group selector
			let selector = Woo_Custom_Installments.getPriceSelector();

			// update discount on pix inside main group
			selector.find('.woo-custom-installments-offer').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price ) );

			// update discount on pix inside modal
			$('.wci-popup-body').find('.pix-method-container').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price ) );

			// update discount on pix inside accordion
			$('.wci-accordion-content').find('.pix-method-container').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price ) );
		},

		/**
		 * Update slip bank discount element
		 * 
		 * @since 5.4.0
		 * @param {float} price | Price value
		 */
		updateSlipBankElement: function( price ) {
			// get main group selector
			let selector = Woo_Custom_Installments.getPriceSelector();

			selector.find('.woo-custom-installments-ticket-discount').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price ) );
			$('.wci-popup-body').find('.woo-custom-installments-ticket-section').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price ) );
			$('.wci-accordion-content').find('.woo-custom-installments-ticket-section').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price ) );
		},

		/**
		 * Update economy on Pix element
		 * 
		 * @since 5.4.0
		 * @param {float} price | Price value
		 */
		updateEconomyElement: function( price ) {
			// get main group selector
			let selector = Woo_Custom_Installments.getPriceSelector();

			selector.find('.woo-custom-installments-economy-pix-badge').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price ) );
			$('.wci-popup-body').find('.woo-custom-installments-economy-pix-badge').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price ) );
			$('.wci-accordion-content').find('.woo-custom-installments-economy-pix-badge').find('.amount').html( Woo_Custom_Installments.getFormattedPrice( price ) );
		},

		/**
		 * Get final price with percentage discount
		 * 
		 * @since 5.4.0
		 * @param {float} discount | Discount value
		 * @param {float} price | Price value
		 * @return {float}
		 */
		getPercentageDiscount: function( discount, price ) {
			return price - ( price * ( discount / 100 ) );
		},

		/**
		 * Compat with Tiered Price Table plugin
		 * 
		 * @since 5.4.0
		 */
		updatedTieredPrice: function() {
			/**
             * On change of tiered price, update the amounts
             * 
             * @since 5.1.0
			 * @version 5.4.0
             * @param {object} e | Event object
             * @param {object} variation | Variation product object
             */
            $(document).on('tiered_price_update', function(e, variation) {
				current_price = {
					old_price: null,
					new_price: variation.price,
				};

                Woo_Custom_Installments.updateAmounts( current_price, variation.quantity );
            });
		},

        /**
         * Update table installments based on variation or direct price
         * 
         * @since 2.3.5
         * @version 5.4.0
         * @param {float} price | Product price
         */
        updateTableInstallments: function( price ) {
            var get_price = price;
            var tbody = $('.woo-custom-installments-table').find('tbody');
            var default_text = tbody.data('default-text');

            tbody.html('<tr style="display: none !important;"></tr>');

            var i = 1;
            var fees = params.installments.fees;
            var last_installment_without_fee = null;
            var last_installment_with_fee = null;

			// get main group selector
			var price_container = Woo_Custom_Installments.getPriceSelector();

			// get best installments label with variables for single product page
			let best_installments_label = params.i18n.best_installments_sp;

			// loop through installments
            while ( i <= params.installments.max_installments ) {
                var fee = fees.hasOwnProperty(i) ? fees[i] : params.installments.fee;
                var price, final_cost;

                if ( i <= params.installments.max_installments_no_fee ) {
                    price = get_price / i;

                    if ( price < params.installments.min_installment ) {
                        break;
                    }

                    // Append row without fee (no interest)
                    if ( default_text ) {
                        tbody.append('<tr class="no-fee"><th>' +
							default_text.replace('{{ parcelas }}', i).replace('{{ valor }}', Woo_Custom_Installments.getFormattedPrice( price )).replace('{{ juros }}', params.i18n.without_fee_label) +
							'</th><th>' + Woo_Custom_Installments.getFormattedPrice(get_price) + '</th></tr>');
                    }

                    last_installment_without_fee = {
                        installments: i,
                        price: Woo_Custom_Installments.getFormattedPrice(price),
                    };

					// Update best installments without fee
					if ( best_installments_label ) {
						let installments_details = best_installments_label.replace('{{ parcelas }}', i).replace('{{ valor }}', Woo_Custom_Installments.getFormattedPrice( price )).replace('{{ juros }}', params.i18n.without_fee_label);
					
						price_container.find('.woo-custom-installments-details.best-value.no-fee').html(installments_details);
					}
                } else {
                    fee = fee.toString().replace(',', '.') / 100;

                    if ( params.installments.fee !== fee ) {
                        final_cost = get_price + ( get_price * fee );
                        price = final_cost / i;
                    } else {
                        var exp = Math.pow(1 + fee, i);
						
                        price = get_price * fee * exp / (exp - 1);
                        final_cost = price * i;
                    }

                    if ( price < params.installments.min_installment ) {
                        break;
                    }

                    // Append row with fee (with interest)
                    if ( default_text ) {
                        tbody.append('<tr class="fee-included"><th>' +
							default_text.replace('{{ parcelas }}', i).replace('{{ valor }}', Woo_Custom_Installments.getFormattedPrice(price)).replace('{{ juros }}', params.i18n.with_fee_label) +
							'</th><th>' + Woo_Custom_Installments.getFormattedPrice(final_cost) + '</th></tr>');
                    }

                    last_installment_with_fee = {
                        installments: i,
                        price: Woo_Custom_Installments.getFormattedPrice(price),
                    };

					// Update best installments with fee
            		if ( best_installments_label ) {
						let installments_details = best_installments_label.replace('{{ parcelas }}', i).replace('{{ valor }}', Woo_Custom_Installments.getFormattedPrice( price )).replace('{{ juros }}', params.i18n.with_fee_label);
					
						price_container.find('.woo-custom-installments-details-with-fee .best-value.fee-included').html(installments_details);
					}
                }

                i++;
            }
        },

        /**
         * Get formatted price string
         * 
         * @since 2.3.5
         * @param {number} price | Price value
         * @returns {string} Formatted price
         */
        getFormattedPrice: function( price ) {
            return accounting.formatMoney(price, {
                symbol: params.currency.symbol,
                decimal: params.currency.format_decimal_sep,
                thousand: params.currency.format_thousand_sep,
                precision: params.currency.format_num_decimals,
                format: params.currency.format
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

			// Initialize price range
			if ( params.active_price_range === 'yes' && params.product.type !== 'simple' ) {
				this.replaceRangePrice();
			}

			// Initialize tiered price compatibility
            this.updatedTieredPrice();

			// update price with quantity
			if ( params.update_price_with_quantity === 'yes' ) {
				this.updateQuantity();
			}
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