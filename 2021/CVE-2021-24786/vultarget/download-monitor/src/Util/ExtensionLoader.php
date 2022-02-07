<?php

namespace Never5\DownloadMonitor\Util;

class ExtensionLoader {

	/**
     * Fetch static JSON string from DLM server with extension info.
     * The response is locally stored in a transient to minimize requests.
     *
	 * @return mixed|string
	 */
    public function fetch() {
	    // Load extension json
	    if ( false === ( $extension_json = get_transient( 'dlm_extension_json' ) ) ) {

		    // Extension request
		    $extension_request = wp_remote_get( 'https://download-monitor.com/?dlm-extensions=true' );

		    if ( ! is_wp_error( $extension_request ) ) {

			    // The extension json from server
			    $extension_json = wp_remote_retrieve_body( $extension_request );

			    // Set Transient
			    set_transient( 'dlm_extension_json', $extension_json, DAY_IN_SECONDS );
		    }
	    }

	    return $extension_json;
    }

}