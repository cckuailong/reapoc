<?php

/**
* Display the page where the slugs could be regenerated or replaced
*/
class Permalink_Manager_Tools extends Permalink_Manager_Class {

	public function __construct() {
		add_filter( 'permalink_manager_sections', array($this, 'add_admin_section'), 1 );
	}

	public function add_admin_section($admin_sections) {

		$admin_sections['tools'] = array(
			'name'				=>	__('Tools', 'permalink-manager'),
			'subsections' => array(
				'duplicates' => array(
					'name'				=>	__('Permalink Duplicates', 'permalink-manager'),
					'function'		=>	array('class' => 'Permalink_Manager_Tools', 'method' => 'duplicates_output')
				),
				'find_and_replace' => array(
					'name'				=>	__('Find & Replace', 'permalink-manager'),
					'function'		=>	array('class' => 'Permalink_Manager_Tools', 'method' => 'find_and_replace_output')
				),
				'regenerate_slugs' => array(
					'name'				=>	__('Regenerate/Reset', 'permalink-manager'),
					'function'		=>	array('class' => 'Permalink_Manager_Tools', 'method' => 'regenerate_slugs_output')
				),
				'stop_words' => array(
					'name'				=>	__('Stop Words', 'permalink-manager'),
					'function'		=>	array('class' => 'Permalink_Manager_Admin_Functions', 'method' => 'pro_text')
				),
				'import' => array(
					'name'				=>	__('Custom Permalinks', 'permalink-manager'),
					'function'		=>	array('class' => 'Permalink_Manager_Admin_Functions', 'method' => 'pro_text')
				)
			)
		);

		return $admin_sections;
	}

	public function display_instructions() {
		return wpautop(__('<strong>A MySQL backup is highly recommended before using "<em>Native slugs</em>" mode!</strong>', 'permalink-manager'));
	}

	public function duplicates_output() {
		global $permalink_manager_uris, $permalink_manager_redirects;

		// Get the duplicates & another variables
		$all_duplicates = Permalink_Manager_Helper_Functions::get_all_duplicates();
		$home_url = trim(get_option('home'), "/");

		$html = sprintf("<h3>%s</h3>", __("List of duplicated permalinks", "permalink-manager"));
		$html .= wpautop(sprintf("<a class=\"button button-primary\" href=\"%s\">%s</a>", admin_url('tools.php?page=permalink-manager&section=tools&subsection=duplicates&clear-permalink-manager-uris=1'), __('Fix custom permalinks & redirects', 'permalink-manager')));

		if(!empty($all_duplicates)) {
			foreach($all_duplicates as $uri => $duplicates) {
				$html .= "<div class=\"permalink-manager postbox permalink-manager-duplicate-box\">";
				$html .= "<h4 class=\"heading\"><a href=\"{$home_url}/{$uri}\" target=\"_blank\">{$home_url}/{$uri} <span class=\"dashicons dashicons-external\"></span></a></h4>";
				$html .= "<table>";

				foreach($duplicates as $item_id) {
					$html .= "<tr>";

					// Detect duplicate type
					preg_match("/(redirect-([\d]+)_)?(?:(tax-)?([\d]*))/", $item_id, $parts);

					$is_extra_redirect = (!empty($parts[1])) ? true : false;
					$duplicate_type = ($is_extra_redirect) ? __('Extra Redirect', 'permalink-manager') : __('Custom URI', 'permalink-manager');
					$detected_id = $parts[4];
					$detected_index = $parts[2];
					$detected_term = (!empty($parts[3])) ? true : false;
					$remove_link = ($is_extra_redirect) ? sprintf(" <a href=\"%s\"><span class=\"dashicons dashicons-trash\"></span> %s</a>", admin_url("tools.php?page=permalink-manager&section=tools&subsection=duplicates&remove-redirect={$item_id}"), __("Remove Redirect")) : "";

					// Get term
					if($detected_term && !empty($detected_id)) {
						$term = get_term($detected_id);
						if(!empty($term->name)) {
							$title = $term->name;
							$edit_label = "<span class=\"dashicons dashicons-edit\"></span>" . __("Edit term", "permalink-manager");
							$edit_link = get_edit_tag_link($term->term_id, $term->taxonomy);
						} else {
							$title = __("(Removed term)", "permalink-manager");
							$edit_label = "<span class=\"dashicons dashicons-trash\"></span>" . __("Remove broken URI", "permalink-manager");
							$edit_link = admin_url("tools.php?page=permalink-manager&section=tools&subsection=duplicates&remove-uri=tax-{$detected_id}");
						}
					}
					// Get post
					else if(!empty($detected_id)) {
						$post = get_post($detected_id);
						if(!empty($post->post_title) && post_type_exists($post->post_type)) {
							$title = $post->post_title;
							$edit_label = "<span class=\"dashicons dashicons-edit\"></span>" . __("Edit post", "permalink-manager");
							$edit_link = get_edit_post_link($post->ID);
						} else {
							$title = __("(Removed post)", "permalink-manager");
							$edit_label = "<span class=\"dashicons dashicons-trash\"></span>" . __("Remove broken URI", "permalink-manager");
							$edit_link = admin_url("tools.php?page=permalink-manager&section=tools&subsection=duplicates&remove-uri={$detected_id}");
						}
					} else {
						continue;
					}

					$html .= sprintf(
						'<td><a href="%1$s">%2$s</a>%3$s</td><td>%4$s</td><td class="actions"><a href="%1$s">%5$s</a>%6$s</td>',
						$edit_link,
						$title,
						" <small>#{$detected_id}</small>",
						$duplicate_type,
						$edit_label,
						$remove_link
					);
					$html .= "</tr>";
				}
				$html .= "</table>";
				$html .= "</div>";
			}
		} else {
			$html .= sprintf("<p class=\"alert notice-success notice\">%s</p>", __('Congratulations! No duplicated URIs or Redirects found!', 'permalink-manager'));
		}

		return $html;
	}

