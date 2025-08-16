<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for calculate values on installments
 * 
 * @since 1.0.0
 * @version 5.5.0
 * @package MeuMouse.com
 */
class Calculate_Values {

    /**
     * Calculate total value of single installment with interest
     *
     * @since 2.1.0
     * @version 5.4.5
     * @param float $value (Base value for calc)
     * @param float $fee (Fee of interest)
     * @param int $installments (Total of installments)
     * @return float (Value of installment)
     */
    public static function calculate_installment_with_fee( $value, $fee, $installments ) {
        $percentage = floatval( wc_format_decimal( $fee ) ) / 100.00;
        
        if ( $installments <= 0 ) {
            return 0;
        }
    
        if ( $percentage <= 0 ) {
            $installment_price = $value / $installments;
        } elseif ( Admin_Options::get_setting('set_fee_per_installment') === 'yes' ) {
            $installment_price = ( $value * $percentage + $value ) / $installments;
        } else {
            $denominator = ( ( 1 + $percentage ) ** $installments ) - 1;
    
            if ( $denominator === 0 ) {
                return 0;
            }
    
            $installment_price = $value * $percentage * ( ( 1 + $percentage ) ** $installments ) / $denominator;
        }
    
        /**
         * Filter the installment price with fees
         * 
         * @since 2.1.0
         * @version 5.2.0
         * @param float $installment_price | Calculated installment price
         * @param float $value | Base value for calculation
         * @param float $fee | Interest fee percentage
         * @param int $installments | Total number of installments
         * @return float
         */
        return apply_filters( 'Woo_Custom_Installments/Installments/With_Fees', $installment_price, $value, $fee, $installments );
    }


    /**
     * Calculate total value of single installment without interest
     *
     * @since 1.0.0
     * @version 5.4.0
     * @param float $value (Base value for calc)
     * @param int $installments (Total of installments)
     * @return float (Value of installment)
     */
    public static function calculate_installment_no_fee( $value, $installments ) {
        $installment_price = $value / $installments;

        /**
         * Filter the installment price without fees
         * 
         * @since 1.0.0
         * @version 5.4.0
         * @param float $installment_price | Calculated installment price
         * @param float $value | Base value for calculation
         * @param int $installments | Total number of installments
         * @return float
         */
        return apply_filters( 'Woo_Custom_Installments/Installments/Without_Fee', $installment_price, $value, $installments );
    }

    
    /**
     * Calculate final price in the installments
     *
     * @since 1.0.0
     * @version 5.4.0
     * @param float $value (Value of installment)
     * @param int $installments_total (Number total of installments)
     * @return float (Total value to be paid in installments without interest)
     */
    public static function calculate_final_price( $value, $installments_total ) {
        /**
         * Filter the final price of installments
         * 
         * @since 1.0.0
         * @version 5.4.0
         * @param float $value (Value of installment)
         * @param int $installments_total (Number total of installments)
         * @return float (Total value to be paid in installments)
         */
        return apply_filters( 'Woo_Custom_Installments/Installments/Final_Price', round( $value, 2 ) * $installments_total, $value, $installments_total );
    }
    

    /**
     * Calculate product value after applying a discount
     *
     * @since 1.0.0
     * @version 5.4.0
     * @param float $value (Original value)
     * @param int|float $discount (Discount value)
     * @return float (Final value with discount)
     */
    public static function calculate_discounted_price( $value, $discount, $product = false ) {
        $discount = (float) $discount;
        $value = (float) $value;
        $price = $discount ? $value * ( ( 100 - $discount ) / 100 ) : $value;

        /**
         * Filter the discounted price
         * 
         * @since 1.0.0
         * @version 5.4.0
         * @param float $price | Calculated discounted price
         * @param float $value | Original value
         * @param float $discount | Discount value
         * @param object $product | Product object (optional)
         * @return float (Final value with discount)
         */
        return apply_filters( 'Woo_Custom_Installments/Price/Discounted_Price', $price, $value, $discount, $product );
    }


