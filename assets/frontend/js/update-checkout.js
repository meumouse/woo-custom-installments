/**
 * Update checkout on change payment method
 * 
 * @since 2.0.0
 */
jQuery(document).ready( function($) {
	$(document.body).on('change', 'input[name="payment_method"]', function() {
		$('body').trigger('update_checkout');
	});
});