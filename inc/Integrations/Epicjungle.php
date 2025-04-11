<?php

namespace MeuMouse\Woo_Custom_Installments\Compat;

use MeuMouse\Woo_Custom_Installments\Frontend;
use MeuMouse\Woo_Custom_Installments\Helpers;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with EpicJungle theme
 *
 * @since 4.5.0
 * @package MeuMouse.com
 */
class Compat_Epicjungle {

	/**
	 * Construct function
	 * 
	 * @since 4.5.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_head', array( __CLASS__, 'compat_epicjungle' ) );
        add_action( 'init', array( __CLASS__, 'compat_hooks' ) );
	}


    /**
     * Add compatibility with WooCommerce hooks
     * 
     * @since 4.5.0
     * @return void
     */
    public static function compat_hooks() {
        if ( ! class_exists('EpicJungle') || ! Helpers::has_shortcode_cart() ) {
            return;
        }

        remove_action( 'woocommerce_cart_totals_before_order_total', array( 'MeuMouse\Woo_Custom_Installments\Frontend', 'display_discount_on_cart' ) );
        add_action( 'woocommerce_cart_totals_before_shipping', array( 'MeuMouse\Woo_Custom_Installments\Frontend', 'display_discount_on_cart' ) );
    }


	/**
	 * Add compatibility styles for EpicJungle theme
     * 
     * @since 4.5.0
     * @return string
	 */
	public static function compat_epicjungle() {
        if ( ! class_exists('EpicJungle') ) {
            return;
        }

        ob_start(); ?>

        .media-body .woo-custom-installments-group {
            display: none;
        }

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

        printf( __('<style>%s</style>'), $css );
    }
}

new Compat_Epicjungle();