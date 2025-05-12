( function($) {
	"use strict";

	/**
	 * Get table params
	 * 
	 * @since 2.3.5
	 * @version 5.4.0
	 */
	const params = window.wci_update_table_params || {};

	/**
	 * Object variable for table installments
	 * 
	 * @since 2.3.5
	 */
	var Table_Installments = {
  
		/**
		 * Update table installments
		 *
		 * @since 2.3.5
		 * @version 5.4.0
		 * @param {object} e | Event type
		 * @param {object} variation | Object variation
		 * @param {boolean} direct_price | If object product is unavailable, send direct price on variation param (Compatibility with Tiered Price Table)
		 */
		updateTableInstallments: function(e, variation, direct_price = false) {
			let get_price = variation.display_price || variation;
			let get_variation_id = variation.variation_id || variation;
			let tbody = $('.woo-custom-installments-table').find('tbody');
			let default_text = tbody.data('default-text');

			tbody.html('<tr style="display: none !important;"></tr>');
		
			let i = 1;
			var fees = params.fees;
		
			let last_no_fee_installment = null;
			let last_fee_installment = null;
		
			while ( i <= params.max_installments ) {
				var fee = fees.hasOwnProperty(i) ? fees[i] : params.fee;
		
				if ( i <= params.max_installments_no_fee ) {
					var price = get_price / i;
		
					if ( price < params.min_installment ) {
						break;
					}
		
					// Append row without fee (no interest)
					if ( default_text ) {
						tbody.append('<tr class="no-fee"><th>' + default_text.replace('{{ parcelas }}', i).replace('{{ valor }}', Table_Installments.getFormattedPrice(price)).replace('{{ juros }}', params.without_fee_label) + '</th><th>' + Table_Installments.getFormattedPrice(get_price) + '</th></tr>');
					}

					// Store the last "no-fee" installment
					last_no_fee_installment = {
						installments: i,
						price: Table_Installments.getFormattedPrice(price)
					};
		
				} else {
					if ( params.fee !== fee ) {
						// custom fees
						var fee = fee.toString().replace(',', '.') / 100;
						var final_cost = get_price + (get_price * fee);
						var price = final_cost / i;
					} else {
						var fee = fee.toString().replace(',', '.') / 100;
						var exp = Math.pow(1 + fee, i);
						var price = get_price * fee * exp / (exp - 1);
						var final_cost = price * i;
					}
		
					if ( price < params.min_installment ) {
						break;
					}
		
					// Append row with fee (with interest)
					if ( default_text ) {
						tbody.append('<tr class="fee-included"><th>' + default_text.replace('{{ parcelas }}', i).replace('{{ valor }}', Table_Installments.getFormattedPrice(price)).replace('{{ juros }}', params.with_fee_label) + '</th><th>' + Table_Installments.getFormattedPrice(final_cost) + '</th></tr>');
					}

					// Store the last "fee-included" installment
					last_fee_installment = {
						installments: i,
						price: Table_Installments.getFormattedPrice(price)
					};
				}
		
				i++;
			}
		
			// Update main container price elements without altering the surrounding text
			if ( last_no_fee_installment && params.check_tiered_plugin === '1' ) {
				$('.woo-custom-installments-group.variable-range-price').find('.woo-custom-installments-details-without-fee .best-value.no-fee .amount').html(last_no_fee_installment.price);
				$('.woocommerce-variation-price').find('.woo-custom-installments-details-without-fee .best-value.no-fee .amount').html(last_no_fee_installment.price);

				if ( $('#woo-custom-installments-product-price').hasClass('active') ) {
					$('#woo-custom-installments-product-price').find('.woo-custom-installments-details-without-fee .best-value.no-fee .amount').html(last_no_fee_installment.price);
				}
			}
		
			if ( last_fee_installment && params.check_tiered_plugin === '1' ) {
				$('.woo-custom-installments-group.variable-range-price').find('.woo-custom-installments-details-with-fee .best-value.fee-included .amount').html(last_fee_installment.price);
				$('.woocommerce-variation-price').find('.woo-custom-installments-details-with-fee .best-value.fee-included .amount').html(last_fee_installment.price);

				if ( $('#woo-custom-installments-product-price').hasClass('active') ) {
					$('#woo-custom-installments-product-price').find('.woo-custom-installments-details-with-fee .best-value.fee-included .amount').html(last_fee_installment.price);
				}
			}
		},
  
		/**
		 * Formatted price
		 *
		 * @since 2.3.5
		 * @param {string} price
		 * @returns array
		 */
		getFormattedPrice: function(price) {
			'use strict';
	
			var formatted_price = accounting.formatMoney( price, {
				symbol: params.currency_format_symbol,
				decimal: params.currency_format_decimal_sep,
				thousand: params.currency_format_thousand_sep,
				precision: params.currency_format_num_decimals,
				format: params.currency_format,
			});
	
			return formatted_price;
		},

	  	/**
		 * Initialize functions
		 * 
		 * @since 2.3.5
		 * @version 5.4.0
		 */
		init: function() {
			/**
			 * Initialize action on show or found variation
			 * 
			 * @since 2.3.5
			 * @param {object} e | Event type
			 * @param {object} variation | Variation product object
			 */
			$(document.body).on('show_variation found_variation', function(e, variation) {
				Table_Installments.updateTableInstallments( e, variation, false );
			});

			/**
			 * Compat with Tiered Price Table plugin
			 * 
			 * @since 5.1.0
			 * @param {object} e | Event type
			 * @param {object} variation | Variation product object
			 */
			$(document).on('tiered_price_update', function(e, variation) {
				Table_Installments.updateTableInstallments( e, variation.price, true );
			});
		},
	};
	
	// Initialize functions on document ready
	jQuery(document).ready( function($) {
		Table_Installments.init();
  	});
})(jQuery);