<?php

/**
* Additional back-end functions related to Wordpress Dashboard UI
*/
class Permalink_Manager_Admin_Functions extends Permalink_Manager_Class {

	public $menu_name, $sections, $active_section, $active_subsection;
	public $plugin_basename = PERMALINK_MANAGER_BASENAME;

	public function __construct() {
		add_action( 'admin_menu', array($this, 'add_menu_page') );
		add_action( 'admin_init', array($this, 'init') );
		add_action( 'admin_bar_menu',  array($this, 'fix_customize_url'), 41);

		add_action( 'admin_notices', array($this, 'display_plugin_notices'));
		add_action( 'admin_notices', array($this, 'display_global_notices'));
		add_action( 'wp_ajax_dismissed_notice_handler', array($this, 'hide_global_notice') );

		add_filter( 'default_hidden_columns', array($this, 'quick_edit_hide_column'), 99, 2 );
	}

	/**
	 * Hooks that should be triggered with "admin_init"
	 */
	public function init() {
		// Additional links in "Plugins" page
		add_filter( "plugin_action_links_{$this->plugin_basename}", array($this, "plugins_page_links") );
		add_filter( "plugin_row_meta", array($this, "plugins_page_meta"), 10, 2 );

		// Detect current section
		$this->sections = apply_filters('permalink_manager_sections', array());
		$this->get_current_section();
	}

	/**
	 * Fix Customize URL
	 */
	public function fix_customize_url($wp_admin_bar) {
		$object = get_queried_object();

		$customize = $wp_admin_bar->get_node('customize');

		if(empty($customize->href)) { return; }

		$_REQUEST['customize_url'] = true;
		if(!empty($object->ID)) {
			$new_url = get_permalink($object->ID);
		} else if(!empty($object->term_id)) {

		}
		$_REQUEST['customize_url'] = false;

		if(!empty($new_url)) {
			$new_url = urlencode_deep($new_url);
			$customize_url = preg_replace('/url=([^&]+)/', "url={$new_url}", $customize->href);

		  $wp_admin_bar->add_node(array(
				'id' => 'customize',
				'href' => $customize_url,
		  ));
		}
	}

	/**
	 * Get current section (only in plugin sections)
	 */
	public function get_current_section() {
		global $active_section, $active_subsection, $current_admin_tax;

		// 1. Get current section
		if(isset($_GET['page']) && $_GET['page'] == PERMALINK_MANAGER_PLUGIN_SLUG) {
			if(isset($_POST['section'])) {
				$this->active_section = sanitize_title_with_dashes($_POST['section']);
			} else if(isset($_GET['section'])) {
				$this->active_section = sanitize_title_with_dashes($_GET['section']);
			} else {
				$sections_names = array_keys($this->sections);
				$this->active_section = $sections_names[0];
			}
		}

		// 2. Get current subsection
		if($this->active_section && isset($this->sections[$this->active_section]['subsections'])) {
			if(isset($_POST['subsection'])) {
				$this->active_subsection = sanitize_title_with_dashes($_POST['subsection']);
			} else if(isset($_GET['subsection'])) {
				$this->active_subsection = sanitize_title_with_dashes($_GET['subsection']);
			} else {
				$subsections_names = array_keys($this->sections[$this->active_section]['subsections']);
				$this->active_subsection = $subsections_names[0];
			}
		}

		// Check if current admin page is related to taxonomies
		if(substr($this->active_subsection, 0, 4) == 'tax_') {
			$current_admin_tax = substr($this->active_subsection, 4, strlen($this->active_subsection));
		} else {
			$current_admin_tax = false;
		}

		// Set globals
		$active_section = $this->active_section;
		$active_subsection = $this->active_subsection;
	}

	/**
	 * Add menu page.
	 */
	public function add_menu_page() {
		$this->menu_name = add_management_page( __('Permalink Manager', 'permalink-manager'), __('Permalink Manager', 'permalink-manager'), 'manage_options', PERMALINK_MANAGER_PLUGIN_SLUG, array($this, 'display_section') );

		add_action( 'admin_init', array($this, 'enqueue_styles' ) );
		add_action( 'admin_init', array($this, 'enqueue_scripts' ) );
	}

	/**
	 * Register the CSS file for the dashboard.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'permalink-manager-plugins', PERMALINK_MANAGER_URL . '/out/permalink-manager-plugins.css', array(), PERMALINK_MANAGER_VERSION, 'all' );
		wp_enqueue_style( 'permalink-manager', PERMALINK_MANAGER_URL . '/out/permalink-manager-admin.css', array('permalink-manager-plugins'), PERMALINK_MANAGER_VERSION, 'all' );
	}

	/**
	 * Register the JavaScript file for the dashboard.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'permalink-manager-plugins', PERMALINK_MANAGER_URL . '/out/permalink-manager-plugins.js', array( 'jquery', ), PERMALINK_MANAGER_VERSION, false );
		wp_enqueue_script( 'permalink-manager', PERMALINK_MANAGER_URL . '/out/permalink-manager-admin.js', array( 'jquery', 'permalink-manager-plugins' ), PERMALINK_MANAGER_VERSION, false );

		wp_localize_script( 'permalink-manager', 'permalink_manager', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'url' => PERMALINK_MANAGER_URL,
			'confirm' => __('Are you sure? This action cannot be undone!', 'permalink-manager'),
			'spinners' => admin_url('images'))
		);

	}

	/**
	 * Get admin url for the plugin
	 */
	public static function get_admin_url($append = '') {
		//return menu_page_url(PERMALINK_MANAGER_PLUGIN_SLUG, false) . $append;
		$admin_page = sprintf("tools.php?page=%s", PERMALINK_MANAGER_PLUGIN_SLUG . $append);

		return admin_url($admin_page);
	}

	/**
	 * Additional links on "Plugins" page
	 */
	public function plugins_page_links($links) {
		$new_links = array(
			 sprintf('<a href="%s">%s</a>', $this->get_admin_url(), __( 'URI Editor', 'permalink-manager' )),
			 sprintf('<a href="%s">%s</a>', $this->get_admin_url('&section=settings'), __( 'Settings', 'permalink-manager' )),
		);

		return array_merge($links, $new_links);
	}

