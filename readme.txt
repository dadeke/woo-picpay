=== QR Code PicPay for WooCommerce ===
Contributors: dadeke
Donate link: https://app.picpay.com/user/deividsondamasio
Tags: woocommerce, picpay, payment
Requires at least: 4.9
Tested up to: 5.3
Stable tag: 1.1.5
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add PicPay E-Commerce as payment method in WooCommerce.

== Description ==

### Add PicPay E-Commerce gateway to WooCommerce ###

This plugin adds PicPay E-Commerce gateway to WooCommerce.

Please notice that WooCommerce must be installed and active.

[PicPay E-Commerce](https://ecommerce.picpay.com/) is a brazilian payment method developed by PicPay Serviços S.A.

This plugin was developed with [the official PicPay E-Commerce documentation](https://ecommerce.picpay.com/doc/) and uses the latest version of the payment API.

The PicPay WooCommerce plugin was developed without any incentive from PicPay Serviços S.A.
None of the plugin developers have links to the company.

This software is free and is not associated with PicPay. PicPay is a registered trademark of PicPay Serviços S.A. This plugin is not affiliated with PicPay Serviços S.A and therefore is not an official PicPay product.

= How to test =

The PicPay API does not have an production environment. All sales testing should be done using the production environment.
The values can be refund by the [Painel do Lojista - PicPay](https://lojista.picpay.com/dashboard/login) or by WooCommerce changing the order Status to "Canceled".
After the order Status is changed to "Canceled" in WooCommerce, automatically (after a few seconds) the plugin should change the order Status to "Refunded".

= Contribute =

You can contribute to the source code in our [GitHub](https://github.com/dadeke/woo-picpay) page.

= Compatibility =

Compatible with latest versions of WooCommerce.

The use of the plugin [Brazilian Market on WooCommerce](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) is mandatory, because in this way it is possible to send the fields of "CPF" or "CNPJ" to process the payment.

== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

= What is needed to use this plugin? =

* WooCommerce latest version installed and active.
* Own an account at [PicPay E-commerce](https://ecommerce.picpay.com/ "PicPay E-commerce").
* Generate the PicPay Token and the Seller Token in the [PicPay Dashboard](https://lojista.picpay.com/dashboard/login "PicPay Dashboard").
* Have the plugin [Brazilian Market on WooCommerce](http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) installed and configured.

= PicPay receives payments from which countries? =

At the moment PicPay receives payments only from Brazil.

The plugin has been configured to receive payments only from users who select Brazil in payment information during checkout.

== Screenshots ==

1. Plugin settings.
2. Payment method on the order completion page.
3. Example of the PicPay payment page.

== Changelog ==

= 1.1.5 - 2020/01/03 =

* Remove unused code.
* WC tested up to 3.8.1

= 1.1.4 - 2019/09/25 =

* Set SVG with both fixed width.

= 1.1.3 - 2019/09/24 =

* Updated plugin name from "WooCommerce PicPay" for "QR Code PicPay for WooCommerce".

= 1.1.2 - 2019/07/30 =

* Fixed order cancellation bug.

= 1.1.1 - 2019/04/10 =

* Changed to save the metadata in the order as the unique key.

= 1.1.0 - 2019/04/09 =

* Added the prefix "PicPay_" in all metadata that is saved in the order.

= 1.0.0 - 2019/03/03 =

* Published the first version.

== Upgrade Notice ==

= 1.1.5 =

* Remove unused code.

= 1.1.4 =

* Set SVG with both fixed width.

= 1.1.3 =

* Updated plugin name from "WooCommerce PicPay" for "QR Code PicPay for WooCommerce".

= 1.1.2 =

* Fix bug.

= 1.1.1 =

* Save the PicPay metadata as a unique key.

= 1.1.0 =

* Saves the metadata with a prefix that identifies the PicPay data.

"For God so loved the world, that he gave his only begotten Son, that whosoever believeth on him should not perish, but have eternal life." John 3:16