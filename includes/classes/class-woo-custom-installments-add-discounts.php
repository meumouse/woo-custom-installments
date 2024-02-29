<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class for add discounts in the cart
 * 
 * @package MeuMouse.com
 * @since 2.0.0
 */
class Woo_Custom_Installments_Discounts extends Woo_Custom_Installments_Init {

	public function __construct() {
		parent::__construct();

		// check if license exists
		if ( get_option( 'woo_custom_installments_license_status' ) === 'valid' ) {
			add_filter( 'woocommerce_gateway_title', array( $this, 'woo_custom_installments_payment_method_title' ), 10, 2 );
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'woo_custom_installments_update_order_data' ), 10 );
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'woo_custom_installments_add_discount' ), 10 );

		} else {
			remove_action( 'woocommerce_cart_calculate_fees', array( $this, 'woo_custom_installments_add_discount' ), 10 );
			remove_filter( 'woocommerce_gateway_title', array( $this, 'woo_custom_installments_payment_method_title' ), 10, 2 );
			remove_action( 'woocommerce_checkout_order_processed', array( $this, 'woo_custom_installments_update_order_data' ), 10 );
		}

		/**
		 * Enable discount per quantity for all products
		 * 
		 * @since 2.7.2
		 */
		if ( Woo_Custom_Installments_Init::get_setting('enable_functions_discount_per_quantity') === 'yes' && get_option( 'woo_custom_installments_license_status' ) === 'valid' ) {
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'set_discount_per_quantity' ) );
		} else {
			remove_action( 'woocommerce_before_calculate_totals', array( $this, 'set_discount_per_quantity' ) );
		}
	}


	/**
	 * Calcule the discount amount
	 * 
	 * @since 2.0.0
	 * @param $type | percentage or fixed
	 * @param $value | Cart value
	 * @param $subtotal | Cart subtotal
	 * @return string
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


	/**
	 * Display the discount in payment method title
	 * 
	 * @since 2.0.0
	 * @param $title | Payment gateway title
	 * @param $id | Payment gateway ID
	 * @return string | $title
	 */
	public function woo_custom_installments_payment_method_title( $title, $id ) {
		if ( ! is_object( WC()->cart ) ) {
			return $title;
		}
	
		$discountSettings = get_option( 'woo_custom_installments_discounts_setting' );
		$discountSettings = maybe_unserialize( $discountSettings );
		$product_discount = 0;
		$product_discount_method = '';
	
		if ( Woo_Custom_Installments_Init::get_setting('display_tag_discount_price_checkout') !== 'yes' ) {
			return $title;
		}
	
		// Initially set $is_discount_eligible_product_exists to false.
		$is_discount_eligible_product_exists = false;
		$current_payment_method = $id; // Gets the current payment method.
	
		// Check if there are products in the cart with the "enable_discount_per_unit" option set to 'yes'.
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			$enable_discount = get_post_meta( $product->get_id(), 'enable_discount_per_unit', true ) == 'yes';
			$disable_discount = get_post_meta( $product->get_id(), '__disable_discount_main_price', true ) == 'yes';
			$discount_gateway = get_post_meta( $product->get_id(), 'discount_gateway', true ); // Gets the payment method configured on the product.
	
			if ( $enable_discount && ! $disable_discount ) {
				if ( $discount_gateway === $current_payment_method ) {
					// If an eligible product is found with the same discount as your current payment method, display the product discount and return.
					$product_discount = get_post_meta( $product->get_id(), 'unit_discount_amount', true );
					$product_discount_method = get_post_meta( $product->get_id(), 'discount_per_unit_method', true );
	
					// Verificar se o método de desconto é "percentage" em "discount_per_unit_method".
					if ( $product_discount_method === 'percentage' ) {
						$value = $product_discount . '%';
					} else {
						$value = wc_price( $product_discount );
					}
	
					// Display the product-specific discount.
					$title .= '<span class="badge-discount-checkout">' . sprintf( __( '%s off', 'woo-custom-installments' ), $value ) . '</span>';
					
					return $title; // Returns the title with the product discount.
				}
	
				// If it is not the same payment method, set $is_discount_eligible_product_exists to true to indicate that there is an eligible product discount.
				$is_discount_eligible_product_exists = true;
			}
		}
	
		// Check if $is_discount_eligible_product_exists is true and display the product-specific discount.
		if ( $is_discount_eligible_product_exists ) {
			// Display the global discount only if no eligible products with a specific discount are found.
			return $title;
		}
	
		if ( isset( $discountSettings[ $id ]['amount'] ) && $discountSettings[ $id ]['amount'] > 0 ) {
			$discount = $discountSettings[ $id ]['amount'];
	
			if ( $discountSettings[ $id ]['type'] == 'percentage' ) {
				$value = $discount . '%';
			} else {
				$value = wc_price( $discount );
			}
	
			// Only display the global discount if no eligible products with a specific discount are found.
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
	public function woo_custom_installments_add_discount( $cart ) {
		if ( is_admin() && !defined('DOING_AJAX') || is_cart() ) {
			return;
		}
	
		// Gets the discountSettings.
		$gateways = get_option('woo_custom_installments_discounts_setting');
		$gateways = maybe_unserialize( $gateways );

		// Flag to check if a discount has already been applied
		$discount_applied = false;
		// Get the list of products with individual discount
		$products_with_discounts = array();
	
		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			$product_id = $product->get_id();
			$discount_per_product = get_post_meta($product->get_id(), 'enable_discount_per_unit', true);
	
			if ( $discount_per_product === 'yes' ) {
				// If the product has an individual discount, add it to the list of discounted products
				$products_with_discounts[$product_id] = $product_id;
			}
		}
	
		// Apply discounts to individual products
		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			$product = $cart_item['data'];
			$product_id = $product->get_id();
			$discount_per_product = get_post_meta($product->get_id(), 'enable_discount_per_unit', true);
			$product_discount = get_post_meta($product_id, 'unit_discount_amount', true);
			$product_discount_method = get_post_meta($product_id, 'discount_per_unit_method', true);
			$discount_gateway = get_post_meta($product_id, 'discount_gateway', true);
			$chosen_payment_method = WC()->session->chosen_payment_method;
	
			if ( $discount_per_product === 'yes' && isset($products_with_discounts[$product_id]) ) {
				if ( $product_discount !== '' && ($product_discount_method === 'percentage' || $product_discount_method === 'fixed') && $discount_gateway === $chosen_payment_method) {
					// Apply the discount only to the corresponding product
					$cart_item_price = $product->get_price();
					$quantity = $cart_item['quantity'];
					$payment_gateways = WC()->payment_gateways->payment_gateways();
					$gateway = $payment_gateways[WC()->session->chosen_payment_method];
	
					if ($product_discount_method === 'percentage') {
						$discount_amount = ($cart_item_price * $product_discount / 100) * $quantity;
					} elseif ($product_discount_method === 'fixed') {
						$discount_amount = $product_discount * $quantity;
					}
	
					$discount_name = $this->discount_name($discount_amount, $gateway);
	
					if ($discount_amount > 0) {
						$cart->add_fee($discount_name, -$discount_amount, false);
						$discount_applied = true; // Mark that a discount has been applied.
					}
				}
			}
		}
	
		if ( $discount_applied !== true ) {
			// If no custom product discount was applied, apply the discount based on the payment method.
			if ( isset( $gateways[WC()->session->chosen_payment_method] ) ) {
				$value = $gateways[WC()->session->chosen_payment_method]['amount'];
				$type = $gateways[WC()->session->chosen_payment_method]['type'];
	
				if ( apply_filters('woo_custom_installments_apply_discount', 0 < $value, $cart ) ) {
					$payment_gateways = WC()->payment_gateways->payment_gateways();
					$gateway = $payment_gateways[WC()->session->chosen_payment_method];
					$discount_name = $this->discount_name($value, $gateway);
					$total_discount = 0;
					$total_cart_value = 0;
	
					// iterate over cart items and calculate total cart value
					foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
						$product = $cart_item['data'];
						$disable_discount = get_post_meta($product->get_id(), '__disable_discount_main_price', true) == 'yes';
						$parent_id = $product->get_parent_id();
						$disable_discount_in_parent = get_post_meta($parent_id, '__disable_discount_main_price', true) == 'yes';
	
						// Check if the product or its parent has the discount disabled
						if (!$disable_discount && !$disable_discount_in_parent) {
							// Calculate discount based on the individual item price
							$cart_item_total = $cart_item['data']->get_price() * $cart_item['quantity'];
							$total_cart_value += $cart_item_total;
						}
					}
	
					// Add the shipping total to the cart value if the option is enabled
					if ( Woo_Custom_Installments_Init::get_setting('include_shipping_value_in_discounts') === 'yes') {
						$total_cart_value += $cart->get_shipping_total();
					}
	
					// Calculate discount based on total cart value
					if (self::get_setting('product_price_discount_method') == 'percentage') {
						$total_discount = $this->calculate_discount($type, $value, $total_cart_value) * -1;
					} else {
						$total_discount = $value * -1; // apply fixed discount to total order, not per item
					}
	
					if ($total_discount !== 0) {
						$cart->add_fee($discount_name, $total_discount, true);
					}
				}
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
		$new_payment_method_title = sanitize_text_field( $new_payment_method_title );
		update_post_meta( $order_id, '_payment_method_title', $new_payment_method_title );
	}


	/**
	 * Set discount per quantity
	 * 
	 * @since 2.7.2
	 * @param $cart | WC_Cart object
	 * @return void
	 */
	public function set_discount_per_quantity( $cart ) {
		if ( is_admin() && !defined('DOING_AJAX') ) {
			return;
		}
	
		$total_discount = 0;
	
		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			$product = $cart_item['data'];
			$disable_discount = get_post_meta( $product->get_id(), '__disable_discount_main_price', true ) == 'yes';
			$parent_id = $product->get_parent_id();
			$disable_discount_in_parent = get_post_meta( $parent_id, '__disable_discount_main_price', true ) == 'yes';
	
			// check if the product or its parent has the discount disabled
			if ( !$disable_discount && !$disable_discount_in_parent ) {
				$quantity = $cart_item['quantity'];
	
				// global discount options
				$discount_method = self::get_setting('discount_per_quantity_method');
				$discount_value = self::get_setting('value_for_discount_per_quantity');
				$minimum_quantity = self::get_setting('set_quantity_enable_discount');
	
				// single product discount options
				$discount_method_single = get_post_meta( $product->get_id(), 'discount_per_quantity_method', true );
				$discount_value_single = get_post_meta( $product->get_id(), 'quantity_discount_amount', true );
				$minimum_quantity_single = get_post_meta( $product->get_id(), 'minimum_quantity_discount', true );
	
				if ( $quantity >= $minimum_quantity_single && $discount_value_single ) {
					$price = $product->get_price();
	
					if ( $discount_method_single == 'percentage' ) {
						$discounted_price = $price - ( $price * ( $discount_value_single / 100 ) );
					} else {
						$discounted_price = $price - $discount_value_single;
					}
	
					$discount = $price - $discounted_price;
					$cart_item_discount = $discount;
	
					if ( Woo_Custom_Installments_Init::get_setting('enable_discount_per_unit_discount_per_quantity') === 'yes' ) {
						$cart_item_discount = $discount * $quantity;
					}
	
					$total_discount += $cart_item_discount;
				} elseif ( $quantity >= $minimum_quantity && $discount_value ) {
					$price = $product->get_price();
	
					// check discount method
					if ($discount_method == 'percentage') {
						$discounted_price = $price - ($price * ($discount_value / 100));
					} else {
						$discounted_price = $price - $discount_value;
					}
	
					$discount = $price - $discounted_price;
					$cart_item_discount = $discount;
	
					// check if option discount per unit is activated
					if ( Woo_Custom_Installments_Init::get_setting('enable_discount_per_unit_discount_per_quantity') === 'yes') {
						$cart_item_discount = $discount * $quantity;
					}
	
					$total_discount += $cart_item_discount;
				}
			}
		}
	
		if ( $total_discount > 0 ) {
	
			// Check if the option to disable discounts is active for the product
			if ( $disable_discount || $disable_discount_in_parent ) {
				wc()->cart->remove_fee( __('Desconto por quantidade', 'woo-custom-installments') );
			} else {
				wc()->cart->add_fee( __('Desconto por quantidade', 'woo-custom-installments'), - $total_discount );
			}
		}
	}
	
}

new Woo_Custom_Installments_Discounts();