<?php

namespace Aventura\Wprss\Core\Caching;

if (!class_exists('\\WPRSS_Image_Cache')) {
	require WPRSS_INC . 'image-caching.php';
}

/**
 * Image caching class.
 */
class ImageCache extends \WPRSS_Image_Cache {

	protected function _construct() {
		$this->set_image_class_name( __NAMESPACE__ . '\\ImageCache\\Image' );
	}

}
