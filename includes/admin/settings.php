<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit; 

// var_dump( get_option( 'woo-custom-installments-setting' ) );

?>

<div class="d-flex">
    <h1 class="woo-custom-installments-admin-section-tile"><?php echo get_admin_page_title() ?></h1>
    <span class="badge bg-translucent-primary rounded-pill fs-sm <?php if ( ! $this->responseObj->is_valid ) { echo 'd-none';} ?>" style="margin: 2rem 0.5rem 0;"><?php echo esc_html__( 'Pro' ) ?></span>
</div>

<div class="woo-custom-installments-admin-title-description">
    <p><?php echo esc_html__( 'Configure abaixo o parcelamento, descontos, juros, estilos e entre outras opções disponíveis. Se precisar de ajuda para configurar, acesse nossa', 'woo-custom-installments' ) ?>
        <a class="fancy-link" href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/" target="_blank"><?php echo esc_html__( 'Central de ajuda', 'woo-custom-installments' ) ?></a>
    </p>
    <?php
        if ( class_exists('WC_PagSeguro') || class_exists('PagSeguro_Internacional__WooCommerce') || class_exists( 'WC_PagSeguro_Parceled' ) ) {
            ?>
                <span class="woo-custom-installments-description"><?php echo __( 'O PagSeguro utiliza fator de multiplicação para calcular o juros.', 'woo-custom-installments' ) ?></span>
                <a class="fancy-link" href="https://meumouse.com/docs/plugins/parcelas-customizadas-para-woocommerce/#convert-fee" target="__blank"><?php echo __( 'Como converter fator de multiplicação para juros.', 'woo-custom-installments' ) ?></a>
            <?php
        }
    ?>
</div>

<div class="toast updated-option-success">
    <div class="toast-header bg-success text-white">
        <svg class="woo-custom-installments-toast-check-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><g><path d="M10.5 15.25C10.307 15.2353 10.1276 15.1455 9.99998 15L6.99998 12C6.93314 11.8601 6.91133 11.7029 6.93756 11.55C6.96379 11.3971 7.03676 11.2562 7.14643 11.1465C7.2561 11.0368 7.39707 10.9638 7.54993 10.9376C7.70279 10.9114 7.86003 10.9332 7.99998 11L10.47 13.47L19 5.00004C19.1399 4.9332 19.2972 4.91139 19.45 4.93762C19.6029 4.96385 19.7439 5.03682 19.8535 5.14649C19.9632 5.25616 20.0362 5.39713 20.0624 5.54999C20.0886 5.70286 20.0668 5.86009 20 6.00004L11 15C10.8724 15.1455 10.6929 15.2353 10.5 15.25Z" fill="#ffffff"/> <path d="M12 21C10.3915 20.9974 8.813 20.5638 7.42891 19.7443C6.04481 18.9247 4.90566 17.7492 4.12999 16.34C3.54037 15.29 3.17596 14.1287 3.05999 12.93C2.87697 11.1721 3.2156 9.39921 4.03363 7.83249C4.85167 6.26578 6.1129 4.9746 7.65999 4.12003C8.71001 3.53041 9.87134 3.166 11.07 3.05003C12.2641 2.92157 13.4719 3.03725 14.62 3.39003C14.7224 3.4105 14.8195 3.45215 14.9049 3.51232C14.9903 3.57248 15.0622 3.64983 15.116 3.73941C15.1698 3.82898 15.2043 3.92881 15.2173 4.03249C15.2302 4.13616 15.2214 4.2414 15.1913 4.34146C15.1612 4.44152 15.1105 4.53419 15.0425 4.61352C14.9745 4.69286 14.8907 4.75712 14.7965 4.80217C14.7022 4.84723 14.5995 4.87209 14.4951 4.87516C14.3907 4.87824 14.2867 4.85946 14.19 4.82003C13.2186 4.52795 12.1987 4.43275 11.19 4.54003C10.193 4.64212 9.22694 4.94485 8.34999 5.43003C7.50512 5.89613 6.75813 6.52088 6.14999 7.27003C5.52385 8.03319 5.05628 8.91361 4.77467 9.85974C4.49307 10.8059 4.40308 11.7987 4.50999 12.78C4.61208 13.777 4.91482 14.7431 5.39999 15.62C5.86609 16.4649 6.49084 17.2119 7.23999 17.82C8.00315 18.4462 8.88357 18.9137 9.8297 19.1953C10.7758 19.4769 11.7686 19.5669 12.75 19.46C13.747 19.3579 14.713 19.0552 15.59 18.57C16.4349 18.1039 17.1818 17.4792 17.79 16.73C18.4161 15.9669 18.8837 15.0864 19.1653 14.1403C19.4469 13.1942 19.5369 12.2014 19.43 11.22C19.4201 11.1169 19.4307 11.0129 19.461 10.9139C19.4914 10.8149 19.5409 10.7228 19.6069 10.643C19.6728 10.5631 19.7538 10.497 19.8453 10.4485C19.9368 10.3999 20.0369 10.3699 20.14 10.36C20.2431 10.3502 20.3471 10.3607 20.4461 10.3911C20.5451 10.4214 20.6372 10.471 20.717 10.5369C20.7969 10.6028 20.863 10.6839 20.9115 10.7753C20.9601 10.8668 20.9901 10.9669 21 11.07C21.1821 12.829 20.842 14.6026 20.0221 16.1695C19.2022 17.7363 17.9389 19.0269 16.39 19.88C15.3288 20.4938 14.1495 20.8755 12.93 21C12.62 21 12.3 21 12 21Z" fill="#ffffff"/></g></svg>
        <span class="me-auto"><?php _e( 'Salvo com sucesso', 'woo-custom-installments' ); ?></span>
        <button class="btn-close btn-close-white ms-2 hide-toast" type="button" aria-label="Fechar"></button>
    </div>
    <div class="toast-body"><?php _e( 'As configurações foram atualizadas!', 'woo-custom-installments' ); ?></div>
