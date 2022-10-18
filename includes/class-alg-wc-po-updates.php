<?php
/**
 * Price Offers for WooCommerce - Updates Class
 *
 * @version 2.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Updates' ) ) :

class Alg_WC_PO_Updates {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		add_action( 'alg_wc_price_offerings_before_version_update', array( $this, 'schedule_update_events' ) );
		add_action( 'version_update_from_before_v200', array( $this, 'convert_price_offers_from_meta_to_custom_posts' ) );
	}

	/**
	 * schedule_update_events.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function schedule_update_events( $version ) {
		$prev_version = get_option( 'alg_wc_price_offerings_version', '' );
		if ( '' !== $prev_version && version_compare( $prev_version, '2.0.0', '<' ) ) {
			wp_schedule_single_event( time(), 'version_update_from_before_v200' );
		}
	}

	/**
	 * convert_price_offers_from_meta_to_custom_posts.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function convert_price_offers_from_meta_to_custom_posts() {

		// WP Query
		$query_args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_alg_wc_price_offerings',
					'compare' => 'EXISTS',
				),
			),
		);
		$query = new WP_Query( $query_args );
		if ( $query->have_posts() ) {

			// Get offers from meta
			$offers = array();
			foreach ( $query->posts as $product_id ) {
				$product_offers = get_post_meta( $product_id, '_alg_wc_price_offerings', true );
				if ( ! empty( $product_offers ) ) {
					foreach ( $product_offers as &$product_offer ) {
						$product_offer['product_id'] = $product_id;
						$offers[] = $product_offer;
					}
				}
			}

			// Sort by time
			usort( $offers, function( $a, $b ) {
				if ( $a['offer_timestamp'] == $b['offer_timestamp'] ) {
					return 0;
				}
				return ( $a['offer_timestamp'] < $b['offer_timestamp'] ? -1 : 1 );
			} );

			// Add offers as custom posts
			foreach ( $offers as $offer ) {
				$status = ( ! empty( $offer['status'] ) ? 'alg_wc_po_' . $offer['status'] : false );
				$time   = $offer['offer_timestamp'];
				unset( $offer['offer_timestamp'] );
				unset( $offer['status'] );
				if ( Alg_WC_PO_Core::create_price_offer( $status, $time, $offer ) ) {
					delete_post_meta( $offer['product_id'], '_alg_wc_price_offerings' );
				}
			}

		}
	}

}

endif;

return new Alg_WC_PO_Updates();
