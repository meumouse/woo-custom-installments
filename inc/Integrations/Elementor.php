<?php

namespace MeuMouse\Woo_Custom_Installments\Integrations;

use Elementor\Plugin as Elementor_Plugin;
use ElementorPro\Modules\Woocommerce\Documents\Product as Product_Document;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;
use MeuMouse\Woo_Custom_Installments\Core\Assets;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Add Woo Custom Installments widgets on Elementor
 *
 * @since 5.0.0
 * @version 5.4.1
 * @package MeuMouse.com
 */
class Elementor {

    public $version = WOO_CUSTOM_INSTALLMENTS_VERSION;
    public $assets_url = WOO_CUSTOM_INSTALLMENTS_ASSETS;

    /**
     * Construct function
     * 
     * @since 5.0.0
     * @version 5.4.0
     * @return void
     */
    public function __construct() {
        if ( Admin_Options::get_setting('enable_elementor_widgets') === 'yes' && defined('ELEMENTOR_VERSION') ) {
            // register widgets categories
            add_action( 'elementor/elements/categories_registered', array( $this, 'add_custom_widget_categories' ), 10, 3 );

            // register widgets
            add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 10, 1 );

            // enqueue assets on Elementor editor
            add_action( 'elementor/preview/enqueue_scripts', array( $this, 'preview_assets' ) );

            // set product id on Elementor editor
            add_filter( 'Woo_Custom_Installments/Assets/Set_Product_Id', array( $this, 'set_product_preview' ), 10, 1 );
        }
    }


    /**
     * Add custom widget categories
     *
     * @since 5.0.0
     * @version 5.4.0
     * @param object $elements_manager | Elementor elements manager
     * @return void
     */
    public function add_custom_widget_categories( $elements_manager ) {
        $elements_manager->add_category( 'woo-custom-installments',
            array(
                'title' => esc_html__('Parcelas Customizadas', 'woo-custom-installments'),
                'icon' => 'fa fa-plug',
            ),
        );
    }


    /**
     * Register Custom Installments Widget
     *
     * @since 5.0.0
     * @version 5.4.0
     * @param object $widgets_manager | Elementor widgets manager
     * @return void
     */
    public function register_widgets( $widgets_manager ) {
        /**
         * Filter for register Elementor widgets
         * 
         * @since 5.0.0
         * @version 5.4.0
         * @param array $widgets | Array with widgets to register
         */
        $widgets = apply_filters( 'Woo_Custom_Installments/Elementor/Register_Widgets', array(
            'Inject_Controllers.php',
            'Single_Product_Price.php',
            'Popup_Payment_Methods.php',
            'Accordion_Payment_Methods.php',
            'Credit_Card_Badges.php',
            'Debit_Card_Badges.php',
            'Installments_Table.php',
            'Price_Info_Box.php',
            'Discount_Per_Quantity.php',
            'Discount_Badge.php',
        ));

        foreach ( $widgets as $file ) {
            $file_path = WOO_CUSTOM_INSTALLMENTS_INC .'Integrations/Elementor/Widgets/'. $file;

            if ( file_exists( $file_path ) ) {
                include_once $file_path;
            }
        }
    }


    /**
     * Enqueue assets on Elementor preview editor
     * 
     * @since 5.0.0
     * @version 5.4.0
     * @return void
     */
    public function preview_assets() {
        /**
         * Filter to add cache option for front scripts
         * 
         * @since 5.4.0
         */
        $cache = apply_filters( 'Woo_Custom_Installments/Assets/Front_Scripts/Cache', true );

        // If cache is enabled, set version to current timestamp
        $set_version = $cache === true ? time() : $this->version;

        // Enqueue accounting library for price formatting
        wp_enqueue_script( 'accounting-lib', $this->assets_url . 'vendor/accounting/accounting.min.js', array(), '0.4.2', true );

        // set dependencies
        $deps = array( 'jquery', 'accounting-lib' );

        wp_register_script( 'woo-custom-installments-front-scripts-preview', $this->assets_url . 'frontend/js/woo-custom-installments-front-scripts.js', $deps, $set_version, true );
        wp_enqueue_script('woo-custom-installments-front-scripts-preview');

        // instance assets class
        $assets = new Assets();

        // send params to script
        wp_localize_script( 'woo-custom-installments-front-scripts-preview', 'wci_front_params', $assets->frontend_params() );

        wp_register_style( 'woo-custom-installments-front-styles-preview', $this->assets_url . 'frontend/css/woo-custom-installments-front-styles.css', array(), $set_version );
        wp_enqueue_style('woo-custom-installments-front-styles-preview');
    }


    /**
     * Check if the Elementor editor is currently editing a single product page.
     * 
     * @since 5.0.0
     * @version 5.4.0
     * @return bool True if editing a single product page in Elementor; false otherwise.
     */
    public static function editing_single_product_page() {
        $is_editing = false;

        // Check if Elementor is in edit mode
        if ( self::is_edit_mode() ) {
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
         * @version 5.4.0
         * @param bool $is_editing | Whether Elementor is editing a single product page
         */
        return apply_filters( 'Woo_Custom_Installments/Elementor/Editing_Single_Product', $is_editing );
    }


    /**
     * Check if is editing mode on Elementor
     * 
     * @since 5.0.0
     * @version 5.4.1
     * @return bool
     */
    public static function is_edit_mode() {
        if ( defined('ELEMENTOR_VERSION') && Elementor_Plugin::$instance->editor->is_edit_mode() ) {
            return true;
        }

        return false;
    }


    /**
     * Get product id from preview settings on Elementor editor single product
     * 
     * @since 5.4.0
     * @version 5.4.1
     * @param int $product_id | Current product id
     * @return int
     */
    public function set_product_preview( $product_id ) {
        // Check if we are in Elementor edit mode
        if ( defined('ELEMENTOR_VERSION') && ! Elementor_Plugin::instance()->preview->is_preview_mode() ) {
            return $product_id;
        }

        // get the current template ID
        $template_id = get_the_ID();

        if ( ! $template_id ) {
            return $product_id;
        }

        // get the document object from template
        $document = Elementor_Plugin::instance()->documents->get( $template_id );

        if ( ! $document ) {
            return $product_id;
        }

        // check if the document is a product document
        if ( ! ( $document instanceof Product_Document ) ) {
            return $product_id;
        }

        // get the preview product ID
        $preview_id = (int) $document->get_settings('preview_id');

        if ( $preview_id ) {
            return $preview_id;
        }

        return $product_id;
    }
}