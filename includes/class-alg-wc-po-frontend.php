<?php
/**
 * Price Offers for WooCommerce - Frontend Class
 *
 * @version 2.1.2
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Frontend' ) ) :

class Alg_WC_PO_Frontend {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (feature) AJAX form
	 * @todo    [maybe] (feature) button: multiple positions
	 * @todo    [maybe] (feature) button: add shortcode
	 */
	function __construct() {

		// Button
		$position_options = get_option( 'alg_wc_price_offerings_button', array() );
		$position_options = array_merge( array(
			'position_single_hook'     => 'woocommerce_single_product_summary',
			'position_single_priority' => 31,
		), $position_options );
		if ( 'disable' != $position_options['position_single_hook'] ) {
			add_action( $position_options['position_single_hook'], array( $this, 'add_offer_price_button' ), $position_options['position_single_priority'] );
		}

		// Form
		add_action( 'wp_footer', array( $this, 'add_offer_price_form' ) );

		// Scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// CSS
		add_action( 'wp_head', array( $this, 'add_styling' ), PHP_INT_MAX );

		// Shortcodes
		add_shortcode( 'alg_wc_price_offerings_translate', array( $this, 'language_shortcode' ) );

		// Frontend loaded
		do_action( 'alg_wc_price_offerings_frontend_loaded', $this );

	}

	/**
	 * language_shortcode.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function language_shortcode( $atts, $content = '' ) {
		// E.g.: `[alg_wc_cpt_translate lang="EN,DE" lang_text="Text for EN & DE" not_lang_text="Text for other languages"]`
		if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
			return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
				$atts['not_lang_text'] : $atts['lang_text'];
		}
		// E.g.: `[alg_wc_cpt_translate lang="EN,DE"]Text for EN & DE[/alg_wc_cpt_translate][alg_wc_cpt_translate not_lang="EN,DE"]Text for other languages[/alg_wc_cpt_translate]`
		return (
			( ! empty( $atts['lang'] )     && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
			( ! empty( $atts['not_lang'] ) &&     defined( 'ICL_LANGUAGE_CODE' ) &&   in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
		) ? '' : $content;
	}

	/**
	 * add_styling.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_styling() {
		$styling_options = get_option( 'alg_wc_price_offerings_styling', array() );
		$styling_options = array_merge( array(
			'form_content_width'     => '80%',
			'form_header_back_color' => '#3d9cd2',
			'form_header_text_color' => '#ffffff',
			'form_footer_back_color' => '#3d9cd2',
			'form_footer_text_color' => '#ffffff',
		), $styling_options );
		echo "
			<style type=\"text/css\">
				.alg-wc-price-offerings-modal-content {
					width: {$styling_options['form_content_width']};
				}
				.alg-wc-price-offerings-modal-header {
					background-color: {$styling_options['form_header_back_color']};
					color: {$styling_options['form_header_text_color']};
				}
				.alg-wc-price-offerings-modal-header h1,
				.alg-wc-price-offerings-modal-header h2,
				.alg-wc-price-offerings-modal-header h3,
				.alg-wc-price-offerings-modal-header h4,
				.alg-wc-price-offerings-modal-header h5,
				.alg-wc-price-offerings-modal-header h6 {
					color: {$styling_options['form_header_text_color']};
				}
				.alg-wc-price-offerings-modal-footer {
					background-color: {$styling_options['form_footer_back_color']};
					color: {$styling_options['form_footer_text_color']};
				}
				.alg-wc-price-offerings-modal-footer h1,
				.alg-wc-price-offerings-modal-footer h2,
				.alg-wc-price-offerings-modal-footer h3,
				.alg-wc-price-offerings-modal-footer h4,
				.alg-wc-price-offerings-modal-footer h5,
				.alg-wc-price-offerings-modal-footer h6 {
					color: {$styling_options['form_footer_text_color']};
				}
			</style>
		";
	}

	/**
	 * enqueue_scripts.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @see     https://www.w3schools.com/howto/howto_css_modals.asp
	 *
	 * @todo    [maybe] (dev) enqueue only if really needed
	 */
	function enqueue_scripts() {
		$min_suffix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ? '' : '.min' );
		wp_enqueue_style(
			'alg-wc-price-offerings',
			alg_wc_po()->plugin_url() . '/includes/css/alg-wc-po' . $min_suffix . '.css',
			array(),
			alg_wc_po()->version
		);
		wp_enqueue_script(
			'alg-wc-price-offerings',
			alg_wc_po()->plugin_url() . '/includes/js/alg-wc-po' . $min_suffix . '.js',
			array( 'jquery' ),
			alg_wc_po()->version,
			true
		);
	}

	/**
	 * add_offer_price_form.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (feature) `do_shortcode()`
	 * @todo    [maybe] (feature) style options for input fields (class, style)
	 * @todo    [maybe] (feature) form template
	 * @todo    [maybe] (feature) optional, additional and custom form fields
	 * @todo    [maybe] (dev) fix when empty header
	 * @todo    [maybe] (dev) logged user - check `nickname` and `billing_email`
	 * @todo    [maybe] (dev) better required asterix default
	 */
	function add_offer_price_form() {

		// Form options
		$form_options = get_option( 'alg_wc_price_offerings_form', array() );
		$form_options = array_merge( array(
			'enabled_fields'   => array( 'customer_name', 'customer_message', 'customer_copy' ),
			'required_fields'  => array(),
			'customer_email'   => __( 'Your email', 'price-offerings-for-woocommerce' ),
			'customer_name'    => __( 'Your name', 'price-offerings-for-woocommerce' ),
			'customer_message' => __( 'Your message', 'price-offerings-for-woocommerce' ),
			'customer_copy'    => __( 'Send a copy to your email', 'price-offerings-for-woocommerce' ),
			'button_label'     => __( 'Send', 'price-offerings-for-woocommerce' ),
			'button_style'     => '',
			'footer_template'  => '',
			'required_html'    => ' <abbr class="required" title="required">*</abbr>',
		), $form_options );
		// Form options: Enabled fields
		$form_options['enabled_fields'] = array_intersect(
			array( 'price', 'customer_email', 'customer_name', 'customer_message', 'button', 'customer_copy' ),
			array_merge( array( 'price', 'customer_email', 'button' ), $form_options['enabled_fields'] )
		);
		// Form options: Required fields
		$form_options['required_fields'] = array_merge( array( 'price', 'customer_email' ), $form_options['required_fields'] );

		// Prepare logged user data
		$customer_name  = '';
		$customer_email = '';
		$customer_id    = 0;
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$customer_id  = $current_user->ID;
			if ( '' != ( $meta = get_user_meta( $current_user->ID, 'nickname', true ) ) ) {
				$customer_name = $meta;
			}
			if ( '' != ( $meta = get_user_meta( $current_user->ID, 'billing_email', true ) ) ) {
				$customer_email = $meta;
			}
		}

		// Form fields
		$fields = array();
		foreach ( $form_options['enabled_fields'] as $field ) {

			// Field data
			$data         = array();
			$default_data = array(
				'id'          => 'alg-wc-price-offerings-' . str_replace( '_', '-', $field ),
				'type'        => 'text',
				'is_required' => in_array( $field, $form_options['required_fields'] ),
				'is_label'    => true,
				'label'       => ( isset( $form_options[ $field ] ) ? $form_options[ $field ] : '' ),
				'glue'        => '<br>',
				'value'       => '',
			);
			switch ( $field ) {
				case 'price':
					$data = array(
						'type'        => 'number',
						'label'       => '<span id="alg-wc-price-offerings-price-label"></span>',
					);
					break;
				case 'customer_email':
					$data = array(
						'type'        => 'email',
						'value'       => $customer_email,
					);
					break;
				case 'customer_name':
					$data = array(
						'value'       => $customer_name,
					);
					break;
				case 'customer_message':
					$data = array(
						'id'          => 'alg-wc-price-offerings-message',
						'type'        => 'textarea',
					);
					break;
				case 'button':
					$data = array(
						'id'          => 'alg-wc-price-offerings-submit',
						'type'        => 'submit',
						'is_label'    => false,
						'value'       => $form_options['button_label'],
						'style'       => $form_options['button_style'],
					);
					break;
				case 'customer_copy':
					$data = array(
						'type'        => 'checkbox',
						'glue'        => ' ',
						'value'       => 'yes',
					);
					break;
			}

			// Field HTML
			$data  = array_merge( $default_data, $data );
			$label = ( $data['is_label'] ?
				'<label for="' . $data['id'] . '">' . $data['label'] . ( $data['is_required'] ? $form_options['required_html'] : '' ) . '</label>' . $data['glue'] : '' );
			$input = ( 'textarea' === $data['type'] ?
				'<textarea' . ( $data['is_required'] ? ' required' : '' ) . ' id="' . $data['id'] . '" name="' . $data['id'] . '">' . $data['value'] . '</textarea>' :
				'<input type="' . $data['type'] . '"' . ( $data['is_required'] ? ' required' : '' ) . ' id="' . $data['id'] . '" name="' . $data['id'] . '"' .
					( isset( $data['style'] ) ? ' style="' . $data['style'] . '"' : '' ) . ( '' !== $data['value'] ? ' value="' . $data['value'] . '"' : '' ) . '>' );
			$fields[] = '<p class="' . $data['id'] . '-wrapper">' . $label . $input . '</p>';

		}

		// Final form
		echo '<div id="alg-wc-price-offerings-modal" class="alg-wc-price-offerings-modal">' .
			'<div class="alg-wc-price-offerings-modal-content">' .
				'<div class="alg-wc-price-offerings-modal-header">' .
					'<span class="alg-wc-price-offerings-form-close">&times;</span>' . '<div id="alg-wc-price-offerings-form-header"></div>' .
				'</div>' .
				'<div class="alg-wc-price-offerings-modal-body">' .
					'<form method="post" id="alg-wc-price-offerings-form">' .
						implode( '', $fields ) .
						'<input type="hidden" id="alg-wc-price-offerings-product-id" name="alg-wc-price-offerings-product-id">' .
						'<input type="hidden" name="alg-wc-price-offerings-customer-id" value="' . $customer_id . '">' .
					'</form>' .
				'</div>' .
				( '' != $form_options['footer_template'] ? '<div class="alg-wc-price-offerings-modal-footer">' . $form_options['footer_template'] . '</div>' : '' ) .
			'</div>' .
		'</div>';

	}

	/**
	 * is_offer_price_enabled_for_product.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function is_offer_price_enabled_for_product( $product_id ) {
		return apply_filters( 'alg_wc_price_offerings_is_enabled_for_product', true, $product_id );
	}

	/**
	 * is_offer_price_excluded_for_product.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    [later] (feature) add more conditions to exclude (i.e. not only "out of stock" and "with non-empty price"), e.g. grouped products
	 */
	function is_offer_price_excluded_for_product( $product_id ) {
		$exclude_options = get_option( 'alg_wc_price_offerings_exclude', array() );
		$exclude_options = array_merge( array(
			'out_of_stock' => 'no',
			'with_price'   => 'no',
		), $exclude_options );
		if ( in_array( 'yes', $exclude_options ) ) {
			$product = wc_get_product( $product_id );
			if (
				( 'yes' === $exclude_options['out_of_stock'] && ! $product->is_in_stock() ) ||
				( 'yes' === $exclude_options['with_price']   && '' !== $product->get_price() )
			) {
				return true;
			}
		}
		return false;
	}

	/**
	 * get_data_array.
	 *
	 * @version 2.1.2
	 * @since   1.0.0
	 */
	function get_data_array( $product_id ) {

		$form_options = get_option( 'alg_wc_price_offerings_form', array() );
		$form_options = array_merge( array(
			'price_label'     => sprintf( __( 'Your price (%s)', 'price-offerings-for-woocommerce' ), '%currency_symbol%' ),
			'price_step'      => 0.01,
			'price_min'       => 0,
			'price_max'       => 0,
			'price_default'   => 0,
			'header_template' => '<h3>' . sprintf( __( 'Suggest your price for %s', 'price-offerings-for-woocommerce' ), '%product_title%' ) . '</h3>',
		), $form_options );

		$is_pp         = apply_filters( 'alg_wc_price_offerings_is_enabled_per_product', false );
		$price_step    = ( ! $is_pp || '' === ( $pp = get_post_meta( $product_id, '_alg_wc_price_offerings_price_step',    true ) ) ? $form_options['price_step']    : $pp );
		$price_min     = ( ! $is_pp || '' === ( $pp = get_post_meta( $product_id, '_alg_wc_price_offerings_min_price',     true ) ) ? $form_options['price_min']     : $pp );
		$max_price     = ( ! $is_pp || '' === ( $pp = get_post_meta( $product_id, '_alg_wc_price_offerings_max_price',     true ) ) ? $form_options['price_max']     : $pp );
		$default_price = ( ! $is_pp || '' === ( $pp = get_post_meta( $product_id, '_alg_wc_price_offerings_default_price', true ) ) ? $form_options['price_default'] : $pp );

		return array(
			'price_step'    => $price_step,
			'min_price'     => $price_min,
			'max_price'     => $max_price,
			'default_price' => $default_price,
			'price_label'   => str_replace( '%currency_symbol%', get_woocommerce_currency_symbol(), $form_options['price_label'] ),
			'form_header'   => str_replace( '%product_title%', get_the_title(), $form_options['header_template'] ),
			'product_id'    => $product_id,
		);

	}

	/**
	 * add_offer_price_button.
	 *
	 * @version 2.1.2
	 * @since   1.0.0
	 *
	 * @see     https://www.php.net/manual/en/function.htmlspecialchars.php
	 * @see     https://stackoverflow.com/questions/34769665/php-json-encode-data-with-double-quotes
	 */
	function add_offer_price_button() {
		$product_id = get_the_ID();

		// Check if enabled for current product
		if ( ! $this->is_offer_price_enabled_for_product( $product_id ) || $this->is_offer_price_excluded_for_product( $product_id ) ) {
			return;
		}

		// The button
		$button_options = get_option( 'alg_wc_price_offerings_button', array() );
		$button_options = array_merge( array(
			'class' => 'button',
			'style' => '',
			'label' => __( 'Make an offer', 'price-offerings-for-woocommerce' ),
		), $button_options );

		echo '<p class="alg-wc-price-offerings-button-wrapper">' .
			'<button' .
				' type="submit"' .
				' name="alg-wc-price-offerings-button"' .
				' class="alg-wc-price-offerings-button' . ' ' . $button_options['class'] . '"' .
				' value="' . $product_id . '"' .
				' style="' . $button_options['style'] . '"' .
				' alg_wc_price_offerings_data=\'' . htmlspecialchars( json_encode( $this->get_data_array( $product_id ) ), ENT_QUOTES, 'UTF-8' ) . '\'' .
			'>' .
				$button_options['label'] .
			'</button>' .
		'</p>';

	}

}

endif;

return new Alg_WC_PO_Frontend();
