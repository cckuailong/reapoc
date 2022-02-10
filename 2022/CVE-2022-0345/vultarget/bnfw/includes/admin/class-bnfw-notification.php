<?php

/**
 * BNFW Notification.
 *
 * @since 1.0
 */
class BNFW_Notification {

	const POST_TYPE = 'bnfw_notification';
	const META_KEY_PREFIX = 'bnfw_';
	const TEST_MAIL_ARG = 'test-mail';

	/**
	 *
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'do_meta_boxes', array( $this, 'remove_meta_boxes' ) );
		add_action( 'add_meta_boxes_' . self::POST_TYPE, array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_data' ) );
		add_action( 'edit_form_top', array( $this, 'admin_notices' ) );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_gutenberg_for_notification' ), 10, 2 );

		add_filter( 'bulk_actions-edit-bnfw_notification', array( $this, 'add_custom_edit_action' ) );
		add_filter( 'handle_bulk_actions-edit-bnfw_notification', array( $this, 'handle_custom_edit_action' ), 10, 3 );

		// Custom row actions.
		add_filter( 'post_row_actions', array( $this, 'custom_row_actions' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );

		// Custom columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'columns_header' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'custom_column_row' ), 10, 2 );

		// Enqueue scripts/styles and disables autosave for this post type.
		add_action( 'admin_enqueue_scripts', array( $this, 'is_assets_needed' ) );

		add_action( 'admin_notices', array( $this, 'show_help_notice' ) );

		add_action('admin_print_scripts',array($this,'gutenberg_flag'));
	}

	/**
	* Flag variable to check if gutenberge is active
	* added fix for gutenberge
	*
	* @since 1.3
	*/
	public function gutenberg_flag(){
		$bnfw = BNFW::Factory();
		?>
		<script type="text/javascript">
			var bnfw_gutenberge_is_active = <?php echo ($bnfw->is_gutenberg_active())? 'true;' : 'false;'; ?>
		</script>
		<?php
	}


	/**
	 * Register bnfw_notification custom post type.
	 *
	 * @since 1.0
	 */
	public function register_post_type() {
		register_post_type( self::POST_TYPE, array(
			'labels'            => array(
				'name'               => esc_html__( 'Notifications', 'bnfw' ),
				'singular_name'      => esc_html__( 'Notification', 'bnfw' ),
				'add_new'            => esc_html__( 'Add New', 'bnfw' ),
				'menu_name'          => esc_html__( 'Notifications', 'bnfw' ),
				'name_admin_bar'     => esc_html__( 'Notifications', 'bnfw' ),
				'add_new_item'       => esc_html__( 'Add New Notification', 'bnfw' ),
				'edit_item'          => esc_html__( 'Edit Notification', 'bnfw' ),
				'new_item'           => esc_html__( 'New Notification', 'bnfw' ),
				'view_item'          => esc_html__( 'View Notification', 'bnfw' ),
				'search_items'       => esc_html__( 'Search Notifications', 'bnfw' ),
				'not_found'          => esc_html__( 'No Notifications found', 'bnfw' ),
				'not_found_in_trash' => esc_html__( 'No Notifications found in trash', 'bnfw' ),
				'all_items'          => esc_html__( 'All Notifications', 'bnfw' ),
			),
			'public'            => false,
			'show_in_nav_menus' => true,
			'show_in_admin_bar' => true,
			'has_archive'       => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'menu_icon'         => 'dashicons-email-alt',
			'menu_position'     => 101,
			'rewrite'           => false,
			'map_meta_cap'      => false,
			'capabilities'      => array(

				// meta caps (don't assign these to roles)
				'edit_post'              => 'bnfw',
				'read_post'              => 'bnfw',
				'delete_post'            => 'bnfw',

				// primitive/meta caps
				'create_posts'           => 'bnfw',

				// primitive caps used outside of map_meta_cap()
				'edit_posts'             => 'bnfw',
				'edit_others_posts'      => 'bnfw',
				'publish_posts'          => 'bnfw',
				'read_private_posts'     => 'bnfw',

				// primitive caps used inside of map_meta_cap()
				'read'                   => 'bnfw',
				'delete_posts'           => 'bnfw',
				'delete_private_posts'   => 'bnfw',
				'delete_published_posts' => 'bnfw',
				'delete_others_posts'    => 'bnfw',
				'edit_private_posts'     => 'bnfw',
				'edit_published_posts'   => 'bnfw',
			),

			// What features the post type supports.
			'supports'          => array(
				'title',
			),
		) );
	}

	/**
	 * Remove unwanted meta boxes.
	 *
	 * @since 1.0
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'submitdiv', self::POST_TYPE, 'side' );
		remove_meta_box( 'slugdiv', self::POST_TYPE, 'normal' );
	}

	/**
	 * Add meta box to the post editor screen.
	 *
	 * @since 1.0
	 */
	public function add_meta_boxes() {
		global $post;

		add_meta_box(
			'bnfw-post-notification',                      // Unique ID
			esc_html__( 'Notification Settings', 'bnfw' ), // Title
			array( $this, 'render_settings_meta_box' ),   // Callback function
			self::POST_TYPE,                              // Admin page (or post type)
			'normal',                                     // Context
			'default'
		);

		add_meta_box(
			'bnfw_submitdiv',
			__( 'Save Notification', 'bnfw' ),
			array( $this, 'render_submitdiv' ),
			self::POST_TYPE,
			'side',
			'core'
		);

		if ( self::POST_TYPE !== get_post_type( $post ) ) {
			return;
		}

		do_action( 'bnfw_after_metaboxes', $this->read_settings( $post->ID ) );
	}

	/**
	 * Disable Gutenberg for notifications.
	 *
	 * @param bool $is_enabled Is Gutenberg enabled?
	 * @param string $post_type Post Type.
	 *
	 * @return bool Should Gutenberg be enabled?
	 */
	public function disable_gutenberg_for_notification( $is_enabled, $post_type ) {
		if ( self::POST_TYPE === $post_type ) {
			return false;
		}

		return $is_enabled;
	}

	/**
	 * Render the settings meta box.
	 *
	 * @since 1.0
	 *
	 * @param WP_Post $post
	 */
	public function render_settings_meta_box( $post ) {
		global $wp_version;

		wp_nonce_field( self::POST_TYPE, self::POST_TYPE . '_nonce' );

		$setting = $this->read_settings( $post->ID );
		?>
		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="notification"><?php esc_html_e( 'Notification For', 'bnfw' ); ?></label>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'E.g. If you select "New Post Published" from the list on the right, this notification will be sent when a new post is published.', 'bnfw' ); ?></p></div>
				</th>
				<td>
					<select name="notification" id="notification" class="select2"
							data-placeholder="<?php _e( 'Select the notification type', 'bnfw' ); ?>" style="width:75%">
						<optgroup label="<?php _e( 'Admin', 'bnfw' ); ?>">
							<option
								value="admin-user" <?php selected( 'admin-user', $setting['notification'] ); ?>><?php esc_html_e( 'New User Registration - For Admin', 'bnfw' ); ?></option>
							<option
								value="admin-password" <?php selected( 'admin-password', $setting['notification'] ); ?>><?php esc_html_e( 'User Lost Password - For Admin', 'bnfw' ); ?></option>
							<option
								value="admin-password-changed" <?php selected( 'admin-password-changed', $setting['notification'] ); ?>><?php esc_html_e( 'Password Changed - For Admin', 'bnfw' ); ?></option>
							<option
								value="admin-email-changed" <?php selected( 'admin-email-changed', $setting['notification'] ); ?>><?php esc_html_e( 'User Email Changed - For Admin', 'bnfw' ); ?></option>
							<option
								value="admin-role" <?php selected( 'admin-role', $setting['notification'] ); ?>><?php esc_html_e( 'User Role Changed - For Admin', 'bnfw' ); ?></option>
							<option
								value="admin-user-login" <?php selected( 'admin-user-login', $setting['notification'] ); ?>><?php esc_html_e( 'User Logged In - For Admin', 'bnfw' ); ?></option>
							<option
								value="core-updated" <?php selected( 'core-updated', $setting['notification'] ); ?>><?php esc_html_e( 'WordPress Core Automatic Background Updates', 'bnfw' ); ?></option>

