<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hook for loggins
 * @since 0.1
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_Hook_Logging_In' ) ) :
	class myCRED_Hook_Logging_In extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'logging_in',
				'defaults' => array(
					'creds'   => 1,
					'log'     => '%plural% for logging in',
					'limit'   => '1/d'
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 0.1
		 * @version 1.1
		 */
		public function run() {

			// Social Connect
			if ( function_exists( 'sc_social_connect_process_login' ) )
				add_action( 'social_connect_login', array( $this, 'social_login' ) );

			// WordPress
			add_action( 'wp_login', array( $this, 'logging_in' ), 10, 2 );

		}

		/**
		 * Social Login
		 * Adds support for Social Connect plugin
		 * @since 1.4
		 * @version 1.1
		 */
		public function social_login( $user_login = 0 ) {

			// Get user
			$user = get_user_by( 'login', $user_login );
			if ( ! isset( $user->ID ) ) {
				// In case we use emails for login instead of username
				$user = get_user_by( 'email', $user_login );
				if ( ! is_object( $user ) ) return;
			}

			// Check for exclusion
			if ( $this->core->exclude_user( $user->ID ) ) return;

			// Limit
			if ( ! $this->over_hook_limit( '', 'logging_in', $user->ID ) )
				$this->core->add_creds(
					'logging_in',
					$user->ID,
					$this->prefs['creds'],
					$this->prefs['log'],
					0,
					'',
					$this->mycred_type
				);

		}

		/**
		 * Login Hook
		 * @since 0.1
		 * @version 1.3
		 */
		public function logging_in( $user_login, $user = '' ) {

			// In case the user object is not past along
			if ( ! is_object( $user ) ) {

				$user = get_user_by( 'login', $user_login );
				if ( ! is_object( $user ) ) {

					// In case we use emails for login instead of username
					$user = get_user_by( 'email', $user_login );
					if ( ! is_object( $user ) ) return;

				}

			}

			// Check for exclusion
			if ( $this->core->exclude_user( $user->ID ) ) return;

			// Limit
			if ( ! $this->over_hook_limit( '', 'logging_in', $user->ID ) )
				$this->core->add_creds(
					'logging_in',
					$user->ID,
					$this->prefs['creds'],
					$this->prefs['log'],
					0,
					'',
					$this->mycred_type
				);

		}

		/**
		 * Preference for Login Hook
		 * @since 0.1
		 * @version 1.2
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'creds' ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'creds' ); ?>" id="<?php echo $this->field_id( 'creds' ); ?>" value="<?php echo $this->core->number( $prefs['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'limit' ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( 'limit' ), $this->field_id( 'limit' ), $prefs['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
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

		/**
		 * Sanitise Preferences
		 * @since 1.6
		 * @version 1.0
		 */
		function sanitise_preferences( $data ) {

			if ( isset( $data['limit'] ) && isset( $data['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['limit'] = $limit . '/' . $data['limit_by'];
				unset( $data['limit_by'] );
			}

			return $data;

		}

	}
endif;
