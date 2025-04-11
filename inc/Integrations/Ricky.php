<?php

namespace MeuMouse\Woo_Custom_Installments\Compat;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Ricky theme
 *
 * @since 5.2.1
 * @package MeuMouse.com
 */
class Compat_Ricky {

	/**
	 * Construct function
	 * 
	 * @since 5.2.1
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_head', array( __CLASS__, 'compat_ricky_scripts' ) );
	}


	/**
	 * Add compatibility styles for Ricky theme
     * 
     * @since 5.2.1
     * @return string
	 */
	public static function compat_ricky_scripts() {
        if ( ! function_exists('ideapark_setup_woocommerce') ) {
            return;
        }

        ob_start(); ?>

        .c-product-grid__item .wci-open-popup,
        .c-product-grid__item .wci-accordion-header {
            display: none;
        }

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

        printf( __('<style>%s</style>'), $css );
    }
}

new Compat_Ricky();