<?php

use MeuMouse\Woo_Custom_Installments\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div class="d-flex align-items-center mt-4 ms-2">
    <svg class="woo-custom-installments-logo me-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 732.07 983.38"><path d="M569.06,765.19c115.21,1.52,248.61-42.44,248.61-156.14s-141-162.2-141-162.2V381.66C851,463.52,955.62,601.47,884.37,750S595.83,927.49,595.83,927.49v104H414.44V927.39C258.3,916.78,174.93,801.57,174.93,801.57L287.1,671.2C399.28,751.55,460.92,763.77,569.06,765.19Z" transform="translate(-174.93 -48.16)" style="fill:#040f0f"/><circle cx="299.39" cy="314.55" r="31.83" style="fill:#2ba84a"/><path d="M572.1,546.9l-32.6-77.1c-181.15-27.07-216-163.93-216-163.93C475.08,240.68,593.32,321,593.32,321l72.76-34.87,63.67,25.77,60.64-81.86c-63.67-68.21-194-84.89-194-84.89v-97H412.92V149.73c-203.13,0-283.47,222.84-189.49,341.08S406.86,598.44,546.32,619.66s63.67,94,63.67,94,148.56-7.58,159.17-109.15S660,464,660,464l-23.5,13.94,47,106.12Z" transform="translate(-174.93 -48.16)" style="fill:#2ba84a"/></svg>
    
    <h1 class="woo-custom-installments-admin-section-tile"><?php echo esc_html__( 'Parcelas Customizadas para WooCommerce', 'woo-custom-installments' ) ?></h1>

    <?php if ( License::is_valid() ) : ?>
        <span class="badge bg-translucent-primary rounded-pill fs-sm ms-3">
            <svg class="icon-pro icon-primary" viewBox="0 0 24.00 24.00" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.336"></g><g> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0001 3C12.3334 3 12.6449 3.16613 12.8306 3.443L16.6106 9.07917L21.2523 3.85213C21.5515 3.51525 22.039 3.42002 22.4429 3.61953C22.8469 3.81904 23.0675 4.26404 22.9818 4.70634L20.2956 18.5706C20.0223 19.9812 18.7872 21 17.3504 21H6.64977C5.21293 21 3.97784 19.9812 3.70454 18.5706L1.01833 4.70634C0.932635 4.26404 1.15329 3.81904 1.55723 3.61953C1.96117 3.42002 2.44865 3.51525 2.74781 3.85213L7.38953 9.07917L11.1696 3.443C11.3553 3.16613 11.6667 3 12.0001 3ZM12.0001 5.79533L8.33059 11.2667C8.1582 11.5237 7.8765 11.6865 7.56772 11.7074C7.25893 11.7283 6.95785 11.6051 6.75234 11.3737L3.67615 7.90958L5.66802 18.1902C5.75913 18.6604 6.17082 19 6.64977 19H17.3504C17.8293 19 18.241 18.6604 18.3321 18.1902L20.324 7.90958L17.2478 11.3737C17.0423 11.6051 16.7412 11.7283 16.4324 11.7074C16.1236 11.6865 15.842 11.5237 15.6696 11.2667L12.0001 5.79533Z"></path> </g></svg>
            <?php echo esc_html__( 'Pro', 'woo-custom-installments' ) ?>
        </span>
    <?php endif; ?>
</div>

<div class="woo-custom-installments-admin-title-description mt-3">
    <p><?php echo esc_html__( 'Configure abaixo o parcelamento, descontos, juros, estilos e entre outras opções disponíveis. Se precisar de ajuda para configurar, acesse nossa', 'woo-custom-installments' ) ?>
        <a class="fancy-link" href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/" target="_blank"><?php echo esc_html__( 'Central de ajuda', 'woo-custom-installments' ) ?></a>
    </p>

    <?php if ( class_exists('WC_PagSeguro') || class_exists('PagSeguro_Internacional__WooCommerce') || class_exists('WC_PagSeguro_Parceled') ) : ?>
        <span class="woo-custom-installments-description"><?php echo esc_html__( 'O PagSeguro utiliza fator de multiplicação para calcular o juros.', 'woo-custom-installments' ) ?></span>
        <a class="fancy-link" href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/#convert-fee" target="__blank"><?php echo esc_html__( 'Como converter fator de multiplicação para juros.', 'woo-custom-installments' ) ?></a>
    <?php endif; ?>
</div>

<?php
/**
 * Display admin notices
 * 
 * @since 4.5.0
 */
do_action('woo_custom_installments_display_admin_notices');

settings_errors(); ?>

