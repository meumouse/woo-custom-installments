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
 * @version 5.2.7
 * @package MeuMouse.com
 */
class Assets {

    /**
     * Construct function
     * 
     * @since 4.0.0
     * @version 5.0.0
     * @return void
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
    }


    /**
     * Enqueue admin scripts in page settings only
     * 
     * @since 2.0.0
     * @version 5.2.5
     * @return void
     */
    public function admin_assets() {
        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        if ( strpos( $url, 'admin.php?page=woo-custom-installments' ) !== false ) {
            wp_enqueue_media();

            wp_enqueue_script( 'woo-custom-installments-visibility-controller', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'components/visibility-controller/visibility-controller.min.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );

            // MiniColors
            wp_enqueue_script( 'woo-custom-installments-minicolors-scripts', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'vendor/minicolors/jquery.minicolors.min.js', array('jquery'), '2.3.6' );
            wp_enqueue_style( 'woo-custom-installments-minicolors-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'vendor/minicolors/jquery.minicolors.css', array(), '2.3.6' );

            wp_enqueue_script( 'woo-custom-installments-admin-scripts', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'admin/js/woo-custom-installments-admin-scripts.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
            wp_enqueue_style( 'woo-custom-installments-admin-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'admin/css/woo-custom-installments-admin-styles.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
            
            if ( ! class_exists('Flexify_Dashboard') ) {
                wp_enqueue_style( 'bootstrap-grid', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'vendor/bootstrap/bootstrap-grid.min.css', array(), '5.3.3' );
                wp_enqueue_style( 'bootstrap-utilities', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'vendor/bootstrap/bootstrap-utilities.min.css', array(), '5.3.3' );
            }

            if ( Init::get_setting('icon_format_elements') === 'class' ) {
                wp_enqueue_script( 'font-awesome-lib', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'vendor/font-awesome/font-awesome.min.js', array(), '6.4.0' );
            }

            wp_localize_script( 'woo-custom-installments-admin-scripts', 'wci_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wci_save_options_nonce'),
                'confirm_deactivate_license' => esc_html__( 'Tem certeza que deseja desativar sua licença?', 'woo-custom-installments' ),
                'currency_symbol' => get_woocommerce_currency_symbol(),
                'offline_toast_header' => esc_html__( 'Ops! Não há conexão com a internet', 'woo-custom-installments' ),
                'offline_toast_body' => esc_html__( 'As alterações não serão salvas.', 'woo-custom-installments' ),
                'check_format_icons' => Init::get_setting('icon_format_elements'),
                'set_media_title' => esc_html__( 'Escolher imagem de ícone', 'woo-custom-installments' ),
                'use_this_media_title' => esc_html__( 'Usar esta imagem', 'woo-custom-installments' ),
                'debug_mode' => WOO_CUSTOM_INSTALLMENTS_DEBUG,
                'nonce' => wp_create_nonce('wci_save_options_nonce'),
            ));
        }
    }


    /**
     * Enqueue scripts and styles on frontend
     *
     * @since 1.0.0
     * @version 5.2.7
     * @return void
     */
    public function frontend_assets() {
        wp_enqueue_style( 'woo-custom-installments-front-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/css/woo-custom-installments-front-styles.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );

        if ( Init::get_setting('icon_format_elements') === 'class' ) {
            wp_enqueue_script( 'font-awesome-lib', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'vendor/font-awesome/font-awesome.min.js', array(), '6.4.0' );
        }

        $post_id = get_the_ID();

        if ( is_product() || is_singular() ) {
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

        // update checkout on change payment methods
        if ( is_checkout() && Init::get_setting('enable_all_discount_options') === 'yes' && ! class_exists('Flexify_Checkout') ) {
            wp_enqueue_script( 'woo-custom-installments-update-checkout', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/update-checkout.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        }

        $product_id = get_the_ID();
        $product = wc_get_product( $product_id );

        if ( $product && $product->is_type('variable') && ! Helpers::variations_has_same_price( $product ) ) {
            $timestamp = time();

            if ( Init::get_setting('remove_price_range') === 'yes' && License::is_valid() ) {
                wp_enqueue_script( 'woo-custom-installments-range-price', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-range-price.js', array('jquery'), $timestamp, true );

                wp_localize_script('woo-custom-installments-range-price', 'wci_range_params', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'element_triggers' => Init::get_setting('update_range_price_triggers'),
                    'update_method' => Init::get_setting('price_range_method'),
                    'debug_mode' => WOO_CUSTOM_INSTALLMENTS_DEBUG,
                ));
            }

            wp_enqueue_script( 'accounting-lib', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'vendor/accounting/accounting.min.js', array(), '0.4.2' );
            wp_enqueue_script( 'woo-custom-installments-update-table-installments', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-update-table-installments.js', array('jquery'), $timestamp, true );

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
                'fee' => Helpers::get_fee(),
                'fees' => $installments_fee,
                'without_fee_label' => Init::get_setting('text_without_fee_installments'),
                'with_fee_label' => Init::get_setting('text_with_fee_installments'),
                'ajax_url' => admin_url('admin-ajax.php'),
            )));
        }
    }
}

new Assets();