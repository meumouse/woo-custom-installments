<?php

namespace MeuMouse\Woo_Custom_Installments\Compatibility;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Handles legacy filters for backward compatibility
 *
 * @since 5.4.0
 * @package MeuMouse.com
 */
class Legacy_Filters {

	/**
	 * Construct function
	 * 
	 * @since 5.4.0
	 * @return void
	 */
	public function __construct() {
		if ( defined('WOO_CUSTOM_INSTALLMENTS_VERSION') && version_compare( WOO_CUSTOM_INSTALLMENTS_VERSION, '5.4.0', '>=' ) ) {
			self::map_legacy_filters();
			add_action( 'init', array( $this, 'check_legacy_filters_usage' ), 999 );
		}
	}


	/**
	 * Map legacy filters to their new equivalents
	 *
	 * @since 5.4.0
	 * @return void
	 */
	protected static function map_legacy_filters() {
		$filters = self::get_legacy_filters();

		foreach ( $filters as $old_filter => $data ) {
			add_filter( $old_filter, array( self::class, 'deprecated_filter_callback' ), 9999, 99 );
		}
	}


	/**
	 * Callback to redirect deprecated filters
	 *
	 * @since 5.4.0
	 * @return mixed
	 */
	public static function deprecated_filter_callback( $value, ...$args ) {
		$called_filter = current_filter();
		$filters = self::get_legacy_filters();

		if ( isset( $filters[ $called_filter ] ) ) {
			$new_filter = $filters[ $called_filter ]['new_filter'];
			$version = $filters[ $called_filter ]['version'];

			self::warn_deprecated_filter( $called_filter, $new_filter, $version );

			// redirect to new filter
			return apply_filters_ref_array( $new_filter, array_merge( [ $value ], $args ) );
		}

		return $value;
	}


	/**
	 * Check if legacy filters are being used
	 *
	 * @since 5.4.0
	 * @return void
	 */
	public function check_legacy_filters_usage() {
		$filters = self::get_legacy_filters();

		foreach ( $filters as $old_filter => $data ) {
			global $wp_filter;

			if ( ! isset( $wp_filter[ $old_filter ] ) ) {
				continue;
			}

			$hook = $wp_filter[ $old_filter ];

			if ( is_a( $hook, 'WP_Hook' ) ) {
				$callbacks = $hook->callbacks ?? [];

				// remove our own redirector
				unset( $callbacks[9999] );

				// if have other callbacks registered, emit warning
				if ( ! empty( $callbacks ) ) {
					self::warn_deprecated_filter( $old_filter, $data['new_filter'], $data['version'] );
				}
			}
		}
	}


