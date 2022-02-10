<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

namespace A3Rev\PageViewsCount;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Blocks {

	public function __construct() {

		add_action( 'init', array( $this, 'register_block' ) );

		// Hook: Frontend assets.
		//add_action( 'enqueue_block_assets', array( $this, 'cgb_block_assets' ) );

		// Hook: Editor assets.
		add_action( 'enqueue_block_editor_assets', array( $this, 'cgb_editor_assets' ) );
		
	}

	/**
	 * Enqueue Gutenberg block assets for both frontend + backend.
	 *
	 * @uses {wp-editor} for WP editor styles.
	 * @since 1.0.0
	 */
	function cgb_block_assets() { // phpcs:ignore
		// Styles.
		wp_enqueue_style(
			'page_views_count-cgb-style-css', // Handle.
			plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
			array( 'wp-editor' ) // Dependency to include the CSS after it.
			// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
		);
	}

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 *
	 * @uses {wp-blocks} for block type registration & related functions.
	 * @uses {wp-element} for WP Element abstraction — structure of blocks.
	 * @uses {wp-i18n} to internationalize the block's text.
	 * @uses {wp-editor} for WP editor styles.
	 * @since 1.0.0
	 */
	function cgb_editor_assets() { // phpcs:ignore
		// Scripts.
		wp_enqueue_script(
			'page_views_count-cgb-block-js', // Handle.
			plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
			// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: File modification time.
			true // Enqueue the script in the footer.
		);

		wp_localize_script( 'page_views_count-cgb-block-js', 'pvcblock', array( 
			'preview'    => A3_PVC_URL.  '/src/blocks/stats/preview.jpg',
		) );

		// Styles.
		// wp_enqueue_style(
		// 	'page_views_count-cgb-block-editor-css', // Handle.
		// 	plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		// 	array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// 	// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
		// );
	}

	public function create_a3blocks_section() {

		add_filter( 'block_categories_all', function( $categories ) {

			$category_slugs = wp_list_pluck( $categories, 'slug' );

			if ( in_array( 'a3rev-blocks', $category_slugs ) ) {
				return $categories;
			}

			return array_merge(
				array(
					array(
						'slug' => 'a3rev-blocks',
						'title' => __( 'a3rev Blocks' ),
						'icon' => '',
					),
				),
				$categories
			);
		}, 2 );
	}

	public function register_block() {

		$this->create_a3blocks_section();

		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		// Create Dynamic Block via PHP render callback
		$block_args = array(
			'attributes'      => array(
				'align'	=> array(
					'type' 		=> 'string'
				),
				'className'	=> array(
					'type' 		=> 'string'
				),
				'postID' => array(
					'type'    	=> 'string',
					'default' 	=> 0,
				),
				'isDisabled' => array(
					'type' 		=> 'boolean',
					'default'	=> true,
				),
				'isPreview'	=> array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
			'render_callback' 	=> array( $this, 'render' )
		);

		if ( $GLOBALS[A3_PVC_PREFIX.'less']->register_dynamic_style_file() ) {
			$block_args = array_merge( $block_args, array( 'editor_style' => 'a3pvc' ) );
		}

		register_block_type( 'page-views-count/stats', $block_args );

		// Create Block for show on Editor so that this plugin support AJAX load
		register_block_type( 'page-views-count/stats-editor', array(
			'attributes'      => array(
				'align'	=> array(
					'type' 		=> 'string'
				),
				'className'	=> array(
					'type' 		=> 'string'
				),
				'postID' => array(
					'type'    	=> 'string',
					'default' 	=> 0,
				),
				'isDisabled' => array(
					'type' 		=> 'boolean',
					'default'	=> true,
				),
				'isPreview'	=> array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
			'render_callback' 	=> array( $this, 'render_editor' )
		) );

		if ( function_exists( 'wp_set_script_translations' ) ) {

			wp_set_script_translations( 'page_views_count-cgb-block-js', 'page-views-count' );
		} elseif ( function_exists( 'wp_get_jed_locale_data' ) || function_exists( 'gutenberg_get_jed_locale_data' ) ) {

			$locale_data = function_exists( 'wp_get_jed_locale_data' ) ? wp_get_jed_locale_data( 'page-views-count' ) : gutenberg_get_jed_locale_data( 'page-views-count' );

			wp_add_inline_script(
				'wp-i18n',
				'wp.i18n.setLocaleData( ' . wp_json_encode( $locale_data ) . ', "page-views-count" );',
				'after'
			);
		}
	}

	public function render( $attributes ) {

		if ( is_admin() ) {
			return '';
		}

		if ( ! empty( $attributes['postID'] ) ) {
			return pvc_stats( $attributes['postID'], 0 );
		} elseif( isset( $attributes['isDisabled'] ) && false === $attributes['isDisabled'] ) {

			global $post;

			$post_id = $post->ID;

			return pvc_stats_update( $post_id, 0, $attributes );
		}

		return '';
	}

	public function render_editor( $attributes ) {

		if ( empty( $attributes['postID'] ) ) {
			return '';
		}

		$attributes['in_editor'] = true;

		return pvc_stats( $attributes['postID'], 0, $attributes );

	}
}
