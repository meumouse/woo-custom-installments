( function($) {
	'use strict';

	/**
	 * Change discount in variation when discount per product is activated
	 * 
	 * @since 3.0.0
	 * @version 4.5.0
	 */
	$(document).on('found_variation', 'form.cart', function(e, variation) {
		if ( wci_front_params.enable_discount_per_unit === 'yes' ) {
			var variation_price = variation.display_price;
			var original_price = parseFloat(variation_price);
			var discount_amount = parseFloat(wci_front_params.unit_discount_amount);
			var currency_symbol = wci_front_params.currency_symbol;
			var price_element = $('.woo-custom-installments-offer .discounted-price');

			if ( wci_front_params.discount_per_unit_method === 'percentage' ) {
				var custom_price = original_price - (original_price * (discount_amount / 100));
			} else {
				var custom_price = original_price - discount_amount;
			}

			var formatted_price = currency_symbol + custom_price.toFixed(2).replace('.', ',');

			price_element.text(formatted_price);
		}
	});
}(jQuery));