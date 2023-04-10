<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }


/**
 * Display structures on front-end
 *
 * @package  MeuMouse.com
 * @since  1.0.0
 */

class Woo_Custom_Installments_Frontend_Template extends Woo_Custom_Installments_Init {

  public static $count = 0;

  public function __construct() {
    parent::__construct();

    $options = get_option('woo-custom-installments-setting');

    $this->init();
    $this->change_schema();

    // get boolean condition to display installments in the cart
    if ( isset( $options['display_installments_cart'] ) == 'yes' ) {
      add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_discount_on_cart' ) );
    }

    // get boolean condition to display installments in all the products
    if ( isset( $options['enable_installments_all_products'] ) == 'yes' ) {
      add_filter( 'woocommerce_get_price_html', array( $this, 'discount_main_price' ), 999, 2 );
    }

    // get hook to display accordion or popup payment form in single product page
    if( $this->getSetting( 'hook_payment_form_single_product' ) == 'before_cart' ) {
      add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'full_installment' ), 10 );
    } elseif( $this->getSetting( 'hook_payment_form_single_product' ) == 'after_cart' ) {
      add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'full_installment' ), 10 );
    } else {
      remove_action( 'woocommerce_after_add_to_cart_form', array( $this, 'full_installment' ), 10 );
      remove_action( 'woocommerce_before_add_to_cart_form', array( $this, 'full_installment' ), 10 );
    }

    /**
     * Shortcodes for custom design
     * 
     * @since 2.0.0
     */
    if( get_option( 'license_status') == 'valid' ) {
      add_shortcode( 'woo_custom_installments_modal', array( $this, 'full_installment' ) );
      add_shortcode( 'woo_custom_installments_card_info', array( $this, 'single_product_price' ) );
      add_shortcode( 'woo_custom_installments_pix_container', array( $this, 'woo_custom_installments_pix_flag' ) );
      add_shortcode( 'woo_custom_installments_ticket_container', array( $this, 'woo_custom_installments_ticket_flag' ) );
      add_shortcode( 'woo_custom_installments_credit_card_container', array( $this, 'woo_custom_installments_credit_card_flags' ) );
      add_shortcode( 'woo_custom_installments_debit_card_container', array( $this, 'woo_custom_installments_debit_card_flags' ) );
    }

  }


  private function init() {
  //  add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'clear_product_function' ), 9999 );
    add_action( 'wp_head', array( $this, 'woo_custom_installments_custom_design' ), 9999 );
    
  }


  /**
   * Calculate installments
   * 
   * @return string
   *
  */
  public function set_values( $return, $price = false, $product = false, $echo = true ) {
    $installments_info = array();

    if ( !$price ) {
      global $product;

      if ( !$product ) {
        return $return;
      }

      $args = array();

      if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
        $args['price'] = $product->get_variation_price( 'min' );
      }

      $price = wc_get_price_to_display( $product, $args );
    }

    $price = apply_filters( 'woo_custom_installments_set_values_price', $price, $product );

    if ( !$this->is_available( $product ) ) {
      return false;
    }

    $installments_limit = $this->get_installments_limit( $product );

    // get all installments options till the limit
    for ( $i = 1; $i <= $installments_limit; $i++ ) {

      $fee = $this->get_fee( $product, $i );

      // If interest be zero, use one formule for all
      if ( 0 == $fee ) {
        $installments_info[] = $this->get_installment_details_without_interest( $price, $i );
        continue;
      }

      $max_installment_interest_free = $this->get_max_installment_no_fee( $product );

      // set the installments with no fee
      if ( $i <= $max_installment_interest_free ) {
        // return values for this installment
        $installments_info[] = $this->get_installment_details_without_interest( $price, $i );
      } else {
        $installments_info[] = $this->get_installment_details_with_interest( $price, $fee, $i );
      }

    }

    $min_installment = $this->get_min_installment( $product );

    foreach ( $installments_info as $key => $installment ) {
      if ( $installment['installment_price'] < $min_installment && 0 < $key ) {
        unset( $installments_info[ $key ] );
      }
    }

    return $this->formatting_display( $installments_info, $return, $echo );
  }


  /**
   * Include schema for product searchers
   * 
   * @since 1.0.0
   * @return bool
   */
  private function change_schema() {
    if( isset( $options['display_discount_price_schema'] ) == 'yes' && ! is_admin() ) {
        require_once WOO_CUSTOM_INSTALLMENTS_DIR . '/includes/classes/class-woo-custom-installments-schema.php';
    }
  }


  /**
   * Check if product is available
   * 
   * @return bool
   * @since 1.0.0
   */
  public function is_available( $product = false ) {
    $is_available = true;
    $price = wc_get_price_to_display( $product );

    if ( is_admin() && ! is_ajax() || $product && empty( $price ) || $product && 0 === $price ) {
      $is_available = false;
    }

    return apply_filters( 'woo_custom_installments_is_available', $is_available, $product );
  }


  /**
   * Display installments on loop page
   * 
   * @return string
   * @since 1.0.0
  */
  public function loop_price() {
    echo '<span class="woo-custom-installments-loop">';
      $this->set_values( $this->woo_custom_installments_display_shop_page );
    echo '</span>';
  }


  /**
   * Define WooCommerce Hooks
   * 
   * @return string
   * @since 1.0.0
   */
  public static function hook() {
    if ( self::is_main_product_price() ) {
      $action = 'main_price';
    } else {
      $action = 'loop';
    }

    return $action;
  }

  public static function is_main_product_price() {
    if ( is_product() ) {
      return ( 0 == self::$count );
    }
    return false;
  }