	/**
	 * Render a deprecation warning for old filters
	 *
	 * @since 5.4.0
	 * @param string $old_filter
	 * @param string $new_filter
	 * @param string $version
	 * @return void
	 */
	protected static function warn_deprecated_filter( $old_filter, $new_filter, $version ) {
		if ( function_exists( '_doing_it_wrong' ) ) {
			$message = sprintf(
				__( 'O filtro "%1$s" está obsoleto desde a versão %3$s. Use "%2$s" em seu lugar.', 'woo-custom-installments' ),
				$old_filter,
				$new_filter,
				$version
			);

			_doing_it_wrong( $old_filter, $message, $version );
		}

		// Log on debug.log
		if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
			error_log( "[Woo_Custom_Installments] Obsolet filter detected: {$old_filter} → {$new_filter} (since version: {$version})" );
		}
	}


	/**
	 * Return legacy filters to their new equivalents
	 *
	 * @since 5.4.0
	 * @return array
	 */
	protected static function get_legacy_filters() {
		return array(
			'woo_custom_installments_group_custom_classes' => array(
				'new_filter' => 'Woo_Custom_Installments/Price/Group_Classes',
				'version' => '5.4.0',
			),
			'woo_custom_installments_strings_to_replace' => array(
				'new_filter' => 'Woo_Custom_Installments/Price/Strings_To_Replace',
				'version' => '5.4.0',
			),
			'woo_custom_installments_fee_label' => array(
				'new_filter' => 'Woo_Custom_Installments/Price/Fee_Label',
				'version' => '5.4.0',
			),
			'woo_custom_installments_installment_info' => array(
				'new_filter' => 'Woo_Custom_Installments/Price/Installment_Info',
				'version' => '5.4.0',
			),
			'woo_custom_installments_set_values_price' => array(
				'new_filter' => 'Woo_Custom_Installments/Price/Set_Values_Price',
				'version' => '5.4.0',
			),
			'woo_custom_installments_card_flags' => array(
				'new_filter' => 'Woo_Custom_Installments/Elements/Card_Flags',
				'version' => '5.4.0',
			),
			'woo_custom_installments_full_installment_product' => array(
				'new_filter' => 'Woo_Custom_Installments/Payment_Methods/Set_Product',
				'version' => '5.4.0',
			),
			'woo_custom_installments_economy_pix_price' => array(
				'new_filter' => 'Woo_Custom_Installments/Price/Economy_Pix_Price',
				'version' => '5.4.0',
			),
			'woo_custom_installments_all_installments' => array(
				'new_filter' => 'Woo_Custom_Installments/Installments/All_Installments',
				'version' => '5.4.0',
			),
			'woo_custom_installments_is_single_product_in_elementor' => array(
				'new_filter' => 'Woo_Custom_Installments/Elementor/Editing_Single_Product',
				'version' => '5.4.0',
			),
			'woo_custom_installments_cart_total_title' => array(
				'new_filter' => 'Woo_Custom_Installments/Cart/Total_Title',
				'version' => '5.4.0',
			),
			'woo_custom_installments_is_available' => array(
				'new_filter' => 'Woo_Custom_Installments/Product/Is_Available',
				'version' => '5.4.0',
			),
			'woo_custom_installments_inject_elementor_controllers' => array(
				'new_filter' => 'Woo_Custom_Installments/Elementor/Inject_Controllers',
				'version' => '5.4.0',
			),
			'woo_custom_installments_set_default_options' => array(
				'new_filter' => 'Woo_Custom_Installments/Admin/Set_Default_Options',
				'version' => '5.4.0',
			),
			'woo_custom_installments_hidden_old_price_widget' => array(
				'new_filter' => 'Woo_Custom_Installments/Widgets/Hidden_Old_Price',
				'version' => '5.4.0',
			),
			'woo_custom_installments_enable_grid_price_widgets' => array(
				'new_filter' => 'Woo_Custom_Installments/Widgets/Enable_Grid_Price',
				'version' => '5.4.0',
			),
			'woo_custom_installments_align_price_group_widgets' => array(
				'new_filter' => 'Woo_Custom_Installments/Widgets/Align_Price_Group',
				'version' => '5.4.0',
			),
			'woo_custom_installments_calculate_total_discount' => array(
				'new_filter' => 'Woo_Custom_Installments/Price/Calculate_Total_Discount',
				'version' => '5.4.0',
			),
			'woo_custom_installments_with_fees' => array(
				'new_filter' => 'Woo_Custom_Installments/Installments/With_Fees',
				'version' => '5.4.0',
			),
			'woo_custom_installments_no_fee' => array(
				'new_filter' => 'Woo_Custom_Installments/Installments/Without_Fee',
				'version' => '5.4.0',
			),
			'woo_custom_installments_final_price' => array(
				'new_filter' => 'Woo_Custom_Installments/Installments/Final_Price',
				'version' => '5.4.0',
			),
			'woo_custom_installments_discounted_price' => array(
				'new_filter' => 'Woo_Custom_Installments/Price/Discounted_Price',
				'version' => '5.4.0',
			),
			'woo_custom_installments_front_params' => array(
				'new_filter' => 'Woo_Custom_Installments/Assets/Frontend_Params',
				'version' => '5.4.0',
			),
			'woo_custom_installments_dynamic_table_params' => array(
				'new_filter' => 'Woo_Custom_Installments/Assets/Frontend_Params',
				'version' => '5.4.0',
			),
			'woo_custom_installments_best_no_fee_' => array(
				'new_filter' => 'Woo_Custom_Installments/Installments/Best_Without_Fee_',
				'version' => '5.4.0',
			),
			'woo_custom_installments_best_with_fee_' => array(
				'new_filter' => 'Woo_Custom_Installments/Installments/Best_With_Fee_',
				'version' => '5.4.0',
			),
			'woo_custom_installments_apply_discount' => array(
				'new_filter' => 'Woo_Custom_Installments/Cart/Apply_Discount',
				'version' => '5.4.0',
			),
			'woo_custom_installments_fee' => array(
				'new_filter' => 'Woo_Custom_Installments/Installments/Get_Fee',
				'version' => '5.4.0',
			),
			'woo_custom_installments_apply_interest' => array(
				'new_filter' => 'Woo_Custom_Installments/Cart/Apply_Interest',
				'version' => '5.4.0',
			),
		);
	}
}