(function ( $ ) {
	'use strict';

	/**
	 * Update checkout on change values
	 * 
	 * @since 2.0.0
	 */
	$( function() {
		$( document.body ).on( 'change', 'input[name="payment_method"]', function () {
			$( 'body' ).trigger( 'update_checkout' );
		});
	});


	/**
	 * Accordion installments
	 * 
	 * @since 2.0.0
	 */
	jQuery( function($) {
		$('.accordion-header').click(function() {
			let content = $(this).next('.accordion-content');
			
			if (content.css('max-height') == '0px') {
				content.css('max-height', content.prop('scrollHeight') + 'px');
				content.parent('.accordion-item').addClass('active');
			} else {
				content.css('max-height', '0px');
				content.parent('.accordion-item').removeClass('active');
			}
		});
	});


	/**
	 * Modal installments
	 * 
	 * @since 2.0.0
	 */
	jQuery( function($) {
		const openPopupButton = $('#open-popup');
		const popupContainer = $('#popup-container');
		const closePopup = $('#close-popup');
		
		openPopupButton.on('click', function() {
		popupContainer.addClass('show');
		});
		
		popupContainer.on('click', function(event) {
		if (event.target === this) {
			$(this).removeClass('show');
		}
		});

		closePopup.on('click', function() {
			popupContainer.removeClass('show');
		})
	});


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
	
		// Verifique se os parâmetros estão definidos
		if (typeof wci_front_params !== 'undefined') {
			var enableDiscount = wci_front_params.enable_discount_per_unit;
			var discountMethod = wci_front_params.discount_per_unit_method;
			var discountAmount = parseFloat(wci_front_params.unit_discount_amount);
			var currencySymbol = wci_front_params.currency_symbol;
	
			if (enableDiscount === 'yes') {
				var priceElement = $('.woo-custom-installments-offer .discounted-price');
				var originalPrice = parseFloat(variationPrice);
	
				if (discountMethod === 'percentage') {
					// Calcular o preço com desconto com base na porcentagem
					var customPrice = originalPrice - (originalPrice * (discountAmount / 100));
				} else {
					// Calcular o preço com desconto com base no valor fixo
					var customPrice = originalPrice - discountAmount;
				}
	
				var formattedPrice = currencySymbol + customPrice.toFixed(2).replace('.', ',');
	
				priceElement.text(formattedPrice);
			}
		}
	});
 
}(jQuery));