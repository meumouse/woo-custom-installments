<?php

namespace MeuMouse\Woo_Custom_Installments\Views;

use MeuMouse\Woo_Custom_Installments\API\License;
use MeuMouse\Woo_Custom_Installments\Views\Components;
use MeuMouse\Woo_Custom_Installments\Core\Helpers;
use MeuMouse\Woo_Custom_Installments\Core\Render_Elements;
use MeuMouse\Woo_Custom_Installments\Core\Calculate_Values;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Register shortcodes for custom design
 * 
 * @since 4.5.0
 * @version 5.4.0
 * @package MeuMouse.com
 */
class Shortcodes extends Components {

    /**
     * Construct function
     * 
     * @since 4.5.0
     * @version 5.4.0
     * @return void
     */
    public function __construct() {
        $shortcodes = array(
            'woo_custom_installments_modal' => 'render_full_installment_shortcode',
            'woo_custom_installments_card_info' => 'best_installments_shortcode',
            'woo_custom_installments_group' => 'woo_custom_installments_group_shortcode',
            'woo_custom_installments_table_installments' => 'installments_table_shortcode',
            'woo_custom_installments_pix_container' => 'pix_flag_shortcode',
            'woo_custom_installments_ticket_container' => 'ticket_flag_shortcode',
            'woo_custom_installments_credit_card_container' => 'credit_card_flag_shortcode',
            'woo_custom_installments_debit_card_container' => 'debit_card_flag_shortcode',
            'woo_custom_installments_ticket_discount_badge' => 'discount_ticket_badge_shortcode',
            'woo_custom_installments_pix_info' => 'discount_pix_info_shortcode',
            'woo_custom_installments_economy_pix_badge' => 'economy_pix_badge_shortcode',
            'woo_custom_installments_get_price_on_pix' => 'get_price_on_pix_shortcode',
            'woo_custom_installments_get_price_on_ticket' => 'get_price_on_ticket_shortcode',
            'woo_custom_installments_get_economy_pix_price' => 'get_economy_pix_price_shortcode',
            'woo_custom_installments_sale_badge' => 'sale_badge_shortcode',
        );
        
        // iterate for each shortcode
        foreach ( $shortcodes as $tag => $callback ) {
            add_shortcode( $tag, array( $this, $callback ) );
        }        
    }


    /**
     * Create a shortcode for modal container
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @return string
     */
    public function render_full_installment_shortcode() {
        // compatibility with Elementor editing mode
        $product_id = Helpers::get_product_id_from_post();
        $product = wc_get_product( $product_id );

        if ( $product === false ) {
            global $product;
        }

        // check if product is valid
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        ob_start();

        if ( License::is_valid() ) {
            Render_Elements::display_payment_methods( $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
        
        return ob_get_clean();
    }


    /**
     * Create a shortcode best installments
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @return string
     */
    public function best_installments_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( $product === false ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->display_best_installments( $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode for discount main price
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @return string
     */
    public function woo_custom_installments_group_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( ! $product ) {
            global $product;
        }

        $price = $product->get_price();

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            // instance render elements class
            $elements = new Render_Elements();

            return $elements->display_price_group( $price, $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode installments table
     * 
     * @since 2.0.0
     * @version 5.4.0
     * @return string
     */
    public function installments_table_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( ! $product ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. Insira em um modelo de página de produto individual, ou em um local onde consiga obter o ID de um produto.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            echo $this->render_installments_table( $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create shortcode for pix flag
     * 
     * @since 2.8.0
     * @version 5.4.0
     * @return string
     */
    public function pix_flag_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( ! $product ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. Insira em um modelo de página de produto individual, ou em um local onde consiga obter o ID de um produto.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->render_pix_flag( $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create shortcode for ticket flag
     * 
     * @since 2.8.0
     * @version 4.5.0
     * @return string
     */
    public function ticket_flag_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( ! $product ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. Insira em um modelo de página de produto individual, ou em um local onde consiga obter o ID de um produto.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->render_ticket_flag( $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create shortcode for credit card flags
     * 
     * @since 2.8.0
     * @version 4.5.0
     * @return string
     */
    public function credit_card_flag_shortcode() {
        if ( License::is_valid() ) {
            return $this->render_credit_card_flags();
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create shortcode for debit card flags
     * 
     * @since 2.8.0
     * @version 4.5.0
     * @return string
     */
    public function debit_card_flag_shortcode() {
        if ( License::is_valid() ) {
            return $this->render_debit_card_flags();
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode for discount ticket badge
     * 
     * @since 2.8.0
     * @version 5.4.0
     * @return string
     */
    public function discount_ticket_badge_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( $product === false ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->discount_ticket_badge( $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode for discount on Pix single
     * 
     * @since 3.6.0
     * @version 5.0.0
     * @return string
     */
    public function discount_pix_info_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( $product === false ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->discount_main_price_single( $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create shortcode for economy Pix badge
     * 
     * @since 3.6.0
     * @version 5.0.0
     * @return string
     */
    public function economy_pix_badge_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( $product === false ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->economy_pix_badge( $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Display price on Pix
     * 
     * @since 4.5.0
     * @version 5.0.0
     * @return string
     */
    public function get_price_on_pix_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( $product === false ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return wc_price( Calculate_Values::get_discounted_price( $product, 'main' ) );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Display price on ticket
     * 
     * @since 4.5.0
     * @version 5.0.0
     * @return string
     */
    public function get_price_on_ticket_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( $product === false ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return wc_price( Calculate_Values::get_discounted_price( $product, 'ticket' ) );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Display price for economy pix
     * 
     * @since 4.5.0
     * @version 5.0.0
     * @return string
     */
    public function get_economy_pix_price_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( $product === false ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return wc_price( Calculate_Values::get_pix_economy( $product ) );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode for sale badge
     * 
     * @since 5.4.0
     * @return string
     */
    public function sale_badge_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( $product === false ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product || ! $product instanceof \WC_Product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->sale_badge( $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }
}