							<?php if ( version_compare( $wp_version, '4.9.6' ) >= 0 ) : ?>
								<option value="uc-export-data" <?php selected( 'uc-export-data', $setting['notification'] ); ?>>
									<?php esc_html_e( 'Privacy - Confirm Action: Export Data Request - For Admin', 'bnfw' ); ?>
								</option>

								<option value="uc-erase-data" <?php selected( 'uc-erase-data', $setting['notification'] ); ?>>
									<?php esc_html_e( 'Privacy - Confirm Action: Erase Data Request - For Admin', 'bnfw' ); ?>
								</option>
							<?php endif; ?>

							<?php do_action( 'bnfw_after_default_notifications', $setting ); ?>
						</optgroup>
						<?php do_action( 'bnfw_after_default_notifications_optgroup', $setting ); ?>

						<optgroup label="<?php _e( 'Transactional', 'bnfw' ); ?>">
							<option
								value="new-user" <?php selected( 'new-user', $setting['notification'] ); ?>><?php esc_html_e( 'New User Registration - For User', 'bnfw' ); ?></option>
							<option
								value="welcome-email" <?php selected( 'welcome-email', $setting['notification'] ); ?>><?php esc_html_e( 'New User - Post-registration Email', 'bnfw' ); ?></option>
							<option
								value="user-password" <?php selected( 'user-password', $setting['notification'] ); ?>><?php esc_html_e( 'User Lost Password - For User', 'bnfw' ); ?></option>
							<option
								value="password-changed" <?php selected( 'password-changed', $setting['notification'] ); ?>><?php esc_html_e( 'Password Changed - For User', 'bnfw' ); ?></option>
							<option value="email-changing" <?php selected( 'email-changing', $setting['notification'] ); ?>>
								<?php esc_html_e( 'User Email Changed Confirmation - For User', 'bnfw' ); ?>
							</option>
							<option
								value="email-changed" <?php selected( 'email-changed', $setting['notification'] ); ?>><?php esc_html_e( 'User Email Changed - For User', 'bnfw' ); ?></option>
							<option
								value="user-role" <?php selected( 'user-role', $setting['notification'] ); ?>><?php esc_html_e( 'User Role Changed - For User', 'bnfw' ); ?></option>
							<option
								value="user-login" <?php selected( 'user-login', $setting['notification'] ); ?>><?php esc_html_e( 'User Logged In - For User', 'bnfw' ); ?></option>
							<option
								value="reply-comment" <?php selected( 'reply-comment', $setting['notification'] ); ?>><?php esc_html_e( 'Comment Reply', 'bnfw' ); ?></option>

							<?php if ( version_compare( $wp_version, '4.9.6' ) >= 0 ) : ?>
								<option value="ca-export-data" <?php selected( 'ca-export-data', $setting['notification'] ); ?>>
									<?php esc_html_e( 'Privacy - Confirm Action: Export Data Request - For User', 'bnfw' ); ?>
								</option>

								<option value="ca-erase-data" <?php selected( 'ca-erase-data', $setting['notification'] ); ?>>
									<?php esc_html_e( 'Privacy - Confirm Action: Erase Data Request - For User', 'bnfw' ); ?>
								</option>

								<option value="data-export" <?php selected( 'data-export', $setting['notification'] ); ?>>
									<?php esc_html_e( 'Privacy - Data Export - For User', 'bnfw' ); ?>
								</option>

								<option value="data-erased" <?php selected( 'data-erased', $setting['notification'] ); ?>>
									<?php esc_html_e( 'Privacy - Data Erased - For User', 'bnfw' ); ?>
								</option>
							<?php endif; ?>

							<?php do_action( 'bnfw_after_transactional_notifications', $setting ); ?>
						</optgroup>
						<?php do_action( 'bnfw_after_transactional_notifications_optgroup', $setting ); ?>

						<optgroup label="Posts">
							<option
								value="new-post" <?php selected( 'new-post', $setting['notification'] ); ?>><?php esc_html_e( 'New Post Published', 'bnfw' ); ?></option>
							<option
								value="update-post" <?php selected( 'update-post', $setting['notification'] ); ?>><?php esc_html_e( 'Post Updated', 'bnfw' ); ?></option>
							<option
								value="pending-post" <?php selected( 'pending-post', $setting['notification'] ); ?>><?php esc_html_e( 'Post Pending Review', 'bnfw' ); ?></option>
							<option
								value="private-post" <?php selected( 'private-post', $setting['notification'] ); ?>><?php esc_html_e( 'New Private Post', 'bnfw' ); ?></option>
							<option
								value="future-post" <?php selected( 'future-post', $setting['notification'] ); ?>><?php esc_html_e( 'Post Scheduled', 'bnfw' ); ?></option>
								<option
								value="trash-post" <?php selected( 'trash-post', $setting['notification'] ); ?>><?php esc_html_e( 'Published Post Moved to Trash', 'bnfw' ); ?></option>
							<option value="new-comment" <?php selected( 'new-comment', $setting['notification'] ); ?>>
								<?php esc_html_e( 'New Comment', 'bnfw' ); ?>
							</option>
							<option value="moderate-post-comment" <?php selected( 'moderate-post-comment', $setting['notification'] ); ?>>
								<?php esc_html_e( 'New Comment Awaiting Moderation', 'bnfw' ); ?>
							</option>
							<option value="approve-post-comment" <?php selected( 'approve-post-comment', $setting['notification'] ); ?>>
								<?php esc_html_e( 'Post - Comment Approved', 'bnfw' ); ?>
							</option>
							<option
								value="newterm-category" <?php selected( 'newterm-category', $setting['notification'] ); ?>><?php esc_html_e( 'New Category', 'bnfw' ); ?></option>
							<option
								value="newterm-post_tag" <?php selected( 'newterm-post_tag', $setting['notification'] ); ?>><?php esc_html_e( 'New Tag', 'bnfw' ); ?></option>
							<option
								value="new-trackback" <?php selected( 'new-trackback', $setting['notification'] ); ?>><?php esc_html_e( 'New Trackback', 'bnfw' ); ?></option>
							<option
								value="new-pingback" <?php selected( 'new-pingback', $setting['notification'] ); ?>><?php esc_html_e( 'New Pingback', 'bnfw' ); ?></option>
							<?php do_action( 'bnfw_after_notification_options', 'post', 'Post', $setting ); ?>
						</optgroup>
						<?php do_action( 'bnfw_after_notification_options_optgroup', 'post', 'Post', $setting ); ?>

						<optgroup label="Page">
							<option
								value="new-page" <?php selected( 'new-page', $setting['notification'] ); ?>><?php esc_html_e( 'New Page Published', 'bnfw' ); ?></option>
							<option
								value="update-page" <?php selected( 'update-page', $setting['notification'] ); ?>><?php esc_html_e( 'Page Updated', 'bnfw' ); ?></option>
							<option
								value="pending-page" <?php selected( 'pending-page', $setting['notification'] ); ?>><?php esc_html_e( 'Page Pending Review', 'bnfw' ); ?></option>
							<option
								value="private-page" <?php selected( 'private-page', $setting['notification'] ); ?>><?php esc_html_e( 'New Private Page', 'bnfw' ); ?></option>
							<option
								value="future-page" <?php selected( 'future-page', $setting['notification'] ); ?>><?php esc_html_e( 'Page Scheduled', 'bnfw' ); ?></option>
							<option
								value="comment-page" <?php selected( 'comment-page', $setting['notification'] ); ?>><?php esc_html_e( 'Page - New Comment', 'bnfw' ); ?></option>
							<option value="moderate-page-comment" <?php selected( 'moderate-page-comment', $setting['notification'] ); ?>>
								<?php esc_html_e( 'Page - New Comment Awaiting Moderation', 'bnfw' ); ?>
							</option>
                                                        <option value="approve-page-comment" <?php selected( 'approve-page-comment', $setting['notification'] ); ?>>
								<?php esc_html_e( 'Page - Comment Approved', 'bnfw' ); ?>
							</option>
							<option
								value="commentreply-page" <?php selected( 'commentreply-page', $setting['notification'] ); ?>><?php esc_html_e( 'Page - Comment Reply', 'bnfw' ); ?></option>
							<?php do_action( 'bnfw_after_notification_options', 'page', 'Page', $setting ); ?>
						</optgroup>
						<?php do_action( 'bnfw_after_notification_options_optgroup', 'page', 'Page', $setting ); ?>

