<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\Calculate_Values;
use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit;


/**
 * Admin plugin actions
 *
 * @since 2.0.0
 * @version 5.2.2
 * @package MeuMouse.com
 */
class Admin_Options extends Init {

  /**
   * Construct function
   * 
   * @since 2.0.0
   * @version 5.2.2
   * @return void
   */
  public function __construct() {
    parent::__construct();

    add_action( 'admin_menu', array( $this, 'add_woo_submenu' ) );
    
    add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_options_discount_per_unit_fields' ) );
    add_action( 'woocommerce_product_options_advanced', array( $this, 'woo_custom_installments_add_product_option' ) );
    add_action( 'woocommerce_process_product_meta', array( $this,'woo_custom_installments_save_product_option' ), 2);
    add_action( 'manage_product_posts_custom_column', array( $this, 'woo_custom_installments_output_quick_edit_values' ) );
    add_action( 'woocommerce_product_quick_edit_end', array( $this, 'woo_custom_installments_output_quick_edit_fields' ) );
    add_action( 'woocommerce_product_quick_edit_save', array( $this, 'woo_custom_installments_save_quick_edit_fields' ) );
    add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'woo_custom_installments_output_bulk_edit_fields' ) );
    add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'woo_custom_installments_save_bulk_edit_fields' ) );
    add_action( 'admin_head', array( $this, 'inject_inline_js_product_edit_page' ) );

    /**
     * Enable functions for discount per quantity in product edit
     * 
     * @since 2.7.2
     */
    if ( self::get_setting( 'enable_discount_per_quantity_method' ) == 'product' && License::is_valid() ) {
      add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_options_discount_per_quantity_fields' ) );
      add_action( 'woocommerce_process_product_meta', array( $this, 'save_options_discount_per_quantity_fields' ) );
    }

    // add product post meta for xml feed
    if ( self::get_setting('enable_post_meta_feed_xml_price') === 'yes' && License::is_valid() ) {
      add_action( 'wp_loaded', array( $this, 'product_price_for_xml_feed' ) );
      add_action( 'save_post_product', array( $this, 'update_discount_on_product_price_on_pix' ) );
    }

    add_action( 'admin_notices', array( $this, 'expire_license_notice' ) );
    add_action( 'admin_head', array( $this, 'hide_woo_custom_installments_table_price' ) );
    add_action( 'woocommerce_update_product', array( $this, 'clear_price_cache_on_update' ) );
  }


  /**
   * Function for create submenu in WooCommerce
   * 
   * @since 2.0.0
   * @version 5.0.0
   * @return void
   */
  public function add_woo_submenu() {
    add_submenu_page(
      'woocommerce', // parent page slug
      esc_html__( 'Parcelas Customizadas para WooCommerce', 'woo-custom-installments'), // page title
      esc_html__( 'Parcelas Customizadas', 'woo-custom-installments'), // submenu title
      'manage_woocommerce', // user capabilities
      'woo-custom-installments', // page slug
      array( $this, 'render_settings_page' ) // public function for print content page
    );
  }


  /**
   * Plugin general setting page and save options
   * 
   * @since 2.0.0
   * @version 4.5.0
   * @return void
   */
  public function render_settings_page() {
    include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/settings.php';
  }


  /**
   * Display plugin option on product bulk edit screen
   * 
   * @since 2.0.0
   * @access public 
   */
  public function woo_custom_installments_output_bulk_edit_fields() {
    ?>
    <div class="inline-edit-group woo-custom-installments-field">
      <?php woocommerce_wp_checkbox( array( 'id'  =>  '__disable_installments', 'label'  => __( 'Desativar a exibição de parcelas neste produto', 'woo-custom-installments' ) )); ?>
    </div>
    <div class="inline-quick-edit woo-custom-installments-fields" style="display: block; clear: both;">
      <?php woocommerce_wp_checkbox( array( 'id'  =>  '__disable_discount_main_price', 'label'  =>  __( 'Desativar descontos neste produto', 'woo-custom-installments' ) ) ); ?>
    </div>
    <?php
  }


  /**
   * Display plugin option on product quick edit screen
   * 
   * @since 2.0.0
   * @version 4.1.0
   * @return void
   */
  public function woo_custom_installments_output_quick_edit_fields() {
    global $post;

    $disable_installments_checked = get_post_meta( $post->ID, '__disable_installments', true );
    $disable_discount_checked = get_post_meta( $post->ID, '__disable_discount_main_price', true ); ?>

    <label class="inline-quick-edit woo-custom-installments-fields" style="display: block; clear: both;">
      <input type="checkbox" class="checkbox" name="__disable_installments" <?php checked( $disable_installments_checked === 'yes'); ?> >
      <?php echo esc_html__( 'Desativar a exibição de parcelas neste produto', 'woo-custom-installments' ); ?>
    </label>
    <label class="inline-quick-edit woo-custom-installments-fields" style="display: block; clear: both;">
      <input type="checkbox" class="checkbox" name="__disable_discount_main_price" <?php checked( $disable_discount_checked === 'yes'); ?> >
      <?php echo esc_html__( 'Desativar descontos neste produto', 'woo-custom-installments' ); ?>
    </label>
    <?php
  }


  /**
   * Save product bulk edit options
   * 
   * @since 2.0.0
   * @version 4.1.0
   * @return void
   */
  public function woo_custom_installments_save_bulk_edit_fields( $product ) {
    $product_id = $product->get_id();

    $disable_installments = isset( $_POST['__disable_installments'] ) ? 'yes' : 'no';
    update_post_meta( $product_id, '__disable_installments', $disable_installments );

    $disable_discount_main_price = isset( $_POST['__disable_discount_main_price'] ) ? 'yes' : 'no';
    update_post_meta( $product_id, '__disable_discount_main_price', $disable_discount_main_price );
  }


  /**
   * Save product quick edit options
   * 
   * @since 2.0.0
   * @version 4.0.0
   * @return void
   */
  public function woo_custom_installments_save_quick_edit_fields( $product ) {
    $product_id = $product->get_id();

    $disable_installments = isset( $_POST[ '__disable_installments' ] ) ? 'yes' : 'no';
    update_post_meta( $product_id, '__disable_installments', $disable_installments );

    $disable_discount_main_price = isset( $_POST[ '__disable_discount_main_price' ] ) ? 'yes' : 'no';
    update_post_meta( $product_id, '__disable_discount_main_price', $disable_discount_main_price );
  }


  /**
   * Output plugin option values for product quick edit
   * 
   * @since 2.0.0
   * @version 4.1.0
   * @access public 
   */
  public function woo_custom_installments_output_quick_edit_values( $column ) {
    global $post;
    $product_id = $post->ID;

    if ( $column == 'name') {
      $estMeta = get_post_meta( $product_id, '__disable_installments', true ); ?>

      <div class="hidden" id="woo_custom_installments_inline_<?php echo $product_id; ?>">
          <div class="_woo_custom_installments_enable"><?php echo $estMeta; ?></div>
      </div>
      <?php

      $estMeta = get_post_meta( $product_id, '__disable_discount_main_price', true ); ?>

      <div class="hidden" id="woo_custom_installments_inline_<?php echo $product_id; ?>">
          <div class="_woo_custom_installments_enable"><?php echo $estMeta; ?></div>
      </div>
      <?php
    }
  }


  /**
   * Display plugin option on product edit screen
   * 
   * @since 2.0.0
   * @version 4.1.0
   * @return void
   */
  public function woo_custom_installments_add_product_option() {
    woocommerce_wp_checkbox(
      array(
        'id' => '__disable_installments',
        'label' => __( 'Desativar a exibição de parcelas neste produto', 'woo-custom-installments' ),
      )
    );

    woocommerce_wp_checkbox(
      array(
        'id' => '__disable_discount_main_price',
        'label' => __( 'Desativar descontos neste produto', 'woo-custom-installments' ),
      )
    );
  }


  /**
   * Save product meta
   * 
   * @since 2.0.0
   * @version 4.0.0
   * @return void
   */
  public function woo_custom_installments_save_product_option( $post_id ) {
    $disable_installments = isset( $_POST[ '__disable_installments' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '__disable_installments', $disable_installments );

    $disable_discount_main_price = isset( $_POST[ '__disable_discount_main_price' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '__disable_discount_main_price', $disable_discount_main_price );

    // Salvar o valor da opção do checkbox
    $checkbox_value = isset( $_POST['enable_discount_per_unit'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, 'enable_discount_per_unit', $checkbox_value );

    // Salvar o valor da opção do método
    $discount_method = isset( $_POST['discount_per_unit_method'] ) ? sanitize_text_field( $_POST['discount_per_unit_method'] ) : '';
    update_post_meta( $post_id, 'discount_per_unit_method', $discount_method );

    // Salvar o valor da opção do desconto por unidade
    $discount_amount = isset( $_POST['unit_discount_amount'] ) ? sanitize_text_field( $_POST['unit_discount_amount'] ) : '';
    update_post_meta( $post_id, 'unit_discount_amount', $discount_amount );

    // Salvar a opção selecionada do gateway
    $discount_gateway = isset( $_POST['discount_gateway'] ) ? sanitize_text_field( $_POST['discount_gateway'] ) : '';
    update_post_meta( $post_id, 'discount_gateway', $discount_gateway );
  }


  /**
   * Add custom inputs for discount per unit in General tab of product data WooCommerce
   * 
   * @since 3.0.0
   * @return void
   */
  public function add_options_discount_per_unit_fields() {
    global $post;

    echo '<div class="options_group">';

    // Checkbox para ativar desconto por unidade
    woocommerce_wp_checkbox(
        array(
            'id' => 'enable_discount_per_unit',
            'label' => __('Ativar desconto do produto', 'woo-custom-installments'),
            'value' => get_post_meta( $post->ID, 'enable_discount_per_unit', true ),
        )
    );

    // Select para selecionar o método de desconto por unidade
    woocommerce_wp_select(
        array(
            'id' => 'discount_per_unit_method',
            'label' => __('Método de desconto', 'woo-custom-installments'),
            'value' => get_post_meta( $post->ID, 'discount_per_unit_method', true ),
            'options' => array(
                'percentage' => __('Percentual (%)', 'woo-custom-installments'),
                'fixed' => sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ),
            ),
        )
    );

    // Campo de entrada para o valor do desconto por unidade
    woocommerce_wp_text_input(
        array(
            'id' => 'unit_discount_amount',
            'label' => __('Valor do desconto', 'woo-custom-installments'),
            'value' => get_post_meta( $post->ID, 'unit_discount_amount', true ),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0',
            ),
            'desc_tip' => true,
            'description' => __('Insira o valor do desconto para o preço do produto. Obs.: Desconto para pagamento no Pix', 'woo-custom-installments'),
        )
    );

    // Adicione um título para o seletor de opção
    echo '<p class="form-field discount_per_unit_method_field"><label for="discount_gateway">' . __('Aplicar desconto para o gateway', 'woo-custom-installments') . '</label>';
      // Obtenha a opção atual do gateway (se existir)
      $selected_gateway = get_post_meta($post->ID, 'discount_gateway', true);

      // Crie o seletor de opção com os gateways disponíveis
      echo '<select id="discount_gateway" name="discount_gateway">';
      echo '<option value="">' . __('Selecione um gateway', 'woo-custom-installments') . '</option>';
        // Obtenha a lista de gateways do WooCommerce
        $available_gateways = WC()->payment_gateways->payment_gateways();
        
        foreach ($available_gateways as $gateway_id => $gateway) {
            echo '<option value="' . esc_attr($gateway_id) . '" ' . selected($selected_gateway, $gateway_id, false) . '>' . esc_html($gateway->get_title()) . '</option>';
        }
      echo '</select>';
    echo '</p>';

    echo '</div>';
  }


  /**
   * Add custom inputs for discount per quantity in General tab of product data WooCommerce
   * 
   * @since 2.7.2
   * @return void
   */
  public function add_options_discount_per_quantity_fields() {
    global $post;

    echo '<div class="options_group">';

    // Checkbox para ativar desconto por quantidade
    woocommerce_wp_checkbox(
        array(
            'id' => 'enable_discount_per_quantity',
            'label' => __('Ativar desconto por quantidade', 'woo-custom-installments'),
            'value' => get_post_meta( $post->ID, 'enable_discount_per_quantity', true ),
        )
    );

    // Select para selecionar o método de desconto
    woocommerce_wp_select(
        array(
            'id' => 'discount_per_quantity_method',
            'label' => __('Método de desconto', 'woo-custom-installments'),
            'value' => get_post_meta( $post->ID, 'discount_per_quantity_method', true ),
            'options' => array(
                'percentage' => __('Percentual (%)', 'woo-custom-installments'),
                'fixed' => sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ),
            ),
        )
    );

    // Campo de entrada para o valor do desconto
    woocommerce_wp_text_input(
        array(
            'id' => 'quantity_discount_amount',
            'label' => __('Valor do desconto', 'woo-custom-installments'),
            'value' => get_post_meta( $post->ID, 'quantity_discount_amount', true ),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0',
            ),
            'desc_tip' => true,
            'description' => __('Insira o valor do desconto.', 'woo-custom-installments'),
        )
    );

    // Campo de entrada para a quantidade mínima para oferecer desconto
    woocommerce_wp_text_input(
        array(
            'id' => 'minimum_quantity_discount',
            'label' => __('Quantidade mínima para desconto', 'woo-custom-installments'),
            'value' => get_post_meta( $post->ID, 'minimum_quantity_discount', true ),
            'type' => 'number',
            'custom_attributes' => array(
                'min' => '1',
            ),
            'desc_tip' => true,
            'description' => __('Insira a quantidade mínima de produtos para oferecer o desconto.', 'woo-custom-installments'),
        )
    );

    echo '</div>';
  }


  /**
   * Save options discount per quantity fields
   * 
   * @since 2.7.2
   * @return void
   */
  public function save_options_discount_per_quantity_fields( $post_id ) {
    // save checkbox option value
    $checkbox_value = isset( $_POST['enable_discount_per_quantity'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, 'enable_discount_per_quantity', $checkbox_value );
  
    // save method option value
    $discount_method = isset( $_POST['discount_per_quantity_method'] ) ? sanitize_text_field($_POST['discount_per_quantity_method']) : '';
    update_post_meta( $post_id, 'discount_per_quantity_method', $discount_method );
  
    // save amount option value
    $discount_amount = isset( $_POST['quantity_discount_amount'] ) ? sanitize_text_field($_POST['quantity_discount_amount']) : '';
    update_post_meta( $post_id, 'quantity_discount_amount', $discount_amount );
  
    // save minimum quantity option value
    $minimum_quantity = isset( $_POST['minimum_quantity_discount'] ) ? sanitize_text_field($_POST['minimum_quantity_discount']) : '';
    update_post_meta( $post_id, 'minimum_quantity_discount', $minimum_quantity );
  }


  /**
   * Inject JavaScript on page product WooCommerce
   * 
   * @since 2.7.2
   * @return void
   */
  public function inject_inline_js_product_edit_page() {
    global $post;

    // checks if it's a product edit page
    if ( isset( $post->post_type) && $post->post_type === 'product' && is_admin() ) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready( function($) {
              function toggleDiscountPerUnitFields() {
                  let enableDiscount = $('#enable_discount_per_unit').is(':checked');

                  if (enableDiscount) {
                      $('p.discount_per_unit_method_field, p.unit_discount_amount_field').show();
                  } else {
                      $('p.discount_per_unit_method_field, p.unit_discount_amount_field').hide();
                  }
              }

              toggleDiscountPerUnitFields();

              $('#enable_discount_per_unit').on('change', function() {
                  toggleDiscountPerUnitFields();
              });

              function toggleDiscountFields() {
                  let enableDiscount = $('#enable_discount_per_quantity').is(':checked');

                  if (enableDiscount) {
                      $('p.discount_per_quantity_method_field, p.quantity_discount_amount_field, p.minimum_quantity_discount_field').show();
                  } else {
                      $('p.discount_per_quantity_method_field, p.quantity_discount_amount_field, p.minimum_quantity_discount_field').hide();
                  }
              }

              toggleDiscountFields();

              $('#enable_discount_per_quantity').on('change', function() {
                  toggleDiscountFields();
              });

              // check if discount main price is activated
              if ( $('#__disable_discount_main_price').is(':checked') ) {
                var tooltip = $('<div class="tooltip-danger">A opção "Desativar descontos neste produto" está ativada.</div>');

                $('#enable_discount_per_quantity, #enable_discount_per_unit').after(tooltip);
              } else {
                $('.tooltip-danger').hide();
              }
          });
        </script>

        <style>
          .tooltip-danger {
            background-color: rgba(239, 68, 68, 0.10);
            color: #ef4444;
            display: inline-block;
            padding: 0.35em 0.6em;
            font-size: 0.8125rem;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            margin-left: 5px;
          }
        </style>
        <?php
    }
  }


  /**
   * Generate post meta '_product_price_on_pix' for Feed XML
   * 
   * @since 4.0.0
   * @version 5.2.2
   * @param int $product_id | Product ID
   * @return void
   */
  public function product_price_for_xml_feed() {
    if ( ! is_admin() ) {
        return;
    }

    $cache_key = 'woo_custom_installments_product_price_xml_feed_cache';
    $cached_data = get_transient( $cache_key );

    // If the data is in the cache, return without executing the query
    if ( $cached_data !== false ) {
        return $cached_data;
    }

    // Performs the query if the cache does not exist
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    $products = new \WP_Query( $args );

    if ( $products->have_posts() ) {
        while ( $products->have_posts() ) {
            $products->the_post();
            $product_id = get_the_ID();
            $product_price_on_pix = get_post_meta( $product_id, '_product_price_on_pix', true );
            $product = wc_get_product( $product_id );

            if ( $product && $product->get_price() > 0 && empty( $product_price_on_pix ) ) {
                $discount = self::get_setting( 'discount_main_price' );
                $discount_per_product = get_post_meta( $product_id, 'enable_discount_per_unit', true );
                $discount_per_product_method = get_post_meta( $product_id, 'discount_per_unit_method', true );
                $discount_per_product_value = get_post_meta( $product_id, 'unit_discount_amount', true );

                if ( $discount_per_product === 'yes' ) {
                    if ( $discount_per_product_method === 'percentage' ) {
                        $custom_price = Calculate_Values::calculate_discounted_price( $product->get_price(), $discount_per_product_value, $product );
                    } else {
                        $custom_price = (float) $product->get_price() - (float) $discount_per_product_value;
                    }
                } else {
                    if ( self::get_setting( 'product_price_discount_method' ) === 'percentage' ) {
                        $custom_price = Calculate_Values::calculate_discounted_price( $product->get_price(), $discount, $product );
                    } else {
                        $custom_price = (float) $product->get_price() - (float) $discount;
                    }
                }

                update_post_meta( $product_id, '_product_price_on_pix', $custom_price );
            }
        }
    }

    set_transient( $cache_key, $products, 7 * DAY_IN_SECONDS );

    return $products;
  }
  

  /**
   * Update post meta "_product_price_on_pix" on change value on product post
   * 
   * @since 4.3.0
   * @param int $product_id | Product ID
   * @return void
   */
  public function update_discount_on_product_price_on_pix( $product_id ) {
    $product = wc_get_product( $product_id );

    // Checks if the product exists and has a defined price
    if ( $product && $product->get_price() > 0 ) {
      $product_price_on_pix = get_post_meta( $product_id, '_product_price_on_pix', true );

      if ( ! empty( $product_price_on_pix ) ) {
        $discount = self::get_setting('discount_main_price');
        $discount_per_product = get_post_meta( $product_id, 'enable_discount_per_unit', true );
        $discount_per_product_method = get_post_meta( $product_id, 'discount_per_unit_method', true );
        $discount_per_product_value = get_post_meta( $product_id, 'unit_discount_amount', true );

        if ( $discount_per_product === 'yes' ) {
            if ( $discount_per_product_method === 'percentage' ) {
                $custom_price = Calculate_Values::calculate_discounted_price( $product->get_price(), $discount_per_product_value, $product );
            } else {
                $custom_price = $product->get_price() - $discount_per_product_value;
            }
        } else {
            if ( self::get_setting( 'product_price_discount_method' ) === 'percentage' ) {
                $custom_price = Calculate_Values::calculate_discounted_price( $product->get_price(), $discount, $product );
            } else {
                $custom_price = $product->get_price() - $discount;
            }
        }

        update_post_meta( $product_id, '_product_price_on_pix', $custom_price );
      }
    }
  }


  /**
   * Clear cache on update product
   * 
   * @since 5.2.2
   * @param int $product_id | Product ID
   * @return void
   */
  public function clear_price_cache_on_update( $product_id ) {
    delete_transient( 'woo_custom_installments_product_price_xml_feed_cache' );
  }


  /**
   * Display admin notice when license is expired
   * 
   * @since 4.5.1
   * @return void
   */
  public function expire_license_notice() {
    if ( License::expired_license() ) {
      $class = 'notice notice-error is-dismissible';
			$message = __( 'Sua licença do <strong>Parcelas Customizadas para WooCommerce</strong> expirou, realize a renovação para continuar aproveitando os recursos Pro.', 'woo-custom-installments' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
    }
  }


  /**
   * Hide installments info on WooCommerce product table on admin page
   * 
   * @since 4.3.5
   * @version 5.0.0
   * @return void
   */
  public function hide_woo_custom_installments_table_price() {
    echo '<style>
      .woo-custom-installments-offer,
      .woo-custom-installments-economy-pix-badge,
      .woo-custom-installments-ticket-discount {
        display: none;
      }

      .woo-custom-installments-starting-from {
        margin-right: 0.25rem;
      }
    </style>';
  }
}

new Admin_Options();