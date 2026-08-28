( function($) {
	"use strict";

	/**
	 * Get global parameters
	 *
	 * @since 5.5.9
	 */
	const params = window.wci_updates_params || {};

	/**
	 * Object variable for the manual update check on the plugins list
	 *
	 * @since 5.5.9
	 * @package MeuMouse.com
	 */
	var Updates = {

		/**
		 * Bind the check updates link
		 *
		 * @since 5.5.9
		 */
		init: function() {
			$(document).on( 'click', '.wci-check-updates', Updates.checkUpdates );
		},

		/**
		 * Loading spinner markup, using the WordPress admin class
		 *
		 * @since 5.5.9
		 * @returns {string}
		 */
		spinner: function() {
			return '<span class="spinner is-active" style="float:none;margin:0 4px 0 0;vertical-align:middle;"></span>';
		},

		/**
		 * Request a manual update check on the REST API
		 *
		 * @since 5.5.9
		 * @param {object} e | Click event
		 */
		checkUpdates: function(e) {
			e.preventDefault();

			var link = $(this);

			// ignore new clicks while a check is running
			if ( link.data('wci_checking') ) {
				return;
			}

			var original = link.html();

			link.data( 'wci_checking', true ).html( Updates.spinner() + params.i18n.checking );
			$('.wci-check-updates-result').remove();

			$.ajax({
				url: params.rest_url,
				method: 'POST',
				dataType: 'json',
				beforeSend: function(xhr) {
					xhr.setRequestHeader( 'X-WP-Nonce', params.nonces.wp_rest );
				},
			}).done( function(response) {
				Updates.showResult( link, response );
			}).fail( function(xhr) {
				if ( params.debug_mode ) {
					console.error( 'Woo Custom Installments: check updates request failed', xhr );
				}

				Updates.showResult( link, { message: params.i18n.error } );
			}).always( function() {
				link.removeData('wci_checking').html( original );
			});
		},

		/**
		 * Display the outcome of the check next to the link
		 *
		 * @since 5.5.9
		 * @param {object} link | Check updates link object
		 * @param {object} response | Response from the REST API
		 */
		showResult: function( link, response ) {
			var result = $('<span class="wci-check-updates-result"></span>');

			result.text( ' — ' + ( response.message || params.i18n.error ) );

			// offer the update right away when there is one
			if ( response.has_update && response.update_url ) {
				result.append(' ').append( $('<a></a>').attr( 'href', response.update_url ).text( params.i18n.update_now ) );
			}

			link.after( result );
		},
	};

	$(document).ready( function() {
		Updates.init();
	});
})(jQuery);
