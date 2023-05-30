<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit; } ?>

<div id="payment-form-settings" class="nav-content ">
    <table class="form-table" >
    <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar forma de pagamento Pix', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a forma de pagamento Pix. Shortcode disponível: [woo_custom_installments_pix_container]', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_pix_method_payment_form" name="enable_pix_method_payment_form" value="yes" <?php checked( isset( $options['enable_pix_method_payment_form'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar forma de pagamento boleto bancário', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a forma de pagamento boleto bancário. Shortcode disponível: [woo_custom_installments_ticket_container]', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_ticket_method_payment_form" name="enable_ticket_method_payment_form" value="yes" <?php checked( isset( $options['enable_ticket_method_payment_form'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar forma de pagamento cartão de crédito', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a forma de pagamento cartão de crédito. Shortcode disponível: [woo_custom_installments_credit_card_container]', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_credit_card_method_payment_form" name="enable_credit_card_method_payment_form" value="yes" <?php checked( isset( $options['enable_credit_card_method_payment_form'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar forma de pagamento cartão de débito', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a forma de pagamento cartão de débito. Shortcode disponível: [woo_custom_installments_debit_card_container]', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_debit_card_method_payment_form" name="enable_debit_card_method_payment_form" value="yes" <?php checked( isset( $options['enable_debit_card_method_payment_form'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Mastercard', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Mastercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_mastercard_flag" name="enable_mastercard_flag" value="yes" <?php checked( isset( $options['enable_mastercard_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Visa', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Visa nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_visa_flag" name="enable_visa_flag" value="yes" <?php checked( isset( $options['enable_visa_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Elo', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Elo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_elo_flag" name="enable_elo_flag" value="yes" <?php checked( isset( $options['enable_elo_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Hipercard', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Hipercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_hipercard_flag" name="enable_hipercard_flag" value="yes" <?php checked( isset( $options['enable_hipercard_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Diners Club', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Diners Club nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_diners_club_flag" name="enable_diners_club_flag" value="yes" <?php checked( isset( $options['enable_diners_club_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Discover', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Discover nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_discover_flag" name="enable_discover_flag" value="yes" <?php checked( isset( $options['enable_discover_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira American Express', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira American Express nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_american_express_flag" name="enable_american_express_flag" value="yes" <?php checked( isset( $options['enable_american_express_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira PayPal', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira PayPal nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_paypal_flag" name="enable_paypal_flag" value="yes" <?php checked( isset( $options['enable_paypal_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Stripe', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Stripe nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_stripe_flag" name="enable_stripe_flag" value="yes" <?php checked(isset( $options['enable_stripe_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Mercado Pago', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Mercado Pago nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_mercado_pago_flag" name="enable_mercado_pago_flag" value="yes" <?php checked( isset( $options['enable_mercado_pago_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira PagSeguro', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira PagSeguro nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_pagseguro_flag" name="enable_pagseguro_flag" value="yes" <?php checked( isset( $options['enable_pagseguro_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Pagar.me', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Pagar.me nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_pagarme_flag" name="enable_pagarme_flag" value="yes" <?php checked( isset( $options['enable_pagarme_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
      <tr class="<?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>">
         <th>
            <?php echo esc_html__( 'Ativar bandeira Cielo', 'woo-custom-installments' ) ?><span class="badge bg-primary rounded-pill ms-2 <?php if ( $this->responseObj->is_valid ) { echo 'd-none';} ?>"><?php echo esc_html__( 'Pro' ) ?></span>
            <span class="woo-custom-installments-description"><?php echo esc_html__('Ative para exibir a bandeira Cielo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch <?php if ( ! $this->responseObj->is_valid ) { echo 'pro-version';} ?>" id="enable_cielo_flag" name="enable_cielo_flag" value="yes" <?php checked( isset( $options['enable_cielo_flag'] ) == 'yes' && $this->responseObj->is_valid ); ?> />
            </div>
         </td>
      </tr>
    </table>
</div>