	/**
	 * Additional meta links on "Plugins" page
	 */
	public function plugins_page_meta($links, $file) {
		if($file == PERMALINK_MANAGER_BASENAME) {
			$new_links = array(
				'doc' => sprintf('<a href="%s?utm_source=plugin_admin_page" target="_blank">%s</a>', 'https://permalinkmanager.pro/docs/', __( 'Documentation', 'permalink-manager' ))
			);

			if(!defined('PERMALINK_MANAGER_PRO')) {
				$new_links['upgrade'] = sprintf('<a href="%s?utm_source=plugin_admin_page" target="_blank"><strong>%s</strong></a>', PERMALINK_MANAGER_WEBSITE, __( 'Buy Permalink Manager Pro', 'permalink-manager' ));
			}

			$links = array_merge($links, $new_links);
		}

		return $links;
	}

	/**
	 * Generate the fields
	 */
	static public function generate_option_field($input_name, $args) {
		global $permalink_manager_options, $permalink_manager_permastructs;

		// Reset $fields variables
		$fields = $section_name = $field_name = '';

		// Allow to filter the $args
		$args = apply_filters('permalink_manager_field_args', $args, $input_name);

		$field_type = (isset($args['type'])) ? $args['type'] : 'text';
		$default = (isset($args['default'])) ? $args['default'] : '';
		$label = (isset($args['label'])) ? $args['label'] : '';
		$rows = (isset($args['rows'])) ? "rows=\"{$rows}\"" : "rows=\"5\"";
		$description = (isset($args['before_description'])) ? $args['before_description'] : "";
		$description .= (isset($args['description'])) ? "<p class=\"field-description description\">{$args['description']}</p>" : "";
		$description .= (isset($args['after_description'])) ? $args['after_description'] : "";
		$description .= (isset($args['pro'])) ? sprintf("<p class=\"field-description description alert info\">%s</p>", (self::pro_text(true))) : "";
		$append_content = (isset($args['append_content'])) ? "{$args['append_content']}" : "";

		// Input attributes
		$input_atts = (isset($args['input_class'])) ? "class='{$args['input_class']}'" : '';
		$input_atts .= (isset($args['readonly'])) ? " readonly='readonly'" : '';
		$input_atts .= (isset($args['disabled'])) ? " disabled='disabled'" : '';
		$input_atts .= (isset($args['placeholder'])) ? " placeholder='{$args['placeholder']}'" : '';
		$input_atts .= (isset($args['extra_atts'])) ? " {$args['extra_atts']}" : '';

		// Display the field if the related class exists
		if(!empty($args['class_exists'])) {
			$related_classes = (array) $args['class_exists'];
			$related_classes_exist = 0;

			foreach($related_classes as $related_class) {
				if(class_exists($related_class)) {
					$related_classes_exist = 1;
					break;
				}
			}

			// Do not display if the related class it not found
			if(empty($related_classes_exist)) {
				$field_type = $args['container_class'] = 'hidden';
			}
		}

		// Check the container classes
		$container_class = (isset($args['container_class'])) ? " class=\"{$args['container_class']} field-container\"" : " class=\"field-container\"";

		// Get the field value (if it is not set in $args)
		if(isset($args['value']) && empty($args['value']) == false) {
			$value = $args['value'];
		} else {
			// Extract the section and field name from $input_name
			preg_match('/([^\[]+)(?:\[([^\[]+)\])(?:\[([^\[]+)\])?/', $input_name, $field_section_and_name);

			if($field_section_and_name) {
				$section_name = $field_section_and_name[1];
				if(!empty($field_section_and_name[3])) {
					$field_name = $field_section_and_name[2];
					$subsection_name = $field_section_and_name[3];
					$value = (isset($permalink_manager_options[$section_name][$field_name][$subsection_name])) ? $permalink_manager_options[$section_name][$field_name][$subsection_name] : $default;
				} else {
					$field_name = $field_section_and_name[2];
					$value = (isset($permalink_manager_options[$section_name][$field_name])) ? $permalink_manager_options[$section_name][$field_name] : $default;
				}
			} else {
				$value = (isset($permalink_manager_options[$input_name])) ? $permalink_manager_options[$input_name] : $default;
			}
		}

		switch($field_type) {
			case 'checkbox' :
				$fields .= '<div class="checkboxes">';
				foreach($args['choices'] as $choice_value => $choice) {
					$input_template = "<label for='%s[]'><input type='checkbox' %s value='%s' name='%s[]' %s /> %s</label>";

					if(empty($choice['label']) && is_array($choice)) {
						if(in_array($choice_value, array('post_types', 'taxonomies'))) {
							$group_labela = array('post_types' => __('Post types', 'permalink-manager'), 'taxonomies' => __('Taxonomies', 'permalink-manager'));
							$fields .= sprintf('<p>%s</p>', $group_labela[$choice_value]);
						}

						foreach($choice as $sub_choice_value => $sub_choice) {
							$label = (!empty($sub_choice['label'])) ? $sub_choice['label'] : $sub_choice;
							$atts = (!empty($value[$choice_value]) && in_array($sub_choice_value, $value[$choice_value])) ? "checked='checked'" : "";
							$atts .= (!empty($sub_choice['atts'])) ? " {$sub_choice['atts']}" : "";

							$fields .= sprintf($input_template, $input_name, $input_atts, $sub_choice_value, "{$input_name}[{$choice_value}]", $atts, $label);
						}
					} else {
						$label = (!empty($choice['label'])) ? $choice['label'] : $choice;
						$atts = (is_array($value) && in_array($choice_value, $value)) ? "checked='checked'" : "";
						$atts .= (!empty($choice['atts'])) ? " {$choice['atts']}" : "";

						$fields .= sprintf($input_template, $input_name, $input_atts, $choice_value, $input_name, $atts, $label);
					}
				}
				$fields .= '</div>';

				// Add helper checkboxes for bulk actions
				if(isset($args['select_all']) || isset($args['unselect_all'])) {
					$select_all_label = (!empty($args['select_all'])) ? $args['select_all'] : __('Select all', 'permalink-manager');
					$unselect_all_label = (!empty($args['unselect_all'])) ? $args['unselect_all'] : __('Unselect all', 'permalink-manager');

					$fields .= "<p class=\"checkbox_actions extra-links\">";
					$fields .= (isset($args['select_all'])) ? "<a href=\"#\" class=\"select_all\">{$select_all_label}</a>&nbsp;" : "";
					$fields .= (isset($args['unselect_all'])) ? "<a href=\"#\" class=\"unselect_all\">{$unselect_all_label}</a>" : "";
					$fields .= "</p>";
				}
			break;

			case 'single_checkbox' :
				$fields .= '<div class="single_checkbox">';
				if(is_array($value)) {
					$input_key = preg_replace('/(.*)(?:\[([^\[]+)\])$/', '$2', $input_name);
					$checked = (!empty($value[$input_key])) ? "checked='checked'" : "";
				} else {
					$checked = ($value == 1) ? "checked='checked'" : "";
				}
				$checkbox_label = (isset($args['checkbox_label'])) ? $args['checkbox_label'] : '';

				$fields .= "<input type='hidden' {$input_atts} value='0' name='{$input_name}' />";
				$fields .= "<label for='{$input_name}'><input type='checkbox' {$input_atts} value='1' name='{$input_name}' {$checked} /> {$checkbox_label}</label>";
				$fields .= '</div>';
			break;

			case 'radio' :
				$fields .= '<div class="radios">';
				foreach($args['choices'] as $choice_value => $choice) {
					$label = (is_array($choice)) ? $choice['label'] : $choice;
					$atts = ($choice_value == $value) ? "checked='checked'" : "";
					$atts .= (!empty($choice['atts'])) ? " {$choice['atts']}" : "";

					$fields .= "<label for='{$input_name}[]'><input type='radio' {$input_atts} value='{$choice_value}' name='{$input_name}[]' {$atts} /> {$label}</label>";
				}
				$fields .= '</div>';
			break;

			case 'select' :
				$fields .= '<span class="select">';
				$fields .= "<select name='{$input_name}' {$input_atts}>";
				foreach($args['choices'] as $choice_value => $choice) {
					$label = (is_array($choice)) ? $choice['label'] : $choice;
					$atts = ($choice_value == $value) ? "selected='selected'" : "";
					$atts .= (!empty($choice['atts'])) ? " {$choice['atts']}" : "";

					$fields .= "<option value='{$choice_value}' {$atts}>{$label}</option>";
				}
				$fields .= '</select>';
				$fields .= '</span>';
				break;

			case 'number' :
				$fields .= "<input type='number' {$input_atts} value='{$value}' name='{$input_name}' />";
				break;

			case 'hidden' :
				$fields .= "<input type='hidden' {$input_atts} value='{$value}' name='{$input_name}' />";
				break;

			case 'textarea' :
				$fields .= "<textarea {$input_atts} name='{$input_name}' {$rows}>{$value}</textarea>";
				break;

			case 'pre' :
				$fields .= "<pre {$input_atts}>{$value}</pre>";
				break;

			case 'info' :
				$fields .= "<div {$input_atts}>{$value}</div>";
				break;

			case 'clearfix' :
				return "<div class=\"clearfix\"></div>";

			case 'permastruct' :
				$siteurl = Permalink_Manager_Helper_Functions::get_permalink_base();

				if(!empty($args['post_type'])) {
					$type = $args['post_type'];
					$type_name = $type['name'];
					$content_type = 'post_types';

					$permastructures = (!empty($permalink_manager_permastructs['post_types'])) ? $permalink_manager_permastructs['post_types'] : array();
				} else if(!empty($args['taxonomy'])) {
					$type = $args['taxonomy'];
					$type_name = $type['name'];
					$content_type = "taxonomies";

					$permastructures = (!empty($permalink_manager_permastructs['taxonomies'])) ? $permalink_manager_permastructs['taxonomies'] : array();
				} else {
					break;
				}

				// Get permastructures
				$default_permastruct = trim(Permalink_Manager_Helper_Functions::get_default_permastruct($type_name), "/");
				$current_permastruct = isset($permastructures[$type_name]) ? $permastructures[$type_name] : $default_permastruct;

				// Append extra attributes
				$input_atts .= " data-default=\"{$default_permastruct}\"";
				$input_atts .= " placeholder=\"{$default_permastruct}\"";

				$fields .= "<div class=\"all-permastruct-container\">";

				// 1. Default permastructure
				$fields .= "<div class=\"permastruct-container\">";
				$fields .= "<span><code>{$siteurl}/</code></span>";
				$fields .= "<span><input type='text' {$input_atts} value='{$current_permastruct}' name='{$input_name}'/></span>";
				$fields .= "</div>";

				$fields .= "<div class=\"permastruct-toggle\">";

				// 2A. Permastructure for each language
				$languages = (array) Permalink_Manager_Language_Plugins::get_all_languages(true);
				if($languages) {
					$fields .= sprintf(
						"<h4>%s</h4><p class=\"permastruct-instruction\">%s</p>",
						__("Permastructure translations", "permalink-manager"),
						__("If you would like to translate the permastructures and set-up different permalink structure per language, please fill in the fields below. Otherwise the permastructure set for default language (see field above) will be applied.", "permalink-manager")
					);

					foreach($languages as $lang => $name) {
						$current_lang_permastruct = isset($permastructures["{$type_name}_{$lang}"]) ? $permastructures["{$type_name}_{$lang}"] : '';

						$fields .= "<label>{$name}</label>";
						$fields .= "<div class=\"permastruct-container\">";
						$fields .= "<span><code>{$siteurl}/</code></span>";
						$fields .= sprintf("<span><input type='text' %s value='%s' name='%s'/></span>", $input_atts, $current_lang_permastruct, str_replace("]", "_{$lang}]", $input_name));
						$fields .= "</div>";
					}
				}

				// 2B. Restore default permalinks
				$fields .= sprintf(
					"<p class=\"default-permastruct-row columns-container\"><span class=\"column-2_4\"><strong>%s:</strong> %s</span><span class=\"column-2_4\"><a href=\"#\" class=\"restore-default\"><span class=\"dashicons dashicons-image-rotate\"></span> %s</a></span></p>",
					__("Default permastructure", "permalink-manager"), esc_html($default_permastruct),
					__("Restore default permastructure", "permalink-manager")
				);

				// 2B. Do not auto-append slug field
				$fields .= sprintf(
					"<h4>%s</h4><div class=\"settings-container\">%s</div>",
					__("Permastructure settings", "permalink-manager"),
					self::generate_option_field("permastructure-settings[do_not_append_slug][$content_type][{$type_name}]", array('type' => 'single_checkbox', 'checkbox_label' => __("Do not automatically append the slug", "permalink-manager")))
				);

				$fields .= "</div>";

				// 3. Show toggle button
				$fields .= sprintf(
					"<p class=\"permastruct-toggle-button\"><a href=\"#\"><span class=\"dashicons dashicons-admin-settings\"></span> %s</a></p>",
					__("Show additional settings", "permalink-manager")
				);

				$fields .= "</div>";

				break;

			default :
				$fields .= "<input type='text' {$input_atts} value='{$value}' name='{$input_name}'/>";
		}

		// Get the final HTML output
		if(isset($args['container']) && $args['container'] == 'tools') {
			$html = "<div{$container_class}>";
			$html .= "<h4>{$label}</h4>";
			$html .= "<div class='{$input_name}-container'>{$fields}</div>";
			$html .= $description;
			$html .= $append_content;
			$html .= "</div>";
		} else if(isset($args['container']) && $args['container'] == 'row') {
			$html = sprintf("<tr id=\"%s\" data-field=\"%s\" %s>", esc_attr(preg_replace('/(?:.*\[)(.*)(?:\].*)/', '$1', $input_name)), $input_name, $container_class);
			$html .= sprintf("<th><label for=\"%s\">%s</label></th>", $input_name, $args['label']);
			$html .= sprintf("<td><fieldset>%s%s</fieldset></td>", $fields, $description);
			$html .= "</tr>";
			$html .= ($append_content) ? "<tr class=\"appended-row\"><td colspan=\"2\">{$append_content}</td></tr>" : "";
		} else if(isset($args['container']) && $args['container'] == 'screen-options') {
			$html = "<fieldset data-field=\"{$input_name}\" {$container_class}><legend>{$args['label']}</legend>";
			$html .= "<div class=\"field-content\">{$fields}{$description}</div>";
			$html .= ($append_content) ? "<div class=\"appended-row\">{$append_content}</div>" : "";
			$html .= "</fieldset>";
		} else {
			$html = $fields . $append_content;
		}

		return apply_filters('permalink_manager_field_output', $html);
	}

