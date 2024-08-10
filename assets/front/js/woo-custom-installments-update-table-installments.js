/**
 * Update table installments
 * 
 * @since 2.3.5
 * @version 4.5.0
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
		// Initial load.
		$(document.body).on('show_variation', function( e, variation, purchasable ) {
		  Woo_Custom_Installments.update_table( e, variation, purchasable );
		});
	  },
  
  
	  /**
	   * Update table installments
	   *
	   * @param {string} field Target
	   * @param {boolean} copy
	   */
	  update_table: function( e, variation, purchasable ) {
		var tbody = $('.woo-custom-installments-table').find('tbody');
		tbody.html( '<tr style="display: none !important;"></tr>' );
  
		var i = 1;
		var fees = wci_update_table_params.fees;

		while ( i <= wci_update_table_params.max_installments ) {
		  var fee = fees.hasOwnProperty(i) ? fees[i] : wci_update_table_params.fee;
  
		  if ( i <= wci_update_table_params.max_installments_no_fee ) {
			var price = variation.display_price / i;
  
			if ( price < wci_update_table_params.min_installment ) {
			  break;
			}
  
			tbody.append( '<tr class="fee-included"><th>' + tbody.data('default-text').replace( '{{ parcelas }}', i ).replace( '{{ valor }}', Woo_Custom_Installments.get_formatted_price( price ) ).replace( '{{ juros }}', wci_update_table_params.without_fee_label ) + '</th><th>' + Woo_Custom_Installments.get_formatted_price( variation.display_price ) + '</th></tr>' );
		  } else {
			if ( wci_update_table_params.fee !== fee ) {
				// custom fees
				var fee = fee.toString().replace( ',', '.' ) / 100;
				var final_cost = variation.display_price + ( variation.display_price * fee );
				var price = final_cost / i;
			} else {
			  	var fee = fee.toString().replace( ',', '.' ) / 100;
				var exp = Math.pow( 1 + fee, i );
				var price = variation.display_price * fee * exp / ( exp - 1 );
				var final_cost = price * i;
			}
  
			if ( price < wci_update_table_params.min_installment ) {
			  break;
			}
  
			tbody.append( '<tr class="fee-included"><th>' + tbody.data( 'default-text' ).replace( '{{ parcelas }}', i ).replace( '{{ valor }}', Woo_Custom_Installments.get_formatted_price( price ) ).replace( '{{ juros }}', wci_update_table_params.with_fee_label ) + '</th><th>' + Woo_Custom_Installments.get_formatted_price( final_cost ) + '</th></tr>' );
		  }
  
		  i++;
		}

		// AJAX call to get update prices
		$.ajax({
			url: wci_update_table_params.ajax_url,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'get_updated_variation_prices_action',
				variation_id: variation.variation_id,
			},
			success: function(response) {
				if ( response.success ) {
					$.each(response.data, function(key, value) {
						$(value.element).find('.amount').html(value.price);
					});
				}
			}
		});
	  },
  
	  /**
	   * Formatted price
	   *
	   * @param {string} price
	   */
	  get_formatted_price: function(price) {
		'use strict';
  
		var formatted_price = accounting.formatMoney( price, {
		  symbol: wci_update_table_params.currency_format_symbol,
		  decimal: wci_update_table_params.currency_format_decimal_sep,
		  thousand: wci_update_table_params.currency_format_thousand_sep,
		  precision: wci_update_table_params.currency_format_num_decimals,
		  format: wci_update_table_params.currency_format
		} );
  
		return formatted_price;
	  }
	};
  
	Woo_Custom_Installments.init();
});