</div>

<?php

    if ( $this->activateLicense === true) { ?>
        <div class="toast update-notice-wci">
            <div class="toast-header bg-success text-white">
                <i class="fa-regular fa-circle-check me-3"></i>
                <span class="me-auto"><?php _e( 'Licença ativada com sucesso!', 'woo-custom-installments' ); ?></span>
                <button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"><?php _e( 'Todos os recursos da versão Pro agora estão ativos!', 'woo-custom-installments' ); ?></div>
        </div>
        <?php
    }

    if ( $this->deactivateLicense === true) { ?>
        <div class="toast toast-warning update-notice-wci">
            <div class="toast-header bg-warning text-white">
                <i class="fa-regular fa-circle-check me-3"></i>
                <span class="me-auto"><?php _e( 'A licença foi desativada', 'woo-custom-installments' ); ?></span>
                <button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"><?php _e( 'Todos os recursos da versão Pro agora estão desativados!', 'woo-custom-installments' ); ?></div>
        </div>
        <?php
    }

    if ( !empty( $this->showMessage ) && !empty( $this->licenseMessage ) ) { ?>
        <div class="toast toast-danger update-notice-wci">
            <div class="toast-header bg-danger text-white">
                <i class="fa-regular fa-circle-check me-3"></i>
                <span class="me-auto"><?php _e( 'Ops! Ocorreu um erro.', 'woo-custom-installments' ); ?></span>
                <button class="btn-close btn-close-white ms-2 hide-toast" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"><?php _e( $this->licenseMessage, 'woo-custom-installments' ); ?></div>
        </div>
        <?php
    }

    settings_errors(); ?>

<div id="popup-pro-notice">
    <div id="pro-notice-content">
        <div id="pro-notice-header">
            <h5 id="popup-title"><?php _e( 'Este recurso está disponível na versão Pro', 'woo-custom-installments' ); ?></h5>
            <button id="close-pro-notice" class="btn-close fs-lg" aria-label="Fechar"></button>
        </div>
        <span class="fs-lg mb-3 d-block"><?php _e( 'Compre uma licença Pro para desbloquear todos os recursos!', 'woo-custom-installments' ); ?></span>
        <a class="btn btn-primary my-4 pulsating-button" href="https://meumouse.com/plugins/parcelas-customizadas-para-woocommerce/?utm_source=wordpress&utm_medium=plugin-settings&utm_campaign=parcelas_customizadas" target="_blank">
            <i class="fa-solid fa-key me-1"></i>
            <span><?php _e(  'Comprar licença', 'woo-custom-installments' );?></span>
        </a>
    </div>
</div>

