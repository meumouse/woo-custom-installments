<?php

namespace MeuMouse\Woo_Custom_Installments\Compat;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Astra theme
 *
 * @since 4.5.0
 * @package MeuMouse.com
 */
class Compat_Astra {

	/**
	 * Construct function
	 * 
	 * @since 4.5.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_head', array( __CLASS__, 'compat_astra' ) );
	}


	/**
	 * Add compatibility styles for Astra theme
     * 
     * @since 4.5.0
     * @return string
	 */
	public static function compat_astra() {
        if ( ! class_exists('Astra_Theme_Options') ) {
            return;
        }

        ob_start(); ?>

        .ast-sticky-add-to-cart-action-wrap #wci-open-popup {
            display: none;
        }

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

        printf( __('<style>%s</style>'), $css );
    }
}

new Compat_Astra();