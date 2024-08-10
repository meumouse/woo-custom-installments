/**
 * Modal installments
 * 
 * @since 2.0.0
 * @version 4.5.0
 */
jQuery(document).ready( function($) {
	const trigger = $('#wci-open-popup');
	const container = $('#wci-popup-container');
	const close = $('#wci-close-popup');
	
	trigger.on('click', function() {
		container.addClass('show');
	});
	
	container.on('click', function(e) {
		if (e.target === this) {
			$(this).removeClass('show');
		}
	});

	close.on('click', function() {
		container.removeClass('show');
	});
});