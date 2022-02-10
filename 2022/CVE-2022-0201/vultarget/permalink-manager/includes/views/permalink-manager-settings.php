<?php

/**
* Display the settings page
*/
class Permalink_Manager_Settings extends Permalink_Manager_Class {

	public function __construct() {
		add_filter( 'permalink_manager_sections', array($this, 'add_admin_section'), 3 );
	}

	public function add_admin_section($admin_sections) {
		$admin_sections['settings'] = array(
			'name'				=>	__('Settings', 'permalink-manager'),
			'function'    => array('class' => 'Permalink_Manager_Settings', 'method' => 'output')
		);

		return $admin_sections;
	}

	/**
	* Get the array with settings and render the HTML output
	*/
	public function output() {
		// Get all registered post types array & statuses
		$all_post_statuses_array = get_post_statuses();
		$all_post_types = Permalink_Manager_Helper_Functions::get_post_types_array(null, null, true);
		$all_taxonomies = Permalink_Manager_Helper_Functions::get_taxonomies_array(false, false, false, true);
		$content_types  = (defined('PERMALINK_MANAGER_PRO')) ? array('post_types' => $all_post_types, 'taxonomies' => $all_taxonomies) : array('post_types' => $all_post_types);

		$sections_and_fields = apply_filters('permalink_manager_settings_fields', array(
			'general' => array(
				'section_name' => __('General settings', 'permalink-manager'),
				'container' => 'row',
				'name' => 'general',
				'fields' => array(
					'auto_update_uris' => array(
						'type' => 'single_checkbox',
						'label' => __('Auto-update permalinks', 'permalink-manager'),
						'input_class' => '',
						'description' => sprintf('%s<br />%s',
						__('<strong>Permalink Manager can automatically update the custom permalink after post or term is saved/updated.</strong>', 'permalink-manager'),
						__('If enabled, Permalink Manager will always force the default custom permalink format (based on current <strong>Permastructure</strong> settings).', 'permalink-manager')
						)
					),
					'force_custom_slugs' => array(
							'type' => 'select',
							'label' => __('Slugs mode', 'permalink-manager'),
							'input_class' => 'settings-select',
							'choices' => array(0 => __('Use native slugs', 'permalink-manager'), 1 => __('Use actual titles as slugs', 'permalink-manager'), 2 => __('Inherit parents\' slugs', 'permalink-manager')),
							'description' => sprintf('%s<br />%s<br />%s',
								__('<strong>Permalink Manager can use either native slugs or actual titles for custom permalinks.</strong>', 'permalink-manager'),
								__('The native slug is generated from the initial title after the post or term is published.', 'permalink-manager'),
								__('Use this field if you would like Permalink Manager to use the actual titles instead of native slugs.', 'permalink-manager')
						)
					),
					'trailing_slashes' => array(
						'type' => 'select',
						'label' => __('Trailing slashes', 'permalink-manager'),
						'input_class' => 'settings-select',
						'choices' => array(0 => __('Use default settings', 'permalink-manager'), 1 => __('Add trailing slashes', 'permalink-manager'), 2 => __('Remove trailing slashes', 'permalink-manager')),
						'description' => __('This option can be used to alter the native settings and control if trailing slash should be added or removed from the end of posts & terms permalinks.', 'permalink-manager'),
						'description' => sprintf('%s<br />%s',
							__('<strong>You can use this feature to either add or remove the slases from end of WordPress permalinks.</strong>', 'permalink-manager'),
							__('Please go to "<em>Redirect settings -> Trailing slashes redirect</em>" to force the trailing slashes mode with redirect.', 'permalink-manager')
						)
					),
					'partial_disable' => array(
						'type' => 'checkbox',
						'label' => __('Exclude content types', 'permalink-manager'),
						'choices' => $content_types,
						'description' => __('Permalink Manager will ignore and not filter the custom permalinks of all selected above post types & taxonomies.', 'permalink-manager')
					),
					'ignore_drafts' => array(
						'type' => 'single_checkbox',
						'label' => __('Exclude drafts', 'permalink-manager'),
						'description' => __('If enabled, the custom permalinks for post drafts will not be saved.', 'permalink-manager')
					)
				)
			),
			'redirect' => array(
				'section_name' => __('Redirect settings', 'permalink-manager'),
				'container' => 'row',
				'name' => 'general',
				'fields' => array(
					'canonical_redirect' => array(
						'type' => 'single_checkbox',
						'label' => __('Canonical redirect', 'permalink-manager'),
						'input_class' => '',
						'description' => sprintf('%s<br />%s',
							__('<strong>Canonical redirect allows WordPress to "correct" the requested URL and redirect visitor to the canonical permalink.</strong>', 'permalink-manager'),
							__('This feature will be also used to redirect (old) original permalinks to (new) custom permalinks set with Permalink Manager.', 'permalink-manager')
						)
					),
					/*'endpoint_redirect' => array(
						'type' => 'single_checkbox',
						'label' => __('Redirect with endpoints', 'permalink-manager'),
						'input_class' => '',
						'description' => sprintf('%s',
							__('<strong>Please enable this option if you would like to copy the endpoint from source URL to the target URL during the canonical redirect.</strong>', 'permalink-manager')
						)
					),*/
					'old_slug_redirect' => array(
						'type' => 'single_checkbox',
						'label' => __('Old slug redirect', 'permalink-manager'),
						'input_class' => '',
						'description' => sprintf('%s<br />%s',
							__('<strong>Old slug redirect is used by WordPress to provide a fallback for old version of slugs after they are changed.</strong>', 'permalink-manager'),
							__('If enabled, the visitors trying to access the URL with the old slug will be redirected to the canonical permalink.', 'permalink-manager')
						)
					),
					'extra_redirects' => array(
						'type' => 'single_checkbox',
						'label' => __('Extra redirects (aliases)', 'permalink-manager'),
						'input_class' => '',
						'pro' => true,
						'disabled' => true,
						'description' => sprintf('%s<br /><strong>%s</strong>',
							__('Please enable this option if you would like to manage additional custom redirects (aliasees) in URI Editor for individual posts & terms.', 'permalink-manager'),
							__('You can disable this feature if you use another plugin to control the redirects, eg. Yoast SEO Premium or Redirection.', 'permalink-manager')
						)
					),
					'setup_redirects' => array(
						'type' => 'single_checkbox',
						'label' => __('Save old custom permalinks as extra redirects', 'permalink-manager'),
						'input_class' => '',
						'pro' => true,
						'disabled' => true,
						'description' => sprintf('%s<br /><strong>%s</strong>',
							__('If enabled, Permalink Manager will save the "extra redirect" for earlier version of custom permalink after you change it (eg. with URI Editor or Regenerate/reset tool).', 'permalink-manager'),
							__('Please note that the new redirects will be saved only if "Extra redirects (aliases)" option is turned on above.', 'permalink-manager')
						)
					),
					'trailing_slashes_redirect' => array(
						'type' => 'single_checkbox',
						'label' => __('Trailing slashes redirect', 'permalink-manager'),
						'input_class' => '',
						'description' => sprintf('%s<br /><strong>%s</strong>',
							__('Permalink Manager can force the trailing slashes settings in the custom permalinks with redirect.', 'permalink-manager'),
							__('Please go to "<em>General settings -> Trailing slashes</em>" to choose if trailing slashes should be added or removed from WordPress permalinks.', 'permalink-manager')
						)
					),
					'copy_query_redirect' => array(
						'type' => 'single_checkbox',
						'label' => __('Redirect with query parameters', 'permalink-manager'),
						'input_class' => '',
						'description' => sprintf('%s<br />%s',
							__('If enabled, the query parameters will be copied to the target URL when the redirect is triggered.', 'permalink-manager'),
							__('Example: <em>https://example.com/product/old-product-url/<strong>?discount-code=blackfriday</strong></em> => <em>https://example.com/new-product-url/<strong>?discount-code=blackfriday</strong></em>', 'permalink-manager')
						)
					),
					'sslwww_redirect' => array(
						'type' => 'single_checkbox',
						'label' => __('Force HTTPS/WWW', 'permalink-manager'),
						'input_class' => '',
						'description' => sprintf('%s<br />%s',
							__('<strong>You can use Permalink Manager to force SSL or "www" prefix in WordPress permalinks.</strong>', 'permalink-manager'),
							__('Please disable it if you encounter any redirect loop issues.', 'permalink-manager')
						)
					),
					'redirect' => array(
						'type' => 'select',
						'label' => __('Redirect mode', 'permalink-manager'),
						'input_class' => 'settings-select',
						'choices' => array(0 => __('Disable (Permalink Manager redirect functions)', 'permalink-manager'), "301" => __('301 redirect', 'permalink-manager'), "302" => __('302 redirect', 'permalink-manager')),
						'description' => sprintf('%s<br /><strong>%s</strong>',
							__('Permalink Manager includes a set of hooks that allow to extend the redirect functions used natively by WordPress to avoid 404 errors.', 'permalink-manager'),
							__('You can disable this feature if you do not want Permalink Manager to trigger any additional redirect functions at all.', 'permalink-manager')
						)
					)
				)
			),
			'third_parties' => array(
				'section_name' => __('Third party plugins', 'permalink-manager'),
				'container' => 'row',
				'name' => 'general',
				'fields' => array(
					'fix_language_mismatch' => array(
						'type' => 'single_checkbox',
						'label' => __('WPML/Polylang language mismatch', 'permalink-manager'),
						'input_class' => '',
						'class_exists' => array('SitePress', 'Polylang'),
						'description' => __('If enabled, the plugin will load the adjacent translation of post when the custom permalink is detected, but the language code in the URL does not match the language code assigned to the post/term.', 'permalink-manager')
					),
					'pmxi_support' => array(
						'type' => 'single_checkbox',
						'label' => __('WP All Import support', 'permalink-manager'),
						'input_class' => '',
						'class_exists' => 'PMXI_Plugin',
						'description' => __('If disabled, the custom permalinks <strong>will not be saved</strong> for the posts imported with WP All Import plugin.', 'permalink-manager')
					),
					'um_support' => array(
						'type' => 'single_checkbox',
						'label' => __('Ultimate Member support', 'permalink-manager'),
						'input_class' => '',
						'class_exists' => 'UM',
						'description' => __('If enabled, Permalink Manager will detect the additional Ultimate Member pages (eg. "account" sections).', 'permalink-manager')
					),
					'yoast_breadcrumbs' => array(
						'type' => 'single_checkbox',
						'label' => __('Breadcrumbs support', 'permalink-manager'),
						'input_class' => '',
						'description' => __('If enabled, the HTML breadcrumbs will be filtered by Permalink Manager to mimic the current URL structure.<br />Works with: <strong>WooCommerce, Yoast SEO, Slim Seo, RankMath and SEOPress</strong> breadcrumbs.', 'permalink-manager')
					),
					'primary_category' => array(
						'type' => 'single_checkbox',
						'label' => __('"Primary category" support', 'permalink-manager'),
						'input_class' => '',
						'description' => __('If enabled, Permalink Manager will use the "primary category" for the default post permalinks.<br />Works with: <strong>Yoast SEO, The SEO Framework, RankMath and SEOPress</strong>.', 'permalink-manager')
					),
				)
			),
			'advanced' => array(
				'section_name' => __('Advanced settings', 'permalink-manager'),
				'container' => 'row',
				'name' => 'general',
				'fields' => array(
					'show_native_slug_field' => array(
						'type' => 'single_checkbox',
						'label' => __('Show "Native slug" field in URI Editor', 'permalink-manager')
					),
					'pagination_redirect' => array(
						'type' => 'single_checkbox',
						'label' => __('Force 404 on non-existing pagination pages', 'permalink-manager'),
						'description' => __('If enabled, the non-existing pagination pages (for single posts) will return 404 ("Not Found") error.<br /><strong>Please disable it, if you encounter any problems with pagination pages or use custom pagination system.</strong>', 'permalink-manager')
					),
					'disable_slug_sanitization' => array(
						'type' => 'select',
						'label' => __('Strip special characters', 'permalink-manager'),
						'input_class' => 'settings-select',
						'choices' => array(0 => __('Yes, use native settings', 'permalink-manager'), 1 => __('No, keep special characters (.,|_+) in the slugs', 'permalink-manager')),
						'description' => __('If enabled only alphanumeric characters, underscores and dashes will be allowed for post/term slugs.', 'permalink-manager')
					),
					'keep_accents' => array(
						'type' => 'select',
						'label' => __('Convert accented letters', 'permalink-manager'),
						'input_class' => 'settings-select',
						'choices' => array(0 => __('Yes, use native settings', 'permalink-manager'), 1 => __('No, keep accented letters in the slugs', 'permalink-manager')),
						'description' => __('If enabled, all the accented letters will be replaced with their non-accented equivalent (eg. Å => A, Æ => AE, Ø => O, Ć => C).', 'permalink-manager')
					),
					'edit_uris_cap' => array(
						'type' => 'select',
						'label' => __('URI Editor role capability', 'permalink-manager'),
						'choices' => array('edit_theme_options' => __('Administrator (edit_theme_options)', 'permalink-manager'), 'publish_pages' => __('Editor (publish_pages)', 'permalink-manager'), 'publish_posts' => __('Author (publish_posts)', 'permalink-manager'), 'edit_posts' => __('Contributor (edit_posts)', 'permalink-manager')),
						'description' => sprintf(__('Only the users who have selected capability will be able to access URI Editor.<br />The list of capabilities <a href="%s" target="_blank">can be found here</a>.', 'permalink-manager'), 'https://wordpress.org/support/article/roles-and-capabilities/#capability-vs-role-table')
					),
					'auto_fix_duplicates' => array(
						'type' => 'select',
						'label' => __('Automatically fix broken URIs', 'permalink-manager'),
						'input_class' => 'settings-select',
						'choices' => array(0 => __('Disable', 'permalink-manager'), 1 => __('Fix URIs individually (during page load)', 'permalink-manager'), 2 => __('Bulk fix all URIs (once a day, in the background)', 'permalink-manager')),
						'description' => sprintf('%s',
							__('Enable this option if you would like to automatically remove redundant permalinks & duplicated redirects.', 'permalink-manager')
						)
					)
				)
			)
		));

		$output = Permalink_Manager_Admin_Functions::get_the_form($sections_and_fields, 'tabs', array('text' => __( 'Save settings', 'permalink-manager' ), 'class' => 'primary margin-top'), '', array('action' => 'permalink-manager', 'name' => 'permalink_manager_options'));
		return $output;
	}
}