    /**
     * Calculate discounted price for a cart item
     *
     * @since 4.5.0
     * @param object $product | Product object
     * @param float $quantity | Quantity of the product
     * @return float $custom_price
     */
    public static function get_discounted_cart_item_price( $product, $quantity ) {
        $product_id = $product->get_id();
        $parent_id = $product->get_parent_id();
        
        $disable_discount = get_post_meta( $product_id, '__disable_discount_main_price', true ) === 'yes';
        $disable_discount_in_parent = get_post_meta( $parent_id, '__disable_discount_main_price', true ) === 'yes';

        if ( $disable_discount || $disable_discount_in_parent ) {
            return $product->get_price() * $quantity;
        }

        // Get product discount
        list( $discount_per_product, $discount_per_product_method, $discount_per_product_value ) = self::get_product_discount( $product_id, $parent_id );
        
        $cart_item_total = $product->get_price() * $quantity;

        if ( $discount_per_product === 'yes' ) {
            return self::calculate_price_with_discount( $cart_item_total, $discount_per_product_method, $discount_per_product_value );
        }

        return self::calculate_price_with_discount( $cart_item_total, Admin_Options::get_setting('product_price_discount_method'), Admin_Options::get_setting('discount_main_price') );
    }


    /**
     * Get discounted price based on product, price, and settings, including variations.
     *
     * @since 4.5.0
     * @version 5.5.0
     * @param object $product | Product object
     * @param string $discount_type | Type of discount ('main', 'ticket')
     * @return float | Discounted price
     */
    public static function get_discounted_price( $product, $discount_type = 'main' ) {
        $price = 0;

        if ( ! $product || ! is_object( $product ) || ! method_exists( $product, 'is_type' ) ) {
            return 0;
        }

        // Get the correct price based on the product type
        if ( $product->is_type('variation') ) {
            $price = $product->get_sale_price() ?: $product->get_regular_price();
        } elseif ( $product->is_type('variable') ) {
            // For variable products, get the lowest price with discount
            $price = $product->get_variation_sale_price( 'min', true ) ?: $product->get_variation_regular_price( 'min', true );
        } else {
            $price = $product->get_sale_price() ?: $product->get_regular_price();
        }

        $product_id = $product->get_id();
        $parent_product_id = $product->get_parent_id() ?: $product_id;
    
        // Set discount amounts based on discount type
        switch ( $discount_type ) {
            case 'ticket':
                $discount_value = (float) Admin_Options::get_setting('discount_ticket');
                $discount_method = Admin_Options::get_setting('discount_method_ticket');

                break;
            case 'main':
            default:
                $discount_value = (float) Admin_Options::get_setting('discount_main_price');
                $discount_method = Admin_Options::get_setting('product_price_discount_method');

                break;
        }
    
        // Apply product-specific discounts, if applicable
        if ( $product_id && $parent_product_id ) {
            list( $discount_per_product, $discount_per_product_method, $discount_per_product_value ) = self::get_product_discount( $product_id, $parent_product_id );
    
            $disable_discount_main_price = get_post_meta( $product_id, '__disable_discount_main_price', true );
    
            if ( $disable_discount_main_price === 'yes' ) {
                return $price; // No discount should be applied
            }
    
            // Replace global discount with product-specific discount if applicable
            if ( $discount_per_product === 'yes' ) {
                $discount_method = $discount_per_product_method;
                $discount_value = $discount_per_product_value;
            }
        }
    
        // Calculate discounted price
        return self::calculate_price_with_discount( $price, $discount_method, $discount_value );
    }    


    /**
     * Calculate total discount for the cart
     *
     * @since 4.5.2
     * @version 5.3.0
     * @param WC_Cart $cart | Cart object
     * @param bool $include_shipping | Whether to include shipping cost in the calculation
     * @return float Total discount on cart
     */
    public static function calculate_total_discount( $cart, $include_shipping = false ) {
        $total_discount = 0;
        $total_cart_value = 0;
        $has_individual_discount = false;
    
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            $product = $cart_item['data'];
            $quantity = $cart_item['quantity'];
            $product_id = $product->get_id();
    
            // If it is a variation, get the parent product ID
            $parent_id = $product->is_type('variation') ? $product->get_parent_id() : $product_id;
    
            // If the parent product has '__disable_discount_main_price' enabled, it is ignored from the calculation
            if ( get_post_meta( $parent_id, '__disable_discount_main_price', true ) === 'yes' ) {
                continue;
            }
    
            // Get the original price of the product
            $product_price = wc_get_price_excluding_tax( $product );
    
            // Initialize product discount
            $product_discount = 0;
    
            // Checks if per unit discount is enabled on the parent product
            if ( get_post_meta( $parent_id, 'enable_discount_per_unit', true ) === 'yes' ) {
                $discount_value = get_post_meta( $parent_id, 'unit_discount_amount', true );
                $discount_method = get_post_meta( $parent_id, 'discount_per_unit_method', true );
    
                if ( ! empty( $discount_value ) && (float) $discount_value > 0 ) {
                    $has_individual_discount = true;
    
                    if ( $discount_method === 'percentage' ) {
                        $product_discount = ( $product_price * $discount_value / 100 ) * $quantity;
                    } elseif ( $discount_method === 'fixed' ) {
                        $product_discount = $discount_value * $quantity;
                    }
    
                    // Add individual discounts to the total
                    $total_discount += $product_discount;
                }
            }
    
            // Adds products without individual discounts to the cart total
            if ( $product_discount == 0 ) {
                $total_cart_value += $product_price * $quantity;
            }
        }
    
