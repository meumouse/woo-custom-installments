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
		
		// Sets the active tab based on the value stored in localStorage
		if (activeTabIndex !== null) {
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
			$('.update-notice-wci').fadeOut('fast');
		});

		setTimeout( function() {
			$('.update-notice-wci').fadeOut('fast');
		}, 5000);
	});


	/**
	 * Get symbol after select option
	 * 
	 * @since 2.0.0
	 */
	jQuery( function($) {
		// Change icon on change option selector discount main price
		$('select.get-discount-method-main-price').change( function() {
			let selectedMethod = $(this).children("option:selected").val();

			if (selectedMethod == 'percentage') {
				$('.discount-method-result-main-price').html('%');
			} else {
				$('.discount-method-result-main-price').html('R$');
			}
		});

		// Change icon on change option selector discount per payment method
		$('select.get-discount-method-payment-method').change( function() {
			let selectedOption = $(this).val();
			let elementId = $(this).closest('.wci-method-discount-selector').attr('id');
			let methodResultId = $('#' + elementId + ' .discount-method-result-payment-method');
			
			if (selectedOption == 'fixed') {
				methodResultId.html('R$');
			} else if (selectedOption == 'percentage') {
				methodResultId.html('%');
			}
		});

		// Change icon on change option selector interest per payment method
		$('select.get-interest-method-payment-method').change( function() {
			let selectedOption = $(this).val();
			let elementId = $(this).closest('.wci-method-interest-selector').attr('id');
			let methodResultId = $('#' + elementId + ' .interest-method-result-payment-method');
			
			if (selectedOption == 'fixed') {
				methodResultId.html('R$');
			} else if (selectedOption == 'percentage') {
				methodResultId.html('%');
			}
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
			$('#set-custom-fee-per-installment').removeClass('d-none');
			$('#fee_installments_global').prop('disabled', true);
		} else {
			$('#fee-global-settings').removeClass('d-none');
			$('#set-custom-fee-per-installment').addClass('d-none');
			$('#fee_installments_global').prop('disabled', false);
		}

		// change visibility on click
		$('#set_fee_per_installment').click(function() {
			if ($(this).prop('checked')) {
				$('#fee-global-settings').addClass('d-none');
				$('#set-custom-fee-per-installment').removeClass('d-none');
				$('#fee_installments_global').prop('disabled', true);
			} else {
				$('#fee-global-settings').removeClass('d-none');
				$('#set-custom-fee-per-installment').addClass('d-none');
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
	 * Disable save options button if options are not different
	 * 
	 * @since 2.3.5
	 */
	jQuery( function($) {
		var saveButton = $('#save_settings');
		var settingsForm = $('form[name="woo-custom-installments"]');

		// get original values of options in the data base wordpress
		var originalValues = settingsForm.serialize();

		// disable button if options are not different
		if (settingsForm.serialize() === originalValues) {
			saveButton.prop('disabled', true);
		} else {
			saveButton.prop('disabled', false);
		}

		// Records a change event on form fields
		settingsForm.on('change', function() {
			// Verifica se houve mudanças nos valores dos campos
			if (settingsForm.serialize() === originalValues) {
				// If the values are the same, disable the save button
				saveButton.prop('disabled', true);
			} else {
				// If the values are different, enable the save button
				saveButton.prop('disabled', false);
			}
		});
	});


})(jQuery);