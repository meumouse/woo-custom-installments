<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit; }


/**
 * Display structures on front-end
 *
 * @package  MeuMouse.com
 * @version  1.0.0
 */

class Woo_Custom_Installments_Frontend_Template extends Woo_Custom_Installments_Init {

  public static $count = 0;

  public function __construct() {
    parent::__construct();
    $this->init();
    $this->change_schema();
  }


  // Apply filters
  private function init() {
    add_action( 'template_redirect', array( $this, 'display_table' ), 10 );
    add_filter( 'woocommerce_get_price_html', array( $this, 'woocommerce_get_price_html' ), 999, 2 );
    add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'clear_product_function' ), 9999 );
    add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_discount_on_cart' ) );

    // Manual handler for loop count
    add_shortcode( 'woo_custom_installments_open_single_product', array( __CLASS__, 'open_single_product' ) );
    add_shortcode( 'woo_custom_installments_close_single_product', array( __CLASS__, 'close_single_product' ) );
    add_shortcode( 'woo_custom_installments_modal', array( $this, 'render_full_installment_shortcode' ) );
  }

  /**
   * Calculate installments
   * 
   * @return string
   *
  */
  protected function set_values( $return, $price = false, $product = false, $echo = true ) {
    $installments_info = array();

    if ( ! $price ) {
      global $product;

      if ( ! $product ) {
        return $return;
      }

      $args = array();

      if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
        $args['price'] = $product->get_variation_price( 'min' );
      }

      $price = wc_get_price_to_display( $product, $args );
    }

    $price = apply_filters( 'woo_custom_installments_set_values_price', $price, $product );

    if ( ! $this->is_available( $product ) ) {
      return false;
    }

    $installments_limit = self::get_installments_limit( $product );

    // get all installments options till the limit
    for ( $i = 1; $i <= $installments_limit; $i++ ) {

      $fee = self::get_fee( $product, $i );

      // If interest be zero, use one formule for all
      if ( 0 == $fee ) {
        $installments_info[] = self::get_installment_details_without_interest( $price, $i );
        continue;
      }

      $max_installment_interest_free = self::get_max_installment_no_fee( $product );

      // set the installments with no fee
      if ( $i <= $max_installment_interest_free ) {
        // return values for this installment
        $installments_info[] = self::get_installment_details_without_interest( $price, $i );
      } else {
        $installments_info[] = self::get_installment_details_with_interest( $price, $fee, $i );
      }

    }

    $min_installment = self::get_min_installment( $product );

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
    if ( 'yes' === self::$change_schema && ! is_admin() ) {
      include_once WOO_CUSTOM_INSTALLMENTS_DIR . '/includes/classes/class-woo-custom-installments-schema.php';
    }
  }


  /**
   * Display installments on loop page
   * 
   * @return string
  */
  public function loop_price() {
    echo '<span class="woo-custom-installments-loop">';
      $this->set_values( $this->woo_custom_installments_display_shop_page );
    echo '</span>';
  }

  /**
   * Define WooCommerce Hooks
   */
  public static function hook() {
    if ( self::is_main_product_price() ) {
      $action = 'main_price';
    }
    
    else {
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


  public static function clear_product_function() {
    self::$count++;
  }

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

    if ( $original_price && apply_filters( 'woo_custom_installments_original_with_credit_card', false ) ) {
      $html .= apply_filters( 'woo_custom_installments_show_original_price_credit_card', $original_price, $product );
    }

    $display_single_product = self::get_single_page_view( $product );
    $woo_custom_installments_display_shop_page   = self::get_shop_page_view( $product );

    $args = array();

    if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
      $args['price'] = $product->get_variation_price( 'min' );
    }

    $display = ( $is_product ) ? $display_single_product : $woo_custom_installments_display_shop_page;
    $price    = $this->set_values( $display, wc_get_price_to_display( $product, $args ), $product, false );

    if ( '' != $price ) {

      $html .= ' <span class="woo-custom-installments-info-container">';
        $html .= $price;
      $html .= ' </span>';

    }

    return $html;
  }

  /**
   * Display payment forms in single product page
   * 
   * @return bool
  */
  public function display_table() {
    global $post;

    if ( $post && 'product' === get_post_type( $post ) && is_singular( get_post_type( $post ) ) ) {
      $product = wc_get_product( $post->ID );
      $display_payment_forms = self::get_table_visibility( $product );
      $table_hook = apply_filters( 'woo_custom_installments_table_hook', 'woocommerce_before_add_to_cart_form', apply_filters( 'woo_custom_installments_table_priority', 100 ), $product );

      switch ( $display_payment_forms ) {
        case 'display_before_cart_form':
          add_action( $table_hook, array( $this, 'render_full_installment' ), 30 );
          break;
        case 'display_product_tabs':
          add_filter( 'woocommerce_product_tabs', array( $this, 'custom_tab' ) );
          break;

        default:
          return;
          break;
      }
    }
  }

  /**
   * Include custom tab, if necessary
   * 
  */
  public function custom_tab( $tabs ) {

    $installment = $this->set_values( 'all', false, false, false );

    if ( ! $installment ) {
      return $tabs;
    }

    $tabs['woo-custom-installments'] = array(
      'title'    => apply_filters( 'woo_custom_installments_tab_name', 'Parcelamento' ),
      'priority' => apply_filters( 'woo_custom_installments_tab_priority', 50 ),
      'callback' => array( $this, 'render_full_installment' )
    );

    return $tabs;
  }


  public function render_full_installment() {
    if ( apply_filters( 'woo_custom_installments_show_new_table', true ) ) {
      $this->full_installment();
    } else {
      $old_functions = new Woo_Custom_Installments_Replace_Functions();
      $old_functions->parcelamento_completo();
    }
  }


  public function render_full_installment_shortcode( $atts = array() ) {
    $atts = shortcode_atts( array(
      'product_id' => false,
    ), $atts, 'woo_custom_installments_table' );

    ob_start();

    if ( apply_filters( 'woo_custom_installments_show_new_table', true ) ) {
      $this->full_installment( $atts['product_id'] );
    } else {
      $old_functions = new Woo_Custom_Installments_Replace_Functions();
      $old_functions->parcelamento_completo( $atts['product_id'] );
    }

    return ob_get_clean();
  }


  /**
   * Format full installments
   * 
   * @return interface
  */
  public function full_installment( $product_id = false ) {
    if ( $product_id ) {
      $product = wc_get_product( $product_id );
    }
    
    else {
      global $product;
    }

    $product = apply_filters( 'woo_custom_installments_full_installment_product', $product );

		if ( ! is_a( $product, 'WC_Product' ) ) {
      $admin_error = '[ADMINS] Nenhum produto selecionado. Isso quer dizer que você está colocando o shortcode/tabela em algum local onde o produto do WooCommerce (nativo) não está disponível.';
		 return current_user_can( 'manage_woocommerce' ) ? $admin_error : '';
		}

    $args = array();

    if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
      $args['price'] = $product->get_variation_price( 'min' );
    }

    $price = wc_get_price_to_display( $product, $args );

    $all_installments = $this->set_values( 'all', $price, $product, false );
    if ( ! $all_installments ) {
      return;
    }

    do_action( 'woo_custom_installments_before_installments_table' );


    $table = '<div id="installments">
    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-installments-modal" data-bs-toggle="modal" data-bs-target="#installmentsModal">Detalhes do parcelamento</button>

    <div class="modal fade" id="installmentsModal" tabindex="-1" aria-labelledby="installmentsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title fs-5" id="installmentsModalLabel">Parcelas:</h3>
            <button type="button" class="btn-close" id="close-installment-modal" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="woo-custom-installments modal-body">

    <table class="table table-hover woo-custom-installments-table">

    <tbody data-default-text="' . self::get_table_formatted_text( $product ) . '">';

      foreach ( $all_installments as $installment ) {
        $find       = array_keys( self::strings_to_replace( $installment ) );
        $replace    = array_values( self::strings_to_replace( $installment ) );
        $final_text = str_replace( $find, $replace, self::get_table_formatted_text( $product ) );

        $table .= '<tr class="' . $installment['class'] . '">';
        $table .= '<th class="first-text">' . $final_text . '</th>';
        $table .= '<th class="final-price">' . wc_price( $installment['final_price'] )  . '</th>';
        $table .= '</tr>';
      }

    $table .= '</tbody></table></div></div></div></div></div>';

    echo apply_filters( 'woo_custom_installments_table', $table, $all_installments );

    do_action( 'woo_custom_installments_after_installments_table' );
  }

  /**
   * Replament strings in front-end
   * 
   * @return array
   */
  public static function strings_to_replace( $values ) {
    return array(
      '{{ parcelas }}' => $values['installments_total'],
      '{{ valor }}' => wc_price( $values['installment_price'] ),
      '{{ total }}' => wc_price( $values['final_price'] ),
      '{{ juros }}' => self::get_fee_info( $values ),
    );
  }


  /**
   * Display one of the price on place regular price
   * 
   * @return string
  */
  public function woocommerce_get_price_html( $price, $product ) {

    if ( ! $this->is_available( $product ) ) {
      return $price;
    }

    $hook = self::hook();
    $html = '';

    $main_price_discount = self::get_main_price_discount( $product );

    if ( apply_filters( 'woo_custom_installments_show_original_price', true, $product, $hook ) ) {
      $html .= $price;
    }

    if ( apply_filters( 'woo_custom_installments_card_price_before_ticket_' . $hook, true ) ) {
      $html .= $this->single_product_price( $product, $price );
    }

    $ticket_visibility = self::get_ticket_visibility( $product );

    if ( 0 < self::get_main_price_discount( $product ) && in_array( $ticket_visibility, array( 'both', $hook ) )
         || 'yes' == self::$always_show_boleto && in_array( $ticket_visibility, array( 'both', $hook ) ) ) {

      $html .= '<span class="woo-custom-installments-offer">';

      $args = array();

      if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
        $html .= apply_filters( 'woo_custom_installments_before_variation_text', '<span class="woo-custom-installments-since-price">' . __( 'A partir de', 'woo-custom-installments' ) . ' </span>' );

        // When $get_variations is false, minor price not return in $product->get_price()
        $args['price'] = $product->get_variation_price( 'min' );
      }

      $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( wc_get_price_to_display( $product, $args ), $main_price_discount, $product );

      $html .= wc_price( $custom_price );

      $html .= ' <span class="woo-custom-installments-value-details">';
      $html .= self::get_text_after_price( $product );
      $html .= ' </span>';

      $html .= '</span>';

    }

    if ( apply_filters( 'woo_custom_installments_card_price_after_ticket_' . $hook, false ) ) {
      $html .= $this->single_product_price( $product, $price );
    }

    return $html;
  }

  /**
   * Format display prices
   * 
   * @return string
  */
  private function formatting_display( $installments, $return, $echo = true ) {

    global $product;

    if ( 0 === count( $installments ) ) {
      return;
    }

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
    }

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
  */
  private function best_no_fee( $installments, $product ) {

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
      $text = self::$display_text_formatted_main_price;
    } else {
      $text = self::$display_text_formatted_loop;
    }

    $find = array_keys( self::strings_to_replace( $best_no_fee ) );
    $replace = array_values( self::strings_to_replace( $best_no_fee ) );
    $text = str_replace( $find, $replace, $text );

    return '<span class="woo-custom-installments-details best-value ' . $best_no_fee['class'] . '">' . apply_filters( 'woo_custom_installments_best_no_fee_' . $hook, $text, $best_no_fee, $product ) . '</span>';

  }

  /**
   * Get best installment with interest
   * 
   * @return string
  */
  private function best_with_fee( $installments, $product ) {

    $hook = self::hook();

    $best_with_fee = end( $installments );

    if ( false === $best_with_fee ) {
      return;
    }

    if ( 'main_price' == $hook ) {
      $text = self::$display_text_formatted_main_price;
    } else {
      $text = self::$display_text_formatted_loop;
    }

    $find = array_keys( self::strings_to_replace( $best_with_fee ) );
    $replace = array_values( self::strings_to_replace( $best_with_fee ) );
    $text = str_replace( $find, $replace, $text );

    return '<span class="woo-custom-installments-details best-value ' . $best_with_fee['class'] . '">' . apply_filters( 'woo_custom_installments_best_with_fee_' . $hook, $text, $best_with_fee, $product ) . '</span>';

  }

  /**
   * Get fee info
   * 
   * @return string
  */
  public static function get_fee_info( $installment ) {
    $hook = self::hook();

    $text = ( $installment['interest_fee'] ) ? ' ' . __( 'com juros', 'woo-custom-installments' ) : ' ' . __( 'sem juros', 'woo-custom-installments' );
    return apply_filters( 'woo_custom_installments_fee_label', $text, $installment['interest_fee'], $hook );
  }

  /**
   * Check if variations is equal value
   * If false, display "A partir de"
   * 
   * @return string
  */
  private function variable_has_same_price( $product ) {
    return ( $product->is_type( 'variable', 'variation' ) && $product->get_variation_price( 'min' ) === $product->get_variation_price( 'max' ) );
  }

  /**
   * Save array with all details of installments
   * 
   * @return string
  */
  public static function set_installment_info( $price, $final_price, $interest_fee, $class, $i ) {
    $installment_info = array(
      'installment_price'  => $price,
      'installments_total' => $i,
      'final_price'        => $final_price,
      'interest_fee'       => $interest_fee,
      'class'              => $class,
    );

    return apply_filters( 'woo_custom_installments_installment_info', $installment_info );
  }

  /**
   * Calculate value of installment without interest
   * 
   * @return string
  */
  public static function get_installment_details_without_interest( $total, $i ) {
    $price = Woo_Custom_Installments_Calculate_Values::calculate_installment_no_fee( $total, $i );
    $final_price = Woo_Custom_Installments_Calculate_Values::calculate_final_price( $price, $i );
    $fee = false;
    $class = 'no-fee';
    $installment_info = self::set_installment_info( $price, $final_price, $fee, $class, $i );

    return $installment_info;
  }

  /**
   * Calculate value of installment with interest
   * 
   * @return string
  */
  public static function get_installment_details_with_interest( $total, $fee, $i ) {
    $price = Woo_Custom_Installments_Calculate_Values::calculate_installment_with_fee( $total, $fee, $i );
    $final_price = Woo_Custom_Installments_Calculate_Values::calculate_final_price( $price, $i );
    $fee = true;
    $class = 'fee-included';
    $installment_info = self::set_installment_info( $price, $final_price, $fee, $class, $i );

    return $installment_info;
  }

  /**
   * Check if product is available
   * 
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
   * Display discount in cart page
   * 
   * @return bool
   */
  public function display_discount_on_cart() {
    if ( 'yes' !== self::show_in_cart() ) {
      return false;
    }

    $main_price_discount = self::get_main_price_discount();

    if ( $main_price_discount > 0 ) {
      $custom_price = Woo_Custom_Installments_Calculate_Values::calculate_discounted_price( WC()->cart->get_total( 'edit' ), $main_price_discount );

      ?>
      <div class="woo-custom-installments-cart">
      <tr class="order-discount-total">
        <span class="initial-text">
          <th><?php echo apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), self::get_text_after_price() ) ); ?></th>
        </span>
        <span class="final-price">
          <td data-title="<?php echo esc_attr( apply_filters( 'woo_custom_installments_cart_total_title', sprintf( __( 'Total %s', 'woo-custom-installments' ), self::get_text_after_price() ) ) ); ?>"><?php echo wc_price( $custom_price ); ?></td>
        </span>
      </tr>
      </div>
      <?php
    }
  }

  public static function open_single_product() {
    self::$count = 0;
  }

  public static function close_single_product() {
    self::$count++;
  }

}

new Woo_Custom_Installments_Frontend_Template();