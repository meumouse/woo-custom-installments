<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Astra theme
 *
 * @since 4.5.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Astra {

	/**
	 * Construct function
	 * 
	 * @since 4.5.0
     * @version 5.4.0
	 * @return void
	 */
	public function __construct() {
        if ( class_exists('Astra_Theme_Options') ) {
		    add_action( 'wp_head', array( __CLASS__, 'compat_astra' ) );
        }
	}


	/**
	 * Add compatibility styles for Astra theme
     * 
     * @since 4.5.0
     * @version 5.4.0
     * @return string
	 */
	public static function compat_astra() {
        ob_start(); ?>

        .ast-sticky-add-to-cart-action-wrap button.wci-open-popup {
            display: none;
        }

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

        printf( __('<style>%s</style>'), $css );
    }
}