<?php
/**
 * Price Offers for WooCommerce - Core Class
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Core' ) ) :

class Alg_WC_PO_Core {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (desc) list placeholders in the Actions meta box
	 * @todo    [next] (dev) re-test empty values
	 * @todo    [next] (feature) Offers list: filter, e.g., by product
	 * @todo    [maybe] (dev) recheck multicurrency
	 */
	function __construct() {

		if ( 'yes' === get_option( 'alg_wc_price_offerings_plugin_enabled', 'yes' ) ) {

			// Classes
			require_once( 'classes/class-alg-wc-price-offer.php' );
			require_once( 'classes/class-alg-wc-po-emails.php' );

			// Custom post & statuses
			add_action( 'init', array( $this, 'create_post_type' ), 9 );
			add_action( 'init', array( $this, 'create_post_status' ), 9 );

			// Frontend
			require_once( 'class-alg-wc-po-frontend.php' );

			// Actions
			require_once( 'class-alg-wc-po-actions.php' );

			// Admin
			if ( is_admin() ) {

				// Meta boxes
				require_once( 'class-alg-wc-po-meta-boxes-offer.php' );
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

		}

		// Core loaded
		do_action( 'alg_wc_price_offerings_core_loaded', $this );

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
	 * @todo    [next] (dev) load only when necessary
	 * @todo    [next] (dev) use `admin_enqueue_scripts`
	 * @todo    [maybe] (dev) `alg_wc_po_counter`: different color?
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
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function filter_row_actions( $actions, $post ) {
		if ( 'alg_wc_price_offer' === $post->post_type ) {
			$_actions = array();
			$_actions['edit']  = ( isset( $actions['edit'] )  ? $actions['edit']  : null );
			$_actions['trash'] = ( isset( $actions['trash'] ) ? $actions['trash'] : null );
			return $_actions;
		}
		return $actions;
	}

	/**
	 * modify_columns.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [next] (feature) sortable
	 * @todo    [maybe] (dev) more columns?
	 */
	function modify_columns( $columns ) {
		unset( $columns['date'] );
		$columns['date_created'] = esc_html__( 'Created', 'price-offerings-for-woocommerce' );
		$columns['status']       = esc_html__( 'Status', 'price-offerings-for-woocommerce' );
		$columns['customer']     = esc_html__( 'Customer', 'price-offerings-for-woocommerce' );
		$columns['product']      = esc_html__( 'Product', 'price-offerings-for-woocommerce' );
		$columns['price']        = esc_html__( 'Price', 'price-offerings-for-woocommerce' );
		return $columns;
	}

	/**
	 * render_columns.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    [maybe] (dev) links?
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
					echo $offer->get_product_name();
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
	 * @todo    [next] (dev) re-check `capabilities` and `capability_type`
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
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (dev) move to another class/file?
	 */
	static function get_product_meta_box_columns() {
		return array(
			'title'            => esc_html__( 'Offer', 'price-offerings-for-woocommerce' ),
			'status'           => esc_html__( 'Status', 'price-offerings-for-woocommerce' ),
			'date'             => esc_html__( 'Created', 'price-offerings-for-woocommerce' ),
			'offered_price'    => esc_html__( 'Price', 'price-offerings-for-woocommerce' ),
			'customer_message' => esc_html__( 'Message', 'price-offerings-for-woocommerce' ),
			'customer_name'    => esc_html__( 'Name', 'price-offerings-for-woocommerce' ),
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
	 * @todo    [next] (dev) move to another class/file?
	 * @todo    [next] (dev) `esc_html__`?
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
	 * @todo    [next] (dev) recheck args
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

}

endif;

return new Alg_WC_PO_Core();
