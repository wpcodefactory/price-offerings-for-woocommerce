<?php
/**
 * Price Offers for WooCommerce - Section Settings
 *
 * @version 2.9.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings_Section' ) ) :

class Alg_WC_PO_Settings_Section {

	/**
	 * id.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	public $id;

	/**
	 * desc.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	public $desc;

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_sections_alg_wc_price_offerings',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_wc_price_offerings_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * placeholders_msg.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function placeholders_msg( $values ) {
		return sprintf( _n( 'Available placeholder: %s.', 'Available placeholders: %s.', count( $values ), 'price-offerings-for-woocommerce' ),
			'<code>' . implode( '</code>, <code>', $values ) . '</code>' );
	}

	/**
	 * get_terms.
	 *
	 * @version 2.2.4
	 * @since   1.0.0
	 */
	function get_terms( $args ) {
		if ( ! is_array( $args ) ) {
			$_taxonomy = $args;
			$args = array(
				'taxonomy'   => $_taxonomy,
				'orderby'    => 'name',
				'hide_empty' => false,
			);
		}
		global $wp_version;
		if ( version_compare( $wp_version, '4.5.0', '>=' ) ) {
			$_terms = get_terms( $args );
		} else {
			$_taxonomy = $args['taxonomy'];
			unset( $args['taxonomy'] );
			$_terms = get_terms( $_taxonomy, $args );
		}
		$_terms_options = array();
		if ( ! empty( $_terms ) && ! is_wp_error( $_terms ) ) {
			foreach ( $_terms as $_term ) {
				$_terms_options[ $_term->term_id ] = $_term->name . ' (' . $_term->slug . ')';
			}
		}
		return $_terms_options;
	}

}

endif;
