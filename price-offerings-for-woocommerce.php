<?php
/*
Plugin Name: Price Offers for WooCommerce
Plugin URI: https://wpfactory.com/item/price-offerings-for-woocommerce/
Description: Allows your customers to start product price negotiations with you.
Version: 3.3.0
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: price-offerings-for-woocommerce
Domain Path: /langs
WC tested up to: 9.3
Requires Plugins: woocommerce
*/

defined( 'ABSPATH' ) || exit;

if ( 'price-offerings-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 2.2.1
	 * @since   1.2.0
	 */
	$plugin = 'price-offerings-for-woocommerce-pro/price-offerings-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		defined( 'ALG_WC_PO_FILE_FREE' ) || define( 'ALG_WC_PO_FILE_FREE', __FILE__ );
		return;
	}
}

defined( 'ALG_WC_PO_VERSION' ) || define( 'ALG_WC_PO_VERSION', '3.3.0' );

defined( 'ALG_WC_PO_FILE' ) || define( 'ALG_WC_PO_FILE', __FILE__ );

require_once( 'includes/class-alg-wc-po.php' );

if ( ! function_exists( 'alg_wc_po' ) ) {
	/**
	 * Returns the main instance of Alg_WC_PO to prevent the need to use globals.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function alg_wc_po() {
		return Alg_WC_PO::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_po' );

/**
 * Registers the plugin activation hook.
 *
 * @version 3.1.0
 * @since   3.1.0
 */
register_activation_hook( ALG_WC_PO_FILE, 'Alg_WC_PO::activate' );
