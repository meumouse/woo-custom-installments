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
			add_action( 'init', array( $this, 'check_legacy_hooks_usage' ), 999 );
		}
	}

	
	/**
	 * Maps legacy hooks to their new equivalents
	 *
	 * @since 5.4.0
	 * @return void
	 */
	protected static function map_legacy_hooks() {
		$hooks = self::get_legacy_hooks();

		foreach ( $hooks as $old_hook => $data ) {
			add_filter( $old_hook, array( self::class, 'deprecated_hook_callback' ), 9999, 99 );
		}
	}


	/**
	 * Callback to redirect deprecated hooks and emit warning
	 *
	 * @since 5.4.0
	 * @return mixed
	 */
	public static function deprecated_hook_callback( $value, ...$args ) {
		$called_hook = current_filter();
		$hooks = self::get_legacy_hooks();

		if ( isset( $hooks[ $called_hook ] ) ) {
			$new_hook = $hooks[ $called_hook ]['new_hook'];
			$version = $hooks[ $called_hook ]['version'];

			self::warn_deprecated_hook( $called_hook, $new_hook, $version );

			return apply_filters_ref_array( $new_hook, array_merge( [ $value ], $args ) );
		}

		return $value;
	}


	/**
	 * Checks if legacy hooks are being used by other plugins/themes
	 *
	 * @since 5.4.0
	 * @return void
	 */
	public function check_legacy_hooks_usage() {
		$hooks = self::get_legacy_hooks();

		foreach ( $hooks as $old_hook => $data ) {
			global $wp_filter;

			if ( ! isset( $wp_filter[ $old_hook ] ) ) {
				continue;
			}

			$hook = $wp_filter[ $old_hook ];

			if ( is_a( $hook, 'WP_Hook' ) ) {
				$callbacks = $hook->callbacks ?? [];

				// remove our own redirector
				unset( $callbacks[9999] );

				if ( ! empty( $callbacks ) ) {
					self::warn_deprecated_hook( $old_hook, $data['new_hook'], $data['version'] );
				}
			}
		}
	}


	/**
	 * Triggers a deprecation warning for old hooks
	 *
	 * @since 5.4.0
	 * @param string $old_hook | Old hook name
	 * @param string $new_hook | New hook name
	 * @param string $version | Version when the hook was deprecated
	 * @return void
	 */
	protected static function warn_deprecated_hook( $old_hook, $new_hook, $version ) {
		if ( function_exists('_doing_it_wrong') ) {
			$message = sprintf(
				__( 'O gancho "%1$s" está obsoleto desde a versão %3$s. Use "%2$s" em seu lugar.', 'woo-custom-installments' ),
				$old_hook,
				$new_hook,
				$version
			);

			_doing_it_wrong( $old_hook, $message, $version );
		}

		if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
			error_log( "[Woo_Custom_Installments] Obsolet hook detected: {$old_hook} → {$new_hook} (since version {$version})" );
		}
	}


	/**
	 * Returns legacy hooks and their new equivalents
	 *
	 * @since 5.4.0
	 * @return array
	 */
	protected static function get_legacy_hooks() {
		return [
			'woo_custom_installments_before_installments_container' => [
				'new_hook' => 'Woo_Custom_Installments/Elements/Before_Installments_Container',
				'version'  => '5.4.0',
			],
			'woo_custom_installments_after_installments_container' => [
				'new_hook' => 'Woo_Custom_Installments/Elements/After_Installments_Container',
				'version'  => '5.4.0',
			],
			'woo_custom_installments_popup_header' => [
				'new_hook' => 'Woo_Custom_Installments/Elements/Modal_Header',
				'version'  => '5.4.0',
			],
			'woo_custom_installments_popup_bottom' => [
				'new_hook' => 'Woo_Custom_Installments/Elements/Modal_Footer',
				'version'  => '5.4.0',
			],
			'woo_custom_installments_accordion_header' => [
				'new_hook' => 'Woo_Custom_Installments/Elements/Accordion_Header',
				'version'  => '5.4.0',
			],
			'woo_custom_installments_accordion_bottom' => [
				'new_hook' => 'Woo_Custom_Installments/Elements/Accordion_Footer',
				'version'  => '5.4.0',
			],
			'woo_custom_installments_display_admin_notices' => [
				'new_hook' => 'Woo_Custom_Installments/Admin/Header',
				'version'  => '5.4.0',
			],
		];
	}
}