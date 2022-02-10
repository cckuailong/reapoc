<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Export_Module class
 * @since 1.7
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_Export_Module' ) ) :
	class myCRED_Export_Module extends myCRED_Module {

		/**
		 * Construct
		 */
		public function __construct( $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( 'myCRED_Export_Module', array(
				'module_name' => 'export',
				'defaults'    => array(
					'front'        => 0,
					'front_format' => 'formatted',
					'front_name'   => 'my-%username%-%point_type%-export.csv',
					'admin'        => 0,
					'admin_format' => 'both',
					'admin_name'   => 'mycred-%point_type%-export.csv'
				),
				'accordion'   => false,
				'register'    => false,
				'add_to_core' => true
			), $type );

		}

		/**
		 * Load
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function load() {

			add_filter( 'mycred_log_bulk_actions',          array( $this, 'adjust_log_bulk_actions' ) );
			add_action( 'template_redirect',                array( $this, 'catch_front_end_exports' ) );
			add_action( 'mycred_log_admin_actions',         array( $this, 'catch_admin_log_actions' ) );
			add_action( 'mycred_log_my_admin_actions',      array( $this, 'catch_my_back_end_exports' ) );

			add_action( 'mycred_front_history',             array( $this, 'insert_export_front' ) );
			add_action( 'mycred_bp_profile_before_history', array( $this, 'insert_export_front' ) );

			add_filter( 'mycred_admin_log_title',           array( $this, 'add_export_trigger_to_title' ), 10, 2 );
			add_action( 'mycred_top_log_page',              array( $this, 'add_export_buttons' ) );
			add_action( 'mycred_top_my_log_page',           array( $this, 'add_my_export_buttons' ) );

			add_action( 'mycred_after_core_prefs',          array( $this, 'after_general_settings' ), 20 );
			add_filter( 'mycred_save_core_prefs',           array( $this, 'sanitize_extra_settings' ), 80, 3 );

		}

		/**
		 * Adjust Bulk Actions
		 * @since 1.7.8
		 * @version 1.0
		 */
		public function adjust_log_bulk_actions( $actions ) {

			if ( ! apply_filters( 'mycred_user_can_export_admin', (bool) $this->export['admin'], $this ) ) {

				if ( array_key_exists( 'export-raw', $actions ) )
					unset( $actions['export-raw'] );

				if ( array_key_exists( 'export-format', $actions ) )
					unset( $actions['export-format'] );

			}

			else {

				if ( $this->export['admin_format'] === 'formatted' && array_key_exists( 'export-raw', $actions ) )
					unset( $actions['export-raw'] );

				elseif ( $this->export['admin_format'] === 'raw' && array_key_exists( 'export-format', $actions ) )
					unset( $actions['export-format'] );

			}

			return $actions;

		}

		/**
		 * Insert Export Front
		 * @since 1.7
		 * @version 1.0.2
		 */
		public function insert_export_front( $user_id ) {

		    if ( empty( $user_id ) )
		        $user_id = get_current_user_id();

		    if ( !empty( $user_id )  && get_current_user_id() != $user_id ) return;

			if ( absint( $this->export['front'] ) !== 1 || ! is_user_logged_in() ) return;

			// No need to export if there is nothing to export
			if ( ! mycred_user_has_log_entries( $user_id ) ) return;

			$exports     = mycred_get_log_exports();
			unset( $exports['all'] );
			unset( $exports['search'] );

			echo '<p class="text-right mycred-export">';

			$raw = false;
			if ( $this->export['front_format'] === 'raw' || ( $this->export['front_format'] === 'both' && isset( $_GET['raw'] ) && $_GET['raw'] == 1 ) )
				$raw = true;

			foreach ( (array) $exports as $id => $data ) {

				$url = mycred_get_export_url( $id, $raw );
				if ( $url === false ) continue;

				echo '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $data['class'] ) . '">' . esc_html( $data['my_label'] ) . '</a> ';

			}

			echo '</p>';

		}

		/**
		 * Front-end export handler
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function catch_front_end_exports() {

			if ( ! is_user_logged_in() ) return;

			if ( apply_filters( 'mycred_user_can_export', absint( $this->export['front'] ), $this ) === 0 ) return;

			if ( mycred_is_valid_export_url() ) {

				$args       = array();
				$export_set = sanitize_key( $_GET['set'] );

				if ( $this->export['front_format'] === 'raw' || ( $this->export['front_format'] === 'both' && isset( $_GET['raw'] ) && $_GET['raw'] == 1 ) )
					$args['raw'] = true;

				$file_name  = apply_filters( 'mycred_export_file_name', $this->export['front_name'], false );

				do_action( 'mycred_do_front_export', $export_set, $this );

				$export     = new myCRED_Query_Export( $args );

				if ( $export_set == 'user' )
					$export->get_data_by_user( get_current_user_id() );

				$export->set_export_file_name( $file_name );

				$export->do_export();

			}

		}

		/**
		 * Back-end export handler
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function catch_admin_log_actions( $point_type ) {

			if ( ! is_user_logged_in() || ! apply_filters( 'mycred_user_can_export_admin', (bool) $this->export['admin'], $this ) ) return;

			do_action( 'mycred_do_admin_export', $point_type, $this );

			// Bulk action - export selected log entries
			if ( isset( $_GET['action'] ) && substr( $_GET['action'], 0, 6 ) == 'export' && isset( $_GET['entry'] ) ) {

				$args      = array();

				if ( $this->export['admin_format'] === 'raw' || ( $this->export['admin_format'] === 'both' && isset( $_GET['raw'] ) && $_GET['raw'] == 1 ) )
					$args['raw'] = true;

				$file_name = apply_filters( 'mycred_export_file_name', $this->export['admin_name'], $args, true );

				// First get a clean list of ids to delete
				$export    = new myCRED_Query_Export( $args );

				$export->get_data_by_ids( $_GET['entry'] );
				$export->set_export_file_name( $file_name );

				$export->do_export();

			}

			// Use of an export url
			if ( mycred_is_valid_export_url( true ) ) {

				$export_set     = sanitize_key( $_GET['set'] );
				$export_options = mycred_get_log_exports();
				$search_args    = mycred_get_search_args();

				$args           = array();

				if ( $this->export['admin_format'] === 'raw' || ( $this->export['admin_format'] === 'both' && isset( $_GET['raw'] ) && $_GET['raw'] == 1 ) )
					$args['raw'] = true;

				$file_name      = apply_filters( 'mycred_export_file_name', $this->export['admin_name'], true );

				$export         = new myCRED_Query_Export( $args );

				if ( $export_set == 'all' )
					$export->get_data_by_type( $point_type );

				elseif ( $export_set == 'search' )
					$export->get_data_by_query( $search_args );

				$export->set_export_file_name( $file_name );
				$export->do_export();

			}

		}

		/**
		 * Back-end My export handler
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function catch_my_back_end_exports( $point_type ) {

			if ( ! is_user_logged_in() || ! apply_filters( 'mycred_user_can_export_admin', (bool) $this->export['admin'], $this ) ) return;

			do_action( 'mycred_do_my_admin_export', $point_type, $this );

			if ( mycred_is_valid_export_url( true ) ) {

				$args      = array();

				if ( $this->export['admin_format'] === 'raw' || ( $this->export['admin_format'] === 'both' && isset( $_GET['raw'] ) && $_GET['raw'] == 1 ) )
					$args['raw'] = true;

				$file_name = apply_filters( 'mycred_export_file_name', $this->export['admin_name'], true );

				$export    = new myCRED_Query_Export( $args );

				$export->get_data_by_user( get_current_user_id() );
				$export->set_export_file_name( $file_name );

				$export->do_export();

			}

		}

		/**
		 * Add Export Trigger to Title
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function add_export_trigger_to_title( $title, $log_module ) {

			if ( ! apply_filters( 'mycred_user_can_export_admin', (bool) $this->export['admin'], $this ) ) return $title;

			$title .= ' <a href="javascript:void(0)" class="toggle-exporter add-new-h2" data-toggle="export-log-history">' . __( 'Export', 'mycred' ) . '</a>';

			return $title;

		}

		/**
		 * Add Export Buttons
		 * @since 1.7
		 * @version 1.0
		 */
		public function add_export_buttons( $log_module ) {

			// Export options
			$exports     = mycred_get_log_exports();
			$search_args = mycred_get_search_args();

			if ( array_key_exists( 'user', $exports ) && ! array_key_exists( 'user_id', $search_args ) )
				unset( $exports['user'] );

			if ( empty( $search_args ) )
				unset( $exports['search'] );

			if ( empty( $exports ) ) return;

?>
<div style="display:none;" class="clear" id="export-log-history">
	<strong><?php _e( 'Export', 'mycred' ); ?>:</strong>
	<div>
<?php

			$raw = false;
			if ( $this->export['admin_format'] === 'raw' || ( $this->export['admin_format'] === 'both' && isset( $_GET['raw'] ) && $_GET['raw'] == 1 ) )
				$raw = true;

			foreach ( (array) $exports as $id => $data ) {

				$url = mycred_get_export_url( $id, $raw );
				if ( $url === false ) continue;

				if ( $id === 'search' && ! empty( $search_args ) )
					$url = add_query_arg( $search_args, $url );

				echo '<a href="' . esc_url( $url ) . '" class="' . $data['class'] . '">' . $data['label'] . '</a> ';

			}

?>
	</div>
	<p><span class="description"><?php _e( 'Log entries are exported to a CSV file and depending on the number of entries selected, the process may take a few seconds.', 'mycred' ); ?></span></p>
</div>
<script type="text/javascript">
jQuery(function($) {
	$( '.toggle-exporter' ).click(function(){
		$( '#export-log-history' ).toggle();
	});
});
</script>
<?php

		}

		/**
		 * Add My Export Buttons
		 * @since 1.7
		 * @version 1.0
		 */
		public function add_my_export_buttons( $log_module ) {

			$exports     = mycred_get_log_exports();
			unset( $exports['all'] );
			unset( $exports['search'] );

			if ( empty( $exports ) ) return;

?>
<div style="display:none;" class="clear" id="export-log-history">
	<strong><?php _e( 'Export', 'mycred' ); ?>:</strong>
	<div>
<?php

			$raw = false;
			if ( $this->export['admin_format'] === 'raw' || ( $this->export['admin_format'] === 'both' && isset( $_GET['raw'] ) && $_GET['raw'] == 1 ) )
				$raw = true;

			foreach ( (array) $exports as $id => $data ) {

				$url = mycred_get_export_url( $id, $raw );
				if ( $url === false ) continue;

				echo '<a href="' . esc_url( $url ) . '" class="' . $data['class'] . '">' . $data['my_label'] . '</a> ';

			}

?>
	</div>
	<p><span class="description"><?php _e( 'Log entries are exported to a CSV file and depending on the number of entries selected, the process may take a few seconds.', 'mycred' ); ?></span></p>
</div>
<script type="text/javascript">
jQuery(function($) {
	$( '.toggle-exporter' ).click(function(){
		$( '#export-log-history' ).toggle();
	});
});
</script>
<?php

		}

		/**
		 * Settings Page
		 * @since 1.7
		 * @version 1.0
		 */
		public function after_general_settings( $mycred = NULL ) {

			$enabled_disabled = array(
				0 => __( 'Disabled', 'mycred' ),
				1 => __( 'Enabled', 'mycred' )
			);

			$export_formats         = mycred_get_export_formats();
			$export_formats['both'] = __( 'Make both format options available.', 'mycred' );
?>
<h4><span class="dashicons dashicons-admin-plugins static"></span><?php _e( 'Exports', 'mycred' ); ?></h4>
<div class="body" style="display: none;">

	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
			<div class="form-group">
				<label class="mycred-export-prefs-front-end"><?php _e( 'Front End Exports', 'mycred' ); ?></label>
				<select name="mycred_pref_core[export][front]" id="mycred-export-prefs-front-end" class="form-control">
<?php

			foreach ( $enabled_disabled as $value => $label ) {
				echo '<option value="' . $value . '"';
				if ( $this->export['front'] == $value ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
				</select>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-export-prefs-front-end-format"><?php _e( 'Export Format', 'mycred' ); ?></label>
				<select name="mycred_pref_core[export][front_format]" id="mycred-export-prefs-front-end-format" class="form-control">
<?php

			foreach ( $export_formats as $value => $label ) {
				echo '<option value="' . $value . '"';
				if ( $this->export['front_format'] == $value ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
				</select>
			</div>
		</div>
		<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-export-prefs-front-end-name"><?php _e( 'File Name', 'mycred' ); ?></label>
				<input type="text" class="form-control" name="mycred_pref_core[export][front_name]" id="mycred-export-prefs-front-end-name" value="<?php echo esc_attr( $this->export['front_name'] ); ?>" />
				<p><span class="description"><?php echo '<code>%point_type%</code> = ' . __( 'Point Type', 'mycred' ) . ', <code>%username%</code> = ' . __( 'Username', 'mycred' ); ?></span></p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p><span class="description"><?php echo str_replace( 'mycred_history', '<a href="http://codex.mycred.me/shortcodes/mycred_history/" target="_blank">mycred_history</a>', __( 'If enabled, users will only be able to export their own log entries! Export tools becomes available wherever you are using the mycred_history shortcode or in the users profile.', 'mycred' ) ); ?></span></p>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
			<div class="form-group">
				<label class="mycred-export-prefs-admin-end"><?php _e( 'Back End Exports', 'mycred' ); ?></label>
				<select name="mycred_pref_core[export][admin]" id="mycred-export-prefs-admin-end" class="form-control">
<?php

			foreach ( $enabled_disabled as $value => $label ) {
				echo '<option value="' . $value . '"';
				if ( $this->export['admin'] == $value ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
				</select>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-export-prefs-admin-end-format"><?php _e( 'Export Format', 'mycred' ); ?></label>
				<select name="mycred_pref_core[export][admin_format]" id="mycred-export-prefs-admin-end-format" class="form-control">
<?php

			foreach ( $export_formats as $value => $label ) {
				echo '<option value="' . $value . '"';
				if ( $this->export['admin_format'] == $value ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
				</select>
			</div>
		</div>
		<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-export-prefs-admin-end-name"><?php _e( 'File Name', 'mycred' ); ?></label>
				<input type="text" class="form-control" name="mycred_pref_core[export][admin_name]" id="mycred-export-prefs-admin-end-name" value="<?php echo esc_attr( $this->export['admin_name'] ); ?>" />
				<p><span class="description"><?php echo '<code>%point_type%</code> = ' . __( 'Point Type', 'mycred' ) . ', <code>%username%</code> = ' . __( 'Username', 'mycred' ); ?></span></p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p><span class="description"><?php _e( 'Raw format should be used when you intend to use the export tool to backup or import entries in another installation. Formatted exports reflect what users see in their history archive.', 'mycred' ); ?></span></p>
		</div>
	</div>

	<?php do_action( 'mycred_after_export_prefs', $this ); ?>

</div>
<?php

		}

		/**
		 * Sanitize & Save Settings
		 * @since 1.7
		 * @version 1.0
		 */
		public function sanitize_extra_settings( $new_data, $data, $general ) {

			$new_data['export']['front']        = absint( $data['export']['front'] );
			$new_data['export']['front_format'] = sanitize_key( $data['export']['front_format'] );

			$new_data['export']['admin']        = absint( $data['export']['admin'] );
			$new_data['export']['admin_format'] = sanitize_key( $data['export']['admin_format'] );

			return $new_data;

		}

	}
endif;
