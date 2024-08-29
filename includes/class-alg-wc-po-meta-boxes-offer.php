<?php
/**
 * Price Offers for WooCommerce - Admin Meta Boxes - Custom Post
 *
 * @version 3.1.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_PO_Meta_Boxes_Offer' ) ) :

class Alg_WC_PO_Meta_Boxes_Offer {

	/**
	 * Constructor.
	 *
	 * @version 3.1.0
	 * @since   2.0.0
	 */
	function __construct() {
		if ( ! is_admin() ) {
			return;
		}
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_alg_wc_price_offer', array( $this, 'save_meta_boxes' ) );
		add_action( 'admin_menu', array( $this, 'remove_publish_meta_box' ) );
		add_action( 'admin_head', array( $this, 'css' ) );
		add_action( 'admin_head', array( $this, 'js' ) );
	}

	/**
	 * js.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) use `admin_enqueue_scripts`
	 */
	function js() {
		if ( ( $id = get_the_ID() ) && 'alg_wc_price_offer' === get_post_type( $id ) ) {
			?>
			<script>

				/* "Actions" meta box */
				jQuery( document ).ready( function() {
					jQuery( '#alg-wc-price-offer-action' ).on( 'change' , function() {
						jQuery( '.alg-wc-price-offer-actions-data' ).hide();
						jQuery( '.alg-wc-price-offer-show-for-' + jQuery( this ).val() ).show();
						if ( 'counter' === jQuery( this ).val() ) {
							jQuery( '#alg-wc-price-offer-price-counter' ).prop( 'required', true );
						} else {
							jQuery( '#alg-wc-price-offer-price-counter' ).prop( 'required', false );
						}
					} );
				} );

			</script>
			<?php
		}
	}

	/**
	 * css.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) use `admin_enqueue_scripts`
	 */
	function css() {
		if ( ( $id = get_the_ID() ) && 'alg_wc_price_offer' === get_post_type( $id ) ) {
			?>
			<style>

				/* General */
				#post-body-content {
					display: none;
				}

				/* "Comments" meta box */
				#add-new-comment {
					display: none;
				}

				.comments .row-actions {
					display: none;
				}

				/* "Update" meta box */
				#alg-wc-price-offer-update .inside {
					margin: 0;
					padding: 0;
				}

				/* "Actions" meta box */
				.alg-wc-price-offer-actions-data {
					display: none;
				}

				.alg-wc-price-offer-action-label {
					display: block;
					padding-top: 5px;
					padding-bottom: 5px;
				}

				.alg-wc-price-offer-action-email-content {
					height: 200px;
				}

			</style>
			<?php
		}
	}

	/**
	 * remove_publish_meta_box.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function remove_publish_meta_box() {
		remove_meta_box( 'submitdiv', 'alg_wc_price_offer', 'side' );
	}

	/**
	 * add_meta_boxes.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function add_meta_boxes() {

		add_meta_box(
			'alg-wc-price-offer-update',
			esc_html__( 'Update', 'price-offerings-for-woocommerce' ),
			array( $this, 'meta_box_update' ),
			'alg_wc_price_offer',
			'side'
		);

		add_meta_box(
			'alg-wc-price-offer-data',
			esc_html__( 'Data', 'price-offerings-for-woocommerce' ),
			array( $this, 'meta_box_data' ),
			'alg_wc_price_offer',
			'normal'
		);

		add_meta_box(
			'alg-wc-price-offer-notes',
			esc_html__( 'Notes', 'price-offerings-for-woocommerce' ),
			array( $this, 'meta_box_notes' ),
			'alg_wc_price_offer',
			'side'
		);

		add_meta_box(
			'alg-wc-price-offer-actions',
			esc_html__( 'Actions', 'price-offerings-for-woocommerce' ),
			array( $this, 'meta_box_actions' ),
			'alg_wc_price_offer',
			'normal'
		);

		add_meta_box(
			'alg-wc-price-offer-messages',
			esc_html__( 'Messages', 'price-offerings-for-woocommerce' ),
			array( $this, 'meta_box_messages' ),
			'alg_wc_price_offer',
			'normal'
		);

	}

	/**
	 * meta_box_update.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://developer.wordpress.org/reference/functions/post_submit_meta_box/
	 *
	 * @todo    (dev) better HTML, e.g., date
	 */
	function meta_box_update( $post ) {
		$post_id = (int) $post->ID;
		if ( ! ( $offer = new Alg_WC_Price_Offer( $post_id ) ) ) {
			return;
		}
		?>
		<div class="submitbox" id="submitpost">
			<div id="misc-publishing-actions">
				<div class="misc-pub-section misc-pub-post-status"><?php _e( 'Status:' ); ?>
					<span id="post-status-display"><?php echo $offer->get_status_name(); ?></span>
				</div>
			</div>
			<div class="misc-pub-section curtime misc-pub-curtime">
				<span id="timestamp"><?php printf( __( 'Created: %s', 'price-offerings-for-woocommerce' ), '<b>' . $offer->get_formatted_date() . '</b>' ); ?></span>
			</div>
			<div class="misc-pub-section curtime misc-pub-curtime">
				<span id="timestamp"><?php printf( __( 'Modified: %s', 'price-offerings-for-woocommerce' ), '<b>' . $offer->get_formatted_modified_date() . '</b>' ); ?></span>
			</div>
			<div id="major-publishing-actions">
				<div id="delete-action">
					<?php
					if ( current_user_can( 'delete_post', $post_id ) ) {
						if ( ! EMPTY_TRASH_DAYS ) {
							$delete_text = __( 'Delete permanently' );
						} else {
							$delete_text = __( 'Move to Trash' );
						}
						?>
						<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post_id ); ?>"><?php echo $delete_text; ?></a>
						<?php
					}
					?>
				</div>
				<div id="publishing-action">
					<span class="spinner"></span>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update' ); ?>" />
					<?php submit_button( __( 'Update' ), 'primary large', 'save', false, array( 'id' => 'publish' ) ); ?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * meta_box_data.
	 *
	 * @version 2.5.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) add `get_user_agent()`?
	 */
	function meta_box_data( $post ) {
		if ( ( $offer = new Alg_WC_Price_Offer( $post->ID ) ) ) {
			?>
			<table class="widefat striped">
				<tr><th><?php echo esc_html__( 'Product', 'price-offerings-for-woocommerce' ); ?></th><td><?php
					echo $offer->get_product_name_admin_link( false );
					?></td></tr>
				<tr><th><?php echo esc_html__( 'Price', 'price-offerings-for-woocommerce' ); ?></th><td><?php echo $offer->get_price_summary(); ?></td></tr>
				<?php if ( false !== ( $quantity = $offer->get_quantity() ) && '' !== $quantity ) { ?>
					<tr><th><?php echo esc_html__( 'Quantity', 'price-offerings-for-woocommerce' ); ?></th><td><?php echo $quantity; ?></td></tr>
				<?php } ?>
				<tr><th><?php echo esc_html__( 'Customer', 'price-offerings-for-woocommerce' ); ?></th><td><?php
					$customer_id = $offer->get_customer_id();
					if ( ! empty( $customer_id ) && false !== get_userdata( $customer_id ) ) {
						echo '<a href="' . admin_url( 'user-edit.php?user_id=' . $customer_id ) . '">' . $offer->get_customer_name() . '</a>';
					} else {
						echo $offer->get_customer_name();
					}
					?> (<?php echo esc_html__( 'IP address', 'price-offerings-for-woocommerce' ); ?>: <?php echo $offer->get_user_ip(); ?>)</td></tr>
				<?php if ( false !== ( $customer_phone = $offer->get_customer_phone() ) && '' !== $customer_phone ) { ?>
					<tr><th><?php echo esc_html__( 'Phone', 'price-offerings-for-woocommerce' ); ?></th><td><a href="tel:<?php echo $customer_phone; ?>"><?php echo $customer_phone; ?></a></td></tr>
				<?php } ?>
				<tr><th><?php echo esc_html__( 'Email', 'price-offerings-for-woocommerce' ); ?></th><td><?php echo make_clickable( $offer->get_customer_email() ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Sent to', 'price-offerings-for-woocommerce' ); ?></th><td><?php echo make_clickable( implode( ', ', $offer->get_sent_to() ) ); ?></td></tr>
			</table>
			<?php
		}
	}

	/**
	 * meta_box_notes.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) better styling
	 */
	function meta_box_notes( $post ) {
		if ( ( $offer = new Alg_WC_Price_Offer( $post->ID ) ) ) {
			$notes = $offer->get_notes();
			if ( empty( $notes ) ) {
				?><p><?php echo esc_html__( 'No notes yet.', 'price-offerings-for-woocommerce' ); ?></p><?php
			} else {
				?>
				<table class="widefat striped">
				<?php
				foreach ( array_reverse( $notes ) as $note ) {
					?>
					<tr>
						<td>
							<?php echo $note['content']; ?><br>
							<small style="float:right;"><?php echo date_i18n( Alg_WC_PO_Core::get_date_format(), ( $note['time'] + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) ); ?></small>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
				<?php
			}
		}
	}

	/**
	 * meta_box_actions.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) "Send email" checkboxes
	 * @todo    (dev) better HTML, e.g., shorter names, IDs, classes?
	 * @todo    (dev) Email content: `wp_editor()`?
	 */
	function meta_box_actions( $post ) {
		if ( ( $offer = new Alg_WC_Price_Offer( $post->ID ) ) ) {
			$options = array_replace( Alg_WC_PO_Core::get_default_action_option_values(), get_option( 'alg_wc_po_actions', array() ) );
			?>

			<div>
				<h2 style="font-size: 21px; font-weight: 400; padding: 0; margin-bottom: 10px;"><?php printf( esc_html__( 'Offer %s', 'price-offerings-for-woocommerce' ),  $offer->get_title() ); ?></h2>
				<select id="alg-wc-price-offer-action" name="alg_wc_price_offer_action">
					<option value=""><?php echo esc_html__( 'Choose an action...', 'price-offerings-for-woocommerce' ); ?></option>
					<?php foreach ( Alg_WC_PO_Core::get_actions() as $action_id => $action_title ) { ?>
						<option value="<?php echo esc_attr( $action_id ); ?>"><?php echo esc_html( $action_title ); ?></option>
					<?php } ?>
				</select>
			</div>

			<div class="alg-wc-price-offer-show-for-reject alg-wc-price-offer-actions-data">

				<label for="alg-wc-price-offer-email-subject-reject" class="alg-wc-price-offer-action-label"><?php echo esc_html__( 'Email subject:', 'price-offerings-for-woocommerce' ); ?></label>
				<input id="alg-wc-price-offer-email-subject-reject" name="alg_wc_price_offer_email_subject_reject" class="widefat" type="text" value="<?php echo esc_html( $options['reject_default_email_subject'] ); ?>">

				<label for="alg-wc-price-offer-email-heading-reject" class="alg-wc-price-offer-action-label"><?php echo esc_html__( 'Email heading:', 'price-offerings-for-woocommerce' ); ?></label>
				<input id="alg-wc-price-offer-email-heading-reject" name="alg_wc_price_offer_email_heading_reject" class="widefat" type="text" value="<?php echo esc_html( $options['reject_default_email_heading'] ); ?>">

				<label for="alg-wc-price-offer-email-content-reject" class="alg-wc-price-offer-action-label"><?php echo esc_html__( 'Email content:', 'price-offerings-for-woocommerce' ); ?></label>
				<textarea id="alg-wc-price-offer-email-content-reject" name="alg_wc_price_offer_email_content_reject" class="widefat alg-wc-price-offer-action-email-content"><?php echo wp_kses_post( trim( $options['reject_default_email_content'] ) ); ?></textarea>

			</div>

			<div class="alg-wc-price-offer-show-for-accept alg-wc-price-offer-actions-data">

				<label for="alg-wc-price-offer-email-subject-accept" class="alg-wc-price-offer-action-label"><?php echo esc_html__( 'Email subject:', 'price-offerings-for-woocommerce' ); ?></label>
				<input id="alg-wc-price-offer-email-subject-accept" name="alg_wc_price_offer_email_subject_accept" class="widefat" type="text" value="<?php echo esc_html( $options['accept_default_email_subject'] ); ?>">

				<label for="alg-wc-price-offer-email-heading-accept" class="alg-wc-price-offer-action-label"><?php echo esc_html__( 'Email heading:', 'price-offerings-for-woocommerce' ); ?></label>
				<input id="alg-wc-price-offer-email-heading-accept" name="alg_wc_price_offer_email_heading_accept" class="widefat" type="text" value="<?php echo esc_html( $options['accept_default_email_heading'] ); ?>">

				<label for="alg-wc-price-offer-email-content-accept" class="alg-wc-price-offer-action-label"><?php echo esc_html__( 'Email content:', 'price-offerings-for-woocommerce' ); ?></label>
				<textarea id="alg-wc-price-offer-email-content-accept" name="alg_wc_price_offer_email_content_accept" class="widefat alg-wc-price-offer-action-email-content"><?php echo wp_kses_post( trim( $options['accept_default_email_content'] ) ); ?></textarea>

			</div>

			<div class="alg-wc-price-offer-show-for-counter alg-wc-price-offer-actions-data">

				<label for="alg-wc-price-offer-price-counter" class="alg-wc-price-offer-action-label"><?php printf( esc_html__( 'Counter price (%s):', 'price-offerings-for-woocommerce' ), $offer->get_currency() ); ?></label>
				<input id="alg-wc-price-offer-price-counter" name="alg_wc_price_offer_price_counter" type="number" step="0.0001" value="">

				<label for="alg-wc-price-offer-email-subject-counter" class="alg-wc-price-offer-action-label"><?php echo esc_html__( 'Email subject:', 'price-offerings-for-woocommerce' ); ?></label>
				<input id="alg-wc-price-offer-email-subject-counter" name="alg_wc_price_offer_email_subject_counter" class="widefat" type="text" value="<?php echo esc_html( $options['counter_default_email_subject'] ); ?>">

				<label for="alg-wc-price-offer-email-heading-counter" class="alg-wc-price-offer-action-label"><?php echo esc_html__( 'Email heading:', 'price-offerings-for-woocommerce' ); ?></label>
				<input id="alg-wc-price-offer-email-heading-counter" name="alg_wc_price_offer_email_heading_counter" class="widefat" type="text" value="<?php echo esc_html( $options['counter_default_email_heading'] ); ?>">

				<label for="alg-wc-price-offer-email-content-counter" class="alg-wc-price-offer-action-label"><?php echo esc_html__( 'Email content:', 'price-offerings-for-woocommerce' ); ?></label>
				<textarea id="alg-wc-price-offer-email-content-counter" name="alg_wc_price_offer_email_content_counter" class="widefat alg-wc-price-offer-action-email-content"><?php echo wp_kses_post( trim( $options['counter_default_email_content'] ) ); ?></textarea>

			</div>

			<?php
		}
	}

	/**
	 * meta_box_messages.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) better styling
	 */
	function meta_box_messages( $post ) {
		if ( ( $offer = new Alg_WC_Price_Offer( $post->ID ) ) ) {
			$messages = $offer->get_messages();
			if ( empty( $messages ) ) {
				?><p><?php echo esc_html__( 'No messages yet.', 'price-offerings-for-woocommerce' ); ?></p><?php
			} else {
				?>
				<table class="widefat striped comments">
				<?php
				foreach ( array_reverse( $messages ) as $message ) {
					?>
					<tr>
						<td class="author column-author" style="width:25%;">
							<strong><?php echo get_avatar( $message['author_email'], 32 ); ?><?php echo $message['author']; ?></strong><br>
							<?php echo make_clickable( $message['author_email'] ); ?><br>
							<small><?php echo date_i18n( Alg_WC_PO_Core::get_date_format(), ( $message['time'] + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) ); ?></small>
						</td>
						<td class="comment column-comment column-primary"><?php echo make_clickable( $message['content'] ); ?></td>
					</tr>
					<?php
				}
				?>
				</table>
				<?php
			}
		}
	}

	/**
	 * save_meta_boxes.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) `isset`: `alg_wc_price_offer_email_content_reject`, `alg_wc_price_offer_email_content_accept`, etc.?
	 * @todo    (dev) notices?
	 * @todo    (dev) nonce?
	 */
	function save_meta_boxes( $post_id ) {
		if ( ( $offer = new Alg_WC_Price_Offer( $post_id ) ) ) {
			if ( ! empty( $_POST['alg_wc_price_offer_action'] ) ) {
				remove_action( 'save_post_alg_wc_price_offer', array( $this, 'save_meta_boxes' ) );

				$action = wc_clean( $_POST['alg_wc_price_offer_action'] );

				// Update status
				$offer->update_status( 'alg_wc_po_' . $action );

				// Process action
				$action_args = array();
				switch ( $action ) {

					case 'reject':
						$action_args['do_send_email'] = true;
						$action_args['email_subject'] = wc_clean( $_POST['alg_wc_price_offer_email_subject_reject'] );
						$action_args['email_heading'] = wc_clean( $_POST['alg_wc_price_offer_email_heading_reject'] );
						$action_args['email_content'] = wp_kses_post( trim( $_POST['alg_wc_price_offer_email_content_reject'] ) );
						break;

					case 'accept':
						$action_args['do_send_email'] = true;
						$action_args['email_subject'] = wc_clean( $_POST['alg_wc_price_offer_email_subject_accept'] );
						$action_args['email_heading'] = wc_clean( $_POST['alg_wc_price_offer_email_heading_accept'] );
						$action_args['email_content'] = wp_kses_post( trim( $_POST['alg_wc_price_offer_email_content_accept'] ) );
						break;

					case 'counter':
						$action_args['do_send_email'] = true;
						$action_args['counter_price'] = wc_clean( $_POST['alg_wc_price_offer_price_counter'] );
						$action_args['email_subject'] = wc_clean( $_POST['alg_wc_price_offer_email_subject_counter'] );
						$action_args['email_heading'] = wc_clean( $_POST['alg_wc_price_offer_email_heading_counter'] );
						$action_args['email_content'] = wp_kses_post( trim( $_POST['alg_wc_price_offer_email_content_counter'] ) );
						break;

				}

				$offer->process_action( $action, $action_args );

				add_action( 'save_post_alg_wc_price_offer', array( $this, 'save_meta_boxes' ) );
			}
		}
	}

}

endif;

return new Alg_WC_PO_Meta_Boxes_Offer();
