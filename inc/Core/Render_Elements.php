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

            // display discount on Pix on cart page
			add_action( 'woocommerce_cart_totals_before_order_total', array( __CLASS__, 'display_discount_on_cart' ) );

			// get hook to display accordion or popup payment form in single product page
			if ( Admin_Options::get_setting('hook_payment_form_single_product') === 'before_cart' ) {
				add_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ), 10 );
			} elseif ( Admin_Options::get_setting('hook_payment_form_single_product') === 'after_cart' ) {
				add_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ), 10 );
			} elseif ( Admin_Options::get_setting('hook_payment_form_single_product') === 'custom_hook' ) {
				add_action( Admin_Options::get_setting('set_custom_hook_payment_form'), array( __CLASS__, 'display_payment_methods' ), 10 );
			} else {
				remove_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ), 10 );
				remove_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'display_payment_methods' ), 10 );
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
				add_filter( 'woo_custom_installments_align_price_group_widgets', array( __CLASS__, 'align_center_group_prices' ) );
			}
		}
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
		/**
		 * Filter to change product object on installments table
		 * 
		 * @since 2.0.0
		 * @version 5.4.0
		 * @param object $product | Product object
		 */
		$product = apply_filters( 'Woo_Custom_Installments/Installments/Set_Product', $product );

		if ( ! $product ) {
			return;
		}

		$installments = array(); 
		$all_installments = array();

		// check if product is variation e get your parent id
		if ( $product->is_type('variation') ) {
			$disable_installments = get_post_meta( $product->get_parent_id(), '__disable_installments', true ) === 'yes';
		} else {
			$disable_installments = get_post_meta( $product->get_id(), '__disable_installments', true ) === 'yes';
		}

		// check if '__disable_installments' is true or not purchasable and hide for the simple or variation products
		if ( $disable_installments === 'yes' || ! $product->is_purchasable() ) {
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
		
		if ( Admin_Options::get_setting('display_installment_type') === 'accordion' ) {
			echo apply_filters( 'woo_custom_installments_table', self::payment_methods_accordion( $product ), $all_installments );
		} elseif ( Admin_Options::get_setting('display_installment_type') === 'popup' ) {
			echo apply_filters( 'woo_custom_installments_table', self::payment_methods_modal( $product ), $all_installments );
		} else {
			return;
		}

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
	 * Display group elements
	 * 
	 * @since 2.0.0
	 * @version 5.4.0
	 * @param string $price | Product price
	 * @param object $product | Product object
	 * @return string
	 */
	public function display_price_group( $price, $product ) {
		$price = apply_filters( 'woo_custom_installments_adjusted_price', $price, $product );

		if ( strpos( $price, 'woo-custom-installments-group' ) !== false ) {
			return $price;
		}

		/**
		 * Add custom classes on woo-custom-installments-group element
		 * 
		 * @since 5.3.0
		 * @version 5.4.0
		 * @return string
		 */
		$custom_classes = apply_filters( 'Woo_Custom_Installments/Price/Group_Classes', '' );

		$html = '<div class="woo-custom-installments-group ' . $custom_classes;
			if ( $product && $product->is_type('variable') && ! Helpers::variations_has_same_price( $product ) ) {
				$html .= ' variable-range-price';
			}
		$html .= '">';

			$html .= '<div class="woo-custom-installments-group-main-price">';
				$price_icon_base = Admin_Options::get_setting('elements_design')['price']['icon'];

				// add icon before price
				if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) {
					if ( isset( $price_icon_base['class'] ) && ! empty( $price_icon_base['class'] ) ) {
						$html .= sprintf( __( '<i class="wci-icon-price icon-class %s"></i>' ), esc_attr( $price_icon_base['class'] ) );
					}
				} else {
					if ( isset( $price_icon_base['image'] ) && ! empty( $price_icon_base['image'] ) ) {
						$html .= sprintf( __( '<img class="wci-icon-price icon-image" src="%s"/>' ), esc_url( $price_icon_base['image'] ) );
					}
				}

				if ( $product && $product->is_type('variable') ) {
					// Get variation prices
					$min_regular_price = $product->get_variation_regular_price( 'min', true );
					$min_sale_price = $product->get_variation_sale_price( 'min', true );
					$regular_price = wc_price( $min_regular_price );
					$sale_price = wc_price( $min_sale_price );

					// check if variations has different price between variations
					if ( ! Helpers::variations_has_same_price( $product ) ) {
						if ( Admin_Options::get_setting('remove_price_range') === 'yes' && License::is_valid() ) {
							$html .= ! empty( Admin_Options::get_setting('text_initial_variables') ) ? '<span class="woo-custom-installments-starting-from">' . Admin_Options::get_setting('text_initial_variables') . '</span>' : '';
							$html .= '<span class="woo-custom-installments-price sale-price">' . $sale_price . '</span>';
						} else {
							$html .= '<span class="woo-custom-installments-price">' . $price . '</span>';
						}
					} else {
						$html .= '<span class="woo-custom-installments-price">' . $price . '</span>';
					}
				} else {
					// Check if the product has a sale price for simple products
					if ( $product && $product->is_on_sale() ) {
						$regular_price = wc_price( $product->get_regular_price() );
						$sale_price = wc_price( $product->get_sale_price() );

						$html .= '<span class="woo-custom-installments-price original-price has-discount">' . $regular_price . '</span>';
						$html .= '<span class="woo-custom-installments-price sale-price">' . $sale_price . '</span>';
					} else {
						$html .= '<span class="woo-custom-installments-price">' . $price . '</span>';
					}
				}

				// display sale badge
				if ( Admin_Options::get_setting('enable_sale_badge') === 'yes' ) {
					$html .= self::sale_badge( $product );
				}
			$html .= '</div>';

			// instance components class
			$components = new \MeuMouse\Woo_Custom_Installments\Views\Components();

			$html .= $components->display_best_installments( $product );
			$html .= $components->discount_main_price_single( $product );
			$html .= $components->economy_pix_badge( $product );
			$html .= $components->discount_ticket_badge( $product );
		$html .= '</div>';

		return $html;
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

		$total_cart_value = WC()->cart->get_cart_contents_total() + WC()->cart->get_shipping_total();
		$total_discount = Calculate_Values::calculate_total_discount( WC()->cart, Admin_Options::get_setting('include_shipping_value_in_discounts') === 'yes' ); ?>

		<tr>
			<th><?php echo apply_filters( 'Woo_Custom_Installments/Cart/Total_Title', sprintf( __( 'Total %s', 'woo-custom-installments' ), Admin_Options::get_setting('text_after_price') ) ); ?></th>
			<td data-title="<?php echo esc_attr( apply_filters( 'Woo_Custom_Installments/Cart/Total_Title', sprintf( __( 'Total %s', 'woo-custom-installments' ), Admin_Options::get_setting('text_after_price') ) ) ); ?>"><?php echo wc_price( $total_cart_value - $total_discount ); ?></td>
		</tr>
		<?php
	}


	/**
	 * Display sale badge
	 * 
	 * @since 5.2.5
     * @version 5.4.0
	 * @param object $product | Product object
	 * @return string
	 */
	public static function sale_badge( $product ) {
		if ( $product && $product->is_on_sale() ) {
			if ( $product->is_type('variable') ) {
				$percentages = array();
				$prices = $product->get_variation_prices();
				
				foreach ( $prices['price'] as $key => $price ) {
					if ( $prices['regular_price'][$key] !== $price ) {
						$percentages[] = round( 100 - ( $prices['sale_price'][$key] / $prices['regular_price'][$key] * 100 ) );
					}
				}

				if ( ! empty( $percentages ) ) {
					$percentage = max( $percentages ) . '%';
				} else {
					$percentage = '0%';
				}
			} else {
				$regular_price = (float) $product->get_regular_price();
				$sale_price = (float) $product->get_sale_price();
				$percentage = round( 100 - ( $sale_price / $regular_price * 100 ) ) . '%';
			}
		
			return '<span class="wci-sale-badge">'. sprintf( __( '%s OFF', 'woo-custom-installments' ), $percentage ) .'</span>';
		}
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

			$text_discount_per_quantity_message = Admin_Options::get_setting('text_discount_per_quantity_message');

			if ( ! empty( $text_discount_per_quantity_message ) ) {
				// Count the number of %s in the string
				$placeholders_string_count = substr_count( $text_discount_per_quantity_message, '%s' );
				$placeholders_number_count = substr_count( $text_discount_per_quantity_message, '%d' );

				// Ensure that the number of arguments passed to sprintf matches the number of %s
				if ( $placeholders_string_count === 1 && $placeholders_number_count === 1 ) {
					$formatted_text = sprintf( $text_discount_per_quantity_message, $minimum_quantity, $discount_message );
				} else {
					// If the amount of %s does not match, use the original text
					$formatted_text = $text_discount_per_quantity_message;
				}

				echo '<div class="woo-custom-installments-discount-per-quantity-message">';
				echo '<i class="fa-solid fa-circle-exclamation"></i>';
				echo '<span>' . $formatted_text . '</span>';
				echo '</div>';
			}
		}
	}
}