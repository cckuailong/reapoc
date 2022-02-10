<?php

namespace WebpConverter\Plugin\Deactivation;

use WebpConverter\Loader\LoaderAbstract;

/**
 * Initializes integration with image loading method to disable it.
 */
class RefreshLoader {

	/**
	 * Deactivates image loader.
	 *
	 * @return void
	 */
	public function refresh_image_loader() {
		do_action( LoaderAbstract::ACTION_NAME, false );
	}
}
