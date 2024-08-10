<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for add interests for payment method
 * 
 * @since 2.3.5
 * @version 4.2.0
 * @package MeuMouse.com
 */
class Interests {

	public function __construct() {
		if ( License::is_valid() ) {
			add_filter( 'woocommerce_gateway_title', array( $this, 'woo_custom_installments_payment_method_title_interest' ), 10, 2 );
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'woo_custom_installments_update_order_data_interests' ), 10 );
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
	 * @return string $title
	 */
	public function woo_custom_installments_payment_method_title_interest( $title, $id ) {
		if ( ! is_checkout() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $title;
		}

		$insterestSettings = get_option( 'woo_custom_installments_interests_setting' );
		$insterestSettings = maybe_unserialize( $insterestSettings );

		if ( isset( $insterestSettings[ $id ]['amount'] ) && 0 < $insterestSettings[ $id ]['amount'] ) {
			$discount = $insterestSettings[ $id ]['amount'];

			if ( $insterestSettings[ $id ]['type'] == 'percentage' ) {
				$value = $discount . '%';
			} else {
				$value = wc_price( $discount );
			}

			if ( Init::get_setting('display_tag_interest_checkout') === 'yes' ) {
				$title .= '<span class="badge-interest-checkout">' . sprintf( __( '%s juros', 'woo-custom-installments' ), $value ) . '</span>';
			} else {
				$title .= '';
			}
		}

		return $title;
	}


	/**
	 * Add discount
	 * 
	 * @since 2.3.5
	 * @return void
	 */
	public function woo_custom_installments_add_interest( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) || is_cart() ) {
			return;
		}
	
		// Gets the discountSettings.
		$gateways = get_option( 'woo_custom_installments_interests_setting' );
		$gateways = maybe_unserialize( $gateways );

		if ( isset( $gateways[ WC()->session->chosen_payment_method ] ) ) {
			$value = $gateways[ WC()->session->chosen_payment_method ]['amount'];
			$type = $gateways[ WC()->session->chosen_payment_method ]['type'];
	
			if ( apply_filters( 'woo_custom_installments_apply_interest', 0 < $value, $cart ) ) {
				$payment_gateways = WC()->payment_gateways->payment_gateways();
				$gateway = $payment_gateways[ WC()->session->chosen_payment_method ];
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
	 * @return void
	 */
	public function woo_custom_installments_update_order_data_interests( $order_id ) {
		$payment_method_title = get_post_meta( $order_id, '_payment_method_title', true );
		$new_payment_method_title = preg_replace( '/<small>.*<\/small>/', '', $payment_method_title );
		
		// Save the new payment method title.
		$new_payment_method_title = sanitize_text_field( $new_payment_method_title );
		update_post_meta( $order_id, '_payment_method_title', $new_payment_method_title );
	}

}

if ( License::is_valid() && Init::get_setting('enable_all_interest_options') === 'yes' ) {
	new Interests();
}