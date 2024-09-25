/**
 * recaptcha.js
 *
 * @version 3.1.1
 * @since   2.9.9
 *
 * @author  Algoritmika Ltd
 */

jQuery( document ).ready( function () {

	/**
	 * Form submit.
	 *
	 * @version 3.1.1
	 * @since   2.9.9
	 *
	 * @todo    (dev) clear error message on the reCAPTCHA checkbox click?
	 */
	jQuery( '#alg-wc-price-offerings-form' ).on( 'submit', function ( e ) {

		// Clear error message
		jQuery( '.alg-wc-price-offerings-recaptcha-msg' ).text( '' );

		// Force submit (success)
		if ( jQuery( this ).attr( 'alg-wc-price-offerings-force-submit' ) ) {
			jQuery( this ).attr( 'alg-wc-price-offerings-force-submit', false );
			jQuery( this ).append( '<input type="hidden" name="alg-wc-price-offerings-submit" value="true">' );
			return true;
		}

		// Empty reCAPTCHA response
		if (
			'undefined' === typeof grecaptcha ||
			'' === grecaptcha.getResponse()
		) {
			jQuery( '.alg-wc-price-offerings-recaptcha-msg' ).text( alg_wc_po_recaptcha_object.error_msg );
			return false;
		}

		// AJAX
		jQuery.ajax( {
			type:     'POST',
			dataType: 'json',
			url:      alg_wc_po_recaptcha_object.ajax_url,
			data:     {
				action:             'alg_wc_price_offerings_recaptcha',
				recaptcha_response: grecaptcha.getResponse(),
			},
			success:  function ( msg ) {
				if ( msg ) {
					// Success
					jQuery( '#alg-wc-price-offerings-form' ).attr( 'alg-wc-price-offerings-force-submit', true );
					jQuery( '#alg-wc-price-offerings-form' ).submit();
				} else {
					// Error
					jQuery( '.alg-wc-price-offerings-recaptcha-msg' ).text( alg_wc_po_recaptcha_object.error_msg );
				}
			},
		} );

		// The end
		return false;

	} );

} );
