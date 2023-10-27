<?php
/**
 * Price Offers for WooCommerce - Styling Section Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Styling' ) ) :

class Alg_WC_PO_Settings_Styling extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) rename to "Form Styling"
	 */
	function __construct() {
		$this->id   = 'styling';
		$this->desc = __( 'Styling', 'price-offerings-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) better default values (e.g., match Storefront styling)
	 * @todo    (feature) customizable fonts etc.
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Form Styling Options', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_styling_options',
				'type'     => 'title',
			),
			array(
				'title'    => __( 'Width', 'price-offerings-for-woocommerce' ),
				'id'       => "alg_wc_price_offerings_styling[form_content_width]",
				'type'     => 'text',
				'default'  => '80%',
			),
			array(
				'title'    => __( 'Header background color', 'price-offerings-for-woocommerce' ),
				'id'       => "alg_wc_price_offerings_styling[form_header_back_color]",
				'type'     => 'color',
				'default'  => '#3d9cd2',
			),
			array(
				'title'    => __( 'Header text color', 'price-offerings-for-woocommerce' ),
				'id'       => "alg_wc_price_offerings_styling[form_header_text_color]",
				'type'     => 'color',
				'default'  => '#ffffff',
			),
			array(
				'title'    => __( 'Footer background color', 'price-offerings-for-woocommerce' ),
				'id'       => "alg_wc_price_offerings_styling[form_footer_back_color]",
				'type'     => 'color',
				'default'  => '#3d9cd2',
			),
			array(
				'title'    => __( 'Footer text color', 'price-offerings-for-woocommerce' ),
				'id'       => "alg_wc_price_offerings_styling[form_footer_text_color]",
				'type'     => 'color',
				'default'  => '#ffffff',
			),
			array(
				'id'       => 'alg_wc_price_offerings_styling_options',
				'type'     => 'sectionend',
			),
		);
	}

}

endif;

return new Alg_WC_PO_Settings_Styling();
