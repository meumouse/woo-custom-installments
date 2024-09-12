/**
 * Update table installments
 * 
 * @since 2.3.5
 * @version 5.1.2
 */
jQuery(document).ready( function($) {
	/**
	 * Init object
	 */
	var Woo_Custom_Installments = {
  
		/**
		 * Initialize actions
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
				Woo_Custom_Installments.update_table(e, variation, false);
			});

			/**
			 * Compat with Tiered Price Table plugin
			 * 
			 * @since 5.1.0
			 * @param {object} e | Event type
			 * @param {object} variation | Variation product object
			 */
			$(document).on('tiered_price_update', function(e, variation) {
				Woo_Custom_Installments.update_table(e, variation.price, true);
			});
		},
  
		/**
		 * Update table installments
		 *
		 * @since 2.3.5
		 * @version 5.1.0
		 * @param {object} e | Event type
		 * @param {object} variation | Object variation
		 * @param {boolean} direct_price | If object product is unavailable, send direct price on variation param (Compatibility with Tiered Price Table)
		 */
		update_table: function(e, variation, direct_price = false) {
			var get_price = variation.display_price || variation;
			var get_variation_id = variation.variation_id || variation;
			var tbody = $('.woo-custom-installments-table').find('tbody');
			var default_text = tbody.data('default-text');
			tbody.html('<tr style="display: none !important;"></tr>');
		
			var i = 1;
			var fees = wci_update_table_params.fees;
		
			var last_no_fee_installment = null;
			var last_fee_installment = null;
		
			while (i <= wci_update_table_params.max_installments) {
				var fee = fees.hasOwnProperty(i) ? fees[i] : wci_update_table_params.fee;
		
				if (i <= wci_update_table_params.max_installments_no_fee) {
					var price = get_price / i;
		
					if (price < wci_update_table_params.min_installment) {
						break;
					}
		
					// Append row without fee (no interest)
					if (default_text) {
						tbody.append('<tr class="no-fee"><th>' + default_text.replace('{{ parcelas }}', i).replace('{{ valor }}', Woo_Custom_Installments.get_formatted_price(price)).replace('{{ juros }}', wci_update_table_params.without_fee_label) + '</th><th>' + Woo_Custom_Installments.get_formatted_price(get_price) + '</th></tr>');
					}

					// Store the last "no-fee" installment
					last_no_fee_installment = {
						installments: i,
						price: Woo_Custom_Installments.get_formatted_price(price)
					};
		
				} else {
					if (wci_update_table_params.fee !== fee) {
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
		
					if (price < wci_update_table_params.min_installment) {
						break;
					}
		
					// Append row with fee (with interest)
					if (default_text) {
						tbody.append('<tr class="fee-included"><th>' + default_text.replace('{{ parcelas }}', i).replace('{{ valor }}', Woo_Custom_Installments.get_formatted_price(price)).replace('{{ juros }}', wci_update_table_params.with_fee_label) + '</th><th>' + Woo_Custom_Installments.get_formatted_price(final_cost) + '</th></tr>');
					}

					// Store the last "fee-included" installment
					last_fee_installment = {
						installments: i,
						price: Woo_Custom_Installments.get_formatted_price(price)
					};
				}
		
				i++;
			}
		
			// Update main container price elements without altering the surrounding text
			if ( last_no_fee_installment && wci_update_table_params.check_tiered_plugin === '1' ) {
				$('.woo-custom-installments-group.variable-range-price').find('.woo-custom-installments-details-without-fee .best-value.no-fee .amount').html(last_no_fee_installment.price);
				$('.woocommerce-variation-price').find('.woo-custom-installments-details-without-fee .best-value.no-fee .amount').html(last_no_fee_installment.price);

				if ( $('#woo-custom-installments-product-price').hasClass('active') ) {
					$('#woo-custom-installments-product-price').find('.woo-custom-installments-details-without-fee .best-value.no-fee .amount').html(last_no_fee_installment.price);
				}
			}
		
			if ( last_fee_installment && wci_update_table_params.check_tiered_plugin === '1' ) {
				$('.woo-custom-installments-group.variable-range-price').find('.woo-custom-installments-details-with-fee .best-value.fee-included .amount').html(last_fee_installment.price);
				$('.woocommerce-variation-price').find('.woo-custom-installments-details-with-fee .best-value.fee-included .amount').html(last_fee_installment.price);

				if ( $('#woo-custom-installments-product-price').hasClass('active') ) {
					$('#woo-custom-installments-product-price').find('.woo-custom-installments-details-with-fee .best-value.fee-included .amount').html(last_fee_installment.price);
				}
			}

			/**
			 * Update price elements on popup and accordion on change variation
			 *
			 * @since 4.5.0
			 * @version 5.1.0
			 * @package MeuMouse.com
			 */
			$.ajax({
				url: wci_update_table_params.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'get_updated_variation_prices_action',
					variation_id: get_variation_id,
					direct_price: direct_price,
				},
				success: function(response) {
					if (response.success) {
						$.each(response.data, function(key, value) {
							$.each(value.selectors, function(index, selector) {
								$(selector).find('.amount').html(value.price);
							});
						});
					}
				},
				error: function(error) {
					console.log(error);
				},
			});
	},
  
	  /**
	   * Formatted price
	   *
	   * @since 2.3.5
	   * @param {string} price
	   * @returns array
	   */
	  get_formatted_price: function(price) {
		'use strict';
  
		var formatted_price = accounting.formatMoney( price, {
		  symbol: wci_update_table_params.currency_format_symbol,
		  decimal: wci_update_table_params.currency_format_decimal_sep,
		  thousand: wci_update_table_params.currency_format_thousand_sep,
		  precision: wci_update_table_params.currency_format_num_decimals,
		  format: wci_update_table_params.currency_format,
		} );
  
		return formatted_price;
	  }
	};
  
	Woo_Custom_Installments.init();
});