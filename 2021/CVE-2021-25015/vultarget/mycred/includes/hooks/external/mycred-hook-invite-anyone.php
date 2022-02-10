<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Register Hook
 * @since 0.1
 * @version 1.1
 */
add_filter( 'mycred_setup_hooks', 'mycred_register_invite_anyone_hook', 70 );
function mycred_register_invite_anyone_hook( $installed ) {

	if ( ! function_exists( 'invite_anyone_init' ) ) return $installed;

	$installed['invite_anyone'] = array(
		'title'         => __( 'Invite Anyone Plugin', 'mycred' ),
		'description'   => __( 'Awards %_plural% for sending invitations and/or %_plural% if the invite is accepted.', 'mycred' ),
		'documentation' => 'http://codex.mycred.me/hooks/inviting-users/',
		'callback'      => array( 'myCRED_Invite_Anyone' )
	);

	return $installed;

}

/**
 * Invite Anyone Hook
 * @since 0.1
 * @version 1.4.1
 */
add_action( 'mycred_load_hooks', 'mycred_load_invite_anyone_hook', 70 );
function mycred_load_invite_anyone_hook() {

	// If the hook has been replaced or if plugin is not installed, exit now
	if ( class_exists( 'myCRED_Invite_Anyone' ) || ! function_exists( 'invite_anyone_init' ) ) return;

	class myCRED_Invite_Anyone extends myCRED_Hook {

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'invite_anyone',
				'defaults' => array(
					'send_invite'   => array(
						'creds'        => 1,
						'log'          => '%plural% for sending an invitation',
						'limit'        => '0/x'
					),
					'accept_invite' => array(
						'creds'        => 1,
						'log'          => '%plural% for accepted invitation',
						'limit'        => '0/x'
					)
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 0.1
		 * @version 1.1
		 */
		public function run() {

			if ( $this->prefs['send_invite']['creds'] != 0 )
				add_action( 'sent_email_invite',     array( $this, 'send_invite' ), 10, 3 );

			if ( $this->prefs['accept_invite']['creds'] != 0 ) {

				// Hook into user activation
				if ( function_exists( 'buddypress' ) )
					add_action( 'bp_core_activated_user', array( $this, 'verified_signup' ) );

				add_action( 'accepted_email_invite', array( $this, 'accept_invite' ), 10, 2 );

			}

		}

		/**
		 * Sending Invites
		 * @since 0.1
		 * @version 1.2
		 */
		public function send_invite( $user_id, $email, $group ) {

			// Check for exclusion
			if ( $this->core->exclude_user( $user_id ) ) return;

			// Limit
			if ( $this->over_hook_limit( 'send_invite', 'sending_an_invite', $user_id ) ) return;

			// Award Points
			$this->core->add_creds(
				'sending_an_invite',
				$user_id,
				$this->prefs['send_invite']['creds'],
				$this->prefs['send_invite']['log'],
				0,
				'',
				$this->mycred_type
			);

		}

		/**
		 * Verified Signup
		 * If signups needs to be verified, award points first when they are.
		 * @since 1.4.6
		 * @version 1.1
		 */
		public function verified_signup( $user_id ) {

			// Get Pending List
			$pending = get_transient( 'mycred-pending-bp-signups' );
			if ( $pending === false || ! isset( $pending[ $user_id ] ) ) return;

			// Check for exclusion
			if ( ! $this->core->exclude_user( $pending[ $user_id ] ) && ! $this->over_hook_limit( 'accept_invite', 'accepting_an_invite', $pending[ $user_id ] ) )
				$this->core->add_creds(
					'accepting_an_invite',
					$pending[ $user_id ],
					$this->prefs['accept_invite']['creds'],
					$this->prefs['accept_invite']['log'],
					$user_id,
					array( 'ref_type' => 'user' ),
					$this->mycred_type
				);

			// Remove from list
			unset( $pending[ $user_id ] );

			// Update pending list
			delete_transient( 'mycred-pending-bp-signups' );
			set_transient( 'mycred-pending-bp-signups', $pending, 7 * DAY_IN_SECONDS );

		}

		/**
		 * Accepting Invites
		 * @since 0.1
		 * @version 1.3.1
		 */
		public function accept_invite( $invited_user_id, $inviters = array() ) {

			if ( empty( $inviters ) ) return;

			// Invite Anyone will pass on an array of user IDs of those who have invited this user which we need to loop though
			foreach ( (array) $inviters as $inviter_id ) {

				// Check for exclusion
				if ( $this->core->exclude_user( $inviter_id ) ) continue;

				// Award Points
				$run = true;

				if ( function_exists( 'buddypress' ) ) {

					$run = false;

					// Get pending list
					$pending = get_transient( 'mycred-pending-bp-signups' );
					if ( $pending === false )
						$pending = array();

					// Add to pending list if not there already
					if ( ! isset( $pending[ $invited_user_id ] ) ) {
						$pending[ $invited_user_id ] = $inviter_id;

						delete_transient( 'mycred-pending-bp-signups' );
						set_transient( 'mycred-pending-bp-signups', $pending, 7 * DAY_IN_SECONDS );
					}

				}

				if ( $run && ! $this->over_hook_limit( 'accept_invite', 'accepting_an_invite', $inviter_id ) )
					$this->core->add_creds(
						'accepting_an_invite',
						$inviter_id,
						$this->prefs['accept_invite']['creds'],
						$this->prefs['accept_invite']['log'],
						$invited_user_id,
						array( 'ref_type' => 'user' ),
						$this->mycred_type
					);

			}

		}

		/**
		 * Preferences
		 * @since 0.1
		 * @version 1.2
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<h3><?php _e( 'Sending Invites', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'send_invite' => 'creds' ) ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'send_invite' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'send_invite' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['send_invite']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'send_invite' => 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'send_invite' => 'limit' ) ), $this->field_id( array( 'send_invite' => 'limit' ) ), $prefs['send_invite']['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'send_invite' => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'send_invite' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'send_invite' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['send_invite']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="hook-instance">
	<h3><?php _e( 'Accepted Invites', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'accept_invite' => 'creds' ) ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'accept_invite' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'accept_invite' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['accept_invite']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'accept_invite' => 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'accept_invite' => 'limit' ) ), $this->field_id( array( 'accept_invite' => 'limit' ) ), $prefs['accept_invite']['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'accept_invite' => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'accept_invite' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'accept_invite' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['accept_invite']['log'] ); ?>" class="form-control" />
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
		public function sanitise_preferences( $data ) {

			if ( isset( $data['send_invite']['limit'] ) && isset( $data['send_invite']['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['send_invite']['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['send_invite']['limit'] = $limit . '/' . $data['send_invite']['limit_by'];
				unset( $data['send_invite']['limit_by'] );
			}

			if ( isset( $data['accept_invite']['limit'] ) && isset( $data['accept_invite']['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['accept_invite']['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['accept_invite']['limit'] = $limit . '/' . $data['accept_invite']['limit_by'];
				unset( $data['accept_invite']['limit_by'] );
			}

			return $data;

		}

	}

}
