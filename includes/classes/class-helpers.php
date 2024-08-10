<?php

namespace MeuMouse\Woo_Custom_Installments;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Helpers functions
 * 
 * @since 4.5.0
 * @package MeuMouse.com
 */
class Helpers {
    
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
     * E.g:  convert_rgba_colors( '#3498db', -0.2, 50 ) - Darker color at 50% opacity
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
     * @version 4.5.0
     * @param object $product | Product object
     * @return bool
     */
    public static function variations_has_same_price( $product ) {
        return ( $product->is_type( 'variable', 'variation' ) && $product->get_variation_price('min') === $product->get_variation_price('max') );
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

        if ( Init::get_setting('set_fee_per_installment') === 'yes' ) {
            $fee = isset( $custom_fee[$installments]['amount'] ) ? floatval( $custom_fee[$installments]['amount'] ) : 0;
        } else {
            $fee = Init::get_setting('fee_installments_global');
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
}

new Helpers();