<?php
/**
 * Price Offers for WooCommerce - Core Class
 *
 * @version 3.1.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Core' ) ) :

class Alg_WC_PO_Core {

	/**
	 * frontend.
	 *
	 * @version 2.2.3
	 * @since   2.2.3
	 */
	public $frontend = false;

	/**
	 * actions.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 */
	public $actions;

	/**
	 * offer_meta_boxes.
	 *
	 * @version 3.1.0
	 * @since   3.1.0
	 */
	public $offer_meta_boxes;

	/**
	 * Constructor.
	 *
	 * @version 3.1.0
	 * @since   1.0.0
	 *
	 * @todo    (desc) list placeholders in the Actions meta box
	 * @todo    (dev) re-test empty values
	 * @todo    (feature) Offers list: filter, e.g., by product
	 * @todo    (dev) "New Offer" (manually by admin)?
	 * @todo    (dev) recheck multicurrency
	 */
	function __construct() {

		// Classes
		require_once( 'classes/class-alg-wc-price-offer.php' );
		require_once( 'classes/class-alg-wc-po-emails.php' );

		// Send emails in background
		add_action( 'alg_wc_price_offers_send_email', array( 'Alg_WC_PO_Emails', 'send_email' ), 10, 7 );

		// Custom post & statuses
		add_action( 'init', array( $this, 'create_post_type' ), 9 );
		add_action( 'init', array( $this, 'create_post_status' ), 9 );

		// Frontend
		$this->frontend = require_once( 'class-alg-wc-po-frontend.php' );

		// Actions
		$this->actions = require_once( 'class-alg-wc-po-actions.php' );

		// Payment gateways
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'payment_gateways' ) );

		// reCAPTCHA
		if ( $this->is_recaptcha_enabled() ) {
			add_action( 'wp_ajax_'        . 'alg_wc_price_offerings_recaptcha', array( $this, 'recaptcha' ) );
			add_action( 'wp_ajax_nopriv_' . 'alg_wc_price_offerings_recaptcha', array( $this, 'recaptcha' ) );
		}

		// REST API
		$this->maybe_init_rest_api();

		// Offer meta boxes
		$this->offer_meta_boxes = require_once( 'class-alg-wc-po-meta-boxes-offer.php' );

		// Admin
		if ( is_admin() ) {

			// Product meta boxes
			require_once( 'class-alg-wc-po-meta-boxes-product.php' );

			// CSS
			add_action( 'admin_head', array( $this, 'css' ) );

			// Custom post columns
			add_filter( 'manage_edit-alg_wc_price_offer_columns', array( $this, 'modify_columns' ) );
			add_action( 'manage_alg_wc_price_offer_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );

			// Custom post actions
			add_filter( 'bulk_actions-edit-alg_wc_price_offer', array( $this, 'filter_bulk_actions' ) );
			add_filter( 'post_row_actions', array( $this, 'filter_row_actions' ), 10, 2 );

			// Add screens to WooCommerce screen IDs
			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );

			// Disable "Add new"
			add_action( 'admin_menu', array( $this, 'disable_new_posts_submenu' ) );
			add_action( 'admin_head', array( $this, 'disable_new_posts_buttons' ) );

			// Admin loaded
			do_action( 'alg_wc_price_offerings_admin_loaded', $this );

		}

		// Core loaded
		do_action( 'alg_wc_price_offerings_core_loaded', $this );

	}

	/**
	 * is_recaptcha_enabled.
	 *
	 * @version 2.9.9
	 * @since   2.9.9
	 */
	function is_recaptcha_enabled() {
		$form_options = get_option( 'alg_wc_price_offerings_form', array() );
		$form_options = array_merge( array(
			'enabled_fields' => array( 'customer_name', 'customer_message', 'customer_copy' ),
		), $form_options );
		return ( in_array( 'recaptcha', $form_options['enabled_fields'] ) );
	}

	/**
	 * reCAPTCHA.
	 *
	 * @version 2.9.9
	 * @since   2.9.9
	 *
	 * @todo    (dev) fallback for the `file_get_contents()`
	 */
	function recaptcha() {
		$res = 0;
		if ( isset( $_POST['recaptcha_response'] ) ) {

			$form_options = get_option( 'alg_wc_price_offerings_form', array() );
			$form_options = array_merge( array( 'recaptcha_secret_key' => '' ), $form_options );
			$secret       = $form_options['recaptcha_secret_key'];

			$response = wc_clean( $_POST['recaptcha_response'] );

			$url = 'https://www.google.com/recaptcha/api/siteverify';
			$url = add_query_arg( array( 'secret' => $secret, 'response' => $response ), $url );

			$verify = file_get_contents( $url );
			$verify = json_decode( $verify );

			if ( $verify->success ) {
				$res = 1;
			}

		}
		echo $res;
		die();
	}

	/**
	 * payment_gateways.
	 *
	 * @version 2.9.8
	 * @since   2.9.8
	 */
	function payment_gateways( $gateways ) {
		$selected_gateways = get_option( 'alg_wc_po_payment_gateways', array() );
		if (
			! empty( $selected_gateways ) &&
			isset( WC()->cart )
		) {

			// Check if there are any "price offer" items in the cart
			$is_price_offer_in_cart = false;
			foreach ( WC()->cart->get_cart() as $item_key => $item ) {
				if ( isset( $item['alg_wc_price_offer_id'] ) ) {
					$is_price_offer_in_cart = true;
					break;
				}
			}

			// Disable payment gateways
			if ( $is_price_offer_in_cart ) {
				$action   = get_option( 'alg_wc_po_payment_gateways_action', 'exclude' );
				$gateways = ( 'exclude' === $action ?
					array_diff_key(      $gateways, array_flip( $selected_gateways ) ) :
					array_intersect_key( $gateways, array_flip( $selected_gateways ) )
				);
			}

		}
		return $gateways;
	}

	/**
	 * maybe_init_rest_api.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/8.8.2/plugins/woocommerce/includes/rest-api/Server.php#L59
	 */
	function maybe_init_rest_api() {
		if ( 'yes' === get_option( 'alg_wc_po_rest_api_enabled', 'no' ) ) {
			require_once( 'rest/class-wc-rest-alg-wc-po-controller.php' );
			require_once( 'rest/class-wc-rest-alg-wc-po.php' );
			add_filter( 'woocommerce_rest_api_get_rest_namespaces', array( $this, 'add_rest_controller' ) );
		}
	}

	/**
	 * add_rest_controller.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	function add_rest_controller( $controllers ) {
		$controllers['wc/v3']['alg_wc_price_offers'] = 'WC_REST_Alg_WC_PO_Controller';
		return $controllers;
	}

	/**
	 * get_default_email_from_name.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 */
	function get_default_email_from_name() {
		$wc_option = WC_Emails::instance()->get_from_name();
		return ( '' === $wc_option ? get_bloginfo( 'name', 'display' ) : $wc_option );
	}

	/**
	 * get_default_email_from_address.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 */
	function get_default_email_from_address() {
		$wc_option = WC_Emails::instance()->get_from_address();
		return ( '' === $wc_option ? get_bloginfo( 'admin_email' ) : $wc_option );
	}

	/**
	 * get_email_from_name.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 */
	function get_email_from_name() {
		$option = get_option( 'alg_wc_po_actions_email_from_name', '' );
		return ( '' === $option ? $this->get_default_email_from_name() : $option );
	}

	/**
	 * get_email_from_address.
	 *
	 * @version 2.7.0
	 * @since   2.7.0
	 */
	function get_email_from_address() {
		$option = get_option( 'alg_wc_po_actions_email_from_address', '' );
		return ( '' === $option ? $this->get_default_email_from_address() : $option );
	}

	/**
	 * disable_new_posts_submenu.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function disable_new_posts_submenu() {
		global $submenu;
		unset( $submenu['edit.php?post_type=alg_wc_price_offer'][10] );
	}

	/**
	 * disable_new_posts_buttons.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function disable_new_posts_buttons() {
		if (
			( isset( $_GET['post_type'] ) && 'alg_wc_price_offer' === $_GET['post_type'] ) ||
			( ( $id = get_the_ID() ) && 'alg_wc_price_offer' === get_post_type( $id ) )
		) {
			echo '<style>a.page-title-action { display:none; }</style>';
		}
	}

	/**
	 * css.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) load only when necessary
	 * @todo    (dev) use `admin_enqueue_scripts`
	 * @todo    (dev) `alg_wc_po_counter`: different color?
	 */
	function css() {
		?><style>
			.order-status.status-alg_wc_po_open {
				background: #f8dda7;
				color: #94660c;
			}
			.order-status.status-alg_wc_po_complete {
				background: #c8d7e1;
				color: #2e4453;
			}
			.order-status.status-alg_wc_po_accept,
			.order-status.status-alg_wc_po_counter {
				background: #c6e1c6;
				color: #5b841b;
			}
			.post-type-alg_wc_price_offer .wp-list-table.posts td.column-status {
				padding-top: 15px;
			}
		</style><?php
	}

	/**
	 * filter_bulk_actions.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function filter_bulk_actions( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}

	/**
	 * filter_row_actions.
	 *
	 * @version 3.0.0
	 * @since   2.0.0
	 */
	function filter_row_actions( $actions, $post ) {
		if ( 'alg_wc_price_offer' === $post->post_type ) {
			$_actions = array();
			$_actions['edit']  = ( $actions['edit']  ?? null );
			$_actions['trash'] = ( $actions['trash'] ?? null );
			return $_actions;
		}
		return $actions;
	}

	/**
	 * modify_columns.
	 *
	 * @version 2.1.0
	 * @since   2.0.0
	 *
	 * @todo    (feature) sortable
	 * @todo    (dev) more columns?
	 */
	function modify_columns( $columns ) {
		unset( $columns['date'] );
		$columns['date_created'] = esc_html__( 'Created', 'price-offerings-for-woocommerce' );
		$columns['status']       = esc_html__( 'Status', 'price-offerings-for-woocommerce' );
		$columns['customer']     = esc_html__( 'Customer', 'price-offerings-for-woocommerce' );
		$columns['product']      = esc_html__( 'Product', 'price-offerings-for-woocommerce' );
		$columns['product_sku']  = esc_html__( 'SKU', 'price-offerings-for-woocommerce' );
		$columns['price']        = esc_html__( 'Price', 'price-offerings-for-woocommerce' );
		return $columns;
	}

	/**
	 * render_columns.
	 *
	 * @version 2.1.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) links?
	 */
	function render_columns( $column, $postid ) {
		if ( ( $offer = new Alg_WC_Price_Offer( $postid ) ) ) {
			switch ( $column ) {

				case 'date_created':
					echo $offer->get_human_time_diff_date_label();
					break;

				case 'customer':
					echo $offer->get_customer_email();
					break;

				case 'product':
					echo $offer->get_product_name_admin_link();
					break;

				case 'product_sku':
					echo $offer->get_product_sku();
					break;

				case 'price':
					echo $offer->get_price_summary();
					break;

				case 'status':
					echo $offer->get_status_label();
					break;

			}
		}
	}

	/**
	 * create_post_type.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/register_post_type/
	 *
	 * @todo    (dev) re-check `capabilities` and `capability_type`
	 */
	function create_post_type() {
		register_post_type( 'alg_wc_price_offer',
			array(
				'labels'             => array(
					'name'                    => _x( 'Offers', 'post type general name', 'price-offerings-for-woocommerce' ),
					'singular_name'           => _x( 'Offer', 'post type singular name', 'price-offerings-for-woocommerce' ),
					'menu_name'               => _x( 'Offers', 'admin menu', 'price-offerings-for-woocommerce' ),
					'name_admin_bar'          => _x( 'Offer', 'add new on admin bar', 'price-offerings-for-woocommerce' ),
					'add_new'                 => _x( 'Add New', 'offer', 'price-offerings-for-woocommerce' ),
					'add_new_item'            => __( 'Add New Offer', 'price-offerings-for-woocommerce' ),
					'new_item'                => __( 'New Offer', 'price-offerings-for-woocommerce' ),
					'edit_item'               => __( 'Edit Offer', 'price-offerings-for-woocommerce' ),
					'view_item'               => __( 'View Offer', 'price-offerings-for-woocommerce' ),
					'all_items'               => __( 'All Offers', 'price-offerings-for-woocommerce' ),
					'search_items'            => __( 'Search Offers', 'price-offerings-for-woocommerce' ),
					'parent_item_colon'       => __( 'Parent Offers:', 'price-offerings-for-woocommerce' ),
					'not_found'               => __( 'No offers found.', 'price-offerings-for-woocommerce' ),
					'not_found_in_trash'      => __( 'No offers found in Trash.', 'price-offerings-for-woocommerce' ),
				),
				'description'        => __( 'WooCommerce price offer', 'price-offerings-for-woocommerce' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => false,
				'map_meta_cap'       => true,
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => false,
				'menu_icon'          => 'dashicons-money',
			)
		);
	}

	/**
	 * get_product_meta_box_columns.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) move to another class/file?
	 */
	static function get_product_meta_box_columns() {
		return array(
			'title'            => esc_html__( 'Offer', 'price-offerings-for-woocommerce' ),
			'status'           => esc_html__( 'Status', 'price-offerings-for-woocommerce' ),
			'date'             => esc_html__( 'Created', 'price-offerings-for-woocommerce' ),
			'offered_price'    => esc_html__( 'Price', 'price-offerings-for-woocommerce' ),
			'quantity'         => esc_html__( 'Quantity', 'price-offerings-for-woocommerce' ),
			'customer_message' => esc_html__( 'Message', 'price-offerings-for-woocommerce' ),
			'customer_name'    => esc_html__( 'Name', 'price-offerings-for-woocommerce' ),
			'customer_phone'   => esc_html__( 'Phone', 'price-offerings-for-woocommerce' ),
			'customer_email'   => esc_html__( 'Email', 'price-offerings-for-woocommerce' ),
			'customer_id'      => esc_html__( 'Customer ID', 'price-offerings-for-woocommerce' ),
			'user_ip'          => esc_html__( 'User IP', 'price-offerings-for-woocommerce' ),
			'user_agent'       => esc_html__( 'User Agent', 'price-offerings-for-woocommerce' ),
			'sent_to'          => esc_html__( 'Sent to', 'price-offerings-for-woocommerce' ),
		);
	}

	/**
	 * get_default_action_option_values.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) move to another class/file?
	 * @todo    (dev) `esc_html__`?
	 */
	static function get_default_action_option_values() {

		$signature = __( 'Regards,', 'price-offerings-for-woocommerce' ) . PHP_EOL .
			sprintf( __( 'All at %s', 'price-offerings-for-woocommerce' ), '{site_title}' ) . PHP_EOL .
			'{site_url}';

		return array(

			'reject_default_email_subject' => '[{site_title}]: ' . __( 'Offer rejected', 'price-offerings-for-woocommerce' ),
			'reject_default_email_heading' => __( 'Offer rejected', 'price-offerings-for-woocommerce' ),
			'reject_default_email_content' => __( 'Hi, %customer_name%,', 'price-offerings-for-woocommerce' ) . PHP_EOL . PHP_EOL .
				__( 'We are sorry to inform you, but your offer to buy "%product_title%" for %offered_price% has been rejected.', 'price-offerings-for-woocommerce' ) . PHP_EOL . PHP_EOL .
				$signature,

			'accept_default_email_subject' => '[{site_title}]: ' . __( 'Offer accepted', 'price-offerings-for-woocommerce' ),
			'accept_default_email_heading' => __( 'Offer accepted', 'price-offerings-for-woocommerce' ),
			'accept_default_email_content' => __( 'Hi, %customer_name%,', 'price-offerings-for-woocommerce' ) . PHP_EOL . PHP_EOL .
				__( 'We are happy to inform you that your offer has been accepted. You can buy "%product_title%" for %offered_price% by clicking <a href="%add_to_cart_url%">this link</a>.', 'price-offerings-for-woocommerce' ) . PHP_EOL . PHP_EOL .
				$signature,

			'counter_default_email_subject' => '[{site_title}]: ' . __( 'Counter offer', 'price-offerings-for-woocommerce' ),
			'counter_default_email_heading' => __( 'Counter offer', 'price-offerings-for-woocommerce' ),
			'counter_default_email_content' => __( 'Hi, %customer_name%,', 'price-offerings-for-woocommerce' ) . PHP_EOL . PHP_EOL .
				__( 'Unfortunately, we can\'t go that low, but we can offer it to you for %counter_price%. You can buy "%product_title%" for %counter_price% by clicking <a href="%add_to_cart_url%">this link</a>.', 'price-offerings-for-woocommerce' ) . PHP_EOL . PHP_EOL .
				$signature,

		);
	}

	/**
	 * get_actions.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	static function get_actions() {
		$actions = array(
			'open'     => esc_html__( 'Open', 'price-offerings-for-woocommerce' ),
			'reject'   => esc_html__( 'Reject', 'price-offerings-for-woocommerce' ),
			'accept'   => esc_html__( 'Accept', 'price-offerings-for-woocommerce' ),
			'counter'  => esc_html__( 'Counter', 'price-offerings-for-woocommerce' ),
			'cancel'   => esc_html__( 'Cancel', 'price-offerings-for-woocommerce' ),
			'complete' => esc_html__( 'Complete', 'price-offerings-for-woocommerce' ),
		);
		return $actions;
	}

	/**
	 * get_statuses.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_statuses() {
		return array(
			'alg_wc_po_open'     => esc_html__( 'Open', 'price-offerings-for-woocommerce' ),
			'alg_wc_po_reject'   => esc_html__( 'Rejected', 'price-offerings-for-woocommerce' ),
			'alg_wc_po_accept'   => esc_html__( 'Accepted', 'price-offerings-for-woocommerce' ),
			'alg_wc_po_counter'  => esc_html__( 'Counter', 'price-offerings-for-woocommerce' ),
			'alg_wc_po_cancel'   => esc_html__( 'Cancelled', 'price-offerings-for-woocommerce' ),
			'alg_wc_po_complete' => esc_html__( 'Completed', 'price-offerings-for-woocommerce' ),
		);
	}

	/**
	 * create_post_status.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/register_post_status/
	 *
	 * @todo    (dev) recheck args
	 */
	function create_post_status() {
		foreach ( $this->get_statuses() as $id => $title ) {
			register_post_status( $id, array(
				'label'                     => $title,
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( $title . ' <span class="count">(%s)</span>', $title . ' <span class="count">(%s)</span>' ),
			) );
		}
	}

	/**
	 * get_date_format().
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	static function get_date_format() {
		return get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	}

	/**
	 * add_screen_ids.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function add_screen_ids( $screen_ids ) {
		$screen_ids[] = 'alg_wc_price_offer';
		$screen_ids[] = 'edit-alg_wc_price_offer';
		return $screen_ids;
	}

	/**
	 * create_price_offer.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/wp_insert_post/
	 */
	static function create_price_offer( $status = false, $time = false, $meta = false ) {

		// Insert post
		$id = wp_insert_post( array(
			'post_type'   => 'alg_wc_price_offer',
			'post_status' => ( $status ? $status : 'alg_wc_po_open' ),
			'post_date'   => ( $time ? date( 'Y-m-d H:i:s', $time ) : null ),
		) );

		if ( $id ) {

			// Post title
			wp_update_post( array( 'ID' => $id, 'post_title' => '#' . $id ) );

			// Meta
			if ( $meta ) {
				foreach ( $meta as $key => $value ) {
					update_post_meta( $id, $key, $value );
				}
			}

			// Message
			if ( isset( $meta['customer_message'] ) && '' !== $meta['customer_message'] ) {
				if ( ( $offer = new Alg_WC_Price_Offer( $id ) ) ) {
					$offer->add_message( array(
						'user_id'      => $offer->get_customer_id(),
						'author'       => $offer->get_customer_name(),
						'author_email' => $offer->get_customer_email(),
						'content'      => $offer->get_customer_message(),
						'time'         => $offer->get_timestamp(),
					) );
				}
			}

			// Success
			return $id;
		}

		// Error
		return false;
	}

	/**
	 * get_product_offers.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	static function get_product_offers( $product_id = false ) {
		$offers = array();

		$query_args = array(
			'post_type'      => 'alg_wc_price_offer',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'   => 'product_id',
					'value' => ( $product_id ? $product_id : get_the_ID() ),
				),
			),
		);

		$query = new WP_Query( $query_args );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $offer_id ) {
				if ( ( $offer = new Alg_WC_Price_Offer( $offer_id ) ) ) {
					$offers[ $offer_id ] = $offer;
				}
			}
		}

		return $offers;
	}

	/**
	 * is_duplicate.
	 *
	 * @version 2.9.3
	 * @since   2.9.3
	 *
	 * @todo    (dev) `offer_timestamp`, e.g., check 30-day old statuses only
	 */
	static function is_duplicate( $price_offer ) {

		// Statuses
		$post_status = get_option( 'alg_wc_po_prevent_duplicate_offers_post_status', array(
			'alg_wc_po_open',
		) );
		if ( empty( $post_status ) ) {
			$post_status = array( 'alg_wc_po_open' );
		}

		// Meta query
		$keys = get_option( 'alg_wc_po_prevent_duplicate_offers_keys', array(
			'product_id',
			'customer_id',
			'user_ip',
			'offered_price',
			'currency_code',
			'quantity',
		) );
		$meta_query = array();
		foreach ( $keys as $key ) {
			if ( isset( $price_offer[ $key ] ) ) {
				$meta_query[] = array(
					'key'   => $key,
					'value' => $price_offer[ $key ],
				);
			}
		}
		if ( count( $meta_query ) > 1 ) {
			$meta_query['relation'] = 'AND';
		}

		// Query args
		$query_args = array(
			'post_type'      => 'alg_wc_price_offer',
			'post_status'    => $post_status,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		);
		if ( ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query;
		}
		$query_args = apply_filters( 'alg_wc_po_duplicate_query_args', $query_args );

		// Query
		$query = new WP_Query( $query_args );

		// Result
		return $query->have_posts();

	}

}

endif;

return new Alg_WC_PO_Core();
