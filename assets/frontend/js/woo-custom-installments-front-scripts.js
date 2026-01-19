! function(e) {
    "use strict";
    
    // Plugin configuration from backend
    const t = window.wci_front_params || {};
    
    // Debug mode logging
    t.dev_mode && console.log("Woo Custom Installments: Front scripts loaded.", t);
    
    // Price tracking object
    var n = {
        old_price: t.product.regular_price ? t.product.regular_price : 0,
        new_price: t.product.current_price || 0
    };
    
    // Quantity tracker
    var i = 1;
    
    // Debounce timer for performance optimization
    var debounceTimer = null;
    
    // Main plugin object
    var o = {
        /**
         * Debounce function to prevent multiple rapid executions
         * @param {Function} callback - Function to execute
         * @param {number} delay - Delay in milliseconds
         */
        debounce: function(callback, delay) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(callback, delay);
        },
        
        /**
         * Initialize accordion functionality
         */
        initAccordion: function() {
            e(document).on("click", ".wci-accordion-header", o.toggleAccordion.bind(this));
        },
        
        /**
         * Toggle accordion open/close state
         * @param {Event} t - Click event
         */
        toggleAccordion: function(t) {
            var n = e(t.currentTarget).next(".wci-accordion-content");
            
            if ("0px" === n.css("max-height")) {
                // Open accordion
                n.css("max-height", n.prop("scrollHeight") + "px");
                n.parent(".wci-accordion-item").addClass("active");
                e("body").addClass("wci-accordion-active");
            } else {
                // Close accordion with animation
                n.slideUp(350, function() {
                    n.parent(".wci-accordion-item").removeClass("active");
                    n.css("display", "");
                    n.css("max-height", "0px");
                    e("body").removeClass("wci-accordion-active");
                });
            }
        },
        
        /**
         * Display modal popup
         * @param {string} openSelector - Selector to open modal
         * @param {string} containerSelector - Modal container selector
         * @param {string} closeSelector - Selector to close modal
         */
        displayModal: function(openSelector, containerSelector, closeSelector) {
            // Open modal
            e(document).on("click touchstart", openSelector, function(t) {
                t.preventDefault();
                e(this).siblings(".wci-popup-container").addClass("show");
                e("body").addClass("wci-modal-active");
            });
            
            // Close modal by clicking background
            e(document).on("click touchstart", containerSelector, function(t) {
                if (t.target === this) {
                    e(this).removeClass("show");
                    e("body").removeClass("wci-modal-active");
                }
            });
            
            // Close modal with close button
            e(document).on("click touchstart", closeSelector, function(t) {
                t.preventDefault();
                e(".wci-popup-container").removeClass("show");
                e("body").removeClass("wci-modal-active");
            });
        },
        
        /**
         * Initialize modal functionality
         */
        initModal: function() {
            o.displayModal(".wci-open-popup", ".wci-popup-container", ".wci-close-popup");
        },
        
        /**
         * Replace range price functionality for variable products
         * Optimized with debounce to prevent performance issues
         */
        replaceRangePrice: function() {
            // Check if range price is enabled
            if (!t.product_variation_with_range || !t.license_valid) {
                t.debug_mode && console.log("Woo Custom Installments: Range price is disabled.");
                return;
            }
            
            // Variation debounce timer
            let variationDebounce;
            
            // Handle variation change events with debounce
            e(document).on("found_variation show_variation", "form.variations_form", function(e, variationData) {
                clearTimeout(variationDebounce);
                variationDebounce = setTimeout(() => {
                    o.rangeHandleVariationEvent(variationData);
                }, 50); // 50ms delay to group rapid changes
            });
            
            // Handle direct variation ID changes
            e(document).on("change", 'input[name="variation_id"]', function() {
                let variationId = e(this).val();
                clearTimeout(variationDebounce);
                variationDebounce = setTimeout(() => {
                    o.rangeUpdatePrice(variationId);
                }, 50);
            });
            
            // Handle variation reset
            e(document).on("click", "a.reset_variation, a.reset_variations", function() {
                clearTimeout(variationDebounce);
                o.rangeOnClearVariations();
            });
            
            // Handle custom trigger elements
            const triggers = (t.element_triggers || "").split(",");
            triggers.forEach(function(selector) {
                let trimmedSelector = selector.trim();
                if (trimmedSelector) {
                    e(trimmedSelector).on("click touchstart change", function() {
                        clearTimeout(variationDebounce);
                        variationDebounce = setTimeout(() => {
                            o.rangeHandleVariationEvent();
                        }, 50);
                    });
                }
            });
            
            t.debug_mode && console.log("Woo Custom Installments: Range price initialized with debounce optimization.");
        },
        
        /**
         * Clear variations and reset display
         */
        rangeOnClearVariations: function() {
            let priceContainer = e(".woo-custom-installments-price-container");
            let installmentsGroup = priceContainer.siblings(".woo-custom-installments-group");
            
            priceContainer.removeClass("active")
                         .addClass("d-none")
                         .html("");
            
            installmentsGroup.removeClass("d-none");
        },
        
        /**
         * Handle trigger events for range price
         */
        rangeOnTriggerEvent: function() {
            e(document).on("found_variation show_variation", "form.variations_form", function(e, variationData) {
                o.rangeHandleVariationEvent(variationData);
            });
        },
        
        /**
         * Handle variation change event for range price
         * @param {Object} variationData - Variation data from WooCommerce
         */
        rangeHandleVariationEvent: function(variationData) {
            if (variationData && variationData.variation_id) {
                o.rangeUpdatePrice(variationData.variation_id);
                
                // Update price tracking
                n = {
                    old_price: variationData.display_regular_price,
                    new_price: variationData.display_price
                };
                
                // Update amounts with debounce for performance
                o.debounce(() => {
                    o.updateAmounts(n, i);
                }, 100);
            }
        },
        
        /**
         * Update price display for specific variation
         * @param {string|number} variationId - Variation ID to display
         */
        rangeUpdatePrice: function(variationId) {
            // Cache DOM elements for better performance
            const priceContainer = e(".woo-custom-installments-price-container");
            const variationItem = e("#wci-variation-prices").find(`.wci-variation-item[data-variation-id="${variationId}"]`);
            
            if (variationItem.length) {
                priceContainer.html(variationItem.html())
                              .addClass("active")
                              .removeClass("d-none");
                
                o.rangePreventDuplicatePrices();
            }
        },
        
        /**
         * Prevent duplicate price display by hiding/showing appropriate containers
         */
        rangePreventDuplicatePrices: function() {
            const priceContainer = e(".woo-custom-installments-price-container");
            const installmentsGroup = priceContainer.siblings(".woo-custom-installments-group");
            
            if (priceContainer.hasClass("active") && priceContainer.html().trim() !== "") {
                installmentsGroup.addClass("d-none");
            } else {
                priceContainer.removeClass("active");
                installmentsGroup.removeClass("d-none");
            }
        },
        
        /**
         * Update installments based on quantity changes
         */
        updateQuantity: function() {
            e(document).on("change", 'input[name="quantity"]', function() {
                i = parseInt(e(this).val()) || 1;
                
                // Check if tiered pricing is active
                if (!t.tiered_get_rules || typeof t.tiered_get_rules !== 'object' || Object.keys(t.tiered_get_rules).length === 0) {
                    // Use debounce for performance
                    o.debounce(() => {
                        o.updateAmounts(n, i);
                    }, 150);
                }
            });
        },
        
        /**
         * Update all price amounts and calculations
         * @param {Object} priceData - Price data object
         * @param {number} quantity - Product quantity
         */
        updateAmounts: function(priceData = {}, quantity = 1) {
            // Use debounce wrapper for external calls
            this.executeUpdateAmounts(priceData, quantity);
        },
        
        /**
         * Actual update amounts execution (called via debounce)
         * @param {Object} priceData - Price data object
         * @param {number} quantity - Product quantity
         */
        executeUpdateAmounts: function(priceData = {}, quantity = 1) {
            t.dev_mode && console.log("Woo Custom Installments: Updating amounts with price:", priceData, "and quantity:", quantity);
            
            // Get price selector
            var priceSelector = o.getPriceSelector();
            var calculatedQuantity = quantity;
            
            // Adjust quantity if setting is enabled
            if ("yes" === t.update_price_with_quantity) {
                calculatedQuantity = parseInt(quantity) || e('input[name="quantity"]').val();
            }
            
            // Update installments table
            o.updateTableInstallments(priceData.new_price * calculatedQuantity);
            
            // Calculate prices based on quantity
            let calculatedPrices = {
                old_price: priceData.old_price * calculatedQuantity,
                new_price: priceData.new_price * calculatedQuantity
            };
            
            // Update main price element
            o.updateMainPriceElement(calculatedPrices);
            
            // Discount calculations
            let unitDiscountEnabled = t.discounts.enable_discount_per_unit;
            let unitDiscountMethod = t.discounts.discount_per_unit_method;
            let unitDiscountAmount = t.discounts.unit_discount_amount;
            let pixDiscount = t.discounts.pix_discount;
            let pixDiscountMethod = t.discounts.pix_discount_method;
            let totalDiscount = 0;
            
            // Calculate unit discounts
            if (priceSelector.find(".woo-custom-installments-offer").length > 0) {
                if ("yes" === unitDiscountEnabled) {
                    if ("percentage" === unitDiscountMethod) {
                        let discountAmount = o.getPercentageDiscount(unitDiscountAmount, priceData.new_price);
                        totalDiscount = (priceData.new_price - discountAmount) * calculatedQuantity;
                        o.updatePixDiscountElement(discountAmount * calculatedQuantity);
                    } else if ("fixed" === unitDiscountMethod) {
                        let discountAmount = priceData.new_price - unitDiscountAmount;
                        totalDiscount = (priceData.new_price - discountAmount) * calculatedQuantity;
                        o.updatePixDiscountElement(discountAmount * calculatedQuantity);
                    }
                } else if (pixDiscount) {
                    if ("percentage" === pixDiscountMethod) {
                        let discountAmount = o.getPercentageDiscount(pixDiscount, priceData.new_price);
                        totalDiscount = (priceData.new_price - discountAmount) * calculatedQuantity;
                        o.updatePixDiscountElement(discountAmount * calculatedQuantity);
                    } else if ("fixed" === pixDiscountMethod) {
                        let discountAmount = priceData.new_price - pixDiscount;
                        totalDiscount = (priceData.new_price - discountAmount) * calculatedQuantity;
                        o.updatePixDiscountElement(discountAmount * calculatedQuantity);
                    }
                }
            }
            
            // Bank slip discount calculations
            let slipBankDiscount = t.discounts.slip_bank_discount;
            let slipBankMethod = t.discounts.slip_bank_method;
            
            if (priceSelector.find(".woo-custom-installments-ticket-discount").length > 0) {
                if ("percentage" === slipBankMethod) {
                    let discountAmount = o.getPercentageDiscount(slipBankDiscount, priceData.new_price) * calculatedQuantity;
                    o.updateSlipBankElement(discountAmount);
                } else if ("fixed" === slipBankMethod) {
                    o.updateSlipBankElement((priceData.new_price - slipBankDiscount) * calculatedQuantity);
                }
            }
            
            // Update economy badge if present
            if (priceSelector.find(".woo-custom-installments-economy-pix-badge").length > 0) {
                o.updateEconomyElement(totalDiscount);
            }
        },
        
        /**
         * Get the appropriate price selector based on product type
         * @returns {jQuery} Price selector element
         */
        getPriceSelector: function() {
            const priceContainer = e(".woo-custom-installments-price-container");
            const installmentsGroup = priceContainer.siblings(".woo-custom-installments-group");
            
            if ("simple" === t.product.type) {
                return installmentsGroup;
            }
            
            if ("yes" === t.active_price_range) {
                return priceContainer.hasClass("active") ? priceContainer : installmentsGroup;
            }
            
            if (0 === priceContainer.length) {
                return e(".woocommerce-variation-price").find(".woo-custom-installments-group");
            }
            
            return installmentsGroup;
        },
        
        /**
         * Update main price element display
         * @param {Object} priceData - Price data with old and new prices
         */
        updateMainPriceElement: function(priceData) {
            let priceSelector = o.getPriceSelector();
            
            // Check if discount price is displayed
            if (priceSelector.find(".woo-custom-installments-price.has-discount").length > 0) {
                if (priceData.old_price) {
                    priceSelector.find(".woo-custom-installments-price.has-discount")
                                 .find(".amount")
                                 .html(o.getFormattedPrice(priceData.old_price));
                }
                
                if (priceData.new_price) {
                    priceSelector.find(".woo-custom-installments-price.sale-price")
                                 .find(".amount")
                                 .html(o.getFormattedPrice(priceData.new_price));
                }
            } else if (priceData.new_price) {
                priceSelector.find(".woo-custom-installments-price")
                             .find(".amount")
                             .html(o.getFormattedPrice(priceData.new_price));
            }
        },
        
        /**
         * Update PIX discount element
         * @param {number} amount - Discount amount
         */
        updatePixDiscountElement: function(amount) {
            let priceSelector = o.getPriceSelector();
            priceSelector.find(".woo-custom-installments-offer")
                         .find(".amount")
                         .html(o.getFormattedPrice(amount));
            
            // Update in popup and accordion
            e(".wci-popup-body").find(".pix-method-container")
                                .find(".amount")
                                .html(o.getFormattedPrice(amount));
            
            e(".wci-accordion-content").find(".pix-method-container")
                                       .find(".amount")
                                       .html(o.getFormattedPrice(amount));
        },
        
        /**
         * Update bank slip discount element
         * @param {number} amount - Discount amount
         */
        updateSlipBankElement: function(amount) {
            let priceSelector = o.getPriceSelector();
            priceSelector.find(".woo-custom-installments-ticket-discount")
                         .find(".amount")
                         .html(o.getFormattedPrice(amount));
            
            // Update in popup and accordion
            e(".wci-popup-body").find(".woo-custom-installments-ticket-section")
                                .find(".amount")
                                .html(o.getFormattedPrice(amount));
            
            e(".wci-accordion-content").find(".woo-custom-installments-ticket-section")
                                       .find(".amount")
                                       .html(o.getFormattedPrice(amount));
        },
        
        /**
         * Update economy badge element
         * @param {number} amount - Economy amount
         */
        updateEconomyElement: function(amount) {
            let priceSelector = o.getPriceSelector();
            priceSelector.find(".woo-custom-installments-economy-pix-badge")
                         .find(".amount")
                         .html(o.getFormattedPrice(amount));
            
            // Update in popup and accordion
            e(".wci-popup-body").find(".woo-custom-installments-economy-pix-badge")
                                .find(".amount")
                                .html(o.getFormattedPrice(amount));
            
            e(".wci-accordion-content").find(".woo-custom-installments-economy-pix-badge")
                                       .find(".amount")
                                       .html(o.getFormattedPrice(amount));
        },
        
        /**
         * Calculate percentage discount
         * @param {number} percentage - Discount percentage
         * @param {number} price - Original price
         * @returns {number} Price after discount
         */
        getPercentageDiscount: function(percentage, price) {
            return price - price * (percentage / 100);
        },
        
        /**
         * Handle tiered pricing updates
         */
        updatedTieredPrice: function() {
            e(document).on("found_variation show_variation", "form.variations_form", function(e, variationData) {
                n.old_price = variationData.display_regular_price;
                n.new_price = variationData.display_price;
            });
            
            e(document).on("tiered_price_update", function(e, tieredData) {
                n.new_price = tieredData.price;
                o.debounce(() => {
                    o.updateAmounts(n, tieredData.quantity);
                }, 100);
            });
        },
        
        /**
         * Update installments table based on price
         * @param {number} price - Product price
         */
        updateTableInstallments: function(price) {
            var currentPrice = price;
            var tableBody = e(".woo-custom-installments-table").find("tbody");
            var defaultText = tableBody.data("default-text");
            
            // Clear table with placeholder
            tableBody.html('<tr style="display: none !important;"></tr>');
            
            var installmentCount = 1;
            var fees = t.installments.fees;
            var priceSelector = o.getPriceSelector();
            var bestInstallmentText = t.i18n.best_installments_sp;
            
            // Calculate installments
            while (installmentCount <= t.installments.max_installments) {
                var feeRate = fees.hasOwnProperty(installmentCount) ? fees[installmentCount] : t.installments.fee;
                var installmentValue, totalCost;
                
                // No fee installments
                if (installmentCount <= t.installments.max_installments_no_fee) {
                    installmentValue = currentPrice / installmentCount;
                    
                    // Skip if installment value is below minimum
                    if (installmentValue < t.installments.min_installment) {
                        break;
                    }
                    
                    // Add to table
                    if (defaultText) {
                        let rowText = defaultText.replace("{{ parcelas }}", installmentCount)
                                                 .replace("{{ valor }}", o.getFormattedPrice(installmentValue))
                                                 .replace("{{ juros }}", t.i18n.without_fee_label)
                                                 .replace("{{ total }}", o.getFormattedPrice(installmentValue * installmentCount));
                        
                        tableBody.append('<tr class="no-fee"><th>' + rowText + '</th><th>' + o.getFormattedPrice(currentPrice) + '</th></tr>');
                    }
                    
                    // Update best value display
                    if (bestInstallmentText) {
                        let bestText = bestInstallmentText.replace("{{ parcelas }}", installmentCount)
                                                          .replace("{{ valor }}", o.getFormattedPrice(installmentValue))
                                                          .replace("{{ juros }}", t.i18n.without_fee_label)
                                                          .replace("{{ total }}", o.getFormattedPrice(installmentValue * installmentCount));
                        
                        priceSelector.find(".woo-custom-installments-details.best-value.no-fee").html(bestText);
                    }
                }
                // Installments with fee
                else {
                    feeRate = parseFloat(feeRate.toString().replace(",", ".")) / 100;
                    
                    if (feeRate === 0) {
                        totalCost = currentPrice;
                        installmentValue = currentPrice / installmentCount;
                    } else if (t.installments.fee !== feeRate) {
                        totalCost = currentPrice + currentPrice * feeRate;
                        installmentValue = totalCost / installmentCount;
                    } else {
                        var power = Math.pow(1 + feeRate, installmentCount);
                        installmentValue = currentPrice * feeRate * power / (power - 1);
                        totalCost = installmentValue * installmentCount;
                    }
                    
                    // Skip if installment value is below minimum
                    if (installmentValue < t.installments.min_installment) {
                        break;
                    }
                    
                    // Add to table
                    if (defaultText) {
                        let rowText = defaultText.replace("{{ parcelas }}", installmentCount)
                                                 .replace("{{ valor }}", o.getFormattedPrice(installmentValue))
                                                 .replace("{{ juros }}", t.i18n.with_fee_label)
                                                 .replace("{{ total }}", o.getFormattedPrice(totalCost));
                        
                        tableBody.append('<tr class="fee-included"><th>' + rowText + '</th><th>' + o.getFormattedPrice(totalCost) + '</th></tr>');
                    }
                    
                    // Update best value display
                    if (bestInstallmentText) {
                        let bestText = bestInstallmentText.replace("{{ parcelas }}", installmentCount)
                                                          .replace("{{ valor }}", o.getFormattedPrice(installmentValue))
                                                          .replace("{{ juros }}", t.i18n.with_fee_label)
                                                          .replace("{{ total }}", o.getFormattedPrice(totalCost));
                        
                        priceSelector.find(".woo-custom-installments-details-with-fee .best-value.fee-included").html(bestText);
                    }
                }
                
                // Debug logging
                t.dev_mode && console.log("Installment calculation:", {
                    count: installmentCount,
                    fee: feeRate,
                    value: installmentValue,
                    total: totalCost
                });
                
                installmentCount++;
            }
        },
        
        /**
         * Format price with accounting.js
         * @param {number} amount - Amount to format
         * @returns {string} Formatted price string
         */
        getFormattedPrice: function(amount) {
            // Check if accounting is available
            if (typeof accounting !== 'undefined') {
                return accounting.formatMoney(amount, {
                    symbol: t.currency.symbol,
                    decimal: t.currency.format_decimal_sep,
                    thousand: t.currency.format_thousand_sep,
                    precision: t.currency.format_num_decimals,
                    format: t.currency.format
                });
            }
            
            // Fallback formatting
            return t.currency.symbol + amount.toFixed(t.currency.format_num_decimals).replace('.', t.currency.format_decimal_sep);
        },
        
        /**
         * Initialize the plugin
         */
        init: function() {
            // Initialize components
            this.initAccordion();
            this.initModal();
            
            // Set initial prices
            n.old_price = t.product.regular_price;
            n.new_price = t.product.current_price;
            
            // Handle variation products
            if ("variable" === t.product.type) {
                e(document).on("found_variation show_variation", "form.variations_form", function(e, variationData) {
                    n.old_price = variationData.display_regular_price;
                    n.new_price = variationData.display_price;
                });
            }
            
            // Initialize range price replacement with debounce optimization
            if ("yes" === t.active_price_range && "simple" !== t.product.type) {
                this.replaceRangePrice();
            }
            
            // Initialize quantity updates
            if ("yes" === t.update_price_with_quantity) {
                this.updateQuantity();
            }
            
            // Initialize tiered pricing if plugin is active
            if (t.check_tiered_plugin) {
                this.updatedTieredPrice();
            }
            
            t.dev_mode && console.log("Woo Custom Installments: Plugin initialized with performance optimizations.");
        }
    };
    
    // Initialize plugin when document is ready
    jQuery(document).ready(function() {
        o.init();
    });
    
    // Expose plugin to global scope
    window.Woo_Custom_Installments = o;
    
    // Trigger custom event for other scripts
    jQuery(document).trigger("woo_custom_installments_ready");
    
}(jQuery);