	public function find_and_replace_output() {
		// Get all registered post types array & statuses
		$all_post_statuses_array = Permalink_Manager_Helper_Functions::get_post_statuses();
		$all_post_types = Permalink_Manager_Helper_Functions::get_post_types_array();
		$all_taxonomies = Permalink_Manager_Helper_Functions::get_taxonomies_array();

		$fields = apply_filters('permalink_manager_tools_fields', array(
			'old_string' => array(
				'label' => __( 'Find ...', 'permalink-manager' ),
				'type' => 'text',
				'container' => 'row',
				'input_class' => 'widefat'
			),
			'new_string' => array(
				'label' => __( 'Replace with ...', 'permalink-manager' ),
				'type' => 'text',
				'container' => 'row',
				'input_class' => 'widefat'
			),
			'mode' => array(
				'label' => __( 'Mode', 'permalink-manager' ),
				'type' => 'select',
				'container' => 'row',
				'choices' => array(
					'custom_uris' => __('Custom URIs', 'permalink-manager'),
					'slugs' => __('Native slugs', 'permalink-manager')
				),
			),
			'content_type' => array(
				'label' => __( 'Select content type', 'permalink-manager' ),
				'type' => 'select',
				'disabled' => true,
				'pro' => true,
				'container' => 'row',
				'default' => 'post_types',
				'choices' => array(
					'post_types' => __('Post types', 'permalink-manager'),
					'taxonomies' => __('Taxonomies', 'permalink-manager')
				),
			),
			'post_types' => array(
				'label' => __( 'Select post types', 'permalink-manager' ),
				'type' => 'checkbox',
				'container' => 'row',
				'default' => array('post', 'page'),
				'choices' => $all_post_types,
				'select_all' => '',
				'unselect_all' => '',
			),
			'taxonomies' => array(
				'label' => __( 'Select taxonomies', 'permalink-manager' ),
				'type' => 'checkbox',
				'container' => 'row',
				'container_class' => 'hidden',
				'default' => array('category', 'post_tag'),
				'choices' => $all_taxonomies,
				'pro' => true,
				'select_all' => '',
				'unselect_all' => '',
			),
			'post_statuses' => array(
				'label' => __( 'Select post statuses', 'permalink-manager' ),
				'type' => 'checkbox',
				'container' => 'row',
				'default' => array('publish'),
				'choices' => $all_post_statuses_array,
				'select_all' => '',
				'unselect_all' => '',
			),
			'ids' => array(
				'label' => __( 'Select IDs', 'permalink-manager' ),
				'type' => 'text',
				'container' => 'row',
				//'disabled' => true,
				'description' => __('To narrow the above filters you can type the post IDs (or ranges) here. Eg. <strong>1-8, 10, 25</strong>.', 'permalink-manager'),
				//'pro' => true,
				'input_class' => 'widefat'
			)
		), 'find_and_replace');

		$sidebar = '<h3>' . __('Important notices', 'permalink-manager') . '</h3>';
		$sidebar .= self::display_instructions();

		$output = Permalink_Manager_Admin_Functions::get_the_form($fields, 'columns-3', array('text' => __('Find and replace', 'permalink-manager'), 'class' => 'primary margin-top'), $sidebar, array('action' => 'permalink-manager', 'name' => 'find_and_replace'), true, 'form-ajax');

		return $output;
	}

