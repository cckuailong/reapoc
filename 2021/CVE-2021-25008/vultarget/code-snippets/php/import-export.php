<?php

/**
 * This file includes functions for importing and exporting snippets
 */

/**
 * @access private
 *
 * @param array  $snippets
 * @param null   $multisite
 * @param string $dup_action
 *
 * @return array
 */
function _code_snippets_save_imported_snippets( $snippets, $multisite = null, $dup_action = 'ignore' ) {

	/* Get a list of existing snippet names keyed to their IDs */
	$existing_snippets = array();
	if ( 'replace' === $dup_action || 'skip' === $dup_action ) {
		$all_snippets = get_snippets( array(), $multisite );

		foreach ( $all_snippets as $snippet ) {
			if ( $snippet->name ) {
				$existing_snippets[ $snippet->name ] = $snippet->id;
			}
		}
	}

	/* Save a record of the snippets which were imported */
	$imported = array();

	/* Loop through the provided snippets */
	/** @var Code_Snippet $snippet */
	foreach ( $snippets as $snippet ) {

		/* Check if the snippet already exists */
		if ( 'ignore' !== $dup_action && isset( $existing_snippets[ $snippet->name ] ) ) {

			/* If so, either overwrite the existing ID, or skip this import */
			if ( 'replace' === $dup_action ) {
				$snippet->id = $existing_snippets[ $snippet->name ];
			} elseif ( 'skip' === $dup_action ) {
				continue;
			}
		}

		/* Ensure that imported snippets are inactive */
		$snippet->active = 0;

		/* Save the snippet and increase the counter if successful */
		$snippet_id = save_snippet( $snippet );
		if ( $snippet_id ) {
			$imported[] = $snippet_id;
		}
	}

	return $imported;
}

/**f
 * Imports snippets from a JSON file
 *
 * @since 2.9.7
 *
 * @uses  save_snippet() to add the snippets to the database
 *
 * @param string    $file       The path to the file to import
 * @param bool|null $multisite  Import into network-wide table or site-wide table?
 * @param string    $dup_action Action to take if duplicate snippets are detected. Can be 'skip', 'ignore', or 'replace'
 *
 * @return array|bool An array of imported snippet IDs on success, false on failure
 */
function import_snippets_json( $file, $multisite = null, $dup_action = 'ignore' ) {

	if ( ! file_exists( $file ) || ! is_file( $file ) ) {
		return false;
	}

	$raw_data = file_get_contents( $file );
	$data = json_decode( $raw_data, true );
	$snippets = array();

	/* Reformat the data into snippet objects */
	foreach ( $data['snippets'] as $snippet ) {
		$snippet = new Code_Snippet( $snippet );
		$snippet->network = $multisite;
		$snippets[] = $snippet;
	}

	$imported = _code_snippets_save_imported_snippets( $snippets, $multisite, $dup_action );
	do_action( 'code_snippets/import/json', $file, $multisite );

	return $imported;
}

/**
 * Imports snippets from an XML file
 *
 * @since 2.0
 *
 * @uses  save_snippet() to add the snippets to the database
 *
 * @param string    $file       The path to the file to import
 * @param bool|null $multisite  Import into network-wide table or site-wide table?
 * @param string    $dup_action Action to take if duplicate snippets are detected. Can be 'skip', 'ignore', or 'replace'
 *
 * @return array|bool An array of imported snippet IDs on success, false on failure
 */
function import_snippets_xml( $file, $multisite = null, $dup_action = 'ignore' ) {

	if ( ! file_exists( $file ) || ! is_file( $file ) ) {
		return false;
	}

	/** @phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase */
	$dom = new DOMDocument( '1.0', get_bloginfo( 'charset' ) );
	$dom->load( $file );

	$snippets_xml = $dom->getElementsByTagName( 'snippet' );
	$fields = array( 'name', 'description', 'desc', 'code', 'tags', 'scope' );

	$snippets = array();

	/* Loop through all snippets */

	/** @var DOMElement $snippet_xml */
	foreach ( $snippets_xml as $snippet_xml ) {
		$snippet = new Code_Snippet();
		$snippet->network = $multisite;

		/* Build a snippet object by looping through the field names */
		foreach ( $fields as $field_name ) {

			/* Fetch the field element from the document */
			$field = $snippet_xml->getElementsByTagName( $field_name )->item( 0 );

			/* If the field element exists, add it to the snippet object */
			if ( isset( $field->nodeValue ) ) {
				$snippet->set_field( $field_name, $field->nodeValue );
			}
		}

		/* Get scope from attribute */
		$scope = $snippet_xml->getAttribute( 'scope' );
		if ( ! empty( $scope ) ) {
			$snippet->scope = $scope;
		}

		$snippets[] = $snippet;
	}

	$imported = _code_snippets_save_imported_snippets( $snippets, $dup_action, $multisite );
	do_action( 'code_snippets/import/xml', $file, $multisite );

	/** @phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase */

	return $imported;
}

