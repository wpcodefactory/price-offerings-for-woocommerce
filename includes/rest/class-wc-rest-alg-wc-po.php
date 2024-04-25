<?php
/**
 * Price Offers for WooCommerce - REST Price Offer Class
 *
 * @version 2.9.0
 * @since   2.9.0
 *
 * @author  Algoritmika Ltd
 *
 * @see     https://github.com/woocommerce/woocommerce/blob/8.8.2/plugins/woocommerce/includes/abstracts/abstract-wc-data.php
 * @see     https://github.com/woocommerce/woocommerce/blob/8.8.2/plugins/woocommerce/includes/class-wc-coupon.php
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_REST_Alg_WC_PO' ) ) :

class WC_REST_Alg_WC_PO extends WC_Data {

	/**
	 * offer.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	public $offer;

	/**
	 * Constructor.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @todo    (dev) merge `WC_REST_Alg_WC_PO` and `Alg_WC_Price_Offer` classes
	 */
	function __construct( $id ) {
		parent::__construct( $id );
		if ( 'alg_wc_price_offer' === get_post_type( $id ) ) {
			$this->set_id( $id );
			$this->offer = new Alg_WC_Price_Offer( $id );
		}
	}

	/**
	 * get_status.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	function get_status() {
		return $this->offer->get_status();
	}

	/**
	 * set_status.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	function set_status( $status ) {
		return $this->offer->update_status( $status );
	}

	/**
	 * process_action.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @todo    (dev) check if new status is not equal to old status (same for the `set_status()` function?)
	 */
	function process_action( $action ) {

		// Update status
		$this->set_status( "alg_wc_po_{$action}" );

		// Process action
		$options = array_replace(
			Alg_WC_PO_Core::get_default_action_option_values(),
			get_option( 'alg_wc_po_actions', array() )
		);
		$this->offer->process_action( $action, array(
			'do_send_email' => true,
			'email_subject' => wc_clean( $options[ "{$action}_default_email_subject" ] ),
			'email_heading' => wc_clean( $options[ "{$action}_default_email_heading" ] ),
			'email_content' => wp_kses_post( $options[ "{$action}_default_email_content" ] ),
		) );

	}

}

endif;
