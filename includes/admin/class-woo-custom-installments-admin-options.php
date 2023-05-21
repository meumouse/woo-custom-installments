<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit; }

class Woo_Custom_Installments_Admin_Options extends Woo_Custom_Installments_Init {

  /**
   * Woo_Custom_Installments_Admin constructor.
   *
   * @since 2.0.0
   * @access public
   */
  public function __construct() {
    parent::__construct();

    add_action( 'admin_menu', array( $this, 'woo_custom_installments_admin_menu' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'woo_custom_installments_admin_scripts' ) );
    add_action( 'admin_head', array( $this, 'badge_pro_woo_custom_installments' ) );
    add_action( 'woocommerce_product_options_advanced', array( $this, 'woo_custom_installments_add_product_option' ) );
    add_action( 'woocommerce_process_product_meta', array( $this,'woo_custom_installments_save_product_option' ), 2);
    add_action( 'manage_product_posts_custom_column', array( $this, 'woo_custom_installments_output_quick_edit_values' ) );
    add_action( 'woocommerce_product_quick_edit_end', array( $this, 'woo_custom_installments_output_quick_edit_fields' ) );
    add_action( 'woocommerce_product_quick_edit_save', array( $this, 'woo_custom_installments_save_quick_edit_fields' ) );
    add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'woo_custom_installments_output_bulk_edit_fields' ) );
    add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'woo_custom_installments_save_bulk_edit_fields' ) );
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
    global $options, $discountSettings, $activateLicense, $deactivateLicense;
    
    $settingSaves = false;
    $activateLicense = false;
    $deactivateLicense = false;
    $settings_array = array();

    // Save global options
    if( current_user_can( 'manage_woocommerce' ) && isset( $_POST[ 'save_settings' ] ) ) {
      update_option( 'woo-custom-installments-setting', $_POST );
      $this->woo_custom_installments_settings = $_POST;

      if( isset( $_POST['woo_custom_installments_discounts'] ) && !empty( $_POST['woo_custom_installments_discounts'] ) && $this->responseObj->is_valid ) {
        $settings_array = maybe_serialize( $_POST['woo_custom_installments_discounts'] );
        update_option( 'woo_custom_installments_discounts_setting', $settings_array );
      }

      if( isset( $_POST['woo_custom_installments_interests'] ) && !empty( $_POST['woo_custom_installments_interests'] ) && $this->responseObj->is_valid ) {
        $settings_array = maybe_serialize( $_POST['woo_custom_installments_interests'] );
        update_option( 'woo_custom_installments_interests_setting', $settings_array );
      }

      if( isset( $_POST['custom_fee_installments'] ) && !empty( $_POST['custom_fee_installments'] ) && $this->responseObj->is_valid ) {
        $settings_array = maybe_serialize( $_POST['custom_fee_installments'] );
        update_option( 'woo_custom_installments_custom_fee_installments', $settings_array );
      }

      $settingSaves = true;
    }

    // Display notification on active license
    if( isset( $_POST[ 'active_license' ] ) && $this->responseObj->is_valid ) {
      $activateLicense = true;
    }

    // Display notification on deative license
    if( isset( $_POST[ 'deactive_license' ] ) ) {
      delete_option( 'woo_custom_installments_license_key' );
      $deactivateLicense = true;
    }

    $options = get_option( 'woo-custom-installments-setting' );
    $discountSettings = get_option( 'woo-custom-installments-setting' );
    $insterestSettings = get_option( 'woo-custom-installments-setting' );
    $customFeeInstallments = get_option( 'woo_custom_installments_custom_fee_installments' );

    include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/settings.php';
  }


  /**
   * Enqueue admin scripts in page settings only
   * 
   * @since 2.0.0
   * @access public
   * @return void
   */
  public function woo_custom_installments_admin_scripts() {
    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    
    if ( false !== strpos( $url, 'admin.php?page=woo-custom-installments' ) ) {
      wp_enqueue_script( 'woo-custom-installments-admin', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/js/admin.js' );
      wp_enqueue_style( 'woo-custom-installments-admin-styles', WOO_CUSTOM_INSTALLMENTS_URL . 'assets/css/admin.css' );
    }
  }


  /**
   * Display badge in CSS for get pro in plugins page
   * 
   * @since 2.0.0
   * @access public
   */
  public function badge_pro_woo_custom_installments() {
    echo '<style>
      #get-pro-woo-custom-installments {
        display: inline-block;
        padding: 0.35em 0.6em;
        font-size: 0.8125em;
        font-weight: 600;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
        background-color: #008aff;
        transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
      }
      #get-pro-woo-custom-installments:hover {
        background-color: #0078ed;
      }
    </style>';
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
      <?php woocommerce_wp_checkbox( array( 'id'  =>  '__disable_discount_main_price', 'label'  =>  __( 'Desativar a exibição de desconto no preço principal neste produto', 'woo-custom-installments' ) ) ); ?>
    </div>
    <?php
  }


  /**
   * Save product bulk edit options
   * 
   * @since 2.0.0
   * @access public 
   */
  public function woo_custom_installments_save_bulk_edit_fields( $product ) {
    $product_id = $product->get_id();

    if( $product_id > 0) {
      $disable_installments = isset( $_REQUEST[ '__disable_installments' ] ) ? 'yes' : 'no';
      $disable_discount_main_price = isset( $_REQUEST[ '__disable_discount_main_price' ] ) ? 'yes' : 'no';
      update_post_meta( $product_id, '__disable_installments', $disable_installments );
      update_post_meta( $product_id, '__disable_discount_main_price', $disable_discount_main_price );
    }
  }


  /**
   * Display plugin option on product quick edit screen
   * 
   * @since 2.0.0
   * @access public 
   */
  public function woo_custom_installments_output_quick_edit_fields() {
    ?>
    <div class="inline-quick-edit woo-custom-installments-fields" style="display: block; clear: both;">
      <?php woocommerce_wp_checkbox( array( 'id'  =>  '__disable_installments', 'label'  =>  __( 'Desativar a exibição de parcelas neste produto', 'woo-custom-installments' ) ) ); ?>
    </div>
    <div class="inline-quick-edit woo-custom-installments-fields" style="display: block; clear: both;">
      <?php woocommerce_wp_checkbox( array( 'id'  =>  '__disable_discount_main_price', 'label'  =>  __( 'Desativar a exibição de desconto no preço principal neste produto', 'woo-custom-installments' ) ) ); ?>
    </div>
    <?php
  }


  /**
   * Save product quick edit options
   * 
   * @since 2.0.0
   * @access public 
   */
  public function woo_custom_installments_save_quick_edit_fields( $product ) {
    $product_id = $product->get_id();

    if( $product_id > 0) {
      $disable_installments = isset( $_REQUEST[ '__disable_installments' ] ) ? 'yes' : 'no';
      $disable_discount_main_price = isset( $_REQUEST[ '__disable_discount_main_price' ] ) ? 'yes' : 'no';
      update_post_meta( $product_id, '__disable_installments', $disable_installments );
      update_post_meta( $product_id, '__disable_discount_main_price', $disable_discount_main_price );
    }
  }


  /**
   * Output  plugin option values for product quick edit
   * 
   * @since 2.0.0
   * @access public 
   */
  public function woo_custom_installments_output_quick_edit_values( $column ) {
    global $post;
    $product_id = $post->ID;

    if( $column == 'name') {
      $estMeta = get_post_meta( $product_id, '__disable_installments', true );
      ?>
      <div class="hidden" id="woo_custom_installments_inline_<?php echo $product_id; ?>">
          <div class="_woo_custom_installments_enable"><?php echo $estMeta; ?></div>
      </div>
      <?php

      $estMeta = get_post_meta( $product_id, '__disable_discount_main_price', true );
      ?>
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
   * @access public 
   */
  public function woo_custom_installments_add_product_option() {
    woocommerce_wp_checkbox( array( 'id'  =>  '__disable_installments', 'label'  =>  __( 'Desativar a exibição de parcelas neste produto', 'woo-custom-installments' ) ));
    woocommerce_wp_checkbox( array( 'id'  =>  '__disable_discount_main_price', 'label'  =>  __( 'Desativar a exibição de desconto no preço principal neste produto', 'woo-custom-installments' ) ));
  }


  /**
   * Save product meta
   * 
   * @since 2.0.0
   * @access public 
   */
  public function woo_custom_installments_save_product_option( $post_id ) {
    $disable_installments = isset( $_POST[ '__disable_installments' ] ) ? 'yes' : 'no';
    $disable_discount_main_price = isset( $_POST[ '__disable_discount_main_price' ] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '__disable_installments', $disable_installments );
    update_post_meta( $post_id, '__disable_discount_main_price', $disable_discount_main_price );
  }

}

new Woo_Custom_Installments_Admin_Options();