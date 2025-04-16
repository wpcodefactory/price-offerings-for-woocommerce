<?php
/**
 * Price Offers for WooCommerce - Actions
 *
 * @version 3.4.2
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
	 * @version 3.4.0
	 * @since   2.0.0
	 */
	function __construct() {

		// "Add to cart" action
		add_action( 'wp_loaded', array( $this, 'add_to_cart' ), PHP_INT_MAX );
		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'exclude_cart_item_from_coupons' ), PHP_INT_MAX, 5 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_product_price' ) );
		add_filter( 'woocommerce_cart_item_is_purchasable', array( $this, 'force_cart_item_is_purchasable' ), PHP_INT_MAX, 3 );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), PHP_INT_MAX, 3 );

		// Order item meta
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_offer_id_to_order_item_meta' ), 10, 3 );
		add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'order_item_meta_display' ), 10, 2 );

		// Auto-Complete
		$hooks = get_option( 'alg_wc_po_auto_complete', array( 'woocommerce_checkout_order_processed' ) );
		foreach ( $hooks as $hook ) {
			if ( 'woocommerce_checkout_order_processed' === $hook ) {
				add_action( $hook, array( $this, 'complete_offer_from_cart' ), PHP_INT_MAX, 3 );
			} else {
				add_action( $hook, array( $this, 'complete_offer_from_order' ), PHP_INT_MAX );
			}
		}

		// Auto-Uncomplete
		$hooks = get_option( 'alg_wc_po_auto_uncomplete', array( 'woocommerce_cancelled_order' ) );
		foreach ( $hooks as $hook ) {
			add_action( $hook, array( $this, 'uncomplete_offer' ) );
		}

		// "Create offer" action
		add_action( 'init', array( $this, 'offer_price' ) );

	}

	/**
	 * add_offer_id_to_order_item_meta.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 */
	function add_offer_id_to_order_item_meta( $item, $cart_item_key, $cart_item ) {
		if ( isset( $cart_item['alg_wc_price_offer_id'] ) ) {
			$item->add_meta_data(
				'_alg_wc_price_offer_id',
				wc_clean( $cart_item['alg_wc_price_offer_id'] )
			);
		}
	}

	/**
	 * order_item_meta_display.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 */
	function order_item_meta_display( $display_key, $meta ) {
		return (
			'_alg_wc_price_offer_id' === $meta->key ?
			__( 'Offer ID', 'price-offerings-for-woocommerce' ) :
			$display_key
		);
	}

	/**
	 * exclude_cart_item_from_coupons.
	 *
	 * @version 2.8.0
	 * @since   2.8.0
	 */
	function exclude_cart_item_from_coupons( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		return (
			'yes' === get_option( 'alg_wc_po_exclude_cart_items_from_coupons', 'yes' ) &&
			! empty( $cart_item['alg_wc_price_offer_id'] ) ?
		0 : $discount );
	}

	/**
	 * apply_product_price.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) test `quantity > 1` + `set_sold_individually()`?
	 * @todo    (feature) fixed quantity: `$cart_object->set_quantity( $item_key, X, false );`?
	 */
	function apply_product_price( $cart_object ) {
		foreach ( $cart_object->get_cart() as $item_key => $item ) {
			if ( isset(
				$item['alg_wc_price_offer'],
				$item['alg_wc_price_offer_id'] )
			) {
				if (
					( $offer = new Alg_WC_Price_Offer( $item['alg_wc_price_offer_id'] ) ) &&
					$offer->is_valid()
				) {
					$item['data']->set_price( $item[ 'alg_wc_price_offer' ] );
					$item['data']->set_sold_individually( true );
				}
			}
		}
	}

	/**
	 * uncomplete_offer.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 *
	 * @todo    (feature) add more hooks?
	 */
	function uncomplete_offer( $order_id ) {
		if (
			( $order = wc_get_order( $order_id ) ) &&
			( $offer_ids = $order->get_meta( '_alg_wc_price_offer_ids' ) ) &&
			is_array( $offer_ids )
		) {
			foreach ( $offer_ids as $offer_id ) {
				if (
					( $offer = new Alg_WC_Price_Offer( $offer_id ) ) &&
					'alg_wc_po_complete' === $offer->get_status()
				) {
					$offer->update_status( 'alg_wc_po_accept',
						esc_html__( 'Reason: order cancelled.', 'price-offerings-for-woocommerce' ) );
				}
			}
		}
	}

	/**
	 * complete_offer_from_order.
	 *
	 * @version 3.4.0
	 * @since   3.4.0
	 *
	 * @todo    (feature) add more hooks?
	 * @todo    (dev) `update_status`: check for `'alg_wc_po_complete' !== $offer->get_status()` before updating the status?
	 * @todo    (dev) `$offer->delete_token()`?
	 */
	function complete_offer_from_order( $order_id ) {
		if ( ( $order = wc_get_order( $order_id ) ) ) {
			$offer_ids = array();
			foreach ( $order->get_items() as $item_key => $item ) {
				if ( $item->get_meta( '_alg_wc_price_offer_id' ) ) {
					$offer_id = wc_clean( $item->get_meta( '_alg_wc_price_offer_id' ) );
					if ( ( $offer = new Alg_WC_Price_Offer( $offer_id ) ) ) {
						$offer->update_status( 'alg_wc_po_complete' );
						$offer_ids[] = $offer_id;
					}
				}
			}
			if ( ! empty( $offer_ids ) ) {
				if ( '' === ( $_offer_ids = $order->get_meta( '_alg_wc_price_offer_ids' ) ) ) {
					$_offer_ids = array();
				}
				$order->update_meta_data( '_alg_wc_price_offer_ids', array_unique( array_merge( $_offer_ids, $offer_ids ) ) );
				$order->save();
			}
		}
	}

	/**
	 * complete_offer_from_cart.
	 *
	 * @version 3.4.0
	 * @since   2.0.0
	 *
	 * @todo    (feature) admin order meta box: "Related offers" (use `$order->get_meta( '_alg_wc_price_offer_ids' )`)
	 * @todo    (feature) add more hooks?
	 * @todo    (dev) `update_status`: check for `'alg_wc_po_complete' !== $offer->get_status()` before updating the status?
	 * @todo    (dev) `$offer->delete_token()`?
	 */
	function complete_offer_from_cart( $order_id, $posted_data, $order ) {
		$offer_ids = array();
		foreach ( WC()->cart->get_cart() as $item_key => $item ) {
			if ( isset( $item['alg_wc_price_offer_id'] ) ) {
				$offer_id = wc_clean( $item['alg_wc_price_offer_id'] );
				if ( ( $offer = new Alg_WC_Price_Offer( $offer_id ) ) ) {
					$offer->update_status( 'alg_wc_po_complete' );
					$offer_ids[] = $offer_id;
				}
			}
		}
		if ( ! empty( $offer_ids ) ) {
			$order->update_meta_data( '_alg_wc_price_offer_ids', array_unique( $offer_ids ) );
			$order->save();
		}
	}

	/**
	 * add_to_cart.
	 *
	 * @version 3.3.3
	 * @since   2.0.0
	 *
	 * @todo    (dev) better (and maybe customizable) notices
	 */
	function add_to_cart() {
		if (
			! empty( $_GET['alg_wc_price_offer_id'] ) &&
			! empty( $_GET['alg_wc_price_offer_token'] )
		) {

			$offer_id    = sanitize_text_field( wp_unslash( $_GET['alg_wc_price_offer_id'] ) );
			$offer_token = sanitize_text_field( wp_unslash( $_GET['alg_wc_price_offer_token'] ) );

			if (
				( $offer = new Alg_WC_Price_Offer( $offer_id ) ) &&
				$offer_token === $offer->get_token() &&
				$offer->is_valid()
			) {

				$product_id = $offer->get_product_id();
				$product    = wc_get_product( $product_id );

				if ( $product ) {

					$cart_item_data = array( 'alg_wc_price_offer' => $offer->get_accepted_price(), 'alg_wc_price_offer_id' => $offer_id );
					$cart_item_id   = ( 'variation' === $product->get_type() ?
						WC()->cart->generate_cart_id( $product->get_parent_id(), $product_id, $product->get_variation_attributes(), $cart_item_data ) :
						WC()->cart->generate_cart_id( $product_id,               0,           array(),                              $cart_item_data )
					);

					if ( ! WC()->cart->find_product_in_cart( $cart_item_id ) ) {

						add_filter( 'woocommerce_is_purchasable',              array( $this, 'product_is_purchasable' ), PHP_INT_MAX, 2 );
						add_filter( 'woocommerce_variation_is_purchasable',    array( $this, 'product_is_purchasable' ), PHP_INT_MAX, 2 );

						WC()->cart->add_to_cart( $product_id, max( $offer->get_quantity(), 1 ), 0, array(), $cart_item_data );

						remove_filter( 'woocommerce_is_purchasable',           array( $this, 'product_is_purchasable' ), PHP_INT_MAX );
						remove_filter( 'woocommerce_variation_is_purchasable', array( $this, 'product_is_purchasable' ), PHP_INT_MAX );

					} else {
						wc_add_notice( esc_html__( 'Product is already in the cart.', 'price-offerings-for-woocommerce' ), 'error' );
					}

					wp_safe_redirect( wc_get_cart_url() );
					exit;

				} else {
					wc_add_notice( esc_html__( 'Product not found.', 'price-offerings-for-woocommerce' ), 'error' );
				}

			} else {
				wc_add_notice( esc_html__( 'The link is no longer valid.', 'price-offerings-for-woocommerce' ), 'error' );
			}

		}
	}

	/**
	 * product_is_purchasable.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 */
	function product_is_purchasable( $is_purchasable, $product ) {
		if ( ! $is_purchasable && '' === $product->get_price() ) {
			return (
				$product->exists() &&
				( 'publish' === $product->get_status() || current_user_can( 'edit_post', $product->get_id() ) ) &&
				(
					'variation' !== $product->get_type() ||
					(
						( $parent = wc_get_product( $product->get_parent_id() ) ) &&
						( 'publish' === $parent->get_status() || current_user_can( 'edit_post', $parent->get_id() ) )
					)
				)
			);
		}
		return $is_purchasable;
	}

	/**
	 * force_cart_item_is_purchasable.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 */
	function force_cart_item_is_purchasable( $is_purchasable, $key, $values ) {
		return ( isset( $values['alg_wc_price_offer'], $values['alg_wc_price_offer_id'] ) ? true : $is_purchasable );
	}

	/**
	 * cart_item_quantity.
	 *
	 * @version 2.6.0
	 * @since   2.6.0
	 *
	 * @todo    (dev) make this optional || only for qty != 1
	 */
	function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		if ( isset( $cart_item['alg_wc_price_offer'], $cart_item['alg_wc_price_offer_id'] ) ) {
			return sprintf(
				'<label class="screen-reader-text" for="%1$s">%2$s</label><span id="%1$s">%3$s</span>',
				esc_attr( uniqid( 'quantity_' ) ),
				esc_html__( 'Quantity', 'woocommerce' ), // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
				$cart_item['quantity']
			);
		}
		return $product_quantity;
	}

	/**
	 * offer_price.
	 *
	 * @version 3.4.2
	 * @since   1.0.0
	 *
	 * @todo    (dev) start with "Create price offer"
	 * @todo    (feature) optionally require user to be registered to offer a price
	 * @todo    (feature) optional *plain text* email and optional *no wrapping in WC email template*
	 * @todo    (feature) separate *customer copy* email *template and subject*
	 * @todo    (feature) `%product_title%` etc. in notice, subject, heading etc.
	 * @todo    (dev) redirect (no notice though)
	 * @todo    (dev) check if mail has really been sent
	 * @todo    (dev) recheck "From" header
	 */
	function offer_price() {
		if ( isset(
			$_POST['alg-wc-price-offerings-submit'],
			$_POST['alg-wc-price-offerings-product-id'],
			$_POST['alg-wc-price-offerings-customer-id']
		) ) {

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
					sprintf(
						/* Translators: %s: Product title placeholder. */
						__( 'Product: %s', 'price-offerings-for-woocommerce' ),
						'%product_title%'
					) .
					'<br>' . PHP_EOL .
					sprintf(
						/* Translators: %s: Offered price placeholder. */
						__( 'Offered price: %s', 'price-offerings-for-woocommerce' ),
						'%offered_price%'
					) .
					'<br>' . PHP_EOL .
					sprintf(
						/* Translators: %1$s: Customer name placeholder, %2$s: Customer email placeholder. */
						__( 'From: %1$s %2$s', 'price-offerings-for-woocommerce' ),
						'%customer_name%',
						'%customer_email%'
					) .
					'<br>' . PHP_EOL .
					sprintf(
						/* Translators: %s: Customer message placeholder. */
						__( 'Message: %s', 'price-offerings-for-woocommerce' ),
						'%customer_message%'
					)
			), $email_options );

			// Email address
			$admin_email = get_option( 'admin_email' );
			if ( '' == $email_options['address'] ) {
				$email_options['address'] = $admin_email;
			} else {
				$product_author_email = (
					( $author_id = get_post_field( 'post_author', $product_id ) ) &&
					( $user_info = get_userdata( $author_id ) ) &&
					isset( $user_info->user_email ) ?
						$user_info->user_email :
						$admin_email
				);
				$email_options['address'] = str_replace(
					array( '%admin_email%', '%product_author_email%' ),
					array( $admin_email, $product_author_email ),
					$email_options['address']
				);
			}

			// Price offer array
			$price_offer = apply_filters( 'alg_wc_po_price_offer', array(
				'offer_timestamp'  => current_time( 'timestamp' ),
				'product_id'       => $product_id,
				'product_title'    => $product->get_title(),
				'product_sku'      => $product->get_sku(),
				'product_url'      => $product->get_permalink(),
				'currency_code'    => get_woocommerce_currency(),
				'customer_id'      => sanitize_text_field( wp_unslash( $_POST['alg-wc-price-offerings-customer-id'] ) ),
				'user_ip'          => ( isset( $_SERVER['REMOTE_ADDR'] )     ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) )     : '' ),
				'user_agent'       => ( isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '' ),
				'sent_to'          => $email_options['address'],
				'offered_price'    => ( isset( $_POST['alg-wc-price-offerings-price'] )          ? sanitize_text_field( wp_unslash( $_POST['alg-wc-price-offerings-price'] ) )          : '' ),
				'quantity'         => ( isset( $_POST['alg-wc-price-offerings-quantity'] )       ? sanitize_text_field( wp_unslash( $_POST['alg-wc-price-offerings-quantity'] ) )       : '' ),
				'customer_message' => ( isset( $_POST['alg-wc-price-offerings-message'] )        ? sanitize_text_field( wp_unslash( $_POST['alg-wc-price-offerings-message'] ) )        : '' ),
				'customer_name'    => ( isset( $_POST['alg-wc-price-offerings-customer-name'] )  ? sanitize_text_field( wp_unslash( $_POST['alg-wc-price-offerings-customer-name'] ) )  : '' ),
				'customer_phone'   => ( isset( $_POST['alg-wc-price-offerings-customer-phone'] ) ? sanitize_text_field( wp_unslash( $_POST['alg-wc-price-offerings-customer-phone'] ) ) : '' ),
				'customer_email'   => ( isset( $_POST['alg-wc-price-offerings-customer-email'] ) ? sanitize_text_field( wp_unslash( $_POST['alg-wc-price-offerings-customer-email'] ) ) : '' ),
				'copy_to_customer' => ( isset( $_POST['alg-wc-price-offerings-customer-copy'] )  ? sanitize_text_field( wp_unslash( $_POST['alg-wc-price-offerings-customer-copy'] ) )  : 'no' ),
			), $product );

			// Prevent duplicate offers
			if (
				'yes' === get_option( 'alg_wc_po_prevent_duplicate_offers', 'no' ) &&
				Alg_WC_PO_Core::is_duplicate( $price_offer )
			) {
				$message = get_option( 'alg_wc_po_prevent_duplicate_offers_notice',
					__( 'You have already sent this offer.', 'price-offerings-for-woocommerce' ) );
				wc_add_notice( $message, 'error' );
				return;
			}

			// Create price offer
			$offer_id = Alg_WC_PO_Core::create_price_offer( false, false, $price_offer );

			// Email content
			$placeholders = array(
				'%product_title%'    => $price_offer['product_title'],
				'%product_sku%'      => $price_offer['product_sku'],
				'%product_url%'      => $price_offer['product_url'],
				'%currency_code%'    => $price_offer['currency_code'],
				'%offered_price%'    => wc_price( $price_offer['offered_price'], array( 'currency' => $price_offer['currency_code'] ) ),
				'%quantity%'         => $price_offer['quantity'],
				'%customer_message%' => $price_offer['customer_message'],
				'%customer_name%'    => $price_offer['customer_name'],
				'%customer_phone%'   => $price_offer['customer_phone'],
				'%customer_email%'   => $price_offer['customer_email'],
				'%user_ip%'          => $price_offer['user_ip'],
				'%user_agent%'       => $price_offer['user_agent'],
			);
			$email_content = str_replace( array_keys( $placeholders ), $placeholders, $email_options['template'] );

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
				'message' => __( 'Your price offer has been sent.', 'price-offerings-for-woocommerce' ),
			), $notice_options );
			wc_add_notice( $notice_options['message'], 'notice' );

			// Action
			do_action( 'alg_wc_po_price_offer_created', $offer_id );

		}
	}

}

endif;

return new Alg_WC_PO_Actions();
