<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;


/**
 * Admin plugin actions
 *
 * @since 2.0.0
 * @version 4.2.0
 * @package MeuMouse.com
 */
class Woo_Custom_Installments_Admin_Options extends Woo_Custom_Installments_Init {

  /**
   * Construct function
   * 
   * @since 2.0.0
   * @version 4.0.0
   * @return void
   */
  public function __construct() {
    parent::__construct();

    add_action( 'admin_menu', array( $this, 'woo_custom_installments_admin_menu' ) );
    add_action( 'wp_ajax_woo_custom_installments_ajax_save_options', array( $this, 'woo_custom_installments_ajax_save_options_callback' ) );
    add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_options_discount_per_unit_fields' ) );
    add_action( 'woocommerce_product_options_advanced', array( $this, 'woo_custom_installments_add_product_option' ) );
    add_action( 'woocommerce_process_product_meta', array( $this,'woo_custom_installments_save_product_option' ), 2);
    add_action( 'manage_product_posts_custom_column', array( $this, 'woo_custom_installments_output_quick_edit_values' ) );
    add_action( 'woocommerce_product_quick_edit_end', array( $this, 'woo_custom_installments_output_quick_edit_fields' ) );
    add_action( 'woocommerce_product_quick_edit_save', array( $this, 'woo_custom_installments_save_quick_edit_fields' ) );
    add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'woo_custom_installments_output_bulk_edit_fields' ) );
    add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'woo_custom_installments_save_bulk_edit_fields' ) );
    add_action( 'admin_head', array( $this, 'inject_inline_js_product_edit_page' ) );
    add_action( 'wp_ajax_deactive_license_process', array( $this, 'deactive_license_process_callback' ) );

    /**
     * Enable functions for discount per quantity in product edit
     * 
     * @since 2.7.2
     */
    if ( self::get_setting( 'enable_discount_per_quantity_method' ) == 'product' && self::license_valid() ) {
      add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_options_discount_per_quantity_fields' ) );
      add_action( 'woocommerce_process_product_meta', array( $this, 'save_options_discount_per_quantity_fields' ) );
    }
  }

  /**
   * Function for create submenu in WooCommerce
   * 
   * @since 2.0.0
   * @access public
   * @return array
   */
  public function woo_custom_installments_admin_menu() {
    add_submenu_page(
      'woocommerce', // parent page slug
      esc_html__( 'Parcelas Customizadas para WooCommerce', 'woo-custom-installments'), // page title
      esc_html__( 'Parcelas', 'woo-custom-installments'), // submenu title
      'manage_woocommerce', // user capabilities
      'woo-custom-installments', // page slug
      array( $this, 'woo_custom_installments_settings_page' ) // public function for print content page
    );
  }


  /**
   * Plugin general setting page and save options
   * 
   * @since 2.0.0
   * @access public
   */
  public function woo_custom_installments_settings_page() {
    include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/settings.php';
  }


  /**
   * Save options in AJAX
   * 
   * @since 3.0.0
   * @return void
   * @package MeuMouse.com
   */
  public function woo_custom_installments_ajax_save_options_callback() {
    if ( isset( $_POST['form_data'] ) ) {
      // Convert serialized data into an array
      parse_str( $_POST['form_data'], $form_data );

      $options = get_option( 'woo-custom-installments-setting' );
      $options['enable_installments_all_products'] = isset( $form_data['enable_installments_all_products'] ) ? 'yes' : 'no';
      $options['remove_price_range'] = isset( $form_data['remove_price_range'] ) && self::license_valid() ? 'yes' : 'no';
      $options['custom_text_after_price'] = isset( $form_data['custom_text_after_price'] ) ? 'yes' : 'no';
      $options['set_fee_per_installment'] = isset( $form_data['set_fee_per_installment'] ) && self::license_valid() ? 'yes' : 'no';
      $options['disable_update_installments'] = isset( $form_data['disable_update_installments'] ) ? 'yes' : 'no';
      $options['enable_all_discount_options'] = isset( $form_data['enable_all_discount_options'] ) ? 'yes' : 'no';
      $options['display_installments_cart'] = isset( $form_data['display_installments_cart'] ) ? 'yes' : 'no';
      $options['include_shipping_value_in_discounts'] = isset( $form_data['include_shipping_value_in_discounts'] ) ? 'yes' : 'no';
      $options['display_tag_discount_price_checkout'] = isset( $form_data['display_tag_discount_price_checkout'] ) ? 'yes' : 'no';
      $options['display_discount_price_schema'] = isset( $form_data['display_discount_price_schema'] ) && self::license_valid() ? 'yes' : 'no';
      $options['enable_functions_discount_per_quantity'] = isset( $form_data['enable_functions_discount_per_quantity'] ) && self::license_valid() ? 'yes' : 'no';
      $options['enable_discount_per_unit_discount_per_quantity'] = isset( $form_data['enable_discount_per_unit_discount_per_quantity'] ) ? 'yes' : 'no';
      $options['message_discount_per_quantity'] = isset( $form_data['message_discount_per_quantity'] ) ? 'yes' : 'no';
      $options['enable_all_interest_options'] = isset( $form_data['enable_all_interest_options'] ) ? 'yes' : 'no';
      $options['display_tag_interest_checkout'] = isset( $form_data['display_tag_interest_checkout'] ) ? 'yes' : 'no';
      $options['enable_pix_method_payment_form'] = isset( $form_data['enable_pix_method_payment_form'] ) ? 'yes' : 'no';
      $options['enable_instant_approval_badge'] = isset( $form_data['enable_instant_approval_badge'] ) ? 'yes' : 'no';
      $options['enable_ticket_method_payment_form'] = isset( $form_data['enable_ticket_method_payment_form'] ) ? 'yes' : 'no';
      $options['enable_ticket_discount_main_price'] = isset( $form_data['enable_ticket_discount_main_price'] ) ? 'yes' : 'no';
      $options['enable_credit_card_method_payment_form'] = isset( $form_data['enable_credit_card_method_payment_form'] ) ? 'yes' : 'no';
      $options['enable_debit_card_method_payment_form'] = isset( $form_data['enable_debit_card_method_payment_form'] ) ? 'yes' : 'no';
      $options['enable_mastercard_flag_credit'] = isset( $form_data['enable_mastercard_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_visa_flag_credit'] = isset( $form_data['enable_visa_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_elo_flag_credit'] = isset( $form_data['enable_elo_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_hipercard_flag_credit'] = isset( $form_data['enable_hipercard_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_diners_club_flag_credit'] = isset( $form_data['enable_diners_club_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_discover_flag_credit'] = isset( $form_data['enable_discover_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_american_express_flag_credit'] = isset( $form_data['enable_american_express_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_paypal_flag_credit'] = isset( $form_data['enable_paypal_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_stripe_flag_credit'] = isset( $form_data['enable_stripe_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_mercado_pago_flag_credit'] = isset( $form_data['enable_mercado_pago_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_pagseguro_flag_credit'] = isset( $form_data['enable_pagseguro_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_pagarme_flag_credit'] = isset( $form_data['enable_pagarme_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_cielo_flag_credit'] = isset( $form_data['enable_cielo_flag_credit'] ) ? 'yes' : 'no';
      $options['enable_mastercard_flag_debit'] = isset( $form_data['enable_mastercard_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_visa_flag_debit'] = isset( $form_data['enable_visa_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_elo_flag_debit'] = isset( $form_data['enable_elo_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_hipercard_flag_debit'] = isset( $form_data['enable_hipercard_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_diners_club_flag_debit'] = isset( $form_data['enable_diners_club_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_discover_flag_debit'] = isset( $form_data['enable_discover_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_american_express_flag_debit'] = isset( $form_data['enable_american_express_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_paypal_flag_debit'] = isset( $form_data['enable_paypal_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_stripe_flag_debit'] = isset( $form_data['enable_stripe_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_mercado_pago_flag_debit'] = isset( $form_data['enable_mercado_pago_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_pagseguro_flag_debit'] = isset( $form_data['enable_pagseguro_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_pagarme_flag_debit'] = isset( $form_data['enable_pagarme_flag_debit'] ) ? 'yes' : 'no';
      $options['enable_cielo_flag_debit'] = isset( $form_data['enable_cielo_flag_debit'] ) ? 'yes' : 'no';
      $options['center_group_elements_loop'] = isset( $form_data['center_group_elements_loop'] ) ? 'yes' : 'no';
      $options['enable_economy_pix_badge'] = isset( $form_data['enable_economy_pix_badge'] ) && self::license_valid() ? 'yes' : 'no';
      $options['enable_post_meta_feed_xml_price'] = isset( $form_data['enable_post_meta_feed_xml_price'] ) && self::license_valid() ? 'yes' : 'no';

      $settings_array = array();

      if ( isset( $form_data['woo_custom_installments_discounts'] ) && !empty( $form_data['woo_custom_installments_discounts'] ) && self::license_valid() ) {
        $settings_array = maybe_serialize( $form_data['woo_custom_installments_discounts'] );
        update_option( 'woo_custom_installments_discounts_setting', $settings_array );
      }

      if ( isset( $form_data['woo_custom_installments_interests'] ) && !empty( $form_data['woo_custom_installments_interests'] ) && self::license_valid() ) {
        $settings_array = maybe_serialize( $form_data['woo_custom_installments_interests'] );
        update_option( 'woo_custom_installments_interests_setting', $settings_array );
      }

      if ( isset( $form_data['custom_fee_installments'] ) && is_array( $form_data['custom_fee_installments'] ) && self::license_valid() ) {
        $custom_fee_installments = $form_data['custom_fee_installments'];
        
        $settings_array = maybe_serialize( $form_data['custom_fee_installments'] );
        update_option( 'woo_custom_installments_custom_fee_installments', $settings_array );
      }

      // Merge the form data with the default options
      $updated_options = wp_parse_args( $form_data, $options );

      // Save the updated options
      update_option( 'woo-custom-installments-setting', $updated_options );

      $response = array(
        'status' => 'success',
        'options' => $updated_options,
        'customFeeInstallments' => isset( $custom_fee_installments ) ? $custom_fee_installments : '',
      );

      echo wp_json_encode( $response ); // Send JSON response
    }

    wp_die();
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
   * Remove license domain on deactive license
   * 
   * @since 4.0.0
   * @return void
   */
  public function deactive_license_process_callback() {
    if ( isset( $_POST['deactive_license_process'] ) ) {
      delete_transient('woo_custom_installments_api_request_cache');
      delete_transient('woo_custom_installments_api_response_cache');
      update_option( 'woo_custom_installments_license_status', 'invalid' );
      update_option( 'woo_custom_installments_license_key', '' );
      delete_option('woo_custom_installments_license_response_object');

      $response = array(
        'status' => 'success',
      );

      echo wp_send_json( $response ); // Send JSON response

      $this->deactive_license = true;
    }

    wp_die();
  }

}

new Woo_Custom_Installments_Admin_Options();