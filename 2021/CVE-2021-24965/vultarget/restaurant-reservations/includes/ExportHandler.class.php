<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'rtbExportHandler' ) ) {
class rtbExportHandler {

	/**
	 * Registered exports
	 *
	 * @since 0.1
	 */
	public $export_types;


	/**
	 * Initialize the class and register hooks
	 */
	public function __construct() {

		// Admin notices
		add_action( 'admin_notices', array( $this, 'make_mpdf_dir_writable' ) );

		// Register available exports
		add_action( 'admin_init', array( $this, 'register_exports' ) );

		// Load an export file
		add_action( 'admin_init', array( $this, 'load_export' ) );

		// Load bookings page and assets
		add_action( 'rtb_bookings_table_actions', array( $this, 'print_button' ) );
		add_action( 'admin_footer-toplevel_page_rtb-bookings', array( $this, 'print_export_options_modal' ), 9 );

	}

	/**
	 * Add an admin notice if the font directory for mpdf is not
	 * writeable
	 *
	 * @since 0.1
	 */
	public function make_mpdf_dir_writable() {

		global $rtb_controller;
		if ( empty( $rtb_controller ) or ! $rtb_controller->permissions->check_permission( 'export' ) ) {
			return;
		}

		// Only trigger a warning when using the mpdf library
		if ( $rtb_controller->settings->get_setting( 'ebfrtb-pdf-lib' ) !== 'mpdf' ) {
			return;
		}


		// No warning needed if the directory is writable
		if ( wp_is_writable( RTB_PLUGIN_DIR . '/lib/mpdf/vendor/mpdf/mpdf/tmp/ttfontdata/' ) ) {
			return;
		}


		$rtb_settings_link = '<a href="' . admin_url( 'admin.php?page=rtb-settings&tab=rtb-export' ) . '">' . _x( 'export settings', 'Name of a link to the Export tab on the settings page', 'restaurant-reservations' ) . '</a>';
		?>

		<div class="error">
			<p>
				<?php printf( __( 'Warning from Export Bookings for Restaurant Reservations: The server is not able to write to the font directory for the mPDF generator. Your PDF exports may not work properly until you change the file permissions for the directory /wp-content/plugins/export-for-rtb/lib/mpdf/ttfontdata/. Your web host can help you change the file permissions to be compatible. Or you can switch to the TCPDF renderer in the %s.', 'restaurant-reservations' ), $rtb_settings_link ); ?>
			</p>
		</div>

		<?php
	}

	/**
	 * Register supported exports
	 *
	 * @since 0.1
	 */
	public function register_exports() {

		// Load the export classes
		require_once( RTB_PLUGIN_DIR . '/includes/Export.class.php' );
		require_once( RTB_PLUGIN_DIR . '/includes/Export.PDF.class.php' );
		require_once( RTB_PLUGIN_DIR . '/includes/Export.CSV.class.php' );

		// Array of export types and the class which should be used
		// to generate them. All classes should extend ebfrtbExport
		// @todo Excel
		$this->export_types = apply_filters(
			'ebfrtb_export_types',
			array(
				'pdf'	=> array(
					'label'	=> __( 'PDF', 'restaurant-reservations' ),
					'class' => 'ebfrtbExportPDF',
				),
				'csv'	=> array(
					'label'	=> __( 'Excel/CSV', 'restaurant-reservations' ),
					'class' => 'ebfrtbExportCSV',
				),
			)
		);

	}

	/**
	 * Load the requested export
	 *
	 * @since 0.1
	 */
	public function load_export() {
		global $rtb_controller;

		if (! $rtb_controller->permissions->check_permission( 'export' ) ) { return; }

		if ( !isset( $_GET['action'] ) || $_GET['action'] !== 'ebfrtb-export' ) {
			return;
		}

		if ( !current_user_can( 'manage_bookings' ) ) {
			wp_die( __( 'You do not have the required permissions to export bookings.', 'restaurant-reservations' ) );
		}

		if ( isset( $_GET['type'] ) && !empty( $this->export_types[ $_GET['type'] ] ) ) {
			$export_class = $this->export_types[ $_GET['type'] ]['class'];
		}

		if ( !isset( $export_class ) || !class_exists( $export_class ) ) {
			wp_die( __( 'Unable to create export to match your request.', 'restaurant-reservations' ) );
		}

		// Prepare query args
		$query = new rtbQuery( array(), 'export' );
		$query->parse_request_args();
		$query->prepare_args();
		$query->args['posts_per_page'] = -1;

		// Show an error if they forgot to enter dates
		if ( isset( $query->args['date_range'] ) && $query->args['date_range'] == 'dates' && !isset( $query->args['start_date'] ) && empty( $query->args['end_date'] ) ) {
			wp_die( __( "You selected a date range but didn't enter a start or end date. Please return and enter a start or end date.", 'restaurant-reservations' ) );
		}

		// Retrieve bookings
		$bookings = $query->get_bookings();

		if ( empty( $bookings ) ) {
			wp_die( __( 'There are no bookings which match your export request.', 'restaurant-reservations' ) );
		}

		$export = new $export_class( $bookings, array( 'query_args' => $query->args ) );
		$export->deliver(); // calls wp_die()
	}

	/**
	 * Print the export button above and below the table
	 *
	 * @since 0.1.0
	 */
	public function print_button( $pos ) {
		global $rtb_controller;

		if (! $rtb_controller->permissions->check_permission( 'export' ) ) { return; }
		
		?>

		<div class="alignleft actions ebfrtb-actions">
			<a href="#" class="button ebfrtb-export-button">
				<span class="dashicons dashicons-media-spreadsheet"></span>
				<?php esc_html_e( 'Export Bookings', 'restaurant-reservations' ); ?>
			</a>
		</div>

		<?php
	}

	/**
	 * Print the export options modal in the footer
	 * of the bookings page
	 *
	 * @since 0.1
	 */
	public function print_export_options_modal() {

		global $rtb_controller;

		if (! $rtb_controller->permissions->check_permission( 'export' ) ) { return; }
		?>

		<!-- Export bookings options modal -->
		<div id="ebfrtb-options-modal" class="rtb-admin-modal">
			<div class="ebfrtb-options-form rtb-container">
				<form>

					<div class="title">
						<h2>
							<?php esc_html_e( 'Export Bookings', 'restaurant-reservations' ); ?>
						</h2>
					</div>

					<fieldset>
						<div class="type">
							<label for="type" class="hidden-label">
								<?php esc_html_e( 'Type', 'restaurant-reservations' ); ?>
							</label>
							<select name="type">
							<?php foreach( $this->export_types as $type => $export ) : ?>
								<option value="<?php echo esc_attr( $type ); ?>"><?php echo $export['label']; ?></option>
							<?php endforeach; ?>
							</select>
						</div>

						<?php if ( !empty( $rtb_controller->locations ) && !empty( $rtb_controller->locations->post_type ) ) : ?>
							<div class="location">
								<label for="location" class="hidden-label">
									<?php esc_html_e( 'Location', 'restaurant-reservations' ); ?>
								</label>
								<select name="location">
									<option value=""><?php esc_html_e( 'All locations', 'restaurant-reservations' ); ?></option>
									<?php
										$locations = $rtb_controller->locations->get_location_options();
										foreach( $locations as $id => $name ) :
											?>
											<option value="<?php echo absint( $id ); ?>"><?php echo esc_attr( $name ); ?></option>
											<?php
										endforeach;
									?>
								</select>
							</div>
						<?php endif; ?>

						<div class="date-range">
							<input type="hidden" name="date_range">
							<label for="date-range" class="hidden-label">
								<?php esc_html_e( 'Date Range', 'restaurant-reservations' ); ?>
							</label>
							<div class="selector">
								<ul class="options">
									<li>
										<a href="#" data-type="today">
											<?php esc_html_e( 'Today', 'restaurant-reservations' ); ?>
										</a>
									</li>
									<li>
										<a href="#" data-type="upcoming">
											<?php esc_html_e( 'Upcoming', 'restaurant-reservations' ); ?>
										</a>
									</li>
									<li>
										<a href="#" data-type="dates">
											<?php esc_html_e( 'Date Range', 'restaurant-reservations' ); ?>
										</a>
									</li>
								</ul>
								<div class="today">
									<?php esc_html_e( "Today's bookings", 'restaurant-reservations' ); ?>
								</div>
								<div class="upcoming">
									<?php esc_html_e( 'All upcoming bookings', 'restaurant-reservations' ); ?>
								</div>
								<div class="dates">
									<div class="date-start">
										<label for="ebfrtb-start-date">
											<?php esc_html_e( 'Start', 'restaurant-reservations' ); ?>
										</label>
										<input type="text" name="start_date" id="ebfrtb-start-date">
									</div>
									<div class="date-end">
										<label for="ebfrtb-end-date">
											<?php esc_html_e( 'End', 'restaurant-reservations' ); ?>
										</label>
										<input type="text" name="end_date" id="ebfrtb-end-date">
									</div>
								</div>
							</div>
						</div>

						<div class="status">
							<?php foreach( $rtb_controller->cpts->booking_statuses as $key => $status ) : ?>
							<label>
								<input type="checkbox" name="status" value="<?php echo esc_attr( $key ); ?>" <?php checked( $key, 'confirmed' ); ?>>
								<?php echo $status['label']; ?>
							</label>
							<?php endforeach; ?>
						</div>

					</fieldset>
					<button type="submit" class="button button-primary">
						<?php esc_html_e( 'Export', 'restaurant-reservations' ); ?>
					</button>
					<a href="#" class="button" id="ebfrtb-cancel-export-modal">
						<?php esc_html_e( 'Cancel', 'restaurant-reservations' ); ?>
					</a>
					<a href="<?php echo admin_url( 'admin.php?page=rtb-settings&tab=rtb-export-tab' ); ?>" class="settings">
						<?php esc_html_e( 'Settings', 'restaurant-reservations' ); ?>
					</a>
				</form>
			</div>
		</div>

		<?php
	}

}
} // endif;
