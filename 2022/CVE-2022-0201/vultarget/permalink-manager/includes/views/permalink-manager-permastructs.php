<?php

/**
* Display the page where the slugs could be regenerated or replaced
*/
class Permalink_Manager_Permastructs extends Permalink_Manager_Class {

	public function __construct() {
		add_filter( 'permalink_manager_sections', array($this, 'add_admin_section'), 2 );
	}

	public function add_admin_section($admin_sections) {

		$admin_sections['permastructs'] = array(
			'name'				=>	__('Permastructures', 'permalink-manager'),
			'function'    => array('class' => 'Permalink_Manager_Permastructs', 'method' => 'output')
		);

		return $admin_sections;
	}

	public function get_fields() {
		global $permalink_manager_permastructs;

		$all_post_types = Permalink_Manager_Helper_Functions::get_post_types_array('full');
		$woocommerce_icon = "<i class=\"woocommerce-icon woocommerce-cart\"></i>";

		// 1. Get fields
		$fields = array(
			'post_types' => array(
				'section_name' => __('Post types', 'permalink-manager'),
				'container' => 'row',
				'fields' => array()
			),
			'taxonomies' => array(
				'section_name' => __('Taxonomies', 'permalink-manager'),
				'container' => 'row',
				'append_content' => Permalink_Manager_Admin_Functions::pro_text(),
				'fields' => array()
			)
		);

		// 2. Woocommerce support
		if(class_exists('WooCommerce')) {
			$fields['woocommerce'] = array(
				'section_name' => "{$woocommerce_icon} " . __('WooCommerce', 'permalink-manager'),
				'container' => 'row',
				'append_content' => Permalink_Manager_Admin_Functions::pro_text(),
				'fields' => array()
			);
		}

		// 3. Append fields for all post types
		foreach($all_post_types as $post_type) {
			if($post_type['name'] == 'shop_coupon') { continue; }

			$fields["post_types"]["fields"][$post_type['name']] = array(
				'label' => $post_type['label'],
				'container' => 'row',
				'input_class' => 'permastruct-field',
				'post_type' => $post_type,
				'type' => 'permastruct'
			);
		}

		return apply_filters('permalink_manager_permastructs_fields', $fields);
	}

	/**
	* Get the array with settings and render the HTML output
	*/
	public function output() {
		global $permalink_manager_permastructs;

		$sidebar = sprintf('<h3>%s</h3>', __('Instructions', 'permalink-manager'));
		$sidebar .= "<div class=\"notice notice-warning\"><p>";
		$sidebar .= __('The current permastructures settings will be automatically applied <strong>only to the new posts & terms</strong>.');
		$sidebar .= '<br />';
		$sidebar .= sprintf(__('To apply the <strong>new format to existing posts and terms</strong>, please use "<a href="%s">Regenerate/reset</a>" tool after you update the permastructure settings below.', 'permalink-manager'), admin_url('tools.php?page=permalink-manager&section=tools&subsection=regenerate_slugs'));
		$sidebar .= "</p></div>";

		$sidebar .= sprintf('<h4>%s</h4>', __('Permastructure tags', 'permalink-manager'));
		$sidebar .= wpautop(sprintf(__('All allowed <a href="%s" target="_blank">permastructure tags</a> are listed below. Please note that some of them can be used only for particular post types or taxonomies.', 'permalink-manager'), "https://codex.wordpress.org/Using_Permalinks#Structure_Tags"));
		$sidebar .= Permalink_Manager_Helper_Functions::get_all_structure_tags();

		return Permalink_Manager_Admin_Functions::get_the_form(self::get_fields(), '', array('text' => __( 'Save permastructures', 'permalink-manager' ), 'class' => 'primary margin-top'), $sidebar, array('action' => 'permalink-manager', 'name' => 'permalink_manager_permastructs'));
	}

}
