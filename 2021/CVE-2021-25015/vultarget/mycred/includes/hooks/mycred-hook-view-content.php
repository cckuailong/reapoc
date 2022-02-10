<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hook for viewing content
 * @since 1.5.1
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Hook_View_Contents' ) ) :
	class myCRED_Hook_View_Contents extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'post'    => array(
					'creds'   => 1,
					'log'     => '%plural% for viewing a post',
					'acreds'  => 1,
					'limit'   => '0/x',
					'alog'    => '%plural% for view of your post',
					'visitor' => 0,
					'alimit'  => '0/x'
				),
				'page'    => array(
					'creds'   => 1,
					'log'     => '%plural% for viewing a page',
					'acreds'  => 1,
					'limit'   => '0/x',
					'alog'    => '%plural% for view of your page',
					'visitor' => 0,
					'alimit'  => '0/x'
				)
			);

			if ( isset( $hook_prefs['view_contents'] ) )
				$defaults = $hook_prefs['view_contents'];

			parent::__construct( array(
				'id'       => 'view_contents',
				'defaults' => $defaults
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.5.1
		 * @version 1.0.1
		 */
		public function run() {

			// First instance where we can safely use conditional template tags
			add_action( 'template_redirect', array( $this, 'content_loading' ), 999 );

			add_filter( 'mycred_hook_limit_query', array( $this, 'view_content_query' ), 10, 7 );

		}

		/**
		 * Content Loaded
		 * @since 1.5.1
		 * @version 1.2
		 */
		public function content_loading() {

			// Only applicable on single post type view by logged in users
			if ( ! is_singular() || ! is_user_logged_in() ) return;

			global $post;

			$user_id    = get_current_user_id();
			$pay_author = true;
			$data       = array( 'ref_type' => 'post' );

			// Post author can not generate points for themselves
			if ( $post->post_author == $user_id ) return;

			// Make sure this post type award points. Any amount but zero.
			if ( isset( $this->prefs[ $post->post_type ]['creds'] ) && $this->prefs[ $post->post_type ]['creds'] != 0 && apply_filters( 'mycred_view_content', true, $this ) === true ) {

				// Make sure we are not excluded
				if ( ! $this->core->exclude_user( $user_id ) ) {

					// Enforce limit and make sure users only get points once per unique post
					if ( ! $this->over_hook_limit( $post->post_type, 'view_content', $user_id ) && ! $this->core->has_entry( 'view_content', $post->ID, $user_id, $data, $this->mycred_type ) ) {

						$this->core->add_creds(
							'view_content',
							$user_id,
							$this->prefs[ $post->post_type ]['creds'],
							$this->prefs[ $post->post_type ]['log'],
							$post->ID,
							$data,
							$this->mycred_type
						);

					}

					// If the visitor does not get points, neither does the author
					else $pay_author = false;

				}

			}

			// Make sure this post type award points to the author. Any amount but zero.
			if ( isset( $this->prefs[ $post->post_type ]['acreds'] ) && $this->prefs[ $post->post_type ]['acreds'] != 0 && apply_filters( 'mycred_view_content_author', $pay_author, $this ) === true ) {

				// No payout for viewing our own content
				if ( ! $this->core->exclude_user( $post->post_author ) ) {

					$data['cui'] = $user_id;

					// Limit
					if ( ! $this->over_hook_limit( $post->post_type, 'view_content_author', $post->post_author ) )
						$this->core->add_creds(
							'view_content_author',
							$post->post_author,
							$this->prefs[ $post->post_type ]['acreds'],
							$this->prefs[ $post->post_type ]['alog'],
							$post->ID,
							$data,
							$this->mycred_type
						);

				}

			}

		}

		/**
		 * Preference for read content hook
		 * @since 1.5.1
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<h3><?php _e( 'Viewing Posts', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'post' => 'creds' ) ); ?>"><?php _e( 'Member', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'post' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'post' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['post']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'post', 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'post', 'limit' ) ), $this->field_id( array( 'post', 'limit' ) ), $prefs['post']['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'post' => 'acreds' ) ); ?>"><?php _e( 'Content Author', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'post' => 'acreds' ) ); ?>" id="<?php echo $this->field_id( array( 'post' => 'acreds' ) ); ?>" value="<?php echo $this->core->number( $prefs['post']['acreds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'post', 'alimit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'post', 'alimit' ) ), $this->field_id( array( 'post', 'alimit' ) ), $prefs['post']['alimit'] ); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'post' => 'log' ) ); ?>"><?php _e( 'Member Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'post' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'post' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['post']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'post' => 'alog' ) ); ?>"><?php _e( 'Content Author Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'post' => 'alog' ) ); ?>" id="<?php echo $this->field_id( array( 'post' => 'alog' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['post']['alog'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="hook-instance">
	<h3><?php _e( 'Viewing Pages', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'page' => 'creds' ) ); ?>"><?php _e( 'Member', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'page' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'page' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['page']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'page', 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'page', 'limit' ) ), $this->field_id( array( 'page', 'limit' ) ), $prefs['page']['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'page' => 'acreds' ) ); ?>"><?php _e( 'Content Author', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'page' => 'acreds' ) ); ?>" id="<?php echo $this->field_id( array( 'page' => 'acreds' ) ); ?>" value="<?php echo $this->core->number( $prefs['page']['acreds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'page', 'alimit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'page', 'alimit' ) ), $this->field_id( array( 'page', 'alimit' ) ), $prefs['page']['alimit'] ); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'page' => 'log' ) ); ?>"><?php _e( 'Member Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'page' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'page' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['page']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'page' => 'alog' ) ); ?>"><?php _e( 'Content Author Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'page' => 'alog' ) ); ?>" id="<?php echo $this->field_id( array( 'page' => 'alog' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['page']['alog'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

			// Get all not built-in post types (excludes posts, pages, media)
			$post_type_args = array(
				'public'   => true,
				'_builtin' => false
			);
			$post_types = get_post_types( $post_type_args, 'objects', 'and' ); 

			foreach ( $post_types as $post_type ) {

				// Points to award/deduct
				if ( isset( $prefs[ $post_type->name ]['creds'] ) )
					$_creds = $prefs[ $post_type->name ]['creds'];
				else
					$_creds = 0;

				if ( isset( $prefs[ $post_type->name ]['limit'] ) )
					$limit = $prefs[ $post_type->name ]['limit'];
				else
					$limit = '0/x';

				// Log template
				if ( isset( $prefs[ $post_type->name ]['log'] ) )
					$_log = $prefs[ $post_type->name ]['log'];
				else
					$_log = '%plural% for viewing ' . $post_type->labels->name;

				// Points to award/deduct
				if ( isset( $prefs[ $post_type->name ]['acreds'] ) )
					$_acreds = $prefs[ $post_type->name ]['acreds'];
				else
					$_acreds = 0;

				if ( isset( $prefs[ $post_type->name ]['alimit'] ) )
					$alimit = $prefs[ $post_type->name ]['alimit'];
				else
					$alimit = '0/x';

				// Log template
				if ( isset( $prefs[ $post_type->name ]['alog'] ) )
					$_alog = $prefs[ $post_type->name ]['alog'];
				else
					$_alog = '%plural% for view of your ' . $post_type->labels->name;

?>
<div class="hook-instance">
	<h3><?php printf( __( 'Viewing %s', 'mycred' ), $post_type->labels->name ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $post_type->name => 'creds' ) ); ?>"><?php _e( 'Member', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( $post_type->name => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( $post_type->name => 'creds' ) ); ?>" value="<?php echo $this->core->number( $_creds ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $post_type->name, 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( $post_type->name, 'limit' ) ), $this->field_id( array( $post_type->name, 'limit' ) ), $limit ); ?>
			</div>
		</div>
		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $post_type->name => 'acreds' ) ); ?>"><?php _e( 'Content Author', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( $post_type->name => 'acreds' ) ); ?>" id="<?php echo $this->field_id( array( $post_type->name => 'acreds' ) ); ?>" value="<?php echo $this->core->number( $_acreds ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $post_type->name, 'alimit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( $post_type->name, 'alimit' ) ), $this->field_id( array( $post_type->name, 'alimit' ) ), $alimit ); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $post_type->name => 'log' ) ); ?>"><?php _e( 'Member', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( $post_type->name => 'log' ) ); ?>" id="<?php echo $this->field_id( array( $post_type->name => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $_log ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $post_type->name => 'alog' ) ); ?>"><?php _e( 'Content Author', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( $post_type->name => 'alog' ) ); ?>" id="<?php echo $this->field_id( array( $post_type->name => 'alog' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $_alog ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

			}

		}

		/**
		 * Sanitise Preferences
		 * @since 1.6
		 * @version 1.0.1
		 */
		function sanitise_preferences( $data ) {

			if ( isset( $data['post']['limit'] ) && isset( $data['post']['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['post']['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['post']['limit'] = $limit . '/' . $data['post']['limit_by'];
				unset( $data['post']['limit_by'] );
			}

			if ( isset( $data['post']['alimit'] ) && isset( $data['post']['alimit_by'] ) ) {
				$limit = sanitize_text_field( $data['post']['alimit'] );
				if ( $limit == '' ) $limit = 0;
				$data['post']['alimit'] = $limit . '/' . $data['post']['alimit_by'];
				unset( $data['post']['alimit_by'] );
			}

			if ( isset( $data['page']['limit'] ) && isset( $data['page']['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['page']['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['page']['limit'] = $limit . '/' . $data['page']['limit_by'];
				unset( $data['page']['limit_by'] );
			}

			if ( isset( $data['page']['alimit'] ) && isset( $data['page']['alimit_by'] ) ) {
				$limit = sanitize_text_field( $data['page']['alimit'] );
				if ( $limit == '' ) $limit = 0;
				$data['page']['alimit'] = $limit . '/' . $data['page']['alimit_by'];
				unset( $data['page']['alimit_by'] );
			}

			$post_type_args = array(
				'public'   => true,
				'_builtin' => false
			);
			$post_types = get_post_types( $post_type_args, 'objects', 'and' ); 

			foreach ( $post_types as $post_type ) {

				if ( isset( $data[ $post_type->name ]['limit'] ) && isset( $data[ $post_type->name ]['limit_by'] ) ) {
					$limit = sanitize_text_field( $data[ $post_type->name ]['limit'] );
					if ( $limit == '' ) $limit = 0;
					$data[ $post_type->name ]['limit'] = $limit . '/' . $data[ $post_type->name ]['limit_by'];
					unset( $data[ $post_type->name ]['limit_by'] );
				}

				if ( isset( $data[ $post_type->name ]['alimit'] ) && isset( $data[ $post_type->name ]['alimit_by'] ) ) {
					$limit = sanitize_text_field( $data[ $post_type->name ]['alimit'] );
					if ( $limit == '' ) $limit = 0;
					$data[ $post_type->name ]['alimit'] = $limit . '/' . $data[ $post_type->name ]['alimit_by'];
					unset( $data[ $post_type->name ]['alimit_by'] );
				}

			}

			return $data;

		}

		public function view_content_query( $query, $instance, $reference, $user_id, $ref_id, $wheres ) {

			global $wpdb, $mycred_log_table;

			if ( 'view_content' == $reference || 'view_content_author' == $reference ) {
				$query = "SELECT COUNT(l.id) FROM {$mycred_log_table} as l JOIN {$wpdb->prefix}posts as p on l.ref_id = p.ID WHERE p.post_type = '{$instance}' AND {$wheres}";
			}

			return $query;
		}

	}
endif;
