<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use Elementor\Plugin as Elementor_Plugin;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Helpers functions
 * 
 * @since 4.5.0
 * @version 5.4.0
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
     * Converts hex color to RGBA
     * 
     * @since 4.5.0
     * @param string $hex | Hexadecimal color
     * @param float $brightness | (Optional) Brightness adjusment 
     * @param int $opacity | (Optional) Opacity color
     * @return string RGBA color
     * 
     * E.g: convert_rgba_colors( '#3498db', 0.2, 80 ) - Lighter color at 80% opacity
     * E.g: convert_rgba_colors( '#3498db', -0.2, 50 ) - Darker color at 50% opacity
     */
    public static function convert_rgba_colors( $hex, $brightness = 0, $opacity = 100 ) {
        // Remove the '#' character if present
        $hex = str_replace( '#', '', $hex );
    
        // Converts hexadecimal color to RGB values
        if ( strlen( $hex ) == 6 ) {
            list( $r, $g, $b ) = [
                hexdec( substr( $hex, 0, 2 ) ),
                hexdec( substr( $hex, 2, 2 ) ),
                hexdec( substr( $hex, 4, 2 ) )
            ];
        } elseif ( strlen( $hex ) == 3 ) {
            list( $r, $g, $b ) = [
                hexdec( str_repeat( substr( $hex, 0, 1 ), 2 ) ),
                hexdec( str_repeat( substr( $hex, 1, 1 ), 2 ) ),
                hexdec( str_repeat( substr( $hex, 2, 1 ), 2 ) ),
            ];
        } else {
            throw new InvalidArgumentException('Formato de cor hexadecimal inválido');
        }
    
        // Adjusts color based on adjustment factor
        $r = max( 0, min( 255, $r + ( $brightness * 255 ) ) );
        $g = max( 0, min( 255, $g + ( $brightness * 255 ) ) );
        $b = max( 0, min( 255, $b + ( $brightness * 255 ) ) );
    
        // Normalize opacity
        $opacity = max( 0, min( 100, $opacity ) ) / 100;
    
        // Returns the color in RGBA format
        return "rgba($r, $g, $b, $opacity)";
    }


    /**
     * Generate RGB color from hexadecimal color
     * 
     * @since 4.5.0
     * @param string $color | Color hexadecimal
     * @return string RGB color
     */
    public static function generate_rgb_color( $color ) {
        // removes the "#" character if present 
        $color = str_replace("#", "", $color);

        // gets the RGB decimal value of each color component
        $red = hexdec( substr( $color, 0, 2 ) );
        $green = hexdec( substr( $color, 2, 2 ) );
        $blue = hexdec( substr( $color, 4, 2 ) );

        // generates RGBA color based on foreground color
        $rgb_color = "$red, $green, $blue";

        return $rgb_color;
    }


    /**
     * Check if product is available
     * 
     * @since 1.0.0
     * @version 4.5.0
     * @param mixed $product | Product ID or false
     * @return bool
     */
    public static function is_available( $product = false ) {
        $is_available = true;
        $price = wc_get_price_to_display( $product );

        if ( is_admin() && ! is_ajax() || $product && empty( $price ) || $product && 0 === $price ) {
            $is_available = false;
        }

        return apply_filters( 'woo_custom_installments_is_available', $is_available, $product );
    }


    /**
     * Check if variations have equal price
     * 
     * @since 1.0.0
     * @version 5.2.7
     * @param object $product | Product object
     * @return bool
     */
    public static function variations_has_same_price( $product ) {
        // get the product object, if id is passed
        if ( ! $product instanceof WC_Product ) {
            $product = wc_get_product( $product );
        }

        // check if is a product variable
        if ( ! $product || ! $product->is_type('variable') ) {
            return false; // it not product variable
        }

        // get all variations from product
        $variations = $product->get_children();
        
        if ( empty( $variations ) ) {
            return false; // there are no variations
        }

        // get price from first variation as a referrer
        $first_price = null;

        foreach ( $variations as $variation_id ) {
            $variation = wc_get_product( $variation_id );

            if ( ! $variation || ! $variation->is_purchasable() ) {
                continue; // ignore invalid variations
            }

            $price = (float) $variation->get_regular_price();

            if ( is_null( $first_price ) ) {
                $first_price = $price; // set initial price
            } elseif ( $price !== $first_price ) {
                return false; // find a different price
            }
        }

        return true;
    }


    /**
     * Get option interest of calc installments
     * 
     * @since 2.3.5
     * @version 4.5.0
     * @return string
     */
    public static function get_fee( $product = false, $installments = 1 ) {
        $custom_fee = maybe_unserialize( get_option('woo_custom_installments_custom_fee_installments', array()) );

        if ( Admin_Options::get_setting('set_fee_per_installment') === 'yes' ) {
            $fee = isset( $custom_fee[$installments]['amount'] ) ? floatval( $custom_fee[$installments]['amount'] ) : 0;
        } else {
            $fee = Admin_Options::get_setting('fee_installments_global');
        }
        
        return apply_filters( 'woo_custom_installments_fee', $fee, $product, $installments );
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
     * Check if the Elementor editor is currently editing a single product page.
     * 
     * @since 5.0.0
     * @version 5.1.0
     * @return bool True if editing a single product page in Elementor; false otherwise.
     */
    public static function is_editing_single_product_in_elementor() {
        $is_editing = false;

        // Check if Elementor is in edit mode
        if ( self::elementor_is_editing_mode() ) {
            $post_type = get_post_type();

            // Checks whether we are editing a product page directly or a product template
            if ( $post_type === 'product' ) {
                // You are directly editing a product page
                $is_editing = true;
            } elseif ( $post_type === 'elementor_library' ) {
                // Check if we are editing a product template
                if ( isset( $_GET['post'] ) ) {
                    $post_id = intval( $_GET['post'] );
                    $template_type = get_post_meta( $post_id, '_elementor_template_type', true );

                    if ( 'product' === $template_type ) {
                        $is_editing = true;
                    }
                }
            }

            // Checks if the post content is in JSON format and contains a product
            global $post;

            if ( $post ) {
                $post_content = $post->post_content;
                $post_data = json_decode( $post_content, true );

                // If the content is JSON and the post_type is 'product', we consider that we are editing a product
                if ( json_last_error() === JSON_ERROR_NONE && isset( $post_data['post_type'] ) && $post_data['post_type'] === 'product' ) {
                    $is_editing = true;
                }
            }
        }

        /**
         * Filter to modify the condition for checking if editing a single product in Elementor
         *
         * @since 5.0.0
         * @param bool $is_editing | Whether Elementor is editing a single product page
         */
        return apply_filters('woo_custom_installments_is_single_product_in_elementor', $is_editing);
    }


    /**
     * Check if is editing mode on Elementor
     * 
     * @since 5.0.0
     * @version 5.3.0
     * @return bool
     */
    public static function elementor_is_editing_mode() {
        if ( Elementor_Plugin::$instance->editor->is_edit_mode() ) {
            return true;
        }

        return false;
    }


    /**
     * Get product ID from post when editing with Elementor
     * 
     * @since 5.0.0
     * @version 5.1.0
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
        if ( ! $product && self::elementor_is_editing_mode() && $post ) {
            $post_content = $post->post_content;

            // Checks if there is JSON content in post_content
            $post_data = json_decode( $post_content, true );

            if ( json_last_error() === JSON_ERROR_NONE && isset( $post_data['ID'] ) ) {
                return (int) $post_data['ID']; // Returns product ID from JSON
            }

            // If the content is not JSON, try to search for the ID in HTML format
            preg_match( '/data-product_id=["\']?(\d+)["\']?/', $post_content, $matches );

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
}