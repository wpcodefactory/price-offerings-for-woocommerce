/**
 * alg-wc-price-offerings.js
 *
 * @version 1.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

var alg_wc_price_offerings_modal = jQuery( '#alg-wc-price-offerings-modal' );

// When the user clicks on the button, fill in values and open the modal
jQuery( '.alg-wc-price-offerings-button' ).click( function () {

	// Get data
	var alg_wc_price_offerings_data = jQuery.parseJSON( jQuery( this ).attr( 'alg_wc_price_offerings_data' ) );

	// Fill in price input
	var alg_wc_price_offerings_price_input = jQuery( '#alg-wc-price-offerings-price' );
	alg_wc_price_offerings_price_input.attr( 'step', alg_wc_price_offerings_data[ 'price_step' ] );
	alg_wc_price_offerings_price_input.attr( 'min', alg_wc_price_offerings_data[ 'min_price' ] );
	if ( 0 != alg_wc_price_offerings_data[ 'max_price' ] ) {
		alg_wc_price_offerings_price_input.attr( 'max', alg_wc_price_offerings_data[ 'max_price' ] );
	}
	if ( 0 != alg_wc_price_offerings_data[ 'default_price' ] ) {
		alg_wc_price_offerings_price_input.val( alg_wc_price_offerings_data[ 'default_price' ] );
	}

	// Price label
	jQuery( '#alg-wc-price-offerings-price-label' ).html( alg_wc_price_offerings_data[ 'price_label' ] );

	// Fill in form header
	jQuery( '#alg-wc-price-offerings-form-header' ).html( alg_wc_price_offerings_data[ 'form_header' ] );

	// Product ID (hidden input)
	jQuery( '#alg-wc-price-offerings-product-id' ).val( alg_wc_price_offerings_data[ 'product_id' ] );

	// Show the form
	alg_wc_price_offerings_modal.css( 'display', 'block' );

} );

// When the user clicks on <span> (x), close the modal
jQuery( '.alg-wc-price-offerings-form-close' ).first().click( function () {
	alg_wc_price_offerings_modal.css( 'display', 'none' );
} );

// When the user clicks anywhere outside of the modal, close it
jQuery( window ).click( function ( e ) {
	if ( alg_wc_price_offerings_modal.is( e.target ) ) {
		alg_wc_price_offerings_modal.css( 'display', 'none' );
	}
} );

// When the user presses ESC, close the modal
jQuery( document ).keyup( function ( e ) {
	if ( 27 === e.keyCode ) {
		alg_wc_price_offerings_modal.css( 'display', 'none' );
	}
} );
