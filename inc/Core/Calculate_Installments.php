<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Class for calculate installments
 *
 * @since 5.4.0
 * @package MeuMouse.com
 */
class Calculate_Installments {

	/**
	 * Calculate installments
	 * 
	 * @since 1.0.0
	 * @version 5.4.0
	 * @return int
	 */
	public static $count = 0;

	/**
	 * Define WooCommerce Hooks
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public static function hook() {
		if ( self::is_main_product_price() ) {
			$action = 'main_price';
		} else {
			$action = 'loop';
		}

		return $action;
	}


	/**
	 * Check price in product
	 * 
	 * @since 1.0.0
	 * @return bool
	 */
	public static function is_main_product_price() {
		if ( is_product() ) {
			return ( 0 === self::$count );
		}
		
		return false;
	}


	/**
	 * Calculate installments
	 * 
	 * @since 1.0.0
	 * @version 5.4.0
	 * @param array $return
	 * @param mixed $price | Product price or false
	 * @param mixed $product | Product ID or false
	 * @param bool $echo
	 * @return string
	 */
	public static function set_values( $return, $price = false, $product = false, $echo = true ) {
		// check if is product
		if ( ! $product ) {
			return $return;
		}

		$installments_info = array();
		$custom_fee = maybe_unserialize( get_option('woo_custom_installments_custom_fee_installments') );

		if ( ! $price ) {
			global $product;

			$args = array();

			if ( ! $product ) {
				return $return;
			}

			if ( $product && $product->is_type( 'variable', 'variation' ) && ! Helpers::variations_has_same_price( $product ) ) {
				$args['price'] = $product->get_variation_price('max');
			}

			$price = wc_get_price_to_display( $product, $args );
		}

        /**
         * Set price for display installments
		 * 
		 * @since 1.0.0
		 * @version 5.4.0
		 * @param float $price | Product price
		 * @param object $product | Product object
         */
		$price = apply_filters( 'Woo_Custom_Installments/Price/Set_Values_Price', $price, $product );

		// check if product is different of available
		if ( ! Helpers::is_product_available( $product ) ) {
			return false;
		}

		// get max quantity of installments
		$installments_limit = (int) Admin_Options::get_setting('max_qtd_installments');

		// get all installments options till the limit
		for ( $i = 1; $i <= $installments_limit; $i++ ) {
			$interest_rate = 0; // start without fee

			// check if option activated is set_fee_per_installment, else global fee is defined
			if ( Admin_Options::get_setting('set_fee_per_installment') === 'yes' ) {
				$interest_rate = isset( $custom_fee[$i]['amount'] ) ? floatval( $custom_fee[$i]['amount'] ) : 0;
			} else {
				$interest_rate = (float) Admin_Options::get_setting('fee_installments_global');
			}

			// If interest be zero, use one formula for all
			if ( 0 == $interest_rate ) {
				$installments_info[] = self::get_installments_without_interest( $price, $i );
				continue;
			}

			// get max quantity of installments without fee
			$max_installments_without_fee = (int) Admin_Options::get_setting('max_qtd_installments_without_fee');

			// set the installments without fee
			if ( $i <= $max_installments_without_fee ) {
				// return values for this installment
				$installments_info[] = self::get_installments_without_interest( $price, $i );
			} else {
				$installments_info[] = self::get_installments_with_interest( $price, $interest_rate, $i );
			}
		}

		// get min value price of installment
		$min_installment_value = (int) Admin_Options::get_setting('min_value_installments');

		foreach ( $installments_info as $index => $installment ) {
			if ( $installment['installment_price'] < $min_installment_value && 0 < $index ) {
				unset( $installments_info[$index] );
			}
		}

		// check if variable $return is array to merge with installments_info
		if ( is_array( $return ) ) {
			$return = array_merge( $installments_info, $return );
		} else {
			$return = $installments_info;
		}

		return self::formatting_display( $installments_info, $return, $echo );
	}


