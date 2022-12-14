<?php
/**
 * Price Offers for WooCommerce - Actions
 *
 * @version 2.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Actions' ) ) :

class Alg_WC_PO_Actions {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {

		// "Add to cart" action
		add_action( 'wp_loaded', array( $this, 'add_to_cart' ), PHP_INT_MAX );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_product_price' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'complete_offer' ) );

		// "Create offer" action
		add_action( 'init', array( $this, 'offer_price' ) );

	}

	/**
	 * apply_product_price.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [next] (feature) fixed quantity: `$cart_object->set_quantity( $item_key, X, false );`
	 */
	function apply_product_price( $cart_object ) {
		foreach ( $cart_object->get_cart() as $item_key => $item ) {
			if ( isset( $item['alg_wc_price_offer'], $item['alg_wc_price_offer_id'] ) ) {
				if ( ( $offer = new Alg_WC_Price_Offer( $item['alg_wc_price_offer_id'] ) ) && $offer->is_valid() ) {
					$item['data']->set_price( $item[ 'alg_wc_price_offer' ] );
					$item['data']->set_sold_individually( true );
				}
			}
		}
	}

	/**
	 * complete_offer.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [next] (dev) `$offer->delete_token()`?
	 */
	function complete_offer() {
		foreach ( WC()->cart->get_cart() as $item_key => $item ) {
			if ( isset( $item['alg_wc_price_offer_id'] ) ) {
				if ( ( $offer = new Alg_WC_Price_Offer( $item['alg_wc_price_offer_id'] ) ) ) {
					$offer->update_status( 'alg_wc_po_complete' );
				}
			}
		}
	}

	/**
	 * add_to_cart.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [next] [!] (dev) `add_to_cart()`: products with an empty price: `woocommerce_is_purchasable`
	 * @todo    [next] (dev) The link is no longer valid: Better (and maybe customizable) message
	 */
	function add_to_cart() {
		if ( ! empty( $_GET['alg_wc_price_offer_id'] ) && ! empty( $_GET['alg_wc_price_offer_token'] ) ) {
			$offer_id    = wc_clean( $_GET['alg_wc_price_offer_id'] );
			$offer_token = wc_clean( $_GET['alg_wc_price_offer_token'] );
			if ( ( $offer = new Alg_WC_Price_Offer( $offer_id ) ) && $offer_token === $offer->get_token() && $offer->is_valid() ) {
				$product_id     = $offer->get_product_id();
				$cart_item_data = array( 'alg_wc_price_offer' => $offer->get_accepted_price(), 'alg_wc_price_offer_id' => $offer_id );
				if ( ! WC()->cart->find_product_in_cart( WC()->cart->generate_cart_id( $product_id, 0, array(), $cart_item_data ) ) ) {
					WC()->cart->add_to_cart( $product_id, 1, 0, array(), $cart_item_data );
				}
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			} else {
				wc_add_notice( esc_html__( 'The link is no longer valid.', 'price-offerings-for-woocommerce' ), 'error' );
			}
		}
	}

	/**
	 * offer_price.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (dev) start with "Create price offer"
	 * @todo    [next] (feature) optionally require user to be registered to offer a price
	 * @todo    [maybe] (feature) optional *plain text* email and optional *no wrapping in WC email template*
	 * @todo    [maybe] (feature) separate *customer copy* email *template and subject*
	 * @todo    [maybe] (feature) `%product_title%` etc. in notice, subject, heading etc.
	 * @todo    [maybe] (dev) redirect (no notice though)
	 * @todo    [maybe] (dev) check if mail has really been sent
	 * @todo    [maybe] (dev) recheck "From" header
	 */
	function offer_price() {
		if ( isset( $_POST['alg-wc-price-offerings-submit'] ) ) {

			// Product
			$product_id = intval( $_POST['alg-wc-price-offerings-product-id'] );
			$product    = wc_get_product( $product_id );
			if ( ! is_a( $product, 'WC_Product' ) ) {
				return;
			}

			// Email options
			$email_options = get_option( 'alg_wc_price_offerings_email', array() );
			$email_options = array_merge( array(
				'address'  => '%admin_email%',
				'subject'  => '[{site_title}]: ' . __( 'Price offer', 'price-offerings-for-woocommerce' ),
				'heading'  => __( 'Price offer', 'price-offerings-for-woocommerce' ),
				'template' =>
					sprintf( __( 'Product: %s', 'price-offerings-for-woocommerce' ),       '%product_title%' ) . '<br>' . PHP_EOL .
					sprintf( __( 'Offered price: %s', 'price-offerings-for-woocommerce' ), '%offered_price%' ) . '<br>' . PHP_EOL .
					sprintf( __( 'From: %s %s', 'price-offerings-for-woocommerce' ),       '%customer_name%', '%customer_email%' ) . '<br>' . PHP_EOL .
					sprintf( __( 'Message: %s', 'price-offerings-for-woocommerce' ),       '%customer_message%' )
			), $email_options );

			// Email address
			$admin_email = get_option( 'admin_email' );
			if ( '' == $email_options['address'] ) {
				$email_options['address'] = $admin_email;
			} else {
				$product_author_email = ( ( $author_id = get_post_field( 'post_author', $product_id ) ) && ( $user_info = get_userdata( $author_id ) ) && isset( $user_info->user_email ) ?
					$user_info->user_email : $admin_email );
				$email_options['address'] = str_replace( array( '%admin_email%', '%product_author_email%' ), array( $admin_email, $product_author_email ), $email_options['address'] );
			}

			// Price offer array
			$price_offer = array(
				'offer_timestamp'  => current_time( 'timestamp' ),
				'product_title'    => $product->get_title(),
				'product_sku'      => $product->get_sku(),
				'currency_code'    => get_woocommerce_currency(),
				'customer_id'      => wc_clean( $_POST['alg-wc-price-offerings-customer-id'] ),
				'user_ip'          => wc_clean( $_SERVER['REMOTE_ADDR'] ),
				'user_agent'       => wc_clean( $_SERVER['HTTP_USER_AGENT'] ),
				'sent_to'          => $email_options['address'],
				'offered_price'    => ( isset( $_POST['alg-wc-price-offerings-price'] )          ? wc_clean( $_POST['alg-wc-price-offerings-price'] )          : '' ),
				'customer_message' => ( isset( $_POST['alg-wc-price-offerings-message'] )        ? wc_clean( $_POST['alg-wc-price-offerings-message'] )        : '' ),
				'customer_name'    => ( isset( $_POST['alg-wc-price-offerings-customer-name'] )  ? wc_clean( $_POST['alg-wc-price-offerings-customer-name'] )  : '' ),
				'customer_email'   => ( isset( $_POST['alg-wc-price-offerings-customer-email'] ) ? wc_clean( $_POST['alg-wc-price-offerings-customer-email'] ) : '' ),
				'copy_to_customer' => ( isset( $_POST['alg-wc-price-offerings-customer-copy'] )  ? wc_clean( $_POST['alg-wc-price-offerings-customer-copy'] )  : 'no' ),
			);

			// Email content
			$replaced_values = array(
				'%product_title%'    => $price_offer['product_title'],
				'%product_sku%'      => $price_offer['product_sku'],
				'%offered_price%'    => wc_price( $price_offer['offered_price'] ),
				'%customer_message%' => $price_offer['customer_message'],
				'%customer_name%'    => $price_offer['customer_name'],
				'%customer_email%'   => $price_offer['customer_email'],
				'%user_ip%'          => $price_offer['user_ip'],
				'%user_agent%'       => $price_offer['user_agent'],
			);
			$email_content = str_replace( array_keys( $replaced_values ), array_values( $replaced_values ), $email_options['template'] );

			// Send email
			Alg_WC_PO_Emails::send_email(
				$email_options['address'],
				$email_options['subject'],
				$email_content,
				$email_options['heading'],
				$price_offer['customer_email'],
				$price_offer['customer_name']
			);
			if ( 'yes' === $price_offer['copy_to_customer'] ) {
				Alg_WC_PO_Emails::send_email(
					$price_offer['customer_email'],
					$email_options['subject'],
					$email_content,
					$email_options['heading'],
					$price_offer['customer_email'],
					$price_offer['customer_name']
				);
			}

			// Notice
			$notice_options = get_option( 'alg_wc_price_offerings_customer_notice', array() );
			$notice_options = array_merge( array(
				'message'  => __( 'Your price offer has been sent.', 'price-offerings-for-woocommerce' ),
			), $notice_options );
			wc_add_notice( $notice_options['message'], 'notice' );

			// Create price offer
			$price_offer['product_id'] = $product_id;
			Alg_WC_PO_Core::create_price_offer( false, false, $price_offer );

		}
	}

}

endif;

return new Alg_WC_PO_Actions();
