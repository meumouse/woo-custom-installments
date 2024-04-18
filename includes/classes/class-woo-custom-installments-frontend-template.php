<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Display elements on front-end
 *
 * @since 1.0.0
 * @version 4.3.0
 * @package MeuMouse.com
 */
class Woo_Custom_Installments_Frontend_Template {

  public static $count = 0;

  public function __construct() {
    // get boolean condition to display installments in all the products
    if ( Woo_Custom_Installments_Init::get_setting('enable_installments_all_products') === 'yes' && ! is_admin() ) {
      add_filter( 'woocommerce_get_price_html', array( $this, 'woo_custom_installments_group' ), 999, 2 );
    }

    // get boolean condition to display installments in the cart
    if ( Woo_Custom_Installments_Init::get_setting('display_installments_cart') === 'yes' ) {
      add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_discount_on_cart' ) );

      // Integration with EpicJungle theme
      if ( class_exists( 'EpicJungle' ) ) {
        remove_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_discount_on_cart' ), 10 );
        add_action( 'woocommerce_cart_totals_before_shipping', array( $this, 'display_discount_on_cart' ) );
      }
    }

    // Include schema for product searchers
    if ( Woo_Custom_Installments_Init::get_setting('display_discount_price_schema') === 'yes' && ! is_admin() ) {
      include_once WOO_CUSTOM_INSTALLMENTS_INC . 'classes/class-woo-custom-installments-schema.php';
    }

    // get hook to display accordion or popup payment form in single product page
    if ( Woo_Custom_Installments_Init::get_setting( 'hook_payment_form_single_product' ) === 'before_cart' ) {
      add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'full_installment' ), 10 );
    } elseif ( Woo_Custom_Installments_Init::get_setting( 'hook_payment_form_single_product' ) === 'after_cart' ) {
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

    add_action( 'woocommerce_single_product_summary', array( $this, 'clear_product_function' ), 9999 );
    
    /**
     * Remove price range
     * 
     * @since 2.6.0
     */
    if ( Woo_Custom_Installments_Init::get_setting('remove_price_range') === 'yes' && Woo_Custom_Installments_Init::license_valid() && ! is_admin() ) {
      add_filter( 'woocommerce_variable_price_html', array( $this, 'starting_from_variable_product_price' ), 10, 2 );
      add_filter( 'woocommerce_variable_sale_price_html', array( $this, 'starting_from_variable_product_price' ), 10, 2 );
    }

    /**
     * Add text after price
     * 
     * @since 2.8.0
     */
    if ( Woo_Custom_Installments_Init::get_setting('custom_text_after_price') === 'yes' && ! is_admin() ) {
      add_filter( 'woocommerce_get_price_html', array( $this, 'add_custom_text_after_price' ), 10, 2 );
    }

    // display discount per quantity message if parent option is activated
    if ( Woo_Custom_Installments_Init::get_setting('enable_functions_discount_per_quantity') === 'yes' && Woo_Custom_Installments_Init::get_setting('message_discount_per_quantity') === 'yes' && ! is_admin() ) {
      add_action( 'woocommerce_single_product_summary', array( $this, 'display_message_discount_per_quantity' ) );
      add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'display_message_discount_per_quantity' ) );
    }
  }
  

  /**
   * Check if product is available
   * 
   * @since 1.0.0
   * @param mixed $product | Product ID or false
   * @return bool
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
   * @since 1.0.0
   * @param array $return
   * @param mixed $price | Product price or false
   * @param mixed $product | Product ID or false
   * @param bool $echo
   * @param $dynamic
   * @return string
  */
  public function set_values( $return, $price = false, $product = false, $echo = true, $dynamic = null ) {
    // check if is product
    if ( !$product ) {
      return $return;
    }

    $installments_info = array();
    $customFeeInstallments = get_option('woo_custom_installments_custom_fee_installments');
    $customFeeInstallments = maybe_unserialize( $customFeeInstallments );

    if ( ! $price ) {
      global $product;
      $args = array();

      if ( ! $product ) {
          return $return;
      }

      if ( $product->is_type( 'variable', 'variation' ) && !$this->variable_has_same_price( $product ) ) {
          $args['price'] = $product->get_variation_price('max');
      }

      $price = wc_get_price_to_display( $product, $args );
    }

    $price = apply_filters('woo_custom_installments_set_values_price', $price, $product);

    // check if product is different of available
    if ( ! $this->is_available( $product ) ) {
        return false;
    }

    // get max quantity of installments
    $installments_limit = Woo_Custom_Installments_Init::get_setting('max_qtd_installments');

    // get all installments options till the limit
    for ( $i = 1; $i <= $installments_limit; $i++ ) {
        $interest_rate = 0; // start without fee

        // check if option activated is set_fee_per_installment, else global fee is defined
        if ( Woo_Custom_Installments_Init::get_setting('set_fee_per_installment') === 'yes' ) {
            $interest_rate = isset( $customFeeInstallments[$i]['amount'] ) ? floatval( $customFeeInstallments[$i]['amount'] ) : 0;
        } else {
            $interest_rate = Woo_Custom_Installments_Init::get_setting('fee_installments_global');
        }

        // If interest be zero, use one formula for all
        if ( 0 == $interest_rate ) {
            $installments_info[] = $this->get_installment_details_without_interest( $price, $i );
            continue;
        }

        // get max quantity of installments without fee
        $max_installments_without_fee = Woo_Custom_Installments_Init::get_setting('max_qtd_installments_without_fee');

        // set the installments without fee
        if ( $i <= $max_installments_without_fee ) {
            // return values for this installment
            $installments_info[] = $this->get_installment_details_without_interest( $price, $i );
        } else {
            $installments_info[] = $this->get_installment_details_with_interest( $price, $interest_rate, $i );
        }
    }

    // get min value price of installment
    $min_installment_value = Woo_Custom_Installments_Init::get_setting('min_value_installments');

    foreach ( $installments_info as $index => $installment ) {
      if ( $installment['installment_price'] < $min_installment_value && 0 < $index ) {
          unset( $installments_info [$index ] );
      }
    }

    // check if variable $return is array to merge with installments_info
    if ( is_array( $return ) ) {
        $return = array_merge( $installments_info, $return );
    } else {
        $return = $installments_info;
    }

    return $this->formatting_display( $installments_info, $return, $echo );
  }


  /**
   * Define WooCommerce Hooks
   * 
   * @since 1.0.0
   * @return string
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
   * @since 1.0.0
   * @return bool
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
   * @since 2.0.0
   * @return string
   */
  public function best_installments_shortcode() {
    global $product;

    // check if local is product page for install shortcode
    if ( !$product ) {
      return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
    }

    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->display_best_installments( $product, $price );
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }


  /**
   * Display best installments
   * 
   * @since 2.1.0
   * @return bool
  */
  public function display_best_installments( $product, $price, $original_price = false ) {
    if ( null !== ( $pre_value = apply_filters( 'woo_custom_installments_pre_installments_price', null, $product, $price, $original_price ) ) ) {
        return $pre_value;
    }
  
    // check if product is purchasable
    if ( ! $product->is_purchasable() ) {
      return;
    }
  
    // check if option __disable_installments in product is true
    $disable_installments_in_product = get_post_meta( $product->get_id(), '__disable_installments', true ) === 'yes';
  
    // check if product is variation e get the id of parent product
    if ( $product->is_type( 'variation' ) ) {
        $parent_id = $product->get_parent_id();
        $disable_installments_in_parent = get_post_meta( $parent_id, '__disable_installments', true ) === 'yes';
    } else {
        $disable_installments_in_parent = false;
    }
  
    // check if '__disable_installments' is true for the simple or variation products
    if ( $disable_installments_in_product || $disable_installments_in_parent ) {
        return;
    }
  
    $display_single_product = Woo_Custom_Installments_Init::get_setting( 'hook_display_best_installments' );
    $display_best_installments_global = Woo_Custom_Installments_Init::get_setting( 'hook_display_best_installments' ) == 'display_loop_and_single_product';
    $display_best_installments_only_single_product = Woo_Custom_Installments_Init::get_setting( 'hook_display_best_installments' ) == 'only_single_product';
    $display_best_installments_only_loop = Woo_Custom_Installments_Init::get_setting( 'hook_display_best_installments' ) == 'only_loop_products';
    $args = array();
  
    if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
        $args['price'] = $product->get_variation_price( 'min' );
    }
  
    $installments = $this->set_values( $display_single_product, wc_get_price_to_display( $product, $args ), $product, false );
    $best_installments = '';
    $get_type_best_installments = Woo_Custom_Installments_Init::get_setting( 'get_type_best_installments' );
  
    if ( $get_type_best_installments == 'best_installment_without_fee' ) {
        $best_installments = $this->best_without_interest( $installments, $product );
    } elseif ( $get_type_best_installments == 'best_installment_with_fee' ) {
        $best_installments = $this->best_with_interest( $installments, $product );
    } elseif ( $get_type_best_installments == 'both' ) {
        $best_installments  = $this->best_without_interest( $installments, $product );
        $best_installments .= $this->best_with_interest( $installments, $product );
    }
  
    if ( ! empty( $best_installments ) ) {
        $html = ' <span class="woo-custom-installments-card-container">';
        $html .= $best_installments;
        $html .= ' </span>';  
    } else {
        return;
    }

    // Display best installments in single product page, loop or both
    if ( ( $display_best_installments_global ) || ( $display_best_installments_only_single_product && is_product() ) || ( $display_best_installments_only_loop && !is_product() && is_archive() ) ) {
        return $html;
    } else {
        return;
    }
  }


  /**
   * Pix flag
   * 
   * @since 2.0.0
   * @return string
   */
  public function woo_custom_installments_pix_flag() {
    if ( ! is_product() ) {
      return;
    }

    global $product;

    $price = wc_get_price_to_display( $product );
    $economy_pix_active = Woo_Custom_Installments_Init::get_setting('enable_economy_pix_badge') === 'yes';
    $pixFlag = '';
    
    if ( Woo_Custom_Installments_Init::get_setting('enable_pix_method_payment_form') === 'yes' ) {
      $pixFlag .= '<div class="woo-custom-installments-pix-section">';
        $pixFlag .= '<h4 class="pix-method-title">'. Woo_Custom_Installments_Init::get_setting( 'text_pix_container' ) .'</h4>';
        $pixFlag .= '<div class="pix-method-container">';
          $pixFlag .= '<span class="pix-method-name">'. __( 'Pix', 'woo-custom-installments' ) .'</span>';
          
          if ( Woo_Custom_Installments_Init::get_setting('enable_instant_approval_badge') === 'yes' ) {
            $pixFlag .= '<span class="instant-approval-badge">'. __( 'Aprovação imediata', 'woo-custom-installments' ) .'</span>';
          }

        $pixFlag .= '</div>';

        if ( $economy_pix_active ) {
          $pixFlag .= '<div class="container-badge-icon pix-flag pix-info instant-approval-badge">';
        } else {
          $pixFlag .= '<div class="container-badge-icon pix-flag pix-info">';
        }
          $pixFlag .= '<i class="pix-icon-badge fa-brands fa-pix"></i>';
          
          if ( $economy_pix_active ) {
            $pixFlag .= '<div class="economy-pix-info">';
              $pixFlag .= $this->economy_pix_badge( $product, $price );
            $pixFlag .= '</div>';
          }

        $pixFlag .= '</div>';
      $pixFlag .= '</div>';
    }

    return $pixFlag;
  }


  /**
   * Create shortcode for pix flag
   * 
   * @since 2.8.0
   * @return string
   */
  public function pix_flag_shortcode() {
    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->woo_custom_installments_pix_flag();
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }

  /**
   * Ticket flag
   * 
   * @since 2.0.0
   * @return string
   */
  public function woo_custom_installments_ticket_flag() {
    $ticket_flag = '';
    
    if ( Woo_Custom_Installments_Init::get_setting('enable_ticket_method_payment_form') === 'yes' ) {
      $ticket_flag .= '<div class="woo-custom-installments-ticket-section">';
        $ticket_flag .= '<h4 class="ticket-method-title">'. Woo_Custom_Installments_Init::get_setting( 'text_ticket_container' ) .'</h4>';
        $ticket_flag .= '<div class="ticket-method-container">';
          $ticket_flag .= '<span class="ticket-instructions">'. Woo_Custom_Installments_Init::get_setting( 'text_instructions_ticket_container' ) .'</span>';
        $ticket_flag .= '</div>';
        $ticket_flag .= '<div class="container-badge-icon ticket-flag">';
          $ticket_flag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/boleto-badge.svg"/>';
        $ticket_flag .= '</div>';
      $ticket_flag .= '</div>';
    }

    return $ticket_flag;
  }


  /**
   * Create shortcode for ticket flag
   * 
   * @since 2.8.0
   * @return string
   */
  public function ticket_flag_shortcode() {
    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->woo_custom_installments_ticket_flag();
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }


  /**
   * Generate card flags
   * 
   * @since 2.0.0
   * @param $options | get_option('woo-custom-installments-setting')
   * @param $card_type | credit-card or debit-card
   * @param $type | credit or debit
   * @return array
   */
  private function generate_card_flags( $options, $card_type, $type ) {
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

    foreach( $flags as $key => $flag ) {
        if ( isset( $options['enable_' . $key . '_flag_' . $type] ) && $options['enable_' . $key . '_flag_' . $type] === 'yes' ) {
            $card_flags .= '<div class="container-badge-icon ' . $card_type . ' ' . $key . '-flag">';
            $card_flags .= '<img class="size-badge-icon" src="' . WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/' . $flag . '"/>';
            $card_flags .= '</div>';
        }
    }

    return $card_flags;
  }


  /**
   * Credit card flags
   * 
   * @since 2.0.0
   * @version 4.1.0
   * @return string
   */
  public function woo_custom_installments_credit_card_flags() {
      $options = get_option('woo-custom-installments-setting');
      $creditCardFlag = '';

      if ( Woo_Custom_Installments_Init::get_setting('enable_credit_card_method_payment_form') === 'yes' ) {
          $creditCardFlag .= '<div class="woo-custom-installments-credit-card-section">';
          $creditCardFlag .= '<h4 class="credit-card-method-title">' . Woo_Custom_Installments_Init::get_setting('text_credit_card_container') . '</h4>';
          
          if ( Woo_Custom_Installments_Init::get_setting('enable_instant_approval_badge') === 'yes' ) {
              $creditCardFlag .= '<div class="credit-card-method-container">';
                $creditCardFlag .= '<span class="instant-approval-badge">' . __('Aprovação imediata', 'woo-custom-installments') . '</span>';
              $creditCardFlag .= '</div>';
          }

          $creditCardFlag .= '<div class="credit-card-container-badges">';
          $creditCardFlag .= $this->generate_card_flags( $options, 'credit-card', 'credit' );
          $creditCardFlag .= '</div>';
          $creditCardFlag .= '</div>';
      }

      return $creditCardFlag;
  }


  /**
   * Create shortcode for credit card flags
   * 
   * @since 2.8.0
   * @return string
   */
  public function credit_card_flag_shortcode() {
    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->woo_custom_installments_credit_card_flags();
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }


  /**
   * Debit card flags
   * 
   * @return string
   * @since 2.0.0
   */
  public function woo_custom_installments_debit_card_flags() {
    $options = get_option('woo-custom-installments-setting');
    $debit_card_flag = '';

    if ( Woo_Custom_Installments_Init::get_setting('enable_debit_card_method_payment_form') === 'yes') {
        $debit_card_flag .= '<div class="woo-custom-installments-debit-card-section">';
        $debit_card_flag .= '<h4 class="debit-card-method-title">' . Woo_Custom_Installments_Init::get_setting('text_debit_card_container') . '</h4>';
        
        if ( Woo_Custom_Installments_Init::get_setting('enable_instant_approval_badge') === 'yes' ) {
            $debit_card_flag .= '<div class="debit-card-method-container">';
              $debit_card_flag .= '<span class="instant-approval-badge">' . __('Aprovação imediata', 'woo-custom-installments') . '</span>';
            $debit_card_flag .= '</div>';
        }

        $debit_card_flag .= '<div class="debit-card-container-badges">';
          $debit_card_flag .= $this->generate_card_flags( $options, 'credit-card', 'debit' );
        $debit_card_flag .= '</div>';

        $debit_card_flag .= '</div>';
    }

    return $debit_card_flag;
  }


  /**
   * Create shortcode for debit card flags
   * 
   * @since 2.8.0
   * @return string
   */
  public function debit_card_flag_shortcode() {
    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->woo_custom_installments_debit_card_flags();
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }


  /**
   * Replament strings in front-end
   * 
   * @since 1.3.0
   * @param array $values | Installment value
   * @return array
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
   * Replace range price for "A partir de"
   * 
   * @since 2.4.0
   * @param string $price | Product price
   * @param object $product | Product object
   * @return string
   */
  public function starting_from_variable_product_price( $price, $product ) {
    if ( $product->is_type( 'variable' ) ) {
        $min_price = $product->get_variation_price( 'min', true );
        $max_price = $product->get_variation_price( 'max', true );

        if ( $min_price !== $max_price ) {
            $text_initial = ! empty( Woo_Custom_Installments_Init::get_setting( 'text_initial_variables' ) ) ? '<span class="woo-custom-installments-starting-from">' . Woo_Custom_Installments_Init::get_setting( 'text_initial_variables' ) . '</span>' : '';

            $price = $text_initial . wc_price( $min_price );
        }
    }

    return $price;
  }


  /**
   * Add custom text after price
   * 
   * @since 2.8.0
   * @return string
   */
  public function add_custom_text_after_price( $price, $product ) {
    $price .= '<span class="woo-custom-installments-text-after-price">'. Woo_Custom_Installments_Init::get_setting( 'custom_text_after_price_front' ) .'</span>';

    return $price;
  }


  /**
   * Display group elements
   * 
   * @since 2.0.0
   * @version 3.6.2
   * @param string $price | Product price
   * @param object $product | Product object
   * @return string
  */
  public function woo_custom_installments_group( $price, $product ) {
    $display_discount_price_hook = Woo_Custom_Installments_Init::get_setting( 'display_discount_price_hook' );
    $display_economy_pix_hook = Woo_Custom_Installments_Init::get_setting( 'display_economy_pix_hook' );
    $display_best_installments_hook = Woo_Custom_Installments_Init::get_setting( 'hook_display_best_installments' );
    $display_discount_ticket_hook = Woo_Custom_Installments_Init::get_setting( 'display_discount_ticket_hook' );

    // Show original price before discount info
    $html = '<span class="original-price">' . $price . '</span>';

    if ( $product->is_type( 'simple' ) || $this->variable_has_same_price( $product ) ) {
      $html .= '<div class="woo-custom-installments-group">';
    } else {
      $html .= '<div class="woo-custom-installments-group variable-range-price">';
    }

    // Check display conditions
    if ( $display_discount_price_hook == 'display_loop_and_single_product'
    || ( $display_discount_price_hook == 'only_single_product' && is_product() )
    || ( $display_discount_price_hook == 'only_loop_products' && is_archive() ) ) {
      $html .= $this->discount_main_price_single( $product, $price );
    }

    // display discount ticket badge
    if ( Woo_Custom_Installments_Init::get_setting('enable_ticket_method_payment_form') === 'yes' ) {
      if ( Woo_Custom_Installments_Init::get_setting('enable_ticket_discount_main_price') === 'yes' ) {
        // Check display conditions
        if ( $display_discount_ticket_hook == 'global'
        || ( $display_discount_ticket_hook == 'only_single_product' && is_product() )
        || ( $display_discount_ticket_hook == 'only_loop_products' && is_archive() ) ) {
          // display best installment with or without fee
          $html .= $this->discount_ticket_badge( $product, $price );
        }
      }
    }

    // Check display conditions
    if ( $display_best_installments_hook == 'display_loop_and_single_product'
    || ( $display_best_installments_hook == 'only_single_product' && is_product() )
    || ( $display_best_installments_hook == 'only_loop_products' && is_archive() ) ) {
      // display best installment with or without fee
      $html .= $this->display_best_installments( $product, $price );
    }

    // display economy pix badge
    if ( Woo_Custom_Installments_Init::get_setting('enable_economy_pix_badge') === 'yes' ) {
        // Check display conditions
        if ( $display_economy_pix_hook == 'global'
        || ( $display_economy_pix_hook == 'only_single_product' && is_product() )
        || ( $display_economy_pix_hook == 'only_loop_products' && is_archive() ) ) {
          $html .= $this->economy_pix_badge( $product, $price );
        }
    }

    // close 'woo-custom-installments-group'
    $html .= '</div>';

    // check if product price is bigger then zero
    if ( $product->get_price() > 0 ) {
      return $html;
    }  else {
      return $price;
    }
  }


  /**
   * Discount product main price
   * 
   * @since 3.6.0
   * @param object $product | Product object
   * @param string $price | Product price
   * @return string $html
   */
  public function discount_main_price_single( $product, $price ) {
    $product_id = $product->get_id();
    $args = array();
    $discount = Woo_Custom_Installments_Init::get_setting('discount_main_price');
    $icon = Woo_Custom_Installments_Init::get_setting('icon_main_price');
    $disable_discount_main_price = get_post_meta( $product_id, '__disable_discount_main_price', true );
    $discount_per_product = get_post_meta( $product_id, 'enable_discount_per_unit', true );
    $discount_per_product_method = get_post_meta( $product_id, 'discount_per_unit_method', true );
    $discount_per_product_value = get_post_meta( $product_id, 'unit_discount_amount', true );
    $enabled_post_meta_feed_xml_price = Woo_Custom_Installments_Init::get_setting('enable_post_meta_feed_xml_price') === 'yes';

    if ( $disable_discount_main_price === 'yes' ) {
      return;
    }

    // prevent error for empty discout value
    if ( empty( $discount ) ) {
      $discount = 0;
    } elseif ( empty( $discount_per_product_value ) ) {
      $discount_per_product_value = 0;
    }

    $html = '<span class="woo-custom-installments-offer">';

    if ( ! empty( $icon ) ) {
      $html .= '<i class="wci-icon-main-price '. $icon .'"></i>';
    }

    // check if product is variation e get your parent id
    if ( $product->is_type( 'variation' ) ) {
      $parent_id = $product->get_parent_id();
      $disable_discount_in_parent = get_post_meta( $parent_id, '__disable_discount_main_price', true );
    } else {
      $disable_discount_in_parent = false;
    }

    // Apply individual product discount if enabled
    if ( $discount_per_product === 'yes' ) {
      if ( $discount_per_product_method === 'percentage' ) {
          $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( wc_get_price_to_display( $product, $args ), $discount_per_product_value, $product );
      } else {
          $custom_price = wc_get_price_to_display( $product, $args ) - $discount_per_product_value;
      }
    } else {
        if ( Woo_Custom_Installments_Init::get_setting( 'product_price_discount_method' ) === 'percentage' ) {
            $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( wc_get_price_to_display( $product, $args ), $discount, $product );
        } else {
            $custom_price = wc_get_price_to_display( $product, $args ) - $discount;
        }
    }
    
    // check if exists text before price for display
    if ( ! empty( Woo_Custom_Installments_Init::get_setting( 'text_before_price' ) ) ) {
      $html .= '<span class="discount-before-price">'. Woo_Custom_Installments_Init::get_setting( 'text_before_price' ) .'</span>';
    }

    $html .= '<span class="discounted-price">'. wc_price( $custom_price ) .'</span>';

    // check if exists text after price for display
    if ( ! empty( Woo_Custom_Installments_Init::get_setting( 'text_after_price' ) ) ) {
      $html .= '<span class="discount-after-price">'. Woo_Custom_Installments_Init::get_setting( 'text_after_price' ) .'</span>';
    }

    // close 'woo-custom-installments-offer' span
    $html .= '</span>';

    // check if product price is bigger then zero
    if ( $product->get_price() > 0 ) {
      return $html;
    }
  }


  /**
   * Create a shortcode for discount on Pix single
   * 
   * @since 3.6.0
   * @param $product
   * @param $price
   * @return string
   */
  public function discount_pix_info_shortcode( $product, $price ) {
    global $product;

    // check if local is product page for install shortcode
    if ( !$product ) {
      return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
    }

    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->discount_main_price_single( $price, $product );
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }


  /**
   * Create a shortcode for discount main price
   * 
   * @return string
   * @since 2.0.0
   */
  public function woo_custom_installments_group_shortcode( $product, $price ) {
    global $product;

    // check if local is product page for install shortcode
    if ( !$product ) {
      return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
    }

    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->woo_custom_installments_group( $price, $product );
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }


  /**
   * Create a ticket discount badge
   * 
   * @since 2.8.0
   * @param $product
   * @param $price
   * @return void
   */
  public function discount_ticket_badge( $product, $price ) {
    $args = array();
    $price = wc_get_price_to_display( $product, $args );
    $icon = Woo_Custom_Installments_Init::get_setting( 'ticket_discount_icon' );
    $ticket_discount_value = Woo_Custom_Installments_Init::get_setting( 'discount_ticket' );
    $product_id = $product->get_id();
    $disable_discount_main_price = get_post_meta( $product_id, '__disable_discount_main_price', true );

    if ( $disable_discount_main_price === 'yes' ) {
      return;
    }

    $html = '<span class="woo-custom-installments-ticket-discount">';

      if ( !empty( $icon ) ) {
        $html .= '<i class="wci-icon-ticket-discount '. $icon .'"></i>';
      }

      // check if exists text before price for display
      if ( !empty( Woo_Custom_Installments_Init::get_setting( 'text_before_discount_ticket' ) ) ) {
        $html .= '<span class="discount-before-discount-ticket">'. Woo_Custom_Installments_Init::get_setting( 'text_before_discount_ticket' ) .'</span>';
      }

      if ( Woo_Custom_Installments_Init::get_setting( 'discount_method_ticket' ) == 'percentage' ) {
        $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( $price, $ticket_discount_value, $product );
      } else {
          $custom_price = $price - $ticket_discount_value;
      }

      $html .= '<span class="discounted-price">'. wc_price( $custom_price ) .'</span>';
        // check if exists text after price for display
        if ( !empty( Woo_Custom_Installments_Init::get_setting( 'text_after_discount_ticket' ) ) ) {
          $html .= '<span class="discount-after-discount-ticket">'. Woo_Custom_Installments_Init::get_setting( 'text_after_discount_ticket' ) .'</span>';
        }
    $html .= '</span>';

    return $html;
  }


  /**
   * Create a shortcode for discount ticket badge
   * 
   * @since 2.8.0
   * @param $product
   * @param $price
   * @return string
   */
  public function discount_ticket_badge_shortcode( $product, $price  ) {
    global $product;

    // check if local is product page for install shortcode
    if ( !$product ) {
      return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
    }

    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->discount_ticket_badge( $price, $product );
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }


  /**
   * Create a economy Pix badge
   * 
   * @since 3.6.0
   * @param $product | Product ID
   * @param $price | Product price
   * @return string
   */
  public function economy_pix_badge( $product, $price ) {
    $product_id = $product->get_id();
    $args = array();
    $price = wc_get_price_to_display( $product, $args );
    $discount = Woo_Custom_Installments_Init::get_setting( 'discount_main_price' );
    $discount_per_product = get_post_meta( $product_id, 'enable_discount_per_unit', true );
    $discount_per_product_method = get_post_meta( $product_id, 'discount_per_unit_method', true );
    $discount_per_product_value = get_post_meta( $product_id, 'unit_discount_amount', true );
    $disable_discount_main_price = get_post_meta( $product_id, '__disable_discount_main_price', true );

    if ( $disable_discount_main_price === 'yes' ) {
      return;
    }

    // Apply individual product discount if enabled
    if ( $discount_per_product === 'yes' ) {
      if ( $discount_per_product_method === 'percentage' ) {
          $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( $price, $discount_per_product_value, $product );
      } else {
          $custom_price = $price - $discount_per_product_value;
      }
    } else {
        if ( Woo_Custom_Installments_Init::get_setting( 'product_price_discount_method' ) === 'percentage' ) {
            $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( $price, $discount, $product );
        } else {
            $custom_price = $price - $discount;
        }
    }

    $get_discount = $price - $custom_price;

    // check if exists text before price for display
    if ( !empty( Woo_Custom_Installments_Init::get_setting( 'text_economy_pix_badge' ) ) ) {
      $html = '<span class="woo-custom-installments-economy-pix-badge">';
        $html .= '<i class="wci-icon-economy-pix fa-solid fa-circle-info"></i>';
        $html .= '<span class="discount-before-economy-pix">'. sprintf( Woo_Custom_Installments_Init::get_setting( 'text_economy_pix_badge' ), wc_price( $get_discount ) ) .'</span>';
      $html .= '</span>';
    }

    return $html;
  }


  /**
   * Create shortcode for economy Pix badge
   * 
   * @since 3.6.0
   * @param $product
   * @param $price
   * @return string
   */
  public function economy_pix_badge_shortcode( $product, $price  ) {
    global $product;

    // check if local is product page for install shortcode
    if ( !$product ) {
      return __( 'O local do shortcode inserido é inválido. É permitido apenas para produtos.', 'woo-custom-installments' );
    }

    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->economy_pix_badge( $price, $product );
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }


  /**
   * Format display prices
   * 
   * @since 1.0.0
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
    // check if $installments is different of array or empty $installments or product price is zero
    if ( ! is_array( $installments ) || empty( $installments ) || $product->get_price() <= 0 ) {
      return;
    }

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
      $text = Woo_Custom_Installments_Init::get_setting( 'text_display_installments_single_product' );
    } else {
      $text = Woo_Custom_Installments_Init::get_setting( 'text_display_installments_loop' );
    }

    $find = array_keys( $this->strings_to_replace( $best_without_interest ) );
    $replace = array_values( $this->strings_to_replace( $best_without_interest ) );
    $text = str_replace( $find, $replace, $text );
    $icon = Woo_Custom_Installments_Init::get_setting( 'icon_best_installments' );

    $html = '<span class="woo-custom-installments-details-without-fee">';
      if ( ! empty( $icon ) ) {
        $html .= '<i class="wci-icon-best-installments '. $icon .'"></i>';
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
    // check if $installments is different of array or empty $installments or product price is zero
    if ( ! is_array( $installments ) || empty( $installments ) || $product->get_price() <= 0 ) {
      return;
    }

    $hook = self::hook();
    $best_with_interest = end( $installments );

    if ( false === $best_with_interest ) {
      return;
    }

    if ( 'main_price' == $hook ) {
      $text = Woo_Custom_Installments_Init::get_setting( 'text_display_installments_single_product' );
    } else {
      $text = Woo_Custom_Installments_Init::get_setting( 'text_display_installments_loop' );
    }

    $find = array_keys( $this->strings_to_replace( $best_with_interest ) );
    $replace = array_values( $this->strings_to_replace( $best_with_interest ) );
    $text = str_replace( $find, $replace, $text );
    $icon = Woo_Custom_Installments_Init::get_setting( 'icon_best_installments' );

    $html = '<span class="woo-custom-installments-details-with-fee">';
      if ( ! empty( $icon ) ) {
        $html .= '<i class="wci-icon-best-installments '. $icon .'"></i>';
      }

      $html .= '<span class="best-value'. $best_with_interest['class'] .'">'. apply_filters( 'woo_custom_installments_best_with_fee_'. $hook, $text, $best_with_interest, $product ) . '</span>';
    $html .= '</span>';

    return $html;
  }

  /**
   * Get fee info
   * 
   * @return string
   * @since 1.0.0
  */
  public function get_fee_info( $installment ) {
    $hook = self::hook();

    $text = ( $installment['interest_fee'] ) ? '' . Woo_Custom_Installments_Init::get_setting( 'text_with_fee_installments' ) : ' '. Woo_Custom_Installments_Init::get_setting( 'text_without_fee_installments' );
    return apply_filters( 'woo_custom_installments_fee_label', $text, $installment['interest_fee'], $hook );
  }


  /**
   * Check if variations have equal price
   * 
   * @return string
   * @since 1.0.0
  */
  public function variable_has_same_price( $product ) {
    return ( $product->is_type( 'variable', 'variation' ) && $product->get_variation_price( 'min' ) === $product->get_variation_price( 'max' ) );
  }


  /**
   * Save array with all details of installments
   * 
   * @since 1.0.0
   * @return string
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
   * @since 1.0.0
   * @param string $total | Product price
   * @param string $i | Installments
   * @return string
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
   * @since 1.0.0
   * @param string $total | Product price
   * @param string $fee | Interest rate
   * @param string $i | Installments
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
   * Generate table of installments
   * 
   * @since 2.0.0
   * @return string
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

    if ( ! $all_installments ) {
        return;
    }

    // Installments table
    $table = '<h4 class="installments-title">'. Woo_Custom_Installments_Init::get_setting( 'text_table_installments' ) .'</h4>';
    $table .= '<div id="table-installments">';
      $table .= '<table class="table table-hover woo-custom-installments-table">';
        $table .= '<tbody data-default-text="'. Woo_Custom_Installments_Init::get_setting( 'text_display_installments_payment_forms' ) .'">';
          foreach ( $all_installments as $installment ) {
              $find = array_keys( $this->strings_to_replace( $installment ) );
              $replace = array_values( $this->strings_to_replace( $installment ) );
              $final_text = str_replace( $find, $replace, Woo_Custom_Installments_Init::get_setting( 'text_display_installments_payment_forms' ) );

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

    if ( Woo_Custom_Installments_Init::license_valid() ) {
      return $this->generate_installments_table( null, $product );
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
  }


  /**
   * Format display popup or accordion
   * 
   * @since 2.0.0
   * @version 4.1.0
   * @param bool $product_id
   * @return string
  */
  public function full_installment( $product_id = false ) {
    if ( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }

    $product = apply_filters( 'woo_custom_installments_full_installment_product', $product );
    $installments = array(); 
    $all_installments = array();

    // check if product is variation e get your parent id
    if ( $product->is_type( 'variation' ) ) {
      $disable_installments = get_post_meta( $product->get_parent_id(), '__disable_installments', true ) === 'yes';
    } else {
      $disable_installments = get_post_meta( $product->get_id(), '__disable_installments', true ) === 'yes';
    }

    // check if '__disable_installments' is true or not purchasable and hide for the simple or variation products
    if ( $disable_installments == 'yes' || !$product->is_purchasable() ) {
        return;
    }

    /**
     * Hook for display custom content before installments container
     * 
     * @since 4.1.0
     */
    do_action('woo_custom_installments_before_installments_container');
    
    if ( Woo_Custom_Installments_Init::get_setting( 'display_installment_type' ) === 'accordion' ) {
      echo apply_filters( 'woo_custom_installments_table', $this->accordion_container( $product, $installments ), $all_installments );
    } elseif ( Woo_Custom_Installments_Init::get_setting( 'display_installment_type' ) === 'popup' ) {
        echo apply_filters( 'woo_custom_installments_table', $this->popup_container( $product, $installments ), $all_installments );
    } else {
        return;
    }

    /**
     * Hook for display custom content after installments container
     * 
     * @since 4.1.0
     */
    do_action('woo_custom_installments_after_installments_container');
  }


  /**
   * Create container for popup installments
   * 
   * @since 4.1.0
   * @param int $product | Product ID
   * @param array $installments | Array of installments
   * @return void
   */
  public function popup_container( $product, $installments ) {
    ?>
    <button id="open-popup">
      <span class="open-popup-text"><?php echo Woo_Custom_Installments_Init::get_setting( 'text_button_installments' ); ?></span>
    </button>

    <div id="popup-container">
      <div id="popup-content">
        <div id="popup-header">
          <h5 id="popup-title"><?php echo Woo_Custom_Installments_Init::get_setting( 'text_container_payment_forms' ); ?></h5>
          <button id="close-popup" aria-label="Fechar">.</button>
        </div>

          <?php
          /**
           * Hook for display custom content inside accordion container
           * 
           * @since 4.1.0
           */
          do_action('woo_custom_installments_popup_header');

          if ( Woo_Custom_Installments_Init::license_valid() ) {
            echo $this->woo_custom_installments_pix_flag();
            echo $this->woo_custom_installments_credit_card_flags();
            echo $this->woo_custom_installments_debit_card_flags();
            echo $this->woo_custom_installments_ticket_flag();
          }

          echo $this->generate_installments_table( $installments, $product );

          /**
           * Hook for display custom content inside bottom popup
           * 
           * @since 4.1.0
           */
          do_action('woo_custom_installments_popup_bottom'); ?>
      </div>
    </div>
    <?php
  }


  /**
   * Create container for accordion installments
   * 
   * @since 4.1.0
   * @param int $product | Product ID
   * @param array $installments | Array of installments
   * @return void
   */
  public function accordion_container( $product, $installments ) {
    ?>
    <div id="accordion-installments" class="accordion">
      <div class="accordion-item">
        <button class="accordion-header"><?php echo Woo_Custom_Installments_Init::get_setting( 'text_button_installments' ); ?></button>
        <div class="accordion-content">

          <?php
          /**
           * Hook for display custom content inside header accordion
           * 
           * @since 4.1.0
           */
          do_action('woo_custom_installments_accordion_header');

          if ( Woo_Custom_Installments_Init::license_valid() ) {
            echo $this->woo_custom_installments_pix_flag();
            echo $this->woo_custom_installments_credit_card_flags();
            echo $this->woo_custom_installments_debit_card_flags();
            echo $this->woo_custom_installments_ticket_flag();
          }
          
          echo $this->generate_installments_table( $installments, $product ); ?>
        </div>

        <?php
        /**
         * Hook for display custom content inside bottom accordion
         * 
         * @since 4.1.0
         */
        do_action('woo_custom_installments_accordion_bottom'); ?>
      </div>
    </div>
    <?php
  }


  /**
   * Create a shortcode for modal container
   * 
   * @since 2.0.0
   * @param array $atts | Shortcode attributes
   * @return object
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

    if ( Woo_Custom_Installments_Init::license_valid() ) {
      $this->full_installment( $atts['product_id'] );
    } else {
      return __( 'Os shortcodes estão disponíveis na versão Pro do Parcelas Customizadas para WooCommerce.', 'woo-custom-installments' );
    }
    
    return ob_get_clean();
  }


  /**
   * Display menssage in elegible products for discount per quantity
   * 
   * @since 2.8.0
   * @version 3.8.0
   * @return void
   */
  public function display_message_discount_per_quantity() {
    global $product;

    $product_id = $product->get_id();
    $current_quantity = $product->get_stock_quantity();
    $enable_global_discount = Woo_Custom_Installments_Init::get_setting('enable_discount_per_quantity_method') === 'global';
    $enable_product_discount = get_post_meta( $product_id, 'enable_discount_per_quantity', true );
    
    if ( $enable_global_discount || $enable_product_discount ) {
      if ( $enable_global_discount ) {
          $method = Woo_Custom_Installments_Init::get_setting('discount_per_quantity_method');
          $value = Woo_Custom_Installments_Init::get_setting('value_for_discount_per_quantity');
          $minimum_quantity = Woo_Custom_Installments_Init::get_setting('set_quantity_enable_discount');
      } else {
          $method = get_post_meta( $product_id, 'discount_per_quantity_method', true );
          $value = get_post_meta( $product_id, 'quantity_discount_amount', true );
          $minimum_quantity = get_post_meta( $product_id, 'minimum_quantity_discount', true );
      }

      if ( $method == 'percentage' ) {
        $discount_message = $value . '%';
      } else {
        $discount_message = get_woocommerce_currency_symbol() . $value;
      }

      echo '<div class="woo-custom-installments-discount-per-quantity-message">';
        echo '<i class="fa-solid fa-circle-exclamation"></i>';
        echo '<span>'. sprintf( Woo_Custom_Installments_Init::get_setting('text_discount_per_quantity_message'), $minimum_quantity, $discount_message ) .'</span>';
      echo '</div>';
    }
  }


  /**
   * Display discount in cart page
   * 
   * @since 2.6.0
   * @version 3.8.0
   * @return string
   */
  public function display_discount_on_cart() {
    if ( Woo_Custom_Installments_Init::get_setting('enable_all_discount_options') !== 'yes' ) {
        return;
    }

    // Initialize variables for total discount price and total cart value
    $total_discount_price = 0;
    $total_cart_value = 0;

    // Iterate over cart items and calculate discount for those where discount is not disabled
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $product = $cart_item['data'];
        $product_id = $product->get_id();
        $disable_discount = get_post_meta( $product_id, '__disable_discount_main_price', true ) === 'yes';
        $parent_id = $product->get_parent_id();
        $disable_discount_in_parent = get_post_meta( $parent_id, '__disable_discount_main_price', true ) === 'yes';

        // Get the discount information specific to the product
        $discount_per_product = get_post_meta( $product_id, 'enable_discount_per_unit', true );
        $discount_per_product_method = get_post_meta( $product_id, 'discount_per_unit_method', true );
        $discount_per_product_value = get_post_meta( $product_id, 'unit_discount_amount', true );

        // Calculate subtotal for individual item
        $cart_item_total = $cart_item['data']->get_price() * $cart_item['quantity'];
        $total_cart_value += $cart_item_total;

        // Check if the product or its parent has the discount disabled
        if ( ! $disable_discount && ! $disable_discount_in_parent ) {
            // Apply individual product discount if enabled
            if ( $discount_per_product === 'yes' ) {
                if ( $discount_per_product_method === 'percentage' ) {
                    $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( $cart_item_total, $discount_per_product_value );
                } else {
                    $custom_price = $cart_item_total - $discount_per_product_value;
                }
            } else {
                // Calculate discounted price for individual item using the default discount
                if ( Woo_Custom_Installments_Init::get_setting('product_price_discount_method') === 'percentage' ) {
                    $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( $cart_item_total, Woo_Custom_Installments_Init::get_setting( 'discount_main_price' ) );
                } else {
                    $custom_price = $cart_item_total - Woo_Custom_Installments_Init::get_setting('discount_main_price');
                }
            }

            // Add the discounted price to total discount price
            $total_discount_price += $custom_price;
        } else {
            // If discount is disabled for this item, add its subtotal to the total price without any discount
            $total_discount_price += $cart_item_total;
        }
    }

    // Check if shipping value should be included in discounts
    if ( Woo_Custom_Installments_Init::get_setting('include_shipping_value_in_discounts') === 'yes' ) {
        // Add shipping cost to the total discount price
        $shipping_cost = WC()->cart->get_shipping_total();
        $total_discount_price += $shipping_cost;
    }

    // Output the total discount price in the cart
    ?>
    <div class="woo-custom-installments-order-discount-cart">
        <tr>
            <span class="table-header-text">
                <th><?php echo apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), Woo_Custom_Installments_Init::get_setting('text_after_price') ) ); ?></th>
            </span>
            <span class="discount-price">
                <td data-title="<?php echo esc_attr( apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), Woo_Custom_Installments_Init::get_setting('text_after_price') ) ) ); ?>"><?php echo wc_price( $total_discount_price ); ?></td>
            </span>
        </tr>
    </div>
    <?php
  }

}

new Woo_Custom_Installments_Frontend_Template();