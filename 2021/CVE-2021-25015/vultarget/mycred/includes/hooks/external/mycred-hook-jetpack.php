<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Register Hook
 * @since 1.0.5
 * @version 1.1
 */
add_filter( 'mycred_setup_hooks', 'mycred_register_jetpack_hook', 75 );
function mycred_register_jetpack_hook( $installed ) {

	if ( ! defined( 'JETPACK__PLUGIN_DIR' ) ) return $installed;

	$installed['jetpack'] = array(
		'title'         => __( 'Jetpack Subscriptions', 'mycred' ),
		'description'   => __( 'Awards %_plural% for users signing up for site or comment updates using Jetpack.', 'mycred' ),
		'documentation' => 'http://codex.mycred.me/hooks/jetpack-subscriptions/',
		'callback'      => array( 'myCRED_Hook_Jetpack' )
	);

	return $installed;

}

/**
 * Jetpack Hook
 * @since 1.0.5
 * @version 1.1.1
 */
add_action( 'mycred_load_hooks', 'mycred_load_jetpack_hook', 75 );
function mycred_load_jetpack_hook() {

	// If the hook has been replaced or if plugin is not installed, exit now
	if ( class_exists( 'myCRED_Hook_Jetpack' ) || ! defined( 'JETPACK__PLUGIN_DIR' ) ) return;

	class myCRED_Hook_Jetpack extends myCRED_Hook {

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'jetpack',
				'defaults' => array(
					'subscribe_site'    => array(
						'creds'            => 1,
						'log'              => '%plural% for site subscription'
					),
					'subscribe_comment' => array(
						'creds'            => 1,
						'log'              => '%plural% for comment subscription'
					)
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.0.5
		 * @version 1.0
		 */
		public function run() {

			// Site Subscriptions
			if ( $this->prefs['subscribe_site']['creds'] != 0 )
				add_filter( 'wp_redirect',   array( $this, 'submit_redirect' ), 1 );

			// Comment Subscriptions
			if ( $this->prefs['subscribe_comment']['creds'] != 0 )
				add_action( 'comment_post',  array( $this, 'comment_subscribe_submit' ), 99, 2 );

			add_action( 'mycred_admin_init', array( $this, 'admin_init' ) );

		}

		/**
		 * Admin Init
		 * Check pending emails if they have confirmed their subscription. If it's confirmed
		 * and no previous points have been awarded we do that here. Else if the email is marked
		 * as pending we save it for a later try.
		 *
		 * @since 1.0.5
		 * @version 1.1.1
		 */
		public function admin_init() {

			$types = array();

			if ( $this->prefs['subscribe_site']['creds'] != 0 )
				$types[] = 'site';

			if ( $this->prefs['subscribe_comment']['creds'] != 0 )
				$types[] = 'comment';

			// Not enabled, bail
			if ( empty( $types ) ) return;

			foreach ( $types as $type ) {

				// Get list if it exist
				if ( false === ( $pending = get_option( 'mycred_jetpack_' . $type . '_pendings' ) ) )
					continue;

				// Make sure list is not empty
				if ( empty( $pending ) ) {
					// Clean up before exit
					delete_option( 'mycred_jetpack_' . $type . '_pendings' );
					continue;
				}

				$new = array();
				foreach ( $pending as $id => $email ) {

					// Validate
					if ( trim( $email ) == '' || ! is_email( $email ) ) continue;

					// Make sure user exist
					$user = get_user_by( 'email', $email );
					if ( $user === false ) continue;

					// Check for exclusion
					if ( $this->core->exclude_user( $user->ID ) === true ) continue;

					// Make sure this is a unique event
					if ( $this->core->has_entry( 'site_subscription', 0, $user->ID ) ) continue;

					// Site Subscriptions
					if ( $type == 'site' ) {

						// Check subscription status
						$subscription = $this->check_jetpack_subscription( $email );
						// Active status = award points if not already
						if ( $subscription == 'active' ) {
							// Execute
							$this->core->add_creds(
								'site_subscription',
								$user->ID,
								$this->prefs['subscribe_site']['creds'],
								$this->prefs['subscribe_site']['log'],
								0,
								'',
								$this->mycred_type
							);
						}

						// Pending status = save so we try again later
						elseif ( $subscription == 'pending' ) {
							$new[] = $email;
							continue;
						}

					}

					// Comment Subscriptions
					else {

						$comment = get_comment( $id );
						if ( empty( $comment ) ) continue;

						// If no user id exist, check and see if the authors email is used by someone
						if ( $comment->user_id == 0 ) {
							$user = get_user_by( 'email', $email );
							if ( $user === false ) continue;
						}

						// Make sure the user still exist
						else {
							$user = get_user_by( 'id', $comment->user_id );
							if ( $user === false ) continue;
						}

						// Check for exclusion
						if ( $this->core->exclude_user( $user->ID ) === true ) continue;

						// Start with making sure this is a unique event
						if ( $this->core->has_entry( 'comment_subscription', $id, $user->ID ) ) continue;

						$post_ids = array();

						if ( isset( $_REQUEST['subscribe_comments'] ) )
							$post_ids[] = $comment->comment_post_ID;

						// Attempt to subscribe again to get results
						$subscription = $this->check_jetpack_subscription( $email, array( $comment->comment_post_ID ) );

						// Subscription is active
						if ( $subscription == 'active' ) {
							// Execute
							$this->core->add_creds(
								'comment_subscription',
								$user->ID,
								$this->prefs['subscribe_comment']['creds'],
								$this->prefs['subscribe_comment']['log'],
								$id,
								array( 'ref_type' => 'comment' ),
								$this->mycred_type
							);
						}
						// Subscription pending
						elseif ( $subscription == 'pending' ) {
							$new[ $id ] = $email;
						}

					}

				}

				// If we still have pending emails save for later
				if ( ! empty( $new ) )
					update_option( 'mycred_jetpack_' . $type . '_pendings', $new );

				// Else delete
				else
					delete_option( 'mycred_jetpack_' . $type . '_pendings' );

			}

		}

		/**
		 * Submit Redirect
		 * Checks if Jetpack signup has been executed by parsing the redirect URL.
		 * @since 1.0.5
		 * @version 1.0
		 */
		public function submit_redirect( $location ) {

			// Make sure we have what we need
			if ( ! isset( $_REQUEST['jetpack_subscriptions_widget'] ) || ! isset( $_REQUEST['email'] ) || empty( $_REQUEST['email'] ) )
				return $location;

			// Make sure Jetpack has executed
			if ( ! isset( $_GET['subscribe'] ) || $_GET['subscribe'] != 'success' )
				return $location;

			// Make sure user exist
			$user = get_user_by( 'email', $_REQUEST['email'] );
			if ( $user === false )
				return $location;

			// Check for exclusion
			if ( $this->core->exclude_user( $user->ID ) === true )
				return $location;

			// Check that this is a unique event
			if ( $this->core->has_entry( 'site_subscription', '', $user->ID ) )
				return $location;

			$this->site_subscribe( $_REQUEST['email'], $user->ID );

			return $location;

		}

		/**
		 * Comment Subscribe Submit
		 * Manage the request to subscribe to comments and/or to the blog
		 * Based on Jetpack Subscriptions
		 * @see jetpack/modules/subscriptions.php
		 * @since 1.0.5
		 * @version 1.0
		 */
		public function comment_subscribe_submit( $comment_id, $approved ) {

			if ( 'spam' === $approved ) return;

			if ( ! isset( $_REQUEST['subscribe_comments'] ) && ! isset( $_REQUEST['subscribe_blog'] ) )
				return;

			$comment = get_comment( $comment_id );

			// If no user id exist, check and see if the authors email is used by someone
			if ( $comment->user_id == 0 ) {
				$user = get_user_by( 'email', $comment->comment_author_email );
				if ( $user === false ) return;
			}

			// Make sure the user still exist
			else {
				$user = get_user_by( 'id', $comment->user_id );
				if ( $user === false ) return;
			}

			// Check for exclusion
			if ( $this->core->exclude_user( $user->ID ) === true ) return;

			// Start with making sure this is a unique event
			if ( $this->core->has_entry( 'comment_subscription', $comment_id, $user->ID ) ) return;

			// Handle comment subscription
			if ( isset( $_REQUEST['subscribe_comments'] ) )
				$this->comment_subscribe( $comment->comment_author_email, $comment->comment_post_ID, $user->ID, $comment_id );

			// Handle site subscription
			if ( isset( $_REQUEST['subscribe_blog'] ) )
				$this->site_subscribe( $comment->comment_author_email, $user->ID );

		}

		/**
		 * Comment Subscribe
		 * Awards points for active subscriptions or adds email and comment id to the pending array.
		 * Note! This methods should only be called once the primary checks have been made, including making sure
		 * the user exist, is not excluded and that this is a unique event!
		 * @since 1.0.5
		 * @version 1.1
		 */
		protected function comment_subscribe( $email = '', $post_ids = '', $user_id = 0, $comment_id = 0 ) {

			// Attempt to subscribe again to get results
			$subscription = $this->check_jetpack_subscription( $email, $post_ids );

			// Subscription is active
			if ( $subscription == 'active' ) {

				// Execute
				$this->core->add_creds(
					'comment_subscription',
					$user_id,
					$this->prefs['subscribe_comment']['creds'],
					$this->prefs['subscribe_comment']['log'],
					$comment_id,
					array( 'ref_type' => 'comment' ),
					$this->mycred_type
				);

				// Let others share our success
				do_action( 'mycred_jetpack_comment', $user_id, $comment_id );

			}

			// Subscription pending
			elseif ( $subscription == 'pending' ) {
				// Add email to pending list if not in it already
				if ( ! $this->is_pending( $email, $comment_id ) )
					$this->add_to_pending( $email, $comment_id );
			}

		}

		/**
		 * Site Subscription
		 * Awards points for active site subscriptions or adds email to the pending array.
		 * Note! This methods should only be called once the primary checks have been made, including making sure
		 * the user exist, is not excluded and that this is a unique event!
		 * @since 1.0.5
		 * @version 1.1
		 */
		protected function site_subscribe( $email = '', $user_id = 0 ) {

			// Attempt to add this email again to check it's status
			$subscription = $this->check_jetpack_subscription( $email );

			// Subscription is active
			if ( $subscription == 'active' ) {

				// Execute
				$this->core->add_creds(
					'site_subscription',
					$user_id,
					$this->prefs['subscribe_site']['creds'],
					$this->prefs['subscribe_site']['log'],
					0,
					'',
					$this->mycred_type
				);

				// Let others share our success
				do_action( 'mycred_jetpack_site', $user_id, $GLOBALS['blog_id'] );

			}

			// Subscription pending
			elseif ( $subscription == 'pending' ) {
				// Add email to pending list if not in it already
				if ( ! $this->is_pending( $email ) )
					$this->add_to_pending( $email );
			}

		}

		/**
		 * Check Jetpack Subscription
		 * @since 1.0.5
		 * @version 1.0
		 */
		protected function check_jetpack_subscription( $email = NULL, $post_ids = NULL ) {

			if ( $email === NULL ) return 'missing';

			if ( ! class_exists( 'Jetpack' ) && defined( 'JETPACK__PLUGIN_DIR' ) )
				require_once( JETPACK__PLUGIN_DIR . 'jetpack.php' );

			if ( ! class_exists( 'Jetpack_Subscriptions' ) && defined( 'JETPACK__PLUGIN_DIR' ) )
				require_once( JETPACK__PLUGIN_DIR . 'modules/subscriptions.php' );

			if ( $post_ids === NULL )
				$subscribe = Jetpack_Subscriptions::subscribe( $email, 0, false );
			else
				$subscribe = Jetpack_Subscriptions::subscribe( $email, $post_ids, false );

			if ( is_wp_error( $subscribe ) ) {
				$error = $subscribe->get_error_code();
			}
			else {
				$error = false;
				foreach ( $subscribe as $response ) {
					if ( is_wp_error( $response ) ) {
						$error = $response->get_error_code();
						break;
					}
				}
			}

			if ( $error ) {
				switch ( $error ) {
					case 'invalid_email':
						$return = 'invalid';
					break;
					case 'active':
						$return = 'active';
					break;
					case 'pending':
						$return = 'pending';
					break;
					default:
						$return = '';
					break;
				}
			}

			else {
				if ( is_array( $subscribe ) && $subscribe[0] === true )
					$error = true;
					$return = 'pending';
			}

			if ( $error )
				return $return;

			return 'new';

		}

		/**
		 * Is Pending
		 * Checks the given email if it's in the pending array.
		 * @param $email (string) required email to check
		 * @param $section (string|int) either 'site' for site subscriptions or comment id, defaults to site
		 * @returns (bool) true or false
		 * @since 1.0.5
		 * @version 1.0
		 */
		protected function is_pending( $email = NULL, $section = 'site' ) {

			if ( $email === NULL || trim( $email ) === '' ) return;

			if ( $section == 'site' )
				$name = $section;
			else
				$name = 'comment';

			// If pending list does not exist, create it and add our email
			if ( false === ( $pending = get_option( 'mycred_jetpack_' . $name . '_pendings' ) ) ) {
				if ( $name == 'site' )
					$pending = array( $email );
				else
					$pending = array( $section => $email );

				update_option( 'mycred_jetpack_' . $name . '_pendings', $pending );
			}

			// Site check
			if ( $section == 'site' && in_array( $email, $pending ) )
				return true;

			// Comment check
			elseif ( array_key_exists( $section, $pending ) && $pending[ $section ] == $email )
				return true;

			return false;

		}

		/**
		 * Add to Pending
		 * Adds a given email to the pending array.
		 * @param $email (string) required email to check
		 * @param $section (string|int) either 'site' for site subscriptions or comment id, defaults to site
		 * @since 1.0.5
		 * @version 1.0
		 */
		protected function add_to_pending( $email = NULL, $section = 'site' ) {

			if ( $email === NULL || trim( $email ) === '' ) return;

			if ( $section == 'site' )
				$name = $section;
			else
				$name = 'comment';

			// If pending list does not exist, create it and add our email
			if ( false === ( $pending = get_option( 'mycred_jetpack_' . $name . '_pendings' ) ) ) {
				if ( $name == 'site' )
					$pending = array( $email );
				else
					$pending = array( $section => $email );

				update_option( 'mycred_jetpack_' . $name . '_pendings', $pending );
			}

			// Site pending list
			if ( $section == 'site' && ! in_array( $email, $pending ) ) {
				$pending[] = $email;
				update_option( 'mycred_jetpack_' . $name . '_pendings', $pending );
			}

			// Comment pending list
			elseif ( ! array_key_exists( $section, $pending ) ) {
				$pending[ $section ] = $email;
				update_option( 'mycred_jetpack_' . $name . '_pendings', $pending );
			}

		}

		/**
		 * Preferences
		 * @since 1.0.5
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<h3><?php _e( 'Site Subscriptions', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'subscribe_site' => 'creds' ) ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'subscribe_site' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'subscribe_site' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['subscribe_site']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'subscribe_site' => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'subscribe_site' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'subscribe_site' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['subscribe_site']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="hook-instance">
	<h3><?php _e( 'Comment Subscriptions', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'subscribe_comment' => 'creds' ) ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'subscribe_comment' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'subscribe_comment' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['subscribe_comment']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'subscribe_comment' => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'subscribe_comment' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'subscribe_comment' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['subscribe_comment']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

		}

	}

}