	/**
	 * Display hidden field to indicate posts or taxonomies admin sections
	 */
	static public function section_type_field($type = 'post') {
		return self::generate_option_field('content_type', array('value' => $type, 'type' => 'hidden'));
	}

	/**
	 * Display the form
	 */
	static public function get_the_form($fields = array(), $container = '', $button = array(), $sidebar = '', $nonce = array(), $wrap = false, $form_class = '') {
		// 1. Check if the content will be displayed in columns and button details
		switch($container) {
			case 'columns-3' :
				$wrapper_class = 'columns-container';
				$form_column_class = 'column column-2_3';
				$sidebar_class = 'column column-1_3';
				break;

			case 'tabs' :
				$wrapper_class = 'form settings-tabs';
				$sidebar_class = $form_column_class = '';
				break;

			// there will be more cases in future ...
			default :
				$sidebar_class = 'sidebar';
				$wrapper_class = $form_column_class = '';
		}

		// 2. Process the array with button and nonce field settings
		$button_text = (!empty($button['text'])) ? $button['text'] : '';
		$button_class = (!empty($button['class'])) ? $button['class'] : '';
		$button_attributes = (!empty($button['attributes'])) ? $button['attributes'] : '';
		$nonce_action = (!empty($nonce['action'])) ? $nonce['action'] : '';
		$nonce_name = (!empty($nonce['name'])) ? $nonce['name'] : '';
		$form_classes = (!empty($form_class)) ? $form_class : '';

		// 3. Now get the HTML output (start section row container)
		$html = ($wrapper_class) ? "<div class=\"{$wrapper_class}\">" : '';

		// 4. Display settings tabs
		if($container == 'tabs') {
			// Get active section
			$active_tab = (!empty($_POST['pm_active_tab'])) ? $_POST['pm_active_tab'] : key(array_slice($fields, 0, 1, true));

			$html .= "<ul class=\"subsubsub\">";
			foreach ($fields as $tab_name => $tab) {
				$active_class = ($active_tab === $tab_name) ? 'current' : '';
				$html .= sprintf("<li><a href=\"%s\" class=\"%s\" data-tab=\"%s\">%s</a></li>", "#pm_tab_{$tab_name}", $active_class, $tab_name, $tab['section_name']);
			}
			$html .= "</ul>";
		}

		// 5. Display some notes
		if($sidebar_class && $sidebar) {
			$html .= "<div class=\"{$sidebar_class}\">";
			$html .= "<div class=\"section-notes\">";
			$html .= $sidebar;
			$html .= "</div>";
			$html .= "</div>";
		}

		// 6. Start fields' section
		$html .= ($form_column_class) ? "<div class=\"{$form_column_class}\">" : "";
		$html .= "<form method=\"POST\" class=\"{$form_classes}\">";
		$html .= ($wrap) ? "<table class=\"form-table\">" : "";

		// 7. Loop through all fields assigned to this section
		foreach($fields as $field_name => $field) {
			$tab_name = (isset($field['fields'])) ? $field_name : '';
			$field_name = (!empty($field['name'])) ? $field['name'] : $field_name;

			// A. Display table row
			if(isset($field['container']) && $field['container'] == 'row') {
				$row_output = "";

				// Loop through all fields assigned to this section
				if(isset($field['fields'])) {
					foreach($field['fields'] as $section_field_id => $section_field) {
						$section_field_name = (!empty($section_field['name'])) ? $section_field['name'] : "{$field_name}[$section_field_id]";
						$section_field['container'] = 'row';

						$row_output .= self::generate_option_field($section_field_name, $section_field);
					}
				} else {
					$row_output .= self::generate_option_field($field_name, $field);
				}

				if(isset($field['section_name'])) {
					if($container == 'tabs') {
						$is_active_tab = (!empty($active_tab) && $active_tab == $tab_name) ? 'class="active-tab"' : '';

						$html .= "<div id=\"pm_{$tab_name}\" data-tab=\"{$tab_name}\" {$is_active_tab}>";
					}

					$html .= "<h3>{$field['section_name']}</h3>";
					$html .= (isset($field['append_content'])) ? $field['append_content'] : "";
					$html .= (isset($field['description'])) ? "<p class=\"description\">{$field['description']}</p>" : "";
					$html .= "<table class=\"form-table\" data-field=\"{$field_name}\">{$row_output}</table>";
					$html .= ($container == 'tabs') ? "</div>" : "";
				} else {
					$html .= $row_output;
				}
			}
			// B. Display single field
			else {
				$html .= self::generate_option_field($field_name, $field);
			}
		}

		$html .= ($wrap) ? "</table>" : "";

		// 8. Add a hidden field with section name for settings page
		if($container == 'tabs' && !empty($active_tab)) {
			$html .= self::generate_option_field('pm_active_tab', array('value' => $active_tab, 'type' => 'hidden', 'readonly' => true));
		}

		// 9. End the fields' section + add button & nonce fields
		if($nonce_action && $nonce_name) {
			$html .= wp_nonce_field($nonce_action, $nonce_name, true, true);
			$html .= self::generate_option_field('pm_session_id', array('value' => uniqid(), 'type' => 'hidden'));
		}
		$html .= ($button_text) ? get_submit_button($button_text, $button_class, '', false, $button_attributes) : "";
		$html .= '</form>';
		$html .= ($form_column_class) ? "</div>" : "";

		// 10. End the section row container
		$html .= ($wrapper_class) ? "</div>" : "";

		return $html;
	}

