<?php

namespace WebpConverter\Plugin\Activation;

use WebpConverter\Loader\LoaderAbstract;

/**
 * Initializes integration with image loading method to enable it.
 */
class RefreshLoader {

	/**
	 * Activates image loader if active.
	 *
	 * @return void
	 */
	public function refresh_image_loader() {
		do_action( LoaderAbstract::ACTION_NAME, true );
	}
}
