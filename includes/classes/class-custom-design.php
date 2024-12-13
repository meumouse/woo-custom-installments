<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\Components;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Change colors on front-end
 *
 * @since 2.1.0
 * @version 5.2.6
 * @package MeuMouse.com
 */
class Custom_Design {

    /**
     * Construct function
     * 
     * @since 2.1.0
     * @return void
     */
    public function __construct() {
        add_action( 'wp_head', array( $this, 'wci_front_styles' ) );
    }


    /**
     * Custom CSS for frontend
     * 
     * @since 2.0.0
     * @version 5.2.6
     * @return string
     */
    public function wci_front_styles() {
        $button_popup_color = Init::get_setting('button_popup_color');
        $button_popup_size = Init::get_setting('button_popup_size');
        $discount_pix_styles = Init::get_setting('elements_design')['discount_pix']['styles'];
        $economy_pix_styles = Init::get_setting('elements_design')['pix_economy']['styles'];
        $installments_styles = Init::get_setting('elements_design')['installments']['styles'];
        $ticket_styles = Init::get_setting('elements_design')['discount_slip_bank']['styles'];
        $price_styles = Init::get_setting('elements_design')['price']['styles'];

        /**
         * Force apply styles on elements
         * 
         * @since 5.2.5
         */
        $apply_styles = apply_filters( 'woo_custom_installments_force_apply_styles', '!important' );

        $css = "
            @media screen and (max-width: 992px) {
                .woo-custom-installments-offer {
                    font-size: ". $discount_pix_styles['mobile']['font_size'] . $discount_pix_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $discount_pix_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $discount_pix_styles['mobile']['font_color'] . $apply_styles .";
                    background-color: ". $discount_pix_styles['mobile']['background_color'] . $apply_styles .";
                    margin: ". Components::format_box_property( $discount_pix_styles['mobile']['margin'], $discount_pix_styles['mobile']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                    padding: ". Components::format_box_property( $discount_pix_styles['mobile']['padding'], $discount_pix_styles['mobile']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                    border-radius: ". Components::format_box_property( $discount_pix_styles['mobile']['border_radius'], $discount_pix_styles['mobile']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                }

                .woo-custom-installments-offer .amount {
                    font-size: ". $discount_pix_styles['mobile']['font_size'] . $discount_pix_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $discount_pix_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $discount_pix_styles['mobile']['font_color'] . $apply_styles .";
                }

                .woo-custom-installments-economy-pix-badge {
                    font-size: ". $economy_pix_styles['mobile']['font_size'] . $economy_pix_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $economy_pix_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $economy_pix_styles['mobile']['font_color'] . $apply_styles .";
                    background-color: ". $economy_pix_styles['mobile']['background_color'] . $apply_styles .";
                    margin: ". Components::format_box_property( $economy_pix_styles['mobile']['margin'], $economy_pix_styles['mobile']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                    padding: ". Components::format_box_property( $economy_pix_styles['mobile']['padding'], $economy_pix_styles['mobile']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                    border-radius: ". Components::format_box_property( $economy_pix_styles['mobile']['border_radius'], $economy_pix_styles['mobile']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                }

                .woo-custom-installments-economy-pix-badge .amount {
                    font-size: ". $economy_pix_styles['mobile']['font_size'] . $economy_pix_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $economy_pix_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $economy_pix_styles['mobile']['font_color'] . $apply_styles .";
                }

                .woo-custom-installments-card-container {
                    font-size: ". $installments_styles['mobile']['font_size'] . $installments_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $installments_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $installments_styles['mobile']['font_color'] . $apply_styles .";
                    background-color: ". $installments_styles['mobile']['background_color'] . $apply_styles .";
                    margin: ". Components::format_box_property( $installments_styles['mobile']['margin'], $installments_styles['mobile']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                    padding: ". Components::format_box_property( $installments_styles['mobile']['padding'], $installments_styles['mobile']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                    border-radius: ". Components::format_box_property( $installments_styles['mobile']['border_radius'], $installments_styles['mobile']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                }

                .woo-custom-installments-card-container .amount {
                    font-size: ". $installments_styles['mobile']['font_size'] . $installments_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $installments_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $installments_styles['mobile']['font_color'] . $apply_styles .";
                }

                .woo-custom-installments-ticket-discount {
                    font-size: ". $ticket_styles['mobile']['font_size'] . $ticket_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $ticket_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $ticket_styles['mobile']['font_color'] . $apply_styles .";
                    background-color: ". $ticket_styles['mobile']['background_color'] . $apply_styles .";
                    margin: ". Components::format_box_property( $ticket_styles['mobile']['margin'], $ticket_styles['mobile']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                    padding: ". Components::format_box_property( $ticket_styles['mobile']['padding'], $ticket_styles['mobile']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                    border-radius: ". Components::format_box_property( $ticket_styles['mobile']['border_radius'], $ticket_styles['mobile']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                }

                .woo-custom-installments-ticket-discount .amount {
                    font-size: ". $ticket_styles['mobile']['font_size'] . $ticket_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $ticket_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $ticket_styles['mobile']['font_color'] . $apply_styles .";
                }

                .woo-custom-installments-group-main-price {
                    font-size: ". $price_styles['mobile']['font_size'] . $price_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $price_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $price_styles['mobile']['font_color'] . $apply_styles .";
                    background-color: ". $price_styles['mobile']['background_color'] . $apply_styles .";
                    margin: ". Components::format_box_property( $price_styles['mobile']['margin'], $price_styles['mobile']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                    padding: ". Components::format_box_property( $price_styles['mobile']['padding'], $price_styles['mobile']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                    border-radius: ". Components::format_box_property( $price_styles['mobile']['border_radius'], $price_styles['mobile']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                }
                    
                .woo-custom-installments-group-main-price .amount {
                    font-size: ". $price_styles['mobile']['font_size'] . $price_styles['mobile']['font_unit'] . $apply_styles .";
                    font-weight: ". $price_styles['mobile']['font_weight'] . $apply_styles .";
                    color: ". $price_styles['mobile']['font_color'] . $apply_styles .";
                }
            }

            .woo-custom-installments-offer {
                font-size: ". $discount_pix_styles['desktop']['font_size'] . $discount_pix_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $discount_pix_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $discount_pix_styles['desktop']['font_color'] . $apply_styles .";
                background-color: ". $discount_pix_styles['desktop']['background_color'] . $apply_styles .";
                margin: ". Components::format_box_property( $discount_pix_styles['desktop']['margin'], $discount_pix_styles['desktop']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                padding: ". Components::format_box_property( $discount_pix_styles['desktop']['padding'], $discount_pix_styles['desktop']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                border-radius: ". Components::format_box_property( $discount_pix_styles['desktop']['border_radius'], $discount_pix_styles['desktop']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                order: ". Init::get_setting('elements_design')['discount_pix']['order'] . $apply_styles .";
            }

            .woo-custom-installments-offer .amount {
                font-size: ". $discount_pix_styles['desktop']['font_size'] . $discount_pix_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $discount_pix_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $discount_pix_styles['desktop']['font_color'] . $apply_styles .";
            }

            .woo-custom-installments-economy-pix-badge {
                font-size: ". $economy_pix_styles['desktop']['font_size'] . $economy_pix_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $economy_pix_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $economy_pix_styles['desktop']['font_color'] . $apply_styles .";
                background-color: ". $economy_pix_styles['desktop']['background_color'] .";
                margin: ". Components::format_box_property( $economy_pix_styles['desktop']['margin'], $economy_pix_styles['desktop']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                padding: ". Components::format_box_property( $economy_pix_styles['desktop']['padding'], $economy_pix_styles['desktop']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                border-radius: ". Components::format_box_property( $economy_pix_styles['desktop']['border_radius'], $economy_pix_styles['desktop']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                order: ". Init::get_setting('elements_design')['pix_economy']['order'] . $apply_styles .";
            }

            .woo-custom-installments-economy-pix-badge .amount {
                font-size: ". $economy_pix_styles['desktop']['font_size'] . $economy_pix_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $economy_pix_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $economy_pix_styles['desktop']['font_color'] . $apply_styles .";
            }

            button.wci-open-popup {
                color: ". $button_popup_color .";
                border-color: ". $button_popup_color .";
                border-radius: " . Init::get_setting('border_radius_popup_installments') . Init::get_setting('unit_border_radius_popup_installments') . $apply_styles .";
            }

            button.wci-open-popup:hover {
                background-color: ". $button_popup_color .";
            }";

        if ( $button_popup_size === 'small' ) {
            $css .= "
                button.wci-open-popup {
                    padding: 0.475rem 1.25rem;
                    font-size: 0.75rem;
                }";
        } elseif ( $button_popup_size === 'normal' ) {
            $css .= "
                button.wci-open-popup {
                    padding: 0.625rem 1.75rem;
                    font-size: 0.875rem;
                }";
        } elseif ($button_popup_size === 'large') {
            $css .= "
                button.wci-open-popup {
                    padding: 0.785rem 2rem;
                    font-size: 1rem;
                }";
        } else {
            $css .= "
                button.wci-open-popup {
                    display: inline-block;
                    position: relative;
                    font-weight: 500;
                    text-decoration: none;
                    border: 0;
                    padding: 0;
                    color: {$button_popup_color};
                }

                button.wci-open-popup::after {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    width: 100%;
                    height: 0.0625rem;
                    background-color: {$button_popup_color};
                }

                button.wci-open-popup:hover {
                    text-decoration: none;
                    color: {$button_popup_color};
                    background-color: transparent;
                }

                button.wci-open-popup:hover::after {
                    -webkit-animation: linkUnderline .6s ease-in-out;
                    animation: linkUnderline .6s ease-in-out;
                }
                    
                @-webkit-keyframes linkUnderline {
                    0% { width: 100%; }
                    50% { width: 0; }
                    100% { left: 0; width: 100%; }
                }

                @keyframes linkUnderline {
                    0% { width: 100%; }
                    50% { width: 0; }
                    100% { left: 0; width: 100%; }
                }";
        }

        $css .= "
            button.wci-open-popup, #wci-accordion-installments {
                margin-top: " . Init::get_setting('margin_top_popup_installments') . Init::get_setting('unit_margin_top_popup_installments') . $apply_styles .";
                margin-bottom: " . Init::get_setting('margin_bottom_popup_installments') . Init::get_setting('unit_margin_bottom_popup_installments') . $apply_styles .";
            }

            .woo-custom-installments-card-container {
                font-size: ". $installments_styles['desktop']['font_size'] . $installments_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $installments_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $installments_styles['desktop']['font_color'] . $apply_styles .";
                background-color: ". $installments_styles['desktop']['background_color'] . $apply_styles .";
                margin: ". Components::format_box_property( $installments_styles['desktop']['margin'], $installments_styles['desktop']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                padding: ". Components::format_box_property( $installments_styles['desktop']['padding'], $installments_styles['desktop']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                border-radius: ". Components::format_box_property( $installments_styles['desktop']['border_radius'], $installments_styles['desktop']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                order: ". Init::get_setting('elements_design')['installments']['order'] . $apply_styles .";
            }

            .woo-custom-installments-card-container .amount {
                font-size: ". $installments_styles['desktop']['font_size'] . $installments_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $installments_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $installments_styles['desktop']['font_color'] . $apply_styles .";
            }

            .woo-custom-installments-ticket-discount {
                font-size: ". $ticket_styles['desktop']['font_size'] . $ticket_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $ticket_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $ticket_styles['desktop']['font_color'] . $apply_styles .";
                background-color: ". $ticket_styles['desktop']['background_color'] . $apply_styles .";
                margin: ". Components::format_box_property( $ticket_styles['desktop']['margin'], $ticket_styles['desktop']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                padding: ". Components::format_box_property( $ticket_styles['desktop']['padding'], $ticket_styles['desktop']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                border-radius: ". Components::format_box_property( $ticket_styles['desktop']['border_radius'], $ticket_styles['desktop']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                order: ". Init::get_setting('elements_design')['discount_slip_bank']['order'] . $apply_styles .";
            }

            .woo-custom-installments-ticket-discount .amount {
                font-size: ". $ticket_styles['desktop']['font_size'] . $ticket_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $ticket_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $ticket_styles['desktop']['font_color'] . $apply_styles .";
            }
                
            .woo-custom-installments-group-main-price {
                font-size: ". $price_styles['desktop']['font_size'] . $price_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $price_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $price_styles['desktop']['font_color'] . $apply_styles .";
                background-color: ". $price_styles['desktop']['background_color'] . $apply_styles .";
                margin: ". Components::format_box_property( $price_styles['desktop']['margin'], $price_styles['desktop']['margin']['unit'] ?? 'px' ) . $apply_styles .";
                padding: ". Components::format_box_property( $price_styles['desktop']['padding'], $price_styles['desktop']['padding']['unit'] ?? 'px' ) . $apply_styles .";
                border-radius: ". Components::format_box_property( $price_styles['desktop']['border_radius'], $price_styles['desktop']['border_radius']['unit'] ?? 'px' ) . $apply_styles .";
                order: ". Init::get_setting('elements_design')['price']['order'] . $apply_styles .";
            }
                
            .woo-custom-installments-group-main-price .amount {
                font-size: ". $price_styles['desktop']['font_size'] . $price_styles['desktop']['font_unit'] . $apply_styles .";
                font-weight: ". $price_styles['desktop']['font_weight'] . $apply_styles .";
                color: ". $price_styles['desktop']['font_color'] . $apply_styles .";
            }
                
            .woo-custom-installments-price.original-price.has-discount .amount,
            .woo-custom-installments-group-main-price del .amount {
                font-size: calc(". $price_styles['desktop']['font_size'] . $price_styles['desktop']['font_unit'] ." - 0.3rem)" . $apply_styles .";
                opacity: 0.75;
            }
                
            .woo-custom-installments-starting-from {
                font-size: calc(". $price_styles['desktop']['font_size'] . $price_styles['desktop']['font_unit'] ." - 0.3rem)" . $apply_styles .";
                font-weight: 500;
            }";

        if ( Init::get_setting('center_group_elements_loop') === 'yes' ) {
            $css .= Init::get_setting('selectors_group_for_center_elements') . " {
                justify-items: center !important;
                align-items: center !important;
                justify-content: center !important;
            }";
        }

        if ( Init::get_setting('remove_price_range') === 'yes' ) {
            $css .= ".woocommerce-variation-price {
                display: none !important;
            }";
        }

        printf( '<style type="text/css">%s</style>', $css );
    }
}

new Custom_Design();