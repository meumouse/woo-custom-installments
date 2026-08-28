# AGENTS.md

Guidelines for AI agents working on **Parcelas Customizadas para WooCommerce** (`woo-custom-installments`), a proprietary WooCommerce extension by MeuMouse.com.

> All code, comments, docblocks, commit messages and documentation **must be written in English (en_US)**. Only user-facing strings passed through translation functions are written in Brazilian Portuguese (pt_BR), since that is the plugin's source locale.

---

## 1. Project overview

The plugin displays installment plans, discounts, interest (fees) and accepted payment methods on WooCommerce stores. It hooks into the product price rendering, cart and checkout, and ships Elementor widgets, shortcodes and theme integrations.

| Item | Value |
| --- | --- |
| Text domain | `woo-custom-installments` |
| PHP namespace root | `MeuMouse\Woo_Custom_Installments\` → `inc/` (PSR-4) |
| Minimum PHP | 7.4 |
| Minimum WordPress | 6.0 |
| Minimum WooCommerce | 6.0.0 |
| License | Proprietary (see `license.md`) |
| Constant prefix | `WOO_CUSTOM_INSTALLMENTS_` |
| Hook namespace | `Woo_Custom_Installments/...` |
| Option key | `woo-custom-installments-setting` (single serialized array) |

### Directory layout

```
woo-custom-installments.php   Plugin bootstrap: headers, autoloader, `new Init()`
CHANGELOG.md                  Canonical changelog (English, Keep a Changelog)
inc/                          PSR-4 source root
  Admin/                      Settings page, default options, product metabox
  API/                        License activation and the MDS SDK update channel
  Compatibility/              Deprecated hook/filter bridges
  Core/                       Init, Ajax, Assets, calculations, helpers, logger
  Integrations/               Themes, page builders and third-party plugins
    Elementor/Widgets/        Elementor widget classes
  Views/                      Settings markup, components, shortcodes, styles
assets/
  admin/{css,js}              Settings and license scripts (+ .min counterparts)
  frontend/{css,js,img}       Storefront scripts, styles and payment badges
  vendor/                     Bundled third-party libs (bootstrap, minicolors, ...)
templates/                    WooCommerce template overrides
languages/                    .pot / .po / .mo / .l10n.php
dist/                         Release ZIPs, versioned archives, update-checker.json
vendor/                       Composer autoloader and the MDS PHP SDK (committed — the plugin ships them)
```

---

## 2. Bootstrapping and class instantiation

`Core\Init` is the only class instantiated directly from `woo-custom-installments.php`. Everything else is discovered and instantiated automatically. **Do not add manual `new Class()` calls in the bootstrap.**

Flow:

1. `Init::__construct()` fires `Woo_Custom_Installments/Before_Init`, checks the PHP version, defines constants and registers `plugins_loaded` (priority 20).
2. `maybe_boot()` verifies WooCommerce is active and compatible, then registers `instance_core_classes` (`init`, priority 1) and `instance_classes` (`init`, priority 5).
3. `instance_classes()` reads `vendor/composer/autoload_classmap.php`, keeps only classes under the plugin namespace, and skips anything containing `Abstract`, `Interface` or `Trait`, plus the core classes already instantiated.
4. Each remaining class is bound to a hook resolved by `get_class_hook()`:

   | Namespace | Instantiation hook |
   | --- | --- |
   | `Integrations\Elementor*` | `elementor/init` |
   | `Admin\*` | `init` |
   | `Views\*` | `wp` |
   | everything else | `init` |

5. `safe_instance_class()` instantiates through Reflection, caches the instance, skips non-instantiable classes and constructors that require arguments, and calls a public non-static `init()` method if it exists.

**Consequences for new classes:**

- A new class is auto-loaded just by living under `inc/` with the correct namespace — no registration needed.
- The constructor **must** work with zero required arguments.
- Never assume a class runs before `init`. Anything needed earlier belongs in `Init` itself or in a hook registered from the constructor. `API\Updater` is the one class with a second, earlier entry point: `Init::__construct()` calls its static `bootstrap()` while the plugin file loads, because the MDS SDK boots on `plugins_loaded` at priority `-100`. Its constructor still runs normally on `init`.
- Third parties may inject classes via the `Woo_Custom_Installments/Init/Instance_Classes` filter.
- The classmap is what drives all of this, so `composer.json` sets `config.optimize-autoloader` — a plain `composer install` must still produce `vendor/composer/autoload_classmap.php`.
- After adding, renaming or moving a class, regenerate the classmap: `composer dump-autoload -o`.

---

## 3. PHP coding style

Follow the surrounding file. The codebase is a WordPress-flavoured style with these house rules:

### File header

```php
<?php

