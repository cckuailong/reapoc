<?php
/**
 * Addon: Import
 * Addon URI: http://mycred.me/add-ons/import/
 * Version: 1.0.2
 * Description: With the Import add-on you can import CSV files, CubePoints or existing points under any custom user meta values.
 * Author: Gabriel S Merovingi
 * Author URI: http://www.merovingi.com
 */
// Translate Header (by Dan bp-fr)
$mycred_addon_header_translate = array(
	__( 'Import', 'mycred' ),
	__( 'With the Import add-on you can import CSV files, CubePoints or existing points under any custom user meta values.', 'mycred' )
);

if ( !defined( 'myCRED_VERSION' ) ) exit;

define( 'myCRED_IMPORT',         __FILE__ );
define( 'myCRED_IMPORT_VERSION', myCRED_VERSION . '.1' );
/**
 * myCRED_Import class
 *
 * Manages all available imports.
 * @since 0.1
 * @version 1.0
 */
if ( !class_exists( 'myCRED_Import' ) ) {
	class myCRED_Import extends myCRED_Module {

		public $errors = '';
		public $import_ok = false;

		/**
		 * Construct
		 */
		function __construct() {
			parent::__construct( 'myCRED_Import', array(
				'module_name' => 'import',
				'labels'      => array(
					'menu'        => __( 'Import', 'mycred' ),
					'page_title'  => __( 'Import', 'mycred' ),
					'page_header' => __( 'Import', 'mycred' )
				),
				'screen_id'   => 'myCRED_page_import',
				'accordion'   => true,
				'register'    => false,
				'menu_pos'    => 90
			) );

			add_action( 'mycred_help',           array( $this, 'help' ), 10, 2 );
		}

		/**
		 * Module Init
		 * @since 0.1
		 * @version 1.0
		 */
		public function module_init() {
			$installed = $this->get();

			// If an import is selected, run it
			if ( empty( $installed ) || !isset( $_REQUEST['selected-import'] ) ) return;
			if ( !array_key_exists( $_REQUEST['selected-import'], $installed ) ) return;

			$call = 'import_' . $_REQUEST['selected-import'];
			$this->$call();

			// Open accordion for import
			add_filter( 'mycred_localize_admin', array( $this, 'accordion' ) );
		}

		/**
		 * Adjust Accordion
		 * Marks the given import as active.
		 * @since 0.1
		 * @version 1.0
		 */
		public function accordion() {
			$key = array_search( trim( $_REQUEST['selected-import'] ), array_keys( $this->installed ) );
			return array( 'active' => $key );
		}

		/**
		 * Get Imports
		 * @since 0.1
		 * @version 1.0
		 */
		public function get( $save = false ) {
			// Defaults
			$installed['csv'] = array(
				'title'        => __( 'CSV File', 'mycred' ),
				'description'  => __( 'Import %_plural% from a comma-separated values (CSV) file.', 'mycred' )
			);
			$installed['cubepoints'] = array(
				'title'       => __( 'CubePoints', 'mycred' ),
				'description' => __( 'Import CubePoints', 'mycred' )
			);
			$installed['custom'] = array(
				'title'       => __( 'Custom User Meta', 'mycred' ),
				'description' => __( 'Import %_plural% from pre-existing custom user meta.', 'mycred' )
			);
			$installed = apply_filters( 'mycred_setup_imports', $installed );

			$this->installed = $installed;
			return $installed;
		}

		/**
		 * Update Users
		 * @param $data (array), required associative array of users and amounts to be added to their account.
		 * @since 0.1
		 * @version 1.1
		 */
		public function update_users( $data = array(), $verify = true ) {
			// Prep
			$id_user_by = 'id';
			if ( isset( $_POST['id_user_by'] ) )
				$id_user_by = $_POST['id_user_by'];

			$xrate = 1;
			if ( isset( $_POST['xrate'] ) )
				$xrate = $_POST['xrate'];

			$round = false;
			if ( isset( $_POST['round'] ) && $_POST['round'] != 'none' )
				$round = $_POST['round'];

			$precision = false;
			if ( isset( $_POST['precision'] ) && $_POST['precision'] != 0 )
				$precision = $_POST['precision'];

			// Loop
			$imports = $skipped = 0;
			foreach ( $data as $row ) {
				// mycred_user and mycred_amount are two mandatory columns!
				if ( !isset( $row['mycred_user'] ) || empty( $row['mycred_user'] ) ) {
					$skipped = $skipped+1;
					continue;
				}
				if ( !isset( $row['mycred_amount'] ) || empty( $row['mycred_amount'] ) ) {
					$skipped = $skipped+1;
					continue;
				}

				// Verify User exist
				if ( $verify === true ) {
					// Get User (and with that confirm user exists)
					$user = get_user_by( $id_user_by, $row['mycred_user'] );

					// User does not exist
					if ( $user === false ) {
						$skipped = $skipped+1;
						continue;
					}

					// User ID
					$user_id = $user->ID;
					unset( $user );
				}
				else {
					$user_id = $row['mycred_user'];
				}

				// Users is excluded
				if ( $this->core->exclude_user( $user_id ) ) {
					$skipped = $skipped+1;
					continue;
				}

				// Amount (can not be zero)
				$cred = $this->core->number( $row['mycred_amount'] );
				if ( $cred == 0 ) {
					$skipped = $skipped+1;
					continue;
				}

				// If exchange rate is not 1 for 1
				if ( $xrate != 1 ) {
					// Cred = rate*amount
					$amount = $xrate * $row['mycred_amount'];
					$cred = $this->core->round_value( $amount, $round, $precision );
				}

				// Adjust Balance
				$new_balance = $this->core->update_users_balance( $user_id, $cred );

				// First we check if the mycred_log column is used
				if ( isset( $row['mycred_log'] ) && !empty( $row['mycred_log'] ) ) {
					$this->core->add_to_log( 'import', $user_id, $cred, $row['mycred_log'] );
				}
				// Second we check if the log template is set
				elseif ( isset( $_POST['log_template'] ) && !empty( $_POST['log_template'] ) ) {
					$this->core->add_to_log( 'import', $user_id, $cred, sanitize_text_field( $_POST['log_template'] ) );
				}

				$imports = $imports+1;
			}

			// Pass on the news
			$this->imports = $imports;
			$this->skipped = $skipped;

			unset( $data );
		}

		/**
		 * CSV Importer
		 * Based on the csv-importer plugin. Thanks for teaching me something new.
		 *
		 * @see http://wordpress.org/extend/plugins/csv-importer/
		 * @since 0.1
		 * @version 1.0
		 */
		public function import_csv() {
			// We need a file. or else...
			if ( !isset( $_FILES['mycred_csv'] ) || empty( $_FILES['mycred_csv']['tmp_name'] ) ) {
				$this->errors = __( 'No file selected. Please select your CSV file and try again.', 'mycred' );
				return;
			}

			// Grab CSV Data Fetcher
			require_once( myCRED_ADDONS_DIR . 'import/includes/File-CSV-DataSource.php' );

			// Prep
			$time_start = microtime( true );
			$csv = new File_CSV_DataSource();
			$file = $_FILES['mycred_csv']['tmp_name'];
			$this->strip_BOM( $file );

			// Failed to load file
			if ( !$csv->load( $file ) ) {
				$this->errors = __( 'Failed to load file.', 'mycred' );
				return;
			}

			// Equality for all
			$csv->symmetrize();

			// Update
			$this->update_users( $csv->connect() );

			// Unlink
			if ( file_exists( $file ) ) {
				@unlink( $file );
			}

			// Time
			$exec_time = microtime( true ) - $time_start;

			// Throw an error if there were no imports just skipps
			if ( $this->imports == 0 && $this->skipped != 0 ) {
				$this->errors = sprintf(
					__( 'Zero rows imported! Skipped %d entries. Import completed in %.2f seconds.', 'mycred' ),
					$this->skipped,
					$exec_time
				);
				return;
			}

			// Throw an error if there were no imports and no skipps
			elseif ( $this->imports == 0 && $this->skipped == 0 ) {
				$this->errors = __( 'No valid records found in file. Make sure you have selected the correct way to identify users in the mycred_user column!', 'mycred' );
				return;
			}

			// The joy of success
			$this->import_ok = sprintf(
				__( 'Import successfully completed. A total of %d users were effected and %d entires were skipped. Import completed in %.2f seconds.', 'mycred' ),
				$this->imports,
				$this->skipped,
				$exec_time
			);

			// Clean Up
			unset( $_FILES );
			unset( $csv );

			// Close accordion
			unset( $_POST );
		}

		/**
		 * Import CubePoints
		 * @since 0.1
		 * @version 1.2
		 */
		public function import_cubepoints() {
			$delete = false;
			if ( isset( $_POST['delete'] ) ) $delete = true;

			$meta_key = 'cpoints';
			$time_start = microtime( true );

			global $wpdb;

			// DB Query
			$SQL = "SELECT * FROM {$wpdb->usermeta} WHERE meta_key = %s;";
			$search = $wpdb->get_results( $wpdb->prepare( $SQL, $meta_key ) );

			// No results
			if ( $wpdb->num_rows == 0 ) {
				$this->errors = __( 'No CubePoints found.', 'mycred' );
				return;
			}

			// Found something
			else {
				// Construct a new array for $this->update_users() to match the format used
				// when importing CSV files. User ID goes under 'mycred_user' while 'mycred_amount' holds the value.
				$data = array();
				foreach ( $search as $result ) {
					$data[] = array(
						'mycred_user'   => $result->user_id,
						'mycred_amount' => $result->meta_value,
						'mycred_log'    => ( isset( $_POST['log_template'] ) ) ? sanitize_text_field( $_POST['log_template'] ) : ''
					);
				}

				// Update User without the need to verify the user
				$this->update_users( $data, false );

				// Delete old value if requested
				if ( $delete === true ) {
					foreach ( $search as $result ) {
						delete_user_meta( $result->user_id, $meta_key );
					}
				}
			}

			// Time
			$exec_time = microtime( true ) - $time_start;

			// Throw an error if there were no imports just skipps
			if ( $this->imports == 0 && $this->skipped != 0 ) {
				$this->errors = sprintf(
					__( 'Zero CubePoints imported! Skipped %d entries. Import completed in %.2f seconds.', 'mycred' ),
					$this->skipped,
					$exec_time
				);
				return;
			}

			// Throw an error if there were no imports and no skipps
			elseif ( $this->imports == 0 && $this->skipped == 0 ) {
				$this->errors = __( 'No valid CubePoints founds.', 'mycred' );
				return;
			}

			// The joy of success
			$this->import_ok = sprintf(
				__( 'Import successfully completed. A total of %d users were effected and %d entires were skipped. Import completed in %.2f seconds.', 'mycred' ),
				$this->imports,
				$this->skipped,
				$exec_time
			);

			// Clean Up
			unset( $search );

			// Close Accordion
			unset( $_POST );
		}

		/**
		 * Import Custom User Meta
		 * @since 0.1
		 * @version 1.0.1
		 */
		public function import_custom() {
			if ( !isset( $_POST['meta_key'] ) || empty( $_POST['meta_key'] ) ) {
				$this->errors = __( 'Missing meta key. Not sure what I should be looking for.', 'mycred' );
				return;
			}

			// Prep
			$delete = false;
			if ( isset( $_POST['delete'] ) ) $delete = true;

			$meta_key = $_POST['meta_key'];
			$time_start = microtime( true );

			global $wpdb;

			// DB Query
			$SQL = "SELECT * FROM {$pwbd->usermeta} WHERE meta_key = %s;";
			$search = $wpdb->get_results( $wpdb->prepare( $SQL, $meta_key ) );

			// No results
			if ( $wpdb->num_rows == 0 ) {
				$this->errors = sprintf( __( 'No rows found for the <strong>%s</strong> meta key.', 'mycred' ), $meta_key );
				return;
			}

			// Found something
			else {
				// Construct a new array for $this->update_users() to match the format used
				// when importing CSV files. User ID goes under 'mycred_user' while 'mycred_amount' holds the value.
				$data = array();
				foreach ( $search as $result ) {
					$data[] = array(
						'mycred_user'   => $result->user_id,
						'mycred_amount' => $result->meta_value
					);
				}

				// Update User without the need to verify the user
				$this->update_users( $data, false );

				// Delete old value if requested
				if ( $delete === true ) {
					foreach ( $search as $result ) {
						delete_user_meta( $result->user_id, $meta_key );
					}
				}
			}

			// Time
			$exec_time = microtime( true ) - $time_start;

			// Throw an error if there were no imports just skipps
			if ( $this->imports == 0 && $this->skipped != 0 ) {
				$this->errors = sprintf(
					__( 'Zero rows imported! Skipped %d entries. Import completed in %.2f seconds.', 'mycred' ),
					$this->skipped,
					$exec_time
				);
				return;
			}

			// Throw an error if there were no imports and no skipps
			elseif ( $this->imports == 0 && $this->skipped == 0 ) {
				$this->errors = __( 'No valid records founds.', 'mycred' );
				return;
			}

			// The joy of success
			$this->import_ok = sprintf(
				__( 'Import successfully completed. A total of %d users were effected and %d entires were skipped. Import completed in %.2f seconds.', 'mycred' ),
				$this->imports,
				$this->skipped,
				$exec_time
			);

			// Clean Up
			unset( $search );

			// Close Accordion
			unset( $_POST );
		}

		/**
		 * Admin Page
		 * @since 0.1
		 * @version 1.0
		 */
		public function admin_page() {
			// Security
			if ( !$this->core->can_edit_plugin( get_current_user_id() ) ) wp_die( __( 'Access Denied', 'mycred' ) );

			// Available Imports
			if ( empty( $this->installed ) )
				$this->get(); ?>

	<div class="wrap list" id="myCRED-wrap">
		<div id="icon-myCRED" class="icon32"><br /></div>
		<h2><?php echo sprintf( __( '%s Import', 'mycred' ), mycred_label() ); ?></h2>
<?php
			// Errors
			if ( !empty( $this->errors ) ) {
				echo '<div class="error"><p>' . $this->errors . '</p></div>';
			}

			// Success
			elseif ( $this->import_ok !== false ) {
				echo '<div class="updated"><p>' . $this->import_ok . '</p></div>';
			} ?>

		<p><?php _e( 'Remember to de-activate this add-on once you are done importing!', 'mycred' ); ?></p>
			<div class="list-items expandable-li" id="accordion">
<?php
			if ( !empty( $this->installed ) ) {
				foreach ( $this->installed as $id => $data ) {
					$call = $id . '_form';
					$this->$call( $data );
				}
			} ?>

			</div>
	</div>
<?php
			unset( $this );
		}

		/**
		 * CSV Import Form
		 * @since 0.1
		 * @version 1.0
		 */
		public function csv_form( $data ) {
			$max_upload = (int) ( ini_get( 'upload_max_filesize' ) );
			$max_post = (int) ( ini_get( 'post_max_size' ) );
			$memory_limit = (int) ( ini_get( 'memory_limit' ) );
			$upload_mb = min( $max_upload, $max_post, $memory_limit ); ?>

				<h4><div class="icon icon-active"></div><label><?php echo $data['title']; ?></label></h4>
				<div class="body" style="display:none;">
					<form class="add:the-list: validate" method="post" enctype="multipart/form-data">
						<input type="hidden" name="selected-import" value="csv" />
						<p><?php echo nl2br( $this->core->template_tags_general( $data['description'] ) ); ?></p>
						<label class="subheader" for="mycred-csv-file"><?php _e( 'File', 'mycred' ); ?></label>
						<ol>
							<li>
								<div><input type="file" name="mycred_csv" id="mycred-csv-file" value="" aria-required="true" /></div>
								<span class="description"><?php echo __( 'Maximum allowed upload size is ', 'mycred' ) . $upload_mb . ' Mb<br />' . __( 'Required columns: <code>mycred_user</code> and <code>mycred_amount</code>. Optional columns: <code>mycred_log</code>.', 'mycred' ); ?></span>
							</li>
						</ol>
						<label class="subheader"><?php _e( 'Identify Users By', 'mycred' ); ?></label>
						<ol>
							<li>
								<input type="radio" name="id_user_by" id="mycred-csv-by-id" value="id" checked="checked" /><label for="mycred-csv-by-id"><?php _e( 'ID', 'mycred' ); ?></label><br />
								<input type="radio" name="id_user_by" id="mycred-csv-by-login" value="login" /><label for="mycred-csv-by-login"><?php _e( 'Username', 'mycred' ); ?></label><br />
								<input type="radio" name="id_user_by" id="mycred-csv-by-email" value="email" /><label for="mycred-csv-by-email"><?php _e( 'Email', 'mycred' ); ?></label>
							</li>
						</ol>
						<label class="subheader" for="mycred-csv-xrate"><?php _e( 'Exchange Rate', 'mycred' ); ?></label>
						<ol>
							<li>
								<div class="h2"><input type="text" name="xrate" id="mycred-csv-xrate" value="<?php echo $this->core->format_number( 1 ); ?>" class="short" /> = <?php echo $this->core->format_creds( 1 ); ?></div>
								<span class="description"><?php _e( 'How much is 1 imported value worth?', 'mycred' ); ?></span>
							</li>
						</ol>
						<ol class="inline">
							<li>
								<label><?php _e( 'Round', 'mycred' ); ?></label><br />
								<input type="radio" name="round" id="mycred-csv-round-none" value="none" checked="checked" /> <label for="mycred-csv-round-none"><?php _e( 'None', 'mycred' ); ?></label><br />
								<input type="radio" name="round" id="mycred-csv-round-up" value="up" /> <label for="mycred-csv-round-up"><?php _e( 'Round Up', 'mycred' ); ?></label><br />
								<input type="radio" name="round" id="mycred-csv-round-down" value="down" /> <label for="mycred-csv-round-down"><?php _e( 'Round Down', 'mycred' ); ?></label>
							</li>
							<?php if ( $this->core->format['decimals'] > 0 ) { ?>

							<li>
								<label for="mycred-csv-precision"><?php _e( 'Precision', 'mycred' ); ?></label>
								<div class="h2"><input type="text" name="precision" id="mycred-csv-precision" value="1" class="short" /></div>
								<span class="description"><?php echo __( 'The optional number of decimal digits to round to. Use zero to round the nearest whole number.', 'mycred' ); ?></span>
							</li>
							<?php } ?>

						</ol>
						<label class="subheader" for="mycred-csv-log-template"><?php _e( 'Log Entry', 'mycred' ); ?></label>
						<ol>
							<li>
								<div class="h2"><input type="text" name="log_template" id="mycred-csv-log-template" value="" class="long" /></div>
								<span class="description"><?php _e( 'See the help tab for available template tags. Leave blank to disable.', 'mycred' ); ?></span>
							</li>
						</ol>
						<ol>
							<li>
								<input type="submit" name="submit" id="mycred-csv-submit" value="<?php _e( 'Run Import', 'mycred' ); ?>" class="button button-primary button-large" />
							</li>
						</ol>
					</form>
				</div>
<?php
		}
		
		/**
		 * CubePoints Import Form
		 * @since 0.1
		 * @version 1.0
		 */
		public function cubepoints_form( $data ) {
			$quick_check = get_users( array(
				'meta_key' => 'cpoints',
				'fields'   => 'ID'
			) );
			$cp_users = count( $quick_check ); ?>

				<h4><div class="icon icon-<?php if ( $cp_users > 0 ) echo 'active'; else echo 'inactive'; ?>"></div><label><?php echo $data['title']; ?></label></h4>
				<div class="body" style="display:none;">
					<form class="add:the-list: validate" method="post" enctype="multipart/form-data">
						<input type="hidden" name="selected-import" value="cubepoints" />
						<p><?php

			if ( $cp_users > 0 )
				echo sprintf( __( 'Found %d users with CubePoints.', 'mycred' ), $cp_users );
			else
				_e( 'No CubePoints found.', 'mycred' ); ?></p>
						<label class="subheader" for="mycred-cubepoints-user-meta-key"><?php _e( 'Meta Key', 'mycred' ); ?></label>
						<ol>
							<li>
								<div class="h2"><input type="text" name="meta_key" id="mycred-cubepoints-user-meta-key" value="cpoints" class="disabled medium" disabled="disabled" /></div>
							</li>
						</ol>
						<label class="subheader" for="mycred-cubepoints-xrate"><?php _e( 'Exchange Rate', 'mycred' ); ?></label>
						<ol>
							<li>
								<div class="h2"><input type="text" name="xrate" id="mycred-cubepoints-xrate" value="<?php echo $this->core->format_number( 1 ); ?>" class="short" /><?php echo 'CubePoint'; ?> = <?php echo $this->core->format_creds( 1 ); ?></div>
							</li>
						</ol>
						<ol class="inline">
							<li>
								<label><?php _e( 'Round', 'mycred' ); ?></label><br />
								<input type="radio" name="round" id="mycred-cubepoints-round-none" value="none" checked="checked" /> <label for="mycred-cubepoints-round-none"><?php _e( 'Do not round', 'mycred' ); ?></label><br />
								<input type="radio" name="round" id="mycred-cubepoints-round-up" value="up" /> <label for="mycred-cubepoints-round-up"><?php _e( 'Round Up', 'mycred' ); ?></label><br />
								<input type="radio" name="round" id="mycred-cubepoints-round-down" value="down" /> <label for="mycred-cubepoints-round-down"><?php _e( 'Round Down', 'mycred' ); ?></label>
							</li>
							<?php if ( $this->core->format['decimals'] > 0 ) { ?>

							<li>
								<label for="mycred-cubepoints-precision"><?php _e( 'Precision', 'mycred' ); ?></label>
								<div class="h2"><input type="text" name="precision" id="mycred-cubepoints-precision" value="1" class="short" /></div>
								<span class="description"><?php echo __( 'The optional number of decimal digits to round to. Use zero to round the nearest whole number.', 'mycred' ); ?></span>
							</li>
							<?php } ?>

						</ol>
						<label class="subheader" for="mycred-cubepoints-delete"><?php _e( 'After Import', 'mycred' ); ?></label>
						<ol>
							<li>
								<input type="checkbox" name="delete" id="mycred-cubepoints-delete" value="no" /> <label for="mycred-cubepoints-delete"><?php _e( 'Delete users CubePoints balance.', 'mycred' ); ?></label>
							</li>
						</ol>
						<label class="subheader" for="mycred-cubepoints-log-template"><?php _e( 'Log Entry', 'mycred' ); ?></label>
						<ol>
							<li>
								<div class="h2"><input type="text" name="log_template" id="mycred-cubepoints-log-template" value="" class="long" /></div>
								<span class="description"><?php _e( 'See the help tab for available template tags. Leave blank to disable.', 'mycred' ); ?></span>
							</li>
						</ol>
						<ol>
							<li>
								<input type="submit" name="submit" id="mycred-cubepoints-submit" value="<?php _e( 'Run Import', 'mycred' ); ?>" class="button button-primary button-large" />
							</li>
						</ol>
					</form>
				</div>
<?php
		}

		/**
		 * Custom User Meta Import Form
		 * @since 0.1
		 * @version 1.0
		 */
		public function custom_form( $data ) { ?>

				<h4><div class="icon icon-active"></div><label><?php echo $data['title']; ?></label></h4>
				<div class="body" style="display:none;">
					<form class="add:the-list: validate" method="post" enctype="multipart/form-data">
						<input type="hidden" name="selected-import" value="custom" />
						<p><?php echo nl2br( $this->core->template_tags_general( $data['description'] ) ); ?></p>
						<label class="subheader" for="mycred-custom-user-meta-key"><?php _e( 'Meta Key', 'mycred' ); ?></label>
						<ol>
							<li>
								<div class="h2"><input type="text" name="meta_key" id="mycred-custom-user-meta-key" value="" class="medium" /></div>
							</li>
						</ol>
						<label class="subheader" for="mycred-custom-xrate"><?php _e( 'Exchange Rate', 'mycred' ); ?></label>
						<ol>
							<li>
								<div class="h2"><input type="text" name="xrate" id="mycred-custom-xrate" value="<?php echo $this->core->format_number( 1 ); ?>" class="short" /> = <?php echo $this->core->format_creds( 1 ); ?></div>
							</li>
						</ol>
						<ol class="inline">
							<li>
								<label><?php _e( 'Round', 'mycred' ); ?></label><br />
								<input type="radio" name="round" id="mycred-custom-round-none" value="none" checked="checked" /> <label for="mycred-custom-round-none"><?php _e( 'Do not round', 'mycred' ); ?></label><br />
								<input type="radio" name="round" id="mycred-custom-round-up" value="up" /> <label for="mycred-custom-round-up"><?php _e( 'Round Up', 'mycred' ); ?></label><br />
								<input type="radio" name="round" id="mycred-custom-round-down" value="down" /> <label for="mycred-custom-round-down"><?php _e( 'Round Down', 'mycred' ); ?></label>
							</li>
							<?php if ( $this->core->format['decimals'] > 0 ) { ?>

							<li>
								<label for="mycred-custom-precision"><?php _e( 'Precision', 'mycred' ); ?></label>
								<div class="h2"><input type="text" name="precision" id="mycred-custom-precision" value="1" class="short" /></div>
								<span class="description"><?php echo __( 'The optional number of decimal digits to round to. Use zero to round the nearest whole number.', 'mycred' ); ?></span>
							</li>
							<?php } ?>

						</ol>
						<label class="subheader" for="mycred-custom-log-template"><?php _e( 'Log Entry', 'mycred' ); ?></label>
						<ol>
							<li>
								<div class="h2"><input type="text" name="log_template" id="mycred-custom-log-template" value="" class="long" /></div>
								<span class="description"><?php _e( 'See the help tab for available template tags. Leave blank to disable.', 'mycred' ); ?></span>
							</li>
						</ol>
						<label class="subheader" for="mycred-custom-delete"><?php _e( 'After Import', 'mycred' ); ?></label>
						<ol>
							<li>
								<input type="checkbox" name="delete" id="mycred-custom-delete" value="no" /> <label for="mycred-custom-delete"><?php _e( 'Delete the old value.', 'mycred' ); ?></label>
							</li>
						</ol>
						<ol>
							<li>
								<input type="submit" name="submit" id="mycred-custom-submit" value="<?php _e( 'Run Import', 'mycred' ); ?>" class="button button-primary button-large" />
							</li>
						</ol>
					</form>
				</div>
<?php
			unset( $this );
		}

		/**
		 * Delete BOM from UTF-8 file.
		 * @see http://wordpress.org/extend/plugins/csv-importer/
		 * @param string $fname
		 * @return void
		 */
		public function strip_BOM( $fname ) {
			$res = fopen( $fname, 'rb' );
			if ( false !== $res ) {
				$bytes = fread( $res, 3 );
				if ( $bytes == pack( 'CCC', 0xef, 0xbb, 0xbf ) ) {
					fclose( $res );

					$contents = file_get_contents( $fname );
					if ( false === $contents ) {
						trigger_error( __( 'Failed to get file contents.', 'mycred' ), E_USER_WARNING );
					}
					$contents = substr( $contents, 3 );
					$success = file_put_contents( $fname, $contents );
					if ( false === $success ) {
						trigger_error( __( 'Failed to put file contents.', 'mycred' ), E_USER_WARNING );
					}
				} else {
					fclose( $res );
				}
			}
		}

		/**
		 * Contextual Help
		 * @since 0.1
		 * @version 1.0
		 */
		public function help( $screen_id, $screen ) {
			if ( $screen_id != 'mycred_page_myCRED_page_import' ) return;

			$screen->add_help_tab( array(
				'id'		=> 'mycred-import',
				'title'		=> __( 'Import', 'mycred' ),
				'content'	=> '
<p>' . $this->core->template_tags_general( __( 'This add-on lets you import %_plural% either though a CSV-file or from your database. Remember that the import can take time depending on your file size or the number of users being imported.', 'mycred' ) ) . '</p>'
			) );
			$screen->add_help_tab( array(
				'id'		=> 'mycred-import-csv',
				'title'		=> __( 'CSV File', 'mycred' ),
				'content'	=> '
<p><strong>' . __( 'CSV Import', 'mycred' ) . '</strong></p>
<p>' . __( 'Imports using a comma-separated values file requires the following columns:', 'mycred' ) . '</p>
<p><code>mycred_user</code> ' . __( 'Column identifing the user. All rows must identify the user the same way, either using an ID, Username (user_login) or email. Users that can not be found will be ignored.', 'mycred' ) . '<br />
<code>mycred_amount</code> ' . __( 'Column with the amount to be imported. If set, an exchange rate is applied to this value before import.', 'mycred' ) . '</p>
<p>' . __( 'Optionally you can also use the <code>mycred_log</code> column to pre-define the log entry for each import.', 'mycred' ) . '</p>'
			) );
			$screen->add_help_tab( array(
				'id'		=> 'mycred-import-cube',
				'title'		=> __( 'Cubepoints', 'mycred' ),
				'content'	=> '
<p><strong>' . __( 'Cubepoints Import', 'mycred' ) . '</strong></p>
<p>' . __( 'When this page loads, the importer will automatically check if you have been using Cubepoints. If you have, you can import these with the option to delete the original Cubepoints once completed to help keep your database clean.', 'mycred' ) . '</p>
<p>' . __( 'Before a value is imported, you can apply an exchange rate. To import without changing the value, use 1 as the exchange rate.', 'mycred' ) . '</p>
<p>' . __( 'You can select to add a log entry for each import or leave the template empty to skip.', 'mycred' ) . '</p>
<p>' . __( 'The Cubepoints importer will automatically disable itself if no Cubepoints installation exists.', 'mycred' ) . '</p>'
			) );
			$screen->add_help_tab( array(
				'id'		=> 'mycred-import-custom',
				'title'		=> __( 'Custom User Meta', 'mycred' ),
				'content'	=> '
<p><strong>' . __( 'Custom User Meta Import', 'mycred' ) . '</strong></p>
<p>' . __( 'You can import any type of points that have previously been saved in your database. All you need is the meta key under which it has been saved.', 'mycred' ) . '</p>
<p>' . __( 'Before a value is imported, you can apply an exchange rate. To import without changing the value, use 1 as the exchange rate.', 'mycred' ) . '</p>
<p>' . __( 'You can select to add a log entry for each import or leave the template empty to skip.', 'mycred' ) . '</p>
<p>' . __( 'Please note that the meta key is case sensitive and can not contain whitespaces!', 'mycred' ) . '</p>'
			) );
		}
	}
	$import = new myCRED_Import();
	$import->load();
}
?>