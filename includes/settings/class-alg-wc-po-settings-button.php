<?php
/**
 * Price Offers for WooCommerce - Button Section Settings
 *
 * @version 2.2.4
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Button' ) ) :

class Alg_WC_PO_Settings_Button extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'button';
		$this->desc = __( 'Button', 'price-offerings-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.2.4
	 * @since   1.0.0
	 *
	 * @todo    (dev) more info about position priorities, e.g.: __( 'Standard priorities for "Inside single product summary": title - 5, rating - 10, price - 10, excerpt - 20, add to cart - 30, meta - 40, sharing - 50', 'price-offerings-for-woocommerce' )
	 * @todo    (feature) more "Make an offer" button position options (on both single and archives)
	 */
	function get_settings() {
		return array(
			array(
				'title'             => __( 'Button Options', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_button_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Label', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_button[label]',
				'type'              => 'text',
				'default'           => __( 'Make an offer', 'price-offerings-for-woocommerce' ),
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'HTML class', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_button[class]',
				'type'              => 'text',
				'default'           => 'button',
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'HTML style', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_button[style]',
				'type'              => 'text',
				'default'           => '',
				'css'               => 'width:100%;',
				'desc'              => sprintf( __( 'E.g.: %s', 'price-offerings-for-woocommerce' ),
					'<code>background-color: #333333; border-color: #333333; color: #ffffff;</code>' ),
			),
			array(
				'title'             => __( 'Position on single product page', 'price-offerings-for-woocommerce' ),
				'desc'              => sprintf( __( 'Alternatively, you can use the %s shortcode.', 'price-offerings-for-woocommerce' ),
					'<code>[alg_wc_price_offers_button]</code>' ),
				'id'                => 'alg_wc_price_offerings_button[position_single_hook]',
				'type'              => 'select',
				'class'             => 'chosen_select',
				'default'           => 'woocommerce_single_product_summary',
				'options'           => array(
					'disable'                                   => __( 'Do not add', 'price-offerings-for-woocommerce' ),
					'woocommerce_before_single_product'         => __( 'Before single product', 'price-offerings-for-woocommerce' ),
					'woocommerce_before_single_product_summary' => __( 'Before single product summary', 'price-offerings-for-woocommerce' ),
					'woocommerce_single_product_summary'        => __( 'Inside single product summary', 'price-offerings-for-woocommerce' ),
					'woocommerce_before_add_to_cart_form'       => __( 'Before add to cart form', 'price-offerings-for-woocommerce' ),
					'woocommerce_after_add_to_cart_form'        => __( 'After add to cart form', 'price-offerings-for-woocommerce' ),
					'woocommerce_after_single_product_summary'  => __( 'After single product summary', 'price-offerings-for-woocommerce' ),
					'woocommerce_after_single_product'          => __( 'After single product', 'price-offerings-for-woocommerce' ),
				),
			),
			array(
				'desc'              => __( 'Position priority', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_button[position_single_priority]',
				'type'              => 'number',
				'default'           => 31,
			),
			array(
				'id'                => 'alg_wc_price_offerings_button_options',
				'type'              => 'sectionend',
			),
			array(
				'title'             => __( 'Advanced Positions Options', 'price-offerings-for-woocommerce' ),
				'desc'              => apply_filters( 'alg_wc_price_offerings_settings',
					'You will need <a href="https://wpfactory.com/item/price-offerings-for-woocommerce/" target="_blank">Price Offers for WooCommerce Pro</a> to change options in this section.' ),
				'id'                => 'alg_wc_price_offerings_button_advanced_positions_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Position on archive pages', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Possible values: Do not add; Before product; After product.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_button[position_archives_hook]',
				'type'              => 'select',
				'class'             => 'chosen_select',
				'default'           => 'disable',
				'options'           => array(
					'disable'                           => __( 'Do not add', 'price-offerings-for-woocommerce' ),
					'woocommerce_before_shop_loop_item' => __( 'Before product', 'price-offerings-for-woocommerce' ),
					'woocommerce_after_shop_loop_item'  => __( 'After product', 'price-offerings-for-woocommerce' ),
				),
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'              => __( 'Position priority', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_button[position_archives_priority]',
				'type'              => 'number',
				'default'           => 10,
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'             => __( 'Custom position(s)', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Add custom hook(s). If adding more than one hook, separate with vertical bar ( | ). Ignored if empty.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_button[position_custom_hook]',
				'type'              => 'textarea',
				'default'           => '',
				'css'               => 'width:100%;',
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'desc'              => __( 'Custom position priority', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Add custom hook priority. If adding more than one hook, separate with vertical bar ( | ).', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_button[position_custom_priority]',
				'type'              => 'textarea',
				'default'           => '',
				'css'               => 'width:100%;',
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'id'                => 'alg_wc_price_offerings_button_advanced_positions_options',
				'type'              => 'sectionend',
			),
		);
	}

}

endif;

return new Alg_WC_PO_Settings_Button();