<div class="woo-custom-installments-wrapper">
    <div class="nav-tab-wrapper woo-custom-installments-tab-wrapper">
        <a href="#general-settings" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon"><path d="M7.5 14.5c-1.58 0-2.903 1.06-3.337 2.5H2v2h2.163c.434 1.44 1.757 2.5 3.337 2.5s2.903-1.06 3.337-2.5H22v-2H10.837c-.434-1.44-1.757-2.5-3.337-2.5zm0 5c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5S9 17.173 9 18s-.673 1.5-1.5 1.5zm9-11c-1.58 0-2.903 1.06-3.337 2.5H2v2h11.163c.434 1.44 1.757 2.5 3.337 2.5s2.903-1.06 3.337-2.5H22v-2h-2.163c-.434-1.44-1.757-2.5-3.337-2.5zm0 5c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5 1.5.673 1.5 1.5-.673 1.5-1.5 1.5z"></path><path d="M12.837 5C12.403 3.56 11.08 2.5 9.5 2.5S6.597 3.56 6.163 5H2v2h4.163C6.597 8.44 7.92 9.5 9.5 9.5s2.903-1.06 3.337-2.5h9.288V5h-9.288zM9.5 7.5C8.673 7.5 8 6.827 8 6s.673-1.5 1.5-1.5S11 5.173 11 6s-.673 1.5-1.5 1.5z"></path></svg>
            <?php echo esc_html__( 'Geral', 'woo-custom-installments' ) ?></a>
        </a>
        <a href="#text-settings" class="nav-tab ">
        <svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M5 8h2V6h3.252L7.68 18H5v2h8v-2h-2.252L13.32 6H17v2h2V4H5z"></path></svg>
            <?php echo esc_html__( 'Textos', 'woo-custom-installments' ) ?>
        </a>
        <a href="#discount-settings" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M13.707 3.293A.996.996 0 0 0 13 3H4a1 1 0 0 0-1 1v9c0 .266.105.52.293.707l8 8a.997.997 0 0 0 1.414 0l9-9a.999.999 0 0 0 0-1.414l-8-8zM12 19.586l-7-7V5h7.586l7 7L12 19.586z"></path><circle cx="8.496" cy="8.495" r="1.505"></circle></svg>
            <?php echo esc_html__( 'Descontos', 'woo-custom-installments' ) ?>
        </a>
        <a href="#interests-settings" class="nav-tab ">
        <svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="m10 10.414 4 4 5.707-5.707L22 11V5h-6l2.293 2.293L14 11.586l-4-4-7.707 7.707 1.414 1.414z"></path></svg><?php echo esc_html__( 'Juros', 'woo-custom-installments' ) ?>
        </a>
        <a href="#payment-form-settings" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path><path d="M12 11c-2 0-2-.63-2-1s.7-1 2-1 1.39.64 1.4 1h2A3 3 0 0 0 13 7.12V6h-2v1.09C9 7.42 8 8.71 8 10c0 1.12.52 3 4 3 2 0 2 .68 2 1s-.62 1-2 1c-1.84 0-2-.86-2-1H8c0 .92.66 2.55 3 2.92V18h2v-1.08c2-.34 3-1.63 3-2.92 0-1.12-.52-3-4-3z"></path></svg>
            <?php echo esc_html__( 'Formas de pagamento', 'woo-custom-installments' ) ?>
        </a>
        <a href="#design-settings" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon"><path d="M13.4 2.096a10.08 10.08 0 0 0-8.937 3.331A10.054 10.054 0 0 0 2.096 13.4c.53 3.894 3.458 7.207 7.285 8.246a9.982 9.982 0 0 0 2.618.354l.142-.001a3.001 3.001 0 0 0 2.516-1.426 2.989 2.989 0 0 0 .153-2.879l-.199-.416a1.919 1.919 0 0 1 .094-1.912 2.004 2.004 0 0 1 2.576-.755l.412.197c.412.198.85.299 1.301.299A3.022 3.022 0 0 0 22 12.14a9.935 9.935 0 0 0-.353-2.76c-1.04-3.826-4.353-6.754-8.247-7.284zm5.158 10.909-.412-.197c-1.828-.878-4.07-.198-5.135 1.494-.738 1.176-.813 2.576-.204 3.842l.199.416a.983.983 0 0 1-.051.961.992.992 0 0 1-.844.479h-.112a8.061 8.061 0 0 1-2.095-.283c-3.063-.831-5.403-3.479-5.826-6.586-.321-2.355.352-4.623 1.893-6.389a8.002 8.002 0 0 1 7.16-2.664c3.107.423 5.755 2.764 6.586 5.826.198.73.293 1.474.282 2.207-.012.807-.845 1.183-1.441.894z"></path><circle cx="7.5" cy="14.5" r="1.5"></circle><circle cx="7.5" cy="10.5" r="1.5"></circle><circle cx="10.5" cy="7.5" r="1.5"></circle><circle cx="14.5" cy="7.5" r="1.5"></circle></svg>
            <?php echo esc_html__( 'Estilos', 'woo-custom-installments' ) ?>
        </a>
        <a href="#about-settings" class="nav-tab ">
            <svg class="woo-custom-installments-tab-icon"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
            <?php echo esc_html__( 'Sobre', 'woo-custom-installments' ) ?></a>
        </a>
    </div>
    <form method="post" action="" class="woo-custom-installments-form" name="woo-custom-installments">
        <input type="hidden" name="woo-custom-installments" value="1"/>
        <?php
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/options.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/texts.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/discounts.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/interests.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/payment_forms.php';
        include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/design.php'; ?>

        <form method="post" action="" class="woo-custom-installments-form-license" name="woo-custom-installments-license">
            <input type="hidden" name="woo-custom-installments-license" value="1"/>
            <?php include_once WOO_CUSTOM_INSTALLMENTS_DIR . 'includes/admin/tabs/about.php'; ?>
        </form>
    </form>
</div>