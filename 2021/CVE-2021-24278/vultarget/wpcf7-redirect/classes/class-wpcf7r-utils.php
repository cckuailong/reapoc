<?php
/**
 * Class WPCF7r_Utils file.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact form 7 redirect utilities
 */
class WPCF7r_Utils {
	public $banner_version = 1.01;
	public static $instance;
	public static $actions_list      = array();
	public static $rendered_elements = array();

	public function __construct() {
		self::$instance = $this;

		$this->api = new Qs_Api();
	}

	/**
	 * Add a message to the session collector
	 *
	 * @param $type
	 * @param $message
	 */
	public static function add_admin_notice( $type, $message ) {
		$_SESSION['wpcf7r_admin_notices'][ $type ] = $message;
	}

	/**
	 * Register a new type of action
	 *
	 * @param  $name
	 * @param  $title
	 * @param  $class
	 */
	public static function register_wpcf7r_actions( $name, $title, $class, $order ) {
		self::$actions_list[ $name ] = array(
			'label'   => $title,
			'attr'    => '',
			'handler' => $class,
			'order'   => $order,
		);
	}

	/**
	 * Get action name
	 *
	 * @param $action_type
	 */
	public static function get_action_name( $action_type ) {
		return isset( self::$actions_list[ $action_type ] ) ? self::$actions_list[ $action_type ]['label'] : $action_type;
	}

	/**
	 * Get the available actions
	 */
	public static function get_wpcf7r_actions() {
		return self::$actions_list;
	}

	/**
	 * Duplicate all action posts and connect it to the new created form
	 *
	 * @param $new_cf7
	 */
	public function duplicate_form_support( $new_cf7 ) {

		if ( isset( $_POST['wpcf7-copy'] ) && 'Duplicate' === $_POST['wpcf7-copy'] || ( isset( $_GET['action'] ) && 'copy' === $_GET['action'] ) ) {

			$original_post_id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : (int) $_GET['post'];

			$original_cf7 = get_cf7r_form( $original_post_id );

			$original_action_posts = $original_cf7->get_actions( 'default' );

			if ( $original_action_posts ) {
				foreach ( $original_action_posts as $original_action_post ) {
					$new_post_id = $this->duplicate_post( $original_action_post->action_post );

					update_post_meta( $new_post_id, 'wpcf7_id', $new_cf7->id() );
				}
			}
		}
	}

	/**
	 * After form deletion delete all its actions
	 *
	 * @param int $post_id
	 */
	public function delete_all_form_actions( $post_id ) {
		global $post_type;

		if ( get_post_type( $post_id ) === 'wpcf7_contact_form' ) {

			$wpcf7r = get_cf7r_form( $post_id );

			$action_posts = $wpcf7r->get_actions( 'default' );

			if ( $action_posts ) {
				foreach ( $action_posts as $action_post ) {
					wp_delete_post( $action_post->get_id() );
				}
			}
		};

	}

	/**
	 * Dupplicate contact form and all its actions
	 *
	 * @param $action
	 */
	public function duplicate_post( $action ) {
		global $wpdb;

		// if you don't want current user to be the new post author,
		// then change next couple of lines to this: $new_post_author = $post->post_author;
		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;
		$post_id         = $action->ID;

		// if post data exists, create the post duplicate
		if ( isset( $action ) && null !== $action ) {
			// new post data array
			$args = array(
				'comment_status' => $action->comment_status,
				'ping_status'    => $action->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $action->post_content,
				'post_excerpt'   => $action->post_excerpt,
				'post_name'      => $action->post_name,
				'post_parent'    => $action->post_parent,
				'post_password'  => $action->post_password,
				'post_status'    => 'private',
				'post_title'     => $action->post_title,
				'post_type'      => $action->post_type,
				'to_ping'        => $action->to_ping,
				'menu_order'     => $action->menu_order,
			);

			// insert the post by wp_insert_post() function
			$new_post_id = wp_insert_post( $args );

			// get all current post terms ad set them to the new post draft
			$taxonomies = get_object_taxonomies( $action->post_type );

			// returns array of taxonomy names for post type, ex array("category", "post_tag");
			if ( $taxonomies ) {
				foreach ( $taxonomies as $taxonomy ) {
					$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
					wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
				}
			}

			// duplicate all post meta just in two SQL queries
			$sql = $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id='%s'", $post_id );

			$post_meta_infos = $wpdb->get_results( $sql );

			if ( count( $post_meta_infos ) !== 0 ) {
				$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

				foreach ( $post_meta_infos as $meta_info ) {
					$meta_key = $meta_info->meta_key;
					if ( '_wp_old_slug' === $meta_key ) {
						continue;
					}
					$meta_value      = addslashes( $meta_info->meta_value );
					$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
				}

				$sql_query .= implode( ' UNION ALL ', $sql_query_sel );

				$wpdb->query( $sql_query );
			}

			return $new_post_id;
		}
	}

