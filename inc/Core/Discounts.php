<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\API\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for add discounts in the cart
 * 
 * @since 2.0.0
 * @version 5.4.8
 * @package MeuMouse\Woo_Custom_Installments\Core
 * @author MeuMouse.com
 */
class Discounts {

	/**
	 * Construct function
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @return void
	 */
	public function __construct() {
		if ( License::is_valid() && Admin_Options::get_setting('enable_all_discount_options') === 'yes' ) {
			if ( ! is_admin() ) {
				add_filter( 'woocommerce_gateway_title', array( $this, 'payment_method_title' ), 10, 2 );
			}
			
			add_action( 'woocommerce_checkout_create_order', array( $this, 'set_original_payment_title' ), 10 );
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_discounts' ), 10 );

			// Enable discount per quantity for all products
			if ( Admin_Options::get_setting('enable_functions_discount_per_quantity') === 'yes' ) {
				add_action( 'woocommerce_before_calculate_totals', array( $this, 'set_discount_per_quantity' ) );
			}
		}
	}


	/**
	 * Display the discount in payment method title
	 * 
	 * @since 2.0.0
	 * @version 5.4.8
	 * @param $title | Payment gateway title
	 * @param $gateway_id | Payment gateway ID
	 * @return string | $title
	 */
	public function payment_method_title( $title, $gateway_id ) {
		if ( ! is_object( WC()->cart ) || ! is_checkout() || Admin_Options::get_setting('display_tag_discount_price_checkout') !== 'yes' ) {
			return $title;
		}

		$total_discount = $this->get_combined_discount_total( $gateway_id );

		if ( $total_discount > 0 ) {
			$badge = '<span class="badge-discount-checkout">' . sprintf( __( '%s off', 'woo-custom-installments' ), wc_price( $total_discount ) ) . '</span>';
			
			return $title . ' ' . $badge;
		}

		return $title;
	}


	/**
	 * Get total discount amount for the given payment method
	 * 
	 * @since 5.4.8
	 * @param string $gateway_id
	 * @return float
	 */
	protected function get_combined_discount_total( $gateway_id ) {
		$cart = WC()->cart;
		$total_discount = 0;
		$global_base = 0;

		if ( ! $cart || empty( $gateway_id ) ) {
			return 0;
		}

		foreach ( $cart->get_cart() as $item ) {
			$product = $item['data'];
			$product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();
			$qty = (int) $item['quantity'];
			$price = (float) $product->get_price();

			// Individual discount
			$enabled = get_post_meta( $product_id, 'enable_discount_per_unit', true ) === 'yes';
			$method = get_post_meta( $product_id, 'discount_per_unit_method', true );
			$amount = get_post_meta( $product_id, 'unit_discount_amount', true );
			$target_gateway = get_post_meta( $product_id, 'discount_gateway', true );
			$disabled = get_post_meta( $product_id, '__disable_discount_main_price', true ) === 'yes';

			if ( $enabled && $target_gateway === $gateway_id && is_numeric( $amount ) ) {
				if ( $method === 'percentage' ) {
					$total_discount += $price * (float) $amount / 100 * $qty;
				} elseif ( $method === 'fixed' ) {
					$total_discount += (float) $amount * $qty;
				}
			} elseif ( ! $disabled ) {
				$global_base += $price * $qty;
			}
		}

		// Apply global gateway discount
		$settings = maybe_unserialize( get_option('woo_custom_installments_discounts_setting') );

		if ( isset( $settings[ $gateway_id ]['amount'] ) && $global_base > 0 ) {
			$value = (float) $settings[ $gateway_id ]['amount'];
			$type  = $settings[ $gateway_id ]['type'];
			$global_discount = ( $type === 'percentage' ) ? $global_base * $value / 100 : $value;

			$total_discount += $global_discount;
		}

		return $total_discount;
	}


