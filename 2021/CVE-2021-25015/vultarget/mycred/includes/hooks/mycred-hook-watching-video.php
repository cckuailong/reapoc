<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hooks for Viewing Videos
 * @since 1.2
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_Hook_Video_Views' ) ) :
	class myCRED_Hook_Video_Views extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'video_view',
				'defaults' => array(
					'creds'    => 1,
					'log'      => '%plural% for viewing video',
					'logic'    => 'play',
					'interval' => '',
					'leniency' => 10
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.2
		 * @version 1.0.1
		 */
		public function run() {

			add_action( 'wp_ajax_mycred-viewing-videos', array( $this, 'ajax_call_video_points' ) );

			add_filter( 'mycred_video_js', array( $this, 'adjust_js' ) );
			add_filter( 'mycred_video_view_' . $this->mycred_type, array( $this, 'video_view' ), 10, 2 );

		}

		/**
		 * Populate JS
		 * @since 1.8
		 * @version 1.0
		 */
		public function adjust_js( $js ) {

			if ( ! array_key_exists( 'default_logic', $js ) )
				$js['default_logic'] = $this->prefs['logic'];
			
			$interval = 0; // Default Interval set to '0'
			if( !empty( $this->prefs['interval'] ) )
				$interval = $this->prefs['interval'];
			
			if ( ! array_key_exists( 'default_interval', $js ) )
				$js['default_logic'] = ( ( $this->prefs['logic'] == '' ) ? 0 : abs( $interval * 1000 ) );

			return $js;

		}

		/**
		 * View Video Request
		 * @since 1.8
		 * @version 1.0
		 */
		public function video_view( $setup = array() ) {

			$user_id  = get_current_user_id();
			if ( $this->core->exclude_user( $user_id ) ) wp_send_json_error();

			list ( $source, $video_id, $amount, $logic, $interval, $point_type ) = $setup;

			// Required
			if ( empty( $source ) || empty( $video_id ) )  wp_send_json_error();

			// Prep
			$amount   = $this->core->number( $amount );
			$interval = abs( $interval / 1000 );

			// Get playback details
			$actions  = sanitize_text_field( $_POST['video_a'] );
			$seconds  = absint( $_POST['video_b'] );
			$duration = absint( $_POST['video_c'] );
			$state    = absint( $_POST['video_d'] );

			// Apply Leniency
			$leniency = $duration * ( $this->prefs['leniency'] / 100 );
			$leniency = floor( $leniency );
			$watched  = $seconds + $leniency;

			$status   = 'silence';

			switch ( $logic ) {

				// Award points when video starts
				case 'play' :

					if ( $state == 1 ) {

						if ( ! $this->has_entry( 'watching_video', '', $user_id, $video_id, $this->mycred_type ) ) {

							// Execute
							$this->core->add_creds(
								'watching_video',
								$user_id,
								$amount,
								$this->prefs['log'],
								0,
								$video_id,
								$this->mycred_type
							);

							$status = 'added';

						}
						else {

							$status = 'max';

						}

					}

				break;

				// Award points when video is viewed in full
				case 'full' :

					// Check for skipping or if we watched more (with leniency) then the video length
					if ( ! preg_match( '/22/', $actions, $matches ) || $watched >= $duration ) {

						if ( $state == 0 ) {

							if ( ! $this->has_entry( 'watching_video', '', $user_id, $video_id, $this->mycred_type ) ) {

								// Execute
								$this->core->add_creds(
									'watching_video',
									$user_id,
									$amount,
									$this->prefs['log'],
									0,
									$video_id,
									$this->mycred_type
								);

								$status = 'added';

							}
							else {
								$status = 'max';
							}

						}

					}

				break;

				// Award points in intervals
				case 'interval' :

					// The maximum points a video can earn you
					$num_intervals = floor( $duration / $interval );
					$max           = abs( $num_intervals * $amount );
					$users_log     = $this->get_users_video_log( $video_id, $user_id );

					// Execution Override
					// Allows us to stop an execution. 
					$execute       = apply_filters( 'mycred_video_interval', true, $video_data, false );

					if ( $execute ) {

						// Film is playing and we just started
						if ( $state == 1 && $users_log === NULL ) {

							// Add points without using mycred_add to prevent
							// notifications from being sent as this amount will change.
							$this->core->update_users_balance( $user_id, $amount );

							$this->core->add_to_log(
								'watching_video',
								$user_id,
								$amount,
								$this->prefs['log'],
								0,
								$video_id,
								$this->mycred_type
							);

							$status = 'added';

						}

						// Film is playing and we have not yet reached maximum on this movie
						elseif ( $state == 1 && isset( $users_log->creds ) && $users_log->creds+$amount <= $max ) {

							$this->update_creds( $users_log->id, $user_id, $users_log->creds+$amount );
							$this->core->update_users_balance( $user_id, $amount );
							$amount = $users_log->creds+$amount;

							$status = 'added';

						}

						// Film has ended and we have not reached maximum
						elseif ( $state == 0 && isset( $users_log->creds ) && $users_log->creds+$amount <= $max ) {

							$this->update_creds( $users_log->id, $user_id, $users_log->creds+$amount );
							$this->core->update_users_balance( $user_id, $amount );
							$amount = $users_log->creds+$amount;

							$status = 'max';

							// If enabled, add notification
							if ( function_exists( 'mycred_add_new_notice' ) ) {

								if ( $amount < 0 )
									$color = '<';
								else
									$color = '>';

								$message = str_replace( '%amount%', $amount, $this->prefs['template'] );
								if ( ! empty( $message ) )
									mycred_add_new_notice( array( 'user_id' => $user_id, 'message' => $message, 'color' => $color ) );

							}

						}

					}

				break;
			}

			wp_send_json( array(
				'status'   => $status,
				'video_id' => $video_id,
				'amount'   => $amount,
				'duration' => $duration,
				'seconds'  => $seconds,
				'watched'  => $watched,
				'actions'  => $actions,
				'state'    => $state,
				'logic'    => $logic,
				'interval' => $interval
			) );

		}

		/**
		 * Get Users Video Log
		 * Returns the log for a given video id.
		 * @since 1.2
		 * @version 1.0.1
		 */
		public function get_users_video_log( $video_id, $user_id ) {

			global $wpdb, $mycred_log_table;

			return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mycred_log_table} WHERE user_id = %d AND data = %s AND ctype = %s;", $user_id, $video_id, $this->mycred_type ) );

		}

		/**
		 * Update Points
		 * @since 1.2
		 * @version 1.1
		 */
		public function update_creds( $row_id, $user_id, $amount ) {

			// Prep format
			if ( ! isset( $this->core->format['decimals'] ) )
				$decimals = $this->core->core['format']['decimals'];

			else
				$decimals = $this->core->format['decimals'];

			if ( $decimals > 0 )
				$format = '%f';

			else
				$format = '%d';

			$amount = $this->core->number( $amount );

			global $wpdb, $mycred_log_table;

			$wpdb->update(
				$mycred_log_table,
				array( 'creds' => $amount ),
				array( 'id'    => $row_id ),
				array( $format ),
				array( '%d' )
			);

		}

		/**
		 * Preference for Viewing Videos
		 * @since 1.2
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'creds' ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'creds' ); ?>" id="<?php echo $this->field_id( 'creds' ); ?>" value="<?php echo $this->core->number( $prefs['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'log' ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'log' ); ?>" id="<?php echo $this->field_id( 'log' ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['log'] ); ?>" class="form-control" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'logic' => 'play' ) ); ?>"><?php _e( 'Award Logic', 'mycred' ); ?></label>
				<div class="checkbox">
					<label for="<?php echo $this->field_id( array( 'logic' => 'play' ) ); ?>"><input type="radio" name="<?php echo $this->field_name( 'logic' ); ?>" id="<?php echo $this->field_id( array( 'logic' => 'play' ) ); ?>"<?php checked( $prefs['logic'], 'play' ); ?> value="play" class="toggle-hook-option" /> <?php _e( 'Play - As soon as video starts playing.', 'mycred' ); ?></label>
				</div>
				<div class="checkbox">
					<label for="<?php echo $this->field_id( array( 'logic' => 'full' ) ); ?>"><input type="radio" name="<?php echo $this->field_name( 'logic' ); ?>" id="<?php echo $this->field_id( array( 'logic' => 'full' ) ); ?>"<?php checked( $prefs['logic'], 'full' ); ?> value="full" class="toggle-hook-option" /> <?php _e( 'Full - First when the entire video has played.', 'mycred' ); ?></label>
				</div>
				<div class="checkbox">
					<label for="<?php echo $this->field_id( array( 'logic' => 'interval' ) ); ?>"><input type="radio" name="<?php echo $this->field_name( 'logic' ); ?>" id="<?php echo $this->field_id( array( 'logic' => 'interval' ) ); ?>"<?php checked( $prefs['logic'], 'interval' ); ?> value="interval" class="toggle-hook-option" /> <?php echo $this->core->template_tags_general( __( 'Interval - For each x number of seconds watched.', 'mycred' ) ); ?></label>
				</div>
			</div>
		</div>
		<div class="col-lg-5 col-md-6 col-sm-12 col-xs-12">
			<div id="<?php echo $this->field_id( array( 'logic-option-interval' ) ); ?>"<?php if ( $prefs['logic'] != 'interval' ) echo ' style="display: none;"';?>>
				<div class="form-group">
					<label for="<?php echo $this->field_id( 'interval' ); ?>"><?php _e( 'Intervals', 'mycred' ); ?></label>
					<input type="text" name="<?php echo $this->field_name( 'interval' ); ?>" id="<?php echo $this->field_id( 'interval' ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['interval'] ); ?>" class="form-control" />
					<span class="description"><?php printf( __( 'The number of seconds a user must watch in order to get %s.', 'mycred' ), $this->core->plural() ); ?></span>
				</div>
			</div>
			<div id="<?php echo $this->field_id( array( 'logic-option-full' ) ); ?>"<?php if ( $prefs['logic'] != 'full' ) echo ' style="display: none;"';?>>
				<div class="form-group">
					<label for="<?php echo $this->field_id( 'leniency' ); ?>"><?php _e( 'Leniency', 'mycred' ); ?></label>
					<input type="text" name="<?php echo $this->field_name( 'leniency' ); ?>" id="<?php echo $this->field_id( 'leniency' ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['leniency'] ); ?>" class="form-control" />
					<span class="description"><?php _e( 'Do not set this value to zero! A lot of thing can happen while a user watches a movie and sometimes a few seconds can drop of the counter due to buffering or play back errors.', 'mycred' ); ?></span>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label><?php _e( 'Available Shortcode', 'mycred' ); ?></label>
				<p class="form-control-static"><a href="http://codex.mycred.me/shortcodes/mycred_video/" target="_blank">[mycred_video]</a></p>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function($){

	$( '#sidebar-active .toggle-hook-option' ).change(function(){

		if ( $(this).val() == 'interval' ) {
			$( '#<?php echo $this->field_id( array( 'logic-option-interval' ) ); ?>' ).show();
			$( '#<?php echo $this->field_id( array( 'logic-option-full' ) ); ?>' ).hide();
		}
		else if ( $(this).val() == 'full' ) {
			$( '#<?php echo $this->field_id( array( 'logic-option-full' ) ); ?>' ).show();
			$( '#<?php echo $this->field_id( array( 'logic-option-interval' ) ); ?>' ).hide();
		}
		else {
			$( '#<?php echo $this->field_id( array( 'logic-option-full' ) ); ?>' ).hide();
			$( '#<?php echo $this->field_id( array( 'logic-option-interval' ) ); ?>' ).hide();
		}

	});

});
</script>
<?php

		}

	}
endif;

/**
 * Load Video Viewing Program
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_load_video_view_program' ) ) :
	function mycred_load_video_view_program() {

		// Logged in users do not get points
		if ( ! is_user_logged_in() ) return;

		global $mycred_video_points;

		$mycred_video_points = array();

		add_action( 'mycred_front_enqueue',        'mycred_video_register_scripts' );
		add_action( 'template_redirect',           'mycred_video_detect_views' );
		add_action( 'mycred_front_enqueue_footer', 'mycred_video_enqueue_footer' );

	}
endif;
add_action( 'mycred_init', 'mycred_load_video_view_program', 91 );

/**
 * Register Scripts
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_video_register_scripts' ) ) :
	function mycred_video_register_scripts() {

		wp_register_script(
			'mycred-video-points',
			plugins_url( 'assets/js/video.js', myCRED_THIS ),
			array( 'jquery' ),
			myCRED_VERSION . '.1',
			true
		);

		global $post;

		wp_localize_script(
			'mycred-video-points',
			'myCRED_Video',
			apply_filters( 'mycred_video_js', array(
				'ajaxurl' => esc_url( ( isset( $post->ID ) ) ? mycred_get_permalink( $post->ID ) : home_url( '/' ) ),
				'token'   => wp_create_nonce( 'mycred-video-points' )
			) )
		);
		wp_enqueue_script( 'mycred-video-points' );

		wp_register_script(
			'mycred-video-youtube',
			plugins_url( 'assets/js/youtube.js', myCRED_THIS ),
			array( 'jquery' ),
			myCRED_VERSION . '.1',
			true
		);

	}
endif;

/**
 * Detect Video View AJAX Calls
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_video_detect_views' ) ) :
	function mycred_video_detect_views() {

		if ( is_user_logged_in() ) {

			if ( isset( $_POST['action'] ) && $_POST['action'] == 'mycred-viewing-videos' && isset( $_POST['setup'] ) && isset( $_POST['type'] ) && isset( $_POST['token'] ) && wp_verify_nonce( $_POST['token'], 'mycred-video-points' ) ) {

				$key        = sanitize_text_field( $_POST['setup'] );
				$point_type = sanitize_text_field( $_POST['type'] );
				$setup      = mycred_verify_token( $key, 6 );

				if ( $setup === false || $setup[5] != $point_type ) wp_send_json_error();

				$types = array();
				foreach ( explode( ',', $setup[5] ) as $type_key ) {

					$type_key = sanitize_key( $type_key );
					if ( mycred_point_type_exists( $type_key ) && ! in_array( $type_id, $types ) )
						$types[] = $type_key;

				}

				// Ok so we would prefer to let all types run so we can give multiple point types
				// This hook is very resource heavy so to multiply this heavy task so it can run for each type
				// is a sure way to kill your server. Especially if your site has heavy traffic and a LOT of video views.
				// If your site has heavy traffic, consider using a different approaach then this hook!
				if ( ! empty( $types ) ) {

					do_action( 'mycred_video_view', $point_type, $setup );

					foreach ( $types as $point_type )
						do_action( 'mycred_video_view_' . $point_type, $setup );

				}

			}

		}

	}
endif;

/**
 * Enqueue Scripts
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_video_enqueue_footer' ) ) :
	function mycred_video_enqueue_footer() {

		global $mycred_video_points;

		// If youtube videos are used
		if ( in_array( 'youtube', (array) $mycred_video_points ) )
			wp_enqueue_script( 'mycred-video-youtube' );

	}
endif;
