# Changelog

All notable changes to Parcelas Customizadas para WooCommerce are recorded here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Three notes on the history below. The releases did not strictly follow SemVer — bug-fix-only releases shipped as minors (1.1.0, 2.9.0, 5.1.0, 5.3.0, 5.5.0), features shipped as patches (3.6.2, 5.2.6, 5.4.6, 5.5.1) and settings were removed in minors (3.8.0, 5.4.9) — so the numbering of that period cannot be read as a compatibility promise. The version numbers are not contiguous either: several were never published, and at least one release that did ship (2.6.0) left no entry behind. And the entries before 5.4.0 were carried over from the original history, keeping the level of detail they had at the time, which is why many of them read only "Bug fixes" and "Optimizations".

Versions before 3.2.5 — except 2.9.2 — were never tagged in Git, so they carry no comparison link.

## [Unreleased]

### Added

- Updates are now delivered by the Modular Distribution Service (MDS), through the `meumouse/mds-php-sdk` package. The plugin no longer polls a static file: it asks the MDS API, which answers with a signed release and a download link tied to the store's license
- Constants `WOO_CUSTOM_INSTALLMENTS_MDS_API_URL`, `WOO_CUSTOM_INSTALLMENTS_MDS_API_KEY` and `WOO_CUSTOM_INSTALLMENTS_MDS_PUBLIC_KEY`, which can be overridden from `wp-config.php`
- Filter `Woo_Custom_Installments/Updates/Config` to change the update channel configuration at runtime
- A warning on the plugins screen when the server has no `sodium` PHP extension, which the signature check depends on
- REST route `woo-custom-installments/v1/check-updates`, which answers the manual update check

### Changed

- The license key keeps being managed by the plugin itself. It is only forwarded on the update check, so the server decides whether this store may download the package
- Automatic updates now go through the WordPress updater instead of a routine of its own, which means maintenance mode, rollback on failure and the update log all behave like any other plugin. The **Ativar atualizações automáticas** option is unchanged
- **Verificar atualizações**, on the plugins list, no longer reloads the page: it asks the server and shows the answer right next to the link, offering the update when there is one
- The update notice now reads the update data WordPress already holds

### Removed

- **BREAKING:** the daily events `Woo_Custom_Installments/Updates/Auto_Updates` and `Woo_Custom_Installments/Updates/Check_Daily_Updates`, along with the `Cron\Routines` class. Update checks now ride the WordPress update cron, and the events are cleared on the first load after the upgrade
- The update check against `https://packages.meumouse.com/v1/updates/woo-custom-installments`, replaced by the MDS API
- The `woo_custom_installments_update_available` option and the `woo_custom_installments_check_updates` / `woo_custom_installments_remote_data` transients, no longer used

### Fixed

- Division by zero on the sale badge percentage calculation

### Security

- Every update response is verified with an ed25519 signature before being used, so a tampered or forged answer cannot offer a package to the store

## [5.5.8] - 2026-02-03

### Changed

- Class instantiation now runs on the appropriate hooks

## [5.5.7] - 2026-01-26

### Changed

- Optimizations

### Fixed

- The variation's old price was not displayed when every variation shared the same price

## [5.5.6] - 2026-01-19

### Changed

- Optimizations

### Fixed

- An expired sale price was still being applied to installments and discounts

## [5.5.5] - 2025-10-28

### Changed

- Update check API changed

## [5.5.4] - 2025-09-04

### Added

- The JavaScript API is exposed on `window.Woo_Custom_Installments` and the `woo_custom_installments_ready` event is fired

### Changed

- Optimizations

### Fixed

- `Unsupported operand types: string + int`, caused by leaving the maximum interest-free installments field empty
- The old price was not updated when the quantity changed while no variation had been selected yet

## [5.5.3] - 2025-08-18

### Fixed

- Elementor compatibility

## [5.5.2] - 2025-08-18

### Changed

- Optimizations

### Fixed

- Some Elementor widgets were not loading
- The sale price was not updated when the quantity changed