	/**
	 * Add discount
	 *
	 * @since 2.6.0
	 * @version 5.4.8
	 * @param WC_Cart $cart | Cart object
	 * @return void
	 */
	public function add_discounts( $cart ) {
		if ( is_admin() && ! defined('DOING_AJAX') || is_cart() ) {
			return;
		}

		$gateway_id = WC()->session->chosen_payment_method;
		$individual_discount_total = 0;
		$global_discount_base = 0;

		foreach ( $cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'];
			$product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();
			$quantity = $cart_item['quantity'];
			$unit_price = (float) $product->get_price();
			$line_total = $unit_price * $quantity;

			// Individual discount
			$enable = get_post_meta( $product_id, 'enable_discount_per_unit', true ) === 'yes';
			$gateway_for_product = get_post_meta( $product_id, 'discount_gateway', true );
			$discount_method = get_post_meta( $product_id, 'discount_per_unit_method', true );
			$discount_value = get_post_meta( $product_id, 'unit_discount_amount', true );

			if (
				$enable &&
				$gateway_for_product === $gateway_id &&
				in_array( $discount_method, [ 'percentage', 'fixed' ], true ) &&
				is_numeric( $discount_value )
			) {
				if ( $discount_method === 'percentage' ) {
					$individual_discount_total += ( $unit_price * (float) $discount_value / 100 ) * $quantity;
				} else {
					$individual_discount_total += (float) $discount_value * $quantity;
				}
			} else {
				// check if the product is disabled in global
				$disable_main = get_post_meta( $product_id, '__disable_discount_main_price', true ) === 'yes';
				$disable_parent = get_post_meta( $product->get_parent_id(), '__disable_discount_main_price', true ) === 'yes';

				if ( ! $disable_main && ! $disable_parent ) {
					$global_discount_base += $line_total;
				}
			}
		}

		if ( $individual_discount_total > 0 ) {
			$this->apply_individual_discount_fee( $individual_discount_total );
		}

		if ( $global_discount_base > 0 ) {
			$this->apply_gateway_discount_on_amount( $global_discount_base, $gateway_id );
		}
	}


	/**
	 * Calculate and return the sum of all individual product discounts
	 *
	 * @since 5.4.0
	 * @version 5.4.8
	 * @param WC_Cart $cart | Cart object
	 * @return float Total discount amount for individual products
	 */
	protected function get_total_individual_discount( $cart ) {
		$total_discount = 0;
		$gateway_id = WC()->session->chosen_payment_method;

		// Loop through each cart item to compute per-item discount
		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			$product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();

			// Check if this product has individual discount enabled
			$enable_flag = get_post_meta( $product_id, 'enable_discount_per_unit', true ) === 'yes';

			if ( ! $enable_flag ) {
				continue;
			}

			$gateway_for_product = get_post_meta( $product_id, 'discount_gateway', true );
			$method_per_unit = get_post_meta( $product_id, 'discount_per_unit_method', true ); // 'percentage' or 'fixed'
			$amount_per_unit = get_post_meta( $product_id, 'unit_discount_amount', true ); // string or float
			$quantity = (int) $cart_item['quantity'];
			$unit_price = (float) $product->get_price();

			// Skip if gateway or method is invalid or doesn't match chosen payment
			if ( empty( $amount_per_unit )
				|| ( $method_per_unit !== 'percentage' && $method_per_unit !== 'fixed' )
				|| $gateway_for_product !== $gateway_id
			) {
				continue;
			}

			// Calculate discount for this cart item
			if ( $method_per_unit === 'percentage' ) {
				$per_unit_discount = ( $unit_price * (float) $amount_per_unit ) / 100;
				$item_discount = $per_unit_discount * $quantity;
			} else { // fixed
				$item_discount = (float) $amount_per_unit * $quantity;
			}

			if ( $item_discount > 0 ) {
				$total_discount += $item_discount;
			}
		}

