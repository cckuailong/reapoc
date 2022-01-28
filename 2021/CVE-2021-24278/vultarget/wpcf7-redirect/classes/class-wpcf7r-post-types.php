<?php
/**
* Class WPCF7R_Post_Types
* Create a post type that will act as a container for the form actions
* This post type is invisible to all users and displayed only under Contact Form 7 tab
*/

defined( 'ABSPATH' ) || exit;

class WPCF7R_Post_Types {

	public function __construct() {
		add_action( 'init', array( $this, 'wpcf7r_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'wporg_add_custom_box' ) );
		add_action( 'save_post', array( $this, 'save_changes' ) );
		add_action( 'init', array( $this, 'wpcf7r_leads_post_type' ) );
	}

	/**
	 * Register leads post type
	 */
	public function wpcf7r_leads_post_type() {

		if ( class_exists( 'WPCF7R_Leads_Manager' ) && class_exists( 'WPCF7R_Action_Save_Lead' ) ) {
			$labels = array(
				'name'                  => _x( 'Leads', 'Post Type General Name', 'wpcf7-redirect' ),
				'singular_name'         => _x( 'Lead', 'Post Type Singular Name', 'wpcf7-redirect' ),
				'menu_name'             => __( 'Leads', 'wpcf7-redirect' ),
				'name_admin_bar'        => __( 'Post Type', 'wpcf7-redirect' ),
				'archives'              => __( 'Item Archives', 'wpcf7-redirect' ),
				'attributes'            => __( 'Item Attributes', 'wpcf7-redirect' ),
				'parent_item_colon'     => __( 'Parent Item:', 'wpcf7-redirect' ),
				'all_items'             => __( 'All Items', 'wpcf7-redirect' ),
				'add_new_item'          => __( 'Add New Item', 'wpcf7-redirect' ),
				'add_new'               => __( 'Add New', 'wpcf7-redirect' ),
				'new_item'              => __( 'New Item', 'wpcf7-redirect' ),
				'edit_item'             => __( 'Edit Item', 'wpcf7-redirect' ),
				'update_item'           => __( 'Update Item', 'wpcf7-redirect' ),
				'view_item'             => __( 'View Item', 'wpcf7-redirect' ),
				'view_items'            => __( 'View Items', 'wpcf7-redirect' ),
				'search_items'          => __( 'Search Item', 'wpcf7-redirect' ),
				'not_found'             => __( 'Not found', 'wpcf7-redirect' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'wpcf7-redirect' ),
				'featured_image'        => __( 'Featured Image', 'wpcf7-redirect' ),
				'set_featured_image'    => __( 'Set featured image', 'wpcf7-redirect' ),
				'remove_featured_image' => __( 'Remove featured image', 'wpcf7-redirect' ),
				'use_featured_image'    => __( 'Use as featured image', 'wpcf7-redirect' ),
				'insert_into_item'      => __( 'Insert into item', 'wpcf7-redirect' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'wpcf7-redirect' ),
				'items_list'            => __( 'Items list', 'wpcf7-redirect' ),
				'items_list_navigation' => __( 'Items list navigation', 'wpcf7-redirect' ),
				'filter_items_list'     => __( 'Filter items list', 'wpcf7-redirect' ),
			);
			$args   = array(
				'label'               => __( 'Leads', 'wpcf7-redirect' ),
				'description'         => __( 'Leads', 'wpcf7-redirect' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'admin.php?page=wpcf7',
				'menu_position'       => 5,
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'show_in_rest'        => false,
			);
			register_post_type( 'wpcf7r_leads', $args );

			if ( defined( 'CF7_REDIRECT_DEBUG' ) && CF7_REDIRECT_DEBUG ) {
				add_post_type_support( 'wpcf7r_leads', 'custom-fields' );
			}

			add_action( 'admin_menu', 'my_admin_menu' );
		}

		function my_admin_menu() {
			add_submenu_page( 'wpcf7', 'Leads', 'Leads', 'manage_options', 'edit.php?post_type=wpcf7r_leads' );
			//add_submenu_page( 'wpcf7', 'New lead', 'New lead', 'manage_options', 'post-new.php?post_type=wpcf7r_leads' );
		}
	}

	// Register Custom Post Type
	function wpcf7r_post_type() {
		$labels = array(
			'name'                  => _x( 'Actions', 'Post Type General Name', 'wpcf7-redirect' ),
			'singular_name'         => _x( 'Action', 'Post Type Singular Name', 'wpcf7-redirect' ),
			'menu_name'             => __( 'Actions', 'wpcf7-redirect' ),
			'name_admin_bar'        => __( 'Post Type', 'wpcf7-redirect' ),
			'archives'              => __( 'Item Archives', 'wpcf7-redirect' ),
			'attributes'            => __( 'Item Attributes', 'wpcf7-redirect' ),
			'parent_item_colon'     => __( 'Parent Item:', 'wpcf7-redirect' ),
			'all_items'             => __( 'All Items', 'wpcf7-redirect' ),
			'add_new_item'          => __( 'Add New Item', 'wpcf7-redirect' ),
			'add_new'               => __( 'Add New', 'wpcf7-redirect' ),
			'new_item'              => __( 'New Item', 'wpcf7-redirect' ),
			'edit_item'             => __( 'Edit Item', 'wpcf7-redirect' ),
			'update_item'           => __( 'Update Item', 'wpcf7-redirect' ),
			'view_item'             => __( 'View Item', 'wpcf7-redirect' ),
			'view_items'            => __( 'View Items', 'wpcf7-redirect' ),
			'search_items'          => __( 'Search Item', 'wpcf7-redirect' ),
			'not_found'             => __( 'Not found', 'wpcf7-redirect' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wpcf7-redirect' ),
			'featured_image'        => __( 'Featured Image', 'wpcf7-redirect' ),
			'set_featured_image'    => __( 'Set featured image', 'wpcf7-redirect' ),
			'remove_featured_image' => __( 'Remove featured image', 'wpcf7-redirect' ),
			'use_featured_image'    => __( 'Use as featured image', 'wpcf7-redirect' ),
			'insert_into_item'      => __( 'Insert into item', 'wpcf7-redirect' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wpcf7-redirect' ),
			'items_list'            => __( 'Items list', 'wpcf7-redirect' ),
			'items_list_navigation' => __( 'Items list navigation', 'wpcf7-redirect' ),
			'filter_items_list'     => __( 'Filter items list', 'wpcf7-redirect' ),
		);

		$args = array(
			'label'               => __( 'Redirection For Contact Form 7 Actions', 'wpcf7-redirect' ),
			'description'         => __( 'Actions', 'wpcf7-redirect' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'custom_fields', 'custom-fields' ),
			'hierarchical'        => true,
			'public'              => CF7_REDIRECT_DEBUG,
			'show_ui'             => CF7_REDIRECT_DEBUG,
			'show_in_menu'        => 'admin.php?page=wpcf7',
			'menu_position'       => 5,
			'show_in_admin_bar'   => CF7_REDIRECT_DEBUG,
			'show_in_nav_menus'   => CF7_REDIRECT_DEBUG,
			'can_export'          => CF7_REDIRECT_DEBUG,
			'has_archive'         => CF7_REDIRECT_DEBUG,
			'exclude_from_search' => CF7_REDIRECT_DEBUG,
			'publicly_queryable'  => CF7_REDIRECT_DEBUG,
			'rewrite'             => CF7_REDIRECT_DEBUG,
			'capability_type'     => 'page',
			'show_in_rest'        => CF7_REDIRECT_DEBUG,
		);

		register_post_type( 'wpcf7r_action', $args );
		add_post_type_support( 'wpcf7r_action', 'custom-fields' );

		if ( defined( 'CF7_REDIRECT_DEBUG' ) && CF7_REDIRECT_DEBUG ) {
			add_action( 'admin_menu', 'add_actions_menu' );

			function add_actions_menu() {
				add_submenu_page( 'wpcf7', __( 'Actions List', 'wpcf7-redirect' ), __( 'Actions List', 'wpcf7-redirect' ), 'manage_options', 'edit.php?post_type=wpcf7r_action' );
			}
		}

	}

	function wporg_add_custom_box() {
		$screens = array( 'wpcf7r_action' );
		if ( is_wpcf7r_debug() ) {
			$screens[] = 'wpcf7r_leads';
		}

		foreach ( $screens as $screen ) {
			add_meta_box(
				'wpcf7r_action_meta',
				__( 'Action Meta', 'wpcf7-redirect' ),
				array( $this, 'debug_helper' ),
				$screen
			);
		}

		add_meta_box(
			'wpcf7r_leads',
			__( 'Lead Details', 'wpcf7-redirect' ),
			array( $this, 'lead_fields_html' ),
			'wpcf7r_leads'
		);
	}

	/**
	 * Get the meta html
	 *
	 * @param $post
	 */
	function lead_fields_html( $post ) {
		$lead = new WPCF7R_Lead( $post->ID );

		$fields = $lead->get_lead_fields();

		foreach ( $fields as $field ) {
			switch ( $field['name'] ) {
				case 'action-save_lead':
					$field['value'] = $field['value']['data']['lead_id'];
					break;
				case 'action-mailchimp':
					if ( is_wp_error( $field['value'] ) ) {
						$field['value'] = $field['value']->get_error_message();
					}
					break;
			}

			WPCF7R_Html::render_field( $field, $field['prefix'] );
		}
	}

	public function save_changes( $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( 'wpcf7r_leads' === $post_type ) {
			if ( isset( $_POST['wpcf7-redirect'] ) ) {
				foreach ( $_POST['wpcf7-redirect'] as $meta_key => $meta_value ) {
					update_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
		}
	}

	function debug_helper() {
		echo '<pre>';
		print_r( get_post_custom() );
		echo '</pre>';
	}
}
