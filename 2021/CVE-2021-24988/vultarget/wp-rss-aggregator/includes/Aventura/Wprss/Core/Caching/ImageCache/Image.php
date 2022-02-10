<?php

namespace Aventura\Wprss\Core\Caching\ImageCache;

if (!class_exists('\\WPRSS_Image_Cache_Image')) {
	require WPRSS_INC . 'image-caching.php';
}

/**
 * Image class for ImageCache module.
 */
class Image extends \WPRSS_Image_Cache_Image {

}
