/**
 * Accordion installments
 * 
 * @since 2.0.0
 * @version 5.0.0
 */
jQuery( function() {
	jQuery('.accordion-header').click( function() {
		var content = jQuery(this).next('.accordion-content');
		
		if (content.css('max-height') == '0px') {
			content.css('max-height', content.prop('scrollHeight') + 'px');
			content.parent('.accordion-item').addClass('active');
		} else {
			content.slideUp(350, function() {
				content.parent('.accordion-item').removeClass('active');
				content.css('display', '');
				content.css('max-height', '0px');
			});
		}
	});
});