<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hook for comments
 * @since 0.1
 * @version 1.3
 */
if ( ! class_exists( 'myCRED_Hook_Comments' ) ) :
	class myCRED_Hook_Comments extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'comments',
				'defaults' => array(
					'limits'   => array(
						'self_reply' => 0,
						'per_post'   => 10,
						'per_day'    => 0
					),
					'approved' => array(
						'creds'   => 1,
						'log'     => '%plural% for Approved Comment',
						'author'  => 0
					),
					'spam'     => array(
						'creds'   => '-5',
						'log'     => '%plural% deduction for Comment marked as SPAM',
						'author'  => 0
					),
					'trash'    => array(
						'creds'   => '-1',
						'log'     => '%plural% deduction for deleted / unapproved Comment',
						'author'  => 0
					)
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 0.1
		 * @version 1.2
		 */
		public function run() {

			if ( ! function_exists( 'dsq_is_installed' ) ) {
				add_action( 'comment_post',              array( $this, 'new_comment' ), 99, 2 );
				add_action( 'transition_comment_status', array( $this, 'comment_transitions' ), 99, 3 );
			}
			else {
				add_action( 'wp_insert_comment',         array( $this, 'disqus' ), 99, 2 );
			}

		}

		/**
		 * New Comment
		 * If comments are approved without moderation, we apply the corresponding method
		 * or else we will wait till the appropriate instance.
		 * @since 0.1
		 * @version 1.2.2
		 */
		public function new_comment( $comment_id, $comment_status ) {

			// Marked SPAM
			if ( $comment_status === 'spam' )
				$this->comment_transitions( 'spam', 'unapproved', $comment_id );

			// Approved comment
			elseif ( $comment_status == 1 )
				$this->comment_transitions( 'approved', 'unapproved', $comment_id );

		}

		/**
		 * Discuss Support
		 * @since 1.4
		 * @version 1.0
		 */
		function disqus( $id, $comment ) {

			// Attempt to get a comment authors ID
			if ( $comment->user_id == 0 ) {

				$email = get_user_by( 'email', $comment->comment_author_email );
				// Failed to find author, can not award points
				if ( $email === false ) return;
				$comment->user_id = $email->ID;

			}

			$new_status = 'spam';
			if ( $comment->comment_approved == 1 )
				$new_status = 'approved';

			elseif ( $comment->comment_approved == 0 )
				$new_status = 'unapproved';

			$this->comment_transitions( $new_status, 'unapproved', $comment );

		}

		/**
		 * Comment Transitions
		 * @since 1.1.2
		 * @version 1.5
		 */
		public function comment_transitions( $new_status, $old_status, $comment ) {

			// Passing an integer instead of an object means we need to grab the comment object ourselves
			if ( ! is_object( $comment ) )
				$comment = get_comment( $comment );

			// No comment object so lets bail
			if ( $comment === NULL ) return;

			// Ignore Pingbacks or Trackbacks
			if ( ! in_array( $comment->comment_type, array( '', 'comment' ) ) ) return;

			// Logged out users miss out
			if ( $comment->user_id == 0 ) return;

			if ( apply_filters( 'mycred_comment_gets_cred', true, $comment, $new_status, $old_status ) === false ) return;

			// Get comment author
			$comment_author = $comment->user_id;

			// Get content author
			$content_author = NULL;
			if ( isset( $comment->comment_post_ID ) || $comment->comment_post_ID != '' ) {
				$post = mycred_get_post( (int) $comment->comment_post_ID );
				$content_author = $post->post_author;
			}

			$comment_author_points = $this->core->zero();
			$content_author_points = $this->core->zero();

			$reference = '';
			$log = '';

			// Approved Comments
			if ( $new_status == 'approved' ) {
				$reference = 'approved_comment';
				$log = $this->prefs['approved']['log'];

				// From unapproved / hold
				if ( in_array( $old_status, array( 'unapproved', 'hold' ) ) ) {
					// Comment author
					if ( ! $this->user_exceeds_limit( $comment_author, $comment->comment_post_ID ) )
						$comment_author_points = $this->prefs['approved']['creds'];

					// Content author
					$content_author_points = $this->prefs['approved']['author'];
				}

				// From being marked as spam
				elseif ( $old_status == 'spam' ) {
					$comment_author_points = abs( $this->prefs['spam']['creds'] );
					$content_author_points = abs( $this->prefs['spam']['author'] );
				}

				// From being in trash
				elseif ( $old_status == 'trash' ) {
					$comment_author_points = abs( $this->prefs['trash']['creds'] );
					$content_author_points = abs( $this->prefs['trash']['author'] );
				}
			}

			// Unapproved Comments
			elseif ( $new_status == 'unapproved' && $old_status == 'approved' ) {
				$reference = 'unapproved_comment';
				$log = $this->prefs['trash']['log'];

				// If we deducted points for approved comments we want to add them back
				if ( $this->prefs['approved']['creds'] < $this->core->zero() ) {
					$comment_author_points = abs( $this->prefs['approved']['creds'] );
					$content_author_points = abs( $this->prefs['approved']['author'] );
				}

				// Else use what we have set
				else {
					$comment_author_points = $this->prefs['trash']['creds'];
					$content_author_points = $this->prefs['trash']['author'];
				}
			}

			// Marked as SPAM
			elseif ( $new_status == 'spam' ) {
				$reference = 'spam_comment';
				$log = $this->prefs['spam']['log'];

				$comment_author_points = $this->prefs['spam']['creds'];
				$content_author_points = $this->prefs['spam']['author'];
			}

			// Trashed Comments
			elseif ( $new_status == 'trash' ) {
				$reference = 'deleted_comment';
				$log = $this->prefs['trash']['log'];

				$comment_author_points = $this->prefs['trash']['creds'];
				$content_author_points = $this->prefs['trash']['author'];
			}

			// Comment Author
			if ( ! $this->core->exclude_user( $comment_author ) && $comment_author_points != $this->core->zero() ) {

				// Check if we are allowed to comment our own comment and are doing it
				if ( $this->prefs['limits']['self_reply'] != 0 && $comment->comment_parent != 0 ) {
					$parent = get_comment( $comment->comment_parent );
					// Comment author is not replying to their own comments
					if ( $parent->user_id != $comment_author ) {
						$this->core->add_creds(
							$reference,
							$comment_author,
							$comment_author_points,
							$log,
							$comment->comment_ID,
							array( 'ref_type' => 'comment' ),
							$this->mycred_type
						);
					}
				}
				// Else
				else {
					$this->core->add_creds(
						$reference,
						$comment_author,
						$comment_author_points,
						$log,
						$comment->comment_ID,
						array( 'ref_type' => 'comment' ),
						$this->mycred_type
					);
				}

			}

			if ( $content_author === NULL ) return;

			// Content Author
			if ( ! $this->core->exclude_user( $content_author ) && $content_author_points != $this->core->zero() ) {
				$this->core->add_creds(
					$reference,
					$content_author,
					$content_author_points,
					$log,
					$comment->comment_ID,
					array( 'ref_type' => 'comment' ),
					$this->mycred_type
				);
			}

		}

		/**
		 * Check if user exceeds limit
		 * @since 1.1.1
		 * @version 1.1
		 */
		public function user_exceeds_limit( $user_id = NULL, $post_id = NULL ) {

			if ( ! isset( $this->prefs['limits'] ) ) return false;

			// Prep
			$today = date( 'Y-m-d', current_time( 'timestamp' ) );

			// First we check post limit
			if ( $this->prefs['limits']['per_post'] > 0 ) {
				$post_limit = 0;

				// Grab limit
				if ( ! $this->is_main_type )
					$limit = mycred_get_user_meta( $user_id, 'mycred_comment_limit_post_' . $this->mycred_type, '', true );
				else
					$limit = mycred_get_user_meta( $user_id, 'mycred_comment_limit_post', '', true );

				// Apply default if none exist
				if ( empty( $limit ) ) $limit = array( $post_id => $post_limit );

				// Check if post_id is in limit array
				if ( array_key_exists( $post_id, $limit ) ) {
					$post_limit = $limit[ $post_id ];

					// Limit is reached
					if ( $post_limit >= $this->prefs['limits']['per_post'] ) return true;
				}

				// Add / Replace post_id counter with an incremented value
				$limit[ $post_id ] = $post_limit+1;

				// Save
				if ( ! $this->is_main_type )
					mycred_update_user_meta( $user_id, 'mycred_comment_limit_post_' . $this->mycred_type, '', $limit );
				else
					mycred_update_user_meta( $user_id, 'mycred_comment_limit_post', '', $limit );

			}

			// Second we check daily limit
			if ( $this->prefs['limits']['per_day'] > 0 ) {
				$daily_limit = 0;

				// Grab limit
				if ( ! $this->is_main_type )
					$limit = mycred_get_user_meta( $user_id, 'mycred_comment_limit_day_' . $this->mycred_type, '', true );
				else
					$limit = mycred_get_user_meta( $user_id, 'mycred_comment_limit_day', '', true );

				// Apply default if none exist
				if ( empty( $limit ) ) $limit = array();

				// Check if todays date is in limit
				if ( array_key_exists( $today, $limit ) ) {
					$daily_limit = $limit[ $today ];

					// Limit is reached
					if ( $daily_limit >= $this->prefs['limits']['per_day'] ) return true;
				}
				// Today is not in limit array so we reset to remove other dates
				else {
					$limit = array();
				}

				// Add / Replace todays counter with an imcremented value
				$limit[ $today ] = $daily_limit+1;

				// Save
				if ( ! $this->is_main_type )
					mycred_update_user_meta( $user_id, 'mycred_comment_limit_day_' . $this->mycred_type, '', $limit );
				else
					mycred_update_user_meta( $user_id, 'mycred_comment_limit_day', '', $limit );

			}

			return false;

		}

		/**
		 * Preferences for Commenting Hook
		 * @since 0.1
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;

			if ( ! isset( $prefs['limits'] ) )
				$prefs['limits'] = array(
					'self_reply' => 0,
					'per_post'   => 10,
					'per_day'    => 0
				);

			if ( function_exists( 'dsq_is_installed' ) )
				echo '<p>' . $this->core->template_tags_general( __( '%plural% are only awarded when your website has been synced with the Disqus server!', 'mycred' ) ) . '</p>';

?>
<div class="hook-instance">
	<h3><?php _e( 'Approved Comments', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'approved' => 'creds' ) ); ?>"><?php _e( 'Member', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'approved' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'approved' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['approved']['creds'] ); ?>" class="form-control" />
				<span class="description"><?php _e( 'Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'approved' => 'author' ) ); ?>"><?php _e( 'Content Author', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'approved' => 'author' ) ); ?>" id="<?php echo $this->field_id( array( 'approved' => 'author' ) ); ?>" value="<?php echo $this->core->number( $prefs['approved']['author'] ); ?>" class="form-control" />
				<span class="description"><?php _e( 'Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'approved' => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'approved' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'approved' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['approved']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'comment' ) ); ?></span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<div class="checkbox">
					<label for="<?php echo $this->field_id( array( 'limits' => 'self_reply' ) ); ?>"><input type="checkbox" name="<?php echo $this->field_name( array( 'limits' => 'self_reply' ) ); ?>" id="<?php echo $this->field_id( array( 'limits' => 'self_reply' ) ); ?>" <?php checked( $prefs['limits']['self_reply'], 1 ); ?> value="1" /> <?php echo $this->core->template_tags_general( __( '%plural% is to be awarded even when comment authors reply to their own comment.', 'mycred' ) ); ?></label>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'limits' => 'per_post' ) ); ?>"><?php _e( 'Limit per post', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'limits' => 'per_post' ) ); ?>" id="<?php echo $this->field_id( array( 'limits' => 'per_post' ) ); ?>" value="<?php echo esc_attr( $prefs['limits']['per_post'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->core->template_tags_general( __( 'The number of comments per post that grants %_plural% to the comment author. Use zero for unlimited.', 'mycred' ) ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'limits' => 'per_day' ) ); ?>"><?php _e( 'Limit per day', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'limits' => 'per_day' ) ); ?>" id="<?php echo $this->field_id( array( 'limits' => 'per_day' ) ); ?>" value="<?php echo $prefs['limits']['per_day']; ?>" class="form-control" />
				<span class="description"><?php echo $this->core->template_tags_general( __( 'Number of comments per day that grants %_plural%. Use zero for unlimited.', 'mycred' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="hook-instance">
	<h3><?php _e( 'SPAM Comments', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'spam' => 'creds' ) ); ?>"><?php _e( 'Member', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'spam' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'spam' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['spam']['creds'] ); ?>" class="form-control" />
				<span class="description"><?php _e( 'Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'spam' => 'author' ) ); ?>"><?php _e( 'Content Author', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'spam' => 'author' ) ); ?>" id="<?php echo $this->field_id( array( 'spam' => 'author' ) ); ?>" value="<?php echo $this->core->number( $prefs['spam']['author'] ); ?>" class="form-control" />
				<span class="description"><?php _e( 'Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'spam' => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'spam' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'spam' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['spam']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'comment' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="hook-instance">
	<h3><?php _e( 'Trashed Comments', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'trash' => 'creds' ) ); ?>"><?php _e( 'Member', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'trash' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'trash' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['trash']['creds'] ); ?>" class="form-control" />
				<span class="description"><?php _e( 'Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'trash' => 'author' ) ); ?>"><?php _e( 'Content Author', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'trash' => 'author' ) ); ?>" id="<?php echo $this->field_id( array( 'trash' => 'author' ) ); ?>" value="<?php echo $this->core->number( $prefs['trash']['author'] ); ?>" class="form-control" />
				<span class="description"><?php _e( 'Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'trash' => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'trash' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'trash' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['trash']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'comment' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

		}

		/**
		 * Sanitise Preference
		 * @since 1.1.1
		 * @version 1.1
		 */
		function sanitise_preferences( $data ) {

			$new_data = $data;

			$new_data['limits']['per_post']   = ( ! empty( $data['limits']['per_post'] ) ) ? absint( $data['limits']['per_post'] ) : 0;
			$new_data['limits']['per_day']    = ( ! empty( $data['limits']['per_day'] ) ) ? absint( $data['limits']['per_day'] ) : 0;
			$new_data['limits']['self_reply'] = ( isset( $data['limits']['self_reply'] ) ) ? 1 : 0;

			return $new_data;

		}

	}
endif;
