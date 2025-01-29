<?php

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="payment" class="nav-content">
    <table class="form-table">
      <tr>
         <th>
            <?php esc_html_e( 'Ativar forma de pagamento Pix', 'woo-custom-installments' );

            if ( ! License::is_valid() ) : ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
               </span>
            <?php endif; ?>
            <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a forma de pagamento Pix.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php esc_html_e( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_pix_container]</code></span>
         </th>
         <td>
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_pix_method_payment_form" name="enable_pix_method_payment_form" value="yes" <?php checked( Init::get_setting( 'enable_pix_method_payment_form') == 'yes' && License::is_valid() ); ?> />
            </div>
         </td>
      </tr>
      <tr class="admin-immediate-aprove-badge">
         <th>
            <?php esc_html_e( 'Exibir emblema de aprovação imediata', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php esc_html_e('Se ativo, irá exibir o emblema de aprovação imediata no pix, cartão de crédito e débito.', 'woo-custom-installments' ) ?></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="enable_instant_approval_badge" name="enable_instant_approval_badge" value="yes" <?php checked( Init::get_setting( 'enable_instant_approval_badge') == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php esc_html_e( 'Ativar forma de pagamento boleto bancário', 'woo-custom-installments' );
            
            if ( ! License::is_valid() ) {
               ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
               </span>
               <?php
            }
            ?>
            <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a forma de pagamento boleto bancário.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php esc_html_e( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_ticket_container]</code></span>
         </th>
         <td>
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_ticket_method_payment_form" name="enable_ticket_method_payment_form" value="yes" <?php checked( Init::get_setting( 'enable_ticket_method_payment_form') == 'yes' && License::is_valid() ); ?> />
            </div>
         </td>
      </tr>
      <tr class="admin-discount-ticket-option">
         <th>
            <?php esc_html_e( 'Mostrar desconto para boleto bancário', 'woo-custom-installments' ) ?>
            <span class="woo-custom-installments-description"><?php esc_html_e('Se ativo, irá ser adicionado uma nova informação de desconto no boleto bancário abaixo do desconto principal.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php esc_html_e( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_ticket_discount_badge]</code></span>
         </th>
         <td>
            <div class="form-check form-switch">
               <input type="checkbox" class="toggle-switch" id="enable_ticket_discount_main_price" name="enable_ticket_discount_main_price" value="yes" <?php checked( Init::get_setting( 'enable_ticket_discount_main_price') == 'yes' ); ?> />
            </div>
         </td>
      </tr>
      <tr>
         <th>
            <?php esc_html_e( 'Ativar forma de pagamento cartão de crédito', 'woo-custom-installments' );
            
            if ( ! License::is_valid() ) {
               ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
               </span>
               <?php
            }
            ?>
            <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a forma de pagamento cartão de crédito.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php esc_html_e( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_credit_card_container]</code></span>
         </th>
         <td>
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_credit_card_method_payment_form" name="enable_credit_card_method_payment_form" value="yes" <?php checked( Init::get_setting( 'enable_credit_card_method_payment_form') == 'yes' && License::is_valid() ); ?> />
               
               <button class="manage-credit-card-trigger btn btn-outline-primary ms-3"><?php esc_html_e( 'Configurar bandeiras', 'woo-custom-installments' ) ?></button>
               
               <div class="manage-credit-card-container popup-container">
                  <div class="manage-credit-card-content">
                     <div class="popup-header">
                        <h5 class="popup-title"><?php esc_html_e( 'Gerenciar bandeiras para cartão de crédito', 'woo-custom-installments' ); ?></h5>
                        <button class="close-manage-credit-card btn-close fs-lg" aria-label="Fechar"></button>
                     </div>
                     <div class="popup-body">
                        <table class="popup-table">
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Mastercard', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Mastercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_mastercard_flag_credit" name="enable_mastercard_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_mastercard_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Visa', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Visa nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_visa_flag_credit" name="enable_visa_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_visa_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Elo', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Elo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_elo_flag_credit" name="enable_elo_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_elo_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Hipercard', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Hipercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_hipercard_flag_credit" name="enable_hipercard_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_hipercard_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Diners Club', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Diners Club nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_diners_club_flag_credit" name="enable_diners_club_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_diners_club_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Discover', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Discover nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_discover_flag_credit" name="enable_discover_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_discover_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira American Express', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira American Express nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_american_express_flag_credit" name="enable_american_express_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_american_express_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira PayPal', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira PayPal nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_paypal_flag_credit" name="enable_paypal_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_paypal_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Stripe', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Stripe nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_stripe_flag_credit" name="enable_stripe_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_stripe_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Mercado Pago', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Mercado Pago nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_mercado_pago_flag_credit" name="enable_mercado_pago_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_mercado_pago_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira PagSeguro', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira PagSeguro nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_pagseguro_flag_credit" name="enable_pagseguro_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_pagseguro_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Pagar.me', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Pagar.me nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_pagarme_flag_credit" name="enable_pagarme_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_pagarme_flag_credit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Cielo', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Cielo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_cielo_flag_credit" name="enable_cielo_flag_credit" value="yes" <?php checked( Init::get_setting( 'enable_cielo_flag_credit') == 'yes' && License::is_valid() ); ?> />
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
            <?php esc_html_e( 'Ativar forma de pagamento cartão de débito', 'woo-custom-installments' );
            
            if ( ! License::is_valid() ) {
               ?>
               <span class="badge pro bg-primary rounded-pill ms-2">
                  <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                  <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
               </span>
               <?php
            }
            ?>
            <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a forma de pagamento cartão de débito.', 'woo-custom-installments' ) ?></span>
            <span class="woo-custom-installments-description mt-2"><?php esc_html_e( 'Shortcode: ', 'woo-custom-installments' ) ?><code>[woo_custom_installments_debit_card_container]</code></span>
         </th>
         <td>
            <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
               <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_debit_card_method_payment_form" name="enable_debit_card_method_payment_form" value="yes" <?php checked( Init::get_setting( 'enable_debit_card_method_payment_form') == 'yes' && License::is_valid() ); ?> />
               
               <button class="manage-debit-card-trigger btn btn-outline-primary ms-3"><?php esc_html_e( 'Configurar bandeiras', 'woo-custom-installments' ) ?></button>
               
               <div class="manage-debit-card-container popup-container">
                  <div class="manage-debit-card-content">
                     <div class="popup-header">
                        <h5 class="popup-title"><?php esc_html_e( 'Gerenciar bandeiras para cartão de débito', 'woo-custom-installments' ); ?></h5>
                        <button class="close-manage-debit-card btn-close fs-lg" aria-label="Fechar"></button>
                     </div>
                     <div class="popup-body">
                        <table class="popup-table">
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Mastercard', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Mastercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_mastercard_flag_debit" name="enable_mastercard_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_mastercard_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Visa', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Visa nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_visa_flag_debit" name="enable_visa_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_visa_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Elo', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Elo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_elo_flag_debit" name="enable_elo_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_elo_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Hipercard', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Hipercard nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_hipercard_flag_debit" name="enable_hipercard_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_hipercard_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Diners Club', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Diners Club nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_diners_club_flag_debit" name="enable_diners_club_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_diners_club_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Discover', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Discover nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_discover_flag_debit" name="enable_discover_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_discover_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira American Express', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira American Express nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_american_express_flag_debit" name="enable_american_express_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_american_express_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira PayPal', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira PayPal nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_paypal_flag_debit" name="enable_paypal_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_paypal_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Stripe', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Stripe nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_stripe_flag_debit" name="enable_stripe_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_stripe_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Mercado Pago', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Mercado Pago nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_mercado_pago_flag_debit" name="enable_mercado_pago_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_mercado_pago_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira PagSeguro', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira PagSeguro nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_pagseguro_flag_debit" name="enable_pagseguro_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_pagseguro_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Pagar.me', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Pagar.me nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_pagarme_flag_debit" name="enable_pagarme_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_pagarme_flag_debit') == 'yes' && License::is_valid() ); ?> />
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th>
                                 <?php esc_html_e( 'Ativar bandeira Cielo', 'woo-custom-installments' );
                                 
                                 if ( ! License::is_valid() ) {
                                    ?>
                                    <span class="badge pro bg-primary rounded-pill ms-2">
                                       <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                                       <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                                    </span>
                                    <?php
                                 }
                                 ?>
                                 <span class="woo-custom-installments-description"><?php esc_html_e('Ative para exibir a bandeira Cielo nas formas de pagamento.', 'woo-custom-installments' ) ?></span>
                              </th>
                              <td>
                                 <div class="form-check form-switch <?php echo ( License::is_valid() ) ? '': 'pro-version-notice'; ?>">
                                    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '': 'pro-version'; ?>" id="enable_cielo_flag_debit" name="enable_cielo_flag_debit" value="yes" <?php checked( Init::get_setting( 'enable_cielo_flag_debit') == 'yes' && License::is_valid() ); ?> />
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