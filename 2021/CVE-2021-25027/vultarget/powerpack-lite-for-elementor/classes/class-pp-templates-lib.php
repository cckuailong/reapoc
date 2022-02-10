<?php
/**
 * PowerPack Template Library.
 *
 * @package PowerPackElements
 */

namespace PowerpackElementsLite\Classes;

use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Base;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\User;

/**
 * PowerPack Template Library.
 *
 * @since 1.6.0
 */
class PP_Templates_Lib {
	/**
	 * PowerPack library option key.
	 */
	const LIBRARY_OPTION_KEY = 'pp_templates_library';

	/**
	 * API templates URL.
	 *
	 * Holds the URL of the templates API.
	 *
	 * @access public
	 * @static
	 *
	 * @var string API URL.
	 */
	public static $api_url = 'https://demo.powerpackelements.com/api/powerpack/v1/templates';

	/**
	 * Init.
	 *
	 * Initializes the hooks.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'elementor/init', [ __CLASS__, 'register_source' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'enqueue_editor_scripts' ] );
		add_action( 'elementor/ajax/register_actions', [ __CLASS__, 'register_ajax_actions' ] );
		add_action( 'elementor/editor/footer', [ __CLASS__, 'render_template' ] );
		// add_action( 'wp_ajax_elementor_reset_library', [ __CLASS__, 'ajax_reset_api_data' ] ); @codingStandardsIgnoreLine.
	}

	/**
	 * Register source.
	 *
	 * Registers the library source.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return void
	 */
	public static function register_source() {
		Plugin::$instance->templates_manager->register_source( __NAMESPACE__ . '\PP_Source' );
	}

	/**
	 * Enqueue Editor Scripts.
	 *
	 * Enqueues required scripts in Elementor edit mode.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return void
	 */
	public static function enqueue_editor_scripts() {
		wp_enqueue_script(
			'powerpack-templates-lib',
			POWERPACK_ELEMENTS_LITE_URL . 'assets/js/pp-templates-lib.js',
			[
				'jquery',
				'backbone-marionette',
				'backbone-radio',
				'elementor-common-modules',
				'elementor-dialog',
				'powerpack-editor',
			],
			POWERPACK_ELEMENTS_LITE_VER,
			true
		);

		wp_localize_script( 'powerpack-templates-lib', 'pp_templates_lib', array(
			'logoUrl'	=> POWERPACK_ELEMENTS_LITE_URL . 'assets/images/pp-logo-sm.png',
		) );
	}

	/**
	 * Init ajax calls.
	 *
	 * Initialize template library ajax calls for allowed ajax requests.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param Ajax $ajax Elementor's Ajax object.
	 * @return void
	 */
	public static function register_ajax_actions( Ajax $ajax ) {
		$library_ajax_requests = [
			'pp_get_library_data',
		];

		foreach ( $library_ajax_requests as $ajax_request ) {
			$ajax->register_ajax_action( $ajax_request, function( $data ) use ( $ajax_request ) {
				return self::handle_ajax_request( $ajax_request, $data );
			} );
		}
	}

	/**
	 * Handle ajax request.
	 *
	 * Fire authenticated ajax actions for any given ajax request.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param string $ajax_request Ajax request.
	 * @param array  $data Elementor data.
	 *
	 * @return mixed
	 * @throws \Exception Throws error message.
	 */
	private static function handle_ajax_request( $ajax_request, array $data ) {
		if ( ! User::is_current_user_can_edit_post_type( Source_Local::CPT ) ) {
			throw new \Exception( 'Access Denied' );
		}

		if ( ! empty( $data['editor_post_id'] ) ) {
			$editor_post_id = absint( $data['editor_post_id'] );

			if ( ! get_post( $editor_post_id ) ) {
				throw new \Exception( __( 'Post not found.', 'powerpack' ) );
			}

			Plugin::$instance->db->switch_to_post( $editor_post_id );
		}

		$result = call_user_func( [ __CLASS__, $ajax_request ], $data );

		if ( is_wp_error( $result ) ) {
			throw new \Exception( $result->get_error_message() );
		}

		return $result;
	}

	/**
	 * Get library data.
	 *
	 * Get data for template library.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $args Arguments.
	 *
	 * @return array Collection of templates data.
	 */
	public static function pp_get_library_data( array $args ) {
		$library_data = self::get_library_data( ! empty( $args['sync'] ) );

		// Ensure all document are registered.
		Plugin::$instance->documents->get_document_types();

		return [
			'templates' => self::get_templates(),
			'config' => $library_data['types_data'],
		];
	}

