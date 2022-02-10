<?php
/**
 * Methods for handling blocks in the Gutenberg editor
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2018, Theme of the Crop
 * @license   GPL-2.0+
 * @since     1.2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpBlocks', false ) ) :

	/**
	 * Class to create, edit and display blocks for the Gutenberg editor
	 *
	 * @since 1.2
	 */
	class bpfwpBlocks {

		/**
		 * Register hooks
		 *
		 * @since  1.2
		 * @access public
		 * @return void
		 */
		public function run() {

			add_action( 'init', array( $this, 'register' ) );

			add_filter( 'block_categories_all', array( $this, 'add_block_category' ) );
		}

		/**
		 * Register blocks
		 *
		 * @since  1.1
		 * @access public
		 * @return void
		 */
		public function register() {

			if ( !function_exists( 'register_block_type' ) ) {
				return;
			}

			wp_register_script(
				'business-profile-blocks',
				BPFWP_PLUGIN_URL . '/assets/js/blocks.build.js',
				array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
				BPFWP_VERSION
			);

			wp_register_script(
				'bpfwp-map',
				BPFWP_PLUGIN_URL . '/assets/js/map.js',
				array( 'jquery' ),
				BPFWP_VERSION,
				true
			);

			wp_register_style(
				'bpfwp-default',
				BPFWP_PLUGIN_URL . '/assets/css/contact-card.css',
				array(),
				BPFWP_VERSION
			);

			register_block_type( 'business-profile/contact-card', array(
				'editor_script' => array('business-profile-blocks', 'bpfwp-map'),
				'editor_style' => 'bpfwp-default',
				'render_callback' => 'bpwfwp_print_contact_card',
				'attributes' => array(
					'location' => array(
						'type' => 'number',
						'minimum' => '0',
					),
					'show_name' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'show_address' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'show_get_directions' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'show_phone' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'show_contact' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'show_opening_hours' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'show_opening_hours_brief' => array(
						'type' => 'boolean',
						'default' => false,
					),
					'show_map' => array(
						'type' => 'boolean',
						'default' => true,
					),
					'show_image' => array(
						'type' => 'boolean',
						'default' => true,
					),
				),
			) );

			add_action( 'admin_init', array( $this, 'register_admin' ) );
		}

		/**
		 * Register admin-only assets for block handling
		 *
		 * @since  1.2
		 * @access public
		 * @return void
		 */
		public function register_admin() {

			global $bpfwp_controller;

			$location_options = array();

			if ( $bpfwp_controller->settings->get_setting( 'multiple-locations' ) ) {
				$locations = new WP_Query( array(
					'post_type' => $bpfwp_controller->cpts->location_cpt_slug,
					'posts_per_page' => 1000,
					'post_status' => 'publish',
				) );

				$location_options = array( array(
					'value' => 0,
					'label' => __('Use the main Business Profile'),
				) );
				while ( $locations->have_posts() ) {
					$locations->the_post();
					$location_options[] = array(
						'value' => get_the_ID(),
						'label' => get_the_title(),
					);
				}
				wp_reset_postdata();
			}

			wp_add_inline_script(
				'business-profile-blocks',
				sprintf(
					'var bpfwp_blocks = %s;',
					json_encode( array(
						'locationOptions' => $location_options,
					) )
				),
				'before'
			);
		}

		/**
		 * Create a new category of blocks to hold our block
		 * @since 2.0.0
		 */
		public function add_block_category( $categories ) {
			
			$categories[] = array(
				'slug'  => 'bpfwp-blocks',
				'title' => __( 'Five Star Business Profile and Schema', 'business-profile' ),
			);

			return $categories;
		}
	}
endif;