	public function regenerate_slugs_output() {
		// Get all registered post types array & statuses
		$all_post_statuses_array = Permalink_Manager_Helper_Functions::get_post_statuses();
		$all_post_types = Permalink_Manager_Helper_Functions::get_post_types_array();
		$all_taxonomies = Permalink_Manager_Helper_Functions::get_taxonomies_array();

		$fields = apply_filters('permalink_manager_tools_fields', array(
			'mode' => array(
				'label' => __( 'Mode', 'permalink-manager' ),
				'type' => 'select',
				'container' => 'row',
				'choices' => array(
					'custom_uris' => __('Regenerate custom permalinks', 'permalink-manager'),
					'slugs' => __('Regenerate native slugs', 'permalink-manager'),
					'native' => __('Use original URLs as custom permalinks', 'permalink-manager')
				),
			),
			'content_type' => array(
				'label' => __( 'Select content type', 'permalink-manager' ),
				'type' => 'select',
				'disabled' => true,
				'pro' => true,
				'container' => 'row',
				'default' => 'post_types',
				'choices' => array(
					'post_types' => __('Post types', 'permalink-manager'),
					'taxonomies' => __('Taxonomies', 'permalink-manager')
				),
			),
			'post_types' => array(
				'label' => __( 'Select post types', 'permalink-manager' ),
				'type' => 'checkbox',
				'container' => 'row',
				'default' => array('post', 'page'),
				'choices' => $all_post_types,
				'select_all' => '',
				'unselect_all' => '',
			),
			'taxonomies' => array(
				'label' => __( 'Select taxonomies', 'permalink-manager' ),
				'type' => 'checkbox',
				'container' => 'row',
				'container_class' => 'hidden',
				'default' => array('category', 'post_tag'),
				'choices' => $all_taxonomies,
				'pro' => true,
				'select_all' => '',
				'unselect_all' => '',
			),
			'post_statuses' => array(
				'label' => __( 'Select post statuses', 'permalink-manager' ),
				'type' => 'checkbox',
				'container' => 'row',
				'default' => array('publish'),
				'choices' => $all_post_statuses_array,
				'select_all' => '',
				'unselect_all' => '',
			),
			'ids' => array(
				'label' => __( 'Select IDs', 'permalink-manager' ),
				'type' => 'text',
				'container' => 'row',
				//'disabled' => true,
				'description' => __('To narrow the above filters you can type the post IDs (or ranges) here. Eg. <strong>1-8, 10, 25</strong>.', 'permalink-manager'),
				//'pro' => true,
				'input_class' => 'widefat'
			)
		), 'regenerate');

		$sidebar = '<h3>' . __('Important notices', 'permalink-manager') . '</h3>';
		$sidebar .= self::display_instructions();

		$output = Permalink_Manager_Admin_Functions::get_the_form($fields, 'columns-3', array('text' => __( 'Regenerate', 'permalink-manager' ), 'class' => 'primary margin-top'), $sidebar, array('action' => 'permalink-manager', 'name' => 'regenerate'), true, 'form-ajax');

		return $output;
	}
}
