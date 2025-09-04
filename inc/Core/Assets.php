<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\API\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Load plugin assets and dependencies
 * 
 * @since 4.0.0
 * @version 5.5.2
 * @package MeuMouse.com
 */
class Assets {

    public $version = WOO_CUSTOM_INSTALLMENTS_VERSION;
    public $min = WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ? '' : '.min';
    public $assets_url = WOO_CUSTOM_INSTALLMENTS_ASSETS;
    public $debug_mode = WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE;
    public $dev_mode = WOO_CUSTOM_INSTALLMENTS_DEV_MODE;

    /**
     * Construct function
     * 
     * @since 4.0.0
     * @version 5.4.0
     * @return void
     */
    public function __construct() {
        // add settings scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

        // add frontend scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
    }


    /**
     * Enqueue admin scripts in page settings only
     * 
     * @since 2.0.0
     * @version 5.5.4
     * @return void
     */
    public function admin_assets() {
        // check if is admin page settings
        if ( Helpers::check_admin_page('woo-custom-installments') ) {
            wp_enqueue_media();

            // MiniColors
            wp_enqueue_script( 'woo-custom-installments-minicolors-scripts', $this->assets_url . 'vendor/minicolors/jquery.minicolors.min.js', array('jquery'), '2.3.6' );
            wp_enqueue_style( 'woo-custom-installments-minicolors-styles', $this->assets_url . 'vendor/minicolors/jquery.minicolors.css', array(), '2.3.6' );

            /**
             * Filter to add dependencies in settings script
             * 
             * @since 5.4.0
             * @param array $deps
             */
            $deps = apply_filters( 'Woo_Custom_Installments/Admin/Assets/Settings_Script', array(
                'jquery',
                'woo-custom-installments-minicolors-scripts',
            ));

            // settings scripts
            wp_enqueue_style( 'woo-custom-installments-admin-styles', $this->assets_url . 'admin/css/settings'. $this->min .'.css', array(), $this->version );
            wp_enqueue_script( 'woo-custom-installments-admin-scripts', $this->assets_url . 'admin/js/settings'. $this->min .'.js', $deps, $this->version );
            
            // set script params
            wp_localize_script( 'woo-custom-installments-admin-scripts', 'wci_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'debug_mode' => $this->debug_mode,
                'license_valid' => License::is_valid(),
                'i18n' => array(
                    'aria_label_modal' => esc_html__( 'Fechar', 'woo-custom-installments' ),
                    'offline_toast_header' => esc_html__( 'Ops! Não há conexão com a internet', 'woo-custom-installments' ),
                    'offline_toast_body' => esc_html__( 'As alterações não serão salvas.', 'woo-custom-installments' ),
                    'set_media_title' => esc_html__( 'Escolher imagem de ícone', 'woo-custom-installments' ),
                    'use_this_media_title' => esc_html__( 'Usar esta imagem', 'woo-custom-installments' ),
                    'confirm_reset_settings' => esc_html__( 'Tem certeza que deseja redefinir suas configurações?', 'woo-custom-installments' ),
                ),
                'nonces' => array(
                    'save_settings' => wp_create_nonce('wci_save_options_nonce'),
                ),
                'currency_symbol' => get_woocommerce_currency_symbol(),
                'check_format_icons' => Admin_Options::get_setting('icon_format_elements'),
            ));

            wp_enqueue_script( 'woo-custom-installments-license-scripts', $this->assets_url . 'admin/js/license'. $this->min .'.js', array('jquery'), $this->version );

            // set script params
            wp_localize_script( 'woo-custom-installments-license-scripts', 'wci_license_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'debug_mode' => $this->debug_mode,
                'i18n' => array(
                    'aria_label_modal' => esc_html__( 'Fechar', 'woo-custom-installments' ),
                    'confirm_deactivate_license' => esc_html__( 'Tem certeza que deseja desativar sua licença?', 'woo-custom-installments' ),
                ),
            ));

            // add Bootstrap grid and utilities if Flexify Dashboard is not installed
            if ( ! class_exists('Flexify_Dashboard') ) {
                wp_enqueue_style( 'bootstrap-grid', $this->assets_url . 'vendor/bootstrap/bootstrap-grid.min.css', array(), '5.3.3' );
                wp_enqueue_style( 'bootstrap-utilities', $this->assets_url . 'vendor/bootstrap/bootstrap-utilities.min.css', array(), '5.3.3' );
            }

