=== Price Offers for WooCommerce ===
Contributors: wpcodefactory, algoritmika, anbinder
Tags: woocommerce, price, offers, offerings, negotiations, woo commerce
Requires at least: 4.4
Tested up to: 6.0
Stable tag: 2.1.2
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Allows your customers to start product price negotiations with you.

== Description ==

**Price Offers for WooCommerce** plugin allows your customers to start product price negotiations with you. The plugin allows them to suggest their price for your products. Then you can reject, accept, counter their offer.

### &#9881; How it Works ###

The plugin adds customizable "Make an offer" button to the frontend product pages of your WooCommerce store. After customer clicks the button, modal form is displayed, where he can enter his suggested price, reply email, optional message etc. You will then receive an email with the price offer and the offer will also be saved in backend price offers dashboard. Then you can perform price offer actions (reject, accept, counter, etc.).

### &#9989; Features ###

* Exclude **out of stock** products and products with **non-empty price** from price offers.
* Set custom **label**, **CSS class**, **style** and **position** for the "Make an offer" button on frontend.
* Customize the **frontend form**: select enabled and required fields, set price input options, labels, notices, styles, etc.
* Set recipient(s), subject, heading, content template for the price offers **emails**.
* Choose columns you want to see in **admin price offers history** for the product.
* Available price offer **actions**: Reject, Accept, Counter, etc.
* And more...

### &#127942; Premium Version ###

[Pro version](https://wpfactory.com/item/price-offerings-for-woocommerce/) allows you to:

* Enable price offers on **per product** and/or **per product category** basis.
* Use **advanced positions** for the "Make an offer" button on frontend, for example, shop, category (i.e. archives) pages.

### &#128472; Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/price-offerings-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Price Offers".

== Changelog ==

= 2.1.2 - 28/10/2022 =
* Fix - Possible JS error fixed.

= 2.1.1 - 19/10/2022 =
* Fix - Deploy script fixed.

= 2.1.0 - 18/10/2022 =
* Dev - Offers - Product - Product links added.
* Dev - Offers - "SKU" column added.
* Deploy script added.
* Readme.txt updated.
* WC tested up to: 7.0.

= 2.0.0 - 10/06/2022 =
* Dev - Actions (Reject, Accept, Counter, etc.) added.
* Dev - Offers are stored as custom posts now. It's now possible to view all offers in the new "Offers" menu.
* Dev - Form - "Price input", "Customer email", "Send button" are always enabled and required now.
* Dev - Admin - Product meta box - "Title" and "Status" columns added. Default value reset.
* Dev - Email - Default values updated.
* Dev - Major code refactoring.
* Plugin renamed to from "Price Offerings for WooCommerce" to "Price Offers for WooCommerce".
* Tested up to: 6.0.
* WC tested up to: 6.5.

= 1.2.1 - 07/08/2021 =
* Fix - "Undefined index: price" PHP notice fixed.
* Dev - Email - Email template - `%product_sku%` placeholder added.
* Tested up to: 5.8.
* WC tested up to: 5.5.

= 1.2.0 - 27/06/2021 =
* Dev - Form - Enabled fields - Default value updated. "Leave empty to enable all fields" feature removed.
* Dev - Admin - "Meta box > Enable" option added.
* Dev - Admin settings - `maybe_unsanitize_option()` removed.
* Dev - Admin settings - Descriptions updated.
* Dev - Code refactoring.

= 1.1.0 - 27/06/2021 =
* Fix - Form - Options fixed.
* Dev - Form - "Enabled fields" option added.
* Dev - Form - "Required fields" option added.
* Dev - Form - Form button - "HTML style" option added.
* Dev - Form - Wrapper HTML classes added to all fields.
* Dev - Button - Wrapper HTML class added.
* Dev - Admin - "Meta box title" option added.
* Dev - Localization - `load_plugin_textdomain()` function moved to the `init` hook.
* Dev - Frontend JS and CSS files minified.
* Dev - Code refactoring.
* Dev - Admin settings descriptions updated.
* Tested up to: 5.7.
* WC tested up to: 5.4.

= 1.0.1 - 05/01/2020 =
* Dev - Admin settings descriptions updated; typos fixed.

= 1.0.0 - 04/01/2020 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
