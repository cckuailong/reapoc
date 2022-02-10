<?php
/**
 * Class for the settings importer.
 */

class FWP_Settings_Importer {

	/**
	 * Process import file - this parses the settings data and returns it.
	 *
	 * @param string $file path to json file.
	 */
	public function process_import_file( $file ) {

		// Get file contents.
		$data = FWP_Demos_Helpers::get_remote( $file );

		// Return from this function if there was an error.
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		// Decode file contents.
	    $data = json_decode( $data, true );

		// Import the data
    	return $this->import_data( $data );

	}

	/**
	 * Import JSON data
	 *
	 * @return array $results
	 */
	private function import_data( $file ) {

		// Import the file
		if ( ! empty( $file ) ) {

			if ( '0' == json_last_error() ) {

				// Loop through mods and add them
				foreach ( $file as $mod => $value ) {
					set_theme_mod( $mod, $value );
				}

			}

		}

		// Return file
		return $file;

	}
}
