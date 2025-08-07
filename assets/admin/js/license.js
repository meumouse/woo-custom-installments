( function($) {
    "use strict";

	/**
	 * Get global parameters
	 * 
	 * @since 5.4.0
	 */
	const params = window.wci_license_params || {};

	/**
	 * Object variable for license actions
	 * 
	 * @since 5.4.0
	 * @package MeuMouse.com
	 */
	var License = {

        /**
		 * Keep button width and height state
		 * 
		 * @since 5.4.0
		 * @param {object} btn | Button object
		 * @returns {object}
		 */
		keepButtonState: function(btn) {
			var btn_width = btn.width();
			var btn_height = btn.height();
			var btn_html = btn.html();
	  
			// keep original width and height
			btn.width(btn_width);
			btn.height(btn_height);
	  
			return {
				width: btn_width,
				height: btn_height,
				html: btn_html,
			};
	  	},

        /**
		 * Hide toast on click button or after 5 seconds
		 * 
		 * @since 5.4.0
		 */
		hideToasts: function() {
			$(document).on('click', '.hide-toast', function() {
				$('.toast').fadeOut('fast');
			});
	
			setTimeout( function() {
				$('.toast').fadeOut('fast');
			}, 3000);
		},

        /**
		 * Show toast messages in wrapper
		 * 
		 * @since 5.4.0
		 * @param {string} type - success, danger, warning
		 * @param {string} header - title of the toast
		 * @param {string} body - body of the toast
		 */
		displayToast: function(type, header, body) {
			if ( type === 'error' ) {
				type = 'danger';
			}

			const toast = `<div class="toast toast-${type} show">
				<div class="toast-header bg-${type} text-white">
					<svg class="icon icon-white me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M9.999 13.587 7.7 11.292l-1.412 1.416 3.713 3.705 6.706-6.706-1.414-1.414z"></path></svg>
					<span class="me-auto">${header}</span>
					<button class="btn-close btn-close-white ms-2 hide-toast" type="button" aria-label="${params.i18n.aria_label_modal}"></button>
				</div>
				<div class="toast-body">${body}</div>
			</div>`;

			$('.woo-custom-installments-tab-wrapper').before(toast);

			setTimeout(() => {
				$(`.toast-${type}`).fadeOut('fast', function() {
					$(this).remove();
				});
			}, 3000);
		},

        /**
		 * Active license process
		 * 
		 * @since 5.4.0
		 */
		activateLicense: function() {
			$('#woo_custom_installments_active_license').on('click', function(e) {
				e.preventDefault();

				let btn = $(this);
				let btn_state = License.keepButtonState(btn);

                // send ajax request
				$.ajax({
					url: params.ajax_url,
					method: 'POST',
					data: {
						action: 'wci_active_license',
						license_key: $('#woo_custom_installments_license_key').val(),
					},
					beforeSend: function() {
						btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
					},
					success: function(response) {
						try {
							if ( response.status === 'success' ) {
                                License.displayToast( 'success', response.toast_header_title, response.toast_body_title );

                                setTimeout( function() {
                                    location.reload();
                                }, 1000);
							} else {
                                License.displayToast( 'error', response.toast_header_title, response.toast_body_title );
							}
						} catch (error) {
							console.log(error);
						}
					},
					complete: function() {
						btn.prop('disabled', false).html(btn_state.html);
					},
					error: function(xhr, status, error) {
						alert('AJAX error: ' + error);
					},
				});
			});
		},

		/**
		 * Handle deactivation of license via AJAX
		 * 
		 * @since 4.5.0
         * @version 5.4.0
		 */
		deactivateLicense: function() {
			$(document).on('click', '#woo_custom_installments_deactive_license', function(e) {
				e.preventDefault();

				// wait for confirmation
				if ( ! confirm( params.i18n.confirm_deactivate_license ) ) {
					return;
				}

				let btn = $(this);
				let btn_state = License.keepButtonState(btn);

				// send AJAX request
				$.ajax({
					url: params.ajax_url,
					type: 'POST',
					data: {
						action: 'wci_deactive_license_action',
					},
					beforeSend: function() {
						btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
					},
					success: function(response) {
						try {
							if ( response.status === 'success' ) {
								btn.html(btn_state.html);

								// display notice
								License.displayToast( 'warning', response.toast_header_title, response.toast_body_title );

								// reload page after 1 second
								setTimeout(() => {
									location.reload()
								}, 1000);
							} else {
								btn.html(btn_state.html);

								License.displayToast( 'danger', response.toast_header_title, response.toast_body_title );
							}
						} catch (error) {
							console.log(error);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.error('AJAX Error:', textStatus, errorThrown);
					},
				});
			});
		},

		/**
		 * Handle alternative license upload via drag & drop
		 * 
         * @since 4.3.0
		 * @version 5.4.0
		 */
		handleLicenseUpload: function() {
			const dropzone = $('#license_key_zone');
			const file_input = $('#upload_license_key');

			dropzone.on('dragover dragleave', function(e) {
				e.preventDefault();

				$(this).toggleClass('drag-over', e.type === 'dragover');
			});

			dropzone.on('drop', function(e) {
				e.preventDefault();
				const file = e.originalEvent.dataTransfer.files[0];

				if ( ! $(this).hasClass('file-uploaded') ) {
					License.processLicenseFile( file, $(this) );
				}
			});

			file_input.on('change', function(e) {
				const file = e.target.files[0];

				License.processLicenseFile( file, file_input.parents('.dropzone-license') );
			});
		},

		/**
		 * Process license file via AJAX
		 * 
		 * @since 4.3.0
         * @version 5.4.0
		 * @param {File} file - Uploaded license file
		 * @param {jQuery} dropzone - Dropzone container
		 */
		processLicenseFile: function(file, dropzone) {
			if ( ! file ) {
				return;
			}

			const filename = file.name;
			const form_data = new FormData();

			form_data.append('action', 'wci_alternative_activation_license');
			form_data.append('file', file);

			dropzone.children('.file-list').removeClass('d-none').text(filename);
			dropzone.addClass('file-processing');
			dropzone.append('<div class="spinner-border"></div>');
			dropzone.children('.drag-text, .drag-and-drop-file, .upload-license-key').addClass('d-none');

			// send AJAX request
			$.ajax({
				url: params.ajax_url,
				type: 'POST',
				data: form_data,
				processData: false,
				contentType: false,
				success: function(response) {
					try {
						if ( response.status === 'success' ) {
							License.displayToast( 'success', response.toast_header, response.toast_body );

							dropzone.addClass('file-uploaded').removeClass('file-processing').children('.spinner-border').remove();
							dropzone.append(`<div class="upload-notice d-flex flex-column align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><path fill="#22c55e" d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path fill="#22c55e" d="M9.999 13.587 7.7 11.292l-1.412 1.416 3.713 3.705 6.706-6.706-1.414-1.414z"></path></svg><span>${response.dropfile_message}</span></div>`);
							dropzone.children('.file-list').addClass('d-none');
							
							setTimeout(() => {
								location.reload();
							}, 1000);
						} else {
							License.displayToast( 'error', response.toast_header, response.toast_body );

							dropzone.addClass('invalid-file').removeClass('file-processing');
							dropzone.children('.spinner-border').remove();
							dropzone.children('.drag-text, .drag-and-drop-file, .upload-license-key').removeClass('d-none');
							dropzone.children('.file-list').addClass('d-none');
						}
					} catch (error) {
						console.log(error);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					dropzone.addClass('fail-upload').removeClass('file-processing');
					console.error('AJAX Error:', textStatus, errorThrown);
				},
			});
		},

		/**
		 * Sync license infoormation
		 * 
		 * @since 5.5.0
		 */
		syncLicense: function() {
			$(document).on('click', '#woo_custom_installments_refresh_license', function(e) {
				e.preventDefault();

				let btn = $(this);
				let btn_state = License.keepButtonState(btn);

				// send AJAX request
				$.ajax({
					url: params.ajax_url,
					type: 'POST',
					data: {
						action: 'wci_sync_license_action',
					},
					beforeSend: function() {
						btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

						// add placeholder animation on each license details item
						$('.license-details-item').each( function() {
							$(this).addClass('placeholder-content');
						});
					},
					success: function(response) {
						try {
							if ( response.status === 'success' ) {
								// display notice
								License.displayToast( 'success', response.toast_header_title, response.toast_body_title );
							} else {
								License.displayToast( 'danger', response.toast_header_title, response.toast_body_title );
							}
						} catch (error) {
							console.log(error);
						}
					},
					complete: function() {
						btn.prop('disabled', false).html(btn_state.html);
						$('.license-details-item').removeClass('placeholder-content');
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.error('AJAX Error:', textStatus, errorThrown);
					},
				});
			});
		},

		/**
		 * Initialize modules
		 * 
		 * @since 5.4.0
		 */
		init: function() {
            this.hideToasts();
            this.activateLicense();
			this.deactivateLicense();
            this.handleLicenseUpload();
			this.syncLicense();
		},
	};

	// Initialize on document ready
	$(document).ready( function() {
		License.init();
	});
})(jQuery);