## [5.5.1] - 2025-08-17

### Added

- Style controls for the Elementor widgets on the XStore theme
- Style controls for the Elementor carousel loop and grid loop
- New filter for developers to change the price component priority: `Woo_Custom_Installments/Price/Priority`

### Changed

- Dynamic class loading
- Optimizations

### Fixed

- The Pix savings information was not displayed in the product loop

## [5.5.0] - 2025-08-07

### Changed

- Optimizations

### Fixed

- The variation price was changed incorrectly on the Woodmart integration
- The price range was not displayed on variable products

## [5.4.11] - 2025-07-07

### Fixed

- The old price was not displayed on variable products
- Desktop styles were overriding mobile styles

## [5.4.10] - 2025-07-04

### Fixed

- Woodmart theme compatibility

## [5.4.9] - 2025-07-01

### Changed

- Style priority

### Removed

- The "Force style priority" option

## [5.4.8] - 2025-06-25

### Changed

- Payment methods accordion icon
- The payment method discount badge now shows the total discount amount

### Fixed

- A price of 0.00 was returned when no price was set
- `Unsupported operand type: string - float` in `inc/Core/Calculate_Values.php:345`
- Discount calculation when the cart held products with different discounts

## [5.4.7] - 2025-06-17

### Fixed

- `Call to undefined method get_available_variations()` in `Render_Elements.php` on line 326

## [5.4.6] - 2025-06-17

### Added

- Notice when a plugin update is available
- Variable subscriptions compatibility

### Changed

- Optimizations

### Fixed

- The `{{ total }}` placeholder did not update the installment total
- Duplicated installment and discount elements

## [5.4.5] - 2025-06-12

### Changed

- Optimizations

### Fixed

- `Uncaught DivisionByZeroError: Division by zero` in `inc/Core/Calculate_Values.php:45`

## [5.4.4] - 2025-06-10

### Fixed

- The update notice did not disappear after the plugin was updated
- Price updates on simple products with Tiered Pricing Table

## [5.4.3] - 2025-06-06

### Changed

- Optimizations

### Fixed

- Installments were not rendered on product types other than simple and variable
- The installment text was not displayed
- The product price was not updated when the quantity changed on simple products

## [5.4.2] - 2025-06-05

### Added

- Shoptimizer theme compatibility

### Changed

- Optimizations

### Fixed

- Woodmart Child theme detection
- The old price was not updated with Tiered Pricing Table

## [5.4.1] - 2025-06-05

### Fixed

- `Uncaught Error: Class "Elementor\Plugin" not found`

## [5.4.0] - 2025-06-04

### Added

- Automatic updates option
- Elementor widget: Discount badge, with its own style controller
- New shortcode `[woo_custom_installments_sale_badge]`: discount badge
- The product price is updated from the quantity

### Changed

- **Architecture change to MACI** (Modular Autoload Class Initialization)
- Display location of the discounted Pix price
- Display location of the Pix savings information
- Display location of the best installments
- en_US translation file updated
- es_ES translation file updated
- Optimizations

### Removed

- EpicJungle theme compatibility
- The "Enable value updates on elements for variable products" option
- The "Price update method" option under "Remove price range on variable products"

### Fixed

- Duplicated installments when the best installments were displayed both with and without interest
- Wrong product ID in the Elementor editor
- The best installments did not appear right away while editing with Elementor
- An individual discount was applied only to the first product when several products had individual discounts

## [5.3.0] - 2025-01-29

### Changed

- Optimizations

### Fixed

- Bug fixes

## [5.2.7] - 2024-12-17

### Changed

- Optimizations

### Fixed

- Bug fixes

## [5.2.6] - 2024-12-12

### Changed

- Center the price group in the product grid
- Optimizations

### Fixed

- Bug fixes

## [5.2.5] - 2024-12-10

### Added

- Icon format option
- "Enable value updates on elements for variable products" option
- Discount percentage badge option

### Changed

- Element order
- Installment display text (payment methods)
- Installment display text (product archives)
- Installment display text (single product)
- Optimizations