						<optgroup label="Media">
							<option
								value="new-media" <?php selected( 'new-media', $setting['notification'] ); ?>><?php esc_html_e( 'New Media Published', 'bnfw' ); ?></option>
							<option
								value="update-media" <?php selected( 'update-media', $setting['notification'] ); ?>><?php esc_html_e( 'Media Updated', 'bnfw' ); ?></option>
							<option
								value="comment-attachment" <?php selected( 'comment-attachment', $setting['notification'] ); ?>><?php esc_html_e( 'Media - New Comment', 'bnfw' ); ?></option>
                                                        <option value="approve-attachment-comment" <?php selected( 'approve-attachment-comment', $setting['notification'] ); ?>>
								<?php esc_html_e( 'Media - Comment Approved', 'bnfw' ); ?>
							</option>
                                                        <option value="moderate-attachment-comment" <?php selected( 'moderate-attachment-comment', $setting['notification'] ); ?>>
								<?php esc_html_e( 'Media - New Comment Awaiting Moderation', 'bnfw' ); ?></option>
                                                        <option
								value="commentreply-attachment" <?php selected( 'commentreply-attachment', $setting['notification'] ); ?>><?php esc_html_e( 'Media - Comment Reply', 'bnfw' ); ?></option>
							<?php do_action( 'bnfw_after_notification_options', 'media', 'Media', $setting ); ?>
						</optgroup>
						<?php do_action( 'bnfw_after_notification_options_optgroup', 'media', 'Media', $setting ); ?>

						<?php
						$types = apply_filters( 'bnfw_notification_dropdown_posttypes', get_post_types( array(
							'public'   => true,
							'_builtin' => false,
							), 'names'
						) );

						foreach ( $types as $type ) {
							if ( $type != self::POST_TYPE ) {
								$post_obj = get_post_type_object( $type );
								$label    = $post_obj->labels->singular_name;
								?>
								<optgroup
									label="<?php esc_attr( printf( "%s - '%s'", esc_html__( 'Custom Post Type', 'bnfw' ), $label ) ); ?>">
									<option
										value="new-<?php echo esc_attr( $type ); ?>" <?php selected( 'new-' . $type, $setting['notification'] ); ?>><?php echo esc_html__( 'New ', 'bnfw' ), "'$label'", esc_html__( ' Published', 'bnfw' ); ?></option>
									<option
										value="update-<?php echo esc_attr( $type ); ?>" <?php selected( 'update-' . $type, $setting['notification'] ); ?>><?php echo "'$label' " . esc_html__( 'Updated', 'bnfw' ); ?></option>
									<option
										value="pending-<?php echo esc_attr( $type ); ?>" <?php selected( 'pending-' . $type, $setting['notification'] ); ?>><?php echo "'$label' ", esc_html__( 'Pending Review', 'bnfw' ); ?></option>
									<option
										value="private-<?php echo esc_attr( $type ); ?>" <?php selected( 'private-' . $type, $setting['notification'] ); ?>><?php echo esc_html__( 'New Private ', 'bnfw' ), "'$label'"; ?></option>
									<option
										value="future-<?php echo esc_attr( $type ); ?>" <?php selected( 'future-' . $type, $setting['notification'] ); ?>><?php echo "'$label' ", esc_html__( 'Scheduled', 'bnfw' ); ?></option>
									<option
										value="comment-<?php echo esc_attr( $type ); ?>" <?php selected( 'comment-' . $type, $setting['notification'] ); ?>><?php echo "'$label' ", esc_html__( 'New Comment', 'bnfw' ); ?></option>
									<option value="moderate-<?php echo esc_attr( $type ); ?>-comment" <?php selected( 'moderate-' . $type . '-comment', $setting['notification'] ); ?>>
										<?php echo "'$label' - ", esc_html__( 'New Comment Awaiting Moderation', 'bnfw' ); ?>
									</option>
                                                                        <option value="approve-<?php echo esc_attr( $type ); ?>-comment" <?php selected( 'approve-' . $type . '-comment', $setting['notification'] ); ?>>
										<?php echo "'$label' - ", esc_html__( 'Comment Approved', 'bnfw' ); ?>
									</option>
									<option
										value="commentreply-<?php echo esc_attr( $type ); ?>" <?php selected( 'commentreply-' . $type, $setting['notification'] ); ?>><?php echo "'$label' ", esc_html__( 'Comment Reply', 'bnfw' ); ?></option>
									<?php do_action( 'bnfw_after_notification_options', $type, $label, $setting ); ?>
								</optgroup>
								<?php do_action( 'bnfw_after_notification_options_optgroup', $type, $label, $setting ); ?>

								<?php
							}
						}

						$taxs = apply_filters( 'bnfw_notification_dropdown_taxonomies', get_taxonomies(
							array(
								'public'   => true,
								'_builtin' => false,
							),
							'objects'
						) );

		if ( count( $taxs ) > 0 ) {
			?>
			<optgroup label="<?php esc_html_e( 'Custom Taxonomy', 'bnfw' ); ?>">
<?php
foreach ( $taxs as $tax ) {
	$tax_name = 'newterm-' . $tax->name;
	?>
	<option
		value="<?php echo esc_attr( $tax_name ); ?>" <?php selected( $tax_name, $setting['notification'] ); ?>><?php printf( "%s '%s'", esc_html__( 'New', 'bnfw' ), $tax->labels->name ); ?></option>
					<?php
}
?>
</optgroup>
<?php
		}
						do_action( 'bnfw_after_notification_optgroups', $setting );
						?>
					</select>
				</td>
			</tr>

			<?php do_action( 'bnfw_after_notification_dropdown', $setting ); ?>

			<tr valign="top" id="user-password-msg">
				<td>&nbsp;</td>
				<td>
					<div>
						<p style="margin-top: 0;"><?php esc_html_e( "This notification doesn't support additional email fields due to a limitation in WordPress.", 'bnfw' ); ?></p>
					</div>
				</td>
			</tr>

			<tr valign="top" id="email-formatting">
				<th>
					<?php esc_html_e( 'Email Formatting', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'How do you want to format the sent email? HTML is recommended as it\'ll show images and links correctly.', 'bnfw' ); ?></p></div>
				</th>
				<td>
					<label style="margin-right: 20px;">
						<input type="radio" name="email-formatting"
						       value="html" <?php checked( 'html', $setting['email-formatting'] ); ?>>
						<?php esc_html_e( 'HTML Formatting', 'bnfw' ); ?>
					</label>

					<label>
						<input type="radio" name="email-formatting"
						       value="text" <?php checked( 'text', $setting['email-formatting'] ); ?>>
						<?php esc_html_e( 'Plain Text', 'bnfw' ); ?>
					</label>
				</td>
			</tr>

			<?php do_action( 'bnfw_after_email_formatting', $setting ); ?>

			<tr valign="top" id="toggle-fields">
				<th>
					<?php esc_html_e( 'Additional Email Fields', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'This should be fairly self explanatory but if you\'re unsure, tick this checkbox and have a look at the available options. You can always untick it again should you decide you don\'t need to use it.', 'bnfw' ); ?></p></div>
				</th>
				<td>
					<input type="checkbox" id="show-fields" name="show-fields"
					       value="true" <?php checked( $setting['show-fields'], 'true', true ); ?>>
					<label for="show-fields"><?php esc_html_e( 'Set "From" Name & Email, Reply To, CC, BCC', 'bnfw' ); ?></label>
				</td>
			</tr>


