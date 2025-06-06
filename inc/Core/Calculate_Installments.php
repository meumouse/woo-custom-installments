<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Admin\Default_Options;
use MeuMouse\Woo_Custom_Installments\Integrations\Elementor;
use MeuMouse\Woo_Custom_Installments\API\License;

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
     * Calculate all possible installment options
     * 
     * @since 1.0.0
     * @version 5.4.0
     * @param array $append | Additional installments arrays to merge
     * @param float|null $price | Product price to use (if null, will fetch from product)
     * @param WC_Product|null $product | Product object (if null, will retrieve from global)
     * @return array List of installment info arrays
     */
    public static function installments_list( array $append = array(), float $price = null, $product = null ): array {
        // Early return if no product provided or product not available
        if ( ! $product ) {
            $product = Helpers::get_product_id_from_post();
        }

		// Check Elementor edit mode
		if ( ! Elementor::is_edit_mode() && ( ! $product || ! Helpers::is_product_available( $product ) ) ) {
			return array();
		}

        // Determine display price if not passed in
        if ( $price === null ) {
            $args = array();

            if ( $product->is_type('variable') && ! Helpers::variations_has_same_price( $product ) ) {
                $args['price'] = $product->get_variation_price('max');
            }

            $price = wc_get_price_to_display( $product, $args );
        }

		/**
		 * Allow external filters to adjust the base price
		 * 
		 * @since 1.0.0
		 * @version 5.4.0
		 * @param float $price | Product price
		 * @param object $product | Product object
		 * @return float
		 */
        $price = apply_filters( 'Woo_Custom_Installments/Price/Set_Values_Price', $price, $product );

        // Load settings once to avoid repeated calls
        $limit = (int) Admin_Options::get_setting('max_qtd_installments');
        $without_fee_limit = (int) Admin_Options::get_setting('max_qtd_installments_without_fee');
        $global_fee = (float) Admin_Options::get_setting('fee_installments_global');
        $fee_per_installment = Admin_Options::get_setting('set_fee_per_installment') === 'yes';
        $min_value = (int) Admin_Options::get_setting('min_value_installments');
        $custom_fees = maybe_unserialize( get_option('woo_custom_installments_custom_fee_installments') );
        $installments = array();

        // Build installments in a single loop
        for ( $i = 1; $i <= $limit; $i++ ) {
            // Determine interest rate per installment
            $fee_rate = $fee_per_installment ? floatval( $custom_fees[ $i ]['amount'] ?? 0 ) : $global_fee;

            // Choose calculation method based on free-fee limit
            if ( $i <= $without_fee_limit || $fee_rate === 0 ) {
                $info = self::get_installments_without_interest( $price, $i );
            } else {
                $info = self::get_installments_with_interest( $price, $fee_rate, $i );
            }

            // Only include if it's the first installment or above the minimum value
            if ( $i === 1 || $info['installment_price'] >= $min_value ) {
                $installments[] = $info;
            }
        }

        // Merge with any externally provided installments
        $result = array_merge( $installments, $append );

        /**
         * Allow external modification of the full installment list
         *
         * @param array $result | All installment info arrays
         * @param WC_Product $product | Product object
         */
        return apply_filters( 'Woo_Custom_Installments/Installments/All_Installments', $result, $product );
    }


    /**
	 * Get best installment without interest
	 * 
	 * @since 1.0.0
	 * @version 5.4.3
	 * @param array $installments | Product installments
	 * @param object $product | Product object
	 * @return string
	 */
	public static function best_without_interest( $installments, $product ) {
		// check if $installments is different of array or empty $installments or product price is zero
		if ( ! is_array( $installments ) || empty( $installments ) || $product && $product->get_price() <= 0 ) {
			return;
		}

		$hook = self::hook();

		foreach ( $installments as $key => $installment ) {
			if ( 'no-fee' != $installment['class'] ) {
				unset( $installments[$key] );
			}
		}

		// get end installment without fee loop foreach
		$get_installments = end( $installments );

		if ( false === $get_installments ) {
			return;
		}

		// get default options
		$default_options = Default_Options::set_default_data_options();

		$text = '';

		if ( 'main_price' === $hook ) {
			$placeholder = Admin_Options::get_setting('text_display_installments_single_product');

			$text = ! License::is_valid() && empty( $placeholder ) ? $default_options['text_display_installments_single_product'] : $placeholder;
		} else {
			$placeholder = Admin_Options::get_setting('text_display_installments_loop');

			$text = ! License::is_valid() && empty( $placeholder ) ? $default_options['text_display_installments_loop'] : $placeholder;
		}

		$find = array_keys( Helpers::strings_to_replace( $get_installments ) );
		$replace = array_values( Helpers::strings_to_replace( $get_installments ) );
		$text = str_replace( $find, $replace, $text );

		$html = '<span class="woo-custom-installments-details-without-fee" data-end-installments="'. esc_attr( $get_installments['installments_total'] ) .'">';
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

			/**
			 * Filter to change the text of best installment without fee
			 * 
			 * @since 1.0.0
			 * @version 5.4.0
			 * @param string $text | Text to display
			 * @param array $get_installments | Best installment without interest
			 * @param object $product | Product object
			 * @return string
			 */
			$installments = apply_filters( 'Woo_Custom_Installments/Installments/Best_Without_Fee_' . $hook, $text, $get_installments, $product );

			$html .= '<span class="woo-custom-installments-details best-value ' . $get_installments['class'] . '">' . $installments . '</span>';
		$html .= '</span>';

		return $html;
	}


	/**
	 * Get best installment with interest
	 * 
	 * @since 1.0.0
	 * @version 5.4.3
	 * @param array $installments | Product installments
	 * @param object $product | Product object
	 * @return string
	 */
	public static function best_with_interest( $installments, $product ) {
		// check if $installments is different of array or empty $installments or product price is zero
		if ( ! is_array( $installments ) || empty( $installments ) || $product && $product->get_price() <= 0 ) {
			return;
		}

		// remove installment without fee if get type best installments is both
		if ( Admin_Options::get_setting('get_type_best_installments') === 'both' ) {
			foreach ( $installments as $key => $installment ) {
				if ( 'fee-included' !== $installment['class'] ) {
					unset( $installments[ $key ] );
				}
			}
		}

		$installments = array_values( $installments );
		$get_installments = end( $installments );

		if ( false === $get_installments ||  $get_installments['installment_price'] < (int) Admin_Options::get_setting('min_value_installments') ) {
			return;
		}

		$hook = self::hook();
		
		// get default options
		$default_options = Default_Options::set_default_data_options();

		$text = '';

		if ( 'main_price' === $hook ) {
			$placeholder = Admin_Options::get_setting('text_display_installments_single_product');

			$text = ! License::is_valid() && empty( $placeholder ) ? $default_options['text_display_installments_single_product'] : $placeholder;
		} else {
			$placeholder = Admin_Options::get_setting('text_display_installments_loop');

			$text = ! License::is_valid() && empty( $placeholder ) ? $default_options['text_display_installments_loop'] : $placeholder;
		}

		$find = array_keys( Helpers::strings_to_replace( $get_installments ) );
		$replace = array_values( Helpers::strings_to_replace( $get_installments ) );
		$text = str_replace( $find, $replace, $text );

		$html = '<span class="woo-custom-installments-details-with-fee" data-end-installments="'. esc_attr( $get_installments['installments_total'] ) .'">';
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

			/**
			 * Filter to change the text of best installment with fee
			 * 
			 * @since 1.0.0
			 * @version 5.4.0
			 * @param string $text | Text to display
			 * @param array $get_installments | Best installment with interest
			 * @param object $product | Product object
			 * @return string
			 */
			$installments = apply_filters( 'Woo_Custom_Installments/Installments/Best_With_Fee_'. $hook, $text, $get_installments, $product );

			$html .= '<span class="best-value '. $get_installments['class'] .'">'. $installments .'</span>';
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