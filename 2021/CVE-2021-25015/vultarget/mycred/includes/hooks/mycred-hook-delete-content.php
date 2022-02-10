<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Hook for deleting content
 * @since 1.7
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Hook_Delete_Content' ) ) :
	class myCRED_Hook_Delete_Content extends myCRED_Hook {

		/**
		 * Construct
		 */
		function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			$defaults = array(
				'post'    => array(
					'creds'  => 1,
					'log'    => '%plural% for deleted Post',
					'limit'  => '0/x'
				),
				'page'    => array(
					'creds'  => 1,
					'log'    => '%plural% for deleted Page',
					'limit'  => '0/x'
				)
			);

			if ( isset( $hook_prefs['deleted_content'] ) )
				$defaults = $hook_prefs['deleted_content'];

			parent::__construct( array(
				'id'       => 'deleted_content',
				'defaults' => $defaults
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.7
		 * @version 1.0
		 */
		public function run() {

			if ( EMPTY_TRASH_DAYS > 0 )
				add_action( 'trashed_post', array( $this, 'delete_content' ) );
			else
				add_action( 'before_delete_post', array( $this, 'delete_content' ) );

		}

		/**
		 * Delete Content Hook
		 * @since 1.7
		 * @version 1.1
		 */
		public function delete_content( $post_id ) {

			global $post_type;

			$post       = mycred_get_post( $post_id );

			$user_id    = $post->post_author;
			$post_type  = $post->post_type;

			// Check for exclusions
			if ( $this->core->exclude_user( $user_id ) === true ) return;

			// Make sure we award points other then zero
			if ( ! isset( $this->prefs[ $post_type ]['creds'] ) || empty( $this->prefs[ $post_type ]['creds'] ) || $this->prefs[ $post_type ]['creds'] == 0 ) return;

			// Prep
			$entry      = $this->prefs[ $post_type ]['log'];
			$data       = array( 'ref_type' => 'post' );
			$references = apply_filters( 'mycred_delete_hook_ref', 'deleted_content', $post, $this );

			// Make sure this is unique
			if ( $this->core->has_entry( $references, $post_id, $user_id, $data, $this->mycred_type ) ) return;

			// Check limit
			if ( ! $this->over_hook_limit( $post_type, $references, $user_id ) )
				$this->core->add_creds(
					$references,
					$user_id,
					$this->prefs[ $post_type ]['creds'],
					$entry,
					$post_id,
					$data,
					$this->mycred_type
				);

		}

		/**
		 * Preference for Delete Content Hook
		 * @since 1.7
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<h3><?php _e( 'Trashing Posts', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'post' => 'creds' ) ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'post' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'post' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['post']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'post' => 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'post' => 'limit' ) ), $this->field_id( array( 'post' => 'limit' ) ), $prefs['post']['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'post' => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'post' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'post' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['post']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="hook-instance">
	<h3><?php _e( 'Trashing Pages', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'page' => 'creds' ) ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'page' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'page' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['page']['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'page' => 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'page' => 'limit' ) ), $this->field_id( array( 'page' => 'limit' ) ), $prefs['page']['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'page' => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'page' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'page' => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['page']['log'] ); ?>" class="form-control" />
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

				// Start by checking if this post type should be excluded
				if ( ! $this->include_post_type( $post_type->name ) ) continue;

				// Points to award/deduct
				if ( isset( $prefs[ $post_type->name ]['creds'] ) )
					$_creds = $prefs[ $post_type->name ]['creds'];
				else
					$_creds = 0;

				// Log template
				if ( isset( $prefs[ $post_type->name ]['log'] ) )
					$_log = $prefs[ $post_type->name ]['log'];
				else
					$_log = '%plural% for deleted content';

				if ( isset( $prefs[ $post_type->name ]['limit'] ) )
					$_limit = $prefs[ $post_type->name ]['limit'];
				else
					$_limit = '0/x';

?>
<div class="hook-instance">
	<h3><?php printf( __( 'Trashing %s', 'mycred' ), $post_type->labels->name ); ?></h3>
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $post_type->name => 'creds' ) ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( $post_type->name => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( $post_type->name => 'creds' ) ); ?>" value="<?php echo $this->core->number( $_creds ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $post_type->name => 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( $post_type->name => 'limit' ) ), $this->field_id( array( $post_type->name => 'limit' ) ), $_limit ); ?>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( $post_type->name => 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( $post_type->name => 'log' ) ); ?>" id="<?php echo $this->field_id( array( $post_type->name => 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $_log ); ?>" class="form-control" />
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
		 * @since 1.7
		 * @version 1.0
		 */
		function sanitise_preferences( $data ) {

			if ( isset( $data['post']['limit'] ) && isset( $data['post']['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['post']['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['post']['limit'] = $limit . '/' . $data['post']['limit_by'];
				unset( $data['post']['limit_by'] );
			}

			if ( isset( $data['page']['limit'] ) && isset( $data['page']['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['page']['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['page']['limit'] = $limit . '/' . $data['page']['limit_by'];
				unset( $data['page']['limit_by'] );
			}

			// Get all not built-in post types (excludes posts, pages, media)
			$post_type_args = array(
				'public'   => true,
				'_builtin' => false
			);
			$post_types = get_post_types( $post_type_args, 'objects', 'and' ); 

			foreach ( $post_types as $post_type ) {

				// Start by checking if this post type should be excluded
				if ( ! $this->include_post_type( $post_type->name ) ) continue;

				if ( isset( $data[ $post_type->name ]['limit'] ) && isset( $data[ $post_type->name ]['limit_by'] ) ) {
					$limit = sanitize_text_field( $data[ $post_type->name ]['limit'] );
					if ( $limit == '' ) $limit = 0;
					$data[ $post_type->name ]['limit'] = $limit . '/' . $data[ $post_type->name ]['limit_by'];
					unset( $data[ $post_type->name ]['limit_by'] );
				}

			}

			return $data;

		}

	}
endif;
