<?php

namespace MeuMouse\Woo_Custom_Installments;



// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Add Woo Custom Installments widgets on Elementor
 *
 * @since 5.0.0
 * @version 5.2.0
 * @package MeuMouse.com
 */
class Elementor_Widgets {

    /**
     * Construct function
     * 
     * @since 5.0.0
     * @version 5.2.0
     * @return void
     */
    public function __construct() {
        if ( Admin_Options::get_setting('enable_elementor_widgets') === 'yes' ) {
            add_action( 'elementor/elements/categories_registered', array( $this, 'add_custom_widget_categories' ), 3 );
            add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 10, 1 );
            add_action( 'elementor/preview/enqueue_scripts', array( $this, 'wci_preview_assets' ) );
        }
    }


    /**
     * Add custom widget categories
     *
     * @since 5.0.0
     * @param \Elementor\Elements_Manager $elements_manager | Elementor elements manager
     * @return void
     */
    public function add_custom_widget_categories( $elements_manager ) {
        $elements_manager->add_category(
            'woo-custom-installments',
            [
                'title' => esc_html__('Parcelas Customizadas', 'woo-custom-installments'),
                'icon' => 'fa fa-plug',
            ]
        );
    }


    /**
     * Register Custom Installments Widget
     *
     * @since 5.0.0
     * @version 5.2.0
     * @param \Elementor\Widgets_Manager $widgets_manager | Elementor widgets manager
     * @return void
     */
    public function register_widgets( $widgets_manager ) {
        $widgets = apply_filters( 'woo_custom_installments_elementor_widgets', array(
            'widgets/class-inject-controllers.php',
            'widgets/class-single-product-price.php',
            'widgets/class-popup-payment-methods.php',
            'widgets/class-accordion-payment-methods.php',
            'widgets/class-credit-card-badges.php',
            'widgets/class-debit-card-badges.php',
            'widgets/class-installments-table.php',
            'widgets/class-price-info-box.php',
            'widgets/class-discount-per-quantity.php',
        ));

        foreach ( $widgets as $file ) {
            $file_path = WOO_CUSTOM_INSTALLMENTS_INC . $file;

            if ( file_exists( $file_path ) ) {
                include_once $file_path;
            }
        }
    }


    /**
     * Enqueue assets on Elementor preview editor
     * 
     * @since 5.0.0
     * @return void
     */
    public function wci_preview_assets() {
        wp_register_style( 'woo-custom-installments-front-modal-styles-preview', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/css/modal.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
        wp_register_script( 'woo-custom-installments-front-modal-preview', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/modal.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        wp_enqueue_style('woo-custom-installments-front-modal-styles-preview');
        wp_enqueue_script('woo-custom-installments-front-modal-preview');

        wp_register_style( 'woo-custom-installments-front-accordion-styles-preview', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/css/accordion.css', array(), WOO_CUSTOM_INSTALLMENTS_VERSION );
        wp_register_script( 'woo-custom-installments-front-accordion-preview', WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/js/accordion.js', array('jquery'), WOO_CUSTOM_INSTALLMENTS_VERSION );
        wp_enqueue_style('woo-custom-installments-front-accordion-styles-preview');
        wp_enqueue_script('woo-custom-installments-front-accordion-preview');
    }
}

new Elementor_Widgets();