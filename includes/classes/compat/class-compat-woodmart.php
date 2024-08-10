<?php

namespace MeuMouse\Woo_Custom_Installments\Compat;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Woodmart theme
 *
 * @since 4.5.0
 * @package MeuMouse.com
 */
class Compat_Woodmart {

	/**
	 * Construct function
	 * 
	 * @since 4.5.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_head', array( __CLASS__, 'compat_woodmart' ) );
	}


	/**
	 * Add compatibility styles for Woodmart theme
     * 
     * @since 4.5.0
     * @return string
	 */
	public static function compat_woodmart() {
        if ( ! function_exists('woodmart_theme_setup') ) {
            return;
        }

        ob_start(); ?>

        .wd-sticky-btn-cart #wci-open-popup {
            display: none;
        }

        .wd-sticky-btn .woo-custom-installments-group {
            display: none;
        }

        .wd-sticky-btn-cart .woo-custom-installments-group {
            display: flex;
            align-items: center;
        }

        .wd-sticky-btn-cart .original-price {
            font-size: 1.125rem;
        }

        .theme-woodmart .woocommerce-grouped-product-list .woo-custom-installments-group {
            display: none;
        }

        .wd-product-nav-desc .woo-custom-installments-group {
            display: none;
        }

        .theme-woodmart .quick-shop-wrapper button#wci-open-popup {
            display: none;
        }

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

        printf( __('<style>%s</style>'), $css );
    }
}

new Compat_Woodmart();