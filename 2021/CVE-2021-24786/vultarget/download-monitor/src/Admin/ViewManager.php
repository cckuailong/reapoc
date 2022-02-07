<?php

class DLM_View_Manager {

	/**
	 * Display a view
	 *
	 * @param String $view
	 * @param array $vars
	 * @param String $path
	 */
	public function display( $view, $vars=array(), $path = '' ) {

		// setup variables
		extract( $vars );

		// set default path if $path is empty
		if ( empty( $path ) ) {
			$path = download_monitor()->get_plugin_path() . 'assets/views/';
		}

		// setup full view path
		$view = $path . $view . '.php';

		// check if view exists
		if ( file_exists( $view ) ) {

			// load view
			include( $view );
		}
	}

}