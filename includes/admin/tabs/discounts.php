<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit; } ?>

<div id="discount-settings" class="nav-content ">
    <table class="form-table" >
    </tr>
        <th>
            <?php echo esc_html__( 'Exibir preço com desconto no carrinho', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir o valor do preço com desconto também no carrinho. (Recomendado)', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
            <div class="form-check form-switch">
                <input type="checkbox" class="toggle-switch" id="display_installments_cart" name="display_installments_cart" value="yes" <?php checked( isset( $options['display_installments_cart'] ) == 'yes' ); ?> />
            </div>
        </td>
        </tr>
        <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
            <th>
                <?php echo esc_html__( 'Exibir preço com desconto no Schema', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
                <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir o preço com desconto em serviços de comparação de preços que fazem leitura de schema. (Recomendado)', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
                <div class="form-check form-switch">
                  <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="display_discount_price_schema" name="display_discount_price_schema" value="yes" <?php checked( isset( $options['display_discount_price_schema'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
                </div>
            </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Método do desconto no preço principal', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Escolha qual método matemático fará o desconto no preço principal do preduto.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <select id="product_price_discount_method" class="form-select get-discount-method-main-price" name="product_price_discount_method">
               <option value="percentage" <?php echo ( $this->getSetting( 'product_price_discount_method' ) == 'percentage' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></option>
               <option value="fixed" <?php echo ( $this->getSetting( 'product_price_discount_method' ) == 'fixed' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Valor fixo (R$)', 'woo-custom-installments' ) ?></option>
            </select>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Desconto no preço principal', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define qual será o valor de desconto sobre o preço principal do produto. Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group">
               <span id="method_result" class="input-group-text discount-method-result-main-price">
                  <?php
                     if( $this->getSetting( 'product_price_discount_method' ) == 'percentage' ) {
                        echo '%';
                     }
                     else {
                        echo get_woocommerce_currency_symbol();
                     }
                  ?>
               </span>
               <input type="text" id="discount_main_price" class="form-control input-control-wd-5 allow-number-and-dots" name="discount_main_price" placeholder="20" value="<?php echo $this->getSetting( 'discount_main_price' ) ?>">
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Mostrar emblema de desconto na finalização da compra', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir o emblema de desconto na página de finalização de compra para a forma de desconto configurada.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="display_tag_discount_price_checkout" name="display_tag_discount_price_checkout" value="yes" <?php checked( isset( $options['display_tag_discount_price_checkout'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Mostrar informação de desconto na revisão do pedido', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir a informação de desconto na página de finalização de compra para a forma de desconto configurada.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="display_info_discount_order_review_checkout" name="display_info_discount_order_review_checkout" value="yes" <?php checked( isset( $options['display_info_discount_order_review_checkout'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="mt-4 <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th class="w-100">
            <?php echo esc_html__( 'Desconto por método de pagamento', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Informe um desconto por método de pagamento para ser adicionado na finalização da compra.', 'woo-custom-installments' ) ?></span>
         </th>
      </tr>
      <tr id="wci-discount-header" class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo __( 'Método de pagamento', 'woo-custom-installments' ); ?>
         </th>
         <th class="w-50">
            <?php echo __( 'Valor', 'woo-custom-installments' ); ?>
         </th>
         <th>
            <?php echo __( 'Método do desconto', 'woo-custom-installments' ); ?>
         </th>
      </tr>
      <?php
      $payment_gateways = WC()->payment_gateways->payment_gateways();
      $discountSettings = array();
      $discountSettings = get_option( 'woo_custom_installments_discounts_setting' );
      $discountSettings = maybe_unserialize( $discountSettings );

      foreach ( $payment_gateways as $gateway ) :
         $current = isset( $discountSettings[ $gateway->id ]['amount'] ) ? $discountSettings[ $gateway->id ]['amount'] : '0';
         ?>
         <tr id="wci-discount-methods-<?php echo esc_attr( $gateway->id ); ?>" class="foreach-method-discount wci-discount-methods <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
            <th class="wci-title-method-discount-header">
               <label for="woo_custom_installments_payment_discounts_<?php echo esc_attr( $gateway->id ); ?>"><?php echo esc_attr( $gateway->title ); ?></label>
            </th>
            <td class="wci-title-method-discount-div">
               <div class="input-group wci-method-discount-selector" id="foreach-payment-<?php echo esc_attr( $gateway->id ); ?>-method-discount" name="form-discount-<?php echo esc_attr( $gateway->id ); ?>-method">
                  <span id="discount-method-result-payment-method-<?php echo esc_attr( $gateway->id ); ?>" class="input-group-text discount-method-result-payment-method" name="discount-method-result-payment-method[<?php echo esc_attr( $gateway->id ); ?>][type]">
                     <?php
                        if( isset( $discountSettings[ $gateway->id ]['type'] ) && $discountSettings[ $gateway->id ]['type'] == 'percentage' ) {
                           echo '%';
                        }
                        else {
                           echo get_woocommerce_currency_symbol();
                        }
                     ?>
                  </span>
                  <input type="text" class="form-control allow-number-and-dots input-control-wd-5 border-right-0 <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" value="<?php echo esc_attr( $current ); ?>" id="woo_custom_installments_payment_discounts_<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_discounts[<?php echo esc_attr( $gateway->id ); ?>][amount]"/>
                  <select class="form-select get-discount-method-payment-method <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="woo-custom-installments-payment-discounts-type-<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_discounts[<?php echo esc_attr( $gateway->id ); ?>][type]">
                     <option value="fixed" <?php if( isset( $discountSettings[ $gateway->id ]['type'] ) && $discountSettings[ $gateway->id ]['type'] == 'fixed' ) { echo 'selected="selected"'; } ?> ><?php echo esc_html__( 'Valor fixo (R$)', 'woo-custom-installments' ) ?></span></option>
                     <option value="percentage" <?php if( isset( $discountSettings[ $gateway->id ]['type'] ) && $discountSettings[ $gateway->id ]['type'] == 'percentage' ) { echo 'selected="selected"'; } ?> ><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></span></option>
                  </select>
               </div>
            </td>
         </tr>
      <?php endforeach; ?>
    </table>
</div>

