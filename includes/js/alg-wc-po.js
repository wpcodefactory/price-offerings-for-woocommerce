/**
 * alg-wc-price-offerings.js
 *
 * @version 2.5.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

jQuery( document ).ready( function () {

	/**
	 * modal.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	var modal = jQuery( '#alg-wc-price-offerings-modal' );

	/**
	 * When the user clicks on the button, fill in values and open the modal.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	jQuery( '.alg-wc-price-offerings-button' ).on( 'click', function () {

		// Get data
		var data = JSON.parse( jQuery( this ).attr( 'alg_wc_price_offerings_data' ) );

		// Fill in price input
		var input = jQuery( '#alg-wc-price-offerings-price' );
		input.attr( 'step', data['price_step'] );
		input.attr( 'min', data['min_price'] );
		if ( 0 != data['max_price'] ) {
			input.attr( 'max', data['max_price'] );
		}
		if ( 0 != data['default_price'] ) {
			input.val( data['default_price'] );
		}

		// Fill in quantity input
		if ( 0 != data['default_quantity'] ) {
			var quantity_input = jQuery( '#alg-wc-price-offerings-quantity' );
			if ( quantity_input.length ) {
				quantity_input.val( data['default_quantity'] );
			}
		}

		// Price label
		jQuery( '#alg-wc-price-offerings-price-label' ).html( data['price_label'] );

		// Fill in form header
		jQuery( '#alg-wc-price-offerings-form-header' ).html( data['form_header'] );

		// Product ID (hidden input)
		jQuery( '#alg-wc-price-offerings-product-id' ).val( data['product_id'] );

		// Show the form
		modal.css( 'display', 'block' );

	} );

	/**
	 * When the user clicks on <span> (x), close the modal.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	jQuery( '.alg-wc-price-offerings-form-close' ).first().on( 'click', function () {
		modal.css( 'display', 'none' );
	} );

	/**
	 * When the user clicks anywhere outside of the modal, close it.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	jQuery( window ).on( 'click', function ( e ) {
		if ( modal.is( e.target ) ) {
			modal.css( 'display', 'none' );
		}
	} );

	/**
	 * When the user presses ESC, close the modal.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	jQuery( document ).on( 'keyup', function ( e ) {
		if ( 27 === e.keyCode ) {
			modal.css( 'display', 'none' );
		}
	} );

} );
