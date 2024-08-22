<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\License;
use MeuMouse\Woo_Custom_Installments\Calculate_Values;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for adding discounts in the cart
 * 
 * @since 2.0.0
 * @version 4.5.2
 * @package MeuMouse.com
 */
class Discounts {

    /**
     * Construct function
     * 
     * @since 2.0.0
     * @version 4.5.2
     * @return void
     */
    public function __construct() {
        // check if license exists
        if ( License::is_valid() ) {
            if ( ! is_admin() ) {
                add_filter( 'woocommerce_gateway_title', array( $this, 'display_discount_on_gateway_title' ), 10, 2 );
            }
            
            add_action( 'woocommerce_checkout_order_processed', array( $this, 'woo_custom_installments_update_order_data' ), 10 );
            add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_discount' ), 10 );

            /**
             * Enable discount per quantity for all products
             * 
             * @since 2.7.2
             */
            if ( Init::get_setting('enable_functions_discount_per_quantity') === 'yes' ) {
                add_action( 'woocommerce_before_calculate_totals', array( $this, 'set_discount_per_quantity' ) );
            }
        }
    }


    /**
     * Display the discount in payment method title
     * 
     * @since 2.0.0
     * @param $title | Payment gateway title
     * @param $id | Payment gateway ID
     * @return string | $title
     */
    public function display_discount_on_gateway_title( $title, $id ) {
        if ( ! is_object( WC()->cart ) ) {
            return $title;
        }
    
        $discount_settings = maybe_unserialize( get_option('woo_custom_installments_discounts_setting') );
    
        if ( Init::get_setting('display_tag_discount_price_checkout') !== 'yes' ) {
            return $title;
        }
    
        // Check if there are products in the cart with the "enable_discount_per_unit" option set to 'yes'.
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $product = $cart_item['data'];
            $enable_discount = get_post_meta( $product->get_id(), 'enable_discount_per_unit', true ) === 'yes';
            $disable_discount = get_post_meta( $product->get_id(), '__disable_discount_main_price', true ) === 'yes';
            $discount_gateway = get_post_meta( $product->get_id(), 'discount_gateway', true ); // Gets the payment method configured on the product.
    
            if ( $enable_discount && ! $disable_discount && $discount_gateway === $id ) {
                $product_discount = get_post_meta( $product->get_id(), 'unit_discount_amount', true );
                $product_discount_method = get_post_meta( $product->get_id(), 'discount_per_unit_method', true );

                $value = $product_discount_method === 'percentage' ? $product_discount . '%' : wc_price( $product_discount );
                
                // Display the product-specific discount.
                $title .= '<span class="badge-discount-checkout">' . sprintf( __( '%s off', 'woo-custom-installments' ), $value ) . '</span>';
                return $title;
            }
        }
    
        if ( isset( $discount_settings[ $id ]['amount'] ) && $discount_settings[ $id ]['amount'] > 0 ) {
            $value = $discount_settings[ $id ]['type'] == 'percentage' ? $discount_settings[ $id ]['amount'] . '%' : wc_price( $discount_settings[ $id ]['amount'] );
            $title .= '<span class="badge-discount-checkout">' . sprintf( __( '%s off', 'woo-custom-installments' ), $value ) . '</span>';
        }
    
