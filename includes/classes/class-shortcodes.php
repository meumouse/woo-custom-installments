<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Frontend;
use MeuMouse\Woo_Custom_Installments\Calculate_Values;
use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Include shortcodes for custom design
 * 
 * @since 4.5.0
 * @package MeuMouse.com
 */
class Shortcodes extends Frontend {

    /**
     * Construct function
     * 
     * @since 4.5.0
     * @package MeuMouse.com
     */
    public function __construct() {
        parent::__construct();

        add_shortcode( 'woo_custom_installments_modal', array( $this, 'render_full_installment_shortcode' ) );
        add_shortcode( 'woo_custom_installments_card_info', array( $this, 'best_installments_shortcode' ) );
        add_shortcode( 'woo_custom_installments_group', array( $this, 'woo_custom_installments_group_shortcode' ) );
        add_shortcode( 'woo_custom_installments_table_installments', array( $this, 'installments_table_shortcode' ) );  
        add_shortcode( 'woo_custom_installments_pix_container', array( $this, 'pix_flag_shortcode' ) );
        add_shortcode( 'woo_custom_installments_ticket_container', array( $this, 'ticket_flag_shortcode' ) );
        add_shortcode( 'woo_custom_installments_credit_card_container', array( $this, 'credit_card_flag_shortcode' ) );
        add_shortcode( 'woo_custom_installments_debit_card_container', array( $this, 'debit_card_flag_shortcode' ) );
        add_shortcode( 'woo_custom_installments_ticket_discount_badge', array( $this, 'discount_ticket_badge_shortcode' ) );
        add_shortcode( 'woo_custom_installments_pix_info', array( $this, 'discount_pix_info_shortcode' ) );
        add_shortcode( 'woo_custom_installments_economy_pix_badge', array( $this, 'economy_pix_badge_shortcode' ) );
        add_shortcode( 'woo_custom_installments_get_price_on_pix', array( $this, 'get_price_on_pix_shortcode' ) );
        add_shortcode( 'woo_custom_installments_get_price_on_ticket', array( $this, 'get_price_on_ticket_shortcode' ) );
        add_shortcode( 'woo_custom_installments_get_economy_pix_price', array( $this, 'get_economy_pix_price_shortcode' ) );
    }


    /**
     * Create a shortcode for modal container
     * 
     * @since 2.0.0
     * @version 4.5.0
     * @param array $atts | Shortcode attributes
     * @return object
     */
    public function render_full_installment_shortcode( $atts = array() ) {
        $product = wc_get_product();

        // check if product is valid
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        $atts = shortcode_atts( array(
            'product_id' => $product->get_id(),
        ), $atts, 'woo_custom_installments_table' );

        ob_start();

        if ( License::is_valid() ) {
            $this->full_installment( $atts['product_id'] );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
        
        return ob_get_clean();
    }


    /**
     * Create a shortcode best installments
     * 
     * @since 2.0.0
     * @version 4.5.0
     * @return string
     */
    public function best_installments_shortcode() {
        global $product;

        // check if local is product page for install shortcode
        if ( ! $product ) {
        return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->display_best_installments( $product, $price );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode for discount main price
     * 
     * @since 2.0.0
     * @version 4.5.0
     * @param $product | Product object
     * @param $price | Product price
     * @return string
     */
    public function woo_custom_installments_group_shortcode( $product, $price ) {
        global $product;

        // check if local is product page for install shortcode
        if ( ! $product ) {
        return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->woo_custom_installments_group( $price, $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode installments table
     * 
     * @since 2.0.0
     * @version 4.5.0
     * @return string
     */
    public function installments_table_shortcode( $atts ) {
        $product = wc_get_product();

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. Insira em um modelo de página de produto individual, ou em um local onde consiga obter o ID de um produto.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->generate_installments_table( null, $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create shortcode for pix flag
     * 
     * @since 2.8.0
     * @version 4.5.0
     * @return string
     */
    public function pix_flag_shortcode() {
        if ( License::is_valid() ) {
            return $this->woo_custom_installments_pix_flag();
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
        if ( License::is_valid() ) {
            return $this->woo_custom_installments_ticket_flag();
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
            return $this->woo_custom_installments_credit_card_flags();
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
            return $this->woo_custom_installments_debit_card_flags();
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode for discount ticket badge
     * 
     * @since 2.8.0
     * @version 4.5.0
     * @param $product | Product object
     * @param $price | Product price
     * @return string
     */
    public function discount_ticket_badge_shortcode( $product, $price  ) {
        global $product;

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->discount_ticket_badge( $price, $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode for discount on Pix single
     * 
     * @since 3.6.0
     * @version 4.5.0
     * @param $product | Product object
     * @param $price | Product price
     * @return string
     */
    public function discount_pix_info_shortcode( $product, $price ) {
        global $product;

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->discount_main_price_single( $price, $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create shortcode for economy Pix badge
     * 
     * @since 3.6.0
     * @version 4.5.0
     * @param $product | Product object
     * @param $price | Product price
     * @return string
     */
    public function economy_pix_badge_shortcode( $product, $price  ) {
        global $product;

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return $this->economy_pix_badge( $price, $product );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Display price on Pix
     * 
     * @since 4.5.0
     * @return string
     */
    public function get_price_on_pix_shortcode() {
        global $product;

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
     * @return string
     */
    public function get_price_on_ticket_shortcode() {
        global $product;

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
     * @return string
     */
    public function get_economy_pix_price_shortcode() {
        global $product;

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            return wc_price( self::calculate_pix_economy( $product ) );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }
}

new Shortcodes();