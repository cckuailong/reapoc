<?php

/**
* Extend WP_List_Table with two subclasses for post types and taxonomies
*/
class Permalink_Manager_URI_Editor_Post extends WP_List_Table {

	public $displayed_post_types, $displayed_post_statuses;

	public function __construct() {
		global $status, $page, $permalink_manager_options, $active_subsection;

		parent::__construct(array(
			'singular'	=> 'slug',
			'plural'	=> 'slugs'
		));

		$this->displayed_post_statuses = (isset($permalink_manager_options['screen-options']['post_statuses'])) ? "'" . implode("', '", $permalink_manager_options['screen-options']['post_statuses']) . "'" : "'no-post-status'";
		$this->displayed_post_types = ($active_subsection && $active_subsection == 'all') ? "'" . implode("', '", $permalink_manager_options['screen-options']['post_types']) . "'" : "'{$active_subsection}'";
	}

	/**
	* Get the HTML output with the WP_List_Table
	*/
	public function display_admin_section() {
		global $wpdb;

		$output = "<form id=\"permalinks-post-types-table\" class=\"slugs-table\" method=\"post\">";
		$output .= wp_nonce_field('permalink-manager', 'uri_editor', true, true);
		$output .= Permalink_Manager_Admin_Functions::generate_option_field('pm_session_id', array('value' => uniqid(), 'type' => 'hidden'));

		// Bypass
		ob_start();

		$this->prepare_items();
		$this->display();
		$output .= ob_get_contents();

		ob_end_clean();

		$output .= "</form>";

		return $output;
	}

	function get_table_classes() {
		return array( 'widefat', 'striped', $this->_args['plural'] );
	}

	/**
	* Override the parent columns method. Defines the columns to use in your listing table
	*/
	public function get_columns() {
		return apply_filters('permalink_manager_uri_editor_columns', array(
			'item_title'		=> __('Post title', 'permalink-manager'),
			'item_uri'	=> __('Full URI & Permalink', 'permalink-manager')
		));
	}

	/**
	* Hidden columns
	*/
	public function get_hidden_columns() {
		return array();
	}

	/**
	* Sortable columns
	*/
	public function get_sortable_columns() {
		return array(
			'item_title' => array('post_title', false)
		);
	}

	/**
	* Data inside the columns
	*/
	public function column_default($item, $column_name) {
		global $permalink_manager_options;

		$uri = Permalink_Manager_URI_Functions_Post::get_post_uri($item['ID'], true);
		$uri = (!empty($permalink_manager_options['general']['decode_uris'])) ? urldecode($uri) : $uri;

		$field_args_base = array('type' => 'text', 'value' => $uri, 'without_label' => true, 'input_class' => 'custom_uri', 'extra_atts' => "data-element-id=\"{$item['ID']}\"");
		$permalink = get_permalink($item['ID']);

		$post_statuses_array = get_post_statuses();
		$post_statuses_array['inherit'] = __('Inherit (Attachment)', 'permalink-manager');

		$output = apply_filters('permalink_manager_uri_editor_column_content', '', $column_name, get_post($item['ID']));
		if(!empty($output)) { return $output; }

		switch( $column_name ) {
			case 'item_uri':
				// Get auto-update settings
				$auto_update_val = get_post_meta($item['ID'], "auto_update_uri", true);
				$auto_update_uri = (!empty($auto_update_val)) ? $auto_update_val : $permalink_manager_options["general"]["auto_update_uris"];
				if($auto_update_uri) {
					$field_args_base['readonly'] = true;
					$field_args_base['append_content'] = sprintf('<p class="small uri_locked">%s %s</p>', '<span class="dashicons dashicons-lock"></span>', __('The above permalink will be automatically updated and is locked for editing.', 'permalink-manager'));
				}

				$output = '<div class="custom_uri_container">';
				$output .= Permalink_Manager_Admin_Functions::generate_option_field("uri[{$item['ID']}]", $field_args_base);
				$output .= "<span class=\"duplicated_uri_alert\"></span>";
				$output .= sprintf("<a class=\"small post_permalink\" href=\"%s\" target=\"_blank\"><span class=\"dashicons dashicons-admin-links\"></span> %s</a>", $permalink, urldecode($permalink));
				$output .= '</div>';
				return $output;

			case 'item_title':
				$output = $item[ 'post_title' ];
				$output .= '<div class="extra-info small">';
				$output .= sprintf("<span><strong>%s:</strong> %s</span>", __("Slug", "permalink-manager"), urldecode($item['post_name']));
				$output .= sprintf(" | <span><strong>%s:</strong> {$post_statuses_array[$item["post_status"]]}</span>", __("Post status", "permalink-manager"));
				$output .= apply_filters('permalink_manager_uri_editor_extra_info', '', $column_name, get_post($item['ID']));
				$output .= '</div>';

				$output .= '<div class="row-actions">';
				$output .= sprintf("<span class=\"edit\"><a href=\"%s/wp-admin/post.php?post={$item['ID']}&amp;action=edit\" title=\"%s\">%s</a> | </span>", get_option('home'), __('Edit', 'permalink-manager'), __('Edit', 'permalink-manager'));
				$output .= '<span class="view"><a target="_blank" href="' . $permalink . '" title="' . __('View', 'permalink-manager') . ' ' . $item[ 'post_title' ] . '" rel="permalink">' . __('View', 'permalink-manager') . '</a> | </span>';
				$output .= '<span class="id">#' . $item[ 'ID' ] . '</span>';
				$output .= '</div>';
				return $output;

			default:
				return $item[$column_name];
		}
	}

