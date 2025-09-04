( function($) {
    "use strict";

	/**
	 * Get global parameters
	 * 
	 * @since 5.4.0
	 */
	const params = window.wci_params || {};

	if ( params.debug_mode ) {
		console.log( 'WCI admin params: ', params );
	}

	/**
	 * Object variable for settings page
	 * 
	 * @since 5.4.0
	 * @package MeuMouse.com
	 */
	var Settings = {

		/**
		 * Global state for storing styles by device and element
		 * 
		 * @since 5.4.0
		 */
		styles_state: {
			desktop: {},
			mobile: {},
		},

		/**
		 * Initialize styles state from DOM inputs
		 * 
		 * @since 5.4.0
		 */
		initializeStyles: function() {
			$('.design-control-input').each( function() {
				const input = $(this);
				const value = input.val()?.trim() || '';
				const device = input.data('device');
				const element = input.data('element');
				const property = input.data('property');
				const position = input.data('box-position') || '';
				const unit = input.closest('.design-control-group').find('.design-control-select').val() || '';
				const full_property = position ? `${property}-${position}` : property;
				const full_value = value && unit ? `${value}${unit}` : value;

				if (value) {
					Settings.updateStyles( device, element, full_property, full_value );
				}
			});
		},

		/**
		 * Activate tabs
		 * 
		 * @since 2.0.0
		 * @version 5.4.0
		 */
		activateTabs: function() {
			/**
			 * Activate the tab based on the index stored in localStorage
			 * 
			 * @since 2.0.0
			 * @version 5.4.0
			 */
			function activate_current_tab() {
				// Reads the index stored in localStorage, if it exists
				let active_tab_index = localStorage.getItem('woo_custom_installments_active_tab_index');

				if ( active_tab_index === null ) {
					// If it is null, activate the general tab
					$('.woo-custom-installments-tab-wrapper a.nav-tab[href="#general"]').click();
				} else {
					$('.woo-custom-installments-tab-wrapper a.nav-tab').eq(active_tab_index).click();
				}
			}
			
			setTimeout( ()=> {
				activate_current_tab();
			}, 100);

			// on click tab
			$(document).on('click', '.woo-custom-installments-tab-wrapper a.nav-tab', function() {
				// Stores the index of the active tab in localStorage
				let tab_index = $(this).index();

				// Stores the index in localStorage
				localStorage.setItem('woo_custom_installments_active_tab_index', tab_index);
				
				let attr_href = $(this).attr('href');
				
				$('.woo-custom-installments-tab-wrapper a.nav-tab').removeClass('nav-tab-active');
				$('.woo-custom-installments-form .nav-content').removeClass('active');
				$(this).addClass('nav-tab-active');
				$('.woo-custom-installments-form').find(attr_href).addClass('active');
				
				return false;
			});
		},

		/**
		 * Save plugin settings
		 * 
		 * @since 1.0.0
		 * @version 5.4.0
		 */
		saveSettings: function() {
			let settings_form = $('form[name="woo-custom-installments"]');
			let original_values = settings_form.serialize();

			// on click save button
			$('#woo_custom_installments_save_options').on('click', function(e) {
				e.preventDefault();
				
				let btn = $(this);
				let btn_state = Settings.keepButtonState(btn);
				
				// send AJAX request
				$.ajax({
					url: params.ajax_url,
					type: 'POST',
					data: {
						action: 'wci_save_options',
						form_data: settings_form.serialize(),
						security: params.nonces.save_settings,
					},
					beforeSend: function() {
						btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
					},
					success: function(response) {
						if ( params.debug_mode ) {
							console.log(response);
						}

						try {
							if ( response.status === 'success' ) {
								original_values = settings_form.serialize();

								// show success notification
								Settings.displayToast('success', response.toast_header_title, response.toast_body_title);
		
								// update custom installments loop
								Settings.updateCustomInstallmentsLoop();
							} else {
								Settings.displayToast('error', response.toast_header_title, response.toast_body_title);
							}
						} catch (error) {
							console.log(error);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.error('AJAX Error:', textStatus, errorThrown);
					},
					complete: function() {
						btn.html(btn_state.html);
					},
				});
			});

			/**
			 * Monitor changes in the form
			 * 
			 * @since 4.5.0
			 * @version 5.2.5
			 */
			settings_form.on('change input', 'input, select, textarea', function() {
				if ( settings_form.serialize() !== original_values ) {
					$('#woo_custom_installments_save_options').prop('disabled', false);
				} else {
					$('#woo_custom_installments_save_options').prop('disabled', true);
				}
			});
		},

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
		 * Validate input numbers
		 * 
		 * @since 2.0.0
		 * @version 5.5.4
		 */
		validateInputNumbers: function() {
			let inputs = $('.allow-numbers-be-1, .allow-numbers-be-0');
		
			inputs.on('input', function() {
				let $input = $(this);
				let value = parseFloat($input.val()) || 0;
				let min_value = $input.hasClass('allow-numbers-be-1') ? 1 : 0;
		
				if ( value < min_value ) {
					$input.val(min_value);
				}
			});

			// on blur, if empty, set to 0
			$('#max_qtd_installments_without_fee').on('blur', function() {
				if ( $(this).val().trim() === '' ) {
					$(this).val(0);
				}
			});
		},

		/**
		 * Update global styles and apply CSS to preview
		 *
		 * @since 5.4.0
		 * @param {string} device - Device type (desktop, mobile)
		 * @param {string} element - Target element class
		 * @param {string} property - CSS property
		 * @param {string} value - Property value
		 */
		updateStyles: function(device, element, property, value) {
			if ( ! property || ! value ) {
				return;
			}
			
			if ( ! this.styles_state[device][element] ) {
				this.styles_state[device][element] = {};
			}

			this.styles_state[device][element][property] = value;
			this.updateCSS(device, element);
		},

		/**
		 * Update the <style> tag dynamically for an element
		 * 
		 * @since 5.4.0
		 * @param {string} device - Device type
		 * @param {string} element - Target element class
		 */
		updateCSS: function(device, element) {
			const elementId = `${device}-${element}-styles`;
			$(`#${elementId}`).remove();

			const styles = { ...this.styles_state[device][element] };
			const composedStyles = {};

			['margin', 'padding', 'border-radius'].forEach((prop) => {
				const top = styles[`${prop}-top`] || '0';
				const right = styles[`${prop}-right`] || '0';
				const bottom = styles[`${prop}-bottom`] || '0';
				const left = styles[`${prop}-left`] || '0';

				if (top || right || bottom || left) {
					composedStyles[prop] = `${top} ${right} ${bottom} ${left}`;
				}

				delete styles[`${prop}-top`];
				delete styles[`${prop}-right`];
				delete styles[`${prop}-bottom`];
				delete styles[`${prop}-left`];
			});

			Object.assign(styles, composedStyles);

			const css = Object.entries(styles).map(([k, v]) => `${k}: ${v} !important;`).join(' ');

			if (css) {
				$('<style>', {
					id: elementId,
					text: `.preview.${element}[data-device="${device}"] { ${css} }`,
				}).appendTo('head');
			}
		},

		/**
		 * Update custom installments loop HTML on change values
		 * 
		 * @since 5.4.0
		 */
		updateCustomInstallmentsLoop: function() {
			let limit_installments = parseInt( $('#max_qtd_installments').val() );
			let limit_installments_without_fee = parseInt( $('#max_qtd_installments_without_fee').val() );
			let loop_html = '';

			for ( let i = limit_installments_without_fee + 1; i <= limit_installments; i++ ) {
				let current_custom_fee = parseFloat( $(`input[name="custom_fee_installments[${i}][amount]"]`).val() ) || 0;

				loop_html += `<div class="input-group mb-2" data-installment="${i}">`;
					loop_html += `<input class="custom-installment-first small-input form-control" type="text" disabled value="${i}"/>`;
					loop_html += `<input class="custom-installment-secondary small-input form-control allow-number-and-dots" type="text" placeholder="1.0" name="custom_fee_installments[${i}][amount]" id="custom_fee_installments[${i}]" value="${current_custom_fee}" />`;
				loop_html += `</div>`;
			}

			$('#custom-installments-fieldset-custom-installments').html(loop_html);
		},

		/**
		 * Change visibility of containers based on checkbox state
		 * 
		 * @since 5.4.0
		 * @version 5.5.1
		 * @param {string|object} trigger | Selector or jQuery object for the checkbox
		 * @param {string} container | Selector for the container
		 * @param {boolean} visibility | If true, container is visible when checked. If false, container is visible when unchecked.
		 */
		changeVisibility: function( trigger, container, visibility = true ) {
			let $trigger = $(trigger);

			// set initial visibility based on checkbox state
			let checked = $trigger.prop('checked');

			$(container).toggleClass('d-none', visibility ? !checked : checked);

			// update state on click
			$trigger.on('click', function() {
				let isChecked = $(this).prop('checked');

        		$(container).toggleClass('d-none', visibility ? !isChecked : isChecked);
			});

			Settings.updatePaymentFormsVisibility();
		},

		/**
		 * Update visibility of payment form container if any method is checked
		 * 
		 * @since 5.4.0
		 */
		updatePaymentFormsVisibility: function() {
			let any_checked = $('#enable_pix_method_payment_form').prop('checked') ||
				$('#enable_ticket_method_payment_form').prop('checked') ||
				$('#enable_credit_card_method_payment_form').prop('checked') ||
				$('#enable_debit_card_method_payment_form').prop('checked');

			$('.container-separator.payment-forms').toggleClass('d-none', ! any_checked);
		},

		/**
		 * Change symbol of discount fields based on selected method (percentage/fixed)
		 * 
		 * @since 5.4.0
		 * @param {jQuery} select - Select element
		 * @param {jQuery} symbol_element - Symbol container
		 */
		changeSymbolIcon: function(select, symbol_element) {
			let selected_value = select.val();

			if ( selected_value === 'percentage' ) {
				symbol_element.html('%');
			} else if ( selected_value === 'fixed' ) {
				symbol_element.html( params.currency_symbol );
			}
		},

		/**
		 * Apply change of icon in loop of method discount inputs
		 * 
		 * @since 5.4.0
		 * @param {jQuery} $row - Row containing select and icon
		 */
		changeSymbolDiscounts: function($row) {
			let select = $row.find('.get-discount-method-payment-method');
			let symbol_element = $row.find('.discount-method-result-payment-method');

			select.change( function() {
				Settings.changeSymbolIcon(select, symbol_element);
			});

			Settings.changeSymbolIcon(select, symbol_element);
		},

		/**
		 * Apply change of icon in loop of method interest inputs
		 * 
		 * @since 5.4.0
		 * @param {jQuery} $row - Row containing select and icon
		 */
		changeSymbolInterests: function($row) {
			let select = $row.find('.get-interest-method-payment-method');
			let symbol_element = $row.find('.interest-method-result-payment-method');

			select.change( function() {
				Settings.changeSymbolIcon(select, symbol_element);
			});

			Settings.changeSymbolIcon(select, symbol_element);
		},

		/**
		 * Refresh styles for a specific element
		 * 
		 * @since 5.4.0
		 * @param {string} device 
		 * @param {string} element 
		 */
		refreshStylesForElement: function(device, element) {
			if ( ! this.styles_state[device][element] ) {
				this.styles_state[device][element] = {};
			}

			$(`.design-control-input[data-device="${device}"][data-element="${element}"]`).each((i, el) => {
				const $el = $(el);
				const value = $el.val().trim();
				const prop = $el.data('property');
				const pos = $el.data('box-position') || '';
				const unit = $el.closest('.design-control-group').find('.design-control-select').val() || '';
				const fullProp = pos ? `${prop}-${pos}` : prop;
				const fullValue = value && unit ? `${value}${unit}` : value;

				if (value) {
					this.styles_state[device][element][fullProp] = fullValue;
				}
			});

			this.updateCSS(device, element);
		},

		/**
		 * Bind events to inputs, selects and device tabs
		 * 
		 * @since 5.4.0
		 */
		bindStyleEvents: function() {
			const self = this;

			$(document).on('input change keyup', '.design-control-input', function() {
				const $input = $(this);
				const device = $input.data('device');
				const element = $input.data('element');
				self.refreshStylesForElement(device, element);
			});

			$(document).on('change', '.design-control-select', function() {
				const $select = $(this);
				const device = $select.data('device');
				const element = $select.data('element');
				self.refreshStylesForElement(device, element);
			});

			$(document).on('input change', '.set-font-weight, .set-font-color, .set-background-color, .set-font-size-unit', function() {
				const $input = $(this);
				const device = $input.data('device');
				const element = $input.data('element');
				const property = $input.data('property');
				const value = $input.val();
				self.updateStyles(device, element, property, value);
				self.refreshStylesForElement(device, element);
			});

			$(document).on('click', '.design-device-wrapper .nav-tab', function() {
				const $tab = $(this);
				const selectedDevice = $tab.data('device');

				$tab.siblings('.nav-tab').removeClass('nav-tab-active');
				$tab.addClass('nav-tab-active');

				$('.design-device-content .nav-content').removeClass('show');
				$(`.design-device-content .nav-content[data-device="${selectedDevice}"]`).addClass('show');
			});
		},

		/**
		 * Show toast messages in wrapper
		 * 
		 * @since 5.4.0
		 * @version 5.5.4
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
					<button class="btn-close btn-close-white ms-2" type="button" aria-label="${params.i18n.aria_label_modal}"></button>
				</div>
				<div class="toast-body">${body}</div>
			</div>`;

			$('.woo-custom-installments-tab-wrapper').before(toast);

			setTimeout(() => {
				$(`.toast-${type}`).fadeOut('fast', function() {
					$(this).remove();
				});
			}, 3000);

			$(document).on('click', '.toast .btn-close', function() {
				$('.toast.show').fadeOut('fast');
			});
		},
		
		/**
		 * Display modal based on Bootstrap
		 * 
		 * @since 3.8.0
		 * @version 5.4.0
		 * @param {string|jQuery} trigger - The element that triggers the popup
		 * @param {string|jQuery} container - The popup container element
		 * @param {string|jQuery} close - The element that closes the popup
		 */
		displayModal: function(trigger, container, close) {
			trigger.on('click', function(e) {
				e.preventDefault();
				container.addClass('show');
			});

			container.on('click', function(e) {
				if (e.target === this) {
					container.removeClass('show');
				}
			});

			close.on('click', function(e) {
				e.preventDefault();
				container.removeClass('show');
			});
		},

		/**
		 * Initialize modals
		 * 
		 * @since 5.4.0
		 * @version 5.5.4
		 */
		initializeModals: function() {
			Settings.displayModal( $('#wci_reset_settings_trigger'), $('#wci_reset_settings_container'), $('#wci_close_reset') );
			Settings.displayModal( $('#custom_product_price_trigger'), $('#custom_product_price_container'), $('#custom_product_price_close') );
			Settings.displayModal( $('#center_group_elements_trigger'), $('#center_group_elements_container'), $('#center_group_elements_close') );

			if ( params.license_valid ) {
				Settings.displayModal( $('#remove_price_range_settings_trigger'), $('#remove_price_range_settings_container'), $('#remove_price_range_settings_close') );
				Settings.displayModal( $('#set_custom_fee_trigger'), $('#set_custom_fee_container'), $('#set_custom_fee_close') );
				Settings.displayModal( $('#discount_per_quantity_trigger'), $('#discount_per_quantity_container'), $('#discount_per_quantity_close') );
				Settings.displayModal( $('.manage-credit-card-trigger'), $('.manage-credit-card-container'), $('.close-manage-credit-card') );
				Settings.displayModal( $('.manage-debit-card-trigger'), $('.manage-debit-card-container'), $('.close-manage-debit-card') );
			}

			// each modal for edit elements
			$('.edit-elements-design').each( function() {
				let trigger = $('#' + $(this).find('.modal-trigger').attr('id') );
				let container = $('#' + $(this).find('.popup-container').attr('id') );
				let close = $('#' + $(this).find('.popup-container').find('.btn-close').attr('id') );
	
				Settings.displayModal( trigger, container, close );
			});
		},

		/**
		 * Update icon preview on input change
		 * 
		 * @since 5.4.0
		 */
		changeIconClass: function() {
			$('.set-icon-class').on('input change keyup paste', function() {
				const icon = $(this).val();
				const container = $(this).closest('.icon-class-container');

				container.find('.icon-preview').removeClass().addClass(`icon-preview fs-4 ${icon}`);

				$(this).closest('.popup-content').find('.preview i').removeClass().addClass(`me-1 ${icon}`);
				$('.popup-container.show').parent('div').siblings('.preview').children('i').removeClass().addClass(`me-1 ${icon}`);
			});
		},

		/**
		 * Initialize sortable UI for ordering elements
		 * 
		 * @since 5.4.0
		 */
		sortableElements: function() {
			function sort_items() {
				let list = $('#reorder_wci_elements ul.sortable');
				let items = list.children('li');

				items.sort((a, b) => {
					let aValue = parseInt($(a).find('input.change-priority').val());
					let bValue = parseInt($(b).find('input.change-priority').val());
					return aValue - bValue;
				});

				items.detach().appendTo(list);
			}

			sort_items();

			$('#reorder_wci_elements .sortable').sortable({
				handle: '.handle',
				update: function(event, ui) {
					let list = $(ui.item).closest('ul');
					list.children('li').each(function(index) {
						$(this).find('input.change-priority').val(index + 1).change();
					});
				}
			});

			// handle UI feedback
			$('.tab-item .handle').on('mousedown', function(e) {
				if (e.which === 1) $(this).addClass('grabbing');
			});
			$('.tab-item .handle').on('mouseup', function() {
				$(this).removeClass('grabbing');
			});
			$(document).on('mouseup', function() {
				$('.tab-item .handle').removeClass('grabbing');
			});
		},

		/**
		 * Handle upload of icon images via wp.media
		 * 
		 * @since 5.4.0
		 */
		handleIconImage: function() {
			let file_frame;

			$('.get-icon-image').on('click', function(e) {
				e.preventDefault();
				const trigger = $(this);

				if ( file_frame ) {
					file_frame.open();
					return;
				}

				file_frame = wp.media({
					title: params.i18n.set_media_title,
					button: {
						text: params.i18n.use_this_media_title,
					},
					multiple: false,
				});

				file_frame.on('select', function() {
					const attachment = file_frame.state().get('selection').first().toJSON();

					trigger.siblings('.set-icon-image').val(attachment.url).trigger('change');
					trigger.siblings('.input-group-text').find('.icon-preview').attr('src', attachment.url);
				});

				file_frame.open();
			});
		},

		/**
		 * Toggle visibility between icon class and icon image
		 * 
		 * @since 5.4.0
		 */
		toggleIconFormatContainers: function() {
			if ( params.check_format_icons === 'class' ) {
				$('.icon-image-container').addClass('d-none');
				$('.icon-class-container').removeClass('d-none');
			} else {
				$('.icon-class-container').addClass('d-none');
				$('.icon-image-container').removeClass('d-none');
			}

			$('#icon_format_elements').change( function() {
				if ( $(this).val() === 'class' ) {
					$('.icon-image-container').addClass('d-none');
					$('.icon-class-container').removeClass('d-none');
				} else {
					$('.icon-class-container').addClass('d-none');
					$('.icon-image-container').removeClass('d-none');
				}
			});
		},

		/**
		 * Reset plugin settings via AJAX
		 * 
		 * @since 4.5.0
		 * @version 5.4.0
		 */
		resetPluginSettings: function() {
			$(document).on('click', '#confirm_reset_settings', function(e) {
				e.preventDefault();

				// wait for confirmation
				if ( ! confirm( params.i18n.confirm_reset_settings ) ) {
					return;
				}

				let btn = $(this);
				let btn_state = Settings.keepButtonState(btn);

				// send AJAX request
				$.ajax({
					url: params.ajax_url,
					type: 'POST',
					data: {
						action: 'reset_plugin_action',
					},
					beforeSend: function() {
						btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
					},
					success: function(response) {
						try {
							if (response.status === 'success') {
								btn.prop('disabled', false).html(btn_state.html);

								// close modal
								$('#wci_close_reset').click();

								// display toast
								Settings.displayToast('success', response.toast_header_title, response.toast_body_title);

								// reload page after 1 second
								setTimeout(() => location.reload(), 1000);
							} else {
								Settings.displayToast('danger', response.toast_header_title, response.toast_body_title);
							}
						} catch (error) {
							console.log(error);
						} finally {
							btn.prop('disabled', false).html(btn_state.html);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.error('AJAX Error:', textStatus, errorThrown);
					},
					complete: function() {
						btn.prop('disabled', false).html(btn_state.html);
					},
				});
			});
		},

		/**
		 * Show toast when connection is offline
		 * 
		 * @since 5.4.0
		 */
		detectOfflineConnection: function() {
			function showOfflineToast() {
				Settings.displayToast('warning', params.i18n.offline_toast_header, params.i18n.offline_toast_body);
			}

			function updateStatus() {
				if (navigator.onLine) {
					$('.toast-offline-connection').remove();
				} else {
					showOfflineToast();
				}
			}

			updateStatus();
			window.addEventListener('online', updateStatus);
			window.addEventListener('offline', updateStatus);
		},

		/**
		 * Check visibility with select component
		 * 
		 * @since 5.0.0
		 * @version 5.4.0
		 * @param {string} select | Select ID
		 * @param {Array} options | Target options for display container
		 * @param {string} container | Container to be displayed
		 * @package MeuMouse.com
		 */
		selectVisibilityController: function(select, options, container) {
			// Remove any existing 'change' event handlers
			$(document).off('change', select);

			// Adds a single 'change' event handler
			$(document).on('change', select, function() {
				var get_option = $(select).val();

				if (options.includes(get_option)) {
					$(container).removeClass('d-none');
				} else {
					$(container).addClass('d-none');
				}
			});

			// Initialize visibility on page load
			var initial_option = $(select).val();
			
			if (options.includes(initial_option)) {
				$(container).removeClass('d-none');
			} else {
				$(container).addClass('d-none');
			}
		},

		/**
		 * Initialize visibility controllers
		 * 
		 * @since 2.4.0
		 * @version 5.5.4
		 */
		initializeVisibilityControllers: function() {
			if ( params.license_valid.length && params.license_valid ) {
				// display price range settings
				Settings.changeVisibility( '#remove_price_range', '.starting-from, .remove-price-range-dep' );

				// Display remove price range settings
				Settings.changeVisibility('#remove_price_range', '.require-remove-price-range' );

				// Display default fee input
				Settings.changeVisibility( '#set_fee_per_installment', '#fee-global-settings', false );

				// Display modal settings for custom fee per installment
				Settings.changeVisibility( '#set_fee_per_installment', '#set_custom_fee_trigger' );

				// Display custom hook settings
				Settings.selectVisibilityController( '#hook_payment_form_single_product', ['custom_hook'], '.requires-custom-hook' );

				// Active text on active credit card method
				Settings.changeVisibility( '#enable_credit_card_method_payment_form', '.admin-container-credit-card' );

				// Active text on active debit card method
				Settings.changeVisibility( '#enable_debit_card_method_payment_form', '.admin-container-debit-card' );
			}
			
			// Enable all interest options
			Settings.changeVisibility( '#enable_all_interest_options', '.display-enable-all-interest-options' );
			
			// Enable all discount options
			Settings.changeVisibility( '#enable_all_discount_options', '.display-enable-all-discount-options' );

			// Active text on active pix method
			Settings.changeVisibility( '#enable_pix_method_payment_form', '.admin-container-transfers' );
		
			// Active text on active ticket method
			Settings.changeVisibility( '#enable_ticket_method_payment_form', '.admin-container-ticket' );
		
			// Display more settings after active discount per quantity
			Settings.changeVisibility( '#enable_functions_discount_per_quantity', '.discount-per-quantity-option' );

			// Display custom text after price
			Settings.changeVisibility( '#custom_text_after_price', '.tr-custom-text-after-price' );

			// Display discount ticket option
			Settings.changeVisibility( '#enable_ticket_method_payment_form', '.admin-discount-ticket-option' );

			// Display economy Pix hook option
			Settings.changeVisibility( '#enable_economy_pix_badge', '.economy-pix-dependency' );

			// Display custom price modal settings
			Settings.changeVisibility( '#custom_text_after_price', '.require-custom-product-price' );

			// Display custom product price container
			Settings.changeVisibility( '#add_discount_custom_product_price', '.require-add-discount-custom-product-price' );

			// Display center elements selectors settings
			Settings.changeVisibility( '#center_group_elements_loop', '.require-center-group-elements' );

			$('select.pro-version, select.pro-version-notice, input.pro-version, input.pro-version-notice').prop('disabled', true);
		},

		/**
		 * Helper color selector
		 * 
		 * @since 3.8.0
		 * @version 5.4.0
		 */
		colorSelector: function() {
			$('.input-color').each( function() {
				$(this).minicolors({
					control: $(this).attr('data-control') || 'hue',
					defaultValue: $(this).attr('data-defaultValue') || '',
					format: $(this).attr('data-format') || 'hex',
					keywords: $(this).attr('data-keywords') || '',
					inline: $(this).attr('data-inline') === 'true',
					letterCase: $(this).attr('data-letterCase') || 'lowercase',
					opacity: $(this).attr('data-opacity'),
					position: $(this).attr('data-position') || 'bottom',
					swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
					change: function(value, opacity) {
						if ( ! value ) {
							return;
						}

						if ( opacity ) value += ', ' + opacity;
					},
					theme: 'bootstrap',
				});
			});
	
			// reset color
			$('.reset-color').on('click', function(e) {
				e.preventDefault();
	
				var color_value = $(this).data('color');
	
				$(this).closest('.color-container').find('.input-color').minicolors('value', color_value);
			});
		},

		/**
		 * Display information when require license
		 * 
		 * @since 5.4.0
		 */
		requireLicenseInfo: function() {
			// display require license modal
			Settings.displayModal( $('.pro-version-notice'), $('#popup-pro-notice'), $('#close-pro-notice') );

			// when click on has license button
			$('#active_license_form').on('click', function(e) {
				e.preventDefault();

				$('#popup-pro-notice').removeClass('show'); // close modal
				$('a.nav-tab[href="#about"]').click(); // active about tab

				// scroll at the license form view
				$('html, body').animate({
					scrollTop: $('#enable_auto_updates').offset().top
				}, 300);
			});
		},

		/**
		 * Initialize modules
		 * 
		 * @since 5.4.0
		 * @version 5.5.4
		 */
		init: function() {
			this.activateTabs();
			this.saveSettings();
			this.validateInputNumbers();
			this.updateCustomInstallmentsLoop();
			this.updatePaymentFormsVisibility();
			this.changeIconClass();
			this.handleIconImage();
			this.toggleIconFormatContainers();
			this.sortableElements();
			this.resetPluginSettings();
			this.detectOfflineConnection();
			this.initializeStyles();
			this.bindStyleEvents();
			this.initializeModals();
			this.initializeVisibilityControllers();
			this.colorSelector();
			this.requireLicenseInfo();
		},
	};

	// Initialize on document ready
	$(document).ready( function() {
		Settings.init();
	});
})(jQuery);