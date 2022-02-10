<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Main_Menu class
 * Manages myCred main menu WordPress admin area.
 * @since 0.1
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Main_Menu' ) ):
	class myCRED_Main_Menu {

		/**
		 * Construct
		 * @since 0.1
		 * @version 1.0
		 */
		function __construct( $modules ) {

			global $mycred;

			add_menu_page(
				'myCred',
				'myCred',
				$mycred->get_point_editor_capability(),
				MYCRED_MAIN_SLUG,
				'',
				'dashicons-mycred'
			);

			mycred_add_main_submenu(
				'General Settings',
				'General Settings',
				$mycred->get_point_editor_capability(),
				MYCRED_MAIN_SLUG,
				array( $modules['type'][ MYCRED_DEFAULT_TYPE_KEY ]['settings'], 'admin_page' )
			);

			global $pagenow;

			if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'mycred-main' ) {
				
				$modules['type'][ MYCRED_DEFAULT_TYPE_KEY ]['settings']->scripts_and_styles();
				$modules['type'][ MYCRED_DEFAULT_TYPE_KEY ]['settings']->settings_header();

				wp_enqueue_style( 'mycred-admin' );
				wp_enqueue_script( 'mycred-accordion' );
			
			}

			add_action( 'admin_menu', array( $this, 'add_submenu' ) );

		}

		public function add_submenu() {

			mycred_add_main_submenu(
				__( 'About', 'mycred' ),
				__( 'About', 'mycred' ),
				'moderate_comments',
				MYCRED_SLUG . '-about',
				'mycred_about_page'
			);
			
			mycred_add_main_submenu(
				__( 'Suggestions', 'mycred' ),
				__( 'Suggestions', 'mycred' ),
				'manage_options',
				'support-screen',
				array( $this,'support_screen_function'),
				99
			);

		}

		public function support_screen_function () {

			$html = '
			<div class="mycred_support_heading" style="margin: 5% 0 0.2% 86%; text-align:left; ">
				<a href="https://mycred.me/support/" class="support_link" style="text-decoration:none; font-size:14px; line-height: 1.55; font-weight:400; text-align:center; margin:10px 0;  transition-duration: .05s; transition-timing-function: ease-in-out; transition-property: border,background,color; color: #135e96; ">Contact Support</a>
			</div>
		
			<div class="mycred_iframe">
				<iframe src="https://app.productstash.io/roadmaps/5f8d483c053518002b4441c4/public" height="900" width="90%" frameborder="0" scrolling="no" style="margin-left: 5%; text-decoration:none;"></iframe>		
			</div>';
		
			echo $html;
		
		}

	}
endif;