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

if ( ! class_exists( 'Alg_WC_PO_Settings_Advanced' ) ) :

class Alg_WC_PO_Settings_Advanced extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'price-offerings-for-woocommerce' );
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
				'title'    => __( 'Advanced Options', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_advanced_options',
				'type'     => 'title',
			),
			array(
				'title'    => __( 'Exclude Payment Gateways', 'price-offerings-for-woocommerce' ),
				'desc'     => __( 'Enable', 'price-offerings-for-woocommerce' ),
				'desc_tip' => __( 'Exclude selected payment gateways when the total price is greater than the restricted price below.', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_advanced_enable',
				'type'     => 'checkbox',
				'default'  => 'yes',
			),
			array(
				'title'    => __( 'Restricted price', 'price-offerings-for-woocommerce' ),
				'desc'     => __( 'Always compare with the total cart price. If the restriction is 0 or empty, it will be disabled.', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_advanced_restricted_price',
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Payment gateways.', 'price-offerings-for-woocommerce' ),
				'desc'     => __( 'These payment gateways will be hidden when the total cart price ( exclude shipping ) exceeds the "Restricted Price."', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_advanced_choosen_gateways',
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => Alg_WC_PO_Core::get_all_payment_gateways(),
			),
			array(
				'id'       => 'alg_wc_price_offerings_advanced_options',
				'type'     => 'sectionend',
			),
		);
	}

}

endif;

return new Alg_WC_PO_Settings_Advanced();
