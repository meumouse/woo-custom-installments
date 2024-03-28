(function ($) {
    "use strict";

	/**
	 * Activate tabs
	 * 
	 * @since 2.0.0
	 */
	jQuery( function($) {
		// Reads the index stored in localStorage, if it exists
		let activeTabIndex = localStorage.getItem('activeTabIndex');

		if (activeTabIndex === null) {
			// If it is null, activate the general tab
			$('.woo-custom-installments-wrapper a.nav-tab[href="#general"]').click();
		} else {
			$('.woo-custom-installments-wrapper a.nav-tab').eq(activeTabIndex).click();
		}
	});
	  
	$(document).on('click', '.woo-custom-installments-wrapper a.nav-tab', function() {
		// Stores the index of the active tab in localStorage
		let tabIndex = $(this).index();
		localStorage.setItem('activeTabIndex', tabIndex);
		
		let attrHref = $(this).attr('href');
		
		$('.woo-custom-installments-wrapper a.nav-tab').removeClass('nav-tab-active');
		$('.woo-custom-installments-form .nav-content').removeClass('active');
		$(this).addClass('nav-tab-active');
		$('.woo-custom-installments-form').find(attrHref).addClass('active');
		
		return false;
	});

	
	/**
	 * Allow only number bigger or equal 1 in inputs
	 * 
	 * @since 2.0.0
	 */
	$(document).ready( function() {
		let input = $('.allow-numbers-be-1');
		
		input.on('input', function() {
			let input_value = $(this).val();
		
			if (input_value > 1) {
				$(this).val(input_value);
			} else {
				$(this).val(1);
			}
		});
	});


	/**
	 * Hide toast on click button or after 5 seconds
	 * 
	 * @since 2.0.0
	 */
	jQuery( function($) {
		$('.hide-toast').click( function() {
			$('.toast').fadeOut('fast');
		});

		setTimeout( function() {
			$('.toast').fadeOut('fast');
		}, 3000);
	});


	/**
	 * Save options in AJAX
	 * 
	 * @since 3.0.0
	 */
	jQuery( function($) {
		let settingsForm = $('form[name="woo-custom-installments"]');
		let originalValues = settingsForm.serialize();
		var notificationDelay;
	
		settingsForm.on('change', function() {
			if ( settingsForm.serialize() != originalValues ) {
				ajax_save_options(); // send option serialized on change
			}
		});
	
		function ajax_save_options() {
			$.ajax({
				url: wci_params.ajax_url,
				type: 'POST',
				data: {
					action: 'woo_custom_installments_ajax_save_options',
					form_data: settingsForm.serialize(),
				},
				success: function(response) {
					try {
						var responseData = JSON.parse(response); // Parse the JSON response
					//	console.log(responseData);

						if ( responseData.status === 'success' ) {
							originalValues = settingsForm.serialize();
							$('.updated-option-success').addClass('active');
							
							if ( notificationDelay ) {
								clearTimeout(notificationDelay);
							}
				
							notificationDelay = setTimeout( function() {
								$('.updated-option-success').fadeOut('fast', function() {
									$(this).removeClass('active').css('display', '');
								});
							}, 3000);

							updateLoop(responseData.customFeeInstallments);
						}
					} catch (error) {
						console.log(error);
					}
				}
			});
		}

		// update loop custom installments HTML on change values
		function updateLoop() {
			var maxQtdInstallments = parseInt($('#max_qtd_installments').val());
			var maxQtdInstallmentsWithoutFee = parseInt($('#max_qtd_installments_without_fee').val());
			var loopHtml = '';
	
			for ( var i = maxQtdInstallmentsWithoutFee + 1; i <= maxQtdInstallments; i++ ) {
				var current_custom_fee = parseFloat($('input[name="custom_fee_installments[' + i + '][amount]"]').val()) || 0;
	
				loopHtml += '<div class="input-group mb-2">';
				loopHtml += '<div data-installment="' + i + '">';
				loopHtml += '<input class="custom-installment-first small-input form-control" type="text" disabled value="' + i + '"/>';
				loopHtml += '<input class="custom-installment-secondary small-input form-control allow-number-and-dots" type="text" placeholder="1.0" name="custom_fee_installments[' + i + '][amount]" id="custom_fee_installments[' + i + ']" value="' + current_custom_fee + '" />';
				loopHtml += '</div>';
				loopHtml += '</div>';
			}
	
			// update loop HTML
			$('#custom-installments-fieldset-custom-installments').html(loopHtml);
		}
	
		// call update function
		updateLoop();
	
		// triggers for update loop function
		$('#max_qtd_installments, #max_qtd_installments_without_fee').change(function() {
			updateLoop();
		});
	});


	/**
	 * Allow insert only numbers and dot in fee installment global
	 * 
	 * @since 2.0.0
	 */
	jQuery( function($) {
		$('.allow-number-and-dots').keydown(function(e) {
			let key = e.charCode || e.keyCode || 0;

			return (
				(key >= 96 && key <= 105) ||
				(key >= 48 && key <= 57) ||
				key == 190 || key == 8
			);
		});
	});


	/**
	 * Allow insert only numbers, dot and dash in design tab
	 * 
	 * @since 2.1.0
	 */
	jQuery( function($) {
		$('.design-parameters').keydown(function(e) {
			let key = e.charCode || e.keyCode || 0;

			return (
				(key >= 96 && key <= 105) || // numbers (numeric keyboard)
				(key >= 48 && key <= 57) || // numbers (top keyboard)
				key == 190 || // dot
				key == 189 || key == 109 || // dash
				key == 8 // backspace
			);
		});
	});


	/**
	 * Display loader and hide span on click
	 * 
	 * @since 2.0.0
	 */
	jQuery( function($) {
		$('.button-loading').on('click', function() {
			let btn = $(this);
			let originalText = btn.text();
			let btn_width = btn.width();
			let btn_height = btn.height();

			// keep original width and height
			btn.width(btn_width);
			btn.height(btn_height);

			// Add spinner inside button
			btn.html('<span class="spinner-border spinner-border-sm"></span>');
		
			setTimeout(function() {
			// Remove spinner
			btn.html(originalText);
			
			}, 5000);
		});

		// Prevent keypress enter
		$('.form-control').keypress(function(event) {
			if (event.keyCode === 13) {
			event.preventDefault();
			}
		});
	});


	/**
	 * Display foreach custom fee per installment
	 * 
	 * @since 2.0.0
	 */
	jQuery( function($) {
		// get visibility on data base
		if( $('#set_fee_per_installment').prop('checked') ) {
			$('#fee-global-settings').addClass('d-none');
			$('.set-custom-fee-per-installment').removeClass('d-none');
			$('#fee_installments_global').prop('disabled', true);
		} else {
			$('#fee-global-settings').removeClass('d-none');
			$('.set-custom-fee-per-installment').addClass('d-none');
			$('#fee_installments_global').prop('disabled', false);
		}

		// change visibility on click
		$('#set_fee_per_installment').click( function() {
			if ($(this).prop('checked')) {
				$('#fee-global-settings').addClass('d-none');
				$('.set-custom-fee-per-installment').removeClass('d-none');
				$('#fee_installments_global').prop('disabled', true);
			} else {
				$('#fee-global-settings').removeClass('d-none');
				$('.set-custom-fee-per-installment').addClass('d-none');
				$('#fee_installments_global').prop('disabled', false);
			}
		});
	});


	/**
	 * Before active license actions
	 * 
	 * @since 2.0.0
	 * @version 4.0.0
	 */
	jQuery( function($) {
		$('.pro-version').prop('disabled', true);

		$('#active_license_form').on('click', function() {
			$('#popup-pro-notice').removeClass('show');
			$('.woo-custom-installments-wrapper a.nav-tab[href="#about"]').click();
		});
	});


	/**
	 * Check if max installments without fee is equal or minus of number installments
	 * 
	 * @since 2.1.0
	 */
	jQuery( function($) {
		let inputMaxInstallmentsWithoutFee = $('#max_qtd_installments_without_fee');
		let inputMaxInstallments = $('#max_qtd_installments');
	
		// adiciona um evento de mudança aos inputs
		inputMaxInstallments.on('input', function() {
			let maxInstallments = parseInt( inputMaxInstallments.val() );
			let maxInstallmentsWithoutFee = parseInt( inputMaxInstallmentsWithoutFee.val() );
		
			if (maxInstallmentsWithoutFee > maxInstallments) {
				inputMaxInstallmentsWithoutFee.val(maxInstallments);
			}
		});
	
		inputMaxInstallmentsWithoutFee.on('input', function() {
			let maxInstallments = parseInt( inputMaxInstallments.val() );
			let maxInstallmentsWithoutFee = parseInt( inputMaxInstallmentsWithoutFee.val() );
		
			if (maxInstallmentsWithoutFee > maxInstallments) {
				inputMaxInstallmentsWithoutFee.val(maxInstallments);
			}
		});
	});


	/**
	 * Set value zero if interest global is empty
	 * 
	 * @since 2.7.2
	 */
	jQuery( function($) {
		$('#fee_installments_global').blur( function() {
			var input = $(this);

			if (input.val() === '') {
			  input.val('0');
			}
		});
	});


	/**
	 * Change container visibility on click
	 * 
	 * @since 2.7.2
	 */
	jQuery(function($) {

		/**
		 * Function to change container visibility
		 * 
		 * @since 2.7.2
		 * @version 3.8.0
		 * @param {string} trigger - activation element selector
		 * @param {string} container - container selector
		 */
		function change_visibility(trigger, container) {
			let checked = $(trigger).prop('checked');

			$(container).toggleClass('d-none', !checked);
			updatePaymentFormsVisibility();
		}
	
		/**
		 * Function to check the visibility of the ".container-separator.payment-forms" element
		 * and removing the "d-none" class if at least one of the selectors is enabled
		 */
		function updatePaymentFormsVisibility() {
			let anyChecked = $('#enable_pix_method_payment_form').prop('checked') ||
				$('#enable_ticket_method_payment_form').prop('checked') ||
				$('#enable_credit_card_method_payment_form').prop('checked') ||
				$('#enable_debit_card_method_payment_form').prop('checked');
		
			$('.container-separator.payment-forms').toggleClass('d-none', !anyChecked);
		}
	
		/**
		 * Hide input "Texto inicial em produtos variáveis (A partir de)"
		 * 
		 * @since 2.4.0
		 */
		change_visibility('#remove_price_range', '.starting-from, .remove-price-range-dep');
		$('#remove_price_range').click( function() {
			change_visibility('#remove_price_range', '.starting-from, .remove-price-range-dep');
		});
	
		/**
		 * Enable all interest options
		 * 
		 * @since 2.4.0
		 */
		change_visibility('#enable_all_interest_options', '.display-enable-all-interest-options');
		$('#enable_all_interest_options').click( function() {
			change_visibility('#enable_all_interest_options', '.display-enable-all-interest-options');
		});
		
		/**
		 * Enable all discount options
		 * 
		 * @since 2.4.0
		 */
		change_visibility('#enable_all_discount_options', '.display-enable-all-discount-options');
		$('#enable_all_discount_options').click( function() {
			change_visibility('#enable_all_discount_options', '.display-enable-all-discount-options');
			$('#discount-settings .container-separator').toggleClass('d-none', !$(this).prop('checked'));
		});

		/**
		 * Active text on active pix method
		 * 
		 * @since 2.7.2
		 */
		change_visibility('#enable_pix_method_payment_form', '.admin-container-transfers');
		$('#enable_pix_method_payment_form').click( function() {
			change_visibility('#enable_pix_method_payment_form', '.admin-container-transfers');
		});
	
		/**
		 * Active text on active ticket method
		 * 
		 * @since 2.7.2
		 */
		change_visibility('#enable_ticket_method_payment_form', '.admin-container-ticket');
		$('#enable_ticket_method_payment_form').click( function() {
			change_visibility('#enable_ticket_method_payment_form', '.admin-container-ticket');
		});
	
		/**
		 * Active text on active credit card method
		 * 
		 * @since 2.7.2
		 */
		change_visibility('#enable_credit_card_method_payment_form', '.admin-container-credit-card');
		$('#enable_credit_card_method_payment_form').click( function() {
		change_visibility('#enable_credit_card_method_payment_form', '.admin-container-credit-card');
		});
	
		/**
		 * Active text on active debit card method
		 * 
		 * @since 2.7.2
		 */
		change_visibility('#enable_debit_card_method_payment_form', '.admin-container-debit-card');
		$('#enable_debit_card_method_payment_form').click( function() {
			change_visibility('#enable_debit_card_method_payment_form', '.admin-container-debit-card');
		});
	
		/**
		 * Display more settings after active discount per quantity
		 * 
		 * @since 2.7.2
		 */
		change_visibility('#enable_functions_discount_per_quantity', '.discount-per-quantity-option');
		$('#enable_functions_discount_per_quantity').click( function() {
			change_visibility('#enable_functions_discount_per_quantity', '.discount-per-quantity-option');
		});

		/**
		 * Display custom text after price
		 * 
		 * @since 2.8.0
		 */
		change_visibility('#custom_text_after_price', '.tr-custom-text-after-price');
		$('#custom_text_after_price').click( function() {
			change_visibility('#custom_text_after_price', '.tr-custom-text-after-price');
		});

		/**
		 * Display discount ticket option
		 * 
		 * @since 2.8.0
		 */
		change_visibility('#enable_ticket_method_payment_form', '.admin-discount-ticket-option');
		$('#enable_ticket_method_payment_form').click( function() {
			change_visibility('#enable_ticket_method_payment_form', '.admin-discount-ticket-option');
		});

		/**
		 * Display economy Pix hook option
		 * 
		 * @since 3.6.2
		 */
		change_visibility('#enable_economy_pix_badge', '.economy-pix-dependency');
		$('#enable_economy_pix_badge').click( function() {
			change_visibility('#enable_economy_pix_badge', '.economy-pix-dependency');
		});
	});


	/**
	 * Display immediate aprove badge option
	 * 
	 * @since 2.8.0
	 */
	jQuery( function($) {
		// get visibility on data base
		if( $('#enable_pix_method_payment_form, #enable_credit_card_method_payment_form, #enable_debit_card_method_payment_form').prop('checked') ) {
			$('.admin-immediate-aprove-badge').removeClass('d-none');
		} else {
			$('.admin-immediate-aprove-badge').addClass('d-none');
		}

		// change visibility on click
		$('#enable_pix_method_payment_form, #enable_credit_card_method_payment_form, #enable_debit_card_method_payment_form').click( function() {
			if ($(this).prop('checked')) {
				$('.admin-immediate-aprove-badge').removeClass('d-none');
			} else {
				$('.admin-immediate-aprove-badge').addClass('d-none');
			}
		});
	});
	

	/**
	 * Display shortcode message in panel
	 * 
	 * @since 3.0.0
	 */
	jQuery( function($) {
		$('select[name="hook_payment_form_single_product"]').change( function() {
			let selectedOption = $(this).children("option:selected").val();

			if (selectedOption == 'shortcode') {
				$('#display-shortcode-info').removeClass('d-none');
			} else {
				$('#display-shortcode-info').addClass('d-none');
			}
		});
	});


	/**
	 * Change visibility elements on change select option
	 * 
	 * @since 3.8.0
	 */
	jQuery( function($) {
		/**
		 * Function for change visibility elements on change select option
		 * 
		 * @since 3.8.0
		 * @param {string} select | Select element trigger
		 * @param {string} option | Option value for check
		 * @param {string} container | Container for hide or display
		 * @param {string} visibility | Check if show or hide container target
		 */
		function change_visibility_selector(select, option, container, visibility) {
			let selected_option = $(select).children('option:selected').val();
			
			if (selected_option === option && visibility === 'show') {
				$(container).removeClass('d-none');
			} else if (selected_option === option && visibility === 'hide') {
				$(container).addClass('d-none');
			}

			$(container).toggleClass('d-none', selected_option === option);
		}

		// hide select for position installments button
		change_visibility_selector( $('#display_installment_type'), 'hide', $('.tr-position-installment-type-button'), 'hide' );
		$('#display_installment_type').change( function() {
			change_visibility_selector( $(this), 'hide', $('.tr-position-installment-type-button'), 'hide' );
		});

		// hide options for discount per quantity
		change_visibility_selector( $('#enable_discount_per_quantity_method'), 'product', $('.global-discount-required'), 'show' );
		$('#enable_discount_per_quantity_method').change( function() {
			change_visibility_selector( $(this), 'product', $('.global-discount-required'), 'show' );
		});
	});


	/**
	 * Change symbol when option is changed and check if value is greater than 100 when option is percentage
	 * 
	 * @since 3.0.0
	 * @version 3.8.0
	 */
	jQuery( function($) {
		/**
		 * Function for change symbol element on discount containers
		 * 
		 * @since 3.8.0
		 * @param {string} select | Select element
		 * @param {string} symbol_element | Container with symbol % or currency
		 */
		function change_symbol_icon(select, symbol_element) {
			var selected_value = $(select).val();
		
			if (selected_value === 'percentage') {
				symbol_element.html('%');
			} else if (selected_value === 'fixed') {
				symbol_element.html(currency_symbol);
			}
		}

		// change icon for pix
		$('#product_price_discount_method').change( function() {
			change_symbol_icon( $(this), $('#symbol_discount_pix') );
		});

		// change icon for slip bank
		$('#discount_method_ticket').change( function() {
			change_symbol_icon( $(this), $('#symbol_discount_slip_bank') );
		});
		
		// change icon for discount per quantity
		$('#discount_per_quantity_method').change( function() {
			change_symbol_icon( $(this), $('#symbol_discount_quantity') );
		});


		/**
		 * Change symbol icon on change select for discount gateways
		 * 
		 * @since 3.0.0
		 * @version 3.8.0
		 * @param {string} $row | Row discount gateway
		 */
		function change_symbol_discounts($row) {
			let select = $row.find('.get-discount-method-payment-method');
			let symbol_element = $row.find('.discount-method-result-payment-method');
		
			select.change(function() {
			  if (select.val() === 'percentage') {
				symbol_element.html('%');
			  } else {
				symbol_element.html(currency_symbol);
			  }
			});
		
			// Check initial select value to set currency symbol
			if (select.val() === 'percentage') {
			  symbol_element.html('%');
			} else {
			  symbol_element.html(currency_symbol);
			}
		}

		$('.foreach-method-discount').each( function() {
			change_symbol_discounts($(this));
		});
	

		/**
		 * Change symbol icon on change select option for interest gateways
		 * 
		 * @since 3.0.0
		 * @version 3.8.0
		 * @param {string} $row | Row gateway
		 */
		function change_symbol_interests($row) {
			let select = $row.find('.get-interest-method-payment-method');
			let symbol_element = $row.find('.interest-method-result-payment-method');
		
			select.change( function() {
			  if (select.val() === 'percentage') {
				symbol_element.html('%');
			  } else {
				symbol_element.html(currency_symbol);
			  }
			});
		
			// Check initial select value to set currency symbol
			if (select.val() === 'percentage') {
			  symbol_element.html('%');
			} else {
			  symbol_element.html(currency_symbol);
			}
		}

		$('.wci-interest-methods').each( function() {
			change_symbol_interests($(this));
		});
	});


	/**
	 * Helper color selector
	 * 
	 * @since 3.8.0
	 */
	jQuery(document).ready( function($) {
		$('.get-color-selected').on('input', function() {
			var color_value = $(this).val();
	
			$(this).closest('.color-container').find('.form-control-color').val(color_value);
		});
	
		$('.form-control-color').on('input', function() {
			var color_value = $(this).val();
	
			$(this).closest('.color-container').find('.get-color-selected').val(color_value);
		});

		$('.reset-color').on('click', function(e) {
			e.preventDefault();
			var color_value = $(this).data('color');

			$(this).closest('.color-container').find('.form-control-color').val(color_value);
			$(this).closest('.color-container').find('.get-color-selected').val(color_value).change();
		});
	});


	/**
	 * Reorder WCI elements
	 * 
	 * @since 3.8.0
	 */
	$(document).ready( function() {
		// Function to classify items
		function sortItems() {
			var $list = $('#reorder_wci_elements ul.sortable');
			var $items = $list.children('li');
	
			// Sort items based on hidden input values
			$items.sort(function(a, b) {
				var aValue = parseInt($(a).find('input[type="hidden"]').val());
				var bValue = parseInt($(b).find('input[type="hidden"]').val());
	
				return aValue - bValue;
			});
	
			// Updates the order of items in the list
			$items.detach().appendTo($list);
		}

		sortItems();
	
		$('#reorder_wci_elements .sortable').sortable({
			draggable: 'li:not(.blocked)',
			animation: 250,
			forceFallback: true,
			removeCloneOnHide: true,
			touchStartThreshold: 5,
			onEnd: function(evt) {
				var $list = $(evt.item).closest('ul');
	
				$list.find('input[type="hidden"]').each(function(index, input) {
					$(input).val(index + 1).change();
				});
			}
		});
	});


	/**
	 * Display popups
	 * 
	 * @since 3.8.0
	 */
	jQuery( function($) {
		/**
		 * Function for display popups based on Bootstrap
		 * 
		 * @param {string} trigger | Trigger for display popup
		 * @param {string} container | Container for display content
		 * @param {string} close | Close button popup
		 */
		function display_popup(trigger, container, close) {
			trigger.on('click', function(e) {
				e.preventDefault();
				container.addClass('show');
			});
		
			container.on('click', function(e) {
				if (e.target === this) {
					$(this).removeClass('show');
				}
			});
		
			close.on('click', function(e) {
				e.preventDefault();
				container.removeClass('show');
			});
		}

		display_popup( $('.manage-credit-card-trigger'), $('.manage-credit-card-container'), $('.close-manage-credit-card') );
		display_popup( $('.manage-debit-card-trigger'), $('.manage-debit-card-container'), $('.close-manage-debit-card') );
		display_popup( $('#discount_per_quantity_trigger'), $('#discount_per_quantity_container'), $('#discount_per_quantity_close') );
		display_popup( $('#set_custom_fee_trigger'), $('#set_custom_fee_container'), $('#set_custom_fee_close') );
		display_popup( $('.pro-version-notice'), $('#popup-pro-notice'), $('#close-pro-notice') );
	});


	/**
	 * Allow only number bigger or equal 0 in inputs
	 * 
	 * @since 3.8.0
	 */
	$(document).ready( function() {
		let input = $('.allow-numbers-be-0');
		
		input.on('input', function() {
			let input_value = $(this).val();
		
			if (input_value > 0) {
				$(this).val(input_value);
			} else {
				$(this).val(0);
			}
		});
	});


	/**
	 * Deactive license process
	 * 
	 * @since 4.0.0
	 */
	jQuery(document).ready( function($) {
		$('#woo_custom_installments_deactive_license').click( function(e) {
			var btn = $(this);
			var btn_text = btn.text();
			var btn_width = btn.width();
			var btn_height = btn.height();
			var form = new FormData();

			btn.width(btn_width);
			btn.height(btn_height);
			btn.html('<span class="spinner-border spinner-border-sm"></span>');

			form.append('api_key', wci_params.api_key);
			form.append('license_code', wci_params.license);
			form.append('domain', wci_params.domain);
	
			var settings = {
				"url": wci_params.api_endpoint + 'license/remove_domain',
				"method": "POST",
				"timeout": 0,
				"processData": false,
				"mimeType": "multipart/form-data",
				"contentType": false,
				"data": form,
			};
	
			$.ajax(settings)
				.done(function(response) {
					console.log(response);
					
					if ( response.status === true && response.msg === "Domain successfully removed" ) {
						reset_license_form();
					} else {
					//	$(btn).html(btn_text);
					}
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					console.error("Erro ao remover o domínio:", errorThrown);
				//	$(this).html(btn_text);
				});
		});

		function reset_license_form() {
			$.ajax({
				url: wci_params.ajax_url,
				type: 'POST',
				data: {
					action: 'deactive_license_process',
				},
				success: function(response) {
					if ( response.status === 'success' ) {
					//	$(btn).html(btn_text);
						window.location.reload();
						console.log('Dominio removido', response);
					}
				},
				error: function(xhr, status, error) {
					console.error("Erro ao excluir opção no servidor:", error);
				//	$(btn).html(btn_text);
				}
			});
		}
	});	

})(jQuery);