### Fixed

- Bug fixes

## [5.2.3] - 2024-11-26

### Fixed

- Bug fixes

## [5.2.2] - 2024-11-26

### Changed

- Optimizations

### Fixed

- Bug fixes

## [5.2.1] - 2024-09-23

### Added

- Ricky theme compatibility

### Changed

- Optimizations

### Fixed

- Bug fixes

## [5.2.0] - 2024-09-17

### Added

- Rank Math JSON-LD compatibility: display the discounted price in structured data (Schema.org)
- "Enable Elementor widgets" option
- Price stacking in widgets
- Discount message display mode
- Elementor widget: Discount per quantity message

### Changed

- "Customize product price"
- Documentation links updated
- Optimizations

### Fixed

- Bug fixes

## [5.1.2] - 2024-09-12

### Fixed

- Bug fixes

## [5.1.0] - 2024-09-10

### Changed

- Optimizations

### Fixed

- Bug fixes

## [5.0.0] - 2024-09-06

### Added

- "Custom hook" option for the position of the payment methods and installments on the single product page
- Elementor widget: Product price
- Elementor widget: Popup — Payment methods
- Elementor widget: Accordion — Payment methods
- Elementor widget: Credit card badges
- Elementor widget: Debit card badges
- Elementor widget: Installments table
- Elementor widget: Price info box

### Changed

- "Remove price range on variable products"
- Optimizations

### Fixed

- Bug fixes

## [4.5.3] - 2024-08-23

### Fixed

- Bug fixes

## [4.5.2] - 2024-08-22

### Added

- Tiered Pricing Table plugin integration

### Changed

- Optimizations

### Fixed

- Bug fixes

## [4.5.1] - 2024-08-16

### Changed

- Optimizations

### Fixed

- Bug fixes

## [4.5.0] - 2024-08-10

### Added

- New shortcode `[woo_custom_installments_get_price_on_pix]`: retrieves the product price on Pix
- New shortcode `[woo_custom_installments_get_price_on_ticket]`: retrieves the product price on bank slip
- New shortcode `[woo_custom_installments_get_economy_pix_price]`: retrieves the Pix savings amount
- Transient API for retrieving the plugin options

### Changed

- New architecture
- Element order: the product price can now be reordered as well
- Optimizations

### Removed

- The "Disable dynamic installment updates on variable products" option

### Fixed

- Bug fixes

## [4.3.1] - 2024-04-20

### Fixed

- Bug fixes

## [4.3.0] - 2024-04-17

### Added

- Alternative license activation

### Changed

- Translation template file updated
- en_US (American English) translation file updated
- es_ES (Spanish) translation file updated
- Optimizations

### Fixed

- Bug fixes

## [4.2.0] - 2024-04-06

### Added

- Clube M compatibility

### Fixed

- Bug fixes

## [4.1.0] - 2024-03-28

### Added

- New hook: `woo_custom_installments_before_installments_container`
- New hook: `woo_custom_installments_after_installments_container`
- New hook: `woo_custom_installments_popup_header`
- New hook: `woo_custom_installments_popup_bottom`
- New hook: `woo_custom_installments_accordion_header`
- New hook: `woo_custom_installments_accordion_bottom`

### Changed

- Optimizations

### Fixed

- Bug fixes

## [4.0.0] - 2024-03-28

### Added

- Option to store the discounted Pix price in post meta for XML feeds

### Changed

- Optimizations

### Fixed

- Bug fixes

## [3.8.5] - 2024-03-13

### Fixed

- Bug fixes

## [3.8.1] - 2024-03-02

### Changed

- License verification server changed

## [3.8.0] - 2024-02-29

### Added

- Informational text for the discount per quantity

### Changed

- Optimizations

### Removed

- The "Bank slip discount order" option
- The "Best installment order" option
- The "Hide discount and installment information when no variation is selected" option
- The "Informational text for variation selection" option
- The "Report interest from the first installment" option

### Fixed

- Bug fixes

## [3.6.7] - 2024-01-25

### Fixed

