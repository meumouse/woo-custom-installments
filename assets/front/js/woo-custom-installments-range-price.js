/**
 * Replace range price
 * 
 * @since 2.8.0
 * @version 3.8.0
 */
jQuery(document).ready( function($) {
	var get_price = get_original_price();
	var wci_siblings = $('.range-price .price').siblings('.woo-custom-installments-group');
	var wci_children = $('.range-price .price').children('.original-price').siblings('.woo-custom-installments-group');

	// prevent duplicate containers
    if (wci_children.length > 0 && wci_siblings.length > 0) {
		wci_siblings.hide();
    }

	function get_original_price() {
		var container_price = $('.original-price').parent('.price');

		container_price.closest('div').addClass('range-price');
		original_price = container_price.html();

        return original_price;
    }

	$(document).on('found_variation', 'form.variations_form', function() {
	  var variation_price = $('.woocommerce-variation-price .price').html();

	  $('.summary .price, .range-price .price').html(variation_price);
	});

	$('form.variations_form').on('change', 'select', function() {
		if ($(this).val() === '') {
		  $('.summary .price, .range-price .price').html(get_price);
		}
	});
  
	$('a.reset_variations').click( function(e) {
	  e.preventDefault();
	  $('.summary .price, .range-price .price').html(get_price);
	});
});