/**
 * Price Offers for WooCommerce - Admin Meta Boxes JS
 *
 * @version 3.5.0
 * @since   3.5.0
 *
 * @author  WPFactory
 */

/* "Actions" meta box */
jQuery( document ).ready( function () {
	jQuery( '#alg-wc-price-offer-action' ).on( 'change' , function () {
		jQuery( '.alg-wc-price-offer-actions-data' ).hide();
		jQuery( '.alg-wc-price-offer-show-for-' + jQuery( this ).val() ).show();
		if ( 'counter' === jQuery( this ).val() ) {
			jQuery( '#alg-wc-price-offer-price-counter' ).prop( 'required', true );
		} else {
			jQuery( '#alg-wc-price-offer-price-counter' ).prop( 'required', false );
		}
	} );
} );
