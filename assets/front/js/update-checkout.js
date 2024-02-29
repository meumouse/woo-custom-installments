/**
 * Update checkout on change values
 * 
 * @since 2.0.0
 */
jQuery(document).ready( function($) {
	$(document.body).on('change', 'input[name="payment_method"]', function() {
		$('body').trigger('update_checkout');
	});
});