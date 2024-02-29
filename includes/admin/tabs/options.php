<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; 

?>

<div id="general-settings" class="nav-content ">
   <table class="form-table">
      <tr>
        <th>
           <?php echo esc_html__( 'Ativar para todos os produtos', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá exibir as formas de pagamento, descontos e parcelamento para todos os produtos.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="enable_installments_all_products" name="enable_installments_all_products" value="yes" <?php checked( self::get_setting( 'enable_installments_all_products') == 'yes' ); ?> />
           </div>
        </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Remover faixa de preço em produtos variáveis', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá remover a faixa de preço em produtos variáveis e o preço será alterado dinâmicamente ao selecionar uma variação.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="remove_price_range" name="remove_price_range" value="yes" <?php checked( self::get_setting( 'remove_price_range') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Mostrar informação de economia no Pix', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative esta opção para mostrar o valor que o usuário economizará ao pagar no Pix.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_economy_pix_badge" name="enable_economy_pix_badge" value="yes" <?php checked( self::get_setting( 'enable_economy_pix_badge') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Adicionar texto personalizado após o preço do produto', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá adicionar um texto personalizado após o preço do produto. Por exemplo: R$100,00 no Pix.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="custom_text_after_price" name="custom_text_after_price" value="yes" <?php checked( self::get_setting( 'custom_text_after_price') == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr>
         <td class="container-separator"></td>
      </tr>
      <tr id="fee-global-settings">
        <th>
           <?php echo esc_html__( 'Taxa de juros padrão', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Percentual da taxa de juros, para realizar o cálculo do valor de cada parcela.', 'woo-custom-installments' ) ?></span>
         </th>
        <td>
           <div class="input-group">
              <span id="method_result_default_fee" class="input-group-text">%</span>
              <input type="text" id="fee_installments_global" class="form-control input-control-wd-5 allow-number-and-dots" name="fee_installments_global" value="<?php echo self::get_setting( 'fee_installments_global' ) ?>" placeholder="2.0"/>
           </div>
        </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Quantidade máxima de parcelas', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define a quantidade máxima de parcelas que será exibida.' ) ?></span>
        </th>
        <td>
           <input type="number" id="max_qtd_installments" class="form-control allow-numbers-be-1 input-control-wd-7-7rem" name="max_qtd_installments" value="<?php echo self::get_setting( 'max_qtd_installments' ) ?>"/>
        </td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Quantidade máxima de parcelas sem juros', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Define a quantidade máxima de parcelas sem juros que será exibida.' ) ?></span>
        </th>
        <td>
           <input type="number" id="max_qtd_installments_without_fee" class="form-control allow-numbers-be-0 input-control-wd-7-7rem" name="max_qtd_installments_without_fee" value="<?php echo self::get_setting( 'max_qtd_installments_without_fee' ) ?>"/>
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
              <input type="number" id="min_value_installments" class="form-control input-control-wd-5 allow-numbers-be-1 input-control-wd-7-7rem" name="min_value_installments" placeholder="20" value="<?php echo self::get_setting( 'min_value_installments' ) ?>">
           </div>
        </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Definir taxa de juros por parcela', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, permite informar uma taxa de juros personalizada para cada parcela.', 'woo-custom-installments' ) ?></span>
            </th>
         <td class="d-flex align-items-center">
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="set_fee_per_installment" name="set_fee_per_installment" value="yes" <?php checked( self::get_setting( 'set_fee_per_installment') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
            <button id="set_custom_fee_trigger" class="btn btn-outline-primary ms-3 set-custom-fee-per-installment"><?php echo esc_html__( 'Configurar', 'woo-custom-installments' ) ?></button>
            <div id="set_custom_fee_container">
               <div class="popup-content">
                  <div class="popup-header">
                     <h5 class="popup-title"><?php echo esc_html__( 'Configure o juros por parcela', 'woo-custom-installments' ); ?></h5>
                     <button id="set_custom_fee_close" class="btn-close fs-lg" aria-label="<?php echo esc_html__( 'Fechar', 'woo-custom-installments' ) ?>"></button>
                  </div>
                  <div class="popup-body">
                     <table class="popup-table">
                        <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
                           <td>
                              <fieldset id="custom-installments-fieldset-custom-installments">
                                 <?php
                                    $customFeeInstallments = array();
                                    $customFeeInstallments = get_option( 'woo_custom_installments_custom_fee_installments' );
                                    $customFeeInstallments = maybe_unserialize( $customFeeInstallments );
                                    $limitInstallments = self::get_setting( 'max_qtd_installments' );
                                    $init_loop = self::get_setting( 'max_qtd_installments_without_fee' ) + 1;
                                    
                                    for ( $i = $init_loop; $i <= $limitInstallments; $i++ ) {
                                       $current_custom_fee = isset( $customFeeInstallments[ $i ]['amount'] ) ? floatval( $customFeeInstallments[ $i ]['amount'] ) : 0;
                                       ?>
                                       <div class="input-group mb-2">
                                          <div data-installment="<?php echo $i; ?>">
                                             <input class="custom-installment-first small-input form-control" type="text" disabled value="<?php echo $i; ?>"/>
                                             <input class="custom-installment-secondary small-input form-control allow-number-and-dots" type="text" placeholder="1.0" name="custom_fee_installments[<?php echo $i; ?>][amount]" id="custom_fee_installments[<?php echo $i; ?>]" value="<?php echo esc_attr( $current_custom_fee ); ?>" />
                                          </div>
                                       </div>
                                       <?php
                                    }
                                    ?>
                              </fieldset>
                           </td>
                        </tr>
                     </table>
                  </div>
               </div>
            </div>
         </td>
      </tr>
      <tr>
         <td class="container-separator"></td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Local de exibição do preço com desconto', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione onde o preço com desconto será exibido.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <select name="display_discount_price_hook" class="form-select">
              <option value="display_loop_and_single_product" <?php echo (self::get_setting( 'display_discount_price_hook' ) == 'display_loop_and_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Mostrar em todo o site (Padrão)', 'woo-custom-installments' ) ?></option>
              <option value="only_single_product" <?php echo (self::get_setting( 'display_discount_price_hook' ) == 'only_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas página do produto individual', 'woo-custom-installments' ) ?></option>
              <option value="only_loop_products" <?php echo (self::get_setting( 'display_discount_price_hook' ) == 'only_loop_products' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas arquivos de produtos', 'woo-custom-installments' ) ?></option>
              <option value="hide" <?php echo (self::get_setting( 'display_discount_price_hook' ) == 'hide' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Ocultar', 'woo-custom-installments' ) ?></option>
           </select>
        </td>
      </tr>
      <tr class="economy-pix-dependency">
        <th>
           <?php echo esc_html__( 'Local de exibição da informação de economia no Pix', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione onde o preço com desconto será exibido.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <select name="display_economy_pix_hook" class="form-select">
               <option value="only_single_product" <?php echo (self::get_setting( 'display_economy_pix_hook' ) == 'only_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas página do produto individual (Padrão)', 'woo-custom-installments' ) ?></option>
              <option value="global" <?php echo (self::get_setting( 'display_economy_pix_hook' ) == 'global' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Mostrar em todo o site', 'woo-custom-installments' ) ?></option>
              <option value="only_loop_products" <?php echo (self::get_setting( 'display_economy_pix_hook' ) == 'only_loop_products' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas arquivos de produtos', 'woo-custom-installments' ) ?></option>
           </select>
        </td>
      </tr>
      <tr class="admin-discount-ticket-option">
        <th>
           <?php echo esc_html__( 'Local de exibição desconto no boleto bancário', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione onde o preço com desconto será exibido.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <select name="display_discount_ticket_hook" class="form-select">
               <option value="global" <?php echo (self::get_setting( 'display_discount_ticket_hook' ) == 'global' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Mostrar em todo o site (Padrão)', 'woo-custom-installments' ) ?></option>
               <option value="only_single_product" <?php echo (self::get_setting( 'display_discount_ticket_hook' ) == 'only_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas página do produto individual', 'woo-custom-installments' ) ?></option>
               <option value="only_loop_products" <?php echo (self::get_setting( 'display_discount_ticket_hook' ) == 'only_loop_products' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas arquivos de produtos', 'woo-custom-installments' ) ?></option>
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
               <option value="display_loop_and_single_product" <?php echo ( self::get_setting( 'hook_display_best_installments' ) == 'display_loop_and_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Mostrar em todo o site (Padrão)', 'woo-custom-installments' ) ?></option>
               <option value="only_single_product" <?php echo ( self::get_setting( 'hook_display_best_installments' ) == 'only_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas página do produto individual', 'woo-custom-installments' ) ?></option>
               <option value="only_loop_products" <?php echo ( self::get_setting( 'hook_display_best_installments' ) == 'only_loop_products' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas arquivos de produtos', 'woo-custom-installments' ) ?></option>
            </select>
         </td>
      </tr>
      <tr>
         <td class="container-separator"></td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Exibir melhor parcela', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione o tipo de exibição da melhor parcela.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
           <select name="get_type_best_installments" class="form-select">
              <option value="hide" <?php echo ( self::get_setting( 'get_type_best_installments' ) == 'hide' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Ocultar', 'woo-custom-installments' ) ?></option>
              <option value="best_installment_without_fee" <?php echo ( self::get_setting( 'get_type_best_installments' ) == 'best_installment_without_fee' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Melhor parcela sem juros', 'woo-custom-installments' ) ?></option>
              <option value="best_installment_with_fee" <?php echo ( self::get_setting( 'get_type_best_installments' ) == 'best_installment_with_fee' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Melhor parcela com juros', 'woo-custom-installments' ) ?></option>
              <option value="both" <?php echo ( self::get_setting( 'get_type_best_installments' ) == 'both' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Melhor parcela com e sem juros', 'woo-custom-installments' ) ?></option>
           </select>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Tipo de exibição das parcelas', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione o tipo de exibição das parcelas na página de produto individual.', 'woo-custom-installments' ) ?></span>
         </th>
        <td>
           <select id="display_installment_type" name="display_installment_type" class="form-select">
              <option value="popup" <?php echo ( self::get_setting( 'display_installment_type' ) == 'popup' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Popup (Padrão)', 'woo-custom-installments' ) ?></option>
              <option value="accordion" <?php echo ( self::get_setting( 'display_installment_type' ) == 'accordion' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Sanfona', 'woo-custom-installments' ) ?></option>
              <option value="hide" <?php echo ( self::get_setting( 'display_installment_type' ) == 'hide' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Ocultar', 'woo-custom-installments' ) ?></option>
           </select>
        </td>
     </tr>
     <tr class="tr-position-installment-type-button">
        <th>
           <?php echo esc_html__( 'Posição das formas de pagamento e parcelas na página de produto individual', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione onde o gancho que será exibido as formas de pagamento na página de produto individual.', 'woo-custom-installments' ) ?></span>
           <span id="display-shortcode-info" class="woo-custom-installments-description mt-2 <?php echo ( self::get_setting( 'hook_payment_form_single_product' ) != 'shortcode' ) ? "d-none" : ""; ?>"><?php echo esc_html__( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_modal]</code></span>
       </th>
        <td>
           <select id="hook_payment_form_single_product" name="hook_payment_form_single_product" class="form-select <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
              <option value="before_cart" <?php echo ( self::get_setting( 'hook_payment_form_single_product' ) == 'before_cart' && $this->responseObj->is_valid ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Antes do carrinho (Padrão)', 'woo-custom-installments' ) ?></option>
              <option value="after_cart" <?php echo ( self::get_setting( 'hook_payment_form_single_product' ) == 'after_cart' && $this->responseObj->is_valid ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Depois do carrinho', 'woo-custom-installments' ) ?></option>
              <option value="shortcode" <?php echo ( self::get_setting( 'hook_payment_form_single_product' ) == 'shortcode' && $this->responseObj->is_valid ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Shortcode', 'woo-custom-installments' ) ?></option>
           </select>
        </td>
      </tr>
      <tr>
         <td class="container-separator"></td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Ordem dos elementos', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Reposicione os elementos arrastando e soltando.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div id="reorder_wci_elements">
               <ul class="sortable">
                  <li class="tab-item" id="best_installments_order">
                     <span><?php echo esc_html__( 'Melhor parcela', 'woo-custom-installments' ) ?></span>
                     <input type="hidden" name="best_installments_order" value="<?php echo self::get_setting( 'best_installments_order' ) ?>"/>
                  </li>
                  <li class="tab-item" id="discount_pix_order">
                     <span><?php echo esc_html__( 'Desconto no Pix', 'woo-custom-installments' ) ?></span>
                     <input type="hidden" name="discount_pix_order" value="<?php echo self::get_setting( 'discount_pix_order' ) ?>"/>
                  </li>
                  <li class="tab-item" id="economy_pix_order">
                     <span><?php echo esc_html__( 'Economia no Pix', 'woo-custom-installments' ) ?></span>
                     <input type="hidden" name="economy_pix_order" value="<?php echo self::get_setting( 'economy_pix_order' ) ?>"/>
                  </li>
                  <li class="tab-item" id="slip_bank_order">
                     <span><?php echo esc_html__( 'Desconto no boleto bancário', 'woo-custom-installments' ) ?></span>
                     <input type="hidden" name="slip_bank_order" value="<?php echo self::get_setting( 'slip_bank_order' ) ?>"/>
                  </li>
               </ul>
            </div>
         </td>
      </tr>
      <tr>
         <td class="container-separator"></td>
      </tr>
      <tr>
        <th>
           <?php echo esc_html__( 'Desativar atualização dinâmica de parcelas em produtos variáveis', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__('Ative esta opção para desativar a atualização de valores nos detalhes do parcelamento em produtos variáveis.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="disable_update_installments" name="disable_update_installments" value="yes" <?php checked( self::get_setting( 'disable_update_installments') == 'yes' ); ?> />
           </div>
        </td>
      </tr>
  </table>
</div>