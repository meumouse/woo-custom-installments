<?php

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="text" class="nav-content">
    <table class="form-table">
        <tr>
            <th>
                <?php esc_html_e( 'Texto antes do preço com desconto', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_before_price" value="<?php echo Init::get_setting( 'text_before_price' ) ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Texto depois do preço com desconto', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_after_price" value="<?php echo Init::get_setting( 'text_after_price' ) ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Texto informativo para economia no Pix', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será exibido no emblema de economia ao pagar com Pix. Obs.: É necessário que a variável %s esteja na frase para exibir o valor.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_economy_pix_badge" value="<?php echo Init::get_setting( 'text_economy_pix_badge' ) ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Texto informativo para desconto por quantidade', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será exibido no emblema de desconto por quantidade. Obs.: É necessário que a variável %d (Quantidade) e %s (Desconto) estejam na frase para exibir o valor.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_discount_per_quantity_message" value="<?php echo Init::get_setting( 'text_discount_per_quantity_message' ) ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Título do botão acionador de parcelas', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será exibido no botão do popup ou sanfona de parcelas.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_button_installments" value="<?php echo Init::get_setting( 'text_button_installments' ) ?>"/>
            </td>
        </tr>

        <tr>
            <td class="container-separator"></td>
        </tr>

        <tr class="admin-container-transfers">
            <th>
                <?php esc_html_e( 'Título do container de transferências', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será usado como título do container de transferências.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_pix_container" value="<?php echo Init::get_setting( 'text_pix_container' ) ?>"/>
            </td>
        </tr>

        <tr class="admin-container-ticket">
            <th>
                <?php esc_html_e( 'Título do container de boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será usado como título do container de boleto bancário.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_ticket_container" value="<?php echo Init::get_setting( 'text_ticket_container' ) ?>"/>
            </td>
        </tr>

        <tr class="admin-container-ticket">
            <th>
                <?php esc_html_e( 'Texto de instruções de boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto de instruções que será exibido no container de boleto bancário.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_instructions_ticket_container" value="<?php echo Init::get_setting( 'text_instructions_ticket_container' ) ?>"/>
            </td>
        </tr>

        <tr class="admin-container-ticket">
            <th>
                <?php esc_html_e( 'Texto antes do preço com desconto no boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_before_discount_ticket" value="<?php echo Init::get_setting( 'text_before_discount_ticket' ) ?>"/>
            </td>
        </tr>

        <tr class="admin-container-ticket">
            <th>
                <?php esc_html_e( 'Texto depois do preço com desconto no boleto bancário', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_after_discount_ticket" value="<?php echo Init::get_setting( 'text_after_discount_ticket' ) ?>"/>
            </td>
        </tr>

        <tr class="admin-container-credit-card">
            <th>
                <?php esc_html_e( 'Título do container de cartões de crédito', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será usado como título do container de cartões de crédito.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_credit_card_container" value="<?php echo Init::get_setting( 'text_credit_card_container' ) ?>"/>
            </td>
        </tr>

        <tr class="admin-container-debit-card">
            <th>
                <?php esc_html_e( 'Título do container de cartões de débito', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será usado como título do container de cartões de débito.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_debit_card_container" value="<?php echo Init::get_setting( 'text_debit_card_container' ) ?>"/>
            </td>
        </tr>

        <tr>
            <td class="container-separator payment-forms d-none"></td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Título da tabela de parcelas', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será exibido antes da tabela de parcelas no popup ou sanfona.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_table_installments" value="<?php echo Init::get_setting( 'text_table_installments' ) ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Texto informativo de parcelas com juros', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será exibido depois da parcela.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_with_fee_installments" value="<?php echo Init::get_setting( 'text_with_fee_installments' ) ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Texto informativo de parcelas sem juros', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Texto que será exibido depois da parcela.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" name="text_without_fee_installments" value="<?php echo Init::get_setting( 'text_without_fee_installments' ) ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Título do container das formas de pagamento', 'woo-custom-installments' ) ?>
                <span class="woo-custom-installments-description"><?php esc_html_e( 'Deixe em branco para não exibir.', 'woo-custom-installments' ) ?></span>
            </th>
            <td>
               <input type="text" class="form-control input-control-wd-20" id="text_container_payment_forms" name="text_container_payment_forms" value="<?php echo Init::get_setting( 'text_container_payment_forms' ); ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Texto de exibição das parcelas (Formas de pagamento)', 'woo-custom-installments' );

                if ( ! License::is_valid() ) : ?>
                    <span class="badge pro bg-primary rounded-pill ms-2">
                        <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                        <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                    </span>
                <?php endif; ?>
                
                <span class="woo-custom-installments-description mb-2"><?php esc_html_e( 'Utilize as variáveis:', 'woo-custom-installments' ) ?></span>
                
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ parcelas }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar o número de parcelas.', 'woo-custom-installments' ) ?></span>
                </div>

                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ valor }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar o valor da parcela.', 'woo-custom-installments' ) ?></span>
                </div>

                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ juros }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar se a parcela é com ou sem juros.', 'woo-custom-installments' ) ?></span>
                </div>

                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ total }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar o valor total da soma das parcelas.', 'woo-custom-installments' ) ?></span>
                </div>
            </th>

            <td>
               <input type="text" class="form-control input-control-wd-20 <?php echo ( License::is_valid() ) ? '' : 'pro-version'; ?>" name="text_display_installments_payment_forms" value="<?php echo ( Init::get_setting('text_display_installments_payment_forms') && License::is_valid() ) ? Init::get_setting('text_display_installments_payment_forms') : ''; ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <?php esc_html_e( 'Texto de exibição das parcelas (Arquivos de produtos)', 'woo-custom-installments' );
                
                if ( ! License::is_valid() ) : ?>
                    <span class="badge pro bg-primary rounded-pill ms-2">
                        <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                        <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                    </span>
                <?php endif; ?>
                
                <span class="woo-custom-installments-description mb-2"><?php esc_html_e( 'Utilize as variáveis:', 'woo-custom-installments' ) ?></span>
                
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ parcelas }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar o número de parcelas.', 'woo-custom-installments' ) ?></span>
                </div>

                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ valor }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar o valor da parcela.', 'woo-custom-installments' ) ?></span>
                </div>

                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ juros }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar se a parcela é com ou sem juros.', 'woo-custom-installments' ) ?></span>
                </div>

                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ total }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar o valor total da soma das parcelas.', 'woo-custom-installments' ) ?></span>
                </div>
            </th>

            <td>
               <input type="text" class="form-control input-control-wd-20 <?php echo ( License::is_valid() ) ? '' : 'pro-version'; ?>" name="text_display_installments_loop" value="<?php echo ( Init::get_setting('text_display_installments_loop') && License::is_valid() ) ? Init::get_setting('text_display_installments_loop') : '' ?>"/>
            </td>
        </tr>
        
        <tr>
            <th>
                <?php esc_html_e( 'Texto de exibição das parcelas (Produto individual)', 'woo-custom-installments' );

                if ( ! License::is_valid() ) : ?>
                    <span class="badge pro bg-primary rounded-pill ms-2">
                        <svg class="icon-pro" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g><path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
                        <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
                    </span>
                <?php endif; ?>

                <span class="woo-custom-installments-description mb-2"><?php esc_html_e( 'Utilize as variáveis:', 'woo-custom-installments' ) ?></span>
                
                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ parcelas }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar o número de parcelas.', 'woo-custom-installments' ) ?></span>
                </div>

                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ valor }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar o valor da parcela.', 'woo-custom-installments' ) ?></span>
                </div>

                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ juros }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar se a parcela é com ou sem juros.', 'woo-custom-installments' ) ?></span>
                </div>

                <div class="d-flex">
                    <span class="woo-custom-installments-description"><code>{{ total }}</code>
                    </span><span class="woo-custom-installments-description ms-2"><?php esc_html_e( 'Para recuperar o valor total da soma das parcelas.', 'woo-custom-installments' ) ?></span>
                </div>
            </th>

            <td>
               <input type="text" class="form-control input-control-wd-20 <?php echo ( License::is_valid() ) ? '' : 'pro-version'; ?>" name="text_display_installments_single_product" value="<?php echo ( Init::get_setting('text_display_installments_single_product') && License::is_valid() ) ? Init::get_setting('text_display_installments_single_product') : ''; ?>"/>
            </td>
        </tr>
    </table>
</div>

