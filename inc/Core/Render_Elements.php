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
 * @package MeuMouse.com
 */
class Render_Elements {

    /**
     * Construct function
     * 
     * @since 5.4.0
     * @return void
     */
    public function __construct() {
        if ( Admin_Options::get_setting('enable_installments_all_products') === 'yes' ) {
            // display wci elements on main price html
			add_filter( 'woocommerce_get_price_html', array( $this, 'display_price_group' ), 30, 2 );
			add_filter( 'Woo_Custom_Installments/Price/Group_Classes', array( $this, 'add_group_classes' ), 10, 2 );

            // display discount on Pix on cart page
			add_action( 'woocommerce_cart_totals_before_order_total', array( __CLASS__, 'display_discount_on_cart' ) );

			// get hook to display accordion or popup payment form in single product page
			if ( Admin_Options::get_setting('hook_payment_form_single_product') === 'before_cart' ) {
				add_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ), 10, 1 );
			} elseif ( Admin_Options::get_setting('hook_payment_form_single_product') === 'after_cart' ) {
				add_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ), 10, 1 );
			} elseif ( Admin_Options::get_setting('hook_payment_form_single_product') === 'custom_hook' ) {
				add_action( Admin_Options::get_setting('set_custom_hook_payment_form'), array( __CLASS__, 'display_payment_methods' ), 10, 1 );
			} else {
				remove_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ), 10, 1 );
				remove_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ), 10, 1 );
			}

			// remove price range
			if ( Admin_Options::get_setting('remove_price_range') === 'yes' && License::is_valid() ) {
				add_filter( 'woocommerce_variable_price_html', array( $this, 'starting_from_variable_product_price' ), 10, 2 );
				add_filter( 'woocommerce_variable_sale_price_html', array( $this, 'starting_from_variable_product_price' ), 10, 2 );
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
	 * @version 5.4.0
	 * @param string $price | Product price
	 * @param object $product | Product object
	 * @return string
	 */
	public function display_price_group( $price, $product ) {
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
			do_action( 'Woo_Custom_Installments/Price/Prepend_Group', $product ); ?>

			<div class="woo-custom-installments-group-main-price">
				<?php
				$price_icon_base = Admin_Options::get_setting('elements_design')['price']['icon'];
				$format = Admin_Options::get_setting('icon_format_elements');

				// add icon before price
				if ( $format === 'class' && ! empty( $price_icon_base['class'] ) ) {
					echo sprintf( '<i class="wci-icon-price icon-class %s"></i>', esc_attr( $price_icon_base['class'] ) );
				} elseif ( $format !== 'class' && ! empty( $price_icon_base['image'] ) ) {
					echo sprintf( '<img class="wci-icon-price icon-image" src="%s"/>', esc_url( $price_icon_base['image'] ) );
				}

				if ( $product && $product->is_type('variable') ) {
					// Get variation prices
					$min_sale_price = $product->get_variation_sale_price( 'min', true );

					// check if variations has different price between variations
					if ( ! Helpers::variations_has_same_price( $product ) ) :
						// display modern price range
						if ( Admin_Options::get_setting('remove_price_range') === 'yes' && License::is_valid() ) :
							$starting_from_text = Admin_Options::get_setting('text_initial_variables');
							
							if ( ! empty( $starting_from_text ) ) : ?>
								<span class="woo-custom-installments-starting-from"><?php echo $starting_from_text ?></span>
							<?php endif; ?>

							<span class="woo-custom-installments-price sale-price"><?php echo wc_price( $min_sale_price ) ?></span>
						<?php else : ?>
							<span class="woo-custom-installments-price"><?php echo $price ?></span>
						<?php endif; ?>
					<?php else : ?>
						<span class="woo-custom-installments-price"><?php echo $price ?></span>
					<?php endif;
				} else {
					// Check if the product has a sale price for simple products
					if ( $product && $product->is_on_sale() ) : ?>
						<span class="woo-custom-installments-price original-price has-discount"><?php echo wc_price( $product->get_regular_price() ) ?></span>
						<span class="woo-custom-installments-price sale-price"><?php echo wc_price( $product->get_sale_price() ) ?></span>
					<?php else : ?>
						<span class="woo-custom-installments-price"><?php echo $price ?></span>
					<?php endif;
				}

				// instance components class
				$components = new Components();

				// display sale badge
				if ( Admin_Options::get_setting('enable_sale_badge') === 'yes' ) :
					echo $components->sale_badge( $product );
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
			echo $components->discount_main_price_single( $product );
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
	public static function display_payment_methods( $product ) {
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

		if ( ! $product ) {
			return;
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
		
		// render payment methods based on selected method
		echo $display_method === 'accordion' ? self::payment_methods_accordion( $product ) : self::payment_methods_modal( $product );

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
	 * Create container for display all payment methods in modal
	 * 
	 * @since 4.1.0
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return void
	 */
	public static function payment_methods_modal( $product ) {
		if ( ! $product ) :
			return;
		endif; ?>

		<button type="button" class="wci-open-popup">
			<span class="open-popup-text"><?php echo Admin_Options::get_setting('text_button_installments'); ?></span>
		</button>

		<div class="wci-popup-container">
			<div class="wci-popup-content">
				<div class="wci-popup-header">
					<h5 class="wci-popup-title"><?php echo Admin_Options::get_setting('text_container_payment_forms'); ?></h5>
					<button type="button" class="btn-close wci-close-popup" aria-label="<?php echo esc_html__( 'Fechar', 'woo-custom-installments' ) ?>"></button>
				</div>

				<?php
				/**
				 * Hook for display custom content inside accordion container
				 * 
				 * @since 4.1.0
				 * @version 5.4.0
				 * @param object $product | Product object
				 */
				do_action( 'Woo_Custom_Installments/Elements/Modal_Header', $product ); ?>

				<div id="wci-popup-body">
					<?php

					if ( License::is_valid() ) {
						echo Components::render_pix_flag( $product );
						echo Components::render_credit_card_flags();
						echo Components::render_debit_card_flags();
						echo Components::render_ticket_flag( $product );
					}

					echo Components::render_installments_table( $product ); ?>
				</div>

				<?php
				/**
				 * Hook for display custom content inside bottom popup
				 * 
				 * @since 4.1.0
				 * @version 5.4.0
				 * @param object $product | Product object
				 */
				do_action( 'Woo_Custom_Installments/Elements/Modal_Footer', $product ); ?>
			</div>
		</div>
		<?php
	}


	/**
	 * Display all payment methods in accordion element
	 * 
	 * @since 4.1.0
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return void
	 */
	public static function payment_methods_accordion( $product ) {
		if ( ! $product ) :
			return;
		endif; ?>

		<div id="wci-accordion-installments" class="accordion">
			<div class="wci-accordion-item">
				<button type="button" class="wci-accordion-header"><?php echo Admin_Options::get_setting('text_button_installments'); ?></button>

				<div class="wci-accordion-content">
					<?php
					/**
					 * Hook for display custom content inside header accordion
					 * 
					 * @since 4.1.0
					 * @version 5.4.0
					 * @param object $product | Product object
					 */
					do_action( 'Woo_Custom_Installments/Elements/Accordion_Header', $product );

					if ( License::is_valid() ) {
						echo Components::render_pix_flag( $product );
						echo Components::render_credit_card_flags();
						echo Components::render_debit_card_flags();
						echo Components::render_ticket_flag( $product );
					}
					
					echo Components::render_installments_table( $product ); ?>
				</div>

				<?php
				/**
				 * Hook for display custom content inside bottom accordion
				 * 
				 * @since 4.1.0
				 * @version 5.4.0
				 * @param object $product | Product object
				 */
				do_action( 'Woo_Custom_Installments/Elements/Accordion_Footer', $product ); ?>
			</div>
		</div>
		<?php
	}


    /**
	 * Replace range price for "A partir de"
	 * 
	 * @since 2.4.0
	 * @version 4.5.0
	 * @param string $price | Product price
	 * @param object $product | Product object
	 * @return string
	 */
	public function starting_from_variable_product_price( $price, $product ) {
		if ( ! Helpers::variations_has_same_price( $product ) ) {
			$text_initial = ! empty( Admin_Options::get_setting('text_initial_variables') ) ? '<span class="woo-custom-installments-starting-from">' . Admin_Options::get_setting('text_initial_variables') . '</span>' : '';
			$min_price = $product->get_variation_price( 'min', true );

			$price = $text_initial . wc_price( $min_price );
		}

		return $price;
	}


	/**
	 * Display product variations for replace on remove price range
	 * 
	 * @since 5.2.6
	 * @version 5.4.0
	 * @param object $product | Product object
	 * @return void
	 */
	public function variation_prices_group( $product ) {
		if ( $product && $product->is_type('variable') && ! Helpers::variations_has_same_price( $product ) ) :
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

		$product_id = $product->get_id();
		$current_quantity = $product->get_stock_quantity();
		$enable_global_discount = Admin_Options::get_setting('enable_discount_per_quantity_method') === 'global';
		$enable_product_discount = get_post_meta( $product_id, 'enable_discount_per_quantity', true );
		
		if ( $enable_global_discount || $enable_product_discount ) {
			if ( $enable_global_discount ) {
				$method = Admin_Options::get_setting('discount_per_quantity_method');
				$value = Admin_Options::get_setting('value_for_discount_per_quantity');
				$minimum_quantity = Admin_Options::get_setting('set_quantity_enable_discount');
			} else {
				$method = get_post_meta( $product_id, 'discount_per_quantity_method', true );
				$value = get_post_meta( $product_id, 'quantity_discount_amount', true );
				$minimum_quantity = get_post_meta( $product_id, 'minimum_quantity_discount', true );
			}

			if ( $method == 'percentage' ) {
				$discount_message = $value . '%';
			} else {
				$discount_message = get_woocommerce_currency_symbol() . $value;
			}

			$text_message = Admin_Options::get_setting('text_discount_per_quantity_message');

			if ( ! empty( $text_message ) ) {
				// Count the number of %s in the string
				$placeholders_string_count = substr_count( $text_message, '%s' );
				$placeholders_number_count = substr_count( $text_message, '%d' );

				// Ensure that the number of arguments passed to sprintf matches the number of %s
				if ( $placeholders_string_count === 1 && $placeholders_number_count === 1 ) {
					$formatted_text = sprintf( $text_message, $minimum_quantity, $discount_message );
				} else {
					// If the amount of %s does not match, use the original text
					$formatted_text = $text_message;
				}

				echo '<div class="woo-custom-installments-discount-per-quantity-message">';
				echo '<i class="fa-solid fa-circle-exclamation"></i>';
				echo '<span>' . $formatted_text . '</span>';
				echo '</div>';
			}
		}
	}
}