<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\API\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for add interests for payment method
 * 
 * @since 2.3.5
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Interests {

	/**
	 * Construct function
	 * 
	 * @since 2.3.5
	 * @version 5.2.5
	 * @return void
	 */
	public function __construct() {
		if ( License::is_valid() && Admin_Options::get_setting('enable_all_interest_options') === 'yes' ) {
			if ( ! is_admin() ) {
				add_filter( 'woocommerce_gateway_title', array( $this, 'payment_method_title' ), 10, 2 );
			}

			add_action( 'woocommerce_checkout_create_order', array( $this, 'set_original_payment_title' ), 10 );
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'woo_custom_installments_add_interest' ), 10 );
		}
	}


	/**
	 * Calcule the discount amount
	 * 
	 * @since 2.3.5
	 * @return string $value
	 */
	protected function calculate_discount( $type, $value, $subtotal ) {
		if ( $type == 'percentage' ) {
			$value = ( $subtotal / 100 ) * ( $value );
		}

		return $value;
	}


	/**
	 * Generate the discount name
	 * 
	 * @since 2.3.5
	 * @return string
	 */
	protected function discount_name( $value, $gateway ) {
		if ( strstr( $value, '%' ) ) {
			return sprintf( __( 'Juros para %s (%s)', 'woo-custom-installments' ), esc_attr( $gateway->title ), $value );
		}

		return sprintf( __( 'Juros para %s', 'woo-custom-installments' ), esc_attr( $gateway->title ) );
	}


	/**
	 * Display the discount in payment method title
	 * 
	 * @since 2.3.5
	 * @version 5.4.0
	 * @param string $title | Payment method title
	 * @param string $id | Payment method ID
	 * @return string payment method title with discount
	 */
	public function payment_method_title( $title, $id ) {
		if ( ! is_checkout() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $title;
		}

		// If the option to display the interest in the checkout is not enabled, return the title
		if ( Admin_Options::get_setting('display_tag_interest_checkout') === 'yes' ) {
			$interest_settings = get_option( 'woo_custom_installments_interests_setting' );
			$interest_settings = maybe_unserialize( $interest_settings );

			if ( isset( $interest_settings[ $id ]['amount'] ) && 0 < $interest_settings[ $id ]['amount'] ) {
				$discount = $interest_settings[ $id ]['amount'];

				if ( $interest_settings[ $id ]['type'] == 'percentage' ) {
					$value = $discount . '%';
				} else {
					$value = wc_price( $discount );
				}

				return $title .= '<span class="badge-interest-checkout">' . sprintf( __( '%s juros', 'woo-custom-installments' ), $value ) . '</span>';
			}
		}

		return $title;
	}


	/**
	 * Add discount
	 * 
	 * @since 2.3.5
	 * @version 5.4.0
	 * @return void
	 */
	public function woo_custom_installments_add_interest( $cart ) {
		if ( is_admin() && ! defined('DOING_AJAX') || is_cart() ) {
			return;
		}
	
		// Gets the payment gateways settings
		$gateways = maybe_unserialize( get_option('woo_custom_installments_interests_setting') );
		$selected_gateway = WC()->session->chosen_payment_method;

		if ( isset( $gateways[ $selected_gateway ] ) ) {
			$value = $gateways[ $selected_gateway ]['amount'];
			$type = $gateways[ $selected_gateway ]['type'];
	
			/**
			 * Apply interest filter
			 * 
			 * @since 2.3.5
			 * @version 5.4.0
			 * @param float $value | Interest value
			 * @param object $cart | Cart object
			 * @return float $interest
			 */
			$interest = apply_filters( 'Woo_Custom_Installments/Cart/Apply_Interest', $value, $cart );

			// If the interest is not numeric or less than or equal to 0, return
			if ( is_numeric( $interest ) && $interest > 0 ) {
				$payment_gateways = WC()->payment_gateways->payment_gateways();
				$gateway = $payment_gateways[ $selected_gateway ];
				$discount_name = $this->discount_name( $value, $gateway );
	
				// Add the shipping total to the cart total to calculate the discount.
				$cart_total = $cart->cart_contents_total + $cart->get_shipping_total();
				$cart_discount = $this->calculate_discount( $type, $value, $cart_total ) * + 1;
	
				$cart->add_fee( $discount_name, $cart_discount, true );
			}
		}
	}
	

	/**
	 * Remove the discount in the payment method title
	 * 
	 * @since 2.3.5
	 * @version 5.2.5
	 * @param object $order | Order object
	 * @return void
	 */
	public function set_original_payment_title( $order ) {
		$payment_method = $order->get_payment_method();
		$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();

		if ( isset( $payment_gateways[ $payment_method ] ) ) {
			$clean_title = isset( $payment_gateways[ $payment_method ]->settings['title'] ) 
				? $payment_gateways[ $payment_method ]->settings['title'] 
				: $payment_gateways[ $payment_method ]->get_title();

			$order->set_payment_method_title( $clean_title );
		}
	}
}