<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for calculate values on installments
 * 
 * @since 1.0.0
 * @version 5.3.0
 * @package MeuMouse.com
 */
class Calculate_Values {

    /**
     * Calculate total value of single installment with interest
     *
     * @since 2.1.0
     * @version 5.2.0
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
    
        if ( Init::get_setting('set_fee_per_installment') === 'yes' ) {
            $installment_price = ( $value * $percentage + $value ) / $installments;
        } else {
            $denominator = ( ( 1 + $percentage ) ** $installments ) - 1;
    
            if ( $denominator === 0 ) {
                return 0;
            }
    
            $installment_price = $value * $percentage * ( ( 1 + $percentage ) ** $installments ) / $denominator;
        }
    
        return apply_filters( 'woo_custom_installments_with_fee', $installment_price, $value, $fee, $installments );
    }    


    /**
     * Calculate total value of single installment without interest
     *
     * @since 1.0.0
     * @param float $value (Base value for calc)
     * @param int $installments (Total of installments)
     * @return float (Value of installment)
     */
    public static function calculate_installment_no_fee( $value, $installments ) {
        $installment_price = $value / $installments;

        return apply_filters( 'woo_custom_installments_no_fee', $installment_price, $value, $installments );
    }

    
    /**
     * Calculate final price in the installments
     *
     * @since 1.0.0
     * @param float $value (Value of installment)
     * @param int $installments_total (Number total of installments)
     * @return float (Total value to be paid in installments without interest)
     */
    public static function calculate_final_price( $value, $installments_total ) {
        return apply_filters( 'woo_custom_installments_final_price', round( $value, 2 ) * $installments_total, $value, $installments_total );
    }
    

    /**
     * Calculate product value after applying a discount
     *
     * @since 1.0.0
     * @version 5.2.5
     * @param float $value (Original value)
     * @param int|float $discount (Discount value)
     * @return float (Final value with discount)
     */
    public static function calculate_discounted_price( $value, $discount, $product = false ) {
        $discount = (float) $discount;
        $value = (float) $value;
        $price = $discount ? $value * ( ( 100 - $discount ) / 100 ) : $value;

        return apply_filters( 'woo_custom_installments_discounted_price', $price, $value, $discount, $product );
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

        return self::calculate_price_with_discount( $cart_item_total, Init::get_setting('product_price_discount_method'), Init::get_setting('discount_main_price') );
    }


    /**
     * Get discounted price based on product, price, and settings, including variations.
     *
     * @since 4.5.0
     * @version 5.2.3
     * @param mixed $product_or_price | Can be a WC_Product object or a numeric price.
     * @param string $discount_type | Type of discount ('main', 'ticket')
     * @return float | Discounted price
     */
    public static function get_discounted_price( $product_or_price, $discount_type = 'main' ) {
        if ( is_a( $product_or_price, 'WC_Product' ) ) {
            $product = $product_or_price;
    
            // Get the correct price based on the product type
            if ( $product->is_type( 'variation' ) ) {
                $price = $product->get_sale_price() ?: $product->get_regular_price();
            } elseif ( $product->is_type( 'variable' ) ) {
                // For variable products, get the lowest price with discount
                $price = $product->get_variation_sale_price( 'min', true ) ?: $product->get_variation_regular_price( 'min', true );
            } else {
                $price = $product->get_sale_price() ?: $product->get_regular_price();
            }
    
            $product_id = $product->get_id();
            $parent_product_id = $product->get_parent_id() ?: $product_id;
    
        // If it is a numeric value
        } elseif ( is_numeric( $product_or_price ) ) {
            $price = floatval( $product_or_price );
            $product_id = null;
            $parent_product_id = null;
        } else {
            return 0; // Invalid input
        }
    
        // Set discount amounts based on discount type
        switch ( $discount_type ) {
            case 'ticket':
                $discount_value = Init::get_setting( 'discount_ticket' );
                $discount_method = Init::get_setting( 'discount_method_ticket' );
                break;
            case 'main':
            default:
                $discount_value = Init::get_setting( 'discount_main_price' );
                $discount_method = Init::get_setting( 'product_price_discount_method' );
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
    
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            $product = $cart_item['data'];
            $quantity = $cart_item['quantity'];
            $product_id = $product->get_id();
    
            // If it is a variation, get the parent product ID
            $parent_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product_id;
    
            // If the parent product has '__disable_discount_main_price' enabled, no discount is applied
            if ( get_post_meta( $parent_id, '__disable_discount_main_price', true ) === 'yes' ) {
                return 0;
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
                    if ( $discount_method === 'percentage' ) {
                        $product_discount = ( $product_price * $discount_value / 100 ) * $quantity;
                    } elseif ( $discount_method === 'fixed' ) {
                        $product_discount = $discount_value * $quantity;
                    }
    
                    // Add individual discounts to the total
                    $total_discount += $product_discount;
                }
            }
    
            // Adds the products without discount to the cart total
            if ( $product_discount == 0 ) {
                $total_cart_value += $product_price * $quantity;
            }
        }
    
        // Adds shipping to cart total if configured
        if ( $include_shipping ) {
            $total_cart_value += $cart->get_shipping_total();
        }
    
        return apply_filters( 'woo_custom_installments_calculate_total_discount', round( $total_discount, 2 ), $cart, $include_shipping );
    }


    /**
     * Helper function to calculate price with discount method
     *
     * @since 4.5.2
     * @version 5.0.0
     * @param float $price | Original price
     * @param string $discount_method | Discount method ('percentage', 'fixed')
     * @param float $discount_value | Discount value
     * @return float $discounted_price
     */
    public static function calculate_price_with_discount( $price, $discount_method, $discount_value ) {
        $discount_value = floatval( $discount_value );

        if ( $discount_method === 'percentage' ) {
            return self::calculate_discounted_price( $price, $discount_value );
        } else {
            return $price - $discount_value;
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
}