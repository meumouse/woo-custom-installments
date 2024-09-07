<?php

namespace MeuMouse\Woo_Custom_Installments;

use MeuMouse\Woo_Custom_Installments\Init;
use MeuMouse\Woo_Custom_Installments\Helpers;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Change colors on front-end
 *
 * @since 2.1.0
 * @version 5.0.0
 * @package MeuMouse.com
 */
class Custom_Design extends Init {

    public function __construct() {
        parent::__construct();

        add_action( 'wp_head', array( $this, 'wci_front_styles' ) );
    }
    

    /**
     * Custom CSS for frontend
     * 
     * @since 2.0.0
     * @version 5.0.0
     * @return string
     */
    public function wci_front_styles() {
        $main_price_color = self::get_setting('discount_main_price_color');
        $main_price_bg = Helpers::convert_rgba_colors( $main_price_color );
        $button_popup_color = self::get_setting('button_popup_color');
        $button_popup_size = self::get_setting('button_popup_size');

        $css = "
            .woo-custom-installments-offer {
                color: {$main_price_color};
                background-color: ". Helpers::convert_rgba_colors( $main_price_color, 0, 15 ) .";
                font-size: " . self::get_setting('font_size_discount_price') . self::get_setting('unit_font_size_discount_price') . ";
                margin-top: " . self::get_setting('margin_top_discount_price') . self::get_setting('unit_margin_top_discount_price') . ";
                margin-bottom: " . self::get_setting('margin_bottom_discount_price') . self::get_setting('unit_margin_bottom_discount_price') . ";
                border-radius: " . self::get_setting('border_radius_discount_main_price') . self::get_setting('unit_border_radius_discount_main_price') . ";
                order: " . self::get_setting('discount_pix_order') . ";
            }

            .woo-custom-installments-offer .amount {
                font-size: " . self::get_setting('font_size_discount_price') . self::get_setting('unit_font_size_discount_price') . ";
                color: {$main_price_color};
            }

            .instant-approval-badge, .badge-discount-checkout {
                color: {$main_price_color};
                background-color: ". Helpers::convert_rgba_colors( $main_price_color, 0, 15 ) .";
            }

            .woo-custom-installments-economy-pix-badge {
                background-color: " . self::get_setting('economy_pix_bg') . ";
                font-size: " . self::get_setting('font_size_economy_pix') . self::get_setting('font_size_economy_pix_unit') . ";
                margin-top: " . self::get_setting('margin_top_economy_pix') . self::get_setting('margin_top_economy_pix_unit') . ";
                margin-bottom: " . self::get_setting('margin_bottom_economy_pix') . self::get_setting('margin_bottom_economy_pix_unit') . ";
                border-radius: " . self::get_setting('border_radius_economy_pix') . self::get_setting('border_radius_economy_pix_unit') . ";
                order: " . self::get_setting('economy_pix_order') . ";
            }

            .woo-custom-installments-economy-pix-badge .amount {
                font-size: " . self::get_setting('font_size_economy_pix') . self::get_setting('font_size_economy_pix_unit') . ";
            }

            button.wci-open-popup {
                color: {$button_popup_color};
                border-color: {$button_popup_color};
                border-radius: " . self::get_setting('border_radius_popup_installments') . self::get_setting('unit_border_radius_popup_installments') . ";
            }

            button.wci-open-popup:hover {
                background-color: {$button_popup_color};
            }";

        if ( $button_popup_size == 'small' ) {
            $css .= "
                button.wci-open-popup {
                    padding: 0.475rem 1.25rem;
                    font-size: 0.75rem;
                }";
        } elseif ($button_popup_size == 'normal') {
            $css .= "
                button.wci-open-popup {
                    padding: 0.625rem 1.75rem;
                    font-size: 0.875rem;
                }";
        } elseif ($button_popup_size == 'large') {
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
                margin-top: " . self::get_setting('margin_top_popup_installments') . self::get_setting('unit_margin_top_popup_installments') . ";
                margin-bottom: " . self::get_setting('margin_bottom_popup_installments') . self::get_setting('unit_margin_bottom_popup_installments') . ";
            }

            .woo-custom-installments-card-container {
                color: " . self::get_setting('best_installments_color') . ";
                font-size: " . self::get_setting('font_size_best_installments') . self::get_setting('unit_font_size_best_installments') . ";
                margin-top: " . self::get_setting('margin_top_best_installments') . self::get_setting('unit_margin_top_best_installments') . ";
                margin-bottom: " . self::get_setting('margin_bottom_best_installments') . self::get_setting('unit_margin_bottom_best_installments') . ";
                order: " . self::get_setting('best_installments_order') . ";
            }

            .woo-custom-installments-card-container .amount {
                color: " . self::get_setting('best_installments_color') . ";
                font-size: " . self::get_setting('font_size_best_installments') . self::get_setting('unit_font_size_best_installments') . ";
            }

            .woo-custom-installments-ticket-discount {
                color: " . self::get_setting('discount_ticket_color_badge') . ";
                background-color: ". Helpers::convert_rgba_colors( self::get_setting('discount_ticket_color_badge'), 0, 15 ) .";
                font-size: " . self::get_setting('font_size_discount_ticket') . self::get_setting('unit_font_size_discount_ticket') . ";
                margin-top: " . self::get_setting('margin_top_discount_ticket') . self::get_setting('unit_margin_top_discount_ticket') . ";
                margin-bottom: " . self::get_setting('margin_bottom_discount_ticket') . self::get_setting('unit_margin_bottom_discount_ticket') . ";
                border-radius: " . self::get_setting('border_radius_discount_ticket') . self::get_setting('unit_border_radius_discount_ticket') . ";
                order: " . self::get_setting('slip_bank_order') . ";
            }

            .woo-custom-installments-ticket-discount .amount {
                color: " . self::get_setting('discount_ticket_color_badge') . ";
                font-size: " . self::get_setting('font_size_discount_ticket') . self::get_setting('unit_font_size_discount_ticket') . ";
            }
                
            .woo-custom-installments-price.original-price {
                order: " . self::get_setting('product_price_order') . ";
            }";

        if ( self::get_setting('center_group_elements_loop') === 'yes' ) {
            $css .= "
                .archive .woo-custom-installments-group,
                .loop .woo-custom-installments-group,
                li.product .woo-custom-installments-group,
                li.wc-block-grid__product .woo-custom-installments-group,
                .product-grid-item .woo-custom-installments-group,
                .e-loop-item.product .woo-custom-installments-group,
                .swiper-slide .type-product .woo-custom-installments-group,
                .shopengine-single-product-item .woo-custom-installments-group,
                .products-list.grid .item-product .woo-custom-installments-group,
                .product-item.grid .woo-custom-installments-group,
                .card-product .woo-custom-installments-group,
                .owl-item .woo-custom-installments-group,
                .jet-woo-products__inner-box .woo-custom-installments-group {
                    justify-items: center;
                    align-items: center;
                    justify-content: center;
                }";
        }

        if ( self::get_setting('remove_price_range') === 'yes' ) {
            $css .= "
                .woocommerce-variation-price {
                    display: none !important;
                }";
        }

        echo '<style type="text/css">' . $css . '</style>';
    }
}

new Custom_Design();