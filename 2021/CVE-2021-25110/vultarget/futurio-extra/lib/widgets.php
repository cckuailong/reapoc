<?php
/**
 * Custom widgets.
 *
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'futurio_extra_load_widgets' ) ) :

	/**
	 * Load widgets.
	 *
	 * @since 1.0.0
	 */
	function futurio_extra_load_widgets() {

		// Extended Recent Post.
		register_widget( 'Futurio_Extra_Extended_Recent_Posts' );

		// Popular Post.
		register_widget( 'Futurio_Extra_Popular_Posts' );
    
    // Social.
		register_widget( 'Futurio_Extra_Social_Widget' );
    
    // About.
		register_widget( 'Futurio_Extra_About_Me_Widget' );
	}

endif;

add_action( 'widgets_init', 'futurio_extra_load_widgets' );

/**
 * Recent Posts Widget
 */
require_once( plugin_dir_path( __FILE__ ) . 'widgets/recent-posts.php' );

/**
 * Popular Posts Widget
 */
require_once( plugin_dir_path( __FILE__ ) . 'widgets/popular-posts.php' );

/**
 * Social Widget
 */
require_once( plugin_dir_path( __FILE__ ) . 'widgets/social.php' );

/**
 * About Me Widget
 */
require_once( plugin_dir_path( __FILE__ ) . 'widgets/about-me.php' );