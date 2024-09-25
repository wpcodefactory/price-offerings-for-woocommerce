<?php
/**
 * Price Offers for WooCommerce - Price Offer Class
 *
 * @version 3.1.1
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Price_Offer' ) ) :

class Alg_WC_Price_Offer {

	/**
	 * id.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	public $id;

	/**
	 * values.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	public $values;

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct( $id ) {
		if ( 'alg_wc_price_offer' === get_post_type( $id ) ) {
			$this->id = $id;
			return $this->id;
		}
		return false;
	}

	/**
	 * get.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get( $option, $default = false ) {
		if ( ! isset( $this->values[ $option ] ) ) {
			$this->values[ $option ] = ( '' !== ( $value = get_post_meta( $this->id, $option, true ) ) ? $value : $default );
		}
		return $this->values[ $option ];
	}

	/**
	 * get_id.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_id() {
		return $this->id;
	}

	/**
	 * get_token.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_token() {
		return $this->get( 'token' );
	}

	/**
	 * get_token_url.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_token_url() {
		return ( ( $token = $this->get_token() ) ?
			add_query_arg( array( 'alg_wc_price_offer_id' => $this->id, 'alg_wc_price_offer_token' => $token ), site_url() ) :
			false
		);
	}

	/**
	 * get_status.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_status() {
		return get_post_status( $this->id );
	}

	/**
	 * get_status_name.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_status_name() {
		return ( ( $status_object = get_post_status_object( $this->get_status() ) ) ? $status_object->label : false );
	}

	/**
	 * get_status_label.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_status_label() {
		return '<mark class="order-status status-' . $this->get_status() . '"><span>' . $this->get_status_name() . '</span></mark>';
	}

	/**
	 * get_title.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_title() {
		return get_the_title( $this->id );
	}

	/**
	 * get_link.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_link() {
		return admin_url( 'post.php?post=' . $this->id . '&action=edit' );
	}

	/**
	 * get_timestamp.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_timestamp() {
		return get_post_time( 'U', true, $this->id );
	}

	/**
	 * get_formatted_date.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_formatted_date() {
		return get_post_time( Alg_WC_PO_Core::get_date_format(), false, $this->id, true );
	}

	/**
	 * get_formatted_modified_date.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_formatted_modified_date() {
		return get_post_modified_time( Alg_WC_PO_Core::get_date_format(), false, $this->id, true );
	}

	/**
	 * get_human_time_diff_date.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_human_time_diff_date() {
		$offer_timestamp = $this->get_timestamp();
		// Check if the offer was created within the last 24 hours, and not in the future.
		if ( $offer_timestamp > strtotime( '-1 day', time() ) && $offer_timestamp <= time() ) {
			return sprintf(
				/* translators: %s: human-readable time difference */
				_x( '%s ago', '%s = human-readable time difference', 'woocommerce' ),
				human_time_diff( $offer_timestamp, time() )
			);
		} else {
			return date_i18n( apply_filters( 'woocommerce_admin_order_date_format', __( 'M j, Y', 'woocommerce' ) ), $offer_timestamp );
		}
	}

	/**
	 * get_human_time_diff_date_label.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_human_time_diff_date_label() {
		return sprintf(
			'<time datetime="%1$s" title="%2$s">%3$s</time>',
			esc_attr( date( '"Y-m-d\TH:i:s+00:00', $this->get_timestamp() ) ),
			esc_html( $this->get_formatted_date() ),
			esc_html( $this->get_human_time_diff_date() )
		);
	}

	/**
	 * get_product.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_product() {
		return ( ( $product_id = $this->get_product_id() ) && ( $product = wc_get_product( $product_id ) ) ? $product : false );
	}

	/**
	 * get_product_id.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_product_id( $do_try_parent = false ) {
		$product_id = $this->get( 'product_id' );
		return ( $do_try_parent && 0 != ( $parent_id = wp_get_post_parent_id( $product_id ) ) ? $parent_id : $product_id );
	}

	/**
	 * get_product_name.
	 *
	 * @version 2.3.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) `get_title()`: variations?
	 */
	function get_product_name( $is_formatted_name = true ) {
		return ( ( $product = $this->get_product() ) ? ( $is_formatted_name ? $product->get_formatted_name() : $product->get_title() ) : false );
	}

	/**
	 * get_product_url.
	 *
	 * @version 2.9.2
	 * @since   2.9.2
	 */
	function get_product_url() {
		return ( ( $product = $this->get_product() ) ? $product->get_permalink() : false );
	}

	/**
	 * get_product_name_admin_link.
	 *
	 * @version 2.3.0
	 * @since   2.1.0
	 */
	function get_product_name_admin_link( $do_open_in_new_tab = true ) {
		return ( ( $product_id = $this->get_product_id( true ) ) ?
			'<a href="' . admin_url( 'post.php?post=' . $product_id . '&action=edit' ) . '"' . ( $do_open_in_new_tab ? ' target="_blank"' : '' ) . '>' .
				$this->get_product_name() .
			'</a>' :
			$this->get_product_name() );
	}

	/**
	 * get_product_sku.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function get_product_sku() {
		return ( ( $product = $this->get_product() ) ? $product->get_sku() : false );
	}

	/**
	 * get_price.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_price() {
		return $this->get( 'offered_price' );
	}

	/**
	 * get_accepted_price.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_accepted_price() {
		return $this->get( 'accepted_price' );
	}

	/**
	 * get_accepted_price_html.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_accepted_price_html() {
		return wc_price( $this->get_accepted_price(), array( 'currency' => $this->get_currency() ) );
	}

	/**
	 * get_price_summary.
	 *
	 * @version 2.9.4
	 * @since   2.0.0
	 *
	 * @todo    (dev) better styling
	 */
	function get_price_summary() {
		$summary = $this->get_price_html();
		if ( $this->get_accepted_price() && $this->get_accepted_price() != $this->get_price() ) {
			$summary .= ' > ' . $this->get_accepted_price_html();
		}
		if ( 'yes' === get_option( 'alg_wc_price_offerings_admin_currency_code', 'no' ) ) {
			$summary .= ' (' . $this->get_currency() . ')';
		}
		return $summary;
	}

	/**
	 * get_currency.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_currency() {
		return $this->get( 'currency_code' );
	}

	/**
	 * get_price_html.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_price_html() {
		return wc_price( $this->get_price(), array( 'currency' => $this->get_currency() ) );
	}

	/**
	 * get_quantity.
	 *
	 * @version 2.5.0
	 * @since   2.5.0
	 */
	function get_quantity() {
		return $this->get( 'quantity' );
	}

	/**
	 * get_customer_message.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_customer_message() {
		return $this->get( 'customer_message' );
	}

	/**
	 * get_customer_name.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_customer_name() {
		$customer_name = $this->get( 'customer_name' );
		if (
			( false === $customer_name || '' === $customer_name ) &&
			( $customer_id = $this->get_customer_id() ) &&
			( $user = get_user_by( 'id', $customer_id ) )
		) {
			$customer_name = $user->display_name;
		}
		return $customer_name;
	}

	/**
	 * get_customer_phone.
	 *
	 * @version 2.5.0
	 * @since   2.5.0
	 *
	 * @todo    (dev) code refactoring: (almost) repeating code: `( ( false === $customer_phone || '' === $customer_phone ) && ( $customer_id = $this->get_customer_id() ) && ( $user = get_user_by( 'id', $customer_id ) ) )`
	 */
	function get_customer_phone() {
		$customer_phone = $this->get( 'customer_phone' );
		if (
			( false === $customer_phone || '' === $customer_phone ) &&
			( $customer_id = $this->get_customer_id() ) &&
			( $user = get_user_by( 'id', $customer_id ) )
		) {
			$customer_phone = get_user_meta( $customer_id, 'billing_phone', true );
		}
		return $customer_phone;
	}

	/**
	 * get_customer_email.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_customer_email() {
		$customer_email = $this->get( 'customer_email' );
		if (
			( false === $customer_email || '' === $customer_email ) &&
			( $customer_id = $this->get_customer_id() ) &&
			( $user = get_user_by( 'id', $customer_id ) )
		) {
			$customer_email = $user->user_email;
		}
		return $customer_email;
	}

	/**
	 * get_customer_id.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_customer_id() {
		return $this->get( 'customer_id' );
	}

	/**
	 * get_user_ip.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_user_ip() {
		return $this->get( 'user_ip' );
	}

	/**
	 * get_user_agent.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_user_agent() {
		return $this->get( 'user_agent' );
	}

	/**
	 * get_sent_to.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_sent_to() {
		return ( array_filter( array(
			get_post_meta( $this->id, 'sent_to', true ),
			( $this->do_copy_to_customer() ? $this->get_customer_email() : '' )
		) ) );
	}

	/**
	 * get_notes.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_notes() {
		return $this->get( 'notes', array() );
	}

	/**
	 * get_messages.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_messages() {
		return $this->get( 'messages', array() );
	}

	/**
	 * set_accepted_price.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function set_accepted_price( $accepted_price ) {
		return update_post_meta( $this->id, 'accepted_price', $accepted_price );
	}

	/**
	 * do_copy_to_customer.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function do_copy_to_customer() {
		return ( 'yes' === get_post_meta( $this->id, 'copy_to_customer', true ) );
	}

	/**
	 * has_status.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function has_status( $statuses ) {
		return in_array( $this->get_status(), $statuses );
	}

	/**
	 * is_valid.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function is_valid() {
		return $this->has_status( array( 'alg_wc_po_counter', 'alg_wc_po_accept' ) );
	}

	/**
	 * add_note.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function add_note( $note ) {
		if ( '' !== $note ) {
			$notes = $this->get_notes();
			$notes[] = array( 'time' => time(), 'content' => $note );
			return update_post_meta( $this->id, 'notes', $notes );
		}
		return false;
	}

	/**
	 * add_message.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function add_message( $message ) {
		if ( isset( $message['content'] ) && '' !== $message['content'] ) {
			$messages = $this->get( 'messages', array() );
			if ( ! isset( $message['time'] ) ) {
				$message['time'] = time();
			}
			$messages[] = $message;
			return update_post_meta( $this->id, 'messages', $messages );
		}
		return false;
	}

	/**
	 * create_token.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) `$this->add_note( esc_html__( 'Token created.', 'price-offerings-for-woocommerce' ) );`?
	 */
	function create_token() {
		$token = md5( time() );
		if ( ( $result = update_post_meta( $this->id, 'token', $token ) ) ) {
			return $token;
		}
		return false;
	}

	/**
	 * delete_token.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function delete_token() {
		return ( delete_post_meta( $this->id, 'token' ) );
	}

	/**
	 * update_status.
	 *
	 * @version 2.7.0
	 * @since   2.0.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/wp_update_post/
	 */
	function update_status( $status, $extra_note = '' ) {
		$prev_status = $this->get_status_name();
		if (
			( $result = wp_update_post( array( 'ID' => $this->id, 'post_status' => $status ) ) ) &&
			$prev_status !== $this->get_status_name()
		) {
			$note = sprintf( esc_html__( 'Status updated from %s to %s.', 'price-offerings-for-woocommerce' ),
				'<strong>' . $prev_status . '</strong>', '<strong>' . $this->get_status_name() . '</strong>' );
			$this->add_note( ( '' !== $extra_note ? $note . ' ' . $extra_note : $note ) );
		}
		return $result;
	}

	/**
	 * process_action.
	 *
	 * @version 3.1.1
	 * @since   2.0.0
	 *
	 * @todo    (dev) `customer_phone`: `<a href="tel:...">...</a>`
	 * @todo    (dev) `process_placeholders`: duplicated in `Alg_WC_PO_Emails::send_email`
	 * @todo    (dev) move to `Alg_WC_PO_Meta_Boxes_Offer`?
	 * @todo    (dev) `%offered_price%`: rethink, maybe `%price%`?
	 * @todo    (dev) `accepted_price`: track prices history
	 */
	function process_action( $action, $args = array() ) {

		// "Accept" or "Counter"
		if ( in_array( $action, array( 'counter', 'accept' ) ) ) {

			$accepted_price = ( 'accept' === $action ?
				$this->get_price() :
				( $args['counter_price'] ?? false )
			);

			// Apply filter
			$accepted_price = apply_filters( 'alg_wc_po_accepted_price', $accepted_price, $action, $args, $this );

			if ( $accepted_price ) {

				// Set accepted price
				$this->set_accepted_price( $accepted_price );

				// Create token
				$this->create_token();
				$token_url = $this->get_token_url();

			} else {
				return false;
			}

		}

		// Send email
		if ( ! empty( $args['do_send_email'] ) ) {

			// Content
			$placeholders = array(
				'%add_to_cart_url%' => ( $token_url ?? '' ),
				'%counter_price%'   => ( isset( $args['counter_price'] ) ?
					wc_price( $args['counter_price'], array( 'currency' => $this->get_currency() ) ) :
					''
				),
				'%offered_price%'   => $this->get_price_html(),
				'%currency_code%'   => $this->get_currency(),
				'%quantity%'        => $this->get_quantity(),
				'%product_title%'   => $this->get_product_name( false ),
				'%product_url%'     => $this->get_product_url(),
				'%customer_name%'   => $this->get_customer_name(),
				'%customer_phone%'  => $this->get_customer_phone(),
			);
			$content = Alg_WC_PO_Emails::process_placeholders( $args['email_content'] );
			$content = wpautop( do_shortcode( str_replace( array_keys( $placeholders ), $placeholders, $content ) ) );

			// Add message
			$user = wp_get_current_user();
			$this->add_message( array(
				'user_id'      => $user->ID,
				'author'       => $user->display_name,
				'author_email' => $user->user_email,
				'content'      => $content,
			) );

			// Email
			Alg_WC_PO_Emails::send_email(
				$this->get_customer_email(),
				$args['email_subject'],
				$content,
				$args['email_heading'],
				alg_wc_po()->core->get_email_from_address(),
				alg_wc_po()->core->get_email_from_name(),
			);

		}

		return true;

	}

}

endif;
