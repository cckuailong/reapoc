<?php
/**
 * Addon: Notifications
 * Addon URI: http://codex.mycred.me/chapter-iii/notifications/
 * Version: 1.1.2
 */
if ( ! defined( 'myCRED_VERSION' ) ) exit;

define( 'myCRED_NOTE',         __FILE__ );
define( 'myCRED_NOTE_VERSION', '1.1.2' );

/**
 * myCRED_Notifications class
 * @since 1.2.3
 * @version 1.3
 */
if ( ! class_exists( 'myCRED_Notifications_Module' ) ) :
	class myCRED_Notifications_Module extends myCRED_Module {

		/**
		 * Construct
		 */
		function __construct() {

			parent::__construct( 'myCRED_Notifications_Module', array(
				'module_name' => 'notifications',
				'defaults'    => mycred_get_addon_defaults( 'notifications' ),
				'register'    => false,
				'add_to_core' => true
			) );
			
			add_filter( 'mycred_add_finished', array( $this, 'mycred_finished' ), 40, 3 );

		}

		/**
		 * Module Init
		 * @since 1.2.3
		 * @version 1.0.1
		 */
		public function module_init() {

			if ( ! is_user_logged_in() ) return;

			add_action( 'mycred_front_enqueue', array( $this, 'register_assets' ), 20 );
			add_action( 'wp_footer',            array( $this, 'get_notices' ), 1 );
			add_action( 'wp_footer',            array( $this, 'wp_footer' ), 999 );

		}

		/**
		 * Load Notice in Footer
		 * @since 1.2.3
		 * @version 1.2
		 */
		public function wp_footer() {

			// Get notifications
			$notices = apply_filters( 'mycred_notifications', array() );
			if ( empty( $notices ) ) return;

			// Should the notice stay till closed / left page or removed automatically
			$stay = 'false';
			if ( $this->notifications['duration'] == 0 )
				$stay = 'true';

			// Let others play before we start
			do_action_ref_array( 'mycred_before_notifications', array( &$notices ) );

			// Loop Notifications
			foreach ( (array) $notices as $notice ) {

				$notice = str_replace( array( "\r", "\n", "\t" ), '', $notice );
				echo '<!-- Notice --><script type="text/javascript">(function(jQuery){jQuery.noticeAdd({ text: "' . $notice . '",stay: ' . $stay . '});})(jQuery);</script>';

			}

			// Let others play after we finished
			do_action_ref_array( 'mycred_after_notifications', array( &$notices ) );

		}

		/**
		 * Register Assets
		 * @since 1.2.3
		 * @version 1.1
		 */
		public function register_assets() {

			// Register script
			wp_register_script(
				'mycred-notifications',
				plugins_url( 'assets/js/notify.js', myCRED_NOTE ),
				array( 'jquery' ),
				myCRED_NOTE_VERSION . '.2',
				true
			);

			// Localize
			wp_localize_script(
				'mycred-notifications',
				'myCRED_Notice',
				array(
					'ajaxurl'  => admin_url( 'admin-ajax.php' ),
					'duration' => $this->notifications['duration']
				)
			);
			wp_enqueue_script( 'mycred-notifications' );

			// If not disabled, enqueue the stylesheet
			if ( $this->notifications['use_css'] == 1 ) {

				wp_register_style(
					'mycred-notifications',
					plugins_url( 'assets/css/notify.css', myCRED_NOTE ),
					false,
					myCRED_NOTE_VERSION . '.2',
					'all',
					true
				);

				wp_enqueue_style( 'mycred-notifications' );

			}

		}

		/**
		 * myCRED Finished
		 * @since 1.6
		 * @version 1.0
		 */
		public function mycred_finished( $reply, $request, $mycred ) {

			if ( $reply === false || $this->notifications['template'] == '' ) return $reply;

			// Parse template
			$template = str_replace( '%entry%', $request['entry'], $this->notifications['template'] );
			$template = str_replace( '%amount%', $request['amount'], $template );

			// Attempt to parse the template tags now that we have the entire request.
			// This way we just need to display it and we are done.
			$template = $mycred->template_tags_amount( $template, $request['amount'] );
			$template = $mycred->parse_template_tags( $template, $this->request_to_entry( $request ) );

			// Let others play
			$template = apply_filters( 'mycred_notifications_note', $template, $request, $mycred );

			// If template is not empty, add it now.
			if ( strlen( $template ) > 0 )
				mycred_add_new_notice( array( 'user_id' => $request['user_id'], 'message' => $template ), $this->notifications['life'] );

			return $reply;

		}

		/**
		 * Get Notices
		 * @since 1.2.3
		 * @version 1.0.1
		 */
		public function get_notices() {

			$user_id = get_current_user_id();
			$data    = get_transient( 'mycred_notice_' . $user_id );

			if ( $data === false || ! is_array( $data ) ) return;

			foreach ( $data as $notice )

				//add_filter( 'mycred_notifications', create_function( '$query', '$query[]=\'' . $notice . '\'; return $query;' ) );
				//replacing above filter second param create function with annonymus function to remove depricated error and passed notice
                add_filter( 'mycred_notifications', function ($query) use ($notice){ $query[]= $notice ; return $query; }  );


            delete_transient( 'mycred_notice_' . $user_id );

		}

		/**
		 * Settings Page
		 * @since 1.2.3
		 * @version 1.2
		 */
		public function after_general_settings( $mycred = NULL ) {

			$prefs = $this->notifications;

?>
<h4><span class="dashicons dashicons-admin-plugins static"></span><?php _e( 'Notifications', 'mycred' ); ?></h4>
<div class="body" style="display:none;">

	<h3><?php _e( 'Setup', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'template' ); ?>"><?php _e( 'Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'template' ); ?>" id="<?php echo $this->field_id( 'template' ); ?>" value="<?php echo esc_attr( $prefs['template'] ); ?>" class="form-control" />
				<p><span class="description"><?php _e( 'Use %entry% to show the log entry in the notice and %amount% for the amount.', 'mycred' ); ?></span> <a href="javascript:void(0);" id="retore-default-notice"><?php _e( 'Restore to default', 'mycred' ); ?></a></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'life' ); ?>"><?php _e( 'Transient Lifespan', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'life' ); ?>" id="<?php echo $this->field_id( 'life' ); ?>" value="<?php echo absint( $prefs['life'] ); ?>" class="form-control" />
				<p><span class="description"><?php _e( 'The number of days a users notification is saved before being automatically deleted.', 'mycred' ); ?></span></p>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'duration' ); ?>"><?php _e( 'Duration', 'mycred' ); ?></label>
				<input type="number" name="<?php echo $this->field_name( 'duration' ); ?>" id="<?php echo $this->field_id( 'duration' ); ?>" value="<?php echo absint( $prefs['duration'] ); ?>" class="form-control" min="0" max="60" />
				<p><span class="description"><?php _e( 'Number of seconds before a notice is automatically removed after being shown to user. Use zero to disable.', 'mycred' ); ?></span></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<div class="checkbox">
					<label for="<?php echo $this->field_id( 'use_css' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'use_css' ); ?>" id="<?php echo $this->field_id( 'use_css' ); ?>" <?php checked( $prefs['use_css'], 1 ); ?> value="1" /> <?php _e( 'Use the included CSS Styling for notifications.', 'mycred' ); ?></label>
				</div>
			</div>
		</div>
	</div>
	<?php if ( MYCRED_SHOW_PREMIUM_ADDONS ) : ?>
	<hr />
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p><strong>Tip:</strong> <?php printf( 'The %s add-on allows you to further style and customize notifications.', sprintf( '<a href="https://mycred.me/store/notifications-plus-add-on/" target="_blank">%s</a>', 'Notifications Plus' ) ); ?></p>
		</div>
	</div>
	<?php endif; ?>