- Bug fixes

## [3.6.5] - 2024-01-19

### Fixed

- Bug fixes

## [3.6.2] - 2023-12-01

### Added

- "Hide" option for the discounted price display location
- Display location for the Pix savings information

### Changed

- Optimizations

### Fixed

- Bug fixes

## [3.6.0] - 2023-11-28

### Added

- Card badges for the credit card payment method
- Card badges for the debit card payment method
- Pix savings badge
- New shortcode `[woo_custom_installments_economy_pix_badge]`: products only
- New shortcode `[woo_custom_installments_pix_info]`: products only

### Changed

- The `[woo_custom_installments_discount_and_card]` shortcode was renamed to `[woo_custom_installments_group]`: products only
- Optimizations

### Removed

- The "Disable value updates at checkout" option

### Fixed

- Bug fixes

## [3.4.8] - 2023-11-22

### Fixed

- Bug fixes

## [3.4.7] - 2023-11-21

### Added

- Spanish translation

### Fixed

- Bug fixes

## [3.4.6] - 2023-11-20

### Changed

- Optimizations

### Fixed

- Bug fixes

## [3.4.5] - 2023-11-17

### Fixed

- Style loading

## [3.4.3] - 2023-11-16

### Fixed

- License verification issues

## [3.4.2] - 2023-11-16

### Changed

- Optimizations

### Fixed

- Bug fixes

## [3.2.5] - 2023-10-27

### Changed

- Optimizations

### Fixed

- Bug fixes

## 3.2.0 - 2023-10-24

### Added

- WooCommerce High-Performance Order Storage (HPOS) compatibility

### Changed

- "Remove price range on variable products"
- Optimizations

### Fixed

- Bug fixes

## 3.0.0 - 2023-09-27

### Added

- New style for the installments popup button
- Set a discount for each product individually

### Changed

- The Pix price information is displayed even when the interest is zero
- Admin panel improvements
- Optimizations

### Removed

- The "Show discount information in the order review" option
- The "Show interest information in the order review" option

### Fixed

- Bug fixes

## [2.9.2] - 2023-08-18

### Changed

- Optimizations

### Fixed

- Bug fixes

## 2.9.0 - 2023-07-24

### Changed

- Optimizations

### Fixed

- Bug fixes

## 2.8.0 - 2023-07-10

### Added

- Bank slip discount badge
- New shortcode `[woo_custom_installments_ticket_discount_badge]`
- Custom text after the product price
- Message on products eligible for the discount per quantity

### Changed

- Optimizations

### Fixed

- Bug fixes

## 2.7.2 - 2023-06-20

### Added

- Discount per minimum quantity
- "Hide" option for the installment display type

### Changed

- Optimizations

### Fixed

- Bug fixes

## 2.4.0 - 2023-05-29

### Added

- "Enable discount features" option
- "Enable interest features" option
- Include the shipping cost in the order discount
- Remove price range on variable products

### Changed

- Optimizations

### Fixed

- Font Awesome icon library
- Bug fixes

## 2.3.5 - 2023-05-18

### Added

- "Disable value updates at checkout" option
- Interest per payment method

### Changed

- Optimizations

### Fixed

- The installments table did not update the amount when a product variation was selected
- Interest calculation on installments using the default interest rate
- Bug fixes

## 2.2.0 - 2023-05-05

### Added

- Center the best installment and the discount in the product grid
- Center the best installment and the discount on the single product

### Changed

- Optimizations

### Fixed

- Discount calculation at checkout
- Bug fixes

## 2.1.0 - 2023-04-24

### Added

- Discounted price rounding
- Installments button rounding
- Best installment position
- Interest per installment

### Changed

- API connection optimization

### Fixed

- Bug fixes

## 2.0.0 - 2023-04-13

### Added

