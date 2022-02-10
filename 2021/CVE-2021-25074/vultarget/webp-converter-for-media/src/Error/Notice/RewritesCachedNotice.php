<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class RewritesCachedNotice implements ErrorNotice {

	const ERROR_KEY = 'rewrites_cached';

	/**
	 * {@inheritdoc}
	 */
	public function get_key(): string {
		return self::ERROR_KEY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_message(): array {
		return [
			__( 'Your server uses the cache for HTTP requests. The rules from .htaccess file or from Nginx configuration are not executed every time when the image is loaded, but the last redirect from cache is performed. With each request to image, your server should execute the rules from .htaccess file or from Nginx configuration. Now it only does this the first time and then uses cache. This means that if your server redirected image to WebP format the first time, it does so on every request. It should check the rules from .htaccess file or from Nginx configuration each time during request to image and redirect only when the conditions are met.', 'webp-converter-for-media' ),
			__( 'In this case, please contact your server administrator.', 'webp-converter-for-media' ),
			sprintf(
			/* translators: %1$s: open strong tag, %2$s: close strong tag, %3$s: loader name */
				__( '%1$sAlso try changing option "Image loading mode" to a different one.%2$s Issues about rewrites can often be resolved by setting this option to "%3$s". You can do this in plugin settings below. After changing settings, remember to flush cache if you use caching plugin or caching via hosting.', 'webp-converter-for-media' ),
				'<strong>',
				'</strong>',
				'Pass Thru'
			),
		];
	}
}
