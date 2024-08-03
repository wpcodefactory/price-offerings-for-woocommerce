<?php
/**
 * Price Offers for WooCommerce - Settings
 *
 * @version 2.9.8
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Settings' ) ) :

class Alg_WC_PO_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.9.8
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_price_offerings';
		$this->label = __( 'Price Offers', 'price-offerings-for-woocommerce' );
		parent::__construct();
		// Sections
		require_once( 'class-alg-wc-po-settings-section.php' );
		require_once( 'class-alg-wc-po-settings-general.php' );
		require_once( 'class-alg-wc-po-settings-button.php' );
		require_once( 'class-alg-wc-po-settings-form.php' );
		require_once( 'class-alg-wc-po-settings-styling.php' );
		require_once( 'class-alg-wc-po-settings-email.php' );
		require_once( 'class-alg-wc-po-settings-actions.php' );
		require_once( 'class-alg-wc-po-settings-admin.php' );
	}

	/**
	 * get_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'price-offerings-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'price-offerings-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'price-offerings-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'price-offerings-for-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			add_action( 'admin_notices', array( $this, 'admin_notices_settings_reset_success' ), PHP_INT_MAX );
		}
	}

	/**
	 * admin_notices_settings_reset_success.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function admin_notices_settings_reset_success() {
		echo '<div class="notice notice-success is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'price-offerings-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * save.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

}

endif;

return new Alg_WC_PO_Settings();