	/**
	 * Get templates.
	 *
	 * Retrieve all the templates from all the registered sources.
	 *
	 * @since 1.16.0
	 * @access public
	 *
	 * @return array Templates array.
	 */
	public static function get_templates() {
		$source = Plugin::$instance->templates_manager->get_source( 'powerpack' );
		return $source->get_items();
	}

	/**
	 * Ajax reset API data.
	 *
	 * Reset Elementor library API data using an ajax call.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 */
	public static function ajax_reset_api_data() {
		check_ajax_referer( 'elementor_reset_library', '_nonce' );

		self::get_templates_data( true );

		wp_send_json_success();
	}

	/**
	 * Get templates data.
	 *
	 * This function the templates data.
	 *
	 * @since 1.6.0
	 * @access private
	 * @static
	 *
	 * @param bool $force_update Optional. Whether to force the data retrieval or
	 *                                     not. Default is false.
	 *
	 * @return array|false Templates data, or false.
	 */
	private static function get_templates_data( $force_update = false ) {
		$cache_key = 'pp_templates_data_' . POWERPACK_ELEMENTS_LITE_VER;

		$templates_data = get_transient( $cache_key );

		if ( $force_update || false === $templates_data ) {
			$timeout = ( $force_update ) ? 25 : 8;

			$response = wp_remote_get( self::$api_url, [
				'timeout' => $timeout,
				'body' => [
					// Which API version is used.
					'api_version' => POWERPACK_ELEMENTS_LITE_VER,
					// Which language to return.
					'site_lang' => get_bloginfo( 'language' ),
				],
			] );

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				set_transient( $cache_key, [], 2 * HOUR_IN_SECONDS );

				return false;
			}

			$templates_data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( empty( $templates_data ) || ! is_array( $templates_data ) ) {
				set_transient( $cache_key, [], 2 * HOUR_IN_SECONDS );

				return false;
			}

			if ( isset( $templates_data['library'] ) ) {
				update_option( self::LIBRARY_OPTION_KEY, $templates_data['library'], 'no' );

				unset( $templates_data['library'] );
			}

			set_transient( $cache_key, $templates_data, 12 * HOUR_IN_SECONDS );
		}

