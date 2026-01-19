<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

use MeuMouse\Woo_Custom_Installments\Core\Helpers;

// Exit if accessed directly.
defined('ABSPATH') || exit;

// check if the current theme is XStore
if ( defined('ETHEME_THEME_NAME') && ETHEME_THEME_NAME === 'XStore' ) {
    /**
     * Compatibility with XStore theme
     *
     * @since 5.5.1
     * @package MeuMouse\Woo_Custom_Installments\Integrations
     * @author MeuMouse.com
     */
    class Xstore {

        /**
         * Construct function
         * 
         * @since 5.5.1
         * @return void
         */
        public function __construct() {
            // inject widget controllers
            add_filter( 'Woo_Custom_Installments/Elementor/Inject_Controllers', array( $this, 'inject_controllers' ), 10, 1 );
        }


        /**
         * Add controllers on XStore Elementor widgets
         * 
         * @since 5.5.1
         * @param array $widgets | Current widgets that receive injected controls
         * @return array
         */
        public function inject_controllers( $widgets ) {
            $new_widgets = array(
                'etheme_product_carousel' => 'section_product_style',
                'woocommerce-product-etheme_cross_sells' => 'section_general_style_section',
                'etheme_product_list' => 'section_product_style',
                'et-custom-products-masonry' => 'style_section',
                'et-advanced-tabs' => 'et_section_tabs_style_settings',
                'etheme_ajax_search' => 'section_style_general',
                'woocommerce-product-etheme_related' => 'section_general_style_section',
            );

            return array_merge( $widgets, $new_widgets );
        }
    }
}