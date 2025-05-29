<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

use MeuMouse\Woo_Custom_Installments\Core\Helpers;
use XTS\Modules\Layouts\Main;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Compatibility with Woodmart theme
 *
 * @since 4.5.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Woodmart {

	/**
	 * Construct function
	 * 
	 * @since 4.5.0
     * @version 5.4.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_head', array( __CLASS__, 'compat_woodmart' ) );

        if ( Helpers::check_theme_active('Woodmart') ) {
            add_filter( 'Woo_Custom_Installments/Elementor/Editing_Single_Product', array( $this, 'check_layout_type' ) );
        }

        // inject widget controllers
        add_filter( 'Woo_Custom_Installments/Elementor/Inject_Controllers', array( $this, 'inject_controllers' ), 10, 1 );
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

        .wd-sticky-btn-cart button.wci-open-popup {
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

        .theme-woodmart .quick-shop-wrapper buttonbutton.wci-open-popup {
            display: none;
        }

        <?php $css = ob_get_clean();
        $css = wp_strip_all_tags( $css );

        printf( __('<style>%s</style>'), $css );
    }


    /**
     * Check Woodmart layout type
     * 
     * @since 5.0.0
     * @param bool $is_editing Whether Elementor is editing a single product page
     */
    public function check_layout_type( $is_editing ) {
        if ( ! function_exists('woodmart_theme_setup') ) {
            return;
        }
        
        if ( Main::is_layout_type('single_product') ) {
            return true;
        }
    
        return $is_editing;
    }


    /**
     * Add controllers on Elementor widgets
     * 
     * @since 5.0.0
     * @param array $widgets | Current widgets that receive injected controls
     * @return array
     */
    public function inject_controllers( $widgets ) {
        $new_widgets = array(
            'wd_products' => 'layout_style_section',
            'wd_single_product_price' => 'general_style_section',
            'wd_products_tabs' => 'heading_style_section',
            'wd_products_widget' => 'general_content_section',
            'wd_archive_products' => 'general_style_section',
        );

        return array_merge( $widgets, $new_widgets );
    }
}