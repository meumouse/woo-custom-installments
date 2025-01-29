/**
 * Accordion payment methods
 * 
 * @since 2.0.0
 * @version 5.3.0
 */
jQuery(document).ready( function($) {
	$(document).on('click', '.wci-accordion-header', function() {
		var content = jQuery(this).next('.wci-accordion-content');
		
		if (content.css('max-height') == '0px') {
			content.css('max-height', content.prop('scrollHeight') + 'px');
			content.parent('.wci-accordion-item').addClass('active');
			$('body').addClass('wci-accordion-active');
		} else {
			content.slideUp(350, function() {
				content.parent('.wci-accordion-item').removeClass('active');
				content.css('display', '');
				content.css('max-height', '0px');
				$('body').removeClass('wci-accordion-active');
			});
		}
	});
});