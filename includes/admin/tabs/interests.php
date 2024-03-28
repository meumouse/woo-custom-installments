<?php

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="interests" class="nav-content">
    <table class="form-table">
      <tr>
        <th>
            <?php echo esc_html__( 'Ativar funções de juros', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Ative esta opção para habilitar todas as opções relacionadas a juros.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
            <div class="form-check form-switch">
                <input type="checkbox" class="toggle-switch" id="enable_all_interest_options" name="enable_all_interest_options" value="yes" <?php checked( self::get_setting( 'enable_all_interest_options') == 'yes' ); ?> />
            </div>
        </td>
      </tr>
      <tr class="display-enable-all-interest-options">
         <th>
            <?php echo esc_html__( 'Mostrar emblema de juros na finalização da compra', 'woo-custom-installments' );

            if ( ! self::license_valid() ) {

               ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
               <?php
            }
            ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir o emblema de juros na página de finalização de compra para a forma de desconto configurada.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch <?php echo ( self::license_valid() ) ? '': 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( self::license_valid() ) ? '': 'pro-version'; ?>" id="display_tag_interest_checkout" name="display_tag_interest_checkout" value="yes" <?php checked( self::get_setting( 'display_tag_interest_checkout') == 'yes' && self::license_valid() ); ?> />
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-interest-options mt-4">
         <th class="w-100">
            <?php echo esc_html__( 'Juros por método de pagamento', 'woo-custom-installments' );

            if ( ! self::license_valid() ) {

               ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
               <?php
            }
            ?>
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
                     <?php echo ( isset( $insterestSettings[ $gateway->id ]['type'] ) && $insterestSettings[ $gateway->id ]['type'] === 'percentage' ) ? '%' : get_woocommerce_currency_symbol(); ?>
                  </span>
                  <input type="text" class="form-control allow-number-and-dots input-control-wd-5 <?php echo ( self::license_valid() ) ? '': 'pro-version-notice'; ?>" value="<?php echo esc_attr( $current ); ?>" id="woo_custom_installments_payment_discounts_<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_interests[<?php echo esc_attr( $gateway->id ); ?>][amount]"/>
                  <select class="form-select get-interest-method-payment-method <?php echo ( self::license_valid() ) ? '': 'pro-version-notice'; ?>" id="woo-custom-installments-payment-discounts-type-<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_interests[<?php echo esc_attr( $gateway->id ); ?>][type]">
                     <option value="fixed" <?php if( isset( $insterestSettings[ $gateway->id ]['type'] ) && $insterestSettings[ $gateway->id ]['type'] == 'fixed' ) { echo 'selected="selected"'; } ?> ><?php echo esc_html__( 'Valor fixo (R$)', 'woo-custom-installments' ) ?></span></option>
                     <option value="percentage" <?php if( isset( $insterestSettings[ $gateway->id ]['type'] ) && $insterestSettings[ $gateway->id ]['type'] == 'percentage' ) { echo 'selected="selected"'; } ?> ><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></span></option>
                  </select>
               </div>
            </td>
         </tr>
      <?php } ?>
    </table>
</div>

