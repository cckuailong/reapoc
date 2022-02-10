<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Filters which are loaded for certain admin URLs
 * 
 */
class CME_AdminMenuNoPrivWorkaround {
	var $create_posts_cap = '';

	function __construct() {
        global $pagenow;

        if ( 'edit.php' == $pagenow ) {
            // Prevent lack of create_posts capability from completely blocking admin menu access to a post type.
            // The "Add New" page is already successfully blocked by other means.
			add_action( '_admin_menu', array( $this, 'menu_nopriv_workaround_enable' ), PHP_INT_MAX );
            add_action( 'admin_menu', array( $this, 'menu_nopriv_workaround_disable' ), - PHP_INT_MAX );
        }
	}

	function menu_nopriv_workaround_enable() {
        global $typenow;
        
        if ( $post_type_obj = get_post_type_object( $typenow ) ) {
            $this->create_posts_cap = $post_type_obj->cap->create_posts;
            add_filter( 'user_has_cap', array( $this, 'admin_menu_caps' ), PHP_INT_MAX, 3 );
        }
	}

	function menu_nopriv_workaround_disable() {
		if ( $this->create_posts_cap ) {
            remove_filter( 'user_has_cap', array( $this, 'admin_menu_caps' ), PHP_INT_MAX, 3 );
		}
	}

	function admin_menu_caps( $wp_sitecaps, $reqd_caps, $args ) {
		if ( is_array($args) && isset($args[0]) && ( $this->create_posts_cap == $args[0] ) ) {
			$wp_sitecaps[ $args[0] ] = true;
		}

		return $wp_sitecaps;
	}
} // end class
