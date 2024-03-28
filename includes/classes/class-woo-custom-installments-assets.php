<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Load plugin assets and dependencies
 * 
 * @since 4.0.0
 * @package MeuMouse.com
 */
class Woo_Custom_Installments_Assets {

    /**
     * Construct function
     * 
     * @since 4.0.0
     * @return void
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'woo_custom_installments_admin_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_update_table_installments' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_update_checkout' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'disable_update_installments' ) );

        if ( Woo_Custom_Installments_Init::get_setting('remove_price_range') === 'yes' && Woo_Custom_Installments_Init::license_valid() && !is_admin() ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'update_price_on_select_variation' ), 10 );
        }
    }


    /**
     * Enqueue admin scripts in page settings only
     * 
     * @since 2.0.0
     * @access public
     * @return void
     */
    public function woo_custom_installments_admin_scripts() {
        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        if ( false !== strpos( $url, 'admin.php?page=woo-custom-installments' ) ) {
            wp_enqueue_script( 'sortable-js', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'admin/js/sortable.min.js', array(), '1.15.1' );
            wp_enqueue_script( 'jquery-sortable', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'admin/js/jquery-sortable-js.js', array('jquery'), null );
            wp_enqueue_script( 'woo-custom-installments-admin-scripts', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'admin/js/woo-custom-installments-admin-scripts.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );

            // add currency symbol to JS var
            $currency_symbol = get_woocommerce_currency_symbol();
            $script = "var currency_symbol = '" . esc_js( $currency_symbol ) . "';";
            wp_add_inline_script('woo-custom-installments-admin-scripts', $script);

            // add ajax_url parameter for AJAX callback
            wp_localize_script( 'woo-custom-installments-admin-scripts', 'wci_params', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'api_endpoint' => 'https://api.meumouse.com/wp-json/license/',
                'api_key' => 'AD320786-A840D179-6789E14F-D844351E',
                'license' => get_option('woo_custom_installments_license_key'),
                'domain' => Woo_Custom_Installments_Api::get_domain(),
            ));

            wp_enqueue_style( 'woo-custom-installments-admin-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'admin/css/woo-custom-installments-admin-styles.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
        }
    }


    /**
     * Enqueue scripts and styles
     *
     * @since 1.0.0
     * @version 3.8.0
     * @return void
     */
    public function enqueue_scripts() {
        if ( is_singular() ) {
            global $post;

            $params = array(
                'enable_discount_per_unit' => get_post_meta( $post->ID, 'enable_discount_per_unit', true ),
                'discount_per_unit_method' => get_post_meta( $post->ID, 'discount_per_unit_method', true ),
                'unit_discount_amount' => get_post_meta( $post->ID, 'unit_discount_amount', true ),
                'currency_symbol' => get_woocommerce_currency_symbol(),
            );

            wp_localize_script( 'woo-custom-installments-front-scripts', 'wci_front_params', $params );
        }

        wp_enqueue_script( 'woo-custom-installments-front-scripts', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-front-scripts.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        wp_enqueue_style( 'woo-custom-installments-front-styles', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/css/woo-custom-installments-front-styles.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
        wp_enqueue_script( 'font-awesome-lib', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/font-awesome.min.js', array(), '6.4.0' );

        if ( Woo_Custom_Installments_Init::get_setting('display_installment_type') == 'popup' ) {
            wp_enqueue_script( 'woo-custom-installments-front-modal', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/modal.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        } elseif ( Woo_Custom_Installments_Init::get_setting('display_installment_type') == 'accordion' ) {
            wp_enqueue_script( 'woo-custom-installments-front-accordion', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/accordion.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        }
    }


    /**
     * Update table installments
     * 
     * @since 2.9.0
     * @version 3.8.0
     * @return void
     */
    public function enqueue_update_table_installments() {
        // check if is product page
        if ( is_product() ) {
            $product_id = get_the_ID();
            $product = wc_get_product( $product_id );

            // check if product is variable
            if ( $product && $product->is_type('variable') && Woo_Custom_Installments_Init::get_setting( 'display_installment_type' ) != 'hide' ) {
                wp_enqueue_script( 'woo-custom-installments-update-table-installments', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-update-table-installments.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
                wp_enqueue_script( 'accounting-lib', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/accounting.min.js', array(), '0.4.2' );

                $interest = Woo_Custom_Installments_Init::get_fee();
                $installments_fee = array();

                foreach ( range( 1, Woo_Custom_Installments_Init::get_setting( 'max_qtd_installments' ) ) as $i ) {
                    $installments_fee[$i] = Woo_Custom_Installments_Init::get_fee( false, $i );
                }

                wp_localize_script( 'woo-custom-installments-update-table-installments', 'Woo_Custom_Installments_Params', apply_filters( 'woo_custom_installments_dynamic_table_params', array(
                    'currency_format_num_decimals' => wc_get_price_decimals(),
                    'currency_format_symbol' => get_woocommerce_currency_symbol(),
                    'currency_format_decimal_sep' => esc_attr( wc_get_price_decimal_separator() ),
                    'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
                    'currency_format' => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
                    'rounding_precision' => wc_get_rounding_precision(),
                    'max_installments' => Woo_Custom_Installments_Init::get_setting( 'max_qtd_installments' ),
                    'max_installments_no_fee' => Woo_Custom_Installments_Init::get_setting( 'max_qtd_installments_without_fee' ),
                    'min_installment' => Woo_Custom_Installments_Init::get_setting( 'min_value_installments' ),
                    'fees' => $installments_fee,
                    'fee' => $interest,
                    'without_fee_label' => Woo_Custom_Installments_Init::get_setting( 'text_without_fee_installments' ),
                    'with_fee_label' => Woo_Custom_Installments_Init::get_setting( 'text_with_fee_installments' ),
                ) ) );
            }
        }
    }


    /**
     * Enqueue update checkout script
     * 
     * @since 3.6.0
     * @version 3.8.0
     * @return void
     */
    public function enqueue_update_checkout() {
        if ( is_checkout() && Woo_Custom_Installments_Init::get_setting('enable_all_discount_options') === 'yes' ) {
            wp_enqueue_script( 'woo-custom-installments-update-table-installments', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/update-checkout.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        }
    }


    /**
     * Disable update table installments
     * 
     * @since 2.9.0
     * @version 3.8.0
     * @return void
     */
    public function disable_update_installments() {
        if ( Woo_Custom_Installments_Init::get_setting('disable_update_installments') === 'yes' ) {
            wp_dequeue_script('woo-custom-installments-update-table-installments');
        }
    }


    /**
     * Enqueue script for update price on select variation
     * 
     * @since 2.9.0
     * @version 3.2.5
     * @return void
     */
    public function update_price_on_select_variation() {
        $product_id = get_the_ID();
        $product = wc_get_product( $product_id );

        if ( $product && is_a( $product, 'WC_Product' ) ) {
            if ( $product->is_type( 'variable' ) && $product->get_variation_price( 'min' ) !== $product->get_variation_price( 'max' ) ) {
                wp_enqueue_script( 'woo-custom-installments-range-price', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/woo-custom-installments-range-price.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
            }
        }
    }
}

new Woo_Custom_Installments_Assets();