/*
  public static function clear_product_function() {
    self::$count++;
  }*/


  /**
   * Display installments in single product page
   * 
   * @return bool
  */
  public function single_product_price( $product, $original_price = false ) {
    if ( null !== ( $pre_value = apply_filters( 'woo_custom_installments_pre_installments_price', null, $product, $original_price ) ) ) {
      return $pre_value;
    }

    if ( self::is_main_product_price() ) {
      $is_product = true;
    } else {
      $is_product = false;
    }

    $html = '';
    $display_single_product = $this->get_single_page_view( $product );
    $woo_custom_installments_display_shop_page = $this->get_shop_page_view( $product );
    $get_icon_best_installments = $this->getSetting( 'icon_best_installments' );

    if ( $original_price && apply_filters( 'woo_custom_installments_original_with_credit_card', false ) ) {
      $html .= apply_filters( 'woo_custom_installments_show_original_price_credit_card', $original_price, $product );
    }


    $args = array();

    if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
      $args['price'] = $product->get_variation_price( 'min' );
    }

    $display = ( $is_product ) ? $display_single_product : $woo_custom_installments_display_shop_page;
    $price = $this->set_values( $display, wc_get_price_to_display( $product, $args ), $product, false );

    if ( '' != $price ) {

      $html .= ' <span class="woo-custom-installments-card-container">';
        $html .= '<i id="wci-icon-best-installments" class="'. $get_icon_best_installments .'"></i>';
        $html .= $price;
      $html .= ' </span>';

    }

    return $html;
  }


  /**
   * Pix flag
   * 
   * @return string
   * @since 2.0.0
   */
  public function woo_custom_installments_pix_flag() {
    $options = get_option('woo-custom-installments-setting');
    $pixFlag = '';
    
    if( isset( $options['enable_pix_method_payment_form'] ) == 'yes' ) {
      $pixFlag .= '<div class="woo-custom-installments-pix-section">';
        $pixFlag .= '<h4 class="pix-method-title">'. $this->getSetting( 'text_pix_container' ) .'</h4>';
        $pixFlag .= '<div class="pix-method-container">';
          $pixFlag .= '<span class="pix-method-name">'. __( 'Pix', 'woo-custom-installments' ) .'</span>';
          $pixFlag .= '<span class="instant-approval-badge">'. __( 'Aprovação imediata', 'woo-custom-installments' ) .'</span>';
        $pixFlag .= '</div>';
        $pixFlag .= '<div class="container-badge-icon pix-flag">';
          $pixFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/pix-badge.svg"/>';
        $pixFlag .= '</div>';
      $pixFlag .= '</div>';
    }

    return $pixFlag;
  }


  /**
   * Ticket flag
   * 
   * @return string
   * @since 2.0.0
   */
  public function woo_custom_installments_ticket_flag() {
    $options = get_option('woo-custom-installments-setting');
    $ticketFlag = '';
    
    if( isset( $options['enable_ticket_method_payment_form'] ) == 'yes' ) {
      $ticketFlag .= '<div class="woo-custom-installments-ticket-section">';
        $ticketFlag .= '<h4 class="ticket-method-title">'. $this->getSetting( 'text_ticket_container' ) .'</h4>';
        $ticketFlag .= '<div class="ticket-method-container">';
          $ticketFlag .= '<span class="ticket-instructions">'. $this->getSetting( 'text_instructions_ticket_container' ) .'</span>';
        $ticketFlag .= '</div>';
        $ticketFlag .= '<div class="container-badge-icon ticket-flag">';
          $ticketFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/boleto-badge.svg"/>';
        $ticketFlag .= '</div>';
      $ticketFlag .= '</div>';
    }

    return $ticketFlag;
  }


  /**
   * Credit card flags
   * 
   * @return string
   * @since 2.0.0
   */
  public function woo_custom_installments_credit_card_flags() {
    $options = get_option('woo-custom-installments-setting');
    $creditCardFlag = '';

    if( isset( $options['enable_credit_card_method_payment_form'] ) == 'yes' ) {
      $creditCardFlag .= '<div class="woo-custom-installments-credit-card-section">';
        $creditCardFlag .= '<h4 class="credit-card-method-title">'. $this->getSetting( 'text_credit_card_container' ) .'</h4>';
        $creditCardFlag .= '<div class="credit-card-method-container">';
          $creditCardFlag .= '<span class="instant-approval-badge">'. __( 'Aprovação imediata', 'woo-custom-installments' ) .'</span>';
        $creditCardFlag .= '</div>';
        $creditCardFlag .= '<div class="credit-card-container-badges">';

          if( isset( $options['enable_mastercard_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card mastercard-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/mastercard-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_visa_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card visa-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/visa-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_elo_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card elo-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/elo-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_hipercard_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card hipercard-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/hipercard-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_diners_club_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card diners-club-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/diners-club-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_discover_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card discover-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/discover-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_american_express_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card american-express-flag">';
            $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/american-express-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_paypal_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card paypal-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/paypal-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_stripe_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card stripe-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/stripe-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_mercado_pago_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card mercado-pago-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/mercado-pago-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_pagseguro_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card pagseguro-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/pagseguro-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_pagarme_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card pagarme-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/pagarme-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }

          if( isset( $options['enable_cielo_flag'] ) == 'yes' ) {
            $creditCardFlag .= '<div class="container-badge-icon credit-card cielo-flag">';
              $creditCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/cielo-badge.svg"/>';
            $creditCardFlag .= '</div>';
          }
          
        $creditCardFlag .= '</div>';
      $creditCardFlag .= '</div>';
    }

    return $creditCardFlag;
  }


  /**
   * Debit card flags
   * 
   * @return string
   * @since 2.0.0
   */
  public function woo_custom_installments_debit_card_flags() {
    $options = get_option('woo-custom-installments-setting');
    $debitCardFlag = '';

    if( isset( $options['enable_debit_card_method_payment_form'] ) == 'yes' ) {
      $debitCardFlag .= '<div class="woo-custom-installments-debit-card-section">';
        $debitCardFlag .= '<h4 class="debit-card-method-title">'. $this->getSetting( 'text_debit_card_container' ) .'</h4>';
        $debitCardFlag .= '<div class="debit-card-method-container">';
          $debitCardFlag .= '<span class="instant-approval-badge">'. __( 'Aprovação imediata', 'woo-custom-installments' ) .'</span>';
        $debitCardFlag .= '</div>';
        $debitCardFlag .= '<div class="debit-card-container-badges">';

          if( isset( $options['enable_mastercard_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card mastercard-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/mastercard-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_visa_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card visa-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/visa-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_elo_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card elo-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/elo-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_hipercard_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card hipercard-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/hipercard-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_diners_club_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card diners-club-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/diners-club-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_discover_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card discover-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/discover-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_american_express_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card american-express-flag">';
            $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/american-express-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_paypal_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card paypal-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/paypal-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_stripe_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card stripe-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/stripe-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_mercado_pago_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card mercado-pago-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/mercado-pago-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_pagseguro_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card pagseguro-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/pagseguro-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_pagarme_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card pagarme-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/pagarme-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }

          if( isset( $options['enable_cielo_flag'] ) == 'yes' ) {
            $debitCardFlag .= '<div class="container-badge-icon debit-card cielo-flag">';
              $debitCardFlag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/cielo-badge.svg"/>';
            $debitCardFlag .= '</div>';
          }
          
        $debitCardFlag .= '</div>';
      $debitCardFlag .= '</div>';
    }

    return $debitCardFlag;
  }


  /**
   * Format full installments
   * 
   * @since 2.0.0
  */
  public function full_installment( $product_id = false ) {
    if( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }
    
    $options = get_option('woo-custom-installments-setting');
    $getTitleButton = $this->getSetting( 'text_button_installments' );
    $getTitleTableInstallments = $this->getSetting( 'text_table_installments' );
    $getPaymentForms = $this->getSetting( 'text_container_payment_forms' );
    $product = apply_filters( 'woo_custom_installments_full_installment_product', $product );
    $args = array();
    $accordion = '';
    $popup = '';

    // check if local is allowed for install shortcode
		if( ! is_a( $product, 'WC_Product' ) ) {
      $admin_error = __( 'O local do shortcode inserido é inválido. Insira em um modelo de página de produto individual.', 'woo-custom-installments' );
		 return current_user_can( 'manage_woocommerce' ) ? $admin_error : '';
		}

    // check if product is variable e get min price variation
    if( $product->is_type( 'variable', 'variation' ) && !$this->variable_has_same_price( $product ) ) {
      $args['price'] = $product->get_variation_price( 'min' );
    }

    $price = wc_get_price_to_display( $product, $args );
    $all_installments = $this->set_values( 'all', $price, $product, false );

    if( !$all_installments ) {
      return;
    }

    do_action( 'woo_custom_installments_before_installments_table' );

    // installments table
    $table = '<div id="installments">
    <table class="table table-hover woo-custom-installments-table">
      <tbody data-default-text="' . $this->get_table_formatted_text( $product ) . '">';
        foreach ( $all_installments as $installment ) {
          $find = array_keys( $this->strings_to_replace( $installment ) );
          $replace = array_values( $this->strings_to_replace( $installment ) );
          $final_text = str_replace( $find, $replace, $this->get_table_formatted_text( $product ) );

          $table .= '<tr class="'. $installment['class'] .'">';
          $table .= '<th class="first-text">'. $final_text .'</th>';
          $table .= '<th class="final-price">'. wc_price( $installment['final_price'] )  .'</th>';
          $table .= '</tr>';
        }
      $table .= '</tbody>';
    $table .= '</table>';
    $table .= '</div>';


    // accordion content
    $accordion .= '<div id="accordion-installments" class="accordion">';
      $accordion .= '<div class="accordion-item">';
        $accordion .= '<button class="accordion-header">'. $getTitleButton .'</button>';
        $accordion .= '<div class="accordion-content">'. $table .'</div>';
      $accordion .= '</div>';
    $accordion .= '</div>';

    // popup content
    $popup .= '<button id="open-popup">'. $getTitleButton .'</button>';
    $popup .= '<div id="popup-container">';
      $popup .= '<div id="popup-content">';
        $popup .= '<div id="popup-header">';
          $popup .= '<h5 id="popup-title">'. $getPaymentForms .'</h5>';
          $popup .= '<button id="close-popup" aria-label="Fechar"></button>';
        $popup .= '</div>';

        $popup .= $this->woo_custom_installments_pix_flag();
        $popup .= $this->woo_custom_installments_credit_card_flags();
        $popup .= $this->woo_custom_installments_debit_card_flags();
        $popup .= $this->woo_custom_installments_ticket_flag();

        $popup .= '<h4 class="installments-title">'. $getTitleTableInstallments .'</h4>';
        $popup .= $table;
      $popup .= '</div>';
    $popup .= '</div>';

    if( $this->getSetting( 'display_installment_type' ) == 'accordion' ) {
      echo apply_filters( 'woo_custom_installments_table', $accordion, $all_installments );
    } else {
      echo apply_filters( 'woo_custom_installments_table', $popup, $all_installments );
    }
    
    do_action( 'woo_custom_installments_after_installments_table' );
  }
  

  /**
   * Replament strings in front-end
   * 
   * @return array
   * @since 1.3.0
   */
  public function strings_to_replace( $values ) {
    return array(
      '{{ parcelas }}' => $values['installments_total'],
      '{{ valor }}' => wc_price( $values['installment_price'] ),
      '{{ total }}' => wc_price( $values['final_price'] ),
      '{{ juros }}' => $this->get_fee_info( $values ),
    );
  }


  /**
   * Display discount in main price and best installments
   * 
   * @return string
   * @since 2.0.0
  */
  public function discount_main_price( $price, $product ) {
    $hook = self::hook();
    $args = array();
    $main_price_discount = $this->getSetting( 'discount_main_price' );
    $displayGlobal = $this->getSetting( 'display_discount_price_hook' ) == 'display_loop_and_single_product';
    $displayOnlySingleProduct = $this->getSetting( 'display_discount_price_hook' ) == 'only_single_product';
    $displayOnlyLoopProducts = $this->getSetting( 'display_discount_price_hook' ) == 'only_loop_products';
    $disable_discount_main_price = get_post_meta( $product->get_id(), '__disable_discount_main_price', true ) == 'yes';
    
    // Show original price before discount info
    $html = '<span class="original-price">' . $price . '</span>';

    $html .= '<span class="woo-custom-installments-offer">';

    // Get icon class Font Awesome
    $html .= '<i id="wci-icon-main-price" class="'. $this->getSetting( 'icon_main_price' ) .'"></i>';

    if ( !$product->is_purchasable() || $main_price_discount <= 0 || $disable_discount_main_price || $this->variable_has_same_price( $product ) ) {
        return $price;
    }

    $args = array();
    if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
        $html .= '<span class="variation-price">' . apply_filters( 'woo_custom_installments_before_variation_text', $this->getSetting( 'text_initial_variables' ) ) . '</span>';
        $args['price'] = $product->get_variation_price( 'min' );
    }

    if ( $this->getSetting( 'product_price_discount_method' ) == 'percentage' ) {
        $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( wc_get_price_to_display( $product, $args ), $main_price_discount, $product );
    } else {
        $custom_price = wc_get_price_to_display( $product, $args ) - $main_price_discount;
    }

    $html .= '<span class="discount-before-price">' . $this->get_text_before_price( $product ) . '</span>';
    $html .= '<span class="discounted-price">' . wc_price( $custom_price ) . '</span>';
    $html .= '<span class="discount-after-price">' . $this->get_text_after_price( $product ) . '</span>';
    $html .= '</span>';

    // Display best installment with or without fee
    $html .= $this->single_product_price( $product, $price );

    // Display discount in main price in loop and single product pages
    if ( $displayGlobal || ( $displayOnlySingleProduct && is_product() ) || ( $displayOnlyLoopProducts && is_archive() ) ) {
        return $html;
    }


    return $price;
  }


  /**
   * Format display prices
   * 
   * @return string
  */
  private function formatting_display( $installments, $return, $echo = true ) {
    global $product;

    if( 0 === count( $installments ) ) {
      return;
    }

    if( $this->getSetting( 'display_installments_loop' ) == 'best_installment_without_fee' ) {
      $return = $this->best_no_fee( $installments, $product );
    } elseif( $this->getSetting( 'display_installments_loop' ) == 'best_installment_with_fee' ) {
      $return = $this->best_with_fee( $installments, $product );
    } elseif( $this->getSetting( 'display_installments_loop' ) == 'best_installment_with_and_without_fee' ) {
      $return  = $this->best_no_fee( $installments, $product );
      $return .= $this->best_with_fee( $installments, $product );
    } else {
      return;
    }

/*
    switch ( $return ) {
      case 'all':
        $return = apply_filters( 'woo_custom_installments_all_installments', $installments );
        break;
      case 'best_no_fee':
        $return = $this->best_no_fee( $installments, $product );
        break;
      case 'best_with_fee':
        $return = $this->best_with_fee( $installments, $product );
        break;
      case 'both':
        if ( $this->best_no_fee( $installments, $product ) === $this->best_with_fee( $installments, $product ) ) {
          $return  = $this->best_no_fee( $installments, $product );
        } else {
          $return  = $this->best_no_fee( $installments, $product );
          $return .= $this->best_with_fee( $installments, $product );
        }
        break;

      default:
        return;
        break;
    }*/

    if ( $echo ) {
      echo $return;
    } else {
      return $return;
    }
  }


  /**
   * Get best installment without interest
   * 
   * @return string
   * @since 1.0.0
  */
  public function best_no_fee( $installments, $product ) {

    $hook = self::hook();

    foreach ( $installments as $key => $installment ) {
      if ( 'no-fee' != $installment['class'] ) {
        unset( $installments[ $key ] );
      }
    }

    $best_no_fee = end( $installments );

    if ( false === $best_no_fee ) {
      return;
    }

    if ( 'main_price' == $hook ) {
      $text = $this->getSetting( 'text_display_installments_single_product' );
    } else {
      $text = $this->getSetting( 'text_display_installments_loop' );
    }

    $find = array_keys( $this->strings_to_replace( $best_no_fee ) );
    $replace = array_values( $this->strings_to_replace( $best_no_fee ) );
    $text = str_replace( $find, $replace, $text );

    return '<span class="woo-custom-installments-details best-value ' . $best_no_fee['class'] . '">' . apply_filters( 'woo_custom_installments_best_no_fee_' . $hook, $text, $best_no_fee, $product ) . '</span>';

  }

  /**
   * Get best installment with interest
   * 
   * @return string
   * @since 1.0.0
  */
  public function best_with_fee( $installments, $product ) {
    $hook = self::hook();
    $best_with_fee = end( $installments );

    if ( false === $best_with_fee ) {
      return;
    }

    if ( 'main_price' == $hook ) {
      $text = $this->getSetting( 'text_display_installments_single_product' );
    } else {
      $text = $this->getSetting( 'text_display_installments_loop' );
    }

    $find = array_keys( $this->strings_to_replace( $best_with_fee ) );
    $replace = array_values( $this->strings_to_replace( $best_with_fee ) );
    $text = str_replace( $find, $replace, $text );

    return '<span class="woo-custom-installments-details best-value '. $best_with_fee['class'] .'">'. apply_filters( 'woo_custom_installments_best_with_fee_'. $hook, $text, $best_with_fee, $product ) . '</span>';

  }

  /**
   * Get fee info
   * 
   * @return string
  */
  public function get_fee_info( $installment ) {
    $hook = self::hook();

    $text = ( $installment['interest_fee'] ) ? '' . $this->getSetting( 'text_with_fee_installments' ) : ' '. $this->getSetting( 'text_without_fee_installments' );
    return apply_filters( 'woo_custom_installments_fee_label', $text, $installment['interest_fee'], $hook );
  }


  /**
   * Check if variations have equal price
   * 
   * @return string
   * @since 1.0.0
  */
  private function variable_has_same_price( $product ) {
    return ( $product->is_type( 'variable', 'variation' ) && $product->get_variation_price( 'min' ) === $product->get_variation_price( 'max' ) );
  }


  /**
   * Save array with all details of installments
   * 
   * @return string
   * @since 1.0.0
  */
  public function set_installment_info( $price, $final_price, $interest_fee, $class, $i ) {
    $installment_info = array(
      'installment_price' => $price,
      'installments_total' => $i,
      'final_price' => $final_price,
      'interest_fee' => $interest_fee,
      'class' => $class
    );

    return apply_filters( 'woo_custom_installments_installment_info', $installment_info );
  }


  /**
   * Calculate value of installment without interest
   * 
   * @return string
   * @since 1.0.0
  */
  public function get_installment_details_without_interest( $total, $i ) {
    $price = Woo_Custom_Installments_Calculate_Values::calculate_installment_no_fee( $total, $i );
    $final_price = Woo_Custom_Installments_Calculate_Values::calculate_final_price( $price, $i );
    $fee = false;
    $class = 'no-fee';
    $installment_info = $this->set_installment_info( $price, $final_price, $fee, $class, $i );

    return $installment_info;
  }


  /**
   * Calculate value of installment with interest
   * 
   * @return string
  */
  public function get_installment_details_with_interest( $total, $fee, $i ) {
    $price = Woo_Custom_Installments_Calculate_Values::calculate_installment_with_fee( $total, $fee, $i );
    $final_price = Woo_Custom_Installments_Calculate_Values::calculate_final_price( $price, $i );
    $fee = true;
    $class = 'fee-included';
    $installment_info = $this->set_installment_info( $price, $final_price, $fee, $class, $i );

    return $installment_info;
  }


  /**
   * Display discount in cart page
   * 
   * @return string
   * @since 1.0.0
   */
  public function display_discount_on_cart() {
    $main_price_discount = $this->getSetting( 'discount_main_price' );

    if ( $main_price_discount > 0 ) {
      // get discount method for display $custom_price
      if( $this->getSetting( 'product_price_discount_method' ) == 'percentage' ) {
        $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( WC()->cart->get_total( 'edit' ), $main_price_discount );
      } else {
        $custom_price = WC()->cart->get_total( 'edit' ) - $main_price_discount;
      }
      ?>
      <tr class="woo-custom-installments-order-discount-cart">
        <span>
          <th class="table-header-text"><?php echo apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), $this->get_text_after_price() ) ); ?></th>
        </span>
        <span>
          <td class="discount-price" data-title="<?php echo esc_attr( apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), $this->get_text_after_price() ) ) ); ?>"><?php echo wc_price( $custom_price ); ?></td>
        </span>
      </tr>
      <?php
    }
  }


  /**
   * Custom option design
   * 
   * @return string
   * @since 2.0.0
   */
  public function woo_custom_installments_custom_design() {
    $main_price_color = $this->getSetting('discount_main_price_color');
    $main_price_bg = $this->hex2rgba( $main_price_color );
    $font_size_main_price = $this->getSetting( 'font_size_discount_price' );
    $font_size_unit_main_price = $this->getSetting( 'unit_font_size_discount_price' );
    $margin_top_main_price = $this->getSetting( 'margin_top_discount_price' );
    $unit_margin_top_main_price = $this->getSetting( 'unit_margin_top_discount_price' );
    $margin_bottom_main_price = $this->getSetting( 'margin_bottom_discount_price' );
    $unit_margin_bottom_main_price = $this->getSetting( 'unit_margin_bottom_discount_price' );
    $button_popup_color = $this->getSetting( 'button_popup_color' );
    $button_popup_size = $this->getSetting( 'button_popup_size' );
    $margin_top_popup = $this->getSetting( 'margin_top_popup_installments' );
    $unit_margin_top_popup = $this->getSetting( 'unit_margin_top_popup_installments' );
    $margin_bottom_popup = $this->getSetting( 'margin_bottom_popup_installments' );
    $unit_margin_bottom_popup = $this->getSetting( 'unit_margin_bottom_popup_installments' );
    $best_installments_color = $this->getSetting( 'best_installments_color' );
    $font_size_best_installments = $this->getSetting( 'font_size_best_installments' );
    $unit_font_size_best_installments = $this->getSetting( 'unit_font_size_best_installments' );
    $margin_top_best_installments = $this->getSetting( 'margin_top_best_installments' );
    $unit_margin_top_best_installments = $this->getSetting( 'unit_margin_top_best_installments' );
    $margin_bottom_best_installments = $this->getSetting( 'margin_bottom_best_installments' );
    $unit_margin_bottom_best_installments = $this->getSetting( 'unit_margin_bottom_best_installments' );
  
    // color main price
    $css = '.woo-custom-installments-offer {';
      $css .= 'color:'. $main_price_color .';';
      $css .= 'background-color:'. $main_price_bg .';';
      $css .= 'font-size:'. $font_size_main_price . $font_size_unit_main_price .';';
      $css .= 'margin-top:'. $margin_top_main_price . $unit_margin_top_main_price .';';
      $css .= 'margin-bottom:'. $margin_bottom_main_price . $unit_margin_bottom_main_price .';';
    $css .= '}';

    // color and border button popup
    $css .= '#open-popup {';
      $css .= 'color:'. $button_popup_color .';';
      $css .= 'border-color:'. $button_popup_color .';';
    $css .= '}';

    $css .= '#open-popup:hover {';
      $css .= 'background-color:'. $button_popup_color .';';
    $css .= '}';

    // button popup size
    if( $button_popup_size == 'small' ) {
      $css .= '#open-popup {';
        $css .= 'padding: 0.475rem 1.25rem;';
        $css .= 'font-size: 0.75rem;';
        $css .= 'border-radius: 0.25rem;';
      $css .= '}';
    } elseif( $button_popup_size == 'normal' ) {
      $css .= '#open-popup {';
        $css .= 'padding: 0.625rem 1.75rem;';
        $css .= 'font-size: 0.875rem;';
        $css .= 'border-radius: 0.375rem;';
      $css .= '}';
    } else {
      $css .= '#open-popup {';
        $css .= 'padding: 0.785rem 2rem;';
        $css .= 'font-size: 1rem;';
        $css .= 'border-radius: 0.5rem;';
      $css .= '}';
    }

    $css .= '#open-popup, #accordion-installments {';
      $css .= 'margin-top:'. $margin_top_popup . $unit_margin_top_popup .';';
      $css .= 'margin-bottom:'. $margin_bottom_popup . $unit_margin_bottom_popup .';';
    $css .= '}';

    $css .= '.woo-custom-installments-card-container {';
      $css .= 'color:'. $best_installments_color .';';
      $css .= 'font-size:'. $font_size_best_installments . $unit_font_size_best_installments .';';
      $css .= 'margin-top:'. $margin_top_best_installments . $unit_margin_top_best_installments .';';
      $css .= 'margin-bottom:'. $margin_bottom_best_installments . $unit_margin_bottom_best_installments .';';
    $css .= '}';

    ?>
    <style type="text/css">
      <?php echo $css; ?>
    </style> <?php
  }


  /**
   * Generate background color for discount main price
   * 
   * @return string
   * @since 2.0.0
   */
  public function hex2rgba( $color, $opacity = 0.1 ) {
    $hex = str_replace('#', '', $color);

    if( strlen( $hex ) === 3 ) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2);
    }

    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );
    $a = $opacity;

    return "rgba($r, $g, $b, $a)";
  }

}

new Woo_Custom_Installments_Frontend_Template();
