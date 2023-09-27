<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div id="interests-settings" class="nav-content ">
    <table class="form-table">
      <tr>
        <th>
            <?php echo esc_html__( 'Habilitar funções de juros', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Ative esta opção para habilitar todas as opções relacionadas a juros.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
            <div class="form-check form-switch">
                <input type="checkbox" class="toggle-switch" id="enable_all_interest_options" name="enable_all_interest_options" value="yes" <?php checked( $this->getSetting( 'enable_all_interest_options') == 'yes' ); ?> />
            </div>
        </td>
      </tr>
      <tr class="display-enable-all-interest-options">
         <th>
            <?php echo esc_html__( 'Mostrar emblema de juros na finalização da compra', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir o emblema de juros na página de finalização de compra para a forma de desconto configurada.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="display_tag_interest_checkout" name="display_tag_interest_checkout" value="yes" <?php checked( $this->getSetting( 'display_tag_interest_checkout') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-interest-options mt-4">
         <th class="w-100">
            <?php echo esc_html__( 'Juros por método de pagamento', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Informe uma taxa de juros por método de pagamento para ser adicionado na finalização da compra.', 'woo-custom-installments' ) ?></span>
         </th>
      </tr>
      <tr id="wci-interest-header" class="display-enable-all-interest-options">
         <th>
            <?php echo __( 'Método de pagamento', 'woo-custom-installments' ); ?>
         </th>
         <th class="w-50">
            <?php echo __( 'Valor', 'woo-custom-installments' ); ?>
         </th>
         <th>
            <?php echo __( 'Método do juros', 'woo-custom-installments' ); ?>
         </th>
      </tr>
      <?php
      $payment_gateways = WC()->payment_gateways->payment_gateways();
      $insterestSettings = array();
      $insterestSettings = get_option( 'woo_custom_installments_interests_setting' );
      $insterestSettings = maybe_unserialize( $insterestSettings );

      foreach ( $payment_gateways as $gateway ) {
         $current = isset( $insterestSettings[ $gateway->id ]['amount'] ) ? $insterestSettings[ $gateway->id ]['amount'] : '0'; ?>
         
         <tr id="wci-interest-methods-<?php echo esc_attr( $gateway->id ); ?>" class="display-enable-all-interest-options foreach-method-discount wci-interest-methods">
            <th class="wci-title-method-interest-header">
               <label for="woo_custom_installments_payment_discounts_<?php echo esc_attr( $gateway->id ); ?>"><?php echo esc_attr( $gateway->title ); ?></label>
            </th>
            <td class="wci-title-method-interest-div">
               <div class="input-group wci-method-interest-selector" id="foreach-payment-<?php echo esc_attr( $gateway->id ); ?>-method-discount" name="form-interest-<?php echo esc_attr( $gateway->id ); ?>-method">
                  <span id="interest-method-result-payment-method-<?php echo esc_attr( $gateway->id ); ?>" class="input-group-text interest-method-result-payment-method" name="interest-method-result-payment-method[<?php echo esc_attr( $gateway->id ); ?>][type]">
                     <?php
                        if( isset( $insterestSettings[ $gateway->id ]['type'] ) && $insterestSettings[ $gateway->id ]['type'] == 'percentage' ) {
                           echo '%';
                        }
                        else {
                           echo get_woocommerce_currency_symbol();
                        }
                     ?>
                  </span>
                  <input type="text" class="form-control allow-number-and-dots input-control-wd-5 border-right-0 <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>" value="<?php echo esc_attr( $current ); ?>" id="woo_custom_installments_payment_discounts_<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_interests[<?php echo esc_attr( $gateway->id ); ?>][amount]"/>
                  <select class="form-select get-interest-method-payment-method <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>" id="woo-custom-installments-payment-discounts-type-<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_interests[<?php echo esc_attr( $gateway->id ); ?>][type]">
                     <option value="fixed" <?php if( isset( $insterestSettings[ $gateway->id ]['type'] ) && $insterestSettings[ $gateway->id ]['type'] == 'fixed' ) { echo 'selected="selected"'; } ?> ><?php echo esc_html__( 'Valor fixo (R$)', 'woo-custom-installments' ) ?></span></option>
                     <option value="percentage" <?php if( isset( $insterestSettings[ $gateway->id ]['type'] ) && $insterestSettings[ $gateway->id ]['type'] == 'percentage' ) { echo 'selected="selected"'; } ?> ><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></span></option>
                  </select>
               </div>
            </td>
         </tr>
      <?php } ?>
    </table>
</div>

