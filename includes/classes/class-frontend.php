<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\Helpers;
use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Display elements on front-end
 *
 * @since 1.0.0
 * @version 5.0.0
 * @package MeuMouse.com
 */
class Frontend {

  public static $count = 0;
  public static $instance;

  /**
   * Run class
   * 
   * @since 4.5.0
   * @return Frontend class
   */
  public static function get_instance() {
      if ( null === self::$instance ) {
          self::$instance = new self();
      }

      return self::$instance;
  }


  /**
   * Construct function
   * 
   * @since 1.0.0
   * @version 5.0.0
   * @return void
   */
  public function __construct() {
    if ( Init::get_setting('enable_installments_all_products') === 'yes' ) {
      // change woocommerce price template
      add_filter( 'woocommerce_locate_template', array( $this, 'woo_custom_installments_locate_templates' ), 10, 3 );

      add_filter( 'woocommerce_get_price_html', array( $this, 'woo_custom_installments_group' ), 999, 2 );

      add_action( 'woocommerce_cart_totals_before_order_total', array( __CLASS__, 'display_discount_on_cart' ) );

      // get hook to display accordion or popup payment form in single product page
      if ( Init::get_setting('hook_payment_form_single_product') === 'before_cart' ) {
        add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'full_installment' ), 10 );
      } elseif ( Init::get_setting( 'hook_payment_form_single_product' ) === 'after_cart' ) {
          add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'full_installment' ), 10 );
      } elseif ( Init::get_setting( 'hook_payment_form_single_product' ) === 'custom_hook' ) {
        add_action( Init::get_setting('set_custom_hook_payment_form'), array( $this, 'full_installment' ), 10 );
      } else {
          remove_action( 'woocommerce_after_add_to_cart_form', array( $this, 'full_installment' ), 10 );
          remove_action( 'woocommerce_before_add_to_cart_form', array( $this, 'full_installment' ), 10 );
      }

      /**
       * Remove price range
       * 
       * @since 2.6.0
       */
      if ( Init::get_setting('remove_price_range') === 'yes' && License::is_valid() ) {
        add_filter( 'woocommerce_variable_price_html', array( $this, 'starting_from_variable_product_price' ), 10, 2 );
        add_filter( 'woocommerce_variable_sale_price_html', array( $this, 'starting_from_variable_product_price' ), 10, 2 );
      }

      /**
       * Add text after price
       * 
       * @since 2.8.0
       */
      if ( Init::get_setting('custom_text_after_price') === 'yes' ) {
        add_filter( 'woocommerce_get_price_html', array( $this, 'add_custom_text_after_price' ), 10, 1 );
      }

      // display discount per quantity message if parent option is activated
      if ( Init::get_setting('enable_functions_discount_per_quantity') === 'yes' && Init::get_setting('message_discount_per_quantity') === 'yes' ) {
        add_action( 'woocommerce_single_product_summary', array( $this, 'display_message_discount_per_quantity' ) );
        add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'display_message_discount_per_quantity' ) );
      }
    }
  }


  /**
   * Change WooCommerce single product price template
   * 
   * @since 4.5.0
   * @param string $template | The full path of the current template being loaded by WooCommerce
   * @param string $template_name | The name of the template being loaded (e.g. 'single-product/price.php')
   * @param string $template_path | WooCommerce template directory path
   * @return string $template | The full path of the template to be used by WooCommerce, which can be the original or a customized one
   */
  public function woo_custom_installments_locate_templates( $template, $template_name, $template_path ) {
    global $woocommerce;

    // Default template path
    $_template = $template;

    if ( ! $template_path ) {
        $template_path = $woocommerce->template_url;
    }

    // Path to plugin template directory
    $plugin_path  = WOO_CUSTOM_INSTALLMENTS_DIR . '/templates/';

    if ( file_exists( $plugin_path . $template_name ) ) {
        $template = $plugin_path . $template_name;
    }

    return $template;
  }
  

  /**
   * Calculate installments
   * 
   * @since 1.0.0
   * @version 4.5.2
   * @param array $return
   * @param mixed $price | Product price or false
   * @param mixed $product | Product ID or false
   * @param bool $echo
   * @return string
  */
  public function set_values( $return, $price = false, $product = false, $echo = true ) {
    // check if is product
    if ( ! $product ) {
      return $return;
    }

    $installments_info = array();
    $custom_fee = maybe_unserialize( get_option('woo_custom_installments_custom_fee_installments') );

    if ( ! $price ) {
      global $product;
      $args = array();

      if ( ! $product ) {
          return $return;
      }

      if ( $product->is_type( 'variable', 'variation' ) && ! Helpers::variations_has_same_price( $product ) ) {
          $args['price'] = $product->get_variation_price('max');
      }

      $price = wc_get_price_to_display( $product, $args );
    }

    $price = apply_filters('woo_custom_installments_set_values_price', $price, $product);

    // check if product is different of available
    if ( ! Helpers::is_available( $product ) ) {
        return false;
    }

    // get max quantity of installments
    $installments_limit = Init::get_setting('max_qtd_installments');

    // get all installments options till the limit
    for ( $i = 1; $i <= $installments_limit; $i++ ) {
        $interest_rate = 0; // start without fee

        // check if option activated is set_fee_per_installment, else global fee is defined
        if ( Init::get_setting('set_fee_per_installment') === 'yes' ) {
            $interest_rate = isset( $custom_fee[$i]['amount'] ) ? floatval( $custom_fee[$i]['amount'] ) : 0;
        } else {
            $interest_rate = Init::get_setting('fee_installments_global');
        }

        // If interest be zero, use one formula for all
        if ( 0 == $interest_rate ) {
            $installments_info[] = $this->get_installment_details_without_interest( $price, $i );
            continue;
        }

        // get max quantity of installments without fee
        $max_installments_without_fee = Init::get_setting('max_qtd_installments_without_fee');

        // set the installments without fee
        if ( $i <= $max_installments_without_fee ) {
            // return values for this installment
            $installments_info[] = $this->get_installment_details_without_interest( $price, $i );
        } else {
            $installments_info[] = $this->get_installment_details_with_interest( $price, $interest_rate, $i );
        }
    }

    // get min value price of installment
    $min_installment_value = Init::get_setting('min_value_installments');

    foreach ( $installments_info as $index => $installment ) {
      if ( $installment['installment_price'] < $min_installment_value && 0 < $index ) {
          unset( $installments_info[$index] );
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
      return ( 0 === self::$count );
    }
    
    return false;
  }


  /**
   * Display best installments
   * 
   * @since 2.1.0
   * @version 5.0.0
   * @param object $product | Product object
   * @return string
  */
  public function display_best_installments( $product ) {
    // check if option __disable_installments in product is true
    $disable_installments_in_product = get_post_meta( $product->get_id(), '__disable_installments', true ) === 'yes';
  
    // check if product is variation e get the id of parent product
    if ( $product && $product->is_type( 'variation', 'variable' ) ) {
        $parent_id = $product->get_parent_id();
        $disable_installments_in_parent = get_post_meta( $parent_id, '__disable_installments', true ) === 'yes';
    } else {
        $disable_installments_in_parent = false;
    }
  
    // check if '__disable_installments' is true for the simple or variation products
    if ( $disable_installments_in_product || $disable_installments_in_parent || ! $product->is_purchasable() ) {
      return;
    }

    $display_single_product = Init::get_setting('hook_display_best_installments');
    $args = array();
  
    if ( $product->is_type( 'variable', 'variation' ) && ! Helpers::variations_has_same_price( $product ) ) {
      $args['price'] = $product->get_variation_price('min');
    }
  
    $installments = $this->set_values( $display_single_product, wc_get_price_to_display( $product, $args ), $product, false );
    $best_installments = '';
  
    if ( Init::get_setting('get_type_best_installments') === 'best_installment_without_fee' ) {
        $best_installments = $this->best_without_interest( $installments, $product );
    } elseif ( Init::get_setting('get_type_best_installments') === 'best_installment_with_fee' ) {
        $best_installments = $this->best_with_interest( $installments, $product );
    } elseif ( Init::get_setting('get_type_best_installments') === 'both' ) {
        $best_installments = $this->best_without_interest( $installments, $product );
        $best_installments .= $this->best_with_interest( $installments, $product );
    }
  
    $html = ' <span class="woo-custom-installments-card-container">';
    $html .= $best_installments;
    $html .= ' </span>';

    // Check display conditions
    if ( Init::get_setting('hook_display_best_installments') === 'display_loop_and_single_product'
    || ( Init::get_setting('hook_display_best_installments') === 'only_single_product' && is_product() )
    || ( Init::get_setting('hook_display_best_installments') === 'only_loop_products' && is_archive() ) ) {
      return $html;
    }
  }


  /**
   * Pix flag
   * 
   * @since 2.0.0
   * @version 4.5.2
   * @return string
   */
  public function woo_custom_installments_pix_flag() {
    if ( ! is_product() ) {
      return;
    }

    global $product;

    $price = wc_get_price_to_display( $product );
    $economy_pix_active = Init::get_setting('enable_economy_pix_badge') === 'yes';
    $pix_flag = '';
    
    if ( Init::get_setting('enable_pix_method_payment_form') === 'yes' ) {
      $pix_flag .= '<div class="woo-custom-installments-pix-section">';
        $pix_flag .= '<h4 class="pix-method-title">'. Init::get_setting('text_pix_container') .'</h4>';
        $pix_flag .= '<div class="pix-method-container">';
          $pix_flag .= '<span class="pix-method-name">'. sprintf( esc_html__( 'Pix: %s', 'woo-custom-installments' ), wc_price( Calculate_Values::get_discounted_price( $product, 'main' ) ) ) .'</span>';
          
          if ( Init::get_setting('enable_instant_approval_badge') === 'yes' ) {
            $pix_flag .= '<span class="instant-approval-badge">'. esc_html__( 'Aprovação imediata', 'woo-custom-installments' ) .'</span>';
          }

        $pix_flag .= '</div>';

        $get_pix_economy_value = self::calculate_pix_economy( $product );

        if ( $economy_pix_active && $get_pix_economy_value > 0 ) {
          $pix_flag .= '<div class="container-badge-icon pix-flag pix-info instant-approval-badge">';
        } else {
          $pix_flag .= '<div class="container-badge-icon pix-flag pix-info">';
        }

        if ( $get_pix_economy_value ) {
          $pix_flag .= '<i class="pix-icon-badge fa-brands fa-pix"></i>';
          
          if ( $economy_pix_active ) {
            $pix_flag .= '<div class="economy-pix-info">';
              $pix_flag .= $this->economy_pix_badge( $product );
            $pix_flag .= '</div>';
          }
        }

        $pix_flag .= '</div>';
      $pix_flag .= '</div>';
    }

    return $pix_flag;
  }


  /**
   * Ticket flag
   * 
   * @since 2.0.0
   * @version 5.0.0
   * @param int $product | Product ID
   * @return string
   */
  public function woo_custom_installments_ticket_flag( $product_id = false ) {
    if ( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }

    $ticket_flag = '';
    
    if ( Init::get_setting('enable_ticket_method_payment_form') === 'yes' ) {
      $ticket_flag .= '<div class="woo-custom-installments-ticket-section">';
        $ticket_flag .= '<h4 class="ticket-method-title">'. esc_html__( 'Cobranças:', 'woo-custom-installments' ) .'</h4>';

        $ticket_flag .= '<span class="ticket-method-name">'. sprintf( __( '%s %s' ), Init::get_setting('text_ticket_container'), wc_price( Calculate_Values::get_discounted_price( $product, 'ticket' ) ) ) .'</span>';

        $ticket_flag .= '<div class="ticket-method-container">';
          $ticket_flag .= '<span class="ticket-instructions">'. Init::get_setting('text_instructions_ticket_container') .'</span>';
        $ticket_flag .= '</div>';

        $ticket_flag .= '<div class="container-badge-icon ticket-flag">';
          $ticket_flag .= '<img class="size-badge-icon" src="'. WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/boleto-badge.svg"/>';
        $ticket_flag .= '</div>';

      $ticket_flag .= '</div>';
    }

    return $ticket_flag;
  }


  /**
   * Generate card flags
   * 
   * @since 2.0.0
   * @version 5.0.0
   * @param string $card_type | credit-card or debit-card
   * @param string $type | credit or debit
   * @return string
   */
  public function generate_card_flags( $card_type, $type ) {
    $default_flags = array(
        'mastercard' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/mastercard-badge.svg',
        'visa' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/visa-badge.svg',
        'elo' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/elo-badge.svg',
        'hipercard' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/hipercard-badge.svg',
        'diners_club' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/diners-club-badge.svg',
        'discover' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/discover-badge.svg',
        'american_express' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/american-express-badge.svg',
        'paypal' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/paypal-badge.svg',
        'stripe' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/stripe-badge.svg',
        'mercado_pago' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/mercado-pago-badge.svg',
        'pagseguro' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/pagseguro-badge.svg',
        'pagarme' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/pagarme-badge.svg',
        'cielo' => WOO_CUSTOM_INSTALLMENTS_ASSETS . 'front/img/cielo-badge.svg',
    );

    $flags = apply_filters( 'woo_custom_installments_card_flags', $default_flags, $card_type, $type );
    $card_flags = '';
    $options = get_option('woo-custom-installments-setting');

    foreach( $flags as $key => $flag_url ) {
        if ( isset( $options['enable_' . $key . '_flag_' . $type] ) && $options['enable_' . $key . '_flag_' . $type] === 'yes' ) {
            $card_flags .= '<div class="container-badge-icon ' . esc_attr( $card_type ) . ' ' . esc_attr( $key ) . '-flag">';
            $card_flags .= '<img class="size-badge-icon" src="' . esc_url( $flag_url ) . '"/>';
            $card_flags .= '</div>';
        }
    }

    return $card_flags;
  }


  /**
   * Credit card flags
   * 
   * @since 2.0.0
   * @version 5.0.0
   * @return string
   */
  public function woo_custom_installments_credit_card_flags() {
      $credit_card_flag = '';

      if ( Init::get_setting('enable_credit_card_method_payment_form') === 'yes' ) {
          $credit_card_flag .= '<div class="woo-custom-installments-credit-card-section">';
          $credit_card_flag .= '<h4 class="credit-card-method-title">' . Init::get_setting('text_credit_card_container') . '</h4>';
          
          if ( Init::get_setting('enable_instant_approval_badge') === 'yes' ) {
              $credit_card_flag .= '<div class="credit-card-method-container">';
                $credit_card_flag .= '<span class="instant-approval-badge">' . esc_html__('Aprovação imediata', 'woo-custom-installments') . '</span>';
              $credit_card_flag .= '</div>';
          }

          $credit_card_flag .= '<div class="credit-card-container-badges">';
          $credit_card_flag .= $this->generate_card_flags( 'credit-card', 'credit' );
          $credit_card_flag .= '</div>';
          $credit_card_flag .= '</div>';
      }

      return $credit_card_flag;
  }


  /**
   * Debit card flags
   * 
   * @since 2.0.0
   * @version 5.0.0
   * @return string
   */
  public function woo_custom_installments_debit_card_flags() {
    $debit_card_flag = '';

    if ( Init::get_setting('enable_debit_card_method_payment_form') === 'yes') {
        $debit_card_flag .= '<div class="woo-custom-installments-debit-card-section">';
        $debit_card_flag .= '<h4 class="debit-card-method-title">' . Init::get_setting('text_debit_card_container') . '</h4>';
        
        if ( Init::get_setting('enable_instant_approval_badge') === 'yes' ) {
            $debit_card_flag .= '<div class="debit-card-method-container">';
              $debit_card_flag .= '<span class="instant-approval-badge">' . esc_html__('Aprovação imediata', 'woo-custom-installments') . '</span>';
            $debit_card_flag .= '</div>';
        }

        $debit_card_flag .= '<div class="debit-card-container-badges">';
          $debit_card_flag .= $this->generate_card_flags( 'debit-card', 'debit' );
        $debit_card_flag .= '</div>';

        $debit_card_flag .= '</div>';
    }

    return $debit_card_flag;
  }


  /**
   * Replament strings in front-end
   * 
   * @since 1.3.0
   * @version 4.5.0
   * @param array $values | Value for replace
   * @return array
   */
  public function strings_to_replace( $values ) {
    return apply_filters( 'woo_custom_installments_strings_to_replace', array(
      '{{ parcelas }}' => $values['installments_total'],
      '{{ valor }}' => wc_price( $values['installment_price'] ),
      '{{ total }}' => wc_price( $values['final_price'] ),
      '{{ juros }}' => $this->get_fee_info( $values ),
    ));
  }


  /**
   * Replace range price for "A partir de"
   * 
   * @since 2.4.0
   * @version 4.5.0
   * @param string $price | Product price
   * @param object $product | Product object
   * @return string
   */
  public function starting_from_variable_product_price( $price, $product ) {
    if ( ! Helpers::variations_has_same_price( $product ) ) {
      $text_initial = ! empty( Init::get_setting('text_initial_variables') ) ? '<span class="woo-custom-installments-starting-from">' . Init::get_setting('text_initial_variables') . '</span>' : '';
      $min_price = $product->get_variation_price( 'min', true );

      $price = $text_initial . wc_price( $min_price );
    }

    return $price;
  }


  /**
   * Add custom text after price
   * 
   * @since 2.8.0
   * @param string $price | Product price
   * @return string
   */
  public function add_custom_text_after_price( $price ) {
    $price .= '<span class="woo-custom-installments-text-after-price">'. Init::get_setting('custom_text_after_price_front') .'</span>';

    return $price;
  }


  /**
   * Display group elements
   * 
   * @since 2.0.0
   * @version 4.5.2
   * @param string $price | Product price
   * @param object $product | Product object
   * @return string
  */
  public function woo_custom_installments_group( $price, $product ) {
    $product_id = Helpers::get_product_id_from_post();
    $product = wc_get_product( $product_id );

    if ( $product === false || ! isset( $product ) ) {
      global $product;
    }

    $price = apply_filters( 'woo_custom_installments_adjusted_price', $price, $product );

    if ( strpos( $price, 'woo-custom-installments-group' ) !== false ) {
        return $price;
    }

    $html = '<div class="woo-custom-installments-group';

    if ( $product && $product->is_type('variable', 'variation') && ! Helpers::variations_has_same_price( $product ) ) {
        $html .= ' variable-range-price';
    }

    $html .= '">';

    // Original price
    $html .= '<span class="woo-custom-installments-price original-price">' . $price . '</span>';

    $html .= $this->discount_main_price_single( $product );
    $html .= $this->discount_ticket_badge( $product );
    $html .= $this->display_best_installments( $product );
    $html .= $this->economy_pix_badge( $product );

    $html .= '</div>';

    return $html;
  }


  /**
   * Discount product main price
   * 
   * @since 3.6.0
   * @version 4.5.1
   * @param object $product | Product object
   * @return string $html
   */
  public function discount_main_price_single( $product ) {
    if ( Init::get_setting('display_discount_price_hook') === 'hide' || Init::get_setting('enable_all_discount_options') !== 'yes' ) {
      return;
    }

    $html = '<span class="woo-custom-installments-offer">';

    if ( ! empty( Init::get_setting('icon_main_price') ) ) {
        $html .= '<i class="wci-icon-main-price '. Init::get_setting('icon_main_price') .'"></i>';
    }

    // check if exists text before price for display
    if ( ! empty( Init::get_setting('text_before_price') ) ) {
        $html .= '<span class="discount-before-price">'. Init::get_setting('text_before_price') .'</span>';
    }

    $html .= '<span class="discounted-price">'. wc_price( Calculate_Values::get_discounted_price( $product, 'main' ) ) .'</span>';

    // check if exists text after price for display
    if ( ! empty( Init::get_setting('text_after_price') ) ) {
        $html .= '<span class="discount-after-price">'. Init::get_setting('text_after_price') .'</span>';
    }

    $html .= '</span>';

    // Check display conditions
    if ( Init::get_setting('display_discount_price_hook') === 'display_loop_and_single_product'
    || ( Init::get_setting('display_discount_price_hook') === 'only_single_product' && is_product() )
    || ( Init::get_setting('display_discount_price_hook') === 'only_loop_products' && is_archive() ) ) {
      if ( $product->get_price() > 0 ) {
        return $html;
      }
    }
  }


  /**
   * Create a ticket discount badge
   * 
   * @since 2.8.0
   * @version 4.5.0
   * @param object $product | Product object
   * @return string $html
   */
  public function discount_ticket_badge( $product ) {
    $html = '<span class="woo-custom-installments-ticket-discount">';

    if ( ! empty( Init::get_setting('ticket_discount_icon') ) ) {
        $html .= '<i class="wci-icon-ticket-discount '. Init::get_setting('ticket_discount_icon') .'"></i>';
    }

    // check if exists text before price for display
    if ( ! empty( Init::get_setting('text_before_discount_ticket') ) ) {
        $html .= '<span class="discount-before-discount-ticket">'. Init::get_setting('text_before_discount_ticket') .'</span>';
    }

    $html .= '<span class="discounted-price">'. wc_price( Calculate_Values::get_discounted_price( $product, 'ticket' ) ) .'</span>';

    // check if exists text after price for display
    if ( ! empty( Init::get_setting('text_after_discount_ticket') ) ) {
        $html .= '<span class="discount-after-discount-ticket">'. Init::get_setting('text_after_discount_ticket') .'</span>';
    }

    $html .= '</span>';

    if ( Init::get_setting('enable_ticket_method_payment_form') === 'yes' && Init::get_setting('enable_ticket_discount_main_price') === 'yes' ) {
      // Check display conditions
      if ( Init::get_setting('display_discount_ticket_hook') === 'global'
      || ( Init::get_setting('display_discount_ticket_hook') === 'only_single_product' && is_product() )
      || ( Init::get_setting('display_discount_ticket_hook') === 'only_loop_products' && is_archive() ) ) {
        return $html;
      }
    }
  }


  /**
   * Calculate Pix economy value
   *
   * @since 4.5.0
   * @version 5.0.0
   * @param WC_Product $product | Product object
   * @return float | Economy value
   */
  public static function calculate_pix_economy( $product ) {
    if ( $product === false || ! isset( $product ) ) {
      global $product;
    }

    $price = $product->get_price();
    $custom_price = Calculate_Values::get_discounted_price( $product, 'main' );
    $economy = (float) $price - (float) $custom_price;

    return apply_filters( 'woo_custom_installments_economy_pix_price', $economy );
  }
  

  /**
   * Create a economy Pix badge
   * 
   * @since 3.6.0
   * @version 5.0.0
   * @param WC_Product $product | Product object
   * @return string
   */
  public function economy_pix_badge( $product ) {
    if ( Init::get_setting('enable_economy_pix_badge') !== 'yes' || Init::get_setting('enable_all_discount_options') !== 'yes' ) {
      return;
    }

    if ( $product === false || ! isset( $product ) ) {
      global $product;
    }

    $economy_value = self::calculate_pix_economy( $product );

    if ( $economy_value <= 0 ) {
        return '';
    }

    // Check if exists text before price for display
    $text_economy_pix_badge = Init::get_setting('text_economy_pix_badge');

    if ( ! empty( $text_economy_pix_badge ) ) {
        // Checks if string contains %s
        if ( strpos( $text_economy_pix_badge, '%s' ) !== false ) {
            $formatted_text = sprintf( $text_economy_pix_badge, wc_price( $economy_value ) );
        } else {
            // If %s is missing, use the original text
            $formatted_text = $text_economy_pix_badge;
        }

        $html = '<span class="woo-custom-installments-economy-pix-badge">';
        $html .= sprintf( __( '<i class="wci-icon-economy-pix %s"></i>' ), Init::get_setting('economy_pix_icon_class') );
        $html .= '<span class="discount-before-economy-pix">' . $formatted_text . '</span>';
        $html .= '</span>';

        // Check display conditions
        if ( Init::get_setting('display_economy_pix_hook') === 'global'
        || ( Init::get_setting('display_economy_pix_hook') === 'only_single_product' && is_product() )
        || ( Init::get_setting('display_economy_pix_hook') === 'only_loop_products' && is_archive() ) ) {
          return $html;
        }
    }

    return '';
  }


  /**
   * Format display prices
   * 
   * @since 1.0.0
   * @return string
   */
  private function formatting_display( $installments, $return, $echo = true ) {
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
   * @since 1.0.0
   * @version 5.0.0
   * @return string
  */
  public function best_without_interest( $installments, $product ) {
    if ( $product === false || ! isset( $product ) ) {
      global $product;
    }

    // check if $installments is different of array or empty $installments or product price is zero
    if ( ! is_array( $installments ) || empty( $installments ) || $product->get_price() <= 0 ) {
      return;
    }

    $hook = self::hook();

    foreach ( $installments as $key => $installment ) {
      if ( 'no-fee' != $installment['class'] ) {
        unset( $installments[$key] );
      }
    }

    // get end installment without fee loop foreach
    $best_without_interest = end( $installments );

    if ( false === $best_without_interest ) {
      return;
    }

    if ( 'main_price' == $hook ) {
      $text = Init::get_setting('text_display_installments_single_product');
    } else {
      $text = Init::get_setting('text_display_installments_loop');
    }

    $find = array_keys( $this->strings_to_replace( $best_without_interest ) );
    $replace = array_values( $this->strings_to_replace( $best_without_interest ) );
    $text = str_replace( $find, $replace, $text );

    $html = '<span class="woo-custom-installments-details-without-fee">';

      if ( ! empty( Init::get_setting('icon_best_installments') ) ) {
        $html .= '<i class="wci-icon-best-installments '. Init::get_setting('icon_best_installments') .'"></i>';
      }

      $html .= '<span class="woo-custom-installments-details best-value ' . $best_without_interest['class'] . '">' . apply_filters( 'woo_custom_installments_best_no_fee_' . $hook, $text, $best_without_interest, $product ) . '</span>';
    $html .= '</span>';

    return $html;
  }

  /**
   * Get best installment with interest
   * 
   * @since 1.0.0
   * @version 5.0.0
   * @return string
  */
  public function best_with_interest( $installments, $product ) {
    if ( $product === false || ! isset( $product ) ) {
      global $product;
    }

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
      $text = Init::get_setting('text_display_installments_single_product');
    } else {
      $text = Init::get_setting('text_display_installments_loop');
    }

    $find = array_keys( $this->strings_to_replace( $best_with_interest ) );
    $replace = array_values( $this->strings_to_replace( $best_with_interest ) );
    $text = str_replace( $find, $replace, $text );

    $html = '<span class="woo-custom-installments-details-with-fee">';

      if ( ! empty( Init::get_setting('icon_best_installments') ) ) {
        $html .= '<i class="wci-icon-best-installments '. Init::get_setting('icon_best_installments') .'"></i>';
      }

      $html .= '<span class="best-value'. $best_with_interest['class'] .'">'. apply_filters( 'woo_custom_installments_best_with_fee_'. $hook, $text, $best_with_interest, $product ) . '</span>';
    $html .= '</span>';

    return $html;
  }


  /**
   * Get fee info
   * 
   * @since 1.0.0
   * @param array $installments | Array installments
   * @return string
  */
  public function get_fee_info( $installment ) {
    $hook = self::hook();
    $text = ( $installment['interest_fee'] ) ? '' . Init::get_setting('text_with_fee_installments') : ' '. Init::get_setting('text_without_fee_installments');
    
    return apply_filters( 'woo_custom_installments_fee_label', $text, $installment['interest_fee'], $hook );
  }


  /**
   * Save array with all details of installments
   * 
   * @since 1.0.0
   * @return string
  */
  public function set_installment_info( $price, $final_price, $interest_fee, $class, $i ) {
    return apply_filters( 'woo_custom_installments_installment_info', array(
      'installment_price' => $price,
      'installments_total' => $i,
      'final_price' => $final_price,
      'interest_fee' => $interest_fee,
      'class' => $class,
    ));
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
    $price = Calculate_Values::calculate_installment_no_fee( $total, $i );
    $final_price = Calculate_Values::calculate_final_price( $price, $i );
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
    $price = Calculate_Values::calculate_installment_with_fee( $total, $fee, $i );
    $final_price = Calculate_Values::calculate_final_price( $price, $i );
    $fee = true;
    $class = 'fee-included';
    $installment_info = $this->set_installment_info( $price, $final_price, $fee, $class, $i );

    return $installment_info;
  }


  /**
   * Generate table of installments
   * 
   * @since 2.0.0
   * @version 5.0.0
   * @param object $product | Product object
   * @return string
  */
  public function generate_installments_table( $product ) {
    if ( ! $product ) {
        return;
    }

    if ( $product && $product->is_type( 'variable', 'variation' ) && ! Helpers::variations_has_same_price( $product ) ) {
      $args = array();
      $args['price'] = $product->get_variation_price('max');
      $price = wc_get_price_to_display( $product, $args );
    } else {
      $price = wc_get_price_to_display( $product );
    }
    
    $all_installments = $this->set_values( 'all', $price, $product, false );

    if ( ! $all_installments ) {
        return;
    }

    // Installments table
    $table = '<h4 class="installments-title">'. Init::get_setting('text_table_installments') .'</h4>';
    $table .= '<div id="table-installments">';
      $table .= '<table class="table table-hover woo-custom-installments-table">';
        $table .= '<tbody data-default-text="'. Init::get_setting('text_display_installments_payment_forms') .'">';
          foreach ( $all_installments as $installment ) {
              $find = array_keys( $this->strings_to_replace( $installment ) );
              $replace = array_values( $this->strings_to_replace( $installment ) );
              $final_text = str_replace( $find, $replace, Init::get_setting('text_display_installments_payment_forms') );

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
   * Format display popup or accordion
   * 
   * @since 2.0.0
   * @version 5.0.0
   * @param bool $product_id | Check product ID
   * @return string
  */
  public function full_installment( $product_id = false ) {
    if ( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }

    if ( ! $product || ! is_product() ) {
      return;
    }

    $product = apply_filters( 'woo_custom_installments_full_installment_product', $product );
    $installments = array(); 
    $all_installments = array();

    // check if product is variation e get your parent id
    if ( $product->is_type('variation') ) {
      $disable_installments = get_post_meta( $product->get_parent_id(), '__disable_installments', true ) === 'yes';
    } else {
      $disable_installments = get_post_meta( $product->get_id(), '__disable_installments', true ) === 'yes';
    }

    // check if '__disable_installments' is true or not purchasable and hide for the simple or variation products
    if ( $disable_installments === 'yes' || ! $product->is_purchasable() ) {
        return;
    }

    /**
     * Hook for display custom content before installments container
     * 
     * @since 4.1.0
     */
    do_action('woo_custom_installments_before_installments_container');
    
    if ( Init::get_setting( 'display_installment_type' ) === 'accordion' ) {
      echo apply_filters( 'woo_custom_installments_table', $this->accordion_container( $product_id ), $all_installments );
    } elseif ( Init::get_setting( 'display_installment_type' ) === 'popup' ) {
        echo apply_filters( 'woo_custom_installments_table', $this->popup_trigger( $product_id ), $all_installments );
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
   * @version 5.0.0
   * @param object $product | Product ID
   * @return void
   */
  public function popup_trigger( $product_id ) {
    if ( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }

    ?>
    <button type="button" class="wci-open-popup">
      <span class="open-popup-text"><?php echo Init::get_setting('text_button_installments'); ?></span>
    </button>

    <div class="wci-popup-container">
      <div class="wci-popup-content">
        <div class="wci-popup-header">
          <h5 class="wci-popup-title"><?php echo Init::get_setting('text_container_payment_forms'); ?></h5>
          <button type="button" class="btn-close wci-close-popup" aria-label="<?php echo esc_html__( 'Fechar', 'woo-custom-installments' ) ?>"></button>
        </div>

        <?php
        /**
         * Hook for display custom content inside accordion container
         * 
         * @since 4.1.0
         */
        do_action('woo_custom_installments_popup_header'); ?>

        <div id="wci-popup-body">
          <?php

          if ( License::is_valid() ) {
            echo $this->woo_custom_installments_pix_flag();
            echo $this->woo_custom_installments_credit_card_flags();
            echo $this->woo_custom_installments_debit_card_flags();
            echo $this->woo_custom_installments_ticket_flag( $product );
          }

          echo $this->generate_installments_table( $product ); ?>
        </div>

        <?php
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
   * @version 5.0.0
   * @param int $product | Product ID
   * @return void
   */
  public function accordion_container( $product_id ) {
    if ( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }

    ?>
    <div id="wci-accordion-installments" class="accordion">
      <div class="wci-accordion-item">
        <button type="button" class="wci-accordion-header"><?php echo Init::get_setting('text_button_installments'); ?></button>

        <div class="wci-accordion-content">

          <?php
          /**
           * Hook for display custom content inside header accordion
           * 
           * @since 4.1.0
           */
          do_action('woo_custom_installments_accordion_header');

          if ( License::is_valid() ) {
            echo $this->woo_custom_installments_pix_flag();
            echo $this->woo_custom_installments_credit_card_flags();
            echo $this->woo_custom_installments_debit_card_flags();
            echo $this->woo_custom_installments_ticket_flag( $product_id );
          }
          
          echo $this->generate_installments_table( $product ); ?>
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
   * Display menssage in elegible products for discount per quantity
   * 
   * @since 2.8.0
   * @version 5.0.0
   * @param int $product_id | Product ID
   * @return void
   */
  public function display_message_discount_per_quantity( $product_id ) {
    if ( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }

    if ( ! $product ) {
      return;
    }

    $product_id = $product->get_id();
    $current_quantity = $product->get_stock_quantity();
    $enable_global_discount = Init::get_setting('enable_discount_per_quantity_method') === 'global';
    $enable_product_discount = get_post_meta( $product_id, 'enable_discount_per_quantity', true );
    
    if ( $enable_global_discount || $enable_product_discount ) {
      if ( $enable_global_discount ) {
          $method = Init::get_setting('discount_per_quantity_method');
          $value = Init::get_setting('value_for_discount_per_quantity');
          $minimum_quantity = Init::get_setting('set_quantity_enable_discount');
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

      $text_discount_per_quantity_message = Init::get_setting('text_discount_per_quantity_message');

      if ( ! empty( $text_discount_per_quantity_message ) ) {
          // Count the number of %s in the string
          $placeholders_count = substr_count( $text_discount_per_quantity_message, '%s' );

          // Ensure that the number of arguments passed to sprintf matches the number of %s
          if ( $placeholders_count === 2 ) {
              $formatted_text = sprintf( $text_discount_per_quantity_message, $minimum_quantity, $discount_message );
          } else {
              // If the amount of %s does not match, use the original text
              $formatted_text = $text_discount_per_quantity_message;
          }

          echo '<div class="woo-custom-installments-discount-per-quantity-message">';
          echo '<i class="fa-solid fa-circle-exclamation"></i>';
          echo '<span>' . $formatted_text . '</span>';
          echo '</div>';
      }
    }
  }


  /**
   * Display discount in cart page
   * 
   * @since 2.6.0
   * @version 4.5.2
   * @return string
   */
  public static function display_discount_on_cart() {
    if ( Init::get_setting('enable_all_discount_options') !== 'yes' || Init::get_setting('display_installments_cart') !== 'yes' ) {
        return;
    }

    $total_cart_value = WC()->cart->get_cart_contents_total() + WC()->cart->get_shipping_total();
    $total_discount = Calculate_Values::calculate_total_discount( WC()->cart, Init::get_setting('include_shipping_value_in_discounts') === 'yes' ); ?>

    <tr>
      <th><?php echo apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), Init::get_setting('text_after_price') ) ); ?></th>
      <td data-title="<?php echo esc_attr( apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), Init::get_setting('text_after_price') ) ) ); ?>"><?php echo wc_price( $total_cart_value - $total_discount ); ?></td>
    </tr>
    <?php
  }
}