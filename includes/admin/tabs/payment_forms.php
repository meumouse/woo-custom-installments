<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; 
  
?>

<div id="payment-form-settings" class="nav-content ">
    <table class="form-table" >
      <tr>
         <th>
            <?php echo esc_html__( 'Ativar forma de pagamento Pix', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a forma de pagamento Pix.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php echo esc_html__( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_pix_container]</code></span>
         </th>
         <td>
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_pix_method_payment_form" name="enable_pix_method_payment_form" value="yes" <?php checked( self::get_setting( 'enable_pix_method_payment_form') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="admin-immediate-aprove-badge">
         <th>
            <?php echo esc_html__( 'Exibir emblema de aprovação imediata', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá exibir o emblema de aprovação imediata no pix, cartão de crédito e débito.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="enable_instant_approval_badge" name="enable_instant_approval_badge" value="yes" <?php checked( self::get_setting( 'enable_instant_approval_badge') == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Ativar forma de pagamento boleto bancário', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a forma de pagamento boleto bancário.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php echo esc_html__( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_ticket_container]</code></span>
         </th>
         <td>
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_ticket_method_payment_form" name="enable_ticket_method_payment_form" value="yes" <?php checked( self::get_setting( 'enable_ticket_method_payment_form') == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="admin-discount-ticket-option">
         <th>
            <?php echo esc_html__( 'Mostrar desconto para boleto bancário', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Se ativo, irá ser adicionado uma nova informação de desconto no boleto bancário abaixo do desconto principal.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php echo esc_html__( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_ticket_discount_badge]</code></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="enable_ticket_discount_main_price" name="enable_ticket_discount_main_price" value="yes" <?php checked( self::get_setting( 'enable_ticket_discount_main_price') == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Ativar forma de pagamento cartão de crédito', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a forma de pagamento cartão de crédito.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php echo esc_html__( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_credit_card_container]</code></span>
         </th>
         <td>
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_credit_card_method_payment_form" name="enable_credit_card_method_payment_form" value="yes" <?php checked( self::get_setting( 'enable_credit_card_method_payment_form') == 'yes' && $this->responseObj->is_valid ); ?> />
               <button class="manage-credit-card-trigger btn btn-outline-primary ms-3"><?php echo esc_html__( 'Configurar bandeiras', 'woo-custom-installments' ) ?></button>
               <div class="manage-credit-card-container">
                  <div class="manage-credit-card-content">
                     <div class="popup-header">
                        <h5 class="popup-title"><?php echo esc_html__( 'Gerenciar bandeiras para cartão de crédito', 'woo-custom-installments' ); ?></h5>
                        <button class="close-manage-credit-card btn-close fs-lg" aria-label="Fechar"></button>
                     </div>
                     <div class="popup-body">
                        <table class="popup-table">
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Mastercard', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Mastercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_mastercard_flag_credit" name="enable_mastercard_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_mastercard_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Visa', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Visa nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_visa_flag_credit" name="enable_visa_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_visa_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Elo', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Elo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_elo_flag_credit" name="enable_elo_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_elo_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Hipercard', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Hipercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_hipercard_flag_credit" name="enable_hipercard_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_hipercard_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Diners Club', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Diners Club nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_diners_club_flag_credit" name="enable_diners_club_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_diners_club_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Discover', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Discover nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_discover_flag_credit" name="enable_discover_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_discover_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira American Express', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira American Express nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_american_express_flag_credit" name="enable_american_express_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_american_express_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira PayPal', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira PayPal nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_paypal_flag_credit" name="enable_paypal_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_paypal_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Stripe', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Stripe nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_stripe_flag_credit" name="enable_stripe_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_stripe_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Mercado Pago', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Mercado Pago nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_mercado_pago_flag_credit" name="enable_mercado_pago_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_mercado_pago_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira PagSeguro', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira PagSeguro nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_pagseguro_flag_credit" name="enable_pagseguro_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_pagseguro_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Pagar.me', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Pagar.me nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_pagarme_flag_credit" name="enable_pagarme_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_pagarme_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Cielo', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Cielo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_cielo_flag_credit" name="enable_cielo_flag_credit" value="yes" <?php checked( self::get_setting( 'enable_cielo_flag_credit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php echo esc_html__( 'Ativar forma de pagamento cartão de débito', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a forma de pagamento cartão de débito.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php echo esc_html__( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_debit_card_container]</code></span>
         </th>
         <td>
            <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_debit_card_method_payment_form" name="enable_debit_card_method_payment_form" value="yes" <?php checked( self::get_setting( 'enable_debit_card_method_payment_form') == 'yes' && $this->responseObj->is_valid ); ?> />
               <button class="manage-debit-card-trigger btn btn-outline-primary ms-3"><?php echo esc_html__( 'Configurar bandeiras', 'woo-custom-installments' ) ?></button>
               <div class="manage-debit-card-container">
                  <div class="manage-debit-card-content">
                     <div class="popup-header">
                        <h5 class="popup-title"><?php echo esc_html__( 'Gerenciar bandeiras para cartão de débito', 'woo-custom-installments' ); ?></h5>
                        <button class="close-manage-debit-card btn-close fs-lg" aria-label="Fechar"></button>
                     </div>
                     <div class="popup-body">
                        <table class="popup-table">
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Mastercard', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Mastercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_mastercard_flag_debit" name="enable_mastercard_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_mastercard_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Visa', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Visa nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_visa_flag_debit" name="enable_visa_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_visa_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Elo', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Elo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_elo_flag_debit" name="enable_elo_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_elo_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Hipercard', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Hipercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_hipercard_flag_debit" name="enable_hipercard_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_hipercard_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Diners Club', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Diners Club nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_diners_club_flag_debit" name="enable_diners_club_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_diners_club_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Discover', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Discover nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_discover_flag_debit" name="enable_discover_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_discover_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira American Express', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira American Express nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_american_express_flag_debit" name="enable_american_express_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_american_express_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira PayPal', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira PayPal nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_paypal_flag_debit" name="enable_paypal_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_paypal_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Stripe', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Stripe nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_stripe_flag_debit" name="enable_stripe_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_stripe_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Mercado Pago', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Mercado Pago nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_mercado_pago_flag_debit" name="enable_mercado_pago_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_mercado_pago_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira PagSeguro', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira PagSeguro nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_pagseguro_flag_debit" name="enable_pagseguro_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_pagseguro_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Pagar.me', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Pagar.me nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_pagarme_flag_debit" name="enable_pagarme_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_pagarme_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php echo esc_html__( 'Ativar bandeira Cielo', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?></span>
                                 <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Cielo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version-notice';} ?>">
                                    <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_cielo_flag_debit" name="enable_cielo_flag_debit" value="yes" <?php checked( self::get_setting( 'enable_cielo_flag_debit') == 'yes' && $this->responseObj->is_valid ); ?> />
                                 </div>
                              </td>
                           </tr>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </td>
      </tr>
    </table>
</div>