<?php
/**
 * Price Offers for WooCommerce - Automation Section Settings
 *
 * @version 3.3.2
 * @since   3.3.2
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Automation' ) ) :

class Alg_WC_PO_Settings_Automation extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.3.2
	 * @since   3.3.2
	 */
	function __construct() {
		$this->id   = 'automation';
		$this->desc = __( 'Automation', 'price-offerings-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.3.2
	 * @since   3.3.2
	 */
	function get_settings() {
		return array(

			array(
				'title'    => __( 'Prevent Duplicate Offers', 'price-offerings-for-woocommerce' ),
				'desc'     => __( 'Checks for duplicated offers, e.g., "Open" offers with the same product ID, customer ID, user IP, offered price, currency code, and quantity.', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_po_prevent_duplicate_offers_options',
				'type'     => 'title',
			),
			array(
				'title'    => __( 'Prevent duplicate offers', 'price-offerings-for-woocommerce' ),
				'desc'     => __( 'Enable', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_po_prevent_duplicate_offers',
				'type'     => 'checkbox',
				'default'  => 'no',
			),
			array(
				'title'    => __( 'Statuses', 'price-offerings-for-woocommerce' ),
				'desc_tip' => __( 'If empty, it will use the "Open" status.', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_po_prevent_duplicate_offers_post_status',
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'default'  => array( 'alg_wc_po_open' ),
				'options'  => alg_wc_po()->core->get_statuses(),
			),
			array(
				'title'    => __( 'Fields', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_po_prevent_duplicate_offers_keys',
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'default'  => array(
					'product_id',
					'customer_id',
					'user_ip',
					'offered_price',
					'currency_code',
					'quantity',
				),
				'options'  => array(
					'product_id'     => __( 'Product ID', 'price-offerings-for-woocommerce' ),
					'customer_id'    => __( 'Customer ID', 'price-offerings-for-woocommerce' ),
					'user_ip'        => __( 'User IP', 'price-offerings-for-woocommerce' ),
					'offered_price'  => __( 'Offered price', 'price-offerings-for-woocommerce' ),
					'currency_code'  => __( 'Currency code', 'price-offerings-for-woocommerce' ),
					'quantity'       => __( 'Quantity', 'price-offerings-for-woocommerce' ),
					'product_sku'    => __( 'Product SKU', 'price-offerings-for-woocommerce' ),
					'product_title'  => __( 'Product title', 'price-offerings-for-woocommerce' ),
					'customer_name'  => __( 'Customer name', 'price-offerings-for-woocommerce' ),
					'customer_phone' => __( 'Customer phone', 'price-offerings-for-woocommerce' ),
					'customer_email' => __( 'Customer email', 'price-offerings-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Customer notice', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_po_prevent_duplicate_offers_notice',
				'type'     => 'textarea',
				'default'  => __( 'You have already sent this offer.', 'price-offerings-for-woocommerce' ),
			),
			array(
				'id'       => 'alg_wc_po_prevent_duplicate_offers_options',
				'type'     => 'sectionend',
			),

			array(
				'title'             => __( 'Auto-Accept Options', 'price-offerings-for-woocommerce' ),
				'desc'              => apply_filters( 'alg_wc_price_offerings_settings',
					'You will need <a href="https://wpfactory.com/item/price-offerings-for-woocommerce/" target="_blank">Price Offers for WooCommerce Pro</a> to auto-accept offers.' ),
				'id'                => 'alg_wc_po_auto_accept_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Minimum price', 'price-offerings-for-woocommerce' ),
				'desc_tip'          =>
					__( 'Minimum price to auto-accept offers.', 'price-offerings-for-woocommerce' ) . ' ' .
					__( 'You can change this option on per product basis.', 'price-offerings-for-woocommerce' ) . ' ' .
					__( 'Set to zero to disable.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_auto_accept[min_price]',
				'type'              => 'number',
				'default'           => 0,
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'title'             => __( 'Customer notice', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_auto_accept[notice]',
				'type'              => 'textarea',
				'default'           => __( 'Your price offer has been accepted.', 'price-offerings-for-woocommerce' ),
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'id'                => 'alg_wc_po_auto_accept_options',
				'type'              => 'sectionend',
			),

			array(
				'title'             => __( 'Auto-Reject Options', 'price-offerings-for-woocommerce' ),
				'desc'              => apply_filters( 'alg_wc_price_offerings_settings',
					'You will need <a href="https://wpfactory.com/item/price-offerings-for-woocommerce/" target="_blank">Price Offers for WooCommerce Pro</a> to auto-reject offers.' ),
				'id'                => 'alg_wc_po_auto_reject_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Maximum price', 'price-offerings-for-woocommerce' ),
				'desc_tip'          =>
					__( 'Maximum price to auto-reject offers.', 'price-offerings-for-woocommerce' ) . ' ' .
					__( 'You can change this option on per product basis.', 'price-offerings-for-woocommerce' ) . ' ' .
					__( 'Set to zero to disable.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_auto_reject[max_price]',
				'type'              => 'number',
				'default'           => 0,
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'title'             => __( 'Customer notice', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_auto_reject[notice]',
				'type'              => 'textarea',
				'default'           => __( 'Your price offer has been rejected.', 'price-offerings-for-woocommerce' ),
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'readonly' => 'readonly' ) ),
			),
			array(
				'id'                => 'alg_wc_po_auto_reject_options',
				'type'              => 'sectionend',
			),
		);
	}

}

endif;

return new Alg_WC_PO_Settings_Automation();
