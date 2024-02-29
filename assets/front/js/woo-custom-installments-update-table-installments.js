/**
 * Update table installments
 * 
 * @since 2.3.5
 */
jQuery( function($) {

	/**
	 * Init object
	 */
	var Woo_Custom_Installments = {
  
	  /**
	   * Initialize actions
	   */
	  init: function() {
		// Initial load.
		$( document.body ).on( 'show_variation', function( event, variation, purchasable ) {
		  Woo_Custom_Installments.updateTable( event, variation, purchasable );
		});
	  },
  
  
	  /**
	   * Update table installments
	   *
	   * @param {String} field Target
	   * @param {Boolean} copy
	   */
	  updateTable: function( event, variation, purchasable ) {
		var tbody = $( '.woo-custom-installments-table' ).find( 'tbody' );
		tbody.html( '<tr style="display: none !important;"></tr>' );
  
		var i = 1;
		var fees = Woo_Custom_Installments_Params.fees;

		while ( i <= Woo_Custom_Installments_Params.max_installments ) {
		  var fee = fees.hasOwnProperty( i ) ? fees[i] : Woo_Custom_Installments_Params.fee;
  
		  if ( i <= Woo_Custom_Installments_Params.max_installments_no_fee ) {
			var price = variation.display_price / i;
  
			if ( price < Woo_Custom_Installments_Params.min_installment ) {
			  break;
			}
  
			tbody.append( '<tr class="fee-included"><th>' + tbody.data( 'default-text' ).replace( '{{ parcelas }}', i ).replace( '{{ valor }}', Woo_Custom_Installments.getFormattedPrice( price ) ).replace( '{{ juros }}', Woo_Custom_Installments_Params.without_fee_label ) + '</th><th>' + Woo_Custom_Installments.getFormattedPrice( variation.display_price ) + '</th></tr>' );
		  } else {
			if ( Woo_Custom_Installments_Params.fee !== fee ) {
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
  
			if ( price < Woo_Custom_Installments_Params.min_installment ) {
			  break;
			}
  
			tbody.append( '<tr class="fee-included"><th>' + tbody.data( 'default-text' ).replace( '{{ parcelas }}', i ).replace( '{{ valor }}', Woo_Custom_Installments.getFormattedPrice( price ) ).replace( '{{ juros }}', Woo_Custom_Installments_Params.with_fee_label ) + '</th><th>' + Woo_Custom_Installments.getFormattedPrice( final_cost ) + '</th></tr>' );
		  }
  
		  i++;
		}
	  },
  
	  /**
	   * Formatted Price.
	   *
	   * @param {String} price
	   */
	  getFormattedPrice: function( price ) {
		'use strict';
  
		var formatted_price = accounting.formatMoney( price, {
		  symbol : Woo_Custom_Installments_Params.currency_format_symbol,
		  decimal : Woo_Custom_Installments_Params.currency_format_decimal_sep,
		  thousand : Woo_Custom_Installments_Params.currency_format_thousand_sep,
		  precision : Woo_Custom_Installments_Params.currency_format_num_decimals,
		  format : Woo_Custom_Installments_Params.currency_format
		} );
  
		return formatted_price;
	  }
	};
  
	Woo_Custom_Installments.init();
});