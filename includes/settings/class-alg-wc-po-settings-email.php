<?php
/**
 * Price Offers for WooCommerce - Email Section Settings
 *
 * @version 2.9.4
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Email' ) ) :

class Alg_WC_PO_Settings_Email extends Alg_WC_PO_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'email';
		$this->desc = __( 'Email', 'price-offerings-for-woocommerce' );
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
				'title'    => __( 'Email Options', 'price-offerings-for-woocommerce' ),
				'id'       => 'alg_wc_price_offerings_email_options',
				'type'     => 'title',
			),
			array(
				'title'    => __( 'Email recipient', 'price-offerings-for-woocommerce' ),
				'desc'     => __( 'Can be comma separated list.', 'price-offerings-for-woocommerce' ) . ' ' .
					sprintf( __( 'Use %s to send to administrator email: %s.', 'price-offerings-for-woocommerce' ),
						'<code>' . '%admin_email%' . '</code>', '<code>' . get_option( 'admin_email' ) . '</code>' ) . ' ' .
					$this->placeholders_msg( array( '%admin_email%', '%product_author_email%' ) ),
				'id'       => 'alg_wc_price_offerings_email[address]',
				'type'     => 'textarea',
				'default'  => '%admin_email%',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Email subject', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}' ) ),
				'id'       => 'alg_wc_price_offerings_email[subject]',
				'type'     => 'text',
				'default'  => '[{site_title}]: ' . __( 'Price offer', 'price-offerings-for-woocommerce' ),
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Email heading', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array( '{site_title}', '{site_url}' ) ),
				'id'       => 'alg_wc_price_offerings_email[heading]',
				'type'     => 'text',
				'default'  => __( 'Price offer', 'price-offerings-for-woocommerce' ),
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Email template', 'price-offerings-for-woocommerce' ),
				'desc'     => $this->placeholders_msg( array(
					'{site_title}',
					'{site_url}',
					'%product_title%',
					'%product_sku%',
					'%product_url%',
					'%offered_price%',
					'%currency_code%',
					'%quantity%',
					'%customer_name%',
					'%customer_phone%',
					'%customer_email%',
					'%customer_message%',
					'%user_ip%',
					'%user_agent%',
				) ),
				'id'       => 'alg_wc_price_offerings_email[template]',
				'type'     => 'textarea',
				'default'  =>
					sprintf( __( 'Product: %s', 'price-offerings-for-woocommerce' ),       '%product_title%' ) . '<br>' . PHP_EOL .
					sprintf( __( 'Offered price: %s', 'price-offerings-for-woocommerce' ), '%offered_price%' ) . '<br>' . PHP_EOL .
					sprintf( __( 'From: %s %s', 'price-offerings-for-woocommerce' ),       '%customer_name%', '%customer_email%' ) . '<br>' . PHP_EOL .
					sprintf( __( 'Message: %s', 'price-offerings-for-woocommerce' ),       '%customer_message%' ),
				'css'      => 'width:100%;height:200px;',
			),
			array(
				'id'       => 'alg_wc_price_offerings_email_options',
				'type'     => 'sectionend',
			),
		);
	}

}

endif;

return new Alg_WC_PO_Settings_Email();
