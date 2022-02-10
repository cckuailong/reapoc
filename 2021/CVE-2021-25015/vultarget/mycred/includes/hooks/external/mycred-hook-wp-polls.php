<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Register Hook
 * @since 1.1
 * @version 1.1
 */
add_filter( 'mycred_setup_hooks', 'mycred_register_wp_polls_hook', 105 );
function mycred_register_wp_polls_hook( $installed ) {

	if ( ! function_exists( 'vote_poll' ) ) return $installed;

	$installed['wppolls'] = array(
		'title'         => __( 'WP-Polls', 'mycred' ),
		'description'   => __( 'Awards %_plural% for users voting in polls.', 'mycred' ),
		'documentation' => '',
		'callback'      => array( 'myCRED_Hook_WPPolls' )
	);

	return $installed;

}

/**
 * WP-Polls Hook
 * @since 1.1
 * @version 1.1
 */
add_action( 'mycred_load_hooks', 'mycred_load_wp_polls_hook', 105 );
function mycred_load_wp_polls_hook() {

	// If the hook has been replaced or if plugin is not installed, exit now
	if ( class_exists( 'myCRED_Hook_WPPolls' ) || ! function_exists( 'vote_poll' ) ) return;

	class myCRED_Hook_WPPolls extends myCRED_Hook {

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'wppolls',
				'defaults' => array(
					'creds' => 1,
					'log'   => '%plural% for voting'
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.1
		 * @version 1.0
		 */
		public function run() {

			add_action( 'wp_ajax_polls',          array( $this, 'vote_poll' ), 1 );
			add_filter( 'mycred_parse_tags_poll', array( $this, 'parse_custom_tags' ), 10, 2 );

		}

		/**
		 * Poll Voting
		 * @since 1.1
		 * @version 1.1
		 */
		public function vote_poll() {

			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'polls' && is_user_logged_in() ) {

				// Get Poll ID
				$poll_id = ( isset( $_REQUEST['poll_id'] ) ? intval( $_REQUEST['poll_id'] ) : 0 );

				// Ensure Poll ID Is Valid
				if ( $poll_id != 0 ) {

					// Verify Referer
					if ( check_ajax_referer( 'poll_' . $poll_id . '-nonce', 'poll_' . $poll_id . '_nonce', false ) ) {

						// Which View
						switch ( $_REQUEST['view'] ) {

							case 'process':

								$poll_aid       = $_POST["poll_$poll_id"];
								$poll_aid_array = array_unique( array_map( 'intval', explode( ',', $poll_aid ) ) );

								if ( $poll_id > 0 && ! empty( $poll_aid_array ) && check_allowtovote() ) {

									$check_voted = check_voted( $poll_id );

									if ( $check_voted == 0 ) {

										$user_id = get_current_user_id();

										// Make sure we are not excluded
										if ( ! $this->core->exclude_user( $user_id ) ) {
											$this->core->add_creds(
												'poll_voting',
												$user_id,
												$this->prefs['creds'],
												$this->prefs['log'],
												$poll_id,
												array( 'ref_type' => 'poll' ),
												$this->mycred_type
											);
										}

									}

								}

							break;

						}

					}

				}

			}

		}

		/**
		 * Parse Custom Tags in Log
		 * @since 1.1
		 * @version 1.0
		 */
		public function parse_custom_tags( $content, $log_entry ) {

			$poll_id = $log_entry->ref_id;
			$content = str_replace( '%poll_id%', $poll_id, $content );
			$content = str_replace( '%poll_question%', $this->get_poll_name( $poll_id ), $content );

			return $content;

		}

		/**
		 * Get Poll Name (Question)
		 * @since 1.1
		 * @version 1.0
		 */
		protected function get_poll_name( $poll_id ) {

			global $wpdb;
			$sql = "SELECT pollq_question FROM {$wpdb->pollsq} WHERE pollq_id = %d ";

			return $wpdb->get_var( $wpdb->prepare( $sql, $poll_id ) );

		}

		/**
		 * Preferences for WP-Polls
		 * @since 1.1
		 * @version 1.0
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

}