    /**
	 * Format display prices
	 * 
	 * @since 1.0.0
     * @version 5.4.0
	 * @return string
	 */
	public static function formatting_display( $installments, $return, $echo = true ) {
		// check if installments equal zero, if true return empty
		if ( 0 === count( $installments ) ) {
			return;
		}

		/**
		 * Filter to change the installments
		 * 
		 * @since 1.0.0
		 * @version 5.4.0
		 * @param array $installments | Product installments
		 */
		$return = apply_filters( 'Woo_Custom_Installments/Installments/All_Installments', $installments );

		if ( $echo ) {
			echo $return;
		} else {
			return $return;
		}
	}


    /**
	 * Get best installment without interest
	 * 
	 * @since 1.0.0
	 * @version 5.4.0
	 * @param array $installments | Product installments
	 * @param object $product | Product object
	 * @return string
	 */
	public static function best_without_interest( $installments, $product ) {
		// check if $installments is different of array or empty $installments or product price is zero
		if ( ! is_array( $installments ) || empty( $installments ) || $product->get_price() <= 0 ) {
			return;
		}

		$hook = self::hook();

		foreach ( $installments as $key => $installment ) {
			if ( 'no-fee' != $installment['class'] ) {
				unset( $installments[$key] );
			}
		}

		// get end installment without fee loop foreach
		$best_without_interest = end( $installments );

		if ( false === $best_without_interest ) {
			return;
		}

		if ( 'main_price' === $hook ) {
			$text = Admin_Options::get_setting('text_display_installments_single_product');
		} else {
			$text = Admin_Options::get_setting('text_display_installments_loop');
		}

		$find = array_keys( Helpers::strings_to_replace( $best_without_interest ) );
		$replace = array_values( Helpers::strings_to_replace( $best_without_interest ) );
		$text = str_replace( $find, $replace, $text );

		$html = '<span class="woo-custom-installments-details-without-fee" data-end-installments="'. esc_attr( $best_without_interest['installments_total'] ) .'">';
			$card_icon_base = Admin_Options::get_setting('elements_design')['installments']['icon'];

			if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) {
				if ( isset( $card_icon_base['class'] ) ) {
					$html .= sprintf( __( '<i class="wci-icon-best-installments icon-class %s"></i>' ), esc_attr( $card_icon_base['class'] ) );
				}
			} else {
				if ( isset( $card_icon_base['image'] ) ) {
					$html .= sprintf( __( '<img class="wci-icon-best-installments icon-image" src="%s"/>' ), esc_url( $card_icon_base['image'] ) );
				}
			}

			$html .= '<span class="woo-custom-installments-details best-value ' . $best_without_interest['class'] . '">' . apply_filters( 'woo_custom_installments_best_no_fee_' . $hook, $text, $best_without_interest, $product ) . '</span>';
		$html .= '</span>';

