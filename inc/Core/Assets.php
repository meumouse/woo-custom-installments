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
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Assets {

    public $version = WOO_CUSTOM_INSTALLMENTS_VERSION;
    public $min = WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE ? '' : '.min';
    public $assets_url = WOO_CUSTOM_INSTALLMENTS_ASSETS;
    public $debug_mode = WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE;

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
     * @version 5.4.0
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
        wp_enqueue_style( 'woo-custom-installments-front-styles', $this->assets_url . 'frontend/css/woo-custom-installments-front-styles.css', array(), $this->version );

        if ( Admin_Options::get_setting('icon_format_elements') === 'class' ) {
            wp_enqueue_script( 'font-awesome-lib', $this->assets_url . 'vendor/font-awesome/font-awesome.min.js', array(), '6.4.0' );
        }

        $post_id = get_the_ID();

        if ( is_product() || is_singular() ) {
            if ( get_post_meta( $post_id, 'enable_discount_per_unit', true ) === 'yes' ) {
                wp_enqueue_script( 'woo-custom-installments-front-scripts', $this->assets_url . 'frontend/js/woo-custom-installments-front-scripts.js', array('jquery'), $this->version );

                /**
                 * Filter to add parameters for front scripts
                 * 
                 * @since 1.0.0
                 * @version 5.4.0
                 * @param array $params
                 */
                $params = apply_filters( 'Woo_Custom_Installments/Assets/Front_Params', array(
                    'enable_discount_per_unit' => get_post_meta( $post_id, 'enable_discount_per_unit', true ),
                    'discount_per_unit_method' => get_post_meta( $post_id, 'discount_per_unit_method', true ),
                    'unit_discount_amount' => get_post_meta( $post_id, 'unit_discount_amount', true ),
                    'currency_symbol' => get_woocommerce_currency_symbol(),
                ));
    
                wp_localize_script( 'woo-custom-installments-front-scripts', 'wci_front_params', $params );
            }
        }

        if ( Admin_Options::get_setting('display_installment_type') === 'popup' ) {
            wp_enqueue_style( 'woo-custom-installments-front-modal-styles', $this->assets_url . 'frontend/css/modal.css', array(), $this->version );
            wp_enqueue_script( 'woo-custom-installments-front-modal', $this->assets_url . 'frontend/js/modal.js', array('jquery'), $this->version );
        } elseif ( Admin_Options::get_setting('display_installment_type') === 'accordion' ) {
            wp_enqueue_style( 'woo-custom-installments-front-accordion-styles', $this->assets_url . 'frontend/css/accordion.css', array(), $this->version );
            wp_enqueue_script( 'woo-custom-installments-front-accordion', $this->assets_url . 'frontend/js/accordion.js', array('jquery'), $this->version );
        }

        // update checkout on change payment methods
        if ( is_checkout() && Admin_Options::get_setting('enable_all_discount_options') === 'yes' && ! class_exists('Flexify_Checkout') ) {
            wp_enqueue_script( 'woo-custom-installments-update-checkout', $this->assets_url . 'frontend/js/update-checkout.js', array('jquery'), $this->version );
        }

        $product_id = get_the_ID();
        $product = wc_get_product( $product_id );

        if ( $product && $product->is_type('variable') && ! Helpers::variations_has_same_price( $product ) ) {
            $timestamp = time();

            if ( Admin_Options::get_setting('remove_price_range') === 'yes' && License::is_valid() ) {
                wp_enqueue_script( 'woo-custom-installments-range-price', $this->assets_url . 'frontend/js/woo-custom-installments-range-price.js', array('jquery'), $timestamp, true );

                wp_localize_script('woo-custom-installments-range-price', 'wci_range_params', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'element_triggers' => Admin_Options::get_setting('update_range_price_triggers'),
                    'debug_mode' => $this->debug_mode,
                ));
            }

            wp_enqueue_script( 'accounting-lib', $this->assets_url . 'vendor/accounting/accounting.min.js', array(), '0.4.2' );
            wp_enqueue_script( 'woo-custom-installments-update-table-installments', $this->assets_url . 'frontend/js/woo-custom-installments-update-table-installments.js', array('jquery'), $timestamp, true );

            $installments_fee = array();

            foreach ( range( 1, Admin_Options::get_setting('max_qtd_installments') ) as $i ) {
                $installments_fee[$i] = Helpers::get_fee( false, $i );
            }

            wp_localize_script( 'woo-custom-installments-update-table-installments', 'wci_update_table_params',
                /**
                 * Filter to add parameters for installments table
                 * 
                 * @since 1.0.0
                 * @version 5.4.0
                 * @param array $params
                 * @return array
                 */
                apply_filters( 'Woo_Custom_Installments/Assets/Dynamic_Table_Params', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'currency_format_num_decimals' => wc_get_price_decimals(),
                    'currency_format_symbol' => get_woocommerce_currency_symbol(),
                    'currency_format_decimal_sep' => esc_attr( wc_get_price_decimal_separator() ),
                    'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
                    'currency_format' => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
                    'rounding_precision' => wc_get_rounding_precision(),
                    'max_installments' => Admin_Options::get_setting('max_qtd_installments'),
                    'max_installments_no_fee' => Admin_Options::get_setting('max_qtd_installments_without_fee'),
                    'min_installment' => Admin_Options::get_setting('min_value_installments'),
                    'fee' => Helpers::get_fee(),
                    'fees' => $installments_fee,
                    'without_fee_label' => Admin_Options::get_setting('text_without_fee_installments'),
                    'with_fee_label' => Admin_Options::get_setting('text_with_fee_installments'),
                ))
            );
        }
    }
}