<?php
/**
 * Price Offers for WooCommerce - General Section Settings
 *
 * @version 2.8.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_General' ) ) :

class Alg_WC_PO_Settings_General extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'price-offerings-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.8.0
	 * @since   1.0.0
	 */
	function get_settings() {

		$general_settings = array(
			array(
				'title'             => __( 'General Options', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_general_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Products', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'You can enable price offers for <strong>all products</strong> or for <strong>selected products</strong> only.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_products',
				'type'              => 'select',
				'class'             => 'chosen_select',
				'default'           => 'yes',
				'options'           => array(
					'yes'           => __( 'Enable for all products', 'price-offerings-for-woocommerce' ),
					'no'            => __( 'Enable for selected products only', 'price-offerings-for-woocommerce' ),
				),
				'desc'              => apply_filters( 'alg_wc_price_offerings_settings',
					'You will need <a href="https://wpfactory.com/item/price-offerings-for-woocommerce/" target="_blank">Price Offers for WooCommerce Pro</a> to use "Enable for selected products only" option.' ),
				'custom_attributes' => apply_filters( 'alg_wc_price_offerings_settings', array( 'disabled' => 'disabled' ) ),
			),
			array(
				'title'             => __( 'Exclude', 'price-offerings-for-woocommerce' ),
				'desc'              => __( 'Out of stock', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Excludes out of stock products.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_exclude[out_of_stock]',
				'type'              => 'checkbox',
				'default'           => 'no',
				'checkboxgroup'     => 'start',
			),
			array(
				'desc'              => __( 'With price', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Excludes all products with price (i.e., with non-empty price).', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_exclude[with_price]',
				'type'              => 'checkbox',
				'default'           => 'no',
				'checkboxgroup'     => 'end',
			),
			array(
				'desc'              => __( 'Above price', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Excludes all products with the price higher than the set price.', 'price-offerings-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_exclude[above_price]',
				'type'              => 'number',
				'default'           => '',
				'custom_attributes' => array( 'min' => 0, 'step' => 0.0000001 ),
			),
			array(
				'desc'              => __( 'Below price', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Excludes all products with the price lower than the set price.', 'price-offerings-for-woocommerce' ) . ' ' .
					__( 'Ignored if set to zero.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_exclude[below_price]',
				'type'              => 'number',
				'default'           => '',
				'custom_attributes' => array( 'min' => 0, 'step' => 0.0000001 ),
			),
			array(
				'title'             => __( 'User visibility', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_po_offer_price_button_user_visibility',
				'type'              => 'select',
				'class'             => 'chosen_select',
				'default'           => 'all',
				'options'           => array(
					'all'       => __( 'All users', 'price-offerings-for-woocommerce' ),
					'logged_in' => __( 'Logged-in users only', 'price-offerings-for-woocommerce' ),
				),
			),
			array(
				'id'                => 'alg_wc_price_offerings_general_options',
				'type'              => 'sectionend',
			),
			array(
				'title'             => __( 'Selected Products Options', 'price-offerings-for-woocommerce' ),
				'desc'              => '<strong>' . __( 'This section is ignored unless "Enable for selected products only" is selected for "Products" option above.', 'price-offerings-for-woocommerce' ) . '</strong>',
				'id'                => 'alg_wc_price_offerings_selected_products_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Per product', 'price-offerings-for-woocommerce' ),
				'desc'              => __( 'Enable', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'If enabled, this will add new settings to each product\'s edit page ("Product data" meta box).', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_per_product',
				'type'              => 'checkbox',
				'default'           => 'no',
				'checkboxgroup'     => 'start',
				'show_if_checked'   => 'option',
			),
			array(
				'desc'              => __( 'Per variation', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Will add new options to each variation of a variable product.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_per_product_variation',
				'type'              => 'checkbox',
				'default'           => 'no',
				'checkboxgroup'     => 'end',
				'show_if_checked'   => 'yes',
			),
			array(
				'title'             => __( 'Per product category', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_per_category',
				'type'              => 'select',
				'class'             => 'chosen_select',
				'default'           => 'no',
				'options'           => array(
					'no'   => __( 'Disabled', 'price-offerings-for-woocommerce' ),
					'yes'  => __( 'Require categories', 'price-offerings-for-woocommerce' ),
					'excl' => __( 'Exclude categories', 'price-offerings-for-woocommerce' ),
				),
			),
			array(
				'desc'              => __( 'Product categories', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Ignored if the "Per product category" option is set to "Disabled".', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_per_category_cats',
				'type'              => 'multiselect',
				'class'             => 'chosen_select',
				'default'           => array(),
				'options'           => $this->get_terms( 'product_cat' ),
			),
			array(
				'id'                => 'alg_wc_price_offerings_selected_products_options',
				'type'              => 'sectionend',
			),
			array(
				'title'             => __( 'Advanced Options', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_advanced_options',
				'type'              => 'title',
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
				'desc_tip'          => sprintf( __( 'Queues emails for background processing with the %s.', 'price-offerings-for-woocommerce' ),
					'<a href="' . admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=alg_wc_price_offers_send_email' ) . '">' .
						__( 'Action Scheduler', 'price-offerings-for-woocommerce' ) .
					'</a>' ),
				'id'                => 'alg_wc_po_send_emails_in_background',
				'type'              => 'checkbox',
				'default'           => 'no',
			),
			array(
				'id'                => 'alg_wc_price_offerings_advanced_options',
				'type'              => 'sectionend',
			),
		);

		$notes = array(
			array(
				'title'             => __( 'Notes', 'price-offerings-for-woocommerce' ),
				'desc'              => '<span class="dashicons dashicons-lightbulb"></span> ' .
					sprintf( __( 'You can see and manage all price offers in the %s dashboard.', 'price-offerings-for-woocommerce' ),
						'<a href="' . admin_url( 'edit.php?post_type=alg_wc_price_offer' ) . '">' . __( 'Offers', 'price-offerings-for-woocommerce' ) . '</a>' ),
				'id'                => 'alg_wc_po_general_notes',
				'type'              => 'title',
			),
			array(
				'id'                => 'alg_wc_po_general_notes',
				'type'              => 'sectionend',
			),
		);

		return array_merge( $general_settings, $notes );
	}

}

endif;

return new Alg_WC_PO_Settings_General();