			<tr valign="top" id="email">
				<th scope="row">
					<?php esc_html_e( 'From Name and Email', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'If you want to send the email from your site name and email address instead of the default "WordPress" from "wordpress@domain.com", this is where you can do it.', 'bnfw' ); ?></p></div>
				</th>
				<td>
					<input type="text" name="from-name" value="<?php echo esc_attr( $setting['from-name'] ); ?>"
					       placeholder="<?php _e( 'Site Name', 'bnfw' ); ?>" style="width: 37.35%">
					<input type="text" name="from-email" value="<?php echo esc_attr( $setting['from-email'] ); ?>"
					       placeholder="<?php _e( 'Site Email', 'bnfw' ); ?>" style="width: 37.3%">
				</td>
			</tr>


			<tr valign="top" id="reply">
				<th scope="row">
					<?php esc_html_e( 'Reply To', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'If you want any replies to your email notification to go to another person, fill in this box with their name and email address.', 'bnfw' ); ?></p></div>
				</th>
				<td>
					<input type="text" name="reply-name" value="<?php echo esc_attr( $setting['reply-name'] ); ?>"
					       placeholder="<?php _e( 'Name', 'bnfw' ); ?>" style="width: 37.35%">
					<input type="text" name="reply-email" value="<?php echo esc_attr( $setting['reply-email'] ); ?>"
					       placeholder="<?php _e( 'Email', 'bnfw' ); ?>" style="width: 37.3%">
				</td>
			</tr>

			<tr valign="top" id="cc">
				<th scope="row">
					<?php esc_html_e( 'CC', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'Publicly copy in any other users or user roles to this email.', 'bnfw' ); ?></p></div>
				</th>

				<td>
					<select multiple name="cc[]" class="<?php echo sanitize_html_class( bnfw_get_user_select_class() ); ?>"
					        data-placeholder="<?php echo apply_filters( 'bnfw_email_dropdown_placeholder', __( 'Select User Roles / Users', 'bnfw' ) ); ?>" style="width:75%">
						<?php bnfw_render_users_dropdown( $setting['cc'] ); ?>
					</select>
				</td>
			</tr>

			<tr valign="top" id="bcc">
				<th scope="row">
					<?php esc_html_e( 'BCC', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'Privately copy in any other users or user roles to this email.', 'bnfw' ); ?></p></div>
				</th>

				<td>
					<select multiple name="bcc[]" class="<?php echo sanitize_html_class( bnfw_get_user_select_class() ); ?>"
							data-placeholder="<?php echo apply_filters( 'bnfw_email_dropdown_placeholder', __( 'Select User Roles / Users', 'bnfw' ) ); ?>" style="width:75%">
						<?php bnfw_render_users_dropdown( $setting['bcc'] ); ?>
					</select>
				</td>
			</tr>

			<?php do_action( 'bnfw_after_additional_email_fields', $setting ); ?>

			<tr valign="top" id="post-author">
				<th>
					<?php esc_html_e( 'Send to Author', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'E.g. If you want a new post published notification to go to the post author, tick this box.', 'bnfw' ); ?></p></div>
				</th>

				<td>
					<label>
						<input type="checkbox" id="only-post-author" name="only-post-author"
						       value="true" <?php checked( 'true', $setting['only-post-author'] ); ?>>
						<?php esc_html_e( 'Send this notification to the Author', 'bnfw' ); ?>
					</label>
				</td>
			</tr>

			<?php do_action( 'bnfw_after_only_post_author', $setting ); ?>

