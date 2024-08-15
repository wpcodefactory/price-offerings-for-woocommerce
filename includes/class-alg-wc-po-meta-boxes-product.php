<?php
/**
 * Price Offers for WooCommerce - Admin Meta Boxes - Product
 *
 * @version 3.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Meta_Boxes_Product' ) ) :

class Alg_WC_PO_Meta_Boxes_Product {

	/**
	 * admin_options.
	 *
	 * @version 2.9.0
	 * @since   2.9.0
	 */
	public $admin_options;

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		// Offers history
		add_action( 'add_meta_boxes', array( $this, 'add_offer_price_history_meta_box' ) );
	}

	/**
	 * get_admin_option.
	 *
	 * @version 3.0.0
	 * @since   1.2.0
	 */
	function get_admin_option( $option, $default = false ) {
		if ( ! isset( $this->admin_options ) ) {
			$this->admin_options = get_option( 'alg_wc_price_offerings_admin', array() );
		}
		return ( $this->admin_options[ $option ] ?? $default );
	}

	/**
	 * add_offer_price_history_meta_box.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function add_offer_price_history_meta_box() {
		if ( 'yes' === $this->get_admin_option( 'meta_box_enabled', 'yes' ) ) {
			add_meta_box(
				'alg-wc-price-offerings',
				$this->get_admin_option( 'meta_box_title', __( 'Price offers', 'price-offerings-for-woocommerce' ) ),
				array( $this, 'create_offer_price_history_meta_box' ),
				'product',
				'normal',
				'high'
			);
		}
	}

	/**
	 * create_offer_price_history_meta_box.
	 *
	 * @version 2.9.4
	 * @since   1.0.0
	 */
	function create_offer_price_history_meta_box() {
		$price_offers = Alg_WC_PO_Core::get_product_offers();
		if ( empty( $price_offers ) ) {
			echo __( 'No price offers yet.', 'price-offerings-for-woocommerce' );
		} else {

			$average_offers   = array();
			$all_columns      = Alg_WC_PO_Core::get_product_meta_box_columns();
			$table_data       = array();
			$header           = array();
			$meta_box_columns = $this->get_admin_option( 'meta_box_cols',
				array( 'title', 'customer_email', 'date', 'status', 'offered_price' ) );
			foreach ( $meta_box_columns as $selected_column ) {
				$header[] = $all_columns[ $selected_column ];
			}
			$table_data[]     = $header;

			foreach ( $price_offers as $offer_id => $price_offer ) {
				$row = array();

				// Columns
				foreach ( $meta_box_columns as $selected_column ) {
					switch ( $selected_column ) {
						case 'title':
							$row[] = '<a href="' . $price_offer->get_link() . '">' . $price_offer->get_title() . '</a>';
							break;
						case 'status':
							$row[] = $price_offer->get_status_label();
							break;
						case 'date':
							$row[] = $price_offer->get_formatted_date();
							break;
						case 'offered_price':
							$row[] = $price_offer->get_price_summary();
							break;
						case 'quantity':
							$row[] = $price_offer->get_quantity();
							break;
						case 'customer_message':
							$row[] = $price_offer->get_customer_message();
							break;
						case 'customer_name':
							$row[] = $price_offer->get_customer_name();
							break;
						case 'customer_phone':
							$row[] = $price_offer->get_customer_phone();
							break;
						case 'customer_email':
							$row[] = $price_offer->get_customer_email();
							break;
						case 'customer_id':
							$row[] = $price_offer->get_customer_id();
							break;
						case 'user_ip':
							$row[] = $price_offer->get_user_ip();
							break;
						case 'user_agent':
							$row[] = $price_offer->get_user_agent();
							break;
						case 'sent_to':
							$row[] = implode( '<br>', $price_offer->get_sent_to() );
							break;
					}
				}

				// Add row
				$table_data[] = $row;

				// Average offer
				if ( ! isset( $average_offers[ $price_offer->get_currency() ] ) ) {
					$average_offers[ $price_offer->get_currency() ] = array( 'total_offers' => 0, 'offers_sum' => 0 );
				}
				if ( is_numeric( $price_offer->get_price() ) ) {
					$average_offers[ $price_offer->get_currency() ]['total_offers']++;
					$average_offers[ $price_offer->get_currency() ]['offers_sum'] += $price_offer->get_price();
				}

			}

			// Offers table
			echo $this->get_table_html( $table_data, array( 'table_class' => 'widefat striped' ) );

			// Average offer
			foreach ( $average_offers as $average_offer_currency_code => $average_offer_data ) {
				if ( ! empty( $average_offer_data['total_offers'] ) ) {
					$average_offer = $average_offer_data['offers_sum'] / $average_offer_data['total_offers'];
					$average_offer = wc_price( $average_offer, array( 'currency' => $average_offer_currency_code ) );
					if ( 'yes' === get_option( 'alg_wc_price_offerings_admin_currency_code', 'no' ) ) {
						$average_offer .= " ({$average_offer_currency_code})";
					}
					$average_offer_template = _n(
						'Average offer: %s (from %s offer)',
						'Average offer: %s (from %s offers)',
						$average_offer_data['total_offers'],
						'price-offerings-for-woocommerce'
					);
					echo '<p>' . sprintf( $average_offer_template,
						$average_offer,
						$average_offer_data['total_offers']
					) . '</p>';
				}
			}

		}
	}

	/**
	 * get_table_html.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) remove this?
	 */
	function get_table_html( $data, $args = array() ) {
		$args = array_merge( array(
			'table_class'        => '',
			'table_style'        => '',
			'row_styles'         => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		), $args );
		$table_class = ( '' == $args['table_class'] ) ? '' : ' class="' . $args['table_class'] . '"';
		$table_style = ( '' == $args['table_style'] ) ? '' : ' style="' . $args['table_style'] . '"';
		$row_styles  = ( '' == $args['row_styles'] )  ? '' : ' style="' . $args['row_styles']  . '"';
		$html = '';
		$html .= '<table' . $table_class . $table_style . '>';
		$html .= '<tbody>';
		foreach( $data as $row_nr => $row ) {
			$html .= '<tr' . $row_styles . '>';
			foreach( $row as $column_nr => $value ) {
				$th_or_td = ( ( 0 === $row_nr && 'horizontal' === $args['table_heading_type'] ) || ( 0 === $column_nr && 'vertical' === $args['table_heading_type'] ) ) ? 'th' : 'td';
				$column_class = ( ! empty( $args['columns_classes'][ $column_nr ] ) ) ? ' class="' . $args['columns_classes'][ $column_nr ] . '"' : '';
				$column_style = ( ! empty( $args['columns_styles'][ $column_nr ] ) )  ? ' style="' . $args['columns_styles'][ $column_nr ]  . '"' : '';
				$html .= '<' . $th_or_td . $column_class . $column_style . '>' . $value . '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

}

endif;

return new Alg_WC_PO_Meta_Boxes_Product();