/**
 * Set up the current page to act like a downloadable file instead of being shown in the browser
 *
 * @param string $format
 * @param array  $ids
 * @param string $table_name
 * @param string $mime_type
 *
 * @return array
 */
function code_snippets_prepare_export( $format, $ids, $table_name = '', $mime_type = '' ) {
	global $wpdb;

	/* Fetch the snippets from the database */
	if ( '' === $table_name ) {
		$table_name = code_snippets()->db->get_table_name();
	}

	if ( count( $ids ) ) {

		$sql = sprintf(
			'SELECT * FROM %s WHERE id IN (%s)', $table_name,
			implode( ',', array_fill( 0, count( $ids ), '%d' ) )
		);

		$snippets = $wpdb->get_results( $wpdb->prepare( $sql, $ids ), ARRAY_A );

	} else {
		$snippets = array();
	}

	/* Build the export filename */
	if ( 1 === count( $ids ) ) {
		/* If there is only snippet to export, use its name instead of the site name */
		$first_snippet = new Code_Snippet( $snippets[0] );
		$title = strtolower( $first_snippet->name );
	} else {
		/* Otherwise, use the site name as set in Settings > General */
		$title = strtolower( get_bloginfo( 'name' ) );
	}

	$filename = "{$title}.code-snippets.{$format}";
	$filename = apply_filters( 'code_snippets/export/filename', $filename, $title );

	/* Set HTTP headers */
	header( 'Content-Disposition: attachment; filename=' . sanitize_file_name( $filename ) );

	if ( '' !== $mime_type ) {
		header( "Content-Type: $mime_type; charset=" . get_bloginfo( 'charset' ) );
	}

	/* Return the retrieved snippets to build the rest of the export file */

	return $snippets;
}

/**
 * Export snippets to a downloadable PHP file
 *
 * @param $ids
 * @param $table_name
 */
function download_snippets( $ids, $table_name = '' ) {
	$snippets = code_snippets_prepare_export( 'php', $ids, $table_name );

	echo "<?php\n";

	/* Loop through the snippets */
	foreach ( $snippets as $snippet ) {
		$snippet = new Code_Snippet( $snippet );

		echo "\n/**\n * {$snippet->name}\n";

		if ( ! empty( $snippet->desc ) ) {

			/* Convert description to PhpDoc */
			$desc = strip_tags( str_replace( "\n", "\n * ", $snippet->desc ) );

			echo " *\n * $desc\n";
		}

		echo " */\n{$snippet->code}\n";
	}

	exit;
}

/**
 * Export snippets in JSON format
 *
 * @param array  $ids        list of snippet IDs to export
 * @param string $table_name name of the database table to fetch snippets from
 */
function export_snippets( $ids, $table_name = '' ) {
	$raw_snippets = code_snippets_prepare_export( 'json', $ids, $table_name, 'application/json' );
	$final_snippets = array();

	foreach ( $raw_snippets as $snippet ) {
		$snippet = new Code_Snippet( $snippet );

		$fields = array( 'name', 'desc', 'tags', 'scope', 'code', 'priority' );
		$final_snippet = array();

		foreach ( $fields as $field ) {
			if ( ! empty( $snippet->$field ) ) {
				$final_snippet[ $field ] = str_replace( "\r\n", "\n", $snippet->$field );
			}
		}

		if ( $final_snippet ) {
			$final_snippets[] = $final_snippet;
		}
	}

	$data = array(
		'generator'    => 'Code Snippets v' . code_snippets()->version,
		'date_created' => gmdate( 'Y-m-d H:i' ),
		'snippets'     => $final_snippets,
	);

	echo wp_json_encode( $data, apply_filters( 'code_snippets/export/json_encode_options', 0 ) );
	exit;
}
