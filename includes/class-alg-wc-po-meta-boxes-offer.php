<?php
/**
 * Price Offers for WooCommerce - Admin Meta Boxes - Custom Post
 *
 * @version 3.4.1
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
	 * @version 3.3.1
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
				<div class="misc-pub-section misc-pub-post-status"><?php esc_html_e( 'Status:' ); /* phpcs:ignore WordPress.WP.I18n.MissingArgDomain */ ?>
					<span id="post-status-display"><?php echo esc_html( $offer->get_status_name() ); ?></span>
				</div>
			</div>
			<div class="misc-pub-section curtime misc-pub-curtime">
				<span id="timestamp"><?php printf(
					/* Translators: %s: Date. */
					wp_kses_post( __( 'Created: %s', 'price-offerings-for-woocommerce' ) ),
					'<b>' . esc_html( $offer->get_formatted_date() ) . '</b>'
				); ?></span>
			</div>
			<div class="misc-pub-section curtime misc-pub-curtime">
				<span id="timestamp"><?php printf(
					/* Translators: %s: Date. */
					wp_kses_post( __( 'Modified: %s', 'price-offerings-for-woocommerce' ) ),
					'<b>' . esc_html( $offer->get_formatted_modified_date() ) . '</b>'
				); ?></span>
			</div>
			<div id="major-publishing-actions">
				<div id="delete-action">
					<?php
					if ( current_user_can( 'delete_post', $post_id ) ) {
						if ( ! EMPTY_TRASH_DAYS ) {
							$delete_text = __( 'Delete permanently' ); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
						} else {
							$delete_text = __( 'Move to Trash' ); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
						}
						?>
						<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post_id ) ); ?>"><?php echo esc_html( $delete_text ); ?></a>
						<?php
					}
					?>
				</div>
				<div id="publishing-action">
					<span class="spinner"></span>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update' ); /* phpcs:ignore WordPress.WP.I18n.MissingArgDomain */ ?>" />
					<?php submit_button( __( 'Update' ), 'primary large', 'save', false, array( 'id' => 'publish' ) ); /* phpcs:ignore WordPress.WP.I18n.MissingArgDomain */ ?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * meta_box_data.
	 *
	 * @version 3.4.1
	 * @since   2.0.0
	 *
	 * @todo    (dev) add `get_user_agent()`?
	 */
	function meta_box_data( $post ) {
		if ( ( $offer = new Alg_WC_Price_Offer( $post->ID ) ) ) {

			$allowed_fields = get_option( 'alg_wc_po_dokan_vendor_allowed_fields', array(
				'product',
				'price',
				'quantity',
				'customer',
				'phone',
				'email',
				'send_to',
			) );

			?>
			<table class="widefat striped">
				<?php if ( in_array( 'product', $allowed_fields ) || is_admin() ) : ?>
					<tr>
						<th><?php echo esc_html__( 'Product', 'price-offerings-for-woocommerce' ); ?></th>
						<td><?php
							echo wp_kses_post( $offer->get_product_name_admin_link( false ) );
							?></td>
					</tr>
				<?php endif; ?>
				<?php if ( in_array( 'price', $allowed_fields ) || is_admin() ) : ?>
					<tr>
						<th><?php echo esc_html__( 'Price', 'price-offerings-for-woocommerce' ); ?></th>
						<td><?php echo wp_kses_post( $offer->get_price_summary() ); ?></td>
					</tr>
				<?php endif; ?>
				<?php if ( in_array( 'quantity', $allowed_fields ) || is_admin() ) : ?>
					<?php if (
						false !== ( $quantity = $offer->get_quantity() ) &&
						'' !== $quantity
					) { ?>
						<tr>
							<th><?php echo esc_html__( 'Quantity', 'price-offerings-for-woocommerce' ); ?></th>
							<td><?php echo (int) $quantity; ?></td>
						</tr>
					<?php } ?>
				<?php endif; ?>
				<?php if ( in_array( 'customer', $allowed_fields ) || is_admin() ) : ?>
					<tr>
						<th><?php echo esc_html__( 'Customer', 'price-offerings-for-woocommerce' ); ?></th>
						<td><?php
							$customer_id = $offer->get_customer_id();
							if (
								! empty( $customer_id ) &&
								false !== get_userdata( $customer_id )
							) {
								echo '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . $customer_id ) ) . '">' .
									esc_html( $offer->get_customer_name() ) .
								'</a>';
							} else {
								echo esc_html( $offer->get_customer_name() );
							}
							?> (<?php
							echo esc_html__( 'IP address', 'price-offerings-for-woocommerce' );
							?>: <?php
							echo esc_html( $offer->get_user_ip() );
							?>)<?php
							?></td>
					</tr>
				<?php endif; ?>
				<?php if ( in_array( 'phone', $allowed_fields ) || is_admin() ) : ?>
					<?php
					if (
						false !== ( $customer_phone = $offer->get_customer_phone() ) &&
						'' !== $customer_phone
					) { ?>
						<tr>
							<th><?php echo esc_html__( 'Phone', 'price-offerings-for-woocommerce' ); ?></th>
							<td>
								<a href="tel:<?php echo esc_attr( $customer_phone ); ?>"><?php echo esc_html( $customer_phone ); ?></a>
							</td>
						</tr>
					<?php } ?>
				<?php endif; ?>
				<?php if ( in_array( 'email', $allowed_fields ) || is_admin() ) : ?>
					<tr>
						<th><?php echo esc_html__( 'Email', 'price-offerings-for-woocommerce' ); ?></th>
						<td><?php echo wp_kses_post( make_clickable( $offer->get_customer_email() ) ); ?></td>
					</tr>
				<?php endif; ?>
				<?php if ( in_array( 'send_to', $allowed_fields ) || is_admin() ) : ?>
					<tr>
						<th><?php echo esc_html__( 'Sent to', 'price-offerings-for-woocommerce' ); ?></th>
						<td><?php echo wp_kses_post( make_clickable( implode( ', ', $offer->get_sent_to() ) ) ); ?></td>
					</tr>
				<?php endif; ?>
			</table>
			<?php
		}
	}

	/**
	 * meta_box_notes.
	 *
	 * @version 3.3.2
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
							<?php echo wp_kses_post( $note['content'] ); ?><br>
							<small style="float:right;"><?php
								echo esc_html(
									date_i18n(
										Alg_WC_PO_Core::get_date_format(),
										( $note['time'] + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) )
									)
								);
							?></small>
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
	 * @version 3.3.2
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
				<h2 style="font-size: 21px; font-weight: 400; padding: 0; margin-bottom: 10px;"><?php
					printf(
						/* Translators: %s: Offer title. */
						esc_html__( 'Offer %s', 'price-offerings-for-woocommerce' ),
						esc_html( $offer->get_title() )
					);
				?></h2>
				<select id="alg-wc-price-offer-action" name="alg_wc_price_offer_action">
					<option value=""><?php
						echo esc_html__( 'Choose an action...', 'price-offerings-for-woocommerce' );
					?></option>
					<?php foreach ( Alg_WC_PO_Core::get_actions() as $action_id => $action_title ) { ?>
						<option value="<?php echo esc_attr( $action_id ); ?>"><?php
							echo esc_html( $action_title );
						?></option>
					<?php } ?>
				</select>
			</div>

			<div class="alg-wc-price-offer-show-for-reject alg-wc-price-offer-actions-data">

				<label for="alg-wc-price-offer-email-subject-reject" class="alg-wc-price-offer-action-label"><?php
					echo esc_html__( 'Email subject:', 'price-offerings-for-woocommerce' );
				?></label>
				<input id="alg-wc-price-offer-email-subject-reject" name="alg_wc_price_offer_email_subject_reject" class="widefat" type="text" value="<?php
					echo esc_html( $options['reject_default_email_subject'] );
				?>">

				<label for="alg-wc-price-offer-email-heading-reject" class="alg-wc-price-offer-action-label"><?php
					echo esc_html__( 'Email heading:', 'price-offerings-for-woocommerce' );
				?></label>
				<input id="alg-wc-price-offer-email-heading-reject" name="alg_wc_price_offer_email_heading_reject" class="widefat" type="text" value="<?php
					echo esc_html( $options['reject_default_email_heading'] );
				?>">

				<label for="alg-wc-price-offer-email-content-reject" class="alg-wc-price-offer-action-label"><?php
					echo esc_html__( 'Email content:', 'price-offerings-for-woocommerce' );
				?></label>
				<textarea id="alg-wc-price-offer-email-content-reject" name="alg_wc_price_offer_email_content_reject" class="widefat alg-wc-price-offer-action-email-content"><?php
					echo wp_kses_post( trim( $options['reject_default_email_content'] ) );
				?></textarea>

			</div>

			<div class="alg-wc-price-offer-show-for-accept alg-wc-price-offer-actions-data">

				<label for="alg-wc-price-offer-email-subject-accept" class="alg-wc-price-offer-action-label"><?php
					echo esc_html__( 'Email subject:', 'price-offerings-for-woocommerce' );
				?></label>
				<input id="alg-wc-price-offer-email-subject-accept" name="alg_wc_price_offer_email_subject_accept" class="widefat" type="text" value="<?php
					echo esc_html( $options['accept_default_email_subject'] );
				?>">

				<label for="alg-wc-price-offer-email-heading-accept" class="alg-wc-price-offer-action-label"><?php echo
					esc_html__( 'Email heading:', 'price-offerings-for-woocommerce' );
				?></label>
				<input id="alg-wc-price-offer-email-heading-accept" name="alg_wc_price_offer_email_heading_accept" class="widefat" type="text" value="<?php
					echo esc_html( $options['accept_default_email_heading'] );
				?>">

				<label for="alg-wc-price-offer-email-content-accept" class="alg-wc-price-offer-action-label"><?php
					echo esc_html__( 'Email content:', 'price-offerings-for-woocommerce' );
				?></label>
				<textarea id="alg-wc-price-offer-email-content-accept" name="alg_wc_price_offer_email_content_accept" class="widefat alg-wc-price-offer-action-email-content"><?php
					echo wp_kses_post( trim( $options['accept_default_email_content'] ) );
				?></textarea>

			</div>

			<div class="alg-wc-price-offer-show-for-counter alg-wc-price-offer-actions-data">

				<label for="alg-wc-price-offer-price-counter" class="alg-wc-price-offer-action-label"><?php
					printf(
						/* Translators: %s: Offer currency. */
						esc_html__( 'Counter price (%s):', 'price-offerings-for-woocommerce' ),
						esc_html( $offer->get_currency() )
					);
				?></label>
				<input id="alg-wc-price-offer-price-counter" name="alg_wc_price_offer_price_counter" type="number" step="0.0001" value="">

				<label for="alg-wc-price-offer-email-subject-counter" class="alg-wc-price-offer-action-label"><?php
					echo esc_html__( 'Email subject:', 'price-offerings-for-woocommerce' );
				?></label>
				<input id="alg-wc-price-offer-email-subject-counter" name="alg_wc_price_offer_email_subject_counter" class="widefat" type="text" value="<?php
					echo esc_html( $options['counter_default_email_subject'] );
				?>">

				<label for="alg-wc-price-offer-email-heading-counter" class="alg-wc-price-offer-action-label"><?php
					echo esc_html__( 'Email heading:', 'price-offerings-for-woocommerce' );
				?></label>
				<input id="alg-wc-price-offer-email-heading-counter" name="alg_wc_price_offer_email_heading_counter" class="widefat" type="text" value="<?php
					echo esc_html( $options['counter_default_email_heading'] );
				?>">

				<label for="alg-wc-price-offer-email-content-counter" class="alg-wc-price-offer-action-label"><?php
					echo esc_html__( 'Email content:', 'price-offerings-for-woocommerce' );
				?></label>
				<textarea id="alg-wc-price-offer-email-content-counter" name="alg_wc_price_offer_email_content_counter" class="widefat alg-wc-price-offer-action-email-content"><?php
					echo wp_kses_post( trim( $options['counter_default_email_content'] ) );
				?></textarea>

			</div>

			<?php
		}
	}

	/**
	 * meta_box_messages.
	 *
	 * @version 3.4.1
	 * @since   2.0.0
	 *
	 * @todo    (dev) better styling
	 */
	function meta_box_messages( $post ) {
		if ( ( $offer = new Alg_WC_Price_Offer( $post->ID ) ) ) {
			$messages = $offer->get_messages();
			if ( empty( $messages ) ) {

				?><p><?php
					echo esc_html__( 'No messages yet.', 'price-offerings-for-woocommerce' );
				?></p><?php

			} else {

				?><table class="widefat striped comments"><?php
				foreach ( array_reverse( $messages ) as $message ) {
					?>
					<tr>
						<td class="author column-author" style="width:25%;">
							<strong><?php
								echo get_avatar( $message['author_email'], 32 ) . esc_html( $message['author'] );
							?></strong><br>
							<?php
							$allowed_fields = get_option(
								'alg_wc_po_dokan_vendor_allowed_fields',
								array(
									'product',
									'price',
									'quantity',
									'customer',
									'phone',
									'email',
									'send_to',
								)
							);

							if ( in_array( 'email', $allowed_fields ) || is_admin() ) :
								echo wp_kses_post( make_clickable( sanitize_email( $message['author_email'] ) ) ) . '<br>';
							endif;
							?>
							<small><?php
								echo esc_html(
									date_i18n(
										Alg_WC_PO_Core::get_date_format(),
										( $message['time'] + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) )
									)
								);
							?></small>
						</td>
						<td class="comment column-comment column-primary"><?php
							echo wp_kses_post( make_clickable( $message['content'] ) );
						?></td>
					</tr>
					<?php
				}
				?></table><?php

			}
		}
	}

	/**
	 * save_meta_boxes.
	 *
	 * @version 3.3.3
	 * @since   2.0.0
	 *
	 * @todo    (dev) notices?
	 * @todo    (dev) nonce?
	 */
	function save_meta_boxes( $post_id ) {
		if ( ( $offer = new Alg_WC_Price_Offer( $post_id ) ) ) {
			if ( ! empty( $_POST['alg_wc_price_offer_action'] ) ) {
				remove_action( 'save_post_alg_wc_price_offer', array( $this, 'save_meta_boxes' ) );

				// Get action
				$action = sanitize_text_field( wp_unslash( $_POST['alg_wc_price_offer_action'] ) );

				// Update status
				$offer->update_status( 'alg_wc_po_' . $action );

				// Action args
				$action_args = array();
				if ( in_array( $action, array( 'reject', 'accept', 'counter' ) ) ) {
					$action_args = array(
						'do_send_email' => true,
						'email_subject' => (
							isset( $_POST[ "alg_wc_price_offer_email_subject_{$action}" ] ) ?
							sanitize_text_field( wp_unslash( $_POST[ "alg_wc_price_offer_email_subject_{$action}" ] ) ) :
							''
						),
						'email_heading' => (
							isset( $_POST[ "alg_wc_price_offer_email_heading_{$action}" ] ) ?
							sanitize_text_field( wp_unslash( $_POST[ "alg_wc_price_offer_email_heading_{$action}" ] ) ) :
							''
						),
						'email_content' => (
							isset( $_POST[ "alg_wc_price_offer_email_content_{$action}" ] ) ?
							trim( wp_kses_post( wp_unslash( $_POST[ "alg_wc_price_offer_email_content_{$action}" ] ) ) ) :
							''
						),
					);
					if ( 'counter' === $action ) {
						$action_args['counter_price'] = (
							isset( $_POST['alg_wc_price_offer_price_counter'] ) ?
							sanitize_text_field( wp_unslash( $_POST['alg_wc_price_offer_price_counter'] ) ) :
							''
						);
					}
				}

				// Process action
				$offer->process_action( $action, $action_args );

				add_action( 'save_post_alg_wc_price_offer', array( $this, 'save_meta_boxes' ) );
			}
		}
	}

}

endif;

return new Alg_WC_PO_Meta_Boxes_Offer();
