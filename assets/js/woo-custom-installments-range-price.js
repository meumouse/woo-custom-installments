/**
 * Replace range price
 * 
 * @since 2.8.0
 */
jQuery.noConflict()( function($) {
	let originalPrice = $('.summary .price').html();
  
	$(document).on('found_variation', 'form.variations_form', function(event, variation) {
	  var variationPrice = $('.woocommerce-variation-price .price').html();

	  $('.summary .price').html(variationPrice);
	  $('.summary .price').siblings('.woo-custom-installments-group').hide();
	});

	$('form.variations_form').on('change', 'select', function() {
		if ($(this).val() === '') {
		  $('.summary .price').html(originalPrice);
		  $('.summary .price').siblings('.woo-custom-installments-group').show();
		}
	});
  
	$('a.reset_variations').click( function(event) {
	  event.preventDefault();
	  $('.summary .price').html(originalPrice);
	  $('.summary .price').siblings('.woo-custom-installments-group').show();
	});
});  