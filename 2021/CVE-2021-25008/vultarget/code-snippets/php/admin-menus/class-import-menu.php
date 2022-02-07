<?php

/**
 * This class handles the import admin menu
 * @since 2.4.0
 * @package Code_Snippets
 */
class Code_Snippets_Import_Menu extends Code_Snippets_Admin_Menu {

	/**
	 * Class constructor
	 */
	function __construct() {
		parent::__construct( 'import',
			_x( 'Import', 'menu label', 'code-snippets' ),
			__( 'Import Snippets', 'code-snippets' )
		);
	}

	/**
	 * Register action and filter hooks
	 */
	public function run() {
		parent::run();
		add_action( 'admin_init', array( $this, 'register_importer' ) );
		add_action( 'load-importer-code-snippets', array( $this, 'load' ) );
	}

	/**
	 * Executed when the menu is loaded
	 */
	public function load() {
		parent::load();

		$contextual_help = new Code_Snippets_Contextual_Help( 'import' );
		$contextual_help->load();

		$this->process_import_files();
	}

	/**
	 * Process the uploaded import files
	 *
	 * @uses import_snippets() to process the import file
	 * @uses wp_redirect() to pass the import results to the page
	 * @uses add_query_arg() to append the results to the current URI
	 */
	private function process_import_files() {

		/* Ensure the import file exists */
		if ( ! isset( $_FILES['code_snippets_import_files'] ) || ! count( $_FILES['code_snippets_import_files'] ) ) {
			return;
		}

		check_admin_referer( 'import_code_snippets_file' );

		$count = 0;
		$network = is_network_admin();
		$uploads = $_FILES['code_snippets_import_files'];
		$dup_action = isset( $_POST['duplicate_action'] ) ? $_POST['duplicate_action'] : 'ignore';
		$error = false;

		/* Loop through the uploaded files and import the snippets */

		foreach ( $uploads['tmp_name'] as $i => $import_file ) {
			$ext = pathinfo( $uploads['name'][ $i ] );
			$ext = $ext['extension'];
			$mime_type = $uploads['type'][ $i ];

			if ( 'json' === $ext || 'application/json' === $mime_type ) {
				$result = import_snippets_json( $import_file, $network, $dup_action );
			} elseif ( 'xml' === $ext || 'text/xml' === $mime_type ) {
				$result = import_snippets_xml( $import_file, $network, $dup_action );
			} else {
				$result = false;
			}

			if ( false === $result || -1 === $result ) {
				$error = true;
			} else {
				$count += count( $result );
			}
		}

		/* Send the amount of imported snippets to the page */
		$url = add_query_arg( $error ? array( 'error' => true ) : array( 'imported' => $count ) );
		wp_redirect( esc_url_raw( $url ) );
		exit;
	}

	/**
	 * Add the importer to the Tools > Import menu
	 */
	function register_importer() {

		/* Only register the importer if the current user can manage snippets */
		if ( ! defined( 'WP_LOAD_IMPORTERS' ) || ! code_snippets()->current_user_can() ) {
			return;
		}

		/* Register the Code Snippets importer with WordPress */
		register_importer(
			'code-snippets',
			__( 'Code Snippets', 'code-snippets' ),
			__( 'Import snippets from a code snippets export file', 'code-snippets' ),
			array( $this, 'render' )
		);
	}

	/**
	 * Print the status and error messages
	 */
	protected function print_messages() {

		if ( isset( $_REQUEST['error'] ) && $_REQUEST['error'] ) {
			echo '<div id="message" class="error fade"><p>';
			_e( 'An error occurred when processing the import files.', 'code-snippets' );
			echo '</p></div>';
		}

		if ( isset( $_REQUEST['imported'] ) && intval( $_REQUEST['imported'] ) >= 0 ) {
			echo '<div id="message" class="updated fade"><p>';

			$imported = intval( $_REQUEST['imported'] );

			if ( 0 === $imported ) {
				esc_html_e( 'No snippets were imported.', 'code-snippets' );

			} else {

				printf(
					/* translators: 1: amount of snippets imported, 2: link to Snippets menu */
					_n(
						'Successfully imported <strong>%1$d</strong> snippet. <a href="%2$s">Have fun!</a>',
						'Successfully imported <strong>%1$d</strong> snippets. <a href="%2$s">Have fun!</a>',
						$imported, 'code-snippets'
					),
					$imported,
					code_snippets()->get_menu_url( 'manage' )
				);
			}

			echo '</p></div>';
		}
	}
}
