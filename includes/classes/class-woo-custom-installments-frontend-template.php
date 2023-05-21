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

    // get boolean condition to display installments in all the products
    if ( isset( $options['enable_installments_all_products'] ) == 'yes' && ! is_admin() ) {
      add_filter( 'woocommerce_get_price_html', array( $this, 'discount_main_price' ), 999, 2 );
    }

    // get boolean condition to display installments in the cart
    if ( isset( $options['display_installments_cart'] ) == 'yes' ) {
      add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_discount_on_cart' ) );

      // Integration with EpicJungle theme
      if ( class_exists( 'EpicJungle' ) ) {
        remove_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_discount_on_cart' ) );
        add_action( 'woocommerce_cart_totals_before_shipping', array( $this, 'display_discount_on_cart' ) );
      }
    }

    // Include schema for product searchers
    if( isset( $options['display_discount_price_schema'] ) == 'yes' && ! is_admin() ) {
      require_once WOO_CUSTOM_INSTALLMENTS_DIR . '/includes/classes/class-woo-custom-installments-schema.php';
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
      add_shortcode( 'woo_custom_installments_modal', array( $this, 'render_full_installment_shortcode' ) );
      add_shortcode( 'woo_custom_installments_card_info', array( $this, 'best_installments_shortcode' ) );
      add_shortcode( 'woo_custom_installments_discount_and_card', array( $this, 'discount_main_price_shortcode' ) );
      add_shortcode( 'woo_custom_installments_table_installments', array( $this, 'installments_table_shortcode' ) );  
      add_shortcode( 'woo_custom_installments_pix_container', array( $this, 'woo_custom_installments_pix_flag' ) );
      add_shortcode( 'woo_custom_installments_ticket_container', array( $this, 'woo_custom_installments_ticket_flag' ) );
      add_shortcode( 'woo_custom_installments_credit_card_container', array( $this, 'woo_custom_installments_credit_card_flags' ) );
      add_shortcode( 'woo_custom_installments_debit_card_container', array( $this, 'woo_custom_installments_debit_card_flags' ) );
    }

    add_action( 'woocommerce_single_product_summary', array( $this, 'clear_product_function' ), 9999 );
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
   * Calculate installments
   * 
   * @return string
   * @since 1.0.0
  */
  public function set_values( $return, $price = false, $product = false, $echo = true, $dynamic = null ) {
    // check if is product
    if( !$product ) {
      return $return;
    }

    global $customFeeInstallments;
    $installments_info = array();
    $customFeeInstallments = get_option('woo_custom_installments_custom_fee_installments');
    $customFeeInstallments = maybe_unserialize( $customFeeInstallments );
    $options = get_option('woo-custom-installments-setting');

    if (!$price) {
      global $product;
      $args = array();

      if( !$product ) {
          return $return;
      }

      if( $product->is_type( 'variable', 'variation' ) && !$this->variable_has_same_price( $product ) ) {
          $args['price'] = $product->get_variation_price('max');
      }

      $price = wc_get_price_to_display($product, $args);
    }

    $price = apply_filters('woo_custom_installments_set_values_price', $price, $product);

    // check if product is different of available
    if( !$this->is_available( $product ) ) {
        return false;
    }

    // get max quantity of installments
    $installments_limit = $this->getSetting('max_qtd_installments');

    // get all installments options till the limit
    for ( $i = 1; $i <= $installments_limit; $i++ ) {
        $interest_rate = 0; // start without fee

        // check if option activated is set_fee_per_installment, else global fee is defined
        if( isset( $options['set_fee_per_installment'] ) == 'yes' ) {
            $interest_rate = isset( $customFeeInstallments[$i]['amount'] ) ? floatval( $customFeeInstallments[$i]['amount'] ) : 0;
        } else {
            $interest_rate = $this->getSetting('fee_installments_global');
        }

        // If interest be zero, use one formula for all
        if( 0 == $interest_rate ) {
            $installments_info[] = $this->get_installment_details_without_interest( $price, $i );
            continue;
        }

        // get max quantity of installments without fee
        $max_installments_without_fee = $this->getSetting('max_qtd_installments_without_fee');

        // set the installments without fee
        if( $i <= $max_installments_without_fee ) {
            // return values for this installment
            $installments_info[] = $this->get_installment_details_without_interest( $price, $i );
        } else {
            $installments_info[] = $this->get_installment_details_with_interest( $price, $interest_rate, $i );
        }
    }

    // get min value price of installment
    $min_installment_value = $this->getSetting('min_value_installments');

    foreach ($installments_info as $index => $installment) {
        if( $installment['installment_price'] < $min_installment_value && 0 < $index ) {
            unset( $installments_info [$index ] );
        }
    }

    // check if variable $return is array to merge with installments_info
    if( is_array( $return ) ) {
        $return = array_merge( $installments_info, $return );
    } else {
        $return = $installments_info;
    }

    return $this->formatting_display( $installments_info, $return, $echo );
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

  /**
   * Check price in product
   * 
   * @return
   * @since 1.0.0
   */
  public static function is_main_product_price() {
    if ( is_product() ) {
      return ( 0 == self::$count );
    }
    return false;
  }

  public static function clear_product_function() {
    self::$count++;
  }


  /**
   * Create a shortcode best installments
   * 
   * @return string
   * @since 2.0.0
   */
  public function best_installments_shortcode() {
    global $product;

    // check if local is product page for install shortcode
    if ( !$product ) {
      return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
    }

    return $this->display_best_installments( $product, $price );
  }


  /**
   * Display best installments
   * 
   * @return bool
   * @since 2.1.0
  */
  public function display_best_installments( $product, $price, $original_price = false ) {
    if ( null !== ( $pre_value = apply_filters( 'woo_custom_installments_pre_installments_price', null, $product, $price, $original_price ) ) ) {
        return $pre_value;
    }
  
    // check if product is purchasable
    if ( !$product->is_purchasable() ) {
      return;
    }
  
    // check if option __disable_installments in product is true
    $disable_installments_in_product = get_post_meta( $product->get_id(), '__disable_installments', true ) == 'yes';
  
    // check if product is variation e get the id of parent product
    if ( $product->is_type( 'variation' ) ) {
        $parent_id = $product->get_parent_id();
        $disable_installments_in_parent = get_post_meta( $parent_id, '__disable_installments', true ) == 'yes';
    } else {
        $disable_installments_in_parent = false;
    }
  
    // check if '__disable_installments' is true for the simple or variation products
    if ( $disable_installments_in_product || $disable_installments_in_parent ) {
        return;
    }
  
    $display_single_product = $this->getSetting( 'hook_display_best_installments' );
    $display_best_installments_global = $this->getSetting( 'hook_display_best_installments' ) == 'display_loop_and_single_product';
    $display_best_installments_only_single_product = $this->getSetting( 'hook_display_best_installments' ) == 'only_single_product';
    $display_best_installments_only_loop = $this->getSetting( 'hook_display_best_installments' ) == 'only_loop_products';
  
    $args = array();
  
    if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
        $args['price'] = $product->get_variation_price( 'min' );
    }
  
    $installments = $this->set_values( $display_single_product, wc_get_price_to_display( $product, $args ), $product, false );
  
    $best_installments = '';
    $get_type_best_installments = $this->getSetting( 'get_type_best_installments' );
  
    if ($get_type_best_installments == 'best_installment_without_fee') {
        $best_installments = $this->best_without_interest( $installments, $product );
    } elseif ($get_type_best_installments == 'best_installment_with_fee') {
        $best_installments = $this->best_with_interest( $installments, $product );
    } elseif ($get_type_best_installments == 'both') {
        $best_installments  = $this->best_without_interest( $installments, $product );
        $best_installments .= $this->best_with_interest( $installments, $product );
    }
  
    if ( !empty( $best_installments ) ) {
        $html = ' <span class="woo-custom-installments-card-container">';
        $html .= $best_installments;
        $html .= ' </span>';  
    } else {
        return;
    }

    // Display best installments in single product page, loop or both
    if( ( $display_best_installments_global ) || ( $display_best_installments_only_single_product && is_product() ) || ( $display_best_installments_only_loop && !is_product() && is_archive() ) ) {
        return $html;
    } else {
        return;
    }
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
          if( isset( $options['enable_instant_approval_badge'] ) == 'yes' ) {
            $pixFlag .= '<span class="instant-approval-badge">'. __( 'Aprovação imediata', 'woo-custom-installments' ) .'</span>';
          }
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
   * Generate card flags
   * 
   * @return array
   * @since 2.0.0
   */
  private function generate_card_flags($options, $card_type) {
    $flags = [
        'mastercard' => 'mastercard-badge.svg',
        'visa' => 'visa-badge.svg',
        'elo' => 'elo-badge.svg',
        'hipercard' => 'hipercard-badge.svg',
        'diners_club' => 'diners-club-badge.svg',
        'discover' => 'discover-badge.svg',
        'american_express' => 'american-express-badge.svg',
        'paypal' => 'paypal-badge.svg',
        'stripe' => 'stripe-badge.svg',
        'mercado_pago' => 'mercado-pago-badge.svg',
        'pagseguro' => 'pagseguro-badge.svg',
        'pagarme' => 'pagarme-badge.svg',
        'cielo' => 'cielo-badge.svg'
    ];

    $card_flags = '';

    foreach( $flags as $key => $value ) {
        if (isset($options['enable_' . $key . '_flag']) && $options['enable_' . $key . '_flag'] === 'yes') {
            $card_flags .= '<div class="container-badge-icon ' . $card_type . ' ' . $key . '-flag">';
            $card_flags .= '<img class="size-badge-icon" src="' . WOO_CUSTOM_INSTALLMENTS_URL . 'assets/img/' . $value . '"/>';
            $card_flags .= '</div>';
        }
    }

    return $card_flags;
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
          $creditCardFlag .= '<h4 class="credit-card-method-title">' . $this->getSetting('text_credit_card_container') . '</h4>';
          if( isset( $options['enable_instant_approval_badge'] ) == 'yes' ) {
              $creditCardFlag .= '<div class="credit-card-method-container">';
                $creditCardFlag .= '<span class="instant-approval-badge">' . __('Aprovação imediata', 'woo-custom-installments') . '</span>';
              $creditCardFlag .= '</div>';
          }

          $creditCardFlag .= '<div class="credit-card-container-badges">';
          $creditCardFlag .= $this->generate_card_flags( $options, 'credit-card' );
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

    if (isset( $options['enable_debit_card_method_payment_form'] ) == 'yes') {
        $debitCardFlag .= '<div class="woo-custom-installments-debit-card-section">';
        $debitCardFlag .= '<h4 class="debit-card-method-title">' . $this->getSetting('text_debit_card_container') . '</h4>';
        if (isset( $options['enable_instant_approval_badge'] ) == 'yes') {
            $debitCardFlag .= '<div class="debit-card-method-container">';
              $debitCardFlag .= '<span class="instant-approval-badge">' . __('Aprovação imediata', 'woo-custom-installments') . '</span>';
            $debitCardFlag .= '</div>';
        }

        $debitCardFlag .= '<div class="debit-card-container-badges">';
          $debitCardFlag .= $this->generate_card_flags( $options, 'credit-card' );
        $debitCardFlag .= '</div>';

        $debitCardFlag .= '</div>';
    }

    return $debitCardFlag;
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
    $get_text_before_variation_text = $this->getSetting( 'text_initial_variables' );
    $get_text_before_price = $this->getSetting( 'text_before_price' );
    $get_text_after_price = $this->getSetting( 'text_after_price' );
    $get_icon_pix = $this->getSetting( 'icon_main_price' );
    
    // Show original price before discount info
    $html = '<span class="original-price">' . $price . '</span>';

    $html .= '<div class="woo-custom-installments-group">';
    $html .= '<span class="woo-custom-installments-offer">';

    if( !empty( $get_icon_pix ) ) {
      $html .= '<i id="wci-icon-main-price" class="'. $get_icon_pix .'"></i>';
    }

    if ( !$product->is_purchasable() || $main_price_discount <= 0 ) {
      $price .= $this->display_best_installments( $product, $price );
      return $price;
    }

    // check if product is variation e get your parent id
    if ( $product->is_type( 'variation' ) ) {
      $parent_id = $product->get_parent_id();
      $disable_discount_in_parent = get_post_meta( $parent_id, '__disable_discount_main_price', true ) == 'yes';
    } else {
        $disable_discount_in_parent = false;
    }

    // check if '__disable_discount_main_price' is true and hide for the simple or variation products
    if ( $disable_discount_main_price || $disable_discount_in_parent ) {
        $price .= $this->display_best_installments( $product, $price );
        return $price;
    }

    // check if product is variable for display before variation text
    if ( $product->is_type( 'variable' ) && ! $this->variable_has_same_price( $product ) ) {
        $args = array();
        $html .= '<span class="variation-text">' . apply_filters( 'woo_custom_installments_before_variation_text', $get_text_before_variation_text ) . '</span>';
        $args['price'] = $product->get_variation_price( 'min' );
    }

    if ( $this->getSetting( 'product_price_discount_method' ) == 'percentage' ) {
        $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( wc_get_price_to_display( $product, $args ), $main_price_discount, $product );
    } else {
        $custom_price = wc_get_price_to_display( $product, $args ) - $main_price_discount;
    }

    // check if exists text before price for display
    if( !empty( $get_text_before_price ) ) {
      $html .= '<span class="discount-before-price">'. $get_text_before_price .'</span>';
    }

    $html .= '<span class="discounted-price">'. wc_price( $custom_price ) .'</span>';
      // check if exists text after price for display
      if( !empty( $get_text_after_price ) ) {
        $html .= '<span class="discount-after-price">'. $get_text_after_price .'</span>';
      }
    $html .= '</span>';

    // Display best installment with or without fee
    $html .= $this->display_best_installments( $product, $price );

    $html .= '</div>';

    // Display discount in main price in loop and single product pages
    if( $displayGlobal || ( $displayOnlySingleProduct && is_product() ) || ( $displayOnlyLoopProducts && is_archive() ) ) {
        return $html;
    } elseif( $displayOnlySingleProduct && is_archive() ) {
      // display best installments if discount main price is in only single product and is archive products
      $html_2 = $price;
      $html_2 .= $this->display_best_installments( $product, $price );
      return $html_2;
    } elseif( $displayOnlyLoopProducts && is_product()  ) {
      // display best installments if discount main price is in only archive products and is single product
      $html_3 = $price;
      $html_3 .= $this->display_best_installments( $product, $price );
      return $html_3;
    } else {
      return $price;
    }
    
  }


  /**
   * Create a shortcode for discount main price
   * 
   * @return string
   * @since 2.0.0
   */
  public function discount_main_price_shortcode( $atts ) {
    global $product;

    // check if local is product page for install shortcode
    if ( !$product ) {
      return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
    }

    return $this->discount_main_price( $price, $product );
  }


  /**
   * Format display prices
   * 
   * @return string
  */
  private function formatting_display( $installments, $return, $echo = true ) {
    global $product;

    // check if installments equal zero, if true return empty
    if ( 0 === count( $installments ) ) {
      return;
    }

    $return = apply_filters( 'woo_custom_installments_all_installments', $installments );

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
  public function best_without_interest( $installments, $product ) {

    $hook = self::hook();

    foreach ( $installments as $key => $installment ) {
      if ( 'no-fee' != $installment['class'] ) {
        unset( $installments[ $key ] );
      }
    }

    // get end installment without fee loop foreach
    $best_without_interest = end( $installments );

    if ( false === $best_without_interest ) {
      return;
    }

    if ( 'main_price' == $hook ) {
      $text = $this->getSetting( 'text_display_installments_single_product' );
    } else {
      $text = $this->getSetting( 'text_display_installments_loop' );
    }

    $find = array_keys( $this->strings_to_replace( $best_without_interest ) );
    $replace = array_values( $this->strings_to_replace( $best_without_interest ) );
    $text = str_replace( $find, $replace, $text );
    $get_icon_best_installments = $this->getSetting( 'icon_best_installments' );

    $html = '<span class="woo-custom-installments-details-without-fee">';
      if( ! empty( $get_icon_best_installments ) ) {
        $html .= '<i class="wci-icon-best-installments '. $get_icon_best_installments .'"></i>';
      }

      $html .= '<span class="woo-custom-installments-details best-value ' . $best_without_interest['class'] . '">' . apply_filters( 'woo_custom_installments_best_no_fee_' . $hook, $text, $best_without_interest, $product ) . '</span>';
    $html .= '</span>';

    return $html;
  }

  /**
   * Get best installment with interest
   * 
   * @return string
   * @since 1.0.0
  */
  public function best_with_interest( $installments, $product ) {
    $hook = self::hook();
    $best_with_interest = end( $installments );

    if ( false === $best_with_interest ) {
      return;
    }

    if ( 'main_price' == $hook ) {
      $text = $this->getSetting( 'text_display_installments_single_product' );
    } else {
      $text = $this->getSetting( 'text_display_installments_loop' );
    }

    $find = array_keys( $this->strings_to_replace( $best_with_interest ) );
    $replace = array_values( $this->strings_to_replace( $best_with_interest ) );
    $text = str_replace( $find, $replace, $text );
    $get_icon_best_installments = $this->getSetting( 'icon_best_installments' );

    $html = '<span class="woo-custom-installments-details-with-fee">';
      if( ! empty( $get_icon_best_installments ) ) {
        $html .= '<i class="wci-icon-best-installments '. $get_icon_best_installments .'"></i>';
      }

      $html .= '<span class="best-value'. $best_with_interest['class'] .'">'. apply_filters( 'woo_custom_installments_best_with_fee_'. $hook, $text, $best_with_interest, $product ) . '</span>';
    $html .= '</span>';

    return $html;
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
   * @since 1.0.0
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
   * Generate table of installments
   * 
   * @return array
   * @since 2.0.0
  */
  public function generate_installments_table( $installments, $product ) {
    if ( !$product ) {
        return;
    }

    if ( $product->is_type( 'variable' ) && ! $this->variable_has_same_price( $product ) ) {
      $args = array();
      $args['price'] = $product->get_variation_price( 'max' );
      $price = wc_get_price_to_display( $product, $args );
    } else {
      $price = wc_get_price_to_display( $product );
    }
    
    $all_installments = $this->set_values( 'all', $price, $product, false );

    if( !$all_installments ) {
        return;
    }

    // Installments table
    $table = '<h4 class="installments-title">'. $this->getSetting( 'text_table_installments' ) .'</h4>';
    $table .= '<div id="table-installments">';
      $table .= '<table class="table table-hover woo-custom-installments-table">';
        $table .= '<tbody data-default-text="'. $this->getSetting( 'text_display_installments_payment_forms' ) .'">';
          foreach ( $all_installments as $installment ) {
              $find = array_keys( $this->strings_to_replace( $installment ) );
              $replace = array_values( $this->strings_to_replace( $installment ) );
              $final_text = str_replace( $find, $replace, $this->getSetting( 'text_display_installments_payment_forms' ) );

              $table .= '<tr class="'. $installment['class'] .'">';
              $table .= '<th class="final-text">'. $final_text .'</th>';
              $table .= '<th class="final-price">'. wc_price( $installment['final_price'] ) .'</th>';
              $table .= '</tr>';
          }
        $table .= '</tbody>';
      $table .= '</table>';
    $table .= '</div>';

    return $table;
  }


  /**
   * Create a shortcode installments table
   * 
   * @return string
   * @since 2.0.0
   */
  public function installments_table_shortcode( $atts ) {
    $product = wc_get_product();

    // check if local is product page for install shortcode
    if ( !$product ) {
      return __( 'O local do shortcode inserido é inválido. Insira em um modelo de página de produto individual, ou em um local onde consiga obter o ID de um produto.', 'woo-custom-installments' );
    }

    return $this->generate_installments_table( null, $product );
  }


  /**
   * Format display popup or accordion
   * 
   * @since 2.0.0
  */
  public function full_installment( $product_id = false ) {
    if( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }
    
    $getTitleButton = $this->getSetting( 'text_button_installments' );
    $getPaymentForms = $this->getSetting( 'text_container_payment_forms' );
    $product = apply_filters( 'woo_custom_installments_full_installment_product', $product );
    $installments = array(); 
    $all_installments = array();

    // accordion content
    $accordion = '<div id="accordion-installments" class="accordion">';
      $accordion .= '<div class="accordion-item">';
        $accordion .= '<button class="accordion-header">'. $getTitleButton .'</button>';
        $accordion .= '<div class="accordion-content">';
          $accordion .= $this->woo_custom_installments_pix_flag();
          $accordion .= $this->woo_custom_installments_credit_card_flags();
          $accordion .= $this->woo_custom_installments_debit_card_flags();
          $accordion .= $this->woo_custom_installments_ticket_flag();
          $accordion .= $this->generate_installments_table( $installments, $product );
        $accordion .= '</div>';
      $accordion .= '</div>';
    $accordion .= '</div>';

    // popup content
    $popup = '<button id="open-popup"><span class="open-popup-text">'. $getTitleButton .'</span></button>';
    $popup .= '<div id="popup-container">';
      $popup .= '<div id="popup-content">';
        $popup .= '<div id="popup-header">';
          $popup .= '<h5 id="popup-title">'. $getPaymentForms .'</h5>';
          $popup .= '<button id="close-popup" aria-label="Fechar">.</button>';
        $popup .= '</div>';
          $popup .= $this->woo_custom_installments_pix_flag();
          $popup .= $this->woo_custom_installments_credit_card_flags();
          $popup .= $this->woo_custom_installments_debit_card_flags();
          $popup .= $this->woo_custom_installments_ticket_flag();
          $popup .= $this->generate_installments_table( $installments, $product );
      $popup .= '</div>';
    $popup .= '</div>';

    // check if product is purchasable
    if( !$product->is_purchasable() ) {
      return;
    }
    
    if( $this->getSetting( 'display_installment_type' ) == 'accordion' ) {
      echo apply_filters( 'woo_custom_installments_table', $accordion, $all_installments );
    } else {
      echo apply_filters( 'woo_custom_installments_table', $popup, $all_installments );
    }
    
  }


  /**
   * Create a shortcode for modal container
   * 
   * @return string
   * @since 2.0.0
   */
  public function render_full_installment_shortcode( $atts = array() ) {
    $product = wc_get_product();

    // check if product is valid
    if ( !$product ) {
        return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
    }

    $atts = shortcode_atts( array(
        'product_id' => $product->get_id(),
    ), $atts, 'woo_custom_installments_table' );

    ob_start();

    $this->full_installment( $atts['product_id'] );

    return ob_get_clean();
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
      <div class="woo-custom-installments-order-discount-cart">
        <tr>
          <span class="table-header-text">
            <th><?php echo apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), $this->getSetting( 'text_after_price' ) ) ); ?></th>
          </span>
          <span class="discount-price">
            <td data-title="<?php echo esc_attr( apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), $this->getSetting( 'text_after_price' ) ) ) ); ?>"><?php echo wc_price( $custom_price ); ?></td>
          </span>
        </tr>
      </div>
      <?php
    }
  }

}

new Woo_Custom_Installments_Frontend_Template();