		return $templates_data;
	}

	/**
	 * Get templates data.
	 *
	 * Retrieve the templates data from a remote server.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @param bool $force_update Optional. Whether to force the data update or
	 *                                     not. Default is false.
	 *
	 * @return array The templates data.
	 */
	public static function get_library_data( $force_update = false ) {
		self::get_templates_data( $force_update );

		$library_data = get_option( self::LIBRARY_OPTION_KEY );

		if ( empty( $library_data ) ) {
			return [];
		}

		return $library_data;
	}

	/**
	 * Get template content.
	 *
	 * Retrieve the templates content received from a remote server.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return object|\WP_Error The template content.
	 */
	public static function get_template_content( $template_id ) {
		$url = self::$api_url . '/' . $template_id;
		$license_key = null;

		if ( function_exists( 'pp_get_license_key' ) ) {
			$license_key = pp_get_license_key();
		}
		if ( ! defined( 'POWERPACK_ELEMENTS_VER' ) ) {
			$license_key = 'lite';
		}

		if ( empty( $license_key ) ) {
			return new \WP_Error( 'no_license', __( 'License is not active.', 'powerpack' ) );
		}

		$args = [
			'body' => [
				// Which API version is used.
				'api_version' 	=> POWERPACK_ELEMENTS_LITE_VER,
				'license_key'	=> $license_key,
				'home_url' 		=> trailingslashit( home_url() ),
			],
			'timeout' => 25,
		];

		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			// @codingStandardsIgnoreStart WordPress.XSS.EscapeOutput.
			wp_die( $response, [
				'back_link' => true,
			] );
			// @codingStandardsIgnoreEnd WordPress.XSS.EscapeOutput.
		}

		$body = wp_remote_retrieve_body( $response );
		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( ! $response_code ) {
			return new \WP_Error( 500, 'No Response' );
		}

		// Server sent a success message without content.
		if ( 'null' === $body ) {
			$body = true;
		}

		$as_array = true;
		$body = json_decode( $body, $as_array );

		if ( false === $body ) {
			return new \WP_Error( 422, 'Wrong Server Response' );
		}

		if ( 200 !== $response_code ) {
			// In case $as_array = true.
			$body = (object) $body;

			$message = isset( $body->message ) ? $body->message : wp_remote_retrieve_response_message( $response );
			$code = isset( $body->code ) ? $body->code : $response_code;

			return new \WP_Error( $code, $message );
		}

		return $body;
	}

	/**
	 * Render template.
	 *
	 * Library modal template.
	 *
	 * @since 1.6.0
	 * @access public
	 * @static
	 *
	 * @return void
	 */
	public static function render_template() {
		?>
		<script type="text/template" id="tmpl-elementor-template-library-header-actions-pp">
			<div id="elementor-template-library-header-sync" class="elementor-templates-modal__header__item">
				<i class="eicon-sync" aria-hidden="true" title="<?php esc_attr_e( 'Sync Templates', 'powerpack' ); ?>"></i>
				<span class="elementor-screen-only"><?php echo esc_html__( 'Sync Templates', 'powerpack' ); ?></span>
			</div>
		</script>
		<script type="text/template" id="tmpl-elementor-templates-modal__header__logo_pp">
			<span class="elementor-templates-modal__header__logo__icon-wrapper">
				<img src="<?php echo esc_url( POWERPACK_ELEMENTS_LITE_URL . 'assets/images/pp-logo-sm.png' ); ?>" style="height: 30px;" />
			</span>
			<span class="elementor-templates-modal__header__logo__title">{{{ title }}}</span>
		</script>
		<script type="text/template" id="tmpl-elementor-template-library-header-preview-pp">
			<div id="elementor-template-library-header-preview-insert-wrapper" class="elementor-templates-modal__header__item">
				{{{ pp_templates_lib.templates.layout.getTemplateActionButton( obj ) }}}
			</div>
		</script>
		<script type="text/template" id="tmpl-elementor-template-library-templates-pp">
			<#
				var activeSource = pp_templates_lib.templates.getFilter('source');
			#>
			<div id="elementor-template-library-toolbar">
				<# if ( 'powerpack' === activeSource ) {
					var activeType = pp_templates_lib.templates.getFilter('type');
					#>
					<div id="elementor-template-library-filter-toolbar-remote" class="elementor-template-library-filter-toolbar">
						<# if ( 'new_page' === activeType ) { #>
							<div id="elementor-template-library-order">
								<input type="radio" id="elementor-template-library-order-new" class="elementor-template-library-order-input" name="elementor-template-library-order" value="date">
								<label for="elementor-template-library-order-new" class="elementor-template-library-order-label"><?php echo esc_html__( 'New', 'powerpack' ); ?></label>
								<input type="radio" id="elementor-template-library-order-trend" class="elementor-template-library-order-input" name="elementor-template-library-order" value="trendIndex">
								<label for="elementor-template-library-order-trend" class="elementor-template-library-order-label"><?php echo esc_html__( 'Trend', 'powerpack' ); ?></label>
								<input type="radio" id="elementor-template-library-order-popular" class="elementor-template-library-order-input" name="elementor-template-library-order" value="popularityIndex">
								<label for="elementor-template-library-order-popular" class="elementor-template-library-order-label"><?php echo esc_html__( 'Popular', 'powerpack' ); ?></label>
							</div>
						<# } else {
							var config = pp_templates_lib.templates.getConfig( activeType );
							if ( config.categories ) { #>
								<div id="elementor-template-library-filter">
									<select id="elementor-template-library-filter-subtype" class="elementor-template-library-filter-select" data-elementor-filter="subtype">
										<option></option>
										<# config.categories.forEach( function( category ) {
											var selected = category === pp_templates_lib.templates.getFilter( 'subtype' ) ? ' selected' : '';
											#>
											<option value="{{ category }}"{{{ selected }}}>{{{ category }}}</option>
										<# } ); #>
									</select>
								</div>
							<# }
						} #>
						<div id="elementor-template-library-my-favorites">
							<# var checked = pp_templates_lib.templates.getFilter( 'favorite' ) ? ' checked' : ''; #>
							<input id="elementor-template-library-filter-my-favorites" type="checkbox"{{{ checked }}}>
							<label id="elementor-template-library-filter-my-favorites-label" for="elementor-template-library-filter-my-favorites">
								<i class="eicon" aria-hidden="true"></i>
								<?php echo esc_html__( 'My Favorites', 'powerpack' ); ?>
							</label>
						</div>
					</div>
				<# } #>
				<div id="elementor-template-library-filter-text-wrapper">
					<label for="elementor-template-library-filter-text" class="elementor-screen-only"><?php echo esc_html__( 'Search Templates:', 'powerpack' ); ?></label>
					<input id="elementor-template-library-filter-text" placeholder="<?php echo esc_attr__( 'Search', 'powerpack' ); ?>">
					<i class="eicon-search"></i>
				</div>
			</div>
			<div id="elementor-template-library-templates-container"></div>
			<# if ( 'powerpack' === activeSource ) { #>
				<div id="elementor-template-library-footer-banner">
					<img class="elementor-nerd-box-icon" src="<?php echo esc_url( ELEMENTOR_ASSETS_URL . 'images/information.svg' ); ?>" />
					<div class="elementor-excerpt"><?php echo esc_html__( 'Stay tuned! More awesome templates coming real soon.', 'powerpack' ); ?></div>
				</div>
			<# } #>
		</script>
		<script type="text/template" id="tmpl-elementor-template-library-template-pp">
			<div class="elementor-template-library-template-body">
				<# if ( 'page' === type ) { #>
					<div class="elementor-template-library-template-screenshot" style="background-image: url({{ thumbnail }});"></div>
				<# } else { #>
					<img src="{{ thumbnail }}">
				<# } #>
				<div class="elementor-template-library-template-preview">
					<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
				</div>
			</div>
			<div class="elementor-template-library-template-footer">
				{{{ pp_templates_lib.templates.layout.getTemplateActionButton( obj ) }}}
				<div class="elementor-template-library-template-name">{{{ title }}} - {{{ type }}}</div>
				<div class="elementor-template-library-favorite">
					<input id="elementor-template-library-template-{{ template_id }}-favorite-input" class="elementor-template-library-template-favorite-input" type="checkbox"{{ favorite ? " checked" : "" }}>
					<label for="elementor-template-library-template-{{ template_id }}-favorite-input" class="elementor-template-library-template-favorite-label">
						<i class="eicon-heart-o" aria-hidden="true"></i>
						<span class="elementor-screen-only"><?php echo esc_html__( 'Favorite', 'powerpack' ); ?></span>
					</label>
				</div>
			</div>
		</script>
		<script type="text/template" id="tmpl-elementor-template-library-get-pro-button-pp">
			<a class="elementor-template-library-template-action elementor-button elementor-go-pro" href="https://powerpackelements.com/pricing/?utm_source=panel-library&utm_campaign=gopro&utm_medium=wp-dash" target="_blank">
				<i class="eicon-external-link-square" aria-hidden="true"></i>
				<span class="elementor-button-title"><?php echo __( 'Go Pro', 'powerpack' ); ?></span>
			</a>
		</script>
		<script type="text/template" id="tmpl-elementor-pro-template-library-activate-license-button-pp">
			<a class="elementor-template-library-template-action elementor-button elementor-go-pro" href="<?php echo PP_Admin_Settings::get_form_action(); ?>" target="_blank">
				<i class="eicon-external-link-square"></i>
				<span class="elementor-button-title"><?php _e( 'Activate License', 'powerpack' ); ?></span>
			</a>
		</script>
		<?php
	}
}