        return $title;
    }


    /**
     * Add discount
     * 
     * @since 2.6.0
     * @version 3.0.0
     * @param $cart | WC_Cart object
     * @return void
     */
    public function add_discount( $cart ) {
        if ( is_admin() && ! defined('DOING_AJAX') || is_cart() ) {
            return;
        }
    
        $discount_applied = false;
        $products_with_discounts = [];

        // Apply discounts to individual products
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            $product = $cart_item['data'];
            $product_id = $product->get_id();
            $discount_gateway = get_post_meta( $product_id, 'discount_gateway', true );
            $chosen_payment_method = WC()->session->chosen_payment_method;

            $discounted_price = Calculate_Values::get_discounted_cart_item_price( $product, $cart_item['quantity'] );

            if ( $discounted_price < $product->get_price() * $cart_item['quantity'] && $discount_gateway === $chosen_payment_method ) {
                $discount_amount = $product->get_price() * $cart_item['quantity'] - $discounted_price;
                $gateway = WC()->payment_gateways->payment_gateways()[WC()->session->chosen_payment_method];
                $discount_name = $this->discount_name( $discount_amount, $gateway );

                if ( $discount_amount > 0 ) {
                    $cart->add_fee( $discount_name, -$discount_amount, false );
                    $discount_applied = true;
                }
            }
        }

        // Apply global discount if no individual product discount was applied
        if ( ! $discount_applied && isset( maybe_unserialize( get_option('woo_custom_installments_discounts_setting') )[WC()->session->chosen_payment_method] ) ) {
            $total_discount = Calculate_Values::calculate_total_discount( $cart, Init::get_setting('include_shipping_value_in_discounts') === 'yes' );

            if ( $total_discount > 0 ) {
                $gateway = WC()->payment_gateways->payment_gateways()[WC()->session->chosen_payment_method];
                $discount_name = $this->discount_name( $total_discount, $gateway );
                $cart->add_fee( $discount_name, -$total_discount, true );
            }
        }
    }


    /**
     * Remove the discount in the payment method title
     * 
     * @since 2.0.0
     * @param $order_id | ID of order
     * @return void
     */
    public function woo_custom_installments_update_order_data( $order_id ) {
        $payment_method_title = get_post_meta( $order_id, '_payment_method_title', true );
        $new_payment_method_title = preg_replace( '/<small>.*<\/small>/', '', $payment_method_title );

        // Save the new payment method title.
        update_post_meta( $order_id, '_payment_method_title', sanitize_text_field( $new_payment_method_title ) );
    }


    /**
     * Set discount per quantity
     * 
     * @since 2.7.2
     * @param $cart | WC_Cart object
     * @return void
     */
    public function set_discount_per_quantity( $cart ) {
        if ( is_admin() && ! defined('DOING_AJAX') ) {
            return;
        }
    
        $total_discount = 0;
    
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            $product = $cart_item['data'];
            $quantity = $cart_item['quantity'];
            $disable_discount = get_post_meta( $product->get_id(), '__disable_discount_main_price', true ) == 'yes';
            $parent_id = $product->get_parent_id();
            $disable_discount_in_parent = get_post_meta( $parent_id, '__disable_discount_main_price', true ) == 'yes';

            if ( !$disable_discount && !$disable_discount_in_parent ) {
                $cart_item_discount = $this->calculate_quantity_discount( $product, $quantity );
                $total_discount += $cart_item_discount;
            }
        }
    
        if ( $total_discount > 0 ) {
            wc()->cart->add_fee( __('Desconto por quantidade', 'woo-custom-installments'), - $total_discount );
        }
    }


    /**
     * Calculate the discount amount for quantity-based discounts
     * 
     * @since 4.5.2
     * @param WC_Product $product | Product object
     * @param int $quantity | Product quantity in the cart
     * @return float | Total discount for the product
     */
    protected function calculate_quantity_discount( $product, $quantity ) {
        $global_discount_method = Init::get_setting('discount_per_quantity_method');
        $global_discount_value = Init::get_setting('value_for_discount_per_quantity');
        $minimum_quantity_global = Init::get_setting('set_quantity_enable_discount');

        $discount_method_single = get_post_meta( $product->get_id(), 'discount_per_quantity_method', true );
        $discount_value_single = get_post_meta( $product->get_id(), 'quantity_discount_amount', true );
        $minimum_quantity_single = get_post_meta( $product->get_id(), 'minimum_quantity_discount', true );

        if ( $quantity >= $minimum_quantity_single && $discount_value_single ) {
            return Calculate_Values::calculate_price_with_discount( $product->get_price() * $quantity, $discount_method_single, $discount_value_single );
        } elseif ( $quantity >= $minimum_quantity_global && $global_discount_value ) {
            return Calculate_Values::calculate_price_with_discount( $product->get_price() * $quantity, $global_discount_method, $global_discount_value );
        }

        return 0;
    }

	
    /**
     * Generate the discount name
     * 
     * @since 2.0.0
     * @param $value | Discount value
     * @param $gateway | Payment gateway
     * @return string
     */
    protected function discount_name( $value, $gateway ) {
		if ( strstr( $value, '%' ) ) {
			return sprintf( __( 'Desconto para %s (%s off)', 'woo-custom-installments' ), esc_attr( $gateway->title ), $value );
		}

		return sprintf( __( 'Desconto para %s', 'woo-custom-installments' ), esc_attr( $gateway->title ) );
	}
}

if ( License::is_valid() && Init::get_setting('enable_all_discount_options') === 'yes' ) {
    new Discounts();
}