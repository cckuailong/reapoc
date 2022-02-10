<?php

/**
* Additional hooks for "Permalink Manager Pro"
*/
class Permalink_Manager_Gutenberg extends Permalink_Manager_Class {

	public function __construct() {
		add_action('enqueue_block_editor_assets', array($this, 'init'));

		add_action('wp_ajax_pm_get_uri_editor', array($this, 'get_uri_editor'));
		add_action('wp_ajax_nopriv_pm_get_uri_editor', array($this, 'get_uri_editor'));
	}

	public function init() {
		global $current_screen, $post;

		// Get displayed post type
		if(empty($current_screen->post_type)) { return; }
		$post_type = $current_screen->post_type;

		// Stop the hook (if needed)
		$show_uri_editor = apply_filters("permalink_manager_show_uri_editor_post_{$post->post_type}", true, $post);
		if(!$show_uri_editor) {
			return;
		}

		// Check if the post is excluded
		if(!empty($post->ID) && Permalink_Manager_Helper_Functions::is_post_excluded($post)) {
			return;
		}

		add_meta_box('permalink-manager', __('Permalink Manager', 'permalink-manager'), array($this, 'get_uri_editor'), '', 'side', 'high' );
		// wp_enqueue_script('permalink-manager-gutenberg', PERMALINK_MANAGER_URL . '/out/permalink-manager-gutenberg.js', array('wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element'));
	}

	public function get_uri_editor($post = null) {
		if(empty($post->ID) && empty($_REQUEST['post_id'])) {
			return '';
		} else if(!empty($_REQUEST['post_id']) && is_numeric($_REQUEST['post_id'])) {
			$post = get_post($_REQUEST['post_id']);
		}

		// Display URI Editor
		echo ($post) ? Permalink_Manager_Admin_Functions::display_uri_box($post, true) : '';

		if(wp_doing_ajax()) {
			die();
		}
	}

}

?>
