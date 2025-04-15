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
		}
	}


	/**
	 * Maps legacy filters to their new equivalents
	 *
	 * @since 5.4.0
	 * @return void
	 */
	protected static function map_legacy_filters() {
		$filters = array(
			'woo_custom_installments_group_custom_classes' => array(
				'new_filter' => 'Woo_Custom_Installments/Price/Group_Classes',
				'version' => '5.4.0',
			),
		);
	
		// iterate for each filter
		foreach ( $filters as $old_filter => $data ) {
			$new_hook = $data['new_filter'];
			$version = $data['version'];
	
			add_filter( $old_filter, function( $value, ...$args ) use ( $old_filter, $new_hook, $version ) {
				self::warn_deprecated_filter( $old_filter, $new_hook, $version );

				return apply_filters_ref_array( $new_hook, array_merge( array( $value ), $args ) );
			}, 10, 99 );
		}
	}


	/**
	 * Triggers a deprecation warning for old filters
	 *
	 * @since 5.4.0
	 * @param string $old_filter | Old filter name
	 * @param string $new_hook | New filter name
	 * @param string $version | Version in which the filter was deprecated
	 * @return void
	 */
	protected static function warn_deprecated_filter( $old_filter, $new_hook, $version ) {
		if ( function_exists('doing_it_wrong') ) {
			doing_it_wrong(
				$old_filter,
				sprintf( __( 'O filtro "%1$s" está obsoleto desde a versão %3$s. Use "%2$s" em seu lugar.', 'woo-custom-installments' ),
					$old_filter,
					$new_hook,
					$version
				),
				$version
			);
		}
	}
}