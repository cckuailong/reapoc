<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Error\Notice\ErrorNotice;

/**
 * Interface for class that checks for configuration errors.
 */
interface ErrorDetector {

	/**
	 * @return ErrorNotice|null
	 */
	public function get_error();
}