</div>
<script type="text/javascript">
jQuery(function($) {

	$( '#retore-default-notice' ).click(function(){
		$( '#<?php echo $this->field_id( 'template' ); ?>' ).val( '<?php echo $this->default_prefs['template']; ?>' );
	});

});
</script>
<?php

		}

		/**
		 * Sanitize & Save Settings
		 * @since 1.2.3
		 * @version 1.1
		 */
		public function sanitize_extra_settings( $new_data, $data, $general ) {

			$new_data['notifications']['use_css']  = ( isset( $data['notifications']['use_css'] ) ) ? 1: 0;
			$new_data['notifications']['template'] = wp_kses( $data['notifications']['template'], $this->core->allowed_html_tags() );
			$new_data['notifications']['life']     = absint( $data['notifications']['life'] );
			$new_data['notifications']['duration'] = absint( $data['notifications']['duration'] );

			// As of 1.6, we are going from miliseconds to seconds.
			if ( strlen( $new_data['notifications']['duration'] ) >= 3 )
				$new_data['notifications']['duration'] = $new_data['notifications']['duration'] / 1000;

			return $new_data;

		}
	}
endif;

/**
 * Load Notifications Module
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_load_notices_addon' ) ) :
	function mycred_load_notices_addon( $modules, $point_types ) {

		$modules['solo']['notices'] = new myCRED_Notifications_Module();
		$modules['solo']['notices']->load();

		return $modules;

	}
endif;
add_filter( 'mycred_load_modules', 'mycred_load_notices_addon', 70, 2 );

/**
 * Add Notice
 * @since 1.2.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_add_new_notice' ) ) :
	function mycred_add_new_notice( $notice = array(), $life = 1 ) {

		// Minimum requirements
		if ( ! isset( $notice['user_id'] ) || ! isset( $notice['message'] ) ) return false;

			// Get transient
		$data = get_transient( 'mycred_notice_' . $notice['user_id'] );

		// If none exists create a new array
		if ( $data === false || ! is_array( $data ) )
			$notices = array();
		else
			$notices = $data;

		// Add new notice
		$notices[] = addslashes( $notice['message'] );

		// Save as a transient
		set_transient( 'mycred_notice_' . $notice['user_id'], $notices, 86400*$life );

	}
endif;
