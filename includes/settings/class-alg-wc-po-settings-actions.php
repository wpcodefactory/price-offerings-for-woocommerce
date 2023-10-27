<?php
/**
 * Price Offers for WooCommerce - Actions Section Settings
 *
 * @version 2.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Actions' ) ) :

class Alg_WC_PO_Settings_Actions extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		$this->id   = 'actions';
		$this->desc = __( 'Actions', 'price-offerings-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) better default values
	 */
	function get_settings() {
		$default = Alg_WC_PO_Core::get_default_action_option_values();
		return array(

			array(
				'title'    => __( '"Reject" Action Options', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_po_actions_reject_options',
				'type'     => 'title',
			),
			array(
				'title'    => __( 'Default email subject', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}' ) ),
				'id'       => 'alg_wc_po_actions[reject_default_email_subject]',
				'type'     => 'text',
				'default'  => $default['reject_default_email_subject'],
			),
			array(
				'title'    => __( 'Default email heading', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}' ) ),
				'id'       => 'alg_wc_po_actions[reject_default_email_heading]',
				'type'     => 'text',
				'default'  => $default['reject_default_email_heading'],
			),
			array(
				'title'    => __( 'Default email content', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}', '%product_title%', '%offered_price%', '%customer_name%' ) ),
				'id'       => 'alg_wc_po_actions[reject_default_email_content]',
				'type'     => 'textarea',
				'default'  => $default['reject_default_email_content'],
				'css'      => 'width:100%;height:200px;',
			),
			array(
				'id'       => 'alg_wc_po_actions_reject_options',
				'type'     => 'sectionend',
			),

			array(
				'title'    => __( '"Accept" Action Options', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_po_actions_accept_options',
				'type'     => 'title',
			),
			array(
				'title'    => __( 'Default email subject', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}' ) ),
				'id'       => 'alg_wc_po_actions[accept_default_email_subject]',
				'type'     => 'text',
				'default'  => $default['accept_default_email_subject'],
			),
			array(
				'title'    => __( 'Default email heading', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}' ) ),
				'id'       => 'alg_wc_po_actions[accept_default_email_heading]',
				'type'     => 'text',
				'default'  => $default['accept_default_email_heading'],
			),
			array(
				'title'    => __( 'Default email content', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}', '%product_title%', '%offered_price%', '%customer_name%', '%add_to_cart_url%' ) ),
				'id'       => 'alg_wc_po_actions[accept_default_email_content]',
				'type'     => 'textarea',
				'default'  => $default['accept_default_email_content'],
				'css'      => 'width:100%;height:200px;',
			),
			array(
				'id'       => 'alg_wc_po_actions_accept_options',
				'type'     => 'sectionend',
			),

			array(
				'title'    => __( '"Counter" Action Options', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_po_actions_counter_options',
				'type'     => 'title',
			),
			array(
				'title'    => __( 'Default email subject', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}' ) ),
				'id'       => 'alg_wc_po_actions[counter_default_email_subject]',
				'type'     => 'text',
				'default'  => $default['counter_default_email_subject'],
			),
			array(
				'title'    => __( 'Default email heading', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}' ) ),
				'id'       => 'alg_wc_po_actions[counter_default_email_heading]',
				'type'     => 'text',
				'default'  => $default['counter_default_email_heading'],
			),
			array(
				'title'    => __( 'Default email content', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}', '%product_title%', '%offered_price%', '%customer_name%', '%add_to_cart_url%', '%counter_price%' ) ),
				'id'       => 'alg_wc_po_actions[counter_default_email_content]',
				'type'     => 'textarea',
				'default'  => $default['counter_default_email_content'],
				'css'      => 'width:100%;height:200px;',
			),
			array(
				'id'       => 'alg_wc_po_actions_counter_options',
				'type'     => 'sectionend',
			),

		);
	}

}

endif;

return new Alg_WC_PO_Settings_Actions();
