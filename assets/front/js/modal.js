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