	/**
	 * Display the plugin sections.
	 */
	public function display_section() {
		global $wpdb, $permalink_manager_before_sections_html, $permalink_manager_after_sections_html;

		$html = "<div id=\"permalink-manager\" class=\"wrap\">";

		$donate_link = (self::is_pro_active()) ? "" : sprintf("<a href=\"%s\" target=\"_blank\" class=\"page-title-action\">%s</a>", PERMALINK_MANAGER_DONATE, __("Donate", "permalink-manager"));
		$html .= sprintf("<h2 id=\"plugin-name-heading\">%s <a href=\"http://maciejbis.net\" class=\"author-link\" target=\"_blank\">%s</a> %s</h2>", PERMALINK_MANAGER_PLUGIN_NAME, __("by Maciej Bis", "permalink-manager"), $donate_link);

		// Display the tab navigation
		$html .= "<div id=\"permalink-manager-tab-nav\" class=\"nav-tab-wrapper\">";
		foreach($this->sections as $section_name => $section_properties) {
			$active_class = ($this->active_section === $section_name) ? 'nav-tab-active nav-tab' : 'nav-tab';
			$section_url = $this->get_admin_url("&section={$section_name}");

			$html .= sprintf("<a href=\"%s\" class=\"%s section_%s\">%s</a>", $section_url, $active_class, $section_name, $section_properties['name']);
		}

		// Upgrade to Pro version
		$html .= (self::is_pro_active() == false) ? sprintf("<a href=\"%s\" target=\"_blank\" class=\"nav-tab section_upgrade\">%s</a>", 'https://permalinkmanager.pro/buy-permalink-manager-pro/?utm_source=plugin_upgrade', __('Upgrade to PRO', 'permalink-manager')) : '';
		$html .= "</div>";

		// Now display the active section
		$html .= "<div id=\"permalink-manager-sections\">";
		$active_section_array = (isset($this->sections[$this->active_section])) ? $this->sections[$this->active_section] : "";

		// Display addidional navigation for subsections
		if(isset($this->sections[$this->active_section]['subsections'])) {
			$html .= "<ul class=\"subsubsub\">";
			foreach ($this->sections[$this->active_section]['subsections'] as $subsection_name => $subsection) {
				$active_class = ($this->active_subsection === $subsection_name) ? 'current' : '';
				$subsection_url = $this->get_admin_url("&section={$this->active_section}&subsection={$subsection_name}");

				$html .= "<li><a href=\"{$subsection_url}\" class=\"{$active_class}\">{$subsection['name']}</a></li>";
			}
			$html .= "</ul>";
		}

		// A. Execute the function assigned to the subsection
		if(isset($active_section_array['subsections'][$this->active_subsection]['function'])) {
			$class_name = $active_section_array['subsections'][$this->active_subsection]['function']['class'];
			$section_object = new $class_name();

			$section_content = call_user_func(array($section_object, $active_section_array['subsections'][$this->active_subsection]['function']['method']));
		}
		// B. Execute the function assigned to the section
		else if(isset($active_section_array['function'])) {
			$class_name = $active_section_array['function']['class'];
			$section_object = new $class_name();

			$section_content = call_user_func(array($section_object, $active_section_array['function']['method']));
		}
		// C. Display the raw HTMl output of subsection
		else if(isset($active_section_array['subsections'][$this->active_subsection]['html'])) {
			$section_content = (isset($active_section_array['subsections'][$this->active_subsection]['html'])) ? $active_section_array['subsections'][$this->active_subsection]['html'] : "";
		}
		// D. Try to display the raw HTMl output of section
		else {
			$section_content = (isset($active_section_array['html'])) ? $active_section_array['html'] : "";
		}

		$html .= "<div class=\"single-section\" data-section=\"{$this->active_section}\" id=\"{$this->active_section}\">{$section_content}</div>";
		$html .= "</div>";

		// Display alerts and another content if needed and close .wrap container
		$html .= $permalink_manager_after_sections_html;
		$html .= "</div>";

		echo $html;
	}