- New shortcode `[woo_custom_installments_card_info]`: products only
- New shortcode `[woo_custom_installments_discount_and_card]`: products only
- New shortcode `[woo_custom_installments_table_installments]`: products only
- New shortcode `[woo_custom_installments_pix_container]`: global
- New shortcode `[woo_custom_installments_ticket_container]`: global
- New shortcode `[woo_custom_installments_credit_card_container]`: global
- New shortcode `[woo_custom_installments_debit_card_container]`: global
- Text before the discounted price
- Initial text on variable products ("Starting at")
- Title of the installments trigger button
- Title of the transfers container
- Title of the bank slip container
- Bank slip instructions text
- Title of the credit cards container
- Title of the debit cards container
- Title of the installments table
- Informational text for installments with interest
- Informational text for interest-free installments
- Title of the payment methods container
- Discount method on the main price (percentage or fixed)
- Show the discount badge at checkout
- Show the discount information in the order review
- Discount per payment method
- Enable the Pix payment method
- Enable the bank slip payment method
- Enable the credit card payment method
- Enable the debit card payment method
- Enable card badges (Mastercard, Visa, Elo, Hipercard, Diners Club, Discover, American Express, PayPal, Stripe, Mercado Pago, PagSeguro, Pagar.me and Cielo)
- Appearance customization: color, font size, top margin, bottom margin and icon for the discounted price; color and size of the installments popup button; top and bottom margin of the installments popup/accordion; color, font size, top margin, bottom margin and icon of the installments display
- Instant approval badge for payment methods
- Position of the payment methods and installments on the single product page
- Installment display type (popup or accordion)

### Changed

- "Display on the single product page" and "Display on product archives" were replaced by "Show best installment"
- Admin panel design improvements
- Optimizations

### Fixed

- Bug fixes

## 1.3.0 - 2022-12-21

### Added

- New shortcode `[woo_custom_installments_modal]`
- Change the default installment text on the single product
- Change the default installment text on product archives
- Change the default text in the installment details popup
- Allow showing the Pix, bank slip and credit card icons

### Changed

- Admin panel design improvements
- Optimizations

### Fixed

- Bug fixes

## 1.2.0 - 2022-11-22

### Changed

- Optimizations

### Fixed

- Bug fixes

## 1.1.2 - 2022-10-31

### Fixed

- Bug fixes

## 1.1.0 - 2022-10-31

### Removed

- The "Always show the bank slip price" option

### Fixed

- Bug fixes

## 1.0.5 - 2022-09-05

### Fixed

- Bug fixes

## 1.0.0 - 2022-08-18

### Added

- Initial release

