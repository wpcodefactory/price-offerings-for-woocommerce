<?php
/**
 * Price Offers for WooCommerce - Form Section Settings
 *
 * @version 2.9.9
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Form' ) ) :

class Alg_WC_PO_Settings_Form extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'form';
		$this->desc = __( 'Form', 'price-offerings-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_fields.
	 *
	 * @version 2.9.9
	 * @since   2.5.0
	 */
	function get_fields() {
		return array(
			'quantity'         => __( 'Quantity', 'price-offerings-for-woocommerce' ),
			'customer_name'    => __( 'Customer name', 'price-offerings-for-woocommerce' ),
			'customer_phone'   => __( 'Customer phone', 'price-offerings-for-woocommerce' ),
			'customer_message' => __( 'Customer message', 'price-offerings-for-woocommerce' ),
			'recaptcha'        => __( 'reCAPTCHA', 'price-offerings-for-woocommerce' ),
			'customer_copy'    => __( 'Send a copy to customer checkbox', 'price-offerings-for-woocommerce' ),
		);
	}

	/**
	 * get_recaptcha_site_key_instructions.
	 *
	 * @version 2.9.9
	 * @since   2.9.9
	 */
	function get_recaptcha_site_key_instructions() {
		return '<details style="cursor:pointer;">' .
			'<summary>' .
				esc_html__( 'Site key', 'price-offerings-for-woocommerce' ) .
			'</summary>' .
			'<p>' .
				sprintf(
					esc_html__( 'Visit the %s site and select "%s" for the "%s" option.', 'price-offerings-for-woocommerce' ),
					'<a href="https://www.google.com/recaptcha/admin/create" target="_blank">' .
						esc_html__( 'Google reCAPTCHA', 'price-offerings-for-woocommerce' ) .
					'</a>',
					'<em>' .
						esc_html__( 'Challenge (v2)', 'price-offerings-for-woocommerce' ) . ' > ' .
						esc_html__( '"I\'m not a robot" Checkbox', 'price-offerings-for-woocommerce' ) .
					'</em>',
					esc_html__( 'reCAPTCHA type', 'price-offerings-for-woocommerce' ),
				) .
			'</p>' .
		'</details>';
	}

	/**
	 * get_settings.
	 *
	 * @version 2.9.9
	 * @since   1.0.0
	 *
	 * @todo    (feature) make it optional - "When the user clicks anywhere outside of the modal, close it"
	 * @todo    (feature) notice: customizable notice type (now `notice`)
	 */
	function get_settings() {
		return array(
			array(
				'title'             => __( 'Field Options', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form_field_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Enabled fields', 'price-offerings-for-woocommerce' ),
				'desc'              => sprintf( __( '%s are always enabled.', 'price-offerings-for-woocommerce' ),
					'"' . implode( '", "', array(
						__( 'Price input', 'price-offerings-for-woocommerce' ),
						__( 'Customer email', 'price-offerings-for-woocommerce' ),
						__( 'Send button', 'price-offerings-for-woocommerce' ),
					) ) . '"' ),
				'id'                => 'alg_wc_price_offerings_form[enabled_fields]',
				'type'              => 'multiselect',
				'class'             => 'chosen_select',
				'default'           => array( 'customer_name', 'customer_message', 'customer_copy' ),
				'options'           => $this->get_fields(),
			),
			array(
				'title'             => __( 'Required fields', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => sprintf( __( 'Field must be included in the "%s" option as well.', 'price-offerings-for-woocommerce' ),
					__( 'Enabled fields', 'price-offerings-for-woocommerce' ) ),
				'desc'              => sprintf( __( '%s are always required.', 'price-offerings-for-woocommerce' ),
					'"' . implode( '", "', array(
						__( 'Price input', 'price-offerings-for-woocommerce' ),
						__( 'Customer email', 'price-offerings-for-woocommerce' ),
						__( 'Send button', 'price-offerings-for-woocommerce' ),
					) ) . '"' ),
				'id'                => 'alg_wc_price_offerings_form[required_fields]',
				'type'              => 'multiselect',
				'class'             => 'chosen_select',
				'default'           => array(),
				'options'           => $this->get_fields(),
			),
			array(
				'title'             => __( 'Price input', 'price-offerings-for-woocommerce' ),
				'desc'              => $this->placeholders_msg( array( '%currency_symbol%' ) ),
				'id'                => 'alg_wc_price_offerings_form[price_label]',
				'type'              => 'textarea',
				'default'           => sprintf( __( 'Your price (%s)', 'price-offerings-for-woocommerce' ), '%currency_symbol%' ),
				'css'               => 'width:100%;',
			),
			array(
				'desc'              => __( 'Price step', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[price_step]',
				'type'              => 'number',
				'default'           => 0.01,
				'custom_attributes' => array( 'min' => 0, 'step' => 0.0000001 ),
			),
			array(
				'desc'              => __( 'Minimal price', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[price_min]',
				'type'              => 'number',
				'default'           => 0,
				'custom_attributes' => array( 'min' => 0, 'step' => 0.0000001 ),
			),
			array(
				'desc'              => __( 'Maximal price', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Set to zero to disable.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[price_max]',
				'type'              => 'number',
				'default'           => 0,
				'custom_attributes' => array( 'min' => 0, 'step' => 0.0000001 ),
			),
			array(
				'desc'              => __( 'Default price', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Set to zero to disable.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[price_default]',
				'type'              => 'number',
				'default'           => 0,
				'custom_attributes' => array( 'min' => 0, 'step' => 0.0000001 ),
			),
			array(
				'title'             => __( 'Quantity', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[quantity]',
				'type'              => 'textarea',
				'default'           => __( 'Quantity', 'price-offerings-for-woocommerce' ),
				'css'               => 'width:100%;',
			),
			array(
				'desc'              => __( 'Default quantity', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'Set to zero to disable.', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[quantity_default]',
				'type'              => 'number',
				'default'           => 1,
				'custom_attributes' => array( 'min' => 0 ),
			),
			array(
				'title'             => __( 'Customer email', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[customer_email]',
				'type'              => 'textarea',
				'default'           => __( 'Your email', 'price-offerings-for-woocommerce' ),
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'Customer name', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[customer_name]',
				'type'              => 'textarea',
				'default'           => __( 'Your name', 'price-offerings-for-woocommerce' ),
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'Customer phone', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[customer_phone]',
				'type'              => 'textarea',
				'default'           => __( 'Your phone', 'price-offerings-for-woocommerce' ),
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'Customer message', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[customer_message]',
				'type'              => 'textarea',
				'default'           => __( 'Your message', 'price-offerings-for-woocommerce' ),
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'reCAPTCHA', 'price-offerings-for-woocommerce' ),
				'desc_tip'          => __( 'reCAPTCHA v2', 'price-offerings-for-woocommerce' ),
				'desc'              => $this->get_recaptcha_site_key_instructions(),
				'id'                => 'alg_wc_price_offerings_form[recaptcha_site_key]',
				'type'              => 'text',
				'default'           => '',
			),
			array(
				'desc'              => __( 'Secret key', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[recaptcha_secret_key]',
				'type'              => 'text',
				'default'           => '',
			),
			array(
				'desc'              => __( 'Error message', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[recaptcha_error_msg]',
				'type'              => 'text',
				'default'           => __( 'reCAPTCHA check failed.', 'price-offerings-for-woocommerce' ),
			),
			array(
				'title'             => __( 'Send a copy to customer checkbox', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[customer_copy]',
				'type'              => 'textarea',
				'default'           => __( 'Send a copy to your email', 'price-offerings-for-woocommerce' ),
				'css'               => 'width:100%;',
			),
			array(
				'id'                => 'alg_wc_price_offerings_form_field_options',
				'type'              => 'sectionend',
			),
			array(
				'title'             => __( 'Form and Notice Options', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form_options',
				'type'              => 'title',
			),
			array(
				'title'             => __( 'Form header', 'price-offerings-for-woocommerce' ),
				'desc'              => $this->placeholders_msg( array( '%product_title%' ) ),
				'id'                => 'alg_wc_price_offerings_form[header_template]',
				'type'              => 'textarea',
				'default'           => '<h3>' . sprintf( __( 'Suggest your price for %s', 'price-offerings-for-woocommerce' ), '%product_title%' ) . '</h3>',
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'Form button', 'price-offerings-for-woocommerce' ),
				'desc'              => __( 'Label', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[button_label]',
				'type'              => 'text',
				'default'           => __( 'Send', 'price-offerings-for-woocommerce' ),
				'css'               => 'width:100%;',
			),
			array(
				'desc'              => __( 'HTML style', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[button_style]',
				'type'              => 'text',
				'default'           => '',
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'Form footer', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[footer_template]',
				'type'              => 'textarea',
				'default'           => '',
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'Required HTML', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_form[required_html]',
				'type'              => 'textarea',
				'default'           => ' <abbr class="required" title="required">*</abbr>',
				'css'               => 'width:100%;',
			),
			array(
				'title'             => __( 'Customer notice', 'price-offerings-for-woocommerce' ),
				'id'                => 'alg_wc_price_offerings_customer_notice[message]',
				'type'              => 'textarea',
				'default'           => __( 'Your price offer has been sent.', 'price-offerings-for-woocommerce' ),
				'css'               => 'width:100%;',
			),
			array(
				'id'                => 'alg_wc_price_offerings_form_options',
				'type'              => 'sectionend',
			),
		);
	}

}

endif;

return new Alg_WC_PO_Settings_Form();
