<?php
/**
 * Price Offers for WooCommerce - Price Offer Advanced Class
 *
 * @version 2.9.7
 * @since   2.9.7
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Price_Offer_Advanced' ) ) :

class Alg_WC_Price_Offer_Advanced {

	/**
	 * Constructor.
	 *
	 * @version 2.9.7
	 * @since   2.9.7
	 */
	function __construct() {
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'filter_available_payment_gateways_price_offer' ), PHP_INT_MAX );
	}

	/**
	 * chosen_gateways.
	 *
	 * @version 2.9.7
	 * @since   2.9.7
	 * @todo    get stored gateways.
	 */
	function chosen_gateways() {
		
		$choosen_gateways = get_option( 'alg_wc_price_offerings_advanced_choosen_gateways', array() );
		
		return $choosen_gateways;
	}
	
	/**
	 * filter_available_payment_gateways_price_offer.
	 *
	 * @version 2.9.7
	 * @since   2.9.7
	 * @todo    Filter payment gateways on based on total cart price.
	 */
	function filter_available_payment_gateways_price_offer( $available_gateways ) {
		
		$alg_wc_price_offerings_advanced_enable = get_option( 'alg_wc_price_offerings_advanced_enable', 'no' );
		$stored_gateways = $this->chosen_gateways();
		
		// Is enabled .	
		if ( $alg_wc_price_offerings_advanced_enable == 'yes' && !empty( $stored_gateways ) ) {
			
			// If WC exist, Is checkout Page, Is cart not empty, Is not in pay failed order page (specially Paypal). 
			if( function_exists( 'WC' ) && ! is_null( WC()->checkout() ) && (! is_null( WC()->cart ) && ! WC()->cart->is_empty() ) && ! is_wc_endpoint_url('order-pay') ){
			
					$restricted_price = (float) get_option( 'alg_wc_price_offerings_advanced_restricted_price', 0 );
					$cart_content_total = (float) WC()->cart->get_cart_contents_total();
					
				// Is condition matched as per pricing rule.
				if( $restricted_price > 0 && $cart_content_total >= $restricted_price ) {
				
					
					foreach ( $available_gateways as $gateway_id => $gateway ) {
						
						// Hide stored gateways by gateway id.
						if ( in_array( $gateway_id, $stored_gateways ) ) {
							unset( $available_gateways[ $gateway_id ] );
						}
						
					}
				}

			}
		}
		
		return $available_gateways;
	} 

}

endif;

return new Alg_WC_Price_Offer_Advanced();