	/**
	 * Display the table with updated slugs after one of the actions is triggered
	 */
	static function display_updated_slugs($updated_array, $return_array = false, $display_full_table = true) {
		global $permalink_manager_before_sections_html, $permalink_manager_after_sections_html;

		$updated_slugs_count = 0;
		$html = $main_content = $alert = "";

		if(is_array($updated_array)) {
			// Check if slugs should be displayed
			$first_slug = reset($updated_array);
			$show_slugs = (!empty($_POST['mode']) && $_POST['mode'] == 'slugs') ? true : false;

			$header_footer = '<tr>';
			$header_footer .= '<th class="column-primary">' . __('Title', 'permalink-manager') . '</th>';
			if($show_slugs) {
				$header_footer .= (isset($first_slug['old_slug'])) ? '<th>' . __('Old Slug', 'permalink-manager') . '</th>' : "";
				$header_footer .= (isset($first_slug['new_slug'])) ? '<th>' . __('New Slug', 'permalink-manager') . '</th>' : "";
			} else {
				$header_footer .= '<th>' . __('Old URI', 'permalink-manager') . '</th>';
				$header_footer .= '<th>' . __('New URI', 'permalink-manager') . '</th>';
			}
			$header_footer .= '</tr>';

			foreach($updated_array as $row) {
				// Odd/even class
				$updated_slugs_count++;
				$alternate_class = ($updated_slugs_count % 2 == 1) ? ' class="alternate"' : '';

				// Taxonomy
				if(!empty($row['tax'])) {
					$term_link = get_term_link(intval($row['ID']), $row['tax']);
					$permalink = (is_wp_error($term_link)) ? "-" : $term_link;
				} else {
					$permalink = get_permalink($row['ID']);
				}

				// Decode permalink
				$permalink = rawurldecode(rawurldecode($permalink));

				$main_content .= "<tr{$alternate_class}>";
				$main_content .= '<td class="row-title column-primary" data-colname="' . __('Title', 'permalink-manager') . '">' . $row['item_title'] . "<a target=\"_blank\" href=\"{$permalink}\"><span class=\"small\">{$permalink}</span></a>" . '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __('Show more details', 'permalink-manager') . '</span></button></td>';
				if($show_slugs) {
					$main_content .= (isset($row['old_slug'])) ? '<td data-colname="' . __('Old Slug', 'permalink-manager') . '">' . rawurldecode($row['old_slug']) . '</td>' : "";
					$main_content .= (isset($row['new_slug'])) ? '<td data-colname="' . __('New Slug', 'permalink-manager') . '">' . rawurldecode($row['new_slug']) . '</td>' : "";
				} else {
					$main_content .= '<td data-colname="' . __('Old URI', 'permalink-manager') . '">' . rawurldecode($row['old_uri']) . '</td>';
					$main_content .= '<td data-colname="' . __('New URI', 'permalink-manager') . '">' . rawurldecode($row['new_uri']) . '</td>';
				}
				$main_content .= '</tr>';
			}

			// Merge header, footer and content
			if($display_full_table) {
				$html = '<h3 id="updated-list">' . __('List of updated items', 'permalink-manager') . '</h3>';
				$html .= '<table class="widefat wp-list-table updated-slugs-table">';
				$html .= "<thead>{$header_footer}</thead><tbody>{$main_content}</tbody><tfoot>{$header_footer}</tfoot>";
			} else {
				$html = $main_content;
			}

			$html .= '</table>';
		}

		// 3. Display the alert
		if(isset($updated_slugs_count)) {
			if($updated_slugs_count > 0) {
				$alert_content = sprintf( _n( '<strong class="updated_count">%d</strong> slug was updated!', '<strong class="updated_count">%d</strong> slugs were updated!', $updated_slugs_count, 'permalink-manager' ), $updated_slugs_count ) . ' ';
				$alert_content .= sprintf( __( '<a %s>Click here</a> to go to the list of updated slugs', 'permalink-manager' ), "href=\"#updated-list\"");

				$alert = self::get_alert_message($alert_content, 'updated updated_slugs');
			} else {
				$alert = self::get_alert_message(__( '<strong>No slugs</strong> were updated!', 'permalink-manager' ), 'error updated_slugs');
			}
		}

		if($return_array) {
			return array(
				'html' => $html,
				'alert' => $alert
			);
		} else {
			$permalink_manager_before_sections_html .= $alert;

			return $html;
		}
	}