        // Adds shipping to cart total if configured
        if ( $include_shipping ) {
            $total_cart_value += $cart->get_shipping_total();
        }
    
        // If there are no individual discounts applied, the global discount applies.
        if ( ! $has_individual_discount ) {
            $global_discount_value = (float) Admin_Options::get_setting('discount_main_price');
            $global_discount = 0;
    
            if ( $global_discount_value > 0 ) {
                if ( Admin_Options::get_setting('product_price_discount_method') === 'percentage' ) {
                    $global_discount = ( $total_cart_value * $global_discount_value ) / 100;
                } else {
                    $global_discount = min( $global_discount_value, $total_cart_value );
                }
    
                $total_discount += $global_discount;
            }
        }
    
        /**
         * Filter the total discount applied to the cart
         * 
         * @since 4.5.2
         * @version 5.4.0
         * @param float $total_discount | Total discount value
         * @param WC_Cart $cart | Cart object
         * @param bool $include_shipping | Whether to include shipping cost in the calculation
         */
        return apply_filters( 'Woo_Custom_Installments/Price/Calculate_Total_Discount', round( $total_discount, 2 ), $cart, $include_shipping );
    }


    /**
     * Helper function to calculate price with discount method
     *
     * @since 4.5.2
     * @version 5.4.8
     * @param float $price | Original price
     * @param string $discount_method | Discount method ('percentage', 'fixed')
     * @param float $discount_value | Discount value
     * @return float $discounted_price
     */
    public static function calculate_price_with_discount( $price, $discount_method, $discount_value ) {
        $discount_value = floatval( $discount_value );

        if ( $discount_method === 'percentage' ) {
            return self::calculate_discounted_price( (float) $price, $discount_value );
        } else {
            return (float) $price - $discount_value;
        }
    }


    /**
     * Helper function to get product discount settings
     *
     * @since 4.5.2
     * @param int $product_id | Product ID
     * @param int $parent_product_id | Parent Product ID
     * @return array | Discount settings [discount_per_product, discount_per_product_method, discount_per_product_value]
     */
    public static function get_product_discount( $product_id, $parent_product_id ) {
        $discount_per_product = get_post_meta( $product_id, 'enable_discount_per_unit', true );
        $discount_per_product_method = get_post_meta( $product_id, 'discount_per_unit_method', true );
        $discount_per_product_value = get_post_meta( $product_id, 'unit_discount_amount', true );

        // Fallback to parent product discount settings if not found on variation
        if ( empty( $discount_per_product ) ) {
            $discount_per_product = get_post_meta( $parent_product_id, 'enable_discount_per_unit', true );
            $discount_per_product_method = get_post_meta( $parent_product_id, 'discount_per_unit_method', true );
            $discount_per_product_value = get_post_meta( $parent_product_id, 'unit_discount_amount', true );
        }

        return [$discount_per_product, $discount_per_product_method, $discount_per_product_value];
    }


    /**
     * Calculate Pix economy value
     *
     * @since 4.5.0
     * @version 5.5.1
     * @param object $product | WC_Product object
     * @return float | Economy value
     */
    public static function get_pix_economy( $product ) {
        if ( ! $product || ! is_object( $product ) || ! method_exists( $product, 'is_type' ) ) {
            return 0;
        }

        // Get base price considering product type
        if ( $product->is_type('variation') ) {
            $price = $product->get_sale_price() ?: $product->get_regular_price();
        } elseif ( $product->is_type('variable') ) {
            // Use lowest variation price
            $price = $product->get_variation_sale_price( 'min', true ) ?: $product->get_variation_regular_price( 'min', true );
        } else {
            $price = $product->get_sale_price() ?: $product->get_regular_price();
        }

        // Calculate the custom discounted price based on the "main" discount type
        $custom_price = self::get_discounted_price( $product, 'main' );

        // Economy = difference between base price and discounted price
        $economy = max( 0, (float) $price - (float) $custom_price );

        /**
         * Filter the calculated economy value
         * 
         * @since 4.5.0
         * @version 5.5.1
         * @param float $economy | Economy value
         * @param object $product | WC_Product object
         */
        return apply_filters( 'Woo_Custom_Installments/Price/Economy_Pix_Price', $economy, $product );
    }
}