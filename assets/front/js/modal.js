/**
 * Modal installments
 * 
 * @since 2.0.0
 * @version 5.3.0
 */
jQuery(document).ready( function($) {
	$(document).on('click touchstart', 'button.wci-open-popup', function(e) {
		e.preventDefault();

		$(this).siblings('.wci-popup-container').addClass('show');
		$('body').addClass('wci-modal-active');
	});
	
	$(document).on('click touchstart', '.wci-popup-container', function(e) {
		if (e.target === this) {
			$(this).removeClass('show');
			$('body').removeClass('wci-modal-active');
		}
	});

	$(document).on('click touchstart', 'button.wci-close-popup', function(e) {
		e.preventDefault();
		
		$('.wci-popup-container').removeClass('show');
		$('body').removeClass('wci-modal-active');
	});
});