	/**
	 * Check if URI Editors should be displayed for current user
	 */
	public static function current_user_can_edit_uris() {
		global $permalink_manager_options;

		$edit_uris_cap = (!empty($permalink_manager_options['general']['edit_uris_cap'])) ? $permalink_manager_options['general']['edit_uris_cap'] : 'publish_posts';

		return current_user_can($edit_uris_cap);
	}

	/**
	 * "Quick Edit" Box
	 */
	public static function quick_edit_column_form($is_taxonomy = false) {
		// Check the user capabilities
		if(self::current_user_can_edit_uris() === false) {
			return;
		}

		$html = self::generate_option_field('permalink-manager-quick-edit', array('value' => true, 'type' => 'hidden'));
		$html .= "<fieldset class=\"inline-edit-permalink\">";
		$html .= sprintf("<legend class=\"inline-edit-legend\">%s</legend>", __("Permalink Manager", "permalink-manager"));

		$html .= "<div class=\"inline-edit-col\">";
		$html .= sprintf("<label class=\"inline-edit-group\"><span class=\"title\">%s</span><span class=\"input-text-wrap\">%s</span></label>",
			__("Current URI", "permalink-manager"),
			self::generate_option_field("custom_uri", array("input_class" => "custom_uri", "value" => ''))
		);
		$html .= "</div>";

		$html .= "</fieldset>";

		// Append nonce field & element ID
		$html .= self::generate_option_field("permalink-manager-edit-uri-element-id", array("type" => "hidden", "input_class" => "permalink-manager-edit-uri-element-id", "value" => ""));
		$html .= wp_nonce_field( 'permalink-manager-edit-uri-box', 'permalink-manager-nonce', true, false );

		return $html;
	}

	/**
	 * Hide "Custom URI" column
	 */
	function quick_edit_hide_column($hidden, $screen) {
		$hidden[] = 'permalink-manager-col';
		return $hidden;
	}

