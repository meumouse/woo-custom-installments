<?php

namespace MeuMouse\Woo_Custom_Installments\Compat;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Machic theme
 *
 * @since 4.5.0
 * @package MeuMouse.com
 */
class Compat_Machic {

	/**
	 * Construct function
	 * 
	 * @since 4.5.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_head', array( __CLASS__, 'compat_machic' ) );
	}


	/**
	 * Add compatibility styles for Machic theme
     * 
     * @since 4.5.0
     * @return string
	 */
	public static function compat_machic() {
        if ( ! function_exists('machic_get_option') ) {
            return;
        }

        ob_start(); ?>

        .theme-machic .single-product-container .product-price .woo-custom-installments-group {
            justify-items: flex-start;
        }

        .theme-machic .single-product-container .product-price .price {
            display: block;
        }

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

        printf( __('<style>%s</style>'), $css );
    }
}

new Compat_Machic();