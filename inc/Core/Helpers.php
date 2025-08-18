<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Integrations\Elementor;
use MeuMouse\Woo_Custom_Installments\Core\Calculate_Installments;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Helpers functions
 * 
 * @since 4.5.0
 * @version 5.5.3
 * @package MeuMouse.com
 */
class Helpers {

    /**
     * Check admin page from partial URL
     * 
     * @since 5.4.0
     * @param $admin_page | Page string for check from admin.php?page=
     * @return bool
     */
    public static function check_admin_page( $admin_page ) {
        $current_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
        return strpos( $current_url, "admin.php?page=$admin_page" );
    }


    /**
     * Check if product is available
     * 
     * @since 1.0.0
     * @version 5.4.0
     * @param object $product | Product object
     * @return bool
     */
    public static function is_product_available( $product ) {
        $is_available = true;
        $price = wc_get_price_to_display( $product );

        if ( is_admin() && ! is_ajax() || $product && empty( $price ) || $product && 0 === $price ) {
            $is_available = false;
        }

        /**
         * Filter to check if product is available
         * 
         * @since 1.0.0
         * @version 5.4.0
         * @param bool $is_available
         * @param object $product | Product object
         */
        return apply_filters( 'Woo_Custom_Installments/Product/Is_Available', $is_available, $product );
    }


    /**
     * Check if variations have equal price
     * 
     * @since 1.0.0
     * @version 5.4.7
     * @param object $product | Product object
     * @return bool
     */
    public static function variations_has_same_price( $product ) {
        // if product is not variable, return true
        if ( ! $product instanceof \WC_Product || ! in_array( $product->get_type(), array( 'variable', 'variable-subscription' ), true ) ) {
            return true;
        }

        // get variation prices
        $variation_prices = $product->get_variation_prices( true );

        if ( empty( $variation_prices['price'] ) ) {
            return true;
        }

        $first_price = null;

        foreach ( $variation_prices['price'] as $price_final ) {
            // skip empty prices and null prices
            if ( '' === $price_final || is_null( $price_final ) ) {
                continue;
            }

            $price_final = (float) $price_final;

            if ( $first_price === null ) {
                $first_price = $price_final;
            } elseif ( $price_final !== $first_price ) {
                return false;
            }
        }

        return true;
    }


    /**
     * Get option interest of calc installments
     * 
     * @since 2.3.5
     * @version 5.4.0
     * @param object|bool $product | Product object or false
     * @param int $installments | Number of installments
     * @return float
     */
    public static function get_fee( $product = false, $installments = 1 ) {
        if ( Admin_Options::get_setting('set_fee_per_installment') === 'yes' ) {
            // get custom fee from database
            $custom_fee = maybe_unserialize( get_option('woo_custom_installments_custom_fee_installments', array()) );

            $fee = isset( $custom_fee[$installments]['amount'] ) ? floatval( $custom_fee[$installments]['amount'] ) : 0;
        } else {
            $fee = floatval( Admin_Options::get_setting('fee_installments_global') );
        }
        
        /**
         * Filter to get fee for installments
         * 
         * @since 2.3.5
         * @version 5.4.0
         * @param float $fee | Fee value
         * @param object $product | Product object
         * @param int $installments | Number of installments
         * @return float
         */
        return apply_filters( 'Woo_Custom_Installments/Installments/Get_Fee', $fee, $product, $installments );
    }


    /**
     * Check if the WooCommerce cart page contains the [woocommerce_cart] shortcode
     *
     * @since 4.5.0
     * @return bool
     */
    public static function has_shortcode_cart() {
        // Get the cart page ID from WooCommerce settings
        $cart_page_id = wc_get_page_id('cart');

        // Get the content of the cart page
        $cart_page = get_post( $cart_page_id );

        // Check if the content of the cart page contains the shortcode
        if ( $cart_page && has_shortcode( $cart_page->post_content, 'woocommerce_cart' ) ) {
            return true;
        }

        return false;
    }


    /**
     * Check if a specific theme is active.
     *
     * @since 4.5.0
     * @param string $theme_name | The name of the theme to check
     * @return bool True if the theme is active, false otherwise
     */
    public static function check_theme_active( $theme_name ) {
        $current_theme = wp_get_theme();
        $current_theme_name = $current_theme->get('Name');
    
        // Check if the lowercase version of both names match
        return ( strtolower( $current_theme_name ) === strtolower( $theme_name ) );
    }


    /**
     * Get product ID from post when editing with Elementor
     * 
     * @since 5.0.0
     * @version 5.5.3
     * @param bool $product | Get product object for get id
     * @return mixed | Product ID or false
     */
    public static function get_product_id_from_post( $product = false ) {
        global $post;

        // Tries to get the WC_Product object if not passed as a parameter
        if ( ! $product ) {
            $product = wc_get_product();
        }

        // Check if post exists
        if ( ! $post ) {
            return false;
        }

        // Check if Elementor is in edit mode
        if ( class_exists('\Elementor\Plugin') && ! $product && Elementor::is_edit_mode() && $post ) {
            $post_content = $post->post_content;

            // Checks if there is JSON content in post_content
            $post_data = json_decode( $post_content, true );

            if ( json_last_error() === JSON_ERROR_NONE && isset( $post_data['ID'] ) ) {
                return (int) $post_data['ID']; // Returns product ID from JSON
            }

            // If the content is not JSON, try to search for the ID in HTML format
            preg_match( '/data-product-id=["\']?(\d+)["\']?/', $post_content, $matches );

            if ( isset( $matches[1] ) ) {
                return (int) $matches[1];
            }
        }

        // If the product was found and is a valid instance of WC_Product
        if ( $product instanceof \WC_Product ) {
            return $product->get_id();
        }

        return false;
    }

    
    /**
     * Recursively merges incoming data with existing values
     * 
     * @since 5.2.5
     * @param array $original_data | Existing data in the bank
     * @param array $new_data | Data received from the form
     * @return array Merged data
     */
    public static function merge_options( $original_data, $new_data ) {
        foreach ( $new_data as $key => $value ) {
            if ( is_array( $value ) && isset( $original_data[ $key ] ) && is_array( $original_data[ $key ] ) ) {
                $original_data[ $key ] = self::merge_options( $original_data[ $key ], $value );
            } else {
                $original_data[ $key ] = $value;
            }
        }
    
        return $original_data;
    }


    /**
	 * Replament strings in front-end
	 * 
	 * @since 1.3.0
	 * @version 5.4.0
	 * @param array $values | Value for replace
	 * @return array
	 */
	public static function strings_to_replace( $values ) {
		/**
		 * Replace strings in front-end
		 * 
		 * @since 1.3.0
		 * @version 5.4.0
		 * @param array $values | Value for replace
		 */
		return apply_filters( 'Woo_Custom_Installments/Price/Strings_To_Replace', array(
			'{{ parcelas }}' => $values['installments_total'],
			'{{ valor }}' => wc_price( $values['installment_price'] ),
			'{{ total }}' => wc_price( $values['final_price'] ),
			'{{ juros }}' => Calculate_Installments::get_fee_info( $values ),
		));
	}


    /**
     * Get the first product in the store
     * 
     * @since 5.4.0
     * @return object | Product object or false
     */
    public static function get_first_product() {
        /**
         * Query to get the first product
         * 
         * @since 5.4.0
         */
        $query_args = array(
            'limit' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'status' => 'publish',
        );

        $product_query = new \WC_Product_Query( $query_args );
        $products = $product_query->get_products();

        if ( ! empty( $products ) ) {
            return $products[0];
        }

        return false;
    }
}