	/**
	* Sort the data
	*/
	private function sort_data($a, $b) {
		// Set defaults
		$orderby = (!empty($_GET['orderby'])) ? sanitize_sql_orderby($_GET['orderby']) : 'post_title';
		$order = (!empty($_GET['order'])) ? sanitize_sql_orderby($_GET['order']) : 'asc';
		$result = strnatcasecmp( $a[$orderby], $b[$orderby] );

		return ($order === 'asc') ? $result : -$result;
	}

	/**
	* The button that allows to save updated slugs
	*/
	function extra_tablenav($which) {
		global $wpdb, $active_section, $active_subsection;

		$button_top = __( 'Save all the URIs below', 'permalink-manager' );
		$button_bottom = __( 'Save all the URIs above', 'permalink-manager' );

		$html = "<div class=\"alignleft actions\">";
		$html .= get_submit_button( ${"button_$which"}, 'primary alignleft', "update_all_slugs[{$which}]", false, array( 'id' => 'doaction', 'value' => 'update_all_slugs' ) );

    if ($which == "top") {
			// Searchbox
			$html .= '<div class="alignright">';
			$html .= $this->search_box(__('Search', 'permalink-manager'), 'search-input');
			$html .= '</div>';

			// Filter by date
			$months = $wpdb->get_results("SELECT DISTINCT month(post_date) AS m, year(post_date) AS y FROM {$wpdb->posts} WHERE post_status IN ($this->displayed_post_statuses) AND post_type IN ($this->displayed_post_types) ORDER BY post_date DESC", ARRAY_A);
			if($months) {
				$month_key = 'month';
				$screen = get_current_screen();
				$current_url = add_query_arg(array(
			    'page' => PERMALINK_MANAGER_PLUGIN_SLUG,
			    'section' => $active_section,
			    'subsection' => $active_subsection
				), admin_url($screen->parent_file));

				$html .= "<div id=\"months-filter\" class=\"alignright hide-if-no-js\" data-filter-url=\"{$current_url}\">";
				$html .= "<select id=\"months-filter-select\" name=\"{$month_key}\">";
				$html .= sprintf("<option value=\"\">%s</option>", __("All dates", "permalink-manager"));
				foreach($months as $month) {
					$month_raw = "{$month['y']}-{$month['m']}";
					$month_human_name = date_i18n("F Y", strtotime($month_raw));

					$selected = (!empty($_REQUEST[$month_key])) ? selected($_REQUEST[$month_key], $month_raw, false) : "";
					$html .= "<option value=\"{$month_raw}\" {$selected}>{$month_human_name}</option>";
				}
				$html .= "</select>";
				$html .= get_submit_button(__("Filter", "permalink-manager"), 'button', false, false, array('id' => 'months-filter-button', 'name' => 'months-filter-button'));
				$html .= "</div>";
			}
    }
		$html .= "</div>";

		echo $html;
	}

