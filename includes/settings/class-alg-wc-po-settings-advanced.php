<?php
/**
 * Price Offers for WooCommerce - Advanced Section Settings
 *
 * @version 3.3.2
 * @since   3.3.2
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Advanced' ) ) :

class Alg_WC_PO_Settings_Advanced extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.3.2
	 * @since   3.3.2
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'price-offerings-for-woocommerce' );
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
				'title'             => __( 'Advanced Options', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_advanced_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Payment gateways', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Exclude (or restrict) select payment gateways from the accepted price offers.', 'price-offerings-for-woocommerce' ) . ' ' .
					__( 'Ignored if empty.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_payment_gateways',
				'type'              => 'multiselect',
				'class'             => 'chosen_select',
				'default'           => array(),
				'options'           => wp_list_pluck( WC()->payment_gateways->payment_gateways(), 'title' ),
			),
			array(
				'desc_tip'          => __( 'Exclude or restrict?', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_payment_gateways_action',
				'type'              => 'select',
				'class'             => 'chosen_select',
				'default'           => 'exclude',
				'options'           => array(
					'exclude' => __( 'Exclude', 'price-offerings-for-woocommerce' ),
					'require' => __( 'Restrict', 'price-offerings-for-woocommerce' ),
				),
			),
			array(
				'title'             => __( 'Exclude price offers from coupons', 'price-offerings-for-woocommerce' ),
				'desc'              => __( 'Exclude', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_exclude_cart_items_from_coupons',
				'type'              => 'checkbox',
				'default'           => 'yes',
			),
			array(
				'title'             => __( 'Send emails in background', 'price-offerings-for-woocommerce' ),
				'desc'              => __( 'Enable', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => sprintf(
					/* Translators: %s: Action Scheduler link. */
					__( 'Queues emails for background processing with the %s.', 'price-offerings-for-woocommerce' ),
					'<a href="' . admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=alg_wc_price_offers_send_email' ) . '">' .
						__( 'Action Scheduler', 'price-offerings-for-woocommerce' ) .
					'</a>'
				),
				'id'                => 'alg_wc_po_send_emails_in_background',
				'type'              => 'checkbox',
				'default'           => 'no',
			),
			array(
				'title'             => __( 'REST API', 'price-offerings-for-woocommerce' ),
				'desc'              => __( 'Enable', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => sprintf(
					'<details><summary style="cursor: pointer;">%s</summary><p>%s</p></details>',
					__( 'Routes', 'price-offerings-for-woocommerce' ),
					implode( '<br>', array(
						sprintf( '%s %s - %s',
							'<code>GET</code>',
							'<code>' . esc_html( '/wp-json/wc/v3/alg_wc_price_offers' ) . '</code>',
							__( 'List all offers.', 'price-offerings-for-woocommerce' )
						),
						sprintf( '%s %s - %s',
							'<code>GET</code>',
							'<code>' . esc_html( '/wp-json/wc/v3/alg_wc_price_offers/<id>' ) . '</code>',
							__( 'Retrieve an offer.', 'price-offerings-for-woocommerce' )
						),
						sprintf( '%s %s - %s',
							'<code>PUT</code>',
							'<code>' . esc_html( '/wp-json/wc/v3/alg_wc_price_offers/<id>' ) . '</code>',
							__( 'Update an offer.', 'price-offerings-for-woocommerce' )
						),
					) )
				),
				'id'                => 'alg_wc_po_rest_api_enabled',
				'type'              => 'checkbox',
				'default'           => 'no',
			),
			array(
				'id'                => 'alg_wc_price_offerings_advanced_options',
				'type'              => 'sectionend',
			),
		);
	}

}

endif;

return new Alg_WC_PO_Settings_Advanced();