            // add Font Awesome if icon format is class
            if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) {
                wp_enqueue_script( 'font-awesome-lib', $this->assets_url . 'vendor/font-awesome/font-awesome.min.js', array(), '6.4.0' );
            }
        }
    }


    /**
     * Enqueue scripts and styles on frontend
     *
     * @since 1.0.0
     * @version 5.4.0
     * @return void
     */
    public function frontend_assets() {
        /**
         * Filter to add cache option for front scripts
         * 
         * @since 5.4.0
         */
        $cache = apply_filters( 'Woo_Custom_Installments/Assets/Front_Scripts/Cache', true );

        // If cache is enabled, set version to current timestamp
        $set_version = $cache === true ? time() : $this->version;

        // Enqueue front-end styles
        wp_enqueue_style( 'woo-custom-installments-front-styles', $this->assets_url . 'frontend/css/woo-custom-installments-front-styles'. $this->min .'.css', array(), $set_version );

        // If icons are set to use Font Awesome classes, enqueue Font Awesome
        if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) {
            wp_enqueue_script( 'font-awesome-lib', $this->assets_url . 'vendor/font-awesome/font-awesome.min.js', array(), '6.4.0', true );
        }

        $deps = array('jquery');

        // Prepare front‐params if discount per unit is enabled on a product page
        if ( is_product() || is_singular('product') ) {
            // Enqueue accounting library for price formatting
            wp_enqueue_script( 'accounting-lib', $this->assets_url . 'vendor/accounting/accounting.min.js', array(), '0.4.2', true );

            // set accounting library as dependency
            $deps[] = 'accounting-lib';

            wp_enqueue_script( 'woo-custom-installments-front-scripts', $this->assets_url . 'frontend/js/woo-custom-installments-front-scripts'. $this->min .'.js', $deps, $set_version, true );

            // send params to script
            wp_localize_script( 'woo-custom-installments-front-scripts', 'wci_front_params', $this->frontend_params() );
        }

        // update checkout on change payment methods
        if ( is_checkout() && Admin_Options::get_setting('enable_all_discount_options') === 'yes' && ! class_exists('Flexify_Checkout') ) {
            wp_enqueue_script( 'woo-custom-installments-update-checkout', $this->assets_url . 'frontend/js/update-checkout'. $this->min .'.js', array('jquery'), $set_version );
        }
    }


    /**
     * Get frontend params to script
     * 
     * @since 5.4.0
     * @version 5.5.2
     * @return array
     */
    public function frontend_params() {
        $product_id = Helpers::get_product_id_from_post();

        /**
         * Filter to set product id on frontend params
         * 
         * @since 5.4.0
         * @param int $product_id | Product ID
         * @return int
         */
        $product_id = apply_filters( 'Woo_Custom_Installments/Assets/Set_Product_Id', $product_id );

        // Get product object
        $product = wc_get_product( $product_id );

        // Get product object if empty on Elementor editor
        if ( ! $product ) {
            $product = Helpers::get_first_product();
        }

        // Prepare installments fees array
        $installments_fee = array();
        $max_installments = (int) Admin_Options::get_setting('max_qtd_installments');

        for ( $i = 1; $i <= $max_installments; $i++ ) {
            $installments_fee[ $i ] = Helpers::get_fee( false, $i );
        }

        /**
         * Filter to add parameters on frontend scripts
         *
         * @since 1.0.0
         * @version 5.4.3
         * @param array $params
         */
        $params = apply_filters( 'Woo_Custom_Installments/Assets/Frontend_Params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'debug_mode' => $this->debug_mode,
            'dev_mode' => $this->dev_mode,
            'license_valid' => License::is_valid(),
            'element_triggers'=> Admin_Options::get_setting('update_range_price_triggers'),
            'active_price_range' => Admin_Options::get_setting('remove_price_range'),
            'product_variation_with_range' => ! Helpers::variations_has_same_price( $product ),
            'update_price_with_quantity' => Admin_Options::get_setting('update_price_with_quantity'),
            'i18n' => array(
                'without_fee_label' => Admin_Options::get_setting('text_without_fee_installments'),
                'with_fee_label' => Admin_Options::get_setting('text_with_fee_installments'),
                'best_installments_sp' => Admin_Options::get_setting('text_display_installments_single_product'),
            ),
            'currency' => array(
                'format_num_decimals' => wc_get_price_decimals(),
                'symbol' => get_woocommerce_currency_symbol(),
                'format_decimal_sep' => esc_attr( wc_get_price_decimal_separator() ),
                'format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
                'format' => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
            ),
            'discounts' => array(
                'enable_discount_per_unit' => get_post_meta( $product_id, 'enable_discount_per_unit', true ),
                'discount_per_unit_method' => get_post_meta( $product_id, 'discount_per_unit_method', true ),
                'unit_discount_amount' => get_post_meta( $product_id, 'unit_discount_amount', true ),
                'pix_discount' => (float) Admin_Options::get_setting('discount_main_price'),
                'pix_discount_method' => Admin_Options::get_setting('product_price_discount_method'),
                'slip_bank_discount' => (float) Admin_Options::get_setting('discount_ticket'),
                'slip_bank_method' => Admin_Options::get_setting('discount_method_ticket'),
            ),
            'installments' => array(
                'max_installments' => $max_installments,
                'max_installments_no_fee' => (int) Admin_Options::get_setting('max_qtd_installments_without_fee'),
                'min_installment' => (float) Admin_Options::get_setting('min_value_installments'),
                'fee' => Helpers::get_fee(),
                'fees' => $installments_fee,
            ),
        ));

        // check if product object exists for prevent conflicts
        if ( $product ) {
            $params['product'] = array(
                'id' => $product->get_id(),
                'type' => $product->get_type(),
                'regular_price' => (float) $product->get_regular_price(),
                'sale_price' => (float) $product->get_sale_price(),
                'current_price' => (float) $product->get_price(),
            );
        }

        return $params;
    }
}