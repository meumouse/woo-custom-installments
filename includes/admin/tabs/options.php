<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit; } ?>

  
  <div id="general-settings" class="nav-content active">
  <table class="form-table">
     <tr>
        <th>
           <?php echo esc_html__( 'Ativar para todos os produtos', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá exibir as formas de pagamento, descontos e parcelamento para todos os produtos.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="enable_installments_all_products" name="enable_installments_all_products" value="yes" <?php checked( isset( $options['enable_installments_all_products'] ) == 'yes' ); ?> />
           </div>
        </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Exibir emblema de aprovação imediata', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá exibir o emblema de aprovação imediata no pix, cartão de crédito e débito.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="enable_instant_approval_badge" name="enable_instant_approval_badge" value="yes" <?php checked( isset( $options['enable_instant_approval_badge'] ) == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Remover faixa de preço em produtos variáveis', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá remover a faixa de preço em produtos variáveis de valores diferentes.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="remove_price_range" name="remove_price_range" value="yes" <?php checked( isset( $options['remove_price_range'] ) == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr id="fee-global-settings">
        <th>
           <?php echo esc_html__( 'Taxa de juros padrão', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Percentual da taxa de juros, para realizar o cálculo do valor de cada parcela.', 'woo-custom-installments' ) ?></span>
         </th>
        <td>
           <div class="input-group">
              <span id="method_result_default_fee" class="input-group-text">%</span>
              <input type="text" id="fee_installments_global" class="form-control input-control-wd-5 allow-number-and-dots" name="fee_installments_global" value="<?php echo $this->getSetting( 'fee_installments_global' ) ?>" placeholder="2.0"/>
           </div>
        </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Quantidade máxima de parcelas', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define a quantidade máxima de parcelas que será exibida.' ) ?></span>
        </th>
        <td>
           <input type="number" id="max_qtd_installments" class="form-control allow-numbers-be-1" name="max_qtd_installments" value="<?php echo $this->getSetting( 'max_qtd_installments' ) ?>"/>
        </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Quantidade máxima de parcelas sem juros', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define a quantidade máxima de parcelas sem juros que será exibida.' ) ?></span>
        </th>
        <td>
           <input type="number" id="max_qtd_installments_without_fee" class="form-control allow-numbers-be-1" name="max_qtd_installments_without_fee" value="<?php echo $this->getSetting( 'max_qtd_installments_without_fee' ) ?>"/>
        </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Valor mínimo da parcela', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define qual será o valor mínimo da parcela que o cliente pagará.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="input-group input-control-wd-5">
              <span id="method_result" class="input-group-text"><?php echo get_woocommerce_currency_symbol(); ?></span>
              <input type="number" id="min_value_installments" class="form-control input-control-wd-5 allow-numbers-be-1" name="min_value_installments" placeholder="20" value="<?php echo $this->getSetting( 'min_value_installments' ) ?>">
           </div>
        </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
        <th>
           <?php echo esc_html__( 'Definir taxa de juros por parcela', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, permite informar uma taxa de juros personalizada para cada parcela.', 'woo-custom-installments' ) ?></span>
         </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="set_fee_per_installment" name="set_fee_per_installment" value="yes" <?php checked( isset( $options['set_fee_per_installment'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
           </div>
        </td>
      </tr>
      <tr id="set-custom-fee-per-installment" class="d-none <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <td>
            <fieldset>
               <?php
                  $customFeeInstallments = array();
                  $customFeeInstallments = get_option( 'woo_custom_installments_custom_fee_installments' );
                  $customFeeInstallments = maybe_unserialize( $customFeeInstallments );
                  $maxInstallmentsWithoutFee = $this->getSetting( 'max_qtd_installments_without_fee' ) + 1;
                  $limitInstallments = $this->getSetting( 'max_qtd_installments' );
                  
                  for ( $i = $maxInstallmentsWithoutFee; $i <= $limitInstallments; $i++ ) :
                  $current_custom_fee = isset( $customFeeInstallments[ $i ]['amount'] ) ? floatval( $customFeeInstallments[ $i ]['amount'] ) : 0;
                  ?>
                  <div class="input-group mb-2">
                     <div data-installment="<?php echo $i; ?>">
                        <input class="custom-installment-first small-input form-control" type="text" disabled value="<?php echo $i; ?>"/>
                        <input class="custom-installment-secondary small-input form-control allow-number-and-dots" type="text" placeholder="1.0" name="custom_fee_installments[<?php echo $i; ?>][amount]" id="custom_fee_installments[<?php echo $i; ?>]" value="<?php echo esc_attr( $current_custom_fee ); ?>" />
                     </div>
                  </div>
               <?php endfor; ?>
            </fieldset>
         </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Local de exibição do preço com desconto', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione onde o preço com desconto será exibido.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <select name="display_discount_price_hook" class="form-select">
              <option value="display_loop_and_single_product" <?php echo ($this->getSetting( 'display_discount_price_hook' ) == 'display_loop_and_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Página do produto individual e arquivos (Padrão)', 'woo-custom-installments' ) ?></option>
              <option value="only_single_product" <?php echo ($this->getSetting( 'display_discount_price_hook' ) == 'only_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas página do produto individual', 'woo-custom-installments' ) ?></option>
              <option value="only_loop_products" <?php echo ($this->getSetting( 'display_discount_price_hook' ) == 'only_loop_products' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas arquivos de produtos', 'woo-custom-installments' ) ?></option>
           </select>
        </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Exibir melhor parcela', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione o tipo de exibição da melhor parcela.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
           <select name="get_type_best_installments" class="form-select">
              <option value="hide" <?php echo ( $this->getSetting( 'get_type_best_installments' ) == 'hide' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Ocultar', 'woo-custom-installments' ) ?></option>
              <option value="best_installment_without_fee" <?php echo ( $this->getSetting( 'get_type_best_installments' ) == 'best_installment_without_fee' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Melhor parcela sem juros', 'woo-custom-installments' ) ?></option>
              <option value="best_installment_with_fee" <?php echo ( $this->getSetting( 'get_type_best_installments' ) == 'best_installment_with_fee' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Melhor parcela com juros', 'woo-custom-installments' ) ?></option>
              <option value="both" <?php echo ( $this->getSetting( 'get_type_best_installments' ) == 'both' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Melhor parcela com e sem juros', 'woo-custom-installments' ) ?></option>
           </select>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Local de exibição das melhores parcelas', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione o local para exibir a informação das melhores parcelas.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <select name="hook_display_best_installments" class="form-select">
               <option value="display_loop_and_single_product" <?php echo ( $this->getSetting( 'hook_display_best_installments' ) == 'display_loop_and_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Página do produto individual e arquivos (Padrão)', 'woo-custom-installments' ) ?></option>
               <option value="only_single_product" <?php echo ( $this->getSetting( 'hook_display_best_installments' ) == 'only_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas página do produto individual', 'woo-custom-installments' ) ?></option>
               <option value="only_loop_products" <?php echo ( $this->getSetting( 'hook_display_best_installments' ) == 'only_loop_products' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas arquivos de produtos', 'woo-custom-installments' ) ?></option>
            </select>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Posição da melhor parcela', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione a posição onde a melhor parcela deve ser exibida.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <select name="hook_display_best_installments_after_before_discount" class="form-select">
              <option value="before_discount" <?php echo ( $this->getSetting( 'hook_display_best_installments_after_before_discount' ) == 'before_discount' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Antes do desconto', 'woo-custom-installments' ) ?></option>
              <option value="after_discount" <?php echo ( $this->getSetting( 'hook_display_best_installments_after_before_discount' ) == 'after_discount' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Depois do desconto (Padrão)', 'woo-custom-installments' ) ?></option>
           </select>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Tipo de exibição das parcelas', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione o tipo de exibição das parcelas na página de produto individual.', 'woo-custom-installments' ) ?></span>
         </th>
        <td>
           <select name="display_installment_type" class="form-select">
              <option value="popup" <?php echo ( $this->getSetting( 'display_installment_type' ) == 'popup' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Popup', 'woo-custom-installments' ) ?></option>
              <option value="accordion" <?php echo ( $this->getSetting( 'display_installment_type' ) == 'accordion' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Sanfona', 'woo-custom-installments' ) ?></option>
           </select>
        </td>
     </tr>
     <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
        <th>
           <?php echo esc_html__( 'Posição das formas de pagamento e parcelas na página de produto individual', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione onde o gancho que será exibido as formas de pagamento na página de produto individual. Shortcode disponível: [woo_custom_installments_modal]', 'woo-custom-installments' ) ?></span>
       </th>
        <td>
           <select name="hook_payment_form_single_product" class="form-select <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
              <option value="before_cart" <?php echo ( $this->getSetting( 'hook_payment_form_single_product' ) == 'before_cart' && $this->responseObj->is_valid ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Antes do carrinho (Padrão)', 'woo-custom-installments' ) ?></option>
              <option value="after_cart" <?php echo ( $this->getSetting( 'hook_payment_form_single_product' ) == 'after_cart' && $this->responseObj->is_valid ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Depois do carrinho', 'woo-custom-installments' ) ?></option>
              <option value="shortcode" <?php echo ( $this->getSetting( 'hook_payment_form_single_product' ) == 'shortcode' && $this->responseObj->is_valid ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Shortcode', 'woo-custom-installments' ) ?></option>
           </select>
        </td>
     </tr>
     <tr>
        <th>
           <?php echo esc_html__( 'Desativar atualização de valores na finalização de compra', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__('Ative esta opção para evitar a atualização de valores na finalização de compra, ou se estiver com carregamento infinito na finalização da compra.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="disable_update_checkout" name="disable_update_checkout" value="yes" <?php checked( isset( $options['disable_update_checkout'] ) == 'yes' ); ?> />
           </div>
        </td>
      </tr>
  </table>
</div>