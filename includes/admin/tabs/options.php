<?php

use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="general" class="nav-content">
   <table class="form-table">
      <tr>
        <th>
           <?php echo esc_html__( 'Ativar Parcelas Customizadas', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__('Ative esta opção para que o plugin será inicializado.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <div class="form-check form-switch">
              <input type="checkbox" class="toggle-switch" id="enable_installments_all_products" name="enable_installments_all_products" value="yes" <?php checked( self::get_setting('enable_installments_all_products') == 'yes' ); ?> />
           </div>
        </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Remover faixa de preço em produtos variáveis', 'woo-custom-installments' );

            if ( ! License::is_valid() ) : ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
            <?php endif; ?>

            <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá remover a faixa de preço em produtos variáveis e o preço será alterado dinâmicamente ao selecionar uma variação.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '' : 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '' : 'pro-version'; ?>" id="remove_price_range" name="remove_price_range" value="yes" <?php checked( self::get_setting('remove_price_range') == 'yes' && License::is_valid() ); ?> />
            </div>
         </td>
      </tr>

      <tr>
         <th>
            <?php echo esc_html__( 'Mostrar informação de economia no Pix', 'woo-custom-installments' );

            if ( ! License::is_valid() ) : ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
            <?php endif; ?>

            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative esta opção para mostrar o valor que o usuário economizará ao pagar no Pix.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '' : 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '' : 'pro-version'; ?>" id="enable_economy_pix_badge" name="enable_economy_pix_badge" value="yes" <?php checked( self::get_setting('enable_economy_pix_badge') == 'yes' && License::is_valid() ); ?> />
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

      <tr class="container-separator"></tr>

      <tr id="fee-global-settings">
        <th>
           <?php echo esc_html__( 'Taxa de juros padrão', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Percentual para cálculo de juros das parcelas de forma progressiva.', 'woo-custom-installments' ) ?></span>
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
            <?php echo esc_html__( 'Definir taxa de juros por parcela', 'woo-custom-installments' );

            if ( ! License::is_valid() ) : ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
            <?php endif; ?>

            <span class="woo-custom-installments-description"><?php echo esc_html__( 'Se ativo, permite informar uma taxa de juros personalizada para cada parcela.', 'woo-custom-installments' ) ?></span>
            </th>
         <td class="d-flex align-items-center">
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '' : 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '' : 'pro-version'; ?>" id="set_fee_per_installment" name="set_fee_per_installment" value="yes" <?php checked( self::get_setting( 'set_fee_per_installment') == 'yes' && License::is_valid() ); ?> />
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
                        <tr class="<?php echo ( License::is_valid()  ) ? '' : 'pro-version'; ?>">
                           <td>
                              <fieldset id="custom-installments-fieldset-custom-installments">
                                 <?php
                                    $custom_fee_installments = array();
                                    $custom_fee_installments = get_option('woo_custom_installments_custom_fee_installments');
                                    $custom_fee_installments = maybe_unserialize( $custom_fee_installments );
                                    $limit_installments = self::get_setting('max_qtd_installments');
                                    $init_loop = self::get_setting('max_qtd_installments_without_fee') + 1;
                                    
                                    for ( $i = $init_loop; $i <= $limit_installments; $i++ ) :
                                       $current_custom_fee = isset( $custom_fee_installments[$i]['amount'] ) ? floatval( $custom_fee_installments[$i]['amount'] ) : 0; ?>

                                       <div class="input-group mb-2" data-installment="<?php echo $i; ?>">
                                          <input class="custom-installment-first small-input form-control" type="text" disabled value="<?php echo $i; ?>"/>
                                          <input class="custom-installment-secondary small-input form-control allow-number-and-dots" type="text" placeholder="1.0" name="custom_fee_installments[<?php echo $i; ?>][amount]" id="custom_fee_installments[<?php echo $i; ?>]" value="<?php echo esc_attr( $current_custom_fee ); ?>" />
                                       </div>
                                    <?php endfor; ?>
                              </fieldset>
                           </td>
                        </tr>
                     </table>
                  </div>
               </div>
            </div>
         </td>
      </tr>

      <tr class="container-separator"></tr>

      <tr>
        <th>
           <?php echo esc_html__( 'Local de exibição do preço com desconto no Pix', 'woo-custom-installments' ) ?>
           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione onde o preço com desconto no Pix será exibido.', 'woo-custom-installments' ) ?></span>
        </th>
        <td>
           <select name="display_discount_price_hook" class="form-select">
              <option value="display_loop_and_single_product" <?php echo ( self::get_setting('display_discount_price_hook') === 'display_loop_and_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Mostrar em todo o site (Padrão)', 'woo-custom-installments' ) ?></option>
              <option value="only_single_product" <?php echo ( self::get_setting('display_discount_price_hook') === 'only_single_product' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas página do produto individual', 'woo-custom-installments' ) ?></option>
              <option value="only_loop_products" <?php echo ( self::get_setting('display_discount_price_hook') === 'only_loop_products' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Apenas arquivos de produtos', 'woo-custom-installments' ) ?></option>
              <option value="hide" <?php echo ( self::get_setting('display_discount_price_hook') === 'hide' ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Ocultar', 'woo-custom-installments' ) ?></option>
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

      <tr class="container-separator"></tr>

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
           <?php echo esc_html__( 'Posição das formas de pagamento e parcelas na página de produto individual', 'woo-custom-installments' );

            if ( ! License::is_valid() ) : ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
               </span>
            <?php endif; ?>

           <span class="woo-custom-installments-description"><?php echo esc_html__( 'Selecione onde o gancho que será exibido as formas de pagamento na página de produto individual.', 'woo-custom-installments' ) ?></span>
           <span id="display-shortcode-info" class="woo-custom-installments-description mt-2 <?php echo ( self::get_setting( 'hook_payment_form_single_product' ) != 'shortcode' ) ? "d-none" : ""; ?>"><?php echo esc_html__( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_modal]</code></span>
       </th>
        <td>
           <select id="hook_payment_form_single_product" name="hook_payment_form_single_product" class="form-select <?php echo ( License::is_valid() ) ? '' : 'pro-version-notice'; ?>">
              <option value="before_cart" <?php echo ( self::get_setting( 'hook_payment_form_single_product' ) == 'before_cart' && License::is_valid() ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Antes do carrinho (Padrão)', 'woo-custom-installments' ) ?></option>
              <option value="after_cart" <?php echo ( self::get_setting( 'hook_payment_form_single_product' ) == 'after_cart' && License::is_valid() ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Depois do carrinho', 'woo-custom-installments' ) ?></option>
              <option value="shortcode" <?php echo ( self::get_setting( 'hook_payment_form_single_product' ) == 'shortcode' && License::is_valid() ) ? "selected=selected" : ""; ?>><?php echo esc_html__( 'Shortcode', 'woo-custom-installments' ) ?></option>
           </select>
        </td>
      </tr>
  </table>
</div>