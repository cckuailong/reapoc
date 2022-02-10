<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Error\Notice\PermalinksStructureInvalidNotice;

/**
 * Checks for configuration errors about Permalinks Structure.
 */
class PermalinksStructureDetector implements ErrorDetector {

	/**
	 * {@inheritdoc}
	 */
	public function get_error() {
		if ( get_option( 'permalink_structure', '' ) !== '' ) {
			return null;
		}

		return new PermalinksStructureInvalidNotice();
	}
}
