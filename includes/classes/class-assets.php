<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\Helpers;
use MeuMouse\Woo_Custom_Installments\Frontend; 
use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Load plugin assets and dependencies
 * 
 * @since 4.0.0
 * @version 4.5.0
 * @package MeuMouse.com
 */
class Assets {

    /**
     * Construct function
     * 
     * @since 4.0.0
     * @version 4.5.0
     * @return void
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'update_checkout' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'update_price_on_select_variation' ), 10 );
    }


    /**
     * Enqueue admin scripts in page settings only
     * 
     * @since 2.0.0
     * @version 4.5.0
     * @return void
     */
    public function admin_assets() {
        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        if ( strpos( $url, 'admin.php?page=woo-custom-installments' ) !== false ) {
            wp_enqueue_media();

            wp_enqueue_script( 'woo-custom-installments-admin-scripts', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'admin/js/woo-custom-installments-admin-scripts.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
            wp_enqueue_style( 'woo-custom-installments-admin-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'admin/css/woo-custom-installments-admin-styles.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
            
            if ( ! class_exists('Flexify_Dashboard') ) {
                wp_enqueue_style( 'bootstrap-grid', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'vendor/bootstrap/bootstrap-grid.min.css', array(), '5.3.3' );
                wp_enqueue_style( 'bootstrap-utilities', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'vendor/bootstrap/bootstrap-utilities.min.css', array(), '5.3.3' );
            }

            wp_localize_script( 'woo-custom-installments-admin-scripts', 'wci_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'confirm_deactivate_license' => esc_html__( 'Tem certeza que deseja desativar sua licença?', 'woo-custom-installments' ),
                'currency_symbol' => get_woocommerce_currency_symbol(),
                'offline_toast_header' => esc_html__( 'Ops! Não há conexão com a internet', 'woo-custom-installments' ),
                'offline_toast_body' => esc_html__( 'As alterações não serão salvas.', 'woo-custom-installments' ),
            ));
        }
    }


    /**
     * Enqueue scripts and styles on frontend
     *
     * @since 1.0.0
     * @version 4.5.0
     * @return void
     */
    public function frontend_assets() {
        wp_enqueue_style( 'woo-custom-installments-front-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/css/woo-custom-installments-front-styles.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
        wp_enqueue_script( 'font-awesome-lib', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/font-awesome.min.js', array(), '6.4.0' );

        if ( is_product() || is_singular() ) {
            $post_id = get_the_ID();

            if ( get_post_meta( $post_id, 'enable_discount_per_unit', true ) === 'yes' ) {
                wp_enqueue_script( 'woo-custom-installments-front-scripts', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-front-scripts.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );

                $params = apply_filters( 'woo_custom_installments_front_params', array(
                    'enable_discount_per_unit' => get_post_meta( $post_id, 'enable_discount_per_unit', true ),
                    'discount_per_unit_method' => get_post_meta( $post_id, 'discount_per_unit_method', true ),
                    'unit_discount_amount' => get_post_meta( $post_id, 'unit_discount_amount', true ),
                    'currency_symbol' => get_woocommerce_currency_symbol(),
                ));
    
                wp_localize_script( 'woo-custom-installments-front-scripts', 'wci_front_params', $params );
            }
        }

        if ( Init::get_setting('display_installment_type') === 'popup' ) {
            wp_enqueue_style( 'woo-custom-installments-front-modal-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/css/modal.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
            wp_enqueue_script( 'woo-custom-installments-front-modal', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/modal.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        } elseif ( Init::get_setting('display_installment_type') === 'accordion' ) {
            wp_enqueue_style( 'woo-custom-installments-front-accordion-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/css/accordion.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
            wp_enqueue_script( 'woo-custom-installments-front-accordion', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/accordion.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        }
    }


    /**
     * Enqueue update checkout script
     * 
     * @since 3.6.0
     * @version 4.5.0
     * @return void
     */
    public function update_checkout() {
        if ( is_checkout() && Init::get_setting('enable_all_discount_options') === 'yes' && ! class_exists('Flexify_Checkout') ) {
            wp_enqueue_script( 'woo-custom-installments-update-checkout', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/update-checkout.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        }
    }


    /**
     * Enqueue script for update price on select variation
     * 
     * @since 2.9.0
     * @version 4.5.1
     * @return void
     */
    public function update_price_on_select_variation() {
        $product_id = get_the_ID();
        $product = wc_get_product( $product_id );

        if ( $product->is_type( 'variable', 'variation' ) && ! Helpers::variations_has_same_price( $product ) ) {
            if ( Init::get_setting('remove_price_range') === 'yes' && License::is_valid() ) {
                wp_enqueue_script( 'woo-custom-installments-range-price', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-range-price.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );

                wp_localize_script('woo-custom-installments-range-price', 'wci_range_params', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                ));                    
            }

            wp_enqueue_script( 'accounting-lib', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/accounting.min.js', array(), '0.4.2' );
            wp_enqueue_script( 'woo-custom-installments-update-table-installments', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-update-table-installments.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );

            $interest = Helpers::get_fee();
            $installments_fee = array();

            foreach ( range( 1, Init::get_setting('max_qtd_installments') ) as $i ) {
                $installments_fee[$i] = Helpers::get_fee( false, $i );
            }

            wp_localize_script( 'woo-custom-installments-update-table-installments', 'wci_update_table_params', apply_filters( 'woo_custom_installments_dynamic_table_params', array(
                'currency_format_num_decimals' => wc_get_price_decimals(),
                'currency_format_symbol' => get_woocommerce_currency_symbol(),
                'currency_format_decimal_sep' => esc_attr( wc_get_price_decimal_separator() ),
                'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
                'currency_format' => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
                'rounding_precision' => wc_get_rounding_precision(),
                'max_installments' => Init::get_setting('max_qtd_installments'),
                'max_installments_no_fee' => Init::get_setting('max_qtd_installments_without_fee'),
                'min_installment' => Init::get_setting('min_value_installments'),
                'fee' => $interest,
                'fees' => $installments_fee,
                'without_fee_label' => Init::get_setting('text_without_fee_installments'),
                'with_fee_label' => Init::get_setting('text_with_fee_installments'),
                'ajax_url' => admin_url('admin-ajax.php'),
            )));
        }
    }
}

new Assets();