		return $total_discount;
	}


	/**
	 * Apply a single fee line for the sum of all individual product discounts
	 *
	 * @since 5.4.0
	 * @version 5.4.8
	 * @param float $amount | Total discount amount to apply (positive number)
	 * @return void
	 */
	protected function apply_individual_discount_fee( $amount ) {
		$label = esc_html__( 'Desconto exclusivo', 'woo-custom-installments' );

		// Add fee as negative value; taxes disabled (false)
		wc()->cart->add_fee( $label, -1 * $amount, false );
	}


	/**
	 * Apply a single fee line for the sum of all individual product discounts
	 * 
	 * @since 5.4.8
	 * @param float $amount_base | Total amount to apply discount on
	 * @param string $gateway_id | Gateway ID
	 * @return void
	 */
	protected function apply_gateway_discount_on_amount( $amount_base, $gateway_id ) {
		$gateways_settings = maybe_unserialize( get_option('woo_custom_installments_discounts_setting') );

		if ( ! isset( $gateways_settings[ $gateway_id ] ) ) {
			return;
		}

		$value = $gateways_settings[ $gateway_id ]['amount'];
		$type = $gateways_settings[ $gateway_id ]['type'];

		if ( ! is_numeric( $value ) || $value <= 0 ) {
			return;
		}

		if ( $type === 'percentage' ) {
			$discount = ( $amount_base * $value ) / 100;
		} else {
			$discount = (float) $value;
		}

		if ( $discount <= 0 ) {
			return;
		}

		$label = esc_html__( 'Desconto por forma de pagamento', 'woo-custom-installments' );

		// apply discount
		wc()->cart->add_fee( $label, -1 * $discount, true );
	}


	/**
	 * Calculate and apply discount based on chosen payment gateway settings
	 *
	 * @since 5.4.0
	 * @version 5.4.8
	 * @param WC_Cart $cart | Cart object
	 * @return void
	 */
	protected function apply_gateway_discount( $cart ) {
		$gateways_settings = maybe_unserialize( get_option('woo_custom_installments_discounts_setting') );
		$gateway_id = WC()->session->chosen_payment_method;

		if ( ! isset( $gateways_settings[ $gateway_id ] ) ) {
			return;
		}

		$value = $gateways_settings[ $gateway_id ]['amount'];
		$type = $gateways_settings[ $gateway_id ]['type']; // 'percentage' or 'fixed'

		/**
		 * Filter to allow custom logic for gateway discount calculation.
		 *
		 * @param float $value | Discount value from settings
		 * @param WC_Cart $cart | Cart object
		 * @return float Modified discount value
		 */
		$discount_value = apply_filters( 'Woo_Custom_Installments/Cart/Apply_Discount', $value, $cart );

		if ( ! is_numeric( $discount_value ) || $discount_value <= 0 ) {
			return;
		}

		// Calculate total cart value excluding disabled products
		$total_cart_value = 0.0;

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			$disable_main = get_post_meta( $product->get_id(), '__disable_discount_main_price', true ) === 'yes';
			$disable_parent  = get_post_meta( $product->get_parent_id(), '__disable_discount_main_price', true ) === 'yes';

			if ( ! $disable_main && ! $disable_parent ) {
				$total_cart_value += (float) $product->get_price() * (int) $cart_item['quantity'];
			}
		}

		// Optionally include shipping in discount base
		if ( Admin_Options::get_setting('include_shipping_value_in_discounts') === 'yes' ) {
			$total_cart_value += $cart->get_shipping_total();
		}

		// Compute discount amount based on percentage or fixed
		if ( Admin_Options::get_setting('product_price_discount_method') === 'percentage' ) {
			$discount_amount = ( $total_cart_value / 100 ) * (float) $discount_value;
		} else {
			$discount_amount = (float) $discount_value;
		}

		if ( $discount_amount <= 0 ) {
			return;
		}

		// Build fee label
		$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$raw_gateway_title = '';

		// check if gateway exists in array
		if ( isset( $payment_gateways[ $gateway_id ] ) ) {
			$current_gateway = $payment_gateways[ $gateway_id ];

			if ( isset( $current_gateway->settings['title'] ) && $current_gateway->settings['title'] !== '' ) {
				$raw_gateway_title = $current_gateway->settings['title'];
			} else {
				$raw_gateway_title = $current_gateway->get_title();
			}
		}

		if ( (string) $raw_gateway_title ) {
			$label = sprintf( __( 'Desconto para %s', 'woo-custom-installments' ), $raw_gateway_title );
		} else {
			$label = __( 'Desconto por forma de pagamento', 'woo-custom-installments' );
		}

		// Add fee as negative value; taxes enabled (true)
		wc()->cart->add_fee( $label, -1 * $discount_amount, true );
	}


	/**
	 * Remove the discount in the payment method title
	 * 
	 * @since 2.0.0
	 * @version 5.2.5
	 * @param object $order | Object order
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


	/**
	 * Set discount per quantity
	 * 
	 * @since 2.7.2
	 * @version 5.4.8
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
			$product_id = $product->get_id();
			$disable_discount = get_post_meta( $product_id, '__disable_discount_main_price', true ) == 'yes';
			$disable_discount_in_parent = get_post_meta( $product->get_parent_id(), '__disable_discount_main_price', true ) == 'yes';
	
			// check if the product or its parent has the discount disabled
			if ( ! $disable_discount && ! $disable_discount_in_parent ) {
				$quantity = $cart_item['quantity'];
	
				// global discount options
				$discount_method = Admin_Options::get_setting('discount_per_quantity_method');
				$discount_value = Admin_Options::get_setting('value_for_discount_per_quantity');
				$minimum_quantity = Admin_Options::get_setting('set_quantity_enable_discount');
	
				// single product discount options
				$discount_method_single = get_post_meta( $product_id, 'discount_per_quantity_method', true );
				$discount_value_single = get_post_meta( $product_id, 'quantity_discount_amount', true );
				$minimum_quantity_single = get_post_meta( $product_id, 'minimum_quantity_discount', true );
	
				if ( $quantity >= $minimum_quantity_single && $discount_value_single ) {
					$price = $product->get_price();
	
					if ( $discount_method_single === 'percentage' ) {
						$discounted_price = $price - ( $price * ( $discount_value_single / 100 ) );
					} else {
						$discounted_price = $price - $discount_value_single;
					}
	
					$discount = $price - $discounted_price;
					$cart_item_discount = $discount;
	
					if ( Admin_Options::get_setting('enable_discount_per_unit_discount_per_quantity') === 'yes' ) {
						$cart_item_discount = $discount * $quantity;
					}
	
					$total_discount += $cart_item_discount;
				} elseif ( $quantity >= $minimum_quantity && $discount_value ) {
					$price = $product->get_price();
	
					// check discount method
					if ( $discount_method == 'percentage' ) {
						$discounted_price = $price - ( $price * ( $discount_value / 100 ) );
					} else {
						$discounted_price = $price - $discount_value;
					}
	
					$discount = $price - $discounted_price;
					$cart_item_discount = $discount;
	
					// check if option discount per unit is activated
					if ( Admin_Options::get_setting('enable_discount_per_unit_discount_per_quantity') === 'yes') {
						$cart_item_discount = $discount * $quantity;
					}
	
					$total_discount += $cart_item_discount;
				}
			}
		}
	
		if ( $total_discount > 0 ) {
			$label = esc_html__('Desconto por quantidade', 'woo-custom-installments');
	
			// Check if the option to disable discounts is active for the product
			if ( $disable_discount || $disable_discount_in_parent ) {
				wc()->cart->remove_fee( $label );
			} else {
				wc()->cart->add_fee( $label, - $total_discount );
			}
		}
	}
}