		return $html;
	}


	/**
	 * Get best installment with interest
	 * 
	 * @since 1.0.0
	 * @version 5.4.0
	 * @param array $installments | Product installments
	 * @param object $product | Product object
	 * @return string
	 */
	public static function best_with_interest( $installments, $product ) {
		if ( $product === false || ! isset( $product ) ) {
			global $product;
		}

		// check if $installments is different of array or empty $installments or product price is zero
		if ( ! is_array( $installments ) || empty( $installments ) || $product->get_price() <= 0 ) {
			return;
		}

		$hook = self::hook();
		$best_with_interest = end( $installments );

		if ( false === $best_with_interest ) {
			return;
		}

		if ( 'main_price' === $hook ) {
			$text = Admin_Options::get_setting('text_display_installments_single_product');
		} else {
			$text = Admin_Options::get_setting('text_display_installments_loop');
		}

		$find = array_keys( Helpers::strings_to_replace( $best_with_interest ) );
		$replace = array_values( Helpers::strings_to_replace( $best_with_interest ) );
		$text = str_replace( $find, $replace, $text );

		$html = '<span class="woo-custom-installments-details-with-fee" data-end-installments="'. esc_attr( $best_with_interest['installments_total'] ) .'">';
			$card_icon_base = Admin_Options::get_setting('elements_design')['installments']['icon'];

			if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) {
				if ( isset( $card_icon_base['class'] ) ) {
					$html .= sprintf( __( '<i class="wci-icon-best-installments icon-class %s"></i>' ), esc_attr( $card_icon_base['class'] ) );
				}
			} else {
				if ( isset( $card_icon_base['image'] ) ) {
					$html .= sprintf( __( '<img class="wci-icon-best-installments icon-image" src="%s"/>' ), esc_url( $card_icon_base['image'] ) );
				}
			}

			$html .= '<span class="best-value '. $best_with_interest['class'] .'">'. apply_filters( 'woo_custom_installments_best_with_fee_'. $hook, $text, $best_with_interest, $product ) . '</span>';
		$html .= '</span>';

		return $html;
	}


	/**
	 * Get fee info
	 * 
	 * @since 1.0.0
     * @version 5.4.0
	 * @param array $installments | Array installments
	 * @return string
	 */
	public static function get_fee_info( $installment ) {
		$hook = self::hook();
		$text = ( $installment['interest_fee'] ) ? '' . Admin_Options::get_setting('text_with_fee_installments') : ' '. Admin_Options::get_setting('text_without_fee_installments');
		
        /**
         * Filter to change fee label
         * 
         * @since 1.0.0
         * @version 5.4.0
         * @param string $text | Fee label
         * @param bool $interest_fee | Interest fee
         * @param string $hook | Hook
         * @return string
         */
		return apply_filters( 'Woo_Custom_Installments/Price/Fee_Label', $text, $installment['interest_fee'], $hook );
	}


	/**
	 * Save array with all details of installments
	 * 
	 * @since 1.0.0
     * @version 5.4.0
	 * @param float $price | Product price
	 * @param float $final_price | Final price
	 * @param float $interest_fee | Interest rate fee
	 * @param string $class | Installments classs
	 * @param int $i | Installments total
	 * @return array
	 */
	public static function set_installment_info( $price, $final_price, $interest_fee, $class, $i ) {
        /**
         * Filter to change installment info
         * 
         * @since 1.0.0
         * @version 5.4.0
         * @param array $installment_info | Installment info
         */
		return apply_filters( 'Woo_Custom_Installments/Price/Installment_Info', array(
			'installment_price' => $price,
			'installments_total' => $i,
			'final_price' => $final_price,
			'interest_fee' => $interest_fee,
			'class' => $class,
		));
	}


	/**
	 * Calculate value of installment without interest
	 * 
	 * @since 1.0.0
     * @version 5.4.0
	 * @param string $total | Product price
	 * @param string $i | Installments
	 * @return string
	 */
	public static function get_installments_without_interest( $total, $i ) {
		$price = Calculate_Values::calculate_installment_no_fee( $total, $i );
		$final_price = Calculate_Values::calculate_final_price( $price, $i );
		$fee = false;
		$class = 'no-fee';
		$installment_info = self::set_installment_info( $price, $final_price, $fee, $class, $i );

		return $installment_info;
	}


	/**
	 * Calculate value of installment with interest
	 * 
	 * @since 1.0.0
     * @version 5.4.0
	 * @param string $total | Product price
	 * @param string $fee | Interest rate
	 * @param string $i | Installments
	 * @return string
	 */
	public static function get_installments_with_interest( $total, $fee, $i ) {
		$price = Calculate_Values::calculate_installment_with_fee( $total, $fee, $i );
		$final_price = Calculate_Values::calculate_final_price( $price, $i );
		$fee = true;
		$class = 'fee-included';
		$installment_info = self::set_installment_info( $price, $final_price, $fee, $class, $i );

		return $installment_info;
	}
}