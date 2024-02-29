(function ( $ ) {
	'use strict';

	/**
	 * Check if theme Machic is active, for adjust in button popup
	 * 
	 * @since 2.4.0
	 */
	jQuery( function($) {
		if ( $('body').hasClass('theme-machic') ) {
			$('#open-popup, #accordion-installments').appendTo('.product-price');
		}
	});


	/**
	 * Change discount in variation when discount per product is activated
	 * 
	 * @since 3.0.0
	 */
	$(document).on('found_variation', 'form.cart', function(event, variation) { 
		var variationPrice = variation.display_price;

		if (typeof wci_front_params !== 'undefined') {
			var enableDiscount = wci_front_params.enable_discount_per_unit;
			var discountMethod = wci_front_params.discount_per_unit_method;
			var discountAmount = parseFloat(wci_front_params.unit_discount_amount);
			var currencySymbol = wci_front_params.currency_symbol;
	
			if (enableDiscount === 'yes') {
				var priceElement = $('.woo-custom-installments-offer .discounted-price');
				var originalPrice = parseFloat(variationPrice);
	
				if (discountMethod === 'percentage') {
					var customPrice = originalPrice - (originalPrice * (discountAmount / 100));
				} else {
					var customPrice = originalPrice - discountAmount;
				}
	
				var formattedPrice = currencySymbol + customPrice.toFixed(2).replace('.', ',');
	
				priceElement.text(formattedPrice);
			}
		}
	});
 
}(jQuery));