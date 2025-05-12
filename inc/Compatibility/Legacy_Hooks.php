<?php

namespace MeuMouse\Woo_Custom_Installments\Compatibility;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Handles legacy hooks for backward compatibility
 *
 * @since 5.4.0
 * @package MeuMouse.com
 */
class Legacy_Hooks {

	/**
	 * Construct function
	 * 
	 * @since 5.4.0
	 * @return void
	 */
	public function __construct() {
		if ( defined('WOO_CUSTOM_INSTALLMENTS_VERSION') && version_compare( WOO_CUSTOM_INSTALLMENTS_VERSION, '5.4.0', '>=' ) ) {
			self::map_legacy_hooks();
		}
	}


	/**
	 * Maps legacy hooks to their new equivalents
	 *
	 * @since 5.4.0
	 * @return void
	 */
	protected static function map_legacy_hooks() {
		$filters = array(
			'woo_custom_installments_before_installments_container' => array(
				'new_hook' => 'Woo_Custom_Installments/Elements/Before_Installments_Container',
				'version' => '5.4.0',
			),
			'woo_custom_installments_after_installments_container' => array(
				'new_hook' => 'Woo_Custom_Installments/Elements/After_Installments_Container',
				'version' => '5.4.0',
			),
			'woo_custom_installments_popup_header' => array(
				'new_hook' => 'Woo_Custom_Installments/Elements/Modal_Header',
				'version' => '5.4.0',
			),
			'woo_custom_installments_popup_bottom' => array(
				'new_hook' => 'Woo_Custom_Installments/Elements/Modal_Footer',
				'version' => '5.4.0',
			),
			'woo_custom_installments_accordion_header' => array(
				'new_hook' => 'Woo_Custom_Installments/Elements/Accordion_Header',
				'version' => '5.4.0',
			),
			'woo_custom_installments_accordion_bottom' => array(
				'new_hook' => 'Woo_Custom_Installments/Elements/Accordion_Footer',
				'version' => '5.4.0',
			),
			'woo_custom_installments_display_admin_notices' => array(
				'new_hook' => 'Woo_Custom_Installments/Admin/Header',
				'version' => '5.4.0',
			),
			
		);
	
		// iterate for each hook
		foreach ( $filters as $old_hook => $data ) {
			$new_hook = $data['new_hook'];
			$version = $data['version'];
	
			add_filter( $old_hook, function( $value, ...$args ) use ( $old_hook, $new_hook, $version ) {
				self::warn_deprecated_hook( $old_hook, $new_hook, $version );

				return apply_filters_ref_array( $new_hook, array_merge( array( $value ), $args ) );
			}, 10, 99 );
		}
	}


	/**
	 * Triggers a deprecation warning for old hooks
	 *
	 * @since 5.4.0
	 * @param string $old_hook | Old hook name
	 * @param string $new_hook | New hook name
	 * @param string $version | Version in which the hook was deprecated
	 * @return void
	 */
	protected static function warn_deprecated_hook( $old_hook, $new_hook, $version ) {
		if ( function_exists('doing_it_wrong') ) {
			doing_it_wrong(
				$old_hook,
				sprintf( __( 'O gancho "%1$s" está obsoleto desde a versão %3$s. Use "%2$s" em seu lugar.', 'woo-custom-installments' ),
					$old_hook,
					$new_hook,
					$version
				),
				$version
			);
		}
	}
}