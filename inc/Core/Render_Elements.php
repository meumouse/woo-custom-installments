<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Views\Components;
use MeuMouse\Woo_Custom_Installments\API\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Display renderized price with elements on frontend
 *
 * @since 5.4.0
 * @version 5.5.6
 * @package MeuMouse\Woo_Custom_Installments\Core
 * @author MeuMouse.com
 */
class Render_Elements {

    /**
     * Construct function
     * 
     * @since 5.4.0
	 * @version 5.5.1
     * @return void
     */
    public function __construct() {
        if ( Admin_Options::get_setting('enable_installments_all_products') === 'yes' ) {
			/**
			 * Set priority for price group
			 *
			 * Developers can change this priority using:
			 * add_filter( 'Woo_Custom_Installments/Price/Priority', function() { return 30; } );
			 *
			 * @since 5.5.1
			 * @return int
			 */
			$priority = apply_filters( 'Woo_Custom_Installments/Price/Priority', 999 );

            // display wci elements on main price html
			add_filter( 'woocommerce_get_price_html', array( $this, 'display_price_group' ), $priority, 2 );
			add_filter( 'Woo_Custom_Installments/Price/Group_Classes', array( $this, 'add_group_classes' ), 10, 2 );

            // display discount on Pix on cart page
			add_action( 'woocommerce_cart_totals_before_order_total', array( __CLASS__, 'display_discount_on_cart' ) );

			// get hook to display accordion or popup payment form in single product page
			if ( Admin_Options::get_setting('hook_payment_form_single_product') === 'before_cart' ) {
				add_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ) );
			} elseif ( Admin_Options::get_setting('hook_payment_form_single_product') === 'after_cart' ) {
				add_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ) );
			} elseif ( Admin_Options::get_setting('hook_payment_form_single_product') === 'custom_hook' ) {
				add_action( Admin_Options::get_setting('set_custom_hook_payment_form'), array( __CLASS__, 'display_payment_methods' ) );
			} else {
				remove_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ) );
				remove_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ) );
			}

			// remove price range
			if ( Admin_Options::get_setting('remove_price_range') === 'yes' && License::is_valid() ) {
				add_action( 'Woo_Custom_Installments/Product/After_Price', array( $this, 'variation_prices_group' ), 10, 1 );
			}

			/**
			 * Add text after price
			 * 
			 * @since 2.8.0
			 * @version 5.2.5
			 */
			if ( Admin_Options::get_setting('custom_text_after_price') === 'yes' ) {
				add_filter( 'woocommerce_get_price_html', array( $this, 'custom_product_price' ), 10, 2 );
			}

			// display discount per quantity message if parent option is activated
			if ( Admin_Options::get_setting('enable_functions_discount_per_quantity') === 'yes' && Admin_Options::get_setting('message_discount_per_quantity') === 'yes' && Admin_Options::get_setting('discount_per_qtd_message_method') === 'hook' ) {
				add_action( 'woocommerce_single_product_summary', array( $this, 'display_message_discount_per_quantity' ) );
				add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'display_message_discount_per_quantity' ) );
			}

			if ( Admin_Options::get_setting('center_group_elements_loop') === 'yes' ) {
				add_filter( 'Woo_Custom_Installments/Widgets/Align_Price_Group', array( __CLASS__, 'align_center_group_prices' ) );
			}
		}
    }


	/**
	 * Display group elements
	 * 
	 * @since 2.0.0
	 * @version 5.5.6
	 * @param string $price | Product price
	 * @param object $product | Product object
	 * @return string
	 */
	public function display_price_group( $price, $product ) {
		if ( empty( $price ) ) {
			return $price;
		}

		// Start buffer
		ob_start();

		/**
		 * Hook for display custom content before group
		 * 
		 * @since 5.4.0
		 * @param object $product | Product object
		 */
		do_action( 'Woo_Custom_Installments/Price/Before_Group', $product );

		/**
		 * Add custom classes on woo-custom-installments-group element
		 * 
		 * @since 5.3.0
		 * @version 5.4.0
		 * @return string
		 */
		$group_classes = apply_filters( 'Woo_Custom_Installments/Price/Group_Classes', '', $product ); ?>

		<div class="woo-custom-installments-group <?php echo esc_attr( $group_classes ) ?>">
			<?php
			/**
			 * Hook for display custom content in prepend group
			 * 
			 * @since 5.4.0
			 * @param object $product | Product object
			 */
			do_action( 'Woo_Custom_Installments/Price/Prepend_Group', $product );
			
			$remove_range_price = Admin_Options::get_setting('remove_price_range') === 'yes';
			$variation_has_same_price = Helpers::variations_has_same_price( $product ); ?>

			
			<div class="woo-custom-installments-group-main-price <?php echo esc_attr( ( ! $variation_has_same_price && ! $remove_range_price ) ? 'has-range-price' : '' ) ?>">
				<?php
				$price_icon_base = Admin_Options::get_setting('elements_design')['price']['icon'];
				$format = Admin_Options::get_setting('icon_format_elements');

				// add icon before price
				if ( $format === 'class' && ! empty( $price_icon_base['class'] ) ) {
					echo sprintf( '<i class="wci-icon-price icon-class %s"></i>', esc_attr( $price_icon_base['class'] ) );
				} elseif ( $format !== 'class' && ! empty( $price_icon_base['image'] ) ) {
					echo sprintf( '<img class="wci-icon-price icon-image" src="%s"/>', esc_url( $price_icon_base['image'] ) );
				}

				// instance components class
				$components = new Components();
				$display_sale_badge = Admin_Options::get_setting('enable_sale_badge');

				if ( $product && $product->is_type('variable') ) :
					$min_current_price = $product->get_variation_price( 'min', true );
					$is_on_sale = $product->is_on_sale();

					// has range price
					if ( ! $variation_has_same_price ) :
						if ( $remove_range_price && License::is_valid() ) :
							$prefix_range = Admin_Options::get_setting('text_initial_variables');

							if ( ! empty( $prefix_range ) ) : ?>
								<span class="woo-custom-installments-starting-from"><?php echo $prefix_range ?></span>
							<?php endif; ?>

							<span class="woo-custom-installments-price sale-price">
								<?php echo wc_price( $min_current_price );

								if ( $display_sale_badge === 'yes' && $is_on_sale ) :
									echo $components->sale_badge( $product );
								endif; ?>
							</span>
						<?php else : ?>
							<span class="woo-custom-installments-price">
								<?php echo wc_price( $product->get_variation_price( 'min', true ) ); ?>
							</span>

							<span class="woo-custom-installments-range-price-dash">-</span>

							<span class="woo-custom-installments-price">
								<?php echo wc_price( $product->get_variation_price( 'max', true ) ); ?>
							</span>
						<?php endif;
					else :
						if ( $is_on_sale && $min_current_price < $min_regular_price ) : ?>
							<span class="woo-custom-installments-price original-price has-discount">
								<?php echo wc_price( $min_regular_price ); ?>
							</span>
						<?php endif; ?>

						<span class="woo-custom-installments-price sale-price">
							<?php echo wc_price( $min_current_price ); ?>
						</span>
					<?php endif; ?>
				<?php else :
					// Check if the product has a sale price for simple products
					if ( $product->is_on_sale() ) : ?>
						<span class="woo-custom-installments-price original-price has-discount"><?php echo wc_price( $product->get_regular_price() ) ?></span>
						
						<span class="woo-custom-installments-price sale-price">
							<?php echo wc_price( $product->get_sale_price() );

							// display sale badge
							if ( $display_sale_badge === 'yes' ) :
								echo $components->sale_badge( $product );
							endif; ?>
						</span>
					<?php else : ?>
						<span class="woo-custom-installments-price"><?php echo wc_price( $product->get_price() ) ?></span>
					<?php endif;
				endif; ?>
			</div>

			<?php
			/**
			 * Hook for display custom content in append group
			 * 
			 * @since 5.4.0
			 * @param object $product | Product object
			 */
			do_action( 'Woo_Custom_Installments/Price/Append_Group', $product );
		
			echo $components->display_best_installments( $product );
			echo $components->discount_main_price( $product );
			echo $components->economy_pix_badge( $product );
			echo $components->discount_ticket_badge( $product ); ?>
		</div>

		<?php
		/**
		 * Hook for display custom content after group
		 * 
		 * @since 5.4.0
		 * @param object $product | Product object
		 */
		do_action( 'Woo_Custom_Installments/Price/After_Group', $product );

		return ob_get_clean();
	}

	
	/**
	 * Add classes on group element
	 * 
	 * @since 5.4.0
	 * @param string $classes | Current group classes
	 * @param object $product | Product object
	 * @return string
	 */
	public function add_group_classes( $classes, $product ) {
		if ( $product && $product->is_type('variable') && ! Helpers::variations_has_same_price( $product ) ) {
			$classes .= ' variable-range-price';
		}

		return $classes;
	}


	/**
	 * Display payment methods on modal or accordion
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return string
	 */
	public static function display_payment_methods( $product = null ) {
		$display_method = Admin_Options::get_setting('display_installment_type');

		// check if display method is accordion or popup
		if ( $display_method !== 'accordion' && $display_method !== 'popup' ) {
			return;
		}

		/**
		 * Filter to change product object on installments table
		 * 
		 * @since 2.0.0
		 * @version 5.4.0
		 * @param object $product | Product object
		 */
		$product = apply_filters( 'Woo_Custom_Installments/Payment_Methods/Set_Product', $product );

		// check if product object exists
		if ( ! $product ) {
			global $product;
		}

		// base product ID on product object
		$product_id = $product->get_id();

		// check if product is variation e get your parent id
		if ( $product->is_type('variation') ) {
			$product_id = $product->get_parent_id();
		}

		// check if '__disable_installments' is true or not purchasable and hide for the simple or variation products
		if ( get_post_meta( $product_id, '__disable_installments', true ) === 'yes' || ! $product->is_purchasable() ) {
			return;
		}

		/**
		 * Hook for display custom content before installments container
		 * 
		 * @since 4.1.0
		 * @version 5.4.0
		 * @param object $product | Product object
		 */
		do_action( 'Woo_Custom_Installments/Elements/Before_Installments_Container', $product );

		// instance components class
		$components = new Components();
		
		// render payment methods based on selected method
		echo $display_method === 'accordion' ? $components->payment_methods_accordion( $product ) : $components->payment_methods_modal( $product );

		/**
		 * Hook for display custom content after installments container
		 * 
		 * @since 4.1.0
		 * @version 5.4.0
		 * @param object $product | Product object
		 */
		do_action( 'Woo_Custom_Installments/Elements/After_Installments_Container', $product );
	}


	/**
	 * Display product variations for replace on remove price range
	 * 
	 * @since 5.2.6
	 * @version 5.4.7
	 * @param object $product | Product object
	 * @return void
	 */
	public function variation_prices_group( $product ) {
		// check product type
		if ( ! $product || ! in_array( $product->get_type(), array( 'variable', 'variable-subscription' ), true ) ) {
			return;
		}

		if ( ! Helpers::variations_has_same_price( $product ) ) :
			$variations = $product->get_available_variations(); ?>

			<ul id="wci-variation-prices">
				<?php foreach ( $variations as $variation ) :
					$variation_product = wc_get_product( $variation['variation_id'] ); ?>

					<li class="wci-variation-item d-none" data-variation-id="<?php echo esc_attr( $variation['variation_id'] ); ?>">
						<?php echo $variation_product->get_price_html(); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif;
	}


    /**
	 * Display discount in cart page
	 * 
	 * @since 2.6.0
	 * @version 5.4.0
	 * @return string
	 */
	public static function display_discount_on_cart() {
		if ( Admin_Options::get_setting('enable_all_discount_options') !== 'yes' || Admin_Options::get_setting('display_installments_cart') !== 'yes' ) {
			return;
		}

		$total_cart_value = (float) WC()->cart->get_cart_contents_total() + WC()->cart->get_shipping_total();
		$total_discount = (float) Calculate_Values::calculate_total_discount( WC()->cart, Admin_Options::get_setting('include_shipping_value_in_discounts') === 'yes' );
		$row_title = sprintf( __( 'Total %s', 'woo-custom-installments' ), Admin_Options::get_setting('text_after_price') );
		
		/**
		 * Filter for change total cart row title
		 * 
		 * @since 2.6.0
		 * @version 5.4.0
		 * @param string $row_title | Row title
		 */
		$title = apply_filters( 'Woo_Custom_Installments/Cart/Total_Title', $row_title ); ?>

		<tr>
			<th><?php echo $title; ?></th>
			<td data-title="<?php echo esc_attr( $title ); ?>"><?php echo wc_price( $total_cart_value - $total_discount ); ?></td>
		</tr>
		<?php
	}


    /**
	 * Custom product price
	 * 
	 * @since 5.2.0
	 * @version 5.4.0
	 * @param string $price | Product price
	 * @param object $product | Product object
	 * @return string
	 */
	public function custom_product_price( $price, $product ) {
		$discount_value = (float) Admin_Options::get_setting('discount_value_custom_product_price') / 100;
		$after_text = Admin_Options::get_setting('custom_text_after_price_front');

		if ( $product && $product->is_type('simple') ) {
			$regular_price = $product->get_regular_price();
			
			if ( Admin_Options::get_setting('add_discount_custom_product_price') === 'yes' ) {
				$discounted_price = (float) $regular_price - ( (float) $regular_price * (float) $discount_value );
			} else {
				$discounted_price = (float) $regular_price;
			}

			if ( ! empty( $after_text ) ) {
				$after_text_element = ' <span class="woo-custom-installments-text-after-price">'. $after_text .'</span>';
			} else {
				$after_text_element = '';
			}

			$price = wc_price( $discounted_price ) . $after_text_element;
		} elseif ( $product && $product->is_type('variable') ) {
			$min_price = $product->get_variation_price( 'min', true );

			if ( Admin_Options::get_setting('add_discount_custom_product_price') === 'yes' ) {
				$discounted_price = (float) $min_price - ( (float) $min_price * (float) $discount_value );
			} else {
				$discounted_price = (float) $min_price;
			}

			if ( ! empty( $after_text ) ) {
				$after_text_element = ' <span class="woo-custom-installments-text-after-price">'. $after_text .'</span>';
			} else {
				$after_text_element = '';
			}

			$price = wc_price( $discounted_price ) . $after_text_element;
		}

		return $price;
	}


    /**
	 * Set price group to center for Elementor widgets
	 * 
	 * @since 5.2.0
	 * @return string
	 */
	public static function align_center_group_prices() {
		return 'center';
	}


	/**
	 * Display menssage in elegible products for discount per quantity
	 * 
	 * @since 2.8.0
	 * @version 5.4.0
	 * @param int $product_id | Product ID
	 * @return void
	 */
	public function display_message_discount_per_quantity( $product_id ) {
		if ( $product_id ) {
			$product = wc_get_product( $product_id );
		} else {
			global $product;
		}

		if ( ! $product ) {
			return;
		}

		// instance of components class
		$components = new Components();

		echo $components->message_for_discount_per_quantity( $product );
	}
}