PP_Templates_Lib::init();

/**
 * Custom source.
 */
class PP_Source extends Source_Base {
	/**
	 * Get remote template ID.
	 *
	 * Retrieve the remote template ID.
	 *
	 * @since 1.14.5
	 * @access public
	 *
	 * @return string The remote template ID.
	 */
	public function get_id() {
		return 'powerpack';
	}

	/**
	 * Get remote template title.
	 *
	 * Retrieve the remote template title.
	 *
	 * @since 1.14.5
	 * @access public
	 *
	 * @return string The remote template title.
	 */
	public function get_title() {
		return 'PowerPack';
	}

	/**
	 * Register remote template data.
	 *
	 * Used to register custom template data like a post type, a taxonomy or any
	 * other data.
	 *
	 * @since 1.14.5
	 * @access public
	 */
	public function register_data() {}

	/**
	 * Get remote templates.
	 *
	 * Retrieve remote templates from PowerpackElements.com servers.
	 *
	 * @since 1.14.5
	 * @access public
	 *
	 * @param array $args Optional. Nou used in remote source.
	 *
	 * @return array Remote templates.
	 */
	public function get_items( $args = [] ) {
		$library_data = PP_Templates_Lib::get_library_data();
		$is_pro_active = defined( 'POWERPACK_ELEMENTS_VER' );
		$pro_status = 'active';

		if ( ! $is_pro_active ) {
			$pro_status = 'inactive';
		} elseif ( 'valid' !== get_option( 'pp_license_status' ) ) {
			$pro_status = 'license_inactive';
		}

		$templates = [];

		if ( ! empty( $library_data['templates'] ) ) {
			foreach ( $library_data['templates'] as $template_data ) {
				$data = $this->prepare_template( $template_data );
				$data['proStatus'] = $pro_status;
				$templates[] = $data;
			}
		}

		return $templates;
	}

