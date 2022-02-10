<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Register Hook
 * @since 1.1
 * @version 1.1
 */
add_filter( 'mycred_setup_hooks', 'mycred_register_wp_favorite_posts_hook', 100 );
function mycred_register_wp_favorite_posts_hook( $installed ) {

	if ( ! function_exists( 'wp_favorite_posts' ) ) return $installed;

	$installed['wpfavorite'] = array(
		'title'         => __( 'WP Favorite Posts', 'mycred' ),
		'description'   => __( 'Awards %_plural% for users adding posts to their favorites.', 'mycred' ),
		'documentation' => 'http://codex.mycred.me/hooks/wp-favorite-posts-actions/',
		'callback'      => array( 'myCRED_Hook_WPFavorite' )
	);

	return $installed;

}

/**
 * WP Favorite Hook
 * @since 1.1
 * @version 1.1
 */
add_action( 'mycred_load_hooks', 'mycred_load_wp_favorite_posts_hook', 100 );
function mycred_load_wp_favorite_posts_hook() {

	// If the hook has been replaced or if plugin is not installed, exit now
	if ( class_exists( 'myCRED_Hook_WPFavorite' ) || ! function_exists( 'wp_favorite_posts' ) ) return;

	class myCRED_Hook_WPFavorite extends myCRED_Hook {

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'wpfavorite',
				'defaults' => array(
					'add'    => array(
						'creds' => 1,
						'log'   => '%plural% for adding a post as favorite',
						'limit' => '0/x'
					),
					'added'    => array(
						'creds' => 1,
						'log'   => '%plural% for your post being added to favorite',
						'limit' => '0/x'
					),
					'remove' => array(
						'creds' => 1,
						'log'   => '%plural% deduction for removing a post from favorites'
					),
					'removed' => array(
						'creds' => 1,
						'log'   => '%plural% deduction for post removed from favorites'
					)
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.1
		 * @version 1.0.1
		 */
		public function run() {

			add_action( 'wpfp_after_add',    array( $this, 'add_favorite' ) );
			add_action( 'wpfp_after_remove', array( $this, 'remove_favorite' ) );

		}

		/**
		 * Add Favorite
		 * @since 1.1
		 * @version 1.2
		 */
		public function add_favorite( $post_id ) {

			// Must be logged in
			if ( ! is_user_logged_in() ) return;

			$post    = mycred_get_post( $post_id );
			$user_id = get_current_user_id();

			if ( $user_id != $post->post_author ) {

				// Award the user adding to favorite
				if ( $this->prefs['add']['creds'] != 0 && ! $this->core->exclude_user( $user_id ) ) {

					// Limit
					if ( ! $this->over_hook_limit( 'add', 'add_favorite_post', $user_id ) ) {

						// Make sure this is unique event
						if ( ! $this->core->has_entry( 'add_favorite_post', $post_id, $user_id ) ) {

							// Execute
							$this->core->add_creds(
								'add_favorite_post',
								$user_id,
								$this->prefs['add']['creds'],
								$this->prefs['add']['log'],
								$post_id,
								array( 'ref_type' => 'post' ),
								$this->mycred_type
							);

						}

					}

				}

				// Award post author for being added to favorite
				if ( $this->prefs['added']['creds'] != 0 && ! $this->core->exclude_user( $post->post_author ) ) {

					// Limit
					if ( ! $this->over_hook_limit( 'added', 'add_favorite_post', $post->post_author ) ) {

						// Make sure this is unique event
						if ( ! $this->core->has_entry( 'favorited_post', $post_id, $post->post_author ) ) {

							// Execute
							$this->core->add_creds(
								'favorited_post',
								$post->post_author,
								$this->prefs['added']['creds'],
								$this->prefs['added']['log'],
								$post_id,
								array( 'ref_type' => 'post', 'by' => $user_id ),
								$this->mycred_type
							);

						}

					}

				}

			}

		}

		/**
		 * Remove Favorite
		 * @since 1.1
		 * @version 1.2
		 */
		public function remove_favorite( $post_id ) {

			// Must be logged in
			if ( ! is_user_logged_in() ) return;

			$post    = mycred_get_post( $post_id );
			$user_id = get_current_user_id();

			if ( $user_id != $post->post_author ) {

				if ( $this->prefs['remove']['creds'] != 0 && ! $this->core->exclude_user( $user_id ) ) {

					if ( ! $this->core->has_entry( 'favorite_post_removed', $post_id, $user_id ) ) {

						$this->core->add_creds(
							'favorite_post_removed',
							$user_id,
							$this->prefs['remove']['creds'],
							$this->prefs['remove']['log'],
							$post_id,
							array( 'ref_type' => 'post' ),
							$this->mycred_type
						);

					}

				}

				if ( $this->prefs['removed']['creds'] != 0 && ! $this->core->exclude_user( $post->post_author ) ) {

					if ( ! $this->core->has_entry( 'favorite_post_removal', $post_id, $post->post_author ) ) {

						$this->core->add_creds(
							'favorite_post_removal',
							$post->post_author,
							$this->prefs['removed']['creds'],
							$this->prefs['removed']['log'],
							$post_id,
							array( 'ref_type' => 'post', 'by' => $user_id ),
							$this->mycred_type
						);

					}

				}

			}

		}

		/**
		 * Preferences for WP-Polls
		 * @since 1.1
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<h3><?php _e( 'Adding Content to Favorites', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'add' => 'creds' ) ); ?>"><?php _e( 'Member', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'add' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'add' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['add']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'add', 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'add', 'limit' ) ), $this->field_id( array( 'add', 'limit' ) ), $prefs['add']['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'added' => 'creds' ) ); ?>"><?php _e( 'Content Author', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'added' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'added' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['added']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'added', 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'added', 'limit' ) ), $this->field_id( array( 'added', 'limit' ) ), $prefs['added']['limit'] ); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'add' => 'log' ) ); ?>"><?php _e( 'Member Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'add' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'add' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['add']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'added' => 'log' ) ); ?>"><?php _e( 'Content Author Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'added' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'added' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['added']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="hook-instance">
	<h3><?php _e( 'Removing Content from Favorites', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'remove' => 'creds' ) ); ?>"><?php _e( 'Member', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'remove' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'remove' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['remove']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'remove' => 'log' ) ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'remove' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'remove' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['remove']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'removed' => 'creds' ) ); ?>"><?php _e( 'Content Author', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'removed' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'removed' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['removed']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-8 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'removed' => 'log' ) ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'removed' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'removed' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['removed']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
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

			if ( isset( $data['add']['limit'] ) && isset( $data['add']['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['add']['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['add']['limit'] = $limit . '/' . $data['add']['limit_by'];
				unset( $data['add']['limit_by'] );
			}

			if ( isset( $data['added']['limit'] ) && isset( $data['added']['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['added']['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['added']['limit'] = $limit . '/' . $data['added']['limit_by'];
				unset( $data['added']['limit_by'] );
			}

			return $data;

		}

	}

}
