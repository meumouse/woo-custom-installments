<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div id="discount-settings" class="nav-content ">
    <table class="form-table" >
      <tr>
        <th>
            <?php echo esc_html__( 'Habilitar funções de descontos', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Ative esta opção para habilitar todas as opções relacionadas a desconto.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
            <div class="form-check form-switch">
                <input type="checkbox" class="toggle-switch" id="enable_all_discount_options" name="enable_all_discount_options" value="yes" <?php checked( $this->getSetting( 'enable_all_discount_options') == 'yes' ); ?> />
            </div>
        </td>
      </tr>
      <tr class="display-enable-all-discount-options">
        <th>
            <?php echo esc_html__( 'Exibir preço com desconto no carrinho', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir o valor do preço com desconto também no carrinho. (Recomendado)', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
            <div class="form-check form-switch">
                <input type="checkbox" class="toggle-switch" id="display_installments_cart" name="display_installments_cart" value="yes" <?php checked( $this->getSetting( 'display_installments_cart') == 'yes' ); ?> />
            </div>
        </td>
      </tr>
      <tr class="display-enable-all-discount-options">
        <th>
            <?php echo esc_html__( 'Incluir valor de frete no desconto do pedido', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá incluir o valor de frete no cálculo de desconto na finalização da compra. (Recomendado)', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
            <div class="form-check form-switch">
                <input type="checkbox" class="toggle-switch" id="include_shipping_value_in_discounts" name="include_shipping_value_in_discounts" value="yes" <?php checked( $this->getSetting( 'include_shipping_value_in_discounts') == 'yes' ); ?> />
            </div>
        </td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <th>
               <?php echo esc_html__( 'Exibir preço com desconto em dados estruturados (Schema.org)', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
               <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibir o preço com desconto em serviços que fazem a leitura de dados estruturados ou "Rich snippets", para ajudar o produto em SEO. (Recomendado)', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
               <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="display_discount_price_schema" name="display_discount_price_schema" value="yes" <?php checked( $this->getSetting( 'display_discount_price_schema') == 'yes' && $this->responseObj->is_valid ); ?> />
               </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <th>
            <?php echo esc_html__( 'Desconto no preço do produto', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define qual será o valor de desconto sobre o preço do produto, para . Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group">
               <span class="input-group-text discount-method-result-main-price method-result">
                  <?php
                     if ( $this->getSetting( 'product_price_discount_method' ) == 'percentage' ) {
                        echo '%';
                     }
                     else {
                        echo get_woocommerce_currency_symbol();
                     }
                  ?>
               </span>

               <input type="text" id="discount_main_price" class="form-control input-control-wd-5 border-right-0 allow-number-and-dots" name="discount_main_price" placeholder="20" value="<?php echo $this->getSetting( 'discount_main_price' ) ?>">
               
               <select id="product_price_discount_method" class="form-select get-discount-method-main-price" name="product_price_discount_method">
                  <option value="percentage" <?php echo ( $this->getSetting( 'product_price_discount_method' ) == 'percentage' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></option>
                  <option value="fixed" <?php echo ( $this->getSetting( 'product_price_discount_method' ) == 'fixed' ) ? "selected=selected" : ""; ?>><?php echo sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options admin-discount-ticket-option">
         <th>
            <?php echo esc_html__( 'Desconto no boleto bancário', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define qual será o valor de desconto sobre o preço do produto para boleto bancário. Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group">
               <span class="input-group-text discount-method-result-ticket method-result">
                  <?php
                     if ( $this->getSetting( 'discount_method_ticket' ) == 'percentage' ) {
                        echo '%';
                     }
                     else {
                        echo get_woocommerce_currency_symbol();
                     }
                  ?>
               </span>
               <input type="text" id="discount_ticket" class="form-control input-control-wd-5 allow-number-and-dots" name="discount_ticket" placeholder="20" value="<?php echo $this->getSetting( 'discount_ticket' ) ?>">
               <select id="discount_method_ticket" class="form-select get-discount-method-ticket" name="discount_method_ticket">
                  <option value="percentage" <?php echo ( $this->getSetting( 'discount_method_ticket' ) == 'percentage' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></option>
                  <option value="fixed" <?php echo ( $this->getSetting( 'discount_method_ticket' ) == 'fixed' ) ? "selected=selected" : ""; ?>><?php echo sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <td class="container-separator"></td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <th>
            <?php echo esc_html__( 'Habilitar funções de desconto por quantidade', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá ser adicionado um desconto a partir de uma quantidade mínima do produto no carrinho, para todos os produtos da loja.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_functions_discount_per_quantity" name="enable_functions_discount_per_quantity" value="yes" <?php checked( $this->getSetting( 'enable_functions_discount_per_quantity') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options table-row-set-quantity-enable-discount disable-discount-per-product-global">
         <th>
            <?php echo esc_html__( 'Oferecer desconto por quantidade do produto (Global)', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá ser adicionado um desconto a partir de uma quantidade mínima do produto no carrinho, para todos os produtos da loja.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="set_discount_per_quantity_global" name="set_discount_per_quantity_global" value="yes" <?php checked( $this->getSetting( 'set_discount_per_quantity_global') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options table-row-set-quantity-enable-discount disable-discount-per-product-single">
         <th>
            <?php echo esc_html__( 'Oferecer desconto por quantidade do produto (Produto individual)', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá exibido as configurações para configuração de desconto por quantidade para cada produto individualmente.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_functions_discount_per_quantity_single_product" name="enable_functions_discount_per_quantity_single_product" value="yes" <?php checked( $this->getSetting( 'enable_functions_discount_per_quantity_single_product') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options table-row-set-quantity-enable-discount">
         <th>
            <?php echo esc_html__( 'Ativar cálculo de desconto para cada unidade do produto elegível', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá ser considerado o desconto para cada unidade do produto.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="enable_discount_per_unit_discount_per_quantity" name="enable_discount_per_unit_discount_per_quantity" value="yes" <?php checked( $this->getSetting( 'enable_discount_per_unit_discount_per_quantity') == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options table-row-set-quantity-enable-discount">
         <th>
            <?php echo esc_html__( 'Ativar mensagem nos produtos elegíveis para desconto por quantidade', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, irá ser adicionado a mensagem informando o usuário que o produto é elegível para desconto por quantidade.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="message_discount_per_quantity" name="message_discount_per_quantity" value="yes" <?php checked( $this->getSetting( 'message_discount_per_quantity') == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options table-row-set-quantity-enable-discount">
         <th>
            <?php echo esc_html__( 'Valor do desconto por quantidade', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define qual será o valor de desconto por quantidade do produto.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="input-group">
               <span class="input-group-text discount-per-quantity-method-result method-result">
                  <?php
                     if ( $this->getSetting( 'discount_per_quantity_method' ) == 'percentage' ) {
                        echo '%';
                     }
                     else {
                        echo get_woocommerce_currency_symbol();
                     }
                  ?>
               </span>
               <input type="text" id="value_for_discount_per_quantity" class="form-control input-control-wd-5 border-right-0 allow-number-and-dots" name="value_for_discount_per_quantity" placeholder="20" value="<?php echo $this->getSetting( 'value_for_discount_per_quantity' ) ?>">
               
               <select id="discount_per_quantity_method" class="form-select get-discount-per-quantity-method" name="discount_per_quantity_method">
                  <option value="percentage" <?php echo ( $this->getSetting( 'discount_per_quantity_method' ) == 'percentage' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></option>
                  <option value="fixed" <?php echo ( $this->getSetting( 'discount_per_quantity_method' ) == 'fixed' ) ? "selected=selected" : ""; ?>><?php echo sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ) ?></option>
               </select>
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options table-row-set-quantity-enable-discount <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
        <th>
           <?php echo esc_html__( 'Quantidade mínima para oferecer desconto', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Informe a quantidade mínima do produto para oferecer desconto no carrinho.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <input type="number" id="set_quantity_enable_discount" class="form-control allow-numbers-be-1 input-control-wd-7-7rem" name="set_quantity_enable_discount" value="<?php echo $this->getSetting( 'set_quantity_enable_discount' ) ?>"/>
        </td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <td class="container-separator"></td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <th>
            <?php echo esc_html__( 'Mostrar informação de desconto na forma de pagamento', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, será exibido o emblema de desconto ao lado do título da forma de pagamento configurada.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="display_tag_discount_price_checkout" name="display_tag_discount_price_checkout" value="yes" <?php checked( $this->getSetting( 'display_tag_discount_price_checkout') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="display-enable-all-discount-options">
         <th class="w-100">
            <?php echo esc_html__( 'Desconto por método de pagamento', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Informe um desconto por método de pagamento para ser adicionado na finalização da compra.', 'woo-custom-installments' ) ?></span>
         </th>
      </tr>
      <tr id="wci-discount-header" class="display-enable-all-discount-options">
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

      foreach ( $payment_gateways as $gateway ) {
         $current = isset( $discountSettings[ $gateway->id ]['amount'] ) ? $discountSettings[ $gateway->id ]['amount'] : '0'; ?>

         <tr id="wci-discount-methods-<?php echo esc_attr( $gateway->id ); ?>" class="display-enable-all-discount-options foreach-method-discount wci-discount-methods">
            <th class="wci-title-method-discount-header">
               <label for="woo_custom_installments_payment_discounts_<?php echo esc_attr( $gateway->id ); ?>"><?php echo esc_attr( $gateway->title ); ?></label>
            </th>
            <td class="wci-title-method-discount-div">
               <div class="input-group wci-method-discount-selector" id="foreach-payment-<?php echo esc_attr( $gateway->id ); ?>-method-discount" name="form-discount-<?php echo esc_attr( $gateway->id ); ?>-method">
                  <span id="discount-method-result-payment-method-<?php echo esc_attr( $gateway->id ); ?>" class="input-group-text discount-method-result-payment-method" name="discount-method-result-payment-method[<?php echo esc_attr( $gateway->id ); ?>][type]">
                     <?php
                        if ( isset( $discountSettings[ $gateway->id ]['type'] ) && $discountSettings[ $gateway->id ]['type'] == 'percentage' ) {
                           echo '%';
                        }
                        else {
                           echo get_woocommerce_currency_symbol();
                        }
                     ?>
                  </span>
                  <input type="text" class="form-control allow-number-and-dots input-control-wd-5 border-right-0 <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>" value="<?php echo esc_attr( $current ); ?>" id="woo_custom_installments_payment_discounts_<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_discounts[<?php echo esc_attr( $gateway->id ); ?>][amount]"/>
                  <select class="form-select get-discount-method-payment-method <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>" id="woo-custom-installments-payment-discounts-type-<?php echo esc_attr( $gateway->id ); ?>" name="woo_custom_installments_discounts[<?php echo esc_attr( $gateway->id ); ?>][type]">
                     <option value="fixed" <?php if ( isset( $discountSettings[ $gateway->id ]['type'] ) && $discountSettings[ $gateway->id ]['type'] == 'fixed' ) { echo 'selected="selected"'; } ?> ><?php echo sprintf( __( 'Valor fixo (%s)', 'woo-custom-installments' ), get_woocommerce_currency_symbol() ) ?></span></option>
                     <option value="percentage" <?php if ( isset( $discountSettings[ $gateway->id ]['type'] ) && $discountSettings[ $gateway->id ]['type'] == 'percentage' ) { echo 'selected="selected"'; } ?> ><?php echo esc_html__( 'Percentual (%)', 'woo-custom-installments' ) ?></span></option>
                  </select>
               </div>
            </td>
         </tr>
      <?php } ?>
    </table>
</div>