	/**
	 * Display "Permalink Manager" box
	 */
	public static function display_uri_box($element, $gutenberg = false) {
		global $permalink_manager_options, $permalink_manager_uris;

		// Check the user capabilities
		if(self::current_user_can_edit_uris() === false) {
			return;
		}

		if(!empty($element->ID)) {
			$id = $element_id = $element->ID;
			$native_slug = $element->post_name;
			$is_draft = (!empty($element->post_status) && (in_array($element->post_status, array('draft', 'auto-draft')))) ? true : false;
			$is_front_page = Permalink_Manager_Helper_Functions::is_front_page($id);

			$auto_update_val = get_post_meta($id, "auto_update_uri", true);

			// Get URIs
			$uri = Permalink_Manager_URI_Functions_Post::get_post_uri($id, true, $is_draft);
			$default_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri($id);
			$native_uri = Permalink_Manager_URI_Functions_Post::get_default_post_uri($id, true);
		} else if(class_exists('Permalink_Manager_URI_Functions_Tax')) {
			$id = $element->term_id;
			$element_id = "tax-{$id}";
			$native_slug = $element->slug;

			$auto_update_val = get_term_meta($id, "auto_update_uri", true);

			// Get URIs
			$uri = Permalink_Manager_URI_Functions_Tax::get_term_uri($element->term_id, true);
			$default_uri = Permalink_Manager_URI_Functions_Tax::get_default_term_uri($element->term_id);
			$native_uri = Permalink_Manager_URI_Functions_Tax::get_default_term_uri($element->term_id, true);
		} else {
			return;
		}

		// Auto-update settings
		$auto_update_def_val = $permalink_manager_options["general"]["auto_update_uris"];
		$auto_update_def_label = ($auto_update_def_val) ? __("Yes", "permalink-manager") : __("No", "permalink-manager");
		$auto_update_choices = array(
			0 => array("label" => sprintf(__("Use global settings [%s]", "permalink-manager"), $auto_update_def_label), "atts" => "data-auto-update=\"{$auto_update_def_val}\""),
			1 => array("label" => __("Yes", "permalink-manager"), "atts" => "data-auto-update=\"1\""),
			-1 => array("label" => __("No", "permalink-manager"), "atts" => "data-auto-update=\"0\""),
			-2 => array("label" => __("No (ignore this URI in bulk tools)", "permalink-manager"), "atts" => "data-auto-update=\"2\"")
		);

		// Decode default URI
		$default_uri = rawurldecode($default_uri);

		// Start HTML output
		// 1. Button
		if(!$gutenberg) {
			$html = sprintf("<span><button type=\"button\" class=\"button button-small hide-if-no-js\" id=\"permalink-manager-toggle\">%s</button></span>", __("Permalink Manager", "permalink-manager"));

			$html .= "<div id=\"permalink-manager\" class=\"postbox permalink-manager-edit-uri-box\" style=\"display: none;\">";

			// 2. The heading
			$html .= "<a class=\"close-button\"><span class=\"screen-reader-text\">" . __("Close: ", "permalink-manager") . __("Permalink Manager", "permalink-manager") . "</span><span class=\"close-icon\" aria-hidden=\"false\"></span></a>";
			$html .= sprintf("<h2><span>%s</span></h2>", __("Permalink Manager", "permalink-manager"));

			// 3. The fields container [start]
			$html .= "<div class=\"inside\">";
		} else {
			$html = "<div class=\"permalink-manager-gutenberg permalink-manager-edit-uri-box\">";
		}

		// 4. Custom URI
		if(!empty($is_front_page)) {
			$custom_uri_field = self::generate_option_field("custom_uri", array("type" => "hidden", "extra_atts" => "data-default=\"{$default_uri}\" data-element-id=\"{$element_id}\"", "input_class" => "widefat custom_uri", "value" => rawurldecode($uri)));
			$custom_uri_field .= __("The custom URI cannot be edited on frontpage.", "permalink-manager");
		} else {
			$custom_uri_field = self::generate_option_field("custom_uri", array("extra_atts" => "data-default=\"{$default_uri}\" data-element-id=\"{$element_id}\"", "input_class" => "widefat custom_uri", "value" => rawurldecode($uri)));
			$custom_uri_field .= sprintf('<p class="uri_locked hidden">%s %s</p>', '<span class="dashicons dashicons-lock"></span>', __('The above permalink will be automatically updated to "Default URI" and is locked for editing.', 'permalink-manager'));
		}

		$html .= sprintf("<div class=\"custom_uri_container\"><p><label for=\"custom_uri\" class=\"strong\">%s %s</label></p><span>%s</span><span class=\"duplicated_uri_alert\"></span></div>",
			__("Current URI", "permalink-manager"),
			($element->ID) ? self::help_tooltip(__("If custom URI is not defined, a default URI will be set (see below). The custom URI can be edited only if 'Auto-update the URI' feature is not enabled.", "permalink-manager")) : "",
			$custom_uri_field
		);

		// 5. Auto-update URI
		if(empty($is_front_page)) {
			if(!empty($auto_update_choices)) {
				$html .= sprintf("<div><p><label for=\"auto_auri\" class=\"strong\">%s %s</label></p><span>%s</span></div>",
					__("Auto-update \"Current URI\"", "permalink-manager"),
					self::help_tooltip(__("If enabled, the 'Current URI' field will be automatically changed to 'Default URI' (displayed below) after the post is saved or updated.", "permalink-manager")),
					self::generate_option_field("auto_update_uri", array("type" => "select", "input_class" => "widefat auto_update", "value" => $auto_update_val, "choices" => $auto_update_choices))
				);
			}
		}

		// 6. Native slug
		if(!empty($element->ID) && !empty($permalink_manager_options["general"]["show_native_slug_field"])) {
			$native_slug_field = self::generate_option_field("native_slug", array("extra_atts" => "data-default=\"{$native_slug}\" data-element-id=\"{$element_id}\"", "input_class" => "widefat native_slug", "value" => rawurldecode($native_slug)));

			$html .= sprintf("<div class=\"native_slug_container\"><p><label for=\"native_slug\" class=\"strong\">%s %s</label></p><span>%s</span></div>",
				__("Native slug", "permalink-manager"),
				self::help_tooltip(__("The native slug is by default automatically used in native permalinks (when Permalink Manager is disabled).", "permalink-manager")),
				$native_slug_field
			);
		}

		if(empty($is_front_page)) {
			// 7. Default URI
			$html .= sprintf(
				"<div class=\"default-permalink-row columns-container\"><span class=\"column-3_4\"><strong>%s:</strong> %s</span><span class=\"column-1_4\"><a href=\"#\" class=\"restore-default\"><span class=\"dashicons dashicons-image-rotate\"></span> %s</a></span></div>",
				__("Default URI", "permalink-manager"), esc_html($default_uri),
				__("Restore Default URI", "permalink-manager")
			);

			// 8. Native URI info
			if(!empty($permalink_manager_options['general']['redirect']) && ((!empty($element->post_status) && in_array($element->post_status, array('auto-draft', 'trash', 'draft'))) == false)) {
				$native_permalink = trim(Permalink_Manager_Helper_Functions::get_permalink_base($element), "/") . "/";
				$native_permalink .= $native_uri;

				$html .= sprintf(
					"<div class=\"default-permalink-row columns-container\"><span><strong>%s</strong> <a href=\"%s\">%s</a></span></div>",
					__("Automatic redirect for native URI enabled:", "permalink-manager"),
					$native_permalink,
					rawurldecode($native_uri)
				);
			}
		}

		// 9. Custom redirects
		$html .= ($element->ID) ? self::display_redirect_panel($id) : self::display_redirect_panel("tax-{$id}");

		// 10. Extra save button for Gutenberg
		if($gutenberg) {
			$html .= sprintf(
				"<div class=\"default-permalink-row save-row columns-container hidden\"><div><a href=\"#\" class=\"button button-primary\" id=\"permalink-manager-save-button\">%s</a></div></div>",
				__("Save permalink", "permalink-manager")
			);
		} else {
			$html .= "</div>";
		}

		$html .= "</div>";

		// 11. Append nonce field, element ID & native slug
		$html .= self::generate_option_field("permalink-manager-edit-uri-element-slug", array("type" => "hidden", "value" => $native_slug));
		$html .= self::generate_option_field("permalink-manager-edit-uri-element-id", array("type" => "hidden", "value" => $element_id));
		$html .= wp_nonce_field('permalink-manager-edit-uri-box', 'permalink-manager-nonce', true, false);

		return $html;
	}

