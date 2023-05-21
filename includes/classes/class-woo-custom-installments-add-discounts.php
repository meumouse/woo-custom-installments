<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Woo_Custom_Installments_Discounts extends Woo_Custom_Installments_Init {

	public function __construct() {
		parent::__construct();
		$options = get_option( 'woo-custom-installments-setting' );
		$licenseValid = get_option( 'license_status' ) == 'valid';

		if( $licenseValid ) {
			add_filter( 'woocommerce_gateway_title', array( $this, 'woo_custom_installments_payment_method_title' ), 10, 2 );
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'woo_custom_installments_update_order_data' ), 10 );

			if( isset( $options['display_info_discount_order_review_checkout'] ) && $options['display_info_discount_order_review_checkout'] == 'yes' ) {
				add_action( 'woocommerce_cart_calculate_fees', array( $this, 'woo_custom_installments_add_discount' ), 10 );
			}
		} else {
			remove_action( 'woocommerce_cart_calculate_fees', array( $this, 'woo_custom_installments_add_discount' ), 10 );
			remove_filter( 'woocommerce_gateway_title', array( $this, 'woo_custom_installments_payment_method_title' ), 10, 2 );
			remove_action( 'woocommerce_checkout_order_processed', array( $this, 'woo_custom_installments_update_order_data' ), 10 );
		}
		
	}


	/**
	 * Calcule the discount amount.
	 */
	protected function calculate_discount( $type, $value, $subtotal ) {
		if ( $type == 'percentage' ) {
			$value = ( $subtotal / 100 ) * ( $value );
		}

		return $value;
	}

	/**
	 * Generate the discount name.
	 */
	protected function discount_name( $value, $gateway ) {
		if ( strstr( $value, '%' ) ) {
			return sprintf( __( 'Desconto para %s (%s off)', 'woo-custom-installments' ), esc_attr( $gateway->title ), $value );
		}

		return sprintf( __( 'Desconto para %s', 'woo-custom-installments' ), esc_attr( $gateway->title ) );
	}

	/**
	 * Display the discount in payment method title
	 * 
	 * @since 2.0.0
	 * @access public
	 */
	public function woo_custom_installments_payment_method_title( $title, $id ) {
		if ( ! is_checkout() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $title;
		}

		$discountSettings = get_option( 'woo_custom_installments_discounts_setting' );
		$discountSettings = maybe_unserialize( $discountSettings );
		$options = get_option( 'woo-custom-installments-setting' );
		if ( isset( $discountSettings[ $id ]['amount'] ) && 0 < $discountSettings[ $id ]['amount'] ) {
			$discount = $discountSettings[ $id ]['amount'];
			if ( $discountSettings[ $id ]['type'] == 'percentage' ) {
				$value = $discount . '%';
			} else {
				$value = wc_price( $discount );
			}
			if( $options['display_tag_discount_price_checkout'] == 'yes' ) {
				$title .= '<span class="badge-discount-checkout">' . sprintf( __( '%s off', 'woo-custom-installments' ), $value ) . '</span>';
			} else {
				$title .= '';
			}
		}
			return $title;
	}

	/**
	 * Add discount
	 * 
	 * @since 2.0.0
	 * @access public
	 */
	public function woo_custom_installments_add_discount( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) || is_cart() ) {
			return;
		}
	
		// Gets the discountSettings.
		$gateways = get_option( 'woo_custom_installments_discounts_setting' );
		$gateways = maybe_unserialize( $gateways );
		if ( isset( $gateways[ WC()->session->chosen_payment_method ] ) ) {
			$value = $gateways[ WC()->session->chosen_payment_method ]['amount'];
			$type = $gateways[ WC()->session->chosen_payment_method ]['type'];
	
			if ( apply_filters( 'woo_payment_discounts_apply_discount', 0 < $value, $cart ) ) {
				$payment_gateways = WC()->payment_gateways->payment_gateways();
				$gateway = $payment_gateways[ WC()->session->chosen_payment_method ];
				$discount_name = $this->discount_name( $value, $gateway );
	
				// Add the shipping total to the cart total to calculate the discount.
				$cart_total = $cart->cart_contents_total + $cart->get_shipping_total();
				$cart_discount = $this->calculate_discount( $type, $value, $cart_total ) * - 1;
	
				$cart->add_fee( $discount_name, $cart_discount, true );
			}
		}
	}
	

	/**
	 * Remove the discount in the payment method title
	 * 
	 * @since 2.0.0
	 * @access public
	 */
	public function woo_custom_installments_update_order_data( $order_id ) {
		$payment_method_title     = get_post_meta( $order_id, '_payment_method_title', true );
		$new_payment_method_title = preg_replace( '/<small>.*<\/small>/', '', $payment_method_title );
		// Save the new payment method title.
		$new_payment_method_title = sanitize_text_field( $new_payment_method_title );
		update_post_meta( $order_id, '_payment_method_title', $new_payment_method_title );
	}

}

new Woo_Custom_Installments_Discounts();