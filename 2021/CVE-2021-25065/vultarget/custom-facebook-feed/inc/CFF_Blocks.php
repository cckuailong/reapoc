<?php
/**
 * Custom Facebook Feed block with live preview.
 *
 * @since 2.3
 */
namespace CustomFacebookFeed;

class CFF_Blocks {

	/**
	 * Indicates if current integration is allowed to load.
	 *
	 * @since 1.8
	 *
	 * @return bool
	 */
	public function allow_load() {
		return function_exists( 'register_block_type' );
	}

	/**
	 * Loads an integration.
	 *
	 * @since 2.3
	 */
	public function load() {
		$this->hooks();
	}

	/**
	 * Integration hooks.
	 *
	 * @since 2.3
	 */
	protected function hooks() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register Custom Facebook Feed Gutenberg block on the backend.
	 *
	 * @since 2.3
	 */
	public function register_block() {

		wp_register_style(
			'cff-blocks-styles',
			trailingslashit( CFF_PLUGIN_URL ) . 'assets/css/cff-blocks.css',
			array( 'wp-edit-blocks' ),
			CFFVER
		);

		$attributes = array(
			'shortcodeSettings' => array(
				'type' => 'string',
			),
			'noNewChanges' => array(
				'type' => 'boolean',
			),
			'executed' => array(
				'type' => 'boolean',
			)
		);

		register_block_type(
			'cff/cff-feed-block',
			array(
				'attributes'      => $attributes,
				'render_callback' => array( $this, 'get_feed_html' ),
			)
		);
	}

	/**
	 * Load Custom Facebook Feed Gutenberg block scripts.
	 *
	 * @since 2.3
	 */
	public function enqueue_block_editor_assets() {
		$access_token = get_option('cff_access_token');

		\cff_main()->enqueue_styles_assets();
		\cff_main()->enqueue_scripts_assets();

		#cff_add_my_stylesheet();
		#cff_scripts_method();

		wp_enqueue_style( 'cff-blocks-styles' );
		wp_enqueue_script(
			'cff-feed-block',
			trailingslashit( CFF_PLUGIN_URL ) . 'assets/js/cff-blocks.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			CFFVER,
			true
		);

		$shortcodeSettings = '';

		$i18n = array(
			'addSettings'         => esc_html__( 'Add Settings', 'custom-facebook-feed' ),
			'shortcodeSettings'   => esc_html__( 'Shortcode Settings', 'custom-facebook-feed' ),
			'example'             => esc_html__( 'Example', 'custom-facebook-feed' ),
			'preview'             => esc_html__( 'Apply Changes', 'custom-facebook-feed' ),

		);

		if ( ! empty( $_GET['cff_wizard'] ) ) {
			$shortcodeSettings = 'feed="' . (int)$_GET['cff_wizard'] . '"';
		}

		wp_localize_script(
			'cff-feed-block',
			'cff_block_editor',
			array(
				'wpnonce'  => wp_create_nonce( 'facebook-blocks' ),
				'canShowFeed' => ! empty( $access_token ),
				'configureLink' => get_admin_url() . '?page=cff-settings',
				'shortcodeSettings'    => $shortcodeSettings,
				'i18n'     => $i18n,
			)
		);
	}

	/**
	 * Get form HTML to display in a Custom Facebook Feed Gutenberg block.
	 *
	 * @param array $attr Attributes passed by Custom Facebook Feed Gutenberg block.
	 *
	 * @since 2.3
	 *
	 * @return string
	 */
	public function get_feed_html( $attr ) {

		$return = '';

		$shortcode_settings = isset( $attr['shortcodeSettings'] ) ? $attr['shortcodeSettings'] : '';

		if ( empty( $cff_statuses['support_legacy_shortcode'] ) ) {
			if ( empty( $shortcode_settings ) || strpos( $shortcode_settings, 'feed=' ) === false ){
				$feeds = \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_feed_list();
				$feed_id = $feeds[0]['id'];
				$shortcode_settings .= ' feed="' . (int)$feed_id . '"';
			}
		}

		$shortcode_settings = str_replace(array( '[custom-facebook-feed', ']' ), '', $shortcode_settings);

		$return .= do_shortcode( '[custom-facebook-feed '.$shortcode_settings.']' );

		return $return;

	}

	/**
	 * Checking if is Gutenberg REST API call.
	 *
	 * @since 2.3
	 *
	 * @return bool True if is Gutenberg REST API call.
	 */
	public static function is_gb_editor() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context']; // phpcs:ignore
	}

}
