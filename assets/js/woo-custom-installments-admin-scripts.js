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
			$('.woo-custom-installments-wrapper a.nav-tab[href="#general-settings"]').click();
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
		let inputField = $('.allow-numbers-be-1');
		
		inputField.on('input', function() {
			let inputValue = $(this).val();
		
			if (inputValue > 1) {
				$(this).val(inputValue);
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
			$('.update-notice-wci, .updated-option-success').fadeOut('fast');
		});

		setTimeout( function() {
			$('.update-notice-wci').fadeOut('fast');
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
			var setFeeFirstInstallment = $('#set_fee_first_installment').is(':checked');
			
			var loopHtml = '';
	
			// construct custom installments loop on change options
			var initLoop = setFeeFirstInstallment ? 1 : maxQtdInstallmentsWithoutFee + 1;

			for ( var i = initLoop; i <= maxQtdInstallments; i++ ) {
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
		$('#max_qtd_installments, #max_qtd_installments_without_fee, #set_fee_first_installment').change(function() {
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
			let $btn = $(this);
			let originalText = $btn.text();
			let btnWidth = $btn.width();
			let btnHeight = $btn.height();

			// keep original width and height
			$btn.width(btnWidth);
			$btn.height(btnHeight);

			// Add spinner inside button
			$btn.html('<span class="spinner-border spinner-border-sm"></span>');
		
			setTimeout(function() {
			// Remove spinner
			$btn.html(originalText);
			
			}, 15000);
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
	 * Check elements if has class pro-version
	 * 
	 * @since 2.0.0
	 */
	jQuery( function($) {
		$('.pro-version').prop('disabled', true);
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
		 * @param {string} method - activation element selector
		 * @param {string} container - container selector
		 */
		function toggleContainerVisibility(method, container) {
			let checked = $(method).prop('checked');

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
		toggleContainerVisibility('#remove_price_range', '#starting-from');
		$('#remove_price_range').click( function() {
			toggleContainerVisibility('#remove_price_range', '#starting-from');
		});
	
		/**
		 * Enable all interest options
		 * 
		 * @since 2.4.0
		 */
		toggleContainerVisibility('#enable_all_interest_options', '.display-enable-all-interest-options');
		$('#enable_all_interest_options').click( function() {
			toggleContainerVisibility('#enable_all_interest_options', '.display-enable-all-interest-options');
		});
		
		/**
		 * Enable all discount options
		 * 
		 * @since 2.4.0
		 */
		toggleContainerVisibility('#enable_all_discount_options', '.display-enable-all-discount-options');
		$('#enable_all_discount_options').click( function() {
			toggleContainerVisibility('#enable_all_discount_options', '.display-enable-all-discount-options');
			$('#discount-settings .container-separator').toggleClass('d-none', !$(this).prop('checked'));
		});

		/**
		 * Active text on active pix method
		 * 
		 * @since 2.7.2
		 */
		toggleContainerVisibility('#enable_pix_method_payment_form', '.admin-container-transfers');
		$('#enable_pix_method_payment_form').click( function() {
			toggleContainerVisibility('#enable_pix_method_payment_form', '.admin-container-transfers');
		});
	
		/**
		 * Active text on active ticket method
		 * 
		 * @since 2.7.2
		 */
		toggleContainerVisibility('#enable_ticket_method_payment_form', '.admin-container-ticket');
		$('#enable_ticket_method_payment_form').click( function() {
			toggleContainerVisibility('#enable_ticket_method_payment_form', '.admin-container-ticket');
		});
	
		/**
		 * Active text on active credit card method
		 * 
		 * @since 2.7.2
		 */
		toggleContainerVisibility('#enable_credit_card_method_payment_form', '.admin-container-credit-card');
		$('#enable_credit_card_method_payment_form').click( function() {
		toggleContainerVisibility('#enable_credit_card_method_payment_form', '.admin-container-credit-card');
		});
	
		/**
		 * Active text on active debit card method
		 * 
		 * @since 2.7.2
		 */
		toggleContainerVisibility('#enable_debit_card_method_payment_form', '.admin-container-debit-card');
		$('#enable_debit_card_method_payment_form').click( function() {
			toggleContainerVisibility('#enable_debit_card_method_payment_form', '.admin-container-debit-card');
		});
	
		/**
		 * Display more settings after active discount per quantity
		 * 
		 * @since 2.7.2
		 */
		toggleContainerVisibility('#enable_functions_discount_per_quantity', '.table-row-set-quantity-enable-discount');
		$('#enable_functions_discount_per_quantity').click( function() {
			toggleContainerVisibility('#enable_functions_discount_per_quantity', '.table-row-set-quantity-enable-discount');
		});

		/**
		 * Hide discount per quantity option
		 * 
		 * @since 2.7.2
		 */
		toggleContainerVisibility('#enable_functions_discount_per_quantity', '.table-row-set-quantity-enable-discount');
		$('#enable_functions_discount_per_quantity').click( function() {
			toggleContainerVisibility('#enable_functions_discount_per_quantity', '.table-row-set-quantity-enable-discount');
		});

		if( $('#enable_functions_discount_per_quantity').prop('checked') ) {
			// Hide discount per quantity option single product if global is activated
			if( $('#set_discount_per_quantity_global').prop('checked') ) {
				$('.disable-discount-per-product-single').addClass('d-none');
			} else {
				$('.disable-discount-per-product-single').removeClass('d-none');
			}
	
			$('#set_discount_per_quantity_global').click( function() {
				if ($(this).prop('checked')) {
					$('.disable-discount-per-product-single').addClass('d-none');
				} else {
					$('.disable-discount-per-product-single').removeClass('d-none');
				}
			});
	
			// Hide discount per quantity option global if single product is activated
			if( $('#enable_functions_discount_per_quantity_single_product').prop('checked') ) {
				$('.disable-discount-per-product-global').addClass('d-none');
			} else {
				$('.disable-discount-per-product-global').removeClass('d-none');
			}
	
			$('#enable_functions_discount_per_quantity_single_product').click( function() {
				if ($(this).prop('checked')) {
					$('.disable-discount-per-product-global').addClass('d-none');
				} else {
					$('.disable-discount-per-product-global').removeClass('d-none');
				}
			});
		}

		/**
		 * Display custom text after price
		 * 
		 * @since 2.8.0
		 */
		toggleContainerVisibility('#custom_text_after_price', '.tr-custom-text-after-price');
		$('#custom_text_after_price').click( function() {
			toggleContainerVisibility('#custom_text_after_price', '.tr-custom-text-after-price');
		});

		/**
		 * Display discount ticket option
		 * 
		 * @since 2.8.0
		 */
		toggleContainerVisibility('#enable_ticket_method_payment_form', '.admin-discount-ticket-option');
		$('#enable_ticket_method_payment_form').click( function() {
			toggleContainerVisibility('#enable_ticket_method_payment_form', '.admin-discount-ticket-option');
		});
	});


	/**
	 * Modal Pro notice
	 * 
	 * @since 2.7.5
	 */
	jQuery( function($) {
		const popupProNotice = $('.pro-version-notice');
		const popupProContainer = $('#popup-pro-notice');
		const closePopupProNotice = $('#close-pro-notice');
		
		popupProNotice.on('click', function() {
		popupProContainer.addClass('show');
		});
		
		popupProContainer.on('click', function(event) {
		if (event.target === this) {
			$(this).removeClass('show');
		}
		});

		closePopupProNotice.on('click', function() {
			popupProContainer.removeClass('show');
		})
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
	 * Hide position select payment form button
	 * 
	 * @since 2.9.0
	 */
	jQuery( function($) {
		// Quando o valor do select for alterado
		$("select[name='display_installment_type']").on("change", function() {
			// check if value is 'hide'
			if ($(this).val() === "hide") {
				$(".tr-position-installment-type-button").addClass("d-none");
			} else {
				$(".tr-position-installment-type-button").removeClass("d-none");
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
	 * Change symbol when option is changed and check if value is greater than 100 when option is percentage
	 * 
	 * @since 3.0.0
	 */
	jQuery(function($) {
		// Function to check and adjust the input value for discounting the product price
		function checkAndAdjustValueForDiscount($element) {
		  var selectElement = $element.find('.get-discount-method-main-price');
		  var inputElement = $element.find('#discount_main_price');
		  var currencySymbolElement = $element.find('.discount-method-result-main-price');
	  
		  function adjustInputValue() {
			var inputValue = parseFloat(inputElement.val());

			if (selectElement.val() === 'percentage' && inputValue > 100) {
			  inputElement.val(100);
			}
		  }
	  
		  selectElement.change(function() {
			if (selectElement.val() === 'percentage') {
			  currencySymbolElement.html('%');
			  adjustInputValue();
			} else {
			  currencySymbolElement.html(currency_symbol);
			}
		  });
	  
		  inputElement.on('input', function() {
			if (selectElement.val() === 'percentage') {
			  adjustInputValue();
			}
		  });
	  
		  // Check initial select value to set currency symbol
		  if (selectElement.val() === 'percentage') {
			currencySymbolElement.html('%');
			adjustInputValue();
		  } else {
			currencySymbolElement.html(currency_symbol);
		  }
		}
	  
		// Function to check and adjust the input value for individual payment methods
		function checkAndAdjustValueForPaymentMethod($row) {
		  var selectElement = $row.find('.get-discount-method-payment-method');
		  var inputElement = $row.find('.input-control-wd-5');
		  var currencySymbolElement = $row.find('.discount-method-result-payment-method');
	  
		  function adjustInputValue() {
			var inputValue = parseFloat(inputElement.val());

			if (selectElement.val() === 'percentage' && inputValue > 100) {
			  inputElement.val(100);
			}
		  }
	  
		  selectElement.change(function() {
			if (selectElement.val() === 'percentage') {
			  currencySymbolElement.html('%');
			  adjustInputValue();
			} else {
			  currencySymbolElement.html(currency_symbol);
			}
		  });
	  
		  inputElement.on('input', function() {
			if (selectElement.val() === 'percentage') {
			  adjustInputValue();
			}
		  });
	  
		  // Check initial select value to set currency symbol
		  if (selectElement.val() === 'percentage') {
			currencySymbolElement.html('%');
			adjustInputValue();
		  } else {
			currencySymbolElement.html(currency_symbol);
		  }
		}
	  
		// Call the function for the product price discount element when the page loads
		checkAndAdjustValueForDiscount($('.display-enable-all-discount-options'));
	  
		// Call the function for each payment method line when the page loads
		$('.foreach-method-discount').each(function() {
		  checkAndAdjustValueForPaymentMethod($(this));
		});

		// Function to check and adjust the input value for interest
		function checkAndAdjustValueForInterest($row) {
			var selectElement = $row.find('.get-interest-method-payment-method');
			var inputElement = $row.find('.input-control-wd-5');
			var currencySymbolElement = $row.find('.interest-method-result-payment-method');
		
			function adjustInputValue() {
			  var inputValue = parseFloat(inputElement.val());

			  if (selectElement.val() === 'percentage' && inputValue > 100) {
				inputElement.val(100);
			  }
			}
		
			selectElement.change(function() {
			  if (selectElement.val() === 'percentage') {
				currencySymbolElement.html('%');
				adjustInputValue();
			  } else {
				currencySymbolElement.html(currency_symbol);
			  }
			});
		
			inputElement.on('input', function() {
			  if (selectElement.val() === 'percentage') {
				adjustInputValue();
			  }
			});
		
			// Check initial select value to set currency symbol
			if (selectElement.val() === 'percentage') {
			  currencySymbolElement.html('%');
			  adjustInputValue();
			} else {
			  currencySymbolElement.html(currency_symbol);
			}
		}

		// Function to check and adjust the input value for quantity discount
		function checkAndAdjustValueForDiscountPerQuantity($element) {
			var selectElement = $element.find('.get-discount-per-quantity-method');
			var inputElement = $element.find('#value_for_discount_per_quantity');
			var currencySymbolElement = $element.find('.discount-per-quantity-method-result');
		
			function adjustInputValue() {
			  var inputValue = parseFloat(inputElement.val());
  
			  if (selectElement.val() === 'percentage' && inputValue > 100) {
				inputElement.val(100);
			  }
			}
		
			selectElement.change(function() {
			  if (selectElement.val() === 'percentage') {
				currencySymbolElement.html('%');
				adjustInputValue();
			  } else {
				currencySymbolElement.html(currency_symbol);
			  }
			});
		
			inputElement.on('input', function() {
			  if (selectElement.val() === 'percentage') {
				adjustInputValue();
			  }
			});
		
			// Check initial select value to set currency symbol
			if (selectElement.val() === 'percentage') {
			  currencySymbolElement.html('%');
			  adjustInputValue();
			} else {
			  currencySymbolElement.html(currency_symbol);
			}
		}
		
		// Call the function for the product price discount element when the page loads
		checkAndAdjustValueForDiscount($('.display-enable-all-discount-options'));

		// Call the function for the product price discount element when the page loads
		checkAndAdjustValueForDiscountPerQuantity($('.display-enable-all-discount-options'));
	
		// Call the function for each payment method line when the page loads
		$('.foreach-method-discount').each( function() {
		checkAndAdjustValueForPaymentMethod($(this));
		});
	
		// Call the function for each interest line when the page loads
		$('.wci-interest-methods').each( function() {
		checkAndAdjustValueForInterest($(this));
		});
	});


})(jQuery);