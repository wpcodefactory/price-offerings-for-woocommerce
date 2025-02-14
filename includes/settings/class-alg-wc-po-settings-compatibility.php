<?php
/**
 * Price Offers for WooCommerce - Compatibility Section Settings
 *
 * @version 3.4.1
 * @since   3.3.2
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Compatibility' ) ) :

class Alg_WC_PO_Settings_Compatibility extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.3.2
	 * @since   3.3.2
	 */
	function __construct() {
		$this->id   = 'compatibility';
		$this->desc = __( 'Compatibility', 'price-offerings-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.4.1
	 * @since   3.3.2
	 */
	function get_settings() {
		return array(
			array(
				'title'             => __( 'Compatibility Options', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_compatibility_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Dokan multi-vendor', 'price-offerings-for-woocommerce' ),
				'desc'              => __( 'Enable', 'price-offerings-for-woocommerce' ),
				'desc_tip'          =>
					sprintf(
						/* Translators: %s: Plugin link. */
						__( '%s plugin compatibility.', 'price-offerings-for-woocommerce' ),
						'<a href="https://wordpress.org/plugins/dokan-lite/" target="_blank">' .
							__( 'Dokan', 'price-offerings-for-woocommerce' ) .
						'</a>'
					) .
					apply_filters( 'alg_wc_price_offerings_settings',
						'<br>You will need the <a href="https://wpfactory.com/item/price-offerings-for-woocommerce/" target="_blank">Price Offers for WooCommerce Pro</a> plugin to enable this option.' ),
				'id'                => 'alg_wc_po_dokan_enabled',
				'type'              => 'checkbox',
				'default'           => 'no',
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'              => __( 'Offer details for vendors', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Select the offer details that you want vendors to see in their dashboard.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_vendor_allowed_fields',
				'type'              => 'multiselect',
				'class'             => 'chosen_select',
				'default'           => array(
					'product',
					'price',
					'quantity',
					'customer',
					'phone',
					'email',
					'send_to',
				),
				'options'           => array(
					'product'  => __( 'Product', 'price-offerings-for-woocommerce' ),
					'price'    => __( 'Price', 'price-offerings-for-woocommerce' ),
					'quantity' => __( 'Quantity', 'price-offerings-for-woocommerce' ),
					'customer' => __( 'Customer', 'price-offerings-for-woocommerce' ),
					'phone'    => __( 'Phone', 'price-offerings-for-woocommerce' ),
					'email'    => __( 'Email', 'price-offerings-for-woocommerce' ),
					'send_to'  => __( 'Sent to', 'price-offerings-for-woocommerce' ),
				),
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'id'                => 'alg_wc_po_compatibility_options',
				'type'              => 'sectionend',
			),
		);
	}

}

endif;

return new Alg_WC_PO_Settings_Compatibility();