<div class="woo-custom-installments-wrapper">
    <div class="nav-tab-wrapper woo-custom-installments-tab-wrapper">
        <?php
        /**
         * Before nav tabs hook
         * 
         * @since 4.5.0
         * @return void
         */
        do_action('woo_custom_installments_before_nav_tabs'); ?>

        <a href="#general" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon"><path d="M7.5 14.5c-1.58 0-2.903 1.06-3.337 2.5H2v2h2.163c.434 1.44 1.757 2.5 3.337 2.5s2.903-1.06 3.337-2.5H22v-2H10.837c-.434-1.44-1.757-2.5-3.337-2.5zm0 5c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5S9 17.173 9 18s-.673 1.5-1.5 1.5zm9-11c-1.58 0-2.903 1.06-3.337 2.5H2v2h11.163c.434 1.44 1.757 2.5 3.337 2.5s2.903-1.06 3.337-2.5H22v-2h-2.163c-.434-1.44-1.757-2.5-3.337-2.5zm0 5c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5 1.5.673 1.5 1.5-.673 1.5-1.5 1.5z"></path><path d="M12.837 5C12.403 3.56 11.08 2.5 9.5 2.5S6.597 3.56 6.163 5H2v2h4.163C6.597 8.44 7.92 9.5 9.5 9.5s2.903-1.06 3.337-2.5h9.288V5h-9.288zM9.5 7.5C8.673 7.5 8 6.827 8 6s.673-1.5 1.5-1.5S11 5.173 11 6s-.673 1.5-1.5 1.5z"></path></svg>
            <?php echo esc_html__( 'Geral', 'woo-custom-installments' ) ?></a>
        </a>

        <a href="#text" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M5 8h2V6h3.252L7.68 18H5v2h8v-2h-2.252L13.32 6H17v2h2V4H5z"></path></svg>
            <?php echo esc_html__( 'Textos', 'woo-custom-installments' ) ?>
        </a>

        <a href="#discount" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M13.707 3.293A.996.996 0 0 0 13 3H4a1 1 0 0 0-1 1v9c0 .266.105.52.293.707l8 8a.997.997 0 0 0 1.414 0l9-9a.999.999 0 0 0 0-1.414l-8-8zM12 19.586l-7-7V5h7.586l7 7L12 19.586z"></path><circle cx="8.496" cy="8.495" r="1.505"></circle></svg>
            <?php echo esc_html__( 'Descontos', 'woo-custom-installments' ) ?>
        </a>

        <a href="#interests" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="m10 10.414 4 4 5.707-5.707L22 11V5h-6l2.293 2.293L14 11.586l-4-4-7.707 7.707 1.414 1.414z"></path></svg>
            <?php echo esc_html__( 'Juros', 'woo-custom-installments' ) ?>
        </a>

        <a href="#payment" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path><path d="M12 11c-2 0-2-.63-2-1s.7-1 2-1 1.39.64 1.4 1h2A3 3 0 0 0 13 7.12V6h-2v1.09C9 7.42 8 8.71 8 10c0 1.12.52 3 4 3 2 0 2 .68 2 1s-.62 1-2 1c-1.84 0-2-.86-2-1H8c0 .92.66 2.55 3 2.92V18h2v-1.08c2-.34 3-1.63 3-2.92 0-1.12-.52-3-4-3z"></path></svg>
            <?php echo esc_html__( 'Formas de pagamento', 'woo-custom-installments' ) ?>
        </a>

        <a href="#design" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon"><path d="M13.4 2.096a10.08 10.08 0 0 0-8.937 3.331A10.054 10.054 0 0 0 2.096 13.4c.53 3.894 3.458 7.207 7.285 8.246a9.982 9.982 0 0 0 2.618.354l.142-.001a3.001 3.001 0 0 0 2.516-1.426 2.989 2.989 0 0 0 .153-2.879l-.199-.416a1.919 1.919 0 0 1 .094-1.912 2.004 2.004 0 0 1 2.576-.755l.412.197c.412.198.85.299 1.301.299A3.022 3.022 0 0 0 22 12.14a9.935 9.935 0 0 0-.353-2.76c-1.04-3.826-4.353-6.754-8.247-7.284zm5.158 10.909-.412-.197c-1.828-.878-4.07-.198-5.135 1.494-.738 1.176-.813 2.576-.204 3.842l.199.416a.983.983 0 0 1-.051.961.992.992 0 0 1-.844.479h-.112a8.061 8.061 0 0 1-2.095-.283c-3.063-.831-5.403-3.479-5.826-6.586-.321-2.355.352-4.623 1.893-6.389a8.002 8.002 0 0 1 7.16-2.664c3.107.423 5.755 2.764 6.586 5.826.198.73.293 1.474.282 2.207-.012.807-.845 1.183-1.441.894z"></path><circle cx="7.5" cy="14.5" r="1.5"></circle><circle cx="7.5" cy="10.5" r="1.5"></circle><circle cx="10.5" cy="7.5" r="1.5"></circle><circle cx="14.5" cy="7.5" r="1.5"></circle></svg>
            <?php echo esc_html__( 'Estilos', 'woo-custom-installments' ) ?>
        </a>

        <a href="#about" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
            <?php echo esc_html__( 'Sobre', 'woo-custom-installments' ) ?></a>
        </a>

        <?php
        /**
         * After nav tabs hook
         * 
         * @since 4.5.0
         * @return void
         */
        do_action('woo_custom_installments_after_nav_tabs'); ?>
    </div>

    <form method="post" class="woo-custom-installments-form" name="woo-custom-installments">
        <?php
        include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/tabs/options.php';
        include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/tabs/texts.php';
        include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/tabs/discounts.php';
        include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/tabs/interests.php';
        include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/tabs/payment-forms.php';
        include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/tabs/design.php';
        include_once WOO_CUSTOM_INSTALLMENTS_INC . 'admin/tabs/about.php';

        /**
         * Add custom tab file on form
         * 
         * @since 4.5.0
         * @return void
         */
        do_action('woo_custom_installments_include_tab_file'); ?>
    </form>
</div>