[Unreleased]: https://github.com/meumouse/woo-custom-installments/compare/v5.5.8...HEAD
[5.5.8]: https://github.com/meumouse/woo-custom-installments/compare/v5.5.7...v5.5.8
[5.5.7]: https://github.com/meumouse/woo-custom-installments/compare/v5.5.6...v5.5.7
[5.5.6]: https://github.com/meumouse/woo-custom-installments/compare/v5.5.5...v5.5.6
[5.5.5]: https://github.com/meumouse/woo-custom-installments/compare/v5.5.4...v5.5.5
[5.5.4]: https://github.com/meumouse/woo-custom-installments/compare/v5.5.3...v5.5.4
[5.5.3]: https://github.com/meumouse/woo-custom-installments/compare/v5.5.2...v5.5.3
[5.5.2]: https://github.com/meumouse/woo-custom-installments/compare/v5.5.1...v5.5.2
[5.5.1]: https://github.com/meumouse/woo-custom-installments/compare/v5.5.0...v5.5.1
[5.5.0]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.11...v5.5.0
[5.4.11]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.10...v5.4.11
[5.4.10]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.9...v5.4.10
[5.4.9]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.8...v5.4.9
[5.4.8]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.7...v5.4.8
[5.4.7]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.6...v5.4.7
[5.4.6]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.5...v5.4.6
[5.4.5]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.4...v5.4.5
[5.4.4]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.3...v5.4.4
[5.4.3]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.2...v5.4.3
[5.4.2]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.1...v5.4.2
[5.4.1]: https://github.com/meumouse/woo-custom-installments/compare/v5.4.0...v5.4.1
[5.4.0]: https://github.com/meumouse/woo-custom-installments/compare/v5.3.0...v5.4.0
[5.3.0]: https://github.com/meumouse/woo-custom-installments/compare/v5.2.7...v5.3.0
[5.2.7]: https://github.com/meumouse/woo-custom-installments/compare/v5.2.6...v5.2.7
[5.2.6]: https://github.com/meumouse/woo-custom-installments/compare/v5.2.5...v5.2.6
[5.2.5]: https://github.com/meumouse/woo-custom-installments/compare/v5.2.3...v5.2.5
[5.2.3]: https://github.com/meumouse/woo-custom-installments/compare/v5.2.2...v5.2.3
[5.2.2]: https://github.com/meumouse/woo-custom-installments/compare/v5.2.1...v5.2.2
[5.2.1]: https://github.com/meumouse/woo-custom-installments/compare/v5.2.0...v5.2.1
[5.2.0]: https://github.com/meumouse/woo-custom-installments/compare/v5.1.2...v5.2.0
[5.1.2]: https://github.com/meumouse/woo-custom-installments/compare/v5.1.0...v5.1.2
[5.1.0]: https://github.com/meumouse/woo-custom-installments/compare/v5.0.0...v5.1.0
[5.0.0]: https://github.com/meumouse/woo-custom-installments/compare/v4.5.3...v5.0.0
[4.5.3]: https://github.com/meumouse/woo-custom-installments/compare/v4.5.2...v4.5.3
[4.5.2]: https://github.com/meumouse/woo-custom-installments/compare/v4.5.1...v4.5.2
[4.5.1]: https://github.com/meumouse/woo-custom-installments/compare/v4.5.0...v4.5.1
[4.5.0]: https://github.com/meumouse/woo-custom-installments/compare/v4.3.1...v4.5.0
[4.3.1]: https://github.com/meumouse/woo-custom-installments/compare/v4.3.0...v4.3.1
[4.3.0]: https://github.com/meumouse/woo-custom-installments/compare/v4.2.0...v4.3.0
[4.2.0]: https://github.com/meumouse/woo-custom-installments/compare/v4.1.0...v4.2.0
[4.1.0]: https://github.com/meumouse/woo-custom-installments/compare/v4.0.0...v4.1.0
[4.0.0]: https://github.com/meumouse/woo-custom-installments/compare/v3.8.5...v4.0.0
[3.8.5]: https://github.com/meumouse/woo-custom-installments/compare/v3.8.1...v3.8.5
[3.8.1]: https://github.com/meumouse/woo-custom-installments/compare/v3.8.0...v3.8.1
[3.8.0]: https://github.com/meumouse/woo-custom-installments/compare/v3.6.7...v3.8.0
[3.6.7]: https://github.com/meumouse/woo-custom-installments/compare/v3.6.5...v3.6.7
[3.6.5]: https://github.com/meumouse/woo-custom-installments/compare/v3.6.2...v3.6.5
[3.6.2]: https://github.com/meumouse/woo-custom-installments/compare/v3.6.0...v3.6.2
[3.6.0]: https://github.com/meumouse/woo-custom-installments/compare/v3.4.8...v3.6.0
[3.4.8]: https://github.com/meumouse/woo-custom-installments/compare/v3.4.7...v3.4.8
[3.4.7]: https://github.com/meumouse/woo-custom-installments/compare/v3.4.6...v3.4.7
[3.4.6]: https://github.com/meumouse/woo-custom-installments/compare/v3.4.5...v3.4.6
[3.4.5]: https://github.com/meumouse/woo-custom-installments/compare/v3.4.3...v3.4.5
[3.4.3]: https://github.com/meumouse/woo-custom-installments/compare/v3.4.2...v3.4.3
[3.4.2]: https://github.com/meumouse/woo-custom-installments/compare/v3.2.5...v3.4.2
[3.2.5]: https://github.com/meumouse/woo-custom-installments/compare/2.9.2...v3.2.5
[2.9.2]: https://github.com/meumouse/woo-custom-installments/releases/tag/2.9.2
