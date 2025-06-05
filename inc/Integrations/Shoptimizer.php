<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

use MeuMouse\Woo_Custom_Installments\Core\Helpers;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Shoptimizer theme
 *
 * @since 5.4.2
 * @package MeuMouse.com
 */
class Shoptimizer {

	/**
	 * Construct function
	 * 
	 * @since 5.4.2
	 * @return void
	 */
	public function __construct() {
        if ( defined('SHOPTIMIZER_VERSION') || Helpers::check_theme_active('shoptimizer') ) {
            // remove actions on init hook
            add_action( 'init', array( $this, 'remove_actions' ), 99 );
        }
	}


	/**
	 * Remove actions for prevent conflicts
     * 
     * @since 5.4.2
     * @return void
	 */
	public function remove_actions() {
        remove_action( 'woocommerce_before_shop_loop_item_title', 'shoptimizer_change_displayed_sale_price_html', 7 );
        remove_action( 'woocommerce_single_product_summary', 'shoptimizer_change_displayed_sale_price_html', 10 );
    }
}