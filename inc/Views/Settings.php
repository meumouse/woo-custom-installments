<?php

use MeuMouse\Woo_Custom_Installments\Core\License;
use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;

/**
 * 
 */

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div class="d-flex align-items-center mt-4 ms-2">
    <svg class="woo-custom-installments-logo me-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 732.07 983.38"><path d="M569.06,765.19c115.21,1.52,248.61-42.44,248.61-156.14s-141-162.2-141-162.2V381.66C851,463.52,955.62,601.47,884.37,750S595.83,927.49,595.83,927.49v104H414.44V927.39C258.3,916.78,174.93,801.57,174.93,801.57L287.1,671.2C399.28,751.55,460.92,763.77,569.06,765.19Z" transform="translate(-174.93 -48.16)" style="fill:#040f0f"/><circle cx="299.39" cy="314.55" r="31.83" style="fill:#2ba84a"/><path d="M572.1,546.9l-32.6-77.1c-181.15-27.07-216-163.93-216-163.93C475.08,240.68,593.32,321,593.32,321l72.76-34.87,63.67,25.77,60.64-81.86c-63.67-68.21-194-84.89-194-84.89v-97H412.92V149.73c-203.13,0-283.47,222.84-189.49,341.08S406.86,598.44,546.32,619.66s63.67,94,63.67,94,148.56-7.58,159.17-109.15S660,464,660,464l-23.5,13.94,47,106.12Z" transform="translate(-174.93 -48.16)" style="fill:#2ba84a"/></svg>
    
    <h1 class="woo-custom-installments-admin-section-tile"><?php esc_html_e( 'Parcelas Customizadas para WooCommerce', 'woo-custom-installments' ) ?></h1>

    <?php if ( License::is_valid() ) : ?>
        <span class="badge bg-translucent-primary rounded-pill fs-sm ms-3">
            <svg class="icon-pro icon-primary" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
            <?php esc_html_e( 'Pro', 'woo-custom-installments' ) ?>
        </span>
    <?php endif; ?>
</div>

<div class="woo-custom-installments-admin-title-description mt-3">
    <p><?php esc_html_e( 'Configure abaixo o parcelamento, descontos, juros, estilos e entre outras opções disponíveis. Se precisar de ajuda para configurar, acesse nossa', 'woo-custom-installments' ) ?>
        <a class="fancy-link" href="<?php esc_attr_e( WOO_CUSTOM_INSTALLMENTS_DOCS_LINK ) ?>" target="_blank"><?php esc_html_e( 'Central de ajuda', 'woo-custom-installments' ) ?></a>
    </p>

    <?php if ( class_exists('WC_PagSeguro') || class_exists('PagSeguro_Internacional__WooCommerce') || class_exists('WC_PagSeguro_Parceled') ) : ?>
        <span class="woo-custom-installments-description"><?php esc_html_e( 'O PagSeguro utiliza fator de multiplicação para calcular o juros.', 'woo-custom-installments' ) ?></span>
        <a class="fancy-link" href="https://ajuda.meumouse.com/docs/woo-custom-installments/fee-per-installment#conversão-de-fator-de-multiplicação-em-percentual-de-juros" target="__blank"><?php esc_html_e( 'Como converter fator de multiplicação para juros.', 'woo-custom-installments' ) ?></a>
    <?php endif; ?>
</div>

<?php
/**
 * Display admin notices
 * 
 * @since 4.5.0
 */
do_action('woo_custom_installments_display_admin_notices'); ?>

<div class="woo-custom-installments-wrapper">
    <div class="nav-tab-wrapper woo-custom-installments-tab-wrapper">
        <?php
        /**
         * Settings nav tabs hook
         * 
         * @since 5.4.0
         */
        do_action('Woo_Custom_Installments/Admin/Settings_Nav_Tabs'); ?>
    </div>

    <div class="woo-custom-installments-form-container">
        <form method="post" class="woo-custom-installments-form" name="woo-custom-installments">
            <?php $tabs = Admin_Options::register_settings_tabs();

            foreach ( $tabs as $tab ) :
                if ( ! empty( $tab['file'] ) ) {
                    include_once $tab['file'];
                }
            endforeach; ?>
        </form>

        <div class="wci-action-footer">
            <button id="woo_custom_installments_save_options" class="btn btn-primary d-flex align-items-center justify-content-center" disabled>
                <svg class="icon me-2 icon-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 21h14a2 2 0 0 0 2-2V8a1 1 0 0 0-.29-.71l-4-4A1 1 0 0 0 16 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2zm10-2H9v-5h6zM13 7h-2V5h2zM5 5h2v4h8V5h.59L19 8.41V19h-2v-5a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v5H5z"></path></svg>
                <?php esc_html_e( 'Salvar alterações', 'woo-custom-installments' ) ?></a>
            </button>
        </div>
    </div>
</div>