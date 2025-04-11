<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Include shortcodes for custom design
 * 
 * @since 4.5.0
 * @version 5.4.0
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
     * @version 5.2.2
     * @return object
     */
    public function render_full_installment_shortcode() {
        // compatibility with Elementor editing mode
        $product_id = Helpers::get_product_id_from_post();
        $product = wc_get_product( $product_id );

        if ( $product === false ) {
            global $product;
        }

        // check if product is valid
        if ( ! $product || ! $product instanceof WC_Product || $product === null ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        $product_id = $product->get_id();

        ob_start();

        if ( License::is_valid() ) {
            $this->full_installment( $product_id );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
        
        return ob_get_clean();
    }


    /**
     * Create a shortcode best installments
     * 
     * @since 2.0.0
     * @version 5.0.0
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
     * @version 5.2.6
     * @return string
     */
    public function woo_custom_installments_group_shortcode() {
        global $product;

        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        $price = $product->get_price();

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            $price = apply_filters( 'woo_custom_installments_adjusted_price', $price, $product );

            if ( strpos( $price, 'woo-custom-installments-group' ) !== false ) {
                return $price;
            }

            $html = '<div class="woo-custom-installments-group';

            if ( $product && $product->is_type('variable') && ! Helpers::variations_has_same_price( $product ) ) {
                $html .= ' variable-range-price';
            }

            $html .= '">';

            // Original price
            $html .= '<span class="woo-custom-installments-price original-price">' . wc_price( $price ) . '</span>';

            $html .= $this->discount_main_price_single( $product );
            $html .= $this->discount_ticket_badge( $product );
            $html .= $this->display_best_installments( $product );
            $html .= $this->economy_pix_badge( $product );

            $html .= '</div>';

            return $html;
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }


    /**
     * Create a shortcode installments table
     * 
     * @since 2.0.0
     * @version 5.0.0
     * @return string
     */
    public function installments_table_shortcode() {
        // compatibility with Elementor editing mode
        $product = wc_get_product( Helpers::get_product_id_from_post() );

        if ( $product === false ) {
            global $product;
        }

        // check if local is product page for install shortcode
        if ( ! $product ) {
            return __( 'O local do shortcode inserido é inválido. Insira em um modelo de página de produto individual, ou em um local onde consiga obter o ID de um produto.', 'woo-custom-installments' );
        }

        if ( License::is_valid() ) {
            echo $this->generate_installments_table( $product );
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
     * @version 5.0.0
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
            return $this->discount_ticket_badge( $price, $product );
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
            return wc_price( self::calculate_pix_economy( $product ) );
        } else {
            return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
        }
    }
}

new Shortcodes();