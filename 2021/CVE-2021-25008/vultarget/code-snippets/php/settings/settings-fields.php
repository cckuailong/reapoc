<?php

/**
 * Retrieve the default setting values
 * @return array
 */
function code_snippets_get_default_settings() {
	static $defaults;

	if ( isset( $defaults ) ) {
		return $defaults;
	}

	$defaults = array();

	foreach ( code_snippets_get_settings_fields() as $section_id => $fields ) {
		$defaults[ $section_id ] = array();

		foreach ( $fields as $field_id => $field_atts ) {
			$defaults[ $section_id ][ $field_id ] = $field_atts['default'];
		}
	}

	return $defaults;
}

/**
 * Retrieve the settings fields
 * @return array
 */
function code_snippets_get_settings_fields() {
	static $fields;

	if ( isset( $fields ) ) {
		return $fields;
	}

	$fields = array();

	$fields['general'] = array(
		'activate_by_default' => array(
			'name'    => __( 'Activate by Default', 'code-snippets' ),
			'type'    => 'checkbox',
			'label'   => __( "Make the 'Save and Activate' button the default action when saving a snippet.", 'code-snippets' ),
			'default' => true,
		),

		'snippet_scope_enabled' => array(
			'name'    => __( 'Enable Scope Selector', 'code-snippets' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable the scope selector when editing a snippet', 'code-snippets' ),
			'default' => true,
		),

		'enable_tags' => array(
			'name'    => __( 'Enable Snippet Tags', 'code-snippets' ),
			'type'    => 'checkbox',
			'label'   => __( 'Show snippet tags on admin pages', 'code-snippets' ),
			'default' => true,
		),

		'enable_description' => array(
			'name'    => __( 'Enable Snippet Descriptions', 'code-snippets' ),
			'type'    => 'checkbox',
			'label'   => __( 'Show snippet descriptions on admin pages', 'code-snippets' ),
			'default' => true,
		),

		'disable_prism' => array(
			'name'    => __( 'Disable Shortcode Syntax Highlighter', 'code-snippets' ),
			'type'    => 'checkbox',
			'label'   => __( 'Disable the syntax highlighting for the [code_snippet] shortcode on the front-end', 'code-snippets' ),
			'default' => false,
		),

		'complete_uninstall' => array(
			'name'    => __( 'Complete Uninstall', 'code-snippets' ),
			'type'    => 'checkbox',
			'label'   => sprintf(
				/* translators: %s: URL for Plugins admin menu */
				__( 'When the plugin is deleted from the <a href="%s">Plugins</a> menu, also delete all snippets and plugin settings.', 'code-snippets' ),
				self_admin_url( 'plugins.php' )
			),
			'default' => false,
		),
	);

	if ( is_multisite() && ! is_main_site() ) {
		unset( $fields['general']['complete_uninstall'] );
	}

	/* Description Editor settings section */
	$fields['description_editor'] = array(

		'rows' => array(
			'name'    => __( 'Row Height', 'code-snippets' ),
			'type'    => 'number',
			'label'   => __( 'rows', 'code-snippets' ),
			'default' => 5,
			'min'     => 0,
		),

		'use_full_mce' => array(
			'name'    => __( 'Use Full Editor', 'code-snippets' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable all features of the visual editor', 'code-snippets' ),
			'default' => false,
		),

		'media_buttons' => array(
			'name'    => __( 'Media Buttons', 'code-snippets' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable the add media buttons', 'code-snippets' ),
			'default' => false,
		),
	);

	/* Code Editor settings section */

	$fields['editor'] = array(
		'theme' => array(
			'name'       => __( 'Theme', 'code-snippets' ),
			'type'       => 'codemirror_theme_select',
			'default'    => 'default',
			'codemirror' => 'theme',
		),

		'indent_with_tabs' => array(
			'name'       => __( 'Indent With Tabs', 'code-snippets' ),
			'type'       => 'checkbox',
			'label'      => __( 'Use hard tabs (not spaces) for indentation.', 'code-snippets' ),
			'default'    => true,
			'codemirror' => 'indentWithTabs',
		),

		'tab_size' => array(
			'name'       => __( 'Tab Size', 'code-snippets' ),
			'type'       => 'number',
			'desc'       => __( 'The width of a tab character.', 'code-snippets' ),
			'default'    => 4,
			'codemirror' => 'tabSize',
			'min'        => 0,
		),

		'indent_unit' => array(
			'name'       => __( 'Indent Unit', 'code-snippets' ),
			'type'       => 'number',
			'desc'       => __( 'How many spaces a block should be indented.', 'code-snippets' ),
			'default'    => 4,
			'codemirror' => 'indentUnit',
			'min'        => 0,
		),

		'wrap_lines' => array(
			'name'       => __( 'Wrap Lines', 'code-snippets' ),
			'type'       => 'checkbox',
			'label'      => __( 'Whether the editor should scroll or wrap for long lines.', 'code-snippets' ),
			'default'    => true,
			'codemirror' => 'lineWrapping',
		),

		'line_numbers' => array(
			'name'       => __( 'Line Numbers', 'code-snippets' ),
			'type'       => 'checkbox',
			'label'      => __( 'Show line numbers to the left of the editor.', 'code-snippets' ),
			'default'    => true,
			'codemirror' => 'lineNumbers',
		),

		'auto_close_brackets' => array(
			'name'       => __( 'Auto Close Brackets', 'code-snippets' ),
			'type'       => 'checkbox',
			'label'      => __( 'Auto-close brackets and quotes when typed.', 'code-snippets' ),
			'default'    => true,
			'codemirror' => 'autoCloseBrackets',
		),

		'highlight_selection_matches' => array(
			'name'       => __( 'Highlight Selection Matches', 'code-snippets' ),
			'label'      => __( 'Highlight all instances of a currently selected word.', 'code-snippets' ),
			'type'       => 'checkbox',
			'default'    => true,
			'codemirror' => 'highlightSelectionMatches',
		),
	);

	$fields = apply_filters( 'code_snippets_settings_fields', $fields );

	return $fields;
}
