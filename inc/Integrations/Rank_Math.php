<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

use MeuMouse\Woo_Custom_Installments\Core\Schema;
use MeuMouse\Woo_Custom_Installments\API\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

// check if Rank Math is active
if ( class_exists('RankMath') ) {
	/**
	 * Compatibility with Rank Math plugin
	 *
	 * @since 5.4.0
	 * @package MeuMouse\Woo_Custom_Installments\Integrations
     * @author MeuMouse.com
	 */
	class Rank_Math extends Schema {

		/**
		 * Construct function
		 * 
		 * @since 5.4.0
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			
			if ( License::is_valid() && class_exists('RankMath') ) {
				add_filter( 'rank_math/json_ld', array( $this, 'rank_math_json_ld' ), 99, 2 );
			}
		}


		/**
		 * Modify Rank Math JSON-LD data
		 * 
		 * @since 5.2.0
		 * @version 5.4.0
		 * @param array $data | JSON-LD data
		 * @param object $jsonld | JSON-LD object
		 */
		public function rank_math_json_ld( $data, $jsonld ) {
			$discount = Schema::get_discount();

			// Check if there is a discount
			if ( 0 >= $discount ) {
				return $data;
			}

			if ( isset( $data['richSnippet']['offers']['price'] ) && $data['richSnippet']['@type'] === 'Product' ) {
				$data['richSnippet']['offers']['price'] = Schema::apply_discount( $data['richSnippet']['offers']['price'] );
			}

			return $data;
		}
	}
}