( function($) {
	"use strict";

	/**
	 * Get global parameters
	 *
	 * @since 6.0.0
	 */
	const params = window.wci_migration_params || {};

	/**
	 * Object variable for the migration screen
	 *
	 * One request per step, chained: the server decides what runs next, and this
	 * only paints what it answers. That keeps the order of the steps in one
	 * place, and means a store owner who reloads the page resumes instead of
	 * starting over.
	 *
	 * @since 6.0.0
	 * @package MeuMouse.com
	 */
	var Migration = {

		/**
		 * Whether a step request is in flight
		 *
		 * @since 6.0.0
		 */
		running: false,

		/**
		 * Paint the state the page was rendered with, and carry on from there
		 *
		 * @since 6.0.0
		 */
		init: function() {
			if ( ! $('#wci-migration-panel').length ) {
				return;
			}

			var state = params.state || {};

			Migration.paint( state );

			if ( Migration.pending( state ) ) {
				Migration.advance();
			}
		},

		/**
		 * Whether there is a step left to run right now
		 *
		 * A failure stops the chain on purpose: retrying the same step in a
		 * loop would hammer whatever refused.
		 *
		 * @since 6.0.0
		 * @param {object} state | State as answered by the server
		 * @returns {boolean}
		 */
		pending: function( state ) {
			return ! state.done && ! state.blocked && ! state.failed;
		},

		/**
		 * Run the next step
		 *
		 * @since 6.0.0
		 */
		advance: function() {
			if ( Migration.running ) {
				return;
			}

			Migration.running = true;
			$('#wci-migration-actions').hide();

			$.ajax({
				url: params.rest_url,
				method: 'POST',
				dataType: 'json',
				beforeSend: function(xhr) {
					xhr.setRequestHeader( 'X-WP-Nonce', params.nonces.wp_rest );
				},
			}).done( function(state) {
				Migration.running = false;
				Migration.paint( state );

				if ( Migration.pending( state ) ) {
					Migration.advance();

					return;
				}

				Migration.settle( state );
			}).fail( function(xhr) {
				if ( params.debug_mode ) {
					console.error( 'Woo Custom Installments: migration step failed', xhr );
				}

				Migration.running = false;

				// The last step switches this plugin off, which takes the route
				// with it: a request that fails right after it succeeded is the
				// expected end, not an error.
				Migration.status( params.i18n.request_error );
				$('#wci-migration-actions').show();
			});
		},

		/**
		 * Mark the steps and say what is happening
		 *
		 * @since 6.0.0
		 * @param {object} state | State as answered by the server
		 */
		paint: function( state ) {
			var steps = state.steps || [];
			var current = -1;

			$.each( steps, function( index, step ) {
				if ( step.id === state.step ) {
					current = index;
				}
			});

			$('.wci-migration-step').each( function( index ) {
				var item = $(this);

				item.removeClass('is-done is-active is-failed');

				if ( state.done || index < current ) {
					item.addClass('is-done');

					return;
				}

				if ( index === current ) {
					item.addClass( state.failed || state.blocked ? 'is-failed' : 'is-active' );
				}
			});

			Migration.status( state.failed || state.blocked ? state.message : state.step_label );
		},

		/**
		 * Write the status line
		 *
		 * @since 6.0.0
		 * @param {string} message | What to say
		 */
		status: function( message ) {
			$('#wci-migration-status').text( message || '' );
		},

		/**
		 * Render the end of the migration, however it ended
		 *
		 * @since 6.0.0
		 * @param {object} state | State as answered by the server
		 */
		settle: function( state ) {
			if ( ! state.done ) {
				$('#wci-migration-actions').show();

				return;
			}

			// This plugin has just been deactivated, so the screen cannot be
			// reloaded to show the server-rendered result: it no longer exists.
			var panel = $('#wci-migration-panel').empty().addClass('is-done');

			panel.append( $('<h2></h2>').text( params.i18n.done_title ) );
			panel.append( $('<p></p>').text( params.i18n.done_body ) );

			if ( state.pro_message ) {
				panel.append( $('<p class="wci-migration-note"></p>').text( state.pro_message ) );
			}

			panel.append(
				$('<p></p>').append(
					$('<a class="button button-primary"></a>')
						.attr( 'href', state.settings_url || params.settings_url )
						.text( params.i18n.open_settings )
				)
			);
		},
	};

	$(document).ready( function() {
		Migration.init();
	});
})(jQuery);