			<tr valign="top" id="current-user">
				<th>
					&nbsp;
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'E.g. If you\'re an editor and regularly update your posts, you might not want to be emailed about this all the time. Ticking this box will prevent you from receiving emails about your own changes.', 'bnfw' ); ?></p></div>
				</th>
				<td>
					<label>
						<input type="checkbox" name="disable-current-user"
							   value="true" <?php checked( 'true', $setting['disable-current-user'] ); ?>>
						<?php esc_html_e( 'Do not send this Notification to the User that triggered it', 'bnfw' ); ?>
					</label>
				</td>
			</tr>

			<?php do_action( 'bnfw_after_disable_current_user', $setting ); ?>

			<tr valign="top" id="users">
				<th scope="row">
					<?php esc_html_e( 'Send To', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'Choose the users and/or user roles to send this email notification to.', 'bnfw' ); ?></p></div>
				</th>
				<td>
					<select multiple id="users-select" name="users[]"
					        class="<?php echo sanitize_html_class( bnfw_get_user_select_class() ); ?>"
					        data-placeholder="<?php echo apply_filters( 'bnfw_email_dropdown_placeholder', __( 'Select User Roles / Users', 'bnfw' ) ); ?>" style="width:75%">
						<?php bnfw_render_users_dropdown( $setting['users'] ); ?>
					</select>
				</td>
			</tr>

			<tr valign="top" id="exclude-users">
				<th scope="row">
					<?php esc_html_e( 'Except For', 'bnfw' ); ?>
					<div class="bnfw-help-tip">
						<p>
							<?php esc_html_e( 'Choose the users and/or user roles that this notification should not be sent to.', 'bnfw' ); ?>
						</p>
					</div>
				</th>
				<td>
					<select multiple id="exclude-users-select" name="exclude-users[]"
					        class="<?php echo sanitize_html_class( bnfw_get_user_select_class() ); ?>"
					        data-placeholder="<?php echo apply_filters( 'bnfw_email_dropdown_placeholder', __( 'Select User Roles / Users', 'bnfw' ) ); ?>" style="width:75%">
						<?php bnfw_render_users_dropdown( $setting['exclude-users'] ); ?>
					</select>
				</td>
			</tr>

			<?php
			$display = 'none';

			if ( $this->should_show_users_count_msg( $setting ) ) {
				$display = 'table-row';
			}
			?>
			<tr valign="top" id="users-count-msg" style="display: <?php echo esc_attr( $display ); ?>">
				<th scope="row">&nbsp;</th>
				<td>
					<div>
						<p>
							<?php _e( 'You have chosen to send this notification to over 200 users. Please check the email sending rate limit at your host before sending.', 'bnfw' ); ?>
						</p>
					</div>
				</td>
			</tr>

			<?php do_action( 'bnfw_after_send_to', $setting ); ?>

			<tr valign="top" id="subject-wrapper">
				<th scope="row">
					<?php esc_html_e( 'Subject', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'Notification subject. You can use ', 'bnfw' ); ?><a href="https://betternotificationsforwp.com/documentation/notifications/shortcodes/" target="_blank">shortcodes</a><?php esc_html_e(' here.', 'bnfw' ); ?></p></div>
				</th>
				<td>
					<input type="text" name="subject" id="subject" value="<?php echo esc_attr( $setting['subject'] ); ?>"
					       style="width:75%;">
				</td>
			</tr>

			<?php do_action( 'bnfw_after_user_dropdown', $setting ); ?>

			<?php do_action( 'bnfw_before_message_body', $setting ); ?>
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Message Body', 'bnfw' ); ?>
					<div class="bnfw-help-tip"><p><?php esc_html_e( 'Notification message. You can use ', 'bnfw' ); ?><a href="https://betternotificationsforwp.com/documentation/notifications/shortcodes/" target="_blank">shortcodes</a><?php esc_html_e(' here.', 'bnfw' ); ?></p></div>

					<div class="wp-ui-text-highlight">
						<p>
							<br>
							<br>
							<br>
							<br>
							<?php esc_html_e( 'Need some more help?', 'bnfw' ); ?>
						</p>
						<?php
						$doc_url = 'https://betternotificationsforwp.com/documentation/';

						if ( bnfw_is_tracking_allowed() ) {
							$doc_url .= "?utm_source=WP%20Admin%20Notification%20Editor%20-%20'Documentation'&amp;utm_medium=referral";
						}
						?>
						<p>
							<a href="#" class="button-secondary" id="insert-default-msg"><?php esc_html_e( 'Insert Default Content', 'bnfw' ); ?></a>
						</p>
						<p>
							<a href="<?php echo $doc_url; ?>"
							   target="_blank" class="button-secondary"><?php esc_html_e( 'Read Documentation', 'bnfw' ); ?></a>
						</p>
						<p>
							<a href="" target="_blank" id="shortcode-help"
							   class="button-secondary"><?php esc_html_e( 'Find Shortcodes', 'bnfw' ); ?></a>
						</p>
					</div>
				</th>
				<td>
					<?php wp_editor( $setting['message'], 'notification_message', array( 'media_buttons' => true ) ); ?>
					<p> &nbsp; </p>
					<div id="disable-autop">
						<label>
							<input type="checkbox" name="disable-autop"
							       value="true" <?php checked( 'true', $setting['disable-autop'] ); ?>>
							<?php esc_html_e( 'Stop additional paragraph and line break HTML from being inserted into my notifications', 'bnfw' ); ?>
						</label>
					</div>
				</td>
			</tr>

			</tbody>
		</table>
		<?php
	}

	/**
	 * Should we enqueue assets?
	 *
	 * @since 1.0
	 *
	 * @param $hook_suffix
	 */
	public function is_assets_needed( $hook_suffix ) {
		if ( self::POST_TYPE === get_post_type() || 'bnfw_notification_page_bnfw-settings' === $hook_suffix ) {
			// The enqueue assets function may be included from addons.
			// We want to disable autosave only for notifications
			wp_dequeue_script( 'autosave' );

			$this->enqueue_assets();

			do_action( 'bnfw_after_enqueue_scripts' );
		}
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.4
	 */
	public function enqueue_assets() {
		wp_deregister_script( 'select2' );
		wp_dequeue_script( 'select2' );
		wp_deregister_style( 'select2' );
		wp_dequeue_style( 'select2' );

		// Ultimate Member plugin is giving us problems. They should upgrade
		wp_deregister_script( 'um_minified' );
		wp_dequeue_script( 'um_minified' );
		wp_deregister_script( 'um_admin_scripts' );
		wp_dequeue_script( 'um_admin_scripts' );

		wp_enqueue_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', array(), '4.0.3' );
		wp_enqueue_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.min.js', array( 'jquery' ), '4.0.3', true );

		wp_enqueue_script( 'bnfw', plugins_url( '../assets/js/bnfw.js', dirname( __FILE__ ) ), array( 'select2' ), '0.1', true );
		wp_enqueue_style( 'bnfw', plugins_url( '../assets/css/bnfw.css', dirname( __FILE__ ) ), array( 'dashicons', 'select2' ), '0.1' );

		$strings = array(
			'validation_element' => apply_filters( 'bnfw_validation_element', '#users-select' ),
			'empty_user' => esc_html__( 'You must choose at least one User or User Role to send the notification to before you can save', 'bnfw' ),
			'enableTags' => false,
		);

		/**
		 * Filter the localized array that is sent to scripts.
		 *
		 * @since 1.7.0
		 */
		$strings = apply_filters( 'bnfw_localize_script', $strings );

		wp_localize_script( 'bnfw', 'BNFW', $strings );
	}

	/**
	 * Save the meta box's post metadata.
	 *
	 * @since 1.0
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_meta_data( $post_id ) {
		if ( self::POST_TYPE !== get_post_type( $post_id ) ) {
			return;
		}

		// Check nonce.
		if ( empty( $_POST[ self::POST_TYPE . '_nonce' ] ) ) {
			return;
		}

		// Verify nonce.
		if ( ! wp_verify_nonce( $_POST[ self::POST_TYPE . '_nonce' ], self::POST_TYPE ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'bnfw' ) ) {
			return;
		}
               
                if ( isset( $_POST['digest-interval'] ) && 'no' != $_POST['digest-interval']) {
                   $subject = $_POST['subject'];
                }else{
                   $subject = sanitize_text_field( $_POST['subject'] );
                }

		$setting = array(
			'notification'         => sanitize_text_field( $_POST['notification'] ),
			'subject'              => $subject,
			'message'              => $_POST['notification_message'],
			'disabled'             => isset( $_POST['disabled'] ) ? sanitize_text_field( $_POST['disabled'] ) : 'false',
			'email-formatting'     => isset( $_POST['email-formatting'] ) ? sanitize_text_field( $_POST['email-formatting'] ) : 'html',
			'disable-current-user' => isset( $_POST['disable-current-user'] ) ? sanitize_text_field( $_POST['disable-current-user'] ) : 'false',
			'disable-autop'        => isset( $_POST['disable-autop'] ) ? sanitize_text_field( $_POST['disable-autop'] ) : 'false',
			'only-post-author'     => isset( $_POST['only-post-author'] ) ? sanitize_text_field( $_POST['only-post-author'] ) : 'false',
			'users'                => array(),
			'exclude-users'        => array(),
		);

		if ( isset( $_POST['users'] ) ) {
			$setting['users'] = array_map( 'sanitize_text_field', $_POST['users'] );
		}

		if ( isset( $_POST['exclude-users'] ) ) {
			$setting['exclude-users'] = array_map( 'sanitize_text_field', $_POST['exclude-users'] );
		}

		if ( isset( $_POST['show-fields'] ) && 'true' == $_POST['show-fields'] ) {
			$setting['show-fields'] = 'true';
			$setting['from-name']   = sanitize_text_field( $_POST['from-name'] );
			$setting['from-email']  = sanitize_text_field( $_POST['from-email'] );
			$setting['reply-name']  = sanitize_text_field( $_POST['reply-name'] );
			$setting['reply-email'] = sanitize_text_field( $_POST['reply-email'] );
			$setting['cc']          = isset( $_POST['cc'] ) ? array_map( 'sanitize_text_field', $_POST['cc'] ) : '';
			$setting['bcc']         = isset( $_POST['bcc'] ) ? array_map( 'sanitize_text_field', $_POST['bcc'] ) : '';
		} else {
			$setting['show-fields'] = 'false';
		}

		$setting = apply_filters( 'bnfw_notification_setting', $setting, $_POST );

		$this->save_settings( $post_id, $setting );

		if ( isset( $_POST['send-test-email'] ) ) {
			if ( 'true' == sanitize_text_field( $_POST['send-test-email'] ) ) {
				BNFW::factory()->engine->send_test_email( $setting );
				add_filter( 'redirect_post_location', array( $this, 'test_mail_sent' ) );
			}
		}
	}

	/**
	 * Add a query parameter to url if test email was sent.
	 *
	 * @since 1.3
	 */
	public function test_mail_sent( $loc ) {
		return add_query_arg( self::TEST_MAIL_ARG, 1, $loc );
	}

	/**
	 * Add a notification if a test email was sent.
	 *
	 * @since 1.3
	 */
	public function admin_notices() {
		if ( isset( $_GET[ self::TEST_MAIL_ARG ] ) ) {
			$screen = get_current_screen();
			if ( in_array( $screen->post_type, array( self::POST_TYPE ) ) ) {
				?>
				<div class="updated below-h2">
					<p><?php echo esc_html__( 'Test Notification Sent.', 'bnfw' ); ?></p>
				</div>
				<?php
			}
		}
	}

	/**
	 * Save settings in post meta.
	 *
	 * @since  1.0
	 * @access private
	 *
	 * @param $post_id
	 * @param $setting
	 */
	private function save_settings( $post_id, $setting ) {
		foreach ( $setting as $key => $value ) {
			update_post_meta( $post_id, self::META_KEY_PREFIX . $key, $value );
		}
	}

	/**
	 * Read settings from post meta.
	 *
	 * @since 1.0
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function read_settings( $post_id ) {
		$setting = array();
		$default = array(
			'notification'         => '',
			'from-name'            => '',
			'from-email'           => '',
			'reply-name'           => '',
			'reply-email'          => '',
			'cc'                   => array(),
			'bcc'                  => array(),
			'users'                => array(),
			'exclude-users'        => array(),
			'subject'              => '',
			'email-formatting'     => get_option( 'bnfw_email_format', 'html' ),
			'message'              => '',
			'show-fields'          => 'false',
			'disable-current-user' => 'false',
			'disable-autop'        => 'false',
			'only-post-author'     => 'false',
			'disabled'             => 'false',
		);

		$default = apply_filters( 'bnfw_notification_setting_fields', $default );

		foreach ( $default as $key => $default_value ) {
			$value = get_post_meta( $post_id, self::META_KEY_PREFIX . $key, true );
			if ( ! empty( $value ) ) {
				$setting[ $key ] = $value;
			} else {
				$setting[ $key ] = $default_value;
			}
		}

		// compatibility code. This will be removed subsequently
		$user_roles = get_post_meta( $post_id, self::META_KEY_PREFIX . 'user-roles', true );
		if ( ! empty( $user_roles ) && is_array( $user_roles ) ) {
			foreach ( $user_roles as $role ) {
				$setting['users'][] = 'role-' . $role;
			}

			update_post_meta( $post_id, self::META_KEY_PREFIX . 'users', $setting['users'] );
			delete_post_meta( $post_id, self::META_KEY_PREFIX . 'user-roles' );
		}

		$setting['id'] = $post_id;

		return $setting;
	}

	/**
	 * Change the post updated message for notification post type.
	 *
	 * @since 1.0
	 *
	 * @param $messages
	 *
	 * @return mixed
	 */
	public function post_updated_messages( $messages ) {
		$messages[ self::POST_TYPE ] = array_fill( 0, 11, esc_html__( 'Notification saved.', 'bnfw' ) );

		return $messages;
	}

	/**
	 * Render submit div meta box.
	 *
	 * @since 1.0
	 *
	 * @param $post
	 */
	public function render_submitdiv( $post ) {
		global $post;
		?>
		<div class="submitbox" id="submitpost">

			<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
			<div style="display:none;">
				<?php submit_button( esc_html__( 'Save', 'bnfw' ), 'button', 'save' ); ?>
			</div>

			<?php // Always publish. ?>
			<div class="disable-notification-checkbox" style="padding: 5px 0 10px 0;">
				<div class="bnfw-help-tip-container">
					<input type="hidden" name="post_status" id="hidden_post_status" value="publish">

					<div class="bnfw-help-tip"><p><?php esc_html_e( 'Use this to enable or disable notifications. If you want to disable a default WordPress notification, just create it on the left, then disable it here.', 'bnfw' ); ?></p></div>

					<?php
					$setting = $this->read_settings( $post->ID );
					?>
					<label>
						<input type="radio" name="disabled"
						       value="false" <?php checked( $setting['disabled'], 'false', true ); ?>><?php esc_html_e( 'Notification Enabled', 'bnfw' ); ?>
					</label>

					<br>

					<label>
						<input type="radio" name="disabled"
						       value="true" <?php checked( $setting['disabled'], 'true', true ); ?>><?php esc_html_e( 'Notification Disabled', 'bnfw' ); ?>
					</label>
				</div>

				<br>
				<br>

				<?php if ( 'publish' == $post->post_status ) { ?>
					<div class="bnfw-help-tip-container">
						<input type="hidden" name="send-test-email" id="send-test-email" value="false">
						<input name="test-email" type="submit" class="button button-secondary button-large" id="test-email"
						       value="<?php esc_attr_e( 'Send Me a Test Email', 'bnfw' ); ?>">

						<div class="bnfw-help-tip"><p><?php esc_html_e( 'This will send you (the currently logged in user) a notification so that you can check for any issues with formatting – it’s doesn\'t mean that a notification will send correctly in the future. You can read about how to improve email delivery', 'bnfw'); ?> <a href="https://betternotificationsforwp.com/documentation/getting-started/how-to-improve-email-delivery/" target="_blank"><?php esc_html_e( 'here', 'bnfw'); ?></a><?php esc_html_e( '. Shortcodes will not be replaced with content.', 'bnfw' ); ?></p></div>
					</div>
				<?php } ?>

			</div>

			<div id="major-publishing-actions">

				<div id="delete-action">
					<?php
					if ( ! EMPTY_TRASH_DAYS ) {
						$delete_text = esc_html__( 'Delete Permanently', 'bnfw' );
					} else {
						$delete_text = esc_html__( 'Move to Trash', 'bnfw' );
					}
					?>
					<a class="submitdelete deletion"
					   href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo esc_html( $delete_text ); ?></a>
				</div>

				<div id="publishing-action">
					<span class="spinner"></span>
					<input name="original_publish" type="hidden" id="original_publish"
					       value="<?php esc_attr_e( 'Save', 'bnfw' ); ?>">
					<input name="save" type="submit" class="button button-primary button-large" id="publish"
					       accesskey="p" value="<?php esc_attr_e( 'Save', 'bnfw' ); ?>">
				</div>
				<div class="clear"></div>

			</div>
			<!-- #major-publishing-actions -->

			<div class="clear"></div>
		</div>
		<!-- #submitpost -->
		<?php
	}

	/**
	 * Get notifications based on type.
	 *
	 * @since 1.0
	 *
	 * @param array|string $types
	 * @param bool         $exclude_disabled (optional) Whether to exclude disabled notifications or not. True by default.
	 *
	 * @return array WP_Post objects
	 */
	public function get_notifications( $types = array(), $exclude_disabled = true ) {
		if ( ! is_array( $types ) ) {
			$types = array( $types );
		}

		$args = array(
			'post_type' => self::POST_TYPE,
		);

		$meta_query = array();

		if ( ! empty( $types ) ) {
			$meta_query[] = array(
				'key'     => self::META_KEY_PREFIX . 'notification',
				'value'   => $types,
				'compare' => 'IN',
			);
		}

		if ( $exclude_disabled ) {
			$meta_query[] = array(
				'key'     => self::META_KEY_PREFIX . 'disabled',
				'value'   => 'true',
				'compare' => '!=',
			);
		}

		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query;
		}

		$args['posts_per_page'] = -1;
		$args['nopagging'] = true;

		$args = apply_filters( 'bnfw_get_notifications_args', $args, $types, $exclude_disabled );

		$wp_query = new WP_Query();
		$posts    = $wp_query->query( $args );

		$posts = apply_filters( 'bnfw_get_notifications_posts', $posts, $args, $types, $exclude_disabled );

		return $posts;
	}

	/**
	 * Are there any disabled notifications for a particular notification type.
	 *
	 * @param string $type Notification type.
	 *
	 * @return bool True if disabled, False otherwise.
	 */
	public function is_notification_disabled( $type ) {
		$args = array(
			'post_type'      => self::POST_TYPE,
			'posts_per_page' => - 1,
			'nopagging'      => true,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'   => self::META_KEY_PREFIX . 'notification',
					'value' => $type,
				),
				array(
					'key'   => self::META_KEY_PREFIX . 'disabled',
					'value' => 'true',
				),
			)
		);

		$args = apply_filters( 'bnfw_is_notification_disabled_args', $args, $type );

		$wp_query = new WP_Query();
		$posts    = $wp_query->query( $args );

		$posts = apply_filters( 'bnfw_is_notification_disabled_posts', $posts, $args, $type );

		return count( $posts ) > 0;
	}

	/**
	 * Does a particular type of notification exists or not.
	 *
	 * @since 1.1
	 *
	 * @param string $type             Notification Type.
	 * @param bool   $exclude_disabled (optional) Whether to exclude disabled notifications or not. True by default.
	 *
	 * @return bool True if present, False otherwise
	 */
	public function notification_exists( $type, $exclude_disabled = true ) {
		$notifications = $this->get_notifications( $type, $exclude_disabled );

		if ( count( $notifications ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Custom columns for this post type.
	 *
	 * @since  1.0
	 * @filter manage_{post_type}_posts_columns
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function columns_header( $columns ) {
		$columns['type']     = esc_html__( 'Notification Type', 'bnfw' );
		$columns['disabled'] = esc_html__( 'Enabled?', 'bnfw' );
		$columns['subject']  = esc_html__( 'Subject', 'bnfw' );
		$columns['users']    = esc_html__( 'User Roles / Users', 'bnfw' );
		$columns['excluded'] = esc_html__( 'Excluded User Roles / Users', 'bnfw' );

		return $columns;
	}

	/**
	 * Custom column appears in each row.
	 *
	 * @since  1.0
	 * @action manage_{post_type}_posts_custom_column
	 *
	 * @param string $column  Column name
	 * @param int    $post_id Post ID
	 */
	public function custom_column_row( $column, $post_id ) {
		$setting = $this->read_settings( $post_id );
		switch ( $column ) {
			case 'disabled':
				if ( 'true' != $setting['disabled'] ) {
					printf( '<span class="dashicons dashicons-yes"></span>' );
				}
				break;
			case 'type':
				echo $this->get_notification_name( $setting['notification'] );
				break;
			case 'subject':
				echo ! empty( $setting['subject'] ) ? $setting['subject'] : '';
				break;
			case 'users':
				$users = $this->get_names_from_users( $setting['users'] );
				if (!empty($users)) {
					echo implode( ', ', $users );
				}
				else {
                                    if(isset($setting['new-user-role'])){
					$users = $this->get_names_from_users( $setting['new-user-role'] );
					echo implode( ', ', $users );
				}
				}

				if ( 'true' === $setting['only-post-author'] ) {
					echo esc_html__( ', Post Author', 'bnfw' );
				}

				break;
			case 'excluded':
				$excluded_users = $this->get_names_from_users( $setting['exclude-users'] );
				echo implode( ', ', $excluded_users );

				break;
		}

		/**
		 * Invoked while displaying a custom column in notification table.
		 *
		 * @since 1.3.9
		 *
		 * @param string $column  Column name
		 * @param int    $post_id Post ID
		 */
		do_action( 'bnfw_notification_table_column', $column, $post_id );
	}

	/**
	 * Get names from users.
	 *
	 * @since 1.2
	 */
	private function get_names_from_users( $users ) {
		$user_ids            = array();
		$user_roles          = array();
		$emails = array();
		$names_from_user_ids = array();

		if ( is_array( $users ) ) {
			foreach ( $users as $user ) {
				if ( $this->starts_with( $user, 'role-' ) ) {
					$user_roles[] = ucfirst( str_replace( 'role-', '', $user ) );
				} elseif ( strpos( $user, '@' ) !== false ) {
					$emails[] = $user;
				} elseif ( absint( $user ) > 0 ) {
					$user_ids[] = absint( $user );
				} else {
					$emails[] = $user;
				}
			}
		}
		else {
			// User Roles not associated with a To/CC/BCC field
			$role = get_role( $users );

			if ( !empty( $role ) ) {
				$user_roles = array( $role->name );
			}
		}

		if ( ! empty( $user_ids ) ) {
			$user_query = new WP_User_Query( array( 'include' => $user_ids ) );
			foreach ( $user_query->results as $user ) {
				$names_from_user_ids[] = $user->user_login;
			}
		}

		return array_merge( $user_roles, $names_from_user_ids, $emails );
	}

	/**
	 * Get name of the notification based on slug.
	 *
	 * @param string $slug Notification Slug.
	 *
	 * @return string Notification Name.
	 */
	private function get_notification_name( $slug ) {
		$name = '';
		switch ( $slug ) {
			case 'new-comment':
				$name = esc_html__( 'New Comment', 'bnfw' );
				break;
			case 'approve-post-comment':
				$name = esc_html__( 'Post - Comment Approved', 'bnfw' );
				break;
			case 'moderate-comment':
				$name = esc_html__( 'New Comment Awaiting Moderation', 'bnfw' );
				break;
			case 'new-trackback':
				$name = esc_html__( 'New Trackback', 'bnfw' );
				break;
			case 'new-pingback':
				$name = esc_html__( 'New Pingback', 'bnfw' );
				break;
			case 'reply-comment':
				$name = esc_html__( 'Comment Reply', 'bnfw' );
				break;
			case 'user-password':
				$name = esc_html__( 'User Lost Password - For User', 'bnfw' );
				break;
			case 'admin-password':
				$name = esc_html__( 'User Lost Password - For Admin', 'bnfw' );
				break;
			case 'admin-password-changed':
				$name = esc_html__( 'Password Changed - For Admin', 'bnfw' );
				break;
			case 'admin-email-changed':
				$name = esc_html__( 'User Email Changed - For Admin', 'bnfw' );
				break;
			case 'password-changed':
				$name = esc_html__( 'Password Changed - For User', 'bnfw' );
				break;
			case 'email-changing':
				$name = esc_html__( 'User Email Changed Confirmation - For User', 'bnfw' );
				break;
			case 'email-changed':
				$name = esc_html__( 'User Email Changed - For User', 'bnfw' );
				break;
			case 'core-updated':
				$name = esc_html__( 'WordPress Core Automatic Background Updates', 'bnfw' );
				break;
			case 'new-user':
				$name = esc_html__( 'New User Registration - For User', 'bnfw' );
				break;
            case 'user-login':
				$name = esc_html__( 'User Logged In - For User', 'bnfw' );
				break;
            case 'admin-user-login':
				$name = esc_html__( 'User Logged In - For Admin', 'bnfw' );
				break;
			case 'welcome-email':
				$name = esc_html__( 'New User - Post-registration Email', 'bnfw' );
				break;
			case 'admin-user':
				$name = esc_html__( 'New User Registration - For Admin', 'bnfw' );
				break;
			case 'user-role':
				$name = esc_html__( 'User Role Changed - For User', 'bnfw' );
				break;
			case 'admin-role':
				$name = esc_html__( 'User Role Changed - For Admin', 'bnfw' );
				break;
			case 'new-post':
				$name = esc_html__( 'New Post Published', 'bnfw' );
				break;
			case 'update-post':
				$name = esc_html__( 'Post Updated', 'bnfw' );
				break;
			case 'pending-post':
				$name = esc_html__( 'Post Pending Review', 'bnfw' );
				break;
			case 'private-post':
				$name = esc_html__( 'New Private Post', 'bnfw' );
				break;
			case 'future-post':
				$name = esc_html__( 'Post Scheduled', 'bnfw' );
				break;
            case 'trash-post':
				$name = esc_html__( 'Published Post Moved to Trash', 'bnfw' );
				break;
			case 'new-page':
				$name = esc_html__( 'New Page Published', 'bnfw' );
				break;
			case 'newterm-category':
				$name = esc_html__( 'New Category', 'bnfw' );
				break;
			case 'newterm-post_tag':
				$name = esc_html__( 'New Tag', 'bnfw' );
				break;
			case 'ca-export-data':
				$name = esc_html__( 'Privacy – Confirm Action: Export Data Request – For User', 'bnfw' );
				break;
			case 'ca-erase-data':
				$name = esc_html__( 'Privacy – Confirm Action: Erase Data Request – For User', 'bnfw' );
				break;
			case 'uc-export-data':
				$name = esc_html__( 'Privacy - Confirm Action: Export Data Request - For Admin', 'bnfw' );
				break;
			case 'uc-erase-data':
				$name = esc_html__( 'Privacy - Confirm Action: Erase Data Request - For Admin', 'bnfw' );
				break;
			case 'data-export':
				$name = esc_html__( 'Privacy - Data Export - For User', 'bnfw' );
				break;
			case 'data-erased':
				$name = esc_html__( 'Privacy - Data Erased - For User', 'bnfw' );
				break;
			case 'new-media':
				$name = esc_html__( 'New Media Published', 'bnfw' );
				break;
			case 'update-media':
			 	$name = esc_html__( 'Media Updated', 'bnfw' );
			 	break;
			case 'comment-attachment':
			 	$name = esc_html__( 'Media - New Comment', 'bnfw' );
			 	break;
                        case 'approve-page-comment':
				$name = esc_html__( 'Page - Comment Approved', 'bnfw' );
				break;
                        case 'approve-attachment-comment':
				$name = esc_html__( 'Media - Comment Approved', 'bnfw' );
				break;
                        case 'moderate-attachment-comment':
				$name = esc_html__( 'Media - New Comment Awaiting Moderation', 'bnfw' );
				break;
                        case 'commentreply-attachment':
				$name = esc_html__( 'Media - Comment Reply', 'bnfw' );
				break;
			

			default:
				$splited  = explode( '-', $slug );
				$label    = $splited[1];
				$post_obj = get_post_type_object( $splited[1] );

				if ( null != $post_obj ) {
					$label = $post_obj->labels->singular_name;
				}

				switch ( $splited[0] ) {
					case 'new':
						$name = esc_html__( 'New ', 'bnfw' ) . $label . ' ' . esc_html__( 'Published', 'bnfw' );
						break;
					case 'update':
						$name = esc_html__( 'Updated ', 'bnfw' ) . $label;
						break;
					case 'pending':
						$name = $label . esc_html__( ' Pending Review', 'bnfw' );
						break;
					case 'future':
						$name = $label . esc_html__( ' Scheduled', 'bnfw' );
						break;
					case 'private':
						$name = esc_html__( 'New Private ', 'bnfw' ) . $label;
						break;
					case 'comment':
						$name = $label . esc_html__( ' Comment', 'bnfw' );
						break;
					case 'moderate':
						$name = $label . ' - ' . esc_html__( 'New Comment Awaiting Moderation', 'bnfw' );
						break;
					case 'commentreply':
						$name = $label . esc_html__( ' Comment Reply', 'bnfw' );
						break;
                                        case 'approve':
						$name = $label . esc_html__( ' Comment Approved', 'bnfw' );
						break;
					case 'newterm':
						$tax = get_taxonomy( $splited[1] );
						if ( ! $tax ) {
							$name = esc_html__( 'New Term', 'bnfw' );
						} else {
							$name = esc_html__( 'New Term in ', 'bnfw' ) . $tax->labels->name;
						}
						break;
				}
				break;
		}

		$name = apply_filters( 'bnfw_notification_name', $name, $slug );

		return $name;
	}

	/**
	 * Add additional custom edit actions for enabling and disabling notifications in bulk.
	 *
	 * @param array $bulk_actions Bulk Actions.
	 *
	 * @return array Modified list of Bulk Actions.
	 */
	public function add_custom_edit_action( $bulk_actions ) {
		$bulk_actions['enable_notifications'] = __( 'Enable Notifications', 'bnfw' );
		$bulk_actions['disable_notifications'] = __( 'Disable Notifications', 'bnfw' );

		return $bulk_actions;
	}

	/**
	 * Handle custom edit actions.
	 *
	 * @param $redirect_to
	 * @param $doaction
	 * @param $post_ids
	 *
	 * @return string
	 */
	public function handle_custom_edit_action( $redirect_to, $doaction, $post_ids ) {
		if ( 'enable_notifications' !== $doaction && 'disable_notifications' !== $doaction ) {
			return $redirect_to;
		}

		$redirect_to = remove_query_arg( array( 'bulk_enable_notifications', 'bulk_disable_notifications', 'bnfw_action' ), $redirect_to );

		$meta_value = 'true';

		if ( 'enable_notifications' === $doaction ) {
			$meta_value = 'false';
		}

		foreach ( $post_ids as $post_id ) {
			update_post_meta( $post_id, self::META_KEY_PREFIX . 'disabled', $meta_value );
		}

		$redirect_to = add_query_arg( 'bulk_' . $doaction, count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	/**
	 * Custom row actions for this post type.
	 *
	 * @since  1.0
	 * @filter post_row_actions
	 *
	 * @param array    $actions
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	public function custom_row_actions( $actions, $post ) {
		if ( self::POST_TYPE === get_post_type( $post ) ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );

			$notification_disabled = get_post_meta( $post->ID, self::META_KEY_PREFIX . 'disabled', true );

			if ( 'true' === $notification_disabled ) {
				$url = add_query_arg(
					array(
						'notification_id' => $post->ID,
						'bnfw_action'     => 'enable_notification',
					)
				);
				$actions['enable_notification'] = '<a href="' . esc_url( $url ) . '">' . __( 'Enable Notification', 'bnfw' ) . '</a>';
			} else {
				$url = add_query_arg(
					array(
						'notification_id' => $post->ID,
						'bnfw_action'     => 'disable_notification',
					)
				);
				$actions['disable_notification'] = '<a href="' . esc_url( $url ) . '">' . __( 'Disable Notification', 'bnfw' ) . '</a>';
			}
		}

		return $actions;
	}

	/**
	 * Handle custom actions.
	 */
	public function handle_actions() {
		if ( ! isset( $_GET['bnfw_action'] ) || ! isset( $_GET['notification_id'] ) ) {
			return;
		}

		$post_id = absint( $_GET['notification_id'] );
		if ( 0 === $post_id ) {
			return;
		}

		$action = sanitize_text_field( $_GET['bnfw_action'] );

		if ( 'enable_notification' === $action ) {
			update_post_meta( $post_id, self::META_KEY_PREFIX . 'disabled', 'false' );
		}

		if ( 'disable_notification' === $action ) {
			update_post_meta( $post_id, self::META_KEY_PREFIX . 'disabled', 'true' );
		}
	}

	/**
	 * Find if a string starts with another string.
	 *
	 * @since 1.2
	 *
	 * @param $haystack
	 * @param $needle
	 *
	 * @return bool
	 */
	public function starts_with( $haystack, $needle ) {
		// search backwards starting from haystack length characters from the end
		return '' === $needle || strrpos( $haystack, $needle, - strlen( $haystack ) ) !== false;
	}

	/**
	 * Display a help notice.
	 *
	 * @since 1.7
	 */
	public function show_help_notice() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->post_type, array( self::POST_TYPE ) ) ) {
			return;
		}

		if ( ! empty( $_REQUEST['bnfw_action'] ) && 'enable_notification' === $_REQUEST['bnfw_action'] ) {
			echo '<div id="message" class="updated fade"><p>' . __( 'Enabled 1 Notification.', 'bnfw' ) . '</p></div>';
		}

		if ( ! empty( $_REQUEST['bnfw_action'] ) && 'disable_notification' === $_REQUEST['bnfw_action'] ) {
			echo '<div id="message" class="updated fade"><p>' . __( 'Disabled 1 Notification.', 'bnfw' ) . '</p></div>';
		}

		if ( ! empty( $_REQUEST['bulk_enable_notifications'] ) ) {
			$enabled_count = intval( $_REQUEST['bulk_enable_notifications'] );
			printf( '<div id="message" class="updated fade"><p>' .
			        _n( 'Enabled %s Notification.',
				        'Enabled %s Notifications.',
				        $enabled_count,
				        'bnfw'
			        ) . '</p></div>', $enabled_count );
		}

		if ( ! empty( $_REQUEST['bulk_disable_notifications'] ) ) {
			$disabled_count = intval( $_REQUEST['bulk_disable_notifications'] );
			printf( '<div id="message" class="updated fade"><p>' .
			        _n( 'Disabled %s Notification.',
				        'Disabled %s Notifications.',
				        $disabled_count,
				        'bnfw'
			        ) . '</p></div>', $disabled_count );
		}

		if ( ! PAnD::is_admin_notice_active( 'disable-bnfw-help-notice-forever' ) ) {
			return;
		}

		?>
		<div data-dismissible="disable-bnfw-help-notice-forever" class="updated notice notice-success is-dismissible">
			<p>
				<?php _e( 'If you send out notifications with BNFW but don\'t receive them, you may need to install an SMTP plugin to <a href="https://betternotificationsforwp.com/documentation/getting-started/how-to-improve-email-delivery/" target="_blank">improve email deliverability</a>. I recommend using <a href="https://wordpress.org/plugins/post-smtp/" target="_blank">Post SMTP</a> as it\'s easy to set-up or <a href="https://wordpress.org/plugins/email-log/" target="_blank">Email Log</a> to just log and view emails that are sent.', 'bnfw' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Should the users count message be shown?
	 *
	 * @since 1.7
	 *
	 * @param array $setting Notification Setting.
	 *
	 * @return bool True if message should be shown.
	 */
	protected function should_show_users_count_msg( $setting ) {
		$users = $setting['users'];

		if ( count( $users ) > 200 ) {
			return true;
		}

		$emails = BNFW::factory()->engine->get_emails_from_users( $users );

		if ( count( $emails ) > 200 ) {
			return true;
		}

		return false;
	}
}