	/**
	 * Get remote template.
	 *
	 * Retrieve a single remote template from PowerPackElements.com servers.
	 *
	 * @since 1.14.5
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return array Remote template.
	 */
	public function get_item( $template_id ) {
		$templates = $this->get_items();

		return $templates[ $template_id ];
	}

	/**
	 * Save remote template.
	 *
	 * Remote template from PowerPackElements.com servers cannot be saved on the
	 * database as they are retrieved from remote servers.
	 *
	 * @since 1.14.5
	 * @access public
	 *
	 * @param array $template_data Remote template data.
	 *
	 * @return \WP_Error
	 */
	public function save_item( $template_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot save template to a remote source' );
	}

	/**
	 * Update remote template.
	 *
	 * Remote template from PowerPackElements.com servers cannot be updated on the
	 * database as they are retrieved from remote servers.
	 *
	 * @since 1.14.5
	 * @access public
	 *
	 * @param array $new_data New template data.
	 *
	 * @return \WP_Error
	 */
	public function update_item( $new_data ) {
		return new \WP_Error( 'invalid_request', 'Cannot update template to a remote source' );
	}

	/**
	 * Delete remote template.
	 *
	 * Remote template from PowerPackElements.com servers cannot be deleted from the
	 * database as they are retrieved from remote servers.
	 *
	 * @since 1.14.5
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Error
	 */
	public function delete_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot delete template from a remote source' );
	}

	/**
	 * Export remote template.
	 *
	 * Remote template from PowerPackElements.com servers cannot be exported from the
	 * database as they are retrieved from remote servers.
	 *
	 * @since 1.14.5
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Error
	 */
	public function export_template( $template_id ) {
		return new \WP_Error( 'invalid_request', 'Cannot export template from a remote source' );
	}

	/**
	 * Get remote template data.
	 *
	 * Retrieve the data of a single remote template from PowerPackElements.com servers.
	 *
	 * @since 1.14.5
	 * @access public
	 *
	 * @param array  $args    Custom template arguments.
	 * @param string $context Optional. The context. Default is `display`.
	 *
	 * @return array|\WP_Error Remote Template data.
	 */
	public function get_data( array $args, $context = 'display' ) {
		$data = PP_Templates_Lib::get_template_content( $args['template_id'] );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$data = (array) $data;

		$data['content'] = $this->replace_elements_ids( $data['content'] );
		$data['content'] = $this->process_export_import_content( $data['content'], 'on_import' );

		$post_id = $args['editor_post_id'];
		$document = Plugin::$instance->documents->get( $post_id );
		if ( $document ) {
			$data['content'] = $document->get_elements_raw_data( $data['content'], true );
		}

		return $data;
	}

	/**
	 * Prepare template.
	 *
	 * Prepare template data.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param array $template_data Collection of template data.
	 * @return array Collection of template data.
	 */
	private function prepare_template( array $template_data ) {
		$favorite_templates = $this->get_user_meta( 'favorites' );

		return [
			'template_id' => $template_data['id'],
			'source' => $this->get_id(),
			'type' => $template_data['type'],
			'subtype' => $template_data['subtype'],
			'title' => $template_data['title'],
			'thumbnail' => $template_data['thumbnail'],
			'date' => $template_data['tmpl_created'],
			'author' => $template_data['author'],
			'tags' => json_decode( $template_data['tags'] ),
			'isPro' => $template_data['is_pro'],
			'url' => $template_data['url'],
			'favorite' => ! empty( $favorite_templates[ $template_data['id'] ] ),
		];
	}
}