	/**
	 * Display the redirect panel
	 */
	public static function display_redirect_panel($element_id) {
		global $permalink_manager_options, $permalink_manager_redirects;

		// Heading
		$html = "<div class=\"permalink-manager redirects-row redirects-panel columns-container\">";

		$html .= sprintf(
			"<div><a class=\"button\" href=\"#\" id=\"toggle-redirect-panel\">%s</a></div>",
			__("Manage redirects", "permalink-manager")
		);

		$html .= "<div id=\"redirect-panel-inside\">";
		if(class_exists('Permalink_Manager_Pro_Addons')) {
			$html .= Permalink_Manager_Pro_Addons::display_redirect_form($element_id);
		} else {
			$html .= self::pro_text(true);
		}
		$html .= "</div>";

		$html .= "</div>";

		return $html;
	}

	/**
	 * Display error/info message
	 */
	public static function get_alert_message($alert_content = "", $alert_type = "", $dismissable = true, $id = false) {
		// Ignore empty messages (just in case)
		if(empty($alert_content) || empty($alert_type)) {
			return "";
		}

		$class = ($dismissable) ? "is-dismissible" : "";
		$alert_id = ($id) ? " data-alert_id=\"{$id}\"" : "";

		$html = sprintf( "<div class=\"{$alert_type} permalink-manager-notice notice {$class}\"{$alert_id}> %s</div>", wpautop($alert_content) );

		return $html;
	}

	/**
	 * Help tooltip
	 */
	static function help_tooltip($text = '') {
		$html = " <a href=\"#\" title=\"{$text}\" class=\"help_tooltip\"><span class=\"dashicons dashicons-editor-help\"></span></a>";
		return $html;
	}

	/**
	 * Display global notices (throughout wp-admin dashboard)
	 */
	function display_global_notices() {
		global $permalink_manager_alerts, $active_section;

		$html = "";
		if(!empty($permalink_manager_alerts) && is_array($permalink_manager_alerts)) {
			foreach($permalink_manager_alerts as $alert_id => $alert) {
				if(!empty($alert['show'])) {
					// Hide notice in Permalink Manager Pro
					if(defined('PERMALINK_MANAGER_PRO') && $alert['show'] == 'pro_hide') { continue; }

					// Display the notice only on the plugin pages
					if(empty($active_section) && !empty($alert['plugin_only'])) { continue; }

					// Check if the notice did not expire
					if(isset($alert['until']) && (time() > strtotime($alert['until']))) { continue; }

					$html .= self::get_alert_message($alert['txt'], $alert['type'], true, $alert_id);
				}
			}
		}

		echo $html;
	}

	/**
	 * Hide global notices (AJAX)
	 */
	function hide_global_notice() {
		global $permalink_manager_alerts;

		// Get the ID of the alert
		$alert_id = (!empty($_REQUEST['alert_id'])) ? sanitize_title($_REQUEST['alert_id']) : "";
		if(!empty($permalink_manager_alerts[$alert_id])) {
			$permalink_manager_alerts[$alert_id]['show'] = 0;
		}

		update_option( 'permalink-manager-alerts', $permalink_manager_alerts);
	}

	/**
	 * Display notices generated by Permalink Manager tools
	 */
	function display_plugin_notices() {
		global $permalink_manager_before_sections_html;

		echo $permalink_manager_before_sections_html;
	}

	/**
	 * Check if Permalink Manager Pro is active
	 */
	public static function is_pro_active($return_text = false) {
		if(defined('PERMALINK_MANAGER_PRO') && PERMALINK_MANAGER_PRO == true) {
			$is_pro = true;
		} else {
			$is_pro = false;
		}

		// Check if license is active
		if(class_exists('Permalink_Manager_Pro_Functions')) {
			$exp_date = Permalink_Manager_Pro_Functions::get_expiration_date(true);

			$is_pro = ($exp_date > 1) ? false : true;
		} else {
			$is_pro = false;
		}

		return $is_pro;
	}

	static function pro_text($text_only = false) {
		if(class_exists('Permalink_Manager_Pro_Functions')) {
			$text = Permalink_Manager_Pro_Functions::get_expiration_date(false, true);
		} else {
			$text = sprintf(__('This functionality is available only in <a href="%s" target="_blank">Permalink Manager Pro</a>.', 'permalink-manager'), PERMALINK_MANAGER_WEBSITE);
		}

		return ($text_only) ? $text : sprintf("<div class=\"alert info\"> %s</div>", wpautop($text, 'alert', false));
	}

}