namespace MeuMouse\Woo_Custom_Installments\Core;

use MeuMouse\Woo_Custom_Installments\Admin\Admin_Options;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Short description of the class
 *
 * @since 4.5.0
 * @version 5.5.8
 * @package MeuMouse\Woo_Custom_Installments\Core
 * @author MeuMouse.com
 */
class Helpers {
```

- Every PHP file starts with `defined('ABSPATH') || exit;` right after the `use` block.
- Class names are `Studly_Snake_Case` (`Calculate_Installments`, `Admin_Options`, `Legacy_Hooks`); the file name matches the class name exactly.
- One class per file; directory structure mirrors the namespace.

### Docblocks

Every class, property and method carries a docblock:

```php
    /**
     * Get option interest of calc installments
     *
     * @since 2.3.5
     * @version 5.4.0
     * @param object|bool $product | Product object or false
     * @param int $installments | Number of installments
     * @return float
     */
```

- `@since` = version where the member was introduced; never change it.
- `@version` = version of the last meaningful change; add or bump it when you modify the member. Omit it when the member has never changed since introduction.
- Parameter descriptions use the ` | ` separator convention: `@param string $key | Array key`.
- Filters and actions get their own docblock immediately above the call, documenting `@since` and each parameter.

### Formatting

- Indentation is mixed in legacy files: **match the file you are editing**. New files use tabs, following the most recently refactored classes (`Core/Init.php`, `Compatibility/*`, `Views/Components.php`).
- Spaces inside parentheses for multi-argument calls: `add_action( 'init', array( $this, 'callback' ), 10, 2 );`
- Single-argument calls to WordPress getters are written tight: `get_option('woo-custom-installments-setting', array())`, `Admin_Options::get_setting('enable_auto_updates')`, `class_exists('WC_PagSeguro')`.
- Use `array()`, not `[]`.
- Two blank lines between methods.
- Inline comments are lowercase single-line `//` comments explaining intent, e.g. `// check security nonce`.
- Prefer early returns over deep nesting.

### WordPress and WooCommerce practices

- Escape on output: `esc_html__()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`. Use `printf()` with numbered placeholders for markup.
- Every AJAX callback starts with `check_ajax_referer()` and ends with `wp_send_json_*()`. Nonces are passed to JS through `wp_localize_script()` under a `nonces` key.
- Guard capabilities with `current_user_can()` for any admin-side mutation.
- Use WooCommerce APIs for prices and formatting: `wc_get_price_to_display()`, `wc_price()`, `wc_get_product()`, `get_woocommerce_currency_symbol()`.
- Always check `$product instanceof \WC_Product` before calling product methods.
- Log through `Core\Logger::set_logger_source()` + `Core\Logger::register_log()` (backed by `wc_get_logger()`), not `error_log()`. `error_log()` is reserved for bootstrap-level failures in `Init`.
- HPOS compatibility is declared in `Init::setup_hpos_compatibility()` — keep it intact.

---

## 4. Hooks

### Naming

Modern hooks use the slash-namespaced format, mirroring the class path:

```
Woo_Custom_Installments/<Area>/<Subject>[/<Detail>]
```

Examples: `Woo_Custom_Installments/Price/Discounted_Price`, `Woo_Custom_Installments/Admin/Register_Settings_Tabs`, `Woo_Custom_Installments/Assets/Frontend_Params`, `Woo_Custom_Installments/Updates/Auto_Updates`.

**New hooks must use this format.** The legacy `woo_custom_installments_*` snake_case hooks are deprecated and kept only for backward compatibility.

### Deprecation bridge

`Compatibility/Legacy_Hooks` and `Compatibility/Legacy_Filters` map old hook names to their new equivalents and emit deprecation warnings. When you rename a public hook, add an entry to the corresponding map with the old name, the new name and the version of the change — never remove a public hook silently.

### Documenting a hook

```php
        /**
         * Filter to set product id on frontend params
         *
         * @since 5.4.0
         * @param int $product_id | Product ID
         * @return int
         */
        $product_id = apply_filters( 'Woo_Custom_Installments/Assets/Set_Product_Id', $product_id );
```

---

## 5. Settings

- All settings live in a single option: `woo-custom-installments-setting` (associative array).
- Read with `Admin_Options::get_setting( 'key' )` — returns `false` when the key is absent.
- Defaults are declared in `Admin\Default_Options::set_default_data_options()`, filterable via `Woo_Custom_Installments/Admin/Set_Default_Options`. `Admin_Options::set_default_options()` merges new keys into existing installations on `admin_init`.
- **Adding a setting requires three edits:** the default in `Default_Options`, the field markup in the matching `inc/Views/Settings/Tabs/*.php`, and — if it is a toggle available without a license — an entry in the `$switchs_without_license` array in `Core\Ajax::save_options_callback()`.
- Settings tabs are registered in `Admin_Options::register_settings_tabs()` (filter: `Woo_Custom_Installments/Admin/Register_Settings_Tabs`); each entry declares `id`, `label`, inline `icon` SVG and the `file` to include.
- Settings are saved over AJAX (`wci_save_options`), not through the WordPress Settings API. The form is serialized client-side and parsed with `parse_str()`.

### Pro gating

`API\License::is_valid()` gates Pro features. In views, the pattern is:

```php
<div class="form-check form-switch <?php echo ( License::is_valid() ) ? '' : 'pro-version-notice'; ?>">
    <input type="checkbox" class="toggle-switch <?php echo ( License::is_valid() ) ? '' : 'pro-version'; ?>" ... />
```

Server-side, gated behaviour must also be checked — never rely on the CSS class alone.

---

## 6. Views, templates and components

- `Views\Components` holds reusable static builders (unit selectors, font weights, design controls). `Views\Shortcodes` extends it and registers all `woo_custom_installments_*` shortcodes from a single map in the constructor.
- `Views\Styles` prints dynamic CSS generated from the Styles tab.
- `templates/` mirrors WooCommerce's template hierarchy and is injected via the `woocommerce_locate_template` filter in `Init::change_price_template()`. Templates must stay theme-overridable — keep the standard WooCommerce template header with `@since` / `@version`.
- Markup uses Bootstrap 5 grid/utility classes, loaded only when `Flexify_Dashboard` is absent. Icons are inline SVG, or Font Awesome when `icon_format_elements` is set to `class`.

---

## 7. Assets

- `Core\Assets` enqueues everything. Admin assets load only when `Helpers::check_admin_page('woo-custom-installments')` matches.
- Every script/style has a hand-maintained `.min` counterpart in the same directory. `Assets::$min` resolves to `''` when `WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE` is `true`, otherwise `.min`. **When you edit a source asset, update its `.min` file in the same change** — there is no build step (no `package.json`, no bundler).
- Data reaches JS through `wp_localize_script()`:
  - `wci_params` — settings page
  - `wci_license_params` — license page
  - `wci_front_params` — storefront (built by `Assets::frontend_params()`, filterable via `Woo_Custom_Installments/Assets/Frontend_Params`)
  - `wci_updates_params` — plugins list, for the manual update check (REST URL and `wp_rest` nonce)
  - Translatable strings go under the `i18n` key; nonces under `nonces`.

### JavaScript style

- IIFE wrapper with `"use strict";` and jQuery passed in: `( function($) { ... })(jQuery);`
- A single namespaced object per file (`var Settings = { ... }`) with method properties.
- Docblocks with `@since` / `@version` mirroring the PHP convention.
- Read localized params defensively: `const params = window.wci_params || {};`
- Debug logging is gated behind `params.debug_mode` / `params.dev_mode`.
- The frontend object is exported as `window.Woo_Custom_Installments` and the `woo_custom_installments_ready` jQuery event is triggered when ready — this is a public API, so do not rename or remove it.
- Frontend selectors and CSS classes are prefixed `wci-` or `woo-custom-installments-`.

---

## 8. Internationalization

- Text domain is always `woo-custom-installments`, loaded on `init`.
- **Source strings are written in Brazilian Portuguese** (the plugin's original locale); translations for `en_US` and `es_ES` live in `languages/`. Keep new strings in pt_BR to stay consistent with the existing catalog — this is the single exception to the English-only rule, which applies to code, comments and documentation.
- Always wrap user-facing strings: `esc_html__()`, `esc_html_e()`, `__()`, `_e()`. Never concatenate translated fragments — use `sprintf()` with placeholders.
- Regenerate `languages/woo-custom-installments.pot` when strings change.

---

## 9. Integrations

`inc/Integrations/` contains one class per third-party target (Astra, Woodmart, Xstore, Shoptimizer, Machic, Ricky, Elementor, Rank Math, Tiered Pricing Table, Dynamic Pricing & Discounts).

Rules:

- Guard every integration with a runtime check in the constructor (`class_exists()`, `defined()`, `function_exists()`, or the active theme name) and bail early when the target is absent.
- Elementor widgets live in `inc/Integrations/Elementor/Widgets/`, one class per widget, and are instantiated on `elementor/init` by `Init`. Register controls through `Elementor\Inject_Controllers` when extending native widgets.
- Never edit third-party files under `assets/vendor/` or `vendor/`.

---

## 10. License and updates

Licensing and updates are two separate channels that only touch at one point: the license key.

**License** — `API\License` talks to `https://api.meumouse.com/wp-json/license/`. The license status is cached in the `woo_custom_installments_license_status` option (`valid` / other) and the key lives in `woo_custom_installments_license_key`. This is the only license implementation; the SDK below ships one too, and it is deliberately switched off.

**Updates** — owned by the [MDS PHP SDK](https://github.com/meumouse/mds-php-sdk) (`meumouse/mds-php-sdk`, installed with Composer), which checks `POST /v2/update-check` on the MDS API, verifies the ed25519 signature of the response and injects the release into the core `update_plugins` transient.

- `API\Updater` owns the whole channel and has **two entry points**. `Updater::bootstrap()` is static and runs from `Init::__construct()` while the plugin file loads, because the SDK elects its newest embedded copy on `plugins_loaded` at priority `-100`; the constructor runs later, on `init`, through the automatic class instantiation, and only registers the admin surface. `bootstrap()` is idempotent, so the two never collide.
- The SDK runs in `updates_only` mode: no license module, no heartbeat cron, no admin menu, no notices of its own. `Updater::set_license_key()` forwards `woo_custom_installments_license_key` on the update check through the SDK's `request_body` filter, and the server decides whether to hand over the package.
- Configuration comes from `WOO_CUSTOM_INSTALLMENTS_MDS_API_URL`, `WOO_CUSTOM_INSTALLMENTS_MDS_API_KEY` and `WOO_CUSTOM_INSTALLMENTS_MDS_PUBLIC_KEY` (defined in `Init::setup_constants()`, so `wp-config.php` can override them), filtered by `Woo_Custom_Installments/Updates/Config`. **With an empty API key or public key the channel stays completely inert** — no request is made and no hook is registered.
- The manual check behind the "Verificar atualizações" link on the plugins list is answered by the REST route `woo-custom-installments/v1/check-updates` (`POST`, capability `update_plugins`, `X-WP-Nonce`), not by `admin-ajax.php`. It purges the SDK cache, runs `wp_update_plugins()` and answers with `has_update`, `new_version` and `update_url`; `assets/admin/js/check-updates.js` renders the outcome inline, with no page reload.
- The SDK requires the `sodium` extension. Without it the channel registers nothing and shows a notice instead — updates are off rather than unverified.
- Do not change endpoints, cache keys or transient names without an explicit instruction — they are coupled to the remote server.
- Never edit `vendor/meumouse/mds-php-sdk/`. Upgrade it with `composer update meumouse/mds-php-sdk`, then `composer dump-autoload -o`, and commit `vendor/`.

---

## 11. Release process

A version bump touches exactly these files — keep them all in sync:

1. `woo-custom-installments.php` — the `Version:` header **and** the `$plugin_version` variable.
2. `inc/Core/Init.php` — the class `@version` docblock.
3. `CHANGELOG.md` — promote the `[Unreleased]` section to the new version and add the comparison link.
4. `README.md` — the same entry mirrored, in pt_BR, into the "Registro de alterações" section.
5. `dist/update-checker.json` — `version`, `last_updated`, and the HTML `sections.changelog` (pt_BR, shown in the WordPress update screen).
6. `dist/woo-custom-installments.zip` and `dist/versions/<version>/woo-custom-installments.zip`.

Also bump the `@version` docblock of every class and method you changed.

### CHANGELOG.md

`CHANGELOG.md` is the canonical changelog, written in **English**, following [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). It replaced the former pt_BR `changelogs.md`.

- Record every change under `## [Unreleased]` as you make it, using only the standard groups, in this order: `Added`, `Changed`, `Deprecated`, `Removed`, `Fixed`, `Security`.
- On release, rename `[Unreleased]` to `## [<version>] - YYYY-MM-DD`, open a fresh empty `[Unreleased]`, and add the compare link at the bottom of the file.
- Write entries for the store owner, not for the compiler: say what changed and what it means, not which function was touched. Prefix breaking changes with `**BREAKING:**`.
- Reference hooks, shortcodes, filters and file paths in backticks.
- Link targets follow the real Git tags: tags are `v`-prefixed from `v3.2.5` onward, `2.9.2` is unprefixed, and anything older was never tagged — those versions keep a plain (unbracketed) heading with no link.

```markdown
## [5.5.9] - 2026-08-27

### Fixed

- Division by zero on the sale badge percentage calculation
```

The pt_BR entries in `README.md` and `dist/update-checker.json` stay in Portuguese — they are what the Brazilian store owner reads in the WordPress admin — and should be a condensed translation of the same release section.

Build the release ZIP with Composer (it excludes `dist`, `composer.json` and `composer.lock`):

```bash
composer run-script build-windows
```

```bash
composer run-script build-linux
```

---

## 12. Git workflow

- `main` is the release branch. Work happens on `update-<version>` branches (e.g. `update-5.5.9`, `update-6.0.0`) and lands on `main` through a pull request.
- Commit messages are written in English, imperative or past tense, one logical change each, matching the existing history:
  - `Fixed division by zero on sale badge percentage calculation`
  - `Optimized initialize plugin`
  - `Added feature: Sync license`
  - `Update release 5.5.8`
- Do not commit or push unless explicitly asked.
- `.env`, `.history` and `vendor/` are gitignored; `composer.json` and `composer.lock` **are** committed. Since `vendor/` does not travel with the repository, run `composer install --no-dev -o` before building the release ZIP: the package ships the autoloader and the MDS SDK inside it, and the build script leaves only the Composer files out.

---

## 13. Testing

There is no automated test suite. Verify changes manually in a WooCommerce environment (this repo lives inside a Local by Flywheel site):

- Simple, variable and out-of-stock products on single product and shop loop pages.
- Cart and checkout totals with discounts and interest applied.
- Settings page: save over AJAX, reload, confirm persistence.
- Both licensed and unlicensed states for anything Pro-gated.
- Elementor editor and frontend when touching widgets.
- Enable `WOO_CUSTOM_INSTALLMENTS_DEBUG_MODE` (in `Init::setup_constants()`) to load unminified assets and surface JS logs while debugging.

Never introduce PHP notices or warnings — many stores run with `WP_DEBUG_DISPLAY` on.

---

## 14. Boundaries

- Do not edit anything under `vendor/`, `assets/vendor/` or `dist/versions/`.
- Do not remove or rename public hooks, shortcodes, CSS classes or the JS API without adding a compatibility bridge.
- Do not weaken the license validation or the illegal-copy protection in `Init::register_prevent_illegal_copies()`.
- Do not migrate the settings storage away from the single `woo-custom-installments-setting` option.
- Do not add build tooling (npm, webpack) or reformat whole files; keep diffs scoped to the change requested.
