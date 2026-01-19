<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

// Exit if accessed directly.
defined('ABSPATH') || exit;

if ( defined('SHOPTIMIZER_VERSION') ) {
	/**
	 * Compatibility with Shoptimizer theme
	 *
	 * @since 5.4.2
	 * @version 5.5.1
	 * @package MeuMouse\Woo_Custom_Installments\Integrations
     * @author MeuMouse.com
	 */
	class Shoptimizer {

		/**
		 * Construct function
		 * 
		 * @since 5.4.2
		 * @version 5.5.1
		 * @return void
		 */
		public function __construct() {
			// remove actions on init hook
			add_action( 'init', array( $this, 'remove_actions' ), 99 );
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
}