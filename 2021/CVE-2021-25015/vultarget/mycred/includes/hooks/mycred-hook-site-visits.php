<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hook for site visits
 * @since 1.5
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_Hook_Site_Visits' ) ) :
	class myCRED_Hook_Site_Visits extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'site_visit',
				'defaults' => array(
					'creds'   => 1,
					'log'     => '%plural% for site visit'
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.5
		 * @version 1.0.2
		 */
		public function run() {

			// Make sure user is logged in. Also to prevent unneccery db queries we
			// check to make sure the user does not have the cookie.
			if ( is_user_logged_in() && ! isset( $_COOKIE['mycred_site_visit'] ) )
				add_action( 'init', array( $this, 'site_visit' ) );

		}

		/**
		 * Visit Hook
		 * @since 1.5
		 * @version 1.1.3
		 */
		public function site_visit() {

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;

			// Current User ID
			$user_id = get_current_user_id();
			$now     = current_time( 'timestamp' );

			// Set cookie to prevent db queries again today.
			$lifespan = (int) ( 24*3600 ) - ( date( 'H', $now ) * 3600 + date( 'i', $now ) * 60 + date( 's', $now ) );
			if ( ! headers_sent() ) setcookie( 'mycred_site_visit', 1, time() +$lifespan, COOKIEPATH, COOKIE_DOMAIN, true );

			// Make sure user is not excluded
			if ( $this->core->exclude_user( $user_id ) ) return;

			// Store todays date as an integer
			$today = (int) apply_filters( 'mycred_site_visit_id', date( 'Ymd', $now ) );
			$data = '';

			// Make sure this is unique
			if ( $this->core->has_entry( 'site_visit', $today, $user_id, $data, $this->mycred_type ) ) return;

			// Execute
			$this->core->add_creds(
				'site_visit',
				$user_id,
				$this->prefs['creds'],
				$this->prefs['log'],
				$today,
				$data,
				$this->mycred_type
			);

		}

		/**
		 * Preference for Site Visit Hook
		 * @since 1.5
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
				<span class="description"><?php echo $this->available_template_tags( array( 'general' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

		}

	}
endif;