	/**
	 * Set actions order
	 */
	public function set_action_menu_order() {
		global $wpdb;

		parse_str( $_POST['data']['order'], $data );

		if ( ! is_array( $data ) ) {
			return false;
		}

		// get objects per now page
		$id_arr = array();
		foreach ( $data as $key => $values ) {
			foreach ( $values as $position => $id ) {
				$id_arr[] = $id;
			}
		}

		foreach ( $id_arr as $key => $post_id ) {
			$menu_order = $key + 1;
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_order ), array( 'ID' => intval( $post_id ) ) );
		}
	}

	/**
	 * Render elements required by actions
	 *
	 * @return void
	 */
	public function render_actions_elements( $properties, $form ) {

		$action_posts = wpcf7r_get_actions( 'wpcf7r_action', -1, $form->id(), 'default', array(), true );

		if ( $action_posts ) {
			foreach ( $action_posts as $action_post ) {
				$action = WPCF7R_Action::get_action( $action_post );

				if ( ! isset( self::$rendered_elements[ $action_post->ID ] ) ) {
					// these actions will run once.
					if ( is_object( $action ) && ! is_wp_error( $action ) && method_exists( $action, 'render_callback_once' ) ) {
						$properties = $action->render_callback_once( $properties, $form );
					}

					self::$rendered_elements[ $action_post->ID ] = $action_post->ID;
				}

				// Render_callback will be called several times because of the way contact form 7 uses these properties.
				// use state and db on the action to limit it to run only once.
				if ( is_object( $action ) && ! is_wp_error( $action ) && method_exists( $action, 'render_callback' ) ) {
					$properties = $action->render_callback( $properties, $form );
				}
			}
		}

		return $properties;
	}
	/**
	 * Delete an action
	 */
	public function delete_action_post() {
		$data = isset( $_POST['data'] ) ? $_POST['data'] : '';

		$response['status'] = 'failed';

		if ( $data ) {
			foreach ( $data as $post_to_delete ) {
				if ( $post_to_delete ) {
					wp_trash_post( $post_to_delete['post_id'] );
					$response['status'] = 'deleted';
				}
			}
		}

		wp_send_json( $response );
	}

	/**
	 * Show notices on admin panel
	 */
	public function show_admin_notices() {
		global $wp_sessions;

		if ( ! isset( $_SESSION['wpcf7r_admin_notices'] ) ) {
			return;
		}

		foreach ( $_SESSION['wpcf7r_admin_notices'] as $notice_type => $notice ) :
			?>

			<div class="notice notice-error is-dismissible <?php echo $notice_type; ?>">
				<p><?php echo $notice; ?></p>
			</div>

			<?php
		endforeach;
	}

	/**
	 * Send debug data to querysol support api
	 *
	 * @return void
	 */
	public function send_debug_info() {
		$data = isset( $_POST['data'] ) ? $_POST['data'] : '';

		if ( $data['form_id'] ) {
			$debug_data = WPCF7r_Form_Helper::get_debug_data( $data['form_id'] );

			$api = new Qs_Api();

			$args = array(
				'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			);

			$url = add_query_arg( 'site_url', home_url(), WPCF7_PRO_REDIRECT_DEBUG_URL );

			$api->api_call( $url, json_encode( array( 'debug_data' => $debug_data ) ), $args );
		}

		wp_send_json_success();
	}
	/**
	 * Auto function to migrate old plugin to the new one
	 *
	 * @param $migration_action migrate_from_cf7_redirect/migrate_from_cf7_api
	 * @param boolean                                                         $force
	 */
	public static function auto_migrate( $migration_action, $force = false ) {

		$instance = self::get_instance();

		$cf7_forms = self::get_all_cf7_forms();

		foreach ( $cf7_forms as $cf7_form_id ) {

			$cf7r_form = new WPCF7R_Form( $cf7_form_id );

			$instance->delete_all_form_actions( $cf7_form_id );

			if ( ! $cf7r_form->has_migrated( $migration_action ) || $force ) {
				$instance->convert_to_action( $cf7r_form, $migration_action, $cf7_form_id, 'default' );

				$cf7r_form->update_migration( $migration_action );
			}
		}
	}

	/**
	 * Create form and actions based on debug info
	 *
	 * @return void
	 */
	public function import_from_debug() {
		$data = isset( $_POST['data'] ) && $_POST['data'] ? $_POST['data'] : '';

		if ( $data ) {
			$formdata = unserialize( base64_decode( $data['debug_info'] ) );

			$this->install_plugins( json_decode( $formdata['plugins'] ) );

			$form_id = $this->import_form( $formdata );

			$this->import_actions( $form_id, $formdata['actions'] );

		}
	}

	/**
	 * Import actions to post
	 *
	 * @return void
	 */
	private function import_actions( $form_id, $actions ) {
		foreach ( $actions as $action ) {
			$post = (array) $action->action_post;
			unset( $post['ID'] );

			$post_id = wp_insert_post( $post );

			foreach ( $action->fields_values as $meta_key => $meta_values ) {
				if ( 'wpcf7_id' === $meta_key ) {
					continue;
				}
				foreach ( $meta_values as $meta_value ) {
					add_post_meta( $post_id, $meta_key, maybe_unserialize( $meta_value ) );
				}
			}

			update_post_meta( $post_id, 'wpcf7_id', $form_id );

		}
	}
	/**
	 * Import form from debug info
	 *
	 * @return void
	 */
	private function import_form( $formdata ) {

		$new_form_post = (array) $formdata['form_post'];

		unset( $new_form_post['ID'] );

		$form_id = wp_insert_post( $new_form_post );

		foreach ( $formdata['form_meta'] as $meta_key => $meta_values ) {
			foreach ( $meta_values as $meta_value ) {
				add_post_meta( $form_id, $meta_key, maybe_unserialize( $meta_value ) );
			}
		}

		return $form_id;
	}
	/**
	 * Install a list of plugins
	 *
	 * @return void
	 */
	private function install_plugins( $plugins ) {

		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/misc.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$results = array();

		foreach ( $plugins as $slug => $plugin ) {

			if ( ! is_plugin_active( $slug ) ) {
				$results[ $slug ] = $this->install_plugin( $slug );
			}
		}

		return $results;
	}

	/**
	 * Install and activate a plugin
	 *
	 * @return void
	 */
	public function install_plugin( $plugin_slug ) {

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => basename( $plugin_slug, '.php' ),
				'fields' => array(
					'short_description' => false,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				),
			)
		);

		if ( ! is_wp_error( $api ) ) {
			$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );

			$upgrader->install( $api->download_link );

			if ( ! is_wp_error( $upgrader->skin->api ) ) {
				return activate_plugin( $plugin_slug );
			} else {
				return $upgrader->skin->api;
			}
		} else {
			return $api;
		}

	}

	/**
	 * Get all Contact Forms 7 forms
	 */
	public static function get_all_cf7_forms() {
		$args = array(
			'post_type'        => 'wpcf7_contact_form',
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'suppress_filters' => true,
		);

		$cf7_forms = get_posts( $args );

		return $cf7_forms;
	}

	/**
	 * Duplicate an existing action and connect it with the form.
	 *
	 * @return void
	 */
	public function duplicate_action() {
		$results['action_row'] = '';

		if ( isset( $_POST['data'] ) ) {
			$action_data = $_POST['data'];

			$action_post_id = $action_data['post_id'];

			$action_post = get_post( $action_post_id );

			$new_action_post_id = $this->duplicate_post( $action_post );

			update_post_meta( $new_action_post_id, 'wpcf7_id', $action_data['form_id'] );

			$action = WPCF7R_Action::get_action( $new_action_post_id );

			$results['action_row'] = $action->get_action_row();
		}

		wp_send_json( $results );
	}
	/**
	 * Create a new action post
	 */
	public function add_action_post() {
		$results['action_row'] = '';

		$post_id     = isset( $_POST['data']['post_id'] ) ? (int) sanitize_text_field( $_POST['data']['post_id'] ) : '';
		$rule_id     = isset( $_POST['data']['rule_id'] ) ? sanitize_text_field( $_POST['data']['rule_id'] ) : '';
		$action_type = isset( $_POST['data']['action_type'] ) ? sanitize_text_field( $_POST['data']['action_type'] ) : '';

		$rule_name = __( 'New Action', 'wpcf7-redirect' );

		$this->cf7r_form = get_cf7r_form( $post_id );

		$actions = array();

		// migrate from old api plugin
		if ( 'migrate_from_cf7_api' === $action_type || 'migrate_from_cf7_redirect' === $action_type ) {
			if ( ! $this->cf7r_form->has_migrated( $action_type ) ) {
				$actions = $this->convert_to_action( $action_type, $post_id, $rule_name, $rule_id );
				$this->cf7r_form->update_migration( $action_type );
			}
		} else {
			$actions[] = $this->create_action( $post_id, $rule_name, $rule_id, $action_type );
		}

		if ( $actions ) {
			foreach ( $actions as $action ) {
				if ( ! is_wp_error( $action ) ) {
					$results['action_row'] .= $action->get_action_row();
				} else {
					wp_send_json( $results );
				}
			}
		} else {
			$results['action_row'] = '';
		}

		wp_send_json( $results );
	}

	/**
	 * Convert old plugin data to new structure
	 *
	 * @param  $required_conversion
	 * @param  $post_id
	 * @param  $rule_name
	 * @param  $rule_id
	 * @return Actions
	 *
	 * @version 1.2
	 */

	private function convert_to_action( $cf7r_form, $required_conversion, $post_id, $rule_id ) {
		$actions = array();

		if ( 'migrate_from_cf7_redirect' === $required_conversion ) {
			$old_api_action = $cf7r_form->get_cf7_redirection_settings();

			if ( $old_api_action ) {
				// CREATE JAVSCRIPT ACTION.
				if ( $old_api_action['fire_sctipt'] ) {
					$javscript_action = $this->create_action( $post_id, __( 'Migrated Javascript Action From Old Plugin', 'wpcf7-redirect' ), $rule_id, 'FireScript' );

					$javscript_action->set( 'script', $old_api_action['fire_sctipt'] );
					$javscript_action->set( 'action_status', 'on' );

					unset( $old_api_action['fire_sctipt'] );

					$actions[] = $javscript_action;
				}

				// CREATE REDIRECT ACTION.
				$action = $this->create_action( $post_id, __( 'Migrated Redirect Action From Old Plugin', 'wpcf7-redirect' ), $rule_id, 'redirect' );

				$action->set( 'action_status', 'on' );

				foreach ( $old_api_action as $key => $value ) {
					$action->set( $key, $value );
				}

				$actions[] = $action;

			}
		} elseif ( 'migrate_from_cf7_api' === $required_conversion ) {
			$old_api_action = $cf7r_form->get_cf7_api_settings();

			if ( $old_api_action ) {

				$old_api__wpcf7_api_data = $old_api_action['_wpcf7_api_data'];
				$old_tags_map            = $old_api_action['_wpcf7_api_data_map'];

				if ( 'params' === $old_api__wpcf7_api_data['input_type'] ) {
					$action_type = 'api_url_request';
				} elseif ( 'xml' === $old_api__wpcf7_api_data['input_type'] || 'json' === $old_api__wpcf7_api_data['input_type'] ) {
					$action_type = 'api_json_xml_request';
				}

				$action = $this->create_action( $post_id, __( 'Migrated Data from Old Plugin', 'wpcf7-redirect' ), $rule_id, $action_type );

				if ( ! is_wp_error( $action ) ) {
					$action->set( 'base_url', $old_api__wpcf7_api_data['base_url'] );
					$action->set( 'input_type', strtolower( $old_api__wpcf7_api_data['method'] ) );
					$action->set( 'record_type', strtolower( $old_api__wpcf7_api_data['input_type'] ) );
					$action->set( 'show_debug', '' );
					$action->set( 'action_status', $old_api__wpcf7_api_data['send_to_api'] );

					$tags_map = array();

					if ( $old_tags_map ) {
						foreach ( $old_tags_map as $tag_key => $tag_api_key ) {
							$tags_map[ $tag_key ] = $tag_api_key;
						}

						$action->set( 'tags_map', $tags_map );
					}

					if ( isset( $old_api_action['_template'] ) && $old_api_action['_template'] ) {
						$action->set( 'request_template', $old_api_action['_template'] );
					} elseif ( isset( $old_api_action['_json_template'] ) && $old_api_action['_json_template'] ) {
						$action->set( 'request_template', $old_api_action['_json_template'] );
					}

					$actions[] = $action;
				}
			}
		}

		return $actions;
	}

	/**
	 * Create new post that will hold the action
	 *
	 * @param  $rule_name
	 * @param  $rule_id
	 * @param  $action_type
	 * @return Actions
	 */
	public function create_action( $post_id, $rule_name, $rule_id, $action_type ) {
		$new_action_post = array(
			'post_type'   => 'wpcf7r_action',
			'post_title'  => $rule_name,
			'post_status' => 'private',
			'menu_order'  => 1,
			'meta_input'  => array(
				'wpcf7_id'      => $post_id,
				'wpcf7_rule_id' => $rule_id,
				'action_type'   => $action_type,
				'action_status' => 'on',
			),
		);

		$new_action_id = wp_insert_post( $new_action_post );

		return WPCF7R_Action::get_action( $new_action_id, $post_id );
	}

	/**
	 * Get instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get the banner template
	 */
	public function get_banner() {
		if ( $this->get_option( 'last_banner_displayed' ) === $this->banner_version ) {
			return;
		}
		ob_start();

		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'banner.php';

		$banner_html = ob_get_clean();

		echo $banner_html;
	}

	/**
	 * Show a message containing the license details
	 */
	public function license_details_message() {
		if ( ! self::get_activation_id() ) {
			printf(
				'<tr class="plugin-update-tr active" id="wpcf7-redirect-pro-update" data-slug="wpcf7-redirect-pro" data-plugin="contact-form-7-redirection-pro/wpcf7-redirect-pro.php"><td colspan="3" class="plugin-update colspanchange"><div class="update-message notice inline notice-warning notice-alt"><p><strong>%s</strong> %s</p></div></td></tr>',
				__( 'Please activate plugin license for updates', 'wpcf7-redirect' ),
				self::get_settings_link()
			);

		}

	}

	/**
	 * Get all data related with plugin activation
	 */
	public static function get_activation_data() {
		return get_option( 'wpcf7r_activation_data' );
	}

	/**
	 * Get date of plugin license expiration
	 */
	public static function get_activation_expiration() {
		return get_option( 'wpcf7r_activation_expiration' );
	}

	/**
	 * A validation function to test the serial key
	 */
	public static function validate_serial_key() {
		$instance = self::get_instance();

		$serial        = self::get_serial_key();
		$activation_id = self::get_activation_id();

		return $instance->api->validate_serial( $activation_id, $serial );
	}

	/**
	 * Get the used serial key
	 */
	public static function get_serial_key() {
		return get_option( 'wpcf7r_serial_number' );
	}

	/**
	 * Delete the used setial key
	 */
	public static function delete_serial_key() {
		return delete_option( 'wpcf7r_serial_number' );
	}

	/**
	 * Get a url to deactivate plugin license
	 */
	public static function get_deactivation_link() {
		$url = self::get_plugin_settings_page_url();

		$url = add_query_arg( 'wpcf7r_deactivate_license', '', $url );

		return $url;
	}

	/**
	 * Get the plugin settings link
	 */
	public static function get_plugin_settings_page_url() {
		return get_admin_url( null, 'options-general.php?page=wpc7_redirect' );
	}

	/**
	 * Get the activation id
	 */
	public static function get_activation_id() {
		return get_option( 'wpcf7r_activation_id' );
	}

	/**
	 * Get a link to the admin settings panel
	 */
	public static function get_settings_link() {
		return '<a href="' . self::get_plugin_settings_page_url() . '">' . __( 'Settings', 'wpcf7-redirect' ) . '</a>';
	}

	/**
	 * Close banner
	 */
	public function close_banner() {
		$this->update_option( 'last_banner_displayed', $this->banner_version );
	}

	/**
	 * Get specific option by key
	 */
	public function get_option( $key ) {
		$options = $this->get_wpcf7_options();

		return isset( $options[ $key ] ) ? $options[ $key ] : '';
	}

	/**
	 * Update specific option
	 *
	 * @param $key
	 * @param $value
	 */
	public function update_option( $key, $value ) {
		$options = $this->get_wpcf7_options();

		$options[ $key ] = $value;

		$this->save_wpcf7_options( $options );

	}

	/**
	 * Get the plugin options
	 */
	public function get_wpcf7_options() {
		return get_option( 'wpcf_redirect_options' );
	}

	/**
	 * Save the plugin options
	 *
	 * @param $options
	 */
	public function save_wpcf7_options( $options ) {
		update_option( 'wpcf_redirect_options', $options );
	}

	/**
	 * Get a list of avaiable text functions and callbacks
	 *
	 * @param string $func
	 * @param string $field_type
	 */
	public static function get_available_text_functions( $func = '', $field_type = '' ) {
		$functions = array(
			'md5'           => array( 'WPCF7r_Utils', 'func_md5' ),
			'base64_encode' => array( 'WPCF7r_Utils', 'func_base64_encode' ),
			'utf8_encode'   => array( 'WPCF7r_Utils', 'func_utf8_encode' ),
			'urlencode'     => array( 'WPCF7r_Utils', 'func_urlencode' ),
			'json_encode'   => array( 'WPCF7r_Utils', 'func_json_encode' ),
			'esc_html'      => array( 'WPCF7r_Utils', 'func_esc_html' ),
			'esc_attr'      => array( 'WPCF7r_Utils', 'func_esc_attr' ),
		);

		if ( 'checkbox' === $field_type || 'checkbox*' === $field_type || 'all' === $field_type ) {
			$functions['implode'] = array( 'WPCF7r_Utils', 'func_implode' );
		}

		$functions = apply_filters( 'get_available_text_functions', $functions );

		if ( $func ) {
			return isset( $functions[ $func ] ) ? $functions[ $func ] : '';
		}

		return $functions;
	}

	/**
	 * [func_utf8_encode description]
	 *
	 * @param  $value
	 */
	public static function func_utf8_encode( $value ) {
		return apply_filters( 'func_utf8_encode', utf8_encode( $value ), $value );
	}

	/**
	 * [func_base64_encode description]
	 *
	 * @param  $value
	 */
	public static function func_base64_encode( $value ) {
		return apply_filters( 'func_base64_encode', base64_encode( $value ), $value );
	}

	/**
	 * [func_base64_encode description]
	 *
	 * @param  $value
	 */
	public static function func_urlencode( $value ) {
		return apply_filters( 'func_urlencode', urlencode( $value ), $value );
	}

		/**
		 * Esc html callback
		 *
		 * @param $value
		 */
	public function func_esc_html( $value ) {
		return apply_filters( 'func_esc_html', esc_html( $value ), $value );
	}

	/**
	 * Esc Attr callback
	 *
	 * @param $value
	 */
	public function func_esc_attr( $value ) {
		return apply_filters( 'func_esc_attr', esc_attr( $value ), $value );
	}

	/**
	 * Json Encode callback
	 *
	 * @param $value
	 */
	public function func_json_encode( $value ) {
		return apply_filters( 'func_json_encode', wp_json_encode( $value ), $value );
	}
	/**
	 * [func_base64_encode description]
	 *
	 * @param  $value
	 */
	public static function func_implode( $value ) {

		if ( is_array( $value ) ) {
			$value = apply_filters( 'func_implode', implode( ',', $value ), $value );
		}

		return $value;
	}

	/**
	 * md5 function
	 *
	 * @param  $value
	 */
	public static function func_md5( $value ) {
		return apply_filters( 'func_md5', md5( $value ), $value );
	}

	public function make_api_test() {
		parse_str( $_POST['data']['data'], $data );

		if ( ! is_array( $data ) ) {
			die( '-1' );
		}

		$action_id = isset( $_POST['data']['action_id'] ) ? (int) sanitize_text_field( $_POST['data']['action_id'] ) : '';
		$cf7_id    = isset( $_POST['data']['cf7_id'] ) ? (int) sanitize_text_field( $_POST['data']['cf7_id'] ) : '';
		$rule_id   = isset( $_POST['data']['rule_id'] ) ? $_POST['data']['rule_id'] : '';

		add_filter( 'after_qs_cf7_api_send_lead', array( $this, 'after_fake_submission' ), 10, 3 );

		if ( isset( $data['wpcf7-redirect']['actions'] ) ) {
			$response = array();

			$posted_action = reset( $data['wpcf7-redirect']['actions'] );
			$posted_action = $posted_action['test_values'];
			$_POST         = $posted_action;
			// this will create a fake form submission
			$this->cf7r_form = get_cf7r_form( $cf7_id );
			$this->cf7r_form->enable_action( $action_id );

			$cf7_form   = $this->cf7r_form->get_cf7_form_instance();
			$submission = WPCF7_Submission::get_instance( $cf7_form );

			if ( $submission->get_status() === 'validation_failed' ) {
				$invalid_fields             = $submission->get_invalid_fields();
				$response['status']         = 'failed';
				$response['invalid_fields'] = $invalid_fields;
			} else {
				$response['status'] = 'success';
				$response['html']   = $this->get_test_api_results_html();
			}

			wp_send_json( $response );
		}
	}
	/**
	 * Store the results from the API
	 *
	 * @param  $result
	 * @param  $record
	 */
	public function after_fake_submission( $result, $record, $args ) {
		$this->results = $result;
		$this->record  = $record;
		$this->request = $args;

		return $result;
	}

	/**
	 * Show A preview for the action
	 */
	public function show_action_preview() {
		if ( isset( $_GET['wpcf7r-preview'] ) ) {
			$action_id = (int) $_GET['wpcf7r-preview'];

			$action = WPCF7R_Action::get_action( $action_id );

			$action->dynamic_params['popup-template'] = isset( $_GET['template'] ) ? sanitize_text_field( $_GET['template'] ) : '';

			$action->preview();
		}
	}

	/**
	 * Get action template in case field are dynamicaly changed
	 */
	public function get_action_template() {
		$data = isset( $_POST['data'] ) ? $_POST['data'] : '';

		$response = array();

		if ( isset( $data['action_id'] ) ) {
			$action_id      = (int) $data['action_id'];
			$popup_template = sanitize_text_field( $data['template'] );

			$action = WPCF7R_Action::get_action( $action_id );

			ob_start();

			$params = array(
				'popup-template' => $popup_template,
			);

			$action->get_action_settings( $params );

			$response['action_content'] = ob_get_clean();
		}

		wp_send_json_success( $response );
	}

	/**
	 * Get the popup html
	 */
	public function get_test_api_results_html() {
		ob_start();

		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'popup-api-test.php';

		return ob_get_clean();
	}
}
