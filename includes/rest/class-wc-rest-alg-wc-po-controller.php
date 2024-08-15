<?php
/**
 * Price Offers for WooCommerce - REST API controller
 *
 * Handles requests to the /alg_wc_price_offers endpoint.
 *
 * @version 3.0.0
 * @since   2.9.0
 *
 * @author  Algoritmika Ltd
 *
 * @see     https://github.com/woocommerce/woocommerce/blob/8.8.2/plugins/woocommerce/includes/rest-api/Controllers/Version3/class-wc-rest-crud-controller.php
 * @see     https://github.com/woocommerce/woocommerce/blob/8.8.2/plugins/woocommerce/includes/rest-api/Controllers/Version2/class-wc-rest-coupons-v2-controller.php
 * @see     https://woocommerce.github.io/woocommerce-rest-api-docs/
 */

use Automattic\WooCommerce\Utilities\StringUtil;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_REST_Alg_WC_PO_Controller' ) ) :

class WC_REST_Alg_WC_PO_Controller extends WC_REST_CRUD_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @var     string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Route base.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @var     string
	 */
	protected $rest_base = 'alg_wc_price_offers';

	/**
	 * Post type.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @var     string
	 */
	protected $post_type = 'alg_wc_price_offer';

	/**
	 * Register the routes for offers.
	 *
	 * Implemented:
	 * - "List all offers"     - GET    - /wp-json/wc/v3/alg_wc_price_offers
	 * - "Retrieve an offer"   - GET    - /wp-json/wc/v3/alg_wc_price_offers/<id>
	 * - "Update an offer"     - PUT    - /wp-json/wc/v3/alg_wc_price_offers/<id>
	 *
	 * To do:
	 * - "Create an offer"     - POST   - /wp-json/wc/v3/alg_wc_price_offers
	 * - "Delete an offer"     - DELETE - /wp-json/wc/v3/alg_wc_price_offers/<id>
	 * - "Batch update offers" - POST   - /wp-json/wc/v3/alg_wc_price_offers/batch
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @todo    (dev) `update_item`: more options
	 * @todo    (dev) `create_item`
	 * @todo    (dev) `delete_item`
	 * @todo    (dev) `batch_items`
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace, '/' . $this->rest_base, array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

	}

	/**
	 * Get object.
	 *
	 * @version 2.9.0
	 * @since   2.9.0

	 * @param   int $id Object ID.
	 * @return  WC_Data
	 */
	protected function get_object( $id ) {
		return new WC_REST_Alg_WC_PO( $id );
	}

	/**
	 * Get formatted item data.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @param   WC_Data $object WC_Data instance.
	 * @return  array
	 */
	protected function get_formatted_item_data( $object ) {
		return array(
			'id'        => $object->get_id(),
			'status'    => $object->get_status(),
			'meta_data' => get_post_meta( $object->get_id() ),
		);
	}

	/**
	 * Prepare a single offer output for response.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @param   WC_Data          $object  Object data.
	 * @param   WP_REST_Request  $request Request object.
	 * @return  WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {
		$data     = $this->get_formatted_item_data( $object );
		$context  = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data     = $this->add_additional_fields_to_object( $data, $request );
		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $object, $request ) );

		/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type,
		 * refers to object type being prepared for the response.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Data          $object   Object data.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}_object", $response, $object, $request );
	}

	/**
	 * Prepare objects query.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @param   WP_REST_Request $request Full details about the request.
	 * @return  array
	 */
	protected function prepare_objects_query( $request ) {
		$args = parent::prepare_objects_query( $request );

		// Get only ids.
		$args['fields'] = 'ids';

		// Set post_status.
		$args['post_status'] = $request['status'];

		return $args;
	}

	/**
	 * Only return writable props from schema.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @param   array $schema Schema.
	 * @return  bool
	 */
	protected function filter_writable_props( $schema ) {
		return empty( $schema['readonly'] );
	}

	/**
	 * Prepare a single offer for create or update.
	 *
	 * @version 3.0.0
	 * @since   2.9.0
	 *
	 * @param   WP_REST_Request  $request Request object.
	 * @param   bool             $creating If is creating a new object.
	 * @return  WP_Error|WC_Data
	 */
	protected function prepare_object_for_database( $request, $creating = false ) {
		$id         = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
		$rest_offer = new WC_REST_Alg_WC_PO( $id );
		$schema     = $this->get_item_schema();
		$data_keys  = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );

		// Handle all writable props.
		foreach ( $data_keys as $key ) {
			$value = $request[ $key ];

			if ( ! is_null( $value ) ) {
				switch ( $key ) {

					case 'action':
						$rest_offer->process_action( $value );
						break;

					case 'meta_data':
						if ( is_array( $value ) ) {
							foreach ( $value as $meta ) {
								$rest_offer->update_meta_data( $meta['key'], $meta['value'], ( $meta['id'] ?? '' ) );
							}
						}
						break;

					default:
						if ( is_callable( array( $rest_offer, "set_{$key}" ) ) ) {
							$rest_offer->{"set_{$key}"}( $value );
						}
						break;

				}
			}

		}

		/**
		 * Filters an object before it is inserted via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`,
		 * refers to the object type slug.
		 *
		 * @param WC_Data         $rest_offer Object object.
		 * @param WP_REST_Request $request    Request object.
		 * @param bool            $creating   If is creating a new object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}_object", $rest_offer, $request, $creating );
	}

	/**
	 * Get the Offer's schema, conforming to JSON Schema.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 *
	 * @return  array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id' => array(
					'description' => __( 'Unique identifier for the object.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'status' => array(
					'description' => __( 'The status of the offer.', 'price-offerings-for-woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'action' => array(
					'description' => __( 'Process action.', 'price-offerings-for-woocommerce' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'meta_data' => array(
					'description' => __( 'Meta data.', 'woocommerce' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'    => array(
								'description' => __( 'Meta ID.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
								'readonly'    => true,
							),
							'key'   => array(
								'description' => __( 'Meta key.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'value' => array(
								'description' => __( 'Meta value.', 'woocommerce' ),
								'type'        => 'mixed',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
			),
		);
		return $this->add_additional_fields_schema( $schema );
	}

}

endif;