	/**
	* Search box
	*/
	public function search_box($text = '', $input_id = '') {
		$search_query = (!empty($_REQUEST['s'])) ? esc_attr($_REQUEST['s']) : "";

    $output = "<p class=\"search-box\">";
		$output .= "<label class=\"screen-reader-text\" for=\"{$input_id}\">{$text}:</label>";
		$output .= Permalink_Manager_Admin_Functions::generate_option_field('s', array('value' => $search_query, 'type' => 'search'));
		$output .= get_submit_button($text, 'button', false, false, array('id' => 'search-submit', 'name' => 'search-submit'));
  	$output .= "</p>";

		return $output;
	}

	/**
	* Prepare the items for the table to process
	*/
	public function prepare_items() {
		global $wpdb, $permalink_manager_options;

		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$current_page = $this->get_pagenum();

		// Get query variables
		$per_page = $permalink_manager_options['screen-options']['per_page'];

		// SQL query parameters
		$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? sanitize_sql_orderby($_REQUEST['order']) : 'desc';
		$orderby = (isset($_REQUEST['orderby'])) ? sanitize_sql_orderby($_REQUEST['orderby']) : 'ID';
		$offset = ($current_page - 1) * $per_page;
		$search_query = (!empty($_REQUEST['s'])) ? esc_sql($_REQUEST['s']) : "";

		// Extra filters
		$extra_filters = $attachment_support = '';
		if(!empty($_GET['month'])) {
			$month = date("n", strtotime($_GET['month']));
			$year = date("Y", strtotime($_GET['month']));

			$extra_filters .= "AND month(post_date) = {$month} AND year(post_date) = {$year}";
		}

		// Support for attachments
		if(strpos($this->displayed_post_types, 'attachment') !== false) {
			$attachment_support = " OR (post_type = 'attachment')";
		}

		// Grab posts from database
		$sql_parts['start'] = "SELECT * FROM {$wpdb->posts} ";
		if($search_query) {
			$sql_parts['where'] = "WHERE (LOWER(post_title) LIKE LOWER('%{$search_query}%') ";

			// Search in array with custom URIs
			$found = Permalink_Manager_Helper_Functions::search_uri($search_query, 'posts');
			if($found) {
				$sql_parts['where'] .= sprintf("OR ID IN (%s)", implode(',', (array) $found));
			}
			$sql_parts['where'] .= " ) AND ((post_status IN ($this->displayed_post_statuses) AND post_type IN ($this->displayed_post_types)) {$attachment_support}) {$extra_filters} ";
		} else {
			$sql_parts['where'] = "WHERE ((post_status IN ($this->displayed_post_statuses) AND post_type IN ($this->displayed_post_types)) {$attachment_support}) {$extra_filters} ";
		}

		// Do not display excluded posts in Bulk URI Editor
		$excluded_posts = (array) apply_filters('permalink_manager_excluded_post_ids', array());
		if(!empty($excluded_posts)) {
			$sql_parts['where'] .= sprintf("AND ID NOT IN ('%s') ", implode("', '", $excluded_posts));
		}

		$sql_parts['end'] = "ORDER BY {$orderby} {$order}";

		// Prepare the SQL query
		$sql_query = implode("", $sql_parts);

		// Count items
		$count_query = str_replace('SELECT *', 'SELECT COUNT(*)', $sql_query);
		$total_items = $wpdb->get_var($count_query);

		// Pagination support
		$sql_query .= sprintf(" LIMIT %d, %d", $offset, $per_page);

		// Get items
		$sql_query = apply_filters('permalink_manager_filter_uri_editor_query', $sql_query, $this, $sql_parts, $is_taxonomy = false);
		$all_items = $wpdb->get_results($sql_query, ARRAY_A);

		// Debug SQL query
		if(isset($_REQUEST['debug_editor_sql'])) {
			$debug_txt = "<textarea style=\"width:100%;height:300px\">{$sql_query} \n\nOffset: {$offset} \nPage: {$current_page}\nPer page: {$per_page} \nTotal: {$total_items}</textarea>";
			wp_die($debug_txt);
		}

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		));

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $all_items;
	}

}
