<?php
/**
 * Price Offers for WooCommerce - Emails
 *
 * @version 2.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Emails' ) ) :

class Alg_WC_PO_Emails {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		return true;
	}

	/**
	 * send_email.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [next] (dev) `stripslashes`?
	 */
	static function send_email( $to, $subject, $content, $heading, $from, $from_name ) {

		// Subject
		$subject = self::process_placeholders( $subject );
		$subject = stripslashes( $subject );

		// Heading
		$heading = self::process_placeholders( $heading );
		$heading = stripslashes( $heading );

		// Content
		$content = self::process_placeholders( $content );
		$content = stripslashes( $content );
		$content = self::wrap_in_wc_email_template( $content, $heading );

		// Headers
		$headers = 'Content-Type: text/html' . "\r\n" .
			'From: "' . $from_name . '" <' . $from . '>' . "\r\n" .
			'Reply-To: ' . $from . "\r\n";

		// Send
		wc_mail( $to, $subject, $content, $headers );

	}

	/**
	 * process_placeholders.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	static function process_placeholders( $string ) {
		$domain = wp_parse_url( home_url(), PHP_URL_HOST );

		return str_replace(
			array(
				'{site_title}',
				'{site_address}',
				'{site_url}',
				'{woocommerce}',
				'{WooCommerce}',
			),
			array(
				wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
				$domain,
				$domain,
				'<a href="https://woocommerce.com">WooCommerce</a>',
				'<a href="https://woocommerce.com">WooCommerce</a>',
			),
			$string
		);
	}

	/**
	 * wrap_in_wc_email_template.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	static function wrap_in_wc_email_template( $content, $email_heading = '' ) {
		return self::get_wc_email_part( 'header', $email_heading ) . $content . self::process_placeholders( self::get_wc_email_part( 'footer' ) );
	}

	/**
	 * get_wc_email_part.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	static function get_wc_email_part( $part, $email_heading = '' ) {
		ob_start();
		switch ( $part ) {
			case 'header':
				wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
				break;
			case 'footer':
				wc_get_template( 'emails/email-footer.php' );
				break;
		}
		return ob_get_clean();
	}

}

endif;
