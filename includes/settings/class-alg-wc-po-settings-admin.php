<?php
/**
 * Price Offers for WooCommerce - Admin Section Settings
 *
 * @version 2.9.4
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Admin' ) ) :

class Alg_WC_PO_Settings_Admin extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'admin';
		$this->desc = __( 'Admin', 'price-offerings-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.9.4
	 * @since   1.0.0
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Admin Options', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_admin_options',
				'type'     => 'title',
			),
			array(
				'title'    => __( 'Product meta box', 'price-offerings-for-woocommerce' ),
				'desc'     => __( 'Enable', 'price-offerings-for-woocommerce' ),
				'desc_tip' => __( 'Adds an offers meta box to each product\'s admin edit page.', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_admin[meta_box_enabled]',
				'type'     => 'checkbox',
				'default'  => 'yes',
			),
			array(
				'desc'     => __( 'Meta box title', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_admin[meta_box_title]',
				'type'     => 'text',
				'default'  => __( 'Price offers', 'price-offerings-for-woocommerce' ),
			),
			array(
				'desc'     => __( 'Meta box columns', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_admin[meta_box_cols]',
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'default'  => array( 'title', 'customer_email', 'date', 'status', 'offered_price' ),
				'options'  => Alg_WC_PO_Core::get_product_meta_box_columns(),
			),
			array(
				'title'    => __( 'Currency code', 'price-offerings-for-woocommerce' ),
				'desc'     => __( 'Enable', 'price-offerings-for-woocommerce' ),
				'desc_tip' => __( 'Adds currency code to the admin columns and meta boxes.', 'price-offerings-for-woocommerce' ) . ' ' .
					__( 'Useful for the multi-currency stores.', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_admin_currency_code',
				'type'     => 'checkbox',
				'default'  => 'no',
			),
			array(
				'id'       => 'alg_wc_price_offerings_admin_options',
				'type'     => 'sectionend',
			),
		);
	}

}

endif;

return new Alg_WC_PO_Settings_Admin();
