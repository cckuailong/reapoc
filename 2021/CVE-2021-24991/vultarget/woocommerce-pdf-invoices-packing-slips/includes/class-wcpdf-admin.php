<?php
namespace WPO\WC\PDF_Invoices;

use WPO\WC\PDF_Invoices\Compatibility\WC_Core as WCX;
use WPO\WC\PDF_Invoices\Compatibility\Order as WCX_Order;
use WPO\WC\PDF_Invoices\Compatibility\Product as WCX_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Admin' ) ) :

class Admin {
	function __construct()	{
		add_action( 'woocommerce_admin_order_actions_end', array( $this, 'add_listing_actions' ) );
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_invoice_number_column' ), 999 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'invoice_number_column_data' ), 2 );
		add_action( 'add_meta_boxes_shop_order', array( $this, 'add_meta_boxes' ) );
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '>=' ) ) {
			add_action( 'bulk_actions-edit-shop_order', array( $this, 'bulk_actions' ), 20 );
		} else {
			add_action( 'admin_footer', array( $this, 'bulk_actions_js' ) );
		}
		add_filter( 'woocommerce_shop_order_search_fields', array( $this, 'search_fields' ) );

		add_action( 'woocommerce_process_shop_order_meta', array( $this,'save_invoice_number_date' ), 35, 2 );

		// manually send emails
		// WooCommerce core processes order actions at priority 50
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'send_emails' ), 60, 2 );

		add_action( 'admin_notices', array( $this, 'review_plugin_notice' ) );
		add_action( 'admin_notices', array( $this, 'install_wizard_notice' ) );

		add_action( 'init', array( $this, 'setup_wizard') );
		// add_action( 'wpo_wcpdf_after_pdf', array( $this,'update_pdf_counter' ), 10, 2 );

		add_action( 'admin_bar_menu', array( $this, 'debug_enabled_warning' ), 999 );


		add_filter( 'manage_edit-shop_order_sortable_columns', array( $this, 'invoice_number_column_sortable' ) );
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0', '>=' ) ) {
			add_filter( 'request', array( $this, 'request_query_sort_by_invoice_number' ) );
		} else {
			add_filter( 'pre_get_posts', array( $this, 'pre_get_posts_sort_by_invoice_number' ) );
		}

		// AJAX actions for deleting, regenerating and saving document data
		add_action( 'wp_ajax_wpo_wcpdf_delete_document', array( $this, 'ajax_crud_document' ) );
		add_action( 'wp_ajax_wpo_wcpdf_regenerate_document', array( $this, 'ajax_crud_document' ) );
		add_action( 'wp_ajax_wpo_wcpdf_save_document', array( $this, 'ajax_crud_document' ) );

		// document actions
		add_action( 'wpo_wcpdf_document_actions', array( $this, 'add_regenerate_document_button' ) );
	}

	// display review admin notice after 100 pdf downloads
	public function review_plugin_notice() {
		if ( $this->is_order_page() === false && !( isset( $_GET['page'] ) && $_GET['page'] == 'wpo_wcpdf_options_page' ) ) {
			return;
		}
		
		if ( get_option( 'wpo_wcpdf_review_notice_dismissed' ) !== false ) {
			return;
		} else {
			if ( isset( $_GET['wpo_wcpdf_dismis_review'] ) ) {
				update_option( 'wpo_wcpdf_review_notice_dismissed', true );
				return;
			}

			// get invoice count to determine whether notice should be shown
			$invoice_count = $this->get_invoice_count();
			if ( $invoice_count > 100 ) {
				// keep track of how many days this notice is show so we can remove it after 7 days
				$notice_shown_on = get_option( 'wpo_wcpdf_review_notice_shown', array() );
				$today = date('Y-m-d');
				if ( !in_array($today, $notice_shown_on) ) {
					$notice_shown_on[] = $today;
					update_option( 'wpo_wcpdf_review_notice_shown', $notice_shown_on );
				}
				// count number of days review is shown, dismiss forever if shown more than 7
				if (count($notice_shown_on) > 7) {
					update_option( 'wpo_wcpdf_review_notice_dismissed', true );
					return;
				}

				$rounded_count = (int) substr( (string) $invoice_count, 0, 1 ) * pow( 10, strlen( (string) $invoice_count ) - 1);
				?>
				<div class="notice notice-info is-dismissible wpo-wcpdf-review-notice">
					<?php /* translators: rounded count */ ?>
					<h3><?php printf( __( 'Wow, you have created more than %d invoices with our plugin!', 'woocommerce-pdf-invoices-packing-slips' ), $rounded_count ); ?></h3>
					<p><?php _e( 'It would mean a lot to us if you would quickly give our plugin a 5-star rating. Help us spread the word and boost our motivation!', 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
					<ul>
						<li><a href="https://wordpress.org/support/plugin/woocommerce-pdf-invoices-packing-slips/reviews/?rate=5#new-post" class="button"><?php _e( 'Yes you deserve it!', 'woocommerce-pdf-invoices-packing-slips' ); ?></span></a></li>
						<li><a href="<?php echo esc_url( add_query_arg( 'wpo_wcpdf_dismis_review', true ) ); ?>" class="wpo-wcpdf-dismiss"><?php _e( 'Hide this message', 'woocommerce-pdf-invoices-packing-slips' ); ?> / <?php _e( 'Already did!', 'woocommerce-pdf-invoices-packing-slips' ); ?></a></li>
						<li><a href="mailto:support@wpovernight.com?Subject=Here%20is%20how%20I%20think%20you%20can%20do%20better"><?php _e( 'Actually, I have a complaint...', 'woocommerce-pdf-invoices-packing-slips' ); ?></a></li>
					</ul>
				</div>
				<script type="text/javascript">
				jQuery( function( $ ) {
					$( '.wpo-wcpdf-review-notice' ).on( 'click', '.notice-dismiss', function( event ) {
						event.preventDefault();
				  		window.location.href = $( '.wpo-wcpdf-dismiss' ).attr('href');
					});
				});
				</script>
				<!-- Hide extensions ad if this is shown -->
				<style>.wcpdf-extensions-ad { display: none; }</style>
				<?php
			}
		}
	}

	public function install_wizard_notice() {
		// automatically remove notice after 1 week, set transient the first time
		if ( $this->is_order_page() === false && !( isset( $_GET['page'] ) && $_GET['page'] == 'wpo_wcpdf_options_page' ) ) {
			return;
		}
		
		if ( get_option( 'wpo_wcpdf_install_notice_dismissed' ) !== false ) {
			return;
		} else {
			if ( isset( $_GET['wpo_wcpdf_dismis_install'] ) ) {
				update_option( 'wpo_wcpdf_install_notice_dismissed', true );
				return;
			}

			if ( get_transient( 'wpo_wcpdf_new_install' ) !== false ) {
				?>
				<div class="notice notice-info is-dismissible wpo-wcpdf-install-notice">
					<p><strong><?php _e( 'New to WooCommerce PDF Invoices & Packing Slips?', 'woocommerce-pdf-invoices-packing-slips' ); ?></strong> &#8211; <?php _e( 'Jumpstart the plugin by following our wizard!', 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
					<p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wpo-wcpdf-setup' ) ); ?>" class="button-primary"><?php _e( 'Run the Setup Wizard', 'woocommerce-pdf-invoices-packing-slips' ); ?></a> <a href="<?php echo esc_url( add_query_arg( 'wpo_wcpdf_dismis_install', true ) ); ?>" class="wpo-wcpdf-dismiss-wizard"><?php _e( 'I am the wizard', 'woocommerce-pdf-invoices-packing-slips' ); ?></a></p>
				</div>
				<script type="text/javascript">
				jQuery( function( $ ) {
					$( '.wpo-wcpdf-install-notice' ).on( 'click', '.notice-dismiss', function( event ) {
						event.preventDefault();
				  		window.location.href = $( '.wpo-wcpdf-dismiss-wizard' ).attr('href');
					});
				});
				</script>
				<?php
			}
		}

	}

	public function setup_wizard() {
		// Setup/welcome
		if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'wpo-wcpdf-setup' ) {
			delete_transient( 'wpo_wcpdf_new_install' );
			include_once( WPO_WCPDF()->plugin_path() . '/includes/class-wcpdf-setup-wizard.php' );
		}
	}

	public function get_invoice_count() {
		global $wpdb;
		$invoice_count = $wpdb->get_var( $wpdb->prepare( "SELECT count(*)  FROM {$wpdb->postmeta} WHERE meta_key = %s", '_wcpdf_invoice_number' ) );
		return (int) $invoice_count;
	}

	public function update_pdf_counter( $document_type, $document ) {
		if ( in_array( $document_type, array('invoice','packing-slip') ) ) {
			$pdf_count = (int) get_option( 'wpo_wcpdf_count_'.$document_type, 0 );
			update_option( 'wpo_wcpdf_count_'.$document_type, $pdf_count + 1 );
		}
	}

	/**
	 * Add PDF actions to the orders listing
	 */
	public function add_listing_actions( $order ) {
		// do not show buttons for trashed orders
		if ( $order->get_status() == 'trash' ) {
			return;
		}
		$this->disable_storing_document_settings();

		$listing_actions = array();
		$documents = WPO_WCPDF()->documents->get_documents();
		foreach ($documents as $document) {
			$document_title = $document->get_title();
			$icon = !empty($document->icon) ? $document->icon : WPO_WCPDF()->plugin_url() . "/assets/images/generic_document.png";
			if ( $document = wcpdf_get_document( $document->get_type(), $order ) ) {
				$document_title = is_callable( array( $document, 'get_title' ) ) ? $document->get_title() : $document_title;
				$document_exists = is_callable( array( $document, 'exists' ) ) ? $document->exists() : false;
				$listing_actions[$document->get_type()] = array(
					'url'    => wp_nonce_url( admin_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type={$document->get_type()}&order_ids=" . WCX_Order::get_id( $order ) ), 'generate_wpo_wcpdf' ),
					'img'    => $icon,
					'alt'    => "PDF " . $document_title,
					'exists' => $document_exists,
					'class'  => apply_filters( 'wpo_wcpdf_action_button_class', $document_exists ? "exists " . $document->get_type() : $document->get_type(), $document ),
				);
			}
		}

		$listing_actions = apply_filters( 'wpo_wcpdf_listing_actions', $listing_actions, $order );

		foreach ($listing_actions as $action => $data) {
			if ( !isset( $data['class'] ) ) {
				$data['class'] = $data['exists'] ? "exists " . $action : $action;
			}
			?><a href="<?php echo $data['url']; ?>" class="button tips wpo_wcpdf <?php echo $data['class']; ?>" target="_blank" alt="<?php echo $data['alt']; ?>" data-tip="<?php echo $data['alt']; ?>">
				<img src="<?php echo $data['img']; ?>" alt="<?php echo $data['alt']; ?>" width="16">
			</a><?php
		}
	}
	
	/**
	 * Create additional Shop Order column for Invoice Numbers
	 * @param array $columns shop order columns
	 */
	public function add_invoice_number_column( $columns ) {
		// get invoice settings
		$invoice = wcpdf_get_invoice( null );
		$invoice_settings = $invoice->get_settings();
		if ( !isset( $invoice_settings['invoice_number_column'] ) ) {
			return $columns;
		}

		// put the column after the Status column
		$new_columns = array_slice($columns, 0, 2, true) +
			array( 'pdf_invoice_number' => __( 'Invoice Number', 'woocommerce-pdf-invoices-packing-slips' ) ) +
			array_slice($columns, 2, count($columns) - 1, true) ;
		return $new_columns;
	}

	/**
	 * Display Invoice Number in Shop Order column (if available)
	 * @param  string $column column slug
	 */
	public function invoice_number_column_data( $column ) {
		global $post, $the_order;

		if ( $column == 'pdf_invoice_number' ) {
			$this->disable_storing_document_settings();
			if ( empty( $the_order ) || WCX_Order::get_id( $the_order ) != $post->ID ) {
				$order = WCX::get_order( $post->ID );
				if ( $invoice = wcpdf_get_invoice( $order ) ) {
					echo $invoice->get_number();
				}
				do_action( 'wcpdf_invoice_number_column_end', $order );
			} else {
				if ( $invoice = wcpdf_get_invoice( $the_order ) ) {
					echo $invoice->get_number();
				}
				do_action( 'wcpdf_invoice_number_column_end', $the_order );
			}
		}
	}

	/**
	 * Add the meta box on the single order page
	 */
	public function add_meta_boxes() {
		// resend order emails
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.2', '>=' ) ) {
			add_meta_box(
				'wpo_wcpdf_send_emails',
				__( 'Send order email', 'woocommerce-pdf-invoices-packing-slips' ),
				array( $this, 'send_order_email_meta_box' ),
				'shop_order',
				'side',
				'high'
			);
		}

		// create PDF buttons
		add_meta_box(
			'wpo_wcpdf-box',
			__( 'Create PDF', 'woocommerce-pdf-invoices-packing-slips' ),
			array( $this, 'pdf_actions_meta_box' ),
			'shop_order',
			'side',
			'default'
		);

		// Invoice number & date
		add_meta_box(
			'wpo_wcpdf-data-input-box',
			__( 'PDF document data', 'woocommerce-pdf-invoices-packing-slips' ),
			array( $this, 'data_input_box_content' ),
			'shop_order',
			'normal',
			'default'
		);
	}

	/**
	 * Resend order emails
	 */
	public function send_order_email_meta_box( $post ) {
		global $theorder;
		// This is used by some callbacks attached to hooks such as woocommerce_resend_order_emails_available
		// which rely on the global to determine if emails should be displayed for certain orders.
		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $post->ID );
		}
		?>
		<ul class="wpo_wcpdf_send_emails submitbox">
			<li class="wide" id="actions">
				<select name="wpo_wcpdf_send_emails">
					<option value=""><?php esc_html_e( 'Choose an email to send&hellip;', 'woocommerce-pdf-invoices-packing-slips' ); ?></option>
					<?php
					$mailer           = WC()->mailer();
					$available_emails = apply_filters( 'woocommerce_resend_order_emails_available', array( 'new_order', 'cancelled_order', 'customer_processing_order', 'customer_completed_order', 'customer_invoice' ) );
					$mails            = $mailer->get_emails();
					if ( ! empty( $mails ) && ! empty( $available_emails ) ) { ?>
						<?php
						foreach ( $mails as $mail ) {
							if ( in_array( $mail->id, $available_emails ) && 'no' !== $mail->enabled ) {
								echo '<option value="send_email_' . esc_attr( $mail->id ) . '">' . esc_html( $mail->title ) . '</option>';
							}
						} ?>
						<?php
					}
					?>
				</select>
				<input type="submit" class="button save_order button-primary" name="save" value="<?php esc_attr_e( 'Save order & send email', 'woocommerce-pdf-invoices-packing-slips' ); ?>" />
				<?php
				$title = __( 'Send email', 'woocommerce-pdf-invoices-packing-slips' );
				$url = wp_nonce_url( add_query_arg('wpo_wcpdf_action','resend_email'), 'generate_wpo_wcpdf' );
				?>
			</li>
		</ul>
		<?php
	}

	/**
	 * Create the meta box content on the single order page
	 */
	public function pdf_actions_meta_box( $post ) {
		global $post_id;
		$this->disable_storing_document_settings();

		$meta_box_actions = array();
		$documents = WPO_WCPDF()->documents->get_documents();
		$order = WCX::get_order( $post->ID );
		foreach ($documents as $document) {
			$document_title = $document->get_title();
			if ( $document = wcpdf_get_document( $document->get_type(), $order ) ) {
				$document_title = is_callable( array( $document, 'get_title' ) ) ? $document->get_title() : $document_title;
				$meta_box_actions[$document->get_type()] = array(
					'url'		=> wp_nonce_url( admin_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type={$document->get_type()}&order_ids=" . $post_id ), 'generate_wpo_wcpdf' ),
					'alt'		=> esc_attr( "PDF " . $document_title ),
					'title'		=> "PDF " . $document_title,
					'exists'	=> is_callable( array( $document, 'exists' ) ) ? $document->exists() : false,
				);
			}
		}

		$meta_box_actions = apply_filters( 'wpo_wcpdf_meta_box_actions', $meta_box_actions, $post_id );

		?>
		<ul class="wpo_wcpdf-actions">
			<?php
			foreach ($meta_box_actions as $document_type => $data) {
				$exists = ( isset( $data['exists'] ) && $data['exists'] == true ) ? 'exists' : '';
				printf('<li><a href="%1$s" class="button %4$s" target="_blank" alt="%2$s">%3$s</a></li>', $data['url'], $data['alt'], $data['title'], $exists);
			}
			?>
		</ul>
		<?php
	}

	public function data_input_box_content( $post ) {
		$order = WCX::get_order( $post->ID );
		$this->disable_storing_document_settings();
		$invoice = wcpdf_get_document( 'invoice', $order );

		do_action( 'wpo_wcpdf_meta_box_start', $order, $this );

		if ( $invoice ) {
			// data
			$data = array(
				'number' => array(
					'label'  => __( 'Invoice Number:', 'woocommerce-pdf-invoices-packing-slips' ),
				),
				'date'   => array(
					'label'  => __( 'Invoice Date:', 'woocommerce-pdf-invoices-packing-slips' ),
				),
				'notes'  => array(
					'label'  => __( 'Notes (printed in the invoice):', 'woocommerce-pdf-invoices-packing-slips' ),
				),
			);
			// output
			$this->output_number_date_edit_fields( $invoice, $data );

		}

		do_action( 'wpo_wcpdf_meta_box_end', $order, $this );
	}

	public function get_current_values_for_document( $document, $data ) {
		$current = array(
			'number' => array(
				'plain'     => $document->exists() && ! empty( $document->get_number() ) ? $document->get_number()->get_plain() : '',
				'formatted' => $document->exists() && ! empty( $document->get_number() ) ? $document->get_number()->get_formatted() : '',
				'name'      => "_wcpdf_{$document->slug}_number",
			),
			'date' => array(
				'formatted' => $document->exists() && ! empty( $document->get_date() ) ? $document->get_date()->date_i18n( wc_date_format().' @ '.wc_time_format() ) : '',
				'date'      => $document->exists() && ! empty( $document->get_date() ) ? $document->get_date()->date_i18n( 'Y-m-d' ) : date_i18n( 'Y-m-d' ),
				'hour'      => $document->exists() && ! empty( $document->get_date() ) ? $document->get_date()->date_i18n( 'H' ) : date_i18n( 'H' ),
				'minute'    => $document->exists() && ! empty( $document->get_date() ) ? $document->get_date()->date_i18n( 'i' ) : date_i18n( 'i' ),
				'name'      => "_wcpdf_{$document->slug}_date",
			),
		);

		if ( !empty( $data['notes'] ) ) {
			$current['notes'] = array(
				'value' => $document->get_document_notes(),
				'name'  =>"_wcpdf_{$document->slug}_notes",
			);
		}

		foreach ( $data as $key => $value ) {
			if ( isset( $current[$key] ) ) {
				$data[$key] = array_merge( $current[$key], $value );
			}
		}

		return apply_filters( 'wpo_wcpdf_current_values_for_document', $data, $document );
	}

	public function output_number_date_edit_fields( $document, $data ) {
		if( empty( $document ) || empty( $data ) ) return;
		$data = $this->get_current_values_for_document( $document, $data );
		?>
		<div class="wcpdf-data-fields" data-document="<?= $document->get_type(); ?>" data-order_id="<?php echo WCX_Order::get_id( $document->order ); ?>">
			<section class="wcpdf-data-fields-section number-date">
				<!-- Title -->
				<h4>
					<?= $document->get_title(); ?>
					<?php if( $document->exists() && ( isset( $data['number'] ) || isset( $data['date'] ) ) ) : ?>
						<span class="wpo-wcpdf-edit-date-number dashicons dashicons-edit"></span>
						<span class="wpo-wcpdf-delete-document dashicons dashicons-trash" data-action="delete" data-nonce="<?php echo wp_create_nonce( "wpo_wcpdf_delete_document" ); ?>"></span>
						<?php do_action( 'wpo_wcpdf_document_actions', $document ); ?>
					<?php endif; ?>
				</h4>

				<!-- Read only -->
				<div class="read-only">
					<?php if( $document->exists() ) : ?>
						<?php if( isset( $data['number'] ) ) : ?>
						<div class="<?= $document->get_type(); ?>-number">
							<p class="form-field <?= $data['number']['name']; ?>_field">	
								<p>
									<span><strong><?= $data['number']['label']; ?></strong></span>
									<span><?= $data['number']['formatted']; ?></span>
								</p>
							</p>
						</div>
						<?php endif; ?>
						<?php if( isset( $data['date'] ) ) : ?>
						<div class="<?= $document->get_type(); ?>-date">
							<p class="form-field form-field-wide">
								<p>
									<span><strong><?= $data['date']['label']; ?></strong></span>
									<span><?= $data['date']['formatted']; ?></span>
								</p>
							</p>
						</div>
						<?php endif; ?>
						<?php do_action( 'wpo_wcpdf_meta_box_after_document_data', $document, $document->order ); ?>
					<?php else : ?>
						<?php /* translators: document title */ ?>
						<span class="wpo-wcpdf-set-date-number button"><?php printf( __( 'Set %s number & date', 'woocommerce-pdf-invoices-packing-slips' ), $document->get_title() ); ?></span>
					<?php endif; ?>
				</div>

				<!-- Editable -->
				<div class="editable">
					<?php if( isset( $data['number'] ) ) : ?>
					<p class="form-field <?= $data['number']['name']; ?>_field ">
						<label for="<?= $data['number']['name']; ?>"><?= $data['number']['label']; ?></label>
						<input type="text" class="short" style="" name="<?= $data['number']['name']; ?>" id="<?= $data['number']['name']; ?>" value="<?= $data['number']['plain']; ?>" disabled="disabled" > (<?= __( 'unformatted!', 'woocommerce-pdf-invoices-packing-slips' ) ?>)
					</p>
					<?php endif; ?>
					<?php if( isset( $data['date'] ) ) : ?>
					<p class="form-field form-field-wide">
						<label for="<?= $data['date']['name'] ?>[date]"><?= $data['date']['label']; ?></label>
						<input type="text" class="date-picker-field" name="<?= $data['date']['name'] ?>[date]" id="<?= $data['date']['name'] ?>[date]" maxlength="10" value="<?= $data['date']['date']; ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" disabled="disabled"/>@<input type="number" class="hour" disabled="disabled" placeholder="<?php _e( 'h', 'woocommerce' ); ?>" name="<?= $data['date']['name']; ?>[hour]" id="<?= $data['date']['name']; ?>[hour]" min="0" max="23" size="2" value="<?= $data['date']['hour']; ?>" pattern="([01]?[0-9]{1}|2[0-3]{1})" />:<input type="number" class="minute" placeholder="<?php _e( 'm', 'woocommerce' ); ?>" name="<?= $data['date']['name']; ?>[minute]" id="<?= $data['date']['name']; ?>[minute]" min="0" max="59" size="2" value="<?= $data['date']['minute']; ?>" pattern="[0-5]{1}[0-9]{1}"  disabled="disabled" />
					</p>
					<?php endif; ?>
				</div>

				<!-- Document Notes -->
				<?php if( array_key_exists( 'notes', $data ) ) : ?>

				<?php do_action( 'wpo_wcpdf_meta_box_before_document_notes', $document, $document->order ); ?>

				<!-- Read only -->
				<div class="read-only">
					<span><strong><?= $data['notes']['label']; ?></strong></span>
					<span class="wpo-wcpdf-edit-document-notes dashicons dashicons-edit" data-edit="notes"></span>
					<p><?= ( $data['notes']['value'] == strip_tags( $data['notes']['value'] ) ) ? nl2br( $data['notes']['value'] ) : $data['notes']['value']; ?></p>
				</div>
				<!-- Editable -->
				<div class="editable-notes">
					<p class="form-field form-field-wide">
						<label for="<?= $data['notes']['name']; ?>"><?= $data['notes']['label']; ?></label>
						<p><textarea name="<?= $data['notes']['name']; ?>" class="<?= $data['notes']['name']; ?>" cols="60" rows="5" disabled="disabled"><?= $data['notes']['value']; ?></textarea></p>
					</p>
				</div>

				<?php do_action( 'wpo_wcpdf_meta_box_after_document_notes', $document, $document->order ); ?>

				<?php endif; ?>
				<!-- / Document Notes -->

			</section>

			<!-- Save/Cancel buttons -->
			<section class="wcpdf-data-fields-section wpo-wcpdf-document-buttons">
				<div>
					<a class="button button-primary wpo-wcpdf-save-document" data-nonce="<?php echo wp_create_nonce( "wpo_wcpdf_save_document" ); ?>" data-action="save"><?php _e( 'Save changes', 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
					<a class="button wpo-wcpdf-cancel"><?php _e( 'Cancel', 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
				</div>
			</section>
			<!-- / Save/Cancel buttons -->
		</div>
		<?php
	}

	public function add_regenerate_document_button( $document ) {
		$document_settings = $document->get_settings( true );
		if ( $document->use_historical_settings() == true || isset( $document_settings['archive_pdf'] ) ) {
			printf( '<span class="wpo-wcpdf-regenerate-document dashicons dashicons-update-alt" data-nonce="%s" data-action="regenerate"></span>', wp_create_nonce( "wpo_wcpdf_regenerate_document" ) );
		}
	}

	/**
	 * Add actions to menu, WP3.5+
	 */
	public function bulk_actions( $actions ) {
		foreach ($this->get_bulk_actions() as $action => $title) {
			$actions[$action] = $title;
		}
		return $actions;
	}

	/**
	 * Add actions to menu, legacy method
	 */
	public function bulk_actions_js() {
		if ( $this->is_order_page() ) {
			?>
			<script type="text/javascript">
			jQuery(document).ready(function() {
				<?php foreach ($this->get_bulk_actions() as $action => $title) { ?>
				jQuery('<option>').val('<?php echo $action; ?>').html('<?php echo esc_attr( $title ); ?>').appendTo("select[name='action'], select[name='action2']");
				<?php }	?>
			});
			</script>
			<?php
		}
	}

	public function get_bulk_actions() {
		$actions = array();
		$documents = WPO_WCPDF()->documents->get_documents();
		foreach ($documents as $document) {
			$actions[$document->get_type()] = "PDF " . $document->get_title();
		}

		return apply_filters( 'wpo_wcpdf_bulk_actions', $actions );
	}

	/**
	 * Save invoice number
	 */
	public function save_invoice_number_date($post_id, $post) {
		$post_type = get_post_type( $post_id );
		if( $post_type == 'shop_order' ) {
			// bail if this is not an actual 'Save order' action
			if ( ! isset($_POST['action']) || $_POST['action'] != 'editpost' ) {
				return;
			}

			$order = WCX::get_order( $post_id );
			if ( $invoice = wcpdf_get_invoice( $order ) ) {
				$is_new = false === $invoice->exists();
				$_POST = stripslashes_deep( $_POST );
				$document_data = $this->process_order_document_form_data( $_POST, $invoice->slug );
				$invoice->set_data( $document_data, $order );

				// check if we have number, and if not generate one
				if( $invoice->get_date() && ! $invoice->get_number() && is_callable( array( $invoice, 'init_number' ) ) ) {
					$invoice->init_number();
				}

				$invoice->save();

				if ( $is_new ) {
					/* translators: name/description of the context for document creation logs */
					WPO_WCPDF()->main->log_to_order_notes( $invoice, __( 'order details (number and/or date set manually)', 'woocommerce-pdf-invoices-packing-slips' ) );
				}
			}

			// allow other documents to hook here and save their form data
			do_action( 'wpo_wcpdf_on_save_invoice_order_data', $_POST, $order, $this );
		}
	}

	/**
	 * Document objects are created in order to check for existence and retrieve data,
	 * but we don't want to store the settings for uninitialized documents.
	 * Only use in frontend/backed (page requests), otherwise settings will never be stored!
	 */
	public function disable_storing_document_settings() {
		add_filter( 'wpo_wcpdf_document_store_settings', array( $this, 'return_false' ), 9999 );
	}

	public function restore_storing_document_settings() {
		remove_filter( 'wpo_wcpdf_document_store_settings', array( $this, 'return_false' ), 9999 );
	}

	public function return_false(){
		return false;
	}

	/**
	 * Send emails manually
	 */
	public function send_emails( $post_id, $post ) {
		if ( ! empty( $_POST['wpo_wcpdf_send_emails'] ) ) {
			$order = wc_get_order( $post_id );
			$action = wc_clean( $_POST['wpo_wcpdf_send_emails'] );
			if ( strstr( $action, 'send_email_' ) ) {
				$email_to_send = str_replace( 'send_email_', '', $action );
				// Switch back to the site locale.
				wc_switch_to_site_locale();
				do_action( 'woocommerce_before_resend_order_emails', $order, $email_to_send );
				// Ensure gateways are loaded in case they need to insert data into the emails.
				WC()->payment_gateways();
				WC()->shipping();
				// Load mailer.
				$mailer = WC()->mailer();
				$mails = $mailer->get_emails();
				if ( ! empty( $mails ) ) {
					foreach ( $mails as $mail ) {
						if ( $mail->id == $email_to_send ) {
							$mail->trigger( $order->get_id(), $order );
							/* translators: %s: email title */
							$order->add_order_note( sprintf( __( '%s email notification manually sent.', 'woocommerce-pdf-invoices-packing-slips' ), $mail->title ), false, true );
						}
					}
				}
				do_action( 'woocommerce_after_resend_order_email', $order, $email_to_send );
				// Restore user locale.
				wc_restore_locale();
				// Change the post saved message.
				add_filter( 'redirect_post_location', function( $location ) {
					// messages in includes/admin/class-wc-admin-post-types.php
					// 11 => 'Order updated and sent.'
					return add_query_arg( 'message', 11, $location );
				} );
			}
		}
	}

	/**
	 * Add invoice number to order search scope
	 */
	public function search_fields ( $custom_fields ) {
		$custom_fields[] = '_wcpdf_invoice_number';
		$custom_fields[] = '_wcpdf_formatted_invoice_number';
		return $custom_fields;
	}

	/**
	 * Check if this is a shop_order page (edit or list)
	 */
	public function is_order_page() {
		global $post_type;
		if( $post_type == 'shop_order' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Add invoice number to order search scope
	 */
	public function invoice_number_column_sortable( $columns ) {
		$columns['pdf_invoice_number'] = 'pdf_invoice_number';
		return $columns;
	}


	/**
	 * Pre WC3.X sorting
	 */
	public function pre_get_posts_sort_by_invoice_number( $query ) {
		if( ! is_admin() ) {
			return;
		}
		$orderby = $query->get( 'orderby');
		if( 'pdf_invoice_number' == $orderby ) {
			$query->set( 'meta_key', '_wcpdf_invoice_number' );
			$query->set( 'orderby', apply_filters( 'wpo_wcpdf_invoice_number_column_orderby', 'meta_value' ) );
		}
	}

	/**
	 * WC3.X+ sorting
	 */
	public function request_query_sort_by_invoice_number( $query_vars ) {
		global $typenow;

		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ), true ) ) {
			if ( isset( $query_vars['orderby'] ) ) {
				if ( 'pdf_invoice_number' === $query_vars['orderby'] ) {
					$query_vars = array_merge( $query_vars, array(
						'meta_key'  => '_wcpdf_invoice_number',
						'orderby'   => apply_filters( 'wpo_wcpdf_invoice_number_column_orderby', 'meta_value' ),
					) );
				}
			}
		}

		return $query_vars;
	}

	/**
	 * Save, regenerate or delete a document from AJAX request
	 */
	public function ajax_crud_document() {
		if ( check_ajax_referer( 'wpo_wcpdf_regenerate_document', 'security', false ) === false && check_ajax_referer( 'wpo_wcpdf_save_document', 'security', false ) === false && check_ajax_referer( 'wpo_wcpdf_delete_document', 'security', false ) === false ) {
			wp_send_json_error( array(
				'message' => __( 'Nonce expired!', 'woocommerce-pdf-invoices-packing-slips' ),
			) );
		}

		if ( ! isset($_POST['action']) ||  ! in_array( $_POST['action'], array( 'wpo_wcpdf_regenerate_document', 'wpo_wcpdf_save_document', 'wpo_wcpdf_delete_document' ) ) ) {
			wp_send_json_error( array(
				'message' => __( 'Bad action!', 'woocommerce-pdf-invoices-packing-slips' ),
			) );
		}

		if( empty($_POST['order_id']) || empty($_POST['document_type']) || empty($_POST['action_type']) ) {
			wp_send_json_error( array(
				'message' => __( 'Incomplete request!', 'woocommerce-pdf-invoices-packing-slips' ),
			) );
		}

		if ( !current_user_can('manage_woocommerce') ) {
			wp_send_json_error( array(
				'message' => __( 'No permissions!', 'woocommerce-pdf-invoices-packing-slips' ),
			) );
		}

		$order_id        = absint( $_POST['order_id'] );
		$order           = WCX::get_order( $order_id );
		$document_type   = sanitize_text_field( $_POST['document_type'] );
		$action_type     = sanitize_text_field( $_POST['action_type'] );
		$notice          = sanitize_text_field( $_POST['wpcdf_document_data_notice'] );

		// parse form data
		parse_str( $_POST['form_data'], $form_data );
		if ( is_array( $form_data ) ) {
			foreach ( $form_data as $key => &$value ) {
				if ( is_array( $value ) && !empty( $value[$order_id] ) ) {
					$value = $value[$order_id];
				}
			}
		}
		$form_data       = stripslashes_deep( $form_data );

		// notice messages
		$notice_messages = array(
			'saved'       => array(
				'success' => __( 'Document data saved!', 'woocommerce-pdf-invoices-packing-slips' ),
				'error'   => __( 'An error occurred while saving the document data!', 'woocommerce-pdf-invoices-packing-slips' ),
			),
			'regenerated' => array(
				'success' => __( 'Document regenerated!', 'woocommerce-pdf-invoices-packing-slips' ),
				'error'   => __( 'An error occurred while regenerating the document!', 'woocommerce-pdf-invoices-packing-slips' ),
			),
			'deleted' => array(
				'success' => __( 'Document deleted!', 'woocommerce-pdf-invoices-packing-slips' ),
				'error'   => __( 'An error occurred while deleting the document!', 'woocommerce-pdf-invoices-packing-slips' ),
			),
		);

		try {
			$document = wcpdf_get_document( $document_type, wc_get_order( $order_id ) );

			if( ! empty( $document ) ) {

				// perform legacy date fields replacements check
				if( isset( $form_data["_wcpdf_{$document->slug}_date"] ) && ! is_array( $form_data["_wcpdf_{$document->slug}_date"] ) ) {
					$form_data = $this->legacy_date_fields_replacements( $form_data, $document->slug );
				}

				// save document data
				$document_data = $this->process_order_document_form_data( $form_data, $document->slug );

				// on regenerate
				if( $action_type == 'regenerate' && $document->exists() ) {
					$document->regenerate( $order, $document_data );

					$response      = array(
						'message' => $notice_messages[$notice]['success'],
					);

				// on delete
				} elseif( $action_type == 'delete' && $document->exists() ) {
					$document->delete();

					$response      = array(
						'message' => $notice_messages[$notice]['success'],
					);

				// on save
				} elseif( $action_type == 'save' ) {
					$is_new = false === $document->exists();
					$document->set_data( $document_data, $order );

					// check if we have number, and if not generate one
					if( $document->get_date() && ! $document->get_number() && is_callable( array( $document, 'init_number' ) ) ) {
						$document->init_number();
					}

					$document->save();

					if ( $is_new ) {
						/* translators: name/description of the context for document creation logs */
						WPO_WCPDF()->main->log_to_order_notes( $document, __( 'order details (number and/or date set manually)', 'woocommerce-pdf-invoices-packing-slips' ) );
					}

					$response      = array(
						'message' => $notice_messages[$notice]['success'],
					);

				// document not exist
				} else {
					$message_complement = __( 'Document does not exist.', 'woocommerce-pdf-invoices-packing-slips' );
					wp_send_json_error( array(
						'message' => $notice_messages[$notice]['error'] . ' ' . $message_complement,
					) );
				}

				wp_send_json_success( $response );

			} else {
				$message_complement = __( 'Document is empty.', 'woocommerce-pdf-invoices-packing-slips' );
				wp_send_json_error( array(
					'message' => $notice_messages[$notice]['error'] . ' ' . $message_complement,
				) );
			}
		} catch ( \Exception $e ) {
			wp_send_json_error( array(
				'message' => $notice_messages[$notice]['error'] . ' ' . $e->getMessage(),
			) );			
		}
	}

	public function legacy_date_fields_replacements( $form_data, $document_slug ) {
		$legacy_date   = sanitize_text_field( $form_data["_wcpdf_{$document_slug}_date"] );
		$legacy_hour   = sanitize_text_field( $form_data["_wcpdf_{$document_slug}_date_hour"] );
		$legacy_minute = sanitize_text_field( $form_data["_wcpdf_{$document_slug}_date_minute"] );
		unset( $form_data["_wcpdf_{$document_slug}_date_hour"] );
		unset( $form_data["_wcpdf_{$document_slug}_date_minute"] );

		$form_data["_wcpdf_{$document_slug}_date"] = array(
			'date'   => $legacy_date,
			'hour'   => $legacy_hour,
			'minute' => $legacy_minute,
		);

		return $form_data;
	}

	public function debug_enabled_warning( $wp_admin_bar ) {
		if ( isset(WPO_WCPDF()->settings->debug_settings['enable_debug']) && current_user_can( 'administrator' ) ) {
			$status_settings_url = 'admin.php?page=wpo_wcpdf_options_page&tab=debug';
			$title = __( 'DEBUG output enabled', 'woocommerce-pdf-invoices-packing-slips' );
			$args = array(
				'id'    => 'admin_bar_wpo_debug_mode',
				'title' => sprintf( '<a href="%s" style="background-color: red; color: white;">%s</a>', $status_settings_url, $title ),
			);
			$wp_admin_bar->add_node( $args );
		}
	}

	public function process_order_document_form_data( $form_data, $document_slug )
	{
		$data = array();

		if( isset( $form_data['_wcpdf_'.$document_slug.'_number'] ) ) {
			$data['number'] = sanitize_text_field( $form_data['_wcpdf_'.$document_slug.'_number'] );
		}

		$date_entered = ! empty( $form_data['_wcpdf_'.$document_slug.'_date'] ) && ! empty( $form_data['_wcpdf_'.$document_slug.'_date']['date'] );
		if( $date_entered ) {
			$date         = $form_data['_wcpdf_'.$document_slug.'_date']['date'];
			$hour         = ! empty( $form_data['_wcpdf_'.$document_slug.'_date']['hour'] ) ? $form_data['_wcpdf_'.$document_slug.'_date']['hour'] : '00';
			$minute       = ! empty( $form_data['_wcpdf_'.$document_slug.'_date']['minute'] ) ? $form_data['_wcpdf_'.$document_slug.'_date']['minute'] : '00';

			// clean & sanitize input
			$date         = date( 'Y-m-d', strtotime( $date ) );
			$hour         = sprintf('%02d', intval( $hour ));
			$minute       = sprintf('%02d', intval( $minute ) );
			$data['date'] = "{$date} {$hour}:{$minute}:00";

		} elseif ( ! $date_entered && !empty( $_POST['_wcpdf_'.$document_slug.'_number'] ) ) {
			$data['date'] = current_time( 'timestamp', true );
		}

		if ( isset( $form_data['_wcpdf_'.$document_slug.'_notes'] ) ) {
			// allowed HTML
			$allowed_html = array(
				'a'		=> array(
					'href' 	=> array(),
					'title' => array(),
					'id' 	=> array(),
					'class'	=> array(),
					'style'	=> array(),
				),
				'br'	=> array(),
				'em'	=> array(),
				'strong'=> array(),
				'div'	=> array(
					'id'	=> array(),
					'class' => array(),
					'style'	=> array(),
				),
				'span'	=> array(
					'id' 	=> array(),
					'class'	=> array(),
					'style'	=> array(),
				),
				'p'		=> array(
					'id' 	=> array(),
					'class' => array(),
					'style' => array(),
				),
				'b'		=> array(),
			);
			
			$data['notes'] = wp_kses( $form_data['_wcpdf_'.$document_slug.'_notes'], $allowed_html );
		}

		return $data;
	}
}